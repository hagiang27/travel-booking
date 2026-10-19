<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(
        Request $request,
        Closure $next,
        string ...$vaiTroChoPhep
    ): Response {
        // Kiểm tra người dùng đã đăng nhập bằng session chưa
        if (!$request->session()->has('ma_nguoi_dung')) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        // Lấy mã người dùng từ session
        $maNguoiDung = $request->session()->get('ma_nguoi_dung');

        // Lấy thông tin mới nhất từ cơ sở dữ liệu
        $nguoiDung = DB::table('nguoi_dung')
            ->where('ma_nguoi_dung', $maNguoiDung)
            ->first();

        // Không tồn tại tài khoản hoặc tài khoản bị khóa
        if (!$nguoiDung || $nguoiDung->trang_thai !== 'kich_hoat') {
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        // Kiểm tra vai trò được phép truy cập
        if (!in_array($nguoiDung->vai_tro, $vaiTroChoPhep, true)) {
            return response()->json([
                'message' => 'Bạn không có quyền truy cập chức năng này.',
            ], 403);
        }

        // Cho phép tiếp tục nếu người dùng hợp lệ
        return $next($request);
    }
}