<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\SanPhamController;
use App\Http\Controllers\DatTourController;
use App\Http\Controllers\Admin\SanPhamAdminController;

// API mặc định của Laravel
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// API đăng ký
Route::post('/v1/register', [AuthController::class, 'dangKy']);

// API danh sách và chi tiết sản phẩm
Route::get('/v1/products', [SanPhamController::class, 'index']);
Route::get('/v1/products/{ma_san_pham}', [SanPhamController::class, 'show']);

// Các API sử dụng session
Route::middleware('web')->group(function () {

    // Đăng nhập
    Route::post('/v1/login', [AuthController::class, 'dangNhap']);

    // Đăng xuất
    Route::post('/v1/logout', [AuthController::class, 'dangXuat']);

    // Lấy thông tin người dùng hiện tại
    Route::get('/v1/me', [AuthController::class, 'me']);

    // Đặt tour
    Route::post('/v1/bookings', [DatTourController::class, 'datTour']);

    // API kiểm tra quyền quản trị
    Route::middleware('role:quan_tri')->get('/v1/admin/test', function () {
        return response()->json([
            'message' => 'Bạn có quyền quản trị',
            'vai_tro' => 'quan_tri',
        ]);
    });

    // API quản lý sản phẩm dành cho quản trị viên và nhà cung cấp
    Route::middleware('role:quan_tri,nha_cung_cap')
        ->prefix('/v1/admin/products')
        ->group(function () {
            Route::get('/', [SanPhamAdminController::class, 'index']);
            Route::post('/', [SanPhamAdminController::class, 'store']);
            Route::put('/{ma_san_pham}', [SanPhamAdminController::class, 'update']);
            Route::delete('/{ma_san_pham}', [SanPhamAdminController::class, 'destroy']);
        });
});