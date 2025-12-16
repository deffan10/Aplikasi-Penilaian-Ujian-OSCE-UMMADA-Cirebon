

<?php $__env->startSection('title', 'Edit Jadwal'); ?>

<?php $__env->startSection('content'); ?>
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
        <h2 class="text-xl font-semibold mb-6">Edit Jadwal: <?php echo e($jadwal->nama); ?></h2>

        <form action="<?php echo e(route('admin.jadwal.update', $jadwal)); ?>" method="POST" class="max-w-xl">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="mb-4">
                <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">Nama Jadwal</label>
                <input type="text" name="nama" id="nama" value="<?php echo e(old('nama', $jadwal->nama)); ?>" required
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <?php $__errorArgs = ['nama'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-600 text-sm mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="mb-4">
                <label for="mulai" class="block text-sm font-medium text-gray-700 mb-1">Waktu Mulai</label>
                <input type="datetime-local" name="mulai" id="mulai" value="<?php echo e(old('mulai', $jadwal->mulai->format('Y-m-d\TH:i'))); ?>" required
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <?php $__errorArgs = ['mulai'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-600 text-sm mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="mb-4">
                <label for="selesai" class="block text-sm font-medium text-gray-700 mb-1">Waktu Selesai</label>
                <input type="datetime-local" name="selesai" id="selesai" value="<?php echo e(old('selesai', $jadwal->selesai->format('Y-m-d\TH:i'))); ?>" required
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <?php $__errorArgs = ['selesai'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-600 text-sm mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="tahun_akademik" class="block text-sm font-medium text-gray-700 mb-1">Tahun Akademik</label>
                    <?php
                        $tahunSekarang = date('Y');
                        $tahunOptions = [];
                        for ($i = -2; $i <= 2; $i++) {
                            $t1 = $tahunSekarang + $i;
                            $t2 = $t1 + 1;
                            $tahunOptions[] = "$t1/$t2";
                        }
                    ?>
                    <select name="tahun_akademik" id="tahun_akademik"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">-- Pilih --</option>
                        <?php $__currentLoopData = $tahunOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tahun): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($tahun); ?>" <?php echo e(old('tahun_akademik', $jadwal->tahun_akademik) == $tahun ? 'selected' : ''); ?>><?php echo e($tahun); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['tahun_akademik'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-600 text-sm mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label for="semester" class="block text-sm font-medium text-gray-700 mb-1">Semester</label>
                    <select name="semester" id="semester"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">-- Pilih --</option>
                        <option value="ganjil" <?php echo e(old('semester', $jadwal->semester) == 'ganjil' ? 'selected' : ''); ?>>Ganjil</option>
                        <option value="genap" <?php echo e(old('semester', $jadwal->semester) == 'genap' ? 'selected' : ''); ?>>Genap</option>
                    </select>
                    <?php $__errorArgs = ['semester'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-600 text-sm mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <div class="mb-4">
                <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1">Keterangan (Opsional)</label>
                <textarea name="keterangan" id="keterangan" rows="2"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"><?php echo e(old('keterangan', $jadwal->keterangan)); ?></textarea>
                <?php $__errorArgs = ['keterangan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-600 text-sm mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Peserta</label>
                <div class="space-y-2 max-h-64 overflow-y-auto border border-gray-200 rounded-md p-3">
                    <?php $__empty_1 = true; $__currentLoopData = $allMahasiswa; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mhs): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <label class="flex items-center">
                            <input type="checkbox" name="mahasiswa_ids[]" value="<?php echo e($mhs->id); ?>"
                                <?php echo e(in_array($mhs->id, old('mahasiswa_ids', $pesertaIds)) ? 'checked' : ''); ?>

                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-700"><?php echo e($mhs->nim); ?> - <?php echo e($mhs->nama); ?> (<?php echo e($mhs->kelas->nama ?? '-'); ?>)</span>
                        </label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-gray-500 text-sm">Belum ada mahasiswa.</p>
                    <?php endif; ?>
                </div>
                <?php $__errorArgs = ['mahasiswa_ids'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-600 text-sm mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="flex items-center gap-4">
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                    Update
                </button>
                <a href="<?php echo e(route('admin.jadwal.index')); ?>" class="text-gray-600 hover:text-gray-900">Batal</a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\Aplikasi Ujian\resources\views/admin/jadwal/edit.blade.php ENDPATH**/ ?>