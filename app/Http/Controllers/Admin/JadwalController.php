<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    public function index()
    {
        $jadwal = Jadwal::aktif()
            ->withCount('gelombang')
            ->orderBy('mulai', 'desc')
            ->paginate(20);

        return view('admin.jadwal.index', compact('jadwal'));
    }

    /**
     * Tampilkan daftar jadwal yang diarsip
     */
    public function arsip()
    {
        $jadwal = Jadwal::arsip()
            ->withCount('gelombang')
            ->orderBy('diarsipkan_pada', 'desc')
            ->paginate(20);

        return view('admin.jadwal.arsip', compact('jadwal'));
    }

    public function create()
    {
        return view('admin.jadwal.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:150',
            'mulai' => 'required|date',
            'selesai' => 'required|date|after:mulai',
            'keterangan' => 'nullable|string',
            'tahun_akademik' => 'nullable|string|max:9|regex:/^\d{4}\/\d{4}$/',
            'semester' => 'nullable|in:ganjil,genap',
        ]);

        $jadwal = Jadwal::create([
            'nama' => $data['nama'],
            'mulai' => $data['mulai'],
            'selesai' => $data['selesai'],
            'keterangan' => $data['keterangan'] ?? null,
            'tahun_akademik' => $data['tahun_akademik'] ?? null,
            'semester' => $data['semester'] ?? null,
        ]);

        return redirect()->route('admin.jadwal.show', $jadwal)
            ->with('success', 'Jadwal OSCE berhasil dibuat. Silakan tambahkan Gelombang dan assign mahasiswa/penguji.');
    }

    public function show(Jadwal $jadwal)
    {
        $jadwal->load(['gelombang' => function($q) {
            $q->withCount(['mahasiswa', 'pengujiStasi'])
              ->orderBy('urutan');
        }]);
        
        return view('admin.jadwal.show', compact('jadwal'));
    }

    public function edit(Jadwal $jadwal)
    {
        $jadwal->load('gelombang');
        return view('admin.jadwal.edit', compact('jadwal'));
    }

    public function update(Request $request, Jadwal $jadwal)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:150',
            'mulai' => 'required|date',
            'selesai' => 'required|date|after:mulai',
            'keterangan' => 'nullable|string',
            'tahun_akademik' => 'nullable|string|max:9|regex:/^\d{4}\/\d{4}$/',
            'semester' => 'nullable|in:ganjil,genap',
        ]);

        $jadwal->update([
            'nama' => $data['nama'],
            'mulai' => $data['mulai'],
            'selesai' => $data['selesai'],
            'keterangan' => $data['keterangan'] ?? null,
            'tahun_akademik' => $data['tahun_akademik'] ?? null,
            'semester' => $data['semester'] ?? null,
        ]);

        return redirect()->route('admin.jadwal.show', $jadwal)
            ->with('success', 'Jadwal OSCE berhasil diupdate.');
    }

    public function destroy(Jadwal $jadwal)
    {
        // Check if jadwal has nilai
        if ($jadwal->nilai()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus jadwal karena sudah ada nilai.');
        }
        
        // Delete related gelombang data
        foreach ($jadwal->gelombang as $gelombang) {
            $gelombang->pengujiStasi()->delete();
            $gelombang->mahasiswa()->detach();
        }
        $jadwal->gelombang()->delete();
        $jadwal->delete();
        
        return back()->with('success', 'Jadwal berhasil dihapus.');
    }

    /**
     * Arsipkan jadwal
     */
    public function arsipkan(Jadwal $jadwal)
    {
        $jadwal->update([
            'is_arsip' => true,
            'diarsipkan_pada' => now(),
        ]);

        return back()->with('success', "Jadwal '{$jadwal->nama}' berhasil diarsipkan.");
    }

    /**
     * Restore jadwal dari arsip
     */
    public function restore(Jadwal $jadwal)
    {
        $jadwal->update([
            'is_arsip' => false,
            'diarsipkan_pada' => null,
        ]);

        return redirect()->route('admin.jadwal.arsip')
            ->with('success', "Jadwal '{$jadwal->nama}' berhasil dikembalikan.");
    }
}
