<?php

namespace Modules\Products\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BmsProductPrices extends Model
{
    use HasFactory;

    protected $table = 'bms_product_prices';
    protected $primaryKey = 'id';

    protected $fillable = [
        "bms_product_id",
        "unit",
        "price",
        "active",
        "default",
        "iva",
    ];
    
    protected static function newFactory()
    {
        return \Modules\Products\Database\factories\BmsProductPricesFactory::new();
    }
}
