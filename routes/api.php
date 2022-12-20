<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\CustomerController;
use App\Http\Controllers\api\OrderController;
use App\Http\Controllers\api\ProductController;
use App\Http\Controllers\api\UserController;
use App\Http\Resources\CustomerResource;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
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

    /*Route::get('scopes', 'scopes')->middleware('auth:api');
    Route::get('allScopes', 'testAllScopes')->middleware('auth:api');*/
});

Route::prefix('users')->controller(UserController::class)->middleware('auth:api')->group(function () {
    Route::prefix('me')->group(function () {
        Route::get('/', 'showMe');
        Route::put('/', 'updateMe')->name('update-employee-profile');
        Route::patch('/password', 'changePassword');
        Route::patch('/email', 'changeEmail');
    });


    Route::middleware('scope:manage-users')->group(function () {
        Route::post('/', 'store'); //Register a new employee

        Route::get('/', 'allUsers');
        Route::get('/types', 'allTypes');

        Route::get('/{user}', 'show');
        Route::put('/{user}', 'update');
        Route::delete('/{user}', 'destroy');
        Route::patch('/{user}/block', 'block');
        Route::patch('/{user}/unblock', 'unblock');
    });
});

Route::prefix('customers')->controller(CustomerController::class)->group(function () {
    Route::post('/', 'store')->name('register-customer'); //Register clients
    Route::middleware('auth:api')->group(function () {
        Route::put('/me', 'updateCustomer')->name('update-customer-profile');
        Route::get('/', 'allCostumers'); //TODO: is this needed?
    });
});

Route::prefix('products')->controller(ProductController::class)->group(function () {
    Route::get('/', 'menu');
    Route::get('/types', 'allTypes');
    Route::get('/{product}', 'show');
    Route::middleware(['auth:api', 'scope:manage-products'])->group(function () {
        Route::post('/', 'store');
        Route::put('/{product}', 'update');
        Route::delete('/{product}', 'destroy');
    });
});

Route::prefix('orders')->controller(OrderController::class)->group(function () {
    Route::post('/', 'store');
    Route::get('/ready', 'ordersReady');

    Route::get('/me', 'myOrders')->middleware('auth:api');

    Route::get('/preparing', 'ordersPreparing')->middleware(['auth:api', 'scope:complete-orders']); //Apenas os servers vao necessitar de ver esta informação por isso complete-orders como scope é válido
    Route::get('/preparable-dishes', 'preparableDishes')->middleware(['auth:api', 'scope:prepare-dishes']);

    Route::middleware('auth:api')->group(function () {
        //Route::get('/', 'allOrders');
        Route::prefix('/{order}')->middleware('auth:api')->group(function () {
            Route::get('/', 'show')->middleware('scope:view-orders');
            Route::patch('/cancel', 'cancel')->middleware('scope:cancel-orders');
            Route::patch('/dish/{item}/prepare', 'prepareDish')->middleware('scope:prepare-dishes');
            Route::patch('/dish/{item}/finish', 'finishDish')->middleware('scope:prepare-dishes');
            Route::patch('/finish', 'finishOrder')->middleware('scope:complete-orders');
            Route::patch('/deliver', 'deliverOrder')->middleware('scope:deliver-orders');
        });
    });
});
