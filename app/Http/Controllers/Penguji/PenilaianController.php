<?php

namespace App\Http\Controllers\Penguji;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Stasi;
use App\Models\Mahasiswa;
use App\Models\Nilai;
use App\Models\NilaiDetail;
use App\Models\GlobalRating;
use App\Models\LogPenilaian;
use App\Models\NilaiAcuanStasi;
use App\Services\RegressionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class PenilaianController extends Controller
{
    /**
     * List all stasi (with indicator for assigned ones)
     */
    public function index()
    {
        $user = Auth::user();
        $stasi = Stasi::where('stasi.aktif', true)
            ->withCount('komponens')
            ->orderBy('id')
            ->get();

        $assignedStasiIds = $user->assignedStasi()
            ->where('stasi.aktif', true)
            ->wherePivot('aktif', true)
            ->pluck('stasi.id')
            ->toArray();

        return view('penguji.stasi.index', compact('stasi', 'assignedStasiIds'));
    }

    /**
     * Select jadwal for a stasi
     */
    public function selectJadwal(Stasi $stasi)
    {
        $user = Auth::user();
        $canNilai = Gate::allows('menilai-stasi', $stasi);

        $jadwalAktif = Jadwal::where(function ($query) {
                $query->where('mulai', '<=', now())
                      ->where('selesai', '>=', now());
            })
            ->orWhere('mulai', '>', now())
            ->with('peserta')
            ->orderBy('mulai')
            ->get();

        return view('penguji.penilaian.stasi', compact('stasi', 'jadwalAktif', 'canNilai'));
    }

    /**
     * List mahasiswa for penilaian in a jadwal+stasi
     */
    public function listMahasiswa(Stasi $stasi, Jadwal $jadwal)
    {
        $user = Auth::user();
        $canNilai = Gate::allows('menilai-stasi', $stasi);

        $peserta = $jadwal->peserta()
            ->with(['kelas'])
            ->orderBy('nama')
            ->get();

        // Get existing nilai for this penguji
        $existingNilai = Nilai::where('jadwal_id', $jadwal->id)
            ->where('stasi_id', $stasi->id)
            ->where('penguji_id', $user->id)
            ->pluck('mahasiswa_id')
            ->toArray();

        // Get all nilai from any penguji (for read-only status display)
        $existingNilaiAll = Nilai::where('jadwal_id', $jadwal->id)
            ->where('stasi_id', $stasi->id)
            ->pluck('mahasiswa_id')
            ->toArray();

        return view('penguji.penilaian.list', compact('stasi', 'jadwal', 'peserta', 'existingNilai', 'existingNilaiAll', 'canNilai'));
    }

    /**
     * Show penilaian form
     */
    public function form(Stasi $stasi, Jadwal $jadwal, Mahasiswa $mahasiswa)
    {
        $user = Auth::user();
        $canNilai = Gate::allows('menilai-stasi', $stasi);

        $komponens = $stasi->komponens()->orderBy('urutan')->get();
        $globalRatings = GlobalRating::orderBy('nilai')->get();

        // Check for existing nilai (from current user if can nilai, or from any penguji if read-only)
        if ($canNilai) {
            // Get current user's nilai
            $nilai = Nilai::where('jadwal_id', $jadwal->id)
                ->where('stasi_id', $stasi->id)
                ->where('mahasiswa_id', $mahasiswa->id)
                ->where('penguji_id', $user->id)
                ->with(['detail', 'globalRating', 'penguji'])
                ->first();
            $allNilai = null;
        } else {
            // Get all nilai from any penguji (read-only mode)
            $allNilai = Nilai::where('jadwal_id', $jadwal->id)
                ->where('stasi_id', $stasi->id)
                ->where('mahasiswa_id', $mahasiswa->id)
                ->with(['detail', 'globalRating', 'penguji'])
                ->get();
            $nilai = $allNilai->first(); // Show first one as main
        }

        return view('penguji.penilaian.form', compact(
            'stasi', 'jadwal', 'mahasiswa', 'komponens', 'globalRatings',
            'canNilai', 'nilai', 'allNilai'
        ));
    }

    /**
     * Store/update penilaian
     */
    public function store(Request $request, Stasi $stasi, Jadwal $jadwal, Mahasiswa $mahasiswa, RegressionService $regressionService)
    {
        // Authorization check
        $this->authorize('menilai-stasi', $stasi);

        $data = $request->validate([
            'skor' => 'required|array',
            'skor.*' => 'required|numeric|min:0|max:3',
            'global_rating_id' => 'required|exists:global_ratings,id',
            'catatan' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();

        // Calculate nilai aktual using RegressionService
        // Formula: Σ(skor × bobot) normalized to 0-100
        $nilaiAktual = $regressionService->calculateNilaiAktual($data['skor'], $stasi->id);

        // Get nilai acuan for this stasi (if exists)
        $nilaiAcuan = NilaiAcuanStasi::where('jadwal_id', $jadwal->id)
            ->where('stasi_id', $stasi->id)
            ->first();
        
        $lulusStasi = $nilaiAcuan ? ($nilaiAktual >= $nilaiAcuan->nilai_acuan) : null;

        // Create or update nilai
        $nilai = Nilai::updateOrCreate(
            [
                'jadwal_id' => $jadwal->id,
                'stasi_id' => $stasi->id,
                'mahasiswa_id' => $mahasiswa->id,
                'penguji_id' => $user->id,
            ],
            [
                'global_rating_id' => $data['global_rating_id'],
                'total_nilai' => $nilaiAktual, // Keep for backward compatibility
                'nilai_aktual' => $nilaiAktual,
                'lulus_stasi' => $lulusStasi,
                'catatan' => $data['catatan'] ?? null,
            ]
        );

        // Update detail scores
        foreach ($data['skor'] as $komponenId => $skor) {
            NilaiDetail::updateOrCreate(
                [
                    'nilai_id' => $nilai->id,
                    'komponen_stasi_id' => $komponenId,
                ],
                ['skor' => $skor]
            );
        }

        // Add log
        LogPenilaian::create([
            'nilai_id' => $nilai->id,
            'penguji_id' => $user->id,
            'aksi' => 'simpan',
            'keterangan' => 'Nilai Aktual: ' . number_format($nilaiAktual, 2),
        ]);

        // Recalculate nilai acuan for this stasi (after new data)
        $regressionService->calculateNilaiAcuan($jadwal->id, $stasi->id);

        return redirect()
            ->route('penguji.penilaian.list', [$stasi, $jadwal])
            ->with('success', 'Nilai berhasil disimpan. Nilai Aktual: ' . number_format($nilaiAktual, 2));
    }
}
