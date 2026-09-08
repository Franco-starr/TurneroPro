<?php

it('lets a visitor see the lookup form to find their appointment', function () {
    $this->get(route('mi-turno'))
        ->assertOk()
        ->assertSee('¿Ya tenés un turno?')
        ->assertSee('Buscar');
});
