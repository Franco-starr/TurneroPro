<?php

use App\Models\User;

it('shows the login page', function () {
    $this->get(route('login'))
        ->assertOk()
        ->assertSee('Ingresar');
});

it('allows a registered user to log in', function () {
    $user = User::factory()->create(['password' => 'secret-password']);

    $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'secret-password',
    ])->assertRedirect(route('panel'));

    $this->assertAuthenticatedAs($user);
});

it('rejects invalid credentials', function () {
    $user = User::factory()->create(['password' => 'secret-password']);

    $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ])->assertSessionHasErrors('email');

    $this->assertGuest();
});

it('lets an authenticated user access the panel', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('panel'))
        ->assertOk()
        ->assertSee('Configuración del local');
});

it('redirects an unauthenticated user to the login page', function () {
    $this->get(route('panel'))
        ->assertRedirect(route('login'));

    $this->get(route('agenda'))
        ->assertRedirect(route('login'));

    $this->assertGuest();
});

it('redirects back to the originally requested page after logging in', function () {
    $user = User::factory()->create(['password' => 'secret-password']);

    $this->get(route('agenda'))
        ->assertRedirect(route('login'));

    $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'secret-password',
    ])->assertRedirect(route('agenda'));

    $this->assertAuthenticatedAs($user);
});

it('lets an authenticated user log out', function () {
    $this->actingAs(User::factory()->create());

    $this->post(route('logout'))
        ->assertRedirect(route('home'))
        ->assertSessionHasNoErrors();

    $this->assertGuest();
});

it('hides the admin navigation for guests and shows a login link', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Ingresar')
        ->assertDontSee('Agenda')
        ->assertDontSee('Cerrar sesión');
});

it('shows the authenticated user name and the logout action in the navigation', function () {
    $user = User::factory()->create(['name' => 'Administrador']);

    $this->actingAs($user);

    $this->get(route('panel'))
        ->assertOk()
        ->assertSee('Administrador')
        ->assertSee('Cerrar sesión');
});
