<?php

use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::middleware('apiMiddleware')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        //need to auth for these routes
        Route::controller(UserController::class)->group(function () {
            Route::delete('users',  'destroy');
            Route::get('user/info', 'userInfo');
            Route::put('users', 'update');
            Route::post('user/logout', 'logout');
        });
    });

    Route::post('users', [UserController::class, 'store']);
    Route::post('user/login', [UserController::class, 'login']);
});
