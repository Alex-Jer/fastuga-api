<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\CustomerController;
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
    //Route::get('scopes', 'scopes')->middleware('auth:api');
});

Route::prefix('users/me')->group(function () {
    Route::get('/email/verify', function () {
        //  Only used when a logged in user tries to access a "verified" middleware protected route
        // (currently unused)
        return response(['message' => 'User\'s email is not verified. Cannot complete requested action.'], 403);
    })->name('verification.notice');

    /*Route::get('/forgot-password', 'forgotPassword')->name('password.email');
    Route::post('/reset-password/{token}', function ($token) {
        return view('auth.reset-password', ['token' => $token]);
    })->name('password.reset');*/
});

Route::prefix('users')->controller(UserController::class)->middleware('auth:api')->group(function () {
    Route::prefix('me')->group(function () {
        Route::get('/', 'showMe');
        Route::put('/', 'updateMe')->name('update-employee-profile');
        Route::patch('/password', 'changePassword');
        Route::patch('/email', 'changeEmail');

        Route::patch('/email/verify', 'verifyMyEmail')->name('verification.send');
        Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
            $request->fulfill();

            return response(['message' => 'Your email was successfully verified'], 200);
        })->middleware('signed')->name('verification.verify');
    });


    /*Route::put('/me', 'updateMe');*/
    Route::middleware('scope:manage-users')->group(function () {
        Route::post('/', 'store'); //Register a new employee
        Route::get('/', 'allUsers');
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
        //Route::get('/', 'allCostumers'); //TODO: is this needed?
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
