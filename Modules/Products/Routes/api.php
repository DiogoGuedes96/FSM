<?php

use Illuminate\Http\Request;
use Modules\Products\Http\Controllers\ProductsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/products', function (Request $request) {
//     return $request->user();
// });

Route::get('/products/all/{stock?}', [ProductsController::class, 'getAllProductsPaginate'])->middleware('auth:sanctum', 'module.permission:products,order,orders-tracking');
Route::post('/products/filter', [ProductsController::class, 'filterProducts'])->middleware('auth:sanctum', 'module.permission:products,order,orders-tracking');
Route::get('/products/categories/{withSubCategories}', [ProductsController::class, 'getProductFamilies'])->middleware('auth:sanctum', 'module.permission:products,order,orders-tracking');

Route::prefix('products')->middleware('auth:sanctum', 'module.permission:products')->group(function () {
    Route::get('/list', [ProductsController::class, 'getProducts']);
    Route::post('/update', [ProductsController::class, 'updateProducts']);
    Route::get('/units', [ProductsController::class, 'getUnits']);
    Route::get('/sync', [ProductsController::class, 'syncProducts']);
    Route::get('/batchs/sync', [ProductsController::class, 'syncBatchs']);
    Route::get('/clients/sync', [ProductsController::class, 'syncClients']);
});
