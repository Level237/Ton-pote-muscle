<?php

namespace Tests\Feature;

use App\Models\Exercise;
use App\Models\MemberPlanItem;
use App\Models\User;
use App\Models\Workout;
use App\Models\WorkoutLog;
use App\Support\MemberPlanAssigner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberLiveWorkoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_preview_and_start_todays_workout(): void
    {
        [$member, $item, $animated, $plain] = $this->givenWorkout();

        $this->actingAs($member)
            ->get(route('member.workouts.show', $item))
            ->assertOk()
            ->assertSee('HIIT test')
            ->assertSee('Commencez')
            ->assertSee($animated->title)
            ->assertSee('/storage/exercises/animated/pompes.webp')
            ->assertSee($plain->title)
            ->assertDontSee('/storage/exercises/planche.webp')
            ->assertSee('Voir la démo')
            ->assertSee('https://www.youtube.com/watch?v=planche-demo');

        $this->actingAs($member)
            ->post(route('member.workouts.start', $item))
            ->assertRedirect(route('member.workouts.live', $item));

        $this->assertDatabaseHas('workout_logs', [
            'user_id' => $member->id,
            'workout_id' => $item->workout_id,
            'mode' => 'live',
            'abandoned' => false,
        ]);

        $this->actingAs($member)
            ->get(route('member.workouts.live', $item))
            ->assertOk()
            ->assertSee('Suivant')
            ->assertSee('Voir la démo');
    }

    public function test_member_can_complete_a_live_workout(): void
    {
        [$member, $item] = $this->givenWorkout();
        $exercise = $item->workout->items->first();

        $this->actingAs($member)
            ->post(route('member.workouts.start', $item));

        $this->actingAs($member)
            ->postJson(route('member.workouts.complete', $item), [
                'abandoned' => false,
                'actual_duration_seconds' => 420,
                'exercises' => [
                    [
                        'workout_exercise_id' => $exercise->id,
                        'exercise_id' => $exercise->exercise_id,
                        'position' => 1,
                        'started_at' => now()->subMinutes(7)->toIso8601String(),
                        'ended_at' => now()->toIso8601String(),
                        'skipped' => false,
                    ],
                ],
            ])
            ->assertOk()
            ->assertJsonPath('redirect', route('dashboard'));

        $this->assertDatabaseHas('workout_logs', [
            'user_id' => $member->id,
            'workout_id' => $item->workout_id,
            'points_awarded' => 12,
            'abandoned' => false,
        ]);

        $this->actingAs($member)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Faite aujourd’hui')
            ->assertDontSee('À faire aujourd’hui')
            ->assertDontSee('Débuter ma séance');

        $this->actingAs($member)
            ->get(route('dashboard', ['vue' => 'semaine']))
            ->assertOk()
            ->assertSee('Faite');
    }

    public function test_quitting_deletes_the_log_and_lets_member_start_again(): void
    {
        [$member, $item] = $this->givenWorkout();

        $this->actingAs($member)
            ->post(route('member.workouts.start', $item))
            ->assertRedirect(route('member.workouts.live', $item));

        $this->actingAs($member)
            ->postJson(route('member.workouts.complete', $item), [
                'abandoned' => true,
                'actual_duration_seconds' => 12,
                'exercises' => [],
            ])
            ->assertOk();

        $this->assertDatabaseMissing('workout_logs', [
            'user_id' => $member->id,
            'workout_id' => $item->workout_id,
        ]);

        $this->actingAs($member)
            ->post(route('member.workouts.start', $item))
            ->assertRedirect(route('member.workouts.live', $item));

        $this->assertEquals(1, WorkoutLog::query()
            ->where('user_id', $member->id)
            ->where('workout_id', $item->workout_id)
            ->count());
    }

    public function test_member_cannot_open_another_members_workout(): void
    {
        [, $item] = $this->givenWorkout();
        $other = User::factory()->member()->create();

        $this->actingAs($other)
            ->get(route('member.workouts.show', $item))
            ->assertNotFound();
    }

    /**
     * @return array{0: User, 1: MemberPlanItem, 2?: Exercise, 3?: Exercise}
     */
    private function givenWorkout(): array
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->member()->create();
        $animated = Exercise::factory()->create([
            'title' => 'Pompes animées',
            'gif_path' => 'exercises/animated/pompes.webp',
            'image_path' => 'exercises/pompes.webp',
        ]);
        $plain = Exercise::factory()->create([
            'title' => 'Planche sans gif',
            'image_path' => 'exercises/planche.webp',
            'video_url' => 'https://www.youtube.com/watch?v=planche-demo',
        ]);
        $workout = Workout::factory()->create(['title' => 'HIIT test']);
        $workout->items()->create([
            'exercise_id' => $animated->id,
            'position' => 1,
            'sets' => 1,
            'reps' => 10,
            'rest_seconds' => 20,
        ]);
        $workout->items()->create([
            'exercise_id' => $plain->id,
            'position' => 2,
            'sets' => 1,
            'duration_seconds' => 30,
            'rest_seconds' => 15,
        ]);

        $assignment = app(MemberPlanAssigner::class)->assignWorkout(
            $member,
            $admin,
            $workout,
            [now()->toDateString()],
        );

        $item = $assignment->items()->first();
        $item->load('workout.items.exercise');

        return [$member, $item, $animated, $plain];
    }
}
