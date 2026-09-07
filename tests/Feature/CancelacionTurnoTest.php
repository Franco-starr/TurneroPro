<?php

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Client;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Str;

it('generates a unique token for each booking', function () {
    $service = Service::factory()->create(['duration' => 30]);

    $this->post(route('reserva.store'), [
        'nombre' => 'Franco',
        'apellido' => 'Diaz',
        'telefono' => '1160000000',
        'email' => 'franco@example.com',
        'service_id' => $service->id,
        'fecha' => Carbon::today()->addDays(3)->toDateString(),
        'hora' => '10:00',
    ]);

    $appointments = Appointment::all();

    $this->assertCount(1, $appointments);
    $this->assertNotNull($appointments->first()->token);
    $this->assertTrue(Str::isUuid($appointments->first()->token));
});

it('shows the confirmation page with a cancel link', function () {
    $service = Service::factory()->create(['name' => 'Corte', 'duration' => 30]);

    $this->post(route('reserva.store'), [
        'nombre' => 'Franco',
        'apellido' => 'Diaz',
        'telefono' => '1160000000',
        'email' => 'franco@example.com',
        'service_id' => $service->id,
        'fecha' => Carbon::today()->addDays(3)->toDateString(),
        'hora' => '10:00',
    ]);

    $this->get(route('reserva.confirmada'))
        ->assertOk()
        ->assertSee('Cancelar mi turno');
});

it('finds the future appointments of a client by email', function () {
    $client = Client::factory()->create(['email' => 'franco@example.com']);

    $corte = Service::factory()->create(['name' => 'Corte', 'duration' => 30]);
    $diseño = Service::factory()->create(['name' => 'Diseño', 'duration' => 30]);

    Appointment::factory()->create([
        'client_id' => $client->id,
        'service_id' => $corte->id,
        'fecha_hora' => Carbon::today()->addDays(2)->setTime(10, 0),
        'status' => AppointmentStatus::Pending,
    ]);

    Appointment::factory()->create([
        'client_id' => $client->id,
        'service_id' => $diseño->id,
        'fecha_hora' => Carbon::yesterday()->setTime(10, 0),
        'status' => AppointmentStatus::Pending,
    ]);

    $otroCliente = Client::factory()->create(['email' => 'laura@example.com']);
    Appointment::factory()->create([
        'client_id' => $otroCliente->id,
        'service_id' => $diseño->id,
        'fecha_hora' => Carbon::today()->addDays(2)->setTime(11, 0),
        'status' => AppointmentStatus::Pending,
    ]);

    $this->post(route('mi-turno.buscar'), ['email' => 'franco@example.com'])
        ->assertOk()
        ->assertSee('Tus turnos')
        ->assertSee('Corte')
        ->assertDontSee('Diseño')
        ->assertDontSee('laura@example.com');
});

it('shows a friendly message when there are no future appointments', function () {
    Client::factory()->create(['email' => 'franco@example.com']);

    $this->post(route('mi-turno.buscar'), ['email' => 'franco@example.com'])
        ->assertOk()
        ->assertSee('No encontramos turnos futuros');
});

it('rejects searching with an invalid email', function () {
    $this->post(route('mi-turno.buscar'), ['email' => 'no-es-un-email'])
        ->assertSessionHasErrors('email');
});

it('shows the cancellation summary for a cancellable appointment', function () {
    $service = Service::factory()->create(['name' => 'Corte', 'duration' => 30]);
    $appointment = Appointment::factory()->create([
        'service_id' => $service->id,
        'fecha_hora' => Carbon::today()->addDays(2)->setTime(10, 0),
        'status' => AppointmentStatus::Pending,
    ]);

    $this->get(route('reserva.cancelar', $appointment->token))
        ->assertOk()
        ->assertSee('Cancelar turno')
        ->assertSee('Corte')
        ->assertSee('Sí, cancelar mi turno');
});

it('cancels a pending future appointment and frees the slot', function () {
    $service = Service::factory()->create(['duration' => 30]);
    $appointment = Appointment::factory()->create([
        'service_id' => $service->id,
        'fecha_hora' => Carbon::today()->addDays(2)->setTime(10, 0),
        'status' => AppointmentStatus::Pending,
    ]);

    $this->delete(route('reserva.cancel', $appointment->token))
        ->assertRedirect(route('home'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('appointments', [
        'id' => $appointment->id,
        'status' => AppointmentStatus::Cancelled->value,
    ]);

    $this->get(route('reservar', [
        'service_id' => $service->id,
        'fecha' => Carbon::today()->addDays(2)->toDateString(),
    ]))->assertSee('value="10:00"', false);
});

it('does not cancel a completed appointment', function () {
    $appointment = Appointment::factory()->create([
        'fecha_hora' => Carbon::today()->addDays(2)->setTime(10, 0),
        'status' => AppointmentStatus::Completed,
    ]);

    $this->delete(route('reserva.cancel', $appointment->token))
        ->assertRedirect(route('home'));

    $this->assertDatabaseHas('appointments', [
        'id' => $appointment->id,
        'status' => AppointmentStatus::Completed->value,
    ]);
});

it('does not cancel an appointment that is already in the past', function () {
    $appointment = Appointment::factory()->create([
        'fecha_hora' => Carbon::yesterday()->setTime(10, 0),
        'status' => AppointmentStatus::Pending,
    ]);

    $this->delete(route('reserva.cancel', $appointment->token))
        ->assertRedirect(route('home'));

    $this->assertDatabaseHas('appointments', [
        'id' => $appointment->id,
        'status' => AppointmentStatus::Pending->value,
    ]);
});

it('returns a 404 for an unknown token', function () {
    $this->get(route('reserva.cancelar', Str::uuid()->toString()))
        ->assertNotFound();
});
