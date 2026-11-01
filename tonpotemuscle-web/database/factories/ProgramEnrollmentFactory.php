<?php

namespace Database\Factories;

use App\Enums\EnrollmentStatus;
use App\Models\Program;
use App\Models\ProgramEnrollment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProgramEnrollment>
 */
class ProgramEnrollmentFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->member(),
            'program_id' => Program::factory(),
            'status' => EnrollmentStatus::Active,
            'points' => 0,
            'live_count' => 0,
            'enrolled_at' => now(),
        ];
    }

    public function abandoned(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => EnrollmentStatus::Abandoned,
            'abandoned_at' => now(),
        ]);
    }
}
