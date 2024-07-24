<?php

namespace Modules\Primavera\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PrimaveraProductsBatch extends Model
{
    use HasFactory;

    protected $table = 'primavera_products_batch';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'batch_number',
        'description',
        'quantity',
        'expiration_date',
        'primavera_product_id',
        'active'
    ];

    public function products()
    {
        return $this->hasMany(Products::class);
    }

    public function primaveraProduct()
    {
        return $this->belongsTo(primaveraProduct::class, 'primavera_product_id', 'id');
    }
}
