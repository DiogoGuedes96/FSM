<?php

namespace Modules\Primavera\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AddressPrimavera extends Model
{
    use HasFactory;

    protected $table = 'primavera_address';

    protected $fillable = [
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
        'is_contact'
    ];

    protected $casts = [
        'is_contact' => 'boolean', 
    ];
    
    protected static function newFactory()
    {
        return \Modules\Primavera\Database\factories\AddressPrimaveraFactory::new();
    }
}
