<?php

namespace Modules\Products\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BmsProductImages extends Model
{
    protected $table = 'bms_product_images';
    protected $primaryKey = 'id';

    protected $fillable = [
        "bms_product_id",
        "image_url",
        "active"
    ];
    
    protected static function newFactory()
    {
        return \Modules\Products\Database\factories\BmsProductImagesFactory::new();
    }
}
