<?php

namespace Tests\Unit;

use App\Models\BahanBaku;
use Carbon\Carbon;
use Tests\TestCase;

class BahanBakuStatusTest extends TestCase
{
    public function test_expired_items_are_marked_as_kadaluarsa_even_if_stock_empty(): void
    {
        Carbon::setTestNow('2025-10-03 00:00:00');

        $status = BahanBaku::determineStatus(0, '2025-10-01');

        $this->assertSame('kadaluarsa', $status);
    }

    public function test_non_expired_empty_stock_is_habis(): void
    {
        Carbon::setTestNow('2025-10-01 00:00:00');

        $status = BahanBaku::determineStatus(0, '2025-10-10');

        $this->assertSame('habis', $status);
    }

    public function test_items_within_three_days_are_marked_segera_kadaluarsa(): void
    {
        Carbon::setTestNow('2025-10-01 00:00:00');

        $status = BahanBaku::determineStatus(5, '2025-10-03');

        $this->assertSame('segera_kadaluarsa', $status);
    }

    public function test_future_items_outside_three_days_are_tersedia(): void
    {
        Carbon::setTestNow('2025-10-01 00:00:00');

        $status = BahanBaku::determineStatus(5, '2025-10-10');

        $this->assertSame('tersedia', $status);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Carbon::setTestNow();
    }
}
