/**
 * Startseite.
 *
 * Kapitelmarkierung plus native Hero-Systemgrafik.
 * Kein externes Bild-Asset: Die Grafik ist Inline-SVG und damit Teil des DOM.
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
            '.startseite .home-system-visual{grid-column:2;grid-row:1;position:relative;z-index:1;width:min(100%,760px);aspect-ratio:1200/760;align-self:center;justify-self:center;perspective:1200px;transform-style:preserve-3d;filter:drop-shadow(0 28px 55px color-mix(in srgb,var(--tinte) 10%,transparent));transition:transform .18s ease-out}',
            '.startseite .home-system-visual svg{display:block;width:100%;height:100%;overflow:visible}',
            '.startseite .home-system-visual .sys-shell{fill:color-mix(in srgb,var(--papier) 96%,var(--stempel) 4%);stroke:var(--haar);stroke-width:1.2}',
            '.startseite .home-system-visual .sys-grid{stroke:color-mix(in srgb,var(--tinte) 7%,transparent);stroke-width:1}',
            '.startseite .home-system-visual .sys-card{fill:var(--papier);stroke:color-mix(in srgb,var(--tinte) 16%,transparent);stroke-width:1.2;transform-box:fill-box;transform-origin:center}',
            '.startseite .home-system-visual .sys-card--accent{fill:color-mix(in srgb,var(--stempel) 8%,var(--papier));stroke:color-mix(in srgb,var(--stempel) 55%,transparent)}',
            '.startseite .home-system-visual .sys-label{font-family:var(--mono);font-size:18px;letter-spacing:.16em;fill:var(--grau)}',
            '.startseite .home-system-visual .sys-title{font-family:var(--sans);font-size:31px;font-weight:650;fill:var(--tinte)}',
            '.startseite .home-system-visual .sys-small{font-family:var(--mono);font-size:15px;letter-spacing:.08em;fill:var(--grau)}',
            '.startseite .home-system-visual .sys-copper{fill:var(--stempel)}',
            '.startseite .home-system-visual .sys-flow-base{fill:none;stroke:color-mix(in srgb,var(--tinte) 15%,transparent);stroke-width:2}',
            '.startseite .home-system-visual .sys-flow{fill:none;stroke:var(--stempel);stroke-width:3;stroke-linecap:round;stroke-dasharray:16 18}',
            '.startseite .home-system-visual .sys-orbit{fill:none;stroke:color-mix(in srgb,var(--stempel) 28%,transparent);stroke-width:1.6;stroke-dasharray:4 11;transform-box:fill-box;transform-origin:center}',
            '.startseite .home-system-visual .sys-node{fill:var(--papier);stroke:var(--stempel);stroke-width:3}',
            '.startseite .home-system-visual .sys-core{fill:var(--stempel);transform-box:fill-box;transform-origin:center}',
            '.startseite .home-system-visual .sys-chip{fill:color-mix(in srgb,var(--tinte) 5%,var(--papier));stroke:color-mix(in srgb,var(--tinte) 12%,transparent);stroke-width:1}',
            '.startseite .home-system-visual .sys-bars rect{fill:color-mix(in srgb,var(--stempel) 72%,var(--papier))}',
            '.startseite .home-system-visual .sys-glow{fill:color-mix(in srgb,var(--stempel) 10%,transparent)}',
            '.startseite .home-system-visual .sys-live{fill:var(--stempel)}',
            '.startseite .home-system-visual::after{content:"";position:absolute;inset:5% 2%;pointer-events:none;background:linear-gradient(108deg,transparent 28%,color-mix(in srgb,var(--stempel) 12%,transparent) 48%,transparent 67%);transform:translateX(-115%);mix-blend-mode:multiply}',
            '.startseite .home-portrait{z-index:4}',
            '@media (max-width:820px){.startseite .home-system-visual{grid-column:1;grid-row:2;width:min(100%,44rem);margin-top:var(--s1)}.startseite .home-portrait{grid-row:3}}',
            '@media (max-width:520px){.startseite .home-system-visual{width:calc(100% + var(--s2));margin-inline:calc(var(--s1) * -1)}}',
            '@media (prefers-reduced-motion:no-preference){.startseite .home-system-visual .sys-flow{animation:sys-dash 3.2s linear infinite}.startseite .home-system-visual .sys-orbit{animation:sys-orbit 18s linear infinite}.startseite .home-system-visual .sys-core{animation:sys-pulse 2.5s ease-in-out infinite}.startseite .home-system-visual .sys-card--one{animation:sys-float-a 6s ease-in-out infinite}.startseite .home-system-visual .sys-card--two{animation:sys-float-b 7.4s ease-in-out infinite}.startseite .home-system-visual .sys-live{animation:sys-live 1.7s ease-in-out infinite}.startseite .home-system-visual::after{animation:sys-sheen 8s ease-in-out infinite}}',
            '@keyframes sys-dash{to{stroke-dashoffset:-68}}',
            '@keyframes sys-orbit{to{transform:rotate(360deg)}}',
            '@keyframes sys-pulse{0%,100%{transform:scale(.82);opacity:.72}50%{transform:scale(1.22);opacity:1}}',
            '@keyframes sys-float-a{0%,100%{transform:translateY(0)}50%{transform:translateY(-10px)}}',
            '@keyframes sys-float-b{0%,100%{transform:translateY(0)}50%{transform:translateY(8px)}}',
            '@keyframes sys-live{0%,100%{opacity:.38}50%{opacity:1}}',
            '@keyframes sys-sheen{0%,55%{transform:translateX(-115%)}78%,100%{transform:translateX(115%)}}'
        ].join('');
        document.head.appendChild(style);
    }

    function heroSvg() {
        return '' +
        '<svg viewBox="0 0 1200 760" role="img" aria-label="Animiertes System aus Website, Tracking, Anfrage und CRM">' +
            '<defs>' +
                '<linearGradient id="sysCopper" x1="0" x2="1"><stop offset="0" stop-color="var(--stempel)" stop-opacity=".18"/><stop offset=".5" stop-color="var(--stempel)" stop-opacity=".7"/><stop offset="1" stop-color="var(--stempel)" stop-opacity=".12"/></linearGradient>' +
                '<radialGradient id="sysHalo"><stop offset="0" stop-color="var(--stempel)" stop-opacity=".16"/><stop offset="1" stop-color="var(--stempel)" stop-opacity="0"/></radialGradient>' +
                '<filter id="sysShadow" x="-30%" y="-30%" width="160%" height="160%"><feDropShadow dx="0" dy="18" stdDeviation="18" flood-color="#000" flood-opacity=".08"/></filter>' +
            '</defs>' +
            '<rect class="sys-shell" x="32" y="34" width="1136" height="676" rx="34"/>' +
            '<g opacity=".9">' +
                '<path class="sys-grid" d="M92 142H1108M92 254H1108M92 366H1108M92 478H1108M92 590H1108"/>' +
                '<path class="sys-grid" d="M218 92V650M420 92V650M622 92V650M824 92V650M1026 92V650"/>' +
            '</g>' +
            '<ellipse cx="626" cy="370" rx="278" ry="238" fill="url(#sysHalo)"/>' +
            '<g filter="url(#sysShadow)">' +
                '<g class="sys-card sys-card--one">' +
                    '<rect class="sys-card" x="88" y="146" width="310" height="220" rx="24"/>' +
                    '<text class="sys-label" x="122" y="190">01 · WEBSITE</text>' +
                    '<rect class="sys-chip" x="122" y="220" width="236" height="22" rx="11"/>' +
                    '<rect class="sys-chip" x="122" y="258" width="188" height="15" rx="7.5"/>' +
                    '<rect class="sys-chip" x="122" y="288" width="216" height="15" rx="7.5"/>' +
                    '<rect class="sys-card--accent" x="122" y="326" width="112" height="18" rx="9"/>' +
                '</g>' +
                '<g class="sys-card sys-card--two">' +
                    '<rect class="sys-card" x="812" y="397" width="300" height="206" rx="24"/>' +
                    '<text class="sys-label" x="846" y="441">04 · CRM</text>' +
                    '<text class="sys-title" x="846" y="492">Lead erkannt.</text>' +
                    '<text class="sys-small" x="846" y="526">Quelle · Intent · Status</text>' +
                    '<circle class="sys-live" cx="858" cy="562" r="7"/>' +
                    '<text class="sys-small" x="878" y="568">LIVE HANDOFF</text>' +
                '</g>' +
            '</g>' +
            '<path class="sys-flow-base" d="M398 257 C478 257 471 330 548 345 S715 334 782 286 S870 282 918 397"/>' +
            '<path class="sys-flow" d="M398 257 C478 257 471 330 548 345 S715 334 782 286 S870 282 918 397"/>' +
            '<g>' +
                '<circle class="sys-orbit" cx="631" cy="350" r="145"/>' +
                '<circle class="sys-orbit" cx="631" cy="350" r="108" opacity=".7"/>' +
                '<circle class="sys-node" cx="631" cy="350" r="66"/>' +
                '<circle class="sys-core" cx="631" cy="350" r="16"/>' +
                '<text class="sys-label" text-anchor="middle" x="631" y="315">02 · TRACKING</text>' +
                '<text class="sys-small" text-anchor="middle" x="631" y="398">SIGNAL VERKNÜPFT</text>' +
                '<g>' +
                    '<rect class="sys-chip" x="486" y="480" width="86" height="42" rx="14"/><text class="sys-small" text-anchor="middle" x="529" y="507">GA4</text>' +
                    '<rect class="sys-chip" x="587" y="480" width="86" height="42" rx="14"/><text class="sys-small" text-anchor="middle" x="630" y="507">GTM</text>' +
                    '<rect class="sys-chip" x="688" y="480" width="98" height="42" rx="14"/><text class="sys-small" text-anchor="middle" x="737" y="507">SERVER</text>' +
                '</g>' +
            '</g>' +
            '<g>' +
                '<rect class="sys-card sys-card--accent" x="794" y="154" width="252" height="142" rx="22"/>' +
                '<text class="sys-label" x="826" y="194">03 · ANFRAGE</text>' +
                '<text class="sys-title" x="826" y="240">qualifiziert</text>' +
                '<g class="sys-bars"><rect x="826" y="261" width="44" height="11" rx="5.5"/><rect x="880" y="261" width="74" height="11" rx="5.5"/><rect x="965" y="261" width="46" height="11" rx="5.5"/></g>' +
            '</g>' +
            '<g opacity=".9">' +
                '<circle class="sys-copper" cx="442" cy="267" r="5"/><circle class="sys-copper" cx="492" cy="316" r="5"/><circle class="sys-copper" cx="748" cy="305" r="5"/><circle class="sys-copper" cx="850" cy="310" r="5"/>' +
            '</g>' +
            '<text class="sys-small" x="90" y="675">WEBSITE → SIGNAL → QUALIFIZIERUNG → VERTRIEB</text>' +
        '</svg>';
    }

    function mountHeroVisual(wurzel) {
        var hero = wurzel.querySelector('.home-hero');

        if (!hero) {
            return;
        }

        var old = hero.querySelector('.home-system-visual');
        if (old) {
            old.remove();
        }

        mountHeroStyles();

        var visual = document.createElement('div');
        visual.className = 'home-system-visual';
        visual.setAttribute('aria-hidden', 'true');
        visual.innerHTML = heroSvg();

        var portrait = hero.querySelector('.home-portrait');
        if (portrait) {
            hero.insertBefore(visual, portrait);
        } else {
            hero.appendChild(visual);
        }

        var reduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (!reduced && window.innerWidth > 820) {
            visual.addEventListener('pointermove', function (event) {
                var rect = visual.getBoundingClientRect();
                var px = (event.clientX - rect.left) / rect.width - 0.5;
                var py = (event.clientY - rect.top) / rect.height - 0.5;
                visual.style.transform = 'rotateX(' + (-py * 4.5).toFixed(2) + 'deg) rotateY(' + (px * 5.5).toFixed(2) + 'deg) translate3d(' + (px * 8).toFixed(1) + 'px,' + (py * 6).toFixed(1) + 'px,0)';
            });
            visual.addEventListener('pointerleave', function () {
                visual.style.transform = '';
            });
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
