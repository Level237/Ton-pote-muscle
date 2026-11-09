<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::query()->firstOrCreate(
            ['email' => 'arthur@tonpotemuscle.com'],
            [
                'name' => 'Admin',
                'username' => 'admin',
                'password' => 'arthurtonpotemuscle123',
                'role' => UserRole::Admin,
            ],
        );

        $this->call([
            ExerciseSeeder::class,
            WorkoutSeeder::class,
            ProgramSeeder::class,
            BilanSeeder::class,
        ]);
    }
}
