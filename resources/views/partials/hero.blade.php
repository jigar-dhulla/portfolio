<section id="top" class="hero">
    <div>
        @if ($availability = config('portfolio.availability'))
            <div class="hero__badge">{{ $availability }}</div>
        @endif

        <h1 class="hero__title">Backend engineer who loves to build</h1>

        <p class="hero__lead">
            I help build systems that can handle growth, and I manage the teams that build them.
            Right now I am also playing with Agentic Bots and building software with them.
        </p>

        <div class="hero__actions">
            <a class="btn btn-primary" href="#contact">Get in touch</a>
            <a class="btn btn-secondary" href="#projects">See my projects</a>
        </div>
    </div>

    <div class="hero__portrait">
        <img src="{{ asset('images/jigar-laracon.webp') }}" width="832" height="555"
             alt="Jigar Dhulla speaking on stage at Laracon India">
    </div>
</section>
