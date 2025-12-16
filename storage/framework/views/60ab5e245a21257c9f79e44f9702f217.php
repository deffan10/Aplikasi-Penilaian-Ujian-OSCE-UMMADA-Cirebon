

<?php $__env->startSection('title', 'Kelola Stasi'); ?>

<?php $__env->startSection('content'); ?>
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold">Kelola Stasi OSCE</h2>
            <a href="<?php echo e(route('admin.stasi.create')); ?>" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                + Tambah Stasi
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Stasi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Komponen</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Penguji</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $__empty_1 = true; $__currentLoopData = $stasi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo e($stasi->firstItem() + $index); ?>

                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900"><?php echo e($s->nama); ?></div>
                                <?php if($s->deskripsi): ?>
                                    <div class="text-sm text-gray-500"><?php echo e(Str::limit($s->deskripsi, 50)); ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <a href="<?php echo e(route('admin.stasi.komponen.index', $s)); ?>" class="text-indigo-600 hover:underline">
                                    <?php echo e($s->komponens_count); ?> komponen
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo e($s->penguji_count); ?> penguji
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if($s->aktif): ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Aktif
                                    </span>
                                <?php else: ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Nonaktif
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <a href="<?php echo e(route('admin.stasi.komponen.index', $s)); ?>" class="text-blue-600 hover:text-blue-900">Komponen</a>
                                <a href="<?php echo e(route('admin.stasi.edit', $s)); ?>" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                <form action="<?php echo e(route('admin.stasi.destroy', $s)); ?>" method="POST" class="inline" onsubmit="return confirm('Yakin hapus stasi ini?')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                Belum ada stasi. Klik tombol "Tambah Stasi" untuk menambahkan.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <?php echo e($stasi->links()); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\Aplikasi Ujian\resources\views/admin/stasi/index.blade.php ENDPATH**/ ?>