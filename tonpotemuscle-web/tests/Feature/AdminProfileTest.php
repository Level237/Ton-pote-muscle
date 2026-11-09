<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_profile(): void
    {
        $admin = User::factory()->admin()->create([
            'name' => 'Admin TPM',
            'username' => 'admin',
            'email' => 'admin@tonpotemuscle.test',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.profile'))
            ->assertOk()
            ->assertSee('Profil')
            ->assertSee('Admin TPM')
            ->assertSee('admin@tonpotemuscle.test');
    }

    public function test_admin_can_update_profile(): void
    {
        $admin = User::factory()->admin()->create([
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@tonpotemuscle.test',
        ]);

        $this->actingAs($admin)
            ->put(route('admin.profile.update'), [
                'name' => 'Coach TPM',
                'username' => 'coach',
                'email' => 'coach@tonpotemuscle.test',
                'phone' => '690394365',
            ])
            ->assertRedirect(route('admin.profile'))
            ->assertSessionHas('status', 'Profil enregistré.');

        $admin->refresh();

        $this->assertSame('Coach TPM', $admin->name);
        $this->assertSame('coach', $admin->username);
        $this->assertSame('coach@tonpotemuscle.test', $admin->email);
        $this->assertSame('690394365', $admin->phone);
        $this->assertTrue(Hash::check('password', $admin->password));
    }

    public function test_admin_can_change_password(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->put(route('admin.profile.update'), [
                'name' => $admin->name,
                'username' => $admin->username,
                'email' => $admin->email,
                'current_password' => 'password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ])
            ->assertRedirect(route('admin.profile'));

        $this->assertTrue(Hash::check('new-password', $admin->fresh()->password));
    }

    public function test_member_cannot_access_admin_profile(): void
    {
        $member = User::factory()->member()->create();

        $this->actingAs($member)
            ->get(route('admin.profile'))
            ->assertForbidden();
    }
}
