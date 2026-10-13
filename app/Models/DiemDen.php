<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiemDen extends Model
{
    protected $table = 'diem_den';

    protected $primaryKey = 'ma_diem_den';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;
}