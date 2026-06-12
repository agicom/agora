<?php

namespace App\Filament\Resources\Tournaments\Schemas;

use App\Enums\TournamentStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Enum;

class TournamentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nom')
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (?string $state, callable $set): void {
                        if (filled($state)) {
                            $set('slug', Str::slug($state));
                        }
                    })
                    ->required(),
                TextInput::make('slug')
                    ->label('Slug public')
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->required(),
                Textarea::make('description')
                    ->label('Description')
                    ->rows(5)
                    ->columnSpanFull(),
                DateTimePicker::make('starts_at')
                    ->label('Date de debut')
                    ->seconds(false),
                Select::make('status')
                    ->label('Statut')
                    ->options(TournamentStatus::class)
                    ->default(TournamentStatus::Draft)
                    ->rules([new Enum(TournamentStatus::class)])
                    ->required(),
                TextInput::make('capacity')
                    ->label('Capacite equipes')
                    ->required()
                    ->integer()
                    ->minValue(1),
                TextInput::make('team_min_size')
                    ->label('Taille minimale equipe')
                    ->required()
                    ->integer()
                    ->minValue(1)
                    ->default(1),
                TextInput::make('team_max_size')
                    ->label('Taille maximale equipe')
                    ->required()
                    ->integer()
                    ->minValue(1)
                    ->rules(['gte:data.team_min_size']),
            ]);
    }
}
