<?php

namespace App\Services;

use App\Models\Nilai;
use App\Models\NilaiAcuanStasi;
use App\Models\Jadwal;
use App\Models\Stasi;
use Illuminate\Support\Collection;

class RegressionService
{
    /**
     * Calculate Nilai Acuan for a specific stasi in a jadwal
     * Using linear regression: Global Rating = intercept + slope * Nilai Aktual
     * Then find Nilai Aktual when Global Rating = 2 (Borderline)
     *
     * @param int $jadwalId
     * @param int $stasiId
     * @return NilaiAcuanStasi|null
     */
    public function calculateNilaiAcuan(int $jadwalId, int $stasiId): ?NilaiAcuanStasi
    {
        // Get all nilai for this stasi in this jadwal with their global ratings
        $nilaiData = Nilai::where('jadwal_id', $jadwalId)
            ->where('stasi_id', $stasiId)
            ->whereNotNull('global_rating_id')
            ->with('globalRating')
            ->get();

        if ($nilaiData->count() < 2) {
            // Need at least 2 data points for regression
            return null;
        }

        // Prepare data for regression
        // X = nilai_aktual (checklist score = Σ(skor × bobot))
        // Y = global rating value (1-4)
        $xValues = [];
        $yValues = [];

        foreach ($nilaiData as $nilai) {
            if ($nilai->globalRating) {
                // Include all nilai_aktual including 0
                $xValues[] = (float) ($nilai->nilai_aktual ?? 0);
                $yValues[] = (float) $nilai->globalRating->nilai;
            }
        }

        $n = count($xValues);
        if ($n < 2) {
            return null;
        }

        // Calculate linear regression coefficients
        // Y = a + bX where a = intercept, b = slope
        $sumX = array_sum($xValues);
        $sumY = array_sum($yValues);
        $sumXY = 0;
        $sumX2 = 0;

        for ($i = 0; $i < $n; $i++) {
            $sumXY += $xValues[$i] * $yValues[$i];
            $sumX2 += $xValues[$i] * $xValues[$i];
        }

        $meanX = $sumX / $n;
        $meanY = $sumY / $n;

        // Calculate slope (b) and intercept (a)
        $denominator = $sumX2 - ($sumX * $sumX / $n);
        
        if (abs($denominator) < 0.0001) {
            // Avoid division by zero - use mean as nilai acuan
            $nilaiAcuan = $meanX;
            $slope = 0;
            $intercept = $meanY;
        } else {
            $slope = ($sumXY - ($sumX * $sumY / $n)) / $denominator;
            $intercept = $meanY - ($slope * $meanX);

            // Find nilai_aktual when global_rating = 2 (Borderline/KKM)
            // 2 = intercept + slope * nilai_aktual
            // nilai_aktual = (2 - intercept) / slope
            if (abs($slope) < 0.0001) {
                // If slope is near zero, use mean
                $nilaiAcuan = $meanX;
            } else {
                $nilaiAcuan = (2 - $intercept) / $slope;
            }
        }

        // Ensure nilai_acuan is within reasonable bounds
        $maxPossible = $this->getMaxPossibleScore($stasiId);
        $nilaiAcuan = max(0, min($nilaiAcuan, $maxPossible));

        // Save or update nilai acuan
        return NilaiAcuanStasi::updateOrCreate(
            [
                'jadwal_id' => $jadwalId,
                'stasi_id' => $stasiId,
            ],
            [
                'nilai_acuan' => round($nilaiAcuan, 2),
                'intercept' => round($intercept, 4),
                'slope' => round($slope, 4),
                'sample_count' => $n,
                'calculated_at' => now(),
            ]
        );
    }

    /**
     * Calculate nilai acuan for all stasi in a jadwal
     *
     * @param int $jadwalId
     * @return Collection
     */
    public function calculateAllNilaiAcuan(int $jadwalId): Collection
    {
        $jadwal = Jadwal::findOrFail($jadwalId);
        $stasiIds = Stasi::where('aktif', true)->pluck('id');
        
        $results = collect();
        
        foreach ($stasiIds as $stasiId) {
            $result = $this->calculateNilaiAcuan($jadwalId, $stasiId);
            if ($result) {
                $results->push($result);
            }
        }

        return $results;
    }

    /**
     * Get the maximum possible score for a stasi
     * Max score = sum of (3 * bobot) for all komponen
     *
     * @param int $stasiId
     * @return float
     */
    public function getMaxPossibleScore(int $stasiId): float
    {
        $stasi = Stasi::with('komponens')->find($stasiId);
        if (!$stasi || $stasi->komponens->isEmpty()) {
            return 100; // fallback
        }

        $totalBobot = 0;
        foreach ($stasi->komponens as $komponen) {
            $totalBobot += $komponen->bobot;
        }

        // Max score = 3 (max skor) * total bobot
        // Example: 5 komponen with bobot 1 each = 3 * 5 = 15
        return 3 * $totalBobot;
    }

    /**
     * Calculate nilai aktual from komponen scores
     * Formula: sum of (skor * bobot) - NO normalization
     * Example: skor=3, bobot=2 → 3×2 = 6
     *
     * @param array $skorPerKomponen [komponen_id => skor (0-3)]
     * @param int $stasiId
     * @return float
     */
    public function calculateNilaiAktual(array $skorPerKomponen, int $stasiId): float
    {
        $stasi = Stasi::with('komponens')->find($stasiId);
        if (!$stasi) {
            return 0;
        }

        $totalNilai = 0;

        foreach ($stasi->komponens as $komponen) {
            $skor = $skorPerKomponen[$komponen->id] ?? 0;
            // Nilai komponen = skor * bobot
            $totalNilai += $skor * $komponen->bobot;
        }

        // Return raw total (skor × bobot) without normalization
        // Total Nilai Aktual OSCE = sum of all stasi nilai aktual
        return round($totalNilai, 2);
    }

    /**
     * Determine if a mahasiswa passes a stasi
     *
     * @param float $nilaiAktual
     * @param float $nilaiAcuan
     * @return bool
     */
    public function isLulusStasi(float $nilaiAktual, float $nilaiAcuan): bool
    {
        return $nilaiAktual >= $nilaiAcuan;
    }

    /**
     * Calculate overall OSCE result for a mahasiswa
     *
     * @param int $jadwalId
     * @param int $mahasiswaId
     * @return array
     */
    public function calculateOsceResult(int $jadwalId, int $mahasiswaId): array
    {
        $nilaiList = Nilai::where('jadwal_id', $jadwalId)
            ->where('mahasiswa_id', $mahasiswaId)
            ->get();

        $totalNilaiAktual = 0;
        $totalNilaiAcuan = 0;
        $stasiResults = [];

        foreach ($nilaiList as $nilai) {
            $nilaiAcuan = NilaiAcuanStasi::where('jadwal_id', $jadwalId)
                ->where('stasi_id', $nilai->stasi_id)
                ->first();

            $acuan = $nilaiAcuan ? $nilaiAcuan->nilai_acuan : 0;
            
            $totalNilaiAktual += $nilai->nilai_aktual;
            $totalNilaiAcuan += $acuan;

            $stasiResults[] = [
                'stasi_id' => $nilai->stasi_id,
                'nilai_aktual' => $nilai->nilai_aktual,
                'nilai_acuan' => $acuan,
                'lulus' => $nilai->nilai_aktual >= $acuan,
            ];
        }

        return [
            'total_nilai_aktual' => $totalNilaiAktual,
            'total_nilai_acuan' => $totalNilaiAcuan,
            'lulus_osce' => $totalNilaiAktual >= $totalNilaiAcuan,
            'stasi_results' => $stasiResults,
        ];
    }
}
