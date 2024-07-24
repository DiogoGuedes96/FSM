<?php

namespace Modules\Clients\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Nwidart\Modules\Facades\Module;

class ClientAddress extends Model
{
    use HasFactory;

    protected $table = 'bms_client_address';

    protected $fillable = [
        'bms_client',
        'primavera_id',
        'iecCode',
        'iecExemptionCode',
        'localCode',
        'postalCode',
        'postalCodeLocation',
        'district',
        'eGAR_APACode',
        'eGAR_IsExempt',
        'eGAR_PGLNumber',
        'eGAR_ProducerType',
        'fax',
        'iecExempt',
        'location',
        'address',
        'address2',
        'alternativeAddress',
        'shippingAddress',
        'billingAddress',
        'defaultAddress',
        'country',
        'phone',
        'phone_2',
        'senderType',
        'lastUpdateVersion',
        'bms_address_zone_id',
        'is_contact'
    ];

    protected $casts = [
        'is_contact' => 'boolean', 
    ];

    public function clients()
    {
        return $this->hasOne(Clients::class, 'id', 'bms_client');
    }

    public function zone()
    {
        return $this->hasOne(AddressZone::class, 'id', 'bms_address_zone_id');
    }
}
