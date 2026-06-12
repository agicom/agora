<?php

use App\Actions\Tournaments\CloseTournamentRegistrations;
use App\Enums\TournamentStatus;
use App\Models\Tournament;

test('closes tournament registrations', function () {
    $tournament = Tournament::factory()->open()->create();

    $closedTournament = app(CloseTournamentRegistrations::class)($tournament);

    expect($closedTournament->status)->toBe(TournamentStatus::Closed)
        ->and($tournament->fresh()->status)->toBe(TournamentStatus::Closed);
});
