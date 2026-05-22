@extends('layouts.app')

@section('title', 'Upload Foto Mahasiswa')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-semibold">Upload Foto Mahasiswa</h2>
                    <p class="text-sm text-gray-500 mt-1">Upload file ZIP berisi foto mahasiswa secara massal</p>
                </div>
                <a href="{{ route('admin.mahasiswa.index') }}" class="text-gray-500 hover:text-gray-700">
                    ← Kembali
                </a>
            </div>

            {{-- Instructions --}}
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <h4 class="font-medium text-blue-800 mb-2">📋 Petunjuk Upload:</h4>
                <ol class="list-decimal list-inside text-sm text-blue-700 space-y-1">
                    <li>Siapkan folder berisi foto-foto mahasiswa</li>
                    <li>Rename setiap file foto sesuai <strong>NIM</strong> mahasiswa (contoh: <code>2201001.jpg</code>)</li>
                    <li>Compress folder menjadi file <strong>ZIP</strong></li>
                    <li>Upload file ZIP di form ini</li>
                </ol>
            </div>

            {{-- Format Info --}}
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <h4 class="font-medium text-yellow-800 mb-2">📝 Ketentuan File:</h4>
                <ul class="list-disc list-inside text-sm text-yellow-700 space-y-1">
                    <li>Format foto: <strong>JPG, JPEG, atau PNG</strong></li>
                    <li>Nama file = NIM (tanpa spasi), contoh: <code>2201001.jpg</code>, <code>2201002.png</code></li>
                    <li>Ukuran ZIP maksimal <strong>50MB</strong></li>
                    <li>Foto disarankan ukuran pas foto (3x4 atau 4x6)</li>
                    <li>Jika NIM tidak ditemukan, foto akan di-skip</li>
                </ul>
            </div>

            {{-- Upload Form --}}
            <form action="{{ route('admin.mahasiswa.upload-foto') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Upload File ZIP</label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-indigo-400 transition-colors">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" 
                                      stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="file" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500">
                                    <span>Pilih file ZIP</span>
                                    <input id="file" name="file" type="file" accept=".zip" class="sr-only" required>
                                </label>
                                <p class="pl-1">atau drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">ZIP berisi foto JPG/PNG (maks 50MB)</p>
                        </div>
                    </div>
                    <p id="file-name" class="mt-2 text-sm text-gray-600 hidden"></p>
                    @error('file')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('admin.mahasiswa.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Batal</a>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                        Upload Foto
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('file').addEventListener('change', function(e) {
    const fileName = e.target.files[0]?.name;
    const fileNameEl = document.getElementById('file-name');
    if (fileName) {
        fileNameEl.textContent = '📄 File dipilih: ' + fileName;
        fileNameEl.classList.remove('hidden');
    } else {
        fileNameEl.classList.add('hidden');
    }
});
</script>
@endsection
