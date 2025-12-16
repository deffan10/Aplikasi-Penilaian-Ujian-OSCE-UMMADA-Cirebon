@extends('layouts.app')

@section('title', 'Dashboard Penguji')

@section('content')
<div class="space-y-6">
    {{-- Welcome Card --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h2 class="text-xl font-semibold mb-2">Selamat Datang, {{ auth()->user()->name }}!</h2>
            <p class="text-gray-500">Anda login sebagai Penguji OSCE</p>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="text-2xl font-bold text-indigo-600">{{ $assignedStasi->count() }}</div>
                <div class="text-sm text-gray-600">Stasi Ditugaskan</div>
            </div>
        </div>
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="text-2xl font-bold text-green-600">{{ $totalDinilai }}</div>
                <div class="text-sm text-gray-600">Total Penilaian</div>
            </div>
        </div>
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="text-2xl font-bold text-yellow-600">{{ $jadwalAktif }}</div>
                <div class="text-sm text-gray-600">Jadwal Aktif</div>
            </div>
        </div>
    </div>

    {{-- Stasi yang Ditugaskan --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold mb-4">Stasi yang Ditugaskan</h3>
            
            @if($assignedStasi->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($assignedStasi as $stasi)
                        <div class="border rounded-lg p-4 hover:bg-gray-50">
                            <h4 class="font-medium text-gray-900">{{ $stasi->nama }}</h4>
                            <p class="text-sm text-gray-500 mt-1">{{ $stasi->deskripsi ?? 'Tidak ada deskripsi' }}</p>
                            <div class="mt-3">
                                <a href="{{ route('penguji.penilaian.stasi', $stasi) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                    Lihat Jadwal →
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500">Belum ada stasi yang ditugaskan kepada Anda.</p>
            @endif
        </div>
    </div>

    {{-- Jadwal Aktif --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold mb-4">Jadwal Ujian Aktif</h3>
            
            @if($jadwalList->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jadwal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Peserta</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($jadwalList as $jadwal)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $jadwal->nama }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $jadwal->mulai->format('d M Y H:i') }} - {{ $jadwal->selesai->format('H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $jadwal->peserta->count() }} orang</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500">Tidak ada jadwal ujian aktif saat ini.</p>
            @endif
        </div>
    </div>
</div>
@endsection
