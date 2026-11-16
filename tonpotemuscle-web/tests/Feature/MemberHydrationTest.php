<?php

namespace Tests\Feature;

use App\Models\HydrationLog;
use App\Models\User;
use App\Support\MemberHydration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberHydrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_hydration_card(): void
    {
        $member = User::factory()->member()->create();

        $this->actingAs($member)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Eau aujourd’hui')
            ->assertSee('0')
            ->assertSee('/ 8', false)
            ->assertSee('Ajouter un verre');
    }

    public function test_member_can_add_and_remove_a_glass(): void
    {
        $member = User::factory()->member()->create();

        $this->actingAs($member)
            ->post(route('member.hydration.store'), ['delta' => 1])
            ->assertRedirect(route('dashboard', ['suivi' => 'eau']).'#suivi');

        $this->assertDatabaseHas('hydration_logs', [
            'user_id' => $member->id,
            'day' => now()->toDateString(),
            'glasses' => 1,
        ]);

        $this->actingAs($member)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('0,25 L');

        $this->actingAs($member)
            ->post(route('member.hydration.store'), ['delta' => -1]);

        $this->assertSame(0, (int) HydrationLog::query()->where('user_id', $member->id)->value('glasses'));
    }

    public function test_glasses_cannot_go_below_zero(): void
    {
        $member = User::factory()->member()->create();

        $this->actingAs($member)
            ->post(route('member.hydration.store'), ['delta' => -1])
            ->assertRedirect();

        $this->assertSame(0, (int) HydrationLog::query()->where('user_id', $member->id)->value('glasses'));
    }

    public function test_coach_sees_hydration_and_can_set_goal(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->member()->create();
        MemberHydration::sip($member, 3);

        $this->actingAs($admin)
            ->get(route('admin.enrollments.show', $member))
            ->assertOk()
            ->assertSee('Eau aujourd’hui')
            ->assertSee('Objectif quotidien')
            ->assertDontSee('Ajouter un verre');

        $this->actingAs($admin)
            ->put(route('admin.enrollments.hydration.update', $member), [
                'hydration_goal_glasses' => 10,
            ])
            ->assertRedirect(route('admin.enrollments.show', ['user' => $member, 'suivi' => 'eau']).'#suivi');

        $this->assertSame(10, (int) $member->fresh()->hydration_goal_glasses);

        $this->actingAs($admin)
            ->get(route('admin.enrollments.show', $member))
            ->assertOk()
            ->assertSee('2,5 L');
    }

    public function test_member_cannot_set_hydration_goal(): void
    {
        $member = User::factory()->member()->create();

        $this->actingAs($member)
            ->put(route('admin.enrollments.hydration.update', $member), [
                'hydration_goal_glasses' => 12,
            ])
            ->assertForbidden();
    }

    public function test_invalid_delta_is_rejected(): void
    {
        $member = User::factory()->member()->create();

        $this->actingAs($member)
            ->from(route('dashboard'))
            ->post(route('member.hydration.store'), ['delta' => 4])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHasErrors('delta');
    }
}
