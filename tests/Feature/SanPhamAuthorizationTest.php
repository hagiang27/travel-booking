<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\Support\SanPhamTestSchema;
use Tests\TestCase;

class SanPhamAuthorizationTest extends TestCase
{
    public function test_unauthenticated_user_cannot_list_products(): void
    {
        $response = $this->getJson('/api/v1/san-pham');

        $response->assertUnauthorized();
    }

    public function test_customer_cannot_access_admin_route(): void
    {
        $response = $this
            ->withSession([
                'ma_nguoi_dung' => 'ND0001',
                'vai_tro' => 'khach_hang',
            ])
            ->getJson('/api/v1/admin/test');

        $response->assertForbidden();
    }

    public function test_admin_can_list_products(): void
    {
        SanPhamTestSchema::create();
        $this->taoAdminTest();

        $response = $this
            ->withSession(['ma_nguoi_dung' => 'ND0001'])
            ->getJson('/api/v1/san-pham');

        $response->assertOk();
        $response->assertJsonStructure([
            'data',
            'current_page',
            'per_page',
            'total',
        ]);
    }

    public function test_admin_can_update_product(): void
    {
        SanPhamTestSchema::create();
        $this->taoSanPhamMau();
        $this->taoAdminTest();

        $response = $this
            ->withSession(['ma_nguoi_dung' => 'ND0001'])
            ->putJson('/api/v1/san-pham/SP0001', [
                'ten_san_pham' => 'Tour test da cap nhat',
            ]);

        $response->assertOk();
        $response->assertJsonPath(
            'data.ten_san_pham',
            'Tour test da cap nhat'
        );

        $this->assertDatabaseHas('san_pham', [
            'ma_san_pham' => 'SP0001',
            'ten_san_pham' => 'Tour test da cap nhat',
        ]);
    }

    public function test_admin_cannot_update_product_with_invalid_price(): void
    {
        SanPhamTestSchema::create();
        $this->taoSanPhamMau();
        $this->taoAdminTest();

        $response = $this
            ->withSession(['ma_nguoi_dung' => 'ND0001'])
            ->putJson('/api/v1/san-pham/SP0001', [
                'gia_co_ban' => -100,
            ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['gia_co_ban']);
    }

    public function test_admin_can_delete_unused_product(): void
    {
        SanPhamTestSchema::create();
        $this->taoSanPhamMau();
        $this->taoAdminTest();

        $response = $this
            ->withSession(['ma_nguoi_dung' => 'ND0001'])
            ->deleteJson('/api/v1/san-pham/SP0001');

        $response->assertOk();

        $this->assertDatabaseMissing('san_pham', [
            'ma_san_pham' => 'SP0001',
        ]);
    }

    public function test_admin_cannot_delete_product_used_in_order(): void
    {
        SanPhamTestSchema::create();
        $this->taoSanPhamMau();
        $this->taoAdminTest();

        DB::table('chi_tiet_don')->insert([
            'ma_chi_tiet' => 'CT000001',
            'ma_don' => 'DH00001',
            'ma_san_pham' => 'SP0001',
            'ma_lich' => 'LICH00001',
            'ngay_khoi_hanh' => '2026-11-01',
            'so_luong' => 2,
            'don_gia' => 1000000,
            'thanh_tien' => 2000000,
        ]);

        $response = $this
            ->withSession(['ma_nguoi_dung' => 'ND0001'])
            ->deleteJson('/api/v1/san-pham/SP0001');

        $response->assertStatus(409);

        $this->assertDatabaseHas('san_pham', [
            'ma_san_pham' => 'SP0001',
        ]);
    }

    public function test_admin_cannot_create_product_with_invalid_price(): void
    {
        SanPhamTestSchema::create();
        $this->taoAdminTest();

        $response = $this
            ->withSession(['ma_nguoi_dung' => 'ND0001'])
            ->postJson('/api/v1/san-pham', [
                'ten_san_pham' => 'Tour test',
                'duong_dan_tinh' => 'tour-test',
                'ma_ncc' => 'NCC001',
                'ma_diem_den' => 'DD001',
                'loai_san_pham' => 'Tour',
                'gia_co_ban' => -100,
                'don_vi_tinh' => 'nguoi',
                'so_ngay' => 2,
                'suc_chua_toi_da' => 10,
                'trang_thai' => 'dang_ban',
                'ngay_tao' => '2026-10-23',
            ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['gia_co_ban']);
    }

    public function test_supplier_cannot_update_another_supplier_product(): void
    {
        SanPhamTestSchema::create();
        $this->taoSanPhamMau();
        $this->taoNhaCungCapKhacVaSanPham();
        $this->taoNhaCungCapTest();

        $response = $this
            ->withSession(['ma_nguoi_dung' => 'ND0002'])
            ->putJson('/api/v1/san-pham/SP0002', [
                'ten_san_pham' => 'Khong duoc phep sua',
            ]);

        $response->assertNotFound();

        $this->assertDatabaseHas('san_pham', [
            'ma_san_pham' => 'SP0002',
            'ten_san_pham' => 'San pham NCC002',
        ]);
    }

    public function test_supplier_cannot_delete_another_supplier_product(): void
    {
        SanPhamTestSchema::create();
        $this->taoSanPhamMau();
        $this->taoNhaCungCapKhacVaSanPham();
        $this->taoNhaCungCapTest();

        $response = $this
            ->withSession(['ma_nguoi_dung' => 'ND0002'])
            ->deleteJson('/api/v1/san-pham/SP0002');

        $response->assertNotFound();

        $this->assertDatabaseHas('san_pham', [
            'ma_san_pham' => 'SP0002',
            'ma_ncc' => 'NCC002',
        ]);
    }

    private function taoNhaCungCapTest(): void
    {
        DB::table('nguoi_dung')->insert([
            'ma_nguoi_dung' => 'ND0002',
            'ho_ten' => 'Nha cung cap test',
            'ten_dang_nhap' => 'ncc_test',
            'thu_dien_tu_mo_phong' => 'ncc_test@example.com',
            'mat_khau_hash' => '',
            'vai_tro' => 'nha_cung_cap',
            'ma_ncc' => 'NCC001',
            'ngay_tao' => '2026-10-23',
            'trang_thai' => 'kich_hoat',
        ]);
    }

    private function taoNhaCungCapKhacVaSanPham(): void
    {
        DB::table('nha_cung_cap')->insert([
            'ma_ncc' => 'NCC002',
            'ten_ncc' => 'Nha cung cap khac',
            'trang_thai_hop_tac' => 'dang_hop_tac',
        ]);

        DB::table('san_pham')->insert([
            'ma_san_pham' => 'SP0002',
            'ten_san_pham' => 'San pham NCC002',
            'duong_dan_tinh' => 'san-pham-ncc002',
            'ma_ncc' => 'NCC002',
            'ma_diem_den' => 'DD001',
            'loai_san_pham' => 'Tour',
            'gia_co_ban' => 1000000,
            'don_vi_tinh' => 'nguoi',
            'so_ngay' => 2,
            'suc_chua_toi_da' => 10,
            'chinh_sach_huy' => null,
            'diem_danh_gia_tb' => 0,
            'so_luot_danh_gia' => 0,
            'trang_thai' => 'dang_ban',
            'ngay_tao' => '2026-10-23',
        ]);
    }

    private function taoAdminTest(): void
    {
        DB::table('nguoi_dung')->insert([
            'ma_nguoi_dung' => 'ND0001',
            'ho_ten' => 'Quan tri test',
            'ten_dang_nhap' => 'admin_test',
            'thu_dien_tu_mo_phong' => 'admin_test@example.com',
            'mat_khau_hash' => '',
            'vai_tro' => 'quan_tri',
            'ma_ncc' => null,
            'ngay_tao' => '2026-10-23',
            'trang_thai' => 'kich_hoat',
        ]);
    }

    private function taoSanPhamMau(): void
    {
        DB::table('nha_cung_cap')->insert([
            'ma_ncc' => 'NCC001',
            'ten_ncc' => 'Nha cung cap test',
            'trang_thai_hop_tac' => 'dang_hop_tac',
        ]);

        DB::table('diem_den')->insert([
            'ma_diem_den' => 'DD001',
            'ten_diem_den' => 'Diem den test',
            'duong_dan_tinh' => 'diem-den-test',
            'tinh_thanh' => 'Ha Noi',
            'vung_mien' => 'Mien Bac',
            'loai_hinh' => 'Van hoa',
            'vi_do' => 21.028,
            'kinh_do' => 105.854,
            'trang_thai' => 'hoat_dong',
        ]);

        DB::table('san_pham')->insert([
            'ma_san_pham' => 'SP0001',
            'ten_san_pham' => 'Tour test ban dau',
            'duong_dan_tinh' => 'tour-test-ban-dau',
            'ma_ncc' => 'NCC001',
            'ma_diem_den' => 'DD001',
            'loai_san_pham' => 'Tour',
            'gia_co_ban' => 1000000,
            'don_vi_tinh' => 'nguoi',
            'so_ngay' => 2,
            'suc_chua_toi_da' => 10,
            'chinh_sach_huy' => null,
            'diem_danh_gia_tb' => 0,
            'so_luot_danh_gia' => 0,
            'trang_thai' => 'dang_ban',
            'ngay_tao' => '2026-10-23',
        ]);
    }
}
