@extends('layouts.app')

@section('title', 'Rekap Kelas: ' . $kelas->nama)

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
                <div>
                    <h2 class="text-xl font-semibold">Rekap Kelas: {{ $kelas->nama }}</h2>
                    <p class="text-gray-500 mt-1">Total {{ $mahasiswa->count() }} mahasiswa</p>
                </div>
                <div class="flex flex-wrap gap-2 w-full sm:w-auto">
                    <a href="{{ route('admin.rekap.kelas.pdf', $kelas) }}" target="_blank" class="w-full sm:w-auto text-center bg-red-600 text-white px-3 py-2 rounded-md hover:bg-red-700 text-sm">
                        Export PDF
                    </a>
                    <a href="{{ route('admin.rekap.kelas.excel', $kelas) }}" class="w-full sm:w-auto text-center bg-green-600 text-white px-3 py-2 rounded-md hover:bg-green-700 text-sm">
                        Export Excel
                    </a>
                    <a href="{{ route('admin.rekap.index') }}" class="w-full sm:w-auto text-center bg-gray-200 text-gray-700 px-3 py-2 rounded-md hover:bg-gray-300 text-sm">
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Rekap --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIM</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                            @foreach($stasi as $s)
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">{{ $s->nama }}</th>
                            @endforeach
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Rata-rata</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($mahasiswa as $idx => $mhs)
                            @php
                                $nilaiPerStasi = [];
                                $totalNilai = 0;
                                $countNilai = 0;
                                $globalRatings = [];
                                
                                foreach($stasi as $s) {
                                    $nilai = $mhs->nilai->where('stasi_id', $s->id)->first();
                                    $nilaiPerStasi[$s->id] = $nilai;
                                    if ($nilai) {
                                        $totalNilai += $nilai->total_nilai;
                                        $countNilai++;
                                        if ($nilai->globalRating) {
                                            $globalRatings[] = $nilai->globalRating->kode;
                                        }
                                    }
                                }
                                
                                $rataRata = $countNilai > 0 ? $totalNilai / $countNilai : 0;
                                $tidakLulusCount = collect($globalRatings)->filter(fn($gr) => $gr === 'TIDAK_LULUS')->count();
                                $statusLulus = $rataRata >= 70 && $tidakLulusCount == 0;
                            @endphp
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $idx + 1 }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">{{ $mhs->nim }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $mhs->nama }}</td>
                                @foreach($stasi as $s)
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-center">
                                        @if($nilaiPerStasi[$s->id])
                                            <div class="{{ $nilaiPerStasi[$s->id]->total_nilai >= 70 ? 'text-green-600' : 'text-red-600' }} font-medium">
                                                {{ number_format($nilaiPerStasi[$s->id]->total_nilai, 1) }}
                                            </div>
                                            @if($nilaiPerStasi[$s->id]->globalRating)
                                                <div class="text-xs text-gray-500">{{ $nilaiPerStasi[$s->id]->globalRating->nama }}</div>
                                            @endif
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                @endforeach
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-center font-bold {{ $rataRata >= 70 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $countNilai > 0 ? number_format($rataRata, 1) : '-' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-center">
                                    @if($countNilai == 0)
                                        <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Belum Dinilai</span>
                                    @elseif($statusLulus)
                                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">LULUS</span>
                                    @else
                                        <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">TIDAK LULUS</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 5 + $stasi->count() }}" class="px-4 py-3 text-center text-gray-500">Belum ada mahasiswa.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
