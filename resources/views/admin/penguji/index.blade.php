@extends('layouts.app')

@section('title', 'Kelola Penguji')

@section('content')
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold">Kelola Penguji</h2>
            <div class="flex gap-2">
                <button onclick="document.getElementById('printLabelModal').classList.remove('hidden')" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                    <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Print Label
                </button>
                <a href="{{ route('admin.penguji.import-form') }}" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                    <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    Import
                </a>
                <a href="{{ route('admin.penguji.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                    + Tambah Penguji
                </a>
            </div>
        </div>

        {{-- Import Errors --}}
        @if(session('import_errors'))
            <div class="mb-4 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <h4 class="font-medium text-yellow-800 mb-2">⚠️ Beberapa baris gagal diimport:</h4>
                <ul class="list-disc list-inside text-sm text-yellow-700 max-h-40 overflow-y-auto">
                    @foreach(session('import_errors') as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Username</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stasi (Jadwal Aktif)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($penguji as $p)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $p->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="font-mono bg-gray-100 px-2 py-1 rounded">{{ $p->username ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                @if(isset($pengujiStasiMap[$p->id]) && $pengujiStasiMap[$p->id]->count() > 0)
                                    @foreach($pengujiStasiMap[$p->id] as $stasi)
                                        <span class="inline-block bg-indigo-100 text-indigo-800 text-xs px-2 py-1 rounded mr-1 mb-1">
                                            {{ $stasi->nama }}
                                        </span>
                                    @endforeach
                                @else
                                    <span class="text-gray-400">Belum ditugaskan</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <a href="{{ route('admin.penguji.edit', $p) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                <form action="{{ route('admin.penguji.destroy', $p) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus penguji ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">Belum ada penguji.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $penguji->links() }}</div>
    </div>
</div>

{{-- Print Label Modal --}}
<div id="printLabelModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('printLabelModal').classList.add('hidden')"></div>
        
        <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6 z-10">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Print Label Penguji</h3>
                <button onclick="document.getElementById('printLabelModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <p class="text-sm text-gray-500 mb-4">Pilih jadwal ujian untuk mencetak label penguji yang terdaftar:</p>

            @if($jadwalList->count() > 0)
                <div class="space-y-2 max-h-48 overflow-y-auto">
                    @foreach($jadwalList as $jadwal)
                        <a id="penguji-jadwal-link-{{ $jadwal->id }}" 
                           href="{{ route('admin.penguji.print-labels', ['jadwal_id' => $jadwal->id]) }}" 
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
                <p class="text-xs text-gray-500 mb-2 font-medium">Tampilkan di label (opsional):</p>
                <div class="space-y-1">
                    <label class="flex items-center text-sm cursor-pointer">
                        <input type="checkbox" id="lbl_chk_stasi" checked class="rounded border-gray-300 text-indigo-600 mr-2 lbl-chk-field">
                        Stasi
                    </label>
                    <label class="flex items-center text-sm cursor-pointer">
                        <input type="checkbox" id="lbl_chk_gelombang" checked class="rounded border-gray-300 text-indigo-600 mr-2 lbl-chk-field">
                        Nama Gelombang
                    </label>
                    <label class="flex items-center text-sm cursor-pointer">
                        <input type="checkbox" id="lbl_chk_waktu" checked class="rounded border-gray-300 text-indigo-600 mr-2 lbl-chk-field">
                        Waktu
                    </label>
                </div>
            </div>

            <div class="mt-4 flex justify-end">
                <button onclick="document.getElementById('printLabelModal').classList.add('hidden')" 
                    class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function updatePengujiPrintLinks() {
    var showStasi = document.getElementById('lbl_chk_stasi').checked ? '1' : '0';
    var showGelombang = document.getElementById('lbl_chk_gelombang').checked ? '1' : '0';
    var showWaktu = document.getElementById('lbl_chk_waktu').checked ? '1' : '0';
    @foreach($jadwalList as $jadwal)
        document.getElementById('penguji-jadwal-link-{{ $jadwal->id }}').href = 
            "{{ route('admin.penguji.print-labels') }}?jadwal_id={{ $jadwal->id }}&show_stasi=" + showStasi + "&show_gelombang=" + showGelombang + "&show_waktu=" + showWaktu;
    @endforeach
}

document.querySelectorAll('.lbl-chk-field').forEach(function(cb) {
    cb.addEventListener('change', updatePengujiPrintLinks);
});
</script>
@endpush
