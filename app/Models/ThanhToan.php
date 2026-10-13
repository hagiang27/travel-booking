<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThanhToan extends Model
{
    protected $table = 'thanh_toan';

    protected $primaryKey = 'ma_thanh_toan';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'ma_thanh_toan',
        'ma_don',
        'phuong_thuc',
        'so_tien',
        'trang_thai',
        'ma_giao_dich_sandbox',
        'thoi_diem',
    ];
}