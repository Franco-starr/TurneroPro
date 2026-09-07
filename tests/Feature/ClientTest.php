<?php

use App\Models\Appointment;
use App\Models\Client;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;

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

it('shows the client details with its appointment history', function () {
    $client = Client::factory()->create(['nombre' => 'Juan', 'apellido' => 'Pérez']);

    $corte = Service::factory()->create(['name' => 'Corte', 'duration' => 30, 'price' => 10000]);
    $barba = Service::factory()->create(['name' => 'Barba', 'duration' => 20, 'price' => 5000]);

    Appointment::factory()->create([
        'client_id' => $client->id,
        'service_id' => $corte->id,
        'fecha_hora' => Carbon::today()->addDays(2)->setTime(10, 0),
    ]);

    $otroCliente = Client::factory()->create(['nombre' => 'Laura']);
    Appointment::factory()->create([
        'client_id' => $otroCliente->id,
        'service_id' => $barba->id,
        'fecha_hora' => Carbon::today()->addDays(3)->setTime(11, 0),
    ]);

    $this->get(route('clients.show', $client))
        ->assertOk()
        ->assertSee('Juan')
        ->assertSee('Pérez')
        ->assertSee('Historial de turnos')
        ->assertSee('Corte')
        ->assertSee('Pendiente')
        ->assertDontSee('Barba')
        ->assertDontSee('Laura');
});

it('shows the form to edit a client preloaded with its data', function () {
    $client = Client::factory()->create(['nombre' => 'Juan', 'apellido' => 'Pérez']);

    $this->get(route('clients.edit', $client))
        ->assertOk()
        ->assertSee('Editar Cliente')
        ->assertSee('Juan');
});

it('updates a client and redirects to the list', function () {
    $client = Client::factory()->create(['nombre' => 'Juan', 'email' => 'juan@example.com']);

    $this->put(route('clients.update', $client), [
        'nombre' => 'Juan Pablo',
        'apellido' => 'Gómez',
        'telefono' => '1199999999',
        'email' => 'juanpablo@example.com',
    ])->assertRedirect(route('clients.index'))->assertSessionHas('success');

    $this->assertDatabaseHas('clients', [
        'id' => $client->id,
        'nombre' => 'Juan Pablo',
        'apellido' => 'Gómez',
        'telefono' => '1199999999',
        'email' => 'juanpablo@example.com',
    ]);
});

it('rejects updating a client with invalid data', function () {
    $client = Client::factory()->create(['nombre' => 'Juan']);

    $this->put(route('clients.update', $client), [
        'nombre' => '',
        'apellido' => '',
        'telefono' => '',
        'email' => 'no-es-un-email',
    ])->assertSessionHasErrors(['nombre', 'apellido', 'telefono', 'email']);

    $this->assertDatabaseHas('clients', ['id' => $client->id, 'nombre' => 'Juan']);
});
