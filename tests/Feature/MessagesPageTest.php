<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MessagesPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_guest_is_sent_to_the_sign_in_page(): void
    {
        $this->get(route('messages.index'))->assertRedirect(route('login'));
    }

    public function test_a_guest_cannot_delete_a_message(): void
    {
        $message = ContactMessage::factory()->create();

        $this->delete(route('messages.destroy', $message))->assertRedirect(route('login'));

        $this->assertDatabaseHas('contact_messages', ['id' => $message->id]);
    }

    public function test_the_list_shows_messages_newest_first(): void
    {
        ContactMessage::factory()->create([
            'name' => 'Ada Lovelace',
            'created_at' => now()->subDay(),
        ]);
        ContactMessage::factory()->create([
            'name' => 'Grace Hopper',
            'created_at' => now(),
        ]);

        $response = $this->actingAs(User::factory()->create())
            ->get(route('messages.index'))
            ->assertOk()
            ->assertSee('Ada Lovelace')
            ->assertSee('Grace Hopper')
            ->assertSee('2 messages');

        $this->assertLessThan(
            strpos($response->getContent(), 'Ada Lovelace'),
            strpos($response->getContent(), 'Grace Hopper'),
        );
    }

    public function test_the_message_body_is_escaped(): void
    {
        ContactMessage::factory()->create(['message' => '<script>alert(1)</script>']);

        $this->actingAs(User::factory()->create())
            ->get(route('messages.index'))
            ->assertOk()
            ->assertDontSee('<script>alert(1)</script>', escape: false);
    }

    public function test_a_signed_in_user_can_delete_a_message(): void
    {
        $message = ContactMessage::factory()->create();

        $this->actingAs(User::factory()->create())
            ->from(route('messages.index'))
            ->delete(route('messages.destroy', $message))
            ->assertRedirect(route('messages.index'))
            ->assertSessionHas('message.deleted', true);

        $this->assertDatabaseMissing('contact_messages', ['id' => $message->id]);
    }

    public function test_the_list_pages_once_past_twenty_messages(): void
    {
        ContactMessage::factory()->count(21)->create();

        $this->actingAs(User::factory()->create())
            ->get(route('messages.index'))
            ->assertOk()
            ->assertSee('21 messages')
            ->assertSee('Older');
    }
}
