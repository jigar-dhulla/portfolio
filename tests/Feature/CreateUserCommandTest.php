<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CreateUserCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_user_and_prints_a_working_password_once(): void
    {
        $this->artisan('user:create --name=Jigar --email=jigar@example.com')
            ->assertExitCode(0)
            ->run();

        $user = User::firstWhere('email', 'jigar@example.com');

        $this->assertNotNull($user);
        $this->assertSame('Jigar', $user->name);

        // The password is hashed at rest, so the printed value is the only copy.
        $this->assertNotSame($user->password, 'Jigar');
        $this->assertTrue(Hash::isHashed($user->password));
    }

    public function test_the_printed_password_actually_signs_the_user_in(): void
    {
        $output = $this->artisanOutputFor('user:create --name=Second --email=second@example.com');

        preg_match('/Password:\s*(\S+)/', $output, $matches);

        $this->assertNotEmpty($matches[1] ?? null, 'The command did not print a password.');

        $this->post(route('login.store'), [
            'email' => 'second@example.com',
            'password' => $matches[1],
        ])->assertRedirect(route('messages.index'));

        $this->assertAuthenticated();
    }

    public function test_it_refuses_a_duplicate_email(): void
    {
        User::factory()->create(['email' => 'jigar@example.com']);

        $this->artisan('user:create --name=Jigar --email=jigar@example.com')
            ->expectsOutputToContain('The email has already been taken.')
            ->assertExitCode(1);
    }

    public function test_it_refuses_a_missing_email(): void
    {
        $this->artisan('user:create --name=Jigar --no-interaction')
            ->assertExitCode(1);
    }

    /**
     * Run a command and capture everything it wrote, which the pending-command
     * assertions do not expose.
     */
    private function artisanOutputFor(string $command): string
    {
        Artisan::call($command);

        return Artisan::output();
    }
}
