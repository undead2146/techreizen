<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Group;
use App\Models\Traveller;
use App\Models\User;

class CreateGroupMembersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Assigns travellers directly to groups using the group_id field.
     */
    public function run(): void
    {
        // Get all groups
        $groups = Group::all();
        
        if ($groups->isEmpty()) {
            $this->command->info('No groups found, skipping member assignments');
            return;
        }
        
        // Reset all travellers' group_id to null first to ensure clean state
        Traveller::query()->update(['group_id' => null]);
        
        $assignedCount = 0;
        
        foreach ($groups as $group) {
            // Get travellers for this trip who are not yet assigned to any group
            $availableTravellers = Traveller::whereHas('user')
                                           ->where('trip_id', $group->trip_id)
                                           ->whereNull('group_id')
                                           ->get();
            
            if ($availableTravellers->isEmpty()) {
                continue;
            }
            
            // Determine how many travellers to add (respect max_members)
            $memberCount = min($availableTravellers->count(), rand(3, min(8, $group->max_members)));
            
            // Get random selection of travellers
            $selectedTravellers = $availableTravellers->random($memberCount);
            
            // Add travellers to group
            foreach ($selectedTravellers as $traveller) {
                // Set group_id directly on traveller
                $traveller->group_id = $group->id;
                $traveller->save();
                
                $assignedCount++;
            }
            
            // Ensure one guide is assigned to each group
            $guideUser = User::where('role', 'guide')->first();
            if ($guideUser) {
                // Find guide's traveller profile or create one if not exists
                $guideTraveller = Traveller::where('user_id', $guideUser->id)
                    ->where('trip_id', $group->trip_id)
                    ->whereNull('group_id')
                    ->first();
                
                if ($guideTraveller) {
                    $guideTraveller->group_id = $group->id;
                    $guideTraveller->save();
                    $assignedCount++;
                }
            }
        }
        
        $this->command->info("Assigned {$assignedCount} travellers to groups successfully");
    }
}
