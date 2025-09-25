<?php

namespace Database\Seeders;

use App\Models\ContactSubject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContactSubjectsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = [
            'استفسار عام',
            'استفسار بخصوص منتجات "إنها لك"',
            'استفسار بخصوص برنامج "بداية الرحلة"',
            'مشكلة تقنية',
            'اقتراح أو شكوى',
        ];

        foreach ($subjects as $subject) {
            ContactSubject::create([
                'name' => $subject,
            ]);
        }
    }
}
