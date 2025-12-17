@extends('layouts.app')

@section('title', 'Buat Jadwal')

@section('content')
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
        <h2 class="text-xl font-semibold mb-6">Buat Jadwal Ujian OSCE</h2>

        {{-- Info Box --}}
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex">
                <svg class="h-5 w-5 text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="text-sm text-blue-700">
                    <p class="font-medium">Alur Pembuatan Jadwal OSCE:</p>
                    <ol class="mt-1 ml-4 list-decimal space-y-1">
                        <li><strong>Buat Jadwal</strong> - Tentukan nama dan waktu pelaksanaan</li>
                        <li><strong>Buat Gelombang</strong> - Bagi jadwal menjadi beberapa gelombang/sesi</li>
                        <li><strong>Assign Mahasiswa</strong> - Tentukan mahasiswa peserta per gelombang</li>
                        <li><strong>Assign Penguji</strong> - Tentukan penguji per stasi untuk setiap gelombang</li>
                    </ol>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.jadwal.store') }}" method="POST" class="max-w-2xl">
            @csrf
            <div class="space-y-4">
                <div>
                    <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">Nama Jadwal</label>
                    <input type="text" name="nama" id="nama" value="{{ old('nama') }}" required
                        placeholder="Contoh: OSCE Semester Genap 2024/2025"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('nama') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="mulai" class="block text-sm font-medium text-gray-700 mb-1">Waktu Mulai</label>
                        <input type="datetime-local" name="mulai" id="mulai" value="{{ old('mulai') }}" required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('mulai') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="selesai" class="block text-sm font-medium text-gray-700 mb-1">Waktu Selesai</label>
                        <input type="datetime-local" name="selesai" id="selesai" value="{{ old('selesai') }}" required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('selesai') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="tahun_akademik" class="block text-sm font-medium text-gray-700 mb-1">Tahun Akademik</label>
                        @php
                            $tahunSekarang = date('Y');
                            $tahunOptions = [];
                            for ($i = -1; $i <= 2; $i++) {
                                $t1 = $tahunSekarang + $i;
                                $t2 = $t1 + 1;
                                $tahunOptions[] = "$t1/$t2";
                            }
                        @endphp
                        <select name="tahun_akademik" id="tahun_akademik"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">-- Pilih --</option>
                            @foreach($tahunOptions as $tahun)
                                <option value="{{ $tahun }}" {{ old('tahun_akademik') == $tahun ? 'selected' : '' }}>{{ $tahun }}</option>
                            @endforeach
                        </select>
                        @error('tahun_akademik') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="semester" class="block text-sm font-medium text-gray-700 mb-1">Semester</label>
                        <select name="semester" id="semester"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">-- Pilih --</option>
                            <option value="ganjil" {{ old('semester') == 'ganjil' ? 'selected' : '' }}>Ganjil</option>
                            <option value="genap" {{ old('semester') == 'genap' ? 'selected' : '' }}>Genap</option>
                        </select>
                        @error('semester') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1">Keterangan (Opsional)</label>
                    <textarea name="keterangan" id="keterangan" rows="2"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('keterangan') }}</textarea>
                    @error('keterangan') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex items-center gap-4 mt-6 pt-6 border-t">
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                    Simpan & Lanjutkan
                </button>
                <a href="{{ route('admin.jadwal.index') }}" class="text-gray-600 hover:text-gray-900">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
