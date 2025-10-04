<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BahanBakuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('bahan_baku')->insert([
            [
                'id' => 1,
                'nama' => 'Beras Medium',
                'kategori' => 'Karbohidrat',
                'jumlah' => 500,
                'satuan' => 'kg',
                'tanggal_masuk' => '2025-09-01',
                'tanggal_kadaluarsa' => '2026-03-01',
                'status' => 'tersedia',
                'created_at' => '2025-09-01 09:00:00',
            ],
            [
                'id' => 2,
                'nama' => 'Telur Ayam',
                'kategori' => 'Protein Hewani',
                'jumlah' => 2000,
                'satuan' => 'butir',
                'tanggal_masuk' => '2025-09-20',
                'tanggal_kadaluarsa' => '2025-10-10',
                'status' => 'tersedia',
                'created_at' => '2025-09-20 09:05:00',
            ],
            [
                'id' => 3,
                'nama' => 'Daging Ayam Broiler',
                'kategori' => 'Protein Hewani',
                'jumlah' => 300,
                'satuan' => 'kg',
                'tanggal_masuk' => '2025-09-22',
                'tanggal_kadaluarsa' => '2025-10-02',
                'status' => 'segera_kadaluarsa',
                'created_at' => '2025-09-22 09:10:00',
            ],
            [
                'id' => 4,
                'nama' => 'Tahu Putih',
                'kategori' => 'Protein Nabati',
                'jumlah' => 400,
                'satuan' => 'potong',
                'tanggal_masuk' => '2025-09-28',
                'tanggal_kadaluarsa' => '2025-10-01',
                'status' => 'kadaluarsa',
                'created_at' => '2025-09-28 09:15:00',
            ],
            [
                'id' => 5,
                'nama' => 'Tempe',
                'kategori' => 'Protein Nabati',
                'jumlah' => 350,
                'satuan' => 'potong',
                'tanggal_masuk' => '2025-09-27',
                'tanggal_kadaluarsa' => '2025-10-03',
                'status' => 'segera_kadaluarsa',
                'created_at' => '2025-09-27 09:20:00',
            ],
            [
                'id' => 6,
                'nama' => 'Sayur Bayam',
                'kategori' => 'Sayuran',
                'jumlah' => 150,
                'satuan' => 'ikat',
                'tanggal_masuk' => '2025-09-29',
                'tanggal_kadaluarsa' => '2025-10-01',
                'status' => 'segera_kadaluarsa',
                'created_at' => '2025-09-29 09:25:00',
            ],
            [
                'id' => 7,
                'nama' => 'Wortel',
                'kategori' => 'Sayuran',
                'jumlah' => 100,
                'satuan' => 'kg',
                'tanggal_masuk' => '2025-09-21',
                'tanggal_kadaluarsa' => '2025-10-15',
                'status' => 'tersedia',
                'created_at' => '2025-09-21 09:30:00',
            ],
            [
                'id' => 8,
                'nama' => 'Kentang',
                'kategori' => 'Karbohidrat',
                'jumlah' => 120,
                'satuan' => 'kg',
                'tanggal_masuk' => '2025-09-23',
                'tanggal_kadaluarsa' => '2025-11-20',
                'status' => 'tersedia',
                'created_at' => '2025-09-23 09:35:00',
            ],
            [
                'id' => 9,
                'nama' => 'Minyak Goreng Sawit',
                'kategori' => 'Bahan Masak',
                'jumlah' => 80,
                'satuan' => 'liter',
                'tanggal_masuk' => '2025-09-15',
                'tanggal_kadaluarsa' => '2026-01-01',
                'status' => 'tersedia',
                'created_at' => '2025-09-15 09:40:00',
            ],
            [
                'id' => 10,
                'nama' => 'Susu Bubuk Fortifikasi',
                'kategori' => 'Protein Hewani',
                'jumlah' => 50,
                'satuan' => 'kg',
                'tanggal_masuk' => '2025-09-10',
                'tanggal_kadaluarsa' => '2025-12-10',
                'status' => 'tersedia',
                'created_at' => '2025-09-10 09:45:00',
            ],
        ]);

        if (DB::connection()->getDriverName() === 'pgsql') {
            $maxId = DB::table('bahan_baku')->max('id');

            if ($maxId === null) {
                DB::statement("SELECT setval(pg_get_serial_sequence('bahan_baku','id'), 1, false)");
            } else {
                $maxId = (int) $maxId;
                DB::statement("SELECT setval(pg_get_serial_sequence('bahan_baku','id'), {$maxId}, true)");
            }
        }
    }
}
