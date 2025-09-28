<?php

namespace Database\Seeders;

use App\Models\AboutUs;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AboutUsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AboutUs::updateOrCreate(
            ['id' => 1],
            [
                'main_title'     => 'رحلة كل طفل تبدأ بقصة',
                'main_subtitle'  => 'نؤمن في منصة الرحلة أن لكل طفل هو بطل حكايته، لذلك نصنع حبكات قصصية ومنتجات تربوية مخصصة تعكس شخصية الطفل وتبرز هويته.',

                'section1_title' => 'لم نحن؟',
                'section1_text'  => 'منصة الرحلة هي منظومة تربوية إبداعية متكاملة تسعى لأن تكون الرفيق الأمثل لكل طفل في رحلته نحو اكتشاف ذاته.',

                'section2_title' => 'مشروع "إلهام لك"',
                'section2_text'  => 'هو محور أساسي في منصتنا، حيث نصنع قصصاً ومنتجات تربوية فريدة، الطفل لا يقرأ قصة، بل يعيشها، يرى اسمه وصورته وتفاصيله منسوجة في حكاية ملهمة.',

                'section3_title' => 'برنامج "إبداعات الرحلة"',
                'section3_text'  => 'هو برنامج متكامل لتنمية مهارات الكتابة الإبداعية لدى الأطفال والشباب، في بيئة رقمية آمنة ومشوقة، بإشراف مدربين متخصصين.',

                'section4_title' => 'رؤيتنا',
                'section4_text'  => 'أن تكون الوجهة الأولى لكل أسرة عربية تبحث عن محتوى تربوي إبداعي وأصيل ينشئ شخصية الطفل، يعزز ارتباطه بلغته وهويته، ويطلق العنان لخياله.',
            ]
        );
    }
}
