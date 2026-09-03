<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->words(2, true),
            'duration' => $this->faker->numberBetween(15, 180),
            'price' => $this->faker->randomFloat(2, 5, 200),
        ];
    }
}
