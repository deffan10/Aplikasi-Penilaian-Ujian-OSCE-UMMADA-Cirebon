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

        /* Label container */
        .labels-container {
            display: flex;
            flex-wrap: wrap;
            padding: 5mm;
            gap: 0;
        }

        /* Each label - 105mm x 48mm (fits ~6 labels on A4 portrait in 2 columns) */
        .label {
            width: 95mm;
            height: 45mm;
            border: 1px solid #333;
            padding: 4mm;
            display: flex;
            flex-direction: column;
            justify-content: center;
            margin: 1.5mm;
            page-break-inside: avoid;
        }

        .label-header {
            font-size: 10px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 3mm;
            padding-bottom: 2mm;
            border-bottom: 1px dashed #999;
            text-transform: uppercase;
            letter-spacing: 0.5px;
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
            background: #eef2ff;
            border: 1px solid #c7d2fe;
            padding: 1px 5px;
            border-radius: 3px;
            font-size: 9px;
            margin-right: 2px;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                margin: 0;
                padding: 0;
            }

            .labels-container {
                padding: 3mm;
            }

            .label {
                border: 1px solid #000;
            }

            @page {
                size: A4 portrait;
                margin: 5mm;
            }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()">🖨️ Print Labels</button>
        <a href="{{ route('admin.penguji.index') }}">← Kembali</a>
        <span style="margin-left: 15px; color: #6b7280; font-size: 13px;">
            Total: {{ $penguji->count() }} label penguji
        </span>
    </div>

    <div class="labels-container">
        @foreach($penguji as $p)
            <div class="label">
                <div class="label-header">UJIAN OSCE - Login Penguji</div>
                
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

                @if(isset($pengujiStasiMap[$p->id]) && $pengujiStasiMap[$p->id]->count() > 0)
                    <div class="label-stasi">
                        <span class="label-key" style="display: inline;">Stasi:</span>
                        @foreach($pengujiStasiMap[$p->id] as $stasi)
                            <span class="stasi-badge">{{ $stasi->nama }}</span>
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</body>
</html>
