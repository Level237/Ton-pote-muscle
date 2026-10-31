<?php

namespace Tests\Feature;

use App\Models\Exercise;
use App\Models\User;
use App\Models\Workout;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminWorkoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_cannot_access_workouts(): void
    {
        $member = User::factory()->member()->create();

        $this->actingAs($member)
            ->get(route('admin.workouts.index'))
            ->assertForbidden();
    }

    public function test_admin_can_view_workout_index(): void
    {
        $admin = User::factory()->admin()->create();
        Workout::factory()->create(['title' => 'Full body']);

        $this->actingAs($admin)
            ->get(route('admin.workouts.index'))
            ->assertOk()
            ->assertSee('Full body')
            ->assertSee('Nouvelle séance');
    }

    public function test_admin_can_search_workouts(): void
    {
        $admin = User::factory()->admin()->create();
        Workout::factory()->create(['title' => 'Full body']);
        Workout::factory()->create(['title' => 'Cardio']);

        $this->actingAs($admin)
            ->get(route('admin.workouts.index', ['q' => 'Full']))
            ->assertOk()
            ->assertSee('Full body')
            ->assertDontSee('Cardio');
    }

    public function test_admin_can_create_a_workout_with_ordered_exercises(): void
    {
        $admin = User::factory()->admin()->create();
        $pompes = Exercise::factory()->create(['title' => 'Pompes']);
        $squats = Exercise::factory()->create(['title' => 'Squats']);

        $this->actingAs($admin)
            ->post(route('admin.workouts.store'), [
                'title' => 'Full body',
                'difficulty' => 'beginner',
                'items' => [
                    [
                        'exercise_id' => $pompes->id,
                        'reps' => 12,
                        'duration_seconds' => '',
                        'rest_seconds' => 30,
                    ],
                    [
                        'exercise_id' => $squats->id,
                        'reps' => '',
                        'duration_seconds' => 40,
                        'rest_seconds' => 20,
                    ],
                ],
            ])
            ->assertRedirect(route('admin.workouts.index'));

        $workout = Workout::query()->where('title', 'Full body')->first();

        $this->assertNotNull($workout);
        $this->assertSame('beginner', $workout->difficulty);
        $this->assertSame(90, $workout->total_duration_seconds);
        $this->assertCount(2, $workout->items);
        $this->assertSame($pompes->id, $workout->items[0]->exercise_id);
        $this->assertSame(12, $workout->items[0]->reps);
        $this->assertSame($squats->id, $workout->items[1]->exercise_id);
        $this->assertSame(40, $workout->items[1]->duration_seconds);
    }

    public function test_workout_requires_an_exercise(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->from(route('admin.workouts.create'))
            ->post(route('admin.workouts.store'), [
                'title' => 'Vide',
                'items' => [
                    ['exercise_id' => '', 'rest_seconds' => 30],
                ],
            ])
            ->assertRedirect(route('admin.workouts.create'))
            ->assertSessionHasErrors('items');
    }

    public function test_admin_can_update_a_workout(): void
    {
        $admin = User::factory()->admin()->create();
        $pompes = Exercise::factory()->create(['title' => 'Pompes']);
        $workout = Workout::factory()->create(['title' => 'Full body']);
        $workout->items()->create([
            'exercise_id' => $pompes->id,
            'position' => 1,
            'reps' => 10,
            'rest_seconds' => 30,
        ]);

        $this->actingAs($admin)
            ->put(route('admin.workouts.update', $workout), [
                'title' => 'Upper body',
                'difficulty' => 'intermediate',
                'items' => [
                    [
                        'exercise_id' => $pompes->id,
                        'reps' => 15,
                        'rest_seconds' => 45,
                    ],
                ],
            ])
            ->assertRedirect(route('admin.workouts.edit', $workout));

        $workout->refresh();

        $this->assertSame('Upper body', $workout->title);
        $this->assertSame('intermediate', $workout->difficulty);
        $this->assertSame(15, $workout->items()->first()->reps);
        $this->assertSame(45, $workout->total_duration_seconds);
    }

    public function test_admin_can_archive_and_restore_a_workout(): void
    {
        $admin = User::factory()->admin()->create();
        $workout = Workout::factory()->create(['title' => 'Full body']);

        $this->actingAs($admin)
            ->delete(route('admin.workouts.destroy', $workout))
            ->assertRedirect(route('admin.workouts.index'));

        $this->assertSoftDeleted($workout);

        $this->actingAs($admin)
            ->get(route('admin.workouts.index'))
            ->assertOk()
            ->assertDontSee('Full body');

        $this->actingAs($admin)
            ->get(route('admin.workouts.index', ['archived' => 1]))
            ->assertOk()
            ->assertSee('Full body');

        $this->actingAs($admin)
            ->post(route('admin.workouts.restore', $workout))
            ->assertRedirect(route('admin.workouts.index'));

        $this->assertNotSoftDeleted($workout);
    }
}
