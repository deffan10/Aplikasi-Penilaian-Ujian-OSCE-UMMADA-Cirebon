<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Use raw SQL to ensure correct execution order.
        // 1. Find and drop ALL foreign keys on stasi_id column
        $fks = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
              AND TABLE_NAME = 'gelombang_penguji' 
              AND COLUMN_NAME = 'stasi_id' 
              AND REFERENCED_TABLE_NAME IS NOT NULL
        ");

        foreach ($fks as $fk) {
            DB::statement("ALTER TABLE `gelombang_penguji` DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
        }

        // 2. Now drop the unique index
        DB::statement("ALTER TABLE `gelombang_penguji` DROP INDEX `unique_gelombang_stasi`");

        // 3. Add new unique index (allows multiple penguji per stasi)
        DB::statement("ALTER TABLE `gelombang_penguji` ADD UNIQUE `unique_gelombang_stasi_penguji` (`gelombang_id`, `stasi_id`, `penguji_id`)");

        // 4. Re-add the foreign key
        DB::statement("ALTER TABLE `gelombang_penguji` ADD CONSTRAINT `gelombang_penguji_stasi_id_foreign` FOREIGN KEY (`stasi_id`) REFERENCES `stasi` (`id`) ON DELETE CASCADE");
    }

    public function down(): void
    {
        // Remove duplicates first (keep first entry per gelombang+stasi)
        $duplicates = DB::select("
            SELECT id FROM gelombang_penguji 
            WHERE id NOT IN (
                SELECT MIN(id) FROM gelombang_penguji GROUP BY gelombang_id, stasi_id
            )
        ");
        if (count($duplicates) > 0) {
            $ids = array_map(fn($d) => $d->id, $duplicates);
            DB::table('gelombang_penguji')->whereIn('id', $ids)->delete();
        }

        DB::statement("ALTER TABLE `gelombang_penguji` DROP FOREIGN KEY `gelombang_penguji_stasi_id_foreign`");
        DB::statement("ALTER TABLE `gelombang_penguji` DROP INDEX `unique_gelombang_stasi_penguji`");
        DB::statement("ALTER TABLE `gelombang_penguji` ADD UNIQUE `unique_gelombang_stasi` (`gelombang_id`, `stasi_id`)");
        DB::statement("ALTER TABLE `gelombang_penguji` ADD CONSTRAINT `gelombang_penguji_stasi_id_foreign` FOREIGN KEY (`stasi_id`) REFERENCES `stasi` (`id`) ON DELETE CASCADE");
    }
};
