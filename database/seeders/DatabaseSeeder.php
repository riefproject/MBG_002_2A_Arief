<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\BahanBaku;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin Gudang',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'gudang',
        ]);

        User::factory()->create([
            'name' => 'Dapur User',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
            'role' => 'dapur',
        ]);

        $this->call([
            UserSeeder::class,
            BahanBakuSeeder::class,
            PermintaanSeeder::class,
        ]);

    }
}
