<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_id')->constrained('jadwal')->cascadeOnDelete();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['jadwal_id', 'mahasiswa_id']);
            $table->index('jadwal_id');
            $table->index('mahasiswa_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_mahasiswa');
    }
};
