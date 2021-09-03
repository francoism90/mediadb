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

    // User
    Route::middleware('auth:sanctum')->name('user.')->prefix('user')->namespace('User')->group(function () {
        Route::post('/favorite/{model}', ['uses' => 'FavoriteController', 'as' => 'favorite']);
        Route::post('/follow/{model}', ['uses' => 'FollowController', 'as' => 'follow']);
    });

    // Video
    Route::middleware('auth:sanctum')->name('videos.')->prefix('videos')->namespace('Video')->group(function () {
        Route::get('/', ['uses' => 'IndexController', 'as' => 'index']);
        Route::get('/{video}', ['uses' => 'ShowController', 'as' => 'show']);
        Route::delete('/{video}', ['uses' => 'DestroyController', 'as' => 'destroy']);
        Route::patch('/{video}', ['uses' => 'UpdateController', 'as' => 'update']);
    });

    // Tag
    Route::middleware('auth:sanctum')->name('tags.')->prefix('tags')->namespace('Tag')->group(function () {
        Route::get('/', ['uses' => 'IndexController', 'as' => 'index']);
        Route::get('/{tag}', ['uses' => 'ShowController', 'as' => 'show']);
    });

    // Media
    Route::name('media.')->prefix('media')->namespace('Media')->group(function () {
        Route::middleware('auth:sanctum')->patch('/{media}', ['uses' => 'UpdateController', 'as' => 'update']);
        Route::middleware('signed', 'cache.headers:public;max_age=86400;etag')->get('/asset/{media?}/{name}', ['uses' => 'AssetController', 'as' => 'asset']);
    });

    // VOD
    Route::middleware('auth:sanctum')->name('vod.')->prefix('vod')->namespace('Vod')->group(function () {
        Route::get('/manifest/{video}', ['uses' => 'ManifestController', 'as' => 'manifest']);
    });
});
