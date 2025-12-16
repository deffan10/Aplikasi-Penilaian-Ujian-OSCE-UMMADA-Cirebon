<table>
    {{-- Title Row --}}
    <tr>
        <td colspan="{{ 5 + $stasi->count() }}" style="font-weight: bold; font-size: 14px; text-align: center;">
            REKAP NILAI UJIAN OSCE TAHUN {{ now()->format('Y') }}
        </td>
    </tr>
    <tr>
        <td colspan="{{ 5 + $stasi->count() }}"></td>
    </tr>
    {{-- Info Rows --}}
    <tr>
        <td colspan="2" style="font-weight: bold;">Kelas:</td>
        <td colspan="{{ 3 + $stasi->count() }}">{{ $kelas->nama }} ({{ $kelas->kode }})</td>
    </tr>
    <tr>
        <td colspan="2" style="font-weight: bold;">Jumlah Mahasiswa:</td>
        <td colspan="{{ 3 + $stasi->count() }}">{{ $mahasiswa->count() }} mahasiswa</td>
    </tr>
    <tr>
        <td colspan="2" style="font-weight: bold;">Tanggal Cetak:</td>
        <td colspan="{{ 3 + $stasi->count() }}">{{ now()->format('d F Y H:i') }}</td>
    </tr>
    <tr>
        <td colspan="{{ 5 + $stasi->count() }}"></td>
    </tr>
    <thead>
        <tr>
            <th>No</th>
            <th>NIM</th>
            <th>Nama</th>
            @foreach($stasi as $s)
                <th>{{ $s->nama }}</th>
            @endforeach
            <th>Rata-rata</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($mahasiswa as $idx => $mhs)
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
                <td>{{ $idx + 1 }}</td>
                <td>{{ $mhs->nim }}</td>
                <td>{{ $mhs->nama }}</td>
                @foreach($stasi as $s)
                    <td>{{ $nilaiPerStasi[$s->id] ? number_format($nilaiPerStasi[$s->id]->total_nilai, 1) : '-' }}</td>
                @endforeach
                <td>{{ $countNilai > 0 ? number_format($rataRata, 1) : '-' }}</td>
                <td>{{ $countNilai == 0 ? '-' : ($statusLulus ? 'LULUS' : 'TIDAK LULUS') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
