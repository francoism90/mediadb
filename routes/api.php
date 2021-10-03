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
    Route::name('auth.')->namespace('Auth')->group(function () {
        Route::post('login', ['uses' => 'LoginController', 'as' => 'login']);
        Route::middleware('auth:sanctum')->post('logout', ['uses' => 'LogoutController', 'as' => 'logout']);
        Route::middleware('auth:sanctum')->get('user', ['uses' => 'UserController', 'as' => 'user']);
        Route::middleware('auth:sanctum')->get('refresh', ['uses' => 'RefreshController', 'as' => 'refresh']);
    });

    // User
    Route::name('user.')->prefix('user')->namespace('User')->group(function () {
        Route::middleware('auth:sanctum')->post('/favorite/{model}', ['uses' => 'FavoriteController', 'as' => 'favorite']);
        Route::middleware('auth:sanctum')->post('/follow/{model}', ['uses' => 'FollowController', 'as' => 'follow']);
    });

    // Video
    Route::name('videos.')->prefix('videos')->namespace('Video')->group(function () {
        // Resources
        Route::middleware('auth:sanctum')->get('/', ['uses' => 'IndexController', 'as' => 'index']);
        Route::middleware('auth:sanctum')->get('/{video}', ['uses' => 'ShowController', 'as' => 'show']);
        Route::middleware('auth:sanctum')->delete('/{video}', ['uses' => 'DestroyController', 'as' => 'destroy']);
        Route::middleware('auth:sanctum')->patch('/{video}', ['uses' => 'UpdateController', 'as' => 'update']);

        // DASH
        Route::middleware('auth:sanctum', 'cache.headers:public;max_age=86400;etag')->get('/manifest/{video}', ['uses' => 'ManifestController', 'as' => 'manifest']);
        Route::middleware('signed', 'cache.headers:public;max_age=86400;etag')->get('/sprite/{video}', ['uses' => 'SpriteController', 'as' => 'sprite']);
    });

    // Tag
    Route::name('tags.')->prefix('tags')->namespace('Tag')->group(function () {
        Route::middleware('auth:sanctum')->get('/', ['uses' => 'IndexController', 'as' => 'index']);
        Route::middleware('auth:sanctum')->get('/{tag}', ['uses' => 'ShowController', 'as' => 'show']);
    });

    // Media
    Route::name('media.')->prefix('media')->namespace('Media')->group(function () {
        Route::middleware('auth:sanctum')->patch('/{media}', ['uses' => 'UpdateController', 'as' => 'update']);
        Route::middleware('signed', 'cache.headers:public;max_age=86400;etag')->get('/asset/{media}/{name?}/{version?}', ['uses' => 'AssetController', 'as' => 'asset']);
    });
});
