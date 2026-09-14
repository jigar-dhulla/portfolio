<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // The email rule checks for an MX record. Fake the lookup so the suite
        // never depends on DNS; the toggle itself is covered below.
        Validator::fakeDnsLookups();
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Ada Lovelace',
            'email' => 'ada@example.com',
            'message' => 'I would like to talk about a Laravel project.',
        ], $overrides);
    }

    public function test_a_message_is_stored_and_the_visitor_is_sent_back_to_the_form(): void
    {
        $response = $this->post(route('contact.store'), $this->validPayload());

        $response->assertRedirect(route('home').'#contact');
        $response->assertSessionHas('contact.sent', true);

        $this->assertDatabaseHas('contact_messages', [
            'name' => 'Ada Lovelace',
            'email' => 'ada@example.com',
        ]);
    }

    public function test_the_confirmation_is_shown_after_the_redirect(): void
    {
        $this->followingRedirects()
            ->post(route('contact.store'), $this->validPayload())
            ->assertSee('Thanks. I will reply within a day.');
    }

    public function test_every_field_is_required(): void
    {
        $response = $this->post(route('contact.store'), []);

        $response->assertSessionHasErrors(['name', 'email', 'message']);
        $this->assertSame(0, ContactMessage::count());
    }

    public function test_validation_errors_send_the_visitor_back_to_the_form(): void
    {
        $this->post(route('contact.store'), [])->assertRedirect('/#contact');
    }

    /** @return array<string, array{string}> */
    public static function rejectedNames(): array
    {
        return [
            'too short' => ['A'],
            'longer than the column' => [str_repeat('a', 101)],
            'contains a digit' => ['Ada L0velace'],
            'contains a url' => ['https://spam.example'],
            'contains a bare host' => ['www.spam.example'],
            'starts with punctuation' => ['-Ada'],
            'contains markup' => ['<script>alert(1)</script>'],
            'contains an email address' => ['ada@example.com'],
        ];
    }

    #[DataProvider('rejectedNames')]
    public function test_a_name_that_is_not_a_name_is_rejected(string $name): void
    {
        $this->post(route('contact.store'), $this->validPayload(['name' => $name]))
            ->assertSessionHasErrors('name');

        $this->assertSame(0, ContactMessage::count());
    }

    /** @return array<string, array{string}> */
    public static function acceptedNames(): array
    {
        return [
            'plain' => ['Jigar Dhulla'],
            'accented' => ['Renée Björk'],
            'devanagari' => ['जिगर धुल्ला'],
            'chinese' => ['李雷'],
            'hyphenated' => ['Jean-Luc Picard'],
            'plain apostrophe' => ["Ada O'Brien"],
            'typographic apostrophe' => ['Ada O’Brien'],
            'with an initial' => ['Ada B. Lovelace'],
        ];
    }

    #[DataProvider('acceptedNames')]
    public function test_a_real_name_is_accepted(string $name): void
    {
        $this->post(route('contact.store'), $this->validPayload(['name' => $name]))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('contact_messages', ['name' => $name]);
    }

    public function test_runs_of_whitespace_in_a_name_are_collapsed(): void
    {
        $this->post(route('contact.store'), $this->validPayload(['name' => "Ada    \t Lovelace"]))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('contact_messages', ['name' => 'Ada Lovelace']);
    }

    /** @return array<string, array{string}> */
    public static function rejectedEmails(): array
    {
        return [
            'no domain' => ['ada'],
            'no local part' => ['@example.com'],
            'double at' => ['ada@@example.com'],
            'space inside' => ['ada lovelace@example.com'],
            'trailing dot' => ['ada@example.com.'],
            'longer than rfc allows' => [str_repeat('a', 250).'@example.com'],
        ];
    }

    #[DataProvider('rejectedEmails')]
    public function test_a_malformed_email_is_rejected(string $email): void
    {
        $this->post(route('contact.store'), $this->validPayload(['email' => $email]))
            ->assertSessionHasErrors('email');

        $this->assertSame(0, ContactMessage::count());
    }

    public function test_the_email_domain_is_not_looked_up_when_the_check_is_switched_off(): void
    {
        Validator::fakeDnsLookups(false);
        config(['portfolio.contact.verify_email_domain' => false]);

        $this->post(route('contact.store'), $this->validPayload(['email' => 'ada@no-mx-record.test']))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('contact_messages', ['email' => 'ada@no-mx-record.test']);
    }

    public function test_a_message_shorter_than_twenty_characters_is_rejected(): void
    {
        $this->post(route('contact.store'), $this->validPayload(['message' => 'Call me']))
            ->assertSessionHasErrors('message');

        $this->assertSame(0, ContactMessage::count());
    }

    public function test_a_message_longer_than_the_column_is_rejected(): void
    {
        $this->post(route('contact.store'), $this->validPayload(['message' => str_repeat('a', 5001)]))
            ->assertSessionHasErrors('message');

        $this->assertSame(0, ContactMessage::count());
    }

    public function test_a_message_carrying_a_pile_of_links_is_rejected(): void
    {
        $message = 'Cheap backlinks here: https://a.example https://b.example www.c.example';

        $this->post(route('contact.store'), $this->validPayload(['message' => $message]))
            ->assertSessionHasErrors('message');

        $this->assertSame(0, ContactMessage::count());
    }

    public function test_a_message_mentioning_a_couple_of_links_is_accepted(): void
    {
        $message = 'My repo is at https://github.com/example and the site is www.example.org, can you help?';

        $this->post(route('contact.store'), $this->validPayload(['message' => $message]))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('contact_messages', ['message' => $message]);
    }

    public function test_a_message_containing_control_characters_is_rejected(): void
    {
        $this->post(route('contact.store'), $this->validPayload(['message' => "Hello there\x00, I have a project."]))
            ->assertSessionHasErrors('message');

        $this->assertSame(0, ContactMessage::count());
    }

    public function test_a_message_spanning_several_lines_is_accepted(): void
    {
        $message = "Hello Jigar,\r\n\r\nI have a Laravel project that needs help.\n\nThanks.";

        $this->post(route('contact.store'), $this->validPayload(['message' => $message]))
            ->assertSessionHasNoErrors();

        $this->assertSame(1, ContactMessage::count());
    }

    public function test_a_filled_honeypot_is_rejected(): void
    {
        $response = $this->post(route('contact.store'), $this->validPayload(['website' => 'https://spam.example']));

        $response->assertSessionHasErrors('website');
        $this->assertSame(0, ContactMessage::count());
    }

    public function test_the_form_is_rate_limited(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('contact.store'), $this->validPayload())->assertRedirect();
        }

        $this->post(route('contact.store'), $this->validPayload())->assertStatus(429);
        $this->assertSame(5, ContactMessage::count());
    }
}
