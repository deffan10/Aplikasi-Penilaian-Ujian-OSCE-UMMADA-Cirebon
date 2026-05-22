<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="data:,">
    <title>Kartu Peserta - {{ $kela->kode }} - {{ $jadwal->nama }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; }
        .no-print { padding: 15px 20px; background: #f3f4f6; border-bottom: 1px solid #e5e7eb; }
        .no-print button { padding: 8px 20px; background: #4f46e5; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 14px; margin-right: 10px; }
        .no-print button:hover { background: #4338ca; }
        .no-print a { padding: 8px 20px; background: #6b7280; color: white; border: none; border-radius: 5px; text-decoration: none; font-size: 14px; }
        .no-print .info { display: block; margin-top: 10px; color: #6b7280; font-size: 13px; }

        .page {
            width: 210mm; height: 297mm; padding: 10mm; margin: 0 auto;
            display: flex; flex-wrap: wrap; align-content: flex-start; justify-content: center; gap: 4mm;
            page-break-after: always;
        }
        .page:last-child { page-break-after: avoid; }

        .card {
            width: 90mm; height: 130mm; border: 1px solid #333; padding: 5mm;
            display: flex; flex-direction: column; align-items: center; justify-content: flex-start; overflow: hidden;
        }

        .card-header {
            text-align: center; margin-bottom: 4mm; padding-bottom: 3mm; border-bottom: 1px solid #999;
            width: 100%; display: flex; align-items: center; justify-content: center; gap: 2mm;
        }
        .card-header-logo { height: 12mm; width: auto; flex-shrink: 0; }
        .card-header-text { text-align: center; flex: 1; }
        .card-header-line1 { font-size: 14px; font-weight: bold; text-transform: uppercase; }
        .card-header-line2 { font-size: 14px; font-weight: bold; }
        .card-header-line3 { font-size: 14px; }

        .card-body {
            flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center;
            width: 100%; text-align: center;
        }
        .card-foto { width: 28mm; height: 38mm; object-fit: cover; border: 1px solid #ccc; margin-bottom: 4mm; }
        .card-foto-placeholder { width: 28mm; height: 38mm; border: 1px dashed #ccc; display: flex; align-items: center; justify-content: center; margin-bottom: 4mm; background: #f9f9f9; }
        .card-nama { font-size: 26px; font-weight: bold; margin-bottom: 2mm; line-height: 1.3; text-align: center; word-wrap: break-word; max-width: 100%; }
        .card-nim { font-size: 16px; font-family: 'Courier New', monospace; margin-bottom: 2mm; }
        .card-kelas { font-size: 16px; margin-bottom: 2mm; color: #333; }
        .card-jadwal-nama { font-size: 16px; margin-bottom: 1.5mm; color: #333; }
        .card-gelombang { font-size: 16px; margin-bottom: 1.5mm; color: #333; }
        .card-waktu { font-size: 16px; font-weight: bold; color: #000; }

        @media print {
            .no-print { display: none !important; }
            body { margin: 0; padding: 0; }
            .page { margin: 0; padding: 10mm; }
            .card { border: 1px solid #000; }
            @page { size: A4 portrait; margin: 0; }
        }
        @media screen {
            body { background: #e5e7eb; padding: 20px 0; }
            .page { background: white; box-shadow: 0 2px 8px rgba(0,0,0,0.15); margin-bottom: 20px; }
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
            {{ ceil($mahasiswa->count() / 4) }} halaman (4 kartu/halaman)
        </span>
    </div>

    @if($mahasiswa->count() == 0)
        <div style="padding: 40px; text-align: center; color: #666;">
            <p style="font-size: 16px;">Tidak ada mahasiswa dari kelas ini yang terdaftar di jadwal tersebut.</p>
            <p style="margin-top: 10px;">Pastikan mahasiswa sudah di-assign ke gelombang.</p>
        </div>
    @else
        @foreach($mahasiswa->chunk(4) as $pageItems)
            <div class="page">
                @foreach($pageItems as $mhs)
                    <div class="card">
                        <div class="card-header">
                            @if($kartuLogoKiri)
                                <img src="{{ asset('storage/' . $kartuLogoKiri) }}" class="card-header-logo" alt="Logo">
                            @endif
                            <div class="card-header-text">
                                @if($kartuKopLine1)
                                    <div class="card-header-line1">{{ $kartuKopLine1 }}</div>
                                @else
                                    <div class="card-header-line1">Kartu Peserta</div>
                                @endif
                                @if($kartuKopLine2)
                                    <div class="card-header-line2">{{ $kartuKopLine2 }}</div>
                                @endif
                                @if($kartuKopLine3)
                                    <div class="card-header-line3">{{ $kartuKopLine3 }}</div>
                                @endif
                            </div>
                            @if($kartuLogoKanan)
                                <img src="{{ asset('storage/' . $kartuLogoKanan) }}" class="card-header-logo" alt="Logo">
                            @endif
                        </div>

                        <div class="card-body">
                            @if($mhs->foto)
                                <img src="{{ asset('storage/' . $mhs->foto) }}" class="card-foto" alt="Foto">
                            @else
                                <div class="card-foto-placeholder">
                                    <svg width="28" height="28" fill="#bbb" viewBox="0 0 24 24">
                                        <path d="M12 12c2.7 0 5-2.3 5-5s-2.3-5-5-5-5 2.3-5 5 2.3 5 5 5zm0 2c-3.3 0-10 1.7-10 5v3h20v-3c0-3.3-6.7-5-10-5z"/>
                                    </svg>
                                </div>
                            @endif

                            <div class="card-nama">{{ $mhs->nama }}</div>
                            <div class="card-nim">{{ $mhs->nim }}</div>
                            <div class="card-kelas">{{ $kela->kode }} - {{ $kela->nama }}</div>
                            @if($showJadwal)
                                <div class="card-jadwal-nama">{{ $jadwal->nama }}</div>
                            @endif
                            @if(isset($mahasiswaAssignments[$mhs->id]))
                                @if($showGelombang)
                                    <div class="card-gelombang">{{ $mahasiswaAssignments[$mhs->id]->gelombang }}</div>
                                @endif
                                @if($showWaktu && $mahasiswaAssignments[$mhs->id]->jadwal_ujian)
                                    <div class="card-waktu">{{ $mahasiswaAssignments[$mhs->id]->jadwal_ujian }}</div>
                                @endif
                            @endif
                        </div>
                    </div>
                @endforeach

                @for($i = $pageItems->count(); $i < 4; $i++)
                    <div class="card" style="border: 1px dashed #ccc;"></div>
                @endfor
            </div>
        @endforeach
    @endif
</body>
</html>
