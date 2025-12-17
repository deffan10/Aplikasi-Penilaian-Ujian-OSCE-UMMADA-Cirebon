@extends('layouts.app')

@section('title', 'Daftar Stasi')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h2 class="text-xl font-semibold">Daftar Stasi OSCE</h2>
            <p class="text-gray-500 mt-1">Anda dapat melihat semua stasi. Stasi yang ditugaskan bisa Anda nilai.</p>
        </div>
    </div>

    {{-- Stasi List --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($stasi as $s)
            @php
                $isAssigned = in_array($s->id, $assignedStasiIds);
                $gelombangAssignments = $stasiGelombang[$s->id] ?? collect();
            @endphp
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg {{ $isAssigned ? 'ring-2 ring-indigo-500' : '' }}">
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-lg font-semibold {{ $isAssigned ? 'text-indigo-600' : 'text-gray-600' }}">
                                {{ $s->nama }}
                            </h3>
                            <p class="text-sm text-gray-500 mt-1">{{ $s->deskripsi ?? 'Tidak ada deskripsi' }}</p>
                        </div>
                        @if($isAssigned)
                            <span class="px-2 py-1 text-xs font-medium bg-indigo-100 text-indigo-800 rounded-full">
                                Ditugaskan
                            </span>
                        @else
                            <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-600 rounded-full">
                                Lihat Saja
                            </span>
                        @endif
                    </div>
                    
                    <div class="mt-4 flex items-center text-sm text-gray-500">
                        <span>{{ $s->komponens_count }} komponen</span>
                    </div>

                    @if($isAssigned && $gelombangAssignments->isNotEmpty())
                        {{-- Show gelombang assignments --}}
                        <div class="mt-4 space-y-2">
                            <p class="text-xs text-gray-500 font-medium">Gelombang ditugaskan:</p>
                            @foreach($gelombangAssignments as $assignment)
                                <a href="{{ route('penguji.penilaian.list', [$assignment->gelombang, $s]) }}" 
                                   class="block px-3 py-2 bg-indigo-50 text-indigo-700 text-sm rounded-md hover:bg-indigo-100 transition">
                                    <div class="flex items-center justify-between">
                                        <span>{{ $assignment->gelombang->jadwal->nama }}</span>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </div>
                                    <span class="text-xs text-indigo-500">{{ $assignment->gelombang->nama }}</span>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="mt-4">
                            @if($isAssigned)
                                <p class="text-sm text-orange-600">Belum ada jadwal aktif</p>
                            @else
                                {{-- Tombol lihat detail stasi --}}
                                <a href="{{ route('penguji.stasi.show', $s) }}" 
                                   class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Lihat Detail
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
