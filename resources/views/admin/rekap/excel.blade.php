<table>
    {{-- Title Row --}}
    <tr>
        <td colspan="{{ 7 + $stasi->count() }}" style="font-weight: bold; font-size: 14px; text-align: center;">
            REKAP NILAI UJIAN OSCE TAHUN {{ $jadwal->mulai->format('Y') }}
        </td>
    </tr>
    <tr>
        <td colspan="{{ 7 + $stasi->count() }}"></td>
    </tr>
    {{-- Info Rows --}}
    <tr>
        <td colspan="2" style="font-weight: bold;">Jadwal:</td>
        <td colspan="{{ 5 + $stasi->count() }}">{{ $jadwal->nama }}</td>
    </tr>
    <tr>
        <td colspan="2" style="font-weight: bold;">Tanggal:</td>
        <td colspan="{{ 5 + $stasi->count() }}">{{ $jadwal->mulai->format('d F Y H:i') }} - {{ $jadwal->selesai->format('H:i') }}</td>
    </tr>
    <tr>
        <td colspan="2" style="font-weight: bold;">Jumlah Peserta:</td>
        <td colspan="{{ 5 + $stasi->count() }}">{{ $peserta->count() }} mahasiswa</td>
    </tr>
    <tr>
        <td colspan="{{ 7 + $stasi->count() }}"></td>
    </tr>
    {{-- Keterangan Penilaian --}}
    <tr>
        <td colspan="{{ 7 + $stasi->count() }}" style="font-weight: bold;">Keterangan Penilaian (BAB VII):</td>
    </tr>
    <tr>
        <td colspan="{{ 7 + $stasi->count() }}">- Nilai Aktual per Stasi: Σ(skor × bobot) - Contoh: skor 3, bobot 2 = 6</td>
    </tr>
    <tr>
        <td colspan="{{ 7 + $stasi->count() }}">- Nilai Acuan: Standard setting berbasis regresi Global Rating (1-4)</td>
    </tr>
    <tr>
        <td colspan="{{ 7 + $stasi->count() }}">- Kelulusan: Total Nilai Aktual ≥ Total Nilai Acuan</td>
    </tr>
    <tr>
        <td colspan="{{ 7 + $stasi->count() }}"></td>
    </tr>
    {{-- Nilai Acuan per Stasi --}}
    @if(count($nilaiAcuan) > 0)
    <tr>
        <td colspan="3" style="font-weight: bold;">Nilai Acuan per Stasi:</td>
        @foreach($stasi as $s)
            <td style="text-align: center;">{{ $s->nama }}</td>
        @endforeach
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td colspan="3"></td>
        @foreach($stasi as $s)
            <td style="text-align: center; font-weight: bold;">{{ isset($nilaiAcuan[$s->id]) ? number_format($nilaiAcuan[$s->id], 1) : '-' }}</td>
        @endforeach
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td colspan="{{ 7 + $stasi->count() }}"></td>
    </tr>
    @endif
    <thead>
        <tr>
            <th style="font-weight: bold; background-color: #f0f0f0;">No</th>
            <th style="font-weight: bold; background-color: #f0f0f0;">NIM</th>
            <th style="font-weight: bold; background-color: #f0f0f0;">Nama</th>
            @foreach($stasi as $s)
                <th style="font-weight: bold; background-color: #f0f0f0; text-align: center;">{{ $s->nama }}</th>
            @endforeach
            <th style="font-weight: bold; background-color: #f0f0f0; text-align: center;">Total Aktual</th>
            <th style="font-weight: bold; background-color: #f0f0f0; text-align: center;">Total Acuan</th>
            <th style="font-weight: bold; background-color: #f0f0f0; text-align: center;">Selisih</th>
            <th style="font-weight: bold; background-color: #f0f0f0; text-align: center;">Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($peserta as $idx => $mhs)
            @php
                $nilaiPerStasi = [];
                $totalNilaiAktual = 0;
                $totalNilaiAcuanMhs = 0;
                $countNilai = 0;
                
                foreach($stasi as $s) {
                    $nilai = $mhs->nilai->where('jadwal_id', $jadwal->id)->where('stasi_id', $s->id)->first();
                    $nilaiPerStasi[$s->id] = $nilai;
                    if ($nilai) {
                        // Use nilai_aktual if available, fallback to total_nilai
                        $nilaiAktualStasi = $nilai->nilai_aktual ?? $nilai->total_nilai;
                        $totalNilaiAktual += $nilaiAktualStasi;
                        $countNilai++;
                        
                        // Sum nilai acuan for stasi that have nilai
                        if (isset($nilaiAcuan[$s->id])) {
                            $totalNilaiAcuanMhs += $nilaiAcuan[$s->id];
                        }
                    }
                }
                
                // Kelulusan: Total Nilai Aktual >= Total Nilai Acuan
                $statusLulus = ($countNilai > 0 && count($nilaiAcuan) > 0) 
                    ? $totalNilaiAktual >= $totalNilaiAcuanMhs 
                    : null;
                $selisih = $totalNilaiAktual - $totalNilaiAcuanMhs;
            @endphp
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td>{{ $mhs->nim }}</td>
                <td>{{ $mhs->nama }}</td>
                @foreach($stasi as $s)
                    <td style="text-align: center;">
                        @if($nilaiPerStasi[$s->id])
                            {{ number_format($nilaiPerStasi[$s->id]->nilai_aktual ?? $nilaiPerStasi[$s->id]->total_nilai, 1) }}
                            @if($nilaiPerStasi[$s->id]->globalRating)
                                (GR:{{ $nilaiPerStasi[$s->id]->globalRating->nilai }})
                            @endif
                        @else
                            -
                        @endif
                    </td>
                @endforeach
                <td style="text-align: center; font-weight: bold;">{{ $countNilai > 0 ? number_format($totalNilaiAktual, 1) : '-' }}</td>
                <td style="text-align: center;">{{ ($countNilai > 0 && count($nilaiAcuan) > 0) ? number_format($totalNilaiAcuanMhs, 1) : '-' }}</td>
                <td style="text-align: center; {{ $selisih >= 0 ? 'color: green;' : 'color: red;' }}">
                    {{ ($countNilai > 0 && count($nilaiAcuan) > 0) ? ($selisih >= 0 ? '+' : '') . number_format($selisih, 1) : '-' }}
                </td>
                <td style="text-align: center; font-weight: bold; {{ $statusLulus ? 'color: green;' : 'color: red;' }}">
                    @if($countNilai == 0)
                        -
                    @elseif($statusLulus === null)
                        Menunggu
                    @else
                        {{ $statusLulus ? 'LULUS' : 'TIDAK LULUS' }}
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
    {{-- Summary Row --}}
    <tr>
        <td colspan="{{ 7 + $stasi->count() }}"></td>
    </tr>
    <tr>
        <td colspan="3" style="font-weight: bold;">Ringkasan:</td>
        <td colspan="{{ 4 + $stasi->count() }}"></td>
    </tr>
    @php
        $lulusCount = 0;
        $tidakLulusCount = 0;
        $pendingCount = 0;
        
        foreach($peserta as $mhs) {
            $nilaiMhs = $mhs->nilai->where('jadwal_id', $jadwal->id);
            if ($nilaiMhs->count() > 0 && count($nilaiAcuan) > 0) {
                $totalAktual = 0;
                $totalAcuan = 0;
                foreach($stasi as $s) {
                    $n = $nilaiMhs->where('stasi_id', $s->id)->first();
                    if ($n && isset($nilaiAcuan[$s->id])) {
                        $totalAktual += $n->nilai_aktual ?? $n->total_nilai;
                        $totalAcuan += $nilaiAcuan[$s->id];
                    }
                }
                if ($totalAktual >= $totalAcuan) {
                    $lulusCount++;
                } else {
                    $tidakLulusCount++;
                }
            } elseif ($nilaiMhs->count() > 0) {
                $pendingCount++;
            }
        }
    @endphp
    <tr>
        <td colspan="2">Lulus:</td>
        <td>{{ $lulusCount }} mahasiswa</td>
        <td colspan="{{ 4 + $stasi->count() }}"></td>
    </tr>
    <tr>
        <td colspan="2">Tidak Lulus:</td>
        <td>{{ $tidakLulusCount }} mahasiswa</td>
        <td colspan="{{ 4 + $stasi->count() }}"></td>
    </tr>
    <tr>
        <td colspan="2">Menunggu Nilai Acuan:</td>
        <td>{{ $pendingCount }} mahasiswa</td>
        <td colspan="{{ 4 + $stasi->count() }}"></td>
    </tr>
</table>
