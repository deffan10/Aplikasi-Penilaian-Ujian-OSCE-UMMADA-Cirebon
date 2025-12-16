@extends('layouts.app')

@section('title', 'Kelola Mahasiswa')

@section('content')
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold">Kelola Mahasiswa</h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.mahasiswa.import.form') }}" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                    Import Excel
                </a>
                <a href="{{ route('admin.mahasiswa.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                    + Tambah Mahasiswa
                </a>
            </div>
        </div>

        <!-- Filter -->
        <form method="GET" class="flex flex-wrap gap-4 mb-6">
            <div>
                <select name="kelas_id" class="border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">-- Semua Kelas --</option>
                    @foreach($kelasList as $k)
                        <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->kode }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari NIM/Nama..."
                    class="border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">Filter</button>
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIM</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kelas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($mahasiswa as $m)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $m->nim }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $m->nama }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $m->kelas->kode ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <a href="{{ route('admin.mahasiswa.edit', $m) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                <form action="{{ route('admin.mahasiswa.destroy', $m) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus mahasiswa ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">Belum ada mahasiswa.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $mahasiswa->withQueryString()->links() }}</div>
    </div>
</div>
@endsection
