@extends('layouts.app')

@section('title', 'Kelola Penguji')

@section('content')
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold">Kelola Penguji</h2>
            <div class="flex gap-2">
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stasi Ditugaskan</th>
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
                                @if($p->assignedStasi->count() > 0)
                                    @foreach($p->assignedStasi as $stasi)
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
@endsection
