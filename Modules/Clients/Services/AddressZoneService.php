<?php

namespace Modules\Clients\Services;

use Exception;
use Modules\Clients\Entities\Address;
use Modules\Clients\Entities\AddressZone;

class AddressZoneService
{
    private $addressZone;
    public function __construct() {
        $this->addressZone = new AddressZone();
     }

    public function addZone($description, $code = null): AddressZone {
        if ($code == null) {
            $highestCode = $this->addressZone->max('zone');
            if ($highestCode > 9000) {
                $code = $highestCode + 1;
            } else {
                $code = $highestCode + 9000;
            }
        }

        $newZone = AddressZone::create([
            'zone' => $code,
            'description' => $description,
        ]);

        return $newZone;
    }

    public function findOrCreateZone($zone): int {
        if (isset($zone['id'])) {
            $zoneFound = $this->addressZone->where('zone', $zone['id'])->first();
            if (!empty($zoneFound) && $zoneFound->description === $zone['value']) return $zone->id;
        }

        if (isset($zone['value'])) {
            $zoneFound = $this->addressZone->where('description', $zone['value'])->first();
            if (!empty($zoneFound)) return $zoneFound->id;
        }

        $newZone = $this->addZone($zone['value']);
        return $newZone->id;
    }

    public function getAllZones() {
        $zones = $this->addressZone->where('zone', '<>', '')->where('description', '<>', '')->orderBy('zone', 'asc')->get();

        if (empty($zones)) throw new Exception('No zones found!', 500);
        
        return $zones;
    }
}
