<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SanPham;
use Illuminate\Http\Request;

class SanPhamAdminController extends Controller
{
    /**
     * Danh sách sản phẩm dành cho quản trị.
     * Bao gồm cả sản phẩm đang bán và ngừng bán.
     */
    public function index(Request $request)
    {
$vaiTro = $request->session()->get('vai_tro');
$maNguoiDung = $request->session()->get('ma_nguoi_dung');

$query = SanPham::with('diemDen');

if ($vaiTro === 'nha_cung_cap') {
    $nguoiDung = \Illuminate\Support\Facades\DB::table('nguoi_dung')
        ->where('ma_nguoi_dung', $maNguoiDung)
        ->where('trang_thai', 'kich_hoat')
        ->first();

    if (!$nguoiDung || !$nguoiDung->ma_ncc) {
        return response()->json([
            'message' => 'Tài khoản chưa được liên kết với nhà cung cấp.',
        ], 403);
    }

    $query->where('ma_ncc', $nguoiDung->ma_ncc);
}

        // Tìm theo tên sản phẩm.
        if ($request->filled('tu_khoa')) {
            $tuKhoa = $request->input('tu_khoa');

            $query->where(
                'ten_san_pham',
                'like',
                '%' . $tuKhoa . '%'
            );
        }

        // Lọc theo trạng thái nếu có truyền.
        if ($request->filled('trang_thai')) {
            $query->where(
                'trang_thai',
                $request->input('trang_thai')
            );
        }

        // Lọc theo loại sản phẩm nếu có truyền.
        if ($request->filled('loai_san_pham')) {
            $query->where(
                'loai_san_pham',
                $request->input('loai_san_pham')
            );
        }

        // Phân trang, mặc định 15 sản phẩm mỗi trang.
        $perPage = min(
            max((int) $request->input('per_page', 15), 1),
            100
        );

        $sanPhams = $query
            ->orderBy('ngay_tao', 'desc')
            ->orderBy('ma_san_pham', 'desc')
            ->paginate($perPage);

        return response()->json([
            'thanh_cong' => true,
            'du_lieu' => $sanPhams,
        ]);
    }
    /**
     * Tạo sản phẩm mới.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ten_san_pham' => 'required|string|max:200',
            'duong_dan_tinh' => 'required|string|max:200',
            'ma_diem_den' => 'required|exists:diem_den,ma_diem_den',
            'loai_san_pham' => 'required|in:tour,stay,transfer,ticket,experience',
            'gia_co_ban' => 'required|integer|min:0',
            'don_vi_tinh' => 'required|string|max:20',
            'so_ngay' => 'nullable|integer|min:1',
            'suc_chua_toi_da' => 'required|integer|min:1',
            'chinh_sach_huy' => 'nullable|string|max:120',
            'trang_thai' => 'sometimes|in:dang_ban,ngung_ban',
        ]);

        $vaiTro = $request->session()->get('vai_tro');
        $maNguoiDung = $request->session()->get('ma_nguoi_dung');

        if ($vaiTro === 'nha_cung_cap') {
            $nguoiDung = \Illuminate\Support\Facades\DB::table('nguoi_dung')
                ->where('ma_nguoi_dung', $maNguoiDung)
                ->where('trang_thai', 'kich_hoat')
                ->first();

            if (!$nguoiDung || !$nguoiDung->ma_ncc) {
                return response()->json([
                    'message' => 'Tài khoản chưa được liên kết với nhà cung cấp.',
                ], 403);
            }

            $maNcc = $nguoiDung->ma_ncc;
        } else {
            $maNcc = $request->input('ma_ncc');
        }

        if (!$maNcc || !\Illuminate\Support\Facades\DB::table('nha_cung_cap')
            ->where('ma_ncc', $maNcc)
            ->exists()) {
            return response()->json([
                'message' => 'Mã nhà cung cấp không hợp lệ.',
            ], 422);
        }

        $soThuTu = ((int) \Illuminate\Support\Facades\DB::table('san_pham')
            ->selectRaw("MAX(CAST(SUBSTRING(ma_san_pham, 3) AS UNSIGNED)) AS so_lon_nhat")
            ->value('so_lon_nhat')) + 1;

        $maSanPham = 'SP' . str_pad((string) $soThuTu, 4, '0', STR_PAD_LEFT);

        $duLieu = [
            'ma_san_pham' => $maSanPham,
            'ten_san_pham' => $validated['ten_san_pham'],
            'duong_dan_tinh' => $validated['duong_dan_tinh'],
            'ma_ncc' => $maNcc,
            'ma_diem_den' => $validated['ma_diem_den'],
            'loai_san_pham' => $validated['loai_san_pham'],
            'gia_co_ban' => $validated['gia_co_ban'],
            'don_vi_tinh' => $validated['don_vi_tinh'],
            'so_ngay' => $validated['so_ngay'] ?? 1,
            'suc_chua_toi_da' => $validated['suc_chua_toi_da'],
            'chinh_sach_huy' => $validated['chinh_sach_huy'] ?? null,
            'trang_thai' => $validated['trang_thai'] ?? 'ngung_ban',
            'diem_danh_gia_tb' => 0,
            'so_luot_danh_gia' => 0,
            'ngay_tao' => now()->toDateString(),
        ];

        \Illuminate\Support\Facades\DB::table('san_pham')->insert($duLieu);

        $sanPham = SanPham::with('diemDen')
            ->where('ma_san_pham', $maSanPham)
            ->first();

        return response()->json([
            'thanh_cong' => true,
            'thong_bao' => 'Tạo sản phẩm thành công.',
            'du_lieu' => $sanPham,
        ], 201);
    }
        /**
     * Cap nhat san pham.
     */
    public function update(Request $request, string $ma_san_pham)
    {
        $vaiTro = $request->session()->get('vai_tro');
        $maNguoiDung = $request->session()->get('ma_nguoi_dung');

        $sanPham = SanPham::where(
            'ma_san_pham',
            $ma_san_pham
        )->first();

        if (!$sanPham) {
            return response()->json([
                'thanh_cong' => false,
                'thong_bao' => 'Khong tim thay san pham.',
            ], 404);
        }

        // Nha cung cap chi duoc sua san pham cua minh.
        if ($vaiTro === 'nha_cung_cap') {
            $nguoiDung = \Illuminate\Support\Facades\DB::table('nguoi_dung')
                ->where('ma_nguoi_dung', $maNguoiDung)
                ->where('trang_thai', 'kich_hoat')
                ->first();

            if (
                !$nguoiDung ||
                !$nguoiDung->ma_ncc ||
                $sanPham->ma_ncc !== $nguoiDung->ma_ncc
            ) {
                return response()->json([
                    'thanh_cong' => false,
                    'thong_bao' => 'Ban khong co quyen sua san pham nay.',
                ], 403);
            }
        } elseif ($vaiTro !== 'quan_tri') {
            return response()->json([
                'thanh_cong' => false,
                'thong_bao' => 'Khong du quyen thao tac.',
            ], 403);
        }

        $validated = $request->validate([
            'ten_san_pham' => 'sometimes|required|string|max:200',
            'duong_dan_tinh' => 'sometimes|required|string|max:200',
            'ma_diem_den' => 'sometimes|required|exists:diem_den,ma_diem_den',
            'loai_san_pham' => 'sometimes|required|in:tour,stay,transfer,ticket,experience',
            'gia_co_ban' => 'sometimes|required|integer|min:0',
            'don_vi_tinh' => 'sometimes|required|string|max:20',
            'so_ngay' => 'sometimes|nullable|integer|min:1',
            'suc_chua_toi_da' => 'sometimes|required|integer|min:1',
            'chinh_sach_huy' => 'sometimes|nullable|string|max:120',
            'trang_thai' => 'sometimes|required|in:dang_ban,ngung_ban',
        ]);

        if (empty($validated)) {
            return response()->json([
                'thanh_cong' => false,
                'thong_bao' => 'Ban chua gui truong nao de cap nhat.',
            ], 422);
        }

        \Illuminate\Support\Facades\DB::table('san_pham')
            ->where('ma_san_pham', $ma_san_pham)
            ->update($validated);

        $sanPhamCapNhat = SanPham::with('diemDen')
            ->where('ma_san_pham', $ma_san_pham)
            ->first();

        return response()->json([
            'thanh_cong' => true,
            'thong_bao' => 'Cap nhat san pham thanh cong.',
            'du_lieu' => $sanPhamCapNhat,
        ]);
    }
/**
     * Ngung ban san pham, khong xoa vat ly khoi co so du lieu.
     */
    public function destroy(Request $request, string $ma_san_pham)
    {
        $sanPham = SanPham::where('ma_san_pham', $ma_san_pham)->first();

        if (!$sanPham) {
            return response()->json([
                'thanh_cong' => false,
                'thong_bao' => 'Khong tim thay san pham.',
            ], 404);
        }

        $vaiTro = session('vai_tro');

        if ($vaiTro === 'nha_cung_cap') {
            $nguoiDung = \Illuminate\Support\Facades\DB::table('nguoi_dung')
                ->where('ma_nguoi_dung', session('ma_nguoi_dung'))
                ->where('trang_thai', 'kich_hoat')
                ->first();

            if (!$nguoiDung || $nguoiDung->ma_ncc !== $sanPham->ma_ncc) {
                return response()->json([
                    'thanh_cong' => false,
                    'thong_bao' => 'Ban khong co quyen ngung ban san pham nay.',
                ], 403);
            }
        } elseif ($vaiTro !== 'quan_tri') {
            return response()->json([
                'thanh_cong' => false,
                'thong_bao' => 'Ban khong co quyen thuc hien thao tac nay.',
            ], 403);
        }

        if ($sanPham->trang_thai === 'ngung_ban') {
            return response()->json([
                'thanh_cong' => true,
                'thong_bao' => 'San pham da ngung ban tu truoc.',
                'du_lieu' => $sanPham,
            ]);
        }

        \Illuminate\Support\Facades\DB::table('san_pham')
            ->where('ma_san_pham', $ma_san_pham)
            ->update(['trang_thai' => 'ngung_ban']);

        $sanPham->refresh();

        return response()->json([
            'thanh_cong' => true,
            'thong_bao' => 'Da ngung ban san pham thanh cong.',
            'du_lieu' => $sanPham,
        ]);
    }
}