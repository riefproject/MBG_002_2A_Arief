<?php

namespace App\Http\Middleware;

use App\Support\Permintaan\PermintaanStatusRefresher;
use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;

class RefreshPermintaanStatus
{
    public function __construct(private Application $app)
    {
    }

    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            $today = now()->toDateString();
            $lastRun = $request->session()->get('permintaan_status_refreshed_at');

            if ($lastRun !== $today) {
                $this->refreshStatuses();
                // cukup jalanin refresh sekali per hari biar ga boros query
                $request->session()->put('permintaan_status_refreshed_at', $today);
            }
        }

        return $next($request);
    }

    private function refreshStatuses(): void
    {
        $refresher = $this->app->make(PermintaanStatusRefresher::class);
        $refresher->refresh();
    }
}
