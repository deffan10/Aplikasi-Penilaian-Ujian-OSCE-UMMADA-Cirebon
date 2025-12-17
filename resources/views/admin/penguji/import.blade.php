@extends('layouts.app')

@section('title', 'Import Penguji')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-semibold">Import Data Penguji</h2>
                    <p class="text-sm text-gray-500 mt-1">Upload file CSV untuk import data penguji secara massal</p>
                </div>
                <a href="{{ route('admin.penguji.index') }}" class="text-gray-500 hover:text-gray-700">
                    ← Kembali
                </a>
            </div>

            {{-- Instructions --}}
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <h4 class="font-medium text-blue-800 mb-2">📋 Petunjuk Import:</h4>
                <ol class="list-decimal list-inside text-sm text-blue-700 space-y-1">
                    <li>Download template CSV terlebih dahulu</li>
                    <li>Isi data penguji sesuai format (Nama, Username, Password)</li>
                    <li>Simpan file dalam format CSV</li>
                    <li>Upload file untuk import</li>
                </ol>
            </div>

            {{-- Download Template --}}
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="font-medium text-gray-800">Template Import</h4>
                        <p class="text-sm text-gray-500">Download template CSV sebagai panduan format data</p>
                    </div>
                    <a href="{{ route('admin.penguji.download-template') }}" 
                       class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Download Template
                    </a>
                </div>
            </div>

            {{-- Format Info --}}
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <h4 class="font-medium text-yellow-800 mb-2">📝 Format Kolom:</h4>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b border-yellow-200">
                                <th class="text-left py-2 text-yellow-800">Kolom</th>
                                <th class="text-left py-2 text-yellow-800">Keterangan</th>
                                <th class="text-left py-2 text-yellow-800">Contoh</th>
                            </tr>
                        </thead>
                        <tbody class="text-yellow-700">
                            <tr class="border-b border-yellow-100">
                                <td class="py-2 font-medium">Nama</td>
                                <td class="py-2">Nama lengkap penguji</td>
                                <td class="py-2">Dr. Budi Santoso</td>
                            </tr>
                            <tr class="border-b border-yellow-100">
                                <td class="py-2 font-medium">Username</td>
                                <td class="py-2">Username untuk login (huruf, angka, titik, dash, underscore)</td>
                                <td class="py-2">budi.santoso</td>
                            </tr>
                            <tr>
                                <td class="py-2 font-medium">Password</td>
                                <td class="py-2">Password minimal 6 karakter</td>
                                <td class="py-2">password123</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Upload Form --}}
            <form action="{{ route('admin.penguji.import') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Upload File CSV
                    </label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-indigo-400 transition-colors">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" 
                                      stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="file" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500">
                                    <span>Pilih file</span>
                                    <input id="file" name="file" type="file" accept=".csv,.txt" class="sr-only" required>
                                </label>
                                <p class="pl-1">atau drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">CSV hingga 2MB</p>
                        </div>
                    </div>
                    <p id="file-name" class="mt-2 text-sm text-gray-600 hidden"></p>
                    @error('file')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('admin.penguji.index') }}" 
                       class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Batal
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                        <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        Import Data
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
