<?php

namespace App\Filament\Resources\Tournaments\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class TournamentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('slug')
                    ->label('URL publique'),
                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('starts_at')
                    ->label('Date de debut')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('status')
                    ->label('Statut')
                    ->badge(),
                TextEntry::make('capacity')
                    ->label('Capacite equipes')
                    ->numeric(),
                TextEntry::make('team_min_size')
                    ->label('Taille minimale equipe')
                    ->numeric(),
                TextEntry::make('team_max_size')
                    ->label('Taille maximale equipe')
                    ->numeric(),
                TextEntry::make('registrations_count')
                    ->label('Inscriptions')
                    ->state(fn ($record): string => "{$record->registrations()->count()} / {$record->capacity}"),
                TextEntry::make('created_at')
                    ->label('Cree le')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->label('Modifie le')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
