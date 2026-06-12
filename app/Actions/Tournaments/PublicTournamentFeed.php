<?php

namespace App\Actions\Tournaments;

use Illuminate\Support\Collection;

readonly class PublicTournamentFeed
{
    /**
     * @param  Collection<int, PublicTournamentCard>  $cards
     */
    public function __construct(
        public Collection $cards,
        public int $openCount,
        public int $registrationsCount,
    ) {}

    public function tournamentCount(): int
    {
        return $this->cards->count();
    }

    public function isEmpty(): bool
    {
        return $this->cards->isEmpty();
    }
}
