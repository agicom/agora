<?php

namespace App\Actions\Tournaments;

use App\Enums\UserRole;
use App\Exceptions\RegistrationNotAllowed;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Str;

class CreatePublicTeamFromRegistrationIntake
{
    public function __invoke(PublicTeamRegistrationIntake $intake): Team
    {
        $captain = $this->publicUserFor($intake->captain());

        $team = Team::query()->create([
            'name' => $intake->teamName,
            'captain_id' => $captain->getKey(),
        ]);

        $memberIds = $intake->members
            ->map(fn (PublicTeamMemberIntake $member): int => $this->publicUserFor($member)->getKey())
            ->all();

        $team->users()->sync($memberIds);

        return $team->refresh();
    }

    private function publicUserFor(PublicTeamMemberIntake $member): User
    {
        $user = User::query()
            ->whereRaw('lower(email) = ?', [$member->email])
            ->first();

        if ($user?->isAdmin()) {
            throw RegistrationNotAllowed::administratorEmailCannotRegister();
        }

        if (! $user instanceof User) {
            $user = User::query()->create([
                'name' => $member->name,
                'email' => $member->email,
                'role' => UserRole::Public,
                'password' => Str::random(32),
            ]);
        }

        return $user;
    }
}
