<?php

namespace Modules\Products\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Orders\Entities\OrderProducts;
use Nwidart\Modules\Facades\Module;
use Modules\Products\Entities\BmsProductImages;
use Modules\Products\Entities\BmsProductPrices;
use Modules\Products\Entities\BmsPoductCategories;
use Modules\Products\Entities\BmsPoductSubCategories;


class Products extends Model
{
    use HasFactory;

    protected $table = 'bms_products';
    protected $primaryKey = 'id';

    protected $fillable = [
        "name",
        "avg_price",
        "last_price",
        "sell_unit",
        "current_stock",
        "stock_mov",
        "category_code",
        "sub_category_code",
        "pvp_1",
        "pvp_2",
        "pvp_3",
        "pvp_4",
        "pvp_5",
        "pvp_6",
        "erp_product_id",
        "erp_product_id",
        "primavera_table_id",
        "iva",
    ];

    protected static function newFactory()
    {
        return \Modules\Primavera\Database\factories\PrimaveraFactory::new();
    }

    /**
     * It returns the orderProduct associated with the current product.
     * 
     * @return The orderProduct method is returning the relationship between the client and the order
     * product.
     */
    public function orderProduct()
    {
        if (!Module::has('orders') && Module::isDisabled('orders')){
            return false;
        }
        return $this->hasMany(OrderProducts::class, 'bms_product', 'id');
    }

    public function images()
    {
        return $this->hasMany(BmsProductImages::class, 'bms_product_id');
    }

    public function prices()
    {
        return $this->hasMany(BmsProductPrices::class, 'bms_product_id');
    }

    public function category()
    {
        return $this->belongsTo(BmsPoductCategories::class);
    }

    public function subCategory()
    {
        return $this->belongsTo(BmsPoductSubCategories::class);
    }

    public function batches()
    {
        return $this->hasMany(BmsProductsBatch::class, 'bms_product_id', 'id');
    }
}
