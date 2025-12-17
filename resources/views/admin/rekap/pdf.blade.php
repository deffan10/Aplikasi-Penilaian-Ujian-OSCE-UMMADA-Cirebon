<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Nilai OSCE - {{ $jadwal->nama }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 9px;
            margin: 0;
            padding: 15px;
        }
        .kop-surat {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .kop-surat img {
            max-width: 100%;
            height: auto;
        }
        .info {
            margin-bottom: 10px;
        }
        .info p {
            margin: 2px 0;
        }
        .nilai-acuan-box {
            background-color: #f5f0ff;
            border: 1px solid #8b5cf6;
            padding: 8px;
            margin-bottom: 10px;
            border-radius: 4px;
        }
        .nilai-acuan-box h4 {
            margin: 0 0 5px 0;
            color: #6d28d9;
            font-size: 10px;
        }
        .nilai-acuan-table {
            width: auto;
            border-collapse: collapse;
        }
        .nilai-acuan-table td {
            border: none;
            padding: 2px 8px;
            text-align: center;
            font-size: 8px;
        }
        .nilai-acuan-table .label {
            font-weight: bold;
            color: #6d28d9;
        }
        table.main {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table.main th, table.main td {
            border: 1px solid #333;
            padding: 3px;
            text-align: center;
            font-size: 8px;
        }
        table.main th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        table.main td.left {
            text-align: left;
        }
        .lulus {
            color: green;
            font-weight: bold;
        }
        .tidak-lulus {
            color: red;
            font-weight: bold;
        }
        .gr-badge {
            font-size: 7px;
            color: #666;
        }
        .footer {
            margin-top: 20px;
            overflow: hidden;
            page-break-inside: avoid;
        }
        .signature {
            float: right;
            width: 200px;
            text-align: center;
        }
        .signature .date {
            margin-bottom: 50px;
        }
        .signature .name {
            font-weight: bold;
            text-decoration: underline;
        }
        .signature .nik {
            font-size: 8px;
        }
        .print-date {
            font-size: 8px;
            color: #666;
            margin-top: 15px;
            clear: both;
        }
        .keterangan {
            font-size: 8px;
            color: #666;
            margin-bottom: 10px;
            padding: 5px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
        }
        .keterangan ul {
            margin: 3px 0;
            padding-left: 15px;
        }
        .keterangan li {
            margin: 2px 0;
        }
    </style>
</head>
<body>
    {{-- Kop Surat dari Gambar --}}
    <div class="kop-surat">
        @if($setting && $setting->kop_surat_path)
            <img src="{{ public_path('storage/' . $setting->kop_surat_path) }}" alt="Kop Surat">
        @else
            <h2>KOP SURAT BELUM DIATUR</h2>
            <p style="color: red; font-size: 9px;">Silakan upload gambar kop surat di menu Pengaturan</p>
        @endif
    </div>

    <h2 style="text-align: center; font-size: 12px; margin-bottom: 5px;">REKAP NILAI UJIAN OSCE TAHUN {{ $jadwal->mulai->format('Y') }}</h2>

    <div class="info">
        <p><strong>Jadwal:</strong> {{ $jadwal->nama }}</p>
        <p><strong>Tanggal:</strong> {{ $jadwal->mulai->format('d F Y H:i') }} - {{ $jadwal->selesai->format('H:i') }}</p>
        <p><strong>Peserta:</strong> {{ $peserta->count() }} mahasiswa</p>
    </div>

    {{-- Keterangan Penilaian --}}
    <div class="keterangan">
        <strong>Keterangan Penilaian (BAB VII - Penetapan Kelulusan):</strong>
        <ul>
            <li><strong>Nilai Aktual per Stasi:</strong> Σ(skor × bobot) - Contoh: skor 3, bobot 2 = 6</li>
            <li><strong>Global Rating (GR):</strong> 1=Tidak Lulus, 2=Borderline, 3=Lulus, 4=Superior</li>
            <li><strong>Nilai Acuan:</strong> Standard setting berbasis regresi linear Global Rating</li>
            <li><strong>Kelulusan:</strong> Total Nilai Aktual ≥ Total Nilai Acuan</li>
        </ul>
    </div>

    {{-- Nilai Acuan per Stasi --}}
    @if(count($nilaiAcuan) > 0)
    <div class="nilai-acuan-box">
        <h4>Nilai Acuan per Stasi (Standard Setting)</h4>
        <table class="nilai-acuan-table">
            <tr>
                @foreach($stasi as $s)
                    <td class="label">{{ $s->nama }}</td>
                @endforeach
                <td class="label" style="border-left: 1px solid #8b5cf6;">TOTAL</td>
            </tr>
            <tr>
                @php $totalAcuanAll = 0; @endphp
                @foreach($stasi as $s)
                    <td>{{ isset($nilaiAcuan[$s->id]) ? number_format($nilaiAcuan[$s->id], 1) : '-' }}</td>
                    @php $totalAcuanAll += $nilaiAcuan[$s->id] ?? 0; @endphp
                @endforeach
                <td style="border-left: 1px solid #8b5cf6; font-weight: bold;">{{ number_format($totalAcuanAll, 1) }}</td>
            </tr>
        </table>
    </div>
    @endif

    <table class="main">
        <thead>
            <tr>
                <th style="width: 20px;" rowspan="2">No</th>
                <th style="width: 60px;" rowspan="2">NIM</th>
                <th style="width: 80px;" rowspan="2">Nama</th>
                <th style="width: 40px;" rowspan="2">Gelombang</th>
                @foreach($stasi as $s)
                    <th colspan="2">{{ $s->nama }}</th>
                @endforeach
                <th style="width: 40px;" rowspan="2">Total Aktual</th>
                <th style="width: 40px;" rowspan="2">Total Acuan</th>
                <th style="width: 45px;" rowspan="2">Status</th>
            </tr>
            <tr>
                @foreach($stasi as $s)
                    <th style="font-size: 7px;">Nilai</th>
                    <th style="font-size: 7px;">Penguji</th>
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
                        $nilai = $mhs->nilai->where('jadwal_id', $jadwal->id)->where('stasi_id', $s->id)->first();
                        $nilaiPerStasi[$s->id] = $nilai;
                        if ($nilai) {
                            $nilaiAktualStasi = $nilai->nilai_aktual ?? $nilai->total_nilai;
                            $totalNilaiAktual += $nilaiAktualStasi;
                            $countNilai++;
                            
                            if (isset($nilaiAcuan[$s->id])) {
                                $totalNilaiAcuanMhs += $nilaiAcuan[$s->id];
                            }
                        }
                    }
                    
                    $statusLulus = ($countNilai > 0 && count($nilaiAcuan) > 0) 
                        ? $totalNilaiAktual >= $totalNilaiAcuanMhs 
                        : null;
                @endphp
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td>{{ $mhs->nim }}</td>
                    <td class="left">{{ $mhs->nama }}</td>
                    <td>{{ $gelombang ? $gelombang->nama : '-' }}</td>
                    @foreach($stasi as $s)
                        @php
                            $nilai = $nilaiPerStasi[$s->id];
                            $pengujiNama = '-';
                            if ($nilai) {
                                $pengujiNama = $nilai->penguji ? $nilai->penguji->name : '-';
                            } elseif ($gelombang) {
                                $gp = $gelombang->pengujiStasi->where('stasi_id', $s->id)->first();
                                $pengujiNama = $gp ? $gp->penguji->name : '-';
                            }
                        @endphp
                        <td>
                            @if($nilai)
                                @php
                                    $nilaiAktualStasi = $nilai->nilai_aktual ?? $nilai->total_nilai;
                                    $acuanStasi = $nilaiAcuan[$s->id] ?? null;
                                    $lulusStasi = $acuanStasi ? $nilaiAktualStasi >= $acuanStasi : $nilaiAktualStasi >= 70;
                                @endphp
                                <span class="{{ $lulusStasi ? 'lulus' : 'tidak-lulus' }}">
                                    {{ number_format($nilaiAktualStasi, 1) }}
                                </span>
                                @if($nilai->globalRating)
                                    <br><span class="gr-badge">GR:{{ $nilai->globalRating->nilai }}</span>
                                @endif
                            @else
                                -
                            @endif
                        </td>
                        <td style="font-size: 6px;">{{ $pengujiNama }}</td>
                    @endforeach
                    <td class="{{ $statusLulus === true ? 'lulus' : ($statusLulus === false ? 'tidak-lulus' : '') }}">
                        {{ $countNilai > 0 ? number_format($totalNilaiAktual, 1) : '-' }}
                    </td>
                    <td>
                        {{ ($countNilai > 0 && count($nilaiAcuan) > 0) ? number_format($totalNilaiAcuanMhs, 1) : '-' }}
                    </td>
                    <td class="{{ $statusLulus ? 'lulus' : 'tidak-lulus' }}">
                        @if($countNilai == 0)
                            -
                        @elseif($statusLulus === null)
                            Pending
                        @else
                            {{ $statusLulus ? 'LULUS' : 'TIDAK LULUS' }}
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Ringkasan --}}
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
    <div style="margin-bottom: 15px;">
        <strong>Ringkasan:</strong> 
        Lulus: <span class="lulus">{{ $lulusCount }}</span> | 
        Tidak Lulus: <span class="tidak-lulus">{{ $tidakLulusCount }}</span>
        @if($pendingCount > 0)
            | Pending: {{ $pendingCount }}
        @endif
    </div>

    <div style="margin-top: 30px; page-break-inside: avoid;">
        <div style="float: right; width: 200px; text-align: center;">
            <div style="margin-bottom: 10px;">{{ now()->translatedFormat('d F Y') }}</div>
            <div style="margin-bottom: 50px;">{{ !empty($setting->penandatangan_jabatan) ? $setting->penandatangan_jabatan : 'Koordinator OSCE' }}</div>
            <div style="font-weight: bold; text-decoration: underline;">{{ !empty($setting->penandatangan_nama) ? $setting->penandatangan_nama : '____________________' }}</div>
            @if(!empty($setting->penandatangan_nik))
                <div style="font-size: 8px;">{{ $setting->penandatangan_nik }}</div>
            @endif
        </div>
        <div style="clear: both;"></div>
    </div>
    
    <div style="font-size: 8px; color: #666; margin-top: 15px;">
        Dicetak pada: {{ now()->format('d F Y H:i') }}
    </div>
</body>
</html>
