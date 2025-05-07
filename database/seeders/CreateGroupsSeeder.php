<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Group;
use App\Models\User;
use App\Models\Trip;
use App\Models\Traveller;

class CreateGroupsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all trips to create groups for each
        $trips = Trip::all();
        
        if ($trips->isEmpty()) {
            $this->command->info('No trips found, skipping group creation');
            return;
        }
        
        // Get guide/admin users who can create groups
        $guides = User::whereIn('role', ['guide', 'admin'])->get();
        
        if ($guides->isEmpty()) {
            $this->command->info('No guides found, skipping group creation');
            return;
        }

        // Create groups for each trip
        foreach ($trips as $trip) {
            // Randomly assign a guide for each trip's groups
            $creator = $guides->random();
            
            // Create 2-4 groups per trip with different configurations
            $groupCount = rand(2, 4);
            
            for ($i = 1; $i <= $groupCount; $i++) {
                $groupTheme = $this->getRandomGroupTheme();
                
                Group::create([
                    'name' => "{$groupTheme} - {$trip->name}",
                    'description' => "Een groep gefocust op {$groupTheme} tijdens de reis naar {$trip->name}.",
                    'trip_id' => $trip->id,
                    'created_by' => $creator->id,
                    'max_members' => rand(5, 15)
                ]);
            }
        }
        
        $this->command->info('Created ' . ($groupCount * $trips->count()) . ' groups successfully');
    }
    
    /**
     * Get a random interesting group theme
     */
    private function getRandomGroupTheme()
    {
        $themes = [
            'Cultuur & Geschiedenis',
            'Technologie & Innovatie',
            'Architectuur',
            'Duurzaamheid',
            'Lokale Industrie',
            'Smart Cities',
            'Gastronomie & Culinair',
            'Urban Engineering',
            'Moderne Kunst',
            'Transport & Mobiliteit'
        ];
        
        return $themes[array_rand($themes)];
    }
}
