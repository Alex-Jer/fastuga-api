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

/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::delete('logout', 'logout')->middleware('auth:api');
    //Route::get('scopes', 'scopes')->middleware('auth:api');
});

Route::middleware('auth:api')->group(function () {
    Route::prefix('users')->controller(UserController::class)->group(function () {
        Route::get('/', 'allUsers');
    });
    Route::prefix('customers')->controller(CustomerController::class)->group(function () {
        Route::get('/', 'allCostumers');
    });
    Route::prefix('products')->controller(ProductController::class)->group(function () {
        Route::get('/', 'menu');
        Route::post('/', 'store');
        Route::put('/{product}', 'update');
        Route::delete('/{product}', 'destroy');
    });
});
