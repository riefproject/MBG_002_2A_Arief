<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermintaanDetail extends Model
{
    use HasFactory;

    protected $table = 'permintaan_detail';
    public $timestamps = false;

    protected $fillable = [
        'permintaan_id',
        'bahan_id',
        'jumlah_diminta',
        'bahan_nama_snapshot',
        'bahan_satuan_snapshot',
    ];

    protected $casts = [
        'jumlah_diminta' => 'integer',
        'bahan_nama_snapshot' => 'string',
        'bahan_satuan_snapshot' => 'string',
    ];

    // relasi ke permintaan induk
    public function permintaan(): BelongsTo
    {
        return $this->belongsTo(Permintaan::class, 'permintaan_id');
    }

    // relasi ke bahan baku yg diminta
    public function bahan(): BelongsTo
    {
        return $this->belongsTo(BahanBaku::class, 'bahan_id');
    }

    // ambil nama bahan fallback snapshot
    public function getBahanNamaLabelAttribute(): ?string
    {
        return optional($this->bahan)->nama ?? $this->bahan_nama_snapshot;
    }

    // ambil satuan bahan fallback snapshot
    public function getBahanSatuanLabelAttribute(): ?string
    {
        return optional($this->bahan)->satuan ?? $this->bahan_satuan_snapshot;
    }
}
