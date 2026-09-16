/**
 * Startseite – Hero V6.
 *
 * Leistung zuerst. Die rechte Seite erklärt die Strecke nur noch als präzise
 * Prozesszeichnung: Quellen → Website → Anfrage → Segmentierung → CRM.
 * Keine externen Hero-Bildassets, keine dauerhafte dekorative Animation.
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
        if (document.getElementById('home-hero-v6-styles')) {
            return;
        }

        var style = document.createElement('style');
        style.id = 'home-hero-v6-styles';
        style.textContent = `
.startseite .home-hero.home-hero-v6::before{content:''!important;display:none!important;background:none!important;background-image:none!important}
.startseite .home-hero.home-hero-v6{position:relative;display:grid;grid-template-columns:minmax(0,1.42fr) minmax(31rem,.94fr);gap:clamp(2.6rem,4.8vw,5.8rem);align-items:center;min-height:min(47rem,calc(100svh - 5.5rem));padding-block:clamp(2.5rem,4.6vw,5rem);isolation:isolate}
.startseite .home-hero-v6>.home-hero-copy{grid-column:1;grid-row:1;min-width:0;position:relative;z-index:3}
.startseite .home-hero-v6 .gegenstand{margin:0 0 clamp(1.45rem,2vw,2.15rem);color:var(--stempel);font-family:var(--mono);font-size:clamp(.66rem,.62rem + .12vw,.76rem);font-weight:650;letter-spacing:.2em;line-height:1.45;text-transform:uppercase}
.startseite .home-hero-v6 h1{margin:0!important;max-width:none!important;font-size:inherit!important;line-height:1!important;letter-spacing:0!important}
.startseite .home-title-primary,.startseite .home-title-secondary{display:block}
.startseite .home-title-primary{max-width:9.3ch;font-family:var(--sans);font-size:clamp(3.65rem,4.75vw,5.55rem);font-weight:650;line-height:.89;letter-spacing:-.058em;color:var(--tinte)}
.startseite .home-title-secondary{max-width:11.9ch;margin-top:.22em;font-family:var(--sans);font-size:clamp(2.6rem,3.45vw,3.95rem);font-weight:470;line-height:.96;letter-spacing:-.047em;color:color-mix(in srgb,var(--tinte) 68%,var(--papier))}
.startseite .home-title-stop{color:var(--stempel)}
.startseite .home-hero-v6 .aufriss{max-width:42ch;margin-top:clamp(1.55rem,2vw,2.1rem);font-size:clamp(1rem,.95rem + .18vw,1.12rem);line-height:1.58;color:color-mix(in srgb,var(--tinte) 88%,var(--papier))}
.startseite .home-hero-v6 .ausgang{display:flex;flex-wrap:wrap;gap:.8rem;margin-top:clamp(1.55rem,2vw,2.1rem)}
.startseite .home-hero-v6 .tun{min-height:3.2rem;padding-inline:1.35rem;border-radius:2px;transition:transform var(--t-norm) var(--ease-aus),box-shadow var(--t-norm) var(--ease-aus),border-color var(--t-norm) var(--ease-aus)}
.startseite .home-hero-v6 .tun:not(.still){box-shadow:0 9px 22px color-mix(in srgb,var(--stempel) 15%,transparent)}
.startseite .home-hero-v6 .tun.still{background:transparent;border-color:color-mix(in srgb,var(--tinte) 43%,transparent)}
@media (hover:hover) and (pointer:fine){.startseite .home-hero-v6 .tun:hover{transform:translateY(-2px)}.startseite .home-hero-v6 .tun:not(.still):hover{box-shadow:0 13px 28px color-mix(in srgb,var(--stempel) 21%,transparent)}.startseite .home-hero-v6 .tun.still:hover{border-color:var(--tinte)}}
.startseite .home-trust-row{display:flex;flex-wrap:wrap;align-items:center;gap:.58rem .86rem;margin-top:1rem;color:var(--grau);font-size:.77rem;line-height:1.35}
.startseite .home-trust-row span{display:inline-flex;align-items:center;gap:.4rem;white-space:nowrap}
.startseite .home-trust-row span::before{content:'✓';display:grid;place-items:center;width:1rem;height:1rem;border:1px solid var(--stempel);border-radius:50%;color:var(--stempel);font-size:.57rem;font-weight:800;line-height:1}
.startseite .home-trust-row span+span::after{content:'';order:-1;width:1px;height:.78rem;margin-right:.12rem;background:var(--haar)}
.startseite .home-portrait{position:relative!important;inset:auto!important;z-index:2!important;width:min(17.25rem,100%)!important;display:grid!important;grid-template-columns:3.35rem minmax(0,1fr)!important;gap:.72rem!important;align-items:center!important;margin:1rem 0 0!important;padding:.62rem!important;border:1px solid color-mix(in srgb,var(--tinte) 9%,var(--haar))!important;border-radius:8px!important;background:var(--papier)!important;box-shadow:0 8px 24px color-mix(in srgb,var(--tinte) 6%,transparent)!important;backdrop-filter:none!important;-webkit-backdrop-filter:none!important}
.startseite .home-portrait img{width:3.35rem!important;height:3.35rem!important;aspect-ratio:1;object-fit:cover;object-position:50% 18%;border-radius:6px!important}
.startseite .home-portrait figcaption{display:grid!important;gap:.1rem!important;padding:0!important;font-size:.67rem!important;line-height:1.3!important}
.startseite .home-portrait figcaption strong{font-size:.75rem!important;color:var(--tinte)}
.startseite .home-portrait figcaption span{color:var(--grau)!important;font-size:.62rem!important}
.startseite .home-portrait figcaption a{justify-self:start;font-size:.62rem!important}

.startseite .home-leadflow{grid-column:2;grid-row:1;position:relative;width:100%;min-width:0;min-height:29rem;display:grid;align-content:center;isolation:isolate}
.startseite .home-leadflow__grid{position:absolute;inset:7% -3% 5% -5%;z-index:-3;opacity:.48;pointer-events:none;background-image:linear-gradient(color-mix(in srgb,var(--tinte) 3.8%,transparent) 1px,transparent 1px),linear-gradient(90deg,color-mix(in srgb,var(--tinte) 3.8%,transparent) 1px,transparent 1px);background-size:46px 46px;mask-image:linear-gradient(90deg,transparent 0,#000 12%,#000 88%,transparent 100%);-webkit-mask-image:linear-gradient(90deg,transparent 0,#000 12%,#000 88%,transparent 100%)}
.startseite .home-leadflow__note{position:absolute;right:.15rem;top:1.3rem;color:color-mix(in srgb,var(--grau) 82%,transparent);font-family:var(--mono);font-size:.5rem;font-weight:600;letter-spacing:.16em;line-height:1.55;text-transform:uppercase;text-align:right}
.startseite .home-leadflow__note::after{content:'';display:block;width:2rem;height:1px;margin:.5rem 0 0 auto;background:var(--stempel);opacity:.75}
.startseite .home-leadflow__track{position:relative;display:grid;grid-template-columns:minmax(5.7rem,.8fr) repeat(4,minmax(0,1fr));gap:clamp(.25rem,.55vw,.5rem);align-items:center;padding-block:3.25rem 4rem}
.startseite .home-leadflow__rail{position:absolute;left:15%;right:4%;top:50%;height:1px;background:linear-gradient(90deg,color-mix(in srgb,var(--stempel) 34%,transparent),var(--stempel) 18%,var(--stempel) 82%,color-mix(in srgb,var(--stempel) 30%,transparent));transform:translateY(-50%);z-index:-1}
.startseite .home-leadflow__signal{position:absolute;left:15%;top:50%;width:.42rem;height:.42rem;border-radius:50%;background:var(--stempel);box-shadow:0 0 0 .34rem color-mix(in srgb,var(--stempel) 7%,transparent);transform:translate(-50%,-50%);z-index:4}
.startseite .home-leadflow__sources{position:relative;display:grid;gap:.44rem;align-content:center;z-index:2}
.startseite .home-leadflow__source{display:flex;align-items:center;gap:.38rem;width:max-content;max-width:100%;min-width:4.6rem;padding:.31rem .52rem;border:1px solid color-mix(in srgb,var(--tinte) 9%,var(--haar));border-radius:999px;background:var(--papier);box-shadow:0 4px 12px color-mix(in srgb,var(--tinte) 4%,transparent);font-family:var(--mono);font-size:.5rem;color:var(--grau);white-space:nowrap}
.startseite .home-leadflow__source svg{width:.85rem;height:.85rem;stroke:var(--stempel);fill:none;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round;flex:0 0 auto}
.startseite .home-leadflow__source--ads::before{content:'G';display:grid;place-items:center;width:.85rem;height:.85rem;color:var(--stempel);font-family:var(--sans);font-size:.7rem;font-weight:750;line-height:1}
.startseite .home-leadflow__source-lines{position:absolute;left:5.5rem;top:50%;width:4.5rem;height:7.8rem;transform:translateY(-50%);overflow:visible;z-index:1;pointer-events:none}
.startseite .home-leadflow__source-lines path{fill:none;stroke:color-mix(in srgb,var(--tinte) 25%,transparent);stroke-width:1;vector-effect:non-scaling-stroke}
.startseite .home-leadflow__source-lines circle{fill:var(--stempel)}
.startseite .home-leadflow__stage{position:relative;display:grid;justify-items:center;align-content:center;gap:.48rem;min-width:0;text-align:center;z-index:2}
.startseite .home-leadflow__disc{position:relative;display:grid;place-items:center;width:4.45rem;height:4.45rem;border:1px solid color-mix(in srgb,var(--tinte) 9%,var(--haar));border-radius:50%;background:var(--papier);box-shadow:0 10px 24px color-mix(in srgb,var(--tinte) 6%,transparent),inset 0 0 0 .42rem color-mix(in srgb,var(--tinte) 2.4%,transparent)}
.startseite .home-leadflow__disc svg{width:1.6rem;height:1.6rem;stroke:currentColor;color:color-mix(in srgb,var(--tinte) 78%,var(--papier));fill:none;stroke-width:1.65;stroke-linecap:round;stroke-linejoin:round}
.startseite .home-leadflow__stage strong{font-size:.73rem;line-height:1.15;color:var(--tinte);font-weight:700;letter-spacing:-.01em}
.startseite .home-leadflow__stage small{max-width:8.5ch;color:color-mix(in srgb,var(--grau) 80%,transparent);font-family:var(--mono);font-size:.42rem;letter-spacing:.1em;line-height:1.38;text-transform:uppercase}
.startseite .home-leadflow__stage--request .home-leadflow__disc{width:5rem;height:5rem;border-color:color-mix(in srgb,var(--stempel) 48%,var(--haar));box-shadow:0 0 0 .8rem color-mix(in srgb,var(--stempel) 4.2%,transparent),0 0 0 1.55rem color-mix(in srgb,var(--stempel) 2.1%,transparent),0 14px 32px color-mix(in srgb,var(--stempel) 9%,transparent)}
.startseite .home-leadflow__stage--request .home-leadflow__disc::before,.startseite .home-leadflow__stage--request .home-leadflow__disc::after{content:'';position:absolute;border-radius:50%;pointer-events:none}
.startseite .home-leadflow__stage--request .home-leadflow__disc::before{inset:-2rem;border:1px dashed color-mix(in srgb,var(--stempel) 19%,transparent)}
.startseite .home-leadflow__stage--request .home-leadflow__disc::after{inset:-3rem;border:1px solid color-mix(in srgb,var(--stempel) 10%,transparent)}
.startseite .home-leadflow__stage--segment .home-leadflow__disc{width:4.2rem;height:4.2rem}
.startseite .home-leadflow__tags{display:flex;justify-content:center;gap:.18rem;flex-wrap:nowrap;margin-top:.05rem}
.startseite .home-leadflow__tags span{padding:.18rem .34rem;border:1px solid var(--haar);border-radius:999px;background:var(--papier);font-family:var(--mono);font-size:.39rem;color:var(--grau);white-space:nowrap}
.startseite .home-leadflow__outcomes{position:absolute;left:6%;right:1%;bottom:.8rem;display:grid;grid-template-columns:1fr auto 1fr auto 1fr;align-items:center;gap:.65rem;color:color-mix(in srgb,var(--grau) 72%,transparent);font-family:var(--mono);font-size:.43rem;letter-spacing:.14em;text-transform:uppercase;white-space:nowrap}
.startseite .home-leadflow__outcomes i{display:block;height:1px;background:var(--haar);min-width:1rem}
.startseite .home-leadflow__outcomes span:nth-of-type(1){text-align:left}.startseite .home-leadflow__outcomes span:nth-of-type(2){text-align:center}.startseite .home-leadflow__outcomes span:nth-of-type(3){text-align:right}

@media (max-width:1240px){.startseite .home-hero.home-hero-v6{grid-template-columns:minmax(0,1.28fr) minmax(27rem,.9fr);gap:clamp(2rem,3vw,3.2rem)}.startseite .home-title-primary{font-size:clamp(3.3rem,4.65vw,5rem)}.startseite .home-title-secondary{font-size:clamp(2.45rem,3.35vw,3.55rem)}.startseite .home-leadflow__disc{width:4rem;height:4rem}.startseite .home-leadflow__stage--request .home-leadflow__disc{width:4.55rem;height:4.55rem}.startseite .home-leadflow__track{grid-template-columns:minmax(5.1rem,.78fr) repeat(4,minmax(0,1fr))}}
@media (max-width:980px){.startseite .home-hero.home-hero-v6{grid-template-columns:1fr;min-height:auto;gap:2rem;padding-block:2rem}.startseite .home-hero-v6>.home-hero-copy{grid-column:1;grid-row:1}.startseite .home-title-primary{font-size:clamp(3.2rem,8vw,5rem)}.startseite .home-title-secondary{font-size:clamp(2.35rem,6.5vw,3.5rem)}.startseite .home-leadflow{grid-column:1;grid-row:2;width:min(100%,44rem);min-height:24rem;justify-self:center}.startseite .home-leadflow__note{top:.4rem}.startseite .home-leadflow__outcomes{bottom:.35rem}}
@media (max-width:640px){.startseite .home-title-primary{font-size:clamp(2.75rem,13vw,4.1rem)}.startseite .home-title-secondary{font-size:clamp(2.05rem,9.6vw,2.95rem)}.startseite .home-hero-v6 .aufriss{font-size:1rem}.startseite .home-trust-row{gap:.5rem .68rem}.startseite .home-trust-row span+span::after{display:none}.startseite .home-portrait{width:min(16rem,100%)!important}.startseite .home-leadflow{min-height:22rem;width:calc(100% + .5rem);margin-inline:-.25rem}.startseite .home-leadflow__track{grid-template-columns:3.85rem repeat(4,minmax(0,1fr));gap:.08rem;padding-block:3rem 4.5rem}.startseite .home-leadflow__sources{gap:.25rem}.startseite .home-leadflow__source{min-width:auto;padding:.2rem .28rem;font-size:.36rem}.startseite .home-leadflow__source svg,.startseite .home-leadflow__source--ads::before{display:none}.startseite .home-leadflow__source-lines{display:none}.startseite .home-leadflow__rail{left:12%;right:2%}.startseite .home-leadflow__signal{left:12%}.startseite .home-leadflow__disc{width:3rem;height:3rem}.startseite .home-leadflow__stage--request .home-leadflow__disc{width:3.35rem;height:3.35rem;box-shadow:0 0 0 .5rem color-mix(in srgb,var(--stempel) 4%,transparent),0 0 0 1rem color-mix(in srgb,var(--stempel) 2%,transparent),0 9px 22px color-mix(in srgb,var(--stempel) 8%,transparent)}.startseite .home-leadflow__stage--request .home-leadflow__disc::before{inset:-1.2rem}.startseite .home-leadflow__stage--request .home-leadflow__disc::after{display:none}.startseite .home-leadflow__stage--segment .home-leadflow__disc{width:2.9rem;height:2.9rem}.startseite .home-leadflow__disc svg{width:1.12rem;height:1.12rem}.startseite .home-leadflow__stage strong{font-size:.58rem}.startseite .home-leadflow__stage small{display:none}.startseite .home-leadflow__tags{display:none}.startseite .home-leadflow__note{font-size:.4rem;right:.2rem}.startseite .home-leadflow__outcomes{grid-template-columns:1fr;gap:.22rem;left:0;right:0;bottom:0;text-align:center;font-size:.36rem}.startseite .home-leadflow__outcomes i,.startseite .home-leadflow__outcomes span:nth-of-type(1),.startseite .home-leadflow__outcomes span:nth-of-type(3){display:none}.startseite .home-leadflow__outcomes span:nth-of-type(2){text-align:center}}
@media (prefers-reduced-motion:no-preference){.startseite .home-leadflow__signal{animation:home-v6-signal 1.8s var(--ease-weich) .45s 1 both}.startseite .home-leadflow__stage--request .home-leadflow__disc::before{animation:home-v6-ring 1.6s var(--ease-aus) .65s 1 both}}
@media (prefers-reduced-motion:reduce){.startseite .home-hero-v6 .tun{transition-duration:0s}.startseite .home-leadflow__signal{opacity:.65}}
@keyframes home-v6-signal{0%{left:15%;opacity:.35}35%{opacity:1}100%{left:78%;opacity:.65}}
@keyframes home-v6-ring{0%{opacity:0;transform:scale(.88)}100%{opacity:1;transform:scale(1)}}
`;
        document.head.appendChild(style);
    }

    function makeSource(label, kind, className) {
        var el = document.createElement('span');
        el.className = 'home-leadflow__source ' + (className || '');
        if (kind) {
            el.innerHTML = icon(kind) + '<span>' + label + '</span>';
        } else {
            el.textContent = label;
        }
        return el;
    }

    function makeStage(kind, label, subline, className) {
        var el = document.createElement('div');
        el.className = 'home-leadflow__stage ' + (className || '');
        el.innerHTML = '<span class="home-leadflow__disc">' + icon(kind) + '</span><strong>' + label + '</strong>' + (subline ? '<small>' + subline + '</small>' : '');
        return el;
    }

    function buildLeadFlow() {
        var visual = document.createElement('figure');
        visual.className = 'home-leadflow';
        visual.setAttribute('role', 'img');
        visual.setAttribute('aria-label', 'Ads, SEO und Empfehlungen führen über die Website zur Anfrage. Die Anfrage wird nach Region, Leistung und Qualität segmentiert und anschließend ins CRM übergeben.');

        var grid = document.createElement('div');
        grid.className = 'home-leadflow__grid';
        grid.setAttribute('aria-hidden', 'true');

        var note = document.createElement('div');
        note.className = 'home-leadflow__note';
        note.setAttribute('aria-hidden', 'true');
        note.innerHTML = 'Messbar<br>bis ins CRM';

        var track = document.createElement('div');
        track.className = 'home-leadflow__track';
        track.setAttribute('aria-hidden', 'true');

        var rail = document.createElement('span');
        rail.className = 'home-leadflow__rail';

        var signal = document.createElement('span');
        signal.className = 'home-leadflow__signal';

        var sources = document.createElement('div');
        sources.className = 'home-leadflow__sources';
        sources.appendChild(makeSource('Ads', null, 'home-leadflow__source--ads'));
        sources.appendChild(makeSource('SEO', 'search'));
        sources.appendChild(makeSource('Empfehlung', 'people'));
        sources.insertAdjacentHTML('beforeend', '<svg class="home-leadflow__source-lines" viewBox="0 0 72 124" aria-hidden="true"><path d="M2 20 C30 20 36 62 70 62"></path><path d="M2 62 C30 62 40 62 70 62"></path><path d="M2 104 C30 104 36 62 70 62"></path><circle cx="2" cy="20" r="2"></circle><circle cx="2" cy="62" r="2"></circle><circle cx="2" cy="104" r="2"></circle><circle cx="70" cy="62" r="2"></circle></svg>');

        var website = makeStage('website', 'Website', 'Besucher werden Leads', 'home-leadflow__stage--website');
        var request = makeStage('request', 'Anfrage', 'Qualifizierte Anfragen', 'home-leadflow__stage--request');
        var segment = makeStage('filter', 'Segmentierung', 'Region · Leistung · Qualität', 'home-leadflow__stage--segment');
        var tags = document.createElement('span');
        tags.className = 'home-leadflow__tags';
        tags.innerHTML = '<span>Region</span><span>Leistung</span><span>Qualität</span>';
        segment.appendChild(tags);
        var crm = makeStage('crm', 'CRM', 'Leads im System', 'home-leadflow__stage--crm');

        track.appendChild(rail);
        track.appendChild(signal);
        track.appendChild(sources);
        track.appendChild(website);
        track.appendChild(request);
        track.appendChild(segment);
        track.appendChild(crm);

        var outcomes = document.createElement('div');
        outcomes.className = 'home-leadflow__outcomes';
        outcomes.setAttribute('aria-hidden', 'true');
        outcomes.innerHTML = '<span>Mehr Sichtbarkeit</span><i></i><span>Mehr Anfragen</span><i></i><span>Mehr Umsatz</span>';

        visual.appendChild(grid);
        visual.appendChild(note);
        visual.appendChild(track);
        visual.appendChild(outcomes);
        return visual;
    }

    function initHero() {
        var hero = document.querySelector('.startseite .home-hero');
        if (!hero || hero.dataset.heroV6 === 'true') {
            return;
        }

        var copy = hero.firstElementChild;
        if (!copy) {
            return;
        }

        hero.dataset.heroV6 = 'true';
        hero.classList.add('home-hero-v6');
        copy.classList.add('home-hero-copy');
        mountStyles();

        hero.querySelectorAll('.home-system-visual,.home-leadflow').forEach(function (oldVisual) {
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
                portraitTitle.textContent = 'Direkt mit Haşim.';
            }
            if (portraitText) {
                portraitText.textContent = 'Konzeption, Entwicklung, Übergabe.';
            }
            copy.appendChild(portrait);
        }

        hero.appendChild(buildLeadFlow());
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initHero, { once: true });
    } else {
        initHero();
    }
})();