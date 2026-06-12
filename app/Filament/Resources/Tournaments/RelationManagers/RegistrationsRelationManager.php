<?php

namespace App\Filament\Resources\Tournaments\RelationManagers;

use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RegistrationsRelationManager extends RelationManager
{
    protected static string $relationship = 'registrations';

    protected static ?string $title = 'Inscriptions';

    protected static ?string $modelLabel = 'inscription';

    protected static ?string $pluralModelLabel = 'inscriptions';

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('team.name')
                    ->label('Equipe'),
                TextEntry::make('team.captain.name')
                    ->label('Capitaine'),
                TextEntry::make('created_at')
                    ->label('Inscrite le')
                    ->dateTime('d/m/Y H:i'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('team.name')
                    ->label('Equipe')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('team.captain.name')
                    ->label('Capitaine')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Inscrite le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
