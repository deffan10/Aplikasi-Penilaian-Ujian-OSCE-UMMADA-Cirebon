<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Nilai OSCE - Kelas {{ $kelas->nama }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 20px;
        }
        .kop-surat {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .kop-surat img {
            max-width: 100%;
            height: auto;
        }
        .info {
            margin-bottom: 15px;
        }
        .info p {
            margin: 3px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #333;
            padding: 4px;
            text-align: center;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        td.left {
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
        .footer {
            margin-top: 30px;
            overflow: hidden;
            page-break-inside: avoid;
        }
        .signature {
            float: right;
            width: 250px;
            text-align: center;
        }
        .signature .date {
            margin-bottom: 70px;
        }
        .signature .name {
            font-weight: bold;
            text-decoration: underline;
        }
        .signature .nik {
            font-size: 9px;
        }
        .print-date {
            font-size: 9px;
            color: #666;
            margin-top: 20px;
            clear: both;
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

    <h2 style="text-align: center; font-size: 14px; margin-bottom: 5px;">REKAP NILAI UJIAN OSCE TAHUN {{ now()->format('Y') }}</h2>

    <div class="info">
        <p><strong>Kelas:</strong> {{ $kelas->nama }}</p>
        <p><strong>Tanggal Cetak:</strong> {{ now()->format('d F Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th style="width: 80px;">NIM</th>
                <th style="width: 120px;">Nama</th>
                @foreach($stasi as $s)
                    <th>{{ $s->nama }}</th>
                @endforeach
                <th style="width: 50px;">Rata-rata</th>
                <th style="width: 60px;">Status</th>
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
                    <td class="left">{{ $mhs->nama }}</td>
                    @foreach($stasi as $s)
                        <td>
                            @if($nilaiPerStasi[$s->id])
                                {{ number_format($nilaiPerStasi[$s->id]->total_nilai, 1) }}
                            @else
                                -
                            @endif
                        </td>
                    @endforeach
                    <td class="{{ $rataRata >= 70 ? 'lulus' : 'tidak-lulus' }}">
                        {{ $countNilai > 0 ? number_format($rataRata, 1) : '-' }}
                    </td>
                    <td class="{{ $statusLulus ? 'lulus' : 'tidak-lulus' }}">
                        @if($countNilai == 0)
                            -
                        @else
                            {{ $statusLulus ? 'LULUS' : 'TIDAK LULUS' }}
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 30px; page-break-inside: avoid;">
        <div style="float: right; width: 250px; text-align: center;">
            <div style="margin-bottom: 10px;">{{ now()->translatedFormat('d F Y') }}</div>
            <div style="margin-bottom: 70px;">{{ !empty($setting->penandatangan_jabatan) ? $setting->penandatangan_jabatan : 'Koordinator OSCE' }}</div>
            <div style="font-weight: bold; text-decoration: underline;">{{ !empty($setting->penandatangan_nama) ? $setting->penandatangan_nama : '____________________' }}</div>
            @if(!empty($setting->penandatangan_nik))
                <div style="font-size: 9px;">{{ $setting->penandatangan_nik }}</div>
            @endif
        </div>
        <div style="clear: both;"></div>
    </div>
    
    <div style="font-size: 9px; color: #666; margin-top: 20px;">
        Dicetak pada: {{ now()->format('d F Y H:i') }}
    </div>
</body>
</html>
