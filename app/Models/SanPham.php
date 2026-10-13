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