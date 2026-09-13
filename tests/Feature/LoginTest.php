<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        RateLimiter::clear('jigar@example.com|127.0.0.1');
    }

    public function test_the_sign_in_page_renders(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Sign in')
            ->assertSee('noindex, nofollow', escape: false);
    }

    public function test_a_user_can_sign_in_and_lands_on_the_messages_list(): void
    {
        $user = User::factory()->create([
            'email' => 'jigar@example.com',
            'password' => 'a-known-password',
        ]);

        $this->post(route('login.store'), [
            'email' => 'jigar@example.com',
            'password' => 'a-known-password',
        ])->assertRedirect(route('messages.index'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_a_wrong_password_is_rejected(): void
    {
        User::factory()->create([
            'email' => 'jigar@example.com',
            'password' => 'a-known-password',
        ]);

        $this->post(route('login.store'), [
            'email' => 'jigar@example.com',
            'password' => 'not-the-password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_the_form_locks_out_after_five_failed_attempts(): void
    {
        User::factory()->create(['email' => 'jigar@example.com']);

        foreach (range(1, 5) as $ignored) {
            $this->post(route('login.store'), [
                'email' => 'jigar@example.com',
                'password' => 'wrong',
            ]);
        }

        $this->post(route('login.store'), [
            'email' => 'jigar@example.com',
            'password' => 'wrong',
        ])->assertSessionHasErrorsIn('default', ['email']);

        $this->assertStringContainsString(
            'Too many attempts',
            session('errors')->first('email'),
        );
    }

    public function test_a_signed_in_user_can_sign_out(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('logout'))
            ->assertRedirect(route('home'));

        $this->assertGuest();
    }

    public function test_a_signed_in_user_is_kept_away_from_the_sign_in_page(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('login'))
            ->assertRedirect(route('home'));
    }
}
