<?php

namespace Modules\Dashboard\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Dashboard\Services\DashboardService;

class DashboardController extends Controller
{
    private $dashboardService;

    private $entities = [
        'invoices',
        'clients',
        'calls',
        'orders'
    ];

    public function __construct()
    {
        $this->dashboardService = new DashboardService();
    }

    public function index()
    {
        return view('dashboard::index');
    }

    public function getKpis(Request $request)
    {
        try {
            if (!in_array($request->entity, $this->entities)) {
                throw new \Exception('Entity not found', 404);
            }

            return $this->dashboardService->{'get'. ucfirst($request->entity) .'Kpis'}($request->period ?? 'now');
        } catch (\Exception $error) {
            return response()->json(['message' => $error->getMessage()], $error->getCode());
        }
    }
}
