<?php

namespace Database\Factories;

use App\Models\Permintaan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Permintaan>
 */
class PermintaanFactory extends Factory
{
    protected $model = Permintaan::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tglMasak = Carbon::instance(fake()->dateTimeBetween('now', '+14 days'));

        return [
            'pemohon_id' => User::factory()->state(['role' => 'dapur']),
            'tgl_masak' => $tglMasak,
            'menu_makan' => fake()->words(3, true),
            'jumlah_porsi' => fake()->numberBetween(1, 200),
            'status' => fake()->randomElement([
                Permintaan::STATUS_MENUNGGU,
                Permintaan::STATUS_DISETUJUI,
                Permintaan::STATUS_DITOLAK,
                Permintaan::STATUS_KADALUARSA,
            ]),
            'created_at' => Carbon::instance(fake()->dateTimeBetween('-7 days', 'now')),
        ];
    }

    /**
     * Status menunggu.
     */
    public function menunggu(): static
    {
        return $this->state([
            'status' => Permintaan::STATUS_MENUNGGU,
        ]);
    }

    /**
     * Status disetujui.
     */
    public function disetujui(): static
    {
        return $this->state([
            'status' => Permintaan::STATUS_DISETUJUI,
        ]);
    }

    /**
     * Status ditolak.
     */
    public function ditolak(): static
    {
        return $this->state([
            'status' => Permintaan::STATUS_DITOLAK,
        ]);
    }

    /**
     * Status kadaluarsa.
     */
    public function kadaluarsa(): static
    {
        return $this->state([
            'status' => Permintaan::STATUS_KADALUARSA,
        ]);
    }
}
