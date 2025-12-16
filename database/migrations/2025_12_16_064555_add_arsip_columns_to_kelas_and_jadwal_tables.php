<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tambah kolom arsip di tabel kelas
        Schema::table('kelas', function (Blueprint $table) {
            $table->string('tahun_akademik', 9)->nullable()->after('nama'); // format: 2025/2026
            $table->enum('semester', ['ganjil', 'genap'])->nullable()->after('tahun_akademik');
            $table->boolean('is_arsip')->default(false)->after('semester');
            $table->timestamp('diarsipkan_pada')->nullable()->after('is_arsip');
        });

        // Tambah kolom arsip di tabel jadwal
        Schema::table('jadwal', function (Blueprint $table) {
            $table->string('tahun_akademik', 9)->nullable()->after('keterangan'); // format: 2025/2026
            $table->enum('semester', ['ganjil', 'genap'])->nullable()->after('tahun_akademik');
            $table->boolean('is_arsip')->default(false)->after('semester');
            $table->timestamp('diarsipkan_pada')->nullable()->after('is_arsip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kelas', function (Blueprint $table) {
            $table->dropColumn(['tahun_akademik', 'semester', 'is_arsip', 'diarsipkan_pada']);
        });

        Schema::table('jadwal', function (Blueprint $table) {
            $table->dropColumn(['tahun_akademik', 'semester', 'is_arsip', 'diarsipkan_pada']);
        });
    }
};
