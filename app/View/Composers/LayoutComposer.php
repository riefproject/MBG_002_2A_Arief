<?php

namespace App\View\Composers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class LayoutComposer
{
    // simpen request biar gampang cek route
    public function __construct(private Request $request)
    {
    }

    // share status nav aktif ke layout
    public function compose(View $view): void
    {
        $view->with('navActive', [
            'dashboard' => $this->request->routeIs('dashboard'),
            'profile' => $this->request->routeIs('profile'),
            'admin_bahan_baku' => $this->request->routeIs('admin.bahan_baku', 'admin.bahan_baku.*'),
            'admin_permintaan' => $this->request->routeIs('admin.permintaan', 'admin.permintaan.*'),
            'user_permintaan' => $this->request->routeIs('user.permintaan', 'user.permintaan.*'),
        ]);
    }
}
