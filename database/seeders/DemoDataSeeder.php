<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Client;
use App\Models\Service;
use App\Services\AvailabilityService;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed sample services, clients and appointments for demonstration.
     */
    public function run(): void
    {
        if (Service::query()->exists()) {
            return;
        }

        $services = collect([
            ['name' => 'Corte de cabello', 'duration' => 30, 'price' => 2500],
            ['name' => 'Corte + barba', 'duration' => 45, 'price' => 3500],
            ['name' => 'Barba', 'duration' => 20, 'price' => 1500],
            ['name' => 'Color', 'duration' => 90, 'price' => 8000],
        ])->map(fn (array $datos) => Service::create($datos));

        $clientes = collect([
            ['nombre' => 'Martín', 'apellido' => 'Gómez', 'telefono' => '11 5555 0101', 'email' => 'martin.gomez@example.com'],
            ['nombre' => 'Lucía', 'apellido' => 'Fernández', 'telefono' => '11 5555 0102', 'email' => 'lucia.fernandez@example.com'],
            ['nombre' => 'Julián', 'apellido' => 'Pérez', 'telefono' => '11 5555 0103', 'email' => 'julian.perez@example.com'],
        ])->map(fn (array $datos) => Client::create($datos));

        $availability = app(AvailabilityService::class);

        $horarios = [9 => 0, 10 => 30, 11 => 30, 14 => 0, 15 => 0, 16 => 30, 17 => 30];
        $turnosCreados = 0;

        foreach (range(0, 13) as $offset) {
            $fecha = Carbon::today()->addDays($offset);

            if (! $availability->isOpenOn($fecha)) {
                continue;
            }

            foreach ($horarios as $hora => $minutos) {
                if ($turnosCreados >= 18) {
                    break 2;
                }

                $inicio = $fecha->copy()->setTime($hora, $minutos);
                $servicio = $services->random();

                if (! $availability->isWithinBusinessHours($inicio, $servicio)) {
                    continue;
                }

                Appointment::create([
                    'client_id' => $clientes->random()->id,
                    'service_id' => $servicio->id,
                    'fecha_hora' => $inicio,
                    'status' => $inicio->lt(Carbon::now()->addHours(2)) ? 'completed' : 'pending',
                ]);

                $turnosCreados++;
            }
        }

        $this->command?->info(sprintf('Demo: %d servicios, %d clientes y %d turnos creados.', $services->count(), $clientes->count(), $turnosCreados));
    }
}
