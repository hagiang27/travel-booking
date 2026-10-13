<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Throwable;

class DatTourController extends Controller
{
    public function datTour(Request $request)
    {
        // 1. Kiểm tra người dùng đã đăng nhập
        $maNguoiDung = $request->session()->get('ma_nguoi_dung');

        if (!$maNguoiDung) {
            return response()->json([
                'message' => 'Vui lòng đăng nhập trước khi đặt tour.',
            ], 401);
        }

        // 2. Kiểm tra dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'ma_lich' => ['required', 'string', 'size:9'],
            'so_luong' => ['required', 'integer', 'min:1', 'max:20'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Dữ liệu đặt tour không hợp lệ.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Khóa toàn bộ quá trình đặt tour để tránh sinh trùng mã đơn
        $khoaCapMa = Cache::lock('travel_booking:cap_ma', 60);

        if (!$khoaCapMa->get()) {
            return response()->json([
                'message' => 'Hệ thống đang xử lý đơn khác. Vui lòng thử lại.',
            ], 409);
        }

        try {
            try {
                $ketQua = DB::transaction(function () use (
                    $request,
                    $maNguoiDung
                ) {
                    // 3. Khóa lịch khởi hành trong giao dịch
                    $lich = DB::table('lich_kha_dung')
                        ->where('ma_lich', $request->ma_lich)
                        ->lockForUpdate()
                        ->first();

                    if (!$lich) {
                        throw ValidationException::withMessages([
                            'ma_lich' => 'Không tìm thấy lịch khởi hành.',
                        ]);
                    }

                    // 4. Kiểm tra lịch còn mở bán và chưa khởi hành
                    if (
                        $lich->trang_thai !== 'mo_ban' ||
                        $lich->ngay_khoi_hanh < now()->toDateString()
                    ) {
                        throw ValidationException::withMessages([
                            'ma_lich' => 'Lịch khởi hành không còn mở bán.',
                        ]);
                    }

                    $soLuong = (int) $request->so_luong;

                    // 5. Kiểm tra số chỗ
                    if ((int) $lich->con_lai < $soLuong) {
                        throw ValidationException::withMessages([
                            'so_luong' => 'Số chỗ còn lại không đủ.',
                        ]);
                    }

                    // 6. Kiểm tra sản phẩm còn bán
                    $sanPham = DB::table('san_pham')
                        ->where('ma_san_pham', $lich->ma_san_pham)
                        ->where('trang_thai', 'dang_ban')
                        ->first();

                    if (!$sanPham) {
                        throw ValidationException::withMessages([
                            'ma_lich' => 'Sản phẩm không còn được bán.',
                        ]);
                    }

                    // 7. Kiểm tra tài khoản và quyền đặt tour
                    $nguoiDung = DB::table('nguoi_dung')
                        ->where('ma_nguoi_dung', $maNguoiDung)
                        ->first();

                    if (
                        !$nguoiDung ||
                        $nguoiDung->trang_thai !== 'kich_hoat'
                    ) {
                        throw ValidationException::withMessages([
                            'nguoi_dung' =>
                                'Tài khoản không tồn tại hoặc không hoạt động.',
                        ]);
                    }

                    if ($nguoiDung->vai_tro !== 'khach_hang') {
                        throw new AuthorizationException(
                            'Chỉ khách hàng mới được phép đặt tour.'
                        );
                    }

                    // 8. Tính tiền theo giá trong cơ sở dữ liệu
                    $donGia = (int) $sanPham->gia_co_ban;
                    $thanhTien = $donGia * $soLuong;

                    // 9. Sinh mã đơn tiếp theo
                    $maDonLonNhat = DB::table('don_dat')
                        ->where('ma_don', 'like', 'DH%')
                        ->orderByDesc('ma_don')
                        ->value('ma_don');

                    $soDonTiepTheo = $maDonLonNhat
                        ? ((int) substr($maDonLonNhat, 2)) + 1
                        : 1;

                    if ($soDonTiepTheo > 99999) {
                        throw new \RuntimeException(
                            'Đã vượt quá giới hạn mã đơn DH99999.'
                        );
                    }

                    $maDon = 'DH' . str_pad(
                        (string) $soDonTiepTheo,
                        5,
                        '0',
                        STR_PAD_LEFT
                    );

                    // 10. Sinh mã chi tiết đơn tiếp theo
                    $maChiTietLonNhat = DB::table('chi_tiet_don')
                        ->where('ma_chi_tiet', 'like', 'CT%')
                        ->orderByDesc('ma_chi_tiet')
                        ->value('ma_chi_tiet');

                    $soChiTietTiepTheo = $maChiTietLonNhat
                        ? ((int) substr($maChiTietLonNhat, 2)) + 1
                        : 1;

                    if ($soChiTietTiepTheo > 999999) {
                        throw new \RuntimeException(
                            'Đã vượt quá giới hạn mã chi tiết CT999999.'
                        );
                    }

                    $maChiTiet = 'CT' . str_pad(
                        (string) $soChiTietTiepTheo,
                        6,
                        '0',
                        STR_PAD_LEFT
                    );

                    // 11. Tạo đơn đặt, giữ chỗ trong 15 phút
                    DB::table('don_dat')->insert([
                        'ma_don' => $maDon,
                        'ma_nguoi_dung' => $maNguoiDung,
                        'ngay_dat' => now()->toDateString(),
                        'trang_thai' => 'cho_thanh_toan',
                        'hold_expires_at' => now()->addMinutes(15),
                        'tong_tien' => $thanhTien,
                        'phi_huy' => 0,
                        'so_tien_hoan' => 0,
                        'kenh_dat' => 'website',
                        'ghi_chu' => null,
                    ]);

                    // 12. Tạo chi tiết đơn
                    DB::table('chi_tiet_don')->insert([
                        'ma_chi_tiet' => $maChiTiet,
                        'ma_don' => $maDon,
                        'ma_san_pham' => $sanPham->ma_san_pham,
                        'ma_lich' => $lich->ma_lich,
                        'ngay_khoi_hanh' => $lich->ngay_khoi_hanh,
                        'so_luong' => $soLuong,
                        'don_gia' => $donGia,
                        'thanh_tien' => $thanhTien,
                    ]);

                    // 13. Cập nhật số chỗ trong cùng giao dịch
                    $conLaiMoi = (int) $lich->con_lai - $soLuong;
                    $daDatMoi = (int) $lich->da_dat + $soLuong;

                    DB::table('lich_kha_dung')
                        ->where('ma_lich', $lich->ma_lich)
                        ->update([
                            'da_dat' => $daDatMoi,
                            'con_lai' => $conLaiMoi,
                            'trang_thai' => $conLaiMoi === 0
                                ? 'het_cho'
                                : 'mo_ban',
                        ]);

                    return [
                        'ma_don' => $maDon,
                        'ma_chi_tiet' => $maChiTiet,
                        'ma_lich' => $lich->ma_lich,
                        'so_luong' => $soLuong,
                        'don_gia' => $donGia,
                        'tong_tien' => $thanhTien,
                        'trang_thai' => 'cho_thanh_toan',
                        'hold_expires_at' => now()
                            ->addMinutes(15)
                            ->toDateTimeString(),
                        'con_lai' => $conLaiMoi,
                    ];
                });

                return response()->json([
                    'message' =>
                        'Đặt tour thành công. Vui lòng tiếp tục thanh toán.',
                    'data' => $ketQua,
                ], 201);
            } catch (ValidationException $e) {
                return response()->json([
                    'message' => 'Không thể đặt tour.',
                    'errors' => $e->errors(),
                ], 422);
            } catch (AuthorizationException $e) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], 403);
            } catch (Throwable $e) {
                report($e);

                return response()->json([
                    'message' => 'Đã xảy ra lỗi khi đặt tour. Vui lòng thử lại.',
                ], 500);
            }
        } finally {
            // Luôn giải phóng khóa, kể cả khi có lỗi
            $khoaCapMa->release();
        }
    }
}