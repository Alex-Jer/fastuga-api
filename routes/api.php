<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\CustomerController;
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
    Route::delete('logout', 'logout');
});

Route::middleware('auth:api')->group(function () {
    Route::get('customers', [CustomerController::class, 'index']);
});
