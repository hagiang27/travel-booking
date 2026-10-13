<?php

namespace Tests\Feature;

use Tests\TestCase;

class DatTourTest extends TestCase
{
    /**
     * Khách chưa đăng nhập không được đặt tour.
     */
    public function test_khach_chua_dang_nhap_khong_the_dat_tour(): void
    {
        $response = $this->postJson('/api/v1/bookings', [
            'ma_lich' => 'LKD002152',
            'so_luong' => 1,
        ]);

        $response->assertUnauthorized();
    }

    /**
     * Từ chối yêu cầu đặt tour có số lượng bằng 0.
     */
    public function test_tu_choi_so_luong_dat_tour_bang_khong(): void
    {
        $response = $this->withSession([
            'ma_nguoi_dung' => 'ND0115',
        ])->postJson('/api/v1/bookings', [
            'ma_lich' => 'LKD002152',
            'so_luong' => 0,
        ]);

        $response->assertUnprocessable();
    }
}