<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

// API mặc định của Laravel
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Đăng nhập
// Đăng ký: API thuần, không cần Session/CSRF
Route::post('/v1/register', [AuthController::class, 'dangKy']);


// Đăng nhập / đăng xuất: dùng Session
Route::middleware('web')->group(function () {
    Route::post('/v1/login', [AuthController::class, 'dangNhap']);
    Route::post('/v1/logout', [AuthController::class, 'dangXuat']);
});
Route::middleware(['web', 'role:quan_tri'])->get('/v1/admin/test', function () {
    return response()->json([
        'message' => 'Bạn có quyền quản trị',
        'vai_tro' => 'quan_tri'
    ]);
});