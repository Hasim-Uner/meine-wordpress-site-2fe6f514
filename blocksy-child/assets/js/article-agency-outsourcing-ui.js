/* Route-specific editorial refinements for /wordpress-projekte-auslagern/.
 * Behaviour stays deliberately small: section markers, TOC state and share rail.
 */
(function () {
    'use strict';

    if (typeof document === 'undefined') return;

    var sectionLabels = {
        'wann-auslagern': 'Einordnung',
        'delivery-kette': 'Prozess',
        'kontrollverlust': 'Steuerung',
        'freelancer-auswaehlen': 'Auswahl',
        'warnsignale': 'Vor dem Start',
        'kosten': 'Wirtschaftlichkeit',
        'klein-starten': 'Einstieg',
        'checkliste': 'Checkliste',
        'zusammenarbeit': 'Zusammenarbeit',
        'faq': 'FAQ'
    };

    function padIndex(index) {
        return String(index + 1).padStart(2, '0');
    }

    function injectStyles() {
        ['ao-editorial-refinement-v2', 'ao-editorial-refinement-v3', 'ao-editorial-refinement-v4', 'ao-editorial-refinement-v5'].forEach(function (id) {
            var oldStyle = document.getElementById(id);
            if (oldStyle) oldStyle.remove();
        });

        if (document.getElementById('ao-editorial-refinement-v6')) return;

        var style = document.createElement('style');
        style.id = 'ao-editorial-refinement-v6';
        style.textContent = [
            '.single-post .nexus-blog-header__context-links{display:none!important;}',
            '.single-post .nexus-article-hero--editorial::before{display:none!important;content:none!important;}',

            /* Hero: keep the strong split, but reduce visual competition. */
            '@media (min-width:1100px){',
            '.single-post .nexus-article-reader-header~.nexus-single-container{grid-template-columns:minmax(0,.94fr) minmax(470px,1.06fr)!important;column-gap:clamp(4.2rem,5.8vw,6.4rem)!important;}',
            '.single-post .nexus-article-reader-header~.nexus-single-container .nexus-title{max-width:15.8ch!important;font-size:clamp(3rem,4.25vw,4.65rem)!important;line-height:1.01!important;letter-spacing:-.041em!important;}',
            '.single-post .nexus-article-reader-header~.nexus-single-container .nexus-article-cover{width:min(100%,745px)!important;max-width:745px!important;}',
            '}',

            /* Share rail: one route-owned state, positioned beside the reading column. */
            '@media (min-width:1180px){',
            'body.single-post .nexus-share-rail{display:flex!important;left:max(22px,calc((100vw - 1280px)/2 - 62px))!important;opacity:0!important;visibility:hidden!important;pointer-events:none!important;transform:translateY(-50%) translateX(-6px)!important;transition:opacity .18s ease,transform .18s ease,visibility .18s ease!important;}',
            'body.single-post .nexus-share-rail.ao-route-visible{opacity:1!important;visibility:visible!important;pointer-events:auto!important;transform:translateY(-50%) translateX(0)!important;}',
            '}',
            '@media (max-width:1179px){body.single-post .nexus-share-rail{display:none!important;}}',

            /* Reading measure and deliberately quiet sidebar. */
            '@media (min-width:1100px){',
            '.single-post .nexus-article-reader-header~.nexus-single-container .nexus-post-layout{grid-template-columns:minmax(0,780px) 205px!important;gap:clamp(3.3rem,4.4vw,4rem)!important;max-width:1040px!important;align-items:start!important;}',
            '.single-post .nexus-article-reader-header~.nexus-single-container .nexus-article-content{max-width:780px!important;}',
            '.single-post .nexus-article-reader-header~.nexus-single-container .nexus-sidebar,.single-post .nexus-article-reader-header~.nexus-single-container .nexus-sidebar.nexus-reader-toc{position:sticky!important;top:112px!important;width:205px!important;max-width:205px!important;align-self:start!important;justify-self:end!important;transform:none!important;}',
            '.single-post .nexus-sidebar .sticky-toc,.single-post .nexus-reader-toc .sticky-toc{width:205px!important;max-height:calc(100vh - 145px)!important;margin:0!important;padding:.3rem 0 .4rem 1rem!important;overflow:auto!important;border:0!important;border-left:1px solid #d9d6cf!important;border-radius:0!important;background:transparent!important;box-shadow:none!important;backdrop-filter:none!important;transition:none!important;}',
            '.single-post .nexus-sidebar .nexus-reader-toc__toggle{display:none!important;}',
            '.single-post .nexus-sidebar .sticky-toc h2,.single-post .nexus-sidebar .sticky-toc .nexus-reader-toc__label{display:block!important;margin:0 0 .85rem!important;color:#777c7f!important;font-size:.58rem!important;font-weight:760!important;letter-spacing:.13em!important;text-transform:uppercase!important;}',
            '.single-post .nexus-sidebar #toc-list{display:block!important;margin:0!important;padding:0!important;list-style:none!important;}',
            '.single-post .nexus-sidebar #toc-list::before{display:none!important;}',
            '.single-post .nexus-sidebar #toc-list li{display:grid!important;grid-template-columns:24px minmax(0,1fr)!important;gap:.42rem!important;align-items:start!important;margin:0!important;padding:.28rem 0!important;}',
            '.single-post .nexus-sidebar #toc-list li.is-subsection{display:none!important;}',
            '.single-post .nexus-sidebar #toc-list li::before{display:block!important;width:auto!important;height:auto!important;margin:0!important;border:0!important;border-radius:0!important;background:transparent!important;box-shadow:none!important;color:#a4a6a7!important;font-family:var(--font-mono,ui-monospace,monospace)!important;font-size:.58rem!important;font-weight:700!important;line-height:1.45!important;content:attr(data-ao-index)!important;}',
            '.single-post .nexus-sidebar #toc-list li.is-current::before{color:#c76a27!important;}',
            '.single-post .nexus-sidebar #toc-list a{display:block!important;min-width:0!important;color:#71767a!important;font-size:.66rem!important;line-height:1.34!important;text-decoration:none!important;white-space:normal!important;overflow:visible!important;opacity:1!important;transform:none!important;}',
            '.single-post .nexus-sidebar #toc-list a.active,.single-post .nexus-sidebar #toc-list a[aria-current="location"]{color:#242729!important;font-weight:700!important;}',
            '.single-post .ao-sidebar-cta{display:none!important;}',
            '}',

            /* Editorial hierarchy: fewer decorative effects, clearer rhythm. */
            '.single-post .nexus-article-content{color:#4f5559!important;}',
            '.single-post .nexus-article-content>p,.single-post .nexus-article-content>ul,.single-post .nexus-article-content>ol{font-size:1.01rem!important;line-height:1.72!important;}',
            '.single-post .nexus-article-content h2{max-width:21ch!important;font-size:clamp(2.08rem,2.65vw,2.82rem)!important;line-height:1.08!important;letter-spacing:-.03em!important;}',
            '.single-post .nexus-article-content h3{margin-top:2.6rem!important;margin-bottom:.72rem!important;color:#202326!important;font-size:clamp(1.2rem,1.45vw,1.38rem)!important;line-height:1.28!important;letter-spacing:-.018em!important;}',
            '.single-post .ao-section-label{position:relative!important;margin-top:clamp(4.5rem,6vw,5.8rem)!important;padding-top:1.1rem!important;border-top:1px solid #dedbd4!important;}',
            '.single-post .ao-section-label__index{color:#25292c!important;}',
            '.single-post .ao-section-label__kicker{color:#7a7f82!important;}',
            '.single-post .ao-section-label--feature::after{display:none!important;content:none!important;}',

            /* Key process modules: flat editorial grids instead of dashboard cards. */
            '.single-post .agency-delivery-flow,.single-post .agency-control-model,.single-post .agency-risk-matrix{width:100%!important;max-width:none!important;margin:2.2rem 0 2.8rem!important;box-shadow:none!important;}',
            '.single-post .agency-delivery-flow{padding:0!important;border:0!important;background:transparent!important;}',
            '.single-post .agency-delivery-flow figcaption,.single-post .agency-control-model figcaption{margin-bottom:1rem!important;color:#666c70!important;font-size:.82rem!important;line-height:1.5!important;}',
            '.single-post .agency-delivery-flow__track{display:grid!important;grid-template-columns:repeat(3,minmax(0,1fr))!important;gap:0!important;margin:0!important;padding:0!important;border-top:1px solid #dcd8d1!important;border-left:1px solid #dcd8d1!important;list-style:none!important;}',
            '.single-post .agency-delivery-flow__track li{min-height:150px!important;margin:0!important;padding:1.15rem 1.1rem!important;border:0!important;border-right:1px solid #dcd8d1!important;border-bottom:1px solid #dcd8d1!important;border-radius:0!important;background:rgba(255,253,249,.34)!important;box-shadow:none!important;}',
            '.single-post .agency-delivery-flow__track li span{color:#c66a29!important;font-size:.63rem!important;}',
            '.single-post .agency-delivery-flow__track li strong{margin-top:1.05rem!important;color:#222629!important;font-size:.97rem!important;}',
            '.single-post .agency-delivery-flow__track li small{margin-top:.5rem!important;color:#6a7074!important;font-size:.77rem!important;line-height:1.5!important;}',

            '.single-post .agency-control-model{padding:0!important;border:1px solid #ddd9d2!important;border-radius:10px!important;background:#fffdf9!important;overflow:hidden!important;}',
            '.single-post .agency-control-model figcaption{margin:0!important;padding:1rem 1.15rem!important;border-bottom:1px solid #e1ddd6!important;}',
            '.single-post .agency-control-model__grid{gap:0!important;}',
            '.single-post .agency-control-model__grid section{padding:1.55rem 1.25rem!important;border:0!important;border-right:1px solid #e1ddd6!important;border-radius:0!important;background:transparent!important;box-shadow:none!important;}',
            '.single-post .agency-control-model__grid section:last-of-type{border-right:0!important;}',
            '.single-post .agency-control-model__bridge{background:#f1eee8!important;color:#92979a!important;}',

            '.single-post .agency-risk-matrix{border:1px solid #ddd9d2!important;border-radius:10px!important;overflow:auto!important;background:#fffdf9!important;}',
            '.single-post .agency-risk-matrix table{margin:0!important;border:0!important;box-shadow:none!important;}',
            '.single-post .agency-risk-matrix th{background:#f1eee8!important;color:#25292c!important;font-size:.75rem!important;}',
            '.single-post .agency-risk-matrix td{color:#5f6569!important;font-size:.86rem!important;line-height:1.5!important;}',

            /* Notes: editorial emphasis, not another card. */
            '.single-post .agency-outsourcing-note{display:grid!important;grid-template-columns:30px minmax(0,1fr)!important;column-gap:.85rem!important;align-items:start!important;padding:1.25rem 1.15rem!important;border:0!important;border-left:2px solid #d9792c!important;border-radius:0!important;background:linear-gradient(90deg,rgba(217,121,44,.055),rgba(255,253,249,0))!important;box-shadow:none!important;opacity:1!important;transform:none!important;}',
            '.single-post .agency-outsourcing-note::before{width:26px!important;height:26px!important;margin:.05rem 0 0!important;border:1px solid rgba(217,121,44,.22)!important;border-radius:50%!important;background:rgba(217,121,44,.06)!important;color:#c76a27!important;font-size:1.22rem!important;}',
            '.single-post .agency-outsourcing-note p{grid-column:2!important;}',
            '.single-post .agency-outsourcing-note--quiet{border-left-color:#b3b7b9!important;background:linear-gradient(90deg,rgba(95,102,107,.045),rgba(255,253,249,0))!important;}',

            /* Checklist stays editorial and airy. */
            '.single-post .agency-readiness-checklist{padding-top:1rem!important;border-top:1px solid #dcd9d2!important;}',
            '.single-post .agency-readiness-checklist ol{display:grid!important;grid-template-columns:repeat(2,minmax(0,1fr))!important;column-gap:1.8rem!important;row-gap:0!important;}',
            '.single-post .agency-readiness-checklist li{position:relative!important;min-height:82px!important;margin:0!important;padding:1rem .2rem 1.05rem 3.1rem!important;border:0!important;border-top:1px solid #e1ded8!important;border-radius:0!important;background:transparent!important;box-shadow:none!important;}',
            '.single-post .agency-readiness-checklist li::before{position:absolute!important;top:.98rem!important;left:0!important;width:36px!important;height:auto!important;border:0!important;border-radius:0!important;background:transparent!important;color:#c56823!important;font-family:ui-serif,Georgia,serif!important;font-size:1.2rem!important;font-weight:500!important;line-height:1!important;text-align:left!important;}',

            /* One commercial moment only: compact horizontal CTA near the end. */
            '.single-post .agency-outsourcing-cta{display:grid!important;grid-template-columns:minmax(0,1fr) auto!important;gap:1.3rem 2rem!important;align-items:center!important;margin:2.1rem 0 0!important;padding:1.35rem 1.45rem!important;border:1px solid rgba(217,121,44,.34)!important;border-radius:12px!important;background:linear-gradient(105deg,#111416 0%,#171515 68%,#352317 100%)!important;box-shadow:0 16px 36px rgba(19,20,21,.10)!important;}',
            '.single-post .agency-outsourcing-cta p{margin:0!important;}',
            '.single-post .agency-outsourcing-cta__eyebrow{margin-bottom:.35rem!important;color:#e58a3d!important;font-size:.61rem!important;letter-spacing:.13em!important;text-transform:uppercase!important;}',
            '.single-post .agency-outsourcing-cta strong{color:#f4efe7!important;font-size:1.05rem!important;line-height:1.35!important;}',
            '.single-post .agency-outsourcing-cta .callout-cta{margin:0!important;white-space:nowrap!important;}',

            /* Never hide content while scrolling. */
            '.single-post .nexus-reveal,.single-post .nexus-reveal.is-in,.single-post .ao-reveal,.single-post .ao-reveal.is-visible{opacity:1!important;transform:none!important;transition:none!important;}',

            '@media (max-width:800px){',
            '.single-post .agency-delivery-flow__track{grid-template-columns:1fr!important;}',
            '.single-post .agency-delivery-flow__track li{min-height:0!important;}',
            '.single-post .agency-readiness-checklist ol{grid-template-columns:1fr!important;}',
            '.single-post .agency-outsourcing-cta{grid-template-columns:1fr!important;}',
            '.single-post .agency-outsourcing-cta .callout-cta{white-space:normal!important;}',
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

    function removeSidebarCta() {
        Array.prototype.slice.call(document.querySelectorAll('.ao-sidebar-cta')).forEach(function (node) {
            node.remove();
        });
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
        observer.observe(list, {
            childList: true,
            subtree: true,
            attributes: true,
            attributeFilter: ['class', 'aria-current']
        });
    }

    function setupShareRail(article, headings) {
        var rail = document.querySelector('.nexus-share-rail');
        if (!rail) return;

        var startMarker = headings && headings.length > 1 ? headings[1] : article.querySelector('h2');
        var endMarker = article.querySelector('#zusammenarbeit') ||
                        document.querySelector('.nexus-article-next') ||
                        document.querySelector('.nexus-rating') ||
                        document.querySelector('.nexus-author-bio');
        var ticking = false;

        rail.classList.remove('ao-route-visible');

        function update() {
            var y = window.scrollY;
            var offset = Math.min(220, window.innerHeight * 0.28);
            var start = startMarker ? startMarker.getBoundingClientRect().top + window.scrollY - offset : Number.MAX_SAFE_INTEGER;
            var end = endMarker ? endMarker.getBoundingClientRect().top + window.scrollY - 260 : Number.MAX_SAFE_INTEGER;
            rail.classList.toggle('ao-route-visible', y >= start && y < end);
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

        injectStyles();
        removeDuplicateHeroCategory();
        removeSidebarCta();

        var headings = enhanceSections(article);
        enhanceToc(document.querySelector('.nexus-sidebar'));
        setupShareRail(article, headings);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();