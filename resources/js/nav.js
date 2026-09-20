/**
 * The header navigation: below the nav breakpoint in site.css the links
 * collapse behind a Menu button, and this opens and closes them.
 *
 * With JavaScript off the panel is left open instead (see the <noscript> block
 * in the layout), so the sections are always reachable.
 */

const header = document.querySelector('[data-nav]');
const toggle = header?.querySelector('[data-nav-toggle]');

if (header && toggle) {
    const desktop = window.matchMedia('(min-width: 720px)');

    const setOpen = (open) => {
        header.toggleAttribute('data-nav-open', open);
        toggle.setAttribute('aria-expanded', String(open));
    };

    toggle.addEventListener('click', () => {
        setOpen(!header.hasAttribute('data-nav-open'));
    });

    /** Every link points at a section of this same page, so the panel has to get out of the way itself. */
    header.querySelectorAll('[data-nav-panel] a').forEach((link) => {
        link.addEventListener('click', () => setOpen(false));
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && header.hasAttribute('data-nav-open')) {
            setOpen(false);
            toggle.focus();
        }
    });

    /** Widening past the breakpoint shows the links anyway; clear the state so the button
        does not come back already open. */
    desktop.addEventListener('change', (event) => {
        if (event.matches) {
            setOpen(false);
        }
    });
}
