<?php

namespace Modules\Primavera\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Modules\Primavera\Entities\PrimaveraInvoices;
use Modules\Primavera\Entities\PrimaveraProducts;
use Modules\Primavera\Services\PrimaveraAuthService;
use Modules\Clients\Entities\Clients;
use Modules\Orders\Services\OrderProductsService;
use Modules\Orders\Services\OrdersService;


class PrimaveraInvoicesService
{

    private $primaveraAuth;
    private $bmsClients;
    private $primaveraProducts;
    private $ordersService;
    private $orderProductsService;

    public function __construct()
    {
        $this->primaveraAuth        = new PrimaveraAuthService();
        $this->bmsClients           = new Clients();
        $this->primaveraProducts    = new PrimaveraProducts();
        $this->ordersService        = new OrdersService();
        $this->orderProductsService = new OrderProductsService();
    }


    /**
     * It gets all the invoices of a client between two dates
     * 
     * @param clientPrimaveraId The Primavera ID of the client you want to get invoices from.
     * @param startDate The start date of the invoices you want to retrieve.
     * @param endDate The end date of the invoice.
     * 
     * @return An array of invoices.
     */
    public function getClientInvoicesByDate($clientPrimaveraId, $startDate, $endDate)
    {
        return $this->primaveraAuth->requestPrimaveraApi(
            'GET',
            '/WebApi/ApiExtended/Documentos',
            [
                'ID'          => $clientPrimaveraId,
                'DataInicial' => $startDate,
                'DataFinal'   => $endDate
            ]
        );
    }

    /**
     * It gets all invoices from a client
     * 
     * @param clientPrimaveraId The ID of the client in Primavera
     * 
     * @return An array of invoices.
     */
    public function getAllClientInvoices($clientPrimaveraId)
    {
        return $this->primaveraAuth->requestPrimaveraApi(
            'GET',
            '/WebApi/ApiExtended/LstDocumentos',
            ["ID" => $clientPrimaveraId]
        );
    }

    /**
     * It gets all the clients from the database, then for each client it gets all the invoices from the
     * ERP
     * Then for each invoice it creates a new invoice in the database, then for each invoice product
     * it creates a new invoice product in the database.
     * Then it creates a new order for that invoice, then
     * for each invoice product it creates a new orderProduct and links it to the order
     * 
     * @param command This is the command that is being run.
     * @param startDate The start date of the invoices we want to get.
     * @param endDate The end date of the invoices we want to get.
     * @param clientId The client id from the BMS.
     */
    public function updateOrCreateInvoicesWithOrders($command, $startDate = null, $endDate = null, $clientId = null)
    {
        if ($clientId) {
            $lastClientRun = $this->bmsClients->where('erp_client_id', $clientId)->first()->id;
            $bmsClients = $this->bmsClients->where('id', '>=', $lastClientRun)->get();
        } else {
            $bmsClients = $this->bmsClients->all();
        }

        foreach ($bmsClients as $bmsClient) {
            $invoices = null;

            try {
                if ($startDate) { //If we have a start date, then are filtering by dates
                    $invoices = $this->getClientInvoicesByDate($bmsClient->erp_client_id, $startDate, $endDate)->Documentos; //One invoice can have one or several products
                } else { //If we dont have a start date, we are getting all the invoices
                    $invoices = $this->getAllClientInvoices($bmsClient->erp_client_id)->Documentos; //One invoice can have one or several products
                }
            } catch (\Exception $e) {
                $command->error('Error to get Documents from Client: ' . $bmsClient->erp_client_id . '.');
                continue;
            }

            if (!$invoices) {
                $command->error('Client: ' . $bmsClient->erp_client_id . ' Does not have any invoices!');
                continue;
            }

            foreach ($invoices as $invoice) {
                try {
                    //Create a new invoice
                    $newInvoice = $this->saveInvoices($invoice, $bmsClient->addresses->first()->address, $bmsClient, $command); //Saves invoices and invoiceProducts

                    if ($newInvoice) {
                        //SaveInvoiceProducts
                        $invoiceProducts = $invoice->Linhas;
                        $this->saveInvoiceProducts($invoiceProducts, $newInvoice, $command);

                        //Create a new order for that invoice
                        $neworder = $this->ordersService->saveInvoiceOrder($newInvoice, $bmsClient, $command);

                        //For each invoice, get the invoice products
                        //For each incoide product, create a orderProduct and linkint to the order 
                        if ($neworder) {
                            foreach ($invoiceProducts as $invoiceProduct) {
                                $this->orderProductsService->saveOrderProductFromInvoice($invoiceProduct, $neworder, $command);
                            }
                        }
                    }
                } catch (\Throwable $th) {
                    $command->error('Error to save Invoice ' . $invoice->NumDoc);
                    $command->error($th);
                }
            }
        }
    }


    /**
     * It gets all the invoices from a client, saves them and then saves the invoice products
     * 
     * @param command The command that is being executed.
     * @param startDate The date you want to start the invoices from.
     * @param endDate The end date of the invoices you want to retrieve.
     * @param clientId If you want to update only one client, you can pass the client id.
     */
    public function updateOrCreateInvoices($command, $startDate = null, $endDate = null, $clientId = null)
    {
        if ($clientId) {
            $lastClientRun = $this->bmsClients->where('erp_client_id', $clientId)->first()->id;
            $bmsClients = $this->bmsClients->where('id', '>=', $lastClientRun)->get();
        } else {
            $bmsClients = $this->bmsClients->all();
        }

        foreach ($bmsClients as $bmsClient) {
            if ($startDate) {
                $invoices = $this->getClientInvoicesByDate($bmsClient->erp_client_id, $startDate, $endDate)->Documentos; //One invoice can have one or several products
            } else {
                $invoices = $this->getAllClientInvoices($bmsClient->erp_client_id)->Documentos; //One invoice can have one or several products
            }

            if (!$invoices) {
                $command->error('Client: ' . $bmsClient->erp_client_id . ' Does not have any invoices!');
                continue;
            }

            foreach ($invoices as $invoice) {
                try {
                    $newInvoice = $this->saveInvoices($invoice, $bmsClient->addresses->first()->address, $bmsClient, $command); //Saves invoices and invoiceProducts
                    if ($newInvoice) { //If doesnt return an invoice, it already showed an error, move to the next invoice
                        $invoiceProducts = $invoice->Linhas; //Retrive all invoice products
                        $this->saveInvoiceProducts($invoiceProducts, $newInvoice, $command);
                    }
                } catch (\Throwable $th) {
                    $command->error('Error to save Invoice ' . $invoice->NumDoc);
                    $command->error($th);
                }
            }
        }
    }

    /**
     * It saves the invoice in the database, if it's a FA (Fatura) type
     * 
     * @param invoice The invoice object from the Primavera API
     * @param address The address of the client
     * @param clientId The id of the client in the database
     * @param command The command that is being executed.
     * 
     * @return The new invoice that was created or updated.
     */
    public function saveInvoices($invoice, $address, $bmsClient, $command)
    {
        if ($invoice->TipoDoc !== 'FA') { //Save only Faturas (FA)
            $command->error('Document: ' . $invoice->NumDoc . ' is not an FA, will not be saved!' . ' For Client ' . $bmsClient->erp_client_id);
            return false;
        }

        $ec = $this->extractDescriptionDetails(collect($invoice->Linhas));

        if (!empty($ec)) {
            $ecInvoice = PrimaveraInvoices::where('number', $ec["order_number"])->first();

            if (!$ecInvoice) {
                return false;
            }

            $newInvoice = $this->saveNewInvoice($invoice, $address, $bmsClient->id, $ecInvoice->id);

            if (!empty($ecInvoice->orders)) {
                $order = $ecInvoice->orders->firstWhere('status', '<>', 'completed');

                if ($order) {
                    try {
                        $order->status = "completed";
                        $order->save();
                    } catch (\Throwable $th) {
                        $command->error('Error to update order status to completed for invoice EC ' . $order->NumDoc);
                        $command->error($th);
                    }

                    $command->info('Invoice EC' . $order->NumDoc . ' was completed.');
                }
            }
        } else {
            $newInvoice = $this->saveNewInvoice($invoice, $address, $bmsClient->id);
        }

        $command->info('Invoice Saved ' . $invoice->NumDoc . ' For Client ' . $bmsClient->erp_client_id);
        return $newInvoice;
    }

    private function saveNewInvoice($invoice, $address, $bmsClientId, $parentId = null)
    {
        return PrimaveraInvoices::updateOrCreate(
            ['number' => $invoice->NumDoc],
            [
                'invoice_address'    => $address,
                'doc_type'           => $invoice->TipoDoc,
                'doc_series'         => $invoice->Serie,
                'payment_conditions' => $invoice->CondPag,
                'description'        => $invoice->CondPagDescricao,
                'invoice_date'       => $invoice->Data,
                'invoice_expires'    => $invoice->DataVencimento,
                'total_value'        => $invoice->TotalDocumento,
                'liquid_value'       => $invoice->TotalIliquido,
                'total_discounts'    => $invoice->TotalDescontos,
                'iva_value'          => $invoice->TotalIVA,
                'pendent_value'      => $invoice->ValorPendente,
                'primavera_client'   => $bmsClientId,
                'parent'             => $parentId
            ]
        );
    }

    public function extractDescriptionDetails(Collection $linhas)
    {
        // Define the pattern for the description string
        $pattern = '/^EC (\d{4})\/N\.º(\d+) de (\d{2}\/\d{2}\/\d{4} \d{2}:\d{2}:\d{2})$/';

        // Filter the collection to find descriptions matching the pattern
        $matchingItems = $linhas->filter(function ($item) use ($pattern) {
            return isset($item->Descricao) && preg_match($pattern, $item->Descricao);
        });

        // If no matching items found, return null
        if ($matchingItems->isEmpty()) {
            return null;
        }

        // Extract the matched parts from the descriptions
        return $matchingItems->map(function ($item) use ($pattern) {
            preg_match($pattern, $item->Descricao, $matches);
            // $matches[1] is the year, $matches[2] is the number of order, $matches[3] is the date and time
            return [
                'year' => $matches[1],
                'order_number' => $matches[2],
                'datetime' => $matches[3],
            ];
        })->first(); // Return the first matching item, you can adjust if you need all matches
    }

    /**
     * It saves the products of an invoice to the database
     * 
     * @param invoiceProducts The products that are in the invoice
     * @param newInvoice The new invoice that was just created
     * @param command The command object that is running the command.
     */
    public function saveInvoiceProducts($invoiceProducts, $newInvoice, $command)
    {
        foreach ($invoiceProducts as $product) {
            if (!!$product->Artigo) {
                $primaveraProductId = $this->primaveraProducts->where('primavera_id', $product->Artigo)->first()->id;
                $newInvoice->products()->attach($primaveraProductId);
                $command->comment('Invoice ' . $product->Artigo . ' Porduct Saved - From invoice ' . $newInvoice->number);
            }
        }
    }

    public function sendInvoice($order)
    {
        $phone =  $order->clientAddress->phone ?? $order->client->phone_1 ?? $order->client->phone_2 ?? $order->client->phone_3 ?? null;
        $products = $order->orderProducts->map(function ($product) {
            $batch = $product->productBatch->batch_number ?? null;
            if ($product->sale_price > $product->unit_price || $product->discount_value <= 0) {
                $priceUnit = $product->sale_price;
            } else {
                $priceUnit = $product->unit_price ?? 0;
            }
            $discount = $product->discount_percent ?? 0;
            $correctionPricePercent = $product->correction_price_percent ?? 0;

            $price = $priceUnit;

            if ($correctionPricePercent > 0) {
                $price = (float) number_format($priceUnit * ((100 - $correctionPricePercent) / 100), 2);
            }

            return [
                'Artigo' => $product->bmsProduct->erp_product_id,
                'Descricao' => $product->name,
                'Lote' => $batch,
                'Quantidade' => $product->conversion ?? $product->quantity,
                'Volume' => $product->volume,
                'Unidade' => $product->unit,
                'PrecoUnit' => $price,
                'CodIVA' => $product->bmsProduct->iva ?? null,
                'Desconto' => $discount,
                'DescricaoArtigo' => $product->notes ?? "",
                //'Total' => $product->total_liquid_price
            ];
        })->toArray();

        $responsePrimavera = $this->primaveraAuth->requestPrimaveraApi(
            'POST',
            '/WebApi/ApiExtended/Encomenda',
            [
                "Entidade" =>  $order->client->erp_client_id,
                "Nome" =>  $order->client->name,
                "NumPedido" =>  strval($order->id),
                "NumRequisicao" => $order->request_number,
                "NumReferencia" => $order->request_number,
                "CabecDoc" => $order->request_number,
                "Data" =>  $order->created_at,
                "DataEntrega" =>  $order->delivery_date,
                "MoradaEntrega" =>  $order->clientAddress->alternativeAddress ?? null,
                "Contacto" =>  $phone,
                "Observacoes" =>  htmlspecialchars($order->description),
                "Linhas" => $products
            ]
        );

        $newInvoice = PrimaveraInvoices::updateOrCreate(
            [
                'primavera_client'   => $order->client->primavera_table_id,
                'number'             => $responsePrimavera->NumDoc,
                'doc_type'           => $responsePrimavera->Tipodoc,
                'doc_series'         => $responsePrimavera->Serie,
            ],
            [
                'invoice_address'    => $order->delivery_address ? $order->delivery_address : " ",
                'payment_conditions' => $responsePrimavera->CondPag,
                'description'        => $responsePrimavera->CondPagDescricao ?? '',
                'invoice_date'       => date('Y-m-d', strtotime($responsePrimavera->DataDoc)),
                'invoice_expires'    => date('Y-m-d', strtotime($responsePrimavera->DataDoc)),
                'total_value'        => $responsePrimavera->TotalMerc,
                'liquid_value'       => $responsePrimavera->TotalMerc,
                'total_discounts'    => $responsePrimavera->TotalDesc,
                'iva_value'          => $responsePrimavera->TotalIva
            ]
        );

        return $newInvoice;
    }

    public function save($invoice, $address, $bmsClient)
    {
        $newInvoice = PrimaveraInvoices::updateOrCreate(
            ['number' => $invoice->NumDoc],
            [
                'invoice_address'    => $address,
                'doc_type'           => $invoice->TipoDoc,
                'doc_series'         => $invoice->Serie,
                'payment_conditions' => $invoice->CondPag,
                'description'        => $invoice->CondPagDescricao,
                'invoice_date'       => $invoice->Data,
                'invoice_expires'    => $invoice->DataVencimento,
                'total_value'        => $invoice->TotalDocumento,
                'liquid_value'       => $invoice->TotalIliquido,
                'total_discounts'    => $invoice->TotalDescontos,
                'iva_value'          => $invoice->TotalIVA,
                'primavera_client'   => $bmsClient->id,
            ]
        );

        return $newInvoice;
    }
}
