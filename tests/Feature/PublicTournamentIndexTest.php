<?php

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
        ->assertSee('Choisis ton tournoi')
        ->assertSee('Friday Arena')
        ->assertSee('Solo Sprint')
        ->assertSee(route('tournaments.registrations.create', $openTournament))
        ->assertSee('Inscrire une équipe')
        ->assertSee('Complet');
});
