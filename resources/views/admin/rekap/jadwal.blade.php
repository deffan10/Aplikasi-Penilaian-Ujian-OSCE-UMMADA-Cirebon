@extends('layouts.app')

@section('title', 'Rekap Jadwal: ' . $jadwal->nama)

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex justify-between items-start">
                <div>
                    <h2 class="text-xl font-semibold">{{ $jadwal->nama }}</h2>
                    <p class="text-gray-500 mt-1">
                        Tanggal: {{ $jadwal->mulai->format('d F Y H:i') }} - {{ $jadwal->selesai->format('H:i') }} | 
                        Peserta: {{ $peserta->count() }} mahasiswa
                    </p>
                </div>
                <div class="flex gap-2">
                    {{-- Tombol Hitung Nilai Acuan --}}
                    <form action="{{ route('admin.rekap.jadwal.hitungNilaiAcuan', $jadwal) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700" 
                                onclick="return confirm('Hitung/Update Nilai Acuan berdasarkan regresi Global Rating? Proses ini membutuhkan minimal 3 data penilaian per stasi.')">
                            📊 Hitung Nilai Acuan
                        </button>
                    </form>
                    <a href="{{ route('admin.rekap.jadwal.pdf', $jadwal) }}" target="_blank" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">
                        Export PDF
                    </a>
                    <a href="{{ route('admin.rekap.jadwal.excel', $jadwal) }}" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                        Export Excel
                    </a>
                    <a href="{{ route('admin.rekap.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300">
                        Kembali
                    </a>
                </div>
            </div>

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif
        </div>
    </div>

    {{-- Info Nilai Acuan per Stasi --}}
    @if(count($nilaiAcuan) > 0)
        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
            <h4 class="font-semibold text-purple-800 mb-2">Nilai Acuan (Standard Setting) per Stasi</h4>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-2">
                @foreach($stasi as $s)
                    <div class="bg-white rounded p-2 text-center">
                        <div class="text-xs text-gray-500">{{ $s->nama }}</div>
                        <div class="text-lg font-bold {{ isset($nilaiAcuan[$s->id]) ? 'text-purple-600' : 'text-gray-400' }}">
                            {{ isset($nilaiAcuan[$s->id]) ? number_format($nilaiAcuan[$s->id], 2) : '-' }}
                        </div>
                        @if(isset($nilaiAcuanDetails[$s->id]))
                            <div class="text-xs text-gray-400">
                                n={{ $nilaiAcuanDetails[$s->id]->sample_count }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
            <p class="text-xs text-purple-600 mt-2">
                * Nilai Acuan = nilai saat Global Rating = 2 (Borderline), dihitung dengan regresi linear dari seluruh penilaian peserta per stasi.
                <br>Mahasiswa dinyatakan LULUS jika Total Nilai Aktual ≥ Total Nilai Acuan.
            </p>
        </div>
    @else
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <p class="text-yellow-800">
                ⚠️ Nilai Acuan belum dihitung. Klik tombol "Hitung Nilai Acuan" setelah ada minimal 2 penilaian per stasi.
            </p>
        </div>
    @endif

    {{-- Tabel Rekap --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIM</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Gelombang</th>
                            @foreach($stasi as $s)
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase" colspan="2">
                                    {{ $s->nama }}
                                    @if(isset($nilaiAcuan[$s->id]))
                                        <div class="text-xxs text-purple-500 font-normal">(≥{{ number_format($nilaiAcuan[$s->id], 0) }})</div>
                                    @endif
                                </th>
                            @endforeach
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Total Aktual</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Total Acuan</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                        {{-- Sub header untuk Nilai & Penguji --}}
                        <tr class="bg-gray-100">
                            <th colspan="4"></th>
                            @foreach($stasi as $s)
                                <th class="px-2 py-1 text-center text-xxs font-medium text-gray-400 uppercase">Nilai</th>
                                <th class="px-2 py-1 text-center text-xxs font-medium text-gray-400 uppercase">Penguji</th>
                            @endforeach
                            <th colspan="3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($peserta as $idx => $mhs)
                            @php
                                $nilaiPerStasi = [];
                                $totalNilaiAktual = 0;
                                $totalNilaiAcuanMhs = 0;
                                $countNilai = 0;
                                
                                // Get gelombang mahasiswa
                                $gelombangMhs = $mahasiswaGelombang[$mhs->id] ?? null;
                                
                                foreach($stasi as $s) {
                                    $nilai = $mhs->nilai->where('jadwal_id', $jadwal->id)->where('stasi_id', $s->id)->first();
                                    $nilaiPerStasi[$s->id] = $nilai;
                                    if ($nilai) {
                                        // Use nilai_aktual if available, fallback to total_nilai
                                        $nilaiAktualStasi = $nilai->nilai_aktual ?? $nilai->total_nilai;
                                        $totalNilaiAktual += $nilaiAktualStasi;
                                        $countNilai++;
                                        
                                        // Sum nilai acuan for stasi that have nilai
                                        if (isset($nilaiAcuan[$s->id])) {
                                            $totalNilaiAcuanMhs += $nilaiAcuan[$s->id];
                                        }
                                    }
                                }
                                
                                // Kelulusan: Total Nilai Aktual >= Total Nilai Acuan
                                $statusLulus = ($countNilai > 0 && count($nilaiAcuan) > 0) 
                                    ? $totalNilaiAktual >= $totalNilaiAcuanMhs 
                                    : null;
                            @endphp
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $idx + 1 }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">{{ $mhs->nim }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $mhs->nama }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-center">
                                    @if($gelombangMhs)
                                        <span class="px-2 py-1 text-xs rounded bg-indigo-100 text-indigo-800">{{ $gelombangMhs->nama }}</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                @foreach($stasi as $s)
                                    {{-- Kolom Nilai --}}
                                    <td class="px-2 py-3 whitespace-nowrap text-sm text-center">
                                        @if($nilaiPerStasi[$s->id])
                                            @php
                                                $nilaiAktualStasi = $nilaiPerStasi[$s->id]->nilai_aktual ?? $nilaiPerStasi[$s->id]->total_nilai;
                                                $acuanStasi = $nilaiAcuan[$s->id] ?? null;
                                                $lulusStasi = $acuanStasi ? $nilaiAktualStasi >= $acuanStasi : $nilaiAktualStasi >= 70;
                                            @endphp
                                            <div class="{{ $lulusStasi ? 'text-green-600' : 'text-red-600' }} font-medium">
                                                {{ number_format($nilaiAktualStasi, 1) }}
                                            </div>
                                            @if($nilaiPerStasi[$s->id]->globalRating)
                                                <div class="text-xs 
                                                    {{ $nilaiPerStasi[$s->id]->globalRating->nilai == 1 ? 'text-red-500' : '' }}
                                                    {{ $nilaiPerStasi[$s->id]->globalRating->nilai == 2 ? 'text-yellow-500' : '' }}
                                                    {{ $nilaiPerStasi[$s->id]->globalRating->nilai == 3 ? 'text-green-500' : '' }}
                                                    {{ $nilaiPerStasi[$s->id]->globalRating->nilai == 4 ? 'text-blue-500' : '' }}">
                                                    GR:{{ $nilaiPerStasi[$s->id]->globalRating->nilai }}
                                                </div>
                                            @endif
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    {{-- Kolom Penguji --}}
                                    <td class="px-2 py-3 whitespace-nowrap text-xs text-center text-gray-500">
                                        @if($nilaiPerStasi[$s->id] && $nilaiPerStasi[$s->id]->penguji)
                                            {{ $nilaiPerStasi[$s->id]->penguji->name }}
                                        @elseif($gelombangMhs)
                                            @php
                                                $pengujiGel = $gelombangMhs->getPengujiForStasi($s->id);
                                            @endphp
                                            @if($pengujiGel)
                                                <span class="text-gray-400">{{ $pengujiGel->name }}</span>
                                            @else
                                                <span class="text-gray-300">-</span>
                                            @endif
                                        @else
                                            <span class="text-gray-300">-</span>
                                        @endif
                                    </td>
                                @endforeach
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-center font-bold {{ $statusLulus === true ? 'text-green-600' : ($statusLulus === false ? 'text-red-600' : 'text-gray-500') }}">
                                    {{ $countNilai > 0 ? number_format($totalNilaiAktual, 1) : '-' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-center text-purple-600">
                                    {{ $countNilai > 0 && count($nilaiAcuan) > 0 ? number_format($totalNilaiAcuanMhs, 1) : '-' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-center">
                                    @if($countNilai == 0)
                                        <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Belum Dinilai</span>
                                    @elseif($statusLulus === null)
                                        <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Menunggu Nilai Acuan</span>
                                    @elseif($statusLulus)
                                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">LULUS</span>
                                    @else
                                        <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">TIDAK LULUS</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 7 + ($stasi->count() * 2) }}" class="px-4 py-3 text-center text-gray-500">Belum ada peserta.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Statistik --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold mb-4">Statistik</h3>
            @php
                $allNilai = App\Models\Nilai::where('jadwal_id', $jadwal->id)->get();
                $avgAktual = $allNilai->avg('nilai_aktual') ?? $allNilai->avg('total_nilai') ?? 0;
                $lulusCount = 0;
                $tidakLulusCount = 0;
                $pendingCount = 0;
                
                foreach($peserta as $mhs) {
                    $nilaiMhs = $mhs->nilai->where('jadwal_id', $jadwal->id);
                    if ($nilaiMhs->count() > 0 && count($nilaiAcuan) > 0) {
                        $totalAktual = 0;
                        $totalAcuan = 0;
                        foreach($stasi as $s) {
                            $n = $nilaiMhs->where('stasi_id', $s->id)->first();
                            if ($n && isset($nilaiAcuan[$s->id])) {
                                $totalAktual += $n->nilai_aktual ?? $n->total_nilai;
                                $totalAcuan += $nilaiAcuan[$s->id];
                            }
                        }
                        if ($totalAktual >= $totalAcuan) {
                            $lulusCount++;
                        } else {
                            $tidakLulusCount++;
                        }
                    } elseif ($nilaiMhs->count() > 0) {
                        $pendingCount++;
                    }
                }
            @endphp
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div class="bg-indigo-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-indigo-600">{{ $peserta->count() }}</div>
                    <div class="text-sm text-gray-600">Total Peserta</div>
                </div>
                <div class="bg-green-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-green-600">{{ $lulusCount }}</div>
                    <div class="text-sm text-gray-600">Lulus</div>
                </div>
                <div class="bg-red-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-red-600">{{ $tidakLulusCount }}</div>
                    <div class="text-sm text-gray-600">Tidak Lulus</div>
                </div>
                <div class="bg-yellow-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-yellow-600">{{ $pendingCount }}</div>
                    <div class="text-sm text-gray-600">Menunggu Nilai Acuan</div>
                </div>
                <div class="bg-blue-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-blue-600">{{ number_format($avgAktual, 1) }}</div>
                    <div class="text-sm text-gray-600">Rata-rata Nilai Aktual</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Panduan Kelulusan --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold mb-4">Panduan Kelulusan (BAB VII)</h3>
            <div class="prose prose-sm max-w-none">
                <ul class="text-gray-600 space-y-2">
                    <li><strong>Nilai Aktual per Stasi:</strong> Σ(skor × bobot) - Contoh: skor 3, bobot 2 = 6</li>
                    <li><strong>Global Rating:</strong> Penilaian subjektif penguji (1=Tidak Lulus, 2=Borderline, 3=Lulus, 4=Superior)</li>
                    <li><strong>Nilai Acuan:</strong> Standard setting berbasis regresi linear antara Nilai Aktual dan Global Rating</li>
                    <li><strong>Kriteria Kelulusan:</strong> Total Nilai Aktual semua stasi ≥ Total Nilai Acuan semua stasi</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
