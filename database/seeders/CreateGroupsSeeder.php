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
            ['name' => 'Group A'],
            ['name' => 'Group B'],
            ['name' => 'Group C'],
        ];

        foreach ($groups as $group) {
            Group::create($group);
        }
    }
}
