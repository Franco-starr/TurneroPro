<?php

use App\Models\Service;
use App\Models\StoreSetting;
use App\Models\User;

it('shows the services from the database with their details', function () {
    Service::factory()->create(['name' => 'Corte de pelo', 'duration' => 30, 'price' => 10000]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Nuestros servicios')
        ->assertSee('Corte de pelo')
        ->assertSee('30 min')
        ->assertSee('$10.000')
        ->assertSee(route('reservar', ['service_id' => Service::first()->id]));
});

it('shows the business hours stored in the database', function () {
    StoreSetting::factory()->create(['opening_time' => '09:30', 'closing_time' => '19:00']);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Horario de atención')
        ->assertSee('Lunes a Domingo')
        ->assertSee('09:30')
        ->assertSee('19:00');
});

it('falls back to the configured hours when there is no store setting', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee(config('store.opening_time'))
        ->assertSee(config('store.closing_time'));
});

it('lets a guest see the public sections without the admin call to action', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Cómo funciona')
        ->assertSee('Beneficios')
        ->assertSee('Preguntas frecuentes')
        ->assertSee('Reservar un turno')
        ->assertDontSee('Ir al Panel');
});

it('shows the admin call to action for an authenticated user', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Ir al Panel')
        ->assertDontSee('Reservar un turno');
});
