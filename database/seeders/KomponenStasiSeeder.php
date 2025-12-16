<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Stasi;
use App\Models\KomponenStasi;

class KomponenStasiSeeder extends Seeder
{
    public function run(): void
    {
        $stasiAll = Stasi::all();

        // Default komponen for each stasi
        $komponenTemplates = [
            ['nama' => 'Persiapan dan Perkenalan', 'bobot' => 15],
            ['nama' => 'Pengumpulan Informasi', 'bobot' => 20],
            ['nama' => 'Pelaksanaan Prosedur', 'bobot' => 30],
            ['nama' => 'Komunikasi dan Edukasi', 'bobot' => 20],
            ['nama' => 'Dokumentasi dan Penutup', 'bobot' => 15],
        ];

        foreach ($stasiAll as $stasi) {
            foreach ($komponenTemplates as $index => $komponen) {
                KomponenStasi::firstOrCreate(
                    [
                        'stasi_id' => $stasi->id,
                        'nama' => $komponen['nama'],
                    ],
                    [
                        'bobot' => $komponen['bobot'],
                        'urutan' => $index + 1,
                    ]
                );
            }
        }
    }
}
