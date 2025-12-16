<?php

namespace App\Exports;

use App\Models\Kelas;
use App\Models\Stasi;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class RekapPerKelasExport implements FromView, WithTitle
{
    protected Kelas $kelas;

    public function __construct(Kelas $kelas)
    {
        $this->kelas = $kelas;
    }

    public function view(): View
    {
        $stasi = Stasi::where('aktif', true)->orderBy('id')->get();
        $mahasiswa = $this->kelas->mahasiswa()
            ->with(['nilai' => function ($q) {
                $q->with(['stasi', 'globalRating', 'penguji', 'jadwal']);
            }])
            ->orderBy('nama')
            ->get();

        return view('admin.rekap.excel-kelas', [
            'kelas' => $this->kelas,
            'stasi' => $stasi,
            'mahasiswa' => $mahasiswa,
        ]);
    }

    public function title(): string
    {
        return 'Rekap ' . $this->kelas->kode;
    }
}
