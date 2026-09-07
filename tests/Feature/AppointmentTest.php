<?php

use App\Models\Appointment;
use App\Models\Client;
use App\Models\Service;
use App\Models\StoreSetting;
use App\Models\User;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

it('lists the registered appointments with their client and service', function () {
    $appointment = Appointment::factory()->create();

    $this->get(route('appointments.index'))
        ->assertOk()
        ->assertSee($appointment->client->nombre.' '.$appointment->client->apellido)
        ->assertSee($appointment->service->name)
        ->assertSee($appointment->fecha_hora->format('d/m/Y'))
        ->assertSee($appointment->fecha_hora->format('H:i'))
        ->assertSee('Turnos');
});

it('shows the form to create an appointment', function () {
    Client::factory()->create(['nombre' => 'Juan', 'apellido' => 'Pérez']);
    Service::factory()->create(['name' => 'Corte']);

    $this->get(route('appointments.create'))
        ->assertOk()
        ->assertSee('Nuevo Turno')
        ->assertSee('Juan Pérez')
        ->assertSee('Corte');
});

it('creates an appointment and redirects to the list', function () {
    $client = Client::factory()->create();
    $service = Service::factory()->create();

    $this->post(route('appointments.store'), [
        'client_id' => $client->id,
        'service_id' => $service->id,
        'fecha' => '2026-09-10',
        'hora' => '15:00',
    ])->assertRedirect(route('appointments.index'))->assertSessionHas('success');

    $this->assertDatabaseHas('appointments', [
        'client_id' => $client->id,
        'service_id' => $service->id,
        'fecha_hora' => '2026-09-10 15:00:00',
    ]);
});

it('rejects an appointment without a client', function () {
    $this->post(route('appointments.store'), [
        'client_id' => '',
        'service_id' => Service::factory()->create()->id,
        'fecha' => '2026-09-10',
        'hora' => '15:00',
    ])->assertSessionHasErrors('client_id');

    $this->assertDatabaseCount('appointments', 0);
});

it('rejects an appointment without a service', function () {
    $this->post(route('appointments.store'), [
        'client_id' => Client::factory()->create()->id,
        'service_id' => '',
        'fecha' => '2026-09-10',
        'hora' => '15:00',
    ])->assertSessionHasErrors('service_id');

    $this->assertDatabaseCount('appointments', 0);
});

it('rejects an appointment with an invalid date', function () {
    $this->post(route('appointments.store'), [
        'client_id' => Client::factory()->create()->id,
        'service_id' => Service::factory()->create()->id,
        'fecha' => 'not-a-date',
        'hora' => '15:00',
    ])->assertSessionHasErrors('fecha');

    $this->assertDatabaseCount('appointments', 0);
});

it('rejects an appointment with an invalid time', function () {
    $this->post(route('appointments.store'), [
        'client_id' => Client::factory()->create()->id,
        'service_id' => Service::factory()->create()->id,
        'fecha' => '2026-09-10',
        'hora' => '25:99',
    ])->assertSessionHasErrors('hora');

    $this->assertDatabaseCount('appointments', 0);
});

it('rejects an appointment that overlaps the start of an existing one', function () {
    Appointment::factory()->create([
        'fecha_hora' => '2026-09-10 10:00:00',
        'service_id' => Service::factory()->create(['duration' => 30])->id,
    ]);

    $this->post(route('appointments.store'), [
        'client_id' => Client::factory()->create()->id,
        'service_id' => Service::factory()->create(['duration' => 30])->id,
        'fecha' => '2026-09-10',
        'hora' => '10:15',
    ])->assertSessionHasErrors('fecha');

    $this->assertDatabaseCount('appointments', 1);
});

it('rejects an appointment that overlaps the end of an existing one', function () {
    Appointment::factory()->create([
        'fecha_hora' => '2026-09-10 10:00:00',
        'service_id' => Service::factory()->create(['duration' => 30])->id,
    ]);

    $this->post(route('appointments.store'), [
        'client_id' => Client::factory()->create()->id,
        'service_id' => Service::factory()->create(['duration' => 30])->id,
        'fecha' => '2026-09-10',
        'hora' => '09:45',
    ])->assertSessionHasErrors('fecha');

    $this->assertDatabaseCount('appointments', 1);
});

it('allows an appointment that starts exactly when another ends', function () {
    Appointment::factory()->create([
        'fecha_hora' => '2026-09-10 10:00:00',
        'service_id' => Service::factory()->create(['duration' => 30])->id,
    ]);

    $this->post(route('appointments.store'), [
        'client_id' => Client::factory()->create()->id,
        'service_id' => Service::factory()->create(['duration' => 30])->id,
        'fecha' => '2026-09-10',
        'hora' => '10:30',
    ])->assertRedirect(route('appointments.index'));

    $this->assertDatabaseCount('appointments', 2);
});

it('rejects an appointment that overlaps an existing one from another service', function () {
    Appointment::factory()->create([
        'fecha_hora' => '2026-09-10 10:00:00',
        'service_id' => Service::factory()->create(['duration' => 30])->id,
    ]);

    $this->post(route('appointments.store'), [
        'client_id' => Client::factory()->create()->id,
        'service_id' => Service::factory()->create(['name' => 'Otro servicio', 'duration' => 60])->id,
        'fecha' => '2026-09-10',
        'hora' => '10:15',
    ])->assertSessionHasErrors('fecha');

    $this->assertDatabaseCount('appointments', 1);
});

it('rejects an appointment before the opening time', function () {
    $this->post(route('appointments.store'), [
        'client_id' => Client::factory()->create()->id,
        'service_id' => Service::factory()->create(['duration' => 30])->id,
        'fecha' => '2026-09-10',
        'hora' => '07:45',
    ])->assertSessionHasErrors('hora');

    $this->assertDatabaseCount('appointments', 0);
});

it('rejects an appointment that ends after the closing time', function () {
    $this->post(route('appointments.store'), [
        'client_id' => Client::factory()->create()->id,
        'service_id' => Service::factory()->create(['duration' => 90])->id,
        'fecha' => '2026-09-10',
        'hora' => '22:15',
    ])->assertSessionHasErrors('hora');

    $this->assertDatabaseCount('appointments', 0);
});

it('allows an appointment that ends exactly at the closing time', function () {
    $this->post(route('appointments.store'), [
        'client_id' => Client::factory()->create()->id,
        'service_id' => Service::factory()->create(['duration' => 30])->id,
        'fecha' => '2026-09-10',
        'hora' => '22:30',
    ])->assertRedirect(route('appointments.index'));

    $this->assertDatabaseCount('appointments', 1);
});

it('enforces the opening time configured in the settings', function () {
    StoreSetting::factory()->create(['opening_time' => '10:00', 'closing_time' => '18:00']);

    $this->post(route('appointments.store'), [
        'client_id' => Client::factory()->create()->id,
        'service_id' => Service::factory()->create(['duration' => 30])->id,
        'fecha' => '2026-09-10',
        'hora' => '09:00',
    ])->assertSessionHasErrors('hora');

    $this->assertDatabaseCount('appointments', 0);
});

it('rejects an appointment on a closed day', function () {
    StoreSetting::factory()->create(['days' => [1, 2, 3]]);

    $this->post(route('appointments.store'), [
        'client_id' => Client::factory()->create()->id,
        'service_id' => Service::factory()->create(['duration' => 30])->id,
        'fecha' => '2026-09-10',
        'hora' => '15:00',
    ])->assertSessionHasErrors('fecha');

    $this->assertDatabaseCount('appointments', 0);
});

it('shows the configured opening hours in the creation form', function () {
    StoreSetting::factory()->create(['opening_time' => '10:00', 'closing_time' => '18:00']);

    $this->get(route('appointments.create'))
        ->assertOk()
        ->assertSee('Horario de atención: 10:00 a 18:00')
        ->assertSee('min="10:00"', false)
        ->assertSee('max="18:00"', false);
});

it('rejects an appointment that completely wraps an existing one', function () {
    Appointment::factory()->create([
        'fecha_hora' => '2026-09-10 10:00:00',
        'service_id' => Service::factory()->create(['duration' => 30])->id,
    ]);

    $this->post(route('appointments.store'), [
        'client_id' => Client::factory()->create()->id,
        'service_id' => Service::factory()->create(['duration' => 90])->id,
        'fecha' => '2026-09-10',
        'hora' => '09:30',
    ])->assertSessionHasErrors('fecha');

    $this->assertDatabaseCount('appointments', 1);
});

it('rejects an appointment that starts exactly when an existing one starts', function () {
    Appointment::factory()->create([
        'fecha_hora' => '2026-09-10 10:00:00',
        'service_id' => Service::factory()->create(['duration' => 30])->id,
    ]);

    $this->post(route('appointments.store'), [
        'client_id' => Client::factory()->create()->id,
        'service_id' => Service::factory()->create(['duration' => 30])->id,
        'fecha' => '2026-09-10',
        'hora' => '10:00',
    ])->assertSessionHasErrors('fecha');

    $this->assertDatabaseCount('appointments', 1);
});

it('allows an appointment that ends exactly when an existing one starts', function () {
    Appointment::factory()->create([
        'fecha_hora' => '2026-09-10 10:00:00',
        'service_id' => Service::factory()->create(['duration' => 30])->id,
    ]);

    $this->post(route('appointments.store'), [
        'client_id' => Client::factory()->create()->id,
        'service_id' => Service::factory()->create(['duration' => 90])->id,
        'fecha' => '2026-09-10',
        'hora' => '08:30',
    ])->assertRedirect(route('appointments.index'));

    $this->assertDatabaseCount('appointments', 2);
});
