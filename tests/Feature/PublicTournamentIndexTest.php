<?php

use App\Actions\Tournaments\PublicTournamentFeed;
use App\Models\Registration;
use App\Models\Tournament;

test('public homepage lists tournaments and links to registration pages', function () {
    $openTournament = Tournament::factory()->open()->create([
        'name' => 'Friday Arena',
        'slug' => 'friday-arena',
        'capacity' => 8,
        'team_min_size' => 2,
        'team_max_size' => 4,
    ]);

    $fullTournament = Tournament::factory()->open()->create([
        'name' => 'Solo Sprint',
        'slug' => 'solo-sprint',
        'capacity' => 1,
        'team_min_size' => 1,
        'team_max_size' => 1,
    ]);

    Registration::factory()
        ->for($fullTournament)
        ->create();

    $this->get(route('home'))
        ->assertOk()
        ->assertViewHas('feed', fn (PublicTournamentFeed $feed): bool => $feed->tournamentCount() === 2)
        ->assertSee('Choisis ton tournoi')
        ->assertSee('Friday Arena')
        ->assertSee('Solo Sprint')
        ->assertSee(route('tournaments.registrations.create', $openTournament))
        ->assertSee('Inscrire une équipe')
        ->assertSee('Complet');
});

test('public homepage only lists open tournaments', function () {
    $openTournament = Tournament::factory()->open()->create([
        'name' => 'Friday Arena',
        'slug' => 'friday-arena',
    ]);

    Tournament::factory()->create([
        'name' => 'Winter Cup',
        'slug' => 'winter-cup',
    ]);

    Tournament::factory()->closed()->create([
        'name' => 'Strategy Masters',
        'slug' => 'strategy-masters',
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Friday Arena')
        ->assertSee(route('tournaments.registrations.create', $openTournament))
        ->assertDontSee('Winter Cup')
        ->assertDontSee('Strategy Masters');
});
