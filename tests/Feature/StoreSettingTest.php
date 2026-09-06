<?php

use App\Models\StoreSetting;

it('shows the form to edit the store hours with the current values', function () {
    StoreSetting::factory()->create(['opening_time' => '08:00', 'closing_time' => '23:00']);

    $this->get(route('store-settings.edit'))
        ->assertOk()
        ->assertSee('Configuración del local')
        ->assertSee('value="08:00"', false)
        ->assertSee('value="23:00"', false);
});

it('updates the store hours and redirects to the form', function () {
    StoreSetting::factory()->create();

    $this->put(route('store-settings.update'), [
        'opening_time' => '09:00',
        'closing_time' => '20:00',
    ])->assertRedirect(route('store-settings.edit'))->assertSessionHas('success');

    $this->assertDatabaseHas('store_settings', [
        'opening_time' => '09:00',
        'closing_time' => '20:00',
    ]);
});

it('creates the settings row when none exists yet', function () {
    $this->put(route('store-settings.update'), [
        'opening_time' => '09:00',
        'closing_time' => '20:00',
    ])->assertRedirect(route('store-settings.edit'));

    $this->assertDatabaseCount('store_settings', 1);
});

it('rejects a closing time not later than the opening time', function () {
    StoreSetting::factory()->create();

    $this->put(route('store-settings.update'), [
        'opening_time' => '10:00',
        'closing_time' => '10:00',
    ])->assertSessionHasErrors('closing_time');
});

it('rejects an invalid time format', function () {
    StoreSetting::factory()->create();

    $this->put(route('store-settings.update'), [
        'opening_time' => 'not-a-time',
        'closing_time' => '23:00',
    ])->assertSessionHasErrors('opening_time');
});
