<x-layout title="Sign in" robots="noindex, nofollow">
    <div class="shell">
        <main class="admin admin--narrow">
            <h1 class="admin__title">Sign in</h1>

            <form class="admin__form" method="POST" action="{{ route('login.store') }}">
                @csrf

                @if ($errors->any())
                    <p class="admin__error">{{ $errors->first() }}</p>
                @endif

                <div class="field">
                    <label for="email">Email</label>
                    <input class="input" id="email" name="email" type="email" value="{{ old('email') }}"
                           required autofocus autocomplete="username" inputmode="email">
                </div>

                <div class="field">
                    <label for="password">Password</label>
                    <input class="input" id="password" name="password" type="password"
                           required autocomplete="current-password">
                </div>

                <button class="btn btn-primary" type="submit">Sign in</button>
            </form>
        </main>
    </div>
</x-layout>
