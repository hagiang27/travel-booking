<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SanPham extends Model
{
    protected $table = 'san_pham';

    protected $primaryKey = 'ma_san_pham';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'ma_san_pham',
        'ten_san_pham',
        'duong_dan_tinh',
        'ma_ncc',
        'ma_diem_den',
        'loai_san_pham',
        'gia_co_ban',
        'don_vi_tinh',
        'so_ngay',
        'suc_chua_toi_da',
        'chinh_sach_huy',
        'diem_danh_gia_tb',
        'so_luot_danh_gia',
        'trang_thai',
        'ngay_tao',
    ];

    public function diemDen()
    {
        return $this->belongsTo(
            DiemDen::class,
            'ma_diem_den',
            'ma_diem_den'
        );
    }

    public function lichKhaDung()
    {
        return $this->hasMany(
            LichKhaDung::class,
            'ma_san_pham',
            'ma_san_pham'
        );
    }
}