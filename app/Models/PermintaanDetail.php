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
    ];

    protected $casts = [
        'jumlah_diminta' => 'integer',
    ];

    // Relasi ke permintaan induk.
    public function permintaan(): BelongsTo
    {
        return $this->belongsTo(Permintaan::class, 'permintaan_id');
    }

    // Relasi ke bahan baku yang diminta.
    public function bahan(): BelongsTo
    {
        return $this->belongsTo(BahanBaku::class, 'bahan_id');
    }
}
