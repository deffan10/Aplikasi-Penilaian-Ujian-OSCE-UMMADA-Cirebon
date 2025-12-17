@extends('layouts.app')

@section('title', 'Pilih Stasi - ' . $gelombang->nama)

@section('content')
<div class="space-y-6">
    {{-- Breadcrumb & Info --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex justify-between items-start">
                <div>
                    <nav class="text-sm text-gray-500 mb-2">
                        <a href="{{ route('penguji.penilaian.index') }}" class="hover:text-indigo-600">Penilaian</a>
                        <span class="mx-2">›</span>
                        <span>{{ $gelombang->nama }}</span>
                    </nav>
                    <h2 class="text-xl font-semibold">{{ $jadwal->nama }}</h2>
                    <p class="text-gray-500 mt-1">
                        {{ $gelombang->nama }}
                        @if($gelombang->waktu_mulai && $gelombang->waktu_selesai)
                            • {{ \Carbon\Carbon::parse($gelombang->waktu_mulai)->format('H:i') }} - 
                            {{ \Carbon\Carbon::parse($gelombang->waktu_selesai)->format('H:i') }}
                        @endif
                    </p>
                </div>
                <a href="{{ route('penguji.penilaian.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300">
                    ← Kembali
                </a>
            </div>
        </div>
    </div>

    {{-- Time Status Alert --}}
    @if(!$canInputNilai)
        @if($statusWaktu['status'] === 'selesai')
            <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-r-lg">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Waktu Penilaian Telah Selesai</h3>
                        <p class="mt-1 text-sm text-red-700">{{ $statusWaktu['message'] }}. Anda tidak dapat lagi menginput atau mengubah nilai.</p>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">{{ $statusWaktu['label'] }}</h3>
                        <p class="mt-1 text-sm text-yellow-700">{{ $statusWaktu['message'] }}</p>
                        @if(isset($statusWaktu['start_time']))
                            <p class="mt-2 text-sm text-yellow-600 font-medium" id="countdown-container">
                                Menunggu waktu dimulai...
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    @endif

    {{-- Pilih Stasi --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold mb-4">Pilih Stasi untuk Menilai</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($stasiProgress as $stasiId => $data)
                    @php
                        $stasi = $data['stasi'];
                        $nilaiCount = $data['nilai_count'];
                        $mhsCount = $data['mahasiswa_count'];
                        $progress = $mhsCount > 0 ? ($nilaiCount / $mhsCount * 100) : 0;
                        $isComplete = $progress >= 100;
                    @endphp
                    <div class="border rounded-lg p-4 {{ $isComplete ? 'bg-green-50 border-green-200' : ($canInputNilai ? 'hover:shadow-md' : 'opacity-75') }} transition-shadow">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h4 class="font-semibold text-gray-900">{{ $stasi->nama }}</h4>
                                @if($stasi->deskripsi)
                                    <p class="text-xs text-gray-500 mt-1">{{ Str::limit($stasi->deskripsi, 50) }}</p>
                                @endif
                            </div>
                            @if($isComplete)
                                <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            @endif
                        </div>

                        {{-- Progress --}}
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Progress:</span>
                                <span class="font-medium {{ $isComplete ? 'text-green-600' : 'text-gray-900' }}">
                                    {{ $nilaiCount }}/{{ $mhsCount }} mahasiswa
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="{{ $isComplete ? 'bg-green-500' : 'bg-indigo-600' }} h-2 rounded-full" 
                                     style="width: {{ min($progress, 100) }}%"></div>
                            </div>
                        </div>

                        {{-- Action --}}
                        <div class="mt-4">
                            @if($statusWaktu['status'] === 'selesai')
                                <div class="w-full text-center bg-red-100 text-red-700 py-2 px-4 rounded-md text-sm font-medium border border-red-200">
                                    Waktu Selesai
                                </div>
                            @elseif($canInputNilai)
                                <a href="{{ route('penguji.penilaian.list', [$gelombang, $stasi]) }}" 
                                   class="block w-full text-center {{ $isComplete ? 'bg-green-600 hover:bg-green-700' : 'bg-indigo-600 hover:bg-indigo-700' }} text-white py-2 px-4 rounded-md text-sm font-medium">
                                    {{ $isComplete ? 'Lihat Nilai' : 'Mulai Menilai' }}
                                </a>
                            @else
                                <div class="w-full text-center bg-yellow-100 text-yellow-700 py-2 px-4 rounded-md text-sm font-medium border border-yellow-200">
                                    Menunggu Waktu
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@if(isset($statusWaktu['start_time']) && !$canInputNilai && $statusWaktu['status'] === 'belum_mulai')
@push('scripts')
<script>
    // Countdown timer
    const startTime = new Date('{{ $statusWaktu['start_time']->toIso8601String() }}').getTime();
    const countdownEl = document.getElementById('countdown-container');
    
    function updateCountdown() {
        const now = new Date().getTime();
        const distance = startTime - now;
        
        if (distance < 0) {
            countdownEl.innerHTML = '<span class="font-medium">Waktu sudah dimulai! Silakan refresh halaman.</span>';
            setTimeout(() => location.reload(), 2000);
            return;
        }
        
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
        countdownEl.innerHTML = `Dimulai dalam: <span class="font-mono font-bold">${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}</span>`;
    }
    
    updateCountdown();
    setInterval(updateCountdown, 1000);
</script>
@endpush
@endif
@endsection
