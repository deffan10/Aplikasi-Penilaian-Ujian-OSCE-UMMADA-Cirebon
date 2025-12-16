@extends('layouts.app')

@section('title', 'Edit Kelas')

@section('content')
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
        <h2 class="text-xl font-semibold mb-6">Edit Kelas: {{ $kelas->kode }}</h2>

        <form action="{{ route('admin.kelas.update', $kelas) }}" method="POST" class="max-w-xl">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label for="kode" class="block text-sm font-medium text-gray-700 mb-1">Kode Kelas</label>
                <input type="text" name="kode" id="kode" value="{{ old('kode', $kelas->kode) }}" required
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('kode')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="mb-4">
                <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap (opsional)</label>
                <input type="text" name="nama" id="nama" value="{{ old('nama', $kelas->nama) }}"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('nama')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label for="tahun_akademik" class="block text-sm font-medium text-gray-700 mb-1">Tahun Akademik</label>
                    @php
                        $tahunSekarang = date('Y');
                        $tahunOptions = [];
                        for ($i = -2; $i <= 2; $i++) {
                            $t1 = $tahunSekarang + $i;
                            $t2 = $t1 + 1;
                            $tahunOptions[] = "$t1/$t2";
                        }
                    @endphp
                    <select name="tahun_akademik" id="tahun_akademik"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">-- Pilih --</option>
                        @foreach($tahunOptions as $tahun)
                            <option value="{{ $tahun }}" {{ old('tahun_akademik', $kelas->tahun_akademik) == $tahun ? 'selected' : '' }}>{{ $tahun }}</option>
                        @endforeach
                    </select>
                    @error('tahun_akademik')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="semester" class="block text-sm font-medium text-gray-700 mb-1">Semester</label>
                    <select name="semester" id="semester"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">-- Pilih --</option>
                        <option value="ganjil" {{ old('semester', $kelas->semester) == 'ganjil' ? 'selected' : '' }}>Ganjil</option>
                        <option value="genap" {{ old('semester', $kelas->semester) == 'genap' ? 'selected' : '' }}>Genap</option>
                    </select>
                    @error('semester')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Update</button>
                <a href="{{ route('admin.kelas.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
