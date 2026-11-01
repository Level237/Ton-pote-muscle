<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminMemberTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_cannot_access_inscrits(): void
    {
        $member = User::factory()->member()->create();

        $this->actingAs($member)
            ->get(route('admin.enrollments.index'))
            ->assertForbidden();
    }

    public function test_admin_can_view_member_index(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->member()->create([
            'name' => 'Marie Dupont',
            'username' => 'marie',
            'email' => 'marie@example.com',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.enrollments.index'))
            ->assertOk()
            ->assertSee('Marie Dupont')
            ->assertSee('marie')
            ->assertSee('marie@example.com')
            ->assertSee('Désactiver')
            ->assertDontSee('Modifier');
    }

    public function test_admin_can_search_members(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->member()->create(['name' => 'Marie Dupont', 'username' => 'marie']);
        User::factory()->member()->create(['name' => 'Paul Martin', 'username' => 'paul']);

        $this->actingAs($admin)
            ->get(route('admin.enrollments.index', ['q' => 'marie']))
            ->assertOk()
            ->assertSee('Marie Dupont')
            ->assertDontSee('Paul Martin');
    }

    public function test_admin_index_does_not_list_admins(): void
    {
        $admin = User::factory()->admin()->create(['name' => 'Admin TPM']);
        User::factory()->member()->create(['name' => 'Marie Dupont']);

        $this->actingAs($admin)
            ->get(route('admin.enrollments.index'))
            ->assertOk()
            ->assertSee('Marie Dupont')
            ->assertDontSee('Admin TPM');
    }

    public function test_admin_can_deactivate_a_member(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->member()->create(['name' => 'Marie Dupont']);

        $this->actingAs($admin)
            ->post(route('admin.enrollments.deactivate', $member))
            ->assertRedirect(route('admin.enrollments.index'))
            ->assertSessionHas('status', 'Compte désactivé.');

        $this->assertNotNull($member->fresh()->deactivated_at);

        $this->actingAs($admin)
            ->get(route('admin.enrollments.index'))
            ->assertDontSee('Marie Dupont');

        $this->actingAs($admin)
            ->get(route('admin.enrollments.index', ['deactivated' => 1]))
            ->assertSee('Marie Dupont')
            ->assertSee('Réactiver');
    }

    public function test_admin_can_reactivate_a_member(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->member()->deactivated()->create(['name' => 'Marie Dupont']);

        $this->actingAs($admin)
            ->post(route('admin.enrollments.activate', $member))
            ->assertRedirect(route('admin.enrollments.index', ['deactivated' => 1]))
            ->assertSessionHas('status', 'Compte réactivé.');

        $this->assertNull($member->fresh()->deactivated_at);
    }

    public function test_admin_cannot_deactivate_an_admin_account(): void
    {
        $admin = User::factory()->admin()->create();
        $otherAdmin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('admin.enrollments.deactivate', $otherAdmin))
            ->assertNotFound();

        $this->assertNull($otherAdmin->fresh()->deactivated_at);
    }

    public function test_deactivated_member_cannot_log_in(): void
    {
        $member = User::factory()->member()->deactivated()->create();

        $this->post(route('login'), [
            'email' => $member->email,
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
        $this->assertStringContainsString(
            'désactivé',
            session('errors')->first('email'),
        );
    }

    public function test_deactivated_member_is_logged_out_from_dashboard(): void
    {
        $member = User::factory()->member()->create();

        $this->actingAs($member)
            ->get(route('dashboard'))
            ->assertOk();

        $member->update(['deactivated_at' => now()]);

        $this->actingAs($member)
            ->get(route('dashboard'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }
}
