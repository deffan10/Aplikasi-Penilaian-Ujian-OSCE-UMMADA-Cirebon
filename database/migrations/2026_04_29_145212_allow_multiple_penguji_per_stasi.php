<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Allow multiple penguji per stasi per gelombang.
     * Drop the old unique constraint (gelombang_id, stasi_id)
     * and add a new one (gelombang_id, stasi_id, penguji_id)
     * so the same penguji can't be assigned twice to the same stasi,
     * but different penguji CAN be assigned to the same stasi.
     */
    public function up(): void
    {
        Schema::table('gelombang_penguji', function (Blueprint $table) {
            $table->dropUnique('unique_gelombang_stasi');
            $table->unique(['gelombang_id', 'stasi_id', 'penguji_id'], 'unique_gelombang_stasi_penguji');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gelombang_penguji', function (Blueprint $table) {
            $table->dropUnique('unique_gelombang_stasi_penguji');
            $table->unique(['gelombang_id', 'stasi_id'], 'unique_gelombang_stasi');
        });
    }
};
