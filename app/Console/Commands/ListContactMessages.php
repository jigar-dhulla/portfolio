<?php

namespace App\Console\Commands;

use App\Models\ContactMessage;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

#[Signature('contact:messages {--limit=20 : How many of the newest messages to list} {--id= : Show a single message in full, by id}')]
#[Description('Read messages sent through the contact form')]
class ListContactMessages extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if ($this->option('id') !== null) {
            return $this->showSingleMessage((int) $this->option('id'));
        }

        return $this->listNewestMessages((int) $this->option('limit'));
    }

    /**
     * Print the newest messages as a summary table.
     */
    private function listNewestMessages(int $limit): int
    {
        if ($limit < 1) {
            $this->error('--limit must be at least 1.');

            return self::FAILURE;
        }

        $messages = ContactMessage::query()
            ->latest()
            ->limit($limit)
            ->get();

        if ($messages->isEmpty()) {
            $this->info('No contact messages yet.');

            return self::SUCCESS;
        }

        $this->table(
            ['ID', 'Received', 'Name', 'Email', 'Message'],
            $messages->map(fn (ContactMessage $message): array => [
                $message->id,
                $message->created_at->format('Y-m-d H:i'),
                $message->name,
                $message->email,
                Str::limit($this->flatten($message->message), 50),
            ])->all(),
        );

        $total = ContactMessage::query()->count();
        $this->line(sprintf('Showing %d of %d message(s).', $messages->count(), $total));
        $this->line('Read one in full with: php artisan contact:messages --id=<id>');

        return self::SUCCESS;
    }

    /**
     * Print one message with its body untruncated.
     */
    private function showSingleMessage(int $id): int
    {
        $message = ContactMessage::find($id);

        if ($message === null) {
            $this->error("No contact message found with id {$id}.");

            return self::FAILURE;
        }

        $this->newLine();
        $this->line("<comment>From:</comment>     {$message->name} <{$message->email}>");
        $this->line("<comment>Received:</comment> {$message->created_at->format('Y-m-d H:i:s')}");
        $this->newLine();
        $this->line($message->message);
        $this->newLine();

        return self::SUCCESS;
    }

    /**
     * Collapse whitespace so a multi-line message stays on one table row.
     */
    private function flatten(string $message): string
    {
        return trim(preg_replace('/\s+/', ' ', $message));
    }
}
