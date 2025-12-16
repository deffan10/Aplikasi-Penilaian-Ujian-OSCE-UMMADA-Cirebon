

<?php $__env->startSection('title', ($canNilai ? 'Form Penilaian' : 'Lihat Nilai') . ' - ' . $mahasiswa->nama); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex justify-between items-start">
                <div>
                    <div class="flex items-center gap-3">
                        <h2 class="text-xl font-semibold"><?php echo e($canNilai ? 'Form Penilaian' : 'Lihat Nilai'); ?></h2>
                        <?php if($canNilai): ?>
                            <span class="px-2 py-1 text-xs font-medium bg-indigo-100 text-indigo-800 rounded-full">
                                Dapat Menilai
                            </span>
                        <?php else: ?>
                            <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">
                                Hanya Lihat
                            </span>
                        <?php endif; ?>
                    </div>
                    <p class="text-gray-500 mt-1">
                        Stasi: <?php echo e($stasi->nama); ?> | 
                        Jadwal: <?php echo e($jadwal->nama); ?>

                    </p>
                </div>
                <a href="<?php echo e(route('penguji.penilaian.list', ['stasi' => $stasi, 'jadwal' => $jadwal])); ?>" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300">
                    ← Kembali
                </a>
            </div>
        </div>
    </div>

    
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold mb-4">Data Mahasiswa</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <span class="text-sm text-gray-500">NIM</span>
                    <p class="font-medium"><?php echo e($mahasiswa->nim); ?></p>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Nama</span>
                    <p class="font-medium"><?php echo e($mahasiswa->nama); ?></p>
                </div>
            </div>
        </div>
    </div>

    <?php if($canNilai): ?>
        
        <form action="<?php echo e(route('penguji.penilaian.store', ['stasi' => $stasi, 'jadwal' => $jadwal, 'mahasiswa' => $mahasiswa])); ?>" method="POST">
            <?php echo csrf_field(); ?>
            
            
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <h4 class="font-semibold text-blue-800 mb-2">Panduan Penilaian (Skala 0-3)</h4>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-sm">
                    <div class="flex items-center">
                        <span class="w-6 h-6 rounded-full bg-red-500 text-white flex items-center justify-center text-xs font-bold mr-2">0</span>
                        <span class="text-gray-700">Tidak melakukan</span>
                    </div>
                    <div class="flex items-center">
                        <span class="w-6 h-6 rounded-full bg-orange-500 text-white flex items-center justify-center text-xs font-bold mr-2">1</span>
                        <span class="text-gray-700">1 langkah benar</span>
                    </div>
                    <div class="flex items-center">
                        <span class="w-6 h-6 rounded-full bg-yellow-500 text-white flex items-center justify-center text-xs font-bold mr-2">2</span>
                        <span class="text-gray-700">2 langkah benar</span>
                    </div>
                    <div class="flex items-center">
                        <span class="w-6 h-6 rounded-full bg-green-500 text-white flex items-center justify-center text-xs font-bold mr-2">3</span>
                        <span class="text-gray-700">Semua benar</span>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Komponen Penilaian (Checklist)</h3>
                    
                    <div class="space-y-4">
                        <?php $__currentLoopData = $komponens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $komponen): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $nilaiDetail = $nilai ? $nilai->detail->where('komponen_stasi_id', $komponen->id)->first() : null;
                                $currentSkor = old('skor.' . $komponen->id, $nilaiDetail->skor ?? '');
                            ?>
                            <div class="border rounded-lg p-4">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <label class="font-medium text-gray-900"><?php echo e($komponen->nama); ?></label>
                                        <p class="text-sm text-gray-500">Bobot: <?php echo e($komponen->bobot); ?></p>
                                    </div>
                                </div>
                                <div class="flex gap-2" id="komponen-<?php echo e($komponen->id); ?>">
                                    <?php for($i = 0; $i <= 3; $i++): ?>
                                        <?php
                                            $isSelected = ($currentSkor !== '' && (int)$currentSkor === $i);
                                            $bgColors = [
                                                0 => $isSelected ? 'bg-red-200 border-red-600 ring-2 ring-red-600' : 'bg-red-50 border-red-300 hover:bg-red-100',
                                                1 => $isSelected ? 'bg-amber-200 border-amber-600 ring-2 ring-amber-600' : 'bg-amber-50 border-amber-300 hover:bg-amber-100',
                                                2 => $isSelected ? 'bg-yellow-200 border-yellow-600 ring-2 ring-yellow-600' : 'bg-yellow-50 border-yellow-300 hover:bg-yellow-100',
                                                3 => $isSelected ? 'bg-green-200 border-green-600 ring-2 ring-green-600' : 'bg-green-50 border-green-300 hover:bg-green-100',
                                            ];
                                            $textColors = [
                                                0 => 'text-red-700',
                                                1 => 'text-amber-700',
                                                2 => 'text-yellow-700',
                                                3 => 'text-green-700',
                                            ];
                                        ?>
                                        <button type="button" 
                                                onclick="selectSkor(<?php echo e($komponen->id); ?>, <?php echo e($i); ?>)"
                                                class="skor-btn flex-1 p-3 text-center border-2 rounded-lg transition-all cursor-pointer <?php echo e($bgColors[$i]); ?>"
                                                data-komponen="<?php echo e($komponen->id); ?>"
                                                data-skor="<?php echo e($i); ?>">
                                            <span class="text-2xl font-bold <?php echo e($textColors[$i]); ?>"><?php echo e($i); ?></span>
                                        </button>
                                    <?php endfor; ?>
                                    <input type="hidden" 
                                           name="skor[<?php echo e($komponen->id); ?>]" 
                                           id="skor-input-<?php echo e($komponen->id); ?>"
                                           value="<?php echo e($currentSkor); ?>"
                                           required>
                                </div>
                                <?php $__errorArgs = ['skor.' . $komponen->id];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> 
                                    <p class="text-red-600 text-sm mt-1"><?php echo e($message); ?></p> 
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>

            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-2">Global Rating</h3>
                    <p class="text-sm text-gray-500 mb-4">Penilaian subjektif keseluruhan untuk mahasiswa ini pada stasi ini.</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3" id="global-rating-container">
                        <?php
                            $currentGR = old('global_rating_id', $nilai->global_rating_id ?? '');
                        ?>
                        <?php $__currentLoopData = $globalRatings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $isSelected = ($currentGR == $gr->id);
                                $grColors = [
                                    1 => $isSelected ? 'bg-red-100 border-red-500 ring-2 ring-red-500' : 'bg-white border-gray-300 hover:bg-red-50',
                                    2 => $isSelected ? 'bg-yellow-100 border-yellow-500 ring-2 ring-yellow-500' : 'bg-white border-gray-300 hover:bg-yellow-50',
                                    3 => $isSelected ? 'bg-green-100 border-green-500 ring-2 ring-green-500' : 'bg-white border-gray-300 hover:bg-green-50',
                                    4 => $isSelected ? 'bg-blue-100 border-blue-500 ring-2 ring-blue-500' : 'bg-white border-gray-300 hover:bg-blue-50',
                                ];
                                $grTextColors = [
                                    1 => 'text-red-600',
                                    2 => 'text-yellow-600',
                                    3 => 'text-green-600',
                                    4 => 'text-blue-600',
                                ];
                            ?>
                            <button type="button"
                                    onclick="selectGlobalRating(<?php echo e($gr->id); ?>, <?php echo e($gr->nilai); ?>)"
                                    class="gr-btn p-4 border-2 rounded-lg transition-all text-center cursor-pointer <?php echo e($grColors[$gr->nilai] ?? 'bg-white border-gray-300'); ?>"
                                    data-gr-id="<?php echo e($gr->id); ?>"
                                    data-gr-nilai="<?php echo e($gr->nilai); ?>">
                                <div class="text-3xl font-bold mb-1 <?php echo e($grTextColors[$gr->nilai] ?? 'text-gray-600'); ?>">
                                    <?php echo e($gr->nilai); ?>

                                </div>
                                <div class="font-medium text-gray-900"><?php echo e($gr->label); ?></div>
                                <?php if($gr->deskripsi): ?>
                                    <div class="text-xs text-gray-500 mt-1"><?php echo e($gr->deskripsi); ?></div>
                                <?php endif; ?>
                            </button>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <input type="hidden" name="global_rating_id" id="global-rating-input" value="<?php echo e($currentGR); ?>" required>
                    </div>
                    <?php $__errorArgs = ['global_rating_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-600 text-sm mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Catatan (Opsional)</h3>
                    <textarea name="catatan" rows="3" 
                        placeholder="Catatan tambahan untuk mahasiswa ini..."
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"><?php echo e(old('catatan', $nilai->catatan ?? '')); ?></textarea>
                </div>
            </div>

            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6 flex justify-end gap-4">
                    <a href="<?php echo e(route('penguji.penilaian.list', ['stasi' => $stasi, 'jadwal' => $jadwal])); ?>" 
                       class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                        Simpan Nilai
                    </button>
                </div>
            </div>
        </form>

        <script>
            function selectSkor(komponenId, skor) {
                // Update hidden input
                document.getElementById('skor-input-' + komponenId).value = skor;
                
                // Update button styles
                const container = document.getElementById('komponen-' + komponenId);
                const buttons = container.querySelectorAll('.skor-btn');
                
                const colorClasses = {
                    0: { selected: 'bg-red-200 border-red-600 ring-2 ring-red-600', default: 'bg-red-50 border-red-300 hover:bg-red-100' },
                    1: { selected: 'bg-amber-200 border-amber-600 ring-2 ring-amber-600', default: 'bg-amber-50 border-amber-300 hover:bg-amber-100' },
                    2: { selected: 'bg-yellow-200 border-yellow-600 ring-2 ring-yellow-600', default: 'bg-yellow-50 border-yellow-300 hover:bg-yellow-100' },
                    3: { selected: 'bg-green-200 border-green-600 ring-2 ring-green-600', default: 'bg-green-50 border-green-300 hover:bg-green-100' }
                };
                
                buttons.forEach(btn => {
                    const btnSkor = parseInt(btn.dataset.skor);
                    const classes = colorClasses[btnSkor];
                    
                    // Remove all possible classes first
                    const allClasses = [
                        'bg-red-200', 'border-red-600', 'ring-red-600', 'bg-red-50', 'border-red-300', 'hover:bg-red-100',
                        'bg-amber-200', 'border-amber-600', 'ring-amber-600', 'bg-amber-50', 'border-amber-300', 'hover:bg-amber-100',
                        'bg-yellow-200', 'border-yellow-600', 'ring-yellow-600', 'bg-yellow-50', 'border-yellow-300', 'hover:bg-yellow-100',
                        'bg-green-200', 'border-green-600', 'ring-green-600', 'bg-green-50', 'border-green-300', 'hover:bg-green-100',
                        'ring-2'
                    ];
                    allClasses.forEach(c => btn.classList.remove(c));
                    
                    if (btnSkor === skor) {
                        // Selected state
                        classes.selected.split(' ').forEach(c => btn.classList.add(c));
                    } else {
                        // Default state
                        classes.default.split(' ').forEach(c => btn.classList.add(c));
                    }
                });
            }
            
            function selectGlobalRating(grId, grNilai) {
                // Update hidden input
                document.getElementById('global-rating-input').value = grId;
                
                // Update button styles
                const buttons = document.querySelectorAll('.gr-btn');
                
                const grColorClasses = {
                    1: { selected: 'bg-red-100 border-red-500 ring-2 ring-red-500', hover: 'bg-white border-gray-300 hover:bg-red-50' },
                    2: { selected: 'bg-yellow-100 border-yellow-500 ring-2 ring-yellow-500', hover: 'bg-white border-gray-300 hover:bg-yellow-50' },
                    3: { selected: 'bg-green-100 border-green-500 ring-2 ring-green-500', hover: 'bg-white border-gray-300 hover:bg-green-50' },
                    4: { selected: 'bg-blue-100 border-blue-500 ring-2 ring-blue-500', hover: 'bg-white border-gray-300 hover:bg-blue-50' }
                };
                
                buttons.forEach(btn => {
                    const btnGrId = parseInt(btn.dataset.grId);
                    const btnGrNilai = parseInt(btn.dataset.grNilai);
                    
                    // Remove all state classes
                    btn.classList.remove('bg-red-100', 'border-red-500', 'ring-2', 'ring-red-500',
                                         'bg-yellow-100', 'border-yellow-500', 'ring-yellow-500',
                                         'bg-green-100', 'border-green-500', 'ring-green-500',
                                         'bg-blue-100', 'border-blue-500', 'ring-blue-500',
                                         'bg-white', 'border-gray-300', 'hover:bg-red-50',
                                         'hover:bg-yellow-50', 'hover:bg-green-50', 'hover:bg-blue-50');
                    
                    if (btnGrId === grId) {
                        // Selected state
                        grColorClasses[btnGrNilai].selected.split(' ').forEach(c => btn.classList.add(c));
                    } else {
                        // Default state
                        grColorClasses[btnGrNilai].hover.split(' ').forEach(c => btn.classList.add(c));
                    }
                });
            }
        </script>
    <?php else: ?>
        
        <?php if($allNilai && $allNilai->count() > 0): ?>
            <?php $__currentLoopData = $allNilai; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $nilaiItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">Nilai dari: <?php echo e($nilaiItem->penguji->name ?? 'Unknown'); ?></h3>
                            <div class="text-right">
                                <span class="text-2xl font-bold text-indigo-600"><?php echo e(number_format($nilaiItem->nilai_aktual ?? $nilaiItem->total_nilai, 2)); ?></span>
                                <p class="text-sm text-gray-500">Nilai Aktual</p>
                            </div>
                        </div>
                        
                        <div class="space-y-3">
                            <?php $__currentLoopData = $komponens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $komponen): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $nilaiDetail = $nilaiItem->detail->where('komponen_stasi_id', $komponen->id)->first();
                                    $skor = $nilaiDetail->skor ?? 0;
                                ?>
                                <div class="flex justify-between items-center py-2 border-b">
                                    <div>
                                        <span class="font-medium text-gray-900"><?php echo e($komponen->nama); ?></span>
                                        <span class="text-sm text-gray-500 ml-2">(Bobot: <?php echo e($komponen->bobot); ?>)</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="w-8 h-8 rounded-full flex items-center justify-center text-white font-bold
                                            <?php echo e($skor == 0 ? 'bg-red-500' : ''); ?>

                                            <?php echo e($skor == 1 ? 'bg-orange-500' : ''); ?>

                                            <?php echo e($skor == 2 ? 'bg-yellow-500' : ''); ?>

                                            <?php echo e($skor == 3 ? 'bg-green-500' : ''); ?>">
                                            <?php echo e($skor); ?>

                                        </span>
                                        <span class="text-sm text-gray-500">/ 3</span>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>

                        
                        <div class="mt-4 pt-4 border-t">
                            <span class="text-sm text-gray-500">Global Rating:</span>
                            <?php if($nilaiItem->globalRating): ?>
                                <span class="ml-2 px-3 py-1 rounded-full text-sm font-medium 
                                    <?php echo e($nilaiItem->globalRating->nilai == 1 ? 'bg-red-100 text-red-800' : ''); ?>

                                    <?php echo e($nilaiItem->globalRating->nilai == 2 ? 'bg-yellow-100 text-yellow-800' : ''); ?>

                                    <?php echo e($nilaiItem->globalRating->nilai == 3 ? 'bg-green-100 text-green-800' : ''); ?>

                                    <?php echo e($nilaiItem->globalRating->nilai == 4 ? 'bg-blue-100 text-blue-800' : ''); ?>">
                                    <?php echo e($nilaiItem->globalRating->nilai); ?> - <?php echo e($nilaiItem->globalRating->label); ?>

                                </span>
                            <?php else: ?>
                                <span class="ml-2 text-gray-400">-</span>
                            <?php endif; ?>
                        </div>

                        
                        <?php if($nilaiItem->catatan): ?>
                            <div class="mt-4 pt-4 border-t">
                                <span class="text-sm text-gray-500">Catatan:</span>
                                <p class="mt-1 text-gray-700"><?php echo e($nilaiItem->catatan); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php else: ?>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="mt-4 text-gray-500">Belum ada nilai untuk mahasiswa ini di stasi ini.</p>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\Aplikasi Ujian\resources\views/penguji/penilaian/form.blade.php ENDPATH**/ ?>