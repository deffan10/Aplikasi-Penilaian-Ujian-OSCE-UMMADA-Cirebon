<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Stasi;
use App\Models\Setting;
use App\Models\NilaiAcuanStasi;
use App\Services\RegressionService;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RekapPerJadwalExport;
use App\Exports\RekapPerKelasExport;
use Illuminate\Http\Request;

class RekapController extends Controller
{
    protected $regressionService;

    public function __construct(RegressionService $regressionService)
    {
        $this->regressionService = $regressionService;
    }

    public function index()
    {
        $jadwalList = Jadwal::orderBy('mulai', 'desc')->get();
        $kelasList = Kelas::orderBy('kode')->get();
        
        return view('admin.rekap.index', compact('jadwalList', 'kelasList'));
    }

    public function perJadwal(Jadwal $jadwal)
    {
        $stasi = Stasi::where('aktif', true)->orderBy('id')->get();
        
        // Load gelombang dengan penguji per stasi DAN mahasiswa
        $gelombangList = $jadwal->gelombang()
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
        // Jika ada mahasiswa di gelombang, ambil dari sana
        if (!empty($allMahasiswaIds)) {
            $peserta = \App\Models\Mahasiswa::whereIn('id', $allMahasiswaIds)
                ->with(['kelas', 'nilai' => function ($q) use ($jadwal) {
                    $q->where('jadwal_id', $jadwal->id)
                      ->with(['stasi', 'globalRating', 'penguji', 'gelombang']);
                }])
                ->orderBy('nama')
                ->get();
        } else {
            // Fallback ke jadwal_mahasiswa jika belum ada gelombang
            $peserta = $jadwal->peserta()
                ->with(['kelas', 'nilai' => function ($q) use ($jadwal) {
                    $q->where('jadwal_id', $jadwal->id)
                      ->with(['stasi', 'globalRating', 'penguji', 'gelombang']);
                }])
                ->orderBy('nama')
                ->get();
        }

        // Load nilai acuan per stasi for this jadwal with full details
        $nilaiAcuanData = NilaiAcuanStasi::where('jadwal_id', $jadwal->id)->get();
        $nilaiAcuan = $nilaiAcuanData->pluck('nilai_acuan', 'stasi_id')->toArray();
        $nilaiAcuanDetails = $nilaiAcuanData->keyBy('stasi_id');

        return view('admin.rekap.jadwal', compact('jadwal', 'stasi', 'peserta', 'nilaiAcuan', 'nilaiAcuanDetails', 'gelombangList', 'mahasiswaGelombang'));
    }

    /**
     * Calculate/Recalculate nilai acuan for a jadwal
     */
    public function hitungNilaiAcuan(Jadwal $jadwal)
    {
        try {
            $results = $this->regressionService->calculateAllNilaiAcuan($jadwal->id);
            
            $details = [];
            foreach ($results as $r) {
                $details[] = "Stasi {$r->stasi_id}: Acuan={$r->nilai_acuan} (n={$r->sample_count})";
            }
            
            $message = 'Nilai Acuan berhasil dihitung untuk ' . count($results) . ' stasi.';
            if (count($details) > 0) {
                $message .= ' ' . implode(', ', $details);
            }
            
            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghitung Nilai Acuan: ' . $e->getMessage());
        }
    }

    /**
     * Rekap Per Stasi - Filter by Jadwal and Stasi
     */
    public function perStasi(Request $request)
    {
        $jadwalList = Jadwal::orderBy('mulai', 'desc')->get();
        $stasiList = Stasi::where('aktif', true)->orderBy('id')->get();
        
        $jadwal = null;
        $stasi = null;
        $nilaiList = collect();
        $nilaiAcuan = null;
        
        if ($request->filled('jadwal_id') && $request->filled('stasi_id')) {
            $jadwal = Jadwal::find($request->jadwal_id);
            $stasi = Stasi::find($request->stasi_id);
            
            if ($jadwal && $stasi) {
                // Get all nilai for this jadwal + stasi
                $nilaiList = \App\Models\Nilai::where('jadwal_id', $jadwal->id)
                    ->where('stasi_id', $stasi->id)
                    ->with(['mahasiswa.kelas', 'penguji', 'globalRating', 'gelombang'])
                    ->get()
                    ->sortBy(function($n) {
                        return [
                            $n->gelombang->urutan ?? 999,
                            $n->mahasiswa->nama ?? ''
                        ];
                    });
                
                // Get nilai acuan for this stasi
                $nilaiAcuanData = NilaiAcuanStasi::where('jadwal_id', $jadwal->id)
                    ->where('stasi_id', $stasi->id)
                    ->first();
                $nilaiAcuan = $nilaiAcuanData ? $nilaiAcuanData->nilai_acuan : null;
            }
        }
        
        return view('admin.rekap.stasi', compact('jadwalList', 'stasiList', 'jadwal', 'stasi', 'nilaiList', 'nilaiAcuan'));
    }

    public function perKelas(Kelas $kelas)
    {
        $stasi = Stasi::where('aktif', true)->orderBy('id')->get();
        $mahasiswa = $kelas->mahasiswa()
            ->with(['nilai' => function ($q) {
                $q->with(['stasi', 'globalRating', 'penguji', 'jadwal']);
            }])
            ->orderBy('nama')
            ->get();

        return view('admin.rekap.kelas', compact('kelas', 'stasi', 'mahasiswa'));
    }

    public function exportPdfJadwal(Jadwal $jadwal)
    {
        $setting = (object) [
            'kop_surat_path' => Setting::get('kop_surat_path'),
            'penandatangan_jabatan' => Setting::get('penandatangan_jabatan', ''),
            'penandatangan_nama' => Setting::get('penandatangan_nama', ''),
            'penandatangan_nik' => Setting::get('penandatangan_nik', ''),
        ];
        
        $stasi = Stasi::where('aktif', true)->orderBy('id')->get();
        
        // Load gelombang dengan penguji per stasi DAN mahasiswa
        $gelombangList = $jadwal->gelombang()
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
            $peserta = \App\Models\Mahasiswa::whereIn('id', $allMahasiswaIds)
                ->with(['kelas', 'nilai' => function ($q) use ($jadwal) {
                    $q->where('jadwal_id', $jadwal->id)
                      ->with(['stasi', 'globalRating', 'penguji', 'gelombang']);
                }])
                ->orderBy('nama')
                ->get();
        } else {
            $peserta = $jadwal->peserta()
                ->with(['kelas', 'nilai' => function ($q) use ($jadwal) {
                    $q->where('jadwal_id', $jadwal->id)
                      ->with(['stasi', 'globalRating', 'penguji', 'gelombang']);
                }])
                ->orderBy('nama')
                ->get();
        }

        // Load nilai acuan per stasi for this jadwal
        $nilaiAcuan = NilaiAcuanStasi::where('jadwal_id', $jadwal->id)
            ->pluck('nilai_acuan', 'stasi_id')
            ->toArray();

        $pdf = Pdf::loadView('admin.rekap.pdf', compact('jadwal', 'stasi', 'peserta', 'setting', 'nilaiAcuan', 'gelombangList', 'mahasiswaGelombang'));
        $pdf->setPaper('a4', 'landscape');
        
        return $pdf->download('rekap-jadwal-' . $jadwal->id . '.pdf');
    }

    public function exportExcelJadwal(Jadwal $jadwal)
    {
        return Excel::download(
            new RekapPerJadwalExport($jadwal),
            'rekap-jadwal-' . $jadwal->id . '.xlsx'
        );
    }

    public function exportPdfKelas(Kelas $kelas)
    {
        $setting = (object) [
            'kop_surat_path' => Setting::get('kop_surat_path'),
            'penandatangan_jabatan' => Setting::get('penandatangan_jabatan', ''),
            'penandatangan_nama' => Setting::get('penandatangan_nama', ''),
            'penandatangan_nik' => Setting::get('penandatangan_nik', ''),
        ];
        
        $stasi = Stasi::where('aktif', true)->orderBy('id')->get();
        $mahasiswa = $kelas->mahasiswa()
            ->with(['nilai' => function ($q) {
                $q->with(['stasi', 'globalRating', 'penguji', 'jadwal']);
            }])
            ->orderBy('nama')
            ->get();

        $pdf = Pdf::loadView('admin.rekap.pdf-kelas', compact('kelas', 'stasi', 'mahasiswa', 'setting'));
        $pdf->setPaper('a4', 'landscape');
        
        return $pdf->download('rekap-kelas-' . $kelas->kode . '.pdf');
    }

    public function exportExcelKelas(Kelas $kelas)
    {
        return Excel::download(
            new RekapPerKelasExport($kelas),
            'rekap-kelas-' . $kelas->kode . '.xlsx'
        );
    }
}
