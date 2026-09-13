<section id="contact" class="section section--split section--contact section--last">
    <div>
        <div class="section__number">06</div>
        <h2 class="section__title">Get in touch</h2>

        <p class="contact__intro">
            Happy to talk about backend work, Laravel, and systems that have grown too big for how they
            were first built. Send me a message and I will reply.
        </p>

        <div class="contact__links">
            <a href="mailto:jigar.tidus@gmail.com">
                <x-icon.mail />jigar.tidus@gmail.com
            </a>
            <a href="https://www.linkedin.com/in/jigardhulla" target="_blank" rel="noopener">
                <x-icon.linkedin />linkedin.com/in/jigardhulla
            </a>
            <a href="https://twitter.com/jigar_dhulla" target="_blank" rel="noopener">
                <x-icon.x />@jigar_dhulla
            </a>
        </div>
    </div>

    <form class="contact-form" method="POST" action="{{ route('contact.store') }}">
        @csrf

        @if ($errors->has('website'))
            <p class="contact-form__alert">{{ $errors->first('website') }}</p>
        @endif

        <div class="field">
            <label for="contact-name">Name</label>
            <input class="input" id="contact-name" name="name" value="{{ old('name') }}"
                   placeholder="Your name" required autocomplete="name">
            @error('name')
                <p class="contact-form__error">{{ $message }}</p>
            @enderror
        </div>

        <div class="field">
            <label for="contact-email">Email</label>
            <input class="input" id="contact-email" name="email" type="email" value="{{ old('email') }}"
                   placeholder="you@company.com" required autocomplete="email">
            @error('email')
                <p class="contact-form__error">{{ $message }}</p>
            @enderror
        </div>

        <div class="field">
            <label for="contact-message">Your message</label>
            <textarea class="input" id="contact-message" name="message" rows="5" required
                      placeholder="Have you already built something and need support to finish or maintain it?">{{ old('message') }}</textarea>
            @error('message')
                <p class="contact-form__error">{{ $message }}</p>
            @enderror
        </div>

        {{-- Honeypot: people never see this, bots fill it in and the request is rejected. --}}
        <div class="contact-form__honeypot" aria-hidden="true">
            <label for="contact-website">Website</label>
            <input id="contact-website" name="website" type="text" tabindex="-1" autocomplete="off">
        </div>

        <button class="btn btn-primary" type="submit">Send message</button>

        @if (session('contact.sent'))
            <p class="contact-form__sent">Thanks. I will reply within a day.</p>
        @endif
    </form>
</section>
