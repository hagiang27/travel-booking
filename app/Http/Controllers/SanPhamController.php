<?php

namespace App\Http\Controllers;

use App\Models\SanPham;
use Illuminate\Http\Request;

class SanPhamController extends Controller
{
    public function index(Request $request)
    {
 $request->validate([
    'q' => 'nullable|string|max:120',
    'tinh_thanh' => 'nullable|string|max:80',
    'loai_san_pham' => 'nullable|string|max:50',
    'gia_min' => 'nullable|numeric|min:0',
    'gia_max' => 'nullable|numeric|min:0|gte:gia_min',
    'sort' => 'nullable|in:gia_tang,gia_giam,moi_nhat,danh_gia',
    'per_page' => 'nullable|integer|min:1|max:50',
]);
        $query = SanPham::query()
            ->where('trang_thai', 'dang_ban');

        // Tìm kiếm theo tên sản phẩm
        if ($request->filled('q')) {
            $query->where(
                'ten_san_pham',
                'like',
                '%' . $request->q . '%'
            );
        }

        // Lọc theo tỉnh/thành
        if ($request->filled('tinh_thanh')) {
            $tinhThanh = mb_strtolower(
                trim($request->tinh_thanh)
            );

            $query->whereHas('diemDen', function ($q) use ($tinhThanh) {
                $q->whereRaw(
                    'LOWER(duong_dan_tinh) LIKE ?',
                    ['%' . str_replace(' ', '-', $tinhThanh) . '%']
                );
            });
        }
        if ($request->filled('loai_san_pham')) {
    $query->where(
        'loai_san_pham',
        $request->loai_san_pham
    );
    }
    if ($request->filled('gia_min')) {
    $query->where(
        'gia_co_ban',
        '>=',
        $request->gia_min
    );
}

if ($request->filled('gia_max')) {
    $query->where(
        'gia_co_ban',
        '<=',
        $request->gia_max
    );
}
if ($request->filled('sort')) {
    switch ($request->sort) {
        case 'gia_tang':
            $query->orderBy('gia_co_ban', 'asc');
            break;

        case 'gia_giam':
            $query->orderBy('gia_co_ban', 'desc');
            break;

        case 'moi_nhat':
            $query->orderBy('ngay_tao', 'desc');
            break;

        case 'danh_gia':
            $query->orderBy('diem_danh_gia_tb', 'desc');
            break;
    }
}
$perPage = (int) $request->input('per_page', 10);
$perPage = max(1, min($perPage, 50));

$products = $query->paginate($perPage)->withQueryString();

return response()->json($products);
}

/**
 * Xem chi tiết một sản phẩm
 */
public function show($ma_san_pham)
{
   $product = SanPham::with([
    'lichKhaDung' => function ($query) {
        $query->whereDate('ngay_khoi_hanh', '>=', now()->toDateString())
            ->where('trang_thai', 'mo_ban')
            ->where('con_lai', '>', 0)
            ->orderBy('ngay_khoi_hanh', 'asc');
    }
])
    ->where('ma_san_pham', $ma_san_pham)
    ->where('trang_thai', 'dang_ban')
    ->first();
    if (!$product) {
        return response()->json([
            'message' => 'Không tìm thấy sản phẩm.'
        ], 404);
    }

    return response()->json($product);
}
}