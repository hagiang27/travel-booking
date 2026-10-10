<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'khachhang@test.com'],
            [
                'name' => 'Khách hàng Demo',
                'password' => Hash::make('12345678'),
                'role' => 'khach_hang',
            ]
        );

        User::updateOrCreate(
            ['email' => 'nhacungcap@test.com'],
            [
                'name' => 'Nhà cung cấp Demo',
                'password' => Hash::make('12345678'),
                'role' => 'nha_cung_cap',
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Quản trị viên Demo',
                'password' => Hash::make('12345678'),
                'role' => 'quan_tri',
            ]
        );
    }
}