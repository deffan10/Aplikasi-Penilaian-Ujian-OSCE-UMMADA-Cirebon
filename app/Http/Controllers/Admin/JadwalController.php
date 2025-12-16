<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Mahasiswa;
use App\Models\Kelas;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    public function index()
    {
        $jadwal = Jadwal::aktif()
            ->withCount('peserta')
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
            ->withCount('peserta')
            ->orderBy('diarsipkan_pada', 'desc')
            ->paginate(20);

        return view('admin.jadwal.arsip', compact('jadwal'));
    }

    public function create()
    {
        $kelasList = Kelas::aktif()->with('mahasiswa')->orderBy('kode')->get();
        return view('admin.jadwal.create', compact('kelasList'));
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
            'mahasiswa_ids' => 'required|array|min:1',
            'mahasiswa_ids.*' => 'exists:mahasiswa,id',
        ]);

        $jadwal = Jadwal::create([
            'nama' => $data['nama'],
            'mulai' => $data['mulai'],
            'selesai' => $data['selesai'],
            'keterangan' => $data['keterangan'] ?? null,
            'tahun_akademik' => $data['tahun_akademik'] ?? null,
            'semester' => $data['semester'] ?? null,
        ]);

        // Add selected mahasiswa as peserta
        $jadwal->peserta()->attach($data['mahasiswa_ids']);

        return redirect()->route('admin.jadwal.index')
            ->with('success', 'Jadwal OSCE berhasil dibuat dengan ' . count($data['mahasiswa_ids']) . ' peserta.');
    }

    public function show(Jadwal $jadwal)
    {
        $jadwal->load('peserta.kelas');
        return view('admin.jadwal.show', compact('jadwal'));
    }

    public function edit(Jadwal $jadwal)
    {
        $kelasList = Kelas::aktif()->orderBy('kode')->get();
        $pesertaIds = $jadwal->peserta()->pluck('mahasiswa.id')->toArray();
        $allMahasiswa = Mahasiswa::with('kelas')->orderBy('nama')->get();
        
        return view('admin.jadwal.edit', compact('jadwal', 'kelasList', 'pesertaIds', 'allMahasiswa'));
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
            'mahasiswa_ids' => 'nullable|array',
            'mahasiswa_ids.*' => 'exists:mahasiswa,id',
        ]);

        $jadwal->update([
            'nama' => $data['nama'],
            'mulai' => $data['mulai'],
            'selesai' => $data['selesai'],
            'keterangan' => $data['keterangan'] ?? null,
            'tahun_akademik' => $data['tahun_akademik'] ?? null,
            'semester' => $data['semester'] ?? null,
        ]);

        $jadwal->peserta()->sync($data['mahasiswa_ids'] ?? []);

        return redirect()->route('admin.jadwal.index')
            ->with('success', 'Jadwal OSCE berhasil diupdate.');
    }

    public function destroy(Jadwal $jadwal)
    {
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
