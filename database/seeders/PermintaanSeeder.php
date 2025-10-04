<?php

namespace Database\Seeders;

use App\Models\Permintaan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PermintaanSeeder extends Seeder
{
    /**
     * Seed permintaan and permintaan_detail tables with predefined data.
     */
    public function run(): void
    {
        $permintaan = [
            [
                'id' => 1,
                'pemohon_id' => 6,
                'tgl_masak' => '2025-09-30',
                'menu_makan' => 'Nasi ayam goreng + sayur bayam',
                'jumlah_porsi' => 200,
                'status' => Permintaan::STATUS_DISETUJUI,
                'created_at' => Carbon::parse('2025-09-28 10:00:00'),
            ],
            [
                'id' => 2,
                'pemohon_id' => 7,
                'tgl_masak' => '2025-09-30',
                'menu_makan' => 'Tempe goreng + sayur wortel',
                'jumlah_porsi' => 150,
                'status' => Permintaan::STATUS_DISETUJUI,
                'created_at' => Carbon::parse('2025-09-28 10:05:00'),
            ],
            [
                'id' => 3,
                'pemohon_id' => 8,
                'tgl_masak' => '2025-10-01',
                'menu_makan' => 'Nasi + ayam rebus + bayam bening',
                'jumlah_porsi' => 180,
                'status' => Permintaan::STATUS_MENUNGGU,
                'created_at' => Carbon::parse('2025-09-29 10:10:00'),
            ],
            [
                'id' => 4,
                'pemohon_id' => 9,
                'tgl_masak' => '2025-10-01',
                'menu_makan' => 'Kentang balado + telur rebus',
                'jumlah_porsi' => 120,
                'status' => Permintaan::STATUS_DISETUJUI,
                'created_at' => Carbon::parse('2025-09-29 10:15:00'),
            ],
            [
                'id' => 5,
                'pemohon_id' => 10,
                'tgl_masak' => '2025-10-02',
                'menu_makan' => 'Nasi tempe orek + sayur sop',
                'jumlah_porsi' => 200,
                'status' => Permintaan::STATUS_MENUNGGU,
                'created_at' => Carbon::parse('2025-09-29 10:20:00'),
            ],
            [
                'id' => 6,
                'pemohon_id' => 6,
                'tgl_masak' => '2025-10-02',
                'menu_makan' => 'Ayam goreng tepung + wortel kukus',
                'jumlah_porsi' => 220,
                'status' => Permintaan::STATUS_DITOLAK,
                'created_at' => Carbon::parse('2025-09-29 10:25:00'),
            ],
            [
                'id' => 7,
                'pemohon_id' => 7,
                'tgl_masak' => '2025-10-03',
                'menu_makan' => 'Nasi telur dadar + bayam bening',
                'jumlah_porsi' => 180,
                'status' => Permintaan::STATUS_MENUNGGU,
                'created_at' => Carbon::parse('2025-09-30 10:30:00'),
            ],
            [
                'id' => 8,
                'pemohon_id' => 8,
                'tgl_masak' => '2025-10-03',
                'menu_makan' => 'Kentang rebus + ayam suwir',
                'jumlah_porsi' => 160,
                'status' => Permintaan::STATUS_MENUNGGU,
                'created_at' => Carbon::parse('2025-09-30 10:35:00'),
            ],
            [
                'id' => 9,
                'pemohon_id' => 9,
                'tgl_masak' => '2025-10-04',
                'menu_makan' => 'Nasi + tempe goreng + sayur bening',
                'jumlah_porsi' => 190,
                'status' => Permintaan::STATUS_MENUNGGU,
                'created_at' => Carbon::parse('2025-09-30 10:40:00'),
            ],
            [
                'id' => 10,
                'pemohon_id' => 10,
                'tgl_masak' => '2025-10-04',
                'menu_makan' => 'Sup ayam + susu fortifikasi',
                'jumlah_porsi' => 210,
                'status' => Permintaan::STATUS_MENUNGGU,
                'created_at' => Carbon::parse('2025-09-30 10:45:00'),
            ],
        ];

        DB::table('permintaan')->upsert(
            $permintaan,
            ['id'],
            ['pemohon_id', 'tgl_masak', 'menu_makan', 'jumlah_porsi', 'status', 'created_at']
        );

        $permintaanMaxId = DB::table('permintaan')->max('id');
        if ($permintaanMaxId !== null) {
            DB::statement(
                "SELECT setval(pg_get_serial_sequence('permintaan','id'), ?, true)",
                [$permintaanMaxId]
            );
        }

        $detail = [
            ['id' => 1, 'permintaan_id' => 1, 'bahan_id' => 1, 'jumlah_diminta' => 50],
            ['id' => 2, 'permintaan_id' => 1, 'bahan_id' => 3, 'jumlah_diminta' => 40],
            ['id' => 3, 'permintaan_id' => 1, 'bahan_id' => 6, 'jumlah_diminta' => 50],
            ['id' => 4, 'permintaan_id' => 2, 'bahan_id' => 1, 'jumlah_diminta' => 40],
            ['id' => 5, 'permintaan_id' => 2, 'bahan_id' => 5, 'jumlah_diminta' => 30],
            ['id' => 6, 'permintaan_id' => 2, 'bahan_id' => 7, 'jumlah_diminta' => 20],
            ['id' => 7, 'permintaan_id' => 3, 'bahan_id' => 1, 'jumlah_diminta' => 45],
            ['id' => 8, 'permintaan_id' => 3, 'bahan_id' => 3, 'jumlah_diminta' => 30],
            ['id' => 9, 'permintaan_id' => 3, 'bahan_id' => 6, 'jumlah_diminta' => 40],
            ['id' => 10, 'permintaan_id' => 4, 'bahan_id' => 1, 'jumlah_diminta' => 30],
            ['id' => 11, 'permintaan_id' => 4, 'bahan_id' => 8, 'jumlah_diminta' => 20],
            ['id' => 12, 'permintaan_id' => 4, 'bahan_id' => 2, 'jumlah_diminta' => 60],
            ['id' => 13, 'permintaan_id' => 5, 'bahan_id' => 1, 'jumlah_diminta' => 60],
            ['id' => 14, 'permintaan_id' => 5, 'bahan_id' => 5, 'jumlah_diminta' => 30],
            ['id' => 15, 'permintaan_id' => 5, 'bahan_id' => 7, 'jumlah_diminta' => 20],
            ['id' => 16, 'permintaan_id' => 6, 'bahan_id' => 1, 'jumlah_diminta' => 50],
            ['id' => 17, 'permintaan_id' => 6, 'bahan_id' => 3, 'jumlah_diminta' => 50],
            ['id' => 18, 'permintaan_id' => 7, 'bahan_id' => 1, 'jumlah_diminta' => 40],
            ['id' => 19, 'permintaan_id' => 7, 'bahan_id' => 2, 'jumlah_diminta' => 40],
            ['id' => 20, 'permintaan_id' => 7, 'bahan_id' => 6, 'jumlah_diminta' => 30],
            ['id' => 21, 'permintaan_id' => 8, 'bahan_id' => 1, 'jumlah_diminta' => 35],
            ['id' => 22, 'permintaan_id' => 8, 'bahan_id' => 8, 'jumlah_diminta' => 25],
            ['id' => 23, 'permintaan_id' => 8, 'bahan_id' => 3, 'jumlah_diminta' => 20],
            ['id' => 24, 'permintaan_id' => 9, 'bahan_id' => 1, 'jumlah_diminta' => 45],
            ['id' => 25, 'permintaan_id' => 9, 'bahan_id' => 5, 'jumlah_diminta' => 25],
            ['id' => 26, 'permintaan_id' => 9, 'bahan_id' => 6, 'jumlah_diminta' => 30],
            ['id' => 27, 'permintaan_id' => 10, 'bahan_id' => 1, 'jumlah_diminta' => 60],
            ['id' => 28, 'permintaan_id' => 10, 'bahan_id' => 3, 'jumlah_diminta' => 50],
            ['id' => 29, 'permintaan_id' => 10, 'bahan_id' => 10, 'jumlah_diminta' => 10],
        ];

        DB::table('permintaan_detail')->upsert(
            $detail,
            ['id'],
            ['permintaan_id', 'bahan_id', 'jumlah_diminta']
        );

        $detailMaxId = DB::table('permintaan_detail')->max('id');
        if ($detailMaxId !== null) {
            DB::statement(
                "SELECT setval(pg_get_serial_sequence('permintaan_detail','id'), ?, true)",
                [$detailMaxId]
            );
        }
    }
}
