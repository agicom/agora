<?php

namespace Database\Seeders;

use App\Actions\Tournaments\CloseTournamentRegistrations;
use App\Actions\Tournaments\RegisterTeamForTournament;
use App\Enums\TournamentStatus;
use App\Enums\UserRole;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DemoTournamentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(
        RegisterTeamForTournament $registerTeamForTournament,
        CloseTournamentRegistrations $closeTournamentRegistrations,
    ): void {
        $fridayArena = $this->tournament([
            'name' => 'Friday Arena',
            'slug' => 'friday-arena',
            'description' => 'Tournoi principal de la soirée, ouvert aux équipes de deux à quatre joueurs.',
            'starts_at' => Carbon::now()->addDays(10)->setTime(20, 0),
            'status' => TournamentStatus::Open,
            'capacity' => 8,
            'team_min_size' => 2,
            'team_max_size' => 4,
        ]);

        $soloSprint = $this->tournament([
            'name' => 'Solo Sprint',
            'slug' => 'solo-sprint',
            'description' => 'Format court en solo, volontairement complet pour démontrer le blocage public.',
            'starts_at' => Carbon::now()->addDays(17)->setTime(18, 30),
            'status' => TournamentStatus::Open,
            'capacity' => 2,
            'team_min_size' => 1,
            'team_max_size' => 1,
        ]);

        $strategyMasters = $this->tournament([
            'name' => 'Strategy Masters',
            'slug' => 'strategy-masters',
            'description' => 'Tournoi clos par l’administration, visible en back-office.',
            'starts_at' => Carbon::now()->addDays(24)->setTime(21, 0),
            'status' => TournamentStatus::Open,
            'capacity' => 16,
            'team_min_size' => 3,
            'team_max_size' => 5,
        ]);

        $this->tournament([
            'name' => 'Winter Cup',
            'slug' => 'winter-cup',
            'description' => 'Tournoi en préparation, conservé en brouillon.',
            'starts_at' => Carbon::now()->addMonth()->setTime(19, 0),
            'status' => TournamentStatus::Draft,
            'capacity' => 12,
            'team_min_size' => 2,
            'team_max_size' => 4,
        ]);

        $this->register($registerTeamForTournament, $fridayArena, $this->team('Pixel Punchers', [
            ['name' => 'Nina Pixel', 'email' => 'nina.pixel@example.test'],
            ['name' => 'Sam Circuit', 'email' => 'sam.circuit@example.test'],
        ]));

        $this->register($registerTeamForTournament, $fridayArena, $this->team('LAN Rangers', [
            ['name' => 'Maya LAN', 'email' => 'maya.lan@example.test'],
            ['name' => 'Theo Switch', 'email' => 'theo.switch@example.test'],
            ['name' => 'Zoé Packet', 'email' => 'zoe.packet@example.test'],
        ]));

        $this->register($registerTeamForTournament, $soloSprint, $this->team('Solo Alpha', [
            ['name' => 'Alex Alpha', 'email' => 'alex.alpha@example.test'],
        ]));

        $this->register($registerTeamForTournament, $soloSprint, $this->team('Solo Bravo', [
            ['name' => 'Billie Bravo', 'email' => 'billie.bravo@example.test'],
        ]));

        $this->register($registerTeamForTournament, $strategyMasters, $this->team('Macro Minds', [
            ['name' => 'Clara Macro', 'email' => 'clara.macro@example.test'],
            ['name' => 'Noah Build', 'email' => 'noah.build@example.test'],
            ['name' => 'Iris Scout', 'email' => 'iris.scout@example.test'],
        ]));

        $closeTournamentRegistrations($strategyMasters);
    }

    /**
     * @param  array{name: string, slug: string, description: string, starts_at: Carbon, status: TournamentStatus, capacity: int, team_min_size: int, team_max_size: int}  $attributes
     */
    private function tournament(array $attributes): Tournament
    {
        return Tournament::query()->updateOrCreate(
            ['slug' => $attributes['slug']],
            $attributes,
        );
    }

    /**
     * @param  array<int, array{name: string, email: string}>  $members
     */
    private function team(string $name, array $members): Team
    {
        $users = collect($members)
            ->map(fn (array $member): User => User::query()->updateOrCreate(
                ['email' => $member['email']],
                [
                    'name' => $member['name'],
                    'role' => UserRole::Public,
                    'email_verified_at' => now(),
                    'password' => 'password',
                ],
            ));

        $captain = $users->firstOrFail();

        $team = Team::query()->updateOrCreate(
            ['name' => $name],
            ['captain_id' => $captain->getKey()],
        );

        $team->users()->sync($users->pluck('id')->all());

        return $team->refresh();
    }

    private function register(
        RegisterTeamForTournament $registerTeamForTournament,
        Tournament $tournament,
        Team $team,
    ): void {
        if ($tournament->registrations()->whereBelongsTo($team)->exists()) {
            return;
        }

        $registerTeamForTournament($tournament, $team);
    }
}
