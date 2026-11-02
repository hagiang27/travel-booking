<?php

namespace App\Policies;

class SanPhamPolicy
{
    public function xemSua(object $nguoiDung, object $sanPham): bool
    {
        return match ($nguoiDung->vai_tro) {
            'quan_tri' => true,
            'nha_cung_cap' => $sanPham->ma_ncc === $nguoiDung->ma_ncc,
            default => false,
        };
    }
}