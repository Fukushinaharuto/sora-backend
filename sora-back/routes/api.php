<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// 認証不要（新規登録・ログイン）
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
