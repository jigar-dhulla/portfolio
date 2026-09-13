<section id="about" class="section section--split">
    <div>
        <div class="section__number">01</div>
        <h2 class="section__title">About me</h2>
    </div>

    <div class="about__body">
        <p>I was born and raised in Mumbai and now live in Pune.</p>

        <p>
            Most of my work is on the backend: designing applications and APIs, working with MySQL,
            integrating 3rd party APIs. As a lead I plan the work, run the team&rsquo;s sprints in JIRA,
            generate code and review peer&rsquo;s generated code.
        </p>

        <p>
            Outside work, I organise the Laravel Pune community. Currently building carpool bot,
            YaarPool. Enjoy playing table-tennis.
        </p>

        <div class="tag-row">
            @foreach (['PHP', 'Laravel', 'MySQL', 'Docker', 'AWS', 'Terraform', 'Python', 'React'] as $skill)
                <span class="tag tag-outline">{{ $skill }}</span>
            @endforeach
        </div>
    </div>
</section>
