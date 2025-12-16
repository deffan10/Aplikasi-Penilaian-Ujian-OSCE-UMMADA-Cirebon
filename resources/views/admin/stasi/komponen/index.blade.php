@extends('layouts.app')

@section('title', 'Komponen Stasi')

@section('content')
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
    <div class="p-6">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-semibold">Komponen Penilaian: {{ $stasi->nama }}</h2>
                <p class="text-sm text-gray-500 mt-1">Total Bobot: {{ $totalBobot }}</p>
                @if($totalBobot != 100)

                @endif
            </div>
            <a href="{{ route('admin.stasi.index') }}" class="text-indigo-600 hover:underline">
                ← Kembali ke Daftar Stasi
            </a>
        </div>
    </div>
</div>

<!-- Add New Komponen -->
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
    <div class="p-6">
        <h3 class="text-lg font-medium mb-4">Tambah Komponen Baru</h3>
        <form action="{{ route('admin.stasi.komponen.store', $stasi) }}" method="POST" class="flex flex-wrap gap-4 items-end">
            @csrf
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Komponen</label>
                <input type="text" name="nama" required
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="Contoh: Komunikasi dengan Pasien">
            </div>
            <div class="w-24">
                <label class="block text-sm font-medium text-gray-700 mb-1">Bobot</label>
                <input type="number" name="bobot" min="0" max="100" required
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="20">
            </div>
            <div class="w-20">
                <label class="block text-sm font-medium text-gray-700 mb-1">Urutan</label>
                <input type="number" name="urutan" min="0"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="1">
            </div>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                Tambah
            </button>
        </form>
    </div>
</div>

<!-- Komponen List -->
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
        <h3 class="text-lg font-medium mb-4">Daftar Komponen</h3>
        
        @if($komponen->count() > 0)
            <div class="space-y-4">
                @foreach($komponen as $k)
                    <div class="border rounded-lg p-4">
                        <form action="{{ route('admin.stasi.komponen.update', [$stasi, $k]) }}" method="POST" class="flex flex-wrap gap-4 items-end">
                            @csrf
                            @method('PUT')
                            <div class="flex-1 min-w-[200px]">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Komponen</label>
                                <input type="text" name="nama" value="{{ $k->nama }}" required
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div class="w-24">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Bobot</label>
                                <input type="number" name="bobot" value="{{ $k->bobot }}" min="0" max="100" required
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div class="w-20">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Urutan</label>
                                <input type="number" name="urutan" value="{{ $k->urutan }}" min="0"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <button type="submit" class="bg-green-600 text-white px-3 py-2 rounded-md hover:bg-green-700 text-sm">
                                Update
                            </button>
                        </form>
                        <form action="{{ route('admin.stasi.komponen.destroy', [$stasi, $k]) }}" method="POST" class="mt-2" onsubmit="return confirm('Yakin hapus komponen ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm">
                                Hapus Komponen
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500">Belum ada komponen. Silakan tambahkan komponen penilaian di atas.</p>
        @endif
    </div>
</div>
@endsection
