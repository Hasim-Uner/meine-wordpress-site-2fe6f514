/**
 * Startseite.
 *
 * Hero V3: typografische Inszenierung + dynamisches Systemdiagramm.
 * Die Systemgrafik besteht aus DOM/SVG und benoetigt kein Bild-Asset.
 */
(function () {
    'use strict';

    function mountHeroStyles() {
        if (document.getElementById('home-hero-supatomic-styles')) {
            return;
        }

        var style = document.createElement('style');
        style.id = 'home-hero-supatomic-styles';
        style.textContent = `
.startseite .home-hero::before{content:none!important;display:none!important}
.startseite .home-hero{position:relative;display:grid;grid-template-columns:minmax(0,.78fr) minmax(32rem,1.22fr);gap:clamp(2.5rem,4.2vw,5.7rem);align-items:center;isolation:isolate;padding-block:clamp(.5rem,1.5vw,1.5rem)}
.startseite .home-hero>.home-hero-copy{grid-column:1;grid-row:1;min-width:0;position:relative;z-index:3}
.startseite .home-hero .gegenstand{margin-bottom:clamp(1.5rem,2vw,2.2rem);color:var(--stempel);font-family:var(--mono);font-size:clamp(.69rem,.62rem + .16vw,.8rem);font-weight:500;letter-spacing:.18em;line-height:1.55;text-transform:uppercase;max-width:48ch}
.startseite .home-hero h1{max-width:none!important;margin:0!important;font-size:inherit!important;line-height:1!important;letter-spacing:0!important}
.startseite .home-title-primary,.startseite .home-title-secondary{display:block}
.startseite .home-title-primary{font-family:var(--sans);font-size:clamp(3.55rem,4.55vw,5.35rem);font-weight:630;line-height:.88;letter-spacing:-.055em;color:var(--tinte)}
.startseite .home-title-secondary{margin-top:.22em;font-family:var(--sans);font-size:clamp(3rem,3.85vw,4.55rem);font-weight:520;line-height:.94;letter-spacing:-.048em;color:color-mix(in srgb,var(--tinte) 68%,var(--papier))}
.startseite .home-title-stop{color:var(--stempel)}
.startseite .home-hero .aufriss{max-width:42ch;margin-top:clamp(1.8rem,2.4vw,2.8rem);font-size:clamp(1rem,.92rem + .24vw,1.16rem);line-height:1.62;color:color-mix(in srgb,var(--tinte) 91%,var(--papier))}
.startseite .home-hero .ausgang{display:flex;flex-wrap:wrap;gap:.85rem;margin-top:clamp(1.8rem,2.3vw,2.6rem)}
.startseite .home-hero .tun{min-height:3.35rem;padding-inline:1.45rem;border-radius:3px;transition:transform .2s ease,box-shadow .2s ease,background .2s ease,border-color .2s ease}
.startseite .home-hero .tun:not(.still){box-shadow:0 10px 28px color-mix(in srgb,var(--stempel) 22%,transparent)}
.startseite .home-hero .tun:not(.still):hover{transform:translateY(-2px);box-shadow:0 14px 34px color-mix(in srgb,var(--stempel) 28%,transparent)}
.startseite .home-hero .tun.still{background:color-mix(in srgb,var(--papier) 94%,transparent);border-color:color-mix(in srgb,var(--tinte) 46%,transparent)}
.startseite .home-hero .tun.still:hover{transform:translateY(-2px);border-color:var(--tinte);box-shadow:0 10px 24px color-mix(in srgb,var(--tinte) 8%,transparent)}
.startseite .home-trust-row{display:flex;flex-wrap:wrap;align-items:center;gap:.7rem 1rem;margin-top:1.15rem;color:var(--grau);font-size:.8rem;line-height:1.35}
.startseite .home-trust-row span{display:inline-flex;align-items:center;gap:.45rem;white-space:nowrap}
.startseite .home-trust-row span::before{content:'✓';display:grid;place-items:center;width:1.08rem;height:1.08rem;border:1px solid var(--stempel);border-radius:50%;color:var(--stempel);font-size:.64rem;font-weight:700;line-height:1}
.startseite .home-trust-row span+span::after{content:'';position:relative;order:-1;width:1px;height:.95rem;margin-right:.25rem;background:var(--haar)}

.startseite .home-system-visual{grid-column:2;grid-row:1;position:relative;z-index:2;width:min(112%,56rem);justify-self:center;align-self:center;margin-right:-7%;aspect-ratio:1.33/1;perspective:1300px;contain:layout paint}
.startseite .home-system-canvas{position:absolute;inset:0;transform-style:preserve-3d;transition:transform .16s cubic-bezier(.2,.7,.3,1);will-change:transform}
.startseite .home-system-canvas::before{content:'';position:absolute;inset:1% 2% 0 4%;border-radius:50%;background:radial-gradient(circle at 52% 47%,color-mix(in srgb,var(--stempel) 13%,transparent) 0 8%,color-mix(in srgb,var(--stempel) 7%,transparent) 27%,transparent 68%);filter:blur(2px);pointer-events:none}
.startseite .home-system-canvas::after{content:'';position:absolute;inset:8% 4% 10% 4%;opacity:.72;background-image:linear-gradient(color-mix(in srgb,var(--tinte) 5%,transparent) 1px,transparent 1px),linear-gradient(90deg,color-mix(in srgb,var(--tinte) 5%,transparent) 1px,transparent 1px);background-size:58px 58px;mask-image:radial-gradient(ellipse at center,#000 32%,transparent 82%);-webkit-mask-image:radial-gradient(ellipse at center,#000 32%,transparent 82%);pointer-events:none}
.startseite .home-system-lines{position:absolute;inset:0;width:100%;height:100%;overflow:visible;z-index:1;pointer-events:none}
.startseite .home-system-lines .orbit{fill:none;stroke:color-mix(in srgb,var(--stempel) 25%,transparent);stroke-width:1.1;stroke-dasharray:2.5 8;transform-box:fill-box;transform-origin:center}
.startseite .home-system-lines .orbit-soft{fill:none;stroke:color-mix(in srgb,var(--stempel) 12%,transparent);stroke-width:1}
.startseite .home-system-lines .flow-base{fill:none;stroke:color-mix(in srgb,var(--tinte) 10%,transparent);stroke-width:2}
.startseite .home-system-lines .flow{fill:none;stroke:var(--stempel);stroke-width:2.4;stroke-linecap:round;stroke-dasharray:10 14;filter:drop-shadow(0 0 4px color-mix(in srgb,var(--stempel) 18%,transparent))}
.startseite .home-system-lines .flow-dot{fill:var(--stempel);filter:drop-shadow(0 0 5px color-mix(in srgb,var(--stempel) 38%,transparent))}
.startseite .home-system-lines .node{fill:var(--papier);stroke:var(--stempel);stroke-width:1.6}
.startseite .home-system-lines .axis{stroke:color-mix(in srgb,var(--stempel) 18%,transparent);stroke-width:1}

.startseite .sys-card{position:absolute;z-index:3;padding:1.1rem 1.15rem 1.05rem;border:1px solid color-mix(in srgb,var(--stempel) 23%,var(--haar));border-radius:15px;background:color-mix(in srgb,var(--papier) 95%,transparent);box-shadow:0 18px 42px color-mix(in srgb,var(--tinte) 11%,transparent),inset 0 1px 0 rgba(255,255,255,.86);backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);transform-style:preserve-3d}
.startseite .sys-card::before{content:'';position:absolute;inset:0;border-radius:inherit;background:linear-gradient(138deg,rgba(255,255,255,.64),transparent 42%);pointer-events:none}
.startseite .sys-card--website{left:4%;top:15%;width:34%;min-height:34%}
.startseite .sys-card--request{right:1%;top:9%;width:31%;min-height:31%;background:color-mix(in srgb,var(--stempel) 7%,var(--papier))}
.startseite .sys-card--crm{right:0;top:54%;width:31%;min-height:32%}
.startseite .sys-kicker{position:relative;z-index:1;display:flex;align-items:center;justify-content:space-between;gap:.75rem;margin-bottom:.72rem;color:var(--grau);font-family:var(--mono);font-size:.62rem;font-weight:600;letter-spacing:.16em;text-transform:uppercase}
.startseite .sys-kicker::after{content:'•••';color:var(--stempel);letter-spacing:.08em}
.startseite .sys-title{position:relative;z-index:1;margin:0;color:var(--tinte);font-family:var(--sans);font-size:clamp(.9rem,1.08vw,1.12rem);font-weight:700;line-height:1.2;letter-spacing:-.02em}
.startseite .sys-browser{position:relative;z-index:1;margin:.2rem 0 .9rem;padding:.7rem;border:1px solid var(--haar);border-radius:8px;background:color-mix(in srgb,var(--zone) 76%,var(--papier));box-shadow:inset 0 1px 0 rgba(255,255,255,.72)}
.startseite .sys-browser-bar{display:flex;gap:.22rem;margin-bottom:.55rem}
.startseite .sys-browser-bar i{display:block;width:.27rem;height:.27rem;border-radius:50%;background:color-mix(in srgb,var(--grau) 45%,transparent)}
.startseite .sys-browser-layout{display:grid;grid-template-columns:.7fr 1.3fr;gap:.55rem;align-items:center}
.startseite .sys-browser-image{aspect-ratio:1.2;border-radius:5px;background:linear-gradient(145deg,color-mix(in srgb,var(--tinte) 8%,var(--papier)),color-mix(in srgb,var(--tinte) 16%,var(--papier)))}
.startseite .sys-browser-copy{display:grid;gap:.3rem}
.startseite .sys-browser-copy i{display:block;height:.28rem;border-radius:2px;background:color-mix(in srgb,var(--tinte) 9%,transparent)}
.startseite .sys-browser-copy i:nth-child(2){width:82%}
.startseite .sys-browser-copy i:last-child{width:47%;height:.42rem;margin-top:.16rem;background:var(--stempel)}
.startseite .sys-list{position:relative;z-index:1;display:grid;gap:.32rem;margin-top:.72rem;color:color-mix(in srgb,var(--tinte) 70%,var(--papier));font-size:.68rem;line-height:1.3}
.startseite .sys-list span{display:flex;align-items:center;gap:.45rem}
.startseite .sys-list span::before{content:'✓';display:grid;place-items:center;width:.83rem;height:.83rem;flex:0 0 auto;border:1px solid color-mix(in srgb,var(--stempel) 70%,transparent);border-radius:50%;color:var(--stempel);font-size:.5rem;font-weight:800}
.startseite .sys-metric{position:relative;z-index:1;display:grid;grid-template-columns:auto 1fr;gap:.7rem;align-items:center;margin:.35rem 0 .7rem}
.startseite .sys-bars-icon{display:flex;align-items:end;gap:.16rem;width:2.35rem;height:2.35rem;padding:.42rem;border-radius:8px;background:color-mix(in srgb,var(--stempel) 9%,var(--papier))}
.startseite .sys-bars-icon i{width:.28rem;border-radius:3px 3px 1px 1px;background:var(--stempel);opacity:.86}
.startseite .sys-bars-icon i:nth-child(1){height:38%}.startseite .sys-bars-icon i:nth-child(2){height:68%}.startseite .sys-bars-icon i:nth-child(3){height:90%}.startseite .sys-bars-icon i:nth-child(4){height:58%}
.startseite .sys-progress{position:relative;z-index:1;display:flex;gap:.28rem;margin:.1rem 0 .75rem}
.startseite .sys-progress i{height:.34rem;flex:1;border-radius:999px;background:var(--stempel);opacity:.78}.startseite .sys-progress i:last-child{opacity:.14}
.startseite .sys-meta{position:relative;z-index:1;margin:.28rem 0 .72rem;color:var(--grau);font-family:var(--mono);font-size:.58rem;letter-spacing:.04em}
.startseite .sys-live{position:relative;z-index:1;display:flex;align-items:center;gap:.45rem;width:100%;margin:.42rem 0 .7rem;padding:.42rem .55rem;border-radius:6px;background:color-mix(in srgb,var(--stempel) 8%,var(--papier));color:var(--stempel);font-family:var(--mono);font-size:.58rem;font-weight:700;letter-spacing:.09em}
.startseite .sys-live::before{content:'';width:.45rem;height:.45rem;border-radius:50%;background:var(--stempel);box-shadow:0 0 0 0 color-mix(in srgb,var(--stempel) 28%,transparent)}

.startseite .sys-core{position:absolute;z-index:4;left:47%;top:44%;width:21%;aspect-ratio:1;transform:translate(-50%,-50%);display:grid;place-items:center;border-radius:50%;background:radial-gradient(circle,color-mix(in srgb,var(--stempel) 11%,var(--papier)) 0 31%,var(--papier) 32% 52%,transparent 53%);border:1px solid color-mix(in srgb,var(--stempel) 57%,transparent);box-shadow:0 0 0 17px color-mix(in srgb,var(--stempel) 4%,transparent),0 0 0 36px color-mix(in srgb,var(--stempel) 3%,transparent),0 16px 42px color-mix(in srgb,var(--stempel) 12%,transparent)}
.startseite .sys-core::before,.startseite .sys-core::after{content:'';position:absolute;border-radius:50%;border:1px dashed color-mix(in srgb,var(--stempel) 31%,transparent)}
.startseite .sys-core::before{inset:-25%}.startseite .sys-core::after{inset:-48%;opacity:.7}
.startseite .sys-core-dot{width:1.35rem;height:1.35rem;border-radius:50%;background:var(--stempel);box-shadow:0 0 0 .55rem color-mix(in srgb,var(--stempel) 8%,transparent)}
.startseite .sys-core-copy{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:space-between;padding:11% 0 13%;pointer-events:none}
.startseite .sys-core-copy span{font-family:var(--mono);font-size:.54rem;font-weight:600;letter-spacing:.13em;color:var(--grau);text-transform:uppercase}
.startseite .sys-core-copy span:last-child{font-size:.45rem;letter-spacing:.08em}
.startseite .sys-chips{position:absolute;z-index:4;left:47%;top:61%;transform:translateX(-50%);display:flex;gap:.36rem}
.startseite .sys-chips span{padding:.34rem .62rem;border:1px solid var(--haar);border-radius:999px;background:color-mix(in srgb,var(--papier) 94%,transparent);box-shadow:0 5px 14px color-mix(in srgb,var(--tinte) 6%,transparent);font-family:var(--mono);font-size:.5rem;font-weight:600;letter-spacing:.05em;color:var(--grau)}
.startseite .sys-flow-caption{position:absolute;z-index:4;left:27%;bottom:1%;font-family:var(--mono);font-size:.52rem;font-weight:600;letter-spacing:.16em;color:color-mix(in srgb,var(--grau) 78%,transparent);white-space:nowrap}
.startseite .sys-note{position:absolute;z-index:4;color:var(--stempel);font-family:var(--sans);font-style:italic;font-size:.7rem;line-height:1.12;letter-spacing:-.02em;opacity:.86;transform:rotate(-7deg)}
.startseite .sys-note--direct{left:14%;bottom:15%;color:color-mix(in srgb,var(--tinte) 58%,var(--papier));transform:rotate(-8deg)}
.startseite .sys-note--result{right:1%;bottom:7%;text-align:right;transform:rotate(5deg)}

.startseite .home-system-visual .home-portrait{position:absolute!important;z-index:6;left:35%;bottom:8%;width:48%!important;display:grid!important;grid-template-columns:4.2rem minmax(0,1fr)!important;gap:.8rem!important;align-items:center!important;margin:0!important;padding:.72rem .82rem!important;border:1px solid color-mix(in srgb,var(--stempel) 18%,var(--haar))!important;border-radius:13px!important;background:color-mix(in srgb,var(--papier) 96%,transparent)!important;box-shadow:0 18px 42px color-mix(in srgb,var(--tinte) 10%,transparent)!important;backdrop-filter:blur(14px);-webkit-backdrop-filter:blur(14px);transform:translateZ(34px)}
.startseite .home-system-visual .home-portrait img{width:4.2rem!important;height:4.2rem!important;aspect-ratio:1!important;object-fit:cover!important;object-position:50% 18%!important;border-radius:8px!important}
.startseite .home-system-visual .home-portrait figcaption{display:grid!important;gap:.2rem!important;padding:0!important;font-size:.7rem!important;line-height:1.34!important}
.startseite .home-system-visual .home-portrait figcaption strong{font-size:.79rem!important;color:var(--tinte)}
.startseite .home-system-visual .home-portrait figcaption span{display:block!important;color:var(--grau)}
.startseite .home-system-visual .home-portrait figcaption a{font-size:.68rem!important;justify-self:start}

@media (hover:hover) and (pointer:fine){
.startseite .sys-card{transition:transform .25s ease,box-shadow .25s ease,border-color .25s ease}
.startseite .sys-card:hover{transform:translateY(-5px)!important;border-color:color-mix(in srgb,var(--stempel) 42%,var(--haar));box-shadow:0 25px 52px color-mix(in srgb,var(--tinte) 14%,transparent)}
}
@media (max-width:1180px){
.startseite .home-hero{grid-template-columns:minmax(0,.9fr) minmax(27rem,1.1fr);gap:2.7rem}
.startseite .home-system-visual{width:min(108%,48rem);margin-right:-4%}
.startseite .home-title-primary{font-size:clamp(3.1rem,4.2vw,4.65rem)}
.startseite .home-title-secondary{font-size:clamp(2.65rem,3.55vw,3.95rem)}
}
@media (max-width:920px){
.startseite .home-hero{grid-template-columns:1fr;gap:2.2rem}
.startseite .home-hero>.home-hero-copy{grid-column:1;grid-row:1;max-width:44rem}
.startseite .home-system-visual{grid-column:1;grid-row:2;width:min(100%,48rem);margin:0 auto;justify-self:center}
.startseite .home-title-primary{font-size:clamp(3.4rem,8.2vw,5.2rem)}
.startseite .home-title-secondary{font-size:clamp(3rem,7.3vw,4.65rem)}
}
@media (max-width:640px){
.startseite .home-hero{gap:1.75rem;padding-top:0}
.startseite .home-hero .gegenstand{margin-bottom:1.25rem;font-size:.64rem;letter-spacing:.14em}
.startseite .home-title-primary{font-size:clamp(2.75rem,14vw,4rem);line-height:.91}
.startseite .home-title-secondary{font-size:clamp(2.38rem,11.9vw,3.45rem);line-height:.96;margin-top:.28em}
.startseite .home-hero .aufriss{font-size:1rem;line-height:1.58;margin-top:1.45rem;max-width:36ch}
.startseite .home-hero .ausgang{display:grid;grid-template-columns:1fr;margin-top:1.45rem}
.startseite .home-hero .tun{width:100%;justify-content:center}
.startseite .home-trust-row{display:grid;grid-template-columns:1fr 1fr;gap:.55rem .8rem;font-size:.72rem}
.startseite .home-trust-row span+span::after{display:none}
.startseite .home-system-visual{aspect-ratio:auto;min-height:auto;width:100%;margin-top:.35rem;contain:none}
.startseite .home-system-canvas{position:relative;inset:auto;display:grid;grid-template-columns:1fr 1fr;gap:.7rem;padding:1.2rem .2rem 7.7rem;transform:none!important}
.startseite .home-system-canvas::before{inset:0;border-radius:30%;background:radial-gradient(circle at 50% 40%,color-mix(in srgb,var(--stempel) 9%,transparent),transparent 68%)}
.startseite .home-system-canvas::after{inset:0;background-size:42px 42px;opacity:.55}
.startseite .home-system-lines{display:none}
.startseite .sys-card{position:relative;left:auto!important;right:auto!important;top:auto!important;width:auto!important;min-height:0!important;padding:.85rem;border-radius:12px}
.startseite .sys-card--website{grid-column:1;grid-row:1}
.startseite .sys-card--request{grid-column:2;grid-row:1}
.startseite .sys-card--crm{grid-column:2;grid-row:2}
.startseite .sys-core{position:relative;grid-column:1;grid-row:2;left:auto;top:auto;width:7.8rem;align-self:center;justify-self:center;transform:none}
.startseite .sys-chips{position:relative;grid-column:1;grid-row:3;left:auto;top:auto;transform:none;align-self:start;justify-self:center;margin-top:-.25rem}
.startseite .sys-flow-caption{display:none}
.startseite .sys-note{display:none}
.startseite .home-system-visual .home-portrait{left:.2rem!important;right:.2rem!important;bottom:.2rem!important;width:auto!important;grid-template-columns:3.8rem minmax(0,1fr)!important}
.startseite .home-system-visual .home-portrait img{width:3.8rem!important;height:3.8rem!important}
.startseite .sys-list{font-size:.59rem}.startseite .sys-kicker{font-size:.52rem}.startseite .sys-title{font-size:.88rem}.startseite .sys-meta{font-size:.5rem}
}
@media (max-width:440px){
.startseite .home-trust-row{grid-template-columns:1fr}
.startseite .home-system-canvas{grid-template-columns:1fr;padding-bottom:7.8rem}
.startseite .sys-card--website,.startseite .sys-card--request,.startseite .sys-card--crm,.startseite .sys-core,.startseite .sys-chips{grid-column:1;grid-row:auto}
.startseite .sys-browser{display:none}
.startseite .sys-card--website{order:1}.startseite .sys-core{order:2}.startseite .sys-chips{order:3}.startseite .sys-card--request{order:4}.startseite .sys-card--crm{order:5}
}
@media (prefers-reduced-motion:no-preference){
.startseite .home-system-lines .flow{animation:home-sys-flow 3.4s linear infinite}
.startseite .home-system-lines .orbit:first-of-type{animation:home-sys-orbit 26s linear infinite}
.startseite .home-system-lines .orbit:nth-of-type(2){animation:home-sys-orbit-reverse 31s linear infinite}
.startseite .sys-core-dot{animation:home-sys-core 2.4s ease-in-out infinite}
.startseite .sys-live::before{animation:home-sys-live 1.9s ease-out infinite}
.startseite .sys-card--website{animation:home-sys-float-a 7.2s ease-in-out infinite}
.startseite .sys-card--request{animation:home-sys-float-b 8.3s ease-in-out infinite}
.startseite .sys-card--crm{animation:home-sys-float-c 7.7s ease-in-out infinite}
}
@keyframes home-sys-flow{to{stroke-dashoffset:-48}}
@keyframes home-sys-orbit{to{transform:rotate(360deg)}}
@keyframes home-sys-orbit-reverse{to{transform:rotate(-360deg)}}
@keyframes home-sys-core{0%,100%{transform:scale(.82);opacity:.74}50%{transform:scale(1.18);opacity:1}}
@keyframes home-sys-live{0%{box-shadow:0 0 0 0 color-mix(in srgb,var(--stempel) 26%,transparent)}70%{box-shadow:0 0 0 .55rem transparent}100%{box-shadow:0 0 0 0 transparent}}
@keyframes home-sys-float-a{0%,100%{transform:translate3d(0,0,0)}50%{transform:translate3d(0,-6px,18px)}}
@keyframes home-sys-float-b{0%,100%{transform:translate3d(0,0,0)}50%{transform:translate3d(0,5px,22px)}}
@keyframes home-sys-float-c{0%,100%{transform:translate3d(0,0,0)}50%{transform:translate3d(-3px,-5px,14px)}}
@media (prefers-reduced-motion:reduce){.startseite .home-system-canvas,.startseite .sys-card,.startseite .home-hero .tun{transition:none!important;animation:none!important;transform:none!important}}
`;
        document.head.appendChild(style);
    }

    function enhanceHeroCopy(hero) {
        var copy = hero.firstElementChild;
        if (copy && !copy.classList.contains('home-hero-copy')) {
            copy.classList.add('home-hero-copy');
        }

        var heading = hero.querySelector('h1');
        if (heading && !heading.querySelector('.home-title-primary')) {
            heading.innerHTML = '<span class="home-title-primary">WordPress<br>Freelancer<br>Hannover<span class="home-title-stop">.</span></span><span class="home-title-secondary">Von der Website<br>bis zur Anfrage.</span>';
        }

        var reply = hero.querySelector('.home-reply');
        if (reply) {
            reply.className = 'home-trust-row';
            reply.innerHTML = '<span>Antwort in 2 Werktagen</span><span>Transparente Preise</span><span>Direkter Ansprechpartner</span>';
        }
    }

    function systemMarkup() {
        return '' +
            '<div class="home-system-canvas">' +
                '<svg class="home-system-lines" viewBox="0 0 1000 760" aria-hidden="true" focusable="false">' +
                    '<circle class="orbit" cx="485" cy="345" r="235"/>' +
                    '<circle class="orbit" cx="485" cy="345" r="168"/>' +
                    '<circle class="orbit-soft" cx="485" cy="345" r="305"/>' +
                    '<path class="axis" d="M486 40V680M145 346H900" opacity=".22"/>' +
                    '<path class="flow-base" d="M310 250 C370 250 355 337 430 344 C548 355 566 257 688 228 C765 209 790 278 805 325 C825 388 850 398 862 430"/>' +
                    '<path class="flow" d="M310 250 C370 250 355 337 430 344 C548 355 566 257 688 228 C765 209 790 278 805 325 C825 388 850 398 862 430"/>' +
                    '<circle class="node" cx="310" cy="250" r="5"/><circle class="flow-dot" cx="356" cy="292" r="5"/><circle class="flow-dot" cx="430" cy="344" r="5"/><circle class="flow-dot" cx="688" cy="228" r="5"/><circle class="flow-dot" cx="790" cy="278" r="5"/><circle class="node" cx="862" cy="430" r="5"/>' +
                '</svg>' +
                '<div class="sys-card sys-card--website" aria-hidden="true">' +
                    '<div class="sys-kicker">01 · Website</div>' +
                    '<div class="sys-browser"><div class="sys-browser-bar"><i></i><i></i><i></i></div><div class="sys-browser-layout"><div class="sys-browser-image"></div><div class="sys-browser-copy"><i></i><i></i><i></i></div></div></div>' +
                    '<div class="sys-list"><span>Individuelles Design</span><span>Technisch sauber</span><span>Conversion-orientiert</span></div>' +
                '</div>' +
                '<div class="sys-core" aria-hidden="true"><div class="sys-core-dot"></div><div class="sys-core-copy"><span>02 · Tracking</span><span>Signal verknüpft</span></div></div>' +
                '<div class="sys-chips" aria-hidden="true"><span>GA4</span><span>GTM</span><span>SERVER</span></div>' +
                '<div class="sys-card sys-card--request" aria-hidden="true">' +
                    '<div class="sys-kicker">03 · Anfrage</div>' +
                    '<div class="sys-metric"><div class="sys-bars-icon"><i></i><i></i><i></i><i></i></div><h3 class="sys-title">qualifiziert</h3></div>' +
                    '<div class="sys-progress"><i></i><i></i><i></i><i></i></div>' +
                    '<div class="sys-list"><span>Mehr passende Anfragen</span><span>Weniger Streuverlust</span><span>Messbar mehr Umsatz</span></div>' +
                '</div>' +
                '<div class="sys-card sys-card--crm" aria-hidden="true">' +
                    '<div class="sys-kicker">04 · CRM</div><h3 class="sys-title">Lead erkannt.</h3>' +
                    '<div class="sys-meta">Quelle · Intent · Status</div><div class="sys-live">LIVE HANDOFF</div>' +
                    '<div class="sys-list"><span>Automatische Übergabe</span><span>Saubere Daten</span><span>Effiziente Nachverfolgung</span></div>' +
                '</div>' +
                '<div class="sys-note sys-note--direct" aria-hidden="true">Direkt.<br>Persönlich.<br>Effizient.</div>' +
                '<div class="sys-note sys-note--result" aria-hidden="true">Mehr Anfragen.<br>Weniger Aufwand.</div>' +
                '<div class="sys-flow-caption" aria-hidden="true">WEBSITE → SIGNAL → QUALIFIZIERUNG → VERTRIEB</div>' +
            '</div>';
    }

    function mountHeroVisual(hero) {
        var oldVisual = hero.querySelector('.home-system-visual');
        if (oldVisual) {
            var nestedPortrait = oldVisual.querySelector('.home-portrait');
            if (nestedPortrait) {
                hero.appendChild(nestedPortrait);
            }
            oldVisual.remove();
        }

        var portrait = hero.querySelector('.home-portrait');
        var visual = document.createElement('div');
        visual.className = 'home-system-visual home-system-visual--supatomic';
        visual.innerHTML = systemMarkup();

        hero.appendChild(visual);
        if (portrait) {
            visual.querySelector('.home-system-canvas').appendChild(portrait);
        }

        var reduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        var canvas = visual.querySelector('.home-system-canvas');
        if (!reduced && canvas && window.innerWidth > 920) {
            visual.addEventListener('pointermove', function (event) {
                var rect = visual.getBoundingClientRect();
                var x = (event.clientX - rect.left) / rect.width - 0.5;
                var y = (event.clientY - rect.top) / rect.height - 0.5;
                canvas.style.transform = 'rotateX(' + (-y * 3.6).toFixed(2) + 'deg) rotateY(' + (x * 4.8).toFixed(2) + 'deg) translate3d(' + (x * 6).toFixed(1) + 'px,' + (y * 4).toFixed(1) + 'px,0)';
            });
            visual.addEventListener('pointerleave', function () {
                canvas.style.transform = '';
            });
        }
    }

    function initSectionObserver(root) {
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

        var hero = root.querySelector('.home-hero');
        if (hero) {
            mountHeroStyles();
            enhanceHeroCopy(hero);
            mountHeroVisual(hero);
        }

        initSectionObserver(root);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init, { once: true });
    } else {
        init();
    }
})();
