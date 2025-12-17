@extends('layouts.app')

@section('title', 'Kelola Gelombang - ' . $jadwal->nama)

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex justify-between items-start">
                <div>
                    <h2 class="text-xl font-semibold">Kelola Gelombang Ujian</h2>
                    <p class="text-gray-500 mt-1">
                        Jadwal: {{ $jadwal->nama }} | {{ $jadwal->mulai->format('d F Y') }}
                    </p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('admin.jadwal.gelombang.create', $jadwal) }}" 
                       class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                        + Tambah Gelombang
                    </a>
                    <a href="{{ route('admin.jadwal.show', $jadwal) }}" 
                       class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300">
                        Kembali
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif
        </div>
    </div>

    {{-- Info Peserta --}}
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex justify-between items-center">
            <div>
                <span class="font-medium text-blue-800">Total Peserta Jadwal:</span>
                <span class="text-blue-600">{{ $jadwal->peserta->count() }} mahasiswa</span>
            </div>
            <div>
                @php
                    $assignedCount = $gelombangList->sum(fn($g) => $g->mahasiswa->count());
                    $unassignedCount = $jadwal->peserta->count() - $assignedCount;
                @endphp
                <span class="font-medium text-blue-800">Sudah di Gelombang:</span>
                <span class="text-blue-600">{{ $assignedCount }}</span>
                @if($unassignedCount > 0)
                    <span class="text-yellow-600 ml-2">({{ $unassignedCount }} belum diassign)</span>
                @endif
            </div>
        </div>
    </div>

    {{-- List Gelombang --}}
    @forelse($gelombangList as $gelombang)
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">{{ $gelombang->nama }}</h3>
                        <p class="text-sm text-gray-500">
                            @if($gelombang->waktu_mulai && $gelombang->waktu_selesai)
                                Waktu: {{ \Carbon\Carbon::parse($gelombang->waktu_mulai)->format('H:i') }} - 
                                {{ \Carbon\Carbon::parse($gelombang->waktu_selesai)->format('H:i') }} |
                            @endif
                            {{ $gelombang->mahasiswa->count() }} mahasiswa
                        </p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.jadwal.gelombang.edit', [$jadwal, $gelombang]) }}" 
                           class="text-indigo-600 hover:text-indigo-800">
                            Edit
                        </a>
                        <form action="{{ route('admin.jadwal.gelombang.destroy', [$jadwal, $gelombang]) }}" 
                              method="POST" class="inline"
                              onsubmit="return confirm('Yakin hapus gelombang ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800">Hapus</button>
                        </form>
                    </div>
                </div>

                {{-- Penguji per Stasi --}}
                <div class="mb-4">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Penguji per Stasi:</h4>
                    <div class="flex flex-wrap gap-2">
                        @foreach($stasiList as $stasi)
                            @php
                                $gp = $gelombang->pengujiStasi->where('stasi_id', $stasi->id)->first();
                            @endphp
                            <div class="px-3 py-1 rounded-full text-xs {{ $gp ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-500' }}">
                                {{ $stasi->nama }}: {{ $gp ? $gp->penguji->name : '-' }}
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Mahasiswa --}}
                @if($gelombang->mahasiswa->count() > 0)
                    <div>
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Mahasiswa ({{ $gelombang->mahasiswa->count() }}):</h4>
                        <div class="flex flex-wrap gap-1">
                            @foreach($gelombang->mahasiswa->take(10) as $mhs)
                                <span class="px-2 py-0.5 bg-gray-100 text-gray-700 text-xs rounded">
                                    {{ $mhs->nim }} - {{ $mhs->nama }}
                                </span>
                            @endforeach
                            @if($gelombang->mahasiswa->count() > 10)
                                <span class="px-2 py-0.5 bg-gray-200 text-gray-600 text-xs rounded">
                                    +{{ $gelombang->mahasiswa->count() - 10 }} lainnya
                                </span>
                            @endif
                        </div>
                    </div>
                @else
                    <p class="text-sm text-gray-400 italic">Belum ada mahasiswa di gelombang ini.</p>
                @endif
            </div>
        </div>
    @empty
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
            <p class="text-yellow-800">Belum ada gelombang untuk jadwal ini.</p>
            <a href="{{ route('admin.jadwal.gelombang.create', $jadwal) }}" 
               class="inline-block mt-2 text-indigo-600 hover:text-indigo-800">
                + Tambah Gelombang Pertama
            </a>
        </div>
    @endforelse
</div>
@endsection
