<?php

namespace App\Actions\Tournaments;

use App\Exceptions\RegistrationNotAllowed;
use App\Models\Registration;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class RegisterTeamForTournament
{
    public function __invoke(Tournament $tournament, Team $team): Registration
    {
        return DB::transaction(function () use ($tournament, $team): Registration {
            $tournament = Tournament::query()
                ->whereKey($tournament->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $team = Team::query()
                ->with('users')
                ->findOrFail($team->getKey());

            $memberIds = $team->users->modelKeys();

            $this->ensureTournamentIsOpen($tournament);
            $this->ensureTournamentHasCapacity($tournament);
            $this->ensureTeamIsNotAlreadyRegistered($tournament, $team);
            $this->ensureCaptainBelongsToTeam($team, $memberIds);
            $this->ensureTeamSizeIsAllowed($tournament, count($memberIds));
            $this->ensureMembersAreNotRegisteredWithAnotherTeam($tournament, $team, $memberIds);

            return Registration::query()->create([
                'tournament_id' => $tournament->getKey(),
                'team_id' => $team->getKey(),
            ]);
        }, attempts: 3);
    }

    private function ensureTournamentIsOpen(Tournament $tournament): void
    {
        if (! $tournament->isOpen()) {
            throw RegistrationNotAllowed::tournamentIsNotOpen();
        }
    }

    private function ensureTournamentHasCapacity(Tournament $tournament): void
    {
        if ($tournament->isFull()) {
            throw RegistrationNotAllowed::tournamentIsFull();
        }
    }

    private function ensureTeamIsNotAlreadyRegistered(Tournament $tournament, Team $team): void
    {
        if ($tournament->registrations()->whereBelongsTo($team)->exists()) {
            throw RegistrationNotAllowed::teamAlreadyRegistered();
        }
    }

    /**
     * @param  array<int, int|string>  $memberIds
     */
    private function ensureCaptainBelongsToTeam(Team $team, array $memberIds): void
    {
        if (! in_array($team->captain_id, $memberIds, true)) {
            throw RegistrationNotAllowed::captainMustBeMember();
        }
    }

    private function ensureTeamSizeIsAllowed(Tournament $tournament, int $memberCount): void
    {
        if ($memberCount < $tournament->team_min_size || $memberCount > $tournament->team_max_size) {
            throw RegistrationNotAllowed::teamSizeIsInvalid(
                $tournament->team_min_size,
                $tournament->team_max_size,
            );
        }
    }

    /**
     * @param  array<int, int|string>  $memberIds
     */
    private function ensureMembersAreNotRegisteredWithAnotherTeam(Tournament $tournament, Team $team, array $memberIds): void
    {
        $hasConflictingMember = $tournament->registrations()
            ->where('team_id', '!=', $team->getKey())
            ->whereHas('team.users', function (Builder $query) use ($memberIds): void {
                $query->whereIn((new User)->getQualifiedKeyName(), $memberIds);
            })
            ->exists();

        if ($hasConflictingMember) {
            throw RegistrationNotAllowed::memberAlreadyRegistered();
        }
    }
}
