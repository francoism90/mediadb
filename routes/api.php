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
        Route::apiResource('collection', 'CollectionController')->only(['index', 'store', 'show', 'update', 'destroy']);
        Route::apiResource('media', 'MediaController')->only(['index', 'store', 'show', 'update', 'destroy']);
        Route::apiResource('tags', 'TagController')->only(['index']);
    });

    // Media
    Route::middleware('doNotCacheResponse')->name('media.')->prefix('media')->namespace('Media')->group(function () {
        // Authenticated
        Route::middleware('auth:sanctum')->patch('/{media}/frameshot', ['uses' => 'FrameshotController', 'as' => 'frameshot']);
        Route::middleware('auth:sanctum')->put('/{media}/save', ['uses' => 'SaveController', 'as' => 'save']);

        // Signed URLs
        Route::middleware(['signed', 'cache.headers:public;max_age=604800;etag'])->get('/asset/{media}/{user}/{name}', ['uses' => 'AssetController', 'as' => 'asset']);
        Route::middleware('signed')->get('/download/{media}/{user}', ['uses' => 'DownloadController', 'as' => 'download']);
        Route::middleware('signed')->get('/sprite/{media}/{user}', ['uses' => 'SpriteController', 'as' => 'sprite']);
        Route::middleware('signed')->get('/stream/{media}/{user}', ['uses' => 'StreamController', 'as' => 'stream']);
    });
});
