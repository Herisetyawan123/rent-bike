<?php

namespace Database\Seeders;

use App\Models\BusinessSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BussinessSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            ['setting_key' => 'app_margin', 'setting_value' => '10'],
            ['setting_key' => 'app_margin_type', 'setting_value' => 'percentage'], // atau 'flat'
            ['setting_key' => 'app_tax', 'setting_value' => '11'],
            ['setting_key' => 'app_name', 'setting_value' => 'My Rental App'],
            ['setting_key' => 'app_email', 'setting_value' => 'admin@myapp.com'],
            ['setting_key' => 'app_contact', 'setting_value' => '081234567890'],
            ['setting_key' => 'app_address', 'setting_value' => 'Jl. Merdeka No.123, Jakarta'],
        ];

        foreach ($settings as $setting) {
            BusinessSetting::updateOrCreate(
                ['setting_key' => $setting['setting_key']],
                ['setting_value' => $setting['setting_value']]
            );
        }
    }
}
