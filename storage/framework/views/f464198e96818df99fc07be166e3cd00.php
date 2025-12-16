

<?php $__env->startSection('title', 'Detail Jadwal'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h2 class="text-xl font-semibold"><?php echo e($jadwal->nama); ?></h2>
                    <p class="text-gray-500 mt-1">
                        Waktu: <?php echo e($jadwal->mulai->format('d F Y H:i')); ?> - <?php echo e($jadwal->selesai->format('d F Y H:i')); ?> |
                        Status: 
                        <?php if($jadwal->isActive()): ?>
                            <span class="text-green-600 font-medium">Aktif</span>
                        <?php elseif($jadwal->mulai > now()): ?>
                            <span class="text-yellow-600 font-medium">Akan Datang</span>
                        <?php else: ?>
                            <span class="text-gray-600">Selesai</span>
                        <?php endif; ?>
                    </p>
                </div>
                <div class="flex gap-2">
                    <a href="<?php echo e(route('admin.jadwal.edit', $jadwal)); ?>" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                        Edit Jadwal
                    </a>
                    <a href="<?php echo e(route('admin.jadwal.index')); ?>" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300">
                        Kembali
                    </a>
                </div>
            </div>

            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-indigo-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-indigo-600"><?php echo e($jadwal->peserta->count()); ?></div>
                    <div class="text-sm text-gray-600">Total Peserta</div>
                </div>
                <div class="bg-green-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-green-600"><?php echo e($sudahDinilai ?? 0); ?></div>
                    <div class="text-sm text-gray-600">Sudah Dinilai</div>
                </div>
                <div class="bg-yellow-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-yellow-600"><?php echo e($jadwal->peserta->count() - ($sudahDinilai ?? 0)); ?></div>
                    <div class="text-sm text-gray-600">Belum Dinilai</div>
                </div>
                <div class="bg-blue-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-blue-600"><?php echo e(App\Models\Stasi::where('aktif', true)->count()); ?></div>
                    <div class="text-sm text-gray-600">Jumlah Stasi</div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold mb-4">Daftar Peserta</h3>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIM</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Progress Stasi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rata-rata</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php $__empty_1 = true; $__currentLoopData = $jadwal->peserta; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $mhs): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $nilaiMhs = App\Models\Nilai::where('jadwal_id', $jadwal->id)
                                    ->where('mahasiswa_id', $mhs->id)->get();
                                $nilaiCount = $nilaiMhs->count();
                                $stasiCount = App\Models\Stasi::where('aktif', true)->count();
                                $avgNilai = $nilaiCount > 0 ? $nilaiMhs->avg('total_nilai') : null;
                            ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo e($idx + 1); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo e($mhs->nim); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo e($mhs->nama); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <div class="flex items-center">
                                        <div class="w-24 bg-gray-200 rounded-full h-2.5 mr-2">
                                            <div class="bg-indigo-600 h-2.5 rounded-full" style="width: <?php echo e($stasiCount > 0 ? ($nilaiCount / $stasiCount * 100) : 0); ?>%"></div>
                                        </div>
                                        <span class="text-gray-600"><?php echo e($nilaiCount); ?>/<?php echo e($stasiCount); ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <?php if($avgNilai !== null): ?>
                                        <span class="font-medium <?php echo e($avgNilai >= 70 ? 'text-green-600' : 'text-red-600'); ?>">
                                            <?php echo e(number_format($avgNilai, 1)); ?>

                                        </span>
                                    <?php else: ?>
                                        <span class="text-gray-400">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">Belum ada peserta.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold mb-4">Rekap Per Stasi</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php $__currentLoopData = App\Models\Stasi::where('aktif', true)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stasi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $nilaiStasi = App\Models\Nilai::where('jadwal_id', $jadwal->id)
                            ->where('stasi_id', $stasi->id)->get();
                        $dinilai = $nilaiStasi->count();
                        $avgNilai = $dinilai > 0 ? $nilaiStasi->avg('total_nilai') : null;
                    ?>
                    <div class="border rounded-lg p-4">
                        <h4 class="font-medium text-gray-900"><?php echo e($stasi->nama); ?></h4>
                        <p class="text-sm text-gray-500 mt-1"><?php echo e($stasi->deskripsi ?? '-'); ?></p>
                        <div class="mt-3 flex justify-between text-sm">
                            <span class="text-gray-600">Dinilai: <?php echo e($dinilai); ?>/<?php echo e($jadwal->peserta->count()); ?></span>
                            <span class="font-medium <?php echo e(($avgNilai ?? 0) >= 70 ? 'text-green-600' : 'text-gray-600'); ?>">
                                Rata-rata: <?php echo e($avgNilai !== null ? number_format($avgNilai, 1) : '-'); ?>

                            </span>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\Aplikasi Ujian\resources\views/admin/jadwal/show.blade.php ENDPATH**/ ?>