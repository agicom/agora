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

        Flux::toast(variant: 'success', text: __('Inscription envoyee.'));
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

<div class="min-h-screen bg-zinc-50 dark:bg-zinc-950">
    <main class="mx-auto grid min-h-screen w-full max-w-6xl grid-cols-1 gap-8 px-4 py-6 sm:px-6 lg:grid-cols-[0.9fr_1.1fr] lg:px-8 lg:py-10">
        <section class="flex flex-col justify-between rounded-lg bg-zinc-950 p-6 text-white shadow-sm dark:bg-zinc-900">
            <div class="space-y-8">
                <div class="flex items-center justify-between gap-4">
                    <a href="{{ route('home') }}" class="flex items-center gap-3" wire:navigate>
                        <x-app-logo-icon class="size-8 fill-current text-white" />
                        <span class="font-semibold">{{ config('app.name', 'Agora') }}</span>
                    </a>

                    @if ($this->isFull)
                        <flux:badge color="red">{{ __('Complet') }}</flux:badge>
                    @elseif ($tournament->isOpen())
                        <flux:badge color="green">{{ __('Ouvert') }}</flux:badge>
                    @else
                        <flux:badge color="zinc">{{ __('Clos') }}</flux:badge>
                    @endif
                </div>

                <div class="space-y-4">
                    <p class="text-sm font-medium text-emerald-300">{{ __('Inscription publique') }}</p>
                    <h1 class="text-3xl font-semibold tracking-normal sm:text-4xl">{{ $tournament->name }}</h1>

                    @if ($tournament->description)
                        <p class="max-w-xl text-base leading-7 text-zinc-300">{{ $tournament->description }}</p>
                    @endif
                </div>
            </div>

            <dl class="mt-10 grid grid-cols-1 gap-3 sm:grid-cols-3 lg:grid-cols-1 xl:grid-cols-3">
                <div class="rounded-lg bg-white/10 p-4">
                    <dt class="text-sm text-zinc-300">{{ __('Places restantes') }}</dt>
                    <dd class="mt-1 text-2xl font-semibold">{{ $this->remainingCapacity }}</dd>
                </div>
                <div class="rounded-lg bg-white/10 p-4">
                    <dt class="text-sm text-zinc-300">{{ __('Taille equipe') }}</dt>
                    <dd class="mt-1 text-2xl font-semibold">{{ $tournament->team_min_size }}-{{ $tournament->team_max_size }}</dd>
                </div>
                <div class="rounded-lg bg-white/10 p-4">
                    <dt class="text-sm text-zinc-300">{{ __('Debut') }}</dt>
                    <dd class="mt-1 text-lg font-semibold">
                        {{ $tournament->starts_at?->format('d/m/Y') ?? __('A venir') }}
                    </dd>
                </div>
            </dl>
        </section>

        <section class="flex items-center">
            <div class="w-full rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                @if ($registered)
                    <div class="space-y-4">
                        <flux:badge color="green">{{ __('Inscription confirmee') }}</flux:badge>
                        <flux:heading size="xl">{{ __('Equipe inscrite') }}</flux:heading>
                        <flux:text>
                            {{ __('Votre equipe est bien inscrite au tournoi. Vous pouvez conserver cette page comme confirmation.') }}
                        </flux:text>
                    </div>
                @elseif ($this->isFull || ! $tournament->isOpen())
                    <div class="space-y-4">
                        <flux:badge color="red">{{ $this->isFull ? __('Complet') : __('Inscriptions closes') }}</flux:badge>
                        <flux:heading size="xl">{{ __('Les inscriptions ne sont pas disponibles') }}</flux:heading>
                        <flux:text>
                            {{ __('Ce tournoi reste consultable, mais aucune nouvelle equipe ne peut etre inscrite pour le moment.') }}
                        </flux:text>
                    </div>
                @else
                    <form wire:submit="submit" class="space-y-6">
                        <div class="space-y-1">
                            <flux:heading size="xl">{{ __('Inscrire une equipe') }}</flux:heading>
                            <flux:text>{{ __('Le premier membre est le capitaine et servira de contact principal.') }}</flux:text>
                        </div>

                        <flux:input
                            wire:model.live.blur="teamName"
                            :label="__('Nom de l equipe')"
                            type="text"
                            autocomplete="organization"
                            required
                        />

                        <div class="space-y-4">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <flux:heading size="lg">{{ __('Membres') }}</flux:heading>
                                    <flux:text>{{ count($members) }} / {{ $tournament->team_max_size }}</flux:text>
                                </div>

                                <flux:button
                                    type="button"
                                    variant="filled"
                                    wire:click="addMember"
                                    :disabled="count($members) >= $tournament->team_max_size"
                                >
                                    {{ __('Ajouter') }}
                                </flux:button>
                            </div>

                            @error('members')
                                <flux:error name="members" />
                            @enderror

                            <div class="space-y-3">
                                @foreach ($members as $index => $member)
                                    <div wire:key="member-{{ $index }}" class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-800">
                                        <div class="mb-4 flex items-center justify-between gap-3">
                                            <flux:badge :color="$index === 0 ? 'green' : 'zinc'">
                                                {{ $index === 0 ? __('Capitaine') : __('Membre :number', ['number' => $index + 1]) }}
                                            </flux:badge>

                                            @if ($index > 0)
                                                <flux:button type="button" variant="subtle" size="sm" wire:click="removeMember({{ $index }})">
                                                    {{ __('Retirer') }}
                                                </flux:button>
                                            @endif
                                        </div>

                                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                            <flux:input
                                                wire:model.live.blur="members.{{ $index }}.name"
                                                :label="__('Pseudo')"
                                                type="text"
                                                autocomplete="name"
                                                required
                                            />

                                            <flux:input
                                                wire:model.live.blur="members.{{ $index }}.email"
                                                :label="__('Email')"
                                                type="email"
                                                autocomplete="email"
                                                required
                                            />
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <flux:text class="text-sm">
                                {{ __('Les emails existants seront reutilises si ce sont des utilisateurs publics.') }}
                            </flux:text>

                            <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                                <span wire:loading.remove>{{ __('Confirmer l inscription') }}</span>
                                <span wire:loading>{{ __('Inscription...') }}</span>
                            </flux:button>
                        </div>
                    </form>
                @endif
            </div>
        </section>
    </main>
</div>
