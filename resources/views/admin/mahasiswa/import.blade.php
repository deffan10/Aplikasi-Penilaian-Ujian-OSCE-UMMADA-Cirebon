@extends('layouts.app')

@section('title', 'Import Mahasiswa')

@section('content')
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
        <h2 class="text-xl font-semibold mb-6">Import Mahasiswa dari Excel</h2>

        <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mb-6">
            <h3 class="font-medium text-blue-800 mb-2">Format File Excel:</h3>
            <p class="text-sm text-blue-700">File Excel harus memiliki header: <strong>nim</strong>, <strong>nama</strong></p>
            <p class="text-sm text-blue-700 mt-1">Contoh:</p>
            <table class="text-sm mt-2 border border-blue-300">
                <thead>
                    <tr class="bg-blue-100">
                        <th class="border border-blue-300 px-3 py-1">nim</th>
                        <th class="border border-blue-300 px-3 py-1">nama</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="border border-blue-300 px-3 py-1">123456</td>
                        <td class="border border-blue-300 px-3 py-1">Ahmad Farhan</td>
                    </tr>
                    <tr>
                        <td class="border border-blue-300 px-3 py-1">123457</td>
                        <td class="border border-blue-300 px-3 py-1">Siti Aisyah</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <form action="{{ route('admin.mahasiswa.import') }}" method="POST" enctype="multipart/form-data" class="max-w-xl">
            @csrf
            <div class="mb-4">
                <label for="kelas_id" class="block text-sm font-medium text-gray-700 mb-1">Kelas Tujuan</label>
                <select name="kelas_id" id="kelas_id" required
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($kelasList as $k)
                        <option value="{{ $k->id }}">{{ $k->kode }} - {{ $k->nama }}</option>
                    @endforeach
                </select>
                @error('kelas_id')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="mb-6">
                <label for="file" class="block text-sm font-medium text-gray-700 mb-1">File Excel</label>
                <input type="file" name="file" id="file" accept=".xlsx,.xls,.csv" required
                    class="w-full border border-gray-300 rounded-md shadow-sm p-2">
                @error('file')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">Import</button>
                <a href="{{ route('admin.mahasiswa.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
