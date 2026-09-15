/**
 * Startseite.
 *
 * Kapitelmarkierung plus interaktive Hero-Systemgrafik.
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
            '.startseite .home-system-stage{--hero-rx:0deg;--hero-ry:0deg;--hero-mx:50%;--hero-my:50%;grid-column:2;grid-row:1;position:relative;z-index:1;width:100%;align-self:center;justify-self:center;overflow:hidden;border-radius:18px;isolation:isolate;perspective:1200px}',
            '.startseite .home-system-stage::before{content:"";position:absolute;inset:-20%;z-index:2;pointer-events:none;background:radial-gradient(circle at var(--hero-mx) var(--hero-my),color-mix(in srgb,var(--stempel) 28%,transparent) 0,transparent 22%);mix-blend-mode:multiply;opacity:.68;transition:opacity .25s ease}',
            '.startseite .home-system-stage::after{content:"";position:absolute;inset:0;z-index:3;pointer-events:none;background:linear-gradient(115deg,transparent 25%,color-mix(in srgb,var(--stempel) 11%,transparent) 44%,transparent 58%);transform:translateX(-70%);opacity:.7}',
            '.startseite .home-system-visual{display:block;width:100%;height:auto;max-width:100%;border:0!important;outline:0!important;transform:rotateX(var(--hero-rx)) rotateY(var(--hero-ry)) scale(1.018);transform-origin:center;transition:transform .16s ease-out,filter .25s ease;filter:saturate(.98) contrast(1.025);will-change:transform}',
            '.startseite .home-system-stage:hover .home-system-visual{filter:saturate(1.04) contrast(1.035)}',
            '@media (max-width:820px){.startseite .home-system-stage{grid-column:1;grid-row:2;width:min(100%,44rem);justify-self:center}.startseite .home-portrait{grid-row:3}}',
            '@media (max-width:520px){.startseite .home-system-stage{width:calc(100% + var(--s2));margin-inline:calc(var(--s1) * -1);border-radius:12px}}',
            '@media (prefers-reduced-motion:no-preference){.startseite .home-system-stage{animation:home-system-stage-float 8s ease-in-out infinite alternate}.startseite .home-system-stage::after{animation:home-system-sheen 6.5s ease-in-out infinite}}',
            '@media (prefers-reduced-motion:reduce){.startseite .home-system-stage,.startseite .home-system-visual{animation:none!important;transition:none!important}.startseite .home-system-stage::after{display:none}}',
            '@keyframes home-system-stage-float{from{transform:translate3d(0,0,0)}to{transform:translate3d(0,-7px,0)}}',
            '@keyframes home-system-sheen{0%,18%{transform:translateX(-85%);opacity:0}38%{opacity:.7}62%{opacity:.45}82%,100%{transform:translateX(85%);opacity:0}}'
        ].join('');
        document.head.appendChild(style);
    }

    function mountHeroVisual(wurzel) {
        var hero = wurzel.querySelector('.home-hero');

        if (!hero || hero.querySelector('.home-system-stage')) {
            return;
        }

        mountHeroStyles();

        var stage = document.createElement('div');
        stage.className = 'home-system-stage';
        stage.setAttribute('aria-hidden', 'true');

        var visual = document.createElement('img');
        visual.className = 'home-system-visual';
        visual.src = '/wp-content/themes/blocksy-child/assets/img/home-hero-system-copper.webp?v=b3cb2fd59beb7a7b142a14cd4089c76bdb8a38da';
        visual.alt = '';
        visual.width = 1200;
        visual.height = 675;
        visual.loading = 'eager';
        visual.decoding = 'async';
        visual.fetchPriority = 'high';

        stage.appendChild(visual);

        if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            stage.addEventListener('pointermove', function (event) {
                var rect = stage.getBoundingClientRect();
                var x = Math.max(0, Math.min(1, (event.clientX - rect.left) / rect.width));
                var y = Math.max(0, Math.min(1, (event.clientY - rect.top) / rect.height));
                stage.style.setProperty('--hero-ry', ((x - 0.5) * 7).toFixed(2) + 'deg');
                stage.style.setProperty('--hero-rx', ((0.5 - y) * 5).toFixed(2) + 'deg');
                stage.style.setProperty('--hero-mx', (x * 100).toFixed(1) + '%');
                stage.style.setProperty('--hero-my', (y * 100).toFixed(1) + '%');
            });

            stage.addEventListener('pointerleave', function () {
                stage.style.setProperty('--hero-rx', '0deg');
                stage.style.setProperty('--hero-ry', '0deg');
                stage.style.setProperty('--hero-mx', '50%');
                stage.style.setProperty('--hero-my', '50%');
            });
        }

        var portrait = hero.querySelector('.home-portrait');
        if (portrait) {
            hero.insertBefore(stage, portrait);
        } else {
            hero.appendChild(stage);
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
