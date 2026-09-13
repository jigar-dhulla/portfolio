<x-layout>
    <a class="skip-link" href="#main">Skip to content</a>

    <div class="shell">
        @include('partials.header')

        <main id="main">
            @include('partials.hero')

            @if (config('portfolio.show_stats'))
                @include('partials.stats')
            @endif

            @include('partials.about')
            @include('partials.experience')
            @include('partials.projects')
            @include('partials.open-source')
            @include('partials.talks')
            @include('partials.contact')
        </main>

        @include('partials.footer')
    </div>

    {{-- Outside .shell on purpose: .shell keeps a filling transform animation,
         which would make it the containing block for a fixed-position child. --}}
    <canvas class="pizza-canvas" data-pizza-canvas aria-hidden="true"></canvas>
</x-layout>
