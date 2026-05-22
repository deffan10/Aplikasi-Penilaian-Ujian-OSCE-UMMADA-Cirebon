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

            {{-- Label Header Section --}}
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b">Header Label Print</h3>
                <p class="text-sm text-gray-500 mb-4">Pengaturan kop/header yang muncul di setiap label saat print (penguji/mahasiswa). Bisa berisi nama institusi, fakultas, program studi, dll.</p>
                
                {{-- Logo Label --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Logo Label (opsional)</label>
                    @if($setting->label_logo_path)
                        <div class="mb-2 p-2 bg-gray-50 rounded inline-block">
                            <img src="{{ asset('storage/' . $setting->label_logo_path) }}" alt="Logo Label" class="h-10">
                        </div>
                    @endif
                    <input type="file" name="label_logo" accept="image/png,image/jpeg"
                        class="w-full border border-gray-300 rounded-md shadow-sm p-2 focus:border-indigo-500 focus:ring-indigo-500">
                    <p class="text-xs text-gray-500 mt-1">Logo kecil yang tampil di kiri header label. Ukuran disarankan 80x80px. Maks 2MB.</p>
                    @error('label_logo') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-4">
                    <div>
                        <label for="label_header_line1" class="block text-sm font-medium text-gray-700 mb-1">Baris 1 (Nama Institusi)</label>
                        <input type="text" name="label_header_line1" id="label_header_line1" 
                            value="{{ old('label_header_line1', $setting->label_header_line1 ?? '') }}"
                            placeholder="Contoh: UNIVERSITAS MUHAMMADIYAH MAJALENGKA"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('label_header_line1') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="label_header_line2" class="block text-sm font-medium text-gray-700 mb-1">Baris 2 (Fakultas/Program)</label>
                        <input type="text" name="label_header_line2" id="label_header_line2" 
                            value="{{ old('label_header_line2', $setting->label_header_line2 ?? '') }}"
                            placeholder="Contoh: FAKULTAS FARMASI"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('label_header_line2') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="label_header_line3" class="block text-sm font-medium text-gray-700 mb-1">Baris 3 (Keterangan tambahan)</label>
                        <input type="text" name="label_header_line3" id="label_header_line3" 
                            value="{{ old('label_header_line3', $setting->label_header_line3 ?? '') }}"
                            placeholder="Contoh: UJIAN OSCE FARMASI 2026"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('label_header_line3') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Preview --}}
                <div class="mt-4 p-3 bg-gray-50 border rounded-lg">
                    <p class="text-xs text-gray-500 mb-2">Preview header label:</p>
                    <div class="text-center border border-dashed border-gray-300 p-2 bg-white rounded">
                        <div style="font-size: 9px; font-weight: bold; text-transform: uppercase;">
                            {{ $setting->label_header_line1 ?? 'NAMA INSTITUSI' }}
                        </div>
                        @if($setting->label_header_line2)
                            <div style="font-size: 8px; font-weight: bold;">
                                {{ $setting->label_header_line2 }}
                            </div>
                        @endif
                        @if($setting->label_header_line3)
                            <div style="font-size: 8px;">
                                {{ $setting->label_header_line3 }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Kartu Peserta Kop Section --}}
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b">Kop Kartu Peserta</h3>
                <p class="text-sm text-gray-500 mb-4">Pengaturan kop/header yang muncul di setiap kartu peserta saat cetak. Logo kiri dan kanan bisa berbeda.</p>
                
                {{-- Logo Kiri --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Logo Kiri</label>
                    @if($setting->kartu_logo_kiri_path)
                        <div class="mb-2 p-2 bg-gray-50 rounded inline-block">
                            <img src="{{ asset('storage/' . $setting->kartu_logo_kiri_path) }}" alt="Logo Kiri" class="h-10">
                        </div>
                    @endif
                    <input type="file" name="kartu_logo_kiri" accept="image/png,image/jpeg"
                        class="w-full border border-gray-300 rounded-md shadow-sm p-2 focus:border-indigo-500 focus:ring-indigo-500">
                    @error('kartu_logo_kiri') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Logo Kanan --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Logo Kanan</label>
                    @if($setting->kartu_logo_kanan_path)
                        <div class="mb-2 p-2 bg-gray-50 rounded inline-block">
                            <img src="{{ asset('storage/' . $setting->kartu_logo_kanan_path) }}" alt="Logo Kanan" class="h-10">
                        </div>
                    @endif
                    <input type="file" name="kartu_logo_kanan" accept="image/png,image/jpeg"
                        class="w-full border border-gray-300 rounded-md shadow-sm p-2 focus:border-indigo-500 focus:ring-indigo-500">
                    @error('kartu_logo_kanan') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-4">
                    <div>
                        <label for="kartu_kop_line1" class="block text-sm font-medium text-gray-700 mb-1">Baris 1</label>
                        <input type="text" name="kartu_kop_line1" id="kartu_kop_line1" 
                            value="{{ old('kartu_kop_line1', $setting->kartu_kop_line1 ?? '') }}"
                            placeholder="Contoh: Kartu Peserta"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('kartu_kop_line1') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="kartu_kop_line2" class="block text-sm font-medium text-gray-700 mb-1">Baris 2</label>
                        <input type="text" name="kartu_kop_line2" id="kartu_kop_line2" 
                            value="{{ old('kartu_kop_line2', $setting->kartu_kop_line2 ?? '') }}"
                            placeholder="Contoh: OSCE Farmasi"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('kartu_kop_line2') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="kartu_kop_line3" class="block text-sm font-medium text-gray-700 mb-1">Baris 3</label>
                        <input type="text" name="kartu_kop_line3" id="kartu_kop_line3" 
                            value="{{ old('kartu_kop_line3', $setting->kartu_kop_line3 ?? '') }}"
                            placeholder="Contoh: Mahasiswa Farmasi Vokasi D3"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('kartu_kop_line3') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
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
