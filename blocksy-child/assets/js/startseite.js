/**
 * Startseite – Hero V9.1 + Arbeiten V3.
 *
 * Hero bleibt als eingefrorene B2B-Strecke bestehen.
 * Segmentierung sitzt als eigener Gate-Punkt direkt in der Strecke zwischen
 * Anfrage und CRM und kann die Anfrage-Beschriftung nicht mehr überlagern.
 * Arbeiten V3 übersetzt den E3-Fall in eine präzise technische Zeichnung.
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
        ['home-hero-v6-styles', 'home-hero-v7-styles', 'home-hero-v8-styles', 'home-hero-v9-styles', 'home-works-v3-styles'].forEach(function (id) {
            document.getElementById(id)?.remove();
        });

        var style = document.createElement('style');
        style.id = 'home-hero-v9-styles';
        style.textContent = `
/* Hero V9.1 – Segmentierung als integriertes Gate */
.startseite .home-hero.home-hero-v9::before{content:''!important;display:none!important;background:none!important;background-image:none!important;animation:none!important}
.startseite .home-hero.home-hero-v9{position:relative;display:grid;grid-template-columns:minmax(0,1.04fr) minmax(35rem,1fr);gap:clamp(1.2rem,1.8vw,2rem);align-items:start;min-height:min(42rem,calc(100svh - 5.5rem));padding-block:clamp(1.2rem,1.8vw,1.8rem);isolation:isolate}
.startseite .home-hero-v9>.home-hero-copy{grid-column:1;grid-row:1;min-width:0;position:relative;z-index:3}
.startseite .home-hero-v9 .gegenstand{margin:0 0 clamp(1.05rem,1.35vw,1.35rem);color:var(--stempel);font-family:var(--mono);font-size:clamp(.62rem,.59rem + .1vw,.7rem);font-weight:650;letter-spacing:.17em;line-height:1.4;text-transform:uppercase}
.startseite .home-hero-v9 h1{margin:0!important;max-width:none!important;font-size:inherit!important;line-height:1!important;letter-spacing:0!important}
.startseite .home-title-primary,.startseite .home-title-secondary{display:block}
.startseite .home-title-primary{max-width:9.25ch;font-family:var(--sans);font-size:clamp(3.55rem,4.55vw,5.2rem);font-weight:650;line-height:.92;letter-spacing:-.056em;color:var(--tinte)}
.startseite .home-title-secondary{max-width:11.9ch;margin-top:.25em;font-family:var(--sans);font-size:clamp(2.5rem,3.25vw,3.7rem);font-weight:470;line-height:.99;letter-spacing:-.044em;color:color-mix(in srgb,var(--tinte) 72%,var(--papier))}
.startseite .home-title-stop{color:var(--stempel)}
.startseite .home-hero-v9 .aufriss{max-width:40ch;margin-top:clamp(1.7rem,2.1vw,2rem);font-size:clamp(1rem,.95rem + .16vw,1.1rem);line-height:1.58;color:color-mix(in srgb,var(--tinte) 94%,var(--papier))}
.startseite .home-hero-v9 .ausgang{display:flex;flex-wrap:wrap;gap:.82rem;margin-top:clamp(1.55rem,1.9vw,1.85rem)}
.startseite .home-hero-v9 .tun{min-height:3.18rem;padding-inline:1.35rem;border-radius:2px;transition:transform var(--t-norm) var(--ease-aus),box-shadow var(--t-norm) var(--ease-aus),border-color var(--t-norm) var(--ease-aus),color var(--t-norm) var(--ease-aus)}
.startseite .home-hero-v9 .tun:not(.still){box-shadow:0 9px 22px color-mix(in srgb,var(--stempel) 14%,transparent)}
.startseite .home-hero-v9 .tun.still{background:transparent;border-color:color-mix(in srgb,var(--tinte) 60%,transparent);color:var(--tinte)}
@media (hover:hover) and (pointer:fine){.startseite .home-hero-v9 .tun:hover{transform:translateY(-2px)}.startseite .home-hero-v9 .tun:not(.still):hover{box-shadow:0 13px 28px color-mix(in srgb,var(--stempel) 20%,transparent)}.startseite .home-hero-v9 .tun.still:hover{border-color:var(--tinte)}}
.startseite .home-trust-row{display:flex;flex-wrap:wrap;align-items:center;gap:.65rem 1rem;margin-top:1.2rem;color:color-mix(in srgb,var(--tinte) 76%,var(--papier));font-size:.8rem;line-height:1.35}
.startseite .home-trust-row span{display:inline-flex;align-items:center;gap:.42rem;white-space:nowrap}
.startseite .home-trust-row span::before{content:'✓';display:grid;place-items:center;width:1.02rem;height:1.02rem;border:1px solid var(--stempel);border-radius:50%;color:var(--stempel);font-size:.58rem;font-weight:800;line-height:1}
.startseite .home-trust-row span+span::after{content:'';order:-1;width:1px;height:.82rem;margin-right:.1rem;background:color-mix(in srgb,var(--tinte) 16%,transparent)}
.startseite .home-portrait{position:relative!important;inset:auto!important;z-index:2!important;width:max-content!important;max-width:100%!important;display:grid!important;grid-template-columns:3rem minmax(0,1fr)!important;gap:.72rem!important;align-items:center!important;margin:.85rem 0 0!important;padding:.8rem 0 0!important;border:0!important;border-top:1px solid color-mix(in srgb,var(--tinte) 12%,transparent)!important;border-radius:0!important;background:transparent!important;box-shadow:none!important;backdrop-filter:none!important;-webkit-backdrop-filter:none!important}
.startseite .home-portrait img{width:3rem!important;height:3rem!important;aspect-ratio:1;object-fit:cover;object-position:50% 18%;border-radius:50%!important}
.startseite .home-portrait figcaption{display:grid!important;gap:.08rem!important;padding:0!important;font-size:.66rem!important;line-height:1.28!important}
.startseite .home-portrait figcaption strong{font-size:.8rem!important;color:var(--tinte)}
.startseite .home-portrait figcaption span{color:color-mix(in srgb,var(--tinte) 68%,var(--papier))!important;font-size:.62rem!important}
.startseite .home-portrait figcaption a{justify-self:start;font-size:.61rem!important}
.startseite .home-flow-v9{grid-column:2;grid-row:1;position:relative;width:calc(100% + 4.2rem);min-width:0;min-height:25rem;display:grid;align-content:center;align-self:start;margin-top:.15rem;margin-left:-3.25rem;isolation:isolate;overflow:visible}
.startseite .home-flow-v9__grid{position:absolute;inset:2% -2% 4% -2%;z-index:-3;opacity:.17;pointer-events:none;background-image:linear-gradient(color-mix(in srgb,var(--tinte) 3%,transparent) 1px,transparent 1px),linear-gradient(90deg,color-mix(in srgb,var(--tinte) 3%,transparent) 1px,transparent 1px);background-size:56px 56px;mask-image:linear-gradient(90deg,transparent 0,#000 10%,#000 90%,transparent 100%);-webkit-mask-image:linear-gradient(90deg,transparent 0,#000 10%,#000 90%,transparent 100%)}
.startseite .home-flow-v9__track{position:relative;display:grid;grid-template-columns:minmax(6.35rem,.8fr) repeat(3,minmax(0,1fr));gap:clamp(.9rem,1.45vw,1.3rem);align-items:center;padding-block:5rem 5.5rem}
.startseite .home-flow-v9__rail{position:absolute;left:14%;right:4.4%;top:50%;height:2px;background:linear-gradient(90deg,color-mix(in srgb,var(--stempel) 30%,transparent),var(--stempel) 26%,var(--stempel) 88%,color-mix(in srgb,var(--stempel) 34%,transparent));transform:translateY(-50%);z-index:-1}
.startseite .home-flow-v9__signal{position:absolute;left:15%;top:50%;z-index:7;width:.58rem;height:.58rem;border-radius:50%;background:var(--stempel);box-shadow:0 0 0 .3rem color-mix(in srgb,var(--stempel) 10%,transparent),0 0 22px color-mix(in srgb,var(--stempel) 28%,transparent);opacity:0;transform:translate(-50%,-50%) scale(.75);pointer-events:none;will-change:left,opacity,transform}
.startseite .home-flow-v9__sources{position:relative;display:grid;gap:.48rem;align-content:center;z-index:2}
.startseite .home-flow-v9__source{display:flex;align-items:center;gap:.42rem;width:max-content;max-width:100%;padding:.36rem .58rem;border:1px solid color-mix(in srgb,var(--tinte) 15%,var(--haar));border-radius:999px;background:var(--papier);font-family:var(--mono);font-size:.54rem;color:color-mix(in srgb,var(--tinte) 70%,var(--papier));white-space:nowrap;box-shadow:0 5px 14px color-mix(in srgb,var(--tinte) 3.5%,transparent)}
.startseite .home-flow-v9__source svg{width:.9rem;height:.9rem;stroke:var(--stempel);fill:none;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round;flex:0 0 auto}
.startseite .home-flow-v9__source--ads::before{content:'G';display:grid;place-items:center;width:.9rem;height:.9rem;color:var(--stempel);font-family:var(--sans);font-size:.72rem;font-weight:750;line-height:1}
.startseite .home-flow-v9__stage{position:relative;display:grid;justify-items:center;align-content:center;gap:.65rem;min-width:0;text-align:center;z-index:3}
.startseite .home-flow-v9__disc{position:relative;display:grid;place-items:center;width:5.8rem;height:5.8rem;border:1px solid color-mix(in srgb,var(--tinte) 19%,var(--haar));border-radius:50%;background:var(--papier);box-shadow:0 14px 30px color-mix(in srgb,var(--tinte) 5%,transparent)}
.startseite .home-flow-v9__disc svg{width:1.95rem;height:1.95rem;stroke:currentColor;color:color-mix(in srgb,var(--tinte) 86%,var(--papier));fill:none;stroke-width:1.7;stroke-linecap:round;stroke-linejoin:round}
.startseite .home-flow-v9__stage strong{font-size:.88rem;line-height:1.1;color:var(--tinte);font-weight:720;letter-spacing:-.014em}
.startseite .home-flow-v9__stage--request .home-flow-v9__disc{width:6.95rem;height:6.95rem;border-color:color-mix(in srgb,var(--stempel) 64%,var(--haar));box-shadow:0 0 0 .78rem color-mix(in srgb,var(--stempel) 3.6%,transparent),0 0 0 1.5rem color-mix(in srgb,var(--stempel) 1.45%,transparent),0 18px 38px color-mix(in srgb,var(--stempel) 8%,transparent)}
.startseite .home-flow-v9__stage--request .home-flow-v9__disc::before{content:'';position:absolute;inset:-2.35rem;border:1px dashed color-mix(in srgb,var(--stempel) 20%,transparent);border-radius:50%;pointer-events:none}
.startseite .home-flow-v9__stage--request strong{position:relative;z-index:8;color:var(--stempel);font-size:.94rem}
.startseite .home-flow-v9__stage--crm{transform:translateX(.25rem)}
.startseite .home-flow-v9__stage--crm .home-flow-v9__disc{width:5.9rem;height:5.9rem}
/* Segmentierung: keine zweite Station unter Anfrage, sondern ein Gate in der Rail. */
.startseite .home-flow-v9__filter{position:absolute;left:78.6%;top:50%;z-index:9;display:grid;place-items:center;width:2.5rem;height:2.5rem;transform:translate(-50%,-50%);white-space:nowrap;pointer-events:none}
.startseite .home-flow-v9__filter::before,.startseite .home-flow-v9__filter::after{display:none!important}
.startseite .home-flow-v9__filter-icon{display:grid;place-items:center;width:2.5rem;height:2.5rem;border:1px solid color-mix(in srgb,var(--tinte) 33%,var(--haar));border-radius:50%;background:var(--papier);color:color-mix(in srgb,var(--tinte) 92%,var(--papier));box-shadow:0 0 0 .34rem var(--papier),0 8px 20px color-mix(in srgb,var(--tinte) 8%,transparent)}
.startseite .home-flow-v9__filter-icon svg{width:1.08rem;height:1.08rem;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
.startseite .home-flow-v9__filter-copy{display:contents}
.startseite .home-flow-v9__filter-copy strong{position:absolute;left:50%;bottom:calc(100% + .7rem);transform:translateX(-50%);font-size:.68rem;line-height:1;color:color-mix(in srgb,var(--tinte) 94%,var(--papier));font-weight:720;letter-spacing:-.01em}
.startseite .home-flow-v9__tags{position:absolute;left:50%;top:calc(100% + .62rem);display:flex;gap:.22rem;transform:translateX(-50%)}
.startseite .home-flow-v9__tags span{padding:.19rem .34rem;border:1px solid color-mix(in srgb,var(--tinte) 19%,var(--haar));border-radius:999px;background:color-mix(in srgb,var(--papier) 95%,var(--stempel) 5%);font-family:var(--mono);font-size:.39rem;font-weight:650;color:color-mix(in srgb,var(--tinte) 78%,var(--papier))}
.startseite .home-flow-v9__arrow{position:absolute;top:50%;z-index:4;color:var(--stempel);font-size:1.28rem;line-height:1;transform:translate(-50%,-55%);background:var(--papier);padding:0 .14rem}
.startseite .home-flow-v9__arrow--a{left:46.5%}.startseite .home-flow-v9__arrow--b{left:70.2%}

/* Abschnitt 02 – Arbeiten V3: technische Zeichnung statt Kartenwand */
.startseite #nachweis .voll>.kopf{max-width:18ch;letter-spacing:-.035em}
.startseite #nachweis .voll>.vorspann{max-width:66ch;color:color-mix(in srgb,var(--tinte) 74%,var(--papier));line-height:1.55}
.startseite #nachweis .home-featured-case-v2{position:relative;margin-top:var(--s4);padding:0!important;overflow:visible!important;background:transparent!important;color:var(--tinte)!important;border:0!important;border-top:1px solid var(--strich)!important;border-bottom:1px solid var(--strich)!important;border-radius:0!important;box-shadow:none!important}
.startseite #nachweis .home-featured-case-v2::before{content:'';position:absolute;left:0;top:-1px;width:clamp(4rem,8vw,7rem);height:2px;background:var(--stempel)}
.startseite #nachweis .home-featured-case__intro{display:grid;grid-template-columns:minmax(0,1fr) minmax(21rem,.82fr);gap:clamp(var(--s3),4vw,var(--s5));align-items:start;padding:var(--s4) 0 var(--s3)}
.startseite #nachweis .home-featured-case__intro .mono{color:var(--stempel);font-size:.72rem;letter-spacing:.12em}
.startseite #nachweis .home-featured-case__intro h3{max-width:13ch;margin-top:var(--s2);font-size:clamp(2.25rem,1.72rem + 1.65vw,3.35rem);line-height:.98;letter-spacing:-.047em;color:var(--tinte)}
.startseite #nachweis .home-featured-case__intro>p{max-width:46ch;margin-top:.2rem;color:color-mix(in srgb,var(--tinte) 74%,var(--papier));font-size:clamp(.94rem,.89rem + .13vw,1.02rem);line-height:1.58}
.startseite #nachweis .home-system-board{display:grid;grid-template-columns:minmax(0,1fr) minmax(13.5rem,.28fr);gap:clamp(var(--s3),3vw,var(--s4));margin:0;padding:var(--s3) 0 var(--s4);border-top:1px solid var(--haar)}
.startseite #nachweis .home-system-core{position:relative;min-width:0;padding:var(--s2);border:1px solid color-mix(in srgb,var(--tinte) 11%,var(--haar));border-radius:8px;background:color-mix(in srgb,var(--zone) 42%,var(--papier));overflow:hidden;isolation:isolate}
.startseite #nachweis .home-system-core::before{content:'';position:absolute;inset:0;z-index:-1;opacity:.3;background-image:linear-gradient(color-mix(in srgb,var(--tinte) 2.5%,transparent) 1px,transparent 1px),linear-gradient(90deg,color-mix(in srgb,var(--tinte) 2.5%,transparent) 1px,transparent 1px);background-size:48px 48px;pointer-events:none}
.startseite #nachweis .home-system-band{position:relative;display:grid;grid-template-columns:minmax(6.4rem,.2fr) minmax(0,1fr);gap:var(--s2);align-items:center;padding:var(--s2)}
.startseite #nachweis .home-system-band+.home-system-band{border-top:1px solid color-mix(in srgb,var(--tinte) 10%,var(--haar))}
.startseite #nachweis .home-system-band__label{display:grid;align-content:start;gap:.32rem;padding:.1rem .55rem 0 0}
.startseite #nachweis .home-system-band__label .mono{color:var(--stempel);font-size:.6rem;letter-spacing:.11em}
.startseite #nachweis .home-system-band__label strong{font-size:.81rem;line-height:1.22;color:var(--tinte)}
.startseite #nachweis .home-system-flow{position:relative;display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:.7rem;align-items:start;padding:.15rem 0 .25rem}
.startseite #nachweis .home-system-flow--sales{grid-template-columns:repeat(3,minmax(0,1fr))}
.startseite #nachweis .home-system-flow::before{content:'';position:absolute;top:1.05rem;left:7%;right:7%;height:1px;background:color-mix(in srgb,var(--stempel) 42%,var(--haar));z-index:0}
.startseite #nachweis .home-system-core .home-system-band:first-child .home-system-flow::after{content:'';position:absolute;top:calc(1.05rem - 3px);left:7%;width:7px;height:7px;border-radius:50%;background:var(--stempel);box-shadow:0 0 0 5px color-mix(in srgb,var(--stempel) 7%,transparent);z-index:4;opacity:.9}
.startseite #nachweis .home-system-node{position:relative;z-index:1;min-width:0;min-height:5.7rem;display:grid;grid-template-rows:1.15rem auto 1fr;align-content:start;gap:.3rem;padding:1.65rem .15rem .2rem;background:transparent;border:0;border-radius:0;box-shadow:none}
.startseite #nachweis .home-system-node::before{content:'';position:absolute;top:.78rem;left:50%;width:.55rem;height:.55rem;transform:translate(-50%,-50%);border-radius:50%;background:var(--papier);border:2px solid var(--stempel);box-shadow:0 0 0 4px color-mix(in srgb,var(--papier) 92%,transparent);z-index:2}
.startseite #nachweis .home-system-node .mono{color:var(--stempel);font-size:.58rem;letter-spacing:.08em;text-align:center}
.startseite #nachweis .home-system-node strong{font-size:clamp(.72rem,.68rem + .12vw,.82rem);font-weight:720;line-height:1.15;color:var(--tinte);text-align:center;overflow-wrap:normal;word-break:normal;hyphens:none;white-space:nowrap}
.startseite #nachweis .home-system-node small{max-width:13ch;justify-self:center;color:color-mix(in srgb,var(--tinte) 61%,var(--papier));font-size:.64rem;line-height:1.32;text-align:center}
.startseite #nachweis .home-system-measure{position:relative;display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:0;align-items:center;padding:.65rem 0;border:0;border-block:1px solid color-mix(in srgb,var(--stempel) 42%,var(--haar));border-radius:0;background:transparent}
.startseite #nachweis .home-system-measure::before,.startseite #nachweis .home-system-measure::after{display:none}
.startseite #nachweis .home-system-measure span{position:relative;display:grid;place-items:center;min-height:2.35rem;padding:.35rem .55rem;background:transparent;border:0;border-right:1px solid color-mix(in srgb,var(--tinte) 11%,var(--haar));border-radius:0;font-family:var(--mono);font-size:.61rem;font-weight:600;color:color-mix(in srgb,var(--tinte) 77%,var(--papier));line-height:1.25;text-align:center}
.startseite #nachweis .home-system-measure span:last-child{border-right:0}
.startseite #nachweis .home-system-measure span::before{content:'';width:.28rem;height:.28rem;margin-bottom:.38rem;border-radius:50%;background:var(--stempel);opacity:.8}
.startseite #nachweis .home-system-results{display:grid;grid-template-rows:repeat(2,minmax(0,1fr));gap:0;min-width:0;border-left:1px solid color-mix(in srgb,var(--tinte) 16%,var(--haar));padding-left:var(--s3)}
.startseite #nachweis .home-system-result{display:flex;flex-direction:column;justify-content:center;min-width:0;padding:var(--s2) 0;background:transparent;border:0;border-bottom:1px solid var(--haar);border-radius:0}
.startseite #nachweis .home-system-result:last-child{border-bottom:0}
.startseite #nachweis .home-system-result .mono{margin:0 0 var(--s2);color:color-mix(in srgb,var(--tinte) 58%,var(--papier));font-size:.62rem;letter-spacing:.1em}
.startseite #nachweis .home-system-result strong{display:block;margin:0;font-family:var(--mono);font-size:clamp(2.35rem,1.75rem + 1.8vw,3.8rem);line-height:.88;letter-spacing:-.055em;color:var(--stempel)}
.startseite #nachweis .home-system-result>span{margin-top:.72rem;font-size:.86rem;font-weight:720;line-height:1.25;color:var(--tinte)}
.startseite #nachweis .home-system-result small{margin-top:.38rem;color:color-mix(in srgb,var(--tinte) 60%,var(--papier));font-family:var(--mono);font-size:.65rem;line-height:1.35}
.startseite #nachweis .home-featured-case__footer{display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:var(--s2) var(--s3);margin:0;padding:var(--s2) 0;border-top:1px solid var(--haar)}
.startseite #nachweis .home-featured-case__footer .mono{color:color-mix(in srgb,var(--tinte) 56%,var(--papier));font-size:.64rem}
.startseite #nachweis .home-featured-case__footer .textlink{font-size:.77rem;letter-spacing:.07em}
.startseite #nachweis .home-reference-heading{margin-top:clamp(var(--s4),4vw,var(--s5));font-size:clamp(1.35rem,1.15rem + .55vw,1.7rem);letter-spacing:-.02em}
.startseite #nachweis .home-reference-intro{max-width:58ch;margin-top:.5rem;color:color-mix(in srgb,var(--tinte) 66%,var(--papier));font-size:.9rem;line-height:1.5}
.startseite #nachweis .home-references{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));margin-top:var(--s2);border-top:1px solid var(--strich);border-bottom:1px solid var(--haar)}
.startseite #nachweis .home-references article{min-width:0;padding:var(--s2) var(--s2) var(--s2) 0;border:0;border-right:1px solid var(--haar)}
.startseite #nachweis .home-references article+article{padding-left:var(--s2)}
.startseite #nachweis .home-references article:last-child{border-right:0;padding-right:0}
.startseite #nachweis .home-references .mono{color:color-mix(in srgb,var(--tinte) 56%,var(--papier));font-size:.61rem;letter-spacing:.1em}
.startseite #nachweis .home-references h3{margin:.65rem 0 .55rem;font-size:1.12rem;line-height:1.18;overflow-wrap:anywhere}
.startseite #nachweis .home-references article>p:last-child{max-width:34ch;color:color-mix(in srgb,var(--tinte) 64%,var(--papier));font-size:.8rem;line-height:1.48}
.startseite #nachweis .home-technical{display:grid;grid-template-columns:minmax(0,.85fr) minmax(0,1.15fr);gap:var(--s2);align-items:start;margin-top:var(--s3);padding:var(--s2) var(--s3);background:color-mix(in srgb,var(--zone) 54%,var(--papier));border:0;border-left:2px solid var(--stempel)}
.startseite #nachweis .home-technical h3{margin:0;font-size:1.04rem;line-height:1.24}
.startseite #nachweis .home-technical p{margin-top:.4rem;color:color-mix(in srgb,var(--tinte) 64%,var(--papier));font-size:.82rem;line-height:1.46}
.startseite #nachweis .home-technical ul{display:flex;flex-wrap:wrap;justify-content:flex-start;gap:.45rem .9rem;margin:0;padding:0;list-style:none;font-size:.76rem}
.startseite #nachweis>.blatt .ausgang{margin-top:var(--s2)}
@media (hover:hover) and (pointer:fine){.startseite #nachweis .home-system-node strong{transition:color var(--t-norm) var(--ease-aus)}.startseite #nachweis .home-system-node:hover strong{color:var(--stempel)}.startseite #nachweis .home-system-node:hover::before{background:var(--stempel)}}
@media (max-width:1120px){.startseite #nachweis .home-system-node strong{font-size:.72rem}.startseite #nachweis .home-system-node small{font-size:.61rem}}
@media (max-width:1000px){.startseite #nachweis .home-featured-case__intro{grid-template-columns:minmax(0,1fr) minmax(18rem,.8fr)}.startseite #nachweis .home-system-board{grid-template-columns:1fr}.startseite #nachweis .home-system-results{grid-template-columns:repeat(2,minmax(0,1fr));grid-template-rows:none;border-left:0;border-top:1px solid var(--haar);padding:var(--s2) 0 0;gap:var(--s3)}.startseite #nachweis .home-system-result{border-bottom:0}.startseite #nachweis .home-references{grid-template-columns:1fr}.startseite #nachweis .home-references article,.startseite #nachweis .home-references article+article{padding:var(--s2) 0;border-right:0;border-bottom:1px solid var(--haar)}.startseite #nachweis .home-references article:last-child{border-bottom:0}.startseite #nachweis .home-references article>p:last-child{max-width:60ch}.startseite #nachweis .home-technical{grid-template-columns:1fr}}
@media (max-width:760px){.startseite #nachweis .home-featured-case__intro{grid-template-columns:1fr;gap:var(--s2)}.startseite #nachweis .home-featured-case__intro h3{max-width:16ch}.startseite #nachweis .home-system-core{padding:.35rem}.startseite #nachweis .home-system-band{grid-template-columns:1fr;gap:.65rem;padding:.85rem}.startseite #nachweis .home-system-band__label{padding:0}.startseite #nachweis .home-system-flow{grid-template-columns:repeat(2,minmax(0,1fr));row-gap:1rem}.startseite #nachweis .home-system-flow--sales{grid-template-columns:repeat(3,minmax(0,1fr))}.startseite #nachweis .home-system-flow::before,.startseite #nachweis .home-system-core .home-system-band:first-child .home-system-flow::after{display:none}.startseite #nachweis .home-system-node{min-height:5.3rem;padding:.3rem .15rem}.startseite #nachweis .home-system-node::before{position:static;justify-self:center;transform:none;margin-bottom:.15rem}.startseite #nachweis .home-system-node strong{white-space:normal;font-size:.75rem}.startseite #nachweis .home-system-measure{grid-template-columns:repeat(2,minmax(0,1fr))}.startseite #nachweis .home-system-measure span:nth-child(2){border-right:0}.startseite #nachweis .home-system-results{grid-template-columns:1fr;gap:0}.startseite #nachweis .home-system-result{padding:var(--s3) 0;border-bottom:1px solid var(--haar)}.startseite #nachweis .home-system-result:last-child{border-bottom:0}.startseite #nachweis .home-featured-case__footer{align-items:flex-start}.startseite #nachweis .home-technical ul{display:grid;gap:.6rem}}
@media (max-width:520px){.startseite #nachweis .home-system-flow--sales{grid-template-columns:1fr}.startseite #nachweis .home-system-node{min-height:auto}.startseite #nachweis .home-reference-heading{font-size:1.3rem}}
@media (prefers-reduced-motion:no-preference){.startseite #nachweis .home-system-core .home-system-band:first-child .home-system-flow::after{animation:home-case-signal-v3 7.6s cubic-bezier(.4,0,.2,1) 1.1s infinite both}}
@keyframes home-case-signal-v3{0%,8%{left:7%;opacity:0;transform:scale(.7)}12%{opacity:1}28%{left:32%;opacity:1;transform:scale(1)}46%{left:57%;opacity:1}64%{left:82%;opacity:1;transform:scale(1)}72%,100%{left:93%;opacity:0;transform:scale(.75)}}

/* Hero responsive + motion */
@media (max-width:1240px){.startseite .home-hero.home-hero-v9{grid-template-columns:minmax(0,1.06fr) minmax(30rem,.94fr);gap:1.25rem}.startseite .home-flow-v9{width:calc(100% + 2.2rem);margin-left:-1.8rem}.startseite .home-flow-v9__track{gap:.72rem}.startseite .home-flow-v9__disc{width:5.1rem;height:5.1rem}.startseite .home-flow-v9__stage--request .home-flow-v9__disc{width:6.15rem;height:6.15rem}.startseite .home-flow-v9__stage--crm .home-flow-v9__disc{width:5.2rem;height:5.2rem}.startseite .home-flow-v9__filter{left:78%;}.startseite .home-flow-v9__filter-copy strong{font-size:.64rem}.startseite .home-flow-v9__tags span{font-size:.37rem}}
@media (max-width:980px){.startseite .home-hero.home-hero-v9{grid-template-columns:1fr;min-height:auto;gap:1.65rem;padding-block:1.35rem 2rem}.startseite .home-hero-v9>.home-hero-copy{grid-column:1;grid-row:1}.startseite .home-flow-v9{grid-column:1;grid-row:2;width:min(100%,46rem);min-height:23rem;justify-self:center;margin:0}.startseite .home-flow-v9__track{padding-block:4.6rem 5.2rem}}
@media (max-width:640px){.startseite .home-title-primary{font-size:clamp(2.7rem,13vw,4rem)}.startseite .home-title-secondary{font-size:clamp(2rem,9.4vw,2.85rem)}.startseite .home-hero-v9 .aufriss{font-size:1rem}.startseite .home-trust-row{gap:.55rem .72rem;margin-top:1.1rem}.startseite .home-trust-row span+span::after{display:none}.startseite .home-portrait{grid-template-columns:2.55rem minmax(0,1fr)!important;padding-top:.75rem!important}.startseite .home-portrait img{width:2.55rem!important;height:2.55rem!important}.startseite .home-flow-v9{min-height:18.5rem;width:calc(100% + .4rem);margin-inline:-.2rem}.startseite .home-flow-v9__track{grid-template-columns:3.6rem repeat(3,minmax(0,1fr));gap:.12rem;padding-block:3.4rem 4.6rem}.startseite .home-flow-v9__sources{gap:.22rem}.startseite .home-flow-v9__source{padding:.19rem .27rem;font-size:.35rem}.startseite .home-flow-v9__source svg,.startseite .home-flow-v9__source--ads::before{display:none}.startseite .home-flow-v9__rail{left:12%;right:2%;height:1px}.startseite .home-flow-v9__signal{left:12%;width:.42rem;height:.42rem}.startseite .home-flow-v9__disc{width:3.1rem;height:3.1rem}.startseite .home-flow-v9__stage--request .home-flow-v9__disc{width:3.65rem;height:3.65rem;box-shadow:0 0 0 .46rem color-mix(in srgb,var(--stempel) 3.5%,transparent),0 0 0 .92rem color-mix(in srgb,var(--stempel) 1.5%,transparent),0 9px 22px color-mix(in srgb,var(--stempel) 8%,transparent)}.startseite .home-flow-v9__stage--request .home-flow-v9__disc::before{inset:-1.15rem}.startseite .home-flow-v9__stage--crm{transform:none}.startseite .home-flow-v9__stage--crm .home-flow-v9__disc{width:3.15rem;height:3.15rem}.startseite .home-flow-v9__disc svg{width:1.18rem;height:1.18rem}.startseite .home-flow-v9__stage strong{font-size:.62rem}.startseite .home-flow-v9__filter{left:77.8%;width:1.82rem;height:1.82rem}.startseite .home-flow-v9__filter-icon{width:1.82rem;height:1.82rem;box-shadow:0 0 0 .2rem var(--papier),0 5px 12px color-mix(in srgb,var(--tinte) 7%,transparent)}.startseite .home-flow-v9__filter-copy strong{bottom:calc(100% + .38rem);font-size:.52rem}.startseite .home-flow-v9__tags{display:none}.startseite .home-flow-v9__arrow{font-size:.82rem}.startseite .home-flow-v9__arrow--a{left:47%}.startseite .home-flow-v9__arrow--b{left:69%}}
@media (prefers-reduced-motion:no-preference){
.startseite .home-flow-v9__signal{animation:home-v9-signal 6.4s cubic-bezier(.4,0,.2,1) .8s infinite both}
.startseite .home-flow-v9__stage--request .home-flow-v9__disc::before{animation:home-v9-ring 6.4s ease-in-out .8s infinite both}
.startseite .home-flow-v9__arrow--a{animation:home-v9-arrow-a 6.4s ease-in-out .8s infinite both}
.startseite .home-flow-v9__arrow--b{animation:home-v9-arrow-b 6.4s ease-in-out .8s infinite both}
.startseite .home-flow-v9__filter{animation:home-v9-filter 6.4s ease-in-out .8s infinite both}
}
@media (prefers-reduced-motion:reduce){.startseite .home-hero-v9 .tun{transition-duration:0s}.startseite .home-flow-v9__signal{display:none}}
@keyframes home-v9-signal{0%,8%{left:15%;opacity:0;transform:translate(-50%,-50%) scale(.72)}12%{opacity:1}28%{left:40%;opacity:1;transform:translate(-50%,-50%) scale(1)}47%{left:61%;opacity:1;transform:translate(-50%,-50%) scale(1)}53%{left:61%;opacity:1;transform:translate(-50%,-50%) scale(1.42)}59%{left:61%;opacity:1;transform:translate(-50%,-50%) scale(1)}73%{left:78.6%;opacity:1}87%{left:91%;opacity:1;transform:translate(-50%,-50%) scale(1)}94%,100%{left:91%;opacity:0;transform:translate(-50%,-50%) scale(.78)}}
@keyframes home-v9-ring{0%,35%,64%,100%{opacity:.28;transform:scale(.98)}45%,57%{opacity:.9;transform:scale(1.045)}}
@keyframes home-v9-arrow-a{0%,10%,34%,100%{opacity:.32;transform:translate(-58%,-55%)}18%,28%{opacity:1;transform:translate(-50%,-55%)}}
@keyframes home-v9-arrow-b{0%,50%,88%,100%{opacity:.32;transform:translate(-58%,-55%)}62%,80%{opacity:1;transform:translate(-50%,-55%)}}
@keyframes home-v9-filter{0%,54%,84%,100%{opacity:.74;transform:translate(-50%,-50%) scale(.96)}64%,77%{opacity:1;transform:translate(-50%,-50%) scale(1.05)}}
`;
        document.head.appendChild(style);
    }

    function source(label, kind, className) {
        var el = document.createElement('span');
        el.className = 'home-flow-v9__source ' + (className || '');
        if (kind) {
            el.innerHTML = icon(kind) + '<span>' + label + '</span>';
        } else {
            el.textContent = label;
        }
        return el;
    }

    function stage(kind, label, className) {
        var el = document.createElement('div');
        el.className = 'home-flow-v9__stage ' + (className || '');
        el.innerHTML = '<span class="home-flow-v9__disc">' + icon(kind) + '</span><strong>' + label + '</strong>';
        return el;
    }

    function buildFlow() {
        var visual = document.createElement('figure');
        visual.className = 'home-flow-v9';
        visual.setAttribute('role', 'img');
        visual.setAttribute('aria-label', 'Ads, SEO und Empfehlungen führen über die Website zur Anfrage. Die Anfrage wird nach Region, Leistung und Qualität segmentiert und anschließend ins CRM übergeben.');

        var grid = document.createElement('div');
        grid.className = 'home-flow-v9__grid';
        grid.setAttribute('aria-hidden', 'true');

        var track = document.createElement('div');
        track.className = 'home-flow-v9__track';
        track.setAttribute('aria-hidden', 'true');

        var rail = document.createElement('span');
        rail.className = 'home-flow-v9__rail';

        var signal = document.createElement('span');
        signal.className = 'home-flow-v9__signal';
        signal.setAttribute('aria-hidden', 'true');

        var sources = document.createElement('div');
        sources.className = 'home-flow-v9__sources';
        sources.appendChild(source('Ads', null, 'home-flow-v9__source--ads'));
        sources.appendChild(source('SEO', 'search'));
        sources.appendChild(source('Empfehlung', 'people'));

        var website = stage('website', 'Website', 'home-flow-v9__stage--website');
        var request = stage('request', 'Anfrage', 'home-flow-v9__stage--request');
        var crm = stage('crm', 'CRM', 'home-flow-v9__stage--crm');

        var filter = document.createElement('div');
        filter.className = 'home-flow-v9__filter';
        filter.innerHTML = '<span class="home-flow-v9__filter-icon">' + icon('filter') + '</span><span class="home-flow-v9__filter-copy"><strong>Segmentierung</strong><span class="home-flow-v9__tags"><span>Region</span><span>Leistung</span><span>Qualität</span></span></span>';

        track.appendChild(rail);
        track.appendChild(signal);
        track.appendChild(sources);
        track.appendChild(website);
        track.appendChild(request);
        track.appendChild(crm);
        track.appendChild(filter);
        track.insertAdjacentHTML('beforeend', '<span class="home-flow-v9__arrow home-flow-v9__arrow--a" aria-hidden="true">→</span><span class="home-flow-v9__arrow home-flow-v9__arrow--b" aria-hidden="true">→</span>');

        visual.appendChild(grid);
        visual.appendChild(track);
        return visual;
    }

    function initHero() {
        var hero = document.querySelector('.startseite .home-hero');
        if (!hero || hero.dataset.heroV9 === 'true') return;

        var copy = hero.firstElementChild;
        if (!copy) return;

        hero.dataset.heroV9 = 'true';
        hero.classList.remove('home-hero-v6', 'home-hero-v7', 'home-hero-v8');
        hero.classList.add('home-hero-v9');
        copy.classList.add('home-hero-copy');

        hero.querySelectorAll('.home-system-visual,.home-leadflow,.home-flow-v7,.home-flow-v8,.home-flow-v9').forEach(function (oldVisual) {
            oldVisual.remove();
        });

        var eyebrow = copy.querySelector('.gegenstand');
        if (eyebrow) eyebrow.textContent = 'WordPress · Tracking · CRM';

        var heading = copy.querySelector('h1');
        if (heading) heading.innerHTML = '<span class="home-title-primary">WordPress<br>Freelancer<br>Hannover<span class="home-title-stop">.</span></span><span class="home-title-secondary">Von der Website<br>bis zur Anfrage<span class="home-title-stop">.</span></span>';

        var intro = copy.querySelector('.aufriss');
        if (intro) intro.textContent = 'Ich entwickle WordPress-Websites, die Angebote verständlich machen und Anfragen sauber bis ins CRM führen. Direkt mit mir – ohne Übergabe an ein fremdes Entwicklerteam.';

        var reply = copy.querySelector('.home-reply');
        var trust = copy.querySelector('.home-trust-row');
        if (!trust) {
            trust = document.createElement('div');
            trust.className = 'home-trust-row';
            trust.innerHTML = '<span>Antwort in 2 Werktagen</span><span>Klare Projektpreise</span><span>Direkt mit dem Entwickler</span>';
            if (reply) reply.replaceWith(trust); else copy.appendChild(trust);
        }

        var portrait = hero.querySelector(':scope > .home-portrait') || copy.querySelector('.home-portrait');
        if (portrait) {
            var portraitTitle = portrait.querySelector('figcaption strong');
            var portraitText = portrait.querySelector('figcaption span');
            if (portraitTitle) portraitTitle.textContent = 'Direkt mit Haşim Üner.';
            if (portraitText) portraitText.textContent = 'Konzeption · Entwicklung · Übergabe';
            copy.appendChild(portrait);
        }

        hero.appendChild(buildFlow());
    }

    function initWorks() {
        var section = document.querySelector('.startseite #nachweis');
        if (!section) return;

        section.dataset.worksV3 = 'true';

        var intro = section.querySelector(':scope .voll > .vorspann');
        if (intro) intro.textContent = 'Ausgewählte Arbeiten zeigen die Umsetzung. Der B2B-Fall zeigt zusätzlich, wie Website, Anfrage, Messung und CRM zu einer durchgehenden Strecke werden.';

        var featured = section.querySelector('.home-featured-case');
        if (featured) {
            featured.classList.remove('tafel');
            featured.classList.add('home-featured-case-v2');

            var caseText = featured.querySelector('.home-featured-case__intro > p');
            if (caseText) caseText.textContent = 'Für einen mittelständischen PV-Installationsbetrieb entstand eine durchgehende Strecke aus Website, Qualifizierung, Messung, Server-Side Tracking, CRM und Vertriebsübergabe.';
        }
    }

    function initPage() {
        mountStyles();
        initHero();
        initWorks();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPage, { once: true });
    } else {
        initPage();
    }
})();
