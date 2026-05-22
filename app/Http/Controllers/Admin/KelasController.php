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
        $jadwalList = \App\Models\Jadwal::where('is_arsip', false)->orderBy('mulai', 'desc')->get();
        return view('admin.kelas.index', compact('kelas', 'jadwalList'));
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

    /**
     * Print kartu peserta for a kelas filtered by jadwal
     */
    public function printKartu(Request $request, Kelas $kela)
    {
        $request->validate([
            'jadwal_id' => 'required|exists:jadwal,id',
        ]);

        $jadwal = \App\Models\Jadwal::findOrFail($request->jadwal_id);

        // Get mahasiswa in this kelas that are assigned to gelombang in this jadwal
        $gelombangIds = $jadwal->gelombang()->pluck('id');

        // Get mahasiswa IDs assigned to gelombang in this jadwal
        $mahasiswaIds = \DB::table('gelombang_mahasiswa')
            ->whereIn('gelombang_id', $gelombangIds)
            ->pluck('mahasiswa_id');

        // Get mahasiswa from this kelas that are in the jadwal
        $mahasiswa = \App\Models\Mahasiswa::where('kelas_id', $kela->id)
            ->whereIn('id', $mahasiswaIds)
            ->orderBy('nama')
            ->get();

        // Get gelombang assignment per mahasiswa
        $mahasiswaGelombang = \DB::table('gelombang_mahasiswa')
            ->whereIn('gelombang_id', $gelombangIds)
            ->whereIn('mahasiswa_id', $mahasiswa->pluck('id'))
            ->get()
            ->groupBy('mahasiswa_id');

        $gelombangList = $jadwal->gelombang()->orderBy('urutan')->get()->keyBy('id');

        // Build assignment data per mahasiswa
        $mahasiswaAssignments = [];
        foreach ($mahasiswa as $mhs) {
            $gIds = $mahasiswaGelombang->get($mhs->id, collect())->pluck('gelombang_id');
            $gelombangNames = [];
            $jadwalUjian = '';
            foreach ($gIds as $gId) {
                $g = $gelombangList->get($gId);
                if ($g) {
                    $gelombangNames[] = $g->nama;
                    if (!$jadwalUjian && $g->waktu_mulai) {
                        $tanggal = $jadwal->mulai ? $jadwal->mulai->format('d M Y') : '';
                        $waktu = $g->waktu_mulai->format('H:i');
                        if ($g->waktu_selesai) {
                            $waktu .= ' - ' . $g->waktu_selesai->format('H:i');
                        }
                        $jadwalUjian = $tanggal . ' ' . $waktu;
                    }
                }
            }
            $mahasiswaAssignments[$mhs->id] = (object) [
                'gelombang' => implode(', ', $gelombangNames),
                'jadwal_ujian' => $jadwalUjian,
            ];
        }

        // Load label header settings
        $labelLogo = \App\Models\Setting::get('label_logo_path');

        return view('admin.kelas.print-kartu', compact(
            'kela', 'jadwal', 'mahasiswa', 'mahasiswaAssignments', 'labelLogo'
        ));
    }
}
