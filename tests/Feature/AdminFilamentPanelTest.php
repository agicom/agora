<?php

use App\Enums\TournamentStatus;
use App\Filament\Resources\Tournaments\Pages\CreateTournament;
use App\Filament\Resources\Tournaments\Pages\ListTournaments;
use App\Filament\Resources\Tournaments\TournamentResource;
use App\Models\Tournament;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\assertDatabaseHas;

test('admin users can access the Filament panel', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get('/admin')
        ->assertOk();
});

test('public users cannot access the Filament panel', function () {
    $user = User::factory()->publicParticipant()->create();

    $this->actingAs($user)
        ->get('/admin')
        ->assertForbidden();
});

test('admin users can list tournaments in Filament', function () {
    $admin = User::factory()->admin()->create();
    $tournaments = Tournament::factory()->count(3)->create();

    $this->actingAs($admin);

    $this->get(TournamentResource::getUrl('index'))
        ->assertOk();

    Livewire::test(ListTournaments::class)
        ->assertOk()
        ->assertCanSeeTableRecords($tournaments);
});

test('admin users can create tournaments in Filament', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    Livewire::test(CreateTournament::class)
        ->fillForm([
            'name' => 'Friday Arena',
            'slug' => 'friday-arena',
            'status' => TournamentStatus::Open,
            'capacity' => 16,
            'team_min_size' => 2,
            'team_max_size' => 4,
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertNotified();

    assertDatabaseHas(Tournament::class, [
        'name' => 'Friday Arena',
        'slug' => 'friday-arena',
        'status' => TournamentStatus::Open->value,
        'capacity' => 16,
        'team_min_size' => 2,
        'team_max_size' => 4,
    ]);
});
