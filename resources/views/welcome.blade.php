<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <?php $title = 'Tournois'; ?>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-zinc-50 text-zinc-950 antialiased dark:bg-zinc-950 dark:text-white">
        <div class="min-h-screen bg-[linear-gradient(to_bottom,rgba(16,185,129,0.10),transparent_22rem)]">
            <flux:header container class="border-b border-zinc-200/80 bg-white/85 backdrop-blur dark:border-white/10 dark:bg-zinc-950/80">
                <a href="{{ route('home') }}" class="flex items-center gap-3 rounded-lg outline-none focus-visible:ring-2 focus-visible:ring-emerald-500" wire:navigate>
                    <span class="grid size-10 place-items-center rounded-lg bg-zinc-950 text-white shadow-sm dark:bg-white dark:text-zinc-950">
                        <x-app-logo-icon class="size-6 fill-current" />
                    </span>
                    <span class="leading-tight">
                        <span class="block text-sm font-semibold uppercase tracking-[0.16em] text-emerald-700 dark:text-emerald-300">Agora</span>
                        <span class="block text-xs text-zinc-500 dark:text-zinc-400">Tournois LAN</span>
                    </span>
                </a>

                <flux:spacer />

                <flux:button href="{{ url('/admin') }}" variant="ghost" size="sm">Admin</flux:button>
            </flux:header>

            <flux:main container class="space-y-8 py-8 lg:py-12">
                <section class="grid gap-6 lg:grid-cols-[1fr_24rem] lg:items-end">
                    <div class="space-y-5">
                        <flux:badge color="green">Inscriptions publiques</flux:badge>

                        <div class="max-w-4xl space-y-4">
                            <flux:heading size="xl" level="1" class="!text-4xl !leading-tight sm:!text-5xl lg:!text-6xl">
                                Choisis ton tournoi, assemble ton équipe.
                            </flux:heading>

                            <flux:text class="max-w-2xl text-base! leading-7!">
                                Consulte les tournois disponibles, vérifie la capacité restante et inscris ton équipe en quelques minutes.
                            </flux:text>
                        </div>
                    </div>

                    <flux:card class="grid gap-4 sm:grid-cols-3 lg:grid-cols-1">
                        <div class="flex items-center justify-between gap-4">
                            <flux:text>Tournois</flux:text>
                            <span class="text-2xl font-semibold tabular-nums">{{ $feed->tournamentCount() }}</span>
                        </div>

                        <flux:separator class="max-sm:hidden lg:block" />

                        <div class="flex items-center justify-between gap-4">
                            <flux:text>Ouverts</flux:text>
                            <span class="text-2xl font-semibold tabular-nums text-emerald-600 dark:text-emerald-300">
                                {{ $feed->openCount }}
                            </span>
                        </div>

                        <flux:separator class="max-sm:hidden lg:block" />

                        <div class="flex items-center justify-between gap-4">
                            <flux:text>Inscriptions</flux:text>
                            <span class="text-2xl font-semibold tabular-nums">{{ $feed->registrationsCount }}</span>
                        </div>
                    </flux:card>
                </section>

                @if ($feed->isEmpty())
                    <flux:callout
                        color="zinc"
                        icon="trophy"
                        heading="Aucun tournoi disponible"
                        text="Crée un tournoi depuis le panel Filament pour alimenter cette page."
                    />
                @else
                    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                        @foreach ($feed->cards as $card)
                            <flux:card wire:key="tournament-{{ $card->id }}" class="flex min-h-80 flex-col justify-between gap-6 transition hover:-translate-y-0.5 hover:border-emerald-300 hover:shadow-lg dark:hover:border-emerald-500/40">
                                <div class="space-y-5">
                                    <div class="flex items-start justify-between gap-4">
                                        <flux:badge :color="$card->statusColor">{{ $card->statusLabel }}</flux:badge>

                                        <span class="text-sm font-medium tabular-nums text-zinc-500 dark:text-zinc-400">
                                            {{ $card->startsAt?->format('d/m/Y') ?? 'À venir' }}
                                        </span>
                                    </div>

                                    <div class="space-y-3">
                                        <flux:heading size="lg" level="2">{{ $card->name }}</flux:heading>
                                        <flux:text class="line-clamp-3 leading-6">
                                            {{ $card->description }}
                                        </flux:text>
                                    </div>
                                </div>

                                <div class="space-y-5">
                                    <div class="grid grid-cols-3 gap-2">
                                        <div class="rounded-lg bg-zinc-100 p-3 dark:bg-white/10">
                                            <div class="text-lg font-semibold tabular-nums">{{ $card->remainingCapacity }}</div>
                                            <flux:text size="sm">places</flux:text>
                                        </div>
                                        <div class="rounded-lg bg-zinc-100 p-3 dark:bg-white/10">
                                            <div class="text-lg font-semibold tabular-nums">{{ $card->teamMinSize }}-{{ $card->teamMaxSize }}</div>
                                            <flux:text size="sm">équipe</flux:text>
                                        </div>
                                        <div class="rounded-lg bg-zinc-100 p-3 dark:bg-white/10">
                                            <div class="text-lg font-semibold tabular-nums">{{ $card->registrationsCount }}</div>
                                            <flux:text size="sm">inscrites</flux:text>
                                        </div>
                                    </div>

                                    <flux:button
                                        href="{{ $card->registrationUrl }}"
                                        variant="{{ $card->callToActionVariant }}"
                                        class="w-full"
                                        wire:navigate
                                    >
                                        {{ $card->callToActionLabel }}
                                    </flux:button>
                                </div>
                            </flux:card>
                        @endforeach
                    </section>
                @endif
            </flux:main>
        </div>

        @fluxScripts
    </body>
</html>
