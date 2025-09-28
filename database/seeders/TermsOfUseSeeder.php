<?php

namespace Database\Seeders;

use App\Models\TermsOfUse;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TermsOfUseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TermsOfUse::updateOrCreate(
            ['id' => 1],
            [
                'main_title'     => 'شروط الاستخدام',
                'main_subtitle'  => 'آخر تحديث: يوليو 2024. يرجى قراءة هذه الشروط بعناية قبل استخدام خدماتنا.',

                'section1_title' => 'الموافقة على الشروط',
                'section1_text'  => 'باستخدامك لمنصة الرحلة، فإنك توافق على الالتزام بهذه الشروط. إذا كنت لا توافق عليها، يرجى عدم استخدام المنصة.',

                'section2_title' => 'الملكية الفكرية',
                'section2_text'  => 'المحتوى الأصلي والميزات والوظائف هي وستظل ملكية حصرية لمنصة الرحلة ومرخصيها. جميع القصص المخصصة والرسومات تظل ملكيتها للمنصة مع منح العميل ترخيصاً للاستخدام الشخصي وغير التجاري.',
            ]
        );
    }
}
