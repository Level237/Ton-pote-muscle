<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WeightLog;
use App\Support\MemberProfile;
use App\Support\MemberWeight;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class MemberWeightLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_weekly_weigh_in(): void
    {
        $member = User::factory()->member()->create();

        $this->actingAs($member)
            ->get(route('dashboard', ['suivi' => 'pese']))
            ->assertOk()
            ->assertSee('Pesée de la semaine')
            ->assertSee('Enregistrer');
    }

    public function test_member_can_save_weekly_weight(): void
    {
        $member = User::factory()->member()->create(['weight_kg' => 70]);

        $this->actingAs($member)
            ->post(route('member.weight.store'), ['weight_kg' => 68.5])
            ->assertRedirect(route('dashboard', ['suivi' => 'pese']).'#suivi');

        $member->refresh();
        $this->assertSame('68.50', (string) $member->weight_kg);
        $this->assertDatabaseHas('weight_logs', [
            'user_id' => $member->id,
            'week_start' => MemberWeight::weekStart()->toDateString(),
            'weight_kg' => '68.50',
        ]);

        $this->actingAs($member)
            ->get(route('dashboard', ['suivi' => 'pese']))
            ->assertOk()
            ->assertSee('68,5 kg')
            ->assertSee('Mettre à jour');
    }

    public function test_saving_weight_twice_in_the_same_week_updates_the_same_log(): void
    {
        $member = User::factory()->member()->create();

        $this->actingAs($member)->post(route('member.weight.store'), ['weight_kg' => 70]);
        $this->actingAs($member)->post(route('member.weight.store'), ['weight_kg' => 69.2]);

        $this->assertSame(1, WeightLog::query()->where('user_id', $member->id)->count());
        $this->assertSame('69.20', (string) $member->fresh()->weight_kg);
    }

    public function test_updating_profile_weight_records_the_week(): void
    {
        $member = User::factory()->member()->create([
            'name' => 'Marie Dupont',
            'username' => 'marie',
            'phone' => '0611223344',
            'weight_kg' => 62.4,
        ]);

        $this->actingAs($member)
            ->put(route('member.profile.update'), [
                'name' => 'Marie Dupont',
                'username' => 'marie',
                'phone' => '0611223344',
                'gender' => 'feminin',
                'height_cm' => 168,
                'weight_kg' => 61.8,
                'main_goal' => 'fitness',
                'fitness_level' => 'regular',
                'activity_level' => 'moderately',
                'motivations' => ['health'],
            ])
            ->assertRedirect(route('member.profile.edit'));

        $this->assertArrayHasKey('fitness', MemberProfile::GOALS);
        $this->assertDatabaseHas('weight_logs', [
            'user_id' => $member->id,
            'weight_kg' => '61.80',
        ]);
    }

    public function test_coach_sees_weigh_ins_but_cannot_submit(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->member()->create();
        MemberWeight::record($member, 74.2, Carbon::parse('2026-11-09'));

        $this->actingAs($admin)
            ->get(route('admin.enrollments.show', ['user' => $member, 'suivi' => 'pese']))
            ->assertOk()
            ->assertSee('Pesée de la semaine')
            ->assertSee('74,2 kg')
            ->assertDontSee('Enregistrer')
            ->assertDontSee('Mettre à jour');
    }
}
