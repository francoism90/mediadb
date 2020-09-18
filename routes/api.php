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

    // Media
    Route::middleware('signed')->name('media.')->prefix('media')->namespace('Media')->group(function () {
        Route::middleware('cache.headers:public;max_age=604800;etag')->get('/asset/{media}/{user}/{name}/{version?}', ['uses' => 'AssetController', 'as' => 'asset']);
        Route::middleware('doNotCacheResponse')->get('/download/{media}/{user}', ['uses' => 'DownloadController', 'as' => 'download']);
        Route::middleware('doNotCacheResponse')->get('/stream/{media}/{user}', ['uses' => 'StreamController', 'as' => 'stream']);
    });

    // Video
    Route::middleware('auth:sanctum')->name('videos.')->prefix('videos')->namespace('Video')->group(function () {
        // Resource
        Route::match(['get', 'head'], '/', ['uses' => 'IndexController', 'as' => 'index']);
        Route::match(['put', 'patch'], '/{video}', ['uses' => 'UpdateController', 'as' => 'update']);
        Route::match(['delete'], '/{video}', ['uses' => 'DestroyController', 'as' => 'destroy']);
        Route::match(['get'], '/{video}', ['uses' => 'ShowController', 'as' => 'show']);

        // Miscellaneous
        Route::middleware('doNotCacheResponse')->match(['put', 'patch'], '/{video}/frameshot', ['uses' => 'FrameshotController', 'as' => 'frameshot']);
        Route::middleware('doNotCacheResponse')->match(['put', 'post'], '/{video}/save', ['uses' => 'SaveController', 'as' => 'save']);
    });

    // Collection
    Route::middleware('auth:sanctum')->name('collections.')->prefix('collections')->namespace('Collection')->group(function () {
        // Resource
        Route::match(['get', 'head'], '/', ['uses' => 'IndexController', 'as' => 'index']);
        Route::match(['put', 'patch'], '/{collection}', ['uses' => 'UpdateController', 'as' => 'update']);
        Route::match(['delete'], '/{collection}', ['uses' => 'DestroyController', 'as' => 'destroy']);
        Route::match(['get'], '/{collection}', ['uses' => 'ShowController', 'as' => 'show']);
    });

    // Tag
    Route::middleware('auth:sanctum')->name('tags.')->prefix('tags')->namespace('Tag')->group(function () {
        // Resource
        Route::match(['get', 'head'], '/', ['uses' => 'IndexController', 'as' => 'index']);
    });
});
