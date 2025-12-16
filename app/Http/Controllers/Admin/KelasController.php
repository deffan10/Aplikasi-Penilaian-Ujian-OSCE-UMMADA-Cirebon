<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index()
    {
        $kelas = Kelas::aktif()->withCount('mahasiswa')->orderBy('kode')->paginate(20);
        return view('admin.kelas.index', compact('kelas'));
    }

    /**
     * Tampilkan daftar kelas yang diarsip
     */
    public function arsip()
    {
        $kelas = Kelas::arsip()->withCount('mahasiswa')->orderBy('diarsipkan_pada', 'desc')->paginate(20);
        return view('admin.kelas.arsip', compact('kelas'));
    }

    public function create()
    {
        return view('admin.kelas.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kode' => 'required|string|max:20|unique:kelas,kode',
            'nama' => 'nullable|string|max:100',
            'tahun_akademik' => 'nullable|string|max:9|regex:/^\d{4}\/\d{4}$/',
            'semester' => 'nullable|in:ganjil,genap',
        ]);

        Kelas::create($data);

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Kelas berhasil dibuat.');
    }

    public function edit(Kelas $kela)
    {
        return view('admin.kelas.edit', ['kelas' => $kela]);
    }

    public function update(Request $request, Kelas $kela)
    {
        $data = $request->validate([
            'kode' => 'required|string|max:20|unique:kelas,kode,' . $kela->id,
            'nama' => 'nullable|string|max:100',
            'tahun_akademik' => 'nullable|string|max:9|regex:/^\d{4}\/\d{4}$/',
            'semester' => 'nullable|in:ganjil,genap',
        ]);

        $kela->update($data);

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Kelas berhasil diupdate.');
    }

    public function destroy(Kelas $kela)
    {
        $kela->delete();
        return back()->with('success', 'Kelas berhasil dihapus.');
    }

    /**
     * Arsipkan kelas
     */
    public function arsipkan(Kelas $kela)
    {
        $kela->update([
            'is_arsip' => true,
            'diarsipkan_pada' => now(),
        ]);

        return back()->with('success', "Kelas {$kela->kode} berhasil diarsipkan.");
    }

    /**
     * Restore kelas dari arsip
     */
    public function restore(Kelas $kela)
    {
        $kela->update([
            'is_arsip' => false,
            'diarsipkan_pada' => null,
        ]);

        return redirect()->route('admin.kelas.arsip')
            ->with('success', "Kelas {$kela->kode} berhasil dikembalikan.");
    }
}
