<?php

namespace Modules\Primavera\Services;

use Exception;
use Modules\Primavera\Entities\PrimaveraProductsBatch;
use Modules\Products\Entities\BmsProductsBatch;

class PrimaveraProductsBatchService
{

    private $primaveraAuth;
    private $primaveraProductBatch;

    public function __construct()
    {
        $this->primaveraAuth         = new PrimaveraAuthService();
        $this->primaveraProductBatch = new PrimaveraProductsBatch();
    }

    public function getAllProductBatches()
    {
        return $this->primaveraAuth->requestPrimaveraApi(
            'GET',
            '/WebApi/ApiExtended/LstProdutosStock'
        );
    }

    /**
     * It updates the products table with the data from the primavera database.
     *
     * @param command The command that is being run.
     */
    public function updateProductBatch($command)
    {
        try {
            $products = $this->getAllProductBatches();

            $primaveraProductsService = new PrimaveraProductsService();
            $productIdsWithBatches = [];
            
            foreach ($products as $product) {
                $batchNumbersToKeepFromProduct = [];
                $primaveraProduct = $primaveraProductsService->getProductByPrimaveraId($product->ARTIGO);

                if (!$primaveraProduct) {
                    $command->error('Product with given id not found Id not found' . $product->ARTIGO);

                    continue;
                }

                $productBatches = $product->LOTES;
                $productIdsWithBatches[] = $primaveraProduct->id;

                foreach ($productBatches as $batch) {

                    if (!$batch->LOTE) continue;

                    PrimaveraProductsBatch::updateOrCreate(
                        [
                            'batch_number'         => $batch->LOTE,
                            'primavera_product_id' => $primaveraProduct->id
                        ],
                        [

                            'active'               => $batch->ACTIVO,
                            'description'          => $batch->DESCRICAOLOTE,
                            'quantity'             => $batch->Quantidade,
                            'expiration_date'      => $batch->VALIDADE,
                        ]
                    );

                    $command->info('Batch: ' . $batch->LOTE . ' Saved for product: ' . $product->ARTIGO);

                    $batchNumbersToKeepFromProduct[] = $batch->LOTE;
                }

                // For every product
                // We have an array of batches that came on the API
                // We are gona set zero to the ones that are FROM THAT PRODUCT and are not in the array that came in the API
                if (!empty($batchNumbersToKeepFromProduct)) {
                    $this->primaveraProductBatch
                        ->where('primavera_product_id', $primaveraProduct->id)
                        ->whereNotIn('batch_number', $batchNumbersToKeepFromProduct)
                        ->update([
                            'active' => 0,
                            'quantity' => 0
                        ]);
                }
            }

            // The api does not return all the producs in the system
            // The pruducts that dont come in the api dont have batches
            // So we search the products that dont have batches and we set zero to all the batches from that product
            if (!empty($productIdsWithBatches)) {
                $productsWithoutBatches = $primaveraProductsService->getProductsExcludingIds($productIdsWithBatches);
                foreach ($productsWithoutBatches as $product) {
                    foreach ($product->batches as $batch) {
                        $batch->update([
                            'active' => 0,
                            'quantity' => 0
                        ]);
                    }
                }
            }

        } catch (Exception $e) {
            $command->error('Error Saving Product Batchs');
            $command->error($e);
        }
    }

    public function syncProductBatches()
    {
        try {
            $primaveraProductBatches = $this->primaveraProductBatch->all()->toArray();
            foreach ($primaveraProductBatches as $primaveraBatch) {
                BmsProductsBatch::where('erp_product_batch_id', $primaveraBatch['id'])
                    ->update([
                        'batch_number' => $primaveraBatch['batch_number'],
                        'active' => $primaveraBatch['active'],
                        'description' => $primaveraBatch['description'],
                        'quantity' => $primaveraBatch['quantity'],
                        'expiration_date' => $primaveraBatch['expiration_date'],
                    ]);
            }
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }
}
