<?php

namespace Tests\Feature;

use App\Enums\ProgramStatus;
use App\Models\Program;
use App\Models\ProgramEnrollment;
use App\Models\RankingSnapshot;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRankingTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_cannot_access_rankings(): void
    {
        $member = User::factory()->member()->create();

        $this->actingAs($member)
            ->get(route('admin.rankings.index'))
            ->assertForbidden();
    }

    public function test_admin_index_lists_programs_with_rankings(): void
    {
        $admin = User::factory()->admin()->create();
        $open = Program::factory()->create(['title' => '4 semaines']);
        Program::factory()->draft()->create(['title' => 'Brouillon secret']);
        ProgramEnrollment::factory()->count(2)->create(['program_id' => $open->id]);

        $this->actingAs($admin)
            ->get(route('admin.rankings.index'))
            ->assertOk()
            ->assertSee('4 semaines')
            ->assertSee('2 inscrits')
            ->assertSee('Provisoire')
            ->assertDontSee('Brouillon secret');
    }

    public function test_admin_can_search_ranking_programs(): void
    {
        $admin = User::factory()->admin()->create();
        Program::factory()->create(['title' => '4 semaines']);
        Program::factory()->create(['title' => 'Cut']);

        $this->actingAs($admin)
            ->get(route('admin.rankings.index', ['q' => 'semaine']))
            ->assertOk()
            ->assertSee('4 semaines')
            ->assertDontSee('Cut');
    }

    public function test_ranking_is_ordered_by_points_then_lives_then_enrollment_date(): void
    {
        $admin = User::factory()->admin()->create();
        $program = Program::factory()->create(['title' => '4 semaines']);

        $third = User::factory()->member()->create(['username' => 'clara']);
        $first = User::factory()->member()->create(['username' => 'alice']);
        $second = User::factory()->member()->create(['username' => 'bruno']);

        ProgramEnrollment::factory()->create([
            'program_id' => $program->id,
            'user_id' => $third->id,
            'points' => 10,
            'live_count' => 1,
            'enrolled_at' => now()->subDays(3),
        ]);
        ProgramEnrollment::factory()->create([
            'program_id' => $program->id,
            'user_id' => $first->id,
            'points' => 20,
            'live_count' => 1,
            'enrolled_at' => now()->subDay(),
        ]);
        ProgramEnrollment::factory()->create([
            'program_id' => $program->id,
            'user_id' => $second->id,
            'points' => 20,
            'live_count' => 2,
            'enrolled_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.rankings.show', $program))
            ->assertOk()
            ->assertSeeInOrder(['bruno', 'alice', 'clara'])
            ->assertSee('Provisoire')
            ->assertDontSee($first->email);
    }

    public function test_abandoned_enrollments_are_excluded_from_live_ranking(): void
    {
        $admin = User::factory()->admin()->create();
        $program = Program::factory()->create();
        $active = User::factory()->member()->create(['username' => 'alice']);
        $left = User::factory()->member()->create(['username' => 'bruno']);

        ProgramEnrollment::factory()->create([
            'program_id' => $program->id,
            'user_id' => $active->id,
            'points' => 6,
        ]);
        ProgramEnrollment::factory()->abandoned()->create([
            'program_id' => $program->id,
            'user_id' => $left->id,
            'points' => 40,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.rankings.show', $program))
            ->assertOk()
            ->assertSee('alice')
            ->assertDontSee('bruno');
    }

    public function test_draft_program_ranking_is_not_found(): void
    {
        $admin = User::factory()->admin()->create();
        $program = Program::factory()->draft()->create();

        $this->actingAs($admin)
            ->get(route('admin.rankings.show', $program))
            ->assertNotFound();
    }

    public function test_admin_can_publish_ranking_and_freeze_it(): void
    {
        $admin = User::factory()->admin()->create();
        $program = Program::factory()->create(['title' => '4 semaines']);
        $member = User::factory()->member()->create(['username' => 'alice']);

        $enrollment = ProgramEnrollment::factory()->create([
            'program_id' => $program->id,
            'user_id' => $member->id,
            'points' => 10,
            'live_count' => 1,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.rankings.publish', $program))
            ->assertRedirect(route('admin.rankings.show', $program))
            ->assertSessionHas('status', 'Classement publié.');

        $program->refresh();
        $this->assertSame(ProgramStatus::Closed, $program->status);
        $this->assertNotNull($program->closed_at);
        $this->assertTrue(RankingSnapshot::query()->where('program_id', $program->id)->exists());

        $enrollment->update(['points' => 99]);

        $this->actingAs($admin)
            ->get(route('admin.rankings.show', $program))
            ->assertOk()
            ->assertSee('alice')
            ->assertSee('10')
            ->assertDontSee('99')
            ->assertSee('Publié');
    }

    public function test_admin_cannot_publish_ranking_twice(): void
    {
        $admin = User::factory()->admin()->create();
        $program = Program::factory()->create();
        ProgramEnrollment::factory()->create(['program_id' => $program->id]);

        $this->actingAs($admin)
            ->post(route('admin.rankings.publish', $program))
            ->assertRedirect();

        $this->actingAs($admin)
            ->post(route('admin.rankings.publish', $program))
            ->assertNotFound();
    }

    public function test_admin_can_export_ranking_csv(): void
    {
        $admin = User::factory()->admin()->create();
        $program = Program::factory()->create(['title' => '4 semaines']);
        $member = User::factory()->member()->create(['username' => 'alice']);
        ProgramEnrollment::factory()->create([
            'program_id' => $program->id,
            'user_id' => $member->id,
            'points' => 12,
            'live_count' => 1,
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.rankings.export', $program));

        $response->assertOk();
        $response->assertHeader('content-disposition');

        $csv = $response->streamedContent();

        $this->assertStringContainsString('Rang', $csv);
        $this->assertStringContainsString('alice', $csv);
        $this->assertStringContainsString('12', $csv);
        $this->assertStringNotContainsString($member->email, $csv);
    }
}
