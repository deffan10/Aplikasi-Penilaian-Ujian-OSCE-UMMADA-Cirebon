<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Stasi;

class StasiSeeder extends Seeder
{
    public function run(): void
    {
        $stasiList = [
            ['nama' => 'Stasi 1 - Komunikasi Pasien', 'deskripsi' => 'Menilai kemampuan komunikasi dengan pasien'],
            ['nama' => 'Stasi 2 - Dispensing Obat', 'deskripsi' => 'Menilai kemampuan dispensing dan penyerahan obat'],
            ['nama' => 'Stasi 3 - Konseling Obat', 'deskripsi' => 'Menilai kemampuan konseling penggunaan obat'],
            ['nama' => 'Stasi 4 - Compounding', 'deskripsi' => 'Menilai kemampuan peracikan sediaan farmasi'],
            ['nama' => 'Stasi 5 - Skrining Resep', 'deskripsi' => 'Menilai kemampuan skrining dan validasi resep'],
            ['nama' => 'Stasi 6 - Monitoring Efek Samping', 'deskripsi' => 'Menilai kemampuan monitoring efek samping obat'],
        ];

        foreach ($stasiList as $stasi) {
            Stasi::firstOrCreate(
                ['nama' => $stasi['nama']],
                $stasi
            );
        }
    }
}
