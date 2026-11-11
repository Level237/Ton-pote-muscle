<?php

namespace Tests\Feature;

use App\Models\Exercise;
use App\Models\User;
use App\Models\Workout;
use App\Models\WorkoutLog;
use App\Support\MemberPlanAssigner;
use App\Support\WorkoutFeedback;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberWorkoutFeedbackTest extends TestCase
{
    use RefreshDatabase;

    public function test_recap_asks_how_the_session_felt(): void
    {
        [$member, $item] = $this->givenCompletedWorkout();

        $this->actingAs($member)
            ->get(route('member.workouts.recap', $item))
            ->assertOk()
            ->assertSee('Comment tu as trouvé cette séance')
            ->assertSee('Très facile')
            ->assertSee('Moyen')
            ->assertSee('Difficile');
    }

    public function test_member_can_save_session_feedback(): void
    {
        [$member, $item] = $this->givenCompletedWorkout();

        $this->actingAs($member)
            ->post(route('member.workouts.feedback', $item), [
                'difficulty_feedback' => WorkoutFeedback::Hard,
            ])
            ->assertRedirect(route('member.workouts.recap', $item));

        $this->assertDatabaseHas('workout_logs', [
            'user_id' => $member->id,
            'workout_id' => $item->workout_id,
            'difficulty_feedback' => WorkoutFeedback::Hard,
        ]);

        $this->actingAs($member)
            ->get(route('member.workouts.recap', $item))
            ->assertOk()
            ->assertSee('Noté · Difficile');
    }

    public function test_member_can_change_session_feedback(): void
    {
        [$member, $item] = $this->givenCompletedWorkout(WorkoutFeedback::Easy);

        $this->actingAs($member)
            ->post(route('member.workouts.feedback', $item), [
                'difficulty_feedback' => WorkoutFeedback::Medium,
            ])
            ->assertRedirect(route('member.workouts.recap', $item));

        $this->assertDatabaseHas('workout_logs', [
            'user_id' => $member->id,
            'difficulty_feedback' => WorkoutFeedback::Medium,
        ]);
    }

    public function test_other_members_cannot_save_feedback(): void
    {
        [$member, $item] = $this->givenCompletedWorkout();
        $other = User::factory()->member()->create();

        $this->actingAs($other)
            ->post(route('member.workouts.feedback', $item), [
                'difficulty_feedback' => WorkoutFeedback::Easy,
            ])
            ->assertNotFound();
    }

    /**
     * @return array{0: User, 1: \App\Models\MemberPlanItem}
     */
    private function givenCompletedWorkout(?int $feedback = null): array
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->member()->create();
        $exercise = Exercise::factory()->create();
        $workout = Workout::factory()->create();
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
            'difficulty_feedback' => $feedback,
        ]);

        return [$member, $item];
    }
}
