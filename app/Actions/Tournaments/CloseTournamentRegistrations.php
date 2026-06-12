<?php

namespace App\Actions\Tournaments;

use App\Enums\TournamentStatus;
use App\Models\Tournament;

class CloseTournamentRegistrations
{
    public function __invoke(Tournament $tournament): Tournament
    {
        $tournament->forceFill([
            'status' => TournamentStatus::Closed,
        ])->save();

        return $tournament->refresh();
    }
}
