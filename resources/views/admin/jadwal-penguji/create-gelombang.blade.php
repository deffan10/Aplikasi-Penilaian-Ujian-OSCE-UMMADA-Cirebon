@extends('layouts.app')

@section('title', 'Tambah Gelombang')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <nav class="text-sm text-gray-500 mb-2">
                <a href="{{ route('admin.jadwal-penguji.index') }}" class="hover:text-indigo-600">Jadwal Penguji</a>
                <span class="mx-2">→</span>
                <a href="{{ route('admin.jadwal-penguji.gelombang', $jadwal) }}" class="hover:text-indigo-600">{{ $jadwal->nama }}</a>
                <span class="mx-2">→</span>
                <span class="text-gray-900">Tambah Gelombang</span>
            </nav>
            <h2 class="text-xl font-semibold">Tambah Gelombang Baru</h2>
            <p class="text-gray-500 mt-1">{{ $jadwal->nama }} | {{ $jadwal->mulai->format('d F Y') }}</p>
        </div>
    </div>

    {{-- Form --}}
    <form action="{{ route('admin.jadwal-penguji.store-gelombang', $jadwal) }}" method="POST">
        @csrf
        
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 space-y-4">
                <div>
                    <label for="nama" class="block text-sm font-medium text-gray-700">Nama Gelombang</label>
                    <input type="text" name="nama" id="nama" required
                           value="{{ old('nama', 'Gelombang ' . ($lastUrutan + 1)) }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('nama')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="waktu_mulai" class="block text-sm font-medium text-gray-700">Waktu Mulai</label>
                        <input type="time" name="waktu_mulai" id="waktu_mulai"
                               value="{{ old('waktu_mulai') }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('waktu_mulai')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="waktu_selesai" class="block text-sm font-medium text-gray-700">Waktu Selesai</label>
                        <input type="time" name="waktu_selesai" id="waktu_selesai"
                               value="{{ old('waktu_selesai') }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('waktu_selesai')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="urutan" class="block text-sm font-medium text-gray-700">Urutan</label>
                    <input type="number" name="urutan" id="urutan" required min="1"
                           value="{{ old('urutan', $lastUrutan + 1) }}"
                           class="mt-1 block w-32 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('urutan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-4 flex justify-between">
                    <a href="{{ route('admin.jadwal-penguji.gelombang', $jadwal) }}" 
                       class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300">
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
