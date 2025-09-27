<?php

namespace Database\Seeders;

use App\Models\LogoAndLink;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LogoAndLinkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        LogoAndLink::updateOrCreate(
            ['id' => 1],
            [
                'main_logo'                => 'logos/main_logo.png',
                'creative_writing_logo'    => 'logos/creative_writing.png',
                'gate_inha_lak_image'      => 'logos/inha_lak.png',
                'gate_start_journey_image' => 'logos/start_journey.png',
                'about_page_image'         => 'logos/about.png',
                'facebook_link'            => 'https://facebook.com/yourpage',
                'twitter_link'             => 'https://twitter.com/yourpage',
                'instagram_link'           => 'https://instagram.com/yourpage',
            ]
        );
    }
}
