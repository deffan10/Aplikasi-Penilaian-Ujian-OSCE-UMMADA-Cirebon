<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::firstOrCreate(
            ['email' => 'admin@osce.test'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // Penguji users
        User::firstOrCreate(
            ['email' => 'penguji1@osce.test'],
            [
                'name' => 'Penguji Satu',
                'password' => Hash::make('password'),
                'role' => 'penguji',
            ]
        );

        User::firstOrCreate(
            ['email' => 'penguji2@osce.test'],
            [
                'name' => 'Penguji Dua',
                'password' => Hash::make('password'),
                'role' => 'penguji',
            ]
        );
    }
}
