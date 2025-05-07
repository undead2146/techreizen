<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Trip;

class CreateTripsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $trips = [
            [
                'name' => 'Barcelona Tech Tour',
                'contact_email' => 'barcelona@techreizen.edu',
                'description' => 'Een technologische reis naar Barcelona, met focus op smart city innovaties en mobile tech bedrijven.',
                'start_date' => '2025-04-15',
                'end_date' => '2025-04-22',
            ],
            [
                'name' => 'Zürich Innovation Hub',
                'contact_email' => 'zurich@techreizen.edu',
                'description' => 'Verken de innovatieve technologische hub van Zürich, met bezoeken aan onderzoekscentra en startups.',
                'start_date' => '2025-05-10',
                'end_date' => '2025-05-17',
            ],
            [
                'name' => 'Parijs Digital Campus',
                'contact_email' => 'paris@techreizen.edu',
                'description' => 'Ontdek de digitale campus van Parijs, met focus op AI ontwikkeling en digital media.',
                'start_date' => '2025-06-05',
                'end_date' => '2025-06-12',
            ],
            [
                'name' => 'Berlin Tech Ecosystem',
                'contact_email' => 'berlin@techreizen.edu',
                'description' => 'Duik in het Berlijnse tech ecosysteem, met bezoeken aan innovatieve startups en tech hubs.',
                'start_date' => '2025-09-20',
                'end_date' => '2025-09-27',
            ]
        ];

        foreach ($trips as $trip) {
            Trip::create($trip);
        }
        
        $this->command->info('Created ' . count($trips) . ' trips successfully');
    }
}
