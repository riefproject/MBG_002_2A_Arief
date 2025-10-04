<?php

namespace App\Support\Permintaan;

use App\Models\Permintaan;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class PermintaanViewPresenter
{
    private const STATUS_BADGES = [
        Permintaan::STATUS_MENUNGGU => 'bg-amber-100 text-amber-700 border-amber-200',
        Permintaan::STATUS_DISETUJUI => 'bg-emerald-100 text-emerald-700 border-emerald-200',
        Permintaan::STATUS_DITOLAK => 'bg-rose-100 text-rose-700 border-rose-200',
        Permintaan::STATUS_KADALUARSA => 'bg-orange-100 text-orange-700 border-orange-200',
    ];

    // siapin data permintaan buat tabel user
    public static function transformForUserIndex(Collection $permintaan): Collection
    {
        return $permintaan
            ->map(fn (Permintaan $item) => self::formatItem($item))
            ->values();
    }

    // bentuk 1 permintaan jadi array siap render
    private static function formatItem(Permintaan $permintaan): array
    {
        $status = $permintaan->status;

        return [
            'id' => $permintaan->id,
            'menu' => $permintaan->menu_makan,
            'status' => $status,
            'status_label' => Str::headline($status),
            'status_badge_class' => self::STATUS_BADGES[$status] ?? 'bg-gray-100 text-gray-700 border-gray-200',
            'tgl_masak_label' => optional($permintaan->tgl_masak)->format('d F Y'),
            'detail_payload' => $permintaan->details->map(function ($detail) {
                $nama = $detail->bahan_nama_label ?? '-';
                $satuan = $detail->bahan_satuan_label ?? '';

                return [
                    'bahan' => $nama,
                    'jumlah' => (int) $detail->jumlah_diminta,
                    'satuan' => $satuan,
                ];
            })->values()->all(),
        ];
    }
}
