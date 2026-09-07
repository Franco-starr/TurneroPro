<?php

use App\Models\Service;
use App\Models\User;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

it('lists the registered services', function () {
    $service = Service::factory()->create(['name' => 'Corte de cabello']);

    $this->get(route('services.index'))
        ->assertOk()
        ->assertSee('Corte de cabello')
        ->assertSee($service->duration)
        ->assertSee('Servicios');
});

it('shows the form to create a service', function () {
    $this->get(route('services.create'))
        ->assertOk()
        ->assertSee('Nuevo Servicio');
});

it('creates a service and redirects to the list', function () {
    $this->post(route('services.store'), [
        'name' => 'Corte de cabello',
        'duration' => 30,
        'price' => 15.00,
    ])->assertRedirect(route('services.index'))->assertSessionHas('success');

    $this->assertDatabaseHas('services', [
        'name' => 'Corte de cabello',
        'duration' => 30,
        'price' => 15.00,
    ]);
});

it('rejects a service with an empty name', function () {
    $this->post(route('services.store'), [
        'name' => '',
        'duration' => 30,
        'price' => 15.00,
    ])->assertSessionHasErrors('name');

    $this->assertDatabaseCount('services', 0);
});

it('rejects a service with a duration lower than one', function () {
    $this->post(route('services.store'), [
        'name' => 'Corte',
        'duration' => 0,
        'price' => 15.00,
    ])->assertSessionHasErrors('duration');

    $this->assertDatabaseCount('services', 0);
});

it('rejects a service with a negative price', function () {
    $this->post(route('services.store'), [
        'name' => 'Corte',
        'duration' => 30,
        'price' => -1,
    ])->assertSessionHasErrors('price');

    $this->assertDatabaseCount('services', 0);
});

it('shows the form to edit a service', function () {
    $service = Service::factory()->create();

    $this->get(route('services.edit', $service))
        ->assertOk()
        ->assertSee('Editar Servicio');
});

it('updates a service and redirects to the list', function () {
    $service = Service::factory()->create(['name' => 'Original']);

    $this->put(route('services.update', $service), [
        'name' => 'Actualizado',
        'duration' => 45,
        'price' => 20.50,
    ])->assertRedirect(route('services.index'))->assertSessionHas('success');

    $this->assertDatabaseHas('services', [
        'id' => $service->id,
        'name' => 'Actualizado',
        'duration' => 45,
        'price' => 20.50,
    ]);
});

it('deletes a service and redirects to the list', function () {
    $service = Service::factory()->create();

    $this->delete(route('services.destroy', $service))
        ->assertRedirect(route('services.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseMissing('services', ['id' => $service->id]);
});
