<?php

namespace Tests\Feature;

use App\Enums\BilanQuestionType;
use App\Models\Bilan;
use App\Models\User;
use Database\Seeders\BilanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminBilanTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_cannot_access_bilans(): void
    {
        $member = User::factory()->member()->create();

        $this->actingAs($member)
            ->get(route('admin.bilans.index'))
            ->assertForbidden();
    }

    public function test_admin_can_view_bilan_index(): void
    {
        $admin = User::factory()->admin()->create();
        Bilan::factory()->create(['title' => 'Bilan de santé']);

        $this->actingAs($admin)
            ->get(route('admin.bilans.index'))
            ->assertOk()
            ->assertSee('Bilan de santé')
            ->assertSee('Nouveau bilan');
    }

    public function test_admin_can_create_a_bilan_with_questions(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('admin.bilans.store'), [
                'title' => 'Bilan de départ',
                'description' => 'Objectifs et disponibilités.',
                'questions' => [
                    [
                        'title' => 'Objectif principal',
                        'type' => BilanQuestionType::Choice->value,
                        'options' => "Perte de poids\nPrise de muscle",
                    ],
                    [
                        'title' => 'Décrivez votre objectif',
                        'type' => BilanQuestionType::Text->value,
                        'options' => '',
                    ],
                    [
                        'title' => 'Motivation',
                        'type' => BilanQuestionType::Rating->value,
                        'options' => '',
                    ],
                ],
            ])
            ->assertRedirect(route('admin.bilans.index'));

        $bilan = Bilan::query()->where('title', 'Bilan de départ')->first();

        $this->assertNotNull($bilan);
        $this->assertCount(3, $bilan->questions);
        $this->assertSame(BilanQuestionType::Choice, $bilan->questions[0]->type);
        $this->assertSame(['Perte de poids', 'Prise de muscle'], $bilan->questions[0]->options);
        $this->assertSame(BilanQuestionType::Text, $bilan->questions[1]->type);
        $this->assertSame(BilanQuestionType::Rating, $bilan->questions[2]->type);
    }

    public function test_choice_question_requires_at_least_two_options(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->from(route('admin.bilans.create'))
            ->post(route('admin.bilans.store'), [
                'title' => 'Incomplet',
                'questions' => [
                    [
                        'title' => 'Lieu',
                        'type' => BilanQuestionType::Choice->value,
                        'options' => 'Salle',
                    ],
                ],
            ])
            ->assertRedirect(route('admin.bilans.create'))
            ->assertSessionHasErrors('questions.0.options');
    }

    public function test_admin_can_update_a_bilan(): void
    {
        $admin = User::factory()->admin()->create();
        $bilan = Bilan::factory()->create(['title' => 'Santé']);
        $bilan->questions()->create([
            'position' => 1,
            'title' => 'Douleur',
            'type' => BilanQuestionType::Text,
        ]);

        $this->actingAs($admin)
            ->put(route('admin.bilans.update', $bilan), [
                'title' => 'Bilan de santé',
                'questions' => [
                    [
                        'title' => 'Énergie',
                        'type' => BilanQuestionType::Rating->value,
                    ],
                    [
                        'title' => 'Sommeil heures',
                        'type' => BilanQuestionType::Number->value,
                    ],
                ],
            ])
            ->assertRedirect(route('admin.bilans.edit', $bilan));

        $bilan->refresh();

        $this->assertSame('Bilan de santé', $bilan->title);
        $this->assertCount(2, $bilan->questions);
        $this->assertSame('Énergie', $bilan->questions[0]->title);
        $this->assertSame(BilanQuestionType::Number, $bilan->questions[1]->type);
    }

    public function test_admin_can_archive_and_restore_a_bilan(): void
    {
        $admin = User::factory()->admin()->create();
        $bilan = Bilan::factory()->create(['title' => 'Bilan de santé']);

        $this->actingAs($admin)
            ->delete(route('admin.bilans.destroy', $bilan))
            ->assertRedirect(route('admin.bilans.index'));

        $this->assertSoftDeleted($bilan);

        $this->actingAs($admin)
            ->post(route('admin.bilans.restore', $bilan))
            ->assertRedirect(route('admin.bilans.index'));

        $this->assertNotSoftDeleted($bilan);
    }

    public function test_bilan_seeder_creates_mesure_questionnaire(): void
    {
        $this->seed(BilanSeeder::class);

        $bilan = Bilan::query()->where('title', 'Bilan de mesure')->first();

        $this->assertNotNull($bilan);
        $this->assertCount(8, $bilan->questions);
        $this->assertSame('Quel est votre poids actuel', $bilan->questions[0]->title);
        $this->assertSame(BilanQuestionType::Number, $bilan->questions[0]->type);
        $this->assertSame('Tour de cuisse (cm)', $bilan->questions[5]->title);
        $this->assertSame(BilanQuestionType::Text, $bilan->questions[6]->type);
        $this->assertSame('Taille', $bilan->questions[7]->title);
    }
}
