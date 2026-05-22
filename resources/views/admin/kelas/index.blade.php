@extends('layouts.app')

@section('title', 'Kelola Kelas')

@section('content')
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold">Kelola Kelas</h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.kelas.arsip') }}" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                    </svg>
                    Lihat Arsip
                </a>
                <a href="{{ route('admin.kelas.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                    + Tambah Kelas
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tahun Akademik</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah Mahasiswa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($kelas as $k)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $k->kode }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $k->nama ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $k->label_tahun_akademik ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $k->mahasiswa_count }} mahasiswa</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <button onclick="openPrintModal({{ $k->id }}, '{{ $k->kode }}')" class="text-gray-600 hover:text-gray-900">
                                    <svg class="w-4 h-4 inline mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                    </svg>Cetak Kartu
                                </button>
                                <a href="{{ route('admin.kelas.edit', $k) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                <form action="{{ route('admin.kelas.arsipkan', $k) }}" method="POST" class="inline" onsubmit="return confirm('Arsipkan kelas ini? Data tidak akan hilang.')">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="text-amber-600 hover:text-amber-900">Arsipkan</button>
                                </form>
                                <form action="{{ route('admin.kelas.destroy', $k) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus kelas ini? Data mahasiswa juga akan terhapus!')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">Belum ada kelas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $kelas->links() }}</div>
    </div>
</div>

{{-- Print Kartu Modal --}}
<div id="printKartuModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closePrintModal()"></div>
        
        <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6 z-10">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Cetak Kartu Peserta</h3>
                <button onclick="closePrintModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <p class="text-sm text-gray-500 mb-1">Kelas: <strong id="modalKelasName"></strong></p>
            <p class="text-sm text-gray-500 mb-4">Pilih jadwal ujian:</p>

            @if($jadwalList->count() > 0)
                <div class="space-y-2 max-h-48 overflow-y-auto">
                    @foreach($jadwalList as $jadwal)
                        <a id="jadwal-link-{{ $jadwal->id }}" href="#" 
                           target="_blank"
                           class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:border-indigo-400 hover:bg-indigo-50 transition-colors">
                            <div>
                                <div class="font-medium text-gray-900 text-sm">{{ $jadwal->nama }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">{{ $jadwal->mulai ? $jadwal->mulai->format('d M Y') : '-' }}</div>
                            </div>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                    <p class="text-yellow-800 text-sm">Belum ada jadwal aktif.</p>
                </div>
            @endif

            {{-- Optional fields --}}
            <div class="mt-4 pt-3 border-t">
                <p class="text-xs text-gray-500 mb-2 font-medium">Tampilkan di kartu (opsional):</p>
                <div class="space-y-1">
                    <label class="flex items-center text-sm cursor-pointer">
                        <input type="checkbox" id="chk_jadwal_nama" checked class="rounded border-gray-300 text-indigo-600 mr-2 chk-field">
                        Nama Jadwal
                    </label>
                    <label class="flex items-center text-sm cursor-pointer">
                        <input type="checkbox" id="chk_gelombang" checked class="rounded border-gray-300 text-indigo-600 mr-2 chk-field">
                        Nama Gelombang
                    </label>
                    <label class="flex items-center text-sm cursor-pointer">
                        <input type="checkbox" id="chk_waktu" checked class="rounded border-gray-300 text-indigo-600 mr-2 chk-field">
                        Waktu Ujian
                    </label>
                </div>
            </div>

            <div class="mt-4 flex justify-end">
                <button onclick="closePrintModal()" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
function openPrintModal(kelasId, kelasKode) {
    document.getElementById('modalKelasName').textContent = kelasKode;
    window._printKelasId = kelasId;
    updatePrintLinks();
    document.getElementById('printKartuModal').classList.remove('hidden');
}

function updatePrintLinks() {
    var kelasId = window._printKelasId;
    var showJadwal = document.getElementById('chk_jadwal_nama').checked ? '1' : '0';
    var showGelombang = document.getElementById('chk_gelombang').checked ? '1' : '0';
    var showWaktu = document.getElementById('chk_waktu').checked ? '1' : '0';
    @foreach($jadwalList as $jadwal)
        document.getElementById('jadwal-link-{{ $jadwal->id }}').href = 
            "{{ url('admin/kelas') }}/" + kelasId + "/print-kartu?jadwal_id={{ $jadwal->id }}&show_jadwal=" + showJadwal + "&show_gelombang=" + showGelombang + "&show_waktu=" + showWaktu;
    @endforeach
}

// Update links when checkboxes change
document.querySelectorAll('.chk-field').forEach(function(cb) {
    cb.addEventListener('change', updatePrintLinks);
});

function closePrintModal() {
    document.getElementById('printKartuModal').classList.add('hidden');
}
</script>
@endsection
