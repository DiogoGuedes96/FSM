<?php

namespace Modules\Primavera\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Primavera\Entities\PrimaveraProductsBatch;

class PrimaveraProducts extends Model
{
    use HasFactory;

    protected $table = 'primavera_products';
    protected $primaryKey = 'id';

    protected $fillable = [
        "primavera_id",
        "name",
        "avg_price",
        "last_price",
        "sell_unit",
        "current_stock",
        "stock_mov",
        "family",
        "sub_family",
        "pvp_1",
        "pvp_2",
        "pvp_3",
        "pvp_4",
        "pvp_5",
        "pvp_6",
        "iva",
    ];

    protected static function newFactory()
    {
        return \Modules\Primavera\Database\factories\PrimaveraFactory::new();
    }

    /**
     * > This function returns a collection of all the invoices that have this product
     * 
     * @return A collection of all the invoices that have this product.
     */
    public function invoices() 
    {
        return $this->belongsToMany(PrimaveraInvoices::class, 'invoices_have_products', 'product_id', 'invoice_id')->withTimestamps();
    }

    public function batches()
    {
        return $this->hasMany(PrimaveraProductsBatch::class, 'primavera_product_id', 'id');
    }
}
