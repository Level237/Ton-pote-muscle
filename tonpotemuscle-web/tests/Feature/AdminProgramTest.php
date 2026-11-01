<?php

namespace Tests\Feature;

use App\Enums\ProgramStatus;
use App\Models\Program;
use App\Models\User;
use App\Models\Workout;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminProgramTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_cannot_access_admin_programs(): void
    {
        $member = User::factory()->member()->create();

        $this->actingAs($member)
            ->get(route('admin.programs.index'))
            ->assertForbidden();
    }

    public function test_admin_can_view_program_index(): void
    {
        $admin = User::factory()->admin()->create();
        Program::factory()->create(['title' => '4 semaines']);

        $this->actingAs($admin)
            ->get(route('admin.programs.index'))
            ->assertOk()
            ->assertSee('4 semaines')
            ->assertSee('Nouveau programme');
    }

    public function test_admin_can_search_programs(): void
    {
        $admin = User::factory()->admin()->create();
        Program::factory()->create(['title' => '4 semaines']);
        Program::factory()->create(['title' => 'Cut']);

        $this->actingAs($admin)
            ->get(route('admin.programs.index', ['q' => 'semaine']))
            ->assertOk()
            ->assertSee('4 semaines')
            ->assertDontSee('Cut');
    }

    public function test_admin_can_create_a_program_with_a_calendar(): void
    {
        $admin = User::factory()->admin()->create();
        $workout = Workout::factory()->create(['title' => 'Full body']);

        $this->actingAs($admin)
            ->post(route('admin.programs.store'), [
                'title' => '4 semaines',
                'starts_on' => '2026-11-01',
                'ends_on' => '2026-11-28',
                'status' => ProgramStatus::Open->value,
                'level' => 'beginner',
                'slots' => [
                    ['day_number' => 1, 'kind' => 'workout', 'workout_id' => $workout->id],
                    ['day_number' => 2, 'kind' => 'rest'],
                    ['day_number' => 3, 'kind' => 'workout', 'workout_id' => $workout->id],
                ],
            ])
            ->assertRedirect(route('admin.programs.index'));

        $program = Program::query()->where('title', '4 semaines')->first();

        $this->assertNotNull($program);
        $this->assertSame(ProgramStatus::Open, $program->status);
        $this->assertCount(3, $program->schedule);
        $this->assertSame(1, $program->schedule[0]->day_number);
        $this->assertSame($workout->id, $program->schedule[0]->workout_id);
        $this->assertTrue($program->schedule[1]->isRest());
        $this->assertSame('Repos', $program->schedule[1]->label());
    }

    public function test_program_cannot_contain_only_rest_days(): void
    {
        $admin = User::factory()->admin()->create();
        Workout::factory()->create();

        $this->actingAs($admin)
            ->from(route('admin.programs.create'))
            ->post(route('admin.programs.store'), [
                'title' => 'Que du repos',
                'starts_on' => '2026-11-01',
                'ends_on' => '2026-11-28',
                'status' => ProgramStatus::Draft->value,
                'slots' => [
                    ['day_number' => 1, 'kind' => 'rest'],
                    ['day_number' => 2, 'kind' => 'rest'],
                ],
            ])
            ->assertRedirect(route('admin.programs.create'))
            ->assertSessionHasErrors('slots');
    }

    public function test_program_requires_a_calendar_slot(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->from(route('admin.programs.create'))
            ->post(route('admin.programs.store'), [
                'title' => 'Vide',
                'starts_on' => '2026-11-01',
                'ends_on' => '2026-11-28',
                'status' => ProgramStatus::Draft->value,
                'slots' => [
                    ['day_number' => 1, 'workout_id' => ''],
                ],
            ])
            ->assertRedirect(route('admin.programs.create'))
            ->assertSessionHasErrors('slots');
    }

    public function test_program_calendar_days_must_be_unique(): void
    {
        $admin = User::factory()->admin()->create();
        $workout = Workout::factory()->create();

        $this->actingAs($admin)
            ->from(route('admin.programs.create'))
            ->post(route('admin.programs.store'), [
                'title' => 'Doublon',
                'starts_on' => '2026-11-01',
                'ends_on' => '2026-11-28',
                'status' => ProgramStatus::Draft->value,
                'slots' => [
                    ['day_number' => 1, 'kind' => 'workout', 'workout_id' => $workout->id],
                    ['day_number' => 1, 'kind' => 'workout', 'workout_id' => $workout->id],
                ],
            ])
            ->assertRedirect(route('admin.programs.create'))
            ->assertSessionHasErrors('slots');
    }

    public function test_admin_can_update_a_program(): void
    {
        $admin = User::factory()->admin()->create();
        $workout = Workout::factory()->create();
        $program = Program::factory()->create(['title' => '4 semaines']);
        $program->schedule()->create([
            'day_number' => 1,
            'workout_id' => $workout->id,
        ]);

        $this->actingAs($admin)
            ->put(route('admin.programs.update', $program), [
                'title' => '8 semaines',
                'starts_on' => $program->starts_on->toDateString(),
                'ends_on' => $program->ends_on->toDateString(),
                'status' => ProgramStatus::Open->value,
                'slots' => [
                    ['day_number' => 2, 'kind' => 'workout', 'workout_id' => $workout->id],
                    ['day_number' => 3, 'kind' => 'rest'],
                ],
            ])
            ->assertRedirect(route('admin.programs.edit', $program));

        $program->refresh();

        $this->assertSame('8 semaines', $program->title);
        $this->assertSame(2, $program->schedule()->first()->day_number);
        $this->assertTrue($program->schedule()->orderBy('day_number')->get()[1]->isRest());
    }

    public function test_admin_can_archive_and_restore_a_program(): void
    {
        $admin = User::factory()->admin()->create();
        $program = Program::factory()->create(['title' => '4 semaines']);

        $this->actingAs($admin)
            ->delete(route('admin.programs.destroy', $program))
            ->assertRedirect(route('admin.programs.index'));

        $this->assertSoftDeleted($program);

        $this->actingAs($admin)
            ->post(route('admin.programs.restore', $program))
            ->assertRedirect(route('admin.programs.index'));

        $this->assertNotSoftDeleted($program);
    }

    public function test_public_program_page_is_visible_when_open(): void
    {
        $workout = Workout::factory()->create(['title' => 'Full body']);
        $program = Program::factory()->create(['title' => '4 semaines']);
        $program->schedule()->create([
            'day_number' => 1,
            'workout_id' => $workout->id,
        ]);
        $program->schedule()->create([
            'day_number' => 2,
            'workout_id' => null,
        ]);

        $this->get(route('programs.show', $program))
            ->assertOk()
            ->assertSee('4 semaines')
            ->assertSee('Full body')
            ->assertSee('Jour 1')
            ->assertSee('Repos');
    }

    public function test_draft_program_is_not_public(): void
    {
        $program = Program::factory()->draft()->create();

        $this->get(route('programs.show', $program))
            ->assertNotFound();
    }
}
