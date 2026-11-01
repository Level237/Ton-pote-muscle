<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_is_displayed(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Inscription');
    }

    public function test_member_can_register(): void
    {
        $this->post(route('register'), [
            'name' => 'Marie Dupont',
            'username' => 'marie',
            'email' => 'marie@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'marie@example.com',
            'username' => 'marie',
            'role' => UserRole::Member->value,
        ]);
    }

    public function test_member_can_log_in(): void
    {
        $user = User::factory()->member()->create();

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_admin_cannot_log_in_on_member_form(): void
    {
        $admin = User::factory()->admin()->create();

        $this->post(route('login'), [
            'email' => $admin->email,
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_member_cannot_log_in_on_admin_form(): void
    {
        $member = User::factory()->member()->create();

        $this->post(route('admin.login'), [
            'email' => $member->email,
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_admin_can_log_in(): void
    {
        $admin = User::factory()->admin()->create();

        $this->post(route('admin.login'), [
            'email' => $admin->email,
            'password' => 'password',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($admin);
    }

    public function test_admin_dashboard_shows_sidebar_options(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Tableau de bord')
            ->assertSee('Exercices')
            ->assertSee('Séances')
            ->assertSee('Programmes')
            ->assertSee('Bilans')
            ->assertSee('Inscrits')
            ->assertSee('Classements');
    }

    public function test_member_cannot_access_admin_dashboard(): void
    {
        $member = User::factory()->member()->create();

        $this->actingAs($member)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_admin_cannot_access_member_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertForbidden();
    }

    public function test_guest_is_redirected_from_member_dashboard_to_login(): void
    {
        $this->get(route('dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_guest_is_redirected_from_admin_dashboard_to_admin_login(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_member_login_is_rate_limited(): void
    {
        $user = User::factory()->member()->create();

        foreach (range(1, 5) as $attempt) {
            $this->post(route('login'), [
                'email' => $user->email,
                'password' => 'wrong-password',
            ])->assertSessionHasErrors('email');
        }

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('email');

        $this->assertStringContainsString(
            'Trop de tentatives',
            session('errors')->first('email'),
        );
    }

    public function test_member_can_log_out(): void
    {
        $member = User::factory()->member()->create();

        $this->actingAs($member)
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }
}
