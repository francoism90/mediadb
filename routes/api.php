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
        Route::middleware('guest')->post('login', ['uses' => 'LoginController', 'as' => 'login']);
        Route::middleware('auth:sanctum')->post('logout', ['uses' => 'LogoutController', 'as' => 'logout']);
        Route::middleware('auth:sanctum')->get('user', ['uses' => 'UserController', 'as' => 'user']);
        Route::middleware('auth:sanctum')->get('refresh', ['uses' => 'RefreshController', 'as' => 'refresh']);
        // Route::middleware('auth:sanctum')->get('impersonate', ['uses' => 'ImpersonateController', 'as' => 'impersonate']);
        // Route::middleware('auth:sanctum')->get('unimpersonate', ['uses' => 'UnimpersonateController', 'as' => 'unimpersonate']);
    });

    // Resources
    Route::middleware('auth:sanctum')->name('resource.')->namespace('Resources')->group(function () {
        Route::apiResource('collections', 'CollectionController')->only(['index', 'store', 'show', 'update', 'destroy']);
        Route::apiResource('tags', 'TagController')->only(['index']);
        Route::apiResource('videos', 'VideoController')->only(['index', 'store', 'show', 'update', 'destroy']);
    });

    // Media
    Route::middleware('doNotCacheResponse')->name('media.')->prefix('media')->namespace('Media')->group(function () {
        Route::middleware(['signed', 'cache.headers:public;max_age=604800;etag'])->get('/asset/{media}/{user}/{name}/{version?}', ['uses' => 'AssetController', 'as' => 'asset']);
        Route::middleware(['signed', 'cache.headers:public;max_age=604800;etag'])->get('/preview/{media}/{user}', ['uses' => 'PreviewController', 'as' => 'preview']);
        Route::middleware(['signed', 'cache.headers:public;max_age=604800;etag'])->get('/sprite/{media}/{user}', ['uses' => 'SpriteController', 'as' => 'sprite']);
        Route::middleware('signed')->get('/download/{media}/{user}', ['uses' => 'DownloadController', 'as' => 'download']);
        Route::middleware('signed')->get('/stream/{media}/{user}', ['uses' => 'StreamController', 'as' => 'stream']);
    });

    // Video
    Route::middleware('doNotCacheResponse')->name('videos.')->prefix('videos')->namespace('Video')->group(function () {
        Route::middleware('auth:sanctum')->patch('/{video}/frameshot', ['uses' => 'FrameshotController', 'as' => 'frameshot']);
        Route::middleware('auth:sanctum')->put('/{video}/save', ['uses' => 'SaveController', 'as' => 'save']);
    });
});
