<?php

use App\Actions\Tournaments\PublicTeamRegistrationIntake;
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
        PublicTeamRegistrationIntake::fromValidated([
            'teamName' => ' Pixel Pushers ',
            'members' => [
                ['name' => ' Nina Captain ', 'email' => ' NINA@example.com '],
                ['name' => 'Sam Player', 'email' => 'sam@example.com'],
            ],
        ]),
    );

    $team = $registration->team()->with(['captain', 'users'])->first();

    $this->assertModelExists($registration);

    expect($team->name)->toBe('Pixel Pushers')
        ->and($team->captain->email)->toBe('nina@example.com')
        ->and($team->captain->name)->toBe('Nina Captain')
        ->and($team->users)->toHaveCount(2)
        ->and($team->users->pluck('role')->unique()->all())->toBe([UserRole::Public]);
});

test('public registration intake normalizes team and member data', function () {
    $intake = PublicTeamRegistrationIntake::fromValidated([
        'teamName' => ' Known Team ',
        'members' => [
            ['name' => 'Nina Captain', 'email' => 'NINA@example.com'],
            ['name' => ' Sam Player ', 'email' => ' SAM@example.com '],
        ],
    ]);

    expect($intake->teamName)->toBe('Known Team')
        ->and($intake->captain()->email)->toBe('nina@example.com')
        ->and($intake->members->last()->name)->toBe('Sam Player')
        ->and($intake->members->last()->email)->toBe('sam@example.com');
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
        PublicTeamRegistrationIntake::fromValidated([
            'teamName' => 'Known Team',
            'members' => [
                ['name' => 'New Name', 'email' => 'LEE@example.com'],
            ],
        ]),
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
        PublicTeamRegistrationIntake::fromValidated([
            'teamName' => 'Admin Team',
            'members' => [
                ['name' => 'Admin', 'email' => 'admin@example.com'],
            ],
        ]),
    ))->toThrow(RegistrationNotAllowed::class, 'An administrator email cannot be used for a public registration.');
});

test('refuses duplicate member emails', function () {
    expect(fn () => PublicTeamRegistrationIntake::fromValidated([
        'teamName' => 'Duplicate Team',
        'members' => [
            ['name' => 'First', 'email' => 'same@example.com'],
            ['name' => 'Second', 'email' => 'SAME@example.com'],
        ],
    ]))->toThrow(RegistrationNotAllowed::class, 'Each team member must use a different email address.');
});
