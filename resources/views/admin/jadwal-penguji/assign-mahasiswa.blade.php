@extends('layouts.app')

@section('title', 'Assign Mahasiswa - ' . $gelombang->nama)

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex justify-between items-start">
                <div>
                    <nav class="text-sm text-gray-500 mb-2">
                        <a href="{{ route('admin.jadwal-penguji.index') }}" class="hover:text-indigo-600">Jadwal Penguji</a>
                        <span class="mx-2">›</span>
                        <a href="{{ route('admin.jadwal-penguji.gelombang', $jadwal) }}" class="hover:text-indigo-600">{{ $jadwal->nama }}</a>
                        <span class="mx-2">›</span>
                        <span>Assign Mahasiswa</span>
                    </nav>
                    <h2 class="text-xl font-semibold">Assign Mahasiswa ke {{ $gelombang->nama }}</h2>
                    <p class="text-gray-500 mt-1">Pilih mahasiswa yang akan mengikuti ujian di gelombang ini</p>
                </div>
                <a href="{{ route('admin.jadwal-penguji.gelombang', $jadwal) }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300">
                    ← Kembali
                </a>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.jadwal-penguji.store-mahasiswa', [$jadwal, $gelombang]) }}" method="POST">
        @csrf
        
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                {{-- Filter & Search --}}
                <div class="mb-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- Filter Kelas --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Filter Kelas</label>
                        <select id="kelasFilter" onchange="filterMahasiswa()" 
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Semua Kelas</option>
                            @foreach($kelasList as $kelas)
                                <option value="{{ $kelas->id }}">{{ $kelas->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    {{-- Search --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cari Mahasiswa</label>
                        <input type="text" id="searchInput" placeholder="Cari NIM atau Nama..."
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            onkeyup="filterMahasiswa()">
                    </div>

                    {{-- Quick Actions --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Aksi Cepat</label>
                        <div class="flex gap-2">
                            <button type="button" onclick="selectFiltered()" 
                                class="flex-1 px-3 py-2 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                                Pilih Semua (Filtered)
                            </button>
                            <button type="button" onclick="deselectFiltered()" 
                                class="flex-1 px-3 py-2 bg-gray-500 text-white text-sm rounded hover:bg-gray-600">
                                Batal Pilih
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Info --}}
                <div class="mb-4 flex justify-between items-center">
                    <div class="text-sm text-gray-500">
                        <span class="font-semibold text-indigo-600" id="selectedCount">{{ count($currentMahasiswaIds) }}</span> mahasiswa dipilih
                        <span class="mx-2">|</span>
                        <span id="visibleCount">{{ $allMahasiswa->count() }}</span> mahasiswa ditampilkan
                        @if(count($assignedInOtherGelombang) > 0)
                            <span class="ml-2 text-orange-600">({{ count($assignedInOtherGelombang) }} sudah di gelombang lain)</span>
                        @endif
                    </div>
                </div>

                {{-- Kelas Sections --}}
                <div class="space-y-4 max-h-[500px] overflow-y-auto border border-gray-200 rounded-md p-3" id="mahasiswaContainer">
                    @foreach($kelasList as $kelas)
                        @php
                            $mahasiswaKelas = $allMahasiswa->where('kelas_id', $kelas->id);
                        @endphp
                        @if($mahasiswaKelas->count() > 0)
                            <div class="kelas-section" data-kelas-id="{{ $kelas->id }}">
                                {{-- Kelas Header --}}
                                <div class="bg-gray-100 px-3 py-2 rounded-t-md flex justify-between items-center sticky top-0">
                                    <div class="flex items-center gap-2">
                                        <input type="checkbox" 
                                               id="kelas_{{ $kelas->id }}" 
                                               class="kelas-checkbox rounded border-gray-300 text-indigo-600"
                                               onchange="toggleKelas({{ $kelas->id }})">
                                        <label for="kelas_{{ $kelas->id }}" class="font-semibold text-gray-700 cursor-pointer">
                                            {{ $kelas->nama }}
                                        </label>
                                        <span class="text-xs text-gray-500">({{ $mahasiswaKelas->count() }} mahasiswa)</span>
                                    </div>
                                    <button type="button" onclick="toggleKelasVisibility({{ $kelas->id }})" 
                                            class="text-gray-500 hover:text-gray-700 kelas-toggle" data-kelas="{{ $kelas->id }}">
                                        <svg class="w-5 h-5 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>
                                </div>
                                
                                {{-- Mahasiswa List --}}
                                <div class="border border-t-0 border-gray-200 rounded-b-md mahasiswa-kelas-list" id="kelas_list_{{ $kelas->id }}">
                                    @foreach($mahasiswaKelas as $mhs)
                                        @php
                                            $isAssigned = in_array($mhs->id, $currentMahasiswaIds);
                                            $isInOther = in_array($mhs->id, $assignedInOtherGelombang);
                                        @endphp
                                        <label class="flex items-center p-2 hover:bg-gray-50 mahasiswa-item {{ $isInOther ? 'opacity-50 bg-orange-50' : '' }}"
                                               data-kelas-id="{{ $kelas->id }}"
                                               data-nim="{{ strtolower($mhs->nim) }}"
                                               data-nama="{{ strtolower($mhs->nama) }}">
                                            <input type="checkbox" 
                                                   name="mahasiswa_ids[]" 
                                                   value="{{ $mhs->id }}"
                                                   {{ $isAssigned ? 'checked' : '' }}
                                                   {{ $isInOther ? 'disabled' : '' }}
                                                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 mahasiswa-checkbox"
                                                   data-kelas-id="{{ $kelas->id }}"
                                                   onchange="updateCount(); updateKelasCheckbox({{ $kelas->id }})">
                                            <span class="ml-2 text-sm text-gray-700 flex-1">
                                                <span class="font-mono font-medium">{{ $mhs->nim }}</span> 
                                                <span class="mx-1">-</span>
                                                {{ $mhs->nama }}
                                            </span>
                                            @if($isInOther)
                                                <span class="text-xs text-orange-600 bg-orange-100 px-2 py-0.5 rounded">Di gelombang lain</span>
                                            @endif
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach

                    {{-- Mahasiswa tanpa kelas --}}
                    @php
                        $mahasiswaTanpaKelas = $allMahasiswa->whereNull('kelas_id');
                    @endphp
                    @if($mahasiswaTanpaKelas->count() > 0)
                        <div class="kelas-section" data-kelas-id="0">
                            <div class="bg-gray-100 px-3 py-2 rounded-t-md flex justify-between items-center">
                                <div class="flex items-center gap-2">
                                    <input type="checkbox" 
                                           id="kelas_0" 
                                           class="kelas-checkbox rounded border-gray-300 text-indigo-600"
                                           onchange="toggleKelas(0)">
                                    <label for="kelas_0" class="font-semibold text-gray-500 cursor-pointer">
                                        Tanpa Kelas
                                    </label>
                                    <span class="text-xs text-gray-500">({{ $mahasiswaTanpaKelas->count() }} mahasiswa)</span>
                                </div>
                            </div>
                            <div class="border border-t-0 border-gray-200 rounded-b-md mahasiswa-kelas-list" id="kelas_list_0">
                                @foreach($mahasiswaTanpaKelas as $mhs)
                                    @php
                                        $isAssigned = in_array($mhs->id, $currentMahasiswaIds);
                                        $isInOther = in_array($mhs->id, $assignedInOtherGelombang);
                                    @endphp
                                    <label class="flex items-center p-2 hover:bg-gray-50 mahasiswa-item {{ $isInOther ? 'opacity-50 bg-orange-50' : '' }}"
                                           data-kelas-id="0"
                                           data-nim="{{ strtolower($mhs->nim) }}"
                                           data-nama="{{ strtolower($mhs->nama) }}">
                                        <input type="checkbox" 
                                               name="mahasiswa_ids[]" 
                                               value="{{ $mhs->id }}"
                                               {{ $isAssigned ? 'checked' : '' }}
                                               {{ $isInOther ? 'disabled' : '' }}
                                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 mahasiswa-checkbox"
                                               data-kelas-id="0"
                                               onchange="updateCount(); updateKelasCheckbox(0)">
                                        <span class="ml-2 text-sm text-gray-700 flex-1">
                                            <span class="font-mono font-medium">{{ $mhs->nim }}</span> - {{ $mhs->nama }}
                                        </span>
                                        @if($isInOther)
                                            <span class="text-xs text-orange-600 bg-orange-100 px-2 py-0.5 rounded">Di gelombang lain</span>
                                        @endif
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($allMahasiswa->count() == 0)
                        <p class="text-gray-500 text-sm text-center py-8">Tidak ada mahasiswa.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
            <div class="p-6 flex justify-end gap-4">
                <a href="{{ route('admin.jadwal-penguji.gelombang', $jadwal) }}" 
                   class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    Simpan Mahasiswa
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    // Initialize kelas checkbox states
    document.addEventListener('DOMContentLoaded', function() {
        @foreach($kelasList as $kelas)
            updateKelasCheckbox({{ $kelas->id }});
        @endforeach
        updateKelasCheckbox(0);
        updateCount();
    });

    function toggleKelas(kelasId) {
        const kelasCheckbox = document.getElementById('kelas_' + kelasId);
        const checkboxes = document.querySelectorAll('.mahasiswa-checkbox[data-kelas-id="' + kelasId + '"]:not(:disabled)');
        
        checkboxes.forEach(cb => {
            const item = cb.closest('.mahasiswa-item');
            if (item.style.display !== 'none') {
                cb.checked = kelasCheckbox.checked;
            }
        });
        updateCount();
    }

    function updateKelasCheckbox(kelasId) {
        const checkboxes = document.querySelectorAll('.mahasiswa-checkbox[data-kelas-id="' + kelasId + '"]:not(:disabled)');
        const kelasCheckbox = document.getElementById('kelas_' + kelasId);
        
        if (!kelasCheckbox || checkboxes.length === 0) return;
        
        const visibleCheckboxes = Array.from(checkboxes).filter(cb => {
            const item = cb.closest('.mahasiswa-item');
            return item.style.display !== 'none';
        });
        
        const checkedCount = visibleCheckboxes.filter(cb => cb.checked).length;
        
        if (checkedCount === 0) {
            kelasCheckbox.checked = false;
            kelasCheckbox.indeterminate = false;
        } else if (checkedCount === visibleCheckboxes.length) {
            kelasCheckbox.checked = true;
            kelasCheckbox.indeterminate = false;
        } else {
            kelasCheckbox.checked = false;
            kelasCheckbox.indeterminate = true;
        }
    }

    function toggleKelasVisibility(kelasId) {
        const list = document.getElementById('kelas_list_' + kelasId);
        const toggle = document.querySelector('.kelas-toggle[data-kelas="' + kelasId + '"] svg');
        
        if (list.style.display === 'none') {
            list.style.display = '';
            toggle.style.transform = '';
        } else {
            list.style.display = 'none';
            toggle.style.transform = 'rotate(-90deg)';
        }
    }

    function selectFiltered() {
        document.querySelectorAll('.mahasiswa-checkbox:not(:disabled)').forEach(cb => {
            const item = cb.closest('.mahasiswa-item');
            if (item.style.display !== 'none') {
                cb.checked = true;
            }
        });
        updateAllKelasCheckboxes();
        updateCount();
    }

    function deselectFiltered() {
        document.querySelectorAll('.mahasiswa-checkbox:not(:disabled)').forEach(cb => {
            const item = cb.closest('.mahasiswa-item');
            if (item.style.display !== 'none') {
                cb.checked = false;
            }
        });
        updateAllKelasCheckboxes();
        updateCount();
    }

    function updateAllKelasCheckboxes() {
        document.querySelectorAll('.kelas-section').forEach(section => {
            const kelasId = section.dataset.kelasId;
            updateKelasCheckbox(kelasId);
        });
    }

    function updateCount() {
        const count = document.querySelectorAll('.mahasiswa-checkbox:checked').length;
        document.getElementById('selectedCount').textContent = count;
        
        const visibleCount = document.querySelectorAll('.mahasiswa-item').length - 
                            document.querySelectorAll('.mahasiswa-item[style*="display: none"]').length;
        document.getElementById('visibleCount').textContent = visibleCount;
    }

    function filterMahasiswa() {
        const kelasFilter = document.getElementById('kelasFilter').value;
        const query = document.getElementById('searchInput').value.toLowerCase();
        
        // Filter kelas sections
        document.querySelectorAll('.kelas-section').forEach(section => {
            const sectionKelasId = section.dataset.kelasId;
            
            if (kelasFilter && kelasFilter !== sectionKelasId) {
                section.style.display = 'none';
            } else {
                section.style.display = '';
            }
        });
        
        // Filter individual mahasiswa by search
        document.querySelectorAll('.mahasiswa-item').forEach(item => {
            const nim = item.dataset.nim;
            const nama = item.dataset.nama;
            const section = item.closest('.kelas-section');
            
            // Only filter if section is visible
            if (section.style.display !== 'none') {
                if (query === '' || nim.includes(query) || nama.includes(query)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            }
        });
        
        updateAllKelasCheckboxes();
        updateCount();
    }
</script>
@endsection
