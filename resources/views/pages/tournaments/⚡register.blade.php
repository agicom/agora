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

<div class="min-h-screen overflow-x-hidden bg-zinc-50 text-zinc-950 dark:bg-zinc-950 dark:text-white">
    <flux:header container class="border-b border-zinc-200/80 bg-white/85 backdrop-blur dark:border-white/10 dark:bg-zinc-950/80">
        <flux:button href="{{ route('home') }}" variant="ghost" size="sm" icon="arrow-left" wire:navigate>
            Tournois
        </flux:button>

        <flux:spacer />

        @if ($this->isFull)
            <flux:badge color="red">Complet</flux:badge>
        @elseif ($tournament->isOpen())
            <flux:badge color="green">Ouvert</flux:badge>
        @else
            <flux:badge color="zinc">{{ $tournament->status->getLabel() }}</flux:badge>
        @endif
    </flux:header>

    <flux:main container class="grid gap-6 !px-4 py-6 sm:!px-6 lg:grid-cols-[24rem_1fr] lg:py-10">
        <aside class="space-y-4 lg:sticky lg:top-6 lg:self-start">
            <flux:card class="space-y-6">
                <div class="space-y-4">
                    <flux:badge color="green">Inscription publique</flux:badge>

                    <div class="space-y-3">
                        <flux:heading size="xl" level="1">{{ $tournament->name }}</flux:heading>

                        @if ($tournament->description)
                            <flux:text class="leading-7">{{ $tournament->description }}</flux:text>
                        @else
                            <flux:text>Les détails du tournoi seront complétés par l'organisation.</flux:text>
                        @endif
                    </div>
                </div>

                <flux:separator />

                <dl class="grid gap-3">
                    <div class="flex items-center justify-between gap-4">
                        <dt class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400">
                            <flux:icon name="trophy" variant="mini" class="size-4" />
                            Places restantes
                        </dt>
                        <dd class="font-semibold tabular-nums">{{ $this->remainingCapacity }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-4">
                        <dt class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400">
                            <flux:icon name="user-group" variant="mini" class="size-4" />
                            Taille équipe
                        </dt>
                        <dd class="font-semibold tabular-nums">{{ $tournament->team_min_size }}-{{ $tournament->team_max_size }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-4">
                        <dt class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400">
                            <flux:icon name="calendar-days" variant="mini" class="size-4" />
                            Début
                        </dt>
                        <dd class="font-semibold tabular-nums">{{ $tournament->starts_at?->format('d/m/Y') ?? 'À venir' }}</dd>
                    </div>
                </dl>
            </flux:card>

            <flux:card size="sm" x-data="{ copied: false }" class="space-y-3">
                <div class="flex items-start gap-3">
                    <flux:icon name="link" variant="mini" class="mt-1 size-4 text-zinc-400" />
                    <div class="min-w-0 flex-1">
                        <flux:text size="sm" class="truncate">{{ route('tournaments.registrations.create', $tournament) }}</flux:text>
                    </div>
                </div>

                <flux:button
                    type="button"
                    variant="filled"
                    size="sm"
                    class="w-full"
                    x-on:click="navigator.clipboard.writeText('{{ route('tournaments.registrations.create', $tournament) }}'); copied = true; setTimeout(() => copied = false, 1600)"
                >
                    <span x-show="! copied">Copier le lien</span>
                    <span x-cloak x-show="copied">Lien copié</span>
                </flux:button>
            </flux:card>
        </aside>

        <section>
            @if ($registered)
                <flux:card class="space-y-6">
                    <flux:callout
                        color="green"
                        icon="check-circle"
                        heading="Équipe inscrite"
                        text="Votre équipe est bien inscrite au tournoi. Le premier membre renseigné est le capitaine et contact principal."
                    />

                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">
                        <flux:button href="{{ route('home') }}" variant="primary" wire:navigate>Voir les autres tournois</flux:button>
                    </div>
                </flux:card>
            @elseif ($this->isFull || ! $tournament->isOpen())
                <flux:card class="space-y-6">
                    <flux:callout
                        :color="$this->isFull ? 'red' : 'zinc'"
                        icon="x-circle"
                        :heading="$this->isFull ? 'Tournoi complet' : 'Inscriptions closes'"
                        text="Les inscriptions ne sont pas disponibles. Ce tournoi reste consultable, mais aucune nouvelle équipe ne peut être inscrite pour le moment."
                    />

                    <flux:button href="{{ route('home') }}" variant="primary" wire:navigate>Retour aux tournois</flux:button>
                </flux:card>
            @else
                <flux:card class="space-y-8">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                        <div class="space-y-2">
                            <flux:heading size="xl" level="2">Inscrire une équipe</flux:heading>
                            <flux:text>Renseigne le nom de l'équipe puis ajoute les membres participants.</flux:text>
                        </div>

                        <flux:badge color="zinc">{{ count($members) }} / {{ $tournament->team_max_size }} membres</flux:badge>
                    </div>

                    <flux:separator />

                    <form wire:submit="submit" class="space-y-8">
                        <flux:input
                            wire:model="teamName"
                            :label="__('Nom de l’équipe')"
                            type="text"
                            autocomplete="organization"
                            placeholder="Ex. LAN Rangers"
                            required
                        />

                        <div class="space-y-4">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                                <div class="space-y-1">
                                    <flux:heading size="lg" level="3">Membres</flux:heading>
                                    <flux:text>Minimum {{ $tournament->team_min_size }}, maximum {{ $tournament->team_max_size }}.</flux:text>
                                </div>

                                <flux:button
                                    type="button"
                                    variant="filled"
                                    icon="user-plus"
                                    wire:click="addMember"
                                    :disabled="count($members) >= $tournament->team_max_size"
                                >
                                    Ajouter
                                </flux:button>
                            </div>

                            @error('members')
                                <flux:error name="members" />
                            @enderror

                            <div class="grid gap-3">
                                @foreach ($members as $index => $member)
                                    <flux:card wire:key="member-{{ $index }}" size="sm" class="space-y-4 !bg-zinc-50 dark:!bg-white/5">
                                        <div class="flex items-center justify-between gap-3">
                                            <flux:badge :color="$index === 0 ? 'green' : 'zinc'">
                                                {{ $index === 0 ? 'Capitaine' : 'Membre '.($index + 1) }}
                                            </flux:badge>

                                            @if ($index > 0)
                                                <flux:button type="button" variant="ghost" size="sm" wire:click="removeMember({{ $index }})">
                                                    Retirer
                                                </flux:button>
                                            @endif
                                        </div>

                                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                            <flux:input
                                                wire:model="members.{{ $index }}.name"
                                                :label="__('Pseudo')"
                                                type="text"
                                                autocomplete="name"
                                                placeholder="Nina"
                                                required
                                            />

                                            <flux:input
                                                wire:model="members.{{ $index }}.email"
                                                :label="__('Email')"
                                                type="email"
                                                autocomplete="email"
                                                placeholder="nina@example.com"
                                                required
                                            />
                                        </div>
                                    </flux:card>
                                @endforeach
                            </div>
                        </div>

                        <flux:callout color="zinc" icon="information-circle">
                            <flux:callout.text>
                                Les emails existants seront réutilisés si ce sont des utilisateurs publics.
                            </flux:callout.text>
                        </flux:callout>

                        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-end">
                            <flux:button href="{{ route('home') }}" variant="ghost" wire:navigate>Annuler</flux:button>
                            <flux:button type="submit" variant="primary" wire:target="submit" wire:loading.attr="disabled">
                                <span wire:target="submit" wire:loading.remove>Confirmer l’inscription</span>
                                <span wire:target="submit" wire:loading>Inscription...</span>
                            </flux:button>
                        </div>
                    </form>
                </flux:card>
            @endif
        </section>
    </flux:main>
</div>
