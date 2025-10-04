<?php

namespace Tests\Feature\User;

use App\Models\BahanBaku;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class PermintaanCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_create_request_with_past_cooking_date(): void
    {
        Carbon::setTestNow('2025-10-04 08:00:00');

        $user = User::factory()->create(['role' => 'dapur']);
        $bahan = BahanBaku::factory()->create(['jumlah' => 10]);

        $response = $this->actingAs($user)->post(route('user.permintaan.store'), [
            'pemohon_id' => $user->id,
            'tgl_masak' => Carbon::yesterday()->toDateString(),
            'menu_makan' => 'Menu Percobaan',
            'jumlah_porsi' => 10,
            'bahan_id' => [$bahan->id],
            'jumlah_diminta' => [5],
        ]);

        $response->assertSessionHasErrors('tgl_masak');

        $this->assertDatabaseMissing('permintaan', [
            'menu_makan' => 'Menu Percobaan',
        ]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Carbon::setTestNow();
    }
}
