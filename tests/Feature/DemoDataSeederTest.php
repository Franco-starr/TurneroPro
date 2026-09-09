<?php

use App\Models\Appointment;
use App\Models\Client;
use App\Models\Service;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\DemoDataSeeder;
use Illuminate\Support\Facades\Hash;

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

it('seeds the admin user with the portfolio credentials', function () {
    (new DatabaseSeeder)->run();

    $admin = User::query()->where('email', 'test@example.com')->first();

    expect($admin)->not->toBeNull()
        ->and(Hash::check('password', $admin->password))->toBeTrue();
});
