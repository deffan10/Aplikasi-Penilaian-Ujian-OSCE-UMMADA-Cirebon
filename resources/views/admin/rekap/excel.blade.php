<table>
    {{-- Title Row --}}
    <tr>
        <td colspan="{{ 8 + ($stasi->count() * 2) }}" style="font-weight: bold; font-size: 14px; text-align: center;">
            REKAP NILAI UJIAN OSCE TAHUN {{ $jadwal->mulai->format('Y') }}
        </td>
    </tr>
    <tr>
        <td colspan="{{ 8 + ($stasi->count() * 2) }}"></td>
    </tr>
    {{-- Info Rows --}}
    <tr>
        <td colspan="2" style="font-weight: bold;">Jadwal:</td>
        <td colspan="{{ 6 + ($stasi->count() * 2) }}">{{ $jadwal->nama }}</td>
    </tr>
    <tr>
        <td colspan="2" style="font-weight: bold;">Tanggal:</td>
        <td colspan="{{ 6 + ($stasi->count() * 2) }}">{{ $jadwal->mulai->format('d F Y H:i') }} - {{ $jadwal->selesai->format('H:i') }}</td>
    </tr>
    <tr>
        <td colspan="2" style="font-weight: bold;">Jumlah Peserta:</td>
        <td colspan="{{ 6 + ($stasi->count() * 2) }}">{{ $peserta->count() }} mahasiswa</td>
    </tr>
    <tr>
        <td colspan="{{ 8 + ($stasi->count() * 2) }}"></td>
    </tr>
    {{-- Keterangan Penilaian --}}
    <tr>
        <td colspan="{{ 8 + ($stasi->count() * 2) }}" style="font-weight: bold;">Keterangan Penilaian (BAB VII):</td>
    </tr>
    <tr>
        <td colspan="{{ 8 + ($stasi->count() * 2) }}">- Nilai Aktual per Stasi: Σ(skor × bobot) - Contoh: skor 3, bobot 2 = 6</td>
    </tr>
    <tr>
        <td colspan="{{ 8 + ($stasi->count() * 2) }}">- Nilai Acuan: Standard setting berbasis regresi Global Rating (1-4)</td>
    </tr>
    <tr>
        <td colspan="{{ 8 + ($stasi->count() * 2) }}">- Kelulusan: Total Nilai Aktual ≥ Total Nilai Acuan</td>
    </tr>
    <tr>
        <td colspan="{{ 8 + ($stasi->count() * 2) }}">- Multi-Penguji: Jika >1 penguji per stasi, nilai dirata-ratakan</td>
    </tr>
    <tr>
        <td colspan="{{ 8 + ($stasi->count() * 2) }}"></td>
    </tr>
    {{-- Nilai Acuan per Stasi --}}
    @if(count($nilaiAcuan) > 0)
    <tr>
        <td colspan="4" style="font-weight: bold;">Nilai Acuan per Stasi:</td>
        @foreach($stasi as $s)
            <td style="text-align: center;" colspan="2">{{ $s->nama }}</td>
        @endforeach
        <td colspan="4"></td>
    </tr>
    <tr>
        <td colspan="4"></td>
        @foreach($stasi as $s)
            <td style="text-align: center; font-weight: bold;" colspan="2">{{ isset($nilaiAcuan[$s->id]) ? number_format($nilaiAcuan[$s->id], 1) : '-' }}</td>
        @endforeach
        <td colspan="4"></td>
    </tr>
    <tr>
        <td colspan="{{ 8 + ($stasi->count() * 2) }}"></td>
    </tr>
    @endif
    <thead>
        <tr>
            <th style="font-weight: bold; background-color: #f0f0f0;" rowspan="2">No</th>
            <th style="font-weight: bold; background-color: #f0f0f0;" rowspan="2">NIM</th>
            <th style="font-weight: bold; background-color: #f0f0f0;" rowspan="2">Nama</th>
            <th style="font-weight: bold; background-color: #f0f0f0;" rowspan="2">Gelombang</th>
            @foreach($stasi as $s)
                <th style="font-weight: bold; background-color: #f0f0f0; text-align: center;" colspan="2">{{ $s->nama }}</th>
            @endforeach
            <th style="font-weight: bold; background-color: #f0f0f0; text-align: center;" rowspan="2">Total Aktual</th>
            <th style="font-weight: bold; background-color: #f0f0f0; text-align: center;" rowspan="2">Total Acuan</th>
            <th style="font-weight: bold; background-color: #f0f0f0; text-align: center;" rowspan="2">Selisih</th>
            <th style="font-weight: bold; background-color: #f0f0f0; text-align: center;" rowspan="2">Status</th>
        </tr>
        <tr>
            @foreach($stasi as $s)
                <th style="font-weight: bold; background-color: #e0e0e0; text-align: center;">Nilai</th>
                <th style="font-weight: bold; background-color: #e0e0e0; text-align: center;">Penguji</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($peserta as $idx => $mhs)
            @php
                $nilaiPerStasi = [];
                $totalNilaiAktual = 0;
                $totalNilaiAcuanMhs = 0;
                $countNilai = 0;
                $gelombang = $mahasiswaGelombang[$mhs->id] ?? null;
                
                foreach($stasi as $s) {
                    // Get ALL nilai for multi-penguji support
                    $allNilaiStasi = $mhs->nilai->where('jadwal_id', $jadwal->id)->where('stasi_id', $s->id);
                    
                    if ($allNilaiStasi->count() > 0) {
                        $avgNilaiAktual = $allNilaiStasi->avg(function($n) {
                            return $n->nilai_aktual ?? $n->total_nilai;
                        });
                        $avgGlobalRating = $allNilaiStasi->avg(function($n) {
                            return $n->globalRating ? $n->globalRating->nilai : null;
                        });
                        
                        $nilaiPerStasi[$s->id] = [
                            'avg_nilai_aktual' => round($avgNilaiAktual, 2),
                            'avg_global_rating' => $avgGlobalRating ? round($avgGlobalRating, 1) : null,
                            'penguji_count' => $allNilaiStasi->count(),
                            'penguji_names' => $allNilaiStasi->map(fn($n) => $n->penguji ? $n->penguji->name : '-')->implode(', '),
                        ];
                        
                        $totalNilaiAktual += $avgNilaiAktual;
                        $countNilai++;
                        
                        if (isset($nilaiAcuan[$s->id])) {
                            $totalNilaiAcuanMhs += $nilaiAcuan[$s->id];
                        }
                    } else {
                        $nilaiPerStasi[$s->id] = null;
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
                <td>{{ $gelombang ? $gelombang->nama : '-' }}</td>
                @foreach($stasi as $s)
                    @php
                        $data = $nilaiPerStasi[$s->id];
                        $pengujiNama = '-';
                        if ($data) {
                            $pengujiNama = $data['penguji_names'];
                        } elseif ($gelombang) {
                            $gp = $gelombang->pengujiStasi->where('stasi_id', $s->id);
                            $pengujiNama = $gp->map(fn($p) => $p->penguji->name)->implode(', ') ?: '-';
                        }
                    @endphp
                    <td style="text-align: center;">
                        @if($data)
                            {{ number_format($data['avg_nilai_aktual'], 1) }}{{ $data['penguji_count'] > 1 ? '*' : '' }}
                            @if($data['avg_global_rating'])
                                (GR:{{ $data['penguji_count'] > 1 ? number_format($data['avg_global_rating'], 1) : number_format($data['avg_global_rating'], 0) }})
                            @endif
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $pengujiNama }}</td>
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
        <td colspan="{{ 8 + ($stasi->count() * 2) }}"></td>
    </tr>
    <tr>
        <td colspan="4" style="font-weight: bold;">Ringkasan:</td>
        <td colspan="{{ 4 + ($stasi->count() * 2) }}"></td>
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
                    $nilaiStasi = $nilaiMhs->where('stasi_id', $s->id);
                    if ($nilaiStasi->count() > 0 && isset($nilaiAcuan[$s->id])) {
                        $avgVal = $nilaiStasi->avg(function($n) {
                            return $n->nilai_aktual ?? $n->total_nilai;
                        });
                        $totalAktual += $avgVal;
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
        <td colspan="2">{{ $lulusCount }} mahasiswa</td>
        <td colspan="{{ 4 + ($stasi->count() * 2) }}"></td>
    </tr>
    <tr>
        <td colspan="2">Tidak Lulus:</td>
        <td colspan="2">{{ $tidakLulusCount }} mahasiswa</td>
        <td colspan="{{ 4 + ($stasi->count() * 2) }}"></td>
    </tr>
    <tr>
        <td colspan="2">Menunggu Nilai Acuan:</td>
        <td colspan="2">{{ $pendingCount }} mahasiswa</td>
        <td colspan="{{ 4 + ($stasi->count() * 2) }}"></td>
    </tr>
    <tr>
        <td colspan="{{ 8 + ($stasi->count() * 2) }}"></td>
    </tr>
    <tr>
        <td colspan="{{ 8 + ($stasi->count() * 2) }}">* = Nilai rata-rata dari beberapa penguji</td>
    </tr>
</table>
