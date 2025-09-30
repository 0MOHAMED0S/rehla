<?php

namespace Database\Seeders;

use App\Models\SubscribeDetails;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubscribeDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SubscribeDetails::updateOrCreate(
            ['title' => 'الباقة الأساسية'],
            [
                'description' => 'وصف مختصر للباقة الأساسية',
                'price'       => 100,
                'image'       => 'images/basic.png',
                'features'    => 'دعم أساسي، تحديثات شهرية، وصول محدود',
            ]
        );
    }
}
