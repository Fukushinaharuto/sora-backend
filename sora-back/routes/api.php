<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;

// 認証不要（新規登録・ログイン）
Route::post('/user/register', [AuthController::class, 'register']);
Route::post('/user/login', [AuthController::class, 'login']);

// 認証必須（動作確認用）
Route::middleware('auth:sanctum')->get('/me', function () {
    return auth()->user();
});
