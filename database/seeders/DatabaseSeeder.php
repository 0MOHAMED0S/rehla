<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            ContactSubjectsSeeder::class,
            ShippingSeeder::class,
            LogoAndLinkSeeder::class,
            AboutUsSeeder::class,
            TermsOfUseSeeder::class,
            SubscribeDetailSeeder::class,
        ]);

        User::updateOrCreate(
            ['email' => 'superadmin@gmail.com'],
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@gmail.com',
                'password' => Hash::make('10203040'),
                'role_id' => 1,
            ]
        );
    }
}
