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
    Route::get('/', ['uses' => 'HomeController', 'as' => 'home']);

    // Auth
    Route::name('auth.')->prefix('auth')->namespace('Auth')->group(function () {
        Route::post('login', ['uses' => 'LoginController', 'as' => 'login']);
        Route::middleware('auth:sanctum')->post('logout', ['uses' => 'LogoutController', 'as' => 'logout']);
        Route::middleware('auth:sanctum')->get('user', ['uses' => 'UserController', 'as' => 'user']);
        Route::middleware('auth:sanctum')->get('refresh', ['uses' => 'RefreshController', 'as' => 'refresh']);
        // Route::middleware('auth:sanctum')->get('impersonate', ['uses' => 'ImpersonateController', 'as' => 'impersonate']);
        // Route::middleware('auth:sanctum')->get('unimpersonate', ['uses' => 'UnimpersonateController', 'as' => 'unimpersonate']);
    });

    // Notification
    Route::middleware('auth:sanctum')->name('notifications.')->prefix('notifications')->namespace('Notification')->group(function () {
        Route::get('/', ['uses' => 'IndexController', 'as' => 'index']);
        Route::post('/read', ['uses' => 'ReadController', 'as' => 'read']);
        Route::post('/delete', ['uses' => 'DeleteController', 'as' => 'delete']);
    });

    // Video
    Route::middleware('auth:sanctum')->name('videos.')->prefix('videos')->namespace('Video')->group(function () {
        Route::get('/', ['uses' => 'IndexController', 'as' => 'index']);
        Route::get('/{video}', ['uses' => 'ShowController', 'as' => 'show']);
        Route::delete('/{video}', ['uses' => 'DestroyController', 'as' => 'destroy']);
        Route::patch('/{video}', ['uses' => 'UpdateController', 'as' => 'update']);
        Route::match(['delete', 'post'], '/{video}/favorite', ['uses' => 'FavoriteController', 'as' => 'favorite']);
    });

    // Tag
    Route::middleware('auth:sanctum')->name('tags.')->prefix('tags')->namespace('Tag')->group(function () {
        Route::get('/', ['uses' => 'IndexController', 'as' => 'index']);
        Route::get('/{tag}', ['uses' => 'ShowController', 'as' => 'show']);
        Route::delete('/{tag}', ['uses' => 'DestroyController', 'as' => 'destroy']);
        Route::patch('/{tag}', ['uses' => 'UpdateController', 'as' => 'update']);
    });

    // Media
    Route::name('media.')->prefix('media')->namespace('Media')->group(function () {
        Route::patch('/{media}', ['uses' => 'UpdateController', 'as' => 'update']);

        Route::middleware(['cache.headers:public;max_age=31536000;etag', 'signed'])->get('/asset/{media}/{user?}/{name}/{version?}', ['uses' => 'ConversionController', 'as' => 'asset']);
        Route::middleware('signed')->get('/download/{media}/{user?}', ['uses' => 'DownloadController', 'as' => 'download']);
        Route::middleware('signed')->get('/stream/{media}/{user?}', ['uses' => 'StreamController', 'as' => 'stream']);
        Route::middleware('cache.headers:public;max_age=86400;etag')->get('/manifest/{token}/{type?}', ['uses' => 'ManifestController', 'as' => 'manifest']);
    });
});
