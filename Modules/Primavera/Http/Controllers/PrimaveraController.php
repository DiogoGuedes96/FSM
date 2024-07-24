<?php

namespace Modules\Primavera\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Clients\Services\ClientsService;
use Modules\Primavera\Services\PrimaveraClientsService;
use Modules\Primavera\Services\PrimaveraProductsBatchService;

class PrimaveraController extends Controller
{
    /**
     * @var PrimaveraClientsService
     */
    private $primaveraService;

    /**
     * @var ClientsService
     */
    private $clientsService;
    private $primaveraProductBatchService;

    public function __construct()
    {
        $this->primaveraService = new PrimaveraClientsService();
        $this->clientsService = new ClientsService();
        $this->primaveraProductBatchService = new PrimaveraProductsBatchService();
    }

    public function getClients(Request $request)
    {
        try {
            $data = $this->primaveraService->getAllClients();

            return response()->json([
                'message' => 'All clients from primavera.',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], $e->getCode()  ?? 500);
        };
    }

    public function getClientsByPhone(Request $request)
    {
        try {
            $data = $this->clientsService->getClientsByPhone($request->input('phones'));

            return response()->json([
                'message' => 'List clients from primavera by phone.',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], $e->getCode()  ?? 500);
        };
    }

    public function syncProductBatches(){
        try {
            $this->primaveraProductBatchService->syncProductBatches();
        } catch (\Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }
}
