<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Đăng ký tài khoản mới
     */
    public function dangKy(Request $request)
    {
        $validated = $request->validate([
            'ho_ten' => 'required|string|max:100',
            'ten_dang_nhap' => 'required|string|max:80|unique:nguoi_dung,ten_dang_nhap',
            'thu_dien_tu_mo_phong' => 'required|email|max:120|unique:nguoi_dung,thu_dien_tu_mo_phong',
            'mat_khau' => 'required|string|min:6',
            'vai_tro' => 'required|in:quan_tri,nha_cung_cap,khach_hang,huong_dan_vien,kiem_thu',
        ]);

        // Sinh mã người dùng dạng ND0001, ND0002,...
        $soThuTu = DB::table('nguoi_dung')->count() + 1;

        $maNguoiDung = 'ND' . str_pad(
            (string) $soThuTu,
            4,
            '0',
            STR_PAD_LEFT
        );

        // Đảm bảo mã người dùng không bị trùng
        while (
            DB::table('nguoi_dung')
                ->where('ma_nguoi_dung', $maNguoiDung)
                ->exists()
        ) {
            $soThuTu++;

            $maNguoiDung = 'ND' . str_pad(
                (string) $soThuTu,
                4,
                '0',
                STR_PAD_LEFT
            );
        }

        DB::table('nguoi_dung')->insert([
            'ma_nguoi_dung' => $maNguoiDung,
            'ho_ten' => $validated['ho_ten'],
            'ten_dang_nhap' => $validated['ten_dang_nhap'],
            'thu_dien_tu_mo_phong' => $validated['thu_dien_tu_mo_phong'],
            'mat_khau_hash' => Hash::make($validated['mat_khau']),
            'vai_tro' => $validated['vai_tro'],
            'ma_ncc' => null,
            'so_dien_thoai_mo_phong' => null,
            'so_thich' => null,
            'ngay_tao' => now()->toDateString(),
            'trang_thai' => 'kich_hoat',
        ]);

        return response()->json([
            'message' => 'Đăng ký thành công',
            'user' => [
                'ma_nguoi_dung' => $maNguoiDung,
                'ho_ten' => $validated['ho_ten'],
                'ten_dang_nhap' => $validated['ten_dang_nhap'],
                'thu_dien_tu_mo_phong' => $validated['thu_dien_tu_mo_phong'],
                'vai_tro' => $validated['vai_tro'],
                'trang_thai' => 'kich_hoat',
            ],
        ], 201);
    }

    /**
     * Đăng nhập
     */
    public function dangNhap(Request $request)
{
    $validated = $request->validate([
        'ten_dang_nhap' => 'required|string',
        'mat_khau' => 'required|string',
    ]);

    $nguoiDung = DB::table('nguoi_dung')
        ->where('ten_dang_nhap', $validated['ten_dang_nhap'])
        ->first();

    // Không tìm thấy tài khoản hoặc sai mật khẩu
    if (
        !$nguoiDung ||
        !Hash::check($validated['mat_khau'], $nguoiDung->mat_khau_hash)
    ) {
        return response()->json([
            'message' => 'Sai tên đăng nhập hoặc mật khẩu'
        ], 401);
    }

    // Kiểm tra tài khoản có đang bị khóa hay không
    if ($nguoiDung->trang_thai !== 'kich_hoat') {
        return response()->json([
            'message' => 'Tài khoản đã bị khóa'
        ], 403);
    }

    // Tạo session mới để tránh session fixation
    $request->session()->regenerate();

    // Lưu thông tin người dùng vào session
    $request->session()->put(
        'ma_nguoi_dung',
        $nguoiDung->ma_nguoi_dung
    );

    $request->session()->put(
        'vai_tro',
        $nguoiDung->vai_tro
    );

    return response()->json([
        'message' => 'Đăng nhập thành công',
        'user' => [
            'ma_nguoi_dung' => $nguoiDung->ma_nguoi_dung,
            'ho_ten' => $nguoiDung->ho_ten,
            'ten_dang_nhap' => $nguoiDung->ten_dang_nhap,
            'thu_dien_tu_mo_phong' => $nguoiDung->thu_dien_tu_mo_phong,
            'vai_tro' => $nguoiDung->vai_tro,
            'trang_thai' => $nguoiDung->trang_thai,
        ],
    ]);
}

/**
 * Đăng xuất
 */
public function dangXuat(Request $request)
{
    // Xóa toàn bộ dữ liệu session
    $request->session()->invalidate();

    // Tạo CSRF token mới cho session tiếp theo
    $request->session()->regenerateToken();

    return response()->json([
        'message' => 'Đăng xuất thành công'
    ]);
}

/**
 * Lấy thông tin người dùng đang đăng nhập
 */
public function me(Request $request)
{
    if (!$request->session()->has('ma_nguoi_dung')) {
        return response()->json([
            'message' => 'Unauthenticated.'
        ], 401);
    }

    $maNguoiDung = $request->session()->get('ma_nguoi_dung');

    $user = DB::table('nguoi_dung')
        ->where('ma_nguoi_dung', $maNguoiDung)
        ->first();

    if (!$user) {
        return response()->json([
            'message' => 'Unauthenticated.'
        ], 401);
    }

    return response()->json([
        'ma_nguoi_dung' => $user->ma_nguoi_dung,
        'ho_ten' => $user->ho_ten,
        'vai_tro' => $user->vai_tro,
    ], 200);
}
}