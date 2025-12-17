<?php

namespace App\Exports;

use App\Models\Jadwal;
use App\Models\Stasi;
use App\Models\Mahasiswa;
use App\Models\NilaiAcuanStasi;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class RekapPerJadwalExport implements FromView, WithTitle
{
    protected Jadwal $jadwal;

    public function __construct(Jadwal $jadwal)
    {
        $this->jadwal = $jadwal;
    }

    public function view(): View
    {
        $stasi = Stasi::where('aktif', true)->orderBy('id')->get();
        
        // Load gelombang dengan penguji per stasi DAN mahasiswa
        $gelombangList = $this->jadwal->gelombang()
            ->with(['pengujiStasi.penguji', 'pengujiStasi.stasi', 'mahasiswa'])
            ->orderBy('urutan')
            ->get();
        
        // Map mahasiswa ke gelombang dan kumpulkan semua mahasiswa
        $mahasiswaGelombang = [];
        $allMahasiswaIds = [];
        foreach ($gelombangList as $gel) {
            foreach ($gel->mahasiswa as $mhs) {
                $mahasiswaGelombang[$mhs->id] = $gel;
                $allMahasiswaIds[] = $mhs->id;
            }
        }
        
        // Get peserta dari gelombang (bukan dari jadwal_mahasiswa)
        if (!empty($allMahasiswaIds)) {
            $peserta = Mahasiswa::whereIn('id', $allMahasiswaIds)
                ->with(['kelas', 'nilai' => function ($q) {
                    $q->where('jadwal_id', $this->jadwal->id)
                      ->with(['stasi', 'globalRating', 'penguji', 'gelombang']);
                }])
                ->orderBy('nama')
                ->get();
        } else {
            $peserta = $this->jadwal->peserta()
                ->with(['kelas', 'nilai' => function ($q) {
                    $q->where('jadwal_id', $this->jadwal->id)
                      ->with(['stasi', 'globalRating', 'penguji', 'gelombang']);
                }])
                ->orderBy('nama')
                ->get();
        }

        // Load nilai acuan per stasi for this jadwal
        $nilaiAcuan = NilaiAcuanStasi::where('jadwal_id', $this->jadwal->id)
            ->pluck('nilai_acuan', 'stasi_id')
            ->toArray();

        return view('admin.rekap.excel', [
            'jadwal' => $this->jadwal,
            'stasi' => $stasi,
            'peserta' => $peserta,
            'nilaiAcuan' => $nilaiAcuan,
            'gelombangList' => $gelombangList,
            'mahasiswaGelombang' => $mahasiswaGelombang,
        ]);
    }

    public function title(): string
    {
        return 'Rekap ' . substr($this->jadwal->nama, 0, 25);
    }
}
