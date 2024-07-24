<?php

use Illuminate\Http\Request;
use Modules\Clients\Http\Controllers\ClientsController;
use Modules\Clients\Http\Controllers\AddressZoneController;

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
Route::prefix('clients')->middleware('auth:sanctum', 'module.permission:calls,order,orders-tracking,scheduling,products')->group(function() {
    Route::post('/filter', [ClientsController::class, 'filterClients']);
});

Route::prefix('addresses')->middleware('auth:sanctum', 'module.permission:calls,order,orders-tracking,scheduling,products')->group(function() {
    Route::post('/zone', [AddressZoneController::class, 'addZone']);
    Route::get('/zones/all', [AddressZoneController::class, 'getAllZones']);
});
