/**
 * Startseite.
 *
 * Hero V5: Leistung zuerst, reduzierte Prozessarchitektur, persönliche Vertrauenskomponente.
 * Progressive Enhancement: Ohne JavaScript bleiben Copy, CTA und Portrait sichtbar.
 */
(function () {
    'use strict';

    function mountHeroStyles() {
        if (document.getElementById('home-hero-v5-styles')) {
            return;
        }

        var style = document.createElement('style');
        style.id = 'home-hero-v5-styles';
        style.textContent = `
.startseite .home-hero::before{content:none!important;display:none!important}
.startseite .home-hero{position:relative;display:grid;grid-template-columns:minmax(0,1.55fr) minmax(23rem,.82fr);gap:clamp(2.5rem,4.5vw,5.25rem);align-items:center;isolation:isolate;padding-block:clamp(.55rem,1.2vw,1.25rem)}
.startseite .home-hero>.home-hero-copy{grid-column:1;grid-row:1;min-width:0;position:relative;z-index:3}
.startseite .home-hero .gegenstand{margin:0 0 clamp(1.25rem,1.7vw,1.7rem);color:var(--stempel);font-family:var(--mono);font-size:clamp(.66rem,.62rem + .12vw,.75rem);font-weight:600;letter-spacing:.18em;line-height:1.45;text-transform:uppercase}
.startseite .home-hero h1{margin:0!important;max-width:none!important;font-size:inherit!important;line-height:1!important;letter-spacing:0!important}
.startseite .home-title-primary,.startseite .home-title-secondary{display:block}
.startseite .home-title-primary{max-width:9.4ch;font-family:var(--sans);font-size:clamp(3.35rem,4.55vw,5.25rem);font-weight:650;line-height:.89;letter-spacing:-.055em;color:var(--tinte)}
.startseite .home-title-secondary{max-width:12ch;margin-top:.22em;font-family:var(--sans);font-size:clamp(2.55rem,3.28vw,3.7rem);font-weight:470;line-height:.97;letter-spacing:-.044em;color:color-mix(in srgb,var(--tinte) 67%,var(--papier))}
.startseite .home-title-stop{color:var(--stempel)}
.startseite .home-hero .aufriss{max-width:43ch;margin-top:clamp(1.4rem,1.8vw,1.9rem);font-size:clamp(1rem,.95rem + .16vw,1.1rem);line-height:1.58;color:color-mix(in srgb,var(--tinte) 88%,var(--papier))}
.startseite .home-hero .ausgang{display:flex;flex-wrap:wrap;gap:.78rem;margin-top:clamp(1.45rem,1.9vw,2rem)}
.startseite .home-hero .tun{min-height:3.18rem;padding-inline:1.3rem;border-radius:2px;transition:transform var(--t-norm) var(--ease-aus),box-shadow var(--t-norm) var(--ease-aus),border-color var(--t-norm) var(--ease-aus)}
.startseite .home-hero .tun:not(.still){box-shadow:0 9px 22px color-mix(in srgb,var(--stempel) 16%,transparent)}
.startseite .home-hero .tun.still{background:transparent;border-color:color-mix(in srgb,var(--tinte) 42%,transparent)}
@media (hover:hover) and (pointer:fine){.startseite .home-hero .tun:hover{transform:translateY(-2px)}.startseite .home-hero .tun:not(.still):hover{box-shadow:0 13px 28px color-mix(in srgb,var(--stempel) 22%,transparent)}.startseite .home-hero .tun.still:hover{border-color:var(--tinte)}}
.startseite .home-trust-row{display:flex;flex-wrap:wrap;align-items:center;gap:.58rem .85rem;margin-top:.95rem;color:var(--grau);font-size:.77rem;line-height:1.35}
.startseite .home-trust-row span{display:inline-flex;align-items:center;gap:.4rem;white-space:nowrap}
.startseite .home-trust-row span::before{content:'✓';display:grid;place-items:center;width:1rem;height:1rem;border:1px solid var(--stempel);border-radius:50%;color:var(--stempel);font-size:.57rem;font-weight:800;line-height:1}
.startseite .home-trust-row span+span::after{content:'';order:-1;width:1px;height:.78rem;margin-right:.12rem;background:var(--haar)}
.startseite .home-portrait{position:relative!important;inset:auto!important;z-index:2!important;width:min(17rem,100%)!important;display:grid!important;grid-template-columns:3.3rem minmax(0,1fr)!important;gap:.68rem!important;align-items:center!important;margin:1rem 0 0!important;padding:.58rem!important;border:1px solid color-mix(in srgb,var(--tinte) 9%,var(--haar))!important;border-radius:8px!important;background:color-mix(in srgb,var(--papier) 97%,transparent)!important;box-shadow:0 10px 24px color-mix(in srgb,var(--tinte) 6%,transparent)!important;backdrop-filter:none!important;-webkit-backdrop-filter:none!important}
.startseite .home-portrait img{width:3.3rem!important;height:3.3rem!important;aspect-ratio:1;object-fit:cover;object-position:50% 18%;border-radius:6px!important}
.startseite .home-portrait figcaption{display:grid!important;gap:.1rem!important;padding:0!important;font-size:.67rem!important;line-height:1.3!important}
.startseite .home-portrait figcaption strong{font-size:.74rem!important;color:var(--tinte)}
.startseite .home-portrait figcaption span{color:var(--grau)!important;font-size:.62rem!important}
.startseite .home-portrait figcaption a{justify-self:start;font-size:.62rem!important}

.startseite .home-system-visual{grid-column:2;grid-row:1;position:relative;z-index:2;width:100%;min-height:24.5rem;align-self:center;justify-self:stretch;overflow:visible}
.startseite .home-flow-grid{position:absolute;inset:8% -5% 9% -7%;opacity:.52;pointer-events:none;background-image:linear-gradient(color-mix(in srgb,var(--tinte) 3.7%,transparent) 1px,transparent 1px),linear-gradient(90deg,color-mix(in srgb,var(--tinte) 3.7%,transparent) 1px,transparent 1px);background-size:44px 44px;mask-image:radial-gradient(ellipse at 52% 48%,#000 22%,transparent 78%);-webkit-mask-image:radial-gradient(ellipse at 52% 48%,#000 22%,transparent 78%)}
.startseite .home-flow-orbit{position:absolute;left:52%;top:43%;width:58%;aspect-ratio:1;border:1px solid color-mix(in srgb,var(--stempel) 10%,transparent);border-radius:50%;transform:translate(-50%,-50%);pointer-events:none}
.startseite .home-flow-orbit::before,.startseite .home-flow-orbit::after{content:'';position:absolute;border-radius:50%;border:1px dashed color-mix(in srgb,var(--stempel) 16%,transparent)}
.startseite .home-flow-orbit::before{inset:19%}.startseite .home-flow-orbit::after{inset:38%;border-style:solid;opacity:.72}
.startseite .home-flow-note{position:absolute;right:1%;top:9%;font-family:var(--mono);font-size:.48rem;font-weight:600;letter-spacing:.14em;line-height:1.5;text-transform:uppercase;color:color-mix(in srgb,var(--grau) 78%,transparent);text-align:right}
.startseite .home-flow-note::after{content:'';display:block;width:1.85rem;height:1px;margin:.48rem 0 0 auto;background:var(--stempel);opacity:.62}
.startseite .home-flow-track{position:absolute;left:-1%;right:0;top:42%;display:grid;grid-template-columns:minmax(3.9rem,.74fr) repeat(2,minmax(0,1fr)) minmax(3.35rem,.78fr) minmax(0,1fr);gap:.34rem;align-items:center;transform:translateY(-50%)}
.startseite .home-flow-track::before{content:'';position:absolute;left:14%;right:4%;top:50%;height:1px;background:linear-gradient(90deg,color-mix(in srgb,var(--stempel) 25%,transparent),var(--stempel) 18%,var(--stempel) 84%,color-mix(in srgb,var(--stempel) 26%,transparent));z-index:0}
.startseite .home-flow-sources{position:relative;z-index:2;display:grid;gap:.34rem;align-content:center}
.startseite .home-flow-sources span{display:flex;align-items:center;gap:.34rem;width:max-content;max-width:100%;padding:.26rem .46rem;border:1px solid color-mix(in srgb,var(--tinte) 8%,var(--haar));border-radius:999px;background:var(--papier);box-shadow:0 3px 9px color-mix(in srgb,var(--tinte) 3%,transparent);font-family:var(--mono);font-size:.49rem;color:var(--grau)}
.startseite .home-flow-sources span::before{content:'';width:.26rem;height:.26rem;flex:0 0 auto;border-radius:50%;background:var(--stempel)}
.startseite .home-flow-stage{position:relative;z-index:2;display:grid;justify-items:center;align-content:center;gap:.46rem;min-width:0;text-align:center}
.startseite .home-flow-stage__icon{position:relative;display:grid;place-items:center;width:3.65rem;height:3.65rem;border:1px solid color-mix(in srgb,var(--tinte) 8%,var(--haar));border-radius:50%;background:var(--papier);box-shadow:0 8px 22px color-mix(in srgb,var(--tinte) 6%,transparent),inset 0 0 0 .36rem color-mix(in srgb,var(--tinte) 2.2%,transparent)}
.startseite .home-flow-stage__icon svg{width:1.34rem;height:1.34rem;stroke:currentColor;color:color-mix(in srgb,var(--tinte) 78%,var(--papier));fill:none;stroke-width:1.65;stroke-linecap:round;stroke-linejoin:round}
.startseite .home-flow-stage strong{font-size:.69rem;line-height:1.18;color:var(--tinte);font-weight:700;letter-spacing:-.01em}
.startseite .home-flow-stage--request .home-flow-stage__icon{width:4rem;height:4rem;border-color:color-mix(in srgb,var(--stempel) 34%,var(--haar));box-shadow:0 0 0 .62rem color-mix(in srgb,var(--stempel) 4.5%,transparent),0 0 0 1.2rem color-mix(in srgb,var(--stempel) 2.2%,transparent),0 11px 26px color-mix(in srgb,var(--stempel) 9%,transparent)}
.startseite .home-flow-stage--request .home-flow-stage__icon::after{content:'';position:absolute;inset:-1.05rem;border:1px dotted color-mix(in srgb,var(--stempel) 20%,transparent);border-radius:50%}
.startseite .home-flow-filter{position:relative;z-index:2;display:grid;justify-items:center;gap:.45rem;align-content:center}
.startseite .home-flow-filter__icon{display:grid;place-items:center;width:2.55rem;height:2.55rem;color:color-mix(in srgb,var(--tinte) 74%,var(--papier))}
.startseite .home-flow-filter__icon svg{width:1.32rem;height:1.32rem;stroke:currentColor;fill:none;stroke-width:1.65;stroke-linecap:round;stroke-linejoin:round}
.startseite .home-segment-tags{display:flex;justify-content:center;gap:.18rem;flex-wrap:nowrap}
.startseite .home-segment-tags span{padding:.18rem .32rem;border:1px solid var(--haar);border-radius:999px;background:color-mix(in srgb,var(--papier) 97%,transparent);font-family:var(--mono);font-size:.4rem;color:var(--grau);white-space:nowrap}
.startseite .home-flow-caption{position:absolute;left:16%;right:3%;top:64%;display:flex;align-items:center;gap:.48rem;color:color-mix(in srgb,var(--grau) 70%,transparent);font-family:var(--mono);font-size:.43rem;letter-spacing:.1em;text-transform:uppercase;white-space:nowrap}
.startseite .home-flow-caption::before,.startseite .home-flow-caption::after{content:'';height:1px;flex:1;background:var(--haar)}
.startseite .home-flow-signal{position:absolute;z-index:4;left:18%;top:calc(50% - .2rem);width:.4rem;height:.4rem;border-radius:50%;background:var(--stempel);box-shadow:0 0 0 .34rem color-mix(in srgb,var(--stempel) 7%,transparent);opacity:.55}

@media (max-width:1180px){.startseite .home-hero{grid-template-columns:minmax(0,1.42fr) minmax(21rem,.86fr);gap:clamp(2rem,3.4vw,3.4rem)}.startseite .home-title-primary{font-size:clamp(3.15rem,4.5vw,4.9rem)}.startseite .home-title-secondary{font-size:clamp(2.35rem,3.25vw,3.35rem)}.startseite .home-system-visual{min-height:23rem}.startseite .home-flow-stage__icon{width:3.35rem;height:3.35rem}.startseite .home-flow-stage--request .home-flow-stage__icon{width:3.7rem;height:3.7rem}.startseite .home-flow-track{grid-template-columns:minmax(3.5rem,.7fr) repeat(2,minmax(0,1fr)) minmax(3rem,.72fr) minmax(0,1fr)}}
@media (max-width:900px){.startseite .home-hero{grid-template-columns:1fr;gap:1.8rem;padding-block:.65rem}.startseite .home-hero>.home-hero-copy{grid-column:1;grid-row:1}.startseite .home-title-primary{max-width:8.8ch;font-size:clamp(3.05rem,8.5vw,4.7rem)}.startseite .home-title-secondary{max-width:12ch;font-size:clamp(2.25rem,6.7vw,3.35rem)}.startseite .home-system-visual{grid-column:1;grid-row:2;width:min(100%,39rem);min-height:18rem;justify-self:center}.startseite .home-flow-track{top:46%}.startseite .home-flow-caption{top:68%}.startseite .home-flow-note{top:3%}}
@media (max-width:620px){.startseite .home-title-primary{font-size:clamp(2.72rem,13vw,4rem)}.startseite .home-title-secondary{font-size:clamp(2rem,9.4vw,2.85rem)}.startseite .home-hero .aufriss{font-size:1rem}.startseite .home-trust-row{gap:.52rem .68rem}.startseite .home-trust-row span+span::after{display:none}.startseite .home-portrait{width:min(16rem,100%)!important}.startseite .home-system-visual{min-height:16rem;width:calc(100% + .5rem);margin-inline:-.25rem}.startseite .home-flow-track{grid-template-columns:3.2rem repeat(2,minmax(0,1fr)) 2.7rem minmax(0,1fr);gap:.1rem;top:46%}.startseite .home-flow-track::before{left:11%;right:2%}.startseite .home-flow-sources{gap:.22rem}.startseite .home-flow-sources span{padding:.19rem .27rem;font-size:.38rem}.startseite .home-flow-stage__icon{width:2.85rem;height:2.85rem}.startseite .home-flow-stage--request .home-flow-stage__icon{width:3.12rem;height:3.12rem}.startseite .home-flow-stage__icon svg,.startseite .home-flow-filter__icon svg{width:1.08rem;height:1.08rem}.startseite .home-flow-stage strong{font-size:.55rem}.startseite .home-flow-filter__icon{width:2rem;height:2rem}.startseite .home-segment-tags{display:none}.startseite .home-flow-caption{left:8%;right:1%;top:66%;font-size:.34rem;letter-spacing:.06em}.startseite .home-flow-note{display:none}.startseite .home-flow-orbit{width:64%}}
@media (prefers-reduced-motion:no-preference){.startseite .home-flow-signal{animation:home-flow-pass 1.55s var(--ease-aus) .42s 1 both}.startseite .home-flow-stage--request .home-flow-stage__icon{animation:home-request-focus .72s var(--ease-aus) 1.12s 1 both}}
@media (prefers-reduced-motion:reduce){.startseite .home-flow-signal{opacity:.55}.startseite .home-flow-stage--request .home-flow-stage__icon{transform:none}}
@keyframes home-flow-pass{0%{transform:translateX(0) scale(.82);opacity:.18}18%{opacity:.9}100%{transform:translateX(clamp(9rem,18vw,14rem)) scale(1);opacity:.45}}
@keyframes home-request-focus{0%{transform:scale(.97)}100%{transform:scale(1)}}
`;
        document.head.appendChild(style);
    }

    function icon(kind) {
        var icons = {
            website: '<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="2"></rect><path d="M3 8h18"></path><circle cx="6.5" cy="6" r=".7" fill="currentColor" stroke="none"></circle><circle cx="9" cy="6" r=".7" fill="currentColor" stroke="none"></circle></svg>',
            request: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 3h7l4 4v14H7z"></path><path d="M14 3v5h5M10 12h5M10 16h5"></path></svg>',
            filter: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5h16l-6.5 7.2V19l-3 1v-7.8z"></path></svg>',
            crm: '<svg viewBox="0 0 24 24" aria-hidden="true"><ellipse cx="12" cy="5.5" rx="7" ry="2.5"></ellipse><path d="M5 5.5v6c0 1.4 3.1 2.5 7 2.5s7-1.1 7-2.5v-6M5 11.5v6c0 1.4 3.1 2.5 7 2.5s7-1.1 7-2.5v-6"></path></svg>'
        };
        return icons[kind] || '';
    }

    function stage(kind, label, className) {
        var el = document.createElement('div');
        el.className = 'home-flow-stage ' + (className || '');
        el.innerHTML = '<span class="home-flow-stage__icon">' + icon(kind) + '</span><strong>' + label + '</strong>';
        return el;
    }

    function buildSystemVisual() {
        var visual = document.createElement('figure');
        visual.className = 'home-system-visual';
        visual.setAttribute('role', 'img');
        visual.setAttribute('aria-label', 'Ads, SEO und Empfehlungen führen zur Website, dann zur Anfrage, zur Segmentierung nach Region, Leistung und Qualität und anschließend ins CRM.');

        var grid = document.createElement('div');
        grid.className = 'home-flow-grid';
        grid.setAttribute('aria-hidden', 'true');

        var orbit = document.createElement('div');
        orbit.className = 'home-flow-orbit';
        orbit.setAttribute('aria-hidden', 'true');

        var note = document.createElement('div');
        note.className = 'home-flow-note';
        note.setAttribute('aria-hidden', 'true');
        note.innerHTML = 'Messbar<br>bis ins CRM';

        var track = document.createElement('div');
        track.className = 'home-flow-track';
        track.setAttribute('aria-hidden', 'true');

        var sources = document.createElement('div');
        sources.className = 'home-flow-sources';
        ['Ads', 'SEO', 'Empfehlung'].forEach(function (source) {
            var chip = document.createElement('span');
            chip.textContent = source;
            sources.appendChild(chip);
        });

        var website = stage('website', 'Website', 'home-flow-stage--website');
        var request = stage('request', 'Anfrage', 'home-flow-stage--request');

        var filter = document.createElement('div');
        filter.className = 'home-flow-filter';
        filter.innerHTML = '<span class="home-flow-filter__icon">' + icon('filter') + '</span><span class="home-segment-tags"><span>Region</span><span>Leistung</span><span>Qualität</span></span>';

        var crm = stage('crm', 'CRM', 'home-flow-stage--crm');
        var signal = document.createElement('span');
        signal.className = 'home-flow-signal';
        signal.setAttribute('aria-hidden', 'true');

        track.appendChild(sources);
        track.appendChild(website);
        track.appendChild(request);
        track.appendChild(filter);
        track.appendChild(crm);
        track.appendChild(signal);

        var caption = document.createElement('div');
        caption.className = 'home-flow-caption';
        caption.setAttribute('aria-hidden', 'true');
        caption.textContent = 'Quelle → Website → Anfrage → Segmentierung → CRM';

        visual.appendChild(grid);
        visual.appendChild(orbit);
        visual.appendChild(note);
        visual.appendChild(track);
        visual.appendChild(caption);
        return visual;
    }

    function initHero() {
        var hero = document.querySelector('.startseite .home-hero');
        if (!hero || hero.dataset.heroV5 === 'true') {
            return;
        }

        var copy = hero.firstElementChild;
        var portrait = hero.querySelector(':scope > .home-portrait');
        if (!copy) {
            return;
        }

        hero.dataset.heroV5 = 'true';
        copy.classList.add('home-hero-copy');
        mountHeroStyles();

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
        var trust = document.createElement('div');
        trust.className = 'home-trust-row';
        trust.innerHTML = '<span>Antwort in 2 Werktagen</span><span>Klare Projektpreise</span><span>Direkt mit dem Entwickler</span>';
        if (reply) {
            reply.replaceWith(trust);
        } else {
            copy.appendChild(trust);
        }

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

        hero.appendChild(buildSystemVisual());
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initHero, { once: true });
    } else {
        initHero();
    }
})();
