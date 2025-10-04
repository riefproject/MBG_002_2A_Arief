<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Permintaan;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Tampilkan dashboard untuk pengguna dapur.
     */
    public function __invoke(): View
    {
        $user = Auth::user();

        if (!$user) {
            abort(403, 'Pengguna belum terautentikasi.');
        }

        $baseQuery = Permintaan::query()->where('pemohon_id', $user->id);

        $totalPermintaan = (clone $baseQuery)->count();

        $statusCounts = (clone $baseQuery)
            ->select('status')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $menungguCount = (int) ($statusCounts[Permintaan::STATUS_MENUNGGU] ?? 0);
        $disetujuiCount = (int) ($statusCounts[Permintaan::STATUS_DISETUJUI] ?? 0);
        $ditolakCount = (int) ($statusCounts[Permintaan::STATUS_DITOLAK] ?? 0);
        $kadaluarsaCount = (int) ($statusCounts[Permintaan::STATUS_KADALUARSA] ?? 0);

        $statCards = [
            [
                'label' => 'Total Permintaan',
                'value' => $totalPermintaan,
                'description' => 'Seluruh permintaan yang pernah kamu ajukan',
                'accent' => 'bg-blue-100 text-blue-700 border-blue-200',
            ],
            [
                'label' => 'Menunggu',
                'value' => $menungguCount,
                'description' => 'Sedang ditinjau oleh tim gudang',
                'accent' => 'bg-amber-100 text-amber-700 border-amber-200',
            ],
            [
                'label' => 'Disetujui',
                'value' => $disetujuiCount,
                'description' => 'Siap diproses dan diambil',
                'accent' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
            ],
            [
                'label' => 'Ditolak',
                'value' => $ditolakCount,
                'description' => 'Perlu ditinjau kembali atau revisi',
                'accent' => 'bg-rose-100 text-rose-700 border-rose-200',
            ],
            [
                'label' => 'Kadaluarsa',
                'value' => $kadaluarsaCount,
                'description' => 'Udah lewat tanggal masak jadi ga bisa diproses',
                'accent' => 'bg-orange-100 text-orange-700 border-orange-200',
            ],
        ];

        return view('user.dashboard', [
            'userName' => $user->name ?? 'Tim Dapur',
            'totalPermintaan' => $totalPermintaan,
            'menungguCount' => $menungguCount,
            'disetujuiCount' => $disetujuiCount,
            'ditolakCount' => $ditolakCount,
            'kadaluarsaCount' => $kadaluarsaCount,
            'statCards' => $statCards,
        ]);
    }
}
