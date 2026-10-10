<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/dang-nhap', [AuthController::class, 'apiLogin']);
Route::post('/auth/dang-xuat', [AuthController::class, 'apiLogout']);

Route::middleware('auth')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'apiMe']);

    Route::get('/admin/test', function () {
        return response()->json([
            'message' => 'Bạn có quyền truy cập khu vực quản trị.',
        ]);
    })->middleware('role:quan_tri');
});