<?php

use App\Enums\TournamentStatus;
use App\Models\Tournament;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome', [
        'tournaments' => Tournament::query()
            ->where('status', TournamentStatus::Open)
            ->withCount('registrations')
            ->orderByRaw('starts_at is null, starts_at asc')
            ->get(),
    ]);
})->name('home');

Route::livewire('tournois/{tournament:slug}/inscription', 'pages::tournaments.register')
    ->name('tournaments.registrations.create');
