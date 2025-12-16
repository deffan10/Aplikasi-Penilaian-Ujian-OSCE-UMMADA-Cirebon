<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_penilaian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nilai_id')->constrained('nilai')->cascadeOnDelete();
            $table->foreignId('penguji_id')->constrained('users')->cascadeOnDelete();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index('nilai_id');
            $table->index('penguji_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_penilaian');
    }
};
