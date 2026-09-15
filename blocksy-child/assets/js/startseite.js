/**
 * Startseite.
 *
 * Kapitelmarkierung plus robuste Hero-Systemgrafik.
 * Die Grafik wird als echtes <img> eingesetzt, damit CSS-Optimierer keine
 * background-image-URL aus einem Pseudo-Element verschlucken koennen.
 */
(function () {
    'use strict';

    function mountHeroVisual(wurzel) {
        var hero = wurzel.querySelector('.home-hero');

        if (!hero || hero.querySelector('.home-system-visual')) {
            return;
        }

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
