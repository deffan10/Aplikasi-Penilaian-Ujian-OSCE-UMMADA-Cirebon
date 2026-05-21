<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Stasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class PengujiController extends Controller
{
    public function index()
    {
        $penguji = User::where('role', 'penguji')
            ->orderBy('name')
            ->paginate(20);

        // Load stasi assignments from active jadwal (gelombang_penguji)
        $activeJadwalIds = \App\Models\Jadwal::where('is_arsip', false)->pluck('id');
        $activeGelombangIds = \App\Models\Gelombang::whereIn('jadwal_id', $activeJadwalIds)->pluck('id');
        
        $pengujiStasiMap = \App\Models\GelombangPenguji::whereIn('gelombang_id', $activeGelombangIds)
            ->with('stasi')
            ->get()
            ->groupBy('penguji_id')
            ->map(function ($items) {
                return $items->pluck('stasi')->unique('id')->values();
            });

        return view('admin.penguji.index', compact('penguji', 'pengujiStasiMap'));
    }

    public function create()
    {
        return view('admin.penguji.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username|alpha_dash',
            'email' => 'nullable|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'] ?? null,
            'password' => Hash::make($data['password']),
            'plain_password' => $data['password'],
            'role' => 'penguji',
        ]);

        return redirect()->route('admin.penguji.index')
            ->with('success', 'Penguji berhasil ditambahkan.');
    }

    public function edit(User $penguji)
    {
        return view('admin.penguji.edit', compact('penguji'));
    }

    public function update(Request $request, User $penguji)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'username' => 'required|string|max:50|alpha_dash|unique:users,username,' . $penguji->id,
            'email' => 'nullable|email|unique:users,email,' . $penguji->id,
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $penguji->update([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'] ?? null,
        ]);

        if (!empty($data['password'])) {
            $penguji->update([
                'password' => Hash::make($data['password']),
                'plain_password' => $data['password'],
            ]);
        }

        return redirect()->route('admin.penguji.index')
            ->with('success', 'Penguji berhasil diupdate.');
    }

    public function destroy(User $penguji)
    {
        $penguji->delete();
        return back()->with('success', 'Penguji berhasil dihapus.');
    }

    /**
     * Show import form
     */
    public function importForm()
    {
        return view('admin.penguji.import');
    }

    /**
     * Download import template
     */
    public function downloadTemplate()
    {
        $headers = ['Nama', 'Username', 'Password'];
        $exampleData = [
            ['Dr. Budi Santoso', 'budi.santoso', 'password123'],
            ['Dra. Siti Aminah', 'siti.aminah', 'password123'],
        ];

        $callback = function() use ($headers, $exampleData) {
            $file = fopen('php://output', 'w');
            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, $headers);
            foreach ($exampleData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return Response::stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template_import_penguji.csv"',
        ]);
    }

    /**
     * Process import
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();

        $imported = 0;
        $errors = [];
        $rowNum = 0;

        if (($handle = fopen($path, 'r')) !== false) {
            // Skip BOM if present
            $bom = fread($handle, 3);
            if ($bom !== chr(0xEF).chr(0xBB).chr(0xBF)) {
                rewind($handle);
            }

            // Skip header
            fgetcsv($handle);

            DB::beginTransaction();
            try {
                while (($row = fgetcsv($handle)) !== false) {
                    $rowNum++;

                    // Skip empty rows
                    if (empty(array_filter($row))) {
                        continue;
                    }

                    // Validate row has enough columns
                    if (count($row) < 3) {
                        $errors[] = "Baris {$rowNum}: Data tidak lengkap";
                        continue;
                    }

                    $nama = trim($row[0]);
                    $username = trim($row[1]);
                    $password = trim($row[2]);

                    // Validate required fields
                    if (empty($nama) || empty($username) || empty($password)) {
                        $errors[] = "Baris {$rowNum}: Nama, Username, dan Password wajib diisi";
                        continue;
                    }

                    // Validate username format (alphanumeric, dash, underscore)
                    if (!preg_match('/^[a-zA-Z0-9._-]+$/', $username)) {
                        $errors[] = "Baris {$rowNum}: Username '{$username}' hanya boleh huruf, angka, titik, dash, dan underscore";
                        continue;
                    }

                    // Check duplicate username
                    if (User::where('username', $username)->exists()) {
                        $errors[] = "Baris {$rowNum}: Username '{$username}' sudah terdaftar";
                        continue;
                    }

                    // Validate password length
                    if (strlen($password) < 6) {
                        $errors[] = "Baris {$rowNum}: Password minimal 6 karakter";
                        continue;
                    }

                    // Create user
                    User::create([
                        'name' => $nama,
                        'username' => $username,
                        'password' => Hash::make($password),
                        'plain_password' => $password,
                        'role' => 'penguji',
                    ]);

                    $imported++;
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
            }

            fclose($handle);
        }

        $message = "Berhasil import {$imported} penguji.";
        if (count($errors) > 0) {
            $message .= " " . count($errors) . " baris gagal:";
            return redirect()->route('admin.penguji.index')
                ->with('success', $message)
                ->with('import_errors', $errors);
        }

        return redirect()->route('admin.penguji.index')
            ->with('success', $message);
    }

    /**
     * Show jadwal selection before printing labels
     */
    public function printLabels(Request $request)
    {
        // If no jadwal selected, show selection page
        if (!$request->has('jadwal_id')) {
            $jadwalList = \App\Models\Jadwal::where('is_arsip', false)
                ->orderBy('mulai', 'desc')
                ->get();
            
            return view('admin.penguji.print-labels-select', compact('jadwalList'));
        }

        // Filter by selected jadwal
        $jadwal = \App\Models\Jadwal::findOrFail($request->jadwal_id);
        $gelombangIds = $jadwal->gelombang()->pluck('id');

        // Get penguji assignments for this jadwal only
        $assignments = \App\Models\GelombangPenguji::whereIn('gelombang_id', $gelombangIds)
            ->with(['stasi', 'gelombang', 'penguji'])
            ->get();

        // Get unique penguji IDs from this jadwal
        $pengujiIds = $assignments->pluck('penguji_id')->unique();
        $penguji = User::whereIn('id', $pengujiIds)
            ->orderBy('name')
            ->get();

        // Group assignments by penguji
        $pengujiAssignments = $assignments->groupBy('penguji_id')
            ->map(function ($items) {
                return $items->map(function ($gp) {
                    $waktu = '';
                    if ($gp->gelombang->waktu_mulai) {
                        $waktu = $gp->gelombang->waktu_mulai->format('H:i');
                        if ($gp->gelombang->waktu_selesai) {
                            $waktu .= '-' . $gp->gelombang->waktu_selesai->format('H:i');
                        }
                    }
                    return (object) [
                        'stasi_nama' => $gp->stasi->nama ?? '-',
                        'gelombang_nama' => $gp->gelombang->nama ?? '-',
                        'waktu' => $waktu,
                    ];
                })->unique(function ($item) {
                    return $item->stasi_nama . '|' . $item->gelombang_nama;
                })->values();
            });

        // Load label header settings
        $labelLine1 = \App\Models\Setting::get('label_header_line1', '');
        $labelLine2 = \App\Models\Setting::get('label_header_line2', '');
        $labelLine3 = \App\Models\Setting::get('label_header_line3', '');
        $labelLogo = \App\Models\Setting::get('label_logo_path');

        return view('admin.penguji.print-labels', compact(
            'penguji', 'pengujiAssignments', 'jadwal', 'labelLine1', 'labelLine2', 'labelLine3', 'labelLogo'
        ));
    }
}
