@extends('layouts.app')

@section('title', 'Daftar Peserta - ' . $stasi->nama)

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex justify-between items-start">
                <div>
                    <nav class="text-sm text-gray-500 mb-2">
                        <a href="{{ route('penguji.penilaian.index') }}" class="hover:text-indigo-600">Penilaian</a>
                        <span class="mx-2">›</span>
                        <a href="{{ route('penguji.penilaian.select-stasi', $gelombang) }}" class="hover:text-indigo-600">{{ $gelombang->nama }}</a>
                        <span class="mx-2">›</span>
                        <span>{{ $stasi->nama }}</span>
                    </nav>
                    <h2 class="text-xl font-semibold">{{ $jadwal->nama }}</h2>
                    <p class="text-gray-500 mt-1">
                        Gelombang: {{ $gelombang->nama }} | 
                        Stasi: {{ $stasi->nama }}
                        @if($gelombang->waktu_mulai && $gelombang->waktu_selesai)
                            | Waktu: {{ \Carbon\Carbon::parse($gelombang->waktu_mulai)->format('H:i') }} - 
                            {{ \Carbon\Carbon::parse($gelombang->waktu_selesai)->format('H:i') }}
                        @endif
                    </p>
                </div>
                <a href="{{ route('penguji.penilaian.select-stasi', $gelombang) }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300">
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

    {{-- Progress --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            @php
                $sudahDinilai = count($existingNilai);
            @endphp
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-700">Progress Penilaian</span>
                <span class="text-sm text-gray-500">{{ $sudahDinilai }} / {{ $peserta->count() }} mahasiswa</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2.5">
                <div class="bg-indigo-600 h-2.5 rounded-full" style="width: {{ $peserta->count() > 0 ? ($sudahDinilai / $peserta->count() * 100) : 0 }}%"></div>
            </div>
            @if($sudahDinilai == $peserta->count() && $peserta->count() > 0)
                <p class="text-sm text-green-600 mt-2">✓ Semua mahasiswa di gelombang ini sudah dinilai untuk stasi {{ $stasi->nama }}.</p>
            @endif
        </div>
    </div>

    {{-- Daftar Peserta --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold mb-4">Daftar Peserta {{ $gelombang->nama }}</h3>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIM</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kelas</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($peserta as $idx => $mhs)
                            @php
                                $sudahNilai = in_array($mhs->id, $existingNilai);
                            @endphp
                            <tr class="{{ $sudahNilai ? 'bg-green-50' : '' }}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $idx + 1 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $mhs->nim }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $mhs->nama }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $mhs->kelas->nama ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                    @if($sudahNilai)
                                        <span class="inline-flex items-center px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                            Sudah Dinilai
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Belum Dinilai</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                    @if($statusWaktu['status'] === 'selesai')
                                        <span class="inline-flex items-center px-3 py-1.5 text-sm font-medium rounded-md text-red-600 bg-red-100">
                                            Waktu Selesai
                                        </span>
                                    @elseif($canInputNilai)
                                        <a href="{{ route('penguji.penilaian.form', ['gelombang' => $gelombang, 'stasi' => $stasi, 'mahasiswa' => $mhs]) }}" 
                                           class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md {{ $sudahNilai ? 'text-indigo-700 bg-indigo-100 hover:bg-indigo-200' : 'text-white bg-indigo-600 hover:bg-indigo-700' }}">
                                            {{ $sudahNilai ? 'Edit Nilai' : 'Nilai Sekarang' }}
                                        </a>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1.5 text-sm font-medium rounded-md text-yellow-700 bg-yellow-100">
                                            Menunggu Waktu
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada peserta di gelombang ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
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
