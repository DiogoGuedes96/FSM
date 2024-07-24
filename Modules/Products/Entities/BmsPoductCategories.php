<?php

namespace Modules\Products\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Products\Entities\BmsPoductSubCategories;
use Modules\Products\Entities\Products;


class BmsPoductCategories extends Model
{
    use HasFactory;

    protected $table = 'bms_products_categories';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'name',
        'erp_category_code',
        'group'
    ];

    public function products()
    {
        return $this->hasMany(Products::class);
    }

    public function subCategories()
    {
        return $this->hasMany(BmsPoductSubCategories::class, 'category', 'id');
    }
}
