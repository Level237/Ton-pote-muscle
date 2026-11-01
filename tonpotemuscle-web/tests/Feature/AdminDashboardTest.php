<?php

namespace Tests\Feature;

use App\Models\Bilan;
use App\Models\Exercise;
use App\Models\Program;
use App\Models\User;
use App\Models\Workout;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_shows_stats_and_recent_lists(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->member()->create(['username' => 'marie', 'name' => 'Marie']);
        Exercise::factory()->create(['title' => 'Pompes']);
        Workout::factory()->create(['title' => 'Full body', 'total_duration_seconds' => 1800]);
        Program::factory()->inProgress()->create(['title' => '4 semaines']);
        $bilan = Bilan::factory()->create(['title' => 'Bilan de santé']);
        $bilan->questions()->create([
            'position' => 1,
            'title' => 'Objectif principal',
            'type' => 'choice',
            'options' => ['Perte de poids', 'Prise de muscle'],
        ]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Home')
            ->assertSee('Profil')
            ->assertSee('Membres')
            ->assertSee('Exercices')
            ->assertSee('Programmes')
            ->assertSee('Plus')
            ->assertSee('Programmes en cours')
            ->assertSee('Déconnexion')
            ->assertSee('Programmes récents')
            ->assertSee('Séances récentes')
            ->assertSee('Membres récents')
            ->assertSee('Bilans')
            ->assertSee('Bilan de santé')
            ->assertSee('Full body')
            ->assertSee('30 min')
            ->assertSee('4 semaines')
            ->assertSee('Ouvert')
            ->assertSee('marie');
    }

    public function test_admin_dashboard_shows_empty_states_when_no_data(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Aucun programme.')
            ->assertSee('Aucune séance.')
            ->assertSee('Aucun membre.')
            ->assertSee('Aucun bilan.');
    }
}
