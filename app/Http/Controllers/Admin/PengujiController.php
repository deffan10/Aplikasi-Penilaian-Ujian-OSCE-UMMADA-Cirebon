<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Stasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PengujiController extends Controller
{
    public function index()
    {
        $penguji = User::where('role', 'penguji')
            ->with('assignedStasi')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.penguji.index', compact('penguji'));
    }

    public function create()
    {
        $stasiList = Stasi::where('aktif', true)->orderBy('id')->get();
        return view('admin.penguji.create', compact('stasiList'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'stasi_ids' => 'nullable|array',
            'stasi_ids.*' => 'exists:stasi,id',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'penguji',
        ]);

        if (!empty($data['stasi_ids'])) {
            $user->assignedStasi()->attach($data['stasi_ids'], ['aktif' => true]);
        }

        return redirect()->route('admin.penguji.index')
            ->with('success', 'Penguji berhasil ditambahkan.');
    }

    public function edit(User $penguji)
    {
        $stasiList = Stasi::where('aktif', true)->orderBy('id')->get();
        $assignedStasiIds = $penguji->assignedStasi()->pluck('stasi.id')->toArray();
        
        return view('admin.penguji.edit', compact('penguji', 'stasiList', 'assignedStasiIds'));
    }

    public function update(Request $request, User $penguji)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $penguji->id,
            'password' => 'nullable|string|min:6|confirmed',
            'stasi_ids' => 'nullable|array',
            'stasi_ids.*' => 'exists:stasi,id',
        ]);

        $penguji->update([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        if (!empty($data['password'])) {
            $penguji->update(['password' => Hash::make($data['password'])]);
        }

        // Sync assigned stasi
        $stasiIds = $data['stasi_ids'] ?? [];
        $syncData = [];
        foreach ($stasiIds as $id) {
            $syncData[$id] = ['aktif' => true];
        }
        $penguji->assignedStasi()->sync($syncData);

        return redirect()->route('admin.penguji.index')
            ->with('success', 'Penguji berhasil diupdate.');
    }

    public function destroy(User $penguji)
    {
        $penguji->delete();
        return back()->with('success', 'Penguji berhasil dihapus.');
    }
}
