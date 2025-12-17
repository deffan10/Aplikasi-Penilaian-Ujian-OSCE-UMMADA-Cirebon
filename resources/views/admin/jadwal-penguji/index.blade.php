@extends('layouts.app')

@section('title', 'Jadwal Penguji')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h2 class="text-xl font-semibold">Jadwal Penguji</h2>
            <p class="text-gray-500 mt-1">Pilih jadwal ujian untuk mengatur penguji per gelombang</p>
        </div>
    </div>

    {{-- Daftar Jadwal --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            @if($jadwalList->isEmpty())
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="mt-2 text-gray-500">Belum ada jadwal ujian</p>
                    <a href="{{ route('admin.jadwal.create') }}" class="mt-4 inline-block text-indigo-600 hover:text-indigo-800">
                        Buat Jadwal Baru →
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($jadwalList as $jadwal)
                        <a href="{{ route('admin.jadwal-penguji.gelombang', $jadwal) }}" 
                           class="block p-4 border border-gray-200 rounded-lg hover:border-indigo-500 hover:shadow-md transition-all">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="font-semibold text-gray-900">{{ $jadwal->nama }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">
                                        {{ $jadwal->mulai->format('d M Y') }}
                                    </p>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $jadwal->gelombang_count > 0 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $jadwal->gelombang_count }} Gelombang
                                </span>
                            </div>
                            <div class="mt-3 flex items-center text-sm text-gray-500">
                                <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ $jadwal->mulai->format('H:i') }} - {{ $jadwal->selesai->format('H:i') }}
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
