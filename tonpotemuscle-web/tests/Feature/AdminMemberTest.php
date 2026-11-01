<?php

namespace Tests\Feature;

use App\Enums\EnrollmentStatus;
use App\Enums\ProgramStatus;
use App\Models\MemberPlanItem;
use App\Models\Program;
use App\Models\User;
use App\Models\Workout;
use App\Support\MemberPlanAssigner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AdminMemberTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_cannot_access_inscrits(): void
    {
        $member = User::factory()->member()->create();

        $this->actingAs($member)
            ->get(route('admin.enrollments.index'))
            ->assertForbidden();
    }

    public function test_admin_can_view_member_index(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->member()->create([
            'name' => 'Marie Dupont',
            'username' => 'marie',
            'email' => 'marie@example.com',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.enrollments.index'))
            ->assertOk()
            ->assertSee('Marie Dupont')
            ->assertSee('marie')
            ->assertSee('marie@example.com')
            ->assertSee('Désactiver')
            ->assertSee('Voir')
            ->assertDontSee('Modifier');
    }

    public function test_admin_can_search_members(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->member()->create(['name' => 'Marie Dupont', 'username' => 'marie']);
        User::factory()->member()->create(['name' => 'Paul Martin', 'username' => 'paul']);

        $this->actingAs($admin)
            ->get(route('admin.enrollments.index', ['q' => 'marie']))
            ->assertOk()
            ->assertSee('Marie Dupont')
            ->assertDontSee('Paul Martin');
    }

    public function test_admin_index_does_not_list_admins(): void
    {
        $admin = User::factory()->admin()->create(['name' => 'Admin TPM']);
        User::factory()->member()->create(['name' => 'Marie Dupont']);

        $this->actingAs($admin)
            ->get(route('admin.enrollments.index'))
            ->assertOk()
            ->assertSee('Marie Dupont')
            ->assertDontSee('Admin TPM');
    }

    public function test_admin_can_deactivate_a_member(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->member()->create(['name' => 'Marie Dupont']);

        $this->actingAs($admin)
            ->post(route('admin.enrollments.deactivate', $member))
            ->assertRedirect(route('admin.enrollments.index'))
            ->assertSessionHas('status', 'Compte désactivé.');

        $this->assertNotNull($member->fresh()->deactivated_at);

        $this->actingAs($admin)
            ->get(route('admin.enrollments.index'))
            ->assertDontSee('Marie Dupont');

        $this->actingAs($admin)
            ->get(route('admin.enrollments.index', ['deactivated' => 1]))
            ->assertSee('Marie Dupont')
            ->assertSee('Réactiver');
    }

    public function test_admin_can_reactivate_a_member(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->member()->deactivated()->create(['name' => 'Marie Dupont']);

        $this->actingAs($admin)
            ->post(route('admin.enrollments.activate', $member))
            ->assertRedirect(route('admin.enrollments.index', ['deactivated' => 1]))
            ->assertSessionHas('status', 'Compte réactivé.');

        $this->assertNull($member->fresh()->deactivated_at);
    }

    public function test_admin_cannot_deactivate_an_admin_account(): void
    {
        $admin = User::factory()->admin()->create();
        $otherAdmin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('admin.enrollments.deactivate', $otherAdmin))
            ->assertNotFound();

        $this->assertNull($otherAdmin->fresh()->deactivated_at);
    }

    public function test_deactivated_member_cannot_log_in(): void
    {
        $member = User::factory()->member()->deactivated()->create();

        $this->post(route('login'), [
            'email' => $member->email,
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
        $this->assertStringContainsString(
            'désactivé',
            session('errors')->first('email'),
        );
    }

    public function test_deactivated_member_is_logged_out_from_dashboard(): void
    {
        $member = User::factory()->member()->create();

        $this->actingAs($member)
            ->get(route('dashboard'))
            ->assertOk();

        $member->update(['deactivated_at' => now()]);

        $this->actingAs($member)
            ->get(route('dashboard'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_admin_can_view_member_detail(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->member()->create(['name' => 'Marie Dupont']);

        $this->actingAs($admin)
            ->get(route('admin.enrollments.show', $member))
            ->assertOk()
            ->assertSee('Marie Dupont')
            ->assertSee('Donner')
            ->assertSee('← Inscrits')
            ->assertSee('Accueil')
            ->assertSee('Semaine')
            ->assertSee('Progrès');
    }

    public function test_admin_can_view_member_week_and_progress(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->member()->create(['name' => 'Marie Dupont']);
        $workout = Workout::factory()->create(['title' => 'Pompes & abdos']);
        app(MemberPlanAssigner::class)->assignWorkout($member, $admin, $workout, [now()->toDateString()]);

        $this->actingAs($admin)
            ->get(route('admin.enrollments.show', ['user' => $member, 'vue' => 'semaine']))
            ->assertOk()
            ->assertSee('Pompes & abdos')
            ->assertSee('À faire');

        $this->actingAs($admin)
            ->get(route('admin.enrollments.show', ['user' => $member, 'vue' => 'progres', 'range' => 'month']))
            ->assertOk()
            ->assertSee('séance')
            ->assertSee('Records');
    }

    public function test_admin_can_give_a_workout_to_a_member(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->member()->create();
        $workout = Workout::factory()->create(['title' => 'Full body']);

        $this->actingAs($admin)
            ->post(route('admin.enrollments.give.store', $member), [
                'mode' => 'workout',
                'workout_id' => $workout->id,
                'planned_on' => '2026-11-03',
            ])
            ->assertRedirect(route('admin.enrollments.give', $member));

        $this->assertTrue(
            MemberPlanItem::query()
                ->where('user_id', $member->id)
                ->where('workout_id', $workout->id)
                ->whereDate('planned_on', '2026-11-03')
                ->exists(),
        );
    }

    public function test_admin_can_give_a_program_week_to_a_member(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->member()->create();
        $workout = Workout::factory()->create(['title' => 'Full body']);
        $program = Program::factory()->create([
            'title' => 'Force maison',
            'status' => ProgramStatus::Open,
        ]);
        $program->schedule()->create(['day_number' => 1, 'workout_id' => $workout->id]);
        $program->schedule()->create(['day_number' => 2, 'workout_id' => null]);

        $this->actingAs($admin)
            ->post(route('admin.enrollments.give.store', $member), [
                'mode' => 'program',
                'program_id' => $program->id,
                'program_week' => 1,
                'target_week' => 'next',
            ])
            ->assertRedirect(route('admin.enrollments.show', $member));

        $monday = now()->startOfWeek(Carbon::MONDAY)->addWeek()->toDateString();
        $tuesday = now()->startOfWeek(Carbon::MONDAY)->addWeek()->addDay()->toDateString();

        $this->assertTrue(
            MemberPlanItem::query()
                ->where('user_id', $member->id)
                ->where('kind', 'workout')
                ->where('workout_id', $workout->id)
                ->whereDate('planned_on', $monday)
                ->exists(),
        );
        $this->assertTrue(
            MemberPlanItem::query()
                ->where('user_id', $member->id)
                ->where('kind', 'rest')
                ->whereDate('planned_on', $tuesday)
                ->exists(),
        );
        $this->assertDatabaseHas('program_enrollments', [
            'user_id' => $member->id,
            'program_id' => $program->id,
            'status' => EnrollmentStatus::Active->value,
        ]);
        $this->assertTrue(MemberPlanItem::query()->where('user_id', $member->id)->count() >= 2);
    }

    public function test_admin_can_view_give_workout_calendar(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->member()->create();
        $workout = Workout::factory()->create(['title' => 'Pompes & abdos']);

        $this->actingAs($admin)
            ->get(route('admin.enrollments.give', $member))
            ->assertOk()
            ->assertSee('Une séance')
            ->assertSee('Toutes')
            ->assertSee('Choisir une séance')
            ->assertSee('Semaine précédente')
            ->assertSee('Historique')
            ->assertSee('Pompes & abdos');
    }

    public function test_admin_can_give_a_workout_on_several_days(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->member()->create();
        $workout = Workout::factory()->create(['title' => 'Full body']);

        $this->actingAs($admin)
            ->post(route('admin.enrollments.give.store', $member), [
                'mode' => 'workout',
                'workout_id' => $workout->id,
                'planned_on' => ['2026-11-01', '2026-11-03'],
            ])
            ->assertRedirect(route('admin.enrollments.give', $member));

        $this->assertSame(2, MemberPlanItem::query()->where('user_id', $member->id)->count());
    }

    public function test_admin_can_remove_a_plan_item(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->member()->create();
        $workout = Workout::factory()->create();
        app(MemberPlanAssigner::class)->assignWorkout($member, $admin, $workout, ['2026-11-01']);
        $item = MemberPlanItem::query()->where('user_id', $member->id)->firstOrFail();

        $this->actingAs($admin)
            ->delete(route('admin.enrollments.plan.destroy', [$member, $item]))
            ->assertRedirect(route('admin.enrollments.give', $member));

        $this->assertDatabaseMissing('member_plan_items', ['id' => $item->id]);
    }
}
