<?php

namespace Database\Factories;

use App\Models\BahanBaku;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BahanBaku>
 */
class BahanBakuFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tanggalMasuk = Carbon::instance(fake()->dateTimeBetween('-30 days', '-1 day'));
        $tanggalKadaluarsa = Carbon::instance(fake()->dateTimeBetween('now', '+45 days'));
        $jumlah = fake()->numberBetween(0, 500);
        $satuan = fake()->randomElement(['kg', 'liter', 'gram', 'pcs', 'pack']);

        return [
            'nama' => fake()->words(3, true),
            'kategori' => fake()->randomElement(['Protein', 'Sayuran', 'Bumbu', 'Karbohidrat', 'Minuman']),
            'jumlah' => $jumlah,
            'satuan' => $satuan,
            'tanggal_masuk' => $tanggalMasuk,
            'tanggal_kadaluarsa' => $tanggalKadaluarsa,
            'status' => BahanBaku::determineStatus($jumlah, $tanggalKadaluarsa),
            'created_at' => Carbon::instance(fake()->dateTimeBetween('-60 days', 'now')),
        ];
    }
}