/* Route-specific editorial refinements for /wordpress-projekte-auslagern/. */
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

    function injectRefinementStyles() {
        if (document.getElementById('ao-editorial-refinement-v2')) return;

        var style = document.createElement('style');
        style.id = 'ao-editorial-refinement-v2';
        style.textContent = [
            '.single-post .nexus-blog-header__context-links{display:none!important;}',
            '@media (min-width:1280px){',
            'body.single-post .nexus-share-rail{left:34px!important;opacity:0!important;visibility:hidden!important;pointer-events:none!important;transform:translateY(-50%) translateX(-10px)!important;transition:opacity .22s ease,transform .22s ease,visibility .22s ease!important;}',
            'body.single-post.ao-share-ready .nexus-share-rail{opacity:1!important;visibility:visible!important;pointer-events:auto!important;transform:translateY(-50%) translateX(0)!important;}',
            '}',
            '@media (min-width:1100px){',
            '.single-post .nexus-article-reader-header~.nexus-single-container .nexus-post-layout{grid-template-columns:minmax(0,820px) 244px!important;gap:clamp(3.1rem,4.5vw,4.7rem)!important;max-width:1135px!important;}',
            '.single-post .nexus-article-reader-header~.nexus-single-container .nexus-sidebar,.single-post .nexus-article-reader-header~.nexus-single-container .nexus-sidebar.nexus-reader-toc{top:116px!important;max-width:244px!important;justify-self:end!important;}',
            '.single-post .nexus-sidebar .sticky-toc,.single-post .nexus-reader-toc .sticky-toc{padding:1.05rem 1rem 1.08rem!important;border-color:#e0ddd7!important;border-radius:12px!important;background:rgba(255,253,249,.92)!important;box-shadow:0 10px 28px rgba(22,27,31,.04)!important;backdrop-filter:blur(8px)!important;}',
            '.single-post .nexus-sidebar .sticky-toc h2,.single-post .nexus-sidebar .sticky-toc .nexus-reader-toc__label{margin-bottom:.72rem!important;font-size:.68rem!important;letter-spacing:.08em!important;text-transform:uppercase!important;color:#5e6265!important;}',
            '.single-post .nexus-sidebar #toc-list::before{left:5px!important;background:#dddad4!important;}',
            '.single-post .nexus-sidebar #toc-list li{grid-template-columns:20px minmax(0,1fr)!important;gap:.38rem!important;padding:.24rem 0!important;}',
            '.single-post .nexus-sidebar #toc-list li::before{width:11px!important;height:11px!important;margin-top:.22rem!important;}',
            '.single-post .nexus-sidebar #toc-list a{display:block!important;min-width:0!important;white-space:normal!important;overflow:visible!important;text-overflow:clip!important;color:#666c71!important;font-size:.69rem!important;line-height:1.34!important;}',
            '.single-post .nexus-sidebar #toc-list a.active,.single-post .nexus-sidebar #toc-list a[aria-current="location"]{color:#a95318!important;font-weight:650!important;}',
            '.single-post .ao-sidebar-cta{margin-top:.75rem!important;padding:1.05rem 1rem 1rem!important;border-radius:12px!important;box-shadow:0 14px 36px rgba(17,20,22,.13)!important;}',
            '.single-post .ao-sidebar-cta__title{font-size:1.26rem!important;line-height:1.08!important;}',
            '.single-post .ao-sidebar-cta__text{font-size:.72rem!important;line-height:1.45!important;}',
            '.single-post .ao-sidebar-cta__button{width:100%!important;min-height:36px!important;font-size:.69rem!important;}',
            '}',
            '.single-post .agency-outsourcing-note{display:grid!important;grid-template-columns:34px minmax(0,1fr)!important;column-gap:.85rem!important;align-items:start!important;padding:1.3rem 1.45rem!important;border:1px solid #e2ded7!important;border-left:2px solid #d9792c!important;border-radius:11px!important;background:linear-gradient(135deg,#fffdf9 0%,#faf7f1 100%)!important;box-shadow:0 12px 30px rgba(27,31,34,.035)!important;}',
            '.single-post .agency-outsourcing-note::before{float:none!important;grid-column:1!important;grid-row:1 / span 2!important;margin:.05rem 0 0!important;width:30px!important;height:30px!important;display:grid!important;place-items:center!important;border-radius:50%!important;background:rgba(217,121,44,.10)!important;color:#d9792c!important;font-size:1.55rem!important;line-height:1!important;}',
            '.single-post .agency-outsourcing-note p{grid-column:2!important;}',
            '.single-post .agency-outsourcing-note p:first-of-type{color:#202428!important;font-size:1.02rem!important;line-height:1.5!important;}',
            '.single-post .agency-outsourcing-note p+p{margin-top:.35rem!important;color:#62686d!important;font-size:.94rem!important;line-height:1.55!important;}',
            '.single-post .agency-outsourcing-note--quiet{background:#f2f0eb!important;border-left-color:#aeb4b8!important;}',
            '.single-post .agency-readiness-checklist{padding-top:1.35rem!important;}',
            '.single-post .agency-readiness-checklist ol{gap:.8rem!important;}',
            '.single-post .agency-readiness-checklist li{min-height:82px!important;padding:1.08rem 1.05rem 1.08rem 3.65rem!important;border:1px solid #e1ded7!important;border-radius:12px!important;background:linear-gradient(135deg,#fffdf9 0%,#fbf9f5 100%)!important;box-shadow:0 8px 22px rgba(28,32,35,.025)!important;}',
            '.single-post .agency-readiness-checklist li::before{top:1.18rem!important;left:1.15rem!important;width:28px!important;height:28px!important;display:grid!important;place-items:center!important;border:1px solid rgba(217,121,44,.25)!important;border-radius:50%!important;background:rgba(217,121,44,.07)!important;color:#bd6422!important;font-size:.67rem!important;}',
            '.single-post .nexus-article-content h2{max-width:19ch!important;font-size:clamp(2.05rem,2.85vw,3rem)!important;line-height:1.07!important;}',
            '.single-post .ao-section-label{margin-top:clamp(4.25rem,6vw,5.7rem)!important;}',
            '@media (max-width:700px){',
            '.single-post .agency-outsourcing-note{grid-template-columns:28px minmax(0,1fr)!important;padding:1.1rem!important;column-gap:.7rem!important;}',
            '.single-post .agency-outsourcing-note::before{width:26px!important;height:26px!important;font-size:1.35rem!important;}',
            '.single-post .agency-readiness-checklist li{padding-left:3.35rem!important;}',
            '}',
            '@media (prefers-reduced-motion:reduce){body.single-post .nexus-share-rail{transition:none!important;}}'
        ].join('');
        document.head.appendChild(style);
    }

    function enhanceSections(article) {
        var headings = Array.prototype.slice.call(article.querySelectorAll('h2'));

        headings.forEach(function (heading, index) {
            if (heading.dataset.aoEditorialReady === 'true') return;

            var marker = document.createElement('div');
            marker.className = 'ao-section-label';
            marker.setAttribute('aria-hidden', 'true');

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
            var topLevelIndex = 0;
            Array.prototype.slice.call(list.children).forEach(function (item) {
                if (item.tagName !== 'LI') return;

                if (item.classList.contains('is-subsection')) {
                    item.removeAttribute('data-ao-index');
                    return;
                }

                item.setAttribute('data-ao-index', padIndex(topLevelIndex));
                topLevelIndex += 1;
            });
        }

        markItems();

        var observer = new MutationObserver(markItems);
        observer.observe(list, {
            childList: true,
            subtree: true,
            attributes: true,
            attributeFilter: ['class']
        });
    }

    function setupShareRail(article, headings) {
        var rail = document.querySelector('.nexus-share-rail');
        if (!rail) return;

        var startMarker = headings && headings.length > 1 ? headings[1] : article.querySelector('h2');
        var endMarker = document.querySelector('.nexus-article-next') ||
                        document.querySelector('.nexus-rating') ||
                        document.querySelector('.nexus-author-bio');
        var ticking = false;

        document.body.classList.remove('ao-share-ready');

        function update() {
            if (!startMarker) {
                document.body.classList.remove('ao-share-ready');
                ticking = false;
                return;
            }

            var start = startMarker.getBoundingClientRect().top + window.scrollY - Math.min(220, window.innerHeight * 0.28);
            var end = endMarker ? endMarker.getBoundingClientRect().top + window.scrollY - 260 : Number.MAX_SAFE_INTEGER;
            var y = window.scrollY;
            var visible = y >= start && y < end;

            document.body.classList.toggle('ao-share-ready', visible);
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

    function removeDuplicateCategoryContext() {
        var contextLinks = document.querySelector('.nexus-blog-header__context-links');
        if (contextLinks) {
            contextLinks.hidden = true;
            contextLinks.setAttribute('aria-hidden', 'true');
        }
    }

    function removeDuplicateHeroCategory() {
        var container = document.querySelector('.nexus-single-container');
        var hero = document.querySelector('.nexus-article-hero--editorial');
        if (!container || !hero) return;

        var keep = hero.querySelector('.nexus-meta-top .nexus-hero-category') ||
                   hero.querySelector('.nexus-meta-top a') ||
                   hero.querySelector('.nexus-hero-category');
        if (!keep) return;

        var label = (keep.textContent || '').trim().replace(/\s+/g, ' ');
        if (!label) return;

        Array.prototype.slice.call(container.querySelectorAll('a, span, p, div')).forEach(function (node) {
            if (node === keep || node.contains(keep) || keep.contains(node)) return;
            if (node.closest('.nexus-sidebar') || node.closest('.agency-outsourcing-article')) return;

            var text = (node.textContent || '').trim().replace(/\s+/g, ' ');
            if (text !== label) return;

            node.style.setProperty('display', 'none', 'important');
            node.setAttribute('aria-hidden', 'true');
        });
    }

    function init() {
        var article = document.querySelector('.agency-outsourcing-article');
        if (!article) return;

        injectRefinementStyles();
        removeDuplicateCategoryContext();
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