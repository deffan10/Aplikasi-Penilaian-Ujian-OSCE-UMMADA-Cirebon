<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL won't let us drop a unique index that is used by a foreign key.
        // We must drop the FK first, drop the index, add the new index, then re-add the FK.
        Schema::table('gelombang_penguji', function (Blueprint $table) {
            $table->dropForeign(['stasi_id']);
            $table->dropUnique('unique_gelombang_stasi');
            $table->unique(['gelombang_id', 'stasi_id', 'penguji_id'], 'unique_gelombang_stasi_penguji');
            $table->foreign('stasi_id')->references('id')->on('stasi')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('gelombang_penguji', function (Blueprint $table) {
            $table->dropForeign(['stasi_id']);
            $table->dropUnique('unique_gelombang_stasi_penguji');
            $table->unique(['gelombang_id', 'stasi_id'], 'unique_gelombang_stasi');
            $table->foreign('stasi_id')->references('id')->on('stasi')->cascadeOnDelete();
        });
    }
};
