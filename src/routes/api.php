<?php

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

Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', 'AuthController@login');
    Route::post('register', 'AuthController@register');
    Route::get('refresh', 'AuthController@refresh')->middleware('refresh_token');
    Route::delete('logout', 'AuthController@logout')->middleware('refresh_token');

    Route::group([
        'middleware' => ['auth:api']
    ], function () {
        Route::get('email/verify/{id}/{hash}', 'EmailVerificationController@verify')->name('verification.verify');
        Route::get('email/resend', 'EmailVerificationController@resend')->name('verification.resend');
    });
});
