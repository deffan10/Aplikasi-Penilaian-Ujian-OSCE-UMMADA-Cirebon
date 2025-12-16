@extends('layouts.app')

@section('title', 'Arsip Jadwal')

@section('content')
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-xl font-semibold">Arsip Jadwal Ujian</h2>
                <p class="text-sm text-gray-500 mt-1">Jadwal yang sudah diarsipkan. Data nilai tetap tersimpan dan bisa dilihat di Rekap.</p>
            </div>
            <a href="{{ route('admin.jadwal.index') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                ← Kembali ke Jadwal Aktif
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Jadwal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tahun Akademik</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Peserta</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Diarsipkan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($jadwal as $j)
                        <tr class="bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $j->nama }}
                                <span class="ml-2 px-2 py-0.5 text-xs bg-amber-100 text-amber-800 rounded">Arsip</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $j->label_tahun_akademik ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $j->mulai->format('d M Y H:i') }} - {{ $j->selesai->format('d M Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $j->peserta_count ?? $j->peserta->count() }} peserta</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $j->diarsipkan_pada ? $j->diarsipkan_pada->format('d M Y H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <a href="{{ route('admin.rekap.jadwal', $j) }}" class="text-blue-600 hover:text-blue-900">Lihat Rekap</a>
                                <form action="{{ route('admin.jadwal.restore', $j) }}" method="POST" class="inline" onsubmit="return confirm('Kembalikan jadwal ini ke daftar aktif?')">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="text-green-600 hover:text-green-900">Restore</button>
                                </form>
                                <form action="{{ route('admin.jadwal.destroy', $j) }}" method="POST" class="inline" onsubmit="return confirm('PERINGATAN: Hapus permanen jadwal ini? Semua data nilai akan HILANG!')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Hapus Permanen</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada jadwal yang diarsipkan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $jadwal->links() }}</div>
    </div>
</div>
@endsection
