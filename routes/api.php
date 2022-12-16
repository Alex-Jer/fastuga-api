<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\CustomerController;
use App\Http\Controllers\api\ProductController;
use App\Http\Controllers\api\UserController;
use App\Http\Resources\CustomerResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::delete('logout', 'logout')->middleware('auth:api');
    Route::get('test', 'test')->middleware('auth:api');
    //Route::get('scopes', 'scopes')->middleware('auth:api');
});

Route::prefix('users')->controller(UserController::class)->middleware('auth:api')->group(function () {
    Route::get('/me', 'showMe');
    /*Route::put('/me', 'updateMe');*/
    Route::middleware('scope:manage-users')->group(function () {
        Route::post('/', 'store'); //Register a new employee
        Route::get('/', 'allUsers');
        Route::get('/{user}', 'show');
        Route::put('/{user}', 'update');
        Route::delete('/{user}', 'destroy');
        Route::put('/{user}/block', 'block');
        Route::put('/{user}/unblock', 'unblock');
    });
});
Route::prefix('customers')->controller(CustomerController::class)->group(function () {
    Route::post('/', 'store')->name('register-client'); //Register clients
    Route::middleware('auth:api')->group(function () {
        Route::get('/', 'allCostumers');
    });
});
Route::prefix('products')->controller(ProductController::class)->group(function () {
    Route::get('/', 'menu');
    Route::get('/{product}', 'show');
    Route::middleware(['auth:api', 'scope:manage-products'])->group(function () {
        Route::post('/', 'store');
        Route::put('/{product}', 'update');
        Route::delete('/{product}', 'destroy');
    });
});
