<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListContactMessagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_reports_when_there_are_no_messages(): void
    {
        $this->artisan('contact:messages')
            ->expectsOutputToContain('No contact messages yet.')
            ->assertExitCode(0);
    }

    public function test_it_lists_the_newest_messages_first(): void
    {
        ContactMessage::factory()->create([
            'name' => 'Ada Lovelace',
            'created_at' => now()->subDay(),
        ]);
        ContactMessage::factory()->create([
            'name' => 'Grace Hopper',
            'created_at' => now(),
        ]);

        $this->artisan('contact:messages')
            ->expectsOutputToContain('Grace Hopper')
            ->expectsOutputToContain('Ada Lovelace')
            ->expectsOutputToContain('Showing 2 of 2 message(s).')
            ->assertExitCode(0);
    }

    public function test_the_limit_option_caps_the_rows_but_the_total_still_counts_everything(): void
    {
        ContactMessage::factory()->count(3)->create();

        $this->artisan('contact:messages --limit=1')
            ->expectsOutputToContain('Showing 1 of 3 message(s).')
            ->assertExitCode(0);
    }

    public function test_a_limit_below_one_is_rejected(): void
    {
        $this->artisan('contact:messages --limit=0')
            ->expectsOutputToContain('--limit must be at least 1.')
            ->assertExitCode(1);
    }

    public function test_it_shows_a_single_message_in_full(): void
    {
        $body = 'A message long enough that the summary table would truncate it well before the end of this sentence.';

        $message = ContactMessage::factory()->create([
            'name' => 'Ada Lovelace',
            'email' => 'ada@example.com',
            'message' => $body,
        ]);

        $this->artisan("contact:messages --id={$message->id}")
            ->expectsOutputToContain('Ada Lovelace <ada@example.com>')
            ->expectsOutputToContain($body)
            ->assertExitCode(0);
    }

    public function test_it_fails_for_an_unknown_id(): void
    {
        $this->artisan('contact:messages --id=404')
            ->expectsOutputToContain('No contact message found with id 404.')
            ->assertExitCode(1);
    }
}
