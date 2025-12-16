

<?php $__env->startSection('title', 'Daftar Stasi'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h2 class="text-xl font-semibold">Daftar Stasi OSCE</h2>
            <p class="text-gray-500 mt-1">Anda dapat melihat semua stasi. Stasi yang ditugaskan bisa Anda nilai.</p>
        </div>
    </div>

    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php $__currentLoopData = $stasi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $isAssigned = in_array($s->id, $assignedStasiIds);
            ?>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg <?php echo e($isAssigned ? 'ring-2 ring-indigo-500' : ''); ?>">
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-lg font-semibold <?php echo e($isAssigned ? 'text-indigo-600' : 'text-gray-600'); ?>">
                                <?php echo e($s->nama); ?>

                            </h3>
                            <p class="text-sm text-gray-500 mt-1"><?php echo e($s->deskripsi ?? 'Tidak ada deskripsi'); ?></p>
                        </div>
                        <?php if($isAssigned): ?>
                            <span class="px-2 py-1 text-xs font-medium bg-indigo-100 text-indigo-800 rounded-full">
                                Ditugaskan
                            </span>
                        <?php else: ?>
                            <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-600 rounded-full">
                                Lihat Saja
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mt-4 flex items-center text-sm text-gray-500">
                        <span><?php echo e($s->komponens_count); ?> komponen</span>
                    </div>

                    <div class="mt-4">
                        <?php if($isAssigned): ?>
                            <a href="<?php echo e(route('penguji.penilaian.stasi', $s)); ?>" 
                               class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Mulai Penilaian
                            </a>
                        <?php else: ?>
                            <a href="<?php echo e(route('penguji.penilaian.stasi', $s)); ?>" 
                               class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Lihat Nilai
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\Aplikasi Ujian\resources\views/penguji/stasi/index.blade.php ENDPATH**/ ?>