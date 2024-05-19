<?php

use App\Http\Controllers\AuthController;
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

Route::get('/', function () {
    return view('welcome');
});

Route::controller(AuthController::class)->prefix('oauth/google')->group(function () {
    Route::get('/redirect', 'oauthGoogleRedirect')->name('google-redirect');
    Route::get('/callback', 'oauthGoogleCallback')->name('google-callback');
});
