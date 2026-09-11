/* Stable route-specific editorial refinements for /wordpress-projekte-auslagern/. */
(function () {
    'use strict';

    if (typeof document === 'undefined') return;

    var sectionLabels = {
        'wann-auslagern': 'Einordnung',
        'delivery-kette': 'Der Ablauf',
        'kontrollverlust': 'Kontrolle',
        'freelancer-auswaehlen': 'Auswahl',
        'warnsignale': 'Risiken',
        'kosten': 'Wirtschaftlichkeit',
        'klein-starten': 'Einstieg',
        'checkliste': 'Checkliste',
        'zusammenarbeit': 'Zusammenarbeit',
        'faq': 'FAQ'
    };

    function padIndex(index) {
        return String(index + 1).padStart(2, '0');
    }

    function injectStableStyles() {
        ['ao-editorial-refinement-v2', 'ao-editorial-refinement-v3'].forEach(function (id) {
            var oldStyle = document.getElementById(id);
            if (oldStyle) oldStyle.remove();
        });

        if (document.getElementById('ao-editorial-refinement-v4')) return;

        var style = document.createElement('style');
        style.id = 'ao-editorial-refinement-v4';
        style.textContent = [
            '.single-post .nexus-blog-header__context-links{display:none!important;}',
            '.single-post .nexus-article-hero--editorial::before{display:none!important;content:none!important;}',

            '@media (min-width:1100px){',
            '.single-post .nexus-article-reader-header~.nexus-single-container{grid-template-columns:minmax(0,.92fr) minmax(470px,1.08fr)!important;column-gap:clamp(4.4rem,6vw,6.7rem)!important;}',
            '.single-post .nexus-article-reader-header~.nexus-single-container .nexus-title{max-width:15.5ch!important;font-size:clamp(3rem,4.35vw,4.75rem)!important;line-height:1.005!important;letter-spacing:-.043em!important;}',
            '.single-post .nexus-article-reader-header~.nexus-single-container .nexus-article-cover{width:min(100%,750px)!important;max-width:750px!important;border-color:rgba(217,121,44,.42)!important;box-shadow:0 34px 86px rgba(0,0,0,.40),0 0 0 1px rgba(255,255,255,.025) inset!important;}',
            '.single-post .nexus-article-reader-header~.nexus-single-container .nexus-article-cover::after{border-color:rgba(217,121,44,.08)!important;}',
            '.single-post .nexus-article-reader-header~.nexus-single-container .nexus-article-cover::before{background:rgba(217,121,44,.12)!important;filter:blur(62px)!important;}',
            '}',

            '@media (min-width:1280px){',
            'body.single-post .nexus-share-rail{left:30px!important;opacity:0!important;visibility:hidden!important;pointer-events:none!important;transform:translateY(-50%) translateX(-8px)!important;transition:opacity .18s ease,transform .18s ease,visibility .18s ease!important;}',
            'body.single-post.ao-share-ready .nexus-share-rail{opacity:1!important;visibility:visible!important;pointer-events:auto!important;transform:translateY(-50%) translateX(0)!important;}',
            '}',

            '.single-post .nexus-article-reader-header~.nexus-single-container .nexus-post-layout{position:relative!important;}',
            '.single-post .nexus-article-reader-header~.nexus-single-container .nexus-post-layout::before{position:absolute;top:0;left:50%;width:100vw;height:1px;transform:translateX(-50%);background:linear-gradient(90deg,transparent 0%,rgba(217,121,44,.28) 30%,rgba(217,121,44,.12) 62%,transparent 100%);content:"";}',

            '@media (min-width:1100px){',
            '.single-post .nexus-article-reader-header~.nexus-single-container .nexus-post-layout{grid-template-columns:minmax(0,760px) 230px!important;gap:clamp(3.5rem,4.8vw,4.4rem)!important;max-width:1050px!important;}',
            '.single-post .nexus-article-reader-header~.nexus-single-container .nexus-article-content{max-width:760px!important;}',
            '.single-post .nexus-article-reader-header~.nexus-single-container .nexus-sidebar,.single-post .nexus-article-reader-header~.nexus-single-container .nexus-sidebar.nexus-reader-toc{position:sticky!important;top:112px!important;width:230px!important;max-width:230px!important;align-self:start!important;justify-self:end!important;transform:none!important;}',
            '.single-post .nexus-sidebar .sticky-toc,.single-post .nexus-reader-toc .sticky-toc{width:230px!important;max-height:calc(100vh - 150px)!important;margin:0!important;padding:1rem .95rem 1.05rem!important;overflow:auto!important;border:1px solid #dfdcd5!important;border-radius:12px!important;background:rgba(255,253,249,.94)!important;box-shadow:0 10px 28px rgba(21,26,30,.045)!important;backdrop-filter:blur(8px)!important;transition:none!important;}',
            '.single-post .nexus-sidebar .nexus-reader-toc__toggle{display:none!important;}',
            '.single-post .nexus-sidebar .sticky-toc h2,.single-post .nexus-sidebar .sticky-toc .nexus-reader-toc__label{display:block!important;margin:0 0 .72rem!important;color:#62686c!important;font-size:.62rem!important;font-weight:760!important;letter-spacing:.11em!important;text-transform:uppercase!important;}',
            '.single-post .nexus-sidebar #toc-list{display:block!important;margin:0!important;padding:0!important;list-style:none!important;}',
            '.single-post .nexus-sidebar #toc-list::before{display:none!important;}',
            '.single-post .nexus-sidebar #toc-list li{display:grid!important;grid-template-columns:27px minmax(0,1fr)!important;gap:.45rem!important;align-items:start!important;margin:0!important;padding:.25rem 0!important;}',
            '.single-post .nexus-sidebar #toc-list li.is-subsection{display:none!important;}',
            '.single-post .nexus-sidebar #toc-list li::before{display:block!important;width:auto!important;height:auto!important;margin:0!important;border:0!important;border-radius:0!important;background:transparent!important;box-shadow:none!important;color:#a0a3a5!important;font-family:var(--font-mono,ui-monospace,monospace)!important;font-size:.61rem!important;font-weight:700!important;line-height:1.42!important;content:attr(data-ao-index)!important;}',
            '.single-post .nexus-sidebar #toc-list li.is-current::before{color:#c66722!important;}',
            '.single-post .nexus-sidebar #toc-list a{display:block!important;min-width:0!important;color:#666c71!important;font-size:.68rem!important;line-height:1.35!important;text-decoration:none!important;white-space:normal!important;overflow:visible!important;opacity:1!important;transform:none!important;}',
            '.single-post .nexus-sidebar #toc-list a.active,.single-post .nexus-sidebar #toc-list a[aria-current="location"],.single-post .nexus-sidebar #toc-list a:hover,.single-post .nexus-sidebar #toc-list a:focus-visible{color:#a85218!important;font-weight:650!important;}',
            '.single-post .ao-sidebar-cta{display:block!important;width:230px!important;margin-top:.8rem!important;padding:1.05rem .95rem 1rem!important;opacity:1!important;transform:none!important;max-height:none!important;overflow:visible!important;border:1px solid rgba(217,121,44,.28)!important;pointer-events:auto!important;box-shadow:0 14px 34px rgba(17,20,22,.12)!important;transition:none!important;}',
            '.single-post .ao-sidebar-cta__title{font-size:1.16rem!important;line-height:1.1!important;}',
            '.single-post .ao-sidebar-cta__text{font-size:.70rem!important;line-height:1.47!important;}',
            '.single-post .ao-sidebar-cta__button{width:100%!important;min-height:36px!important;font-size:.67rem!important;}',
            '}',

            '.single-post .nexus-article-content h2{max-width:20ch!important;font-size:clamp(2.02rem,2.72vw,2.88rem)!important;line-height:1.075!important;}',
            '.single-post .ao-section-label{position:relative!important;margin-top:clamp(4.25rem,5.8vw,5.6rem)!important;}',
            '.single-post .ao-section-label--feature::after{position:absolute;right:.1rem;top:.25rem;z-index:-1;color:rgba(23,25,27,.038);font-family:ui-serif,Georgia,serif;font-size:clamp(4.6rem,6.5vw,6.3rem);font-weight:500;line-height:.75;letter-spacing:-.06em;content:attr(data-ao-index);pointer-events:none;}',

            '@media (min-width:1100px){',
            '.single-post .agency-delivery-flow,.single-post .agency-control-model,.single-post .agency-risk-matrix{width:calc(100% + 48px)!important;max-width:808px!important;margin-right:-48px!important;}',
            '}',

            '.single-post .agency-outsourcing-note{display:grid!important;grid-template-columns:32px minmax(0,1fr)!important;column-gap:.9rem!important;align-items:start!important;padding:1.35rem 1.45rem!important;border:0!important;border-top:1px solid #ded9d1!important;border-bottom:1px solid #ded9d1!important;border-left:2px solid #d9792c!important;border-radius:0 10px 10px 0!important;background:linear-gradient(100deg,rgba(217,121,44,.05),rgba(255,253,249,.55))!important;box-shadow:none!important;opacity:1!important;transform:none!important;}',
            '.single-post .agency-outsourcing-note::before{float:none!important;grid-column:1!important;grid-row:1 / span 2!important;display:grid!important;width:28px!important;height:28px!important;margin:.05rem 0 0!important;place-items:center!important;border:1px solid rgba(217,121,44,.22)!important;border-radius:50%!important;background:rgba(217,121,44,.07)!important;color:#ca6a24!important;font-size:1.42rem!important;line-height:1!important;}',
            '.single-post .agency-outsourcing-note p{grid-column:2!important;}',
            '.single-post .agency-outsourcing-note--quiet{background:linear-gradient(100deg,rgba(90,98,104,.05),rgba(255,253,249,.5))!important;border-left-color:#aeb4b8!important;}',

            '.single-post .agency-readiness-checklist{padding-top:1.25rem!important;border-top:1px solid #dcd9d2!important;opacity:1!important;transform:none!important;}',
            '.single-post .agency-readiness-checklist ol{display:grid!important;grid-template-columns:repeat(2,minmax(0,1fr))!important;column-gap:1.8rem!important;row-gap:0!important;}',
            '.single-post .agency-readiness-checklist li{position:relative!important;min-height:88px!important;margin:0!important;padding:1.12rem .2rem 1.18rem 3.3rem!important;border:0!important;border-top:1px solid #e1ded8!important;border-radius:0!important;background:transparent!important;box-shadow:none!important;}',
            '.single-post .agency-readiness-checklist li::before{position:absolute!important;top:1.02rem!important;left:0!important;width:38px!important;height:auto!important;border:0!important;border-radius:0!important;background:transparent!important;color:#c56823!important;font-family:ui-serif,Georgia,serif!important;font-size:1.28rem!important;font-weight:500!important;line-height:1!important;text-align:left!important;}',
            '.single-post .agency-readiness-checklist li:nth-last-child(-n+2){border-bottom:1px solid #e1ded8!important;}',

            /* Never hide article blocks while scrolling. */
            '.single-post .ao-reveal,.single-post .ao-reveal.is-visible,.single-post .agency-delivery-flow,.single-post .agency-control-model,.single-post .agency-risk-matrix{opacity:1!important;transform:none!important;transition:none!important;}',

            '@media (max-width:1099px){.single-post .ao-sidebar-cta{display:none!important;}}',
            '@media (max-width:700px){',
            '.single-post .agency-outsourcing-note{grid-template-columns:28px minmax(0,1fr)!important;padding:1.12rem 1rem!important;column-gap:.7rem!important;}',
            '.single-post .agency-readiness-checklist ol{grid-template-columns:1fr!important;}',
            '.single-post .agency-readiness-checklist li:nth-last-child(-n+2){border-bottom:0!important;}',
            '.single-post .agency-readiness-checklist li:last-child{border-bottom:1px solid #e1ded8!important;}',
            '}'
        ].join('');

        document.head.appendChild(style);
    }

    function enhanceSections(article) {
        var headings = Array.prototype.slice.call(article.querySelectorAll('h2'));
        var featureSections = {
            'delivery-kette': true,
            'kontrollverlust': true,
            'checkliste': true
        };

        headings.forEach(function (heading, index) {
            if (heading.dataset.aoEditorialReady === 'true') return;

            var marker = document.createElement('div');
            marker.className = 'ao-section-label';
            marker.setAttribute('aria-hidden', 'true');
            marker.setAttribute('data-ao-index', padIndex(index));

            if (featureSections[heading.id]) marker.classList.add('ao-section-label--feature');

            var number = document.createElement('span');
            number.className = 'ao-section-label__index';
            number.textContent = padIndex(index);

            var kicker = document.createElement('span');
            kicker.className = 'ao-section-label__kicker';
            kicker.textContent = sectionLabels[heading.id] || 'Abschnitt';

            marker.appendChild(number);
            marker.appendChild(kicker);
            heading.parentNode.insertBefore(marker, heading);
            heading.dataset.aoEditorialReady = 'true';
        });

        return headings;
    }

    function injectSidebarCta(sidebar) {
        if (!sidebar || sidebar.querySelector('.ao-sidebar-cta')) return;
        var toc = sidebar.querySelector('.sticky-toc');
        if (!toc) return;

        var card = document.createElement('aside');
        card.className = 'ao-sidebar-cta';
        card.setAttribute('aria-label', 'White-Label Zusammenarbeit');
        card.innerHTML = '' +
            '<span class="ao-sidebar-cta__eyebrow">Für Agenturen</span>' +
            '<h3 class="ao-sidebar-cta__title">Technische Kapazität, ohne neuen Steuerungsaufwand.</h3>' +
            '<p class="ao-sidebar-cta__text">WordPress, Tracking und QA im Hintergrund – mit sauberem Scope und nachvollziehbarer Übergabe.</p>' +
            '<a class="ao-sidebar-cta__button" href="/whitelabel-retainer/" data-track-action="cta_sidebar_whitelabel" data-track-category="lead_gen">White-Label ansehen →</a>';
        toc.insertAdjacentElement('afterend', card);
    }

    function enhanceToc(sidebar) {
        if (!sidebar) return;
        var list = sidebar.querySelector('#toc-list');
        if (!list) return;

        function markItems() {
            var index = 0;
            Array.prototype.slice.call(list.children).forEach(function (item) {
                if (item.tagName !== 'LI') return;
                if (item.classList.contains('is-subsection')) return;
                item.setAttribute('data-ao-index', padIndex(index));
                item.classList.toggle('is-current', !!item.querySelector('a.active, a[aria-current="location"]'));
                index += 1;
            });
        }

        markItems();
        var observer = new MutationObserver(markItems);
        observer.observe(list, { childList: true, subtree: true, attributes: true, attributeFilter: ['class', 'aria-current'] });
    }

    function setupShareRail(article, headings) {
        var rail = document.querySelector('.nexus-share-rail');
        if (!rail) return;

        var startMarker = headings && headings.length > 1 ? headings[1] : article.querySelector('h2');
        var endMarker = document.querySelector('.nexus-article-next') || document.querySelector('.nexus-rating') || document.querySelector('.nexus-author-bio');
        var ticking = false;

        document.body.classList.remove('ao-share-ready');

        function update() {
            var y = window.scrollY;
            var offset = Math.min(220, window.innerHeight * 0.28);
            var start = startMarker ? startMarker.getBoundingClientRect().top + window.scrollY - offset : Number.MAX_SAFE_INTEGER;
            var end = endMarker ? endMarker.getBoundingClientRect().top + window.scrollY - 260 : Number.MAX_SAFE_INTEGER;
            document.body.classList.toggle('ao-share-ready', y >= start && y < end);
            ticking = false;
        }

        function onScroll() {
            if (ticking) return;
            ticking = true;
            window.requestAnimationFrame(update);
        }

        window.addEventListener('scroll', onScroll, { passive: true });
        window.addEventListener('resize', onScroll);
        update();
    }

    function removeDuplicateHeroCategory() {
        var hero = document.querySelector('.nexus-article-hero--editorial');
        if (!hero) return;
        var keep = hero.querySelector('.nexus-meta-top .nexus-hero-category');
        if (!keep) return;
        var label = (keep.textContent || '').trim().replace(/\s+/g, ' ');
        if (!label) return;

        Array.prototype.slice.call(hero.querySelectorAll('a, span, p, div')).forEach(function (node) {
            if (node === keep || node.contains(keep) || keep.contains(node)) return;
            var text = (node.textContent || '').trim().replace(/\s+/g, ' ');
            if (text !== label) return;
            node.style.setProperty('display', 'none', 'important');
            node.setAttribute('aria-hidden', 'true');
        });
    }

    function init() {
        var article = document.querySelector('.agency-outsourcing-article');
        if (!article) return;

        injectStableStyles();
        removeDuplicateHeroCategory();

        var headings = enhanceSections(article);
        var sidebar = document.querySelector('.nexus-sidebar');

        if (sidebar) {
            injectSidebarCta(sidebar);
            enhanceToc(sidebar);
        }

        setupShareRail(article, headings);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();