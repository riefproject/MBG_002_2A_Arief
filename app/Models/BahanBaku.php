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
     * Boot the model - auto refresh status on key events
     */
    protected static function boot()
    {
        parent::boot();
        
        // Auto-refresh status sebelum menyimpan data
        static::saving(function ($bahanBaku) {
            $bahanBaku->status = static::determineStatus($bahanBaku->jumlah, $bahanBaku->tanggal_kadaluarsa);
        });
        
        // Auto-refresh status setelah model di-update
        static::updated(function ($bahanBaku) {
            $newStatus = static::determineStatus($bahanBaku->jumlah, $bahanBaku->tanggal_kadaluarsa);
            if ($bahanBaku->status !== $newStatus) {
                // Update tanpa trigger events lagi untuk menghindari infinite loop
                static::withoutEvents(function () use ($bahanBaku, $newStatus) {
                    $bahanBaku->update(['status' => $newStatus]);
                });
            }
        });
    }

    /**
     * Tentukan status bahan baku berdasarkan jumlah stok dan tanggal kadaluarsa.
     * Prioritas: kadaluarsa > habis > segera_kadaluarsa > tersedia
     */
    public static function determineStatus(?int $jumlah, $tanggalKadaluarsa = null): string
    {
        $jumlah = (int) ($jumlah ?? 0);
        $today = now()->startOfDay();
        $expiryDate = $tanggalKadaluarsa
            ? Carbon::parse($tanggalKadaluarsa)->startOfDay()
            : null;

        $diff = $expiryDate ? $today->diffInDays($expiryDate, false) : null;

        if (isset($diff) && $diff < 0) {
            return 'kadaluarsa';
        }

        if ($jumlah <= 0) {
            return 'habis';
        }

        if (isset($diff) && $diff <= 3) {
            return 'segera_kadaluarsa';
        }

        return 'tersedia';
    }

    /**
     * Perbarui status bahan baku jika terjadi perubahan.
     */
    public function refreshStatus(bool $save = true): string
    {
        $status = static::determineStatus($this->jumlah, $this->tanggal_kadaluarsa);

        if ($save && $this->exists && $this->status !== $status) {
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
