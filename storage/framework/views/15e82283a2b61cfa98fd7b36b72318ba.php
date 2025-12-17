

<?php $__env->startSection('title', 'Dashboard Penguji'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $hour = (int) now()->format('H');
    if ($hour >= 5 && $hour < 11) {
        $greeting = 'Selamat Pagi';
        $icon = '🌅';
    } elseif ($hour >= 11 && $hour < 15) {
        $greeting = 'Selamat Siang';
        $icon = '☀️';
    } elseif ($hour >= 15 && $hour < 18) {
        $greeting = 'Selamat Sore';
        $icon = '🌇';
    } else {
        $greeting = 'Selamat Malam';
        $icon = '🌙';
    }
?>
<div class="space-y-6">
    
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-indigo-500">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-1"><?php echo e($icon); ?> <?php echo e($greeting); ?>, <?php echo e(auth()->user()->name); ?>!</h2>
                    <p class="text-gray-500">Anda login sebagai Penguji OSCE</p>
                </div>
                <div x-data="{ 
                    time: '<?php echo e(now()->format('H:i:s')); ?>',
                    date: '<?php echo e(now()->translatedFormat('l, d F Y')); ?>'
                }" 
                x-init="setInterval(() => { 
                    let d = new Date(); 
                    time = d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false }); 
                }, 1000)"
                class="text-right hidden sm:block">
                    <div class="text-3xl font-mono font-bold text-indigo-600" x-text="time"></div>
                    <div class="text-sm text-gray-500"><?php echo e(now()->translatedFormat('l, d F Y')); ?></div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="text-2xl font-bold text-purple-600"><?php echo e($gelombangCount ?? 0); ?></div>
                <div class="text-sm text-gray-600">Gelombang Ditugaskan</div>
            </div>
        </div>
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="text-2xl font-bold text-green-600"><?php echo e($totalDinilai); ?></div>
                <div class="text-sm text-gray-600">Total Penilaian</div>
            </div>
        </div>
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="text-2xl font-bold text-yellow-600"><?php echo e($jadwalAktif); ?></div>
                <div class="text-sm text-gray-600">Jadwal Aktif</div>
            </div>
        </div>
    </div>

    
    <?php if(($gelombangCount ?? 0) > 0): ?>
    <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
        <div class="flex items-center justify-between">
            <div>
                <h4 class="font-medium text-indigo-900">Mulai Penilaian</h4>
                <p class="text-sm text-indigo-700">Anda memiliki <?php echo e($gelombangCount); ?> gelombang yang perlu dinilai</p>
            </div>
            <a href="<?php echo e(route('penguji.penilaian.index')); ?>" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                Mulai Menilai →
            </a>
        </div>
    </div>
    <?php endif; ?>

    
    <?php if($assignedStasi->count() > 0): ?>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold mb-4">Stasi yang Ditugaskan</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <?php $__currentLoopData = $assignedStasi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stasi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="border rounded-lg p-3">
                        <h4 class="font-medium text-gray-900 text-sm"><?php echo e($stasi->nama); ?></h4>
                        <p class="text-xs text-gray-500 mt-1"><?php echo e(Str::limit($stasi->deskripsi, 30) ?? '-'); ?></p>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold mb-4">Jadwal Ujian Aktif</h3>
            
            <?php if($jadwalList->count() > 0): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jadwal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Gelombang</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php $__currentLoopData = $jadwalList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $jadwal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo e($jadwal->nama); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo e($jadwal->mulai->format('d M Y H:i')); ?> - <?php echo e($jadwal->selesai->format('H:i')); ?>

                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo e($jadwal->gelombang_count ?? 0); ?> gelombang</td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-gray-500">Tidak ada jadwal ujian aktif saat ini.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\Aplikasi Ujian\resources\views/penguji/dashboard.blade.php ENDPATH**/ ?>