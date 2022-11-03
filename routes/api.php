<?php

use App\Http\Controllers\PostsController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\PostImagesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => 'auth:sanctum'], function(){
    Route::get('/users', [UsersController::class, 'index']);
    Route::get('/users/{user}/posts', [UsersController::class, 'posts']);
    Route::post('/users/{user}/toggle_following', [UsersController::class, 'toggleFollowing']);
    Route::get('/users/feed', [UsersController::class, 'feed']);
    Route::post('/users/statistics', [UsersController::class, 'getStatistics']);

    Route::get('/posts', [PostsController::class, 'index']);
    Route::post('/posts', [PostsController::class, 'store']);
    Route::post('/posts/{post}/toggle_like', [PostsController::class, 'toggleLike']);
    Route::post('/postImages', [PostImagesController::class, 'store']);
    Route::post('/posts/{post}/repost', [PostsController::class, 'repost']);
    Route::post('/posts/{post}/comment', [PostsController::class, 'comment']);
    Route::get('/posts/{post}/comments', [PostsController::class, 'getComments']);
});
