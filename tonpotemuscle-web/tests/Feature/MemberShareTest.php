<?php

namespace Tests\Feature;

use App\Models\Exercise;
use App\Models\Program;
use App\Models\ProgramEnrollment;
use App\Models\User;
use App\Models\Workout;
use App\Models\WorkoutLog;
use App\Support\MemberPlanAssigner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberShareTest extends TestCase
{
    use RefreshDatabase;

    public function test_completing_a_live_workout_opens_the_recap(): void
    {
        [$member, $item] = $this->givenWorkout();
        $exercise = $item->workout->items->first();

        $this->actingAs($member)->post(route('member.workouts.start', $item));

        $this->actingAs($member)
            ->postJson(route('member.workouts.complete', $item), [
                'abandoned' => false,
                'actual_duration_seconds' => 420,
                'exercises' => [[
                    'workout_exercise_id' => $exercise->id,
                    'exercise_id' => $exercise->exercise_id,
                    'position' => 1,
                    'skipped' => false,
                ]],
            ])
            ->assertOk()
            ->assertJsonPath('redirect', route('member.workouts.recap', $item));

        $log = WorkoutLog::query()->where('user_id', $member->id)->first();
        $this->assertNotNull($log?->share_token);

        $this->actingAs($member)
            ->get(route('member.workouts.recap', $item))
            ->assertOk()
            ->assertSee('C’est fait')
            ->assertSee('HIIT test')
            ->assertSee('Partager')
            ->assertSee('Copier le lien')
            ->assertSee(route('share.show', $log->share_token), false);

        $this->get(route('share.show', $log->share_token))
            ->assertOk()
            ->assertSee('<title>Marie a effectué HIIT test</title>', false)
            ->assertSee('name="description"', false)
            ->assertSee('Marie a effectué HIIT test', false)
            ->assertSee('property="og:image"', false)
            ->assertSee('/assets/img/logo.webp', false)
            ->assertSee('@marie')
            ->assertSee('+12 pts');
    }

    public function test_other_members_cannot_open_a_recap(): void
    {
        [$member, $item] = $this->givenWorkout();
        $other = User::factory()->member()->create();

        WorkoutLog::query()->create([
            'user_id' => $member->id,
            'workout_id' => $item->workout_id,
            'mode' => 'live',
            'performed_on' => $item->planned_on->toDateString(),
            'started_at' => now()->subMinutes(20),
            'completed_at' => now(),
            'abandoned' => false,
            'points_awarded' => 12,
            'share_token' => '01jsharetoken0000000000001',
        ]);

        $this->actingAs($other)
            ->get(route('member.workouts.recap', $item))
            ->assertNotFound();
    }

    public function test_unknown_share_token_is_not_found(): void
    {
        $this->get(route('share.show', '01jdoesnotexist00000000001'))
            ->assertNotFound();
    }

    public function test_member_can_share_published_rank(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->member()->create(['username' => 'marie', 'name' => 'Marie']);
        $program = Program::factory()->create(['title' => 'Force maison']);
        ProgramEnrollment::factory()->create([
            'program_id' => $program->id,
            'user_id' => $member->id,
            'points' => 42,
            'live_count' => 3,
        ]);

        $this->actingAs($member)
            ->get(route('member.programs.show', $program))
            ->assertOk()
            ->assertDontSee('Partager mon rang');

        $this->actingAs($admin)
            ->post(route('admin.rankings.publish', $program))
            ->assertRedirect();

        $entry = $program->fresh()->rankingSnapshot->entries->first();
        $this->assertNotNull($entry->share_token);

        $this->actingAs($member)
            ->get(route('member.programs.show', $program))
            ->assertOk()
            ->assertSee('1er')
            ->assertSee('Partager mon rang')
            ->assertSee('Force maison');

        $this->get(route('share.show', $entry->share_token))
            ->assertOk()
            ->assertSee('<title>Marie termine 1er du programme Force maison</title>', false)
            ->assertSee('Marie termine 1er du programme Force maison', false)
            ->assertSee('/assets/img/logo.webp', false)
            ->assertSee('@marie');
    }

    /**
     * @return array{0: User, 1: \App\Models\MemberPlanItem}
     */
    private function givenWorkout(): array
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->member()->create(['username' => 'marie', 'name' => 'Marie']);
        $exercise = Exercise::factory()->create(['title' => 'Pompes']);
        $workout = Workout::factory()->create(['title' => 'HIIT test', 'total_duration_seconds' => 1200]);
        $workout->items()->create([
            'exercise_id' => $exercise->id,
            'position' => 1,
            'sets' => 1,
            'reps' => 10,
            'rest_seconds' => 20,
        ]);

        $assignment = app(MemberPlanAssigner::class)->assignWorkout(
            $member,
            $admin,
            $workout,
            [now()->toDateString()],
        );

        return [$member, $assignment->items()->first()->load('workout.items')];
    }
}
