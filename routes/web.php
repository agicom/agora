<?php

use App\Models\Tournament;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome', [
        'tournaments' => Tournament::query()
            ->withCount('registrations')
            ->orderByRaw('starts_at is null, starts_at asc')
            ->get(),
    ]);
})->name('home');

Route::livewire('tournois/{tournament:slug}/inscription', 'pages::tournaments.register')
    ->name('tournaments.registrations.create');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
