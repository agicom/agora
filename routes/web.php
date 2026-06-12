<?php

use App\Actions\Tournaments\ListPublicTournamentFeed;
use Illuminate\Support\Facades\Route;

Route::get('/', function (ListPublicTournamentFeed $listPublicTournamentFeed) {
    return view('welcome', [
        'feed' => $listPublicTournamentFeed(),
    ]);
})->name('home');

Route::livewire('tournois/{tournament:slug}/inscription', 'pages::tournaments.register')
    ->name('tournaments.registrations.create');
