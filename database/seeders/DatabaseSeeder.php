<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create demo admin user
        User::factory()->create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'gudang',
        ]);

        // Create demo regular user
        User::factory()->create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
            'role' => 'dapur',
        ]);

        User::factory(8)->create();

        BahanBaku::factory()->create([
            'nama' => 'Beras Medium',
            'kategori' => 'Karbohidrat',
            'jumlah' => 500,
            'satuan' => 'kg',
            'tanggal_masuk' => now(),
            'status' => 'tersedia'
        ])

        BahanBaku::factory()->create([
            'nama' => 'Telur Ayam',
            'kategori' => 'Protein Hewani',
            'jumlah' => 2000,
            'satuan' => 'butir',
            'tanggal_masuk' => now(),
            'status' => 'tersedia'
        ])

    }
}
