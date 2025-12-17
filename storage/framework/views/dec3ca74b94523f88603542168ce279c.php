

<?php $__env->startSection('title', 'Rekap Nilai'); ?>

<?php $__env->startSection('content'); ?>
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
        <h2 class="text-xl font-semibold mb-6">Rekap Nilai</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <div class="border rounded-lg p-6">
                <h3 class="text-lg font-medium mb-4">Rekap Per Jadwal</h3>
                <div class="space-y-4">
                    <div>
                        <label for="jadwal_select" class="block text-sm font-medium text-gray-700 mb-1">Pilih Jadwal</label>
                        <select id="jadwal_select"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">-- Pilih Jadwal --</option>
                            <?php $__currentLoopData = $jadwalList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $j): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($j->id); ?>" class="<?php echo e($j->is_arsip ? 'text-gray-500' : ''); ?>">
                                    <?php echo e($j->nama); ?><?php echo e($j->is_arsip ? ' (Arsip)' : ''); ?> - <?php echo e($j->mulai->format('d M Y')); ?>

                                    <?php if($j->label_tahun_akademik): ?> | <?php echo e($j->label_tahun_akademik); ?> <?php endif; ?>
                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Jadwal arsip tetap bisa dilihat rekapnya</p>
                    </div>
                    <button type="button" onclick="goToJadwal()" class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                        Lihat Rekap
                    </button>
                </div>
            </div>

            
            <div class="border rounded-lg p-6">
                <h3 class="text-lg font-medium mb-4">Rekap Per Stasi</h3>
                <div class="space-y-4">
                    <p class="text-sm text-gray-500">
                        Lihat rekap nilai per stasi untuk jadwal tertentu. 
                        Termasuk informasi gelombang dan penguji.
                    </p>
                    <a href="<?php echo e(route('admin.rekap.stasi')); ?>" class="block w-full text-center bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                        Lihat Rekap Per Stasi
                    </a>
                </div>
            </div>
        </div>

        
        <div class="mt-8">
            <h3 class="text-lg font-medium mb-4">Statistik Umum</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-indigo-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-indigo-600"><?php echo e(App\Models\Jadwal::count()); ?></div>
                    <div class="text-sm text-gray-600">Total Jadwal</div>
                </div>
                <div class="bg-green-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-green-600"><?php echo e(App\Models\Mahasiswa::count()); ?></div>
                    <div class="text-sm text-gray-600">Total Mahasiswa</div>
                </div>
                <div class="bg-yellow-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-yellow-600"><?php echo e(App\Models\Nilai::count()); ?></div>
                    <div class="text-sm text-gray-600">Total Penilaian</div>
                </div>
                <div class="bg-blue-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-blue-600"><?php echo e(App\Models\Stasi::count()); ?></div>
                    <div class="text-sm text-gray-600">Total Stasi</div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function goToJadwal() {
    const select = document.getElementById('jadwal_select');
    const jadwalId = select.value;
    if (jadwalId) {
        window.location.href = "<?php echo e(url('admin/rekap/jadwal')); ?>/" + jadwalId;
    } else {
        alert('Silakan pilih jadwal terlebih dahulu');
    }
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\Aplikasi Ujian\resources\views/admin/rekap/index.blade.php ENDPATH**/ ?>