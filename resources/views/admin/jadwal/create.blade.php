@extends('layouts.app')

@section('title', 'Buat Jadwal')

@section('content')
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
        <h2 class="text-xl font-semibold mb-6">Buat Jadwal Ujian</h2>

        <form action="{{ route('admin.jadwal.store') }}" method="POST" class="max-w-4xl" id="jadwalForm">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div>
                        <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">Nama Jadwal</label>
                        <input type="text" name="nama" id="nama" value="{{ old('nama') }}" required
                            placeholder="Contoh: OSCE Semester Genap 2024/2025"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('nama') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

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

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Peserta</label>
                    <p class="text-xs text-gray-500 mb-2">Klik nama kelas untuk memilih semua mahasiswa dalam kelas tersebut, atau pilih mahasiswa satu per satu</p>
                    
                    <div class="border border-gray-200 rounded-md max-h-96 overflow-y-auto">
                        @forelse($kelasList as $kelas)
                            <div class="border-b last:border-b-0">
                                <div class="bg-gray-50 px-3 py-2 flex items-center justify-between">
                                    <label class="flex items-center cursor-pointer">
                                        <input type="checkbox" class="kelas-checkbox rounded border-gray-300 text-indigo-600" 
                                               data-kelas="{{ $kelas->id }}"
                                               onchange="toggleKelas({{ $kelas->id }})">
                                        <span class="ml-2 text-sm font-medium text-gray-700">{{ $kelas->nama }}</span>
                                        <span class="ml-2 text-xs text-gray-500">({{ $kelas->mahasiswa->count() }} mhs)</span>
                                    </label>
                                    <button type="button" onclick="toggleCollapse({{ $kelas->id }})" class="text-gray-500 hover:text-gray-700">
                                        <svg class="w-4 h-4 transform transition-transform" id="arrow-{{ $kelas->id }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>
                                </div>
                                <div id="kelas-{{ $kelas->id }}" class="hidden px-3 py-2 space-y-1 bg-white">
                                    @foreach($kelas->mahasiswa as $mhs)
                                        <label class="flex items-center">
                                            <input type="checkbox" name="mahasiswa_ids[]" value="{{ $mhs->id }}"
                                                   class="mhs-{{ $kelas->id }} rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                                   {{ in_array($mhs->id, old('mahasiswa_ids', [])) ? 'checked' : '' }}
                                                   onchange="updateKelasCheckbox({{ $kelas->id }})">
                                            <span class="ml-2 text-sm text-gray-600">{{ $mhs->nim }} - {{ $mhs->nama }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-sm p-3">Belum ada kelas. <a href="{{ route('admin.kelas.create') }}" class="text-indigo-600">Buat kelas dulu</a></p>
                        @endforelse
                    </div>
                    @error('mahasiswa_ids') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    
                    <div class="mt-2 text-sm text-gray-500">
                        Terpilih: <span id="selectedCount">0</span> mahasiswa
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-4 mt-6 pt-6 border-t">
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                    Simpan Jadwal
                </button>
                <a href="{{ route('admin.jadwal.index') }}" class="text-gray-600 hover:text-gray-900">Batal</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function toggleCollapse(kelasId) {
    const content = document.getElementById('kelas-' + kelasId);
    const arrow = document.getElementById('arrow-' + kelasId);
    content.classList.toggle('hidden');
    arrow.classList.toggle('rotate-180');
}

function toggleKelas(kelasId) {
    const kelasCheckbox = document.querySelector(`input[data-kelas="${kelasId}"]`);
    const mahasiswaCheckboxes = document.querySelectorAll(`.mhs-${kelasId}`);
    
    mahasiswaCheckboxes.forEach(cb => {
        cb.checked = kelasCheckbox.checked;
    });
    
    // Show the list if selecting
    if (kelasCheckbox.checked) {
        document.getElementById('kelas-' + kelasId).classList.remove('hidden');
        document.getElementById('arrow-' + kelasId).classList.add('rotate-180');
    }
    
    updateSelectedCount();
}

function updateKelasCheckbox(kelasId) {
    const mahasiswaCheckboxes = document.querySelectorAll(`.mhs-${kelasId}`);
    const kelasCheckbox = document.querySelector(`input[data-kelas="${kelasId}"]`);
    const allChecked = Array.from(mahasiswaCheckboxes).every(cb => cb.checked);
    const someChecked = Array.from(mahasiswaCheckboxes).some(cb => cb.checked);
    
    kelasCheckbox.checked = allChecked;
    kelasCheckbox.indeterminate = someChecked && !allChecked;
    
    updateSelectedCount();
}

function updateSelectedCount() {
    const count = document.querySelectorAll('input[name="mahasiswa_ids[]"]:checked').length;
    document.getElementById('selectedCount').textContent = count;
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateSelectedCount();
    
    // Check initial state of kelas checkboxes
    @foreach($kelasList as $kelas)
    updateKelasCheckbox({{ $kelas->id }});
    @endforeach
});
</script>
@endpush
@endsection
