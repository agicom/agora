<?php

namespace App\Actions\Tournaments;

use App\Exceptions\RegistrationNotAllowed;
use Illuminate\Support\Collection;

readonly class PublicTeamRegistrationIntake
{
    /**
     * @param  array{teamName: string, members: array<int, array{name: string, email: string}>}  $validated
     */
    public static function fromValidated(array $validated): self
    {
        $members = collect($validated['members'])
            ->map(fn (array $member): PublicTeamMemberIntake => PublicTeamMemberIntake::fromArray($member))
            ->values();

        if ($members->pluck('email')->duplicates()->isNotEmpty()) {
            throw RegistrationNotAllowed::duplicateMemberEmail();
        }

        return new self(
            teamName: trim($validated['teamName']),
            members: $members,
        );
    }

    /**
     * @param  Collection<int, PublicTeamMemberIntake>  $members
     */
    public function __construct(
        public string $teamName,
        public Collection $members,
    ) {}

    public function captain(): PublicTeamMemberIntake
    {
        return $this->members->first();
    }
}
