@extends('layouts.app')

@section('title', 'Detail Stasi - ' . $stasi->nama)

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex items-start justify-between">
                <div>
                    <nav class="text-sm text-gray-500 mb-2">
                        <a href="{{ route('penguji.stasi.index') }}" class="hover:text-indigo-600">Daftar Stasi</a>
                        <span class="mx-2">›</span>
                        <span>{{ $stasi->nama }}</span>
                    </nav>
                    <h2 class="text-xl font-semibold text-gray-800">{{ $stasi->nama }}</h2>
                    <p class="text-gray-500 mt-1">{{ $stasi->deskripsi ?? 'Tidak ada deskripsi' }}</p>
                </div>
                <a href="{{ route('penguji.stasi.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300">
                    ← Kembali
                </a>
            </div>
        </div>
    </div>

    {{-- Info Box --}}
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-blue-500 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <h4 class="font-medium text-blue-800">Mode Lihat Saja</h4>
                <p class="text-sm text-blue-600 mt-1">Anda tidak ditugaskan di stasi ini. Halaman ini hanya menampilkan informasi komponen penilaian.</p>
            </div>
        </div>
    </div>

    {{-- Komponen List --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold mb-4">Komponen Penilaian ({{ $komponens->count() }} item)</h3>
            
            @if($komponens->isEmpty())
                <div class="text-center py-8 text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <p class="mt-2">Belum ada komponen penilaian untuk stasi ini.</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($komponens as $index => $komponen)
                        <div class="border border-gray-200 rounded-lg p-4 hover:border-gray-300 transition">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3">
                                        <span class="flex-shrink-0 w-8 h-8 bg-gray-100 text-gray-600 rounded-full flex items-center justify-center text-sm font-medium">
                                            {{ $komponen->urutan ?? ($index + 1) }}
                                        </span>
                                        <div>
                                            <h4 class="font-medium text-gray-800">{{ $komponen->nama }}</h4>
                                            @if($komponen->deskripsi)
                                                <p class="text-sm text-gray-500 mt-1">{{ $komponen->deskripsi }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                        Bobot: {{ $komponen->bobot }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Summary --}}
                <div class="mt-6 pt-4 border-t border-gray-200">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Total Komponen:</span>
                        <span class="font-medium">{{ $komponens->count() }}</span>
                    </div>
                    <div class="flex justify-between text-sm mt-1">
                        <span class="text-gray-600">Total Bobot:</span>
                        <span class="font-medium">{{ $komponens->sum('bobot') }}</span>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Skala Penilaian --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold mb-4">Skala Penilaian</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-center">
                    <div class="text-2xl font-bold text-red-600">0</div>
                    <div class="text-sm text-red-700">Tidak Dilakukan</div>
                </div>
                <div class="bg-orange-50 border border-orange-200 rounded-lg p-3 text-center">
                    <div class="text-2xl font-bold text-orange-600">1</div>
                    <div class="text-sm text-orange-700">Dilakukan Tidak Sempurna</div>
                </div>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 text-center">
                    <div class="text-2xl font-bold text-yellow-600">2</div>
                    <div class="text-sm text-yellow-700">Dilakukan dengan Ragu</div>
                </div>
                <div class="bg-green-50 border border-green-200 rounded-lg p-3 text-center">
                    <div class="text-2xl font-bold text-green-600">3</div>
                    <div class="text-sm text-green-700">Dilakukan Sempurna</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
