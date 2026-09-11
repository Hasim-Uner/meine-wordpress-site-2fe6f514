/* Route-specific editorial behaviour for /wordpress-projekte-auslagern/.
 *
 * Behaviour only. All styling lives in assets/css/article-agency-outsourcing.css.
 * This script previously injected a second stylesheet at runtime, which
 * repainted the layout after first paint; that is gone.
 *
 * Scope: section markers, TOC state, share rail visibility, entrance reveal.
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

    /* Blocks where an entrance genuinely helps: the figures the eye stops on,
     * plus the single commercial moment. Body copy, notes and section markers
     * stay static — animating them would interrupt reading, not support it. */
    var REVEAL_SELECTOR = [
        '.agency-delivery-flow',
        '.agency-control-model',
        '.agency-risk-matrix',
        '.agency-readiness-checklist',
        '.agency-outsourcing-cta'
    ].join(',');

    function padIndex(index) {
        return String(index + 1).padStart(2, '0');
    }

    function prefersReducedMotion() {
        return !!(window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches);
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

    /* Entrance reveal.
     *
     * Two guarantees, both deliberate:
     *  - Only elements currently below the viewport are hidden. Anything the
     *    reader can already see keeps its final state, so nothing flashes and
     *    the hero never animates.
     *  - The hidden state is applied by this script. Without JS, without
     *    IntersectionObserver, or under reduced motion, every block renders
     *    at its final state and no content can be trapped invisible.
     */
    function setupReveal(article) {
        if (prefersReducedMotion()) return;
        if (!('IntersectionObserver' in window)) return;

        var candidates = Array.prototype.slice.call(article.querySelectorAll(REVEAL_SELECTOR));
        if (!candidates.length) return;

        var viewportBottom = window.innerHeight || document.documentElement.clientHeight;
        var pending = candidates.filter(function (el) {
            return el.getBoundingClientRect().top > viewportBottom;
        });

        if (!pending.length) return;

        document.documentElement.classList.add('ao-motion');
        pending.forEach(function (el) {
            el.classList.add('ao-reveal');
        });

        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) return;
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -60px 0px' });

        pending.forEach(function (el) {
            observer.observe(el);
        });
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

        removeDuplicateHeroCategory();

        var headings = enhanceSections(article);
        enhanceToc(document.querySelector('.nexus-sidebar'));
        setupShareRail(article, headings);
        setupReveal(article);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
