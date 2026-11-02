<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Workout;
use App\Support\MemberPlanAssigner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_sees_private_dashboard(): void
    {
        $member = User::factory()->member()->create(['name' => 'Marie Dupont']);

        $this->actingAs($member)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Bonjour Marie')
            ->assertSee('Accueil')
            ->assertSee('Mes programmes')
            ->assertSee('Bilans')
            ->assertSee('Profil')
            ->assertSee('Semaine')
            ->assertSee('Progrès')
            ->assertSee('Ton coach ne t’a pas encore donné de séance')
            ->assertDontSee('Donner')
            ->assertDontSee('← Inscrits');
    }

    public function test_member_sees_a_workout_given_by_the_coach(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->member()->create(['name' => 'Marie Dupont']);
        $workout = Workout::factory()->create(['title' => 'Pompes & abdos']);
        app(MemberPlanAssigner::class)->assignWorkout($member, $admin, $workout, [now()->toDateString()]);

        $this->actingAs($member)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Pompes & abdos')
            ->assertSee('À faire aujourd’hui')
            ->assertSee('Débuter ma séance');

        $this->actingAs($member)
            ->get(route('dashboard', ['vue' => 'semaine']))
            ->assertOk()
            ->assertSee('Pompes & abdos')
            ->assertSee('À faire');
    }
}
