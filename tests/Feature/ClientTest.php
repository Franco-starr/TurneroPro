<?php

use App\Models\Client;
use App\Models\User;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

it('lists the registered clients', function () {
    $client = Client::factory()->create(['nombre' => 'Juan', 'email' => 'juan@example.com']);

    $this->get(route('clients.index'))
        ->assertOk()
        ->assertSee('Juan')
        ->assertSee('juan@example.com')
        ->assertSee('Clientes');
});

it('shows the form to create a client', function () {
    $this->get(route('clients.create'))
        ->assertOk()
        ->assertSee('Nuevo Cliente');
});

it('creates a client and redirects to the list', function () {
    $this->post(route('clients.store'), [
        'nombre' => 'Juan',
        'apellido' => 'Pérez',
        'telefono' => '1122334455',
        'email' => 'juan@example.com',
    ])->assertRedirect(route('clients.index'))->assertSessionHas('success');

    $this->assertDatabaseHas('clients', [
        'nombre' => 'Juan',
        'apellido' => 'Pérez',
        'telefono' => '1122334455',
        'email' => 'juan@example.com',
    ]);
});

it('rejects a client with missing required fields', function () {
    $this->post(route('clients.store'), [
        'nombre' => '',
        'apellido' => '',
        'telefono' => '',
        'email' => 'no-es-un-email',
    ])->assertSessionHasErrors(['nombre', 'apellido', 'telefono', 'email']);

    $this->assertDatabaseCount('clients', 0);
});

it('rejects a client with an invalid email', function () {
    $this->post(route('clients.store'), [
        'nombre' => 'Juan',
        'apellido' => 'Pérez',
        'telefono' => '1122334455',
        'email' => 'correo-invalido',
    ])->assertSessionHasErrors('email');

    $this->assertDatabaseCount('clients', 0);
});
