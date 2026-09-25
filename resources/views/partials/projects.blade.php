<section id="projects" class="section">
    <div class="section__number">03</div>
    <h2 class="section__title">Projects</h2>

    <div class="projects">
        <a class="project" href="https://dolocal.io" target="_blank" rel="noopener">
            <div class="project__head">
                <div>
                    <h3 class="project__title">DoLocal</h3>
                    <div class="project__link">dolocal.io&nbsp;&nearr;</div>
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

        <a class="project" href="https://bots.jigardhulla.dev" target="_blank" rel="noopener">
            <div class="project__head">
                <div>
                    <h3 class="project__title">Personal Bots</h3>
                    <div class="project__link">bots.jigardhulla.dev&nbsp;&nearr;</div>
                </div>
                <span class="project__kind is-accent">Side project</span>
            </div>

            <p>
                A collection of small bots I build for myself to automate everyday tasks.
            </p>

            <div class="tag-row">
                <span class="tag tag-accent">Side project</span>
                @foreach (['Laravel', 'Docker'] as $tag)
                    <span class="tag tag-neutral">{{ $tag }}</span>
                @endforeach
            </div>
        </a>
    </div>
</section>
