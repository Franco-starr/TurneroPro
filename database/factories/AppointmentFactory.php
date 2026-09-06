<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Client;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'service_id' => Service::factory(),
            'fecha_hora' => $this->faker->dateTimeBetween('now', '+1 month'),
        ];
    }
}
