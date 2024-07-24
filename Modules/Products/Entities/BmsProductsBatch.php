<?php

namespace Modules\Products\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Orders\Entities\OrderProducts;
use Modules\Products\Entities\Products;
use Nwidart\Modules\Facades\Module;


class BmsProductsBatch extends Model
{
    use HasFactory;

    protected $table = 'bms_products_batch';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'batch_number',
        'erp_product_batch_id',
        'active',
        'quantity',
    ];

    public function products()
    {
        return $this->hasMany(Products::class);
    }

    public function bmsProduct()
    {
        return $this->belongsTo(Products::class, 'bms_product_id', 'id');
    }

    public function orderProduct()
    {
        if (!Module::has('orders') && Module::isDisabled('orders')) {
            return false;
        }
        return $this->belongsTo(OrderProducts::class, 'bms_product_batch', 'id');
    }
}
