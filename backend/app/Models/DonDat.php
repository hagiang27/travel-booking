<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DonDat extends Model
{
    protected $table = 'don_dat';

    protected $primaryKey = 'ma_don';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'ma_don',
        'ma_nguoi_dung',
        'ngay_dat',
        'trang_thai',
        'tong_tien',
        'phi_huy',
        'so_tien_hoan',
        'kenh_dat',
        'ghi_chu',
    ];
}