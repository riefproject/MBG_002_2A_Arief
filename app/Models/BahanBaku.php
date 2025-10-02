<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BahanBaku extends Model
{
    use HasFactory;
    protected $table = 'bahan_baku';
    public $timestamps = false;
    protected $fillable = [
        'nama',
        'kategori',
        'jumlah',
        'satuan',
        'tanggal_masuk',
        'tanggal_kadaluarsa',
        'status',
        'created_at',
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'tanggal_kadaluarsa' => 'date',
        'created_at' => 'datetime',
    ];

    /**
     * Tentukan status bahan baku berdasarkan jumlah stok dan tanggal kadaluarsa.
     */
    public static function determineStatus(int $jumlah, $tanggalKadaluarsa = null): string
    {
        $today = now()->startOfDay();
        $tanggalKadaluarsa = $tanggalKadaluarsa
            ? Carbon::parse($tanggalKadaluarsa)->startOfDay()
            : null;

        $kadaluarsa = $tanggalKadaluarsa && $today->gte($tanggalKadaluarsa);
        $habis = $jumlah === 0;
        $segeraKadaluarsa = $tanggalKadaluarsa && !$kadaluarsa && $today->diffInDays($tanggalKadaluarsa, false) <= 3 && $jumlah > 0;
        $tersedia = $jumlah > 0 && !$kadaluarsa;

        if ($habis) {
            return 'habis';
        }

        if ($kadaluarsa) {
            return 'kadaluarsa';
        }

        if ($segeraKadaluarsa) {
            return 'segera_kadaluarsa';
        }

        if ($tersedia) {
            return 'tersedia';
        }

        return 'tersedia';
    }

    /**
     * Perbarui status bahan baku jika terjadi perubahan.
     */
    public function refreshStatus(bool $save = true): string
    {
        $status = static::determineStatus($this->jumlah, $this->tanggal_kadaluarsa);

        if ($save && $this->status !== $status) {
            $this->status = $status;
            $this->save();
        } else {
            $this->status = $status;
        }

        return $status;
    }

    public function permintaanDetails(): HasMany
    {
        return $this->hasMany(PermintaanDetail::class, 'bahan_id');
    }
}
