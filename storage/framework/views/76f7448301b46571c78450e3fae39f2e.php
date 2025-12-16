

<?php $__env->startSection('title', 'Rekap Kelas: ' . $kelas->nama); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex justify-between items-start">
                <div>
                    <h2 class="text-xl font-semibold">Rekap Kelas: <?php echo e($kelas->nama); ?></h2>
                    <p class="text-gray-500 mt-1">Total <?php echo e($mahasiswa->count()); ?> mahasiswa</p>
                </div>
                <div class="flex gap-2">
                    <a href="<?php echo e(route('admin.rekap.kelas.pdf', $kelas)); ?>" target="_blank" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">
                        Export PDF
                    </a>
                    <a href="<?php echo e(route('admin.rekap.kelas.excel', $kelas)); ?>" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                        Export Excel
                    </a>
                    <a href="<?php echo e(route('admin.rekap.index')); ?>" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300">
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIM</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                            <?php $__currentLoopData = $stasi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase"><?php echo e($s->nama); ?></th>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Rata-rata</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php $__empty_1 = true; $__currentLoopData = $mahasiswa; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $mhs): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
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
                            ?>
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500"><?php echo e($idx + 1); ?></td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo e($mhs->nim); ?></td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900"><?php echo e($mhs->nama); ?></td>
                                <?php $__currentLoopData = $stasi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-center">
                                        <?php if($nilaiPerStasi[$s->id]): ?>
                                            <div class="<?php echo e($nilaiPerStasi[$s->id]->total_nilai >= 70 ? 'text-green-600' : 'text-red-600'); ?> font-medium">
                                                <?php echo e(number_format($nilaiPerStasi[$s->id]->total_nilai, 1)); ?>

                                            </div>
                                            <?php if($nilaiPerStasi[$s->id]->globalRating): ?>
                                                <div class="text-xs text-gray-500"><?php echo e($nilaiPerStasi[$s->id]->globalRating->nama); ?></div>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-gray-400">-</span>
                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-center font-bold <?php echo e($rataRata >= 70 ? 'text-green-600' : 'text-red-600'); ?>">
                                    <?php echo e($countNilai > 0 ? number_format($rataRata, 1) : '-'); ?>

                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-center">
                                    <?php if($countNilai == 0): ?>
                                        <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Belum Dinilai</span>
                                    <?php elseif($statusLulus): ?>
                                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">LULUS</span>
                                    <?php else: ?>
                                        <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">TIDAK LULUS</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="<?php echo e(5 + $stasi->count()); ?>" class="px-4 py-3 text-center text-gray-500">Belum ada mahasiswa.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\Aplikasi Ujian\resources\views/admin/rekap/kelas.blade.php ENDPATH**/ ?>