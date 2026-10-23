<?php

namespace Tests\Support;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

final class SanPhamTestSchema
{
    public static function create(): void
    {
        Schema::create('nha_cung_cap', function (Blueprint $table) {
            $table->char('ma_ncc', 6)->primary();
            $table->string('ten_ncc', 150);
            $table->string('trang_thai_hop_tac', 30)->default('dang_hop_tac');
        });

        Schema::create('diem_den', function (Blueprint $table) {
            $table->char('ma_diem_den', 5)->primary();
            $table->string('ten_diem_den', 150);
            $table->string('duong_dan_tinh', 180)->unique();
            $table->string('tinh_thanh', 60);
            $table->string('vung_mien', 40);
            $table->string('loai_hinh', 60);
            $table->decimal('vi_do', 9, 6);
            $table->decimal('kinh_do', 9, 6);
            $table->string('trang_thai', 30)->default('hoat_dong');
        });

        Schema::create('nguoi_dung', function (Blueprint $table) {
            $table->char('ma_nguoi_dung', 6)->primary();
            $table->string('ho_ten', 100);
            $table->string('ten_dang_nhap', 80)->unique();
            $table->string('thu_dien_tu_mo_phong', 120)->unique();
            $table->string('mat_khau_hash', 255)->default('');
            $table->string('vai_tro', 30);
            $table->char('ma_ncc', 6)->nullable();
$table->string('so_dien_thoai_mo_phong', 30)->nullable();
$table->text('so_thich')->nullable();
$table->date('ngay_tao');
            $table->string('trang_thai', 30)->default('kich_hoat');
        });

        Schema::create('san_pham', function (Blueprint $table) {
            $table->char('ma_san_pham', 6)->primary();
            $table->string('ten_san_pham', 200);
            $table->string('duong_dan_tinh', 200)->unique();
            $table->char('ma_ncc', 6);
            $table->char('ma_diem_den', 5);
            $table->string('loai_san_pham', 50);
            $table->integer('gia_co_ban');
            $table->string('don_vi_tinh', 20);
            $table->integer('so_ngay')->default(1);
            $table->integer('suc_chua_toi_da');
            $table->string('chinh_sach_huy', 120)->nullable();
            $table->decimal('diem_danh_gia_tb', 3, 2)->default(0);
            $table->integer('so_luot_danh_gia')->default(0);
            $table->string('trang_thai', 30)->default('dang_ban');
            $table->date('ngay_tao');
        });

        Schema::create('chi_tiet_don', function (Blueprint $table) {
            $table->char('ma_chi_tiet', 8)->primary();
            $table->char('ma_don', 7);
            $table->char('ma_san_pham', 6);
            $table->char('ma_lich', 9);
            $table->date('ngay_khoi_hanh');
            $table->integer('so_luong');
            $table->integer('don_gia');
            $table->bigInteger('thanh_tien');
        });
    }
}
