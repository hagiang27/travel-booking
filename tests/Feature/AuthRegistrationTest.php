<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\Support\SanPhamTestSchema;
use Tests\TestCase;

class AuthRegistrationTest extends TestCase
{
    public function test_user_can_register_as_customer(): void
    {
        SanPhamTestSchema::create();

        $response = $this->postJson('/api/v1/register', [
            'ho_ten' => 'Nguyen Van Test',
            'ten_dang_nhap' => 'user_test',
            'thu_dien_tu_mo_phong' => 'user_test@example.com',
            'mat_khau' => 'password123',
        ]);

        $response->assertCreated()
            ->assertJsonPath('user.ten_dang_nhap', 'user_test')
            ->assertJsonPath('user.vai_tro', 'khach_hang')
            ->assertJsonPath('user.trang_thai', 'kich_hoat');

        $this->assertDatabaseHas('nguoi_dung', [
            'ten_dang_nhap' => 'user_test',
            'vai_tro' => 'khach_hang',
            'trang_thai' => 'kich_hoat',
        ]);

        $nguoiDung = DB::table('nguoi_dung')
            ->where('ten_dang_nhap', 'user_test')
            ->first();

        $this->assertNotNull($nguoiDung);
        $this->assertTrue(
            Hash::check('password123', $nguoiDung->mat_khau_hash)
        );
    }

    public function test_public_registration_cannot_assign_privileged_role(): void
    {
        SanPhamTestSchema::create();

        $response = $this->postJson('/api/v1/register', [
            'ho_ten' => 'Nguyen Van Test',
            'ten_dang_nhap' => 'user_test',
            'thu_dien_tu_mo_phong' => 'user_test@example.com',
            'mat_khau' => 'password123',
            'vai_tro' => 'quan_tri',
            'ma_ncc' => 'NCC001',
        ]);

        $response->assertCreated()
            ->assertJsonPath('user.vai_tro', 'khach_hang');

        $this->assertDatabaseHas('nguoi_dung', [
            'ten_dang_nhap' => 'user_test',
            'vai_tro' => 'khach_hang',
            'ma_ncc' => null,
        ]);
    }
}
