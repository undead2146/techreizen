<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Group;
use App\Models\Traveller;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateGroupMembersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all groups
        $groups = Group::all();
        
        if ($groups->isEmpty()) {
            $this->command->info('No groups found, skipping member assignments');
            return;
        }
        
        // Get all travellers
        $allTravellers = Traveller::whereHas('user')->get();
        
        if ($allTravellers->isEmpty()) {
            $this->command->info('No travellers found, skipping member assignments');
            return;
        }

        $assignedCount = 0;
        
        foreach ($groups as $group) {
            // Get traveller users for this trip who can join the group
            $travellers = $allTravellers->where('trip_id', $group->trip_id);
            
            if ($travellers->isEmpty()) {
                continue;
            }
            
            // Add 3-6 random travelers to each group, respecting the max_members limit
            $memberCount = min(count($travellers), rand(3, min(6, $group->max_members)));
            $randomTravellers = $travellers->random($memberCount);
            
            foreach ($randomTravellers as $traveller) {
                // Insert directly into group_members table
                DB::table('group_members')->insertOrIgnore([
                    'group_id' => $group->id,
                    'traveller_id' => $traveller->id, 
                    'joined_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                // Also update the traveller's group_id for direct relationship
                $traveller->update(['group_id' => $group->id]);
                
                $assignedCount++;
            }
        }
        
        $this->command->info("Assigned $assignedCount travellers to groups successfully");
    }
}
