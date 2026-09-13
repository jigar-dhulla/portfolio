<x-layout title="Messages" robots="noindex, nofollow">
    <div class="shell">
        <header class="admin-bar">
            <a class="admin-bar__brand" href="{{ route('home') }}">Jigar Dhulla</a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn btn-secondary" type="submit">Sign out</button>
            </form>
        </header>

        <main class="admin">
            <h1 class="admin__title">Messages</h1>

            @if (session('message.deleted'))
                <p class="admin__flash">Message deleted.</p>
            @endif

            <p class="admin__count">{{ $total }} {{ Str::plural('message', $total) }}</p>

            @forelse ($messages as $message)
                <article class="message">
                    <div class="message__head">
                        <h2 class="message__name">{{ $message->name }}</h2>
                        <time class="message__time" datetime="{{ $message->created_at->toIso8601String() }}">
                            {{ $message->created_at->format('j M Y, H:i') }}
                        </time>
                    </div>

                    <a class="message__email" href="mailto:{{ $message->email }}">{{ $message->email }}</a>

                    <p class="message__body">{{ $message->message }}</p>

                    {{-- A disclosure rather than a JS confirm: two taps to delete, no dialog. --}}
                    <details class="message__delete">
                        <summary>Delete</summary>

                        <form method="POST" action="{{ route('messages.destroy', $message) }}">
                            @csrf
                            @method('DELETE')

                            <span>This cannot be undone.</span>
                            <button class="btn btn-primary" type="submit">Yes, delete</button>
                        </form>
                    </details>
                </article>
            @empty
                <p class="admin__empty">No messages yet.</p>
            @endforelse

            @if ($messages->hasPages())
                <nav class="admin-pager">
                    @if ($messages->previousPageUrl())
                        <a class="btn btn-secondary" href="{{ $messages->previousPageUrl() }}">Newer</a>
                    @endif

                    @if ($messages->nextPageUrl())
                        <a class="btn btn-secondary" href="{{ $messages->nextPageUrl() }}">Older</a>
                    @endif
                </nav>
            @endif
        </main>
    </div>
</x-layout>
