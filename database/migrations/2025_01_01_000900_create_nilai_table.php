<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nilai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_id')->constrained('jadwal')->cascadeOnDelete();
            $table->foreignId('stasi_id')->constrained('stasi')->cascadeOnDelete();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->cascadeOnDelete();
            $table->foreignId('penguji_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('global_rating_id')->nullable()->constrained('global_ratings')->nullOnDelete();
            $table->decimal('total_nilai', 5, 2)->default(0);
            $table->timestamps();

            $table->unique(['jadwal_id', 'stasi_id', 'mahasiswa_id', 'penguji_id'], 'nilai_unique');
            $table->index('jadwal_id');
            $table->index('stasi_id');
            $table->index('mahasiswa_id');
            $table->index('penguji_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nilai');
    }
};
