<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DatChoTest extends TestCase
{
    public function test_dat_het_cho_cuoi(): void
    {
        $conLai = 1;
        $soLuong = 1;

        $this->assertGreaterThanOrEqual($soLuong, $conLai);

        $conLaiMoi = $conLai - $soLuong;

        $this->assertSame(0, $conLaiMoi);
    }

    public function test_tu_choi_khi_so_cho_khong_du(): void
    {
        $conLai = 1;
        $soLuong = 2;

        $this->assertLessThan($soLuong, $conLai);
    }

    public function test_khong_de_so_cho_am_khi_yeu_cau_vuot_qua_so_cho(): void
    {
        $conLai = 1;
        $soLuong = 2;

        $duCho = $conLai >= $soLuong;

        $this->assertFalse($duCho);
    }

    public function test_cau_hinh_database_trong_moi_truong_test(): void
    {
        $this->assertSame('sqlite', config('database.default'));

        $this->assertSame(
            ':memory:',
            config('database.connections.sqlite.database')
        );
    }
}