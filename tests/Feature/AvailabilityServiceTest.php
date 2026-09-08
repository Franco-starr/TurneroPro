<?php

use App\Models\StoreSetting;
use App\Services\AvailabilityService;
use Carbon\Carbon;

it('reports an opened day as open', function () {
    StoreSetting::factory()->create(['days' => [1, 2, 3, 4, 5]]);

    $monday = Carbon::parse('2026-09-07'); // Lunes

    expect(app(AvailabilityService::class)->isOpenOn($monday))->toBeTrue();
});

it('reports a closed day as closed', function () {
    StoreSetting::factory()->create(['days' => [1, 2, 3, 4, 5]]);

    $sunday = Carbon::parse('2026-09-13'); // Domingo

    expect(app(AvailabilityService::class)->isOpenOn($sunday))->toBeFalse();
});

it('uses the default days when no settings exist', function () {
    $monday = Carbon::parse('2026-09-07');

    expect(app(AvailabilityService::class)->isOpenOn($monday))->toBeTrue();
});
