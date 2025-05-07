<?php

namespace Database\Seeders;

use App\Models\Group;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class CreateGroupsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $groups = [
            ['name' => 'Group A', 'max_members' => 10],
            ['name' => 'Group B', 'max_members' => 15],
            ['name' => 'Group C', 'max_members' => 20],
        ];

        foreach ($groups as $group) {
            Group::create($group);
        }
    }
}
