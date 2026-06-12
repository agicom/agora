<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <?php $title = 'Tournois'; ?>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-[#f6f3ea] text-zinc-950 antialiased dark:bg-zinc-950 dark:text-white">
        <div class="min-h-screen bg-[radial-gradient(circle_at_top_left,rgba(16,185,129,0.18),transparent_34rem),linear-gradient(135deg,rgba(24,24,27,0.05)_25%,transparent_25%),linear-gradient(225deg,rgba(24,24,27,0.05)_25%,transparent_25%)] bg-[length:auto,32px_32px,32px_32px] dark:bg-[radial-gradient(circle_at_top_left,rgba(16,185,129,0.18),transparent_32rem)]">
            <header class="mx-auto flex w-full max-w-7xl items-center justify-between gap-4 px-4 py-5 sm:px-6 lg:px-8">
                <a href="{{ route('home') }}" class="flex items-center gap-3" wire:navigate>
                    <span class="grid size-10 place-items-center rounded-lg bg-zinc-950 text-white shadow-sm dark:bg-white dark:text-zinc-950">
                        <x-app-logo-icon class="size-6 fill-current" />
                    </span>
                    <span>
                        <span class="block text-sm font-black uppercase tracking-[0.18em] text-emerald-700 dark:text-emerald-300">Agora</span>
                        <span class="block text-xs font-medium text-zinc-600 dark:text-zinc-400">Tournois LAN</span>
                    </span>
                </a>

                <div class="flex items-center gap-2">
                    <flux:button href="{{ url('/admin') }}" variant="filled" size="sm">Admin</flux:button>
                </div>
            </header>

            <main class="mx-auto w-full max-w-7xl px-4 pb-12 sm:px-6 lg:px-8">
                <section class="grid gap-6 py-8 lg:grid-cols-[0.9fr_1.1fr] lg:items-end lg:py-14">
                    <div class="space-y-6">
                        <flux:badge color="green">Inscriptions publiques</flux:badge>

                        <div class="space-y-4">
                            <h1 class="max-w-3xl text-5xl font-black leading-[0.95] tracking-normal text-zinc-950 sm:text-6xl lg:text-7xl dark:text-white">
                                Choisis ton tournoi, assemble ton équipe.
                            </h1>
                            <p class="max-w-2xl text-lg leading-8 text-zinc-700 dark:text-zinc-300">
                                Les tournois ouverts acceptent les inscriptions tant qu'il reste de la capacité. Les tournois complets ou clos restent visibles pour tester les états du parcours.
                            </p>
                        </div>
                    </div>

                    <div class="grid gap-3 rounded-lg border border-zinc-950/10 bg-white/80 p-4 shadow-sm backdrop-blur dark:border-white/10 dark:bg-white/5 sm:grid-cols-3">
                        <div class="rounded-lg bg-zinc-950 p-4 text-white dark:bg-white dark:text-zinc-950">
                            <div class="text-3xl font-black">{{ $tournaments->count() }}</div>
                            <div class="mt-1 text-sm font-medium opacity-75">tournois</div>
                        </div>
                        <div class="rounded-lg bg-emerald-600 p-4 text-white">
                            <div class="text-3xl font-black">{{ $tournaments->where('status', \App\Enums\TournamentStatus::Open)->count() }}</div>
                            <div class="mt-1 text-sm font-medium opacity-80">ouverts</div>
                        </div>
                        <div class="rounded-lg bg-amber-300 p-4 text-zinc-950">
                            <div class="text-3xl font-black">{{ $tournaments->sum('registrations_count') }}</div>
                            <div class="mt-1 text-sm font-medium opacity-70">inscriptions</div>
                        </div>
                    </div>
                </section>

                <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    @if ($tournaments->isEmpty())
                        <div class="rounded-lg border border-dashed border-zinc-400 bg-white/70 p-8 text-center dark:border-zinc-700 dark:bg-zinc-900">
                            <flux:heading size="xl">Aucun tournoi disponible</flux:heading>
                            <flux:text class="mt-2">Crée un tournoi depuis le panel Filament pour alimenter cette page.</flux:text>
                        </div>
                    @endif

                    @foreach ($tournaments as $tournament)
                        @php
                            $remainingCapacity = max(0, $tournament->capacity - $tournament->registrations_count);
                            $isFull = $remainingCapacity === 0;
                            $isOpen = $tournament->isOpen();
                        @endphp

                        <article class="group flex min-h-80 flex-col justify-between rounded-lg border border-zinc-950/10 bg-white p-5 shadow-sm transition duration-200 hover:-translate-y-1 hover:border-zinc-950/30 hover:shadow-xl dark:border-white/10 dark:bg-zinc-900 dark:hover:border-white/25">
                            <div class="space-y-5">
                                <div class="flex items-start justify-between gap-4">
                                    @if ($isFull)
                                        <flux:badge color="red">Complet</flux:badge>
                                    @elseif ($isOpen)
                                        <flux:badge color="green">Ouvert</flux:badge>
                                    @else
                                        <flux:badge color="zinc">{{ $tournament->status->getLabel() }}</flux:badge>
                                    @endif

                                    <span class="text-right text-xs font-bold uppercase tracking-[0.18em] text-zinc-500 dark:text-zinc-400">
                                        {{ $tournament->starts_at?->format('d/m') ?? 'À venir' }}
                                    </span>
                                </div>

                                <div class="space-y-3">
                                    <h2 class="text-2xl font-black tracking-normal">{{ $tournament->name }}</h2>
                                    <p class="line-clamp-3 text-sm leading-6 text-zinc-600 dark:text-zinc-300">
                                        {{ $tournament->description ?: 'Aucune description pour le moment.' }}
                                    </p>
                                </div>
                            </div>

                            <div class="mt-8 space-y-5">
                                <div class="grid grid-cols-3 gap-2 text-sm">
                                    <div class="rounded-lg bg-zinc-100 p-3 dark:bg-white/10">
                                        <div class="font-black">{{ $remainingCapacity }}</div>
                                        <div class="text-xs text-zinc-500 dark:text-zinc-400">places</div>
                                    </div>
                                    <div class="rounded-lg bg-zinc-100 p-3 dark:bg-white/10">
                                        <div class="font-black">{{ $tournament->team_min_size }}-{{ $tournament->team_max_size }}</div>
                                        <div class="text-xs text-zinc-500 dark:text-zinc-400">équipe</div>
                                    </div>
                                    <div class="rounded-lg bg-zinc-100 p-3 dark:bg-white/10">
                                        <div class="font-black">{{ $tournament->registrations_count }}</div>
                                        <div class="text-xs text-zinc-500 dark:text-zinc-400">inscrites</div>
                                    </div>
                                </div>

                                <flux:button
                                    href="{{ route('tournaments.registrations.create', $tournament) }}"
                                    variant="{{ $isOpen && ! $isFull ? 'primary' : 'filled' }}"
                                    class="w-full"
                                    wire:navigate
                                >
                                    {{ $isOpen && ! $isFull ? 'Inscrire une équipe' : 'Voir le tournoi' }}
                                </flux:button>
                            </div>
                        </article>
                    @endforeach
                </section>
            </main>
        </div>

        @fluxScripts
    </body>
</html>
