<?php

namespace Database\Factories;

use App\Enums\TournamentStatus;
use App\Models\Tournament;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Tournament>
 */
class TournamentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'name' => Str::title($name),
            'slug' => Str::slug($name),
            'description' => fake()->paragraph(),
            'starts_at' => fake()->dateTimeBetween('+1 week', '+2 months'),
            'status' => TournamentStatus::Draft,
            'capacity' => fake()->numberBetween(8, 32),
            'team_min_size' => 1,
            'team_max_size' => fake()->numberBetween(2, 5),
        ];
    }

    public function open(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TournamentStatus::Open,
        ]);
    }

    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TournamentStatus::Closed,
        ]);
    }
}
