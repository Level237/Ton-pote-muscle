<?php

namespace Database\Factories;

use App\Enums\ProgramStatus;
use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Program>
 */
class ProgramFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->unique()->words(3, true),
            'starts_on' => now()->subDays(2)->toDateString(),
            'ends_on' => now()->addWeeks(2)->toDateString(),
            'status' => ProgramStatus::Open,
        ];
    }

    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ProgramStatus::Open,
            'starts_on' => now()->subDay()->toDateString(),
            'ends_on' => now()->addWeek()->toDateString(),
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ProgramStatus::Draft,
        ]);
    }
}
