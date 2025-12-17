

<?php $__env->startSection('title', 'Rekap Jadwal: ' . $jadwal->nama); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex justify-between items-start">
                <div>
                    <h2 class="text-xl font-semibold"><?php echo e($jadwal->nama); ?></h2>
                    <p class="text-gray-500 mt-1">
                        Tanggal: <?php echo e($jadwal->mulai->format('d F Y H:i')); ?> - <?php echo e($jadwal->selesai->format('H:i')); ?> | 
                        Peserta: <?php echo e($peserta->count()); ?> mahasiswa
                    </p>
                </div>
                <div class="flex gap-2">
                    
                    <form action="<?php echo e(route('admin.rekap.jadwal.hitungNilaiAcuan', $jadwal)); ?>" method="POST" class="inline">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700" 
                                onclick="return confirm('Hitung/Update Nilai Acuan berdasarkan regresi Global Rating? Proses ini membutuhkan minimal 3 data penilaian per stasi.')">
                            📊 Hitung Nilai Acuan
                        </button>
                    </form>
                    <a href="<?php echo e(route('admin.rekap.jadwal.pdf', $jadwal)); ?>" target="_blank" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">
                        Export PDF
                    </a>
                    <a href="<?php echo e(route('admin.rekap.jadwal.excel', $jadwal)); ?>" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                        Export Excel
                    </a>
                    <a href="<?php echo e(route('admin.rekap.index')); ?>" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300">
                        Kembali
                    </a>
                </div>
            </div>

            
            <?php if(session('success')): ?>
                <div class="mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>
            <?php if(session('error')): ?>
                <div class="mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <?php echo e(session('error')); ?>

                </div>
            <?php endif; ?>
        </div>
    </div>

    
    <?php if(count($nilaiAcuan) > 0): ?>
        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
            <h4 class="font-semibold text-purple-800 mb-2">Nilai Acuan (Standard Setting) per Stasi</h4>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-2">
                <?php $__currentLoopData = $stasi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="bg-white rounded p-2 text-center">
                        <div class="text-xs text-gray-500"><?php echo e($s->nama); ?></div>
                        <div class="text-lg font-bold <?php echo e(isset($nilaiAcuan[$s->id]) ? 'text-purple-600' : 'text-gray-400'); ?>">
                            <?php echo e(isset($nilaiAcuan[$s->id]) ? number_format($nilaiAcuan[$s->id], 2) : '-'); ?>

                        </div>
                        <?php if(isset($nilaiAcuanDetails[$s->id])): ?>
                            <div class="text-xs text-gray-400">
                                n=<?php echo e($nilaiAcuanDetails[$s->id]->sample_count); ?>

                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <p class="text-xs text-purple-600 mt-2">
                * Nilai Acuan = nilai saat Global Rating = 2 (Borderline), dihitung dengan regresi linear dari seluruh penilaian peserta per stasi.
                <br>Mahasiswa dinyatakan LULUS jika Total Nilai Aktual ≥ Total Nilai Acuan.
            </p>
        </div>
    <?php else: ?>
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <p class="text-yellow-800">
                ⚠️ Nilai Acuan belum dihitung. Klik tombol "Hitung Nilai Acuan" setelah ada minimal 2 penilaian per stasi.
            </p>
        </div>
    <?php endif; ?>

    
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIM</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Gelombang</th>
                            <?php $__currentLoopData = $stasi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase" colspan="2">
                                    <?php echo e($s->nama); ?>

                                    <?php if(isset($nilaiAcuan[$s->id])): ?>
                                        <div class="text-xxs text-purple-500 font-normal">(≥<?php echo e(number_format($nilaiAcuan[$s->id], 0)); ?>)</div>
                                    <?php endif; ?>
                                </th>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Total Aktual</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Total Acuan</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                        
                        <tr class="bg-gray-100">
                            <th colspan="4"></th>
                            <?php $__currentLoopData = $stasi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <th class="px-2 py-1 text-center text-xxs font-medium text-gray-400 uppercase">Nilai</th>
                                <th class="px-2 py-1 text-center text-xxs font-medium text-gray-400 uppercase">Penguji</th>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <th colspan="3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php $__empty_1 = true; $__currentLoopData = $peserta; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $mhs): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $nilaiPerStasi = [];
                                $totalNilaiAktual = 0;
                                $totalNilaiAcuanMhs = 0;
                                $countNilai = 0;
                                
                                // Get gelombang mahasiswa
                                $gelombangMhs = $mahasiswaGelombang[$mhs->id] ?? null;
                                
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
                            ?>
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500"><?php echo e($idx + 1); ?></td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo e($mhs->nim); ?></td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900"><?php echo e($mhs->nama); ?></td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-center">
                                    <?php if($gelombangMhs): ?>
                                        <span class="px-2 py-1 text-xs rounded bg-indigo-100 text-indigo-800"><?php echo e($gelombangMhs->nama); ?></span>
                                    <?php else: ?>
                                        <span class="text-gray-400">-</span>
                                    <?php endif; ?>
                                </td>
                                <?php $__currentLoopData = $stasi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    
                                    <td class="px-2 py-3 whitespace-nowrap text-sm text-center">
                                        <?php if($nilaiPerStasi[$s->id]): ?>
                                            <?php
                                                $nilaiAktualStasi = $nilaiPerStasi[$s->id]->nilai_aktual ?? $nilaiPerStasi[$s->id]->total_nilai;
                                                $acuanStasi = $nilaiAcuan[$s->id] ?? null;
                                                $lulusStasi = $acuanStasi ? $nilaiAktualStasi >= $acuanStasi : $nilaiAktualStasi >= 70;
                                            ?>
                                            <div class="<?php echo e($lulusStasi ? 'text-green-600' : 'text-red-600'); ?> font-medium">
                                                <?php echo e(number_format($nilaiAktualStasi, 1)); ?>

                                            </div>
                                            <?php if($nilaiPerStasi[$s->id]->globalRating): ?>
                                                <div class="text-xs 
                                                    <?php echo e($nilaiPerStasi[$s->id]->globalRating->nilai == 1 ? 'text-red-500' : ''); ?>

                                                    <?php echo e($nilaiPerStasi[$s->id]->globalRating->nilai == 2 ? 'text-yellow-500' : ''); ?>

                                                    <?php echo e($nilaiPerStasi[$s->id]->globalRating->nilai == 3 ? 'text-green-500' : ''); ?>

                                                    <?php echo e($nilaiPerStasi[$s->id]->globalRating->nilai == 4 ? 'text-blue-500' : ''); ?>">
                                                    GR:<?php echo e($nilaiPerStasi[$s->id]->globalRating->nilai); ?>

                                                </div>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-gray-400">-</span>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <td class="px-2 py-3 whitespace-nowrap text-xs text-center text-gray-500">
                                        <?php if($nilaiPerStasi[$s->id] && $nilaiPerStasi[$s->id]->penguji): ?>
                                            <?php echo e($nilaiPerStasi[$s->id]->penguji->name); ?>

                                        <?php elseif($gelombangMhs): ?>
                                            <?php
                                                $pengujiGel = $gelombangMhs->getPengujiForStasi($s->id);
                                            ?>
                                            <?php if($pengujiGel): ?>
                                                <span class="text-gray-400"><?php echo e($pengujiGel->name); ?></span>
                                            <?php else: ?>
                                                <span class="text-gray-300">-</span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-gray-300">-</span>
                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-center font-bold <?php echo e($statusLulus === true ? 'text-green-600' : ($statusLulus === false ? 'text-red-600' : 'text-gray-500')); ?>">
                                    <?php echo e($countNilai > 0 ? number_format($totalNilaiAktual, 1) : '-'); ?>

                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-center text-purple-600">
                                    <?php echo e($countNilai > 0 && count($nilaiAcuan) > 0 ? number_format($totalNilaiAcuanMhs, 1) : '-'); ?>

                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-center">
                                    <?php if($countNilai == 0): ?>
                                        <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Belum Dinilai</span>
                                    <?php elseif($statusLulus === null): ?>
                                        <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Menunggu Nilai Acuan</span>
                                    <?php elseif($statusLulus): ?>
                                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">LULUS</span>
                                    <?php else: ?>
                                        <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">TIDAK LULUS</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="<?php echo e(7 + ($stasi->count() * 2)); ?>" class="px-4 py-3 text-center text-gray-500">Belum ada peserta.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold mb-4">Statistik</h3>
            <?php
                $allNilai = App\Models\Nilai::where('jadwal_id', $jadwal->id)->get();
                $avgAktual = $allNilai->avg('nilai_aktual') ?? $allNilai->avg('total_nilai') ?? 0;
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
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div class="bg-indigo-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-indigo-600"><?php echo e($peserta->count()); ?></div>
                    <div class="text-sm text-gray-600">Total Peserta</div>
                </div>
                <div class="bg-green-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-green-600"><?php echo e($lulusCount); ?></div>
                    <div class="text-sm text-gray-600">Lulus</div>
                </div>
                <div class="bg-red-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-red-600"><?php echo e($tidakLulusCount); ?></div>
                    <div class="text-sm text-gray-600">Tidak Lulus</div>
                </div>
                <div class="bg-yellow-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-yellow-600"><?php echo e($pendingCount); ?></div>
                    <div class="text-sm text-gray-600">Menunggu Nilai Acuan</div>
                </div>
                <div class="bg-blue-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-blue-600"><?php echo e(number_format($avgAktual, 1)); ?></div>
                    <div class="text-sm text-gray-600">Rata-rata Nilai Aktual</div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold mb-4">Panduan Kelulusan (BAB VII)</h3>
            <div class="prose prose-sm max-w-none">
                <ul class="text-gray-600 space-y-2">
                    <li><strong>Nilai Aktual per Stasi:</strong> Σ(skor × bobot) - Contoh: skor 3, bobot 2 = 6</li>
                    <li><strong>Global Rating:</strong> Penilaian subjektif penguji (1=Tidak Lulus, 2=Borderline, 3=Lulus, 4=Superior)</li>
                    <li><strong>Nilai Acuan:</strong> Standard setting berbasis regresi linear antara Nilai Aktual dan Global Rating</li>
                    <li><strong>Kriteria Kelulusan:</strong> Total Nilai Aktual semua stasi ≥ Total Nilai Acuan semua stasi</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\Aplikasi Ujian\resources\views/admin/rekap/jadwal.blade.php ENDPATH**/ ?>