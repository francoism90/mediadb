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

Route::name('api.')->namespace('Api')->prefix('v1')->group(function () {
    // API
    Route::middleware('doNotCacheResponse')->group(function () {
        Route::get('/', ['uses' => 'HomeController', 'as' => 'home']);
    });

    // Auth
    Route::middleware('doNotCacheResponse')->name('auth.')->prefix('auth')->namespace('Auth')->group(function () {
        Route::middleware('guest')->post('login', ['uses' => 'AuthController@login', 'as' => 'login']);
        Route::middleware('auth:sanctum')->get('logout', ['uses' => 'AuthController@logout', 'as' => 'logout']);
        Route::middleware('auth:sanctum')->get('me', ['uses' => 'AuthController@me', 'as' => 'user']);
    });

    // Resources
    Route::middleware('auth:sanctum')->name('resource.')->namespace('Resources')->group(function () {
        Route::apiResource('channel', 'ChannelController')->only(['index', 'show', 'update', 'destroy']);
        Route::apiResource('media', 'MediaController')->only(['index', 'store', 'show', 'update', 'destroy']);
        Route::apiResource('playlist', 'PlaylistController')->only(['index', 'store', 'show', 'update', 'destroy']);
        Route::apiResource('tags', 'TagController')->only(['index']);
    });

    // Media
    Route::middleware('doNotCacheResponse')->name('media.')->prefix('media')->namespace('Media')->group(function () {
        Route::middleware('signed')->get('asset/{media}/{user}/{conversion}/{version?}', ['uses' => 'DownloadController', 'as' => 'download'])->where('version', '[0-9]+');
    });
});
