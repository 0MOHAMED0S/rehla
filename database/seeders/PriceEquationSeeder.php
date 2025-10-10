<?php

namespace Database\Seeders;

use App\Models\PriceEquation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PriceEquationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
        public function run(): void
    {
        PriceEquation::updateOrCreate(
            ['id' => 1],
            [
                'base_price' => 150,
                'multiplier' => 6.5,
            ]
        );
    }
}
