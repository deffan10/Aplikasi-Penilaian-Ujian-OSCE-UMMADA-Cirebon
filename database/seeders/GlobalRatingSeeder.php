<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GlobalRating;

class GlobalRatingSeeder extends Seeder
{
    public function run(): void
    {
        $ratings = [
            [
                'kode' => 'TIDAK_LULUS',
                'nilai' => 1,
                'label' => 'Tidak Lulus',
                'deskripsi' => 'Tidak berhasil melakukan sebagian besar langkah dengan benar'
            ],
            [
                'kode' => 'BORDERLINE',
                'nilai' => 2,
                'label' => 'Borderline (KKM)',
                'deskripsi' => 'Berada di ambang batas kelulusan'
            ],
            [
                'kode' => 'LULUS',
                'nilai' => 3,
                'label' => 'Lulus',
                'deskripsi' => 'Berhasil melakukan sebagian besar langkah dengan benar'
            ],
            [
                'kode' => 'SUPERIOR',
                'nilai' => 4,
                'label' => 'Superior',
                'deskripsi' => 'Berhasil melakukan seluruh langkah dengan sangat baik'
            ],
        ];

        foreach ($ratings as $rating) {
            GlobalRating::updateOrCreate(
                ['kode' => $rating['kode']],
                $rating
            );
        }
    }
}
