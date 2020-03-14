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

Route::name('api.')->namespace('Api')->group(function () {
    // API
    Route::middleware('doNotCacheResponse')->group(function () {
        Route::get('/', ['uses' => 'HomeController', 'as' => 'home']);
    });

    // Auth
    Route::middleware('doNotCacheResponse')->name('auth.')->prefix('auth')->namespace('Auth')->group(function () {
        Route::middleware('guest')->post('login', ['uses' => 'AuthController@login', 'as' => 'login']);
        Route::middleware('auth:airlock')->get('logout', ['uses' => 'AuthController@logout', 'as' => 'logout']);
        Route::middleware('auth:airlock')->get('user', ['uses' => 'AuthController@me', 'as' => 'user']);
    });

    // Resources
    Route::middleware('auth:airlock')->name('resource.')->namespace('Resources')->group(function () {
        Route::apiResource('collect', 'CollectionController')->only(['index', 'show', 'update', 'destroy']);
        Route::apiResource('media', 'MediaController')->only(['index', 'store', 'show', 'update', 'destroy']);
        Route::apiResource('tags', 'TagController')->only(['index']);
        Route::apiResource('user', 'UserController')->only(['index', 'show']);
    });

    // Assets
    Route::middleware('doNotCacheResponse')->name('asset.')->prefix('asset')->namespace('Assets')->group(function () {
        Route::middleware('signed')->get('download/{media}/{user}/{version?}', ['uses' => 'DownloadController', 'as' => 'download'])->where('version', '[0-9]+');
        Route::middleware('signed')->get('placeholder/{media}/{user}/{version?}', ['uses' => 'PlaceholderController', 'as' => 'placeholder'])->where('version', '[0-9]+');
        Route::middleware('signed')->get('preview/{media}/{user}/{version?}', ['uses' => 'PreviewController', 'as' => 'preview'])->where('version', '[0-9]+');
        Route::middleware('auth:airlock')->get('thumbnail/{media}/{offset}', ['uses' => 'ThumbnailController', 'as' => 'thumbnail'])->where('offset', '[0-9]+');
    });
});
