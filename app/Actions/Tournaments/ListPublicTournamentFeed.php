<?php

namespace App\Actions\Tournaments;

use App\Enums\TournamentStatus;
use App\Models\Tournament;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class ListPublicTournamentFeed
{
    public function __invoke(): PublicTournamentFeed
    {
        $tournaments = Tournament::query()
            ->where('status', TournamentStatus::Open)
            ->withCount('registrations')
            ->orderByRaw('starts_at is null, starts_at asc')
            ->get();

        return new PublicTournamentFeed(
            cards: $tournaments->map(fn (Tournament $tournament): PublicTournamentCard => $this->cardFor($tournament)),
            openCount: $tournaments->count(),
            registrationsCount: $this->registrationsCount($tournaments),
        );
    }

    private function cardFor(Tournament $tournament): PublicTournamentCard
    {
        $registrationsCount = (int) $tournament->registrations_count;
        $remainingCapacity = max(0, $tournament->capacity - $registrationsCount);
        $isFull = $remainingCapacity === 0;

        return new PublicTournamentCard(
            id: $tournament->getKey(),
            name: $tournament->name,
            description: $tournament->description ?: 'Aucune description pour le moment.',
            startsAt: $tournament->starts_at,
            remainingCapacity: $remainingCapacity,
            teamMinSize: $tournament->team_min_size,
            teamMaxSize: $tournament->team_max_size,
            registrationsCount: $registrationsCount,
            isFull: $isFull,
            statusLabel: $isFull ? 'Complet' : 'Ouvert',
            statusColor: $isFull ? 'red' : 'green',
            registrationUrl: route('tournaments.registrations.create', $tournament),
            callToActionLabel: $isFull ? 'Voir le tournoi' : 'Inscrire une équipe',
            callToActionVariant: $isFull ? 'filled' : 'primary',
        );
    }

    /**
     * @param  EloquentCollection<int, Tournament>  $tournaments
     */
    private function registrationsCount(EloquentCollection $tournaments): int
    {
        return $tournaments->sum(fn (Tournament $tournament): int => (int) $tournament->registrations_count);
    }
}
