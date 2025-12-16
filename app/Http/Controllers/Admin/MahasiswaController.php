<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MahasiswaImport;

class MahasiswaController extends Controller
{
    public function index(Request $request)
    {
        $query = Mahasiswa::with('kelas');

        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nim', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%");
            });
        }

        $mahasiswa = $query->orderBy('nama')->paginate(20);
        $kelasList = Kelas::orderBy('kode')->get();

        return view('admin.mahasiswa.index', compact('mahasiswa', 'kelasList'));
    }

    public function create()
    {
        $kelasList = Kelas::orderBy('kode')->get();
        return view('admin.mahasiswa.create', compact('kelasList'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nim' => 'required|string|max:20|unique:mahasiswa,nim',
            'nama' => 'required|string|max:100',
            'kelas_id' => 'required|exists:kelas,id',
        ]);

        Mahasiswa::create($data);

        return redirect()->route('admin.mahasiswa.index')
            ->with('success', 'Mahasiswa berhasil ditambahkan.');
    }

    public function edit(Mahasiswa $mahasiswa)
    {
        $kelasList = Kelas::orderBy('kode')->get();
        return view('admin.mahasiswa.edit', compact('mahasiswa', 'kelasList'));
    }

    public function update(Request $request, Mahasiswa $mahasiswa)
    {
        $data = $request->validate([
            'nim' => 'required|string|max:20|unique:mahasiswa,nim,' . $mahasiswa->id,
            'nama' => 'required|string|max:100',
            'kelas_id' => 'required|exists:kelas,id',
        ]);

        $mahasiswa->update($data);

        return redirect()->route('admin.mahasiswa.index')
            ->with('success', 'Mahasiswa berhasil diupdate.');
    }

    public function destroy(Mahasiswa $mahasiswa)
    {
        $mahasiswa->delete();
        return back()->with('success', 'Mahasiswa berhasil dihapus.');
    }

    public function importForm()
    {
        $kelasList = Kelas::orderBy('kode')->get();
        return view('admin.mahasiswa.import', compact('kelasList'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
            'kelas_id' => 'required|exists:kelas,id',
        ]);

        try {
            Excel::import(new MahasiswaImport($request->kelas_id), $request->file('file'));
            return redirect()->route('admin.mahasiswa.index')
                ->with('success', 'Import mahasiswa berhasil.');
        } catch (\Exception $e) {
            return back()->with('error', 'Import gagal: ' . $e->getMessage());
        }
    }
}
