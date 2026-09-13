<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

#[Signature('user:create {--name= : The name to store} {--email= : The address used to sign in}')]
#[Description('Create a user who can sign in to read contact messages')]
class CreateUser extends Command
{
    /**
     * Generated password length. Letters and digits only: it is long enough
     * to be strong without symbols, and it gets typed on a phone keyboard.
     */
    private const PASSWORD_LENGTH = 24;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $attributes = [
            'name' => $this->resolveOption('name', 'Name'),
            'email' => $this->resolveOption('email', 'Email'),
        ];

        $validator = Validator::make($attributes, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $password = Str::password(self::PASSWORD_LENGTH, symbols: false);

        $user = User::create([
            ...$validator->validated(),
            'password' => $password,
        ]);

        $this->newLine();
        $this->info("User {$user->email} created.");
        $this->newLine();
        $this->line("  Password: <comment>{$password}</comment>");
        $this->newLine();
        $this->warn('This password is shown once and is not stored anywhere in readable form.');
        $this->warn('Copy it now; if it is lost the only fix is to create another user.');
        $this->newLine();

        return self::SUCCESS;
    }

    /**
     * Take the value from the option, else ask for it, but only when there is
     * somebody to ask. Run with --no-interaction (as a deploy would) a missing
     * value stays null and fails validation instead of hanging on a prompt.
     */
    private function resolveOption(string $option, string $question): ?string
    {
        $value = $this->option($option);

        if (is_string($value) && $value !== '') {
            return $value;
        }

        return $this->input->isInteractive() ? $this->ask($question) : null;
    }
}
