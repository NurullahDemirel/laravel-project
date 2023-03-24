<?php

use App\Http\Controllers\Api\Comment\CommentController;
use App\Http\Controllers\Api\Like\LikeController;
use App\Http\Controllers\Api\Post\PostController;
use App\Http\Controllers\Api\User\UserVeriyController;
use App\Http\Controllers\Api\User\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::middleware('apiMiddleware')->group(function () {

    Route::get('user/verify/{code}',UserVeriyController::class)->name('verify.user');
    Route::post('users', [UserController::class, 'store']);
    Route::post('user/login', [UserController::class, 'login']);

    Route::middleware(['auth:sanctum','checkVerifyUserApi'])->group(function () {
        //need to auth for these routes
        Route::controller(UserController::class)->group(function () {
            Route::delete('users',  'destroy');
            Route::get('user/info', 'userInfo');
            Route::put('users', 'update');
            Route::post('user/logout', 'logout');
            Route::post('user/upload/profile/image', 'uploadImage');
        });
        Route::apiResource('posts',PostController::class);
        Route::apiResource('comments',CommentController::class);

        Route::controller(LikeController::class)->group(function(){
            Route::post('like/{likeableType}','likeOrDislike');

        });
    });
});
