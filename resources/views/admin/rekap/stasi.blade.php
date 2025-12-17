@extends('layouts.app')

@section('title', 'Rekap Per Stasi')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex justify-between items-start">
                <div>
                    <h2 class="text-xl font-semibold">Rekap Per Stasi</h2>
                    <p class="text-gray-500 mt-1">Lihat rekap nilai mahasiswa per stasi untuk jadwal tertentu</p>
                </div>
                <a href="{{ route('admin.rekap.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300">
                    Kembali
                </a>
            </div>
        </div>
    </div>

    {{-- Filter Form --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <form method="GET" action="{{ route('admin.rekap.stasi') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="jadwal_id" class="block text-sm font-medium text-gray-700 mb-1">Pilih Jadwal</label>
                    <select name="jadwal_id" id="jadwal_id" required
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">-- Pilih Jadwal --</option>
                        @foreach($jadwalList as $j)
                            <option value="{{ $j->id }}" {{ request('jadwal_id') == $j->id ? 'selected' : '' }}>
                                {{ $j->nama }} - {{ $j->mulai->format('d M Y') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="stasi_id" class="block text-sm font-medium text-gray-700 mb-1">Pilih Stasi</label>
                    <select name="stasi_id" id="stasi_id" required
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">-- Pilih Stasi --</option>
                        @foreach($stasiList as $s)
                            <option value="{{ $s->id }}" {{ request('stasi_id') == $s->id ? 'selected' : '' }}>
                                {{ $s->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                        Tampilkan
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if($jadwal && $stasi)
        {{-- Info Header --}}
        <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="font-semibold text-indigo-800">{{ $stasi->nama }}</h3>
                    <p class="text-sm text-indigo-600">
                        Jadwal: {{ $jadwal->nama }} | {{ $jadwal->mulai->format('d F Y') }} |
                        Total: {{ $nilaiList->count() }} penilaian
                    </p>
                </div>
                @if($nilaiAcuan)
                    <div class="text-center">
                        <div class="text-sm text-gray-500">Nilai Acuan</div>
                        <div class="text-2xl font-bold text-purple-600">{{ number_format($nilaiAcuan, 2) }}</div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Tabel Rekap --}}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Gelombang</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIM</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kelas</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Nilai Aktual</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Global Rating</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Penguji</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($nilaiList as $idx => $nilai)
                                @php
                                    $nilaiAktual = $nilai->nilai_aktual ?? $nilai->total_nilai;
                                    $lulus = $nilaiAcuan ? $nilaiAktual >= $nilaiAcuan : $nilaiAktual >= 70;
                                @endphp
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $idx + 1 }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm">
                                        @if($nilai->gelombang)
                                            <span class="px-2 py-1 text-xs rounded bg-indigo-100 text-indigo-800">
                                                {{ $nilai->gelombang->nama }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $nilai->mahasiswa->nim ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                        {{ $nilai->mahasiswa->nama ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        {{ $nilai->mahasiswa->kelas->nama ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-center font-bold {{ $lulus ? 'text-green-600' : 'text-red-600' }}">
                                        {{ number_format($nilaiAktual, 1) }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-center">
                                        @if($nilai->globalRating)
                                            <span class="px-2 py-1 text-xs rounded-full 
                                                {{ $nilai->globalRating->nilai == 1 ? 'bg-red-100 text-red-800' : '' }}
                                                {{ $nilai->globalRating->nilai == 2 ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $nilai->globalRating->nilai == 3 ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $nilai->globalRating->nilai == 4 ? 'bg-blue-100 text-blue-800' : '' }}">
                                                {{ $nilai->globalRating->label }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                                        {{ $nilai->penguji->name ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-center">
                                        @if($lulus)
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">LULUS</span>
                                        @else
                                            <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">TIDAK LULUS</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-4 py-3 text-center text-gray-500">
                                        Belum ada data penilaian untuk stasi ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Statistik --}}
        @if($nilaiList->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Statistik Stasi</h3>
                    @php
                        $totalNilai = $nilaiList->count();
                        $avgNilai = $nilaiList->avg(fn($n) => $n->nilai_aktual ?? $n->total_nilai);
                        $lulusCount = $nilaiList->filter(function($n) use ($nilaiAcuan) {
                            $val = $n->nilai_aktual ?? $n->total_nilai;
                            return $nilaiAcuan ? $val >= $nilaiAcuan : $val >= 70;
                        })->count();
                        $tidakLulusCount = $totalNilai - $lulusCount;
                        
                        // Group by gelombang
                        $perGelombang = $nilaiList->groupBy(fn($n) => $n->gelombang->nama ?? 'Tanpa Gelombang');
                    @endphp
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <div class="bg-indigo-50 rounded-lg p-4">
                            <div class="text-2xl font-bold text-indigo-600">{{ $totalNilai }}</div>
                            <div class="text-sm text-gray-600">Total Penilaian</div>
                        </div>
                        <div class="bg-blue-50 rounded-lg p-4">
                            <div class="text-2xl font-bold text-blue-600">{{ number_format($avgNilai, 1) }}</div>
                            <div class="text-sm text-gray-600">Rata-rata Nilai</div>
                        </div>
                        <div class="bg-green-50 rounded-lg p-4">
                            <div class="text-2xl font-bold text-green-600">{{ $lulusCount }}</div>
                            <div class="text-sm text-gray-600">Lulus ({{ $totalNilai > 0 ? round($lulusCount/$totalNilai*100) : 0 }}%)</div>
                        </div>
                        <div class="bg-red-50 rounded-lg p-4">
                            <div class="text-2xl font-bold text-red-600">{{ $tidakLulusCount }}</div>
                            <div class="text-sm text-gray-600">Tidak Lulus ({{ $totalNilai > 0 ? round($tidakLulusCount/$totalNilai*100) : 0 }}%)</div>
                        </div>
                    </div>

                    {{-- Per Gelombang --}}
                    @if($perGelombang->count() > 1)
                        <h4 class="font-medium mb-3">Distribusi per Gelombang</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            @foreach($perGelombang as $gelName => $nilaiGel)
                                @php
                                    $avgGel = $nilaiGel->avg(fn($n) => $n->nilai_aktual ?? $n->total_nilai);
                                    $pengujiGel = $nilaiGel->first()->penguji->name ?? '-';
                                @endphp
                                <div class="border rounded-lg p-3">
                                    <div class="font-medium text-gray-800">{{ $gelName }}</div>
                                    <div class="text-sm text-gray-500">
                                        {{ $nilaiGel->count() }} mahasiswa | 
                                        Rata-rata: {{ number_format($avgGel, 1) }} |
                                        Penguji: {{ $pengujiGel }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endif
    @else
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
            <p class="text-yellow-800">Pilih jadwal dan stasi untuk melihat rekap.</p>
        </div>
    @endif
</div>
@endsection
