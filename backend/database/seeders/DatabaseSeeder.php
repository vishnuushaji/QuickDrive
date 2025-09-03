<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Call UserSeeder
        $this->call([
            UserSeeder::class,
        ]);
    }
}