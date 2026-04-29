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
                        <strong>Catatan:</strong> Centang penguji yang akan ditugaskan per stasi. Anda bisa memilih lebih dari 1 penguji per stasi jika diperlukan.
                    </p>
                </div>

                <div class="space-y-6">
                    @foreach($stasiList as $idx => $stasi)
                        @php
                            $assignedPengujiIds = isset($currentAssignments[$stasi->id]) 
                                ? $currentAssignments[$stasi->id]->pluck('penguji_id')->toArray() 
                                : [];
                            $assignedCount = count($assignedPengujiIds);
                        @endphp
                        <div class="border rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <h4 class="font-medium text-gray-900">{{ $idx + 1 }}. {{ $stasi->nama }}</h4>
                                    @if($stasi->deskripsi)
                                        <p class="text-xs text-gray-500">{{ Str::limit($stasi->deskripsi, 80) }}</p>
                                    @endif
                                </div>
                                <div>
                                    @if($assignedCount > 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            ✓ {{ $assignedCount }} penguji
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            Belum di-assign
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2 max-h-48 overflow-y-auto">
                                @foreach($pengujiList as $penguji)
                                    <label class="flex items-center p-2 rounded hover:bg-gray-50 cursor-pointer">
                                        <input type="checkbox" 
                                               name="penguji[{{ $stasi->id }}][]" 
                                               value="{{ $penguji->id }}"
                                               {{ in_array($penguji->id, $assignedPengujiIds) ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-gray-700">{{ $penguji->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
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
