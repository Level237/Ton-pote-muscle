<?php

namespace Database\Factories;

use App\Models\Bilan;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Bilan>
 */
class BilanFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->unique()->words(3, true),
            'slug' => Str::slug(fake()->unique()->words(3, true)),
            'description' => fake()->optional()->sentence(),
        ];
    }
}
