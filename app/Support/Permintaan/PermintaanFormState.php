<?php

namespace App\Support\Permintaan;

use Illuminate\Http\Request;

class PermintaanFormState
{
    // simpen state detail biar gampang diakses
    public function __construct(private array $details)
    {
    }

    // ambil ulang detail dari request lama
    public static function fromRequest(Request $request): self
    {
        $oldInput = $request->session()->getOldInput();

        $details = PermintaanDetailNormalizer::normalize(
            $oldInput['details'] ?? null,
            $oldInput['bahan_id'] ?? null,
            $oldInput['jumlah_diminta'] ?? null
        );

        return new self($details);
    }

    // ambil detail yg udah rapi
    public function details(): array
    {
        return $this->details;
    }

    // balikin detail atau 1 baris kosong buat form
    public function detailsWithFallbackRow(): array
    {
        if (!empty($this->details)) {
            return $this->details;
        }

        return [
            [
                'bahan_id' => null,
                'jumlah_diminta' => null,
            ],
        ];
    }
}
