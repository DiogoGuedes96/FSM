<?php
use Modules\Calls\Http\Controllers\AsteriskController;
use Modules\Calls\Http\Controllers\CallsController;
use Modules\Orders\Http\Controllers\OrdersController;

Route::get('/calls/orders/{client}',[CallsController::class, 'getOrdersDetailsByClientId'])
    ->middleware('auth:sanctum', 'module.permission:calls,order,orders-tracking');

Route::prefix('calls')->middleware('auth:sanctum', 'module.permission:calls')->group(function () {
    Route::get('/in-progress', [CallsController::class, 'inProgress']);
    Route::get('/hangup', [CallsController::class, 'hangup']);
    Route::get('/missed', [CallsController::class, 'missed']);
    Route::put('/closeGhostCall/{call}', [CallsController::class, 'closeGhostCall']);
    Route::get('/count/missed', [CallsController::class, 'countMissed']);
    Route::put('/set-viewed', [CallsController::class, 'setViewed']);
});

Route::prefix('client')->middleware('auth:sanctum', 'module.permission:calls')->group(function () {
    Route::get('/filteredOrders/{bms_client}', [OrdersController::class, 'getClientFilteredOrders']);
});

Route::prefix('asterisk')->middleware('auth:sanctum', 'module.permission:calls')->group(function () {
    Route::post('/view', [AsteriskController::class, 'index']);
    Route::post('/update', [AsteriskController::class, 'update']);
});