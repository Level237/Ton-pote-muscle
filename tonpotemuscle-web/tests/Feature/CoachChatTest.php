<?php

namespace Tests\Feature;

use App\Models\CoachMessage;
use App\Models\User;
use App\Support\CoachChat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoachChatTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_cannot_open_admin_inbox(): void
    {
        $member = User::factory()->member()->create();

        $this->actingAs($member)
            ->get(route('admin.messages.index'))
            ->assertForbidden();
    }

    public function test_admin_can_list_members_and_start_a_thread(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->member()->create(['name' => 'Marie Dupont']);

        $this->actingAs($admin)
            ->get(route('admin.messages.index'))
            ->assertOk()
            ->assertSee('Marie Dupont')
            ->assertSee('Lancer la discussion');

        $this->actingAs($admin)
            ->post(route('admin.messages.store', $member), [
                'body' => 'Pense à tes haltères demain.',
            ])
            ->assertRedirect(route('admin.messages.show', $member));

        $this->actingAs($admin)
            ->get(route('admin.messages.show', $member))
            ->assertOk()
            ->assertSee('Pense à tes haltères demain.');

        $this->assertDatabaseHas('coach_messages', [
            'member_id' => $member->id,
            'author_id' => $admin->id,
            'from_member' => false,
            'body' => 'Pense à tes haltères demain.',
        ]);
    }

    public function test_member_sees_unread_badge_then_marks_as_read(): void
    {
        $admin = User::factory()->admin()->create(['name' => 'Coach']);
        $member = User::factory()->member()->create();

        CoachChat::send($member, $admin, 'Hydrate-toi.');

        $this->actingAs($member)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('1 message non lu');

        $this->actingAs($member)
            ->get(route('member.messages.show'))
            ->assertOk()
            ->assertSee('Hydrate-toi.')
            ->assertSee('Coach');

        $this->assertNotNull(CoachMessage::query()->first()->read_at);

        $this->actingAs($member)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('1 message non lu');
    }

    public function test_member_reply_shows_unread_for_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->member()->create(['name' => 'Marie Dupont']);

        $this->actingAs($member)
            ->post(route('member.messages.store'), [
                'body' => 'Les 12 kg passent bien.',
            ])
            ->assertRedirect(route('member.messages.show'));

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('1 message non lu');

        $this->actingAs($admin)
            ->get(route('admin.messages.index'))
            ->assertOk()
            ->assertSee('Les 12 kg passent bien.')
            ->assertSee('1 message non lu');

        $this->actingAs($admin)
            ->get(route('admin.messages.show', $member))
            ->assertOk()
            ->assertSee('Les 12 kg passent bien.');

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertDontSee('1 message non lu');
    }

    public function test_admin_cannot_open_a_thread_with_another_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $other = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.messages.show', $other))
            ->assertNotFound();
    }

    public function test_empty_message_is_rejected(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->member()->create();

        $this->actingAs($admin)
            ->from(route('admin.messages.show', $member))
            ->post(route('admin.messages.store', $member), [
                'body' => '   ',
            ])
            ->assertRedirect(route('admin.messages.show', $member))
            ->assertSessionHasErrors('body');
    }

    public function test_unread_filter_hides_members_without_new_messages(): void
    {
        $admin = User::factory()->admin()->create();
        $marie = User::factory()->member()->create(['name' => 'Marie Dupont']);
        User::factory()->member()->create(['name' => 'Paul Martin']);

        CoachChat::send($marie, $marie, 'Question sur le dos.');

        $this->actingAs($admin)
            ->get(route('admin.messages.index', ['unread' => 1]))
            ->assertOk()
            ->assertSee('Marie Dupont')
            ->assertDontSee('Paul Martin');
    }

    public function test_admin_thread_renders_split_inbox_and_read_receipt(): void
    {
        $admin = User::factory()->admin()->create(['name' => 'Coach']);
        $marie = User::factory()->member()->create(['name' => 'Marie Dupont', 'username' => 'marie']);
        $paul = User::factory()->member()->create(['name' => 'Paul Martin']);

        $sent = CoachChat::send($marie, $admin, 'Garde le dos droit.');
        $sent->forceFill(['read_at' => now()])->save();

        $this->actingAs($admin)
            ->get(route('admin.messages.show', $marie))
            ->assertOk()
            ->assertSee('Marie Dupont')
            ->assertSee('@marie')
            ->assertSee('Paul Martin')
            ->assertSee('Garde le dos droit.')
            ->assertSee("Aujourd'hui")
            ->assertSee('Lu');
    }
}
