<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
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
    Route::post('/register', 'register');
    Route::post('/login', 'authenticate');

    Route::prefix('oauth')->group(function () {
        Route::get('/register', function () {
            return redirect()->route('google-redirect');
        });
    });

    Route::middleware('auth:sanctum')->post('/logout', 'logout');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::middleware('role:admin')->group(function () {
        Route::resource('categories', CategoryController::class);
    });

    Route::resource('products', ProductController::class);
});
