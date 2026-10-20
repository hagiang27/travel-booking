<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SanPham;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SanPhamController extends Controller
{
    /**
     * Danh sách sản phẩm đang bán.
     */
    public function index(Request $request)
    {
        $soMoiTrang = (int) $request->query('per_page', 10);
        $soMoiTrang = max(1, min($soMoiTrang, 50));

        $query = SanPham::query()
            ->where('trang_thai', 'dang_ban');

        if ($request->filled('loai_san_pham')) {
            $query->where(
                'loai_san_pham',
                $request->query('loai_san_pham')
            );
        }

        if ($request->filled('ma_diem_den')) {
            $query->where(
                'ma_diem_den',
                $request->query('ma_diem_den')
            );
        }

        if ($request->filled('q')) {
            $query->where(
                'ten_san_pham',
                'like',
                '%' . $request->query('q') . '%'
            );
        }

        return response()->json(
            $query->orderBy('ma_san_pham')
                ->paginate($soMoiTrang)
        );
    }

    /**
     * Chi tiết một sản phẩm và lịch khởi hành còn chỗ.
     */
    public function show(string $ma)
    {
        $sanPham = SanPham::where('ma_san_pham', $ma)
            ->where('trang_thai', 'dang_ban')
            ->first();

        if (!$sanPham) {
            return response()->json([
                'message' => 'Không tìm thấy sản phẩm đang bán.',
            ], 404);
        }

        $lichKhaDung = DB::table('lich_kha_dung')
            ->where('ma_san_pham', $ma)
            ->where('trang_thai', 'mo_ban')
            ->where('con_lai', '>', 0)
            ->orderBy('ngay_khoi_hanh')
            ->get();

        return response()->json(
            array_merge(
                $sanPham->toArray(),
                ['lich_kha_dung' => $lichKhaDung]
            )
        );
    }
}
