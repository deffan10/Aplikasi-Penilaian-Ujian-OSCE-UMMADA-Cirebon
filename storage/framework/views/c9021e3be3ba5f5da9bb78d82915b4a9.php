

<?php $__env->startSection('title', 'Rekap Per Stasi'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex justify-between items-start">
                <div>
                    <h2 class="text-xl font-semibold">Rekap Per Stasi</h2>
                    <p class="text-gray-500 mt-1">Lihat rekap nilai mahasiswa per stasi untuk jadwal tertentu</p>
                </div>
                <a href="<?php echo e(route('admin.rekap.index')); ?>" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300">
                    Kembali
                </a>
            </div>
        </div>
    </div>

    
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <form method="GET" action="<?php echo e(route('admin.rekap.stasi')); ?>" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="jadwal_id" class="block text-sm font-medium text-gray-700 mb-1">Pilih Jadwal</label>
                    <select name="jadwal_id" id="jadwal_id" required
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">-- Pilih Jadwal --</option>
                        <?php $__currentLoopData = $jadwalList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $j): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($j->id); ?>" <?php echo e(request('jadwal_id') == $j->id ? 'selected' : ''); ?>>
                                <?php echo e($j->nama); ?> - <?php echo e($j->mulai->format('d M Y')); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label for="stasi_id" class="block text-sm font-medium text-gray-700 mb-1">Pilih Stasi</label>
                    <select name="stasi_id" id="stasi_id" required
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">-- Pilih Stasi --</option>
                        <?php $__currentLoopData = $stasiList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($s->id); ?>" <?php echo e(request('stasi_id') == $s->id ? 'selected' : ''); ?>>
                                <?php echo e($s->nama); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                        Tampilkan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php if($jadwal && $stasi): ?>
        
        <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="font-semibold text-indigo-800"><?php echo e($stasi->nama); ?></h3>
                    <p class="text-sm text-indigo-600">
                        Jadwal: <?php echo e($jadwal->nama); ?> | <?php echo e($jadwal->mulai->format('d F Y')); ?> |
                        Total: <?php echo e($nilaiList->count()); ?> penilaian
                    </p>
                </div>
                <?php if($nilaiAcuan): ?>
                    <div class="text-center">
                        <div class="text-sm text-gray-500">Nilai Acuan</div>
                        <div class="text-2xl font-bold text-purple-600"><?php echo e(number_format($nilaiAcuan, 2)); ?></div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Gelombang</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIM</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kelas</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Nilai Aktual</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Global Rating</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Penguji</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php $__empty_1 = true; $__currentLoopData = $nilaiList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $nilai): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php
                                    $nilaiAktual = $nilai->nilai_aktual ?? $nilai->total_nilai;
                                    $lulus = $nilaiAcuan ? $nilaiAktual >= $nilaiAcuan : $nilaiAktual >= 70;
                                ?>
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500"><?php echo e($idx + 1); ?></td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm">
                                        <?php if($nilai->gelombang): ?>
                                            <span class="px-2 py-1 text-xs rounded bg-indigo-100 text-indigo-800">
                                                <?php echo e($nilai->gelombang->nama); ?>

                                            </span>
                                        <?php else: ?>
                                            <span class="text-gray-400">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                        <?php echo e($nilai->mahasiswa->nim ?? '-'); ?>

                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo e($nilai->mahasiswa->nama ?? '-'); ?>

                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo e($nilai->mahasiswa->kelas->nama ?? '-'); ?>

                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-center font-bold <?php echo e($lulus ? 'text-green-600' : 'text-red-600'); ?>">
                                        <?php echo e(number_format($nilaiAktual, 1)); ?>

                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-center">
                                        <?php if($nilai->globalRating): ?>
                                            <span class="px-2 py-1 text-xs rounded-full 
                                                <?php echo e($nilai->globalRating->nilai == 1 ? 'bg-red-100 text-red-800' : ''); ?>

                                                <?php echo e($nilai->globalRating->nilai == 2 ? 'bg-yellow-100 text-yellow-800' : ''); ?>

                                                <?php echo e($nilai->globalRating->nilai == 3 ? 'bg-green-100 text-green-800' : ''); ?>

                                                <?php echo e($nilai->globalRating->nilai == 4 ? 'bg-blue-100 text-blue-800' : ''); ?>">
                                                <?php echo e($nilai->globalRating->label); ?>

                                            </span>
                                        <?php else: ?>
                                            <span class="text-gray-400">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                                        <?php echo e($nilai->penguji->name ?? '-'); ?>

                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-center">
                                        <?php if($lulus): ?>
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">LULUS</span>
                                        <?php else: ?>
                                            <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">TIDAK LULUS</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="9" class="px-4 py-3 text-center text-gray-500">
                                        Belum ada data penilaian untuk stasi ini.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        
        <?php if($nilaiList->count() > 0): ?>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Statistik Stasi</h3>
                    <?php
                        $totalNilai = $nilaiList->count();
                        $avgNilai = $nilaiList->avg(fn($n) => $n->nilai_aktual ?? $n->total_nilai);
                        $lulusCount = $nilaiList->filter(function($n) use ($nilaiAcuan) {
                            $val = $n->nilai_aktual ?? $n->total_nilai;
                            return $nilaiAcuan ? $val >= $nilaiAcuan : $val >= 70;
                        })->count();
                        $tidakLulusCount = $totalNilai - $lulusCount;
                        
                        // Group by gelombang
                        $perGelombang = $nilaiList->groupBy(fn($n) => $n->gelombang->nama ?? 'Tanpa Gelombang');
                    ?>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <div class="bg-indigo-50 rounded-lg p-4">
                            <div class="text-2xl font-bold text-indigo-600"><?php echo e($totalNilai); ?></div>
                            <div class="text-sm text-gray-600">Total Penilaian</div>
                        </div>
                        <div class="bg-blue-50 rounded-lg p-4">
                            <div class="text-2xl font-bold text-blue-600"><?php echo e(number_format($avgNilai, 1)); ?></div>
                            <div class="text-sm text-gray-600">Rata-rata Nilai</div>
                        </div>
                        <div class="bg-green-50 rounded-lg p-4">
                            <div class="text-2xl font-bold text-green-600"><?php echo e($lulusCount); ?></div>
                            <div class="text-sm text-gray-600">Lulus (<?php echo e($totalNilai > 0 ? round($lulusCount/$totalNilai*100) : 0); ?>%)</div>
                        </div>
                        <div class="bg-red-50 rounded-lg p-4">
                            <div class="text-2xl font-bold text-red-600"><?php echo e($tidakLulusCount); ?></div>
                            <div class="text-sm text-gray-600">Tidak Lulus (<?php echo e($totalNilai > 0 ? round($tidakLulusCount/$totalNilai*100) : 0); ?>%)</div>
                        </div>
                    </div>

                    
                    <?php if($perGelombang->count() > 1): ?>
                        <h4 class="font-medium mb-3">Distribusi per Gelombang</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <?php $__currentLoopData = $perGelombang; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gelName => $nilaiGel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $avgGel = $nilaiGel->avg(fn($n) => $n->nilai_aktual ?? $n->total_nilai);
                                    $pengujiGel = $nilaiGel->first()->penguji->name ?? '-';
                                ?>
                                <div class="border rounded-lg p-3">
                                    <div class="font-medium text-gray-800"><?php echo e($gelName); ?></div>
                                    <div class="text-sm text-gray-500">
                                        <?php echo e($nilaiGel->count()); ?> mahasiswa | 
                                        Rata-rata: <?php echo e(number_format($avgGel, 1)); ?> |
                                        Penguji: <?php echo e($pengujiGel); ?>

                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
            <p class="text-yellow-800">Pilih jadwal dan stasi untuk melihat rekap.</p>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\Aplikasi Ujian\resources\views/admin/rekap/stasi.blade.php ENDPATH**/ ?>