@extends('layouts.app')

@section('title', ($canNilai ? 'Penilaian: ' : 'Lihat Nilai: ') . $stasi->nama)

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex justify-between items-start">
                <div>
                    <div class="flex items-center gap-3">
                        <h2 class="text-xl font-semibold">{{ $stasi->nama }}</h2>
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
                    <p class="text-gray-500 mt-1">{{ $stasi->deskripsi ?? 'Tidak ada deskripsi' }}</p>
                </div>
                <a href="{{ route('penguji.stasi.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300">
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
            <span class="text-yellow-800">Anda tidak ditugaskan di stasi ini. Anda hanya dapat melihat nilai yang sudah diinput penguji lain.</span>
        </div>
    </div>
    @endif

    {{-- Komponen Penilaian --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold mb-4">Komponen Penilaian</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($stasi->komponens as $komponen)
                    <div class="border rounded-lg p-3">
                        <div class="font-medium text-gray-900">{{ $komponen->nama }}</div>
                        <div class="text-sm text-gray-500">Bobot: {{ $komponen->bobot }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Pilih Jadwal --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold mb-4">Pilih Jadwal Ujian</h3>
            
            @if($jadwalAktif->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($jadwalAktif as $jadwal)
                        <a href="{{ route('penguji.penilaian.list', ['stasi' => $stasi, 'jadwal' => $jadwal]) }}" 
                           class="block border rounded-lg p-4 hover:bg-indigo-50 hover:border-indigo-300 transition">
                            <h4 class="font-medium text-gray-900">{{ $jadwal->nama }}</h4>
                            <p class="text-sm text-gray-500 mt-1">{{ $jadwal->mulai->format('d F Y H:i') }}</p>
                            <p class="text-sm text-indigo-600 mt-2">{{ $jadwal->peserta->count() }} peserta →</p>
                        </a>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500">Tidak ada jadwal ujian aktif saat ini.</p>
            @endif
        </div>
    </div>
</div>
@endsection
