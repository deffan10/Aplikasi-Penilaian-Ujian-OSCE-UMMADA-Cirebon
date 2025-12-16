<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Stasi;
use App\Models\KomponenStasi;
use Illuminate\Http\Request;

class KomponenStasiController extends Controller
{
    public function index(Stasi $stasi)
    {
        $komponen = $stasi->komponens()->orderBy('urutan')->get();
        $totalBobot = $komponen->sum('bobot');

        return view('admin.stasi.komponen.index', compact('stasi', 'komponen', 'totalBobot'));
    }

    public function store(Request $request, Stasi $stasi)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:150',
            'bobot' => 'required|integer|min:0|max:100',
            'urutan' => 'nullable|integer|min:0',
        ]);

        $data['urutan'] = $data['urutan'] ?? ($stasi->komponens()->max('urutan') + 1);

        $stasi->komponens()->create($data);

        return back()->with('success', 'Komponen berhasil ditambahkan.');
    }

    public function update(Request $request, Stasi $stasi, KomponenStasi $komponen)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:150',
            'bobot' => 'required|integer|min:0|max:100',
            'urutan' => 'nullable|integer|min:0',
        ]);

        $komponen->update($data);

        return back()->with('success', 'Komponen berhasil diupdate.');
    }

    public function destroy(Stasi $stasi, KomponenStasi $komponen)
    {
        $komponen->delete();

        return back()->with('success', 'Komponen berhasil dihapus.');
    }
}
