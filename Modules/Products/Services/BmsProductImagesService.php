<?php

namespace Modules\Products\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\Products\Entities\BmsProductImages;

class BmsProductImagesService
{
    public function saveProductImage($image64)
    {
        $imageData = $image64;
        $imageData = str_replace('data:image/png;base64,', '', $imageData);
        $imageData = str_replace(' ', '+', $imageData);
        $imageName = 'product_image_' . time() . '.png';
        Storage::disk('public')->put($imageName, base64_decode($imageData));

        return Storage::disk('public')->url($imageName);
    }
}
