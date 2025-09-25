<?php

namespace Database\Seeders;

use App\Models\Shipping;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShippingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $shippings = [
            ['name' => 'القاهرة', 'price' => 50],
            ['name' => 'الجيزة', 'price' => 55],
            ['name' => 'القليوبية', 'price' => 55],
            ['name' => 'الإسكندرية', 'price' => 60],
            ['name' => 'بورسعيد', 'price' => 60],
            ['name' => 'الإسماعيلية', 'price' => 60],
            ['name' => 'السويس', 'price' => 60],
            ['name' => 'دمياط', 'price' => 65],
            ['name' => 'الدقهلية', 'price' => 65],
            ['name' => 'الشرقية', 'price' => 65],
            ['name' => 'الغربية', 'price' => 65],
            ['name' => 'المنوفية', 'price' => 65],
            ['name' => 'كفر الشيخ', 'price' => 65],
            ['name' => 'الفيوم', 'price' => 70],
            ['name' => 'بني سويف', 'price' => 70],
            ['name' => 'المنيا', 'price' => 75],
            ['name' => 'أسيوط', 'price' => 75],
            ['name' => 'سوهاج', 'price' => 80],
            ['name' => 'قنا', 'price' => 85],
            ['name' => 'الأقصر', 'price' => 85],
            ['name' => 'أسوان', 'price' => 90],
            ['name' => 'البحر الأحمر', 'price' => 95],
            ['name' => 'الوادى الجديد', 'price' => 95],
            ['name' => 'مطروح', 'price' => 100],
            ['name' => 'شمال سيناء', 'price' => 110],
            ['name' => 'جنوب سيناء', 'price' => 110],
        ];

        foreach ($shippings as $shipping) {
            Shipping::create($shipping);
        }
    }
}
