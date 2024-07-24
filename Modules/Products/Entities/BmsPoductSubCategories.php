<?php

namespace Modules\Products\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Products\Entities\BmsPoductCategories;
use Modules\Products\Entities\Products;

class BmsPoductSubCategories extends Model
{
    use HasFactory;

    protected $table = 'bms_products_sub_categories';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'name',
        'erp_sub_category_code',
        'category'
    ];

    public function products()
    {
        return $this->hasMany(Products::class);
    }

    public function category()
    {
        return $this->belongsTo(BmsPoductCategories::class, 'category', 'id');
    }
}
