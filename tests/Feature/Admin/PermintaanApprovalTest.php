<?php

namespace Tests\Feature\Admin;

use App\Models\Permintaan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class PermintaanApprovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_gudang_cannot_approve_expired_request(): void
    {
        Carbon::setTestNow('2025-10-04 08:00:00');

        $gudang = User::factory()->create(['role' => 'gudang']);
        $pemohon = User::factory()->create(['role' => 'dapur']);

        $permintaan = Permintaan::factory()
            ->menunggu()
            ->create([
                'pemohon_id' => $pemohon->id,
                'tgl_masak' => Carbon::yesterday(),
            ]);

        $response = $this->actingAs($gudang)->post(route('admin.permintaan.setujui', $permintaan));

        $response->assertRedirect(route('admin.permintaan.index'));
        $response->assertSessionHas('error');

        $this->assertSame(
            Permintaan::STATUS_KADALUARSA,
            $permintaan->fresh()->status
        );
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Carbon::setTestNow();
    }
}
