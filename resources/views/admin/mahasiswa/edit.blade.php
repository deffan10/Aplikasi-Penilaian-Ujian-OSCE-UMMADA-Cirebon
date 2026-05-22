@extends('layouts.app')

@section('title', 'Edit Mahasiswa')

@section('content')
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
        <h2 class="text-xl font-semibold mb-6">Edit Mahasiswa: {{ $mahasiswa->nama }}</h2>

        <form action="{{ route('admin.mahasiswa.update', $mahasiswa) }}" method="POST" enctype="multipart/form-data" class="max-w-xl">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label for="nim" class="block text-sm font-medium text-gray-700 mb-1">NIM</label>
                <input type="text" name="nim" id="nim" value="{{ old('nim', $mahasiswa->nim) }}" required
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('nim')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="mb-4">
                <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                <input type="text" name="nama" id="nama" value="{{ old('nama', $mahasiswa->nama) }}" required
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('nama')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="mb-4">
                <label for="kelas_id" class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
                <select name="kelas_id" id="kelas_id" required
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @foreach($kelasList as $k)
                        <option value="{{ $k->id }}" {{ old('kelas_id', $mahasiswa->kelas_id) == $k->id ? 'selected' : '' }}>{{ $k->kode }} - {{ $k->nama }}</option>
                    @endforeach
                </select>
                @error('kelas_id')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Foto --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Foto</label>
                @if($mahasiswa->foto)
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $mahasiswa->foto) }}" alt="Foto {{ $mahasiswa->nama }}" 
                            class="w-24 h-32 object-cover rounded border">
                    </div>
                @endif
                <input type="file" name="foto" accept="image/jpeg,image/png"
                    class="w-full border border-gray-300 rounded-md shadow-sm p-2 focus:border-indigo-500 focus:ring-indigo-500">
                <p class="text-xs text-gray-500 mt-1">Format: JPG/PNG, maks 2MB. Kosongkan jika tidak ingin mengubah.</p>
                @error('foto')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="flex gap-2">
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Update</button>
                <a href="{{ route('admin.mahasiswa.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
