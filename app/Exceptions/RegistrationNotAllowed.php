<?php

namespace App\Exceptions;

use RuntimeException;

class RegistrationNotAllowed extends RuntimeException
{
    public static function tournamentIsNotOpen(): self
    {
        return new self('The tournament is not open for registrations.');
    }

    public static function tournamentIsFull(): self
    {
        return new self('The tournament is full.');
    }

    public static function teamAlreadyRegistered(): self
    {
        return new self('The team is already registered for this tournament.');
    }

    public static function captainMustBeMember(): self
    {
        return new self('The team captain must belong to the team.');
    }

    public static function teamSizeIsInvalid(int $minimum, int $maximum): self
    {
        return new self("The team size must be between {$minimum} and {$maximum} members.");
    }

    public static function memberAlreadyRegistered(): self
    {
        return new self('A team member is already registered for this tournament with another team.');
    }

    public static function duplicateMemberEmail(): self
    {
        return new self('Each team member must use a different email address.');
    }

    public static function administratorEmailCannotRegister(): self
    {
        return new self('An administrator email cannot be used for a public registration.');
    }
}
