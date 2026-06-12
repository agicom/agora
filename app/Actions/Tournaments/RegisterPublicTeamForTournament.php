<?php

namespace App\Actions\Tournaments;

use App\Models\Registration;
use App\Models\Tournament;
use Illuminate\Support\Facades\DB;

class RegisterPublicTeamForTournament
{
    public function __construct(
        private readonly CreatePublicTeamFromRegistrationIntake $createPublicTeam,
        private readonly RegisterTeamForTournament $registerTeamForTournament,
    ) {}

    public function __invoke(Tournament $tournament, PublicTeamRegistrationIntake $intake): Registration
    {
        return DB::transaction(function () use ($tournament, $intake): Registration {
            $team = ($this->createPublicTeam)($intake);

            return ($this->registerTeamForTournament)($tournament, $team);
        }, attempts: 3);
    }
}
