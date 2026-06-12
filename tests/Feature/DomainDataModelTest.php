<?php

use App\Enums\TournamentStatus;
use App\Enums\UserRole;
use App\Models\Registration;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\User;

test('teams are durable entities with a public captain and members', function () {
    $captain = User::factory()->publicParticipant()->create();
    $member = User::factory()->publicParticipant()->create();

    $team = Team::factory()
        ->for($captain, 'captain')
        ->create();

    $team->users()->attach([$captain->id, $member->id]);

    expect($team->captain->is($captain))->toBeTrue()
        ->and($team->users)->toHaveCount(2)
        ->and($captain->role)->toBe(UserRole::Public);
});

test('a tournament can receive a team registration', function () {
    $tournament = Tournament::factory()->open()->create([
        'capacity' => 16,
        'team_min_size' => 1,
        'team_max_size' => 5,
    ]);
    $team = Team::factory()->create();

    $registration = Registration::factory()
        ->for($tournament)
        ->for($team)
        ->create();

    expect($registration->tournament->is($tournament))->toBeTrue()
        ->and($registration->team->is($team))->toBeTrue()
        ->and($tournament->fresh()->registrations)->toHaveCount(1)
        ->and($tournament->status)->toBe(TournamentStatus::Open);
});
