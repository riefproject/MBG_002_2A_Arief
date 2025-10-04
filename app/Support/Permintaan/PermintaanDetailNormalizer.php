<?php

namespace App\Support\Permintaan;

class PermintaanDetailNormalizer
{
    // rapihin input detail permintaan biar aman
    public static function normalize(?array $details, ?array $bahanIds, ?array $jumlahs): array
    {
        $rawDetails = [];

        if (is_array($details) && !empty($details)) {
            $rawDetails = $details;
        } elseif (is_array($bahanIds) && is_array($jumlahs)) {
            foreach ($bahanIds as $index => $bahanId) {
                $jumlah = $jumlahs[$index] ?? null;

                if ($bahanId === null || $bahanId === '' || $jumlah === null || $jumlah === '') {
                    continue;
                }

                $rawDetails[] = [
                    'bahan_id' => $bahanId,
                    'jumlah_diminta' => $jumlah,
                ];
            }
        }

        return self::sanitize($rawDetails);
    }

    // buang detail yg ga valid sebelum dipake
    private static function sanitize(array $rawDetails): array
    {
        $details = [];

        foreach ($rawDetails as $detail) {
            if (!is_array($detail) || !isset($detail['bahan_id'], $detail['jumlah_diminta'])) {
                continue;
            }

            $bahanId = (int) $detail['bahan_id'];
            $jumlah = (int) $detail['jumlah_diminta'];

            if ($bahanId <= 0 || $jumlah <= 0) {
                continue;
            }

            $details[] = [
                'bahan_id' => $bahanId,
                'jumlah_diminta' => $jumlah,
            ];
        }

        return $details;
    }
}
