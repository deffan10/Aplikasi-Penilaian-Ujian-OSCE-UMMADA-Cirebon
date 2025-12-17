<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Gelombang;
use App\Models\GelombangPenguji;
use App\Models\Stasi;
use App\Models\User;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;

class JadwalPengujiController extends Controller
{
    /**
     * Display list of jadwal for selecting
     */
    public function index()
    {
        $jadwalList = Jadwal::withCount('gelombang')
            ->where('is_arsip', false)
            ->orderBy('mulai', 'desc')
            ->get();

        return view('admin.jadwal-penguji.index', compact('jadwalList'));
    }

    /**
     * Display gelombang list for a jadwal
     */
    public function gelombang(Jadwal $jadwal)
    {
        $gelombangList = $jadwal->gelombang()
            ->withCount(['pengujiStasi', 'mahasiswa'])
            ->orderBy('urutan')
            ->get();

        $stasiList = Stasi::where('aktif', true)->orderBy('id')->get();

        return view('admin.jadwal-penguji.gelombang', compact('jadwal', 'gelombangList', 'stasiList'));
    }

    /**
     * Show form to assign penguji for a gelombang
     */
    public function assign(Jadwal $jadwal, Gelombang $gelombang)
    {
        // Make sure gelombang belongs to jadwal
        if ($gelombang->jadwal_id !== $jadwal->id) {
            abort(404);
        }

        $stasiList = Stasi::where('aktif', true)->orderBy('id')->get();
        $pengujiList = User::where('role', 'penguji')
            ->orderBy('name')
            ->get();

        // Get current assignments
        $currentAssignments = $gelombang->pengujiStasi()
            ->with(['stasi', 'penguji'])
            ->get()
            ->keyBy('stasi_id');

        return view('admin.jadwal-penguji.assign', compact('jadwal', 'gelombang', 'stasiList', 'pengujiList', 'currentAssignments'));
    }

    /**
     * Store penguji assignments for a gelombang
     */
    public function storeAssignment(Request $request, Jadwal $jadwal, Gelombang $gelombang)
    {
        // Make sure gelombang belongs to jadwal
        if ($gelombang->jadwal_id !== $jadwal->id) {
            abort(404);
        }

        $request->validate([
            'penguji' => 'required|array',
            'penguji.*' => 'nullable|exists:users,id',
        ]);

        // Delete existing assignments
        $gelombang->pengujiStasi()->delete();

        // Create new assignments
        foreach ($request->penguji as $stasiId => $pengujiId) {
            if ($pengujiId) {
                GelombangPenguji::create([
                    'gelombang_id' => $gelombang->id,
                    'stasi_id' => $stasiId,
                    'penguji_id' => $pengujiId,
                ]);
            }
        }

        return redirect()
            ->route('admin.jadwal-penguji.gelombang', $jadwal)
            ->with('success', 'Penguji berhasil di-assign ke gelombang ' . $gelombang->nama);
    }

    /**
     * Show form to assign mahasiswa for a gelombang
     */
    public function assignMahasiswa(Jadwal $jadwal, Gelombang $gelombang)
    {
        if ($gelombang->jadwal_id !== $jadwal->id) {
            abort(404);
        }

        // Get all kelas for filter
        $kelasList = \App\Models\Kelas::where('is_arsip', false)
            ->orderBy('nama')
            ->get();

        // Get all mahasiswa grouped by kelas
        $allMahasiswa = Mahasiswa::with('kelas')
            ->orderBy('kelas_id')
            ->orderBy('nim')
            ->get();

        // Get mahasiswa already assigned to other gelombang in this jadwal
        $assignedInOtherGelombang = Mahasiswa::whereHas('gelombang', function($q) use ($jadwal, $gelombang) {
                $q->where('jadwal_id', $jadwal->id)
                  ->where('gelombang.id', '!=', $gelombang->id);
            })
            ->pluck('id')
            ->toArray();

        // Get currently assigned mahasiswa to this gelombang
        $currentMahasiswaIds = $gelombang->mahasiswa()->pluck('mahasiswa.id')->toArray();

        return view('admin.jadwal-penguji.assign-mahasiswa', compact(
            'jadwal', 'gelombang', 'allMahasiswa', 'assignedInOtherGelombang', 'currentMahasiswaIds', 'kelasList'
        ));
    }

    /**
     * Store mahasiswa assignments for a gelombang
     */
    public function storeMahasiswa(Request $request, Jadwal $jadwal, Gelombang $gelombang)
    {
        if ($gelombang->jadwal_id !== $jadwal->id) {
            abort(404);
        }

        $request->validate([
            'mahasiswa_ids' => 'nullable|array',
            'mahasiswa_ids.*' => 'exists:mahasiswa,id',
        ]);

        // Sync mahasiswa
        $gelombang->mahasiswa()->sync($request->mahasiswa_ids ?? []);

        return redirect()
            ->route('admin.jadwal-penguji.gelombang', $jadwal)
            ->with('success', 'Mahasiswa berhasil di-assign ke gelombang ' . $gelombang->nama);
    }

    /**
     * Copy assignments from one gelombang to another
     */
    public function copyAssignment(Request $request, Jadwal $jadwal)
    {
        $request->validate([
            'source_gelombang_id' => 'required|exists:gelombang,id',
            'target_gelombang_id' => 'required|exists:gelombang,id|different:source_gelombang_id',
        ]);

        $source = Gelombang::findOrFail($request->source_gelombang_id);
        $target = Gelombang::findOrFail($request->target_gelombang_id);

        // Validate both belong to this jadwal
        if ($source->jadwal_id !== $jadwal->id || $target->jadwal_id !== $jadwal->id) {
            abort(404);
        }

        // Delete existing target assignments
        $target->pengujiStasi()->delete();

        // Copy from source
        foreach ($source->pengujiStasi as $assignment) {
            GelombangPenguji::create([
                'gelombang_id' => $target->id,
                'stasi_id' => $assignment->stasi_id,
                'penguji_id' => $assignment->penguji_id,
            ]);
        }

        return redirect()
            ->route('admin.jadwal-penguji.gelombang', $jadwal)
            ->with('success', 'Assignment berhasil di-copy dari ' . $source->nama . ' ke ' . $target->nama);
    }

    /**
     * Create new gelombang for a jadwal
     */
    public function createGelombang(Jadwal $jadwal)
    {
        $lastUrutan = $jadwal->gelombang()->max('urutan') ?? 0;
        
        return view('admin.jadwal-penguji.create-gelombang', compact('jadwal', 'lastUrutan'));
    }

    /**
     * Store new gelombang
     */
    public function storeGelombang(Request $request, Jadwal $jadwal)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'waktu_mulai' => 'nullable|date_format:H:i',
            'waktu_selesai' => 'nullable|date_format:H:i',
            'urutan' => 'required|integer|min:1',
        ]);

        Gelombang::create([
            'jadwal_id' => $jadwal->id,
            'nama' => $request->nama,
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'urutan' => $request->urutan,
        ]);

        return redirect()
            ->route('admin.jadwal-penguji.gelombang', $jadwal)
            ->with('success', 'Gelombang berhasil ditambahkan');
    }

    /**
     * Delete a gelombang
     */
    public function destroyGelombang(Jadwal $jadwal, Gelombang $gelombang)
    {
        if ($gelombang->jadwal_id !== $jadwal->id) {
            abort(404);
        }

        // Check if there are nilai records linked to this gelombang
        $nilaiCount = $gelombang->nilai()->count();
        if ($nilaiCount > 0) {
            return redirect()
                ->route('admin.jadwal-penguji.gelombang', $jadwal)
                ->with('error', 'Tidak dapat menghapus gelombang karena sudah ada ' . $nilaiCount . ' nilai terkait');
        }

        $gelombang->pengujiStasi()->delete();
        $gelombang->mahasiswa()->detach();
        $gelombang->delete();

        return redirect()
            ->route('admin.jadwal-penguji.gelombang', $jadwal)
            ->with('success', 'Gelombang berhasil dihapus');
    }
}
