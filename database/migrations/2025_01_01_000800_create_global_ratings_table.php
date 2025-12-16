<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('global_ratings', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique(); // LULUS, BORDERLINE, TIDAK_LULUS
            $table->string('label');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('global_ratings');
    }
};
