<?php

use App\Actions\Tournaments\RegisterPublicTeamForTournament;
use App\Enums\UserRole;
use App\Exceptions\RegistrationNotAllowed;
use App\Models\Tournament;
use App\Models\User;

test('creates a public team and registers it to a tournament', function () {
    $tournament = Tournament::factory()->open()->create([
        'capacity' => 4,
        'team_min_size' => 2,
        'team_max_size' => 4,
    ]);

    $registration = app(RegisterPublicTeamForTournament::class)(
        $tournament,
        'Pixel Pushers',
        [
            ['name' => 'Nina Captain', 'email' => 'NINA@example.com'],
            ['name' => 'Sam Player', 'email' => 'sam@example.com'],
        ],
    );

    $team = $registration->team()->with(['captain', 'users'])->first();

    $this->assertModelExists($registration);

    expect($team->name)->toBe('Pixel Pushers')
        ->and($team->captain->email)->toBe('nina@example.com')
        ->and($team->users)->toHaveCount(2)
        ->and($team->users->pluck('role')->unique()->all())->toBe([UserRole::Public]);
});

test('reuses existing public users by email', function () {
    $existingUser = User::factory()->publicParticipant()->create([
        'name' => 'Existing Name',
        'email' => 'lee@example.com',
    ]);
    $tournament = Tournament::factory()->open()->create([
        'capacity' => 4,
        'team_min_size' => 1,
        'team_max_size' => 4,
    ]);

    $registration = app(RegisterPublicTeamForTournament::class)(
        $tournament,
        'Known Team',
        [
            ['name' => 'New Name', 'email' => 'LEE@example.com'],
        ],
    );

    expect($registration->team->captain->is($existingUser))->toBeTrue()
        ->and(User::query()->where('email', 'lee@example.com')->count())->toBe(1);
});

test('refuses administrator emails', function () {
    User::factory()->admin()->create([
        'email' => 'admin@example.com',
    ]);
    $tournament = Tournament::factory()->open()->create([
        'capacity' => 4,
        'team_min_size' => 1,
        'team_max_size' => 4,
    ]);

    expect(fn () => app(RegisterPublicTeamForTournament::class)(
        $tournament,
        'Admin Team',
        [
            ['name' => 'Admin', 'email' => 'admin@example.com'],
        ],
    ))->toThrow(RegistrationNotAllowed::class, 'An administrator email cannot be used for a public registration.');
});

test('refuses duplicate member emails', function () {
    $tournament = Tournament::factory()->open()->create([
        'capacity' => 4,
        'team_min_size' => 1,
        'team_max_size' => 4,
    ]);

    expect(fn () => app(RegisterPublicTeamForTournament::class)(
        $tournament,
        'Duplicate Team',
        [
            ['name' => 'First', 'email' => 'same@example.com'],
            ['name' => 'Second', 'email' => 'SAME@example.com'],
        ],
    ))->toThrow(RegistrationNotAllowed::class, 'Each team member must use a different email address.');
});
