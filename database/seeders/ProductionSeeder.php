<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\GlobalRating;
use App\Models\Kelas;
use App\Models\Stasi;
use App\Models\KomponenStasi;
use Illuminate\Support\Facades\Hash;

/**
 * Production Seeder - Untuk deployment ke production
 * 
 * Seeder ini berisi data master minimal yang diperlukan untuk menjalankan
 * aplikasi OSCE Assessment. Data ini meliputi:
 * - Global Rating (4 tingkat: Tidak Lulus, Borderline, Lulus, Superior)
 * - User Admin default
 * 
 * Jalankan dengan: php artisan db:seed --class=ProductionSeeder
 * 
 * @since 2025-12-16
 * @version 1.0.0
 */
class ProductionSeeder extends Seeder
{
    /**
     * Seed the application's database for production.
     */
    public function run(): void
    {
        $this->call([
            GlobalRatingSeeder::class,
        ]);
        
        // Create default admin user
        $this->createDefaultAdmin();
        
        $this->command->info('Production seeder completed!');
        $this->command->info('Default admin: admin@osce.test / password');
        $this->command->warn('PENTING: Segera ubah password admin setelah login!');
    }
    
    /**
     * Create default admin user
     */
    private function createDefaultAdmin(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@osce.test'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );
        
        $this->command->info('Default admin user created.');
    }
}
