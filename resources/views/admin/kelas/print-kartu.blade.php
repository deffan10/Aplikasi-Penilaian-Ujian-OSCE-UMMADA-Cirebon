<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="data:,">
    <title>Kartu Peserta - {{ $kela->kode }} - {{ $jadwal->nama }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
        }

        .no-print {
            padding: 15px 20px;
            background: #f3f4f6;
            border-bottom: 1px solid #e5e7eb;
        }

        .no-print button {
            padding: 8px 20px;
            background: #4f46e5;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            margin-right: 10px;
        }

        .no-print button:hover {
            background: #4338ca;
        }

        .no-print a {
            padding: 8px 20px;
            background: #6b7280;
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
        }

        .no-print .info {
            display: block;
            margin-top: 10px;
            color: #6b7280;
            font-size: 13px;
        }

        /* A4: 210mm x 297mm, padding 10mm = usable 190mm x 277mm */
        /* Card B2 portrait: 62mm x 105mm, 3 cols x 2 rows = 6 per page */
        .page {
            width: 210mm;
            height: 297mm;
            padding: 10mm;
            margin: 0 auto;
            display: flex;
            flex-wrap: wrap;
            align-content: flex-start;
            justify-content: space-between;
            gap: 0;
            page-break-after: always;
        }

        .page:last-child {
            page-break-after: avoid;
        }

        /* Card B2 portrait fixed size: 62mm x 133mm */
        .card {
            width: 62mm;
            height: 133mm;
            border: 1px solid #333;
            padding: 4mm;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            overflow: hidden;
            margin-bottom: 2mm;
        }

        .card-header {
            text-align: center;
            margin-bottom: 3mm;
            padding-bottom: 2mm;
            border-bottom: 1px solid #999;
            width: 100%;
        }

        .card-header-line1 {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .card-header-line2 {
            font-size: 8px;
            font-weight: bold;
        }

        .card-header-line3 {
            font-size: 7.5px;
        }

        .card-body {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 100%;
            text-align: center;
        }

        .card-foto {
            width: 22mm;
            height: 30mm;
            object-fit: cover;
            border: 1px solid #ccc;
            margin-bottom: 3mm;
        }

        .card-foto-placeholder {
            width: 22mm;
            height: 30mm;
            border: 1px dashed #ccc;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 3mm;
            background: #f9f9f9;
        }

        .card-nama {
            font-size: 9px;
            font-weight: bold;
            margin-bottom: 1.5mm;
            line-height: 1.2;
        }

        .card-nim {
            font-size: 8.5px;
            font-family: 'Courier New', monospace;
            margin-bottom: 1.5mm;
        }

        .card-kelas {
            font-size: 7.5px;
            margin-bottom: 1.5mm;
            color: #333;
        }

        .card-jadwal-nama {
            font-size: 7.5px;
            margin-bottom: 1mm;
            color: #333;
        }

        .card-gelombang {
            font-size: 7.5px;
            margin-bottom: 1mm;
            color: #333;
        }

        .card-waktu {
            font-size: 7.5px;
            font-weight: bold;
            color: #000;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                margin: 0;
                padding: 0;
            }

            .page {
                margin: 0;
                padding: 10mm;
            }

            .card {
                border: 1px solid #000;
            }

            @page {
                size: A4 portrait;
                margin: 0;
            }
        }

        @media screen {
            body {
                background: #e5e7eb;
                padding: 20px 0;
            }

            .page {
                background: white;
                box-shadow: 0 2px 8px rgba(0,0,0,0.15);
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()">🖨️ Print Kartu</button>
        <a href="javascript:window.close()">✕ Tutup</a>
        <span class="info">
            Kelas: <strong>{{ $kela->kode }}</strong> | 
            Jadwal: <strong>{{ $jadwal->nama }}</strong> ({{ $jadwal->mulai ? $jadwal->mulai->format('d M Y') : '-' }}) |
            Total: {{ $mahasiswa->count() }} peserta |
            {{ ceil($mahasiswa->count() / 6) }} halaman (6 kartu/halaman)
        </span>
    </div>

    @if($mahasiswa->count() == 0)
        <div style="padding: 40px; text-align: center; color: #666;">
            <p style="font-size: 16px;">Tidak ada mahasiswa dari kelas ini yang terdaftar di jadwal tersebut.</p>
            <p style="margin-top: 10px;">Pastikan mahasiswa sudah di-assign ke gelombang.</p>
        </div>
    @else
        @foreach($mahasiswa->chunk(6) as $pageItems)
            <div class="page">
                @foreach($pageItems as $mhs)
                    <div class="card">
                        <div class="card-header">
                            <div class="card-header-line1">Kartu Peserta</div>
                            <div class="card-header-line2">OSCE Farmasi</div>
                            <div class="card-header-line3">Mahasiswa Farmasi Vokasi D3</div>
                        </div>

                        <div class="card-body">
                            @if($mhs->foto)
                                <img src="{{ asset('storage/' . $mhs->foto) }}" class="card-foto" alt="Foto">
                            @else
                                <div class="card-foto-placeholder">
                                    <svg width="24" height="24" fill="#bbb" viewBox="0 0 24 24">
                                        <path d="M12 12c2.7 0 5-2.3 5-5s-2.3-5-5-5-5 2.3-5 5 2.3 5 5 5zm0 2c-3.3 0-10 1.7-10 5v3h20v-3c0-3.3-6.7-5-10-5z"/>
                                    </svg>
                                </div>
                            @endif

                            <div class="card-nama">{{ $mhs->nama }}</div>
                            <div class="card-nim">{{ $mhs->nim }}</div>
                            <div class="card-kelas">{{ $kela->kode }} - {{ $kela->nama }}</div>
                            <div class="card-jadwal-nama">{{ $jadwal->nama }}</div>
                            @if(isset($mahasiswaAssignments[$mhs->id]))
                                <div class="card-gelombang">{{ $mahasiswaAssignments[$mhs->id]->gelombang }}</div>
                                @if($mahasiswaAssignments[$mhs->id]->jadwal_ujian)
                                    <div class="card-waktu">{{ $mahasiswaAssignments[$mhs->id]->jadwal_ujian }}</div>
                                @endif
                            @endif
                        </div>
                    </div>
                @endforeach

                {{-- Fill empty cards --}}
                @for($i = $pageItems->count(); $i < 6; $i++)
                    <div class="card" style="border: 1px dashed #ccc;"></div>
                @endfor
            </div>
        @endforeach
    @endif
</body>
</html>
