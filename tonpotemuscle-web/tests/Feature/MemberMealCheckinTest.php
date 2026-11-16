<?php

namespace Tests\Feature;

use App\Models\MealLog;
use App\Models\User;
use App\Support\MemberMeals;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberMealCheckinTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_without_plan_sees_empty_state(): void
    {
        $member = User::factory()->member()->create();

        $this->actingAs($member)
            ->get(route('dashboard', ['suivi' => 'repas']))
            ->assertOk()
            ->assertSee('Ton coach ne t’a pas encore donné de repas')
            ->assertDontSee('Tenu');
    }

    public function test_coach_can_give_meals_and_member_checks_in(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->member()->create();

        $this->actingAs($admin)
            ->put(route('admin.enrollments.meals.update', $member), [
                'breakfast' => 'Flocons + œufs',
                'lunch' => 'Poulet riz brocoli',
                'dinner' => '',
            ])
            ->assertRedirect(route('admin.enrollments.show', ['user' => $member, 'suivi' => 'repas']).'#suivi');

        $this->actingAs($member)
            ->get(route('dashboard', ['suivi' => 'repas']))
            ->assertOk()
            ->assertSee('Flocons + œufs')
            ->assertSee('Poulet riz brocoli')
            ->assertDontSee('Soir');

        $this->actingAs($member)
            ->post(route('member.meals.store'), [
                'meal' => 'lunch',
                'status' => 'tenu',
            ])
            ->assertRedirect(route('dashboard', ['suivi' => 'repas']).'#suivi');

        $this->assertDatabaseHas('meal_logs', [
            'user_id' => $member->id,
            'day' => now()->toDateString(),
            'lunch' => 'tenu',
        ]);
    }

    public function test_member_cannot_check_in_a_slot_that_was_not_given(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->member()->create();
        MemberMeals::give($member, $admin, ['lunch' => 'Salade']);

        $this->actingAs($member)
            ->from(route('dashboard', ['suivi' => 'repas']))
            ->post(route('member.meals.store'), [
                'meal' => 'breakfast',
                'status' => 'tenu',
            ])
            ->assertRedirect(route('dashboard', ['suivi' => 'repas']))
            ->assertSessionHasErrors('meal');
    }

    public function test_checking_the_same_status_clears_the_slot(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->member()->create();
        MemberMeals::give($member, $admin, ['breakfast' => 'Skyr']);

        $this->actingAs($member)->post(route('member.meals.store'), [
            'meal' => 'breakfast',
            'status' => 'ecart',
        ]);
        $this->actingAs($member)->post(route('member.meals.store'), [
            'meal' => 'breakfast',
            'status' => 'ecart',
        ]);

        $this->assertNull(MealLog::query()->where('user_id', $member->id)->value('breakfast'));
    }

    public function test_coach_sees_follow_up_on_given_meals(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->member()->create();
        MemberMeals::give($member, $admin, ['dinner' => 'Omelette légumes']);
        MemberMeals::checkIn($member, 'dinner', 'approx');
        MemberMeals::note($member, 'Léger ce soir');

        $this->actingAs($admin)
            ->get(route('admin.enrollments.show', ['user' => $member, 'suivi' => 'repas']))
            ->assertOk()
            ->assertSee('Omelette légumes')
            ->assertSee('À peu près')
            ->assertSee('Léger ce soir')
            ->assertSee('Mettre à jour les repas')
            ->assertDontSee('Retap sur le même état');
    }

    public function test_coach_must_give_at_least_one_meal(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->member()->create();

        $this->actingAs($admin)
            ->from(route('admin.enrollments.show', ['user' => $member, 'suivi' => 'repas']))
            ->put(route('admin.enrollments.meals.update', $member), [
                'breakfast' => '',
                'lunch' => '',
                'dinner' => '',
            ])
            ->assertRedirect(route('admin.enrollments.show', ['user' => $member, 'suivi' => 'repas']))
            ->assertSessionHasErrors('lunch');
    }
}
