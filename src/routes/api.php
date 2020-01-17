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

Route::middleware('api')->name('api.')->namespace('Api')->group(function () {
    Route::middleware('doNotCacheResponse')->name('auth.')->prefix('auth')->namespace('Auth')->group(function () {
        Route::post('login', ['uses' => 'AuthController@login', 'as' => 'login']);
        Route::post('logout', ['uses' => 'AuthController@logout', 'as' => 'logout']);
        Route::get('user', ['uses' => 'AuthController@user', 'as' => 'user']);
        Route::get('refresh', ['uses' => 'AuthController@refresh', 'as' => 'refresh']);
    });

    Route::middleware('jwt.auth')->name('resource.')->namespace('Resource')->group(function () {
        Route::apiResource('media', 'MediaController')->only(['index', 'store', 'show', 'update', 'destroy']);
        Route::apiResource('tag', 'TagController')->only(['index']);
        Route::apiResource('user', 'UserController')->only(['index', 'show']);
    });

    Route::middleware(['doNotCacheResponse', 'signed'])->get('asset/{media}/{user}/{type?}/{version?}', ['uses' => 'ShowMediaConversion', 'as' => 'asset.show']);
});
