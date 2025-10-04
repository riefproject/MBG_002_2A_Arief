<?php

namespace Tests\Unit;

use App\Models\Permintaan;
use App\Support\Permintaan\PermintaanStatusRefresher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class PermintaanStatusRefresherTest extends TestCase
{
    use RefreshDatabase;

    public function test_refresh_marks_past_due_requests_as_kadaluarsa(): void
    {
        Carbon::setTestNow('2025-10-04 09:00:00');

        $expired = Permintaan::factory()
            ->menunggu()
            ->create(['tgl_masak' => Carbon::yesterday()]);

        $upcoming = Permintaan::factory()
            ->menunggu()
            ->create(['tgl_masak' => Carbon::tomorrow()]);

        (new PermintaanStatusRefresher())->refresh();

        $this->assertSame(Permintaan::STATUS_KADALUARSA, $expired->fresh()->status);
        $this->assertSame(Permintaan::STATUS_MENUNGGU, $upcoming->fresh()->status);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Carbon::setTestNow();
    }
}
