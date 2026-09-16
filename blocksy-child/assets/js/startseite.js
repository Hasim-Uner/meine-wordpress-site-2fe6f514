/**
 * Startseite – Hero V7.
 *
 * Leistung zuerst. Rechts nur noch eine in Sekunden erfassbare Strecke:
 * Quellen → Website → Anfrage → Segmentierung → CRM.
 */
(function () {
    'use strict';

    function icon(kind) {
        var icons = {
            website: '<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="2"></rect><path d="M3 8h18"></path><circle cx="6.5" cy="6" r=".7" fill="currentColor" stroke="none"></circle><circle cx="9" cy="6" r=".7" fill="currentColor" stroke="none"></circle></svg>',
            request: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 3h7l4 4v14H7z"></path><path d="M14 3v5h5M10 12h5M10 16h5"></path></svg>',
            filter: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5h16l-6.5 7.2V19l-3 1v-7.8z"></path></svg>',
            crm: '<svg viewBox="0 0 24 24" aria-hidden="true"><ellipse cx="12" cy="5.5" rx="7" ry="2.5"></ellipse><path d="M5 5.5v6c0 1.4 3.1 2.5 7 2.5s7-1.1 7-2.5v-6M5 11.5v6c0 1.4 3.1 2.5 7 2.5s7-1.1 7-2.5v-6"></path></svg>',
            search: '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="10.5" cy="10.5" r="5.5"></circle><path d="m15 15 4.5 4.5"></path></svg>',
            people: '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="9" cy="8" r="3"></circle><circle cx="16.5" cy="9" r="2.3"></circle><path d="M3.5 19c.6-3.4 2.5-5.1 5.5-5.1s5 1.7 5.5 5.1M14.2 14.5c2.8-.3 4.8 1.1 5.5 4.5"></path></svg>'
        };
        return icons[kind] || '';
    }

    function mountStyles() {
        document.getElementById('home-hero-v6-styles')?.remove();
        if (document.getElementById('home-hero-v7-styles')) {
            return;
        }

        var style = document.createElement('style');
        style.id = 'home-hero-v7-styles';
        style.textContent = `
.startseite .home-hero.home-hero-v7::before{content:''!important;display:none!important;background:none!important;background-image:none!important;animation:none!important}
.startseite .home-hero.home-hero-v7{position:relative;display:grid;grid-template-columns:minmax(0,1.34fr) minmax(28.5rem,.9fr);gap:clamp(2.4rem,4.1vw,4.9rem);align-items:start;min-height:min(43rem,calc(100svh - 5.5rem));padding-block:clamp(1.35rem,2.1vw,2.15rem);isolation:isolate}
.startseite .home-hero-v7>.home-hero-copy{grid-column:1;grid-row:1;min-width:0;position:relative;z-index:3}
.startseite .home-hero-v7 .gegenstand{margin:0 0 clamp(1.05rem,1.45vw,1.45rem);color:var(--stempel);font-family:var(--mono);font-size:clamp(.62rem,.59rem + .1vw,.7rem);font-weight:650;letter-spacing:.17em;line-height:1.4;text-transform:uppercase}
.startseite .home-hero-v7 h1{margin:0!important;max-width:none!important;font-size:inherit!important;line-height:1!important;letter-spacing:0!important}
.startseite .home-title-primary,.startseite .home-title-secondary{display:block}
.startseite .home-title-primary{max-width:9.25ch;font-family:var(--sans);font-size:clamp(3.55rem,4.55vw,5.2rem);font-weight:650;line-height:.92;letter-spacing:-.056em;color:var(--tinte)}
.startseite .home-title-secondary{max-width:11.9ch;margin-top:.25em;font-family:var(--sans);font-size:clamp(2.5rem,3.25vw,3.7rem);font-weight:470;line-height:.99;letter-spacing:-.044em;color:color-mix(in srgb,var(--tinte) 72%,var(--papier))}
.startseite .home-title-stop{color:var(--stempel)}
.startseite .home-hero-v7 .aufriss{max-width:40ch;margin-top:clamp(1.65rem,2.1vw,2rem);font-size:clamp(1rem,.95rem + .16vw,1.1rem);line-height:1.58;color:color-mix(in srgb,var(--tinte) 93%,var(--papier))}
.startseite .home-hero-v7 .ausgang{display:flex;flex-wrap:wrap;gap:.82rem;margin-top:clamp(1.55rem,1.9vw,1.85rem)}
.startseite .home-hero-v7 .tun{min-height:3.18rem;padding-inline:1.35rem;border-radius:2px;transition:transform var(--t-norm) var(--ease-aus),box-shadow var(--t-norm) var(--ease-aus),border-color var(--t-norm) var(--ease-aus),color var(--t-norm) var(--ease-aus)}
.startseite .home-hero-v7 .tun:not(.still){box-shadow:0 9px 22px color-mix(in srgb,var(--stempel) 14%,transparent)}
.startseite .home-hero-v7 .tun.still{background:transparent;border-color:color-mix(in srgb,var(--tinte) 58%,transparent);color:var(--tinte)}
@media (hover:hover) and (pointer:fine){.startseite .home-hero-v7 .tun:hover{transform:translateY(-2px)}.startseite .home-hero-v7 .tun:not(.still):hover{box-shadow:0 13px 28px color-mix(in srgb,var(--stempel) 20%,transparent)}.startseite .home-hero-v7 .tun.still:hover{border-color:var(--tinte)}}
.startseite .home-trust-row{display:flex;flex-wrap:wrap;align-items:center;gap:.65rem 1rem;margin-top:1.35rem;color:color-mix(in srgb,var(--tinte) 72%,var(--papier));font-size:.8rem;line-height:1.35}
.startseite .home-trust-row span{display:inline-flex;align-items:center;gap:.42rem;white-space:nowrap}
.startseite .home-trust-row span::before{content:'✓';display:grid;place-items:center;width:1.02rem;height:1.02rem;border:1px solid var(--stempel);border-radius:50%;color:var(--stempel);font-size:.58rem;font-weight:800;line-height:1}
.startseite .home-trust-row span+span::after{content:'';order:-1;width:1px;height:.82rem;margin-right:.1rem;background:color-mix(in srgb,var(--tinte) 16%,transparent)}
.startseite .home-portrait{position:relative!important;inset:auto!important;z-index:2!important;width:max-content!important;max-width:100%!important;display:grid!important;grid-template-columns:2.7rem minmax(0,1fr)!important;gap:.7rem!important;align-items:center!important;margin:1rem 0 0!important;padding:.9rem 0 0!important;border:0!important;border-top:1px solid color-mix(in srgb,var(--tinte) 12%,transparent)!important;border-radius:0!important;background:transparent!important;box-shadow:none!important;backdrop-filter:none!important;-webkit-backdrop-filter:none!important}
.startseite .home-portrait img{width:2.7rem!important;height:2.7rem!important;aspect-ratio:1;object-fit:cover;object-position:50% 18%;border-radius:50%!important}
.startseite .home-portrait figcaption{display:grid!important;gap:.08rem!important;padding:0!important;font-size:.66rem!important;line-height:1.28!important}
.startseite .home-portrait figcaption strong{font-size:.78rem!important;color:var(--tinte)}
.startseite .home-portrait figcaption span{color:color-mix(in srgb,var(--tinte) 66%,var(--papier))!important;font-size:.62rem!important}
.startseite .home-portrait figcaption a{justify-self:start;font-size:.61rem!important}

.startseite .home-flow-v7{grid-column:2;grid-row:1;position:relative;width:100%;min-width:0;min-height:23.5rem;display:grid;align-content:center;align-self:start;margin-top:.05rem;isolation:isolate}
.startseite .home-flow-v7__grid{position:absolute;inset:0 -2% 2% -4%;z-index:-3;opacity:.34;pointer-events:none;background-image:linear-gradient(color-mix(in srgb,var(--tinte) 3.2%,transparent) 1px,transparent 1px),linear-gradient(90deg,color-mix(in srgb,var(--tinte) 3.2%,transparent) 1px,transparent 1px);background-size:52px 52px;mask-image:linear-gradient(90deg,transparent 0,#000 13%,#000 88%,transparent 100%);-webkit-mask-image:linear-gradient(90deg,transparent 0,#000 13%,#000 88%,transparent 100%)}
.startseite .home-flow-v7__note{position:absolute;right:.2rem;top:.1rem;color:color-mix(in srgb,var(--tinte) 54%,var(--papier));font-family:var(--mono);font-size:.48rem;font-weight:650;letter-spacing:.16em;line-height:1.5;text-transform:uppercase;text-align:right}
.startseite .home-flow-v7__note::after{content:'';display:block;width:2rem;height:1px;margin:.5rem 0 0 auto;background:var(--stempel);opacity:.78}
.startseite .home-flow-v7__track{position:relative;display:grid;grid-template-columns:minmax(5.4rem,.78fr) minmax(0,1fr) minmax(0,1.1fr) minmax(3rem,.54fr) minmax(0,1fr);gap:clamp(.28rem,.7vw,.6rem);align-items:center;padding-block:4.25rem 3.4rem}
.startseite .home-flow-v7__rail{position:absolute;left:15%;right:5%;top:50%;height:1px;background:linear-gradient(90deg,color-mix(in srgb,var(--stempel) 35%,transparent),var(--stempel) 22%,var(--stempel) 84%,color-mix(in srgb,var(--stempel) 32%,transparent));transform:translateY(-50%);z-index:-1}
.startseite .home-flow-v7__sources{position:relative;display:grid;gap:.42rem;align-content:center;z-index:2}
.startseite .home-flow-v7__source{display:flex;align-items:center;gap:.38rem;width:max-content;max-width:100%;padding:.3rem .5rem;border:1px solid color-mix(in srgb,var(--tinte) 11%,var(--haar));border-radius:999px;background:var(--papier);font-family:var(--mono);font-size:.5rem;color:color-mix(in srgb,var(--tinte) 62%,var(--papier));white-space:nowrap}
.startseite .home-flow-v7__source svg{width:.82rem;height:.82rem;stroke:var(--stempel);fill:none;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round;flex:0 0 auto}
.startseite .home-flow-v7__source--ads::before{content:'G';display:grid;place-items:center;width:.82rem;height:.82rem;color:var(--stempel);font-family:var(--sans);font-size:.68rem;font-weight:750;line-height:1}
.startseite .home-flow-v7__stage{position:relative;display:grid;justify-items:center;align-content:center;gap:.55rem;min-width:0;text-align:center;z-index:2}
.startseite .home-flow-v7__disc{position:relative;display:grid;place-items:center;width:4.8rem;height:4.8rem;border:1px solid color-mix(in srgb,var(--tinte) 14%,var(--haar));border-radius:50%;background:var(--papier);box-shadow:0 10px 24px color-mix(in srgb,var(--tinte) 5%,transparent)}
.startseite .home-flow-v7__disc svg{width:1.68rem;height:1.68rem;stroke:currentColor;color:color-mix(in srgb,var(--tinte) 82%,var(--papier));fill:none;stroke-width:1.7;stroke-linecap:round;stroke-linejoin:round}
.startseite .home-flow-v7__stage strong{font-size:.8rem;line-height:1.15;color:var(--tinte);font-weight:700;letter-spacing:-.012em}
.startseite .home-flow-v7__stage--request .home-flow-v7__disc{width:5.65rem;height:5.65rem;border-color:color-mix(in srgb,var(--stempel) 55%,var(--haar));box-shadow:0 0 0 .8rem color-mix(in srgb,var(--stempel) 4%,transparent),0 0 0 1.45rem color-mix(in srgb,var(--stempel) 2%,transparent),0 15px 34px color-mix(in srgb,var(--stempel) 8%,transparent)}
.startseite .home-flow-v7__stage--request .home-flow-v7__disc::before{content:'';position:absolute;inset:-2.1rem;border:1px dashed color-mix(in srgb,var(--stempel) 20%,transparent);border-radius:50%;pointer-events:none}
.startseite .home-flow-v7__stage--request strong{color:var(--stempel)}
.startseite .home-flow-v7__filter{position:relative;z-index:3;display:grid;justify-items:center;gap:.42rem;align-content:center;transform:translateY(.1rem)}
.startseite .home-flow-v7__filter-icon{display:grid;place-items:center;width:2.55rem;height:2.55rem;border:1px solid color-mix(in srgb,var(--tinte) 12%,var(--haar));border-radius:50%;background:var(--papier);color:color-mix(in srgb,var(--tinte) 76%,var(--papier))}
.startseite .home-flow-v7__filter-icon svg{width:1.15rem;height:1.15rem;stroke:currentColor;fill:none;stroke-width:1.75;stroke-linecap:round;stroke-linejoin:round}
.startseite .home-flow-v7__filter strong{font-size:.66rem;color:var(--tinte)}
.startseite .home-flow-v7__tags{display:flex;justify-content:center;gap:.16rem;flex-wrap:nowrap}
.startseite .home-flow-v7__tags span{padding:.18rem .32rem;border:1px solid color-mix(in srgb,var(--tinte) 10%,var(--haar));border-radius:999px;background:var(--papier);font-family:var(--mono);font-size:.38rem;color:color-mix(in srgb,var(--tinte) 56%,var(--papier));white-space:nowrap}
.startseite .home-flow-v7__arrow{position:absolute;top:50%;z-index:1;color:var(--stempel);font-size:1.1rem;line-height:1;transform:translate(-50%,-53%);background:var(--papier);padding:0 .12rem}
.startseite .home-flow-v7__arrow--a{left:42.8%}.startseite .home-flow-v7__arrow--b{left:65.3%}.startseite .home-flow-v7__arrow--c{left:82.7%}

@media (max-width:1240px){.startseite .home-hero.home-hero-v7{grid-template-columns:minmax(0,1.24fr) minmax(26rem,.9fr);gap:clamp(2rem,3vw,3rem)}.startseite .home-title-primary{font-size:clamp(3.25rem,4.4vw,4.8rem)}.startseite .home-title-secondary{font-size:clamp(2.35rem,3.2vw,3.35rem)}.startseite .home-flow-v7__disc{width:4.25rem;height:4.25rem}.startseite .home-flow-v7__stage--request .home-flow-v7__disc{width:5rem;height:5rem}}
@media (max-width:980px){.startseite .home-hero.home-hero-v7{grid-template-columns:1fr;min-height:auto;gap:1.8rem;padding-block:1.4rem 2rem}.startseite .home-hero-v7>.home-hero-copy{grid-column:1;grid-row:1}.startseite .home-title-primary{font-size:clamp(3.1rem,8vw,4.8rem)}.startseite .home-title-secondary{font-size:clamp(2.3rem,6.4vw,3.4rem)}.startseite .home-flow-v7{grid-column:1;grid-row:2;width:min(100%,42rem);min-height:22rem;justify-self:center}.startseite .home-flow-v7__note{top:0}}
@media (max-width:640px){.startseite .home-title-primary{font-size:clamp(2.7rem,13vw,4rem)}.startseite .home-title-secondary{font-size:clamp(2rem,9.4vw,2.85rem)}.startseite .home-hero-v7 .aufriss{font-size:1rem}.startseite .home-trust-row{gap:.55rem .72rem;margin-top:1.1rem}.startseite .home-trust-row span+span::after{display:none}.startseite .home-portrait{grid-template-columns:2.45rem minmax(0,1fr)!important;padding-top:.75rem!important}.startseite .home-portrait img{width:2.45rem!important;height:2.45rem!important}.startseite .home-flow-v7{min-height:19rem;width:calc(100% + .5rem);margin-inline:-.25rem}.startseite .home-flow-v7__track{grid-template-columns:3.35rem repeat(2,minmax(0,1fr)) 2.35rem minmax(0,1fr);gap:.06rem;padding-block:3.5rem 3rem}.startseite .home-flow-v7__sources{gap:.22rem}.startseite .home-flow-v7__source{padding:.18rem .25rem;font-size:.35rem}.startseite .home-flow-v7__source svg,.startseite .home-flow-v7__source--ads::before{display:none}.startseite .home-flow-v7__rail{left:12%;right:2%}.startseite .home-flow-v7__disc{width:3rem;height:3rem}.startseite .home-flow-v7__stage--request .home-flow-v7__disc{width:3.45rem;height:3.45rem;box-shadow:0 0 0 .5rem color-mix(in srgb,var(--stempel) 4%,transparent),0 0 0 1rem color-mix(in srgb,var(--stempel) 2%,transparent),0 9px 22px color-mix(in srgb,var(--stempel) 8%,transparent)}.startseite .home-flow-v7__stage--request .home-flow-v7__disc::before{inset:-1.2rem}.startseite .home-flow-v7__disc svg{width:1.15rem;height:1.15rem}.startseite .home-flow-v7__stage strong{font-size:.6rem}.startseite .home-flow-v7__filter-icon{width:2.25rem;height:2.25rem}.startseite .home-flow-v7__filter strong{display:none}.startseite .home-flow-v7__tags{display:none}.startseite .home-flow-v7__note{font-size:.39rem}.startseite .home-flow-v7__arrow{font-size:.8rem}.startseite .home-flow-v7__arrow--a{left:43%}.startseite .home-flow-v7__arrow--b{left:66%}.startseite .home-flow-v7__arrow--c{left:83%}}
@media (prefers-reduced-motion:no-preference){.startseite .home-flow-v7__stage--request .home-flow-v7__disc::before{animation:home-v7-ring 1.35s var(--ease-aus) .35s 1 both}.startseite .home-flow-v7__arrow--b{animation:home-v7-arrow 1s var(--ease-aus) .5s 1 both}}
@media (prefers-reduced-motion:reduce){.startseite .home-hero-v7 .tun{transition-duration:0s}}
@keyframes home-v7-ring{0%{opacity:0;transform:scale(.9)}100%{opacity:1;transform:scale(1)}}
@keyframes home-v7-arrow{0%{opacity:.15;transform:translate(-60%,-53%)}100%{opacity:1;transform:translate(-50%,-53%)}}
`;
        document.head.appendChild(style);
    }

    function source(label, kind, className) {
        var el = document.createElement('span');
        el.className = 'home-flow-v7__source ' + (className || '');
        if (kind) {
            el.innerHTML = icon(kind) + '<span>' + label + '</span>';
        } else {
            el.textContent = label;
        }
        return el;
    }

    function stage(kind, label, className) {
        var el = document.createElement('div');
        el.className = 'home-flow-v7__stage ' + (className || '');
        el.innerHTML = '<span class="home-flow-v7__disc">' + icon(kind) + '</span><strong>' + label + '</strong>';
        return el;
    }

    function buildFlow() {
        var visual = document.createElement('figure');
        visual.className = 'home-flow-v7';
        visual.setAttribute('role', 'img');
        visual.setAttribute('aria-label', 'Ads, SEO und Empfehlungen führen über die Website zu einer Anfrage. Die Anfrage wird nach Region, Leistung und Qualität segmentiert und anschließend ins CRM übergeben.');

        var grid = document.createElement('div');
        grid.className = 'home-flow-v7__grid';
        grid.setAttribute('aria-hidden', 'true');

        var note = document.createElement('div');
        note.className = 'home-flow-v7__note';
        note.setAttribute('aria-hidden', 'true');
        note.innerHTML = 'Messbar<br>bis ins CRM';

        var track = document.createElement('div');
        track.className = 'home-flow-v7__track';
        track.setAttribute('aria-hidden', 'true');

        var rail = document.createElement('span');
        rail.className = 'home-flow-v7__rail';

        var sources = document.createElement('div');
        sources.className = 'home-flow-v7__sources';
        sources.appendChild(source('Ads', null, 'home-flow-v7__source--ads'));
        sources.appendChild(source('SEO', 'search'));
        sources.appendChild(source('Empfehlung', 'people'));

        var website = stage('website', 'Website', 'home-flow-v7__stage--website');
        var request = stage('request', 'Anfrage', 'home-flow-v7__stage--request');

        var filter = document.createElement('div');
        filter.className = 'home-flow-v7__filter';
        filter.innerHTML = '<span class="home-flow-v7__filter-icon">' + icon('filter') + '</span><strong>Segmentierung</strong><span class="home-flow-v7__tags"><span>Region</span><span>Leistung</span><span>Qualität</span></span>';

        var crm = stage('crm', 'CRM', 'home-flow-v7__stage--crm');

        track.appendChild(rail);
        track.appendChild(sources);
        track.appendChild(website);
        track.appendChild(request);
        track.appendChild(filter);
        track.appendChild(crm);
        track.insertAdjacentHTML('beforeend', '<span class="home-flow-v7__arrow home-flow-v7__arrow--a" aria-hidden="true">→</span><span class="home-flow-v7__arrow home-flow-v7__arrow--b" aria-hidden="true">→</span><span class="home-flow-v7__arrow home-flow-v7__arrow--c" aria-hidden="true">→</span>');

        visual.appendChild(grid);
        visual.appendChild(note);
        visual.appendChild(track);
        return visual;
    }

    function initHero() {
        var hero = document.querySelector('.startseite .home-hero');
        if (!hero || hero.dataset.heroV7 === 'true') {
            return;
        }

        var copy = hero.firstElementChild;
        if (!copy) {
            return;
        }

        hero.dataset.heroV7 = 'true';
        hero.classList.remove('home-hero-v6');
        hero.classList.add('home-hero-v7');
        copy.classList.add('home-hero-copy');
        mountStyles();

        hero.querySelectorAll('.home-system-visual,.home-leadflow,.home-flow-v7').forEach(function (oldVisual) {
            oldVisual.remove();
        });

        var eyebrow = copy.querySelector('.gegenstand');
        if (eyebrow) {
            eyebrow.textContent = 'WordPress · Tracking · CRM';
        }

        var heading = copy.querySelector('h1');
        if (heading) {
            heading.innerHTML = '<span class="home-title-primary">WordPress<br>Freelancer<br>Hannover<span class="home-title-stop">.</span></span><span class="home-title-secondary">Von der Website<br>bis zur Anfrage<span class="home-title-stop">.</span></span>';
        }

        var intro = copy.querySelector('.aufriss');
        if (intro) {
            intro.textContent = 'Ich entwickle WordPress-Websites, die Angebote verständlich machen und Anfragen sauber bis ins CRM führen. Direkt mit mir – ohne Übergabe an ein fremdes Entwicklerteam.';
        }

        var reply = copy.querySelector('.home-reply');
        var trust = copy.querySelector('.home-trust-row');
        if (!trust) {
            trust = document.createElement('div');
            trust.className = 'home-trust-row';
            trust.innerHTML = '<span>Antwort in 2 Werktagen</span><span>Klare Projektpreise</span><span>Direkt mit dem Entwickler</span>';
            if (reply) {
                reply.replaceWith(trust);
            } else {
                copy.appendChild(trust);
            }
        }

        var portrait = hero.querySelector(':scope > .home-portrait') || copy.querySelector('.home-portrait');
        if (portrait) {
            var portraitTitle = portrait.querySelector('figcaption strong');
            var portraitText = portrait.querySelector('figcaption span');
            if (portraitTitle) {
                portraitTitle.textContent = 'Direkt mit Haşim Üner.';
            }
            if (portraitText) {
                portraitText.textContent = 'Konzeption · Entwicklung · Übergabe';
            }
            copy.appendChild(portrait);
        }

        hero.appendChild(buildFlow());
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initHero, { once: true });
    } else {
        initHero();
    }
})();