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

it('creates a new appointment as pending', function () {
    $this->post(route('appointments.store'), [
        'client_id' => Client::factory()->create()->id,
        'service_id' => Service::factory()->create()->id,
        'fecha' => '2026-09-10',
        'hora' => '15:00',
    ])->assertRedirect(route('appointments.index'));

    $this->assertDatabaseHas('appointments', [
        'fecha_hora' => '2026-09-10 15:00:00',
        'status' => AppointmentStatus::Pending->value,
    ]);
});

it('marks an appointment as completed from the agenda', function () {
    $appointment = Appointment::factory()->create([
        'fecha_hora' => Carbon::parse('2026-09-10 09:00:00'),
    ]);

    $this->patch(route('appointments.complete', $appointment))
        ->assertRedirect(route('agenda', ['fecha' => '2026-09-10']))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('appointments', [
        'id' => $appointment->id,
        'status' => AppointmentStatus::Completed->value,
    ]);
});

it('marks an appointment as cancelled from the agenda', function () {
    $appointment = Appointment::factory()->create([
        'fecha_hora' => Carbon::parse('2026-09-10 09:00:00'),
    ]);

    $this->patch(route('appointments.cancel', $appointment))
        ->assertRedirect(route('agenda', ['fecha' => '2026-09-10']))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('appointments', [
        'id' => $appointment->id,
        'status' => AppointmentStatus::Cancelled->value,
    ]);
});

it('shows the updated status in the agenda keeping the appointment visible', function () {
    $appointment = Appointment::factory()->create([
        'fecha_hora' => Carbon::parse('2026-09-10 09:00:00'),
    ]);

    $this->patch(route('appointments.complete', $appointment));

    $this->get(route('agenda', ['fecha' => '2026-09-10']))
        ->assertOk()
        ->assertSee($appointment->client->nombre.' '.$appointment->client->apellido)
        ->assertSee('Completado');
});

it('allows marking an appointment as cancelled after being completed', function () {
    $appointment = Appointment::factory()->create([
        'fecha_hora' => Carbon::parse('2026-09-10 09:00:00'),
        'status' => AppointmentStatus::Completed,
    ]);

    $this->patch(route('appointments.cancel', $appointment))
        ->assertRedirect(route('agenda', ['fecha' => '2026-09-10']));

    $this->assertDatabaseHas('appointments', [
        'id' => $appointment->id,
        'status' => AppointmentStatus::Cancelled->value,
    ]);
});
