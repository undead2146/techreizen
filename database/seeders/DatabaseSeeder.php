<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call all seeders here
        $this->call([
            CreateTripsSeeder::class,
            CreateUsersSeeder::class,
            CreateEducationsSeeder::class,
            CreateMajorsSeeder::class,
            CreateCitiesSeeder::class,
            CreateGroupsSeeder::class,
            // Add other seeders here if needed
        ]);

        // User::factory(10)->create();
    }
}
