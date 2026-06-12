<?php

namespace App\Filament\Widgets;

use App\Enums\TournamentStatus;
use App\Models\Registration;
use App\Models\Tournament;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TournamentStatsOverview extends StatsOverviewWidget
{
    protected ?string $pollingInterval = '30s';

    /**
     * @return array<int, Stat>
     */
    protected function getStats(): array
    {
        return [
            Stat::make('Tournois', Tournament::query()->count()),
            Stat::make('Tournois ouverts', Tournament::query()->where('status', TournamentStatus::Open)->count())
                ->color('success'),
            Stat::make('Inscriptions', Registration::query()->count())
                ->color('info'),
        ];
    }
}
