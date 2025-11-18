<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = [
            ['company_name' => 'Veer', 'main_person_name' => 'Sid Shah', 'registered_number' => '9712934788', 'stall_type' => 'Associate 2', 'stall_number' => 'A-2', 'stall_size' => '6x3'],
            ['company_name' => 'Gruham', 'main_person_name' => 'Hareshbhai', 'registered_number' => '9824355311', 'stall_type' => 'Associate 3', 'stall_number' => 'A-3', 'stall_size' => '6x3'],
            ['company_name' => 'Roongta', 'main_person_name' => 'Anurbhai', 'registered_number' => '9712934788', 'stall_type' => 'Associate 4', 'stall_number' => 'A-4', 'stall_size' => '6x3'],
            ['company_name' => 'Raghuveer Developers', 'main_person_name' => 'Abhishek', 'registered_number' => '9825822213', 'stall_type' => 'Associate 5', 'stall_number' => 'A-5', 'stall_size' => '6x3'],
            ['company_name' => 'Atlanta', 'main_person_name' => 'Ketan Bhai', 'registered_number' => '9820020095', 'stall_type' => 'Co Sponsor 1', 'stall_number' => 'CS-1', 'stall_size' => '6x3'],
            ['company_name' => 'SNJ Prestige Group', 'main_person_name' => 'Jinal Italiya', 'registered_number' => '9825308965', 'stall_type' => 'Co Sponsor 2', 'stall_number' => 'CS-2', 'stall_size' => '6x3'],
            ['company_name' => 'Piramyd Shubh Darsh', 'main_person_name' => 'Magukiya', 'registered_number' => '9978622383', 'stall_type' => 'Co Sponsor 3', 'stall_number' => 'CS-3', 'stall_size' => '6x3'],
            ['company_name' => 'Universal', 'main_person_name' => 'Saileshbhai', 'registered_number' => '9879449056', 'stall_type' => 'Co Sponsor 4', 'stall_number' => 'CS-4', 'stall_size' => '6x3'],
            ['company_name' => 'Jainam', 'main_person_name' => 'Shreyanshbhai', 'registered_number' => '9099468358', 'stall_type' => 'Co Sponsor 5', 'stall_number' => 'CS-5', 'stall_size' => '6x3'],
            ['company_name' => 'SNS', 'main_person_name' => 'Devangbhai', 'registered_number' => '9825041960', 'stall_type' => 'Co Sponsor 6', 'stall_number' => 'CS-6', 'stall_size' => '6x3'],
            ['company_name' => 'DMD', 'main_person_name' => 'Shipra', 'registered_number' => '7990273128', 'stall_type' => 'Co Sponsor 7', 'stall_number' => 'CS-7', 'stall_size' => '6x3'],
            ['company_name' => 'Dhanlaxmi', 'main_person_name' => 'Jimmybhai', 'registered_number' => '8758011999', 'stall_type' => 'Co Sponsor 8', 'stall_number' => 'CS-8', 'stall_size' => '6x3'],
            ['company_name' => 'Ankit Agarwal', 'main_person_name' => 'Ankit Agarwal', 'registered_number' => '9712023155', 'stall_type' => 'Premium', 'stall_number' => 'P-1', 'stall_size' => '6x4'],
            ['company_name' => 'Kachhani', 'main_person_name' => 'Rajeshbhai', 'registered_number' => '9879399587', 'stall_type' => 'Premium', 'stall_number' => 'P-3', 'stall_size' => '6x4'],
            ['company_name' => 'Hiren Jagani', 'main_person_name' => 'Hiren Jagani', 'registered_number' => '9825959214', 'stall_type' => 'Premium', 'stall_number' => 'P-5', 'stall_size' => '6x4'],
            ['company_name' => 'Sonani', 'main_person_name' => 'Nileshbhai', 'registered_number' => '9825140390', 'stall_type' => 'Premium', 'stall_number' => 'P-7', 'stall_size' => '6x4'],
            ['company_name' => 'Jasani', 'main_person_name' => 'Nikulbhai', 'registered_number' => '9978383447', 'stall_type' => 'Premium', 'stall_number' => 'P-10', 'stall_size' => '6x4'],
            ['company_name' => 'Dobariya Jay', 'main_person_name' => 'Kevadiya', 'registered_number' => '9537519400', 'stall_type' => 'Premium', 'stall_number' => 'P-11', 'stall_size' => '6x4'],
            ['company_name' => 'Victoria', 'main_person_name' => 'Girdharbhai', 'registered_number' => '9825173221', 'stall_type' => 'Premium', 'stall_number' => 'P-12', 'stall_size' => '6x4'],
            ['company_name' => 'Foresta', 'main_person_name' => 'Kaushal Lehari', 'registered_number' => '9979947677', 'stall_type' => 'Premium', 'stall_number' => 'P-13', 'stall_size' => '6x4'],
            ['company_name' => 'Kelly', 'main_person_name' => 'Piyushbhai', 'registered_number' => '6353369899', 'stall_type' => 'Premium', 'stall_number' => 'P-14', 'stall_size' => '6x4'],
            ['company_name' => 'Sunny', 'main_person_name' => 'Chandwani', 'registered_number' => '9879397643', 'stall_type' => 'Premium', 'stall_number' => 'P-15', 'stall_size' => '6x4'],
            ['company_name' => 'Hardikbhai', 'main_person_name' => 'Hardikbhai', 'registered_number' => '9978624294', 'stall_type' => 'Premium', 'stall_number' => 'P-17', 'stall_size' => '6x4'],
            ['company_name' => 'Rajubhai', 'main_person_name' => 'Rajubhai', 'registered_number' => '9825861011', 'stall_type' => 'Premium', 'stall_number' => 'P-19', 'stall_size' => '6x4'],
            ['company_name' => 'Harnishbhai', 'main_person_name' => 'Harnishbhai', 'registered_number' => '9978111118', 'stall_type' => 'Platinum', 'stall_number' => 'PL-1', 'stall_size' => '6x3'],
            ['company_name' => 'Tej', 'main_person_name' => 'Jigneshbhai', 'registered_number' => '9375544000', 'stall_type' => 'Platinum', 'stall_number' => 'PL-3', 'stall_size' => '6x3'],
            ['company_name' => 'Karshanbhai', 'main_person_name' => 'Karshanbhai', 'registered_number' => '9727712315', 'stall_type' => 'Platinum', 'stall_number' => 'PL-5', 'stall_size' => '6x3'],
        ];

        foreach ($companies as $companyData) {
            Company::create($companyData);
        }

        $this->command->info('Successfully seeded ' . count($companies) . ' companies.');
    }
}
