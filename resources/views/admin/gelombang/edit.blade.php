@extends('layouts.app')

@section('title', 'Edit Gelombang - ' . $gelombang->nama)

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex justify-between items-start">
                <div>
                    <h2 class="text-xl font-semibold">Edit Gelombang</h2>
                    <p class="text-gray-500 mt-1">Jadwal: {{ $jadwal->nama }}</p>
                </div>
                <a href="{{ route('admin.jadwal.gelombang.index', $jadwal) }}" 
                   class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300">
                    Kembali
                </a>
            </div>
        </div>
    </div>

    {{-- Form --}}
    <form action="{{ route('admin.jadwal.gelombang.update', [$jadwal, $gelombang]) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 space-y-6">
                {{-- Info Dasar --}}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">Nama Gelombang *</label>
                        <input type="text" name="nama" id="nama" value="{{ old('nama', $gelombang->nama) }}" required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('nama')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="urutan" class="block text-sm font-medium text-gray-700 mb-1">Urutan *</label>
                        <input type="number" name="urutan" id="urutan" value="{{ old('urutan', $gelombang->urutan) }}" min="1" required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="waktu_mulai" class="block text-sm font-medium text-gray-700 mb-1">Waktu Mulai</label>
                        <input type="time" name="waktu_mulai" id="waktu_mulai" 
                            value="{{ old('waktu_mulai', $gelombang->waktu_mulai ? \Carbon\Carbon::parse($gelombang->waktu_mulai)->format('H:i') : '') }}"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="waktu_selesai" class="block text-sm font-medium text-gray-700 mb-1">Waktu Selesai</label>
                        <input type="time" name="waktu_selesai" id="waktu_selesai" 
                            value="{{ old('waktu_selesai', $gelombang->waktu_selesai ? \Carbon\Carbon::parse($gelombang->waktu_selesai)->format('H:i') : '') }}"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>

                {{-- Info Penguji (Read-Only) --}}
                <div class="border-t pt-6">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="text-lg font-medium text-gray-900">Penguji per Stasi</h3>
                        <a href="{{ route('admin.jadwal-penguji.assign', [$jadwal, $gelombang]) }}" 
                           class="text-sm bg-indigo-100 text-indigo-700 px-3 py-1.5 rounded-md hover:bg-indigo-200 transition">
                            ✏️ Kelola Penguji
                        </a>
                    </div>
                    
                    @if($pengujiPerStasi->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                            @foreach($pengujiPerStasi as $stasiId => $assignments)
                                <div class="border rounded-lg p-3 bg-gray-50">
                                    <div class="text-sm font-medium text-gray-700 mb-1">{{ $assignments->first()->stasi->nama }}</div>
                                    @foreach($assignments as $assignment)
                                        <span class="inline-block bg-indigo-100 text-indigo-800 text-xs px-2 py-1 rounded mr-1 mb-1">
                                            {{ $assignment->penguji->name }}
                                        </span>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                            <p class="text-yellow-700 text-sm">Belum ada penguji yang di-assign.</p>
                        </div>
                    @endif
                </div>

                {{-- Info Mahasiswa (Read-Only) --}}
                <div class="border-t pt-6">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="text-lg font-medium text-gray-900">Mahasiswa Peserta</h3>
                        <a href="{{ route('admin.jadwal-penguji.assign-mahasiswa', [$jadwal, $gelombang]) }}" 
                           class="text-sm bg-indigo-100 text-indigo-700 px-3 py-1.5 rounded-md hover:bg-indigo-200 transition">
                            ✏️ Kelola Mahasiswa
                        </a>
                    </div>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                        <p class="text-blue-800 text-sm font-medium">
                            {{ $mahasiswaCount }} mahasiswa terdaftar di gelombang ini
                        </p>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="flex justify-end gap-2 pt-4 border-t">
                    <a href="{{ route('admin.jadwal.gelombang.index', $jadwal) }}" 
                       class="bg-gray-200 text-gray-700 px-6 py-2 rounded-md hover:bg-gray-300">
                        Batal
                    </a>
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700">
                        Update Gelombang
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
