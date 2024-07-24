<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('orders')->middleware('auth')->group(function() {
    Route::prefix('newOrder')->group(function () {
        Route::get('/', 'OrdersController@index');
        Route::get('/checkout', 'OrdersController@checkout');
        Route::get('/summary',  'OrdersController@summary');
    });

    Route::prefix('directSale')->group(function () {
        Route::get('/', 'OrdersController@directSale');
        Route::get('/checkout', 'OrdersController@directSaleCheckout');
        Route::get('/summary',  'OrdersController@directSaleSummary');
    });

    Route::get('/tracking',  'OrdersController@tracking');
    Route::get('/{order}',  'OrdersController@orderById');
});