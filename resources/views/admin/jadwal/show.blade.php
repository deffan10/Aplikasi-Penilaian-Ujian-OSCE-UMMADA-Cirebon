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
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-indigo-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-indigo-600">{{ $jadwal->peserta->count() }}</div>
                    <div class="text-sm text-gray-600">Total Peserta</div>
                </div>
                <div class="bg-green-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-green-600">{{ $sudahDinilai ?? 0 }}</div>
                    <div class="text-sm text-gray-600">Sudah Dinilai</div>
                </div>
                <div class="bg-yellow-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-yellow-600">{{ $jadwal->peserta->count() - ($sudahDinilai ?? 0) }}</div>
                    <div class="text-sm text-gray-600">Belum Dinilai</div>
                </div>
                <div class="bg-blue-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-blue-600">{{ App\Models\Stasi::where('aktif', true)->count() }}</div>
                    <div class="text-sm text-gray-600">Jumlah Stasi</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Daftar Peserta --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold mb-4">Daftar Peserta</h3>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIM</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Progress Stasi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rata-rata</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($jadwal->peserta as $idx => $mhs)
                            @php
                                $nilaiMhs = App\Models\Nilai::where('jadwal_id', $jadwal->id)
                                    ->where('mahasiswa_id', $mhs->id)->get();
                                $nilaiCount = $nilaiMhs->count();
                                $stasiCount = App\Models\Stasi::where('aktif', true)->count();
                                $avgNilai = $nilaiCount > 0 ? $nilaiMhs->avg('total_nilai') : null;
                            @endphp
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $idx + 1 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $mhs->nim }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $mhs->nama }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <div class="flex items-center">
                                        <div class="w-24 bg-gray-200 rounded-full h-2.5 mr-2">
                                            <div class="bg-indigo-600 h-2.5 rounded-full" style="width: {{ $stasiCount > 0 ? ($nilaiCount / $stasiCount * 100) : 0 }}%"></div>
                                        </div>
                                        <span class="text-gray-600">{{ $nilaiCount }}/{{ $stasiCount }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($avgNilai !== null)
                                        <span class="font-medium {{ $avgNilai >= 70 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ number_format($avgNilai, 1) }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">Belum ada peserta.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
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
                        $avgNilai = $dinilai > 0 ? $nilaiStasi->avg('total_nilai') : null;
                    @endphp
                    <div class="border rounded-lg p-4">
                        <h4 class="font-medium text-gray-900">{{ $stasi->nama }}</h4>
                        <p class="text-sm text-gray-500 mt-1">{{ $stasi->deskripsi ?? '-' }}</p>
                        <div class="mt-3 flex justify-between text-sm">
                            <span class="text-gray-600">Dinilai: {{ $dinilai }}/{{ $jadwal->peserta->count() }}</span>
                            <span class="font-medium {{ ($avgNilai ?? 0) >= 70 ? 'text-green-600' : 'text-gray-600' }}">
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
