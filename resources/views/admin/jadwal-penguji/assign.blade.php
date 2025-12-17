@extends('layouts.app')

@section('title', 'Assign Penguji - ' . $gelombang->nama)

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <nav class="text-sm text-gray-500 mb-2">
                <a href="{{ route('admin.jadwal-penguji.index') }}" class="hover:text-indigo-600">Jadwal Penguji</a>
                <span class="mx-2">→</span>
                <a href="{{ route('admin.jadwal-penguji.gelombang', $jadwal) }}" class="hover:text-indigo-600">{{ $jadwal->nama }}</a>
                <span class="mx-2">→</span>
                <span class="text-gray-900">{{ $gelombang->nama }}</span>
            </nav>
            <h2 class="text-xl font-semibold">Assign Penguji ke Stasi</h2>
            <p class="text-gray-500 mt-1">
                {{ $jadwal->nama }} | {{ $gelombang->nama }}
                @if($gelombang->waktu_mulai && $gelombang->waktu_selesai)
                    ({{ \Carbon\Carbon::parse($gelombang->waktu_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($gelombang->waktu_selesai)->format('H:i') }})
                @endif
            </p>
        </div>
    </div>

    {{-- Form Assignment --}}
    <form action="{{ route('admin.jadwal-penguji.store-assignment', [$jadwal, $gelombang]) }}" method="POST">
        @csrf
        
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="mb-4">
                    <p class="text-sm text-gray-600">
                        <strong>Catatan:</strong> Setiap stasi harus memiliki 1 penguji yang berbeda per gelombang.
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stasi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penguji</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($stasiList as $idx => $stasi)
                                @php
                                    $current = $currentAssignments[$stasi->id] ?? null;
                                @endphp
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $idx + 1 }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-gray-900">{{ $stasi->nama }}</div>
                                        @if($stasi->deskripsi)
                                            <div class="text-xs text-gray-500">{{ Str::limit($stasi->deskripsi, 50) }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <select name="penguji[{{ $stasi->id }}]" 
                                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                            <option value="">-- Pilih Penguji --</option>
                                            @foreach($pengujiList as $penguji)
                                                <option value="{{ $penguji->id }}" 
                                                    {{ $current && $current->penguji_id == $penguji->id ? 'selected' : '' }}>
                                                    {{ $penguji->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($current)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                ✓ Sudah di-assign
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                Belum di-assign
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6 flex justify-between">
                    <a href="{{ route('admin.jadwal-penguji.gelombang', $jadwal) }}" 
                       class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300">
                        Kembali
                    </a>
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700">
                        Simpan Assignment
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
