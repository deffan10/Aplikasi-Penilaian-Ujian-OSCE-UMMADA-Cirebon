<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Add the NEW unique index first (MySQL needs an index for FKs at all times)
        DB::statement("ALTER TABLE `gelombang_penguji` ADD UNIQUE `unique_gelombang_stasi_penguji` (`gelombang_id`, `stasi_id`, `penguji_id`)");

        // Step 2: Drop ALL foreign keys that depend on the old unique index
        $fks = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.TABLE_CONSTRAINTS 
            WHERE TABLE_SCHEMA = DATABASE() 
              AND TABLE_NAME = 'gelombang_penguji' 
              AND CONSTRAINT_TYPE = 'FOREIGN KEY'
        ");
        foreach ($fks as $fk) {
            DB::statement("ALTER TABLE `gelombang_penguji` DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
        }

        // Step 3: Now safe to drop the old unique index
        DB::statement("ALTER TABLE `gelombang_penguji` DROP INDEX `unique_gelombang_stasi`");

        // Step 4: Re-add foreign keys
        DB::statement("ALTER TABLE `gelombang_penguji` ADD CONSTRAINT `gelombang_penguji_gelombang_id_foreign` FOREIGN KEY (`gelombang_id`) REFERENCES `gelombang` (`id`) ON DELETE CASCADE");
        DB::statement("ALTER TABLE `gelombang_penguji` ADD CONSTRAINT `gelombang_penguji_stasi_id_foreign` FOREIGN KEY (`stasi_id`) REFERENCES `stasi` (`id`) ON DELETE CASCADE");
        DB::statement("ALTER TABLE `gelombang_penguji` ADD CONSTRAINT `gelombang_penguji_penguji_id_foreign` FOREIGN KEY (`penguji_id`) REFERENCES `users` (`id`) ON DELETE CASCADE");
    }

    public function down(): void
    {
        // Remove duplicates first
        $dupes = DB::select("
            SELECT id FROM gelombang_penguji 
            WHERE id NOT IN (SELECT MIN(id) FROM gelombang_penguji GROUP BY gelombang_id, stasi_id)
        ");
        if (count($dupes) > 0) {
            DB::table('gelombang_penguji')->whereIn('id', array_map(fn($d) => $d->id, $dupes))->delete();
        }

        DB::statement("ALTER TABLE `gelombang_penguji` ADD UNIQUE `unique_gelombang_stasi` (`gelombang_id`, `stasi_id`)");

        $fks = DB::select("
            SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS 
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'gelombang_penguji' AND CONSTRAINT_TYPE = 'FOREIGN KEY'
        ");
        foreach ($fks as $fk) {
            DB::statement("ALTER TABLE `gelombang_penguji` DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
        }

        DB::statement("ALTER TABLE `gelombang_penguji` DROP INDEX `unique_gelombang_stasi_penguji`");

        DB::statement("ALTER TABLE `gelombang_penguji` ADD CONSTRAINT `gelombang_penguji_gelombang_id_foreign` FOREIGN KEY (`gelombang_id`) REFERENCES `gelombang` (`id`) ON DELETE CASCADE");
        DB::statement("ALTER TABLE `gelombang_penguji` ADD CONSTRAINT `gelombang_penguji_stasi_id_foreign` FOREIGN KEY (`stasi_id`) REFERENCES `stasi` (`id`) ON DELETE CASCADE");
        DB::statement("ALTER TABLE `gelombang_penguji` ADD CONSTRAINT `gelombang_penguji_penguji_id_foreign` FOREIGN KEY (`penguji_id`) REFERENCES `users` (`id`) ON DELETE CASCADE");
    }
};
