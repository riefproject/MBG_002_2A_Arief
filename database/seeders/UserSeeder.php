<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database with predefined users.
     */
    public function run(): void
    {
        $defaultPassword = Hash::make('pass123');
        $demoPassword = Hash::make('password');

        $users = [
            [
                'id' => 1,
                'name' => 'Budi Santoso',
                'email' => 'budi.gudang@mbg.id',
                'password' => $defaultPassword,
                'role' => 'gudang',
                'created_at' => Carbon::parse('2025-09-01 08:00:00'),
            ],
            [
                'id' => 2,
                'name' => 'Siti Aminah',
                'email' => 'siti.gudang@mbg.id',
                'password' => $defaultPassword,
                'role' => 'gudang',
                'created_at' => Carbon::parse('2025-09-01 08:05:00'),
            ],
            [
                'id' => 3,
                'name' => 'Rahmat Hidayat',
                'email' => 'rahmat.gudang@mbg.id',
                'password' => $defaultPassword,
                'role' => 'gudang',
                'created_at' => Carbon::parse('2025-09-01 08:10:00'),
            ],
            [
                'id' => 4,
                'name' => 'Lina Marlina',
                'email' => 'lina.gudang@mbg.id',
                'password' => $defaultPassword,
                'role' => 'gudang',
                'created_at' => Carbon::parse('2025-09-01 08:15:00'),
            ],
            [
                'id' => 5,
                'name' => 'Anton Saputra',
                'email' => 'anton.gudang@mbg.id',
                'password' => $defaultPassword,
                'role' => 'gudang',
                'created_at' => Carbon::parse('2025-09-01 08:20:00'),
            ],
            [
                'id' => 6,
                'name' => 'Dewi Lestari',
                'email' => 'dewi.dapur@mbg.id',
                'password' => $defaultPassword,
                'role' => 'dapur',
                'created_at' => Carbon::parse('2025-09-01 08:30:00'),
            ],
            [
                'id' => 7,
                'name' => 'Andi Pratama',
                'email' => 'andi.dapur@mbg.id',
                'password' => $defaultPassword,
                'role' => 'dapur',
                'created_at' => Carbon::parse('2025-09-01 08:35:00'),
            ],
            [
                'id' => 8,
                'name' => 'Maria Ulfa',
                'email' => 'maria.dapur@mbg.id',
                'password' => $defaultPassword,
                'role' => 'dapur',
                'created_at' => Carbon::parse('2025-09-01 08:40:00'),
            ],
            [
                'id' => 9,
                'name' => 'Surya Kurnia',
                'email' => 'surya.dapur@mbg.id',
                'password' => $defaultPassword,
                'role' => 'dapur',
                'created_at' => Carbon::parse('2025-09-01 08:45:00'),
            ],
            [
                'id' => 10,
                'name' => 'Yanti Fitri',
                'email' => 'yanti.dapur@mbg.id',
                'password' => $defaultPassword,
                'role' => 'dapur',
                'created_at' => Carbon::parse('2025-09-01 08:50:00'),
            ],
            [
                'id' => 11,
                'name' => 'Dapur User',
                'email' => 'user@example.com',
                'password' => $demoPassword,
                'role' => 'dapur',
                'created_at' => Carbon::now(),
            ],
            [
                'id' => 12,
                'name' => 'Admin Gudang',
                'email' => 'admin@example.com',
                'password' => $demoPassword,
                'role' => 'gudang',
                'created_at' => Carbon::now(),
            ],
            [
                'id' => 13,
                'name' => 'Gudang A',
                'email' => 'gudang.a@example.com',
                'password' => Hash::make('gudang.a'),
                'role' => 'gudang',
                'created_at' => Carbon::now(),
            ],
            [
                'id' => 14,
                'name' => 'Gudang B',
                'email' => 'gudang.b@example.com',
                'password' => Hash::make('gudang.b'),
                'role' => 'gudang',
                'created_at' => Carbon::now(),
            ],
            [
                'id' => 15,
                'name' => 'Dapur A',
                'email' => 'dapur.a@example.com',
                'password' => Hash::make('dapur.a'),
                'role' => 'dapur',
                'created_at' => Carbon::now(),
            ],
            [
                'id' => 16,
                'name' => 'Dapur B',
                'email' => 'dapur.b@example.com',
                'password' => Hash::make('dapur.b'),
                'role' => 'dapur',
                'created_at' => Carbon::now(),
            ],
            [
                'id' => 17,
                'name' => 'Dapur C',
                'email' => 'dapur.c@example.com',
                'password' => Hash::make('dapur.c'),
                'role' => 'dapur',
                'created_at' => Carbon::now(),
            ],
        ];

        DB::table('users')->upsert(
            $users,
            ['id'],
            ['name', 'email', 'password', 'role', 'created_at']
        );
    }
}
