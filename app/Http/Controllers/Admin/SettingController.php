<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $setting = (object) [
            'kop_surat_path' => Setting::get('kop_surat_path'),
            'penandatangan_jabatan' => Setting::get('penandatangan_jabatan', ''),
            'penandatangan_nama' => Setting::get('penandatangan_nama', ''),
            'penandatangan_nik' => Setting::get('penandatangan_nik', ''),
        ];

        return view('admin.settings.index', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'kop_surat' => 'nullable|image|mimes:png,jpg,jpeg|max:4096',
            'penandatangan_jabatan' => 'nullable|string|max:200',
            'penandatangan_nama' => 'nullable|string|max:200',
            'penandatangan_nik' => 'nullable|string|max:100',
        ]);

        if ($request->hasFile('kop_surat')) {
            // Delete old file if exists
            $oldPath = Setting::get('kop_surat_path');
            if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }

            // Store new file
            $path = $request->file('kop_surat')->store('kop', 'public');
            Setting::set('kop_surat_path', $path);
        }

        // Save penandatangan settings
        Setting::set('penandatangan_jabatan', $request->penandatangan_jabatan);
        Setting::set('penandatangan_nama', $request->penandatangan_nama);
        Setting::set('penandatangan_nik', $request->penandatangan_nik);

        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }
}
