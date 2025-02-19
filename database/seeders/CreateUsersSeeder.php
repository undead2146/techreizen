<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
class CreateUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin',
                'email' => 'admin@testing.test',
                'role' => 'admin',
                'password' => bcrypt('admin'),
            ],
            [
                'name' => 'Traveller',
                'email' => 'traveller@testing.test',
                'role' => 'traveller',
                'password' => bcrypt('traveller'),
            ],
            [
                'name' => 'Guide',
                'email' => 'guide@testing.test',
                'role' => 'guide',
                'password' => bcrypt('guide'),
            ],
        ];
        foreach ($users as $key => $user) {
            User::create($user);
        }
    }
}
