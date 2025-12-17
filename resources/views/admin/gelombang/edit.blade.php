@extends('layouts.app')

@section('title', 'Edit Gelombang - ' . $gelombang->nama)

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex justify-between items-start">
                <div>
                    <h2 class="text-xl font-semibold">Edit Gelombang</h2>
                    <p class="text-gray-500 mt-1">Jadwal: {{ $jadwal->nama }}</p>
                </div>
                <a href="{{ route('admin.jadwal.gelombang.index', $jadwal) }}" 
                   class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300">
                    Kembali
                </a>
            </div>
        </div>
    </div>

    {{-- Form --}}
    <form action="{{ route('admin.jadwal.gelombang.update', [$jadwal, $gelombang]) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 space-y-6">
                {{-- Info Dasar --}}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">Nama Gelombang *</label>
                        <input type="text" name="nama" id="nama" value="{{ old('nama', $gelombang->nama) }}" required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('nama')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="urutan" class="block text-sm font-medium text-gray-700 mb-1">Urutan *</label>
                        <input type="number" name="urutan" id="urutan" value="{{ old('urutan', $gelombang->urutan) }}" min="1" required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="waktu_mulai" class="block text-sm font-medium text-gray-700 mb-1">Waktu Mulai</label>
                        <input type="time" name="waktu_mulai" id="waktu_mulai" 
                            value="{{ old('waktu_mulai', $gelombang->waktu_mulai ? \Carbon\Carbon::parse($gelombang->waktu_mulai)->format('H:i') : '') }}"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="waktu_selesai" class="block text-sm font-medium text-gray-700 mb-1">Waktu Selesai</label>
                        <input type="time" name="waktu_selesai" id="waktu_selesai" 
                            value="{{ old('waktu_selesai', $gelombang->waktu_selesai ? \Carbon\Carbon::parse($gelombang->waktu_selesai)->format('H:i') : '') }}"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>

                {{-- Penguji per Stasi --}}
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Penguji per Stasi</h3>
                    <p class="text-sm text-gray-500 mb-4">Pilih 1 penguji untuk setiap stasi di gelombang ini.</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($stasiList as $stasi)
                            <div class="border rounded-lg p-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ $stasi->nama }}</label>
                                <select name="penguji[{{ $stasi->id }}]" 
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    <option value="">-- Pilih Penguji --</option>
                                    @foreach($pengujiList as $penguji)
                                        <option value="{{ $penguji->id }}" 
                                            {{ old('penguji.' . $stasi->id, $currentPenguji[$stasi->id] ?? '') == $penguji->id ? 'selected' : '' }}>
                                            {{ $penguji->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Mahasiswa --}}
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Mahasiswa Peserta</h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Pilih mahasiswa yang akan mengikuti ujian di gelombang ini.
                        Saat ini: {{ count($currentMahasiswa) }} mahasiswa terpilih.
                    </p>
                    
                    @if($availableMahasiswa->count() > 0)
                        <div class="mb-4 flex gap-2">
                            <button type="button" onclick="selectAll()" class="text-sm text-indigo-600 hover:text-indigo-800">Pilih Semua</button>
                            <span class="text-gray-300">|</span>
                            <button type="button" onclick="deselectAll()" class="text-sm text-indigo-600 hover:text-indigo-800">Batal Pilih</button>
                        </div>
                        
                        <div class="max-h-64 overflow-y-auto border rounded-lg p-3">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                                @foreach($availableMahasiswa as $mhs)
                                    <label class="flex items-center space-x-2 text-sm cursor-pointer hover:bg-gray-50 p-1 rounded">
                                        <input type="checkbox" name="mahasiswa[]" value="{{ $mhs->id }}" 
                                            class="mahasiswa-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                            {{ in_array($mhs->id, old('mahasiswa', $currentMahasiswa)) ? 'checked' : '' }}>
                                        <span>{{ $mhs->nim }} - {{ $mhs->nama }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <p class="text-yellow-800 text-sm">
                                Tidak ada mahasiswa tersedia untuk dipilih.
                            </p>
                        </div>
                    @endif
                </div>

                {{-- Submit --}}
                <div class="flex justify-end gap-2 pt-4 border-t">
                    <a href="{{ route('admin.jadwal.gelombang.index', $jadwal) }}" 
                       class="bg-gray-200 text-gray-700 px-6 py-2 rounded-md hover:bg-gray-300">
                        Batal
                    </a>
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700">
                        Update Gelombang
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function selectAll() {
    document.querySelectorAll('.mahasiswa-checkbox').forEach(cb => cb.checked = true);
}
function deselectAll() {
    document.querySelectorAll('.mahasiswa-checkbox').forEach(cb => cb.checked = false);
}
</script>
@endpush
