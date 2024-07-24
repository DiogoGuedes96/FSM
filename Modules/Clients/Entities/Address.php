<?php

namespace Modules\Clients\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Nwidart\Modules\Facades\Module;

class Address extends Model
{
    use HasFactory;

    protected $table = 'bms_address';
    protected $primaryKey = 'id';

    protected $fillable = [
        "address",
        "postal_code",
        "postal_code_address",
        "selected_delivery_address",
        "selected_invoice_address",
        "bms_address_zone_id",
        "bms_client",
    ];

    protected static function newFactory()
    {
        return \Modules\Primavera\Database\factories\PrimaveraFactory::new();
    }

    /**
     * > This function returns all the clients associated with the current address
     *
     * @return A Modules\Clients\Entities\Client;
     */
    public function clients(){
        if (!Module::has('clients') && !Module::isEnabled('clients')){
            return false;
        }

        return $this->belongsTo(Clients::class, 'id', 'bms_client');
    }

    public function zone(){
        return $this->hasOne(AddressZone::class, 'id', 'bms_address_zone_id');
    }
}
