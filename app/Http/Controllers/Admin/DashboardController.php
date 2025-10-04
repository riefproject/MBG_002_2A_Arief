<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BahanBaku;
use App\Models\Permintaan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DashboardController extends Controller
{
    private const PRIORITY_BADGES = [
        'habis' => [
            'class' => 'bg-rose-100 text-rose-700 border-rose-200',
            'label' => 'Stok habis',
        ],
        'kadaluarsa' => [
            'class' => 'bg-orange-100 text-orange-700 border-orange-200',
            'label' => 'Kadaluarsa',
        ],
        'segera_kadaluarsa' => [
            'class' => 'bg-amber-100 text-amber-700 border-amber-200',
            'label' => 'Segera kadaluarsa',
        ],
    ];

    // tampilin dashboard utama buat gudang
    public function __invoke(): View
    {
        $user = Auth::user();

        $permintaanQuery = Permintaan::query();
        $totalPermintaan = (clone $permintaanQuery)->count();

        $statusCounts = (clone $permintaanQuery)
            ->select('status')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $menungguCount = (int) ($statusCounts[Permintaan::STATUS_MENUNGGU] ?? 0);
        $disetujuiCount = (int) ($statusCounts[Permintaan::STATUS_DISETUJUI] ?? 0);
        $ditolakCount = (int) ($statusCounts[Permintaan::STATUS_DITOLAK] ?? 0);
        $kadaluarsaCount = (int) ($statusCounts[Permintaan::STATUS_KADALUARSA] ?? 0);

        $permintaanHariIni = (clone $permintaanQuery)
            ->whereDate('created_at', now()->toDateString())
            ->count();

        $permintaanMingguIni = (clone $permintaanQuery)
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();

        $pendingHighlights = Permintaan::select('id', 'menu_makan', 'tgl_masak', 'created_at')
            ->with(['pemohon:id,name'])
            ->where('status', Permintaan::STATUS_MENUNGGU)
            ->orderBy('created_at')
            ->limit(3)
            ->get();

        $priorityStocks = BahanBaku::select('id', 'nama', 'jumlah', 'satuan', 'status', 'tanggal_kadaluarsa')
            ->whereIn('status', ['habis', 'kadaluarsa', 'segera_kadaluarsa'])
            ->orderByRaw("CASE status WHEN 'habis' THEN 0 WHEN 'kadaluarsa' THEN 1 WHEN 'segera_kadaluarsa' THEN 2 ELSE 3 END")
            ->orderBy('tanggal_kadaluarsa')
            ->limit(3)
            ->get();

        $pendingHighlightsView = $pendingHighlights->map(function (Permintaan $permintaan) {
            return [
                'menu' => $permintaan->menu_makan,
                'pemohon' => optional($permintaan->pemohon)->name ?? 'Tidak diketahui',
                'created_diff' => optional($permintaan->created_at)?->diffForHumans() ?? '-',
                'tgl_masak_label' => optional($permintaan->tgl_masak)?->format('d M Y') ?? '-',
            ];
        })->values();

        $priorityStocksView = $priorityStocks->map(function (BahanBaku $stock) {
            $badge = self::PRIORITY_BADGES[$stock->status] ?? null;

            return [
                'nama' => $stock->nama,
                'status_badge_class' => $badge['class'] ?? 'bg-gray-100 text-gray-600 border-gray-200',
                'status_badge_label' => $badge['label'] ?? Str::headline($stock->status),
                'jumlah_label' => trim(number_format((int) $stock->jumlah) . ' ' . $stock->satuan),
                'tanggal_kadaluarsa_label' => optional($stock->tanggal_kadaluarsa)?->format('d M Y') ?? '—',
            ];
        })->values();

        $statCards = [
            [
                'label' => 'Permintaan Menunggu',
                'value' => $menungguCount,
                'description' => 'Perlu segera diproses oleh gudang',
                'icon' => 'heroicon-o-inbox-arrow-down',
                'accent' => 'bg-blue-50 text-blue-700 border-blue-100',
            ],
            [
                'label' => 'Permintaan Disetujui',
                'value' => $disetujuiCount,
                'description' => 'Sudah disetujui dan siap distribusi',
                'icon' => 'heroicon-o-check-circle',
                'accent' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
            ],
            [
                'label' => 'Permintaan Ditolak',
                'value' => $ditolakCount,
                'description' => 'Ditolak dan menunggu revisi dapur',
                'icon' => 'heroicon-o-x-circle',
                'accent' => 'bg-rose-50 text-rose-700 border-rose-100',
            ],
            [
                'label' => 'Permintaan Kadaluarsa',
                'value' => $kadaluarsaCount,
                'description' => 'Lewat tanggal masak jadi ga bisa diproses',
                'icon' => 'heroicon-o-clock',
                'accent' => 'bg-orange-50 text-orange-700 border-orange-100',
            ],
            [
                'label' => 'Permintaan Hari Ini',
                'value' => $permintaanHariIni,
                'description' => 'Pengajuan yang masuk sejak pagi',
                'icon' => 'heroicon-o-calendar',
                'accent' => 'bg-amber-50 text-amber-700 border-amber-100',
            ],
        ];

        $data = [
            'userName' => $user?->name,
            'totalPermintaan' => $totalPermintaan,
            'statCards' => $statCards,
            'menungguCount' => $menungguCount,
            'kadaluarsaCount' => $kadaluarsaCount,
            'permintaanMingguIni' => $permintaanMingguIni,
            'pendingHighlights' => $pendingHighlightsView,
            'priorityStocks' => $priorityStocksView,
        ];

        return view('admin.dashboard', $data);
    }
}
