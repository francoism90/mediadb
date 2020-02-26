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
    // Auth
    Route::middleware('doNotCacheResponse')->name('auth.')->prefix('auth')->namespace('Auth')->group(function () {
        Route::post('login', ['uses' => 'AuthController@login', 'as' => 'login']);
        Route::post('logout', ['uses' => 'AuthController@logout', 'as' => 'logout']);
        Route::get('user', ['uses' => 'AuthController@user', 'as' => 'user']);
        Route::get('refresh', ['uses' => 'AuthController@refresh', 'as' => 'refresh']);
    });

    // Resources
    Route::middleware('jwt.auth')->name('resource.')->namespace('Resource')->group(function () {
        Route::apiResource('media', 'MediaController')->only(['index', 'store', 'show', 'update', 'destroy']);
        Route::apiResource('tags', 'TagController')->only(['index']);
        Route::apiResource('user', 'UserController')->only(['index', 'show']);
    });

    // Assets
    Route::middleware('doNotCacheResponse')->name('asset.')->prefix('asset')->namespace('Assets')->group(function () {
        Route::middleware('signed')->get('download/{media}/{user}/{version?}', ['uses' => 'DownloadController', 'as' => 'download']);
        Route::middleware('signed')->get('placeholder/{media}/{user}/{version?}', ['uses' => 'PlaceholderController', 'as' => 'placeholder']);
        Route::middleware('signed')->get('preview/{media}/{user}/{version?}', ['uses' => 'PreviewController', 'as' => 'preview']);
        Route::middleware('jwt.auth')->get('thumbnail/{media}/{offset}', ['uses' => 'ThumbnailController', 'as' => 'thumbnail'])->where('offset', '[0-9]+');
    });
});
