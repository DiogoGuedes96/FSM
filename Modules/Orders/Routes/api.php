<?php

use Modules\Orders\Http\Controllers\OrdersController;

Route::prefix('orders')->middleware('auth:sanctum')->group(function () {
    Route::get('/all', [OrdersController::class, 'getAllOrders'])->middleware('module.permission:order,orders-tracking');
    Route::get('/{order_id}', [OrdersController::class, 'getOrderById'])->middleware('module.permission:order,orders-tracking');
    Route::get('/products/{clientId}',  [OrdersController::class, 'getProductsByOrders'])->middleware('module.permission:order');
    Route::post('/client/mostBought/{bms_client}', [OrdersController::class, 'getProductsMostBoughtProductsByClient'])->middleware('module.permission:order');
    Route::post('/client/lessBought/{bms_client}', [OrdersController::class, 'getLessBoughtProductsByClient'])->middleware('module.permission:order');
    Route::post('/saveNewOrder', [OrdersController::class, 'store'])->middleware('module.permission:order');
    Route::post('/setPriorityOrder/{order_id}', [OrdersController::class, 'setPriorityOrder'])->middleware('module.permission:order');
    Route::post('/update-order/{id}', [OrdersController::class, 'update'])->middleware('module.permission:order,orders-tracking');
    Route::post('/direct-sale/update-order/{id}', [OrdersController::class, 'updateDirectSale'])->middleware('module.permission:order,orders-tracking');
    Route::post('/duplicate-order/{id}', [OrdersController::class, 'duplicate'])->middleware('module.permission:order,orders-tracking');
    Route::post('/update/{id}', [OrdersController::class, 'updateOrderAndAddProducts'])->middleware('module.permission:order,orders-tracking');
    Route::post('/forkOrder', [OrdersController::class, 'forkOrder'])->middleware('module.permission:orders-tracking');
    Route::post('/removeProducts', [OrdersController::class, 'removeProductsFromOrder'])->middleware('module.permission:orders-tracking');
    Route::put('/{order}/setStatus/{status}', [OrdersController::class, 'setOrderStatus'])->middleware('module.permission:order,orders-tracking');
    Route::get('/getOrdersByStatus/{status}/{zoneId?}', [OrdersController::class, 'getOrdersByStatus'])->middleware('module.permission:orders-tracking');
    Route::get('/searchOrdersByInput/{input}', [OrdersController::class, 'searchOrdersByInput'])->middleware('module.permission:orders-tracking');
    Route::put('/{order}/pending', [OrdersController::class, 'updateOrderPending'])->middleware('module.permission:orders-tracking');
    Route::get('/invoice/{order}', [OrdersController::class, 'generatePDFOrder'])->middleware('module.permission:orders-tracking');
    Route::put('/validateStock/{order}', [OrdersController::class, 'validateStock'])->middleware('module.permission:order,orders-tracking');
    Route::put('{orderId}/setNumberInvoice', [OrdersController::class, 'setNumberInvoice'])->middleware('module.permission:order,orders-tracking');
    Route::post('/change-date/{id}', [OrdersController::class, 'changeDeliveryDate'])->middleware('module.permission:order,orders-tracking');
    Route::put('/gerenate-invoice/{order}', [OrdersController::class, 'generateInvoice'])->middleware('module.permission:order,orders-tracking');
    

    Route::prefix('cache')->group(function () {
        Route::get('/blocked', [OrdersController::class, 'getAllCachedBlocked'])->middleware('module.permission:order,orders-tracking');
        Route::post('/add', [OrdersController::class, 'setOrderBlockedOnCache'])->middleware('module.permission:order,orders-tracking');
        Route::post('/replaceUser', [OrdersController::class, 'replaceUserOnOrderInCache'])->middleware('module.permission:order,orders-tracking');
        Route::post('/replaceOrder', [OrdersController::class, 'replaceOrderBlockedOnCache'])->middleware('module.permission:order,orders-tracking');
        Route::put('/remove', [OrdersController::class, 'removeOrderBlockedFromCache'])->middleware('module.permission:order,orders-tracking');
        Route::put('/remove/all', [OrdersController::class, 'removeAllOrdersBlockedFromCache'])->middleware('module.permission:order,orders-tracking');
    });
});
