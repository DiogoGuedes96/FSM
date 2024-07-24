<?php

namespace Modules\Primavera\Services;

use Modules\Primavera\Entities\PrimaveraProducts;
use Modules\Primavera\Services\PrimaveraAuthService;
use Modules\Primavera\Services\PrimaveraProductsBatchService;


class PrimaveraProductsService
{

    private $primaveraAuth;
    private $primaveraProductsBatchService;
    private $primaveraProduct;

    public function __construct()
    {
        $this->primaveraAuth                 = new PrimaveraAuthService();
        $this->primaveraProductsBatchService = new PrimaveraProductsBatchService();
        $this->primaveraProduct              = new PrimaveraProducts();
    }


    /**
     * It gets all the products from Primavera
     *
     * @return An array of products.
     */
    public function getAllProducts()
    {
        return $this->primaveraAuth->requestPrimaveraApi(
            'GET',
            '/WebApi/ApiExtended/LstProdutos'
        );
    }

    public function getProductByPrimaveraId($primaveraId)
    {
        try {
            return $this->primaveraProduct->with('batches')->where('primavera_id', $primaveraId)->first();
        } catch (\Throwable $th) {
        }
    }

    public function getProductsExcludingIds($excludedIds)
    {
        return $this->primaveraProduct
            ->with('batches')
            ->whereNotIn('id', $excludedIds)
            ->get();
    }

    /**
     * It updates the products table with the data from the primavera database.
     *
     * @param command The command that is being run.
     */
    public function updateProducts($command)
    {
        $products = $this->getAllProducts();

        foreach ($products as $product) {
            try {
                PrimaveraProducts::updateOrCreate(
                    ['primavera_id' => $product->Artigo],
                    [
                        'name' => trim($product->Descricao) ?? "",
                        // 'avg_price' => $product->PCMedio ?? "",
                        'avg_price' => $product->PVP1 ?? "",
                        'last_price' => $product->PCUltimo ?? "",
                        'sell_unit' => $product->UnidadeVenda ?? "",
                        'current_stock' => $product->STKActual ?? "",
                        'stock_mov' => $product->MovStock ?? "",
                        'family' => $product->Familia ?? "",
                        'sub_family' => $product->SubFamilia ?? "",
                        'pvp_1' => $product->PVP1 ?? "",
                        'pvp_2' => $product->PVP2 ?? "",
                        'pvp_3' => $product->PVP3 ?? "",
                        'pvp_4' => $product->PVP4 ?? "",
                        'pvp_5' => $product->PVP5 ?? "",
                        'pvp_6' => $product->PVP6 ?? "",
                        'iva' => $product->Iva ?? "",
                    ]
                );
                $command->info('Product Saved ' . $product->Artigo);
            } catch (\Throwable $th) {
                $command->error('Error to save Product ' . $product->Artigo);
                $command->error($th);
            }
        }
    }
}
