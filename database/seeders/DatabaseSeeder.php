<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

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
            CreateTravellersSeeder::class,
            CreateGroupsSeeder::class,
            CreateGroupMembersSeeder::class,
        ]);

        // User::factory(10)->create();
    }
}
