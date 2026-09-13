<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ContactMessageController extends Controller
{
    /**
     * How many messages are listed per page.
     */
    private const PER_PAGE = 20;

    /**
     * List the messages, newest first.
     */
    public function index(): View
    {
        return view('messages.index', [
            'messages' => ContactMessage::query()->latest()->simplePaginate(self::PER_PAGE),
            'total' => ContactMessage::query()->count(),
        ]);
    }

    /**
     * Delete one message.
     */
    public function destroy(ContactMessage $contactMessage): RedirectResponse
    {
        $contactMessage->delete();

        return back()->with('message.deleted', true);
    }
}
