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

    var featureSections = {
        'delivery-kette': true,
        'kontrollverlust': true,
        'checkliste': true
    };

    function padIndex(index) {
        return String(index + 1).padStart(2, '0');
    }

    function injectRefinementStyles() {
        var oldStyle = document.getElementById('ao-editorial-refinement-v2');
        if (oldStyle) oldStyle.remove();
        if (document.getElementById('ao-editorial-refinement-v3')) return;

        var style = document.createElement('style');
        style.id = 'ao-editorial-refinement-v3';
        style.textContent = [
            /* This route already carries the dossier inside the hero meta. */
            '.single-post .nexus-blog-header__context-links{display:none!important;}',
            '.single-post .nexus-article-hero--editorial::before{display:none!important;content:none!important;}',

            /* Hero: a little calmer, artwork slightly stronger. */
            '@media (min-width:1100px){',
            '.single-post .nexus-article-reader-header~.nexus-single-container{grid-template-columns:minmax(0,.91fr) minmax(480px,1.09fr)!important;column-gap:clamp(4.75rem,6.4vw,7rem)!important;}',
            '.single-post .nexus-article-reader-header~.nexus-single-container .nexus-title{max-width:15.5ch!important;font-size:clamp(3rem,4.35vw,4.75rem)!important;line-height:1.005!important;letter-spacing:-.043em!important;}',
            '.single-post .nexus-article-reader-header~.nexus-single-container .nexus-article-cover{width:min(100%,760px)!important;max-width:760px!important;border-color:rgba(217,121,44,.43)!important;box-shadow:0 36px 92px rgba(0,0,0,.42),0 0 0 1px rgba(255,255,255,.028) inset!important;}',
            '.single-post .nexus-article-reader-header~.nexus-single-container .nexus-article-cover::after{border-color:rgba(217,121,44,.09)!important;}',
            '.single-post .nexus-article-reader-header~.nexus-single-container .nexus-article-cover::before{background:rgba(217,121,44,.13)!important;filter:blur(68px)!important;}',
            '}',

            /* Share rail appears only from the second main section onward. */
            '@media (min-width:1280px){',
            'body.single-post .nexus-share-rail{left:30px!important;opacity:0!important;visibility:hidden!important;pointer-events:none!important;transform:translateY(-50%) translateX(-8px)!important;transition:opacity .22s ease,transform .22s ease,visibility .22s ease!important;}',
            'body.single-post.ao-share-ready .nexus-share-rail{opacity:1!important;visibility:visible!important;pointer-events:auto!important;transform:translateY(-50%) translateX(0)!important;}',
            '}',

            /* A one-pixel editorial seam between dark hero and paper surface. */
            '.single-post .nexus-article-reader-header~.nexus-single-container .nexus-post-layout{position:relative!important;}',
            '.single-post .nexus-article-reader-header~.nexus-single-container .nexus-post-layout::before{position:absolute;top:0;left:50%;width:100vw;height:1px;transform:translateX(-50%);background:linear-gradient(90deg,transparent 0%,rgba(217,121,44,.34) 28%,rgba(217,121,44,.16) 60%,transparent 100%);content:"";}',

            /* Reading measure + reserved sidebar column. */
            '@media (min-width:1100px){',
            '.single-post .nexus-article-reader-header~.nexus-single-container .nexus-post-layout{grid-template-columns:minmax(0,760px) 260px!important;gap:clamp(3.7rem,5vw,4.6rem)!important;max-width:1090px!important;}',
            '.single-post .nexus-article-reader-header~.nexus-single-container .nexus-article-content{max-width:760px!important;}',
            '.single-post .nexus-article-reader-header~.nexus-single-container .nexus-sidebar,.single-post .nexus-article-reader-header~.nexus-single-container .nexus-sidebar.nexus-reader-toc{top:112px!important;width:260px!important;max-width:260px!important;justify-self:end!important;}',

            /* Collapsed index: numbers first. Hover/focus expands to full titles without shifting the article. */
            '.single-post .nexus-sidebar .sticky-toc,.single-post .nexus-reader-toc .sticky-toc{width:74px!important;margin-left:auto!important;padding:.9rem .72rem .95rem!important;overflow:hidden!important;border:1px solid #dfdcd5!important;border-radius:14px!important;background:rgba(255,253,249,.94)!important;box-shadow:0 10px 30px rgba(21,26,30,.045)!important;backdrop-filter:blur(10px)!important;transition:width .2s ease,padding .2s ease,box-shadow .2s ease!important;}',
            '.single-post .nexus-sidebar .sticky-toc:hover,.single-post .nexus-sidebar .sticky-toc:focus-within{width:260px!important;padding:1rem 1rem 1.05rem!important;box-shadow:0 18px 46px rgba(21,26,30,.09)!important;}',
            '.single-post .nexus-sidebar .nexus-reader-toc__toggle{display:none!important;}',
            '.single-post .nexus-sidebar .sticky-toc h2,.single-post .nexus-sidebar .sticky-toc .nexus-reader-toc__label{display:block!important;width:48px!important;margin:0 auto .7rem!important;color:#6c7175!important;font-size:.59rem!important;font-weight:760!important;letter-spacing:.12em!important;text-align:center!important;text-transform:uppercase!important;white-space:nowrap!important;transition:width .2s ease,text-align .2s ease!important;}',
            '.single-post .nexus-sidebar .sticky-toc:hover h2,.single-post .nexus-sidebar .sticky-toc:focus-within h2,.single-post .nexus-sidebar .sticky-toc:hover .nexus-reader-toc__label,.single-post .nexus-sidebar .sticky-toc:focus-within .nexus-reader-toc__label{width:100%!important;margin-left:0!important;text-align:left!important;}',
            '.single-post .nexus-sidebar #toc-list{display:block!important;margin:0!important;padding:0!important;list-style:none!important;}',
            '.single-post .nexus-sidebar #toc-list::before{display:none!important;}',
            '.single-post .nexus-sidebar #toc-list li{display:grid!important;grid-template-columns:28px minmax(0,1fr)!important;gap:.55rem!important;align-items:start!important;margin:0!important;padding:.24rem 0!important;}',
            '.single-post .nexus-sidebar #toc-list li.is-subsection{display:none!important;}',
            '.single-post .nexus-sidebar #toc-list li::before{display:block!important;width:auto!important;height:auto!important;margin:0!important;border:0!important;border-radius:0!important;background:transparent!important;box-shadow:none!important;color:#9a9da0!important;font-family:var(--font-mono,ui-monospace,monospace)!important;font-size:.62rem!important;font-weight:700!important;letter-spacing:.05em!important;line-height:1.45!important;content:attr(data-ao-index)!important;transition:color .16s ease!important;}',
            '.single-post .nexus-sidebar #toc-list li.is-current::before{color:#c66722!important;}',
            '.single-post .nexus-sidebar #toc-list a{display:block!important;min-width:0!important;max-width:0!important;overflow:hidden!important;opacity:0!important;transform:translateX(-4px)!important;color:#656b70!important;font-size:.70rem!important;line-height:1.38!important;text-decoration:none!important;white-space:normal!important;transition:opacity .16s ease,transform .16s ease,color .16s ease,max-width .2s ease!important;}',
            '.single-post .nexus-sidebar .sticky-toc:hover #toc-list a,.single-post .nexus-sidebar .sticky-toc:focus-within #toc-list a{max-width:205px!important;opacity:1!important;transform:translateX(0)!important;}',
            '.single-post .nexus-sidebar #toc-list a.active,.single-post .nexus-sidebar #toc-list a[aria-current="location"],.single-post .nexus-sidebar #toc-list a:hover,.single-post .nexus-sidebar #toc-list a:focus-visible{color:#a85218!important;font-weight:650!important;}',

            /* Sidebar CTA waits until the reader has reached the third main section. */
            '.single-post .ao-sidebar-cta{width:260px!important;max-height:0!important;margin:0!important;padding:0 1rem!important;overflow:hidden!important;opacity:0!important;transform:translateY(8px)!important;border-width:0!important;box-shadow:none!important;pointer-events:none!important;transition:max-height .25s ease,margin .25s ease,padding .25s ease,opacity .2s ease,transform .2s ease,border-width .2s ease!important;}',
            'body.single-post.ao-cta-ready .ao-sidebar-cta{max-height:330px!important;margin-top:.85rem!important;padding:1.08rem 1rem 1rem!important;opacity:1!important;transform:translateY(0)!important;border-width:1px!important;pointer-events:auto!important;box-shadow:0 16px 38px rgba(17,20,22,.13)!important;}',
            '.single-post .ao-sidebar-cta__title{font-size:1.22rem!important;line-height:1.1!important;}',
            '.single-post .ao-sidebar-cta__text{font-size:.71rem!important;line-height:1.48!important;}',
            '.single-post .ao-sidebar-cta__button{width:100%!important;min-height:36px!important;font-size:.68rem!important;}',
            '}',

            /* Editorial rhythm: text stays quiet, selected chapters get one large background index. */
            '.single-post .nexus-article-content h2{max-width:20ch!important;font-size:clamp(2.02rem,2.72vw,2.88rem)!important;line-height:1.075!important;}',
            '.single-post .ao-section-label{position:relative!important;margin-top:clamp(4.4rem,6vw,5.9rem)!important;}',
            '.single-post .ao-section-label--feature{isolation:isolate;}',
            '.single-post .ao-section-label--feature::after{position:absolute;right:.1rem;top:.25rem;z-index:-1;color:rgba(23,25,27,.045);font-family:ui-serif,Georgia,serif;font-size:clamp(4.8rem,7vw,6.8rem);font-weight:500;line-height:.75;letter-spacing:-.06em;content:attr(data-ao-index);pointer-events:none;}',

            /* Key visuals may breathe beyond the text measure. */
            '@media (min-width:1100px){',
            '.single-post .agency-delivery-flow,.single-post .agency-control-model,.single-post .agency-risk-matrix{width:calc(100% + 64px)!important;max-width:824px!important;margin-right:-64px!important;}',
            '}',

            /* Callouts: more editorial, less generic card. */
            '.single-post .agency-outsourcing-note{display:grid!important;grid-template-columns:32px minmax(0,1fr)!important;column-gap:.9rem!important;align-items:start!important;padding:1.35rem 1.45rem!important;border:0!important;border-top:1px solid #ded9d1!important;border-bottom:1px solid #ded9d1!important;border-left:2px solid #d9792c!important;border-radius:0 10px 10px 0!important;background:linear-gradient(100deg,rgba(217,121,44,.055) 0%,rgba(255,253,249,.62) 46%,rgba(255,253,249,.16) 100%)!important;box-shadow:none!important;}',
            '.single-post .agency-outsourcing-note::before{float:none!important;grid-column:1!important;grid-row:1 / span 2!important;display:grid!important;width:28px!important;height:28px!important;margin:.05rem 0 0!important;place-items:center!important;border:1px solid rgba(217,121,44,.22)!important;border-radius:50%!important;background:rgba(217,121,44,.07)!important;color:#ca6a24!important;font-size:1.42rem!important;line-height:1!important;}',
            '.single-post .agency-outsourcing-note p{grid-column:2!important;}',
            '.single-post .agency-outsourcing-note p:first-of-type{color:#202428!important;font-size:1.02rem!important;line-height:1.5!important;}',
            '.single-post .agency-outsourcing-note p+p{margin-top:.3rem!important;color:#646a6e!important;font-size:.94rem!important;line-height:1.56!important;}',
            '.single-post .agency-outsourcing-note--quiet{background:linear-gradient(100deg,rgba(90,98,104,.055),rgba(255,253,249,.5))!important;border-left-color:#aeb4b8!important;}',

            /* Checklist becomes an editorial index rather than a card grid. */
            '.single-post .agency-readiness-checklist{padding-top:1.25rem!important;border-top:1px solid #dcd9d2!important;}',
            '.single-post .agency-readiness-checklist ol{display:grid!important;grid-template-columns:repeat(2,minmax(0,1fr))!important;column-gap:1.8rem!important;row-gap:0!important;}',
            '.single-post .agency-readiness-checklist li{position:relative!important;min-height:88px!important;margin:0!important;padding:1.12rem .2rem 1.18rem 3.3rem!important;border:0!important;border-top:1px solid #e1ded8!important;border-radius:0!important;background:transparent!important;box-shadow:none!important;}',
            '.single-post .agency-readiness-checklist li::before{position:absolute!important;top:1.02rem!important;left:0!important;display:block!important;width:38px!important;height:auto!important;border:0!important;border-radius:0!important;background:transparent!important;color:#c56823!important;font-family:ui-serif,Georgia,serif!important;font-size:1.28rem!important;font-weight:500!important;line-height:1!important;letter-spacing:-.03em!important;text-align:left!important;}',
            '.single-post .agency-readiness-checklist li:nth-last-child(-n+2){border-bottom:1px solid #e1ded8!important;}',

            /* Subtle reveal on diagrams and editorial callouts. */
            '.single-post .ao-reveal{opacity:.001;transform:translateY(8px);transition:opacity .42s ease,transform .42s ease;}',
            '.single-post .ao-reveal.is-visible{opacity:1;transform:translateY(0);}',

            '@media (max-width:1099px){',
            '.single-post .ao-sidebar-cta{display:none!important;}',
            '}',
            '@media (max-width:700px){',
            '.single-post .agency-outsourcing-note{grid-template-columns:28px minmax(0,1fr)!important;padding:1.12rem 1rem!important;column-gap:.7rem!important;}',
            '.single-post .agency-outsourcing-note::before{width:25px!important;height:25px!important;font-size:1.25rem!important;}',
            '.single-post .agency-readiness-checklist ol{grid-template-columns:1fr!important;}',
            '.single-post .agency-readiness-checklist li:nth-last-child(-n+2){border-bottom:0!important;}',
            '.single-post .agency-readiness-checklist li:last-child{border-bottom:1px solid #e1ded8!important;}',
            '.single-post .ao-section-label--feature::after{font-size:4.8rem!important;}',
            '}',
            '@media (prefers-reduced-motion:reduce){',
            'body.single-post .nexus-share-rail,.single-post .nexus-sidebar .sticky-toc,.single-post .nexus-sidebar #toc-list a,.single-post .ao-sidebar-cta,.single-post .ao-reveal{transition:none!important;}',
            '.single-post .ao-reveal{opacity:1!important;transform:none!important;}',
            '}'
        ].join('');
        document.head.appendChild(style);
    }

    function enhanceSections(article) {
        var headings = Array.prototype.slice.call(article.querySelectorAll('h2'));

        headings.forEach(function (heading, index) {
            if (heading.dataset.aoEditorialReady === 'true') return;

            var marker = document.createElement('div');
            marker.className = 'ao-section-label';
            marker.dataset.aoIndex = padIndex(index);
            marker.setAttribute('aria-hidden', 'true');

            if (featureSections[heading.id]) {
                marker.classList.add('ao-section-label--feature');
            }

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
                    item.classList.remove('is-current');
                    return;
                }

                item.setAttribute('data-ao-index', padIndex(topLevelIndex));
                item.classList.toggle('is-current', !!item.querySelector('a.active, a[aria-current="location"]'));
                topLevelIndex += 1;
            });
        }

        markItems();

        var observer = new MutationObserver(markItems);
        observer.observe(list, {
            childList: true,
            subtree: true,
            attributes: true,
            attributeFilter: ['class', 'aria-current']
        });
    }

    function setupScrollStates(article, headings) {
        var rail = document.querySelector('.nexus-share-rail');
        var shareStart = headings && headings.length > 1 ? headings[1] : article.querySelector('h2');
        var ctaStart = headings && headings.length > 2 ? headings[2] : shareStart;
        var endMarker = document.querySelector('.nexus-article-next') ||
                        document.querySelector('.nexus-rating') ||
                        document.querySelector('.nexus-author-bio');
        var ticking = false;

        document.body.classList.remove('ao-share-ready', 'ao-cta-ready');

        function threshold(marker, offset) {
            if (!marker) return Number.MAX_SAFE_INTEGER;
            return marker.getBoundingClientRect().top + window.scrollY - offset;
        }

        function update() {
            var y = window.scrollY;
            var viewportOffset = Math.min(220, window.innerHeight * 0.28);
            var end = endMarker ? endMarker.getBoundingClientRect().top + window.scrollY - 260 : Number.MAX_SAFE_INTEGER;

            if (rail) {
                document.body.classList.toggle('ao-share-ready', y >= threshold(shareStart, viewportOffset) && y < end);
            }

            document.body.classList.toggle('ao-cta-ready', y >= threshold(ctaStart, viewportOffset) && y < end);
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

    function setupReveals(article) {
        var items = Array.prototype.slice.call(article.querySelectorAll(
            '.agency-delivery-flow, .agency-control-model, .agency-risk-matrix, .agency-readiness-checklist, .agency-outsourcing-note'
        ));

        if (!items.length) return;

        if (!('IntersectionObserver' in window) || (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches)) {
            items.forEach(function (item) { item.classList.add('is-visible'); });
            return;
        }

        items.forEach(function (item) { item.classList.add('ao-reveal'); });

        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) return;
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            });
        }, {
            threshold: 0.12,
            rootMargin: '0px 0px -8% 0px'
        });

        items.forEach(function (item) { observer.observe(item); });
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

        setupScrollStates(article, headings);
        setupReveals(article);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();