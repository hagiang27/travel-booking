<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

class HuyDonDatHetHan extends Command
{
    protected $signature = 'app:huy-don-dat-het-han';

    protected $description = 'Hủy đơn đặt tour hết hạn giữ chỗ và hoàn trả số chỗ';

    public function handle(): int
    {
        $soDonDaXuLy = 0;

        // Tìm các đơn chờ thanh toán đã hết hạn.
        $maDonHetHan = DB::table('don_dat')
            ->where('trang_thai', 'cho_thanh_toan')
            ->whereNotNull('hold_expires_at')
            ->where('hold_expires_at', '<=', now())
            ->orderBy('hold_expires_at')
            ->pluck('ma_don');

        foreach ($maDonHetHan as $maDon) {
            try {
                $daXuLy = DB::transaction(function () use ($maDon) {
                    // Khóa đơn để tránh xử lý cùng một đơn nhiều lần.
                    $don = DB::table('don_dat')
                        ->where('ma_don', $maDon)
                        ->lockForUpdate()
                        ->first();

                    // Kiểm tra lại trạng thái sau khi khóa.
                    if (
                        !$don ||
                        $don->trang_thai !== 'cho_thanh_toan' ||
                        !$don->hold_expires_at ||
                        $don->hold_expires_at > now()->toDateTimeString()
                    ) {
                        return false;
                    }

                    // Lấy các chi tiết đơn.
                    $chiTietDon = DB::table('chi_tiet_don')
                        ->where('ma_don', $maDon)
                        ->get();

                    // Khóa từng lịch và hoàn trả số chỗ.
                    foreach ($chiTietDon as $chiTiet) {
                        $lich = DB::table('lich_kha_dung')
                            ->where('ma_lich', $chiTiet->ma_lich)
                            ->lockForUpdate()
                            ->first();

                        if (!$lich) {
                            throw new \RuntimeException(
                                "Không tìm thấy lịch {$chiTiet->ma_lich} của đơn {$maDon}."
                            );
                        }

                        $soLuong = (int) $chiTiet->so_luong;

                        if ($soLuong <= 0) {
                            throw new \RuntimeException(
                                "Số lượng không hợp lệ trong đơn {$maDon}."
                            );
                        }

                        $daDatMoi = max(
                            0,
                            (int) $lich->da_dat - $soLuong
                        );

                        $conLaiMoi = min(
                            (int) $lich->tong_cho,
                            (int) $lich->con_lai + $soLuong
                        );

                        DB::table('lich_kha_dung')
                            ->where('ma_lich', $chiTiet->ma_lich)
                            ->update([
                                'da_dat' => $daDatMoi,
                                'con_lai' => $conLaiMoi,
                                'trang_thai' => 'mo_ban',
                            ]);
                    }

                    // Hủy các khoản thanh toán còn chờ xử lý.
                    DB::table('thanh_toan')
                        ->where('ma_don', $maDon)
                        ->where('trang_thai', 'cho_xu_ly')
                        ->update([
                            'trang_thai' => 'nguoi_dung_huy',
                        ]);

                    // Chuyển trạng thái đơn cuối cùng.
                    DB::table('don_dat')
                        ->where('ma_don', $maDon)
                        ->where('trang_thai', 'cho_thanh_toan')
                        ->update([
                            'trang_thai' => 'da_huy',
                            'ghi_chu' => 'Đơn tự động hủy do hết thời hạn giữ chỗ.',
                        ]);

                    return true;
                });

                if ($daXuLy) {
                    $soDonDaXuLy++;
                    $this->info("Đã hủy đơn {$maDon} và hoàn trả chỗ.");
                }
            } catch (Throwable $e) {
                report($e);
                $this->error(
                    "Không xử lý được đơn {$maDon}. Chi tiết lỗi đã được ghi log."
                );
            }
        }

        $this->info("Hoàn tất. Số đơn đã xử lý: {$soDonDaXuLy}");

        return Command::SUCCESS;
    }
}