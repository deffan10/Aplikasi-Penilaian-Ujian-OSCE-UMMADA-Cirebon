

<?php $__env->startSection('title', 'Dashboard Admin'); ?>

<?php $__env->startSection('content'); ?>
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
    <div class="p-6 text-gray-900">
        <h2 class="text-2xl font-semibold mb-4">Dashboard Admin</h2>
        <p class="text-gray-600">Selamat datang di sistem penilaian OSCE Farmasi.</p>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="text-3xl font-bold text-indigo-600"><?php echo e($stats['total_mahasiswa']); ?></div>
            <div class="text-sm text-gray-500">Total Mahasiswa</div>
        </div>
    </div>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="text-3xl font-bold text-green-600"><?php echo e($stats['total_stasi']); ?></div>
            <div class="text-sm text-gray-500">Stasi Aktif</div>
        </div>
    </div>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="text-3xl font-bold text-blue-600"><?php echo e($stats['total_penguji']); ?></div>
            <div class="text-sm text-gray-500">Penguji</div>
        </div>
    </div>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="text-3xl font-bold text-yellow-600"><?php echo e($stats['total_jadwal']); ?></div>
            <div class="text-sm text-gray-500">Jadwal OSCE</div>
        </div>
    </div>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="text-3xl font-bold text-purple-600"><?php echo e($stats['total_nilai']); ?></div>
            <div class="text-sm text-gray-500">Total Penilaian</div>
        </div>
    </div>
</div>

<!-- Recent Jadwal -->
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
        <h3 class="text-lg font-semibold mb-4">Jadwal OSCE Terbaru</h3>
        <?php if($recentJadwal->count() > 0): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mulai</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Selesai</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php $__currentLoopData = $recentJadwal; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $jadwal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <?php echo e($jadwal->nama); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo e($jadwal->mulai->format('d M Y H:i')); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo e($jadwal->selesai->format('d M Y H:i')); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if($jadwal->isActive()): ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Berlangsung
                                        </span>
                                    <?php elseif($jadwal->mulai > now()): ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Akan Datang
                                        </span>
                                    <?php else: ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            Selesai
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-gray-500">Belum ada jadwal OSCE.</p>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\Aplikasi Ujian\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>