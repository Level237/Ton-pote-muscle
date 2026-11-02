<?php

namespace Tests\Feature;

use App\Enums\BilanQuestionType;
use App\Enums\EnrollmentStatus;
use App\Models\Bilan;
use App\Models\Program;
use App\Models\ProgramEnrollment;
use App\Models\User;
use App\Support\MemberProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberSpaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_sees_programs_given_by_the_coach(): void
    {
        $member = User::factory()->member()->create();
        $program = Program::factory()->create(['title' => 'Force maison']);
        ProgramEnrollment::factory()->create([
            'user_id' => $member->id,
            'program_id' => $program->id,
            'status' => EnrollmentStatus::Active,
        ]);

        $this->actingAs($member)
            ->get(route('member.programs.index'))
            ->assertOk()
            ->assertSee('Mes programmes')
            ->assertSee('Force maison');

        $this->actingAs($member)
            ->get(route('member.programs.show', $program))
            ->assertOk()
            ->assertSee('Force maison');
    }

    public function test_member_cannot_open_a_program_not_given(): void
    {
        $member = User::factory()->member()->create();
        $program = Program::factory()->create();

        $this->actingAs($member)
            ->get(route('member.programs.index'))
            ->assertOk()
            ->assertSee('Ton coach ne t’a pas encore donné de programme');

        $this->actingAs($member)
            ->get(route('member.programs.show', $program))
            ->assertNotFound();
    }

    public function test_new_member_is_sent_to_departure_bilan(): void
    {
        $bilan = Bilan::factory()->create([
            'title' => 'Bilan de départ',
            'slug' => Bilan::SLUG_DEPARTURE,
        ]);
        $question = $bilan->questions()->create([
            'position' => 1,
            'title' => 'Objectif principal',
            'type' => BilanQuestionType::Text,
        ]);

        $this->post(route('register'), [
            'name' => 'Paul Martin',
            'username' => 'paul',
            'email' => 'paul@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertRedirect(route('member.bilans.show', $bilan));

        $member = User::query()->where('email', 'paul@example.com')->first();

        $this->actingAs($member)
            ->get(route('dashboard'))
            ->assertRedirect(route('member.bilans.show', $bilan));

        $this->actingAs($member)
            ->get(route('member.bilans.show', $bilan))
            ->assertOk()
            ->assertSee('Suivant')
            ->assertSee('Objectif principal');

        $this->actingAs($member)
            ->post(route('member.bilans.store', $bilan), [
                'answers' => [
                    $question->id => 'Remise en forme',
                ],
            ])
            ->assertRedirect(route('dashboard'));

        $this->actingAs($member)
            ->get(route('dashboard'))
            ->assertOk();
    }

    public function test_admin_sees_member_bilan_answers(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->member()->create(['name' => 'Marie Dupont']);
        $bilan = Bilan::factory()->create(['title' => 'Bilan de départ', 'slug' => Bilan::SLUG_DEPARTURE]);
        $question = $bilan->questions()->create([
            'position' => 1,
            'title' => 'Objectif principal',
            'type' => BilanQuestionType::Text,
        ]);
        $submission = $member->bilanSubmissions()->create([
            'bilan_id' => $bilan->id,
            'submitted_at' => now(),
        ]);
        $submission->answers()->create([
            'bilan_question_id' => $question->id,
            'value' => 'Remise en forme',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.enrollments.show', $member))
            ->assertOk()
            ->assertSee('Bilans')
            ->assertSee('Bilan de départ')
            ->assertSee('Objectif principal')
            ->assertSee('Remise en forme');
    }

    public function test_member_can_fill_a_bilan(): void
    {
        $member = User::factory()->member()->create();
        $bilan = Bilan::factory()->create([
            'title' => 'Bilan de santé',
            'description' => 'À remplir avant de commencer.',
        ]);
        $choice = $bilan->questions()->create([
            'position' => 1,
            'title' => 'Blessure en cours ?',
            'type' => BilanQuestionType::Choice,
            'options' => ['Non', 'Oui'],
        ]);
        $rating = $bilan->questions()->create([
            'position' => 2,
            'title' => 'Énergie',
            'type' => BilanQuestionType::Rating,
        ]);
        $number = $bilan->questions()->create([
            'position' => 3,
            'title' => 'Poids',
            'type' => BilanQuestionType::Number,
        ]);
        $text = $bilan->questions()->create([
            'position' => 4,
            'title' => 'Précisions',
            'type' => BilanQuestionType::Text,
        ]);

        $this->actingAs($member)
            ->get(route('member.bilans.index'))
            ->assertOk()
            ->assertSee('Bilan de santé')
            ->assertSee('À remplir');

        $this->actingAs($member)
            ->post(route('member.bilans.store', $bilan), [
                'answers' => [
                    $choice->id => 'Non',
                    $rating->id => '4',
                    $number->id => '72.5',
                    $text->id => 'RAS',
                ],
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('bilan_submissions', [
            'user_id' => $member->id,
            'bilan_id' => $bilan->id,
        ]);
        $this->assertDatabaseHas('bilan_answers', [
            'bilan_question_id' => $choice->id,
            'value' => 'Non',
        ]);

        $this->actingAs($member)
            ->get(route('member.bilans.index'))
            ->assertSee('Répondu');

        $this->actingAs($member)
            ->get(route('member.bilans.show', $bilan))
            ->assertOk()
            ->assertSee('RAS')
            ->assertSee('Suivant');
    }

    public function test_member_can_update_profile(): void
    {
        $member = User::factory()->member()->create([
            'name' => 'Marie Dupont',
            'username' => 'marie',
        ]);

        $this->actingAs($member)
            ->get(route('member.profile.edit'))
            ->assertOk()
            ->assertSee('Profil')
            ->assertSee('Marie Dupont');

        $this->actingAs($member)
            ->put(route('member.profile.update'), [
                'name' => 'Marie Martin',
                'username' => 'marie-m',
                'gender' => 'feminin',
                'height_cm' => 168,
                'weight_kg' => 62.4,
                'main_goal' => 'fitness',
                'fitness_level' => 'regular',
                'activity_level' => 'moderately',
                'motivations' => ['health', 'energy'],
            ])
            ->assertRedirect(route('member.profile.edit'));

        $member->refresh();

        $this->assertSame('Marie Martin', $member->name);
        $this->assertSame('marie-m', $member->username);
        $this->assertSame('feminin', $member->gender);
        $this->assertSame(168, $member->height_cm);
        $this->assertSame(['health', 'energy'], $member->motivations);
        $this->assertArrayHasKey('fitness', MemberProfile::GOALS);
    }
}
