<?php

use App\Models\Appointment;
use App\Models\Client;
use App\Models\Service;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\DemoDataSeeder;

it('seeds demo services, clients and appointments for the portfolio', function () {
    (new DatabaseSeeder)->call(DemoDataSeeder::class);

    expect(Service::query()->count())->toBe(4)
        ->and(Client::query()->count())->toBe(3)
        ->and(Appointment::query()->count())->toBeGreaterThan(0);
});

it('does not duplicate demo data when seeded twice', function () {
    (new DatabaseSeeder)->call(DemoDataSeeder::class);
    (new DatabaseSeeder)->call(DemoDataSeeder::class);

    expect(Service::query()->count())->toBe(4)
        ->and(Client::query()->count())->toBe(3);
});
