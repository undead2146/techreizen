<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class CreateUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin and standard users
        $baseUsers = [
            ['login' => 'admin', 'role' => 'admin', 'password' => bcrypt('admin')],
            ['login' => 'guest', 'role' => 'guest', 'password' => bcrypt('guest')],
        ];
        
        // Create multiple guide users
        $guideUsers = [
            ['login' => 'guide1', 'role' => 'guide', 'password' => bcrypt('guide')],
            ['login' => 'guide2', 'role' => 'guide', 'password' => bcrypt('guide')],
            ['login' => 'guide3', 'role' => 'guide', 'password' => bcrypt('guide')],
        ];
        
        // Create multiple traveller users
        $travellerUsers = [];
        for ($i = 1; $i <= 10; $i++) {
            $travellerUsers[] = [
                'login' => "traveller{$i}", 
                'role' => 'traveller', 
                'password' => bcrypt('traveller')
            ];
        }
        
        // Combine all users and create them
        $allUsers = array_merge($baseUsers, $guideUsers, $travellerUsers);
        
        foreach ($allUsers as $user) {
            User::create($user);
        }
        
        $this->command->info('Created ' . count($allUsers) . ' test users');
    }
}
