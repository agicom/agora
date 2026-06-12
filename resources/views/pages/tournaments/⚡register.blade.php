<?php

use App\Actions\Tournaments\RegisterPublicTeamForTournament;
use App\Exceptions\RegistrationNotAllowed;
use App\Models\Tournament;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

new #[Layout('layouts.public')] #[Title('Inscription tournoi')] class extends Component {
    public Tournament $tournament;

    #[Validate]
    public string $teamName = '';

    #[Validate]
    public array $members = [
        ['name' => '', 'email' => ''],
    ];

    public bool $registered = false;

    public function mount(Tournament $tournament): void
    {
        $this->tournament = $tournament;

        $user = Auth::user();

        if ($user !== null && ! $user->isAdmin()) {
            $this->members[0] = [
                'name' => $user->name,
                'email' => $user->email,
            ];
        }
    }

    public function addMember(): void
    {
        if (count($this->members) >= $this->tournament->team_max_size) {
            return;
        }

        $this->members[] = ['name' => '', 'email' => ''];
    }

    public function removeMember(int $index): void
    {
        if (count($this->members) <= 1) {
            return;
        }

        unset($this->members[$index]);
        $this->members = array_values($this->members);
    }

    public function submit(RegisterPublicTeamForTournament $registerPublicTeamForTournament): void
    {
        $validated = $this->validate();

        try {
            $registerPublicTeamForTournament(
                $this->tournament,
                $validated['teamName'],
                $validated['members'],
            );
        } catch (RegistrationNotAllowed $exception) {
            throw ValidationException::withMessages([
                'teamName' => $exception->getMessage(),
            ]);
        }

        $this->registered = true;
        $this->tournament->refresh();

        Flux::toast(variant: 'success', text: __('Inscription envoyée.'));
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'teamName' => ['required', 'string', 'max:255'],
            'members' => ['required', 'array', 'min:'.$this->tournament->team_min_size, 'max:'.$this->tournament->team_max_size],
            'members.*.name' => ['required', 'string', 'max:255'],
            'members.*.email' => ['required', 'email', 'max:255', 'distinct:ignore_case'],
        ];
    }

    public function getRemainingCapacityProperty(): int
    {
        return $this->tournament->remainingCapacity();
    }

    public function getIsFullProperty(): bool
    {
        return $this->tournament->isFull();
    }
};
?>

<div class="min-h-screen bg-[#f6f3ea] text-zinc-950 dark:bg-zinc-950 dark:text-white">
    <main class="mx-auto grid min-h-screen w-full max-w-7xl grid-cols-1 gap-5 px-4 py-4 sm:px-6 lg:grid-cols-[0.88fr_1.12fr] lg:px-8 lg:py-8">
        <section class="relative overflow-hidden rounded-lg bg-zinc-950 p-5 text-white shadow-xl lg:sticky lg:top-8 lg:min-h-[calc(100vh-4rem)] lg:p-7">
            <div class="absolute inset-x-0 top-0 h-48 bg-[radial-gradient(circle_at_30%_0%,rgba(16,185,129,0.45),transparent_28rem)]"></div>
            <div class="absolute -right-24 bottom-10 size-72 rounded-full border border-white/10"></div>
            <div class="absolute -right-12 bottom-24 size-36 rounded-full border border-emerald-300/20"></div>

            <div class="relative flex h-full flex-col justify-between gap-10">
                <div class="space-y-9">
                    <div class="flex items-center justify-between gap-4">
                        <a href="{{ route('home') }}" class="flex items-center gap-3 rounded-lg outline-none transition hover:opacity-80 focus-visible:ring-2 focus-visible:ring-emerald-300" wire:navigate>
                            <span class="grid size-10 place-items-center rounded-lg bg-white text-zinc-950">
                                <x-app-logo-icon class="size-6 fill-current" />
                            </span>
                            <span>
                                <span class="block text-sm font-black uppercase tracking-[0.18em] text-emerald-300">Agora</span>
                                <span class="block text-xs text-zinc-400">Inscription publique</span>
                            </span>
                        </a>

                        @if ($this->isFull)
                            <flux:badge color="red">Complet</flux:badge>
                        @elseif ($tournament->isOpen())
                            <flux:badge color="green">Ouvert</flux:badge>
                        @else
                            <flux:badge color="zinc">{{ $tournament->status->getLabel() }}</flux:badge>
                        @endif
                    </div>

                    <div class="space-y-5">
                        <div class="inline-flex items-center gap-2 rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-xs font-bold uppercase tracking-[0.18em] text-emerald-200">
                            <span class="size-2 rounded-full bg-emerald-300"></span>
                            Tournoi LAN
                        </div>

                        <div class="space-y-4">
                            <h1 class="text-5xl font-black leading-[0.95] tracking-normal sm:text-6xl">{{ $tournament->name }}</h1>

                            @if ($tournament->description)
                                <p class="max-w-xl text-base leading-7 text-zinc-300">{{ $tournament->description }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="relative space-y-5">
                    <dl class="grid grid-cols-3 gap-2">
                        <div class="rounded-lg bg-white/10 p-4">
                            <dt class="text-xs font-medium text-zinc-400">Places</dt>
                            <dd class="mt-1 text-3xl font-black">{{ $this->remainingCapacity }}</dd>
                        </div>
                        <div class="rounded-lg bg-white/10 p-4">
                            <dt class="text-xs font-medium text-zinc-400">Équipe</dt>
                            <dd class="mt-1 text-3xl font-black">{{ $tournament->team_min_size }}-{{ $tournament->team_max_size }}</dd>
                        </div>
                        <div class="rounded-lg bg-white/10 p-4">
                            <dt class="text-xs font-medium text-zinc-400">Début</dt>
                            <dd class="mt-2 text-sm font-black leading-tight">
                                {{ $tournament->starts_at?->format('d/m/Y') ?? 'À venir' }}
                            </dd>
                        </div>
                    </dl>

                    <div x-data="{ copied: false }" class="rounded-lg border border-white/10 bg-white/5 p-3">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <p class="text-sm text-zinc-300">{{ route('tournaments.registrations.create', $tournament) }}</p>
                            <flux:button
                                type="button"
                                variant="filled"
                                size="sm"
                                x-on:click="navigator.clipboard.writeText('{{ route('tournaments.registrations.create', $tournament) }}'); copied = true; setTimeout(() => copied = false, 1600)"
                            >
                                <span x-show="! copied">Copier le lien</span>
                                <span x-cloak x-show="copied">Lien copié</span>
                            </flux:button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="flex items-center">
            <div class="w-full rounded-lg border border-zinc-950/10 bg-white p-5 shadow-xl dark:border-white/10 dark:bg-zinc-900 sm:p-7">
                @if ($registered)
                    <div class="grid gap-7 lg:grid-cols-[0.8fr_1.2fr] lg:items-center">
                        <div class="grid aspect-square max-h-72 place-items-center rounded-lg bg-emerald-500 text-white">
                            <div class="text-center">
                                <div class="text-7xl font-black">OK</div>
                                <div class="mt-2 text-sm font-bold uppercase tracking-[0.18em]">Inscrite</div>
                            </div>
                        </div>
                        <div class="space-y-5">
                            <flux:badge color="green">Inscription confirmée</flux:badge>
                            <flux:heading size="xl">Équipe inscrite</flux:heading>
                            <flux:text>
                                Votre équipe est bien inscrite au tournoi. Conservez cette page ou partagez le lien du tournoi avec les autres membres.
                            </flux:text>
                            <flux:button href="{{ route('home') }}" variant="primary" wire:navigate>Voir les autres tournois</flux:button>
                        </div>
                    </div>
                @elseif ($this->isFull || ! $tournament->isOpen())
                    <div class="space-y-6">
                        <div class="flex items-start justify-between gap-4">
                            <div class="space-y-3">
                                <flux:badge color="red">{{ $this->isFull ? 'Complet' : 'Inscriptions closes' }}</flux:badge>
                                <flux:heading size="xl">Les inscriptions ne sont pas disponibles</flux:heading>
                            </div>
                        </div>

                        <div class="rounded-lg bg-zinc-100 p-5 dark:bg-white/10">
                            <flux:text>
                                Ce tournoi reste consultable, mais aucune nouvelle équipe ne peut être inscrite pour le moment.
                            </flux:text>
                        </div>

                        <flux:button href="{{ route('home') }}" variant="primary" wire:navigate>Retour aux tournois</flux:button>
                    </div>
                @else
                    <form wire:submit="submit" class="space-y-8">
                        <div class="flex flex-col gap-5 border-b border-zinc-200 pb-6 dark:border-white/10 lg:flex-row lg:items-end lg:justify-between">
                            <div class="space-y-2">
                                <flux:badge color="green">Étape 1</flux:badge>
                                <flux:heading size="xl">Inscrire une équipe</flux:heading>
                                <flux:text>Le premier membre devient capitaine et contact principal.</flux:text>
                            </div>

                            <div class="rounded-lg bg-zinc-950 px-4 py-3 text-sm font-bold text-white dark:bg-white dark:text-zinc-950">
                                {{ count($members) }} / {{ $tournament->team_max_size }} membres
                            </div>
                        </div>

                        <flux:input
                            wire:model.live.blur="teamName"
                            :label="__('Nom de l’équipe')"
                            type="text"
                            autocomplete="organization"
                            placeholder="Ex. LAN Rangers"
                            required
                        />

                        <div class="space-y-4">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <flux:heading size="lg">Membres</flux:heading>
                                    <flux:text>Minimum {{ $tournament->team_min_size }}, maximum {{ $tournament->team_max_size }}.</flux:text>
                                </div>

                                <flux:button
                                    type="button"
                                    variant="filled"
                                    wire:click="addMember"
                                    :disabled="count($members) >= $tournament->team_max_size"
                                >
                                    Ajouter un membre
                                </flux:button>
                            </div>

                            @error('members')
                                <flux:error name="members" />
                            @enderror

                            <div class="grid gap-3">
                                @foreach ($members as $index => $member)
                                    <div wire:key="member-{{ $index }}" class="rounded-lg border border-zinc-200 bg-zinc-50 p-4 transition dark:border-white/10 dark:bg-white/5">
                                        <div class="mb-4 flex items-center justify-between gap-3">
                                            <flux:badge :color="$index === 0 ? 'green' : 'zinc'">
                                                {{ $index === 0 ? 'Capitaine' : 'Membre '.($index + 1) }}
                                            </flux:badge>

                                            @if ($index > 0)
                                                <flux:button type="button" variant="subtle" size="sm" wire:click="removeMember({{ $index }})">
                                                    Retirer
                                                </flux:button>
                                            @endif
                                        </div>

                                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                            <flux:input
                                                wire:model.live.blur="members.{{ $index }}.name"
                                                :label="__('Pseudo')"
                                                type="text"
                                                autocomplete="name"
                                                placeholder="Nina"
                                                required
                                            />

                                            <flux:input
                                                wire:model.live.blur="members.{{ $index }}.email"
                                                :label="__('Email')"
                                                type="email"
                                                autocomplete="email"
                                                placeholder="nina@example.com"
                                                required
                                            />
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex flex-col gap-4 rounded-lg bg-zinc-100 p-4 dark:bg-white/10 sm:flex-row sm:items-center sm:justify-between">
                            <flux:text class="text-sm">
                                Les emails existants seront réutilisés si ce sont des utilisateurs publics.
                            </flux:text>

                            <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                                <span wire:loading.remove>Confirmer l’inscription</span>
                                <span wire:loading>Inscription...</span>
                            </flux:button>
                        </div>
                    </form>
                @endif
            </div>
        </section>
    </main>
</div>
