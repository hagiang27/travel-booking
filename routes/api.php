
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\SanPhamController;
use App\Http\Controllers\DatTourController;

// API mặc định của Laravel
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Đăng ký
Route::post('/v1/register', [AuthController::class, 'dangKy']);

// Danh sách sản phẩm và chi tiết sản phẩm
Route::get('/v1/products', [SanPhamController::class, 'index']);
Route::get('/v1/products/{ma_san_pham}', [SanPhamController::class, 'show']);

// Đăng nhập, thông tin người dùng và đăng xuất bằng Session
Route::middleware('web')->group(function () {
    Route::post('/v1/login', [AuthController::class, 'dangNhap']);
    Route::get('/v1/me', [AuthController::class, 'me']);
    Route::post('/v1/logout', [AuthController::class, 'dangXuat']);

    // Đặt tour: kiểm tra đăng nhập bằng Session trong controller
    Route::post('/v1/bookings', [DatTourController::class, 'datTour']);
});

// API kiểm tra quyền quản trị
Route::middleware(['web', 'role:quan_tri'])->get('/v1/admin/test', function () {
    return response()->json([
        'message' => 'Bạn có quyền quản trị',
        'vai_tro' => 'quan_tri',
    ]);
});
