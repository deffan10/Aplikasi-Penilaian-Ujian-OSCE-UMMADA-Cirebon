<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Stasi;

class PenugasanSeeder extends Seeder
{
    public function run(): void
    {
        $penguji1 = User::where('email', 'penguji1@osce.test')->first();
        $penguji2 = User::where('email', 'penguji2@osce.test')->first();
        $stasiAll = Stasi::orderBy('id')->get();

        if ($penguji1 && $stasiAll->count() >= 3) {
            // Penguji 1 ditugaskan di Stasi 1, 2, 3
            $penguji1->assignedStasi()->syncWithoutDetaching([
                $stasiAll[0]->id => ['aktif' => true],
                $stasiAll[1]->id => ['aktif' => true],
                $stasiAll[2]->id => ['aktif' => true],
            ]);
        }

        if ($penguji2 && $stasiAll->count() >= 6) {
            // Penguji 2 ditugaskan di Stasi 4, 5, 6
            $penguji2->assignedStasi()->syncWithoutDetaching([
                $stasiAll[3]->id => ['aktif' => true],
                $stasiAll[4]->id => ['aktif' => true],
                $stasiAll[5]->id => ['aktif' => true],
            ]);
        }
    }
}
