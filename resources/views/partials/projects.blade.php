<section id="projects" class="section">
    <div class="section__number">03</div>
    <h2 class="section__title">Projects</h2>

    <div class="projects">
        <a class="project" href="https://dolocal.io" target="_blank" rel="noopener">
            <div class="project__head">
                <div>
                    <h3 class="project__title">DoLocal</h3>
                    <div class="project__link">dolocal.io &nearr;</div>
                </div>
                <span class="project__kind">DENSOU</span>
            </div>

            <p>
                DENSOU&rsquo;s main product, and the one I work on every day. It helps large brands with
                20 or more stores reach the customers near each store. I work on all of it, from the
                application to the AWS setup.
            </p>

            <div class="tag-row">
                <span class="tag tag-accent">PHP</span>
                @foreach (['PhalconPHP', 'Laravel', 'Docker', 'Python', 'React', 'AWS', 'Terraform'] as $tag)
                    <span class="tag tag-neutral">{{ $tag }}</span>
                @endforeach
            </div>
        </a>

        <a class="project" href="https://rideshare.ing" target="_blank" rel="noopener">
            <div class="project__head">
                <div>
                    <h3 class="project__title">YaarPool</h3>
                    <div class="project__link">rideshare.ing &nearr;</div>
                </div>
                <span class="project__kind is-accent">Side project</span>
            </div>

            <p>
                My own side project. It is a ridesharing bot on WhatsApp, so there is no app to install.
                You send it a message and it finds people who already drive your route. I am building this now.
            </p>

            <div class="tag-row">
                <span class="tag tag-accent">Open source</span>
                @foreach (['Laravel', 'WhatsApp API', 'MySQL', 'AWS'] as $tag)
                    <span class="tag tag-neutral">{{ $tag }}</span>
                @endforeach
            </div>
        </a>
    </div>
</section>
