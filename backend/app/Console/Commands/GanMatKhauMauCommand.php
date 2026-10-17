<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class GanMatKhauMauCommand extends Command
{
    protected $signature = 'app:gan-mat-khau-mau';
    protected $description = 'Gan mat khau mau cho toan bo nguoi dung va 5 tai khoan test theo vai tro';

    public function handle(): int
    {
        DB::table('nguoi_dung')->update([
            'mat_khau_hash' => Hash::make('MatKhau@2026'),
        ]);
        $this->info('Da gan mat khau mac dinh cho toan bo nguoi dung.');

        $taiKhoanTest = [
            'quan_tri'       => 'Admin@123',
            'nha_cung_cap'   => 'NhaCungCap@123',
            'khach_hang'     => 'KhachHang@123',
            'huong_dan_vien' => 'HuongDan@123',
            'kiem_thu'       => 'KiemThu@123',
        ];

        foreach ($taiKhoanTest as $vaiTro => $matKhau) {
            $nguoiDung = DB::table('nguoi_dung')->where('vai_tro', $vaiTro)->first();
            if ($nguoiDung) {
                DB::table('nguoi_dung')
                    ->where('ma_nguoi_dung', $nguoiDung->ma_nguoi_dung)
                    ->update(['mat_khau_hash' => Hash::make($matKhau)]);
                $this->info("Vai tro {$vaiTro}: {$nguoiDung->ten_dang_nhap} / {$matKhau}");
            } else {
                $this->warn("Khong tim thay tai khoan cho vai tro: {$vaiTro}");
            }
        }

        return self::SUCCESS;
    }
}