<?php

use Illuminate\Http\Request;
use Modules\Dashboard\Http\Controllers\DashboardController;

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

Route::get('/dashboard/{entity}/kpis', [DashboardController::class, 'getKpis'])->middleware('auth:sanctum', 'module.permission:homepage,calls,order,orders-tracking,scheduling,products');