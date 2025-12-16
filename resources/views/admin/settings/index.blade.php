@extends('layouts.app')

@section('title', 'Pengaturan Kop Surat')

@section('content')
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
        <h2 class="text-xl font-semibold mb-6">Pengaturan Dokumen</h2>

        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="max-w-xl">
            @csrf

            {{-- Kop Surat Section --}}
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b">Kop Surat</h3>
                
                @if($setting && $setting->kop_surat_path)
                    <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-600 mb-2">Kop surat saat ini:</p>
                        <img src="{{ asset('storage/' . $setting->kop_surat_path) }}" alt="Kop Surat" class="max-w-full h-auto border rounded shadow-sm">
                    </div>
                @else
                    <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <p class="text-sm text-yellow-700">
                            <svg class="inline w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            Belum ada kop surat. Silakan upload gambar kop surat.
                        </p>
                    </div>
                @endif

                <input type="file" name="kop_surat" id="kop_surat" accept="image/png,image/jpeg"
                    class="w-full border border-gray-300 rounded-md shadow-sm p-2 focus:border-indigo-500 focus:ring-indigo-500">
                
                <div class="mt-2 text-sm text-gray-500">
                    <p><strong>Petunjuk:</strong></p>
                    <ul class="list-disc list-inside ml-2 mt-1 space-y-1">
                        <li>Upload gambar kop surat lengkap dalam satu file PNG/JPG</li>
                        <li>Gambar sebaiknya sudah berisi logo, nama institusi, alamat, dll</li>
                        <li>Lebar gambar disarankan sekitar 800px untuk hasil optimal</li>
                        <li>Ukuran file maksimal 4MB</li>
                    </ul>
                </div>
                
                @error('kop_surat') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Penandatangan Section --}}
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b">Penandatangan Dokumen</h3>
                <p class="text-sm text-gray-500 mb-4">Data penandatangan yang akan ditampilkan pada dokumen PDF rekap nilai.</p>
                
                <div class="space-y-4">
                    <div>
                        <label for="penandatangan_jabatan" class="block text-sm font-medium text-gray-700 mb-1">Jabatan</label>
                        <input type="text" name="penandatangan_jabatan" id="penandatangan_jabatan" 
                            value="{{ old('penandatangan_jabatan', $setting->penandatangan_jabatan ?? '') }}"
                            placeholder="Contoh: Koordinator OSCE / Dekan / Ketua Program Studi"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('penandatangan_jabatan') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="penandatangan_nama" class="block text-sm font-medium text-gray-700 mb-1">Nama Penandatangan</label>
                        <input type="text" name="penandatangan_nama" id="penandatangan_nama" 
                            value="{{ old('penandatangan_nama', $setting->penandatangan_nama ?? '') }}"
                            placeholder="Contoh: Dr. Ahmad Farhan, M.Farm., Apt."
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('penandatangan_nama') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="penandatangan_nik" class="block text-sm font-medium text-gray-700 mb-1">NIK / NIP / NIDN</label>
                        <input type="text" name="penandatangan_nik" id="penandatangan_nik" 
                            value="{{ old('penandatangan_nik', $setting->penandatangan_nik ?? '') }}"
                            placeholder="Contoh: 198501012010011001"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('penandatangan_nik') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                    <svg class="inline w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
