<?php

use App\Actions\Tournaments\RegisterTeamForTournament;
use App\Enums\TournamentStatus;
use App\Exceptions\RegistrationNotAllowed;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\User;

test('registers an eligible team to an open tournament', function () {
    $tournament = Tournament::factory()->open()->create([
        'capacity' => 2,
        'team_min_size' => 2,
        'team_max_size' => 3,
    ]);
    $team = teamWithMembers(2);

    $registration = app(RegisterTeamForTournament::class)($tournament, $team);

    $this->assertModelExists($registration);

    expect($registration->tournament->is($tournament))->toBeTrue()
        ->and($registration->team->is($team))->toBeTrue()
        ->and($tournament->fresh()->registeredTeamsCount())->toBe(1)
        ->and($tournament->fresh()->remainingCapacity())->toBe(1)
        ->and($tournament->fresh()->isFull())->toBeFalse();
});

test('refuses tournaments that are not open', function (TournamentStatus $status) {
    $tournament = Tournament::factory()->create([
        'status' => $status,
        'capacity' => 2,
        'team_min_size' => 1,
        'team_max_size' => 3,
    ]);
    $team = teamWithMembers(1);

    expect(fn () => app(RegisterTeamForTournament::class)($tournament, $team))
        ->toThrow(RegistrationNotAllowed::class, 'The tournament is not open for registrations.');
})->with([
    'draft' => TournamentStatus::Draft,
    'closed' => TournamentStatus::Closed,
]);

test('refuses registrations when the tournament is full', function () {
    $tournament = Tournament::factory()->open()->create([
        'capacity' => 1,
        'team_min_size' => 1,
        'team_max_size' => 3,
    ]);

    app(RegisterTeamForTournament::class)($tournament, teamWithMembers(1));

    expect(fn () => app(RegisterTeamForTournament::class)($tournament, teamWithMembers(1)))
        ->toThrow(RegistrationNotAllowed::class, 'The tournament is full.');

    expect($tournament->fresh()->isFull())->toBeTrue();
});

test('refuses duplicate team registrations', function () {
    $tournament = Tournament::factory()->open()->create([
        'capacity' => 2,
        'team_min_size' => 1,
        'team_max_size' => 3,
    ]);
    $team = teamWithMembers(1);

    app(RegisterTeamForTournament::class)($tournament, $team);

    expect(fn () => app(RegisterTeamForTournament::class)($tournament, $team))
        ->toThrow(RegistrationNotAllowed::class, 'The team is already registered for this tournament.');
});

test('refuses teams smaller than the tournament minimum size', function () {
    $tournament = Tournament::factory()->open()->create([
        'capacity' => 2,
        'team_min_size' => 2,
        'team_max_size' => 3,
    ]);

    expect(fn () => app(RegisterTeamForTournament::class)($tournament, teamWithMembers(1)))
        ->toThrow(RegistrationNotAllowed::class, 'The team size must be between 2 and 3 members.');
});

test('refuses teams larger than the tournament maximum size', function () {
    $tournament = Tournament::factory()->open()->create([
        'capacity' => 2,
        'team_min_size' => 1,
        'team_max_size' => 3,
    ]);

    expect(fn () => app(RegisterTeamForTournament::class)($tournament, teamWithMembers(4)))
        ->toThrow(RegistrationNotAllowed::class, 'The team size must be between 1 and 3 members.');
});

test('refuses teams whose captain is not a member', function () {
    $tournament = Tournament::factory()->open()->create([
        'capacity' => 2,
        'team_min_size' => 1,
        'team_max_size' => 3,
    ]);
    $captain = User::factory()->publicParticipant()->create();
    $member = User::factory()->publicParticipant()->create();
    $team = Team::factory()
        ->for($captain, 'captain')
        ->create();
    $team->users()->attach($member);

    expect(fn () => app(RegisterTeamForTournament::class)($tournament, $team))
        ->toThrow(RegistrationNotAllowed::class, 'The team captain must belong to the team.');
});

test('refuses users already registered to the same tournament with another team', function () {
    $tournament = Tournament::factory()->open()->create([
        'capacity' => 2,
        'team_min_size' => 2,
        'team_max_size' => 3,
    ]);
    $sharedMember = User::factory()->publicParticipant()->create();
    $firstTeam = teamWithMembers(2, [$sharedMember]);
    $secondTeam = teamWithMembers(2, [$sharedMember]);

    app(RegisterTeamForTournament::class)($tournament, $firstTeam);

    expect(fn () => app(RegisterTeamForTournament::class)($tournament, $secondTeam))
        ->toThrow(
            RegistrationNotAllowed::class,
            'A team member is already registered for this tournament with another team.',
        );
});

test('allows a user to belong to teams registered on different tournaments', function () {
    $sharedMember = User::factory()->publicParticipant()->create();
    $firstTournament = Tournament::factory()->open()->create([
        'capacity' => 2,
        'team_min_size' => 2,
        'team_max_size' => 3,
    ]);
    $secondTournament = Tournament::factory()->open()->create([
        'capacity' => 2,
        'team_min_size' => 2,
        'team_max_size' => 3,
    ]);

    app(RegisterTeamForTournament::class)($firstTournament, teamWithMembers(2, [$sharedMember]));
    $registration = app(RegisterTeamForTournament::class)($secondTournament, teamWithMembers(2, [$sharedMember]));

    $this->assertModelExists($registration);
});

/**
 * @param  array<int, User>  $forcedMembers
 */
function teamWithMembers(int $memberCount, array $forcedMembers = []): Team
{
    $captain = User::factory()->publicParticipant()->create();
    $team = Team::factory()
        ->for($captain, 'captain')
        ->create();

    $additionalMembersCount = max(0, $memberCount - 1 - count($forcedMembers));

    $additionalMembers = User::factory()
        ->publicParticipant()
        ->count($additionalMembersCount)
        ->create();

    $memberIds = collect([$captain])
        ->merge($forcedMembers)
        ->merge($additionalMembers)
        ->map(fn (User $user): int => $user->getKey())
        ->all();

    $team->users()->syncWithoutDetaching($memberIds);

    return $team->refresh();
}
