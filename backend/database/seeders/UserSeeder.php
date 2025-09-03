<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create Super Admin
        User::firstOrCreate([
            'email' => 'admin@quickdrive.com',
        ], [
            'name' => 'Admin',
            'password' => Hash::make('password123'),
            'role' => 'super_admin',
        ]);

        // Create Developer  
        User::firstOrCreate([
            'email' => 'developer@quickdrive.com',
        ], [
            'name' => 'Developer',
            'password' => Hash::make('password123'),
            'role' => 'developer',
        ]);

        // Create Demo Client
        User::firstOrCreate([
            'email' => 'client@quickdrive.com',
        ], [
            'name' => 'Demo Client',
            'password' => Hash::make('password123'),
            'role' => 'client',
        ]);

        $this->command->info('Users seeded successfully!');
        $this->command->info('Login credentials:');
        $this->command->info('- Admin: admin@quickdrive.com / password123');
        $this->command->info('- Developer: developer@quickdrive.com / password123');
        $this->command->info('- Client: client@quickdrive.com / password123');
    }
}