<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Label Penguji</title>
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

        /* Print button - hide when printing */
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

        .no-print a:hover {
            background: #4b5563;
        }

        .no-print .info {
            margin-left: 15px;
            color: #6b7280;
            font-size: 13px;
        }

        /* Page layout - A4: 210mm x 297mm, margin 5mm each side = 200mm x 287mm usable */
        .page {
            width: 210mm;
            height: 297mm;
            padding: 5mm;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-template-rows: repeat(5, 1fr);
            gap: 0;
            page-break-after: always;
        }

        .page:last-child {
            page-break-after: avoid;
        }

        /* Each label: 100mm x 57.4mm (200mm/2 cols, 287mm/5 rows) */
        .label {
            width: 100mm;
            height: 57.4mm;
            border: 1px solid #333;
            padding: 4mm;
            display: flex;
            flex-direction: column;
            justify-content: center;
            overflow: hidden;
        }

        .label-header {
            text-align: center;
            margin-bottom: 2mm;
            padding-bottom: 2mm;
            border-bottom: 1px solid #333;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 2mm;
        }

        .label-header-logo {
            height: 10mm;
            width: auto;
            flex-shrink: 0;
        }

        .label-header-text {
            text-align: center;
        }

        .label-header-line1 {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .label-header-line2 {
            font-size: 8px;
            font-weight: bold;
        }

        .label-header-line3 {
            font-size: 8px;
        }

        .label-body {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .label-row {
            display: flex;
            margin-bottom: 1.5mm;
            align-items: baseline;
        }

        .label-key {
            font-size: 10px;
            font-weight: bold;
            width: 22mm;
            flex-shrink: 0;
        }

        .label-value {
            font-size: 11px;
            font-family: 'Courier New', monospace;
        }

        .label-stasi {
            margin-top: 1mm;
            padding-top: 2mm;
            border-top: 1px dashed #ccc;
        }

        .label-stasi .stasi-badge {
            display: inline-block;
            border: 1px solid #666;
            padding: 1px 5px;
            border-radius: 3px;
            font-size: 9px;
            margin-right: 2px;
        }

        .label-assignment {
            margin-top: 1.5mm;
            padding-top: 2mm;
            border-top: 1px dashed #999;
        }

        .assign-row {
            display: flex;
            gap: 2px;
            margin-bottom: 1px;
            flex-wrap: wrap;
        }

        .assign-badge {
            display: inline-block;
            padding: 0.5px 4px;
            border-radius: 2px;
            font-size: 7.5px;
            border: 0.5px solid #666;
        }

        .assign-badge.stasi {
            font-weight: bold;
        }

        .assign-badge.gelombang {
            font-style: italic;
        }

        .assign-badge.jadwal {
            color: #444;
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
                padding: 5mm;
            }

            .label {
                border: 1px solid #000;
            }

            @page {
                size: A4 portrait;
                margin: 0;
            }
        }

        /* Screen preview styling */
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
        <button onclick="window.print()">🖨️ Print Labels</button>
        <a href="{{ route('admin.penguji.index') }}">← Kembali</a>
        <span class="info">
            Total: {{ $penguji->count() }} penguji | {{ ceil($penguji->count() / 10) }} halaman (10 label/halaman)
        </span>
    </div>

    @foreach($penguji->chunk(10) as $pageItems)
        <div class="page">
            @foreach($pageItems as $p)
                <div class="label">
                    <div class="label-header">
                        @if($labelLogo)
                            <img src="{{ asset('storage/' . $labelLogo) }}" class="label-header-logo" alt="Logo">
                        @endif
                        <div class="label-header-text">
                            @if($labelLine1)
                                <div class="label-header-line1">{{ $labelLine1 }}</div>
                            @endif
                            @if($labelLine2)
                                <div class="label-header-line2">{{ $labelLine2 }}</div>
                            @endif
                            @if($labelLine3)
                                <div class="label-header-line3">{{ $labelLine3 }}</div>
                            @endif
                            @if(!$labelLine1 && !$labelLine2 && !$labelLine3)
                                <div class="label-header-line1">UJIAN OSCE - Login Penguji</div>
                            @endif
                        </div>
                    </div>

                    <div class="label-body">
                        <div class="label-row">
                            <span class="label-key">Nama</span>
                            <span class="label-value">: {{ $p->name }}</span>
                        </div>
                        
                        <div class="label-row">
                            <span class="label-key">Username</span>
                            <span class="label-value">: {{ $p->username }}</span>
                        </div>
                        
                        <div class="label-row">
                            <span class="label-key">Password</span>
                            <span class="label-value">: {{ $p->plain_password ?? '********' }}</span>
                        </div>

                        @if(isset($pengujiAssignments[$p->id]) && $pengujiAssignments[$p->id]->count() > 0)
                            <div class="label-assignment">
                                @foreach($pengujiAssignments[$p->id] as $assign)
                                    <div class="assign-row">
                                        <span class="assign-badge stasi">{{ $assign->stasi_nama }}</span>
                                        <span class="assign-badge gelombang">{{ $assign->gelombang_nama }}</span>
                                        <span class="assign-badge jadwal">{{ $assign->jadwal_nama }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach

            {{-- Fill empty cells if last page not full --}}
            @for($i = $pageItems->count(); $i < 10; $i++)
                <div class="label" style="border: 1px dashed #ccc;"></div>
            @endfor
        </div>
    @endforeach
</body>
</html>
