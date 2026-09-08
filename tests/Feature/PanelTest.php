<?php

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Client;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

it('shows aggregate counters in the panel', function () {
    $servicioUno = Service::factory()->create();
    $servicioDos = Service::factory()->create();
    $cliente = Client::factory()->create();

    Appointment::factory()->create([
        'client_id' => $cliente->id,
        'service_id' => $servicioUno->id,
        'fecha_hora' => Carbon::today()->setTime(9, 0),
        'status' => AppointmentStatus::Pending,
    ]);
    Appointment::factory()->create([
        'client_id' => $cliente->id,
        'service_id' => $servicioDos->id,
        'fecha_hora' => Carbon::today()->setTime(10, 0),
        'status' => AppointmentStatus::Completed,
    ]);

    $content = $this->get(route('panel'))->assertOk()->getContent();

    expect($content)
        ->toContain('Turnos de hoy')
        ->toContain('Turnos pendientes')
        ->toContain('Clientes')
        ->toContain('Servicios')
        ->toContain('>2<')   // turnos de hoy y servicios
        ->toContain('>1<');  // turnos pendientes y clientes
});
