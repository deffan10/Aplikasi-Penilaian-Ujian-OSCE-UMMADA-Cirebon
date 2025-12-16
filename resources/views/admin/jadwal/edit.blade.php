@extends('layouts.app')

@section('title', 'Edit Jadwal')

@section('content')
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
        <h2 class="text-xl font-semibold mb-6">Edit Jadwal: {{ $jadwal->nama }}</h2>

        <form action="{{ route('admin.jadwal.update', $jadwal) }}" method="POST" class="max-w-xl">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">Nama Jadwal</label>
                <input type="text" name="nama" id="nama" value="{{ old('nama', $jadwal->nama) }}" required
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('nama') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label for="mulai" class="block text-sm font-medium text-gray-700 mb-1">Waktu Mulai</label>
                <input type="datetime-local" name="mulai" id="mulai" value="{{ old('mulai', $jadwal->mulai->format('Y-m-d\TH:i')) }}" required
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('mulai') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label for="selesai" class="block text-sm font-medium text-gray-700 mb-1">Waktu Selesai</label>
                <input type="datetime-local" name="selesai" id="selesai" value="{{ old('selesai', $jadwal->selesai->format('Y-m-d\TH:i')) }}" required
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('selesai') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
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
                            <option value="{{ $tahun }}" {{ old('tahun_akademik', $jadwal->tahun_akademik) == $tahun ? 'selected' : '' }}>{{ $tahun }}</option>
                        @endforeach
                    </select>
                    @error('tahun_akademik') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="semester" class="block text-sm font-medium text-gray-700 mb-1">Semester</label>
                    <select name="semester" id="semester"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">-- Pilih --</option>
                        <option value="ganjil" {{ old('semester', $jadwal->semester) == 'ganjil' ? 'selected' : '' }}>Ganjil</option>
                        <option value="genap" {{ old('semester', $jadwal->semester) == 'genap' ? 'selected' : '' }}>Genap</option>
                    </select>
                    @error('semester') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mb-4">
                <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1">Keterangan (Opsional)</label>
                <textarea name="keterangan" id="keterangan" rows="2"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('keterangan', $jadwal->keterangan) }}</textarea>
                @error('keterangan') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Peserta</label>
                <div class="space-y-2 max-h-64 overflow-y-auto border border-gray-200 rounded-md p-3">
                    @forelse($allMahasiswa as $mhs)
                        <label class="flex items-center">
                            <input type="checkbox" name="mahasiswa_ids[]" value="{{ $mhs->id }}"
                                {{ in_array($mhs->id, old('mahasiswa_ids', $pesertaIds)) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-700">{{ $mhs->nim }} - {{ $mhs->nama }} ({{ $mhs->kelas->nama ?? '-' }})</span>
                        </label>
                    @empty
                        <p class="text-gray-500 text-sm">Belum ada mahasiswa.</p>
                    @endforelse
                </div>
                @error('mahasiswa_ids') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-4">
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                    Update
                </button>
                <a href="{{ route('admin.jadwal.index') }}" class="text-gray-600 hover:text-gray-900">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
