@extends('layouts.app')

@section('title', 'Kelola Stasi')

@section('content')
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold">Kelola Stasi OSCE</h2>
            <a href="{{ route('admin.stasi.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                + Tambah Stasi
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Stasi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Komponen</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Penguji</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($stasi as $index => $s)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $stasi->firstItem() + $index }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $s->nama }}</div>
                                @if($s->deskripsi)
                                    <div class="text-sm text-gray-500">{{ Str::limit($s->deskripsi, 50) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <a href="{{ route('admin.stasi.komponen.index', $s) }}" class="text-indigo-600 hover:underline">
                                    {{ $s->komponens_count }} komponen
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $s->penguji_count }} penguji
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($s->aktif)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Aktif
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <a href="{{ route('admin.stasi.komponen.index', $s) }}" class="text-blue-600 hover:text-blue-900">Komponen</a>
                                <a href="{{ route('admin.stasi.edit', $s) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                <form action="{{ route('admin.stasi.destroy', $s) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus stasi ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                Belum ada stasi. Klik tombol "Tambah Stasi" untuk menambahkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $stasi->links() }}
        </div>
    </div>
</div>
@endsection
