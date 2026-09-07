<?php

use App\Models\StoreSetting;
use App\Models\User;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

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
        'days' => [1, 2, 3, 4, 5],
    ])->assertRedirect(route('store-settings.edit'))->assertSessionHas('success');

    $this->assertDatabaseHas('store_settings', [
        'opening_time' => '09:00',
        'closing_time' => '20:00',
        'days' => '[1,2,3,4,5]',
    ]);
});

it('creates the settings row when none exists yet', function () {
    $this->put(route('store-settings.update'), [
        'opening_time' => '09:00',
        'closing_time' => '20:00',
        'days' => [1, 2, 3, 4, 5],
    ])->assertRedirect(route('store-settings.edit'));

    $this->assertDatabaseCount('store_settings', 1);
});

it('rejects a closing time not later than the opening time', function () {
    StoreSetting::factory()->create();

    $this->put(route('store-settings.update'), [
        'opening_time' => '10:00',
        'closing_time' => '10:00',
        'days' => [1, 2, 3, 4, 5],
    ])->assertSessionHasErrors('closing_time');
});

it('rejects an invalid time format', function () {
    StoreSetting::factory()->create();

    $this->put(route('store-settings.update'), [
        'opening_time' => 'not-a-time',
        'closing_time' => '23:00',
        'days' => [1, 2, 3, 4, 5],
    ])->assertSessionHasErrors('opening_time');
});

it('rejects a schedule without any opening day', function () {
    StoreSetting::factory()->create();

    $this->put(route('store-settings.update'), [
        'opening_time' => '09:00',
        'closing_time' => '20:00',
        'days' => [],
    ])->assertSessionHasErrors('days');
});

it('rejects an invalid opening day', function () {
    StoreSetting::factory()->create();

    $this->put(route('store-settings.update'), [
        'opening_time' => '09:00',
        'closing_time' => '20:00',
        'days' => [1, 8],
    ])->assertSessionHasErrors('days.1');
});

it('shows the stored opening days checked on the form', function () {
    StoreSetting::factory()->create(['days' => [2, 4, 6]]);

    $this->get(route('store-settings.edit'))
        ->assertOk()
        ->assertSee('value="2" class="h-4 w-4" checked', false)
        ->assertSee('value="4" class="h-4 w-4" checked', false)
        ->assertSee('value="6" class="h-4 w-4" checked', false)
        ->assertDontSee('value="1" class="h-4 w-4" checked', false);
});
