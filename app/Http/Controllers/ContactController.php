<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactMessageRequest;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;

class ContactController extends Controller
{
    /**
     * Record a message sent from the contact form.
     */
    public function __invoke(StoreContactMessageRequest $request): RedirectResponse
    {
        ContactMessage::create($request->safe()->only(['name', 'email', 'message']));

        return to_route('home')
            ->withFragment('contact')
            ->with('contact.sent', true);
    }
}
