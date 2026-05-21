@extends('layouts.app')

@section('title', 'Tambah Gelombang - ' . $jadwal->nama)

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex justify-between items-start">
                <div>
                    <h2 class="text-xl font-semibold">Tambah Gelombang Baru</h2>
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
    <form action="{{ route('admin.jadwal.gelombang.store', $jadwal) }}" method="POST">
        @csrf
        
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 space-y-6">
                {{-- Info Dasar --}}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">Nama Gelombang *</label>
                        <input type="text" name="nama" id="nama" value="{{ old('nama', 'Gelombang ' . $nextUrutan) }}" required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('nama')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="urutan" class="block text-sm font-medium text-gray-700 mb-1">Urutan *</label>
                        <input type="number" name="urutan" id="urutan" value="{{ old('urutan', $nextUrutan) }}" min="1" required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="waktu_mulai" class="block text-sm font-medium text-gray-700 mb-1">Waktu Mulai</label>
                        <input type="time" name="waktu_mulai" id="waktu_mulai" value="{{ old('waktu_mulai') }}"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="waktu_selesai" class="block text-sm font-medium text-gray-700 mb-1">Waktu Selesai</label>
                        <input type="time" name="waktu_selesai" id="waktu_selesai" value="{{ old('waktu_selesai') }}"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>

                {{-- Submit --}}
                <div class="flex justify-end gap-2 pt-4 border-t">
                    <a href="{{ route('admin.jadwal.gelombang.index', $jadwal) }}" 
                       class="bg-gray-200 text-gray-700 px-6 py-2 rounded-md hover:bg-gray-300">
                        Batal
                    </a>
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700">
                        Simpan Gelombang
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection


