@extends('layouts.app')

@section('title', 'Penilaian OSCE')

@section('content')
<div class="space-y-6">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h2 class="text-xl font-semibold mb-6">Penilaian OSCE</h2>

            @if(empty($jadwalList))
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Belum Ada Penugasan</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Anda belum ditugaskan untuk menilai di gelombang manapun.
                    </p>
                </div>
            @else
                @foreach($jadwalList as $jadwalId => $data)
                    <div class="mb-8">
                        {{-- Jadwal Header --}}
                        <div class="flex items-center justify-between mb-4 pb-2 border-b">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ $data['jadwal']->nama }}</h3>
                                <p class="text-sm text-gray-500">
                                    {{ $data['jadwal']->mulai->format('d M Y H:i') }} - {{ $data['jadwal']->selesai->format('d M Y H:i') }}
                                    @if($data['jadwal']->isActive())
                                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Sedang Berlangsung
                                        </span>
                                    @elseif($data['jadwal']->mulai > now())
                                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Akan Datang
                                        </span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        {{-- Gelombang Cards --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($data['gelombang'] as $gelItem)
                                @php
                                    $gelombang = $gelItem['gelombang'];
                                    $stasiList = $gelItem['stasi'];
                                    $mhsCount = $gelItem['mahasiswa_count'];
                                    $nilaiCount = $gelItem['nilai_count'];
                                    $totalTarget = $mhsCount * $stasiList->count();
                                    $progress = $totalTarget > 0 ? ($nilaiCount / $totalTarget * 100) : 0;
                                    
                                    // Get time status
                                    $statusWaktu = $gelombang->getStatusWaktu();
                                    $canInputNilai = $gelombang->canInputNilai();
                                @endphp
                                <div class="border rounded-lg p-4 {{ $canInputNilai ? 'hover:shadow-md' : 'opacity-75 bg-gray-50' }} transition-shadow">
                                    <div class="flex justify-between items-start mb-3">
                                        <div>
                                            <h4 class="font-semibold text-gray-900">{{ $gelombang->nama }}</h4>
                                            @if($gelombang->waktu_mulai && $gelombang->waktu_selesai)
                                                <p class="text-xs text-gray-500">
                                                    {{ \Carbon\Carbon::parse($gelombang->waktu_mulai)->format('H:i') }} - 
                                                    {{ \Carbon\Carbon::parse($gelombang->waktu_selesai)->format('H:i') }}
                                                </p>
                                            @endif
                                        </div>
                                        @if($progress >= 100)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Selesai
                                            </span>
                                        @elseif($statusWaktu['status'] === 'selesai')
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Waktu Habis
                                            </span>
                                        @elseif($statusWaktu['status'] === 'belum_mulai')
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                {{ $statusWaktu['label'] }}
                                            </span>
                                        @elseif($progress > 0)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                In Progress
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Aktif
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Stats --}}
                                    <div class="space-y-2 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Mahasiswa:</span>
                                            <span class="font-medium">{{ $mhsCount }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Stasi Ditugaskan:</span>
                                            <span class="font-medium">{{ $stasiList->count() }}</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-600">Progress:</span>
                                            <span class="font-medium">{{ $nilaiCount }}/{{ $totalTarget }}</span>
                                        </div>
                                        {{-- Progress Bar --}}
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ min($progress, 100) }}%"></div>
                                        </div>
                                    </div>

                                    {{-- Stasi Tags --}}
                                    <div class="mt-3 flex flex-wrap gap-1">
                                        @foreach($stasiList as $stasi)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-700">
                                                {{ $stasi->nama }}
                                            </span>
                                        @endforeach
                                    </div>

                                    {{-- Action --}}
                                    <div class="mt-4">
                                        @if($statusWaktu['status'] === 'selesai')
                                            {{-- Waktu sudah habis --}}
                                            <div class="w-full text-center bg-red-100 text-red-700 py-2 px-4 rounded-md text-sm font-medium border border-red-200">
                                                <svg class="inline-block w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                </svg>
                                                Waktu Penilaian Selesai
                                            </div>
                                        @elseif($statusWaktu['status'] === 'belum_mulai')
                                            {{-- Belum waktunya --}}
                                            <div class="w-full text-center bg-yellow-100 text-yellow-700 py-2 px-4 rounded-md text-sm border border-yellow-200">
                                                <svg class="inline-block w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                                </svg>
                                                Menunggu Waktu
                                            </div>
                                            @if(isset($statusWaktu['start_time']))
                                                <p class="text-xs text-center text-yellow-600 mt-2 font-medium">
                                                    Mulai: {{ $statusWaktu['start_time']->format('d M Y H:i') }}
                                                </p>
                                            @endif
                                        @elseif($canInputNilai)
                                            <a href="{{ route('penguji.penilaian.select-stasi', $gelombang) }}" 
                                               class="block w-full text-center bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 text-sm font-medium">
                                                {{ $progress > 0 ? 'Lanjutkan Penilaian' : 'Mulai Penilaian' }}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
@endsection
