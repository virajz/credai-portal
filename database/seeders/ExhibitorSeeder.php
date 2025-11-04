<?php

namespace Database\Seeders;

use App\Models\Exhibitor;
use Illuminate\Database\Seeder;

class ExhibitorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $exhibitors = [
            [
                'brand_name' => 'Prestige Estates',
                'office_address' => '123 Ring Road, Adajan',
                'city' => 'Surat',
                'contact_person_name' => 'Rajesh Kumar',
                'phone_number' => '+91 98765 43210',
                'email' => 'rajesh@prestigeestates.com',
                'website' => 'https://www.prestigeestates.com',
                'facia_name' => 'PRESTIGE ESTATES',
                'video_url' => 'https://youtube.com/watch?v=example1',
                'social_media_links' => [
                    'facebook' => 'https://facebook.com/prestigeestates',
                    'linkedin' => 'https://linkedin.com/company/prestige-estates',
                    'instagram' => 'https://instagram.com/prestigeestates',
                ],
            ],
            [
                'brand_name' => 'Green Valley Developers',
                'office_address' => '456 Ashram Road, Ellis Bridge',
                'city' => 'Ahmedabad',
                'contact_person_name' => 'Priya Patel',
                'phone_number' => '+91 98234 56789',
                'email' => 'priya@greenvalley.com',
                'website' => 'https://www.greenvalley.com',
                'facia_name' => 'GREEN VALLEY',
                'social_media_links' => [
                    'facebook' => 'https://facebook.com/greenvalley',
                    'instagram' => 'https://instagram.com/greenvalley',
                ],
            ],
            [
                'brand_name' => 'Royal Homes',
                'office_address' => '789 RC Dutt Road, Alkapuri',
                'city' => 'Baroda',
                'contact_person_name' => 'Amit Shah',
                'phone_number' => '+91 99876 54321',
                'email' => 'amit@royalhomes.com',
                'facia_name' => 'ROYAL HOMES',
                'video_url' => 'https://youtube.com/watch?v=example2',
            ],
            [
                'brand_name' => 'Sunshine Builders',
                'office_address' => '321 Station Road, Near Railway Station',
                'city' => 'Navsari',
                'contact_person_name' => 'Neha Desai',
                'phone_number' => '+91 97654 32109',
                'email' => 'neha@sunshinebuilders.com',
                'website' => 'https://www.sunshinebuilders.com',
                'facia_name' => 'SUNSHINE BUILDERS',
                'social_media_links' => [
                    'facebook' => 'https://facebook.com/sunshinebuilders',
                    'linkedin' => 'https://linkedin.com/company/sunshine-builders',
                ],
            ],
            [
                'brand_name' => 'Elite Properties',
                'office_address' => '555 Pal Main Road, City Light',
                'city' => 'Surat',
                'contact_person_name' => 'Karan Mehta',
                'phone_number' => '+91 96543 21098',
                'email' => 'karan@eliteproperties.com',
                'website' => 'https://www.eliteproperties.com',
                'facia_name' => 'ELITE PROPERTIES',
                'social_media_links' => [
                    'instagram' => 'https://instagram.com/eliteproperties',
                ],
            ],
            [
                'brand_name' => 'Metro Constructions',
                'office_address' => '888 SG Highway, Bodakdev',
                'city' => 'Ahmedabad',
                'contact_person_name' => 'Sneha Joshi',
                'phone_number' => '+91 95432 10987',
                'facia_name' => 'METRO CONSTRUCTIONS',
                'video_url' => 'https://youtube.com/watch?v=example3',
            ],
        ];

        foreach ($exhibitors as $exhibitor) {
            Exhibitor::create($exhibitor);
        }
    }
}
