<?php

namespace Database\Seeders;

use App\Models\NutritionGrowthStandard;
use Illuminate\Database\Seeder;

class NutritionGrowthStandardSeeder extends Seeder
{
    public function run(): void
    {
        NutritionGrowthStandard::truncate();

        $rows = [
            // ตัวอย่าง: เพศชาย อายุ 120 เดือน
            [
                'gender' => 'male',
                'age_month' => 120,
                'height_cm' => null,
                'standard_type' => 'height_for_age',
                'sd_minus_3' => 120.00,
                'sd_minus_2' => 125.00,
                'sd_minus_1_5' => 128.00,
                'median' => 138.00,
                'sd_plus_1_5' => 148.00,
                'sd_plus_2' => 151.00,
                'sd_plus_3' => 156.00,
            ],

            // ตัวอย่าง: เพศชาย ส่วนสูง 138 ซม.
            [
                'gender' => 'male',
                'age_month' => null,
                'height_cm' => 138.00,
                'standard_type' => 'weight_for_height',
                'sd_minus_3' => 24.00,
                'sd_minus_2' => 26.00,
                'sd_minus_1_5' => 28.00,
                'median' => 32.00,
                'sd_plus_1_5' => 39.00,
                'sd_plus_2' => 42.00,
                'sd_plus_3' => 48.00,
            ],
        ];

        foreach ($rows as $row) {
            NutritionGrowthStandard::create($row);
        }
    }
}