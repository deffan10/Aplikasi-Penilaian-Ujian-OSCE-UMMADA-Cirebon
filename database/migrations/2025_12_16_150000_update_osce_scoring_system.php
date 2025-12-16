<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add 'nilai' column to global_ratings for numeric value (1-4)
        if (!Schema::hasColumn('global_ratings', 'nilai')) {
            Schema::table('global_ratings', function (Blueprint $table) {
                $table->tinyInteger('nilai')->default(1)->after('kode');
            });
        }
        if (!Schema::hasColumn('global_ratings', 'deskripsi')) {
            Schema::table('global_ratings', function (Blueprint $table) {
                $table->text('deskripsi')->nullable()->after('label');
            });
        }

        // 2. Update nilai_detail: change 'skor' to be 0-3 scale
        // Already decimal(5,2), this is fine for 0-3

        // 3. Add nilai_aktual column to nilai table
        if (!Schema::hasColumn('nilai', 'nilai_aktual')) {
            Schema::table('nilai', function (Blueprint $table) {
                $table->decimal('nilai_aktual', 8, 2)->default(0)->after('total_nilai');
            });
        }

        // 4. Create table for storing calculated nilai_acuan per stasi per jadwal
        if (!Schema::hasTable('nilai_acuan_stasi')) {
            Schema::create('nilai_acuan_stasi', function (Blueprint $table) {
                $table->id();
                $table->foreignId('jadwal_id')->constrained('jadwal')->onDelete('cascade');
                $table->foreignId('stasi_id')->constrained('stasi')->onDelete('cascade');
                $table->decimal('nilai_acuan', 8, 2)->default(0); // Passing score from regression
                $table->decimal('intercept', 10, 4)->nullable(); // Regression intercept
                $table->decimal('slope', 10, 4)->nullable(); // Regression slope
                $table->integer('sample_count')->default(0); // Number of samples used
                $table->timestamp('calculated_at')->nullable();
                $table->timestamps();

                $table->unique(['jadwal_id', 'stasi_id']);
            });
        }

        // 5. Add lulus_stasi column
        if (!Schema::hasColumn('nilai', 'lulus_stasi')) {
            Schema::table('nilai', function (Blueprint $table) {
                $table->boolean('lulus_stasi')->nullable()->after('nilai_aktual');
            });
        }
    }

    public function down(): void
    {
        Schema::table('nilai', function (Blueprint $table) {
            $table->dropColumn(['nilai_aktual', 'lulus_stasi']);
        });

        Schema::dropIfExists('nilai_acuan_stasi');

        Schema::table('global_ratings', function (Blueprint $table) {
            $table->dropColumn(['nilai', 'deskripsi']);
        });
    }
};
