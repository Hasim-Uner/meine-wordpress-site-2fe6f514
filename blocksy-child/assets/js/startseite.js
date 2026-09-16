/**
 * Startseite.
 *
 * Hero V4: klare Positionierung, kompakte Anfragestrecke und ruhige Motion.
 * Die Visualisierung wird nativ im DOM aufgebaut; kein externes Bild-Asset.
 */
(function () {
    'use strict';

    function mountHeroStyles() {
        if (document.getElementById('home-hero-v4-styles')) {
            return;
        }

        var style = document.createElement('style');
        style.id = 'home-hero-v4-styles';
        style.textContent = `
.startseite .home-hero::before{content:none!important;display:none!important}
.startseite .home-hero{position:relative;display:grid;grid-template-columns:minmax(0,1.3fr) minmax(24rem,.7fr);gap:clamp(3rem,5vw,6rem);align-items:center;isolation:isolate;padding-block:clamp(1.1rem,2.4vw,2.8rem)}
.startseite .home-hero>.home-hero-copy{grid-column:1;grid-row:1;min-width:0;position:relative;z-index:3}
.startseite .home-hero .gegenstand{margin:0 0 clamp(1.45rem,2vw,2rem);color:var(--stempel);font-family:var(--mono);font-size:clamp(.68rem,.62rem + .15vw,.78rem);font-weight:600;letter-spacing:.18em;line-height:1.5;text-transform:uppercase}
.startseite .home-hero h1{margin:0!important;max-width:none!important;font-size:inherit!important;line-height:1!important;letter-spacing:0!important}
.startseite .home-title-primary,.startseite .home-title-secondary{display:block}
.startseite .home-title-primary{max-width:9.3ch;font-family:var(--sans);font-size:clamp(3.65rem,5.15vw,6.1rem);font-weight:650;line-height:.86;letter-spacing:-.058em;color:var(--tinte)}
.startseite .home-title-secondary{max-width:11.8ch;margin-top:.24em;font-family:var(--sans);font-size:clamp(2.75rem,3.75vw,4.45rem);font-weight:470;line-height:.96;letter-spacing:-.045em;color:color-mix(in srgb,var(--tinte) 66%,var(--papier))}
.startseite .home-title-stop{color:var(--stempel)}
.startseite .home-hero .aufriss{max-width:40ch;margin-top:clamp(1.6rem,2vw,2.2rem);font-size:clamp(1rem,.94rem + .2vw,1.14rem);line-height:1.58;color:color-mix(in srgb,var(--tinte) 86%,var(--papier))}
.startseite .home-hero .ausgang{display:flex;flex-wrap:wrap;gap:.8rem;margin-top:clamp(1.65rem,2vw,2.25rem)}
.startseite .home-hero .tun{min-height:3.25rem;padding-inline:1.35rem;border-radius:2px;transition:transform .2s ease,box-shadow .2s ease,border-color .2s ease}
.startseite .home-hero .tun:not(.still){box-shadow:0 10px 24px color-mix(in srgb,var(--stempel) 17%,transparent)}
.startseite .home-hero .tun:hover{transform:translateY(-2px)}
.startseite .home-hero .tun:not(.still):hover{box-shadow:0 14px 30px color-mix(in srgb,var(--stempel) 23%,transparent)}
.startseite .home-hero .tun.still{background:transparent;border-color:color-mix(in srgb,var(--tinte) 45%,transparent)}
.startseite .home-trust-row{display:flex;flex-wrap:wrap;align-items:center;gap:.65rem 1rem;margin-top:1.05rem;color:var(--grau);font-size:.78rem;line-height:1.35}
.startseite .home-trust-row span{display:inline-flex;align-items:center;gap:.42rem;white-space:nowrap}
.startseite .home-trust-row span::before{content:'✓';display:grid;place-items:center;width:1rem;height:1rem;border:1px solid var(--stempel);border-radius:50%;color:var(--stempel);font-size:.58rem;font-weight:800;line-height:1}
.startseite .home-trust-row span+span::after{content:'';order:-1;width:1px;height:.82rem;margin-right:.18rem;background:var(--haar)}
.startseite .home-system-visual{grid-column:2;grid-row:1;position:relative;z-index:2;width:100%;min-height:31rem;align-self:center;justify-self:stretch;overflow:visible}
.startseite .home-flow-frame{position:absolute;inset:7% -7% 12% -8%;pointer-events:none;opacity:.78;background-image:linear-gradient(color-mix(in srgb,var(--tinte) 4.5%,transparent) 1px,transparent 1px),linear-gradient(90deg,color-mix(in srgb,var(--tinte) 4.5%,transparent) 1px,transparent 1px);background-size:52px 52px;mask-image:radial-gradient(ellipse at 54% 45%,#000 18%,transparent 77%);-webkit-mask-image:radial-gradient(ellipse at 54% 45%,#000 18%,transparent 77%)}
.startseite .home-flow-orbit{position:absolute;left:47%;top:36%;width:54%;aspect-ratio:1;border:1px solid color-mix(in srgb,var(--stempel) 10%,transparent);border-radius:50%;transform:translate(-50%,-50%);pointer-events:none}
.startseite .home-flow-orbit::before,.startseite .home-flow-orbit::after{content:'';position:absolute;border-radius:50%;border:1px dashed color-mix(in srgb,var(--stempel) 21%,transparent)}
.startseite .home-flow-orbit::before{inset:17%}.startseite .home-flow-orbit::after{inset:33%;border-style:solid;opacity:.7}
.startseite .home-flow-map{position:absolute;left:0;right:0;top:27%;display:grid;grid-template-columns:minmax(4.5rem,.76fr) repeat(4,minmax(0,1fr));gap:.48rem;align-items:start;z-index:3}
.startseite .home-flow-map::before{content:'';position:absolute;left:14%;right:6%;top:2.35rem;height:1px;background:linear-gradient(90deg,color-mix(in srgb,var(--stempel) 35%,transparent),var(--stempel) 17%,var(--stempel) 82%,color-mix(in srgb,var(--stempel) 28%,transparent));z-index:0}
.startseite .home-flow-map::after{content:'';position:absolute;left:14%;top:2.13rem;width:.46rem;height:.46rem;border-radius:50%;background:var(--stempel);box-shadow:0 0 0 .42rem color-mix(in srgb,var(--stempel) 7%,transparent);z-index:5}
.startseite .home-flow-sources{position:relative;z-index:2;display:grid;gap:.42rem;align-self:center;padding-top:.25rem}
.startseite .home-flow-sources span{display:flex;align-items:center;gap:.38rem;width:max-content;max-width:100%;padding:.3rem .5rem;border:1px solid color-mix(in srgb,var(--tinte) 10%,var(--haar));border-radius:999px;background:color-mix(in srgb,var(--papier) 95%,transparent);box-shadow:0 4px 12px color-mix(in srgb,var(--tinte) 4%,transparent);font-family:var(--mono);font-size:.52rem;letter-spacing:.02em;color:var(--grau)}
.startseite .home-flow-sources span::before{content:'';width:.28rem;height:.28rem;flex:0 0 auto;border-radius:50%;background:var(--stempel)}
.startseite .home-flow-stage{position:relative;z-index:2;display:grid;justify-items:center;align-content:start;gap:.55rem;min-width:0;text-align:center}
.startseite .home-flow-stage__icon{position:relative;display:grid;place-items:center;width:4.6rem;height:4.6rem;border:1px solid color-mix(in srgb,var(--tinte) 8%,var(--haar));border-radius:50%;background:color-mix(in srgb,var(--papier) 96%,transparent);box-shadow:0 12px 30px color-mix(in srgb,var(--tinte) 8%,transparent),inset 0 0 0 .5rem color-mix(in srgb,var(--tinte) 2.6%,transparent)}
.startseite .home-flow-stage__icon svg{width:1.55rem;height:1.55rem;stroke:currentColor;color:color-mix(in srgb,var(--tinte) 78%,var(--papier));fill:none;stroke-width:1.6;stroke-linecap:round;stroke-linejoin:round}
.startseite .home-flow-stage strong{font-size:.75rem;line-height:1.2;color:var(--tinte);font-weight:700;letter-spacing:-.01em}
.startseite .home-flow-stage--request .home-flow-stage__icon{border-color:color-mix(in srgb,var(--stempel) 30%,var(--haar));box-shadow:0 0 0 .72rem color-mix(in srgb,var(--stempel) 5%,transparent),0 0 0 1.45rem color-mix(in srgb,var(--stempel) 2.5%,transparent),0 14px 34px color-mix(in srgb,var(--stempel) 10%,transparent)}
.startseite .home-flow-stage--request .home-flow-stage__icon::after{content:'';position:absolute;width:.72rem;height:.72rem;border-radius:50%;background:var(--stempel);opacity:.12;filter:blur(1px)}
.startseite .home-flow-stage--segment .home-flow-stage__icon{border-color:color-mix(in srgb,var(--stempel) 18%,var(--haar))}
.startseite .home-segment-tags{display:flex;justify-content:center;gap:.22rem;flex-wrap:wrap;margin-top:-.12rem}
.startseite .home-segment-tags span{padding:.2rem .38rem;border:1px solid var(--haar);border-radius:999px;background:color-mix(in srgb,var(--papier) 96%,transparent);font-family:var(--mono);font-size:.43rem;color:var(--grau);white-space:nowrap}
.startseite .home-flow-caption{position:absolute;left:17%;right:4%;top:59%;display:flex;align-items:center;gap:.55rem;color:color-mix(in srgb,var(--grau) 78%,transparent);font-family:var(--mono);font-size:.48rem;letter-spacing:.11em;text-transform:uppercase;white-space:nowrap}
.startseite .home-flow-caption::before,.startseite .home-flow-caption::after{content:'';height:1px;flex:1;background:var(--haar)}
.startseite .home-flow-note{position:absolute;right:2%;top:8%;font-family:var(--mono);font-size:.5rem;font-weight:600;letter-spacing:.14em;line-height:1.55;text-transform:uppercase;color:color-mix(in srgb,var(--grau) 78%,transparent);text-align:right}
.startseite .home-flow-note::after{content:'';display:block;width:2.1rem;height:1px;margin:.55rem 0 0 auto;background:var(--stempel);opacity:.65}
.startseite .home-portrait{position:absolute!important;right:0;bottom:0;z-index:5;width:min(17rem,72%)!important;display:grid!important;grid-template-columns:3.45rem minmax(0,1fr)!important;gap:.72rem!important;align-items:center!important;margin:0!important;padding:.62rem!important;border:1px solid color-mix(in srgb,var(--tinte) 10%,var(--haar))!important;border-radius:10px!important;background:color-mix(in srgb,var(--papier) 95%,transparent)!important;box-shadow:0 14px 36px color-mix(in srgb,var(--tinte) 9%,transparent)!important;backdrop-filter:blur(10px);-webkit-backdrop-filter:blur(10px)}
.startseite .home-portrait img{width:3.45rem!important;height:3.45rem!important;aspect-ratio:1;object-fit:cover;object-position:50% 18%;border-radius:7px!important}
.startseite .home-portrait figcaption{display:grid!important;gap:.12rem!important;padding:0!important;font-size:.68rem!important;line-height:1.32!important}
.startseite .home-portrait figcaption strong{font-size:.72rem!important;color:var(--tinte)}
.startseite .home-portrait figcaption span{color:var(--grau)!important;font-size:.63rem!important}
.startseite .home-portrait figcaption a{justify-self:start;font-size:.62rem!important}
@media (max-width:1180px){.startseite .home-hero{grid-template-columns:minmax(0,1.18fr) minmax(22rem,.82fr);gap:clamp(2rem,3.5vw,3.5rem)}.startseite .home-title-primary{font-size:clamp(3.35rem,5vw,5.2rem)}.startseite .home-title-secondary{font-size:clamp(2.55rem,3.7vw,3.85rem)}.startseite .home-flow-stage__icon{width:4.2rem;height:4.2rem}.startseite .home-flow-map::before{top:2.15rem}.startseite .home-flow-map::after{top:1.93rem}}
@media (max-width:900px){.startseite .home-hero{grid-template-columns:1fr;gap:2rem;padding-block:1rem}.startseite .home-hero>.home-hero-copy{grid-column:1;grid-row:1}.startseite .home-title-primary{max-width:8.7ch;font-size:clamp(3.25rem,9vw,5rem)}.startseite .home-title-secondary{max-width:12ch;font-size:clamp(2.4rem,7vw,3.6rem)}.startseite .home-system-visual{grid-column:1;grid-row:2;width:min(100%,42rem);min-height:25rem;justify-self:center}.startseite .home-flow-map{top:22%}.startseite .home-flow-caption{top:57%}.startseite .home-portrait{width:min(17rem,54%)!important}}
@media (max-width:620px){.startseite .home-title-primary{font-size:clamp(2.85rem,14vw,4.25rem)}.startseite .home-title-secondary{font-size:clamp(2.15rem,10vw,3rem)}.startseite .home-hero .aufriss{font-size:1rem}.startseite .home-system-visual{min-height:24rem;width:calc(100% + 1rem);margin-inline:-.5rem}.startseite .home-flow-map{grid-template-columns:3.7rem repeat(4,minmax(0,1fr));gap:.15rem;top:20%}.startseite .home-flow-map::before{left:12%;right:4%;top:1.85rem}.startseite .home-flow-map::after{left:12%;top:1.64rem}.startseite .home-flow-sources{gap:.28rem}.startseite .home-flow-sources span{padding:.22rem .34rem;font-size:.42rem}.startseite .home-flow-stage__icon{width:3.55rem;height:3.55rem}.startseite .home-flow-stage__icon svg{width:1.25rem;height:1.25rem}.startseite .home-flow-stage strong{font-size:.61rem}.startseite .home-segment-tags{display:none}.startseite .home-flow-caption{left:10%;right:2%;top:52%;font-size:.38rem;letter-spacing:.07em}.startseite .home-flow-note{display:none}.startseite .home-portrait{left:0;right:auto;bottom:.25rem;width:min(17rem,92%)!important}.startseite .home-trust-row{gap:.55rem .75rem}.startseite .home-trust-row span+span::after{display:none}}
@media (prefers-reduced-motion:no-preference){.startseite .home-flow-map::after{animation:home-flow-beacon 3.8s ease-in-out infinite}.startseite .home-flow-stage--request .home-flow-stage__icon{animation:home-request-breathe 4.8s ease-in-out infinite}.startseite .home-flow-orbit::before{animation:home-orbit-turn 24s linear infinite}}
@keyframes home-flow-beacon{0%,100%{transform:translateX(0);opacity:.6}50%{transform:translateX(12rem);opacity:1}}
@keyframes home-request-breathe{0%,100%{transform:translateY(0)}50%{transform:translateY(-3px)}}
@keyframes home-orbit-turn{to{transform:rotate(360deg)}}
`;
        document.head.appendChild(style);
    }

    function stageIcon(name) {
        var icons = {
            website: '<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="2"></rect><path d="M3 8h18"></path><path d="M7 6h.01M10 6h.01"></path></svg>',
            request: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 3h8l4 4v14H6z"></path><path d="M14 3v5h5"></path><path d="M9 12h6M9 16h6"></path></svg>',
            segment: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5h16l-6 7v5l-4 2v-7z"></path></svg>',
            crm: '<svg viewBox="0 0 24 24" aria-hidden="true"><ellipse cx="12" cy="5" rx="7" ry="3"></ellipse><path d="M5 5v6c0 1.7 3.1 3 7 3s7-1.3 7-3V5"></path><path d="M5 11v6c0 1.7 3.1 3 7 3s7-1.3 7-3v-6"></path></svg>'
        };
        return icons[name] || '';
    }

    function mountHero(root) {
        var hero = root.querySelector('.home-hero');
        if (!hero) {
            return;
        }

        mountHeroStyles();

        var copy = hero.firstElementChild;
        if (!copy) {
            return;
        }
        copy.classList.add('home-hero-copy');

        var eyebrow = copy.querySelector('.gegenstand');
        if (eyebrow) {
            eyebrow.textContent = 'WordPress · Tracking · CRM';
        }

        var title = copy.querySelector('h1');
        if (title) {
            title.innerHTML = '<span class="home-title-primary">WordPress Freelancer <span class="home-title-stop">Hannover.</span></span><span class="home-title-secondary">Von der Website bis zur Anfrage.</span>';
        }

        var intro = copy.querySelector('.aufriss');
        if (intro) {
            intro.textContent = 'Ich entwickle WordPress-Websites, die Angebote verständlich machen und Anfragen sauber bis ins CRM führen. Direkt mit mir – ohne Übergabe an ein fremdes Entwicklerteam.';
        }

        var oldTrust = copy.querySelector('.home-trust-row');
        if (oldTrust) {
            oldTrust.remove();
        }

        var reply = copy.querySelector('.home-reply');
        var responseText = 'Antwort in 2 Werktagen';
        if (reply && reply.textContent) {
            responseText = reply.textContent.split('·')[0].trim().replace('spätestens ', '');
        }
        if (reply) {
            reply.remove();
        }

        var trust = document.createElement('div');
        trust.className = 'home-trust-row';
        trust.setAttribute('aria-label', 'Projektvorteile');
        [responseText, 'Klare Projektpreise', 'Direkt mit dem Entwickler'].forEach(function (text) {
            var item = document.createElement('span');
            item.textContent = text;
            trust.appendChild(item);
        });
        copy.appendChild(trust);

        var existingVisual = hero.querySelector('.home-system-visual');
        if (existingVisual) {
            existingVisual.remove();
        }

        var portrait = hero.querySelector('.home-portrait');
        var visual = document.createElement('div');
        visual.className = 'home-system-visual';
        visual.setAttribute('aria-label', 'Anfragestrecke von Quelle über Website und Segmentierung bis ins CRM');
        visual.innerHTML = '' +
            '<div class="home-flow-frame"></div>' +
            '<div class="home-flow-orbit"></div>' +
            '<div class="home-flow-note">messbar<br>bis ins CRM</div>' +
            '<div class="home-flow-map">' +
                '<div class="home-flow-sources" aria-label="Quellen"><span>Ads</span><span>SEO</span><span>Empfehlung</span></div>' +
                '<div class="home-flow-stage home-flow-stage--website"><div class="home-flow-stage__icon">' + stageIcon('website') + '</div><strong>Website</strong></div>' +
                '<div class="home-flow-stage home-flow-stage--request"><div class="home-flow-stage__icon">' + stageIcon('request') + '</div><strong>Anfrage</strong></div>' +
                '<div class="home-flow-stage home-flow-stage--segment"><div class="home-flow-stage__icon">' + stageIcon('segment') + '</div><strong>Segmentierung</strong><div class="home-segment-tags"><span>Region</span><span>Leistung</span><span>Qualität</span></div></div>' +
                '<div class="home-flow-stage home-flow-stage--crm"><div class="home-flow-stage__icon">' + stageIcon('crm') + '</div><strong>CRM</strong></div>' +
            '</div>' +
            '<div class="home-flow-caption">Quelle → Website → Anfrage → Segmentierung → CRM</div>';

        hero.insertBefore(visual, portrait || null);

        if (portrait) {
            var strong = portrait.querySelector('strong');
            var detail = portrait.querySelector('figcaption span');
            if (strong) {
                strong.textContent = 'Direkt mit Haşim.';
            }
            if (detail) {
                detail.textContent = 'Konzeption, Entwicklung, Übergabe.';
            }
            visual.appendChild(portrait);
        }
    }

    function mountSectionObserver(root) {
        if (!('IntersectionObserver' in window)) {
            return;
        }

        var sections = Array.prototype.slice.call(root.querySelectorAll('section[id]'));
        if (!sections.length) {
            return;
        }

        var observer = new window.IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                entry.target.classList.toggle('aktiv', entry.isIntersecting);
            });
        }, { rootMargin: '-45% 0px -45% 0px' });

        sections.forEach(function (section) {
            observer.observe(section);
        });
    }

    function init() {
        var root = document.querySelector('.startseite');
        if (!root) {
            return;
        }
        mountHero(root);
        mountSectionObserver(root);
    }

    if ('loading' === document.readyState) {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
