<?php

namespace App\Actions\Tournaments;

use Illuminate\Support\Str;

readonly class PublicTeamMemberIntake
{
    /**
     * @param  array{name: string, email: string}  $member
     */
    public static function fromArray(array $member): self
    {
        return new self(
            name: trim($member['name']),
            email: Str::lower(trim($member['email'])),
        );
    }

    public function __construct(
        public string $name,
        public string $email,
    ) {}
}
