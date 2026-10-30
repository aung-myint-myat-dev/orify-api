<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'price_per_bottle',
                'value' => '1800',
            ],
            [
                'key' => 'price_per_empty_bottle',
                'value' => '2000',
            ],
            [
                'key' => 'order_cut_off_time',
                'value' => now(),
            ],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'],
                ]
            );
        }
    }
}
