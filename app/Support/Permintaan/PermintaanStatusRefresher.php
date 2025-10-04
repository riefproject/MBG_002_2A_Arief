<?php

namespace App\Support\Permintaan;

use App\Models\Permintaan;
use Illuminate\Support\Carbon;

class PermintaanStatusRefresher
{
    public function refresh(): void
    {
        $today = Carbon::today();

        Permintaan::query()
            ->where('status', Permintaan::STATUS_MENUNGGU)
            ->whereDate('tgl_masak', '<', $today)
            ->update(['status' => Permintaan::STATUS_KADALUARSA]);
    }
}
