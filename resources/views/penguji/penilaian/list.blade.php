@extends('layouts.app')

@section('title', 'Daftar Peserta - ' . $stasi->nama)

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex justify-between items-start">
                <div>
                    <div class="flex items-center gap-3">
                        <h2 class="text-xl font-semibold">{{ $jadwal->nama }}</h2>
                        @if($canNilai)
                            <span class="px-2 py-1 text-xs font-medium bg-indigo-100 text-indigo-800 rounded-full">
                                Dapat Menilai
                            </span>
                        @else
                            <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">
                                Hanya Lihat
                            </span>
                        @endif
                    </div>
                    <p class="text-gray-500 mt-1">
                        Stasi: {{ $stasi->nama }} | 
                        Waktu: {{ $jadwal->mulai->format('d F Y H:i') }} - {{ $jadwal->selesai->format('H:i') }}
                    </p>
                </div>
                <a href="{{ route('penguji.penilaian.stasi', $stasi) }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300">
                    ← Kembali
                </a>
            </div>
        </div>
    </div>

    @if(!$canNilai)
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="text-yellow-800">Mode hanya lihat. Anda tidak ditugaskan di stasi ini.</span>
        </div>
    </div>
    @endif

    {{-- Progress (only if can nilai) --}}
    @if($canNilai)
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            @php
                $sudahDinilai = count($existingNilai);
            @endphp
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-700">Progress Penilaian Anda</span>
                <span class="text-sm text-gray-500">{{ $sudahDinilai }} / {{ $peserta->count() }}</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2.5">
                <div class="bg-indigo-600 h-2.5 rounded-full" style="width: {{ $peserta->count() > 0 ? ($sudahDinilai / $peserta->count() * 100) : 0 }}%"></div>
            </div>
        </div>
    </div>
    @endif

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
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($peserta as $idx => $mhs)
                            @php
                                $sudahNilai = in_array($mhs->id, $existingNilai);
                                $adaNilaiDariPengujiLain = in_array($mhs->id, $existingNilaiAll ?? []);
                            @endphp
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $idx + 1 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $mhs->nim }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $mhs->nama }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                    @if($canNilai)
                                        @if($sudahNilai)
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Sudah Anda Nilai</span>
                                        @else
                                            <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Belum Dinilai</span>
                                        @endif
                                    @else
                                        @if($adaNilaiDariPengujiLain)
                                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Sudah Dinilai</span>
                                        @else
                                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600">Belum Dinilai</span>
                                        @endif
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                    @if($canNilai)
                                        <a href="{{ route('penguji.penilaian.form', ['stasi' => $stasi, 'jadwal' => $jadwal, 'mahasiswa' => $mhs]) }}" 
                                           class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md {{ $sudahNilai ? 'text-indigo-700 bg-indigo-100 hover:bg-indigo-200' : 'text-white bg-indigo-600 hover:bg-indigo-700' }}">
                                            {{ $sudahNilai ? 'Edit Nilai' : 'Nilai' }}
                                        </a>
                                    @else
                                        <a href="{{ route('penguji.penilaian.form', ['stasi' => $stasi, 'jadwal' => $jadwal, 'mahasiswa' => $mhs]) }}" 
                                           class="inline-flex items-center px-3 py-1 text-sm font-medium rounded-md text-gray-700 bg-gray-100 hover:bg-gray-200">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            Lihat
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada peserta.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
