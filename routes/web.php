<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

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

Route::prefix('/')->middleware('auth')->group(function() {
    Route::view('/frontend/{path?}', 'home')->where('path', '.*');
    Route::get('/user', [UserController::class, 'getUser'])->name('user');
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
});

Route::get('/login', [LoginController::class, 'login'])->name('login');
Route::post('/authenticate', [LoginController::class, 'authenticate'])->name('authenticate');

Route::prefix('/auth')->group(function() {
    Route::get('/redirect/{platform}', [LoginController::class, 'redirectProvider'])
    ->name('auth.redirect');
    Route::get('/callback/{platform}', [LoginController::class, 'providers'])
    ->name('auth.callback');
});

Route::get('/manageModules', [ModuleController::class, 'index'])->name('modulesIndex');
Route::get('/installNpm/{module}', [ModuleController::class, 'installModuleNpm'])->name('installModuleNpm');
Route::get('/removeNpm/{module}', [ModuleController::class, 'removeModuleNpm'])->name('removeModuleNpm');
Route::get('/installcomposer/{module}', [ModuleController::class, 'installModulecomposer'])->name('installModulecomposer');
Route::get('/removeComposer/{module}', [ModuleController::class, 'removeModuleComposer'])->name('removeModuleComposer');
Route::get('/enableModule/{module}', [ModuleController::class, 'enableModule'])->name('enableModule');
Route::get('/disableModule/{module}', [ModuleController::class, 'disableModule'])->name('disableModule');