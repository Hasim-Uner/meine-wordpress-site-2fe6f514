/**
 * Startseite.
 *
 * Kapitelmarkierung plus robuste Hero-Systemgrafik.
 * Die Grafik wird als echtes <img> eingesetzt, damit CSS-Optimierer keine
 * background-image-URL aus einem Pseudo-Element verschlucken koennen.
 */
(function () {
    'use strict';

    function mountHeroStyles() {
        if (document.getElementById('home-system-visual-fix')) {
            return;
        }

        var style = document.createElement('style');
        style.id = 'home-system-visual-fix';
        style.textContent = [
            '.startseite .home-hero::before{content:none!important;display:none!important}',
            '.startseite .home-system-visual{grid-column:2;grid-row:1;display:block;width:100%;height:auto;max-width:100%;align-self:center;justify-self:center;filter:saturate(.96) contrast(1.015);position:relative;z-index:1}',
            '@media (max-width:820px){.startseite .home-system-visual{grid-column:1;grid-row:2;width:min(100%,44rem);justify-self:center}.startseite .home-portrait{grid-row:3}}',
            '@media (max-width:520px){.startseite .home-system-visual{width:calc(100% + var(--s2));margin-inline:calc(var(--s1) * -1)}}',
            '@media (prefers-reduced-motion:no-preference){.startseite .home-system-visual{animation:home-system-visual-drift 9s ease-in-out infinite alternate}}'
        ].join('');
        document.head.appendChild(style);
    }

    function mountHeroVisual(wurzel) {
        var hero = wurzel.querySelector('.home-hero');

        if (!hero || hero.querySelector('.home-system-visual')) {
            return;
        }

        mountHeroStyles();

        var visual = document.createElement('img');
        visual.className = 'home-system-visual';
        visual.src = '/wp-content/themes/blocksy-child/assets/img/home-hero-system-copper.webp';
        visual.alt = '';
        visual.width = 1400;
        visual.height = 788;
        visual.loading = 'eager';
        visual.decoding = 'async';
        visual.fetchPriority = 'high';
        visual.setAttribute('aria-hidden', 'true');

        var portrait = hero.querySelector('.home-portrait');
        if (portrait) {
            hero.insertBefore(visual, portrait);
        } else {
            hero.appendChild(visual);
        }
    }

    function init() {
        var wurzel = document.querySelector('.startseite');

        if (!wurzel) {
            return;
        }

        mountHeroVisual(wurzel);

        if (!('IntersectionObserver' in window)) {
            return;
        }

        var abschnitte = Array.prototype.slice.call(wurzel.querySelectorAll('section[id]'));

        if (!abschnitte.length) {
            return;
        }

        /* Das mittlere Zehntel des Sichtfelds. Enger als eine halbe Seite,
           damit bei zwei sichtbaren Abschnitten nicht beide leuchten. */
        var marke = new window.IntersectionObserver(function (eintraege) {
            eintraege.forEach(function (eintrag) {
                eintrag.target.classList.toggle('aktiv', eintrag.isIntersecting);
            });
        }, { rootMargin: '-45% 0px -45% 0px' });

        abschnitte.forEach(function (abschnitt) {
            marke.observe(abschnitt);
        });
    }

    if ('loading' === document.readyState) {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
