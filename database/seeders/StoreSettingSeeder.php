<?php

namespace Database\Seeders;

use App\Models\StoreSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StoreSettingSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        StoreSetting::firstOrCreate(
            ['id' => 1],
            [
                'opening_time' => config('store.opening_time'),
                'closing_time' => config('store.closing_time'),
            ]
        );
    }
}
