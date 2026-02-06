<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;

// 認証不要（新規登録・ログイン）
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// 認証必要
Route::middleware("auth:sanctum")->group(function () {
    Route::get('/post/{city_id}', [PostController::class, 'index']);
    Route::get('/post/show/{id}', [PostController::class, 'show']);
    Route::post('/post', [PostController::class, 'store']);
    Route::post('/post/like', [PostController::class, 'like']);
});
