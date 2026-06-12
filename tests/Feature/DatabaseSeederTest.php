<?php

use App\Enums\TournamentStatus;
use App\Models\Registration;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

test('database seeder creates demo data for the admin and public flows', function () {
    $this->seed(DatabaseSeeder::class);

    $admin = User::query()->where('email', 'admin@agora.test')->first();
    $fridayArena = Tournament::query()->where('slug', 'friday-arena')->first();
    $soloSprint = Tournament::query()->where('slug', 'solo-sprint')->first();

    expect($admin?->isAdmin())->toBeTrue()
        ->and(Tournament::query()->count())->toBe(4)
        ->and(Team::query()->count())->toBe(5)
        ->and(Registration::query()->count())->toBe(5)
        ->and($fridayArena?->status)->toBe(TournamentStatus::Open)
        ->and($fridayArena?->registeredTeamsCount())->toBe(2)
        ->and($soloSprint?->isFull())->toBeTrue();
});

test('database seeder can be safely run more than once', function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(DatabaseSeeder::class);

    expect(User::query()->where('email', 'admin@agora.test')->count())->toBe(1)
        ->and(Tournament::query()->count())->toBe(4)
        ->and(Team::query()->count())->toBe(5)
        ->and(Registration::query()->count())->toBe(5);
});
