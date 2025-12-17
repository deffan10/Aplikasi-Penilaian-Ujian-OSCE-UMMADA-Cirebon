<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Gelombang ujian: setiap jadwal bisa memiliki beberapa gelombang
     * Setiap gelombang memiliki penguji berbeda per stasi
     * Setiap mahasiswa hanya ikut 1 gelombang per jadwal
     */
    public function up(): void
    {
        // Tabel Gelombang
        Schema::create('gelombang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_id')->constrained('jadwal')->cascadeOnDelete();
            $table->string('nama'); // Gelombang 1, Gelombang 2, dst
            $table->time('waktu_mulai')->nullable();
            $table->time('waktu_selesai')->nullable();
            $table->integer('urutan')->default(1);
            $table->timestamps();
            
            $table->index(['jadwal_id', 'urutan']);
        });

        // Tabel Penguji per Gelombang per Stasi (1 penguji = 1 stasi per gelombang)
        Schema::create('gelombang_penguji', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gelombang_id')->constrained('gelombang')->cascadeOnDelete();
            $table->foreignId('stasi_id')->constrained('stasi')->cascadeOnDelete();
            $table->foreignId('penguji_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            
            // 1 penguji per stasi per gelombang
            $table->unique(['gelombang_id', 'stasi_id'], 'unique_gelombang_stasi');
        });

        // Tabel Mahasiswa per Gelombang (1 mahasiswa = 1 gelombang)
        Schema::create('gelombang_mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gelombang_id')->constrained('gelombang')->cascadeOnDelete();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->cascadeOnDelete();
            $table->timestamps();
            
            // 1 mahasiswa hanya di 1 gelombang (per jadwal)
            $table->unique(['gelombang_id', 'mahasiswa_id'], 'unique_gelombang_mahasiswa');
        });

        // Tambah kolom gelombang_id ke tabel nilai
        Schema::table('nilai', function (Blueprint $table) {
            $table->foreignId('gelombang_id')->nullable()->after('jadwal_id')->constrained('gelombang')->nullOnDelete();
            $table->index('gelombang_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nilai', function (Blueprint $table) {
            $table->dropForeign(['gelombang_id']);
            $table->dropColumn('gelombang_id');
        });
        
        Schema::dropIfExists('gelombang_mahasiswa');
        Schema::dropIfExists('gelombang_penguji');
        Schema::dropIfExists('gelombang');
    }
};
