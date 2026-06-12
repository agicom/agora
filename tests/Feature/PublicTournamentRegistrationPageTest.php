<?php

use App\Models\Registration;
use App\Models\Tournament;
use App\Models\User;
use Livewire\Livewire;

test('public registration page renders from the tournament slug', function () {
    $tournament = Tournament::factory()->open()->create([
        'name' => 'Friday Arena',
        'slug' => 'friday-arena',
        'capacity' => 8,
        'team_min_size' => 1,
        'team_max_size' => 4,
    ]);

    $this->get(route('tournaments.registrations.create', $tournament))
        ->assertOk()
        ->assertSee('Friday Arena')
        ->assertSee('Inscrire une équipe');
});

test('public users can submit a valid team registration', function () {
    $tournament = Tournament::factory()->open()->create([
        'capacity' => 8,
        'team_min_size' => 2,
        'team_max_size' => 4,
    ]);

    Livewire::test('pages::tournaments.register', ['tournament' => $tournament])
        ->set('teamName', 'LAN Rangers')
        ->set('members.0.name', 'Nina')
        ->set('members.0.email', 'nina@example.com')
        ->call('addMember')
        ->set('members.1.name', 'Sam')
        ->set('members.1.email', 'sam@example.com')
        ->call('submit')
        ->assertSet('registered', true)
        ->assertSee('Équipe inscrite');

    expect(Registration::query()->whereBelongsTo($tournament)->count())->toBe(1)
        ->and(User::query()->whereIn('email', ['nina@example.com', 'sam@example.com'])->count())->toBe(2);
});

test('public registration validates required fields', function () {
    $tournament = Tournament::factory()->open()->create([
        'capacity' => 8,
        'team_min_size' => 1,
        'team_max_size' => 4,
    ]);

    Livewire::test('pages::tournaments.register', ['tournament' => $tournament])
        ->call('submit')
        ->assertHasErrors([
            'teamName' => ['required'],
            'members.0.name' => ['required'],
            'members.0.email' => ['required'],
        ]);
});

test('public registration blocks full tournaments', function () {
    $tournament = Tournament::factory()->open()->create([
        'capacity' => 1,
        'team_min_size' => 1,
        'team_max_size' => 4,
    ]);

    Registration::factory()
        ->for($tournament)
        ->create();

    Livewire::test('pages::tournaments.register', ['tournament' => $tournament])
        ->assertSee('Les inscriptions ne sont pas disponibles')
        ->assertDontSee('Confirmer l’inscription');
});
