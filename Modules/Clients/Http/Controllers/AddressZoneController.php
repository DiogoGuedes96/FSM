<?php

namespace Modules\Clients\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Clients\Entities\AddressZone;
use Modules\Clients\Http\Requests\AddressZoneRequest;
use Modules\Clients\Services\AddressZoneService;
use Throwable;

class AddressZoneController extends Controller
{
    /** @var AddressZoneService */
    private $addressZoneService;

    public function __construct() {
        $this->addressZoneService = new AddressZoneService();
    }

    public function getAllZones(){
        try {
            $zones = $this->addressZoneService->getAllZones();

            return $zones;
        } catch (Throwable $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function addZone(AddressZoneRequest $request)
    {
        try {
            $this->addressZoneService->addZone($request);
        } catch (Throwable $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
