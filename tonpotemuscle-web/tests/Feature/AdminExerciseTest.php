<?php

namespace Tests\Feature;

use App\Models\Exercise;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminExerciseTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_cannot_access_exercises(): void
    {
        $member = User::factory()->member()->create();

        $this->actingAs($member)
            ->get(route('admin.exercises.index'))
            ->assertForbidden();
    }

    public function test_admin_can_view_exercise_index(): void
    {
        $admin = User::factory()->admin()->create();
        Exercise::factory()->create(['title' => 'Pompes']);

        $this->actingAs($admin)
            ->get(route('admin.exercises.index'))
            ->assertOk()
            ->assertSee('Pompes')
            ->assertSee('Nouvel exercice');
    }

    public function test_admin_can_search_exercises(): void
    {
        $admin = User::factory()->admin()->create();
        Exercise::factory()->create(['title' => 'Pompes']);
        Exercise::factory()->create(['title' => 'Tractions']);

        $this->actingAs($admin)
            ->get(route('admin.exercises.index', ['q' => 'Pompe']))
            ->assertOk()
            ->assertSee('Pompes')
            ->assertDontSee('Tractions');
    }

    public function test_admin_can_create_an_exercise(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('admin.exercises.store'), [
                'title' => 'Pompes',
                'description' => 'Pectoraux et triceps.',
                'body_parts' => ['chest', 'arms'],
                'equipments' => ['bodyweight'],
                'target_muscles' => 'pectoraux, triceps',
                'instructions' => "Mains au sol\nDescendre poitrine proche du sol",
                'image' => UploadedFile::fake()->image('pompes.jpg'),
            ])
            ->assertRedirect(route('admin.exercises.index'));

        $exercise = Exercise::query()->where('title', 'Pompes')->first();

        $this->assertNotNull($exercise);
        $this->assertSame(['chest', 'arms'], $exercise->body_parts);
        $this->assertSame(['bodyweight'], $exercise->equipments);
        $this->assertSame(['pectoraux', 'triceps'], $exercise->target_muscles);
        $this->assertSame(['Mains au sol', 'Descendre poitrine proche du sol'], $exercise->instructions);
        $this->assertNotNull($exercise->image_path);
        Storage::disk('public')->assertExists($exercise->image_path);
    }

    public function test_exercise_title_is_required(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->from(route('admin.exercises.create'))
            ->post(route('admin.exercises.store'), [
                'title' => '',
            ])
            ->assertRedirect(route('admin.exercises.create'))
            ->assertSessionHasErrors('title');
    }

    public function test_admin_can_update_an_exercise(): void
    {
        $admin = User::factory()->admin()->create();
        $exercise = Exercise::factory()->create(['title' => 'Pompes']);

        $this->actingAs($admin)
            ->put(route('admin.exercises.update', $exercise), [
                'title' => 'Pompes diamant',
                'body_parts' => ['arms'],
            ])
            ->assertRedirect(route('admin.exercises.edit', $exercise));

        $this->assertSame('Pompes diamant', $exercise->fresh()->title);
        $this->assertSame(['arms'], $exercise->fresh()->body_parts);
    }

    public function test_admin_can_archive_and_restore_an_exercise(): void
    {
        $admin = User::factory()->admin()->create();
        $exercise = Exercise::factory()->create(['title' => 'Pompes']);

        $this->actingAs($admin)
            ->delete(route('admin.exercises.destroy', $exercise))
            ->assertRedirect(route('admin.exercises.index'));

        $this->assertSoftDeleted($exercise);

        $this->actingAs($admin)
            ->get(route('admin.exercises.index'))
            ->assertOk()
            ->assertDontSee('Pompes');

        $this->actingAs($admin)
            ->get(route('admin.exercises.index', ['archived' => 1]))
            ->assertOk()
            ->assertSee('Pompes');

        $this->actingAs($admin)
            ->post(route('admin.exercises.restore', $exercise))
            ->assertRedirect(route('admin.exercises.index'));

        $this->assertNotSoftDeleted($exercise);
    }
}
