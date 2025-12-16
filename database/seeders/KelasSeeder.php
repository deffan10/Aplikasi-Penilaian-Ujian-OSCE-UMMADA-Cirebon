<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kelas;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        $kelasList = [
            ['kode' => 'D3-3A', 'nama' => 'D3 Farmasi Semester 3 Kelas A'],
            ['kode' => 'D3-3B', 'nama' => 'D3 Farmasi Semester 3 Kelas B'],
            ['kode' => 'S1-5A', 'nama' => 'S1 Farmasi Semester 5 Kelas A'],
            ['kode' => 'S1-5B', 'nama' => 'S1 Farmasi Semester 5 Kelas B'],
        ];

        foreach ($kelasList as $kelas) {
            Kelas::firstOrCreate(
                ['kode' => $kelas['kode']],
                $kelas
            );
        }
    }
}
