<?php

namespace Tests\Feature;

use App\Enums\EnrollmentStatus;
use App\Enums\ProgramStatus;
use App\Models\Exercise;
use App\Models\MemberAssignment;
use App\Models\MemberPlanItem;
use App\Models\Program;
use App\Models\User;
use App\Models\Workout;
use App\Models\WorkoutLog;
use App\Support\MemberPlanAssigner;
use App\Support\WorkoutFeedback;
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
            ->assertSee('Progrès')
            ->assertSee('Pesée de la semaine')
            ->assertSee('Matériel')
            ->assertSee('Non renseigné')
            ->assertDontSee('Enregistrer');
    }

    public function test_admin_sees_workout_feedback_on_member_detail(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->member()->create(['name' => 'Marie Dupont']);
        $workout = Workout::factory()->create(['title' => 'HIIT test']);
        $assignment = app(MemberPlanAssigner::class)->assignWorkout($member, $admin, $workout, [now()->toDateString()]);
        $item = $assignment->items()->first();

        WorkoutLog::query()->create([
            'user_id' => $member->id,
            'workout_id' => $item->workout_id,
            'mode' => 'live',
            'performed_on' => $item->planned_on->toDateString(),
            'started_at' => now()->subMinutes(20),
            'completed_at' => now(),
            'abandoned' => false,
            'points_awarded' => 12,
            'difficulty_feedback' => WorkoutFeedback::Hard,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.enrollments.show', $member))
            ->assertOk()
            ->assertSee('HIIT test')
            ->assertSee('Ressenti')
            ->assertSee('Difficile');

        $this->actingAs($admin)
            ->get(route('admin.enrollments.show', ['user' => $member, 'vue' => 'semaine']))
            ->assertOk()
            ->assertSee('Difficile');
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

    public function test_admin_can_prescribe_week_loads_once_for_repeated_exercises(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->member()->create();
        $exercise = Exercise::factory()->create([
            'title' => 'Rowing haltères',
            'equipments' => ['dumbbells'],
        ]);
        $mondayWorkout = Workout::factory()->create(['title' => 'Dos']);
        $mondayRow = $mondayWorkout->items()->create([
            'exercise_id' => $exercise->id,
            'position' => 1,
            'sets' => 3,
            'reps' => 10,
            'rest_seconds' => 60,
        ]);
        $thursdayWorkout = Workout::factory()->create(['title' => 'Full body']);
        $thursdayRow = $thursdayWorkout->items()->create([
            'exercise_id' => $exercise->id,
            'position' => 1,
            'sets' => 3,
            'reps' => 12,
            'rest_seconds' => 45,
        ]);
        $program = Program::factory()->create([
            'title' => 'Force maison',
            'status' => ProgramStatus::Open,
        ]);
        $program->schedule()->create(['day_number' => 1, 'workout_id' => $mondayWorkout->id]);
        $program->schedule()->create(['day_number' => 2, 'workout_id' => null]);
        $program->schedule()->create(['day_number' => 4, 'workout_id' => $thursdayWorkout->id]);

        $this->actingAs($admin)
            ->post(route('admin.enrollments.give.store', $member), [
                'mode' => 'program',
                'program_id' => $program->id,
                'program_week' => 1,
                'target_week' => 'next',
                'week_loads' => [$exercise->id => 12],
            ])
            ->assertRedirect(route('admin.enrollments.show', $member));

        $nextMonday = now()->startOfWeek(Carbon::MONDAY)->addWeek();
        $mondayItem = MemberPlanItem::query()
            ->where('user_id', $member->id)
            ->whereDate('planned_on', $nextMonday->toDateString())
            ->first();
        $thursdayItem = MemberPlanItem::query()
            ->where('user_id', $member->id)
            ->whereDate('planned_on', $nextMonday->copy()->addDays(3)->toDateString())
            ->first();

        $this->assertSame(12.0, $mondayItem?->loadKg($mondayRow));
        $this->assertSame(12.0, $thursdayItem?->loadKg($thursdayRow));
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

    public function test_admin_can_cancel_a_given_workout(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->member()->create();
        $workout = Workout::factory()->create(['title' => 'Full body']);
        $assignment = app(MemberPlanAssigner::class)->assignWorkout(
            $member,
            $admin,
            $workout,
            ['2026-11-10', '2026-11-12'],
        );

        $this->actingAs($admin)
            ->get(route('admin.enrollments.give', $member))
            ->assertOk()
            ->assertSee('Full body')
            ->assertSee('Annuler le don');

        $this->actingAs($admin)
            ->delete(route('admin.enrollments.assignments.destroy', [$member, $assignment]))
            ->assertRedirect(route('admin.enrollments.give', $member));

        $this->assertDatabaseMissing('member_assignments', ['id' => $assignment->id]);
        $this->assertSame(0, MemberPlanItem::query()->where('user_id', $member->id)->count());
    }

    public function test_admin_can_cancel_a_given_program_week(): void
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

        $assignment = MemberAssignment::query()
            ->where('user_id', $member->id)
            ->where('type', 'program_week')
            ->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.enrollments.give', $member))
            ->assertOk()
            ->assertSee('Annuler la semaine')
            ->assertSee('Force maison');

        $this->actingAs($admin)
            ->delete(route('admin.enrollments.assignments.destroy', [$member, $assignment]))
            ->assertRedirect(route('admin.enrollments.give', $member));

        $this->assertDatabaseMissing('member_assignments', ['id' => $assignment->id]);
        $this->assertSame(0, MemberPlanItem::query()->where('user_id', $member->id)->count());
    }

    public function test_admin_cannot_cancel_another_members_assignment(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->member()->create();
        $other = User::factory()->member()->create();
        $workout = Workout::factory()->create();
        $assignment = app(MemberPlanAssigner::class)->assignWorkout($member, $admin, $workout, ['2026-11-10']);

        $this->actingAs($admin)
            ->delete(route('admin.enrollments.assignments.destroy', [$other, $assignment]))
            ->assertNotFound();

        $this->assertDatabaseHas('member_assignments', ['id' => $assignment->id]);
    }

    public function test_admin_can_prescribe_loads_when_member_has_equipment(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->member()->create([
            'trains_with_equipment' => true,
            'equipment_notes' => 'Haltères 10 et 20 kg',
        ]);
        $exercise = Exercise::factory()->create([
            'title' => 'Rowing haltères',
            'equipments' => ['dumbbells'],
        ]);
        $workout = Workout::factory()->create(['title' => 'Dos haltères']);
        $row = $workout->items()->create([
            'exercise_id' => $exercise->id,
            'position' => 1,
            'sets' => 3,
            'reps' => 10,
            'rest_seconds' => 60,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.enrollments.give', $member))
            ->assertOk()
            ->assertSee('Charges (kg)');

        $this->actingAs($admin)
            ->post(route('admin.enrollments.give.store', $member), [
                'mode' => 'workout',
                'workout_id' => $workout->id,
                'planned_on' => now()->toDateString(),
                'loads' => [$row->id => 12],
            ])
            ->assertRedirect(route('admin.enrollments.give', $member));

        $item = MemberPlanItem::query()->where('user_id', $member->id)->first();
        $this->assertSame(12.0, $item->loadKg($row));

        $this->actingAs($admin)
            ->get(route('admin.enrollments.show', $member))
            ->assertOk()
            ->assertSee('Matériel')
            ->assertSee('Oui')
            ->assertSee('Haltères 10 et 20 kg')
            ->assertSee('12 kg');

        $this->actingAs($member)
            ->get(route('member.workouts.show', $item))
            ->assertOk()
            ->assertSee('Rowing haltères')
            ->assertSee('12 kg');
    }

    public function test_admin_can_prescribe_loads_when_member_declined_equipment(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->member()->create(['name' => 'Marie Dupont', 'trains_with_equipment' => false]);
        $exercise = Exercise::factory()->create(['equipments' => ['dumbbells']]);
        $workout = Workout::factory()->create();
        $row = $workout->items()->create([
            'exercise_id' => $exercise->id,
            'position' => 1,
            'sets' => 3,
            'reps' => 8,
            'rest_seconds' => 45,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.enrollments.give', $member))
            ->assertOk()
            ->assertSee('Charges (kg)')
            ->assertSee('a indiqué sans matériel');

        $this->actingAs($admin)
            ->post(route('admin.enrollments.give.store', $member), [
                'mode' => 'workout',
                'workout_id' => $workout->id,
                'planned_on' => now()->toDateString(),
                'loads' => [$row->id => 20],
            ])
            ->assertRedirect(route('admin.enrollments.give', $member));

        $item = MemberPlanItem::query()->where('user_id', $member->id)->first();
        $this->assertSame(20.0, $item->loadKg($row));
    }
}
