@extends('layouts.app')

@section('title', 'Detail Jadwal')

@section('content')
<div class="space-y-6">
    {{-- Info Jadwal --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h2 class="text-xl font-semibold">{{ $jadwal->nama }}</h2>
                    <p class="text-gray-500 mt-1">
                        Waktu: {{ $jadwal->mulai->format('d F Y H:i') }} - {{ $jadwal->selesai->format('d F Y H:i') }} |
                        Status: 
                        @if($jadwal->isActive())
                            <span class="text-green-600 font-medium">Aktif</span>
                        @elseif($jadwal->mulai > now())
                            <span class="text-yellow-600 font-medium">Akan Datang</span>
                        @else
                            <span class="text-gray-600">Selesai</span>
                        @endif
                    </p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('admin.jadwal.edit', $jadwal) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                        Edit Jadwal
                    </a>
                    <a href="{{ route('admin.jadwal.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300">
                        Kembali
                    </a>
                </div>
            </div>

            {{-- Stats --}}
            @php
                $totalMahasiswa = 0;
                $totalPenguji = 0;
                foreach($jadwal->gelombang as $gel) {
                    $totalMahasiswa += $gel->mahasiswa_count ?? 0;
                    $totalPenguji += $gel->penguji_stasi_count ?? 0;
                }
            @endphp
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-purple-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-purple-600">{{ $jadwal->gelombang->count() }}</div>
                    <div class="text-sm text-gray-600">Gelombang</div>
                </div>
                <div class="bg-indigo-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-indigo-600">{{ $totalMahasiswa }}</div>
                    <div class="text-sm text-gray-600">Total Mahasiswa</div>
                </div>
                <div class="bg-green-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-green-600">{{ $totalPenguji }}</div>
                    <div class="text-sm text-gray-600">Total Penugasan Penguji</div>
                </div>
                <div class="bg-blue-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-blue-600">{{ App\Models\Stasi::where('aktif', true)->count() }}</div>
                    <div class="text-sm text-gray-600">Jumlah Stasi</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Daftar Gelombang --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Daftar Gelombang</h3>
                <a href="{{ route('admin.jadwal-penguji.gelombang', $jadwal) }}" class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700 text-sm">
                    + Kelola Gelombang & Penguji
                </a>
            </div>
            
            @if($jadwal->gelombang->isEmpty())
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    <p class="mt-2 text-gray-500">Belum ada gelombang untuk jadwal ini</p>
                    <a href="{{ route('admin.jadwal-penguji.gelombang', $jadwal) }}" class="mt-4 inline-block text-purple-600 hover:text-purple-800">
                        Tambah Gelombang Pertama →
                    </a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Gelombang</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mahasiswa</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Penguji</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($jadwal->gelombang as $gelombang)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-gray-900">{{ $gelombang->nama }}</div>
                                        <div class="text-xs text-gray-500">Urutan: {{ $gelombang->urutan }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($gelombang->waktu_mulai && $gelombang->waktu_selesai)
                                            {{ \Carbon\Carbon::parse($gelombang->waktu_mulai)->format('H:i') }} - 
                                            {{ \Carbon\Carbon::parse($gelombang->waktu_selesai)->format('H:i') }}
                                        @else
                                            <span class="text-gray-400">Belum diatur</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $gelombang->mahasiswa_count > 0 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-500' }}">
                                            {{ $gelombang->mahasiswa_count ?? 0 }} mahasiswa
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php $stasiCount = App\Models\Stasi::where('aktif', true)->count(); @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ ($gelombang->penguji_stasi_count ?? 0) == $stasiCount ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800' }}">
                                            {{ $gelombang->penguji_stasi_count ?? 0 }}/{{ $stasiCount }} stasi
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <a href="{{ route('admin.jadwal-penguji.assign', [$jadwal, $gelombang]) }}" class="text-indigo-600 hover:text-indigo-900">
                                            Atur Penguji
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- Rekap Per Stasi --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold mb-4">Rekap Per Stasi</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach(App\Models\Stasi::where('aktif', true)->get() as $stasi)
                    @php
                        $nilaiStasi = App\Models\Nilai::where('jadwal_id', $jadwal->id)
                            ->where('stasi_id', $stasi->id)->get();
                        $dinilai = $nilaiStasi->count();
                        $avgNilai = $dinilai > 0 ? $nilaiStasi->avg('nilai_aktual') : null;
                    @endphp
                    <div class="border rounded-lg p-4">
                        <h4 class="font-medium text-gray-900">{{ $stasi->nama }}</h4>
                        <p class="text-sm text-gray-500 mt-1">{{ $stasi->deskripsi ?? '-' }}</p>
                        <div class="mt-3 flex justify-between text-sm">
                            <span class="text-gray-600">Dinilai: {{ $dinilai }}/{{ $totalMahasiswa }}</span>
                            <span class="font-medium text-gray-600">
                                Rata-rata: {{ $avgNilai !== null ? number_format($avgNilai, 1) : '-' }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
