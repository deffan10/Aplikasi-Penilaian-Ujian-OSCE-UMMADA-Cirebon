<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penguji_stasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('stasi_id')->constrained('stasi')->cascadeOnDelete();
            $table->boolean('aktif')->default(true);
            $table->timestamps();

            $table->unique(['user_id', 'stasi_id']);
            $table->index('user_id');
            $table->index('stasi_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penguji_stasi');
    }
};
