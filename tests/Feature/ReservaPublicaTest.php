<?php

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Client;
use App\Models\Service;
use App\Models\StoreSetting;
use App\Models\User;
use Carbon\Carbon;

beforeEach(function () {
    $this->fechaFutura = Carbon::today()->addDays(5);
});

it('lets a visitor access the public booking page', function () {
    $service = Service::factory()->create(['name' => 'Corte de pelo', 'duration' => 30, 'price' => 10000]);

    $this->get(route('reservar'))
        ->assertOk()
        ->assertSee('Reservar un turno')
        ->assertSee('Corte de pelo')
        ->assertSee('30 min')
        ->assertSee('$10.000');
});

it('shows the available slots for a service on a chosen date', function () {
    $service = Service::factory()->create(['duration' => 30]);

    $this->get(route('reservar', [
        'service_id' => $service->id,
        'fecha' => $this->fechaFutura->toDateString(),
    ]))
        ->assertOk()
        ->assertSee('value="10:00"', false)
        ->assertSee('value="10:30"', false);
});

it('does not show an occupied slot as available', function () {
    $service = Service::factory()->create(['duration' => 30]);

    Appointment::factory()->create([
        'fecha_hora' => $this->fechaFutura->copy()->setTime(10, 0),
        'service_id' => $service->id,
    ]);

    $this->get(route('reservar', [
        'service_id' => $service->id,
        'fecha' => $this->fechaFutura->toDateString(),
    ]))
        ->assertOk()
        ->assertDontSee('value="10:00"', false)
        ->assertSee('value="10:30"', false);
});

it('does not show slots outside the business hours', function () {
    StoreSetting::factory()->create(['opening_time' => '09:00', 'closing_time' => '17:00']);

    $service = Service::factory()->create(['duration' => 60]);

    $this->get(route('reservar', [
        'service_id' => $service->id,
        'fecha' => $this->fechaFutura->toDateString(),
    ]))
        ->assertOk()
        ->assertDontSee('value="08:30"', false)
        ->assertSee('value="09:00"', false)
        ->assertDontSee('value="16:30"', false);
});

it('shows a message when there are no slots for the chosen date', function () {
    $service = Service::factory()->create(['duration' => 1800]);

    $this->get(route('reservar', [
        'service_id' => $service->id,
        'fecha' => $this->fechaFutura->toDateString(),
    ]))
        ->assertOk()
        ->assertSee('No hay horarios disponibles para la fecha seleccionada.');
});

it('rejects booking a past date', function () {
    $service = Service::factory()->create(['duration' => 30]);

    $this->post(route('reserva.store'), [
        'nombre' => 'Franco',
        'apellido' => 'Diaz',
        'telefono' => '1160000000',
        'email' => 'franco@example.com',
        'service_id' => $service->id,
        'fecha' => Carbon::yesterday()->toDateString(),
        'hora' => '10:00',
    ])->assertSessionHasErrors('fecha');

    $this->assertDatabaseCount('appointments', 0);
});

it('rejects booking a time that is already in the past today', function () {
    StoreSetting::factory()->create(['opening_time' => '00:00', 'closing_time' => '23:59']);

    $service = Service::factory()->create(['duration' => 30]);

    $this->post(route('reserva.store'), [
        'nombre' => 'Franco',
        'apellido' => 'Diaz',
        'telefono' => '1160000000',
        'email' => 'franco@example.com',
        'service_id' => $service->id,
        'fecha' => Carbon::today()->toDateString(),
        'hora' => '00:00',
    ])->assertSessionHasErrors('fecha');

    $this->assertDatabaseCount('appointments', 0);
});

it('rejects booking an occupied slot', function () {
    $service = Service::factory()->create(['duration' => 30]);

    Appointment::factory()->create([
        'fecha_hora' => $this->fechaFutura->copy()->setTime(10, 0),
        'service_id' => $service->id,
    ]);

    $this->post(route('reserva.store'), [
        'nombre' => 'Franco',
        'apellido' => 'Diaz',
        'telefono' => '1160000000',
        'email' => 'franco@example.com',
        'service_id' => $service->id,
        'fecha' => $this->fechaFutura->toDateString(),
        'hora' => '10:00',
    ])->assertSessionHasErrors('hora');

    $this->assertDatabaseCount('appointments', 1);
});

it('rejects booking a slot that is not offered for that service and date', function () {
    StoreSetting::factory()->create(['opening_time' => '09:00', 'closing_time' => '17:00']);

    $service = Service::factory()->create(['duration' => 60]);

    $this->post(route('reserva.store'), [
        'nombre' => 'Franco',
        'apellido' => 'Diaz',
        'telefono' => '1160000000',
        'email' => 'franco@example.com',
        'service_id' => $service->id,
        'fecha' => $this->fechaFutura->toDateString(),
        'hora' => '17:30',
    ])->assertSessionHasErrors('hora');

    $this->assertDatabaseCount('appointments', 0);
});

it('creates the appointment as pending and associates the client', function () {
    $service = Service::factory()->create(['duration' => 30]);

    $this->post(route('reserva.store'), [
        'nombre' => 'Franco',
        'apellido' => 'Diaz',
        'telefono' => '1160000000',
        'email' => 'franco@example.com',
        'service_id' => $service->id,
        'fecha' => $this->fechaFutura->toDateString(),
        'hora' => '10:00',
    ])
        ->assertRedirect(route('reserva.confirmada'))
        ->assertSessionHas('reserva');

    $this->assertDatabaseHas('clients', [
        'nombre' => 'Franco',
        'apellido' => 'Diaz',
        'telefono' => '1160000000',
        'email' => 'franco@example.com',
    ]);

    $client = Client::where('email', 'franco@example.com')->firstOrFail();

    $this->assertDatabaseHas('appointments', [
        'client_id' => $client->id,
        'service_id' => $service->id,
        'fecha_hora' => $this->fechaFutura->copy()->setTime(10, 0)->format('Y-m-d H:i:s'),
        'status' => AppointmentStatus::Pending->value,
    ]);
});

it('shows the booking confirmation', function () {
    $service = Service::factory()->create(['name' => 'Corte', 'duration' => 30]);

    $this->post(route('reserva.store'), [
        'nombre' => 'Franco',
        'apellido' => 'Diaz',
        'telefono' => '1160000000',
        'email' => 'franco@example.com',
        'service_id' => $service->id,
        'fecha' => $this->fechaFutura->toDateString(),
        'hora' => '10:00',
    ]);

    $this->get(route('reserva.confirmada'))
        ->assertOk()
        ->assertSee('¡Turno reservado!')
        ->assertSee('Corte')
        ->assertSee('Franco Diaz');
});

it('shows the publicly booked appointment in the admin agenda', function () {
    $service = Service::factory()->create(['name' => 'Corte', 'duration' => 30]);

    $this->post(route('reserva.store'), [
        'nombre' => 'Franco',
        'apellido' => 'Diaz',
        'telefono' => '1160000000',
        'email' => 'franco@example.com',
        'service_id' => $service->id,
        'fecha' => $this->fechaFutura->toDateString(),
        'hora' => '10:00',
    ]);

    $this->actingAs(User::factory()->create());

    $this->get(route('agenda', ['fecha' => $this->fechaFutura->toDateString()]))
        ->assertOk()
        ->assertSee('Franco Diaz')
        ->assertSee('Corte')
        ->assertSee('Pendiente');
});

it('does not let a guest create appointment in the admin panel', function () {
    $this->post(route('appointments.store'), [
        'client_id' => Client::factory()->create()->id,
        'service_id' => Service::factory()->create()->id,
        'fecha' => $this->fechaFutura->toDateString(),
        'hora' => '10:00',
    ])->assertRedirect(route('login'));

    $this->assertDatabaseCount('appointments', 0);
});
