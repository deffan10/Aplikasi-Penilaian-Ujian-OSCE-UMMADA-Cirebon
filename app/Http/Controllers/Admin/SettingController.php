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
            'label_header_line1' => Setting::get('label_header_line1', ''),
            'label_header_line2' => Setting::get('label_header_line2', ''),
            'label_header_line3' => Setting::get('label_header_line3', ''),
            'label_logo_path' => Setting::get('label_logo_path'),
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
            'label_header_line1' => 'nullable|string|max:200',
            'label_header_line2' => 'nullable|string|max:200',
            'label_header_line3' => 'nullable|string|max:200',
            'label_logo' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
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

        if ($request->hasFile('label_logo')) {
            // Delete old file if exists
            $oldPath = Setting::get('label_logo_path');
            if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }

            // Store new file
            $path = $request->file('label_logo')->store('label', 'public');
            Setting::set('label_logo_path', $path);
        }

        // Save penandatangan settings
        Setting::set('penandatangan_jabatan', $request->penandatangan_jabatan);
        Setting::set('penandatangan_nama', $request->penandatangan_nama);
        Setting::set('penandatangan_nik', $request->penandatangan_nik);

        // Save label header settings
        Setting::set('label_header_line1', $request->label_header_line1);
        Setting::set('label_header_line2', $request->label_header_line2);
        Setting::set('label_header_line3', $request->label_header_line3);

        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }
}
