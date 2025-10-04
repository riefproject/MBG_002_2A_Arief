<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Permintaan extends Model
{
    use HasFactory;

    protected $table = 'permintaan';
    public $timestamps = false;

    protected $fillable = [
        'pemohon_id',
        'tgl_masak',
        'menu_makan',
        'jumlah_porsi',
        'status',
        'created_at',
    ];

    protected $casts = [
        'tgl_masak' => 'date',
        'created_at' => 'datetime',
    ];

    public const STATUS_MENUNGGU = 'menunggu';
    public const STATUS_DISETUJUI = 'disetujui';
    public const STATUS_DITOLAK = 'ditolak';
    public const STATUS_KADALUARSA = 'kadaluarsa';

    public const TERMINAL_STATUSES = [
        self::STATUS_DISETUJUI,
        self::STATUS_DITOLAK,
        self::STATUS_KADALUARSA,
    ];

    // Relasi ke pemohon (user dapur).
    public function pemohon(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pemohon_id');
    }

    // Relasi ke detail permintaan.
    public function details(): HasMany
    {
        return $this->hasMany(PermintaanDetail::class, 'permintaan_id');
    }

    // Scope untuk filter berdasarkan status.
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    // Hitung total angka jumlah bahan yang diminta.
    public function totalJumlahDiminta(): int
    {
        return (int) $this->details()->sum('jumlah_diminta');
    }
}
