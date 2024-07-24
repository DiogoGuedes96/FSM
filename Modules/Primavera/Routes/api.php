<?php

use Modules\Primavera\Http\Controllers\PrimaveraController;

Route::prefix('products')->middleware('auth:sanctum', 'module.permission:products')->group(function() {
    Route::get('/syncProductBatches', [PrimaveraController::class, 'syncProductBatches']);
});
