<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('komponen_stasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stasi_id')->constrained('stasi')->cascadeOnDelete();
            $table->string('nama');
            $table->unsignedTinyInteger('bobot'); // 0-100
            $table->unsignedSmallInteger('urutan')->default(0);
            $table->timestamps();

            $table->index('stasi_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('komponen_stasi');
    }
};
