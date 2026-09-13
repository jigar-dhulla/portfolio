<footer class="site-footer">
    <div class="site-footer__group">
        <a href="#top" class="site-footer__mark" aria-label="Jigar Dhulla home">
            <x-logo />
        </a>
        <span class="site-footer__copy">&copy; {{ date('Y') }} Jigar Dhulla</span>
    </div>

    <div class="site-footer__group">
        <a class="site-footer__icon" href="mailto:jigar.tidus@gmail.com" title="Email" aria-label="Email">
            <x-icon.mail :size="18" />
        </a>
        <a class="site-footer__icon" href="https://www.linkedin.com/in/jigardhulla" target="_blank"
           rel="noopener" title="LinkedIn" aria-label="LinkedIn">
            <x-icon.linkedin :size="18" />
        </a>
        <a class="site-footer__icon" href="https://twitter.com/jigar_dhulla" target="_blank"
           rel="noopener" title="X" aria-label="X">
            <x-icon.x :size="18" />
        </a>
        <a class="site-footer__top" href="#top">Back to top</a>
        <button class="pizza-button" type="button" data-pizza title="Pizza" aria-label="Pizza">
            <x-icon.pizza />
        </button>
    </div>
</footer>
