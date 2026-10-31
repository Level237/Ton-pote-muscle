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
        User::factory()->admin()->create([
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@tonpotemuscle.test',
            'password' => 'password',
            'role' => UserRole::Admin,
        ]);
    }
}
