<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChiTietDon extends Model
{
    protected $table = 'chi_tiet_don';

    protected $primaryKey = 'ma_chi_tiet';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'ma_chi_tiet',
        'ma_don',
        'ma_san_pham',
        'ma_lich',
        'ngay_khoi_hanh',
        'so_luong',
        'don_gia',
        'thanh_tien',
    ];
}

