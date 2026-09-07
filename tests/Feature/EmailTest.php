<?php

use App\Mail\ReservaConfirmada;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

it('sends a confirmation email to the client after booking', function () {
    Mail::fake();

    $service = Service::factory()->create(['name' => 'Corte', 'duration' => 30]);
    $fecha = Carbon::today()->addDays(5);

    $this->post(route('reserva.store'), [
        'nombre' => 'Franco',
        'apellido' => 'Diaz',
        'telefono' => '1160000000',
        'email' => 'franco@example.com',
        'service_id' => $service->id,
        'fecha' => $fecha->toDateString(),
        'hora' => '10:00',
    ]);

    Mail::assertSent(ReservaConfirmada::class, function (ReservaConfirmada $mail) use ($fecha) {
        return $mail->hasTo('franco@example.com')
            && $mail->appointment->fecha_hora->format('Y-m-d H:i') === $fecha->format('Y-m-d').' 10:00'
            && $mail->appointment->service->name === 'Corte';
    });
});

it('sends a confirmation email with the cancellation link', function () {
    Mail::fake();

    $service = Service::factory()->create(['name' => 'Corte', 'duration' => 30]);
    $fecha = Carbon::today()->addDays(5);

    $this->post(route('reserva.store'), [
        'nombre' => 'Franco',
        'apellido' => 'Diaz',
        'telefono' => '1160000000',
        'email' => 'franco@example.com',
        'service_id' => $service->id,
        'fecha' => $fecha->toDateString(),
        'hora' => '10:00',
    ]);

    Mail::assertSent(ReservaConfirmada::class, function (ReservaConfirmada $mail) {
        $rendered = $mail->render();

        return str_contains($rendered, route('reserva.cancelar', $mail->appointment->token))
            && str_contains($rendered, 'Cancelar turno');
    });
});

it('creates the appointment even when the email fails to send', function () {
    Mail::shouldReceive('to')->andThrow(new RuntimeException('Mail server down'));

    $service = Service::factory()->create(['duration' => 30]);
    $fecha = Carbon::today()->addDays(5);

    $this->post(route('reserva.store'), [
        'nombre' => 'Franco',
        'apellido' => 'Diaz',
        'telefono' => '1160000000',
        'email' => 'franco@example.com',
        'service_id' => $service->id,
        'fecha' => $fecha->toDateString(),
        'hora' => '10:00',
    ])->assertRedirect(route('reserva.confirmada'));

    $this->assertDatabaseHas('appointments', [
        'fecha_hora' => $fecha->copy()->setTime(10, 0)->format('Y-m-d H:i:s'),
    ]);
});
