<?php

namespace App\Actions\Tournaments;

use Carbon\CarbonInterface;

readonly class PublicTournamentCard
{
    public function __construct(
        public int $id,
        public string $name,
        public string $description,
        public ?CarbonInterface $startsAt,
        public int $remainingCapacity,
        public int $teamMinSize,
        public int $teamMaxSize,
        public int $registrationsCount,
        public bool $isFull,
        public string $statusLabel,
        public string $statusColor,
        public string $registrationUrl,
        public string $callToActionLabel,
        public string $callToActionVariant,
    ) {}
}
