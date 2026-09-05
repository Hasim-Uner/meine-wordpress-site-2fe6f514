/* =============================================================
   Article Reader TOC V1
   Enhances the existing NexusCore-generated #toc-list.
   Desktop/laptop expansion is CSS-driven (hover/focus); smaller viewports use a compact toggle.
   ============================================================= */
(function () {
    'use strict';

    if (typeof document === 'undefined') return;

    function initReaderToc() {
        var readerHeader = document.querySelector('[data-article-system]');
        var tocList = document.querySelector('.nexus-sidebar .sticky-toc #toc-list');
        if (!readerHeader || !tocList) return;

        var shell = tocList.closest('.sticky-toc');
        var aside = shell ? shell.closest('.nexus-sidebar') : null;
        if (!shell || !aside) return;

        aside.classList.add('nexus-reader-toc');
        shell.classList.add('nexus-reader-toc__shell');

        var headingLabel = shell.querySelector('h2, h3, .sticky-toc__label');
        if (headingLabel) {
            headingLabel.classList.add('nexus-reader-toc__label');
        }

        var toggle = document.createElement('button');
        toggle.type = 'button';
        toggle.className = 'nexus-reader-toc__toggle';
        toggle.setAttribute('aria-expanded', 'false');
        toggle.setAttribute('aria-controls', tocList.id || 'toc-list');
        toggle.innerHTML = '' +
            '<span class="nexus-reader-toc__toggle-copy">' +
                '<span class="nexus-reader-toc__toggle-label">Inhalt</span>' +
                '<span class="nexus-reader-toc__toggle-count" aria-live="polite"></span>' +
            '</span>' +
            '<span class="nexus-reader-toc__toggle-mark" aria-hidden="true">+</span>';
        shell.insertBefore(toggle, shell.firstChild);

        var countNode = toggle.querySelector('.nexus-reader-toc__toggle-count');
        var media = window.matchMedia ? window.matchMedia('(max-width: 1099px)') : null;
        var hydrated = false;

        function isMobileLayout() {
            return media ? media.matches : window.innerWidth <= 1099;
        }

        function syncActiveAria() {
            tocList.querySelectorAll('a').forEach(function (link) {
                if (link.classList.contains('active')) {
                    link.setAttribute('aria-current', 'location');
                } else {
                    link.removeAttribute('aria-current');
                }
            });
        }

        function hydrateItems() {
            var items = Array.prototype.slice.call(tocList.children).filter(function (item) {
                return item.tagName === 'LI' && !!item.querySelector('a');
            });

            if (!items.length) return false;

            var topLevelCount = 0;
            items.forEach(function (item) {
                var marginLeft = parseFloat(item.style.marginLeft || '0');
                var isSubsection = marginLeft > 0;

                item.classList.toggle('is-subsection', isSubsection);
                item.style.marginLeft = '';
                item.style.fontSize = '';

                if (!isSubsection) {
                    topLevelCount += 1;
                }
            });

            if (countNode) {
                countNode.textContent = topLevelCount ? '· ' + topLevelCount + ' Abschnitte' : '';
            }

            syncActiveAria();
            hydrated = true;
            return true;
        }

        function closeMobileToc() {
            shell.classList.remove('is-mobile-open');
            toggle.setAttribute('aria-expanded', 'false');
        }

        toggle.addEventListener('click', function () {
            var willOpen = !shell.classList.contains('is-mobile-open');
            shell.classList.toggle('is-mobile-open', willOpen);
            toggle.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
        });

        tocList.addEventListener('click', function (event) {
            if (isMobileLayout() && event.target.closest('a')) {
                closeMobileToc();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && shell.classList.contains('is-mobile-open')) {
                closeMobileToc();
                toggle.focus();
            }
        });

        if (media && typeof media.addEventListener === 'function') {
            media.addEventListener('change', function () {
                closeMobileToc();
            });
        }

        var listObserver = new MutationObserver(function (mutations) {
            var needsHydration = false;
            var needsActiveSync = false;

            mutations.forEach(function (mutation) {
                if (mutation.type === 'childList') {
                    needsHydration = true;
                }

                if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                    needsActiveSync = true;
                }
            });

            if (needsHydration) {
                hydrateItems();
            } else if (needsActiveSync && hydrated) {
                syncActiveAria();
            }
        });

        listObserver.observe(tocList, {
            childList: true,
            subtree: true,
            attributes: true,
            attributeFilter: ['class']
        });

        if (!hydrateItems()) {
            window.requestAnimationFrame(hydrateItems);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initReaderToc);
    } else {
        initReaderToc();
    }
})();
