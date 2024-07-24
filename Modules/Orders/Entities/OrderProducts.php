<?php

namespace Modules\Orders\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Products\Entities\BmsProductsBatch;
use Modules\Products\Entities\Products;
use Nwidart\Modules\Facades\Module;

class OrderProducts extends Model
{
    use HasFactory;

    protected $table = 'order_products';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'quantity',
        'unit',
        'unit_price',
        'total_liquid_price',
        'order_id',
        'bms_product',
        'correction_price_percent',
        'discount_percent',
        'discount_value',
        'sale_unit',
        'sale_price',
        'notes',
        'conversion', 
        'volume', 
        'unavailability',
        'bms_product_batch'
    ];
    
    protected static function newFactory()
    {
        return \Modules\Orders\Database\factories\OrderProductsFactory::new();
    }

    /**
     * Returns the order associated with the current orderProduct
     * 
     * @return The order that the order item belongs to.
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'id', 'order_id');
    }

    /**
     * It returns the product that is associated with the BMS product.
     * 
     * @return A relationship between the current model and the Products model.
     */
    public function bmsProduct()
    {
        if (!Module::has('products') && Module::isDisabled('products')){
            return false;
        }

        return $this->hasOne(Products::class, 'id', 'bms_product');
    }

    public function productBatch()
    {
        if (!Module::has('products') && Module::isDisabled('products')){
            return false;
        }
        return $this->hasOne(BmsProductsBatch::class, 'id', 'bms_product_batch');
    }
}
