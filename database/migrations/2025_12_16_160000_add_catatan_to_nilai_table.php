<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add catatan column to nilai table
        if (!Schema::hasColumn('nilai', 'catatan')) {
            Schema::table('nilai', function (Blueprint $table) {
                $table->text('catatan')->nullable()->after('lulus_stasi');
            });
        }
    }

    public function down(): void
    {
        Schema::table('nilai', function (Blueprint $table) {
            $table->dropColumn('catatan');
        });
    }
};
