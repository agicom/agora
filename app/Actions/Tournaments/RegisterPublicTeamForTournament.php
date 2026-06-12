<?php

namespace App\Actions\Tournaments;

use App\Enums\UserRole;
use App\Exceptions\RegistrationNotAllowed;
use App\Models\Registration;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RegisterPublicTeamForTournament
{
    /**
     * @param  array<int, array{name: string, email: string}>  $members
     */
    public function __invoke(Tournament $tournament, string $teamName, array $members): Registration
    {
        return DB::transaction(function () use ($tournament, $teamName, $members): Registration {
            $normalizedMembers = $this->normalizeMembers($members);

            $team = Team::query()->create([
                'name' => trim($teamName),
                'captain_id' => $this->publicUserFor($normalizedMembers[0])->getKey(),
            ]);

            $memberIds = collect($normalizedMembers)
                ->map(fn (array $member): int => $this->publicUserFor($member)->getKey())
                ->all();

            $team->users()->sync($memberIds);

            return app(RegisterTeamForTournament::class)($tournament, $team);
        }, attempts: 3);
    }

    /**
     * @param  array<int, array{name: string, email: string}>  $members
     * @return array<int, array{name: string, email: string}>
     */
    private function normalizeMembers(array $members): array
    {
        $normalizedMembers = collect($members)
            ->map(fn (array $member): array => [
                'name' => trim($member['name']),
                'email' => Str::lower(trim($member['email'])),
            ])
            ->values();

        if ($normalizedMembers->pluck('email')->duplicates()->isNotEmpty()) {
            throw RegistrationNotAllowed::duplicateMemberEmail();
        }

        return $normalizedMembers->all();
    }

    /**
     * @param  array{name: string, email: string}  $member
     */
    private function publicUserFor(array $member): User
    {
        $user = User::query()
            ->whereRaw('lower(email) = ?', [$member['email']])
            ->first();

        if ($user?->isAdmin()) {
            throw RegistrationNotAllowed::administratorEmailCannotRegister();
        }

        if ($user instanceof User) {
            return $user;
        }

        return User::query()->create([
            'name' => $member['name'],
            'email' => $member['email'],
            'role' => UserRole::Public,
            'password' => Str::random(32),
        ]);
    }
}
