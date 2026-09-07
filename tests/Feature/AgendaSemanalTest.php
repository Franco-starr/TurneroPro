<?php

use App\Models\Appointment;
use App\Models\Client;
use App\Models\StoreSetting;
use App\Models\User;
use Carbon\Carbon;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

it('shows the current week appointments by default', function () {
    $turno = Appointment::factory()->create([
        'fecha_hora' => Carbon::today()->setTime(10, 0),
    ]);

    $this->get(route('agenda.semanal'))
        ->assertOk()
        ->assertSee($turno->client->nombre.' '.$turno->client->apellido)
        ->assertSee($turno->service->name);
});

it('shows seven consecutive days starting on monday', function () {
    $lunes = Carbon::today()->startOfWeek(Carbon::MONDAY);

    $this->get(route('agenda.semanal'))->assertOk();

    foreach (range(0, 6) as $offset) {
        $this->get(route('agenda.semanal'))
            ->assertSee($lunes->copy()->addDays($offset)->locale('es')->translatedFormat('l'));
    }
});

it('navigates to another week', function () {
    $turnoDeHoy = Appointment::factory()->create([
        'fecha_hora' => Carbon::today()->setTime(10, 0),
    ]);

    $lunesSiguiente = Carbon::today()->startOfWeek(Carbon::MONDAY)->addWeek();

    $turnoDeLaSemana = Appointment::factory()->create([
        'fecha_hora' => $lunesSiguiente->copy()->setTime(10, 0),
    ]);

    $this->get(route('agenda.semanal', ['semana' => $lunesSiguiente->toDateString()]))
        ->assertOk()
        ->assertSee($turnoDeLaSemana->client->nombre.' '.$turnoDeLaSemana->client->apellido)
        ->assertDontSee($turnoDeHoy->client->nombre.' '.$turnoDeHoy->client->apellido);
});

it('shows each appointment under its own day', function () {
    $lunes = Carbon::today()->startOfWeek(Carbon::MONDAY);

    $turnoDelMartes = Appointment::factory()->create([
        'fecha_hora' => $lunes->copy()->addDays(1)->setTime(9, 0),
        'client_id' => Client::factory()->create(['nombre' => 'Ana', 'apellido' => 'Ruiz'])->id,
    ]);

    $turnoDelJueves = Appointment::factory()->create([
        'fecha_hora' => $lunes->copy()->addDays(3)->setTime(15, 0),
        'client_id' => Client::factory()->create(['nombre' => 'Bruno', 'apellido' => 'Sosa'])->id,
    ]);

    $this->get(route('agenda.semanal', ['semana' => $lunes->toDateString()]))
        ->assertOk()
        ->assertSeeInOrder([
            'martes',
            $turnoDelMartes->client->nombre.' '.$turnoDelMartes->client->apellido,
            'jueves',
            $turnoDelJueves->client->nombre.' '.$turnoDelJueves->client->apellido,
        ]);
});

it('shows an empty state message for days without appointments', function () {
    $this->get(route('agenda.semanal'))
        ->assertOk()
        ->assertSee('Sin turnos');
});

it('falls back to the current week when the semana filter is invalid', function () {
    $turnoDeHoy = Appointment::factory()->create([
        'fecha_hora' => Carbon::today()->setTime(10, 0),
    ]);

    $this->get(route('agenda.semanal', ['semana' => 'not-a-date']))
        ->assertOk()
        ->assertSee($turnoDeHoy->client->nombre.' '.$turnoDeHoy->client->apellido);

    $this->get(route('agenda.semanal', ['semana' => '2026-02-31']))
        ->assertOk()
        ->assertSee($turnoDeHoy->client->nombre.' '.$turnoDeHoy->client->apellido);
});

it('shows the opening hours configured in the store', function () {
    StoreSetting::factory()->create([
        'opening_time' => '09:00:00',
        'closing_time' => '20:00:00',
    ]);

    $this->get(route('agenda.semanal'))
        ->assertOk()
        ->assertSee('09:00–20:00');
});

it('links to the weekly agenda from the panel and the navigation', function () {
    $this->get(route('panel'))
        ->assertOk()
        ->assertSee('Agenda Semanal');

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Semana');
});

it('navigates to another month showing its week', function () {
    $turnoDeHoy = Appointment::factory()->create([
        'fecha_hora' => Carbon::today()->setTime(10, 0),
    ]);

    $mesAnterior = Carbon::today()->startOfMonth()->subMonthNoOverflow();

    $turnoDelMes = Appointment::factory()->create([
        'fecha_hora' => $mesAnterior->copy()->setTime(10, 0),
    ]);

    $this->get(route('agenda.semanal', ['mes' => $mesAnterior->format('Y-m')]))
        ->assertOk()
        ->assertSee($turnoDelMes->client->nombre.' '.$turnoDelMes->client->apellido)
        ->assertDontSee($turnoDeHoy->client->nombre.' '.$turnoDeHoy->client->apellido);
});

it('falls back to the current week when the mes filter is invalid', function () {
    $turnoDeHoy = Appointment::factory()->create([
        'fecha_hora' => Carbon::today()->setTime(10, 0),
    ]);

    $this->get(route('agenda.semanal', ['mes' => '2026-13']))
        ->assertOk()
        ->assertSee($turnoDeHoy->client->nombre.' '.$turnoDeHoy->client->apellido);

    $this->get(route('agenda.semanal', ['mes' => 'not-a-month']))
        ->assertOk()
        ->assertSee($turnoDeHoy->client->nombre.' '.$turnoDeHoy->client->apellido);
});

it('renders the month navigation controls', function () {
    $this->get(route('agenda.semanal'))
        ->assertOk()
        ->assertSee('Ir al mes')
        ->assertSee('Mes anterior')
        ->assertSee('Mes siguiente');
});
