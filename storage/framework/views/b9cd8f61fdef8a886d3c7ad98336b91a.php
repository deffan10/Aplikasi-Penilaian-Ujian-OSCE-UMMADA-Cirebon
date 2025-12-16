<table>
    
    <tr>
        <td colspan="<?php echo e(7 + $stasi->count()); ?>" style="font-weight: bold; font-size: 14px; text-align: center;">
            REKAP NILAI UJIAN OSCE TAHUN <?php echo e($jadwal->mulai->format('Y')); ?>

        </td>
    </tr>
    <tr>
        <td colspan="<?php echo e(7 + $stasi->count()); ?>"></td>
    </tr>
    
    <tr>
        <td colspan="2" style="font-weight: bold;">Jadwal:</td>
        <td colspan="<?php echo e(5 + $stasi->count()); ?>"><?php echo e($jadwal->nama); ?></td>
    </tr>
    <tr>
        <td colspan="2" style="font-weight: bold;">Tanggal:</td>
        <td colspan="<?php echo e(5 + $stasi->count()); ?>"><?php echo e($jadwal->mulai->format('d F Y H:i')); ?> - <?php echo e($jadwal->selesai->format('H:i')); ?></td>
    </tr>
    <tr>
        <td colspan="2" style="font-weight: bold;">Jumlah Peserta:</td>
        <td colspan="<?php echo e(5 + $stasi->count()); ?>"><?php echo e($peserta->count()); ?> mahasiswa</td>
    </tr>
    <tr>
        <td colspan="<?php echo e(7 + $stasi->count()); ?>"></td>
    </tr>
    
    <tr>
        <td colspan="<?php echo e(7 + $stasi->count()); ?>" style="font-weight: bold;">Keterangan Penilaian (BAB VII):</td>
    </tr>
    <tr>
        <td colspan="<?php echo e(7 + $stasi->count()); ?>">- Nilai Aktual per Stasi: Σ(skor × bobot) - Contoh: skor 3, bobot 2 = 6</td>
    </tr>
    <tr>
        <td colspan="<?php echo e(7 + $stasi->count()); ?>">- Nilai Acuan: Standard setting berbasis regresi Global Rating (1-4)</td>
    </tr>
    <tr>
        <td colspan="<?php echo e(7 + $stasi->count()); ?>">- Kelulusan: Total Nilai Aktual ≥ Total Nilai Acuan</td>
    </tr>
    <tr>
        <td colspan="<?php echo e(7 + $stasi->count()); ?>"></td>
    </tr>
    
    <?php if(count($nilaiAcuan) > 0): ?>
    <tr>
        <td colspan="3" style="font-weight: bold;">Nilai Acuan per Stasi:</td>
        <?php $__currentLoopData = $stasi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <td style="text-align: center;"><?php echo e($s->nama); ?></td>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td colspan="3"></td>
        <?php $__currentLoopData = $stasi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <td style="text-align: center; font-weight: bold;"><?php echo e(isset($nilaiAcuan[$s->id]) ? number_format($nilaiAcuan[$s->id], 1) : '-'); ?></td>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td colspan="<?php echo e(7 + $stasi->count()); ?>"></td>
    </tr>
    <?php endif; ?>
    <thead>
        <tr>
            <th style="font-weight: bold; background-color: #f0f0f0;">No</th>
            <th style="font-weight: bold; background-color: #f0f0f0;">NIM</th>
            <th style="font-weight: bold; background-color: #f0f0f0;">Nama</th>
            <?php $__currentLoopData = $stasi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <th style="font-weight: bold; background-color: #f0f0f0; text-align: center;"><?php echo e($s->nama); ?></th>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <th style="font-weight: bold; background-color: #f0f0f0; text-align: center;">Total Aktual</th>
            <th style="font-weight: bold; background-color: #f0f0f0; text-align: center;">Total Acuan</th>
            <th style="font-weight: bold; background-color: #f0f0f0; text-align: center;">Selisih</th>
            <th style="font-weight: bold; background-color: #f0f0f0; text-align: center;">Status</th>
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
            ?>
            <tr>
                <td><?php echo e($idx + 1); ?></td>
                <td><?php echo e($mhs->nim); ?></td>
                <td><?php echo e($mhs->nama); ?></td>
                <?php $__currentLoopData = $stasi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <td style="text-align: center;">
                        <?php if($nilaiPerStasi[$s->id]): ?>
                            <?php echo e(number_format($nilaiPerStasi[$s->id]->nilai_aktual ?? $nilaiPerStasi[$s->id]->total_nilai, 1)); ?>

                            <?php if($nilaiPerStasi[$s->id]->globalRating): ?>
                                (GR:<?php echo e($nilaiPerStasi[$s->id]->globalRating->nilai); ?>)
                            <?php endif; ?>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <td style="text-align: center; font-weight: bold;"><?php echo e($countNilai > 0 ? number_format($totalNilaiAktual, 1) : '-'); ?></td>
                <td style="text-align: center;"><?php echo e(($countNilai > 0 && count($nilaiAcuan) > 0) ? number_format($totalNilaiAcuanMhs, 1) : '-'); ?></td>
                <td style="text-align: center; <?php echo e($selisih >= 0 ? 'color: green;' : 'color: red;'); ?>">
                    <?php echo e(($countNilai > 0 && count($nilaiAcuan) > 0) ? ($selisih >= 0 ? '+' : '') . number_format($selisih, 1) : '-'); ?>

                </td>
                <td style="text-align: center; font-weight: bold; <?php echo e($statusLulus ? 'color: green;' : 'color: red;'); ?>">
                    <?php if($countNilai == 0): ?>
                        -
                    <?php elseif($statusLulus === null): ?>
                        Menunggu
                    <?php else: ?>
                        <?php echo e($statusLulus ? 'LULUS' : 'TIDAK LULUS'); ?>

                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
    
    <tr>
        <td colspan="<?php echo e(7 + $stasi->count()); ?>"></td>
    </tr>
    <tr>
        <td colspan="3" style="font-weight: bold;">Ringkasan:</td>
        <td colspan="<?php echo e(4 + $stasi->count()); ?>"></td>
    </tr>
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
    <tr>
        <td colspan="2">Lulus:</td>
        <td><?php echo e($lulusCount); ?> mahasiswa</td>
        <td colspan="<?php echo e(4 + $stasi->count()); ?>"></td>
    </tr>
    <tr>
        <td colspan="2">Tidak Lulus:</td>
        <td><?php echo e($tidakLulusCount); ?> mahasiswa</td>
        <td colspan="<?php echo e(4 + $stasi->count()); ?>"></td>
    </tr>
    <tr>
        <td colspan="2">Menunggu Nilai Acuan:</td>
        <td><?php echo e($pendingCount); ?> mahasiswa</td>
        <td colspan="<?php echo e(4 + $stasi->count()); ?>"></td>
    </tr>
</table>
<?php /**PATH C:\laragon\www\Aplikasi Ujian\resources\views/admin/rekap/excel.blade.php ENDPATH**/ ?>