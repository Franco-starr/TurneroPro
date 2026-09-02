<?php

it('shows the home page with the project name', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('TurneroPro')
        ->assertSee('Sistema de gestión de turnos');
});

it('shows the panel page', function () {
    $this->get(route('panel'))
        ->assertOk()
        ->assertSee('Panel');
});
