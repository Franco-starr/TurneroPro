<?php

use App\Models\Appointment;
use App\Models\Client;
use App\Models\Service;
use Carbon\Carbon;

it('shows today appointments by default', function () {
    $appointment = Appointment::factory()->create([
        'fecha_hora' => Carbon::today()->setTime(9, 0),
    ]);

    $this->get(route('agenda'))
        ->assertOk()
        ->assertSee($appointment->client->nombre.' '.$appointment->client->apellido)
        ->assertSee($appointment->service->name);
});

it('filters the appointments by the selected date', function () {
    $turnoDelDia = Appointment::factory()->create([
        'fecha_hora' => Carbon::parse('2026-09-10 09:00:00'),
        'service_id' => Service::factory()->create(['name' => 'Corte'])->id,
    ]);

    Appointment::factory()->create([
        'fecha_hora' => Carbon::parse('2026-09-11 10:00:00'),
        'service_id' => Service::factory()->create(['name' => 'Barba'])->id,
    ]);

    $this->get(route('agenda', ['fecha' => '2026-09-10']))
        ->assertOk()
        ->assertSee($turnoDelDia->client->nombre.' '.$turnoDelDia->client->apellido)
        ->assertSee('Corte')
        ->assertDontSee('Barba');
});

it('shows the start and end time calculated with the service duration', function () {
    Appointment::factory()->create([
        'fecha_hora' => Carbon::parse('2026-09-10 09:00:00'),
        'service_id' => Service::factory()->create(['duration' => 30])->id,
    ]);

    $this->get(route('agenda', ['fecha' => '2026-09-10']))
        ->assertOk()
        ->assertSee('09:00–09:30')
        ->assertSee('30 min');
});

it('sorts the appointments from earliest to latest', function () {
    Appointment::factory()->create([
        'fecha_hora' => Carbon::parse('2026-09-10 11:00:00'),
        'client_id' => Client::factory()->create(['nombre' => 'Ana', 'apellido' => 'Gomez'])->id,
    ]);

    Appointment::factory()->create([
        'fecha_hora' => Carbon::parse('2026-09-10 09:30:00'),
        'client_id' => Client::factory()->create(['nombre' => 'Bruno', 'apellido' => 'Lopez'])->id,
    ]);

    $this->get(route('agenda', ['fecha' => '2026-09-10']))
        ->assertOk()
        ->assertSeeInOrder(['Bruno Lopez', 'Ana Gomez']);
});

it('shows an empty state message when there are no appointments that day', function () {
    $this->get(route('agenda', ['fecha' => '2026-09-10']))
        ->assertOk()
        ->assertSee('No hay turnos programados para este día.');
});

it('falls back to today when the date filter is invalid', function () {
    $turnoDeHoy = Appointment::factory()->create([
        'fecha_hora' => Carbon::today()->setTime(9, 0),
    ]);

    $this->get(route('agenda', ['fecha' => 'not-a-date']))
        ->assertOk()
        ->assertSee($turnoDeHoy->client->nombre.' '.$turnoDeHoy->client->apellido);
});

it('falls back to today when the selected date does not exist in the calendar', function () {
    $turnoDeHoy = Appointment::factory()->create([
        'fecha_hora' => Carbon::today()->setTime(9, 0),
    ]);

    $this->get(route('agenda', ['fecha' => '2026-02-31']))
        ->assertOk()
        ->assertSee($turnoDeHoy->client->nombre.' '.$turnoDeHoy->client->apellido);
});

it('links to the agenda from the panel', function () {
    $this->get(route('panel'))
        ->assertOk()
        ->assertSee('Agenda');
});
