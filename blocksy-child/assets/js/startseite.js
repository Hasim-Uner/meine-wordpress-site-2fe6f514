/**
 * Startseite – Hero V8.
 *
 * Leistung zuerst. Rechts eine klare, großzügige Strecke:
 * Quellen → Website → Anfrage → CRM.
 * Segmentierung liegt als Logik zwischen Anfrage und CRM.
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
        document.getElementById('home-hero-v7-styles')?.remove();
        if (document.getElementById('home-hero-v8-styles')) {
            return;
        }

        var style = document.createElement('style');
        style.id = 'home-hero-v8-styles';
        style.textContent = `
.startseite .home-hero.home-hero-v8::before{content:''!important;display:none!important;background:none!important;background-image:none!important;animation:none!important}
.startseite .home-hero.home-hero-v8{position:relative;display:grid;grid-template-columns:minmax(0,1.06fr) minmax(34rem,1fr);gap:clamp(1.35rem,2.1vw,2.4rem);align-items:start;min-height:min(42rem,calc(100svh - 5.5rem));padding-block:clamp(1.25rem,1.9vw,1.9rem);isolation:isolate}
.startseite .home-hero-v8>.home-hero-copy{grid-column:1;grid-row:1;min-width:0;position:relative;z-index:3}
.startseite .home-hero-v8 .gegenstand{margin:0 0 clamp(1.05rem,1.35vw,1.35rem);color:var(--stempel);font-family:var(--mono);font-size:clamp(.62rem,.59rem + .1vw,.7rem);font-weight:650;letter-spacing:.17em;line-height:1.4;text-transform:uppercase}
.startseite .home-hero-v8 h1{margin:0!important;max-width:none!important;font-size:inherit!important;line-height:1!important;letter-spacing:0!important}
.startseite .home-title-primary,.startseite .home-title-secondary{display:block}
.startseite .home-title-primary{max-width:9.25ch;font-family:var(--sans);font-size:clamp(3.55rem,4.55vw,5.2rem);font-weight:650;line-height:.92;letter-spacing:-.056em;color:var(--tinte)}
.startseite .home-title-secondary{max-width:11.9ch;margin-top:.25em;font-family:var(--sans);font-size:clamp(2.5rem,3.25vw,3.7rem);font-weight:470;line-height:.99;letter-spacing:-.044em;color:color-mix(in srgb,var(--tinte) 72%,var(--papier))}
.startseite .home-title-stop{color:var(--stempel)}
.startseite .home-hero-v8 .aufriss{max-width:40ch;margin-top:clamp(1.7rem,2.1vw,2rem);font-size:clamp(1rem,.95rem + .16vw,1.1rem);line-height:1.58;color:color-mix(in srgb,var(--tinte) 94%,var(--papier))}
.startseite .home-hero-v8 .ausgang{display:flex;flex-wrap:wrap;gap:.82rem;margin-top:clamp(1.55rem,1.9vw,1.85rem)}
.startseite .home-hero-v8 .tun{min-height:3.18rem;padding-inline:1.35rem;border-radius:2px;transition:transform var(--t-norm) var(--ease-aus),box-shadow var(--t-norm) var(--ease-aus),border-color var(--t-norm) var(--ease-aus),color var(--t-norm) var(--ease-aus)}
.startseite .home-hero-v8 .tun:not(.still){box-shadow:0 9px 22px color-mix(in srgb,var(--stempel) 14%,transparent)}
.startseite .home-hero-v8 .tun.still{background:transparent;border-color:color-mix(in srgb,var(--tinte) 60%,transparent);color:var(--tinte)}
@media (hover:hover) and (pointer:fine){.startseite .home-hero-v8 .tun:hover{transform:translateY(-2px)}.startseite .home-hero-v8 .tun:not(.still):hover{box-shadow:0 13px 28px color-mix(in srgb,var(--stempel) 20%,transparent)}.startseite .home-hero-v8 .tun.still:hover{border-color:var(--tinte)}}
.startseite .home-trust-row{display:flex;flex-wrap:wrap;align-items:center;gap:.65rem 1rem;margin-top:1.35rem;color:color-mix(in srgb,var(--tinte) 74%,var(--papier));font-size:.8rem;line-height:1.35}
.startseite .home-trust-row span{display:inline-flex;align-items:center;gap:.42rem;white-space:nowrap}
.startseite .home-trust-row span::before{content:'✓';display:grid;place-items:center;width:1.02rem;height:1.02rem;border:1px solid var(--stempel);border-radius:50%;color:var(--stempel);font-size:.58rem;font-weight:800;line-height:1}
.startseite .home-trust-row span+span::after{content:'';order:-1;width:1px;height:.82rem;margin-right:.1rem;background:color-mix(in srgb,var(--tinte) 16%,transparent)}
.startseite .home-portrait{position:relative!important;inset:auto!important;z-index:2!important;width:max-content!important;max-width:100%!important;display:grid!important;grid-template-columns:2.9rem minmax(0,1fr)!important;gap:.72rem!important;align-items:center!important;margin:1rem 0 0!important;padding:.9rem 0 0!important;border:0!important;border-top:1px solid color-mix(in srgb,var(--tinte) 12%,transparent)!important;border-radius:0!important;background:transparent!important;box-shadow:none!important;backdrop-filter:none!important;-webkit-backdrop-filter:none!important}
.startseite .home-portrait img{width:2.9rem!important;height:2.9rem!important;aspect-ratio:1;object-fit:cover;object-position:50% 18%;border-radius:50%!important}
.startseite .home-portrait figcaption{display:grid!important;gap:.08rem!important;padding:0!important;font-size:.66rem!important;line-height:1.28!important}
.startseite .home-portrait figcaption strong{font-size:.8rem!important;color:var(--tinte)}
.startseite .home-portrait figcaption span{color:color-mix(in srgb,var(--tinte) 66%,var(--papier))!important;font-size:.62rem!important}
.startseite .home-portrait figcaption a{justify-self:start;font-size:.61rem!important}

.startseite .home-flow-v8{grid-column:2;grid-row:1;position:relative;width:calc(100% + 4.5rem);min-width:0;min-height:25.5rem;display:grid;align-content:center;align-self:start;margin-top:.35rem;margin-left:-3.4rem;isolation:isolate;overflow:visible}
.startseite .home-flow-v8__grid{position:absolute;inset:2% -2% 4% -2%;z-index:-3;opacity:.22;pointer-events:none;background-image:linear-gradient(color-mix(in srgb,var(--tinte) 3%,transparent) 1px,transparent 1px),linear-gradient(90deg,color-mix(in srgb,var(--tinte) 3%,transparent) 1px,transparent 1px);background-size:56px 56px;mask-image:linear-gradient(90deg,transparent 0,#000 10%,#000 90%,transparent 100%);-webkit-mask-image:linear-gradient(90deg,transparent 0,#000 10%,#000 90%,transparent 100%)}
.startseite .home-flow-v8__track{position:relative;display:grid;grid-template-columns:minmax(6.5rem,.82fr) repeat(3,minmax(0,1fr));gap:clamp(.8rem,1.3vw,1.15rem);align-items:center;padding-block:5.1rem 5.6rem}
.startseite .home-flow-v8__rail{position:absolute;left:14%;right:5%;top:50%;height:2px;background:linear-gradient(90deg,color-mix(in srgb,var(--stempel) 28%,transparent),var(--stempel) 24%,var(--stempel) 86%,color-mix(in srgb,var(--stempel) 28%,transparent));transform:translateY(-50%);z-index:-1}
.startseite .home-flow-v8__signal{position:absolute;left:15%;top:50%;z-index:7;width:.58rem;height:.58rem;border-radius:50%;background:var(--stempel);box-shadow:0 0 0 .3rem color-mix(in srgb,var(--stempel) 10%,transparent),0 0 22px color-mix(in srgb,var(--stempel) 28%,transparent);opacity:0;transform:translate(-50%,-50%) scale(.75);pointer-events:none;will-change:left,opacity,transform}
.startseite .home-flow-v8__sources{position:relative;display:grid;gap:.48rem;align-content:center;z-index:2}
.startseite .home-flow-v8__source{display:flex;align-items:center;gap:.42rem;width:max-content;max-width:100%;padding:.36rem .58rem;border:1px solid color-mix(in srgb,var(--tinte) 14%,var(--haar));border-radius:999px;background:var(--papier);font-family:var(--mono);font-size:.54rem;color:color-mix(in srgb,var(--tinte) 68%,var(--papier));white-space:nowrap;box-shadow:0 5px 14px color-mix(in srgb,var(--tinte) 3.5%,transparent)}
.startseite .home-flow-v8__source svg{width:.9rem;height:.9rem;stroke:var(--stempel);fill:none;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round;flex:0 0 auto}
.startseite .home-flow-v8__source--ads::before{content:'G';display:grid;place-items:center;width:.9rem;height:.9rem;color:var(--stempel);font-family:var(--sans);font-size:.72rem;font-weight:750;line-height:1}
.startseite .home-flow-v8__stage{position:relative;display:grid;justify-items:center;align-content:center;gap:.65rem;min-width:0;text-align:center;z-index:3}
.startseite .home-flow-v8__disc{position:relative;display:grid;place-items:center;width:5.8rem;height:5.8rem;border:1px solid color-mix(in srgb,var(--tinte) 18%,var(--haar));border-radius:50%;background:var(--papier);box-shadow:0 14px 30px color-mix(in srgb,var(--tinte) 5%,transparent)}
.startseite .home-flow-v8__disc svg{width:1.95rem;height:1.95rem;stroke:currentColor;color:color-mix(in srgb,var(--tinte) 84%,var(--papier));fill:none;stroke-width:1.7;stroke-linecap:round;stroke-linejoin:round}
.startseite .home-flow-v8__stage strong{font-size:.88rem;line-height:1.1;color:var(--tinte);font-weight:720;letter-spacing:-.014em}
.startseite .home-flow-v8__stage--request .home-flow-v8__disc{width:7rem;height:7rem;border-color:color-mix(in srgb,var(--stempel) 62%,var(--haar));box-shadow:0 0 0 .95rem color-mix(in srgb,var(--stempel) 4%,transparent),0 0 0 1.8rem color-mix(in srgb,var(--stempel) 1.8%,transparent),0 18px 38px color-mix(in srgb,var(--stempel) 9%,transparent)}
.startseite .home-flow-v8__stage--request .home-flow-v8__disc::before{content:'';position:absolute;inset:-2.65rem;border:1px dashed color-mix(in srgb,var(--stempel) 22%,transparent);border-radius:50%;pointer-events:none}
.startseite .home-flow-v8__stage--request strong{color:var(--stempel);font-size:.94rem}
.startseite .home-flow-v8__stage--crm .home-flow-v8__disc{width:5.9rem;height:5.9rem}
.startseite .home-flow-v8__filter{position:absolute;left:76%;top:calc(50% + 3.75rem);z-index:5;display:flex;align-items:center;gap:.48rem;transform:translateX(-50%);white-space:nowrap}
.startseite .home-flow-v8__filter-icon{display:grid;place-items:center;width:2.15rem;height:2.15rem;border:1px solid color-mix(in srgb,var(--tinte) 15%,var(--haar));border-radius:50%;background:var(--papier);color:color-mix(in srgb,var(--tinte) 78%,var(--papier));box-shadow:0 6px 15px color-mix(in srgb,var(--tinte) 4%,transparent)}
.startseite .home-flow-v8__filter-icon svg{width:1rem;height:1rem;stroke:currentColor;fill:none;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round}
.startseite .home-flow-v8__filter-copy{display:grid;gap:.28rem;justify-items:start}
.startseite .home-flow-v8__filter-copy strong{font-size:.65rem;color:var(--tinte)}
.startseite .home-flow-v8__tags{display:flex;gap:.2rem}
.startseite .home-flow-v8__tags span{padding:.18rem .34rem;border:1px solid color-mix(in srgb,var(--tinte) 11%,var(--haar));border-radius:999px;background:var(--papier);font-family:var(--mono);font-size:.39rem;color:color-mix(in srgb,var(--tinte) 60%,var(--papier))}
.startseite .home-flow-v8__arrow{position:absolute;top:50%;z-index:4;color:var(--stempel);font-size:1.28rem;line-height:1;transform:translate(-50%,-55%);background:var(--papier);padding:0 .14rem}
.startseite .home-flow-v8__arrow--a{left:46.5%}.startseite .home-flow-v8__arrow--b{left:72.5%}

@media (max-width:1240px){.startseite .home-hero.home-hero-v8{grid-template-columns:minmax(0,1.08fr) minmax(30rem,.92fr);gap:1.4rem}.startseite .home-flow-v8{width:calc(100% + 2.5rem);margin-left:-2rem}.startseite .home-flow-v8__track{gap:.65rem}.startseite .home-flow-v8__disc{width:5.1rem;height:5.1rem}.startseite .home-flow-v8__stage--request .home-flow-v8__disc{width:6.2rem;height:6.2rem}.startseite .home-flow-v8__stage--crm .home-flow-v8__disc{width:5.2rem;height:5.2rem}}
@media (max-width:980px){.startseite .home-hero.home-hero-v8{grid-template-columns:1fr;min-height:auto;gap:1.65rem;padding-block:1.35rem 2rem}.startseite .home-hero-v8>.home-hero-copy{grid-column:1;grid-row:1}.startseite .home-flow-v8{grid-column:1;grid-row:2;width:min(100%,46rem);min-height:23rem;justify-self:center;margin:0}.startseite .home-flow-v8__track{padding-block:4.6rem 5.2rem}}
@media (max-width:640px){.startseite .home-title-primary{font-size:clamp(2.7rem,13vw,4rem)}.startseite .home-title-secondary{font-size:clamp(2rem,9.4vw,2.85rem)}.startseite .home-hero-v8 .aufriss{font-size:1rem}.startseite .home-trust-row{gap:.55rem .72rem;margin-top:1.1rem}.startseite .home-trust-row span+span::after{display:none}.startseite .home-portrait{grid-template-columns:2.55rem minmax(0,1fr)!important;padding-top:.75rem!important}.startseite .home-portrait img{width:2.55rem!important;height:2.55rem!important}.startseite .home-flow-v8{min-height:18.5rem;width:calc(100% + .4rem);margin-inline:-.2rem}.startseite .home-flow-v8__track{grid-template-columns:3.6rem repeat(3,minmax(0,1fr));gap:.12rem;padding-block:3.4rem 4.5rem}.startseite .home-flow-v8__sources{gap:.22rem}.startseite .home-flow-v8__source{padding:.19rem .27rem;font-size:.35rem}.startseite .home-flow-v8__source svg,.startseite .home-flow-v8__source--ads::before{display:none}.startseite .home-flow-v8__rail{left:12%;right:2%;height:1px}.startseite .home-flow-v8__signal{left:12%;width:.42rem;height:.42rem}.startseite .home-flow-v8__disc{width:3.1rem;height:3.1rem}.startseite .home-flow-v8__stage--request .home-flow-v8__disc{width:3.65rem;height:3.65rem;box-shadow:0 0 0 .5rem color-mix(in srgb,var(--stempel) 4%,transparent),0 0 0 1rem color-mix(in srgb,var(--stempel) 2%,transparent),0 9px 22px color-mix(in srgb,var(--stempel) 8%,transparent)}.startseite .home-flow-v8__stage--request .home-flow-v8__disc::before{inset:-1.25rem}.startseite .home-flow-v8__stage--crm .home-flow-v8__disc{width:3.15rem;height:3.15rem}.startseite .home-flow-v8__disc svg{width:1.18rem;height:1.18rem}.startseite .home-flow-v8__stage strong{font-size:.62rem}.startseite .home-flow-v8__filter{left:76%;top:calc(50% + 2.55rem);gap:.24rem}.startseite .home-flow-v8__filter-icon{width:1.75rem;height:1.75rem}.startseite .home-flow-v8__filter-copy strong{display:none}.startseite .home-flow-v8__tags{display:none}.startseite .home-flow-v8__arrow{font-size:.82rem}.startseite .home-flow-v8__arrow--a{left:47%}.startseite .home-flow-v8__arrow--b{left:73%}}
@media (prefers-reduced-motion:no-preference){
.startseite .home-flow-v8__signal{animation:home-v8-signal 6.4s cubic-bezier(.4,0,.2,1) .8s infinite both}
.startseite .home-flow-v8__stage--request .home-flow-v8__disc::before{animation:home-v8-ring 6.4s ease-in-out .8s infinite both}
.startseite .home-flow-v8__arrow--a{animation:home-v8-arrow-a 6.4s ease-in-out .8s infinite both}
.startseite .home-flow-v8__arrow--b{animation:home-v8-arrow-b 6.4s ease-in-out .8s infinite both}
.startseite .home-flow-v8__filter{animation:home-v8-filter 6.4s ease-in-out .8s infinite both}
}
@media (prefers-reduced-motion:reduce){.startseite .home-hero-v8 .tun{transition-duration:0s}.startseite .home-flow-v8__signal{display:none}}
@keyframes home-v8-signal{0%,8%{left:15%;opacity:0;transform:translate(-50%,-50%) scale(.72)}12%{opacity:1}28%{left:40%;opacity:1;transform:translate(-50%,-50%) scale(1)}47%{left:61%;opacity:1;transform:translate(-50%,-50%) scale(1)}53%{left:61%;opacity:1;transform:translate(-50%,-50%) scale(1.42)}59%{left:61%;opacity:1;transform:translate(-50%,-50%) scale(1)}73%{left:76%;opacity:1}87%{left:91%;opacity:1;transform:translate(-50%,-50%) scale(1)}94%,100%{left:91%;opacity:0;transform:translate(-50%,-50%) scale(.78)}}
@keyframes home-v8-ring{0%,35%,64%,100%{opacity:.32;transform:scale(.97)}45%,57%{opacity:1;transform:scale(1.06)}}
@keyframes home-v8-arrow-a{0%,10%,34%,100%{opacity:.32;transform:translate(-58%,-55%)}18%,28%{opacity:1;transform:translate(-50%,-55%)}}
@keyframes home-v8-arrow-b{0%,50%,88%,100%{opacity:.32;transform:translate(-58%,-55%)}62%,80%{opacity:1;transform:translate(-50%,-55%)}}
@keyframes home-v8-filter{0%,54%,84%,100%{opacity:.58;transform:translateX(-50%) scale(.97)}64%,77%{opacity:1;transform:translateX(-50%) scale(1)}}
`;
        document.head.appendChild(style);
    }

    function source(label, kind, className) {
        var el = document.createElement('span');
        el.className = 'home-flow-v8__source ' + (className || '');
        if (kind) {
            el.innerHTML = icon(kind) + '<span>' + label + '</span>';
        } else {
            el.textContent = label;
        }
        return el;
    }

    function stage(kind, label, className) {
        var el = document.createElement('div');
        el.className = 'home-flow-v8__stage ' + (className || '');
        el.innerHTML = '<span class="home-flow-v8__disc">' + icon(kind) + '</span><strong>' + label + '</strong>';
        return el;
    }

    function buildFlow() {
        var visual = document.createElement('figure');
        visual.className = 'home-flow-v8';
        visual.setAttribute('role', 'img');
        visual.setAttribute('aria-label', 'Ads, SEO und Empfehlungen führen über die Website zur Anfrage. Die Anfrage wird nach Region, Leistung und Qualität segmentiert und anschließend ins CRM übergeben.');

        var grid = document.createElement('div');
        grid.className = 'home-flow-v8__grid';
        grid.setAttribute('aria-hidden', 'true');

        var track = document.createElement('div');
        track.className = 'home-flow-v8__track';
        track.setAttribute('aria-hidden', 'true');

        var rail = document.createElement('span');
        rail.className = 'home-flow-v8__rail';

        var signal = document.createElement('span');
        signal.className = 'home-flow-v8__signal';
        signal.setAttribute('aria-hidden', 'true');

        var sources = document.createElement('div');
        sources.className = 'home-flow-v8__sources';
        sources.appendChild(source('Ads', null, 'home-flow-v8__source--ads'));
        sources.appendChild(source('SEO', 'search'));
        sources.appendChild(source('Empfehlung', 'people'));

        var website = stage('website', 'Website', 'home-flow-v8__stage--website');
        var request = stage('request', 'Anfrage', 'home-flow-v8__stage--request');
        var crm = stage('crm', 'CRM', 'home-flow-v8__stage--crm');

        var filter = document.createElement('div');
        filter.className = 'home-flow-v8__filter';
        filter.innerHTML = '<span class="home-flow-v8__filter-icon">' + icon('filter') + '</span><span class="home-flow-v8__filter-copy"><strong>Segmentierung</strong><span class="home-flow-v8__tags"><span>Region</span><span>Leistung</span><span>Qualität</span></span></span>';

        track.appendChild(rail);
        track.appendChild(signal);
        track.appendChild(sources);
        track.appendChild(website);
        track.appendChild(request);
        track.appendChild(crm);
        track.appendChild(filter);
        track.insertAdjacentHTML('beforeend', '<span class="home-flow-v8__arrow home-flow-v8__arrow--a" aria-hidden="true">→</span><span class="home-flow-v8__arrow home-flow-v8__arrow--b" aria-hidden="true">→</span>');

        visual.appendChild(grid);
        visual.appendChild(track);
        return visual;
    }

    function initHero() {
        var hero = document.querySelector('.startseite .home-hero');
        if (!hero || hero.dataset.heroV8 === 'true') {
            return;
        }

        var copy = hero.firstElementChild;
        if (!copy) {
            return;
        }

        hero.dataset.heroV8 = 'true';
        hero.classList.remove('home-hero-v6', 'home-hero-v7');
        hero.classList.add('home-hero-v8');
        copy.classList.add('home-hero-copy');
        mountStyles();

        hero.querySelectorAll('.home-system-visual,.home-leadflow,.home-flow-v7,.home-flow-v8').forEach(function (oldVisual) {
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