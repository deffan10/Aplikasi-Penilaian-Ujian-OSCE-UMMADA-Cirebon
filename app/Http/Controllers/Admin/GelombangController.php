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

class GelombangController extends Controller
{
    /**
     * Display list of jadwal with their gelombang count (for standalone Kelola Gelombang page)
     */
    public function list()
    {
        $jadwalList = Jadwal::where('is_arsip', false)
            ->withCount('gelombang')
            ->orderBy('mulai', 'desc')
            ->get();
        
        return view('admin.gelombang.list', compact('jadwalList'));
    }

    /**
     * Display gelombang list for a jadwal
     */
    public function index(Jadwal $jadwal)
    {
        $gelombangList = $jadwal->gelombang()
            ->with(['mahasiswa', 'pengujiStasi.penguji', 'pengujiStasi.stasi'])
            ->orderBy('urutan')
            ->get();
        
        $stasiList = Stasi::where('aktif', true)->orderBy('id')->get();
        
        return view('admin.gelombang.index', compact('jadwal', 'gelombangList', 'stasiList'));
    }

    /**
     * Show form to create new gelombang
     */
    public function create(Jadwal $jadwal)
    {
        $nextUrutan = $jadwal->gelombang()->max('urutan') + 1;
        
        return view('admin.gelombang.create', compact('jadwal', 'nextUrutan'));
    }

    /**
     * Store new gelombang
     */
    public function store(Request $request, Jadwal $jadwal)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'waktu_mulai' => 'nullable|date_format:H:i',
            'waktu_selesai' => 'nullable|date_format:H:i',
            'urutan' => 'required|integer|min:1',
        ]);

        // Create gelombang
        $jadwal->gelombang()->create([
            'nama' => $request->nama,
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'urutan' => $request->urutan,
        ]);

        return redirect()
            ->route('admin.jadwal.gelombang.index', $jadwal)
            ->with('success', 'Gelombang berhasil dibuat.');
    }

    /**
     * Show form to edit gelombang
     */
    public function edit(Jadwal $jadwal, Gelombang $gelombang)
    {
        // Load current counts for display (read-only)
        $pengujiCount = $gelombang->pengujiStasi()->count();
        $mahasiswaCount = $gelombang->mahasiswa()->count();
        
        // Load penguji per stasi for info display
        $pengujiPerStasi = $gelombang->pengujiStasi()
            ->with(['stasi', 'penguji'])
            ->get()
            ->groupBy('stasi_id');
        
        return view('admin.gelombang.edit', compact(
            'jadwal', 'gelombang', 'pengujiCount', 'mahasiswaCount', 'pengujiPerStasi'
        ));
    }

    /**
     * Update gelombang (only basic info - penguji & mahasiswa managed via assign pages)
     */
    public function update(Request $request, Jadwal $jadwal, Gelombang $gelombang)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'waktu_mulai' => 'nullable|date_format:H:i',
            'waktu_selesai' => 'nullable|date_format:H:i',
            'urutan' => 'required|integer|min:1',
        ]);

        // Only update basic info - DO NOT touch penguji or mahasiswa
        $gelombang->update([
            'nama' => $request->nama,
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'urutan' => $request->urutan,
        ]);

        return redirect()
            ->route('admin.jadwal.gelombang.index', $jadwal)
            ->with('success', 'Gelombang berhasil diupdate.');
    }

    /**
     * Delete gelombang
     */
    public function destroy(Jadwal $jadwal, Gelombang $gelombang)
    {
        // Check if ada nilai terkait
        if ($gelombang->nilai()->count() > 0) {
            return redirect()
                ->route('admin.jadwal.gelombang.index', $jadwal)
                ->with('error', 'Tidak dapat menghapus gelombang yang sudah memiliki data penilaian.');
        }

        $gelombang->delete();

        return redirect()
            ->route('admin.jadwal.gelombang.index', $jadwal)
            ->with('success', 'Gelombang berhasil dihapus.');
    }
}
