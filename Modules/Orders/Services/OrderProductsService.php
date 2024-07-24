<?php

namespace Modules\Orders\Services;

use Illuminate\Http\Request;
use Modules\Orders\Entities\OrderProducts;
use Modules\Products\Entities\BmsProductPrices;
use Modules\Products\Services\BmsProductsService;
use Modules\Products\Entities\Products;
use Modules\Orders\Entities\Order;
use Exception;
use Modules\Clients\Entities\Clients;

class OrderProductsService
{
    private $bmsProductsService;
    private $bmsProducts;
    private $orderProduct;
    private $order;

    public function __construct()
    {
        $this->bmsProductsService = new BmsProductsService();
        $this->bmsProducts        = new Products();
        $this->orderProduct       = new OrderProducts();
        $this->order       = new Order();
    }

    /**
     * It saves the order products in the database.
     *
     * @param invoiceProduct is the product that is in the invoice
     * @param order the order that the product belongs to
     * @param command The command that was executed.
     */
    public function saveOrderProductFromInvoice($invoiceProduct, $order, $command)
    {
        try {
            $bmsProduct = $this->bmsProductsService->getBmsProductByPrimaveraId($invoiceProduct->Artigo);

            if (empty($bmsProduct)) {
                return;
            }

            $orderProduct = OrderProducts::updateOrCreate(
                [
                    'name'                     => $invoiceProduct->Descricao ?? "", //Descricao is the name of the product in the primavera erp
                    'quantity'                 => $invoiceProduct->Quantidade ?? "",
                    'unit'                     => $invoiceProduct->Unidade ?? "",
                    'unit_price'               => $invoiceProduct->PrecoUnit ?? "",
                    'total_liquid_price'       => $invoiceProduct->TotalLiquido ?? "",
                    'discount_value'           => $invoiceProduct->Desconto ?? "",
                    'order_id'                 => $order->id,
                    'correction_price_percent' => 0.0,
                    'discount_percent'         => 0.0,
                    'bms_product'              => $bmsProduct->id,
                ]
            );
            $command->warn('OrderProduct ' . $orderProduct->id . ' created for order ' . $order->id);
        } catch (\Throwable $th) {
            $command->error('Error to save orderProduct ' . $invoiceProduct->Artigo . ' ' . $invoiceProduct->Descricao);
            $command->error($th);
        }
    }

    /**
     * This function saves order products and their details to the database.
     *
     * @param order The order object that the order products will be associated with.
     * @param orderProducts An array of order products, where each order product is an array containing
     * information about a product in the order, such as its name, quantity, unit, unit price, total liquid
     * price, discount, and the ID of the corresponding BMS product.
     *
     * @return an array of OrderProducts.
     */
    public function saveOrderProduct($orderId, Request $request, $client = null)
    {
        try {
            $newOrderProducts = array();
            $requestOrderProducts = $request->orderProducts ?? null;

            if (!$orderId || !$requestOrderProducts) {
                throw new exception('The given data was invalid. Missing parameters: OrderProducts', 422);
            }

            $pvp = $this->checkPvpClient($client);

            foreach ($requestOrderProducts as $requestOrderProduct) {
                if (is_int($requestOrderProduct)) {

                    $orderProduct      = $this->getOrderProductById($requestOrderProduct);
                    $bmsProduct        = $orderProduct->bms_product;
                    $newOrderProductId = $this->createOrderProduct($orderProduct->name, $orderProduct->quantity, $orderProduct->sale_unit, $orderProduct->sale_price,  $orderProduct->unit, $orderProduct->unit_price, $orderProduct->total_liquid_price, $orderId, $orderProduct->correction_price_percent ?? 0.0, $orderProduct->discount_percent ?? 0.0, $orderProduct->discount_value, $bmsProduct, $orderProduct->conversion, $orderProduct->volume);
                } else {
                    $bmsProduct        = $this->getBmsProductById($requestOrderProduct['bms_product']);
                    $productValues     = $this->calcOrderProductValues($requestOrderProduct, $bmsProduct->$pvp);
                    $newOrderProductId = $this->createOrderProduct(
                        $bmsProduct->name,
                        $requestOrderProduct['quantity'],
                        $requestOrderProduct['sale_unit'] ?? $bmsProduct->unit,
                        $requestOrderProduct['price'],
                        $bmsProduct->sell_unit,
                        $bmsProduct->$pvp,
                        $productValues[1],
                        $orderId,
                        $requestOrderProduct['correctionPrice'] ?? 0.0,
                        $requestOrderProduct['discount'] ?? 0.0,
                        $productValues[0],
                        $bmsProduct->id,
                        $requestOrderProduct['conversion'] ?? null,
                        $requestOrderProduct['volume'] ?? null,
                        null,
                        $requestOrderProduct['bms_product_batch'] ?? null,
                    );
                }
                $newOrderProduct = $this->getOrderProductById($newOrderProductId);
                array_push($newOrderProducts, $newOrderProduct);
            }

            return $newOrderProducts;
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    public function createOrderProduct($name, $quantity, $saleUnit, $salePrice, $unit, $unitPrice, $totalLiquidPrice, $orderId, $correctionPricePercent, $discountPercent, $discountValue, $bmsProduct, $conversion = null, $volume = null, $unavailability = null, $bmsProductBatch = null)
    {
        try {
            $teste = OrderProducts::create(
                [
                    'name'                     => $name,
                    'quantity'                 => $quantity,
                    'sale_unit'                => $saleUnit,
                    'sale_price'               => $salePrice,
                    'unit'                     => $unit,
                    'unit_price'               => $unitPrice,
                    'total_liquid_price'       => $totalLiquidPrice,
                    'order_id'                 => $orderId,
                    'correction_price_percent' => $correctionPricePercent ? $correctionPricePercent : 0,
                    'discount_percent'         => $discountPercent > 0 ? $discountPercent : 0,
                    'discount_value'           => $discountValue > 0 ? $discountValue : 0,
                    'bms_product'              => $bmsProduct,
                    'conversion'               => $conversion,
                    'volume'                   => $volume,
                    'unavailability'           => $unavailability,
                    'bms_product_batch'        => $bmsProductBatch
                ]
            )->id;
            return $teste;
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 500);
        }
    }

    /**
     * The function calculates the total price, discount value, and liquid value of a product in an order.
     *
     * @param orderProduct It is an array that contains information about a product in an order, including
     * its price, quantity, and discount percentage.
     *
     * @return An array containing the total price, discount value, and product liquid value of an order
     * product.
     */
    public function calcOrderProductValues($orderProduct, $avg_price)
    {
        try {
            $productValues      = [];
            $discountValue      = $avg_price - $orderProduct['price'];
            $productLiquidValue = $orderProduct['price'] * $orderProduct['quantity'];

            array_push($productValues, $discountValue, $productLiquidValue);

            return $productValues;
        } catch (Exception $e) {
            throw new exception($e->getMessage(), 500);
        }
    }

    public function updateOrderProducts($orderId, $data)
    {
        try {
            if (empty($data['products'])) {
                throw new exception('No OrderProducts found!', 422);
            }

            $order = $this->order->find($orderId);
            $exists = $this->order->where('id', $orderId)->exists();
            if (!$order || !$exists) {
                throw new exception('No order found with that id!', 500);
            }

            foreach ($data['products'] as $product) {
                if ($order->orderProducts()->where('id', $product['id'])->exists()) {
                    $orderProduct = OrderProducts::find($product['id']);

                    if (!empty($product['notes'])) {
                        $orderProduct->notes = $product['notes'];
                    }

                    if (isset($product['conversion']) && $product['conversion'] !== false) {
                        $orderProduct->conversion = $product['conversion'];
                        $orderProduct->total_liquid_price = $product['conversion'] * $orderProduct->sale_price;
                    }

                    if (isset($product['volume']) && $product['volume'] !== false) {
                        $orderProduct->volume = $product['volume'];
                    }

                    if (isset($product['quantity']) && $product['quantity'] !== false) {
                        $orderProduct->quantity = $product['quantity'];
                    }

                    if (isset($product['quantity']) && $product['quantity'] !== false) {
                        $orderProduct->quantity = $product['quantity'];
                    }

                    if (!empty($product['unavailability'])) {
                        $orderProduct->unavailability = $product['unavailability'];
                    }

                    if (!empty($product['batch'])) {
                        $orderProduct->bms_product_batch = $product['batch'];
                    }

                    $orderProduct->save();
                }
            }
        } catch (Exception $e) {
            throw new exception($e->getMessage(), 500);
        }
    }

    public function checkPvpClient($client)
    {
        try {
            $client = Clients::find($client);
            if (!$client) {
                return "pvp_1";
            }

            $typePriceMapping = [
                0 => "pvp_1",
                1 => "pvp_2",
                2 => "pvp_3",
                3 => "pvp_4",
                4 => "pvp_5",
                5 => "pvp_6",
            ];

            return $client->tipo_preco ? $typePriceMapping[$client->tipo_preco] : "pvp_1";
        } catch (Exception $e) {
            throw new exception($e->getMessage(), 500);
        }
    }

    public function updateOrAddProducts($order, $products, $client = null)
    {
        try {
            $pvpClient = $this->checkPvpClient($client);

            $orderProducts = [];
            foreach ($order->orderProducts()->get('id') as $product) {
                array_push($orderProducts, $product->id);
            }

            $requestOrderProducts = [];
            foreach ($products as $product) {
                if (!empty($product['order_products_id'])) {
                    array_push($requestOrderProducts, $product['order_products_id']);
                }
            }

            foreach (array_diff($orderProducts, $requestOrderProducts) as $productToDelete) {
                $orderProduct = OrderProducts::find($productToDelete);
                $orderProduct->delete();
            };

            foreach ($products as $product) {
                if (!empty($product['order_products_id'])) {
                    $orderProduct = OrderProducts::find($product['order_products_id']);
                    $bmsProduct = $this->bmsProducts->where('id', $product['bms_product'])->first();
                    $productValues = $this->calcOrderProductValues($product, $bmsProduct->$pvpClient);

                    $orderProduct->quantity = $product['quantity'];
                    $orderProduct->volume = $product['volume'] ?? null;
                    $orderProduct->sale_unit = $product['sale_unit'] ?? $bmsProduct->unit;
                    $orderProduct->sale_price = $product['price'];
                    $orderProduct->unit_price = $bmsProduct->$pvpClient;
                    $orderProduct->correction_price_percent = $product['correctionPrice'] ?? 0.0;
                    $orderProduct->discount_percent = $product['discount'] ?? 0.0;
                    $orderProduct->discount_value = $productValues[0] ? $productValues[0] : 0;
                    $orderProduct->conversion = $product['conversion'] ?? null;
                    $orderProduct->bms_product_batch = $product['bms_product_batch'] ?? null;
                    $orderProduct->save();

                    continue;
                }

                $bmsProduct = $this->bmsProducts->where('id', $product['bms_product'])->first();
                $productValues = $this->calcOrderProductValues($product, $bmsProduct->$pvpClient);

                $this->createOrderProduct(
                    $bmsProduct->name,
                    $product['quantity'],
                    $product['sale_unit'] ?? $bmsProduct->unit,
                    $product['price'],
                    $bmsProduct->sell_unit,
                    $bmsProduct->$pvpClient,
                    $productValues[1],
                    $order->id,
                    $product['correctionPrice'] ?? 0.0,
                    $product['discount'] ?? 0.0,
                    $productValues[0] ? $productValues[0] : 0,
                    $bmsProduct->id,
                    $product['conversion'] ?? null,
                    $product['volume'] ?? null,
                    null, //unavailability
                    $product['bms_product_batch'] ?? null
                );
            }
        } catch (\Exception $e) {
            throw new exception($e->getMessage(), 500);
        }
    }

    public function getBmsProductById($productId)
    {
        try {
            return $this->bmsProducts->with('orderProduct')->where('id', $productId)->first();
        } catch (Exception $e) {
            throw new exception($e->getMessage(), 500);
        }
    }

    public function getOrderProductById($productId)
    {

        try {
            return $this->orderProduct->with('bmsProduct')->where('id', $productId)->first();
        } catch (Exception $e) {
            throw new exception($e->getMessage(), 500);
        }
    }
}
