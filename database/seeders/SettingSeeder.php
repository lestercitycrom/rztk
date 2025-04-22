<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::firstOrCreate([
            'id'=>1,
        ], [
            'request_delay'=>1000,
            'details_per_category'=>5,
        ]);
    }
}
