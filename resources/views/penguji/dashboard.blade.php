@extends('layouts.app')

@section('title', 'Dashboard Penguji')

@section('content')
@php
    $hour = (int) now()->format('H');
    if ($hour >= 5 && $hour < 11) {
        $greeting = 'Selamat Pagi';
        $icon = '🌅';
    } elseif ($hour >= 11 && $hour < 15) {
        $greeting = 'Selamat Siang';
        $icon = '☀️';
    } elseif ($hour >= 15 && $hour < 18) {
        $greeting = 'Selamat Sore';
        $icon = '🌇';
    } else {
        $greeting = 'Selamat Malam';
        $icon = '🌙';
    }
@endphp
<div class="space-y-6">
    {{-- Welcome Card --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-indigo-500">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-1">{{ $icon }} {{ $greeting }}, {{ auth()->user()->name }}!</h2>
                    <p class="text-gray-500">Anda login sebagai Penguji OSCE</p>
                </div>
                <div x-data="{ 
                    time: '{{ now()->format('H:i:s') }}',
                    date: '{{ now()->translatedFormat('l, d F Y') }}'
                }" 
                x-init="setInterval(() => { 
                    let d = new Date(); 
                    time = d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false }); 
                }, 1000)"
                class="text-right hidden sm:block">
                    <div class="text-3xl font-mono font-bold text-indigo-600" x-text="time"></div>
                    <div class="text-sm text-gray-500">{{ now()->translatedFormat('l, d F Y') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="text-2xl font-bold text-purple-600">{{ $gelombangCount ?? 0 }}</div>
                <div class="text-sm text-gray-600">Gelombang Ditugaskan</div>
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

    {{-- Quick Action --}}
    @if(($gelombangCount ?? 0) > 0)
    <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
        <div class="flex items-center justify-between">
            <div>
                <h4 class="font-medium text-indigo-900">Mulai Penilaian</h4>
                <p class="text-sm text-indigo-700">Anda memiliki {{ $gelombangCount }} gelombang yang perlu dinilai</p>
            </div>
            <a href="{{ route('penguji.penilaian.index') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                Mulai Menilai →
            </a>
        </div>
    </div>
    @endif

    {{-- Stasi yang Ditugaskan (Legacy) --}}
    @if($assignedStasi->count() > 0)
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold mb-4">Stasi yang Ditugaskan</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                @foreach($assignedStasi as $stasi)
                    <div class="border rounded-lg p-3">
                        <h4 class="font-medium text-gray-900 text-sm">{{ $stasi->nama }}</h4>
                        <p class="text-xs text-gray-500 mt-1">{{ Str::limit($stasi->deskripsi, 30) ?? '-' }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

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
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Gelombang</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($jadwalList as $jadwal)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $jadwal->nama }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $jadwal->mulai->format('d M Y H:i') }} - {{ $jadwal->selesai->format('H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $jadwal->gelombang_count ?? 0 }} gelombang</td>
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
