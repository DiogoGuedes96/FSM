<?php

namespace Modules\Orders\Http\Controllers;

use Exception;
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Contracts\Support\Renderable;
use Modules\Clients\Entities\Clients;
use Modules\Orders\Entities\Order;
use Modules\Orders\Services\OrderProductsService;
use Modules\Orders\Services\OrdersService;
use Modules\Orders\Http\Requests\AddOrdersToCacheRequest;
use Modules\Orders\Http\Requests\ChangeOrderDateRequest;
use Modules\Orders\Http\Requests\FilterOrderProductsRequest;
use Modules\Orders\Http\Requests\UpdateOrderPending;
use Modules\Orders\Http\Requests\ForkOrderRequest;
use Modules\Orders\Http\Requests\SaveOrderRequest;
use Modules\Orders\Http\Requests\UpdateOrderRequest;
use Modules\Orders\Http\Requests\ValidateStockOrderRequest;
use Modules\Orders\Http\Requests\DuplicateOrderRequest;
use Modules\Orders\Http\Requests\RemoveOrdersFromCacheRequest;
use Modules\Orders\Http\Requests\ReplaceOrdersOnCacheRequest;
use Modules\Orders\Http\Requests\ReplaceUserOnOrderInCacheRequest;
use Modules\Orders\Http\Requests\UpdateDirectSaleOrderRequest;
use Modules\Primavera\Services\PrimaveraInvoicesService;

class OrdersController extends Controller
{
    private $ordersService;
    private $primaveraInvoicesService;
    private $orderProductsService;
    private $orders;

    public function __construct()
    {
        $this->ordersService        = new OrdersService();
        $this->orderProductsService = new OrderProductsService();
        $this->orders               = new Order();
        $this->primaveraInvoicesService = new PrimaveraInvoicesService();
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return view('orders::index');
    }

    public function summary()
    {
        return view('orders::summary');
    }

    public function checkout()
    {
        return view('orders::checkout');
    }

    public function tracking()
    {
        return view('orders::tracking');
    }

    public function directSale()
    {
        return view('orders::direct-sale');
    }


    public function directSaleCheckout()
    {
        return view('orders::direct-sale-checkout');
    }

    public function directSaleSummary()
    {
        return view('orders::direct-sale-summary');
    }

    public function store(SaveOrderRequest $saveOrderRequest)
    {
        try {
            $newOrder = $this->ordersService->saveNewOrder($saveOrderRequest);
            $this->orderProductsService->saveOrderProduct($newOrder->id, $saveOrderRequest, $saveOrderRequest->bmsClient ?? null);
            $order = $this->ordersService->getOrderById($newOrder->id);
            return response()->json(['order' => $order]);
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function update(UpdateOrderRequest $updateOrderRequest, $id)
    {
        try {
            if (!$id) {
                throw new Exception('Invalid Order Id!', 400);
            }

            $orderData = $updateOrderRequest->orderData;
            $updateValues = $updateOrderRequest->values;

            if (!$orderData && !$updateValues) {
                throw new Exception('Missing parameters!', 400);
            }

            $this->ordersService->updateOrder($id, $updateValues);
            $this->orderProductsService->updateOrderProducts($id, $orderData);

            $order = $this->ordersService->getOrderById($id, 'orderProducts');

            return response()->json(['order' => $order]);
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function updateDirectSale(UpdateDirectSaleOrderRequest $updateOrderRequest, $id){
        try {
            if (!$id) {
                throw new Exception('Invalid Order Id!', 400);
            }

            $orderData = $updateOrderRequest->orderData;
            $updateValues = $updateOrderRequest->values;

            if (!$orderData && !$updateValues) {
                throw new Exception('Missing parameters!', 400);
            }

            $this->ordersService->updateOrder($id, $updateValues, false, true);
            $this->orderProductsService->updateOrderProducts($id, $orderData);

            $order = $this->ordersService->getOrderById($id);

            try {
                if (isset($updateOrderRequest['values']['status']) && $updateOrderRequest['values']['status'] === 'delivering'){
                    $newInvoice = $this->primaveraInvoicesService->sendInvoice($order);
                    $this->ordersService->updateOrder($id, ['erp_invoice_id' => $newInvoice->id, 'prepared_by' => auth()->user()->id], false);
                }
            } catch (\Throwable $th) {
             
            }

            return response()->json(['order' => $order]);
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function updateOrderAndAddProducts($id, Request $request)
    {
        try {
            if (!$id) {
                throw new Exception('Invalid Order Id!', 400);
            }

            $order = Order::find($id);

            if (!$order) {
                throw new exception('No order found with that id!', 500);
            }

            $client = !empty($request->bmsClient) ? $request->bmsClient : null;

            $orderUpdate = [
                "caller_phone" => !empty($request->callerPhone) ? $request->callerPhone : null,
                "notes" => !empty($request->orderNotes) ? $request->orderNotes : null,
                "bms_client" => $client
            ];

            $this->ordersService->updateOrder($id, $orderUpdate, false);

            $this->orderProductsService->updateOrAddProducts($order, $request->orderProducts, $client);

            $order = $this->ordersService->getOrderById($id, 'orderProducts');

            return response()->json(['order' => $order]);
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function getAllOrders()
    {
        try {
            return [
                'orders' => $this->ordersService->getAllOrders(),
            ];
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function getProductsByOrders($clientId)
    {
        try {
            $orders = $this->ordersService->getProductsOrdersByClientId($clientId);

            return response()->json($orders);
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function getOrdersDetailsByClientId($clientId)
    {
        try {
            return [
                'products' => $this->ordersService->getProductsOrdersByClientId($clientId),
                'orders' => $this->getClientFilteredOrders($clientId)
            ];
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function getClientFilteredOrders(Clients $bmsClient)
    {
        try {
            return $this->ordersService->getClientFilteredOrders($bmsClient);
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function getProductsMostBoughtProductsByClient(Clients $bmsClient, FilterOrderProductsRequest $filters)
    {
        try {
            $products = $this->ordersService->getBoughtProductsByClient($bmsClient->id, $filters, 'desc');

            return ['products' => $products];
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }


    public function getLessBoughtProductsByClient(Clients $bmsClient, FilterOrderProductsRequest $filters)
    {
        try {
            $products = $this->ordersService->getBoughtProductsByClient($bmsClient->id, $filters, 'asc');

            return ['products' => $products];
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function getOrderById($orderId)
    {
        try {
            return $this->ordersService->getOrderById($orderId);
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function setPriorityOrder($orderId)
    {
        try {
            $order = $this->ordersService->setPriorityOrder($orderId);

            return response()->json(['message' => 'success', 'order' => $order]);
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function forkOrder(ForkOrderRequest $forkOrderRequest)
    {
        try {
            $newOrder = $this->ordersService->saveNewOrder($forkOrderRequest);
            $newOrderProducts = $this->orderProductsService->saveOrderProduct($newOrder->id, $forkOrderRequest);

            $this->ordersService->softDeleteProductsFromOrder($forkOrderRequest);

            $parentOrder = $this->ordersService->getOrderById($forkOrderRequest->orderId);

            return response()->json([
                'ParentOrder' => ['order' => $parentOrder],
                'NewOrder'    => ['order' => $newOrder, 'orderProducts' => $newOrderProducts]
            ]);
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function removeProductsFromOrder(Request $request)
    {
        try {
            $this->ordersService->softDeleteProductsFromOrder($request);
            $order = $this->ordersService->getOrderById($request->orderId);

            return response()->json([
                'Order' => ['order' => $order]
            ]);
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function setOrderStatus($orderId, $status)
    {
        try {
            $orderID = $this->ordersService->setOrderStatus($orderId, $status);
            $order = $this->ordersService->getOrderById($orderID);

            if ($status === $this->ordersService::STATUS_IN_DELIVERY && !empty($order->client)) {
                $newInvoice = $this->primaveraInvoicesService->sendInvoice($order);
                $this->ordersService->updateOrder($orderID, ['erp_invoice_id' => $newInvoice->id, 'prepared_by' => auth()->user()->id], false);

                return response()->json(['message' => 'success', 'Order Status updated with Success and sent a new invoice to ERP.' => ['Order: ' => $order->id, 'Status' => $status]]);
            }

            return response()->json(['message' => 'success', 'Order Status updated with Success' => ['Order: ' => $order->id, 'Status' => $status]]);
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function setNumberInvoice(Request $request, $orderId)
    {
        $order = $this->ordersService->setNumberInvoice($orderId, $request);

        return response()->json([
            'message' => 'success',
            'success' => true
        ]);
    }

    public function getOrdersByStatus(Request $request, $status, $zoneId = null)
    {
        try {
            $orderList = $this->ordersService->getAllOrdersByStatus($status, $zoneId, $request->all());

            return response()->json(['message' => 'success', 'Orders' => $orderList]);
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function searchOrdersByInput($input)
    {
        try {
            $orderList = $this->ordersService->searchOrdersByInput($input);

            return response()->json(['message' => 'success', 'orders' => $orderList]);
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function validateStock(Order $order, ValidateStockOrderRequest $request)
    {
        try {
            if (!$order) {
                return response()->json(['message' => 'error', 'error' => 'Something went Wrong !'], 500);
            }

            $orderData = $request->all();

            $this->ordersService->validateStock($order, $orderData);

            $order = $this->ordersService->setOrderStatus($order->id, $orderData['status']);

            return response()->json(['message' => 'success', 'Order' => $order]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function updateOrderPending(Order $order, UpdateOrderPending $request)
    {
        try {
            if (!$order) {
                return response()->json(['message' => 'error', 'error' => 'Something went Wrong !'], 500);
            }

            $order = $this->ordersService->updateOrderPending($order, $request->all());

            return response()->json(['message' => 'success', 'Order' => $order]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function generatePDFOrder(Order $order)
    {
        try {
            if (!$order) {
                return response()->json(['message' => 'error', 'error' => 'Something went Wrong !'], 500);
            }

            $data = $this->ordersService->generatePDF($order);

            $html = view('orders::invoice', $data)->render();

            $pdf = new Dompdf();
            $pdf->setPaper('A4');
            $pdf->loadHtml($html);
            $pdf->render();

            return $pdf->stream('invoice.pdf');
        } catch (\Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function duplicate(DuplicateOrderRequest $request, $id)
    {
        try {
            if (!$id) {
                throw new Exception('ID da encomenda inválido!', 400);
            }

            $order = $this->ordersService->getOrderById($id, 'orderProducts');

            $duplicatedOrder = $this->ordersService->duplicateOrder($order, $request->all());

            return response()->json([
                'order' => $duplicatedOrder
            ]);
        } catch (Exception $error) {
            return response()->json([
                'message' => 'error',
                'error' => $error->getMessage()
            ], $error->getCode());
        }
    }

    public function changeDeliveryDate(ChangeOrderDateRequest $request, $id)
    {
        try {
            if (!$id) {
                throw new Exception('ID da encomenda inválido!', 400);
            }

            $order = $this->ordersService->changeDeliveryDate($id, $request->all());

            return response()->json([
                'order' => $order
            ]);
        } catch (Exception $error) {
            return response()->json([
                'message' => 'error',
                'error' => $error->getMessage()
            ], $error->getCode());
        }
    }

    public function getAllCachedBlocked()
    {
        try {
            $blockedOrders = $this->ordersService->getAllCachedBlocked();

            return response()->json([
                'orders' => $blockedOrders
            ]);
        } catch (Exception $error) {
            return response()->json([
                'message' => 'error',
                'error' => $error->getMessage()
            ], $error->getCode());
        }
    }

    public function setOrderBlockedOnCache(AddOrdersToCacheRequest $addOrdersToCache)
    {
        try {
            $blockedOrders = $this->ordersService->setOrderBlockedOnCache(
                \Auth::user()->id,
                $addOrdersToCache->order_id,
                $addOrdersToCache->browser_token
            );

            return response()->json([
                'orders' => $blockedOrders
            ]);
        } catch (Exception $error) {
            return response()->json([
                'message' => 'error',
                'error' => $error->getMessage()
            ], $error->getCode());
        }
    }

    public function removeOrderBlockedFromCache(RemoveOrdersFromCacheRequest $removeOrdersFromCache)
    {
        try {
            $blockedOrders = $this->ordersService->removeOrderBlockedFromCache(
                \Auth::user()->id,
                $removeOrdersFromCache->browser_token
            );

            return response()->json([
                'orders' => $blockedOrders
            ]);
        } catch (Exception $error) {
            return response()->json([
                'message' => 'error',
                'error' => $error->getMessage()
            ], $error->getCode());
        }
    }

    public function removeAllOrdersBlockedFromCache()
    {
        try {
            $blockedOrders = $this->ordersService->removeAllOrdersBlockedFromCache();

            return response()->json([
                'orders' => $blockedOrders
            ]);
        } catch (Exception $error) {
            return response()->json([
                'message' => 'error',
                'error' => $error->getMessage()
            ], $error->getCode());
        }
    }

    public function replaceOrderBlockedOnCache(ReplaceOrdersOnCacheRequest $replaceOrdersOnCacheRequest)
    {
        try {
            $blockedOrders = $this->ordersService->replaceOrderBlockedOnCache(
                \Auth::user()->id,
                $replaceOrdersOnCacheRequest->browser_token,
                $replaceOrdersOnCacheRequest->order_id
            );

            return response()->json([
                'orders' => $blockedOrders
            ]);
        } catch (Exception $error) {
            return response()->json([
                'message' => 'error',
                'error' => $error->getMessage()
            ], $error->getCode());
        }
    }

    public function replaceUserOnOrderInCache(ReplaceUserOnOrderInCacheRequest $replaceUserOnOrderInCacheRequest)
    {
        try {
            $blockedOrders = $this->ordersService->replaceUserOnOrderInCache(
                \Auth::user(),
                $replaceUserOnOrderInCacheRequest->browser_token,
                $replaceUserOnOrderInCacheRequest->order_id
            );

            return response()->json([
                'orders' => $blockedOrders
            ]);
        } catch (Exception $error) {
            return response()->json([
                'message' => 'error',
                'error' => $error->getMessage()
            ], $error->getCode());
        }
    }

    public function generateInvoice($orderId) {
        try {
            $order = $this->ordersService->getOrderById($orderId);

            $newInvoice = $this->primaveraInvoicesService->sendInvoice($order);
            $this->ordersService->updateOrder($orderId, ['erp_invoice_id' => $newInvoice->id, 'prepared_by' => auth()->user()->id], false);

            return response()->json(['message' => 'success', 'Order Status updated with Success and sent a new invoice to ERP.' => ['Order: ' => $order->id]]);
        } catch (Exception $error) {
            return response()->json([
                'message' => 'error',
                'error' => $error->getMessage()
            ], $error->getCode());
        }
    }
}
