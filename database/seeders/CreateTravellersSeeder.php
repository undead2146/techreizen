<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Traveller;
use App\Models\User;
use App\Models\Trip;
use App\Models\Major;
use App\Models\Education;
use App\Models\Cities;
use Illuminate\Support\Str; // For IBAN/BIC generation

class CreateTravellersSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        // Get all traveller users
        $travellerUsers = User::where('role', 'traveller')->get();

        if ($travellerUsers->isEmpty()) {
            $this->command->info('No traveller users found, skipping traveller creation');
            // return; // Keep processing for guides
        }

        // Get all trips
        $trips = Trip::all();

        if ($trips->isEmpty()) {
            $this->command->info('No trips found, skipping traveller creation');
            return;
        }

        // Get available city IDs from the database
        $cityIds = Cities::pluck('id')->toArray();

        if (empty($cityIds)) {
            $this->command->info('No cities found in the database. Make sure to run the Cities seeder first.');
            return;
        }

        // Get all majors for academic info
        $majors = Major::all();
        $educations = Education::all();

        if ($majors->isEmpty() || $educations->isEmpty()) {
            $this->command->info('Missing majors or educations, skipping academic info for some travellers.');
        }

        // Create traveller profiles for each user
        $createdCount = 0;

        foreach ($travellerUsers as $user) {
            // Check if traveller already exists for this user
            if (Traveller::where('user_id', $user->id)->exists()) {
                continue;
            }

            // Random first/last name generation
            $firstName = $this->getRandomFirstName();
            $lastName = $this->getRandomLastName();

            // Assign to random trip
            $trip = $trips->random();

            // Create traveller record
            $traveller = new Traveller();
            $traveller->user_id = $user->id;
            $traveller->trip_id = $trip->id;
            // group_id can be null initially
            $traveller->first_name = $firstName;
            $traveller->last_name = $lastName;
            $traveller->email = strtolower(str_replace(' ', '.', $firstName) . '.' . str_replace(' ', '', $lastName) . '@student.ucll.be');
            $traveller->country = $this->getRandomCountry();
            $traveller->address = $this->getRandomStreet() . ' ' . rand(1, 150);

            // Select a random city ID from actual existing cities
            $traveller->zip_id = $cityIds[array_rand($cityIds)];

            $traveller->gender = ['M', 'V', 'X'][rand(0, 2)];
            $traveller->phone = '04' . rand(10, 99) . rand(100000, 999999);
            $traveller->emergency_phone_1 = '04' . rand(10, 99) . rand(100000, 999999);
            $traveller->emergency_phone_2 = (rand(0, 1) ? '04' . rand(10, 99) . rand(100000, 999999) : null);
            $traveller->nationality = $this->getRandomNationality();
            $traveller->birthdate = now()->subYears(rand(18, 25))->subDays(rand(0, 365));
            $traveller->birthplace = $this->getRandomBelgianCity();
            $traveller->iban = $this->generateFakeIban();
            $traveller->bic = $this->generateFakeBic();
            $traveller->medical_issue = (bool)rand(0, 1);
            $traveller->medical_info = $traveller->medical_issue ? 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.' : null;
            // remember_token is usually handled by Laravel auth, often nullable

            $education = $educations->random();
            // Ensure major belongs to the selected education if possible
            $majorForEducation = $majors->where('education_id', $education->id);
            $major = $majorForEducation->isNotEmpty() ? $majorForEducation->random() : $majors->random();
            $traveller->major_id = $major->id;

            $traveller->save();
            $createdCount++;
        }

        // Also create travellers for guide users (they may need to be part of a group too)
        $guideUsers = User::where('role', 'guide')->get();
        foreach ($guideUsers as $user) {
            // Check if traveller already exists for this user
            if (Traveller::where('user_id', $user->id)->exists()) {
                continue;
            }

            $trip = $trips->random();

            $traveller = new Traveller();
            $traveller->user_id = $user->id;
            $traveller->trip_id = $trip->id;
            // group_id can be null initially
            $traveller->first_name = 'Guide';
            $traveller->last_name = explode('@', $user->login)[0]; // Assuming login is email-like
            $traveller->email = 'guide.' . $traveller->last_name . '@ucll.be';
            $traveller->country = 'Belgium';
            $traveller->address = $this->getRandomStreet() . ' ' . rand(1, 50);

            // Select a random city ID from actual existing cities
            $traveller->zip_id = $cityIds[array_rand($cityIds)];

            $traveller->gender = ['M', 'V', 'X'][rand(0, 2)];
            $traveller->phone = '04' . rand(10, 99) . rand(100000, 999999);
            $traveller->emergency_phone_1 = '04' . rand(10, 99) . rand(100000, 999999);
            // emergency_phone_2 can be null
            $traveller->nationality = 'Belgian';
            $traveller->birthdate = now()->subYears(rand(25, 45))->subDays(rand(0, 365));
            $traveller->birthplace = $this->getRandomBelgianCity();
            $traveller->iban = $this->generateFakeIban();
            $traveller->bic = $this->generateFakeBic();
            $traveller->medical_issue = false;
            // medical_info can be null
            $traveller->major_id = null; // Set major_id to null for guides

            $traveller->save();
            $createdCount++;
        }

        $this->command->info('Created ' . $createdCount . ' traveller profiles');
    }

    /**
     * Get a random Belgian first name
     */
    private function getRandomFirstName() {
        $firstNames = [
            'Emma',
            'Liam',
            'Olivia',
            'Noah',
            'Lucas',
            'Mila',
            'Louis',
            'Ella',
            'Adam',
            'Louise',
            'Lars',
            'Marie',
            'Jules',
            'Nora',
            'Leon',
            'Sofia',
            'Victor',
            'Camille',
            'Arthur',
            'Elena',
            'Thomas',
            'Lukas',
            'Anna',
            'Mathis',
            'Alice',
            'Finn',
            'Zoe',
            'Oscar',
            'Charlotte',
            'Felix',
            'Elise',
            'Samuel',
            'Laura',
            'Gabriel',
            'Luna',
            'Alexander',
            'Julia',
            'Milan',
            'Lily'
        ];
        return $firstNames[array_rand($firstNames)];
    }

    /**
     * Get a random Belgian last name
     */
    private function getRandomLastName() {
        $lastNames = [
            'Peeters',
            'Janssens',
            'Maes',
            'Jacobs',
            'Mertens',
            'Willems',
            'Claes',
            'Goossens',
            'Wouters',
            'De Smet',
            'Dubois',
            'Lambert',
            'Dupont',
            'Martin',
            'Hendrickx',
            'Verhoeven',
            'Hermans',
            'Vermeulen',
            'Declercq',
            'Vandenberghe',
            'Verbeke',
            'Smets',
            'Verheyen',
            'Jansen',
            'Leroy',
            'Devos',
            'Desmet',
            'Lemmens',
            'Martens',
            'Verschueren',
            'De Backer',
            'Van Damme',
            'Michiels'
        ];
        return $lastNames[array_rand($lastNames)];
    }

    /**
     * Get a random Belgian street name
     */
    private function getRandomStreet() {
        $streets = [
            'Kerkstraat',
            'Schoolstraat',
            'Molenstraat',
            'Dorpsstraat',
            'Stationstraat',
            'Nieuwstraat',
            'Hoogstraat',
            'Lindenlaan',
            'Berkenlaan',
            'Eikenstraat',
            'Veldstraat',
            'Boslaan',
            'Beekstraat',
            'Kasteelstraat',
            'Markt',
            'Rozenlaan',
            'Bergstraat',
            'Vijverstraat',
            'Akkerstraat',
            'Weidestraat'
        ];
        return $streets[array_rand($streets)];
    }

    private function getRandomCountry() {
        $countries = ['Belgium', 'Netherlands', 'France', 'Germany', 'Luxembourg'];
        return $countries[array_rand($countries)];
    }

    private function getRandomNationality() {
        $nationalities = ['Belgian', 'Dutch', 'French', 'German', 'Luxembourgish'];
        return $nationalities[array_rand($nationalities)];
    }

    private function getRandomBelgianCity() {
        $cities = ['Brussels', 'Antwerp', 'Ghent', 'Charleroi', 'Liège', 'Bruges', 'Namur', 'Leuven', 'Mons', 'Aalst'];
        return $cities[array_rand($cities)];
    }

    private function generateFakeIban() {
        // Basic fake IBAN generator (BE specific for simplicity)
        return 'BE' . rand(10, 99) . ' ' . rand(1000, 9999) . ' ' . rand(1000, 9999) . ' ' . rand(1000, 9999);
    }

    private function generateFakeBic() {
        // Basic fake BIC generator
        $banks = ['KRED', 'GEBA', 'BBRU', 'AXAB', 'HBKA'];
        return $banks[array_rand($banks)] . 'BE' . rand(10, 99);
    }
}
