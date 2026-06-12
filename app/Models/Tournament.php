<?php

namespace App\Models;

use App\Enums\TournamentStatus;
use Database\Factories\TournamentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property Carbon|null $starts_at
 * @property TournamentStatus $status
 * @property int $capacity
 * @property int $team_min_size
 * @property int $team_max_size
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'slug', 'description', 'starts_at', 'status', 'capacity', 'team_min_size', 'team_max_size'])]
class Tournament extends Model
{
    /** @use HasFactory<TournamentFactory> */
    use HasFactory;

    /**
     * The model's default values for attributes.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => TournamentStatus::Draft->value,
        'team_min_size' => 1,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'status' => TournamentStatus::class,
            'capacity' => 'integer',
            'team_min_size' => 'integer',
            'team_max_size' => 'integer',
        ];
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    public function isOpen(): bool
    {
        return $this->status === TournamentStatus::Open;
    }

    public function registeredTeamsCount(): int
    {
        return $this->registrations()->count();
    }

    public function remainingCapacity(): int
    {
        return max(0, $this->capacity - $this->registeredTeamsCount());
    }

    public function isFull(): bool
    {
        return $this->remainingCapacity() === 0;
    }
}
