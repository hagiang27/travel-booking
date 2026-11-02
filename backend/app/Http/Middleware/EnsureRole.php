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
        if (!$request->session()->has('ma_nguoi_dung')) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $maNguoiDung = $request->session()->get('ma_nguoi_dung');

        $nguoiDung = DB::table('nguoi_dung')
            ->where('ma_nguoi_dung', $maNguoiDung)
            ->first();

        if (!$nguoiDung || $nguoiDung->trang_thai !== 'kich_hoat') {
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        if (!in_array($nguoiDung->vai_tro, $vaiTroChoPhep, true)) {
            return response()->json([
                'message' => 'Bạn không có quyền truy cập chức năng này.',
            ], 403);
        }

        return $next($request);
    }
}
