<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Stasi;
use Illuminate\Http\Request;

class StasiController extends Controller
{
    public function index()
    {
        $stasi = Stasi::withCount('komponens', 'penguji')
            ->orderBy('id')
            ->paginate(20);

        return view('admin.stasi.index', compact('stasi'));
    }

    public function create()
    {
        return view('admin.stasi.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:150',
            'deskripsi' => 'nullable|string',
            'aktif' => 'boolean',
        ]);

        $data['aktif'] = $request->has('aktif');

        Stasi::create($data);

        return redirect()->route('admin.stasi.index')
            ->with('success', 'Stasi berhasil dibuat.');
    }

    public function edit(Stasi $stasi)
    {
        return view('admin.stasi.edit', compact('stasi'));
    }

    public function update(Request $request, Stasi $stasi)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:150',
            'deskripsi' => 'nullable|string',
            'aktif' => 'boolean',
        ]);

        $data['aktif'] = $request->has('aktif');

        $stasi->update($data);

        return redirect()->route('admin.stasi.index')
            ->with('success', 'Stasi berhasil diupdate.');
    }

    public function destroy(Stasi $stasi)
    {
        $stasi->delete();

        return back()->with('success', 'Stasi berhasil dihapus.');
    }
}
