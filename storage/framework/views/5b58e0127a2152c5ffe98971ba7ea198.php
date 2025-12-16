<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Nilai OSCE - <?php echo e($jadwal->nama); ?></title>
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
    
    <div class="kop-surat">
        <?php if($setting && $setting->kop_surat_path): ?>
            <img src="<?php echo e(public_path('storage/' . $setting->kop_surat_path)); ?>" alt="Kop Surat">
        <?php else: ?>
            <h2>KOP SURAT BELUM DIATUR</h2>
            <p style="color: red; font-size: 9px;">Silakan upload gambar kop surat di menu Pengaturan</p>
        <?php endif; ?>
    </div>

    <h2 style="text-align: center; font-size: 12px; margin-bottom: 5px;">REKAP NILAI UJIAN OSCE TAHUN <?php echo e($jadwal->mulai->format('Y')); ?></h2>

    <div class="info">
        <p><strong>Jadwal:</strong> <?php echo e($jadwal->nama); ?></p>
        <p><strong>Tanggal:</strong> <?php echo e($jadwal->mulai->format('d F Y H:i')); ?> - <?php echo e($jadwal->selesai->format('H:i')); ?></p>
        <p><strong>Peserta:</strong> <?php echo e($peserta->count()); ?> mahasiswa</p>
    </div>

    
    <div class="keterangan">
        <strong>Keterangan Penilaian (BAB VII - Penetapan Kelulusan):</strong>
        <ul>
            <li><strong>Nilai Aktual per Stasi:</strong> Σ(skor × bobot) - Contoh: skor 3, bobot 2 = 6</li>
            <li><strong>Global Rating (GR):</strong> 1=Tidak Lulus, 2=Borderline, 3=Lulus, 4=Superior</li>
            <li><strong>Nilai Acuan:</strong> Standard setting berbasis regresi linear Global Rating</li>
            <li><strong>Kelulusan:</strong> Total Nilai Aktual ≥ Total Nilai Acuan</li>
        </ul>
    </div>

    
    <?php if(count($nilaiAcuan) > 0): ?>
    <div class="nilai-acuan-box">
        <h4>Nilai Acuan per Stasi (Standard Setting)</h4>
        <table class="nilai-acuan-table">
            <tr>
                <?php $__currentLoopData = $stasi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <td class="label"><?php echo e($s->nama); ?></td>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <td class="label" style="border-left: 1px solid #8b5cf6;">TOTAL</td>
            </tr>
            <tr>
                <?php $totalAcuanAll = 0; ?>
                <?php $__currentLoopData = $stasi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <td><?php echo e(isset($nilaiAcuan[$s->id]) ? number_format($nilaiAcuan[$s->id], 1) : '-'); ?></td>
                    <?php $totalAcuanAll += $nilaiAcuan[$s->id] ?? 0; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <td style="border-left: 1px solid #8b5cf6; font-weight: bold;"><?php echo e(number_format($totalAcuanAll, 1)); ?></td>
            </tr>
        </table>
    </div>
    <?php endif; ?>

    <table class="main">
        <thead>
            <tr>
                <th style="width: 20px;">No</th>
                <th style="width: 60px;">NIM</th>
                <th style="width: 100px;">Nama</th>
                <?php $__currentLoopData = $stasi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <th><?php echo e($s->nama); ?></th>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <th style="width: 45px;">Total Aktual</th>
                <th style="width: 45px;">Total Acuan</th>
                <th style="width: 50px;">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $peserta; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $mhs): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $nilaiPerStasi = [];
                    $totalNilaiAktual = 0;
                    $totalNilaiAcuanMhs = 0;
                    $countNilai = 0;
                    
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
                ?>
                <tr>
                    <td><?php echo e($idx + 1); ?></td>
                    <td><?php echo e($mhs->nim); ?></td>
                    <td class="left"><?php echo e($mhs->nama); ?></td>
                    <?php $__currentLoopData = $stasi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <td>
                            <?php if($nilaiPerStasi[$s->id]): ?>
                                <?php
                                    $nilaiAktualStasi = $nilaiPerStasi[$s->id]->nilai_aktual ?? $nilaiPerStasi[$s->id]->total_nilai;
                                    $acuanStasi = $nilaiAcuan[$s->id] ?? null;
                                    $lulusStasi = $acuanStasi ? $nilaiAktualStasi >= $acuanStasi : $nilaiAktualStasi >= 70;
                                ?>
                                <span class="<?php echo e($lulusStasi ? 'lulus' : 'tidak-lulus'); ?>">
                                    <?php echo e(number_format($nilaiAktualStasi, 1)); ?>

                                </span>
                                <?php if($nilaiPerStasi[$s->id]->globalRating): ?>
                                    <br><span class="gr-badge">GR:<?php echo e($nilaiPerStasi[$s->id]->globalRating->nilai); ?></span>
                                <?php endif; ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <td class="<?php echo e($statusLulus === true ? 'lulus' : ($statusLulus === false ? 'tidak-lulus' : '')); ?>">
                        <?php echo e($countNilai > 0 ? number_format($totalNilaiAktual, 1) : '-'); ?>

                    </td>
                    <td>
                        <?php echo e(($countNilai > 0 && count($nilaiAcuan) > 0) ? number_format($totalNilaiAcuanMhs, 1) : '-'); ?>

                    </td>
                    <td class="<?php echo e($statusLulus ? 'lulus' : 'tidak-lulus'); ?>">
                        <?php if($countNilai == 0): ?>
                            -
                        <?php elseif($statusLulus === null): ?>
                            Pending
                        <?php else: ?>
                            <?php echo e($statusLulus ? 'LULUS' : 'TIDAK LULUS'); ?>

                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    
    <?php
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
    ?>
    <div style="margin-bottom: 15px;">
        <strong>Ringkasan:</strong> 
        Lulus: <span class="lulus"><?php echo e($lulusCount); ?></span> | 
        Tidak Lulus: <span class="tidak-lulus"><?php echo e($tidakLulusCount); ?></span>
        <?php if($pendingCount > 0): ?>
            | Pending: <?php echo e($pendingCount); ?>

        <?php endif; ?>
    </div>

    <div style="margin-top: 30px; page-break-inside: avoid;">
        <div style="float: right; width: 200px; text-align: center;">
            <div style="margin-bottom: 10px;"><?php echo e(now()->translatedFormat('d F Y')); ?></div>
            <div style="margin-bottom: 50px;"><?php echo e(!empty($setting->penandatangan_jabatan) ? $setting->penandatangan_jabatan : 'Koordinator OSCE'); ?></div>
            <div style="font-weight: bold; text-decoration: underline;"><?php echo e(!empty($setting->penandatangan_nama) ? $setting->penandatangan_nama : '____________________'); ?></div>
            <?php if(!empty($setting->penandatangan_nik)): ?>
                <div style="font-size: 8px;"><?php echo e($setting->penandatangan_nik); ?></div>
            <?php endif; ?>
        </div>
        <div style="clear: both;"></div>
    </div>
    
    <div style="font-size: 8px; color: #666; margin-top: 15px;">
        Dicetak pada: <?php echo e(now()->format('d F Y H:i')); ?>

    </div>
</body>
</html>
<?php /**PATH C:\laragon\www\Aplikasi Ujian\resources\views/admin/rekap/pdf.blade.php ENDPATH**/ ?>