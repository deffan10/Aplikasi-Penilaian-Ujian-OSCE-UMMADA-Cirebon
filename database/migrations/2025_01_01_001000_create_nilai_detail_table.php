<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nilai_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nilai_id')->constrained('nilai')->cascadeOnDelete();
            $table->foreignId('komponen_stasi_id')->constrained('komponen_stasi')->cascadeOnDelete();
            $table->decimal('skor', 5, 2)->default(0);
            $table->timestamps();

            $table->unique(['nilai_id', 'komponen_stasi_id']);
            $table->index('nilai_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nilai_detail');
    }
};
