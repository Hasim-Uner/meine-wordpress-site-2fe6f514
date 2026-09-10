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
    }

    function injectSidebarCta(sidebar) {
        if (!sidebar || sidebar.querySelector('.ao-sidebar-cta')) return;

        var toc = sidebar.querySelector('.sticky-toc');
        if (!toc) return;

        var card = document.createElement('aside');
        card.className = 'ao-sidebar-cta';
        card.setAttribute('aria-label', 'White-Label Zusammenarbeit');
        card.innerHTML = '' +
            '<span class="ao-sidebar-cta__eyebrow">Zusammen mehr möglich machen</span>' +
            '<h3 class="ao-sidebar-cta__title">Zuverlässiger WordPress-Partner für deine Agentur</h3>' +
            '<p class="ao-sidebar-cta__text">Technische Umsetzung im Hintergrund – mit sauberem Scope, QA, Tracking und Übergabe.</p>' +
            '<a class="ao-sidebar-cta__button" href="/whitelabel-retainer/" data-track-action="cta_sidebar_whitelabel" data-track-category="lead_gen">Zusammenarbeit ansehen →</a>';

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

    function init() {
        var article = document.querySelector('.agency-outsourcing-article');
        if (!article) return;

        document.body.classList.remove('ao-share-ready');
        enhanceSections(article);

        var sidebar = document.querySelector('.nexus-sidebar');
        if (sidebar) {
            injectSidebarCta(sidebar);
            enhanceToc(sidebar);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
