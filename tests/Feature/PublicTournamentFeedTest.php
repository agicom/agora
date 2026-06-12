<?php

use App\Actions\Tournaments\ListPublicTournamentFeed;
use App\Models\Registration;
use App\Models\Tournament;

test('public tournament feed exposes display-ready open tournament cards', function () {
    $openTournament = Tournament::factory()->open()->create([
        'name' => 'Friday Arena',
        'slug' => 'friday-arena',
        'starts_at' => now()->addWeek(),
        'capacity' => 4,
        'team_min_size' => 2,
        'team_max_size' => 4,
    ]);

    $fullTournament = Tournament::factory()->open()->create([
        'name' => 'Solo Sprint',
        'slug' => 'solo-sprint',
        'starts_at' => now()->addWeeks(2),
        'capacity' => 1,
        'team_min_size' => 1,
        'team_max_size' => 1,
    ]);

    Tournament::factory()->create(['name' => 'Winter Cup']);
    Tournament::factory()->closed()->create(['name' => 'Strategy Masters']);

    Registration::factory()->for($fullTournament)->create();

    $feed = app(ListPublicTournamentFeed::class)();

    expect($feed->tournamentCount())->toBe(2)
        ->and($feed->openCount)->toBe(2)
        ->and($feed->registrationsCount)->toBe(1)
        ->and($feed->cards->pluck('name')->all())->toBe(['Friday Arena', 'Solo Sprint']);

    $openCard = $feed->cards->firstWhere('name', 'Friday Arena');
    $fullCard = $feed->cards->firstWhere('name', 'Solo Sprint');

    expect($openCard->remainingCapacity)->toBe(4)
        ->and($openCard->teamMinSize)->toBe(2)
        ->and($openCard->teamMaxSize)->toBe(4)
        ->and($openCard->statusLabel)->toBe('Ouvert')
        ->and($openCard->statusColor)->toBe('green')
        ->and($openCard->registrationUrl)->toBe(route('tournaments.registrations.create', $openTournament))
        ->and($openCard->callToActionLabel)->toBe('Inscrire une équipe')
        ->and($openCard->callToActionVariant)->toBe('primary');

    expect($fullCard->remainingCapacity)->toBe(0)
        ->and($fullCard->isFull)->toBeTrue()
        ->and($fullCard->statusLabel)->toBe('Complet')
        ->and($fullCard->statusColor)->toBe('red')
        ->and($fullCard->callToActionLabel)->toBe('Voir le tournoi')
        ->and($fullCard->callToActionVariant)->toBe('filled');
});
