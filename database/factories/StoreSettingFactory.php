<?php

namespace Database\Factories;

use App\Models\StoreSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StoreSetting>
 */
class StoreSettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'opening_time' => '08:00',
            'closing_time' => '23:00',
            'days' => [1, 2, 3, 4, 5, 6, 7],
        ];
    }
}
