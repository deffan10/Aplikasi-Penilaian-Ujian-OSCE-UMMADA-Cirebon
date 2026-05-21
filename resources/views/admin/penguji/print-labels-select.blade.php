@extends('layouts.app')

@section('title', 'Print Label Penguji - Pilih Jadwal')

@section('content')
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-xl font-semibold">Print Label Penguji</h2>
                <p class="text-sm text-gray-500 mt-1">Pilih jadwal ujian untuk mencetak label penguji yang terdaftar</p>
            </div>
            <a href="{{ route('admin.penguji.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300">
                ← Kembali
            </a>
        </div>

        @if($jadwalList->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($jadwalList as $jadwal)
                    <a href="{{ route('admin.penguji.print-labels', ['jadwal_id' => $jadwal->id]) }}" 
                       target="_blank"
                       class="block border border-gray-200 rounded-lg p-4 hover:border-indigo-400 hover:bg-indigo-50 transition-colors">
                        <h3 class="font-semibold text-gray-900">{{ $jadwal->nama }}</h3>
                        <div class="text-sm text-gray-500 mt-2 space-y-1">
                            <p>
                                <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                {{ $jadwal->mulai ? $jadwal->mulai->format('d M Y') : '-' }}
                            </p>
                            @if($jadwal->tahun_akademik)
                                <p class="text-xs text-gray-400">{{ $jadwal->label_tahun_akademik }}</p>
                            @endif
                        </div>
                        <div class="mt-3 flex items-center text-xs text-indigo-600 font-medium">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                            Cetak Label
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <p class="text-yellow-800 text-sm">Belum ada jadwal aktif. Buat jadwal terlebih dahulu.</p>
            </div>
        @endif
    </div>
</div>
@endsection
