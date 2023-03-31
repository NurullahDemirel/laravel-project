<?php

use App\Http\Controllers\Api\Comment\CommentController;
use App\Http\Controllers\Api\Like\LikeController;
use App\Http\Controllers\Api\Post\PostController;
use App\Http\Controllers\Api\PostFollower\PostFollowerController;
use App\Http\Controllers\Api\User\UserVeriyController;
use App\Http\Controllers\Api\User\UserController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('apiMiddleware')->group(function () {

    Route::get('user/verify/{code}', UserVeriyController::class);
    Route::post('users', [UserController::class, 'store']);
    Route::post('user/login', [UserController::class, 'login']);

    Route::middleware(['auth:sanctum', 'checkVerifyUserApi'])->group(function () {
        //need to auth for these routes
        Route::controller(UserController::class)->group(function () {
            Route::delete('users',  'destroy');
            Route::get('user/info', 'userInfo');
            Route::put('users', 'update');
            Route::post('user/logout', 'logout');
            Route::post('user/upload/profile/image', 'uploadImage');
            Route::post('user/follow', 'follow');
            Route::post('response/follower', 'responseFollower');
        });
        Route::apiResource('posts', PostController::class);
        Route::apiResource('comments', CommentController::class);
        Route::post('post/follow', PostFollowerController::class);
        Route::get('all/posts',[PostController::class, 'allPosts']);


        Route::controller(LikeController::class)->group(function () {
            Route::post('like/{likeableType}', 'likeOrDislike');
        });
    });
});


//this route adde by testing some proccess in order to be sure , it should be delete end of the project
Route::get('test', [Controller::class, 'test']);
