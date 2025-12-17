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
use App\Models\Gelombang;
use App\Models\GelombangPenguji;
use App\Services\RegressionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

class PenilaianController extends Controller
{
    /**
     * Dashboard - List gelombang yang ditugaskan ke penguji
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get gelombang dimana penguji ditugaskan
        $penugasan = GelombangPenguji::where('penguji_id', $user->id)
            ->with(['gelombang.jadwal', 'stasi'])
            ->get()
            ->groupBy('gelombang_id');

        // Group by jadwal for better display
        $jadwalList = [];
        foreach ($penugasan as $gelombangId => $items) {
            $gelombang = $items->first()->gelombang;
            $jadwal = $gelombang->jadwal;
            
            if (!isset($jadwalList[$jadwal->id])) {
                $jadwalList[$jadwal->id] = [
                    'jadwal' => $jadwal,
                    'gelombang' => [],
                ];
            }
            
            // Get stasi assigned in this gelombang
            $stasiList = $items->map(fn($p) => $p->stasi)->unique('id');
            
            // Count mahasiswa in gelombang
            $mahasiswaCount = $gelombang->mahasiswa()->count();
            
            // Count nilai yang sudah diinput (hanya untuk mahasiswa yang masih di gelombang ini)
            $mahasiswaIds = $gelombang->mahasiswa()->pluck('mahasiswa.id')->toArray();
            $nilaiCount = Nilai::where('penguji_id', $user->id)
                ->where('gelombang_id', $gelombangId)
                ->whereIn('mahasiswa_id', $mahasiswaIds)
                ->count();
            
            $jadwalList[$jadwal->id]['gelombang'][] = [
                'gelombang' => $gelombang,
                'stasi' => $stasiList,
                'mahasiswa_count' => $mahasiswaCount,
                'nilai_count' => $nilaiCount,
            ];
        }

        return view('penguji.penilaian.index', compact('jadwalList'));
    }

    /**
     * Pilih stasi di gelombang tertentu
     */
    public function selectStasi(Gelombang $gelombang)
    {
        $user = Auth::user();
        
        // Get stasi yang ditugaskan ke penguji di gelombang ini
        $penugasan = GelombangPenguji::where('gelombang_id', $gelombang->id)
            ->where('penguji_id', $user->id)
            ->with('stasi')
            ->get();

        if ($penugasan->isEmpty()) {
            return redirect()->route('penguji.penilaian.index')
                ->with('error', 'Anda tidak ditugaskan di gelombang ini.');
        }

        $jadwal = $gelombang->jadwal;
        $mahasiswaCount = $gelombang->mahasiswa()->count();
        $mahasiswaIds = $gelombang->mahasiswa()->pluck('mahasiswa.id')->toArray();
        
        // Check time status
        $statusWaktu = $gelombang->getStatusWaktu();
        $canInputNilai = $gelombang->canInputNilai();
        
        // Get progress per stasi (hanya hitung nilai untuk mahasiswa yang masih di gelombang ini)
        $stasiProgress = [];
        foreach ($penugasan as $p) {
            $nilaiCount = Nilai::where('penguji_id', $user->id)
                ->where('gelombang_id', $gelombang->id)
                ->where('stasi_id', $p->stasi_id)
                ->whereIn('mahasiswa_id', $mahasiswaIds)
                ->count();
            
            $stasiProgress[$p->stasi_id] = [
                'stasi' => $p->stasi,
                'nilai_count' => $nilaiCount,
                'mahasiswa_count' => $mahasiswaCount,
            ];
        }

        return view('penguji.penilaian.select-stasi', compact('gelombang', 'jadwal', 'stasiProgress', 'statusWaktu', 'canInputNilai'));
    }

    /**
     * List mahasiswa untuk penilaian di gelombang + stasi tertentu
     */
    public function listMahasiswa(Gelombang $gelombang, Stasi $stasi)
    {
        $user = Auth::user();
        
        // Check authorization - penguji harus ditugaskan di gelombang+stasi ini
        $penugasan = GelombangPenguji::where('gelombang_id', $gelombang->id)
            ->where('stasi_id', $stasi->id)
            ->where('penguji_id', $user->id)
            ->first();

        if (!$penugasan) {
            return redirect()->route('penguji.penilaian.index')
                ->with('error', 'Anda tidak ditugaskan untuk menilai stasi ini di gelombang ini.');
        }

        $jadwal = $gelombang->jadwal;
        
        // Check time status
        $statusWaktu = $gelombang->getStatusWaktu();
        $canInputNilai = $gelombang->canInputNilai();
        
        // Get mahasiswa di gelombang ini
        $peserta = $gelombang->mahasiswa()
            ->with(['kelas'])
            ->orderBy('nama')
            ->get();

        // Get existing nilai from this penguji
        $existingNilai = Nilai::where('gelombang_id', $gelombang->id)
            ->where('stasi_id', $stasi->id)
            ->where('penguji_id', $user->id)
            ->pluck('mahasiswa_id')
            ->toArray();

        return view('penguji.penilaian.list', compact('gelombang', 'stasi', 'jadwal', 'peserta', 'existingNilai', 'statusWaktu', 'canInputNilai'));
    }

    /**
     * Show penilaian form
     */
    public function form(Gelombang $gelombang, Stasi $stasi, Mahasiswa $mahasiswa)
    {
        $user = Auth::user();
        
        // Check authorization
        $penugasan = GelombangPenguji::where('gelombang_id', $gelombang->id)
            ->where('stasi_id', $stasi->id)
            ->where('penguji_id', $user->id)
            ->first();

        $canNilai = (bool) $penugasan;
        $jadwal = $gelombang->jadwal;
        
        // Check time validation
        $statusWaktu = $gelombang->getStatusWaktu();
        $canInputNilai = $gelombang->canInputNilai();
        
        // If time hasn't started, canNilai should be false
        if (!$canInputNilai) {
            $canNilai = false;
        }

        // Verify mahasiswa is in this gelombang
        $inGelombang = $gelombang->mahasiswa()->where('mahasiswa.id', $mahasiswa->id)->exists();
        if (!$inGelombang) {
            return redirect()->route('penguji.penilaian.list', [$gelombang, $stasi])
                ->with('error', 'Mahasiswa tidak terdaftar di gelombang ini.');
        }

        $komponens = $stasi->komponens()->orderBy('urutan')->get();
        $globalRatings = GlobalRating::orderBy('nilai')->get();

        // Get existing nilai
        if ($canNilai) {
            $nilai = Nilai::where('gelombang_id', $gelombang->id)
                ->where('stasi_id', $stasi->id)
                ->where('mahasiswa_id', $mahasiswa->id)
                ->where('penguji_id', $user->id)
                ->with(['detail', 'globalRating', 'penguji'])
                ->first();
            $allNilai = null;
        } else {
            // Read-only mode
            $allNilai = Nilai::where('gelombang_id', $gelombang->id)
                ->where('stasi_id', $stasi->id)
                ->where('mahasiswa_id', $mahasiswa->id)
                ->with(['detail', 'globalRating', 'penguji'])
                ->get();
            $nilai = $allNilai->first();
        }

        return view('penguji.penilaian.form', compact(
            'gelombang', 'stasi', 'jadwal', 'mahasiswa', 'komponens', 'globalRatings',
            'canNilai', 'nilai', 'allNilai', 'statusWaktu', 'canInputNilai'
        ));
    }

    /**
     * Store/update penilaian
     */
    public function store(Request $request, Gelombang $gelombang, Stasi $stasi, Mahasiswa $mahasiswa, RegressionService $regressionService)
    {
        $user = Auth::user();
        
        // Time validation - check if gelombang has started
        if (!$gelombang->canInputNilai()) {
            $statusWaktu = $gelombang->getStatusWaktu();
            return redirect()->route('penguji.penilaian.list', [$gelombang, $stasi])
                ->with('error', 'Belum dapat menginput nilai. ' . ($statusWaktu['message'] ?? 'Waktu gelombang belum dimulai.'));
        }
        
        // Authorization check
        $penugasan = GelombangPenguji::where('gelombang_id', $gelombang->id)
            ->where('stasi_id', $stasi->id)
            ->where('penguji_id', $user->id)
            ->first();

        if (!$penugasan) {
            abort(403, 'Anda tidak diizinkan menilai stasi ini di gelombang ini.');
        }

        $jadwal = $gelombang->jadwal;

        $data = $request->validate([
            'skor' => 'required|array',
            'skor.*' => 'required|numeric|min:0|max:3',
            'global_rating_id' => 'required|exists:global_ratings,id',
            'catatan' => 'nullable|string|max:1000',
        ]);

        // Calculate nilai aktual using RegressionService
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
                'gelombang_id' => $gelombang->id,
                'stasi_id' => $stasi->id,
                'mahasiswa_id' => $mahasiswa->id,
                'penguji_id' => $user->id,
            ],
            [
                'global_rating_id' => $data['global_rating_id'],
                'total_nilai' => $nilaiAktual,
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

        // Recalculate nilai acuan for this stasi
        $regressionService->calculateNilaiAcuan($jadwal->id, $stasi->id);

        return redirect()
            ->route('penguji.penilaian.list', [$gelombang, $stasi])
            ->with('success', 'Nilai berhasil disimpan. Nilai Aktual: ' . number_format($nilaiAktual, 2));
    }

    // =====================================================
    // LEGACY ROUTES - untuk backward compatibility
    // =====================================================

    /**
     * [LEGACY] List all stasi (with indicator for assigned ones)
     */
    public function stasiIndex()
    {
        $user = Auth::user();
        $stasi = Stasi::where('stasi.aktif', true)
            ->withCount('komponens')
            ->orderBy('id')
            ->get();

        // Get assigned stasi from gelombang_penguji (new system)
        $assignedFromGelombang = GelombangPenguji::where('penguji_id', $user->id)
            ->whereHas('gelombang.jadwal', function($q) {
                $q->where('is_arsip', false);
            })
            ->pluck('stasi_id')
            ->unique()
            ->toArray();

        // Also check old penguji_stasi table for backward compatibility
        $assignedFromPengujiStasi = $user->assignedStasi()
            ->where('stasi.aktif', true)
            ->wherePivot('aktif', true)
            ->pluck('stasi.id')
            ->toArray();

        // Merge both sources
        $assignedStasiIds = array_unique(array_merge($assignedFromGelombang, $assignedFromPengujiStasi));

        // Get gelombang assignments for each stasi (for linking)
        $stasiGelombang = GelombangPenguji::where('penguji_id', $user->id)
            ->whereHas('gelombang.jadwal', function($q) {
                $q->where('is_arsip', false);
            })
            ->with(['gelombang.jadwal', 'stasi'])
            ->get()
            ->groupBy('stasi_id');

        return view('penguji.stasi.index', compact('stasi', 'assignedStasiIds', 'stasiGelombang'));
    }

    /**
     * Show stasi detail (komponen penilaian)
     */
    public function stasiShow(Stasi $stasi)
    {
        $komponens = $stasi->komponens()->orderBy('urutan')->get();
        
        return view('penguji.stasi.show', compact('stasi', 'komponens'));
    }

    /**
     * [LEGACY] Select jadwal for a stasi
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
            ->orderBy('mulai')
            ->get();

        return view('penguji.penilaian.stasi', compact('stasi', 'jadwalAktif', 'canNilai'));
    }

    /**
     * [LEGACY] List mahasiswa for penilaian in a jadwal+stasi (OLD ROUTE)
     */
    public function listMahasiswaLegacy(Stasi $stasi, Jadwal $jadwal)
    {
        $user = Auth::user();
        $canNilai = Gate::allows('menilai-stasi', $stasi);

        // Get mahasiswa from all gelombang in this jadwal
        $peserta = Mahasiswa::whereHas('gelombang', function($q) use ($jadwal) {
                $q->where('jadwal_id', $jadwal->id);
            })
            ->with(['kelas'])
            ->orderBy('nama')
            ->get();

        // Get existing nilai for this penguji
        $existingNilai = Nilai::where('jadwal_id', $jadwal->id)
            ->where('stasi_id', $stasi->id)
            ->where('penguji_id', $user->id)
            ->pluck('mahasiswa_id')
            ->toArray();

        // Get all nilai from any penguji
        $existingNilaiAll = Nilai::where('jadwal_id', $jadwal->id)
            ->where('stasi_id', $stasi->id)
            ->pluck('mahasiswa_id')
            ->toArray();

        return view('penguji.penilaian.list-legacy', compact('stasi', 'jadwal', 'peserta', 'existingNilai', 'existingNilaiAll', 'canNilai'));
    }
}
