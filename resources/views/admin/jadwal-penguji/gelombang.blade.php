@extends('layouts.app')

@section('title', 'Gelombang - ' . $jadwal->nama)

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex justify-between items-start">
                <div>
                    <nav class="text-sm text-gray-500 mb-2">
                        <a href="{{ route('admin.jadwal-penguji.index') }}" class="hover:text-indigo-600">Jadwal Penguji</a>
                        <span class="mx-2">→</span>
                        <span class="text-gray-900">{{ $jadwal->nama }}</span>
                    </nav>
                    <h2 class="text-xl font-semibold">Daftar Gelombang</h2>
                    <p class="text-gray-500 mt-1">{{ $jadwal->mulai->format('d F Y') }} | {{ $jadwal->mulai->format('H:i') }} - {{ $jadwal->selesai->format('H:i') }}</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('admin.jadwal-penguji.create-gelombang', $jadwal) }}" 
                       class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                        + Tambah Gelombang
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Info Stasi --}}
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h4 class="font-medium text-blue-800 mb-2">Stasi yang tersedia ({{ $stasiList->count() }} stasi):</h4>
        <div class="flex flex-wrap gap-2">
            @foreach($stasiList as $stasi)
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    {{ $stasi->nama }}
                </span>
            @endforeach
        </div>
    </div>

    {{-- Copy Assignment Form --}}
    @if($gelombangList->count() > 1)
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <h4 class="font-medium text-yellow-800 mb-2">Copy Assignment Penguji</h4>
        <form action="{{ route('admin.jadwal-penguji.copy-assignment', $jadwal) }}" method="POST" class="flex flex-wrap items-end gap-4">
            @csrf
            <div>
                <label class="block text-sm text-yellow-700 mb-1">Dari Gelombang:</label>
                <select name="source_gelombang_id" required class="border-yellow-300 rounded-md text-sm focus:border-yellow-500 focus:ring-yellow-500">
                    <option value="">Pilih...</option>
                    @foreach($gelombangList as $gel)
                        <option value="{{ $gel->id }}">{{ $gel->nama }} ({{ $gel->penguji_stasi_count }} penguji)</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm text-yellow-700 mb-1">Ke Gelombang:</label>
                <select name="target_gelombang_id" required class="border-yellow-300 rounded-md text-sm focus:border-yellow-500 focus:ring-yellow-500">
                    <option value="">Pilih...</option>
                    @foreach($gelombangList as $gel)
                        <option value="{{ $gel->id }}">{{ $gel->nama }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" style="background-color: #ca8a04; color: white;" class="px-4 py-2 rounded-md hover:opacity-90 text-sm font-medium">
                Copy Assignment
            </button>
        </form>
    </div>
    @endif

    {{-- Daftar Gelombang --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            @if($gelombangList->isEmpty())
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    <p class="mt-2 text-gray-500">Belum ada gelombang untuk jadwal ini</p>
                    <a href="{{ route('admin.jadwal-penguji.create-gelombang', $jadwal) }}" 
                       class="mt-4 inline-block text-indigo-600 hover:text-indigo-800">
                        Tambah Gelombang Pertama →
                    </a>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($gelombangList as $gelombang)
                        <div class="border border-gray-200 rounded-lg p-4 hover:border-indigo-300 transition-colors">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="font-semibold text-lg">{{ $gelombang->nama }}</h3>
                                    <p class="text-sm text-gray-500">
                                        @if($gelombang->waktu_mulai && $gelombang->waktu_selesai)
                                            {{ \Carbon\Carbon::parse($gelombang->waktu_mulai)->format('H:i') }} - 
                                            {{ \Carbon\Carbon::parse($gelombang->waktu_selesai)->format('H:i') }}
                                        @else
                                            Waktu belum diatur
                                        @endif
                                        | Urutan: {{ $gelombang->urutan }}
                                    </p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ ($gelombang->mahasiswa_count ?? 0) > 0 ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $gelombang->mahasiswa_count ?? 0 }} Mahasiswa
                                    </span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ $gelombang->penguji_stasi_count == $stasiList->count() ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800' }}">
                                        {{ $gelombang->penguji_stasi_count }}/{{ $stasiList->count() }} Penguji
                                    </span>
                                </div>
                            </div>
                            
                            {{-- Preview Penguji --}}
                            @php
                                $assignments = $gelombang->pengujiStasi()->with(['stasi', 'penguji'])->get();
                            @endphp
                            @if($assignments->count() > 0)
                                <div class="mt-3 flex flex-wrap gap-2">
                                    @foreach($assignments as $assign)
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs bg-gray-100">
                                            <span class="font-medium text-gray-700">{{ $assign->stasi->nama }}:</span>
                                            <span class="ml-1 text-gray-600">{{ $assign->penguji->name }}</span>
                                        </span>
                                    @endforeach
                                </div>
                            @endif

                            <div class="mt-4 flex flex-wrap gap-2">
                                <a href="{{ route('admin.jadwal.gelombang.edit', [$jadwal, $gelombang]) }}" 
                                   style="background-color: #059669; color: white;"
                                   class="inline-flex items-center px-3 py-1.5 text-sm rounded hover:opacity-90">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Edit Gelombang
                                </a>
                                <a href="{{ route('admin.jadwal-penguji.assign-mahasiswa', [$jadwal, $gelombang]) }}" 
                                   style="background-color: #2563eb; color: white;"
                                   class="inline-flex items-center px-3 py-1.5 text-sm rounded hover:opacity-90">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                    Atur Mahasiswa
                                </a>
                                <a href="{{ route('admin.jadwal-penguji.assign', [$jadwal, $gelombang]) }}" 
                                   style="background-color: #4f46e5; color: white;"
                                   class="inline-flex items-center px-3 py-1.5 text-sm rounded hover:opacity-90">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    Atur Penguji
                                </a>
                                <form action="{{ route('admin.jadwal-penguji.destroy-gelombang', [$jadwal, $gelombang]) }}" 
                                      method="POST" 
                                      onsubmit="return confirm('Yakin hapus gelombang ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-100 text-red-700 text-sm rounded hover:bg-red-200">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
