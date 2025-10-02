<?php

namespace Database\Factories;

use App\Models\BahanBaku;
use App\Models\Permintaan;
use App\Models\PermintaanDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PermintaanDetail>
 */
class PermintaanDetailFactory extends Factory
{
    protected $model = PermintaanDetail::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'permintaan_id' => Permintaan::factory(),
            'bahan_id' => BahanBaku::factory(),
            'jumlah_diminta' => fake()->numberBetween(1, 30),
        ];
    }
}
