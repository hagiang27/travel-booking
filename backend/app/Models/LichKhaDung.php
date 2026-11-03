<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LichKhaDung extends Model
{
    protected $table = 'lich_kha_dung';

    protected $primaryKey = 'ma_lich';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'ma_lich',
        'ma_san_pham',
        'ngay_khoi_hanh',
        'tong_cho',
        'da_dat',
        'con_lai',
        'trang_thai',
    ];

    public function sanPham()
    {
        return $this->belongsTo(
            SanPham::class,
            'ma_san_pham',
            'ma_san_pham'
        );
    }
}