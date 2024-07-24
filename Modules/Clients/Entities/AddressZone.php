<?php

namespace Modules\Clients\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Orders\Entities\Order;
use Nwidart\Modules\Facades\Module;

class AddressZone extends Model
{
    use HasFactory;

    protected $table = 'bms_address_zone';
    protected $primaryKey = 'id';

    protected $fillable = [
        "zone",
        "description",
    ];

    protected static function newFactory()
    {
        return \Modules\Primavera\Database\factories\PrimaveraFactory::new();
    }

    public function address()
    {
        return $this->belongsTo(ClientAddress::class, 'bms_address_zone_id', 'id');
    }

    public function orders()
    {
        return $this->belongsTo(Order::class, 'bms_address_zone_id', 'id');
    }
}
