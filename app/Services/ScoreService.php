<?php

namespace App\Services;

use App\Models\KomponenStasi;

class ScoreService
{
    /**
     * Calculate total score based on weighted components
     * 
     * @param array $komponenIdKeSkor Array of [komponen_stasi_id => skor]
     * @return float Total weighted score
     */
    public function hitungTotal(array $komponenIdKeSkor): float
    {
        if (empty($komponenIdKeSkor)) {
            return 0;
        }

        $komponen = KomponenStasi::whereIn('id', array_keys($komponenIdKeSkor))->get(['id', 'bobot']);
        
        if ($komponen->isEmpty()) {
            return 0;
        }

        $sumBobot = max(1, (int) $komponen->sum('bobot'));
        $total = 0;

        foreach ($komponen as $k) {
            $skor = (float) ($komponenIdKeSkor[$k->id] ?? 0);
            // Weighted calculation: skor * (bobot / total_bobot)
            $total += $skor * ($k->bobot / $sumBobot);
        }

        return round($total, 2);
    }

    /**
     * Validate that all komponen for a stasi are provided
     * 
     * @param int $stasiId
     * @param array $komponenIds Array of komponen IDs provided
     * @return bool
     */
    public function validateKomponenLengkap(int $stasiId, array $komponenIds): bool
    {
        $requiredKomponen = KomponenStasi::where('stasi_id', $stasiId)->pluck('id')->toArray();
        return empty(array_diff($requiredKomponen, $komponenIds));
    }
}
