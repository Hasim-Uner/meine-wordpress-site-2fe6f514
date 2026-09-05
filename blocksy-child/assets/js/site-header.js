(function () {
    function initSiteHeader() {
        var header = document.querySelector('[data-site-header]');

        if (!header || header.hasAttribute('data-site-header-ready')) {
            return;
        }

        header.setAttribute('data-site-header-ready', '');

        var toggle = header.querySelector('[data-site-header-toggle]');
        var panel = header.querySelector('[data-site-header-panel]');
        var toggleLabel = toggle ? toggle.querySelector('.nx-site-header__toggle-label') : null;
        var desktopMedia = window.matchMedia('(min-width: 1101px)');
        var hoverMedia = window.matchMedia('(hover: hover) and (pointer: fine)');
        var usesFullscreenSheet = Boolean(
            toggle &&
            panel &&
            header.classList.contains('nx-site-header--sheet')
        );
        var supportsInert = typeof window.HTMLElement !== 'undefined' &&
            'inert' in window.HTMLElement.prototype;
        var isCondensed = null;
        var isVisible = null;
        var isPointerInside = false;
        var isFocusInside = false;
        var isNearTopEdge = false;
        var isAtPageEnd = false;
        var isInTopZone = false;
        var isScrollRevealed = false;
        var hideTimer = 0;
        var idleHideDelay = 1700;
        var scrollRevealDelta = 5;
        var scrollHideDelta = 8;
        var topEdgeThreshold = 76;
        var topZoneThreshold = 120;
        var headerHeightRaf = 0;
        var scrollRaf = 0;
        var pointerMoveRaf = 0;
        var pendingPointerY = 0;
        var pointerMoveEvent = window.PointerEvent ? 'pointermove' : 'mousemove';
        var inertedPageChildren = [];
        var inertedBarLinks = [];
        var cachedScrollY = window.scrollY;
        var lastScrollY = cachedScrollY;

        function syncHeaderHeight() {
            var measuredHeight = Math.ceil(header.getBoundingClientRect().height);
            var height = usesFullscreenSheet ? Math.max(64, measuredHeight) : Math.max(76, measuredHeight + 12);

            document.documentElement.style.setProperty('--nx-header-height', height + 'px');
        }

        function queueHeaderHeightSync() {
            if (headerHeightRaf) {
                return;
            }

            headerHeightRaf = window.requestAnimationFrame(function () {
                headerHeightRaf = window.requestAnimationFrame(function () {
                    headerHeightRaf = 0;
                    syncHeaderHeight();
                });
            });
        }

        function clearHideTimer() {
            if (!hideTimer) {
                return;
            }

            window.clearTimeout(hideTimer);
            hideTimer = 0;
        }

        function setHeaderVisibility(nextVisible) {
            if (isVisible === nextVisible) {
                return;
            }

            isVisible = nextVisible;
            header.classList.toggle('is-visible', nextVisible);
        }

        var scrollRevealEnabled = header.getAttribute('data-site-header-scroll-reveal') !== 'off';

        /* isInTopZone und isScrollRevealed werden ausschliesslich fuer die
           Vollflaechen-Leiste gesetzt; die Sonderheader (Audit, Energy, Blog)
           behalten ihr bisheriges Timer-Verhalten. */
        function shouldPinHeader() {
            return isPointerInside || isFocusInside || isNearTopEdge || isAtPageEnd ||
                isInTopZone || isScrollRevealed ||
                header.classList.contains('is-open') ||
                header.hasAttribute('data-site-header-pin');
        }

        function readScrollY() {
            cachedScrollY = window.scrollY;
            return cachedScrollY;
        }

        function scheduleHide(delay) {
            clearHideTimer();

            if (shouldPinHeader()) {
                return;
            }

            hideTimer = window.setTimeout(function () {
                readScrollY();
                if (!shouldPinHeader()) {
                    setHeaderVisibility(false);
                }
            }, delay || idleHideDelay);
        }

        function showHeader(autoHide, delay) {
            setHeaderVisibility(true);

            if (autoHide) {
                scheduleHide(delay);
                return;
            }

            clearHideTimer();
        }

        function hideHeader() {
            clearHideTimer();

            if (shouldPinHeader()) {
                return;
            }

            setHeaderVisibility(false);
        }

        function updateVisibility(forceVisible) {
            if (forceVisible || shouldPinHeader()) {
                showHeader(false);
                return;
            }

            scheduleHide();
        }

        function makeElementsInert(elements) {
            var state = [];

            Array.prototype.forEach.call(elements, function (element) {
                var entry = {
                    element: element,
                    wasInert: supportsInert ? element.inert : false,
                    hadAriaHidden: element.hasAttribute('aria-hidden'),
                    ariaHidden: element.getAttribute('aria-hidden'),
                    hadTabIndex: element.hasAttribute('tabindex'),
                    tabIndex: element.getAttribute('tabindex')
                };

                state.push(entry);

                if (supportsInert) {
                    element.inert = true;
                } else {
                    element.setAttribute('aria-hidden', 'true');
                    if (element.matches('a, button, input, select, textarea, [tabindex]')) {
                        element.setAttribute('tabindex', '-1');
                    }
                }
            });

            return state;
        }

        function restoreInertElements(state) {
            state.forEach(function (entry) {
                if (supportsInert) {
                    entry.element.inert = entry.wasInert;
                    return;
                }

                if (entry.hadAriaHidden) {
                    entry.element.setAttribute('aria-hidden', entry.ariaHidden);
                } else {
                    entry.element.removeAttribute('aria-hidden');
                }

                if (entry.hadTabIndex) {
                    entry.element.setAttribute('tabindex', entry.tabIndex);
                } else {
                    entry.element.removeAttribute('tabindex');
                }
            });
        }

        function setOutsideContentInert(isInert) {
            if (!usesFullscreenSheet || !document.body) {
                return;
            }

            if (isInert) {
                if (inertedPageChildren.length) {
                    return;
                }

                var headerRoot = header;
                var pageChildren = [];

                while (headerRoot.parentElement && headerRoot.parentElement !== document.body) {
                    headerRoot = headerRoot.parentElement;
                }

                Array.prototype.forEach.call(document.body.children, function (child) {
                    if (child !== headerRoot) {
                        pageChildren.push(child);
                    }
                });

                inertedPageChildren = makeElementsInert(pageChildren);
                return;
            }

            restoreInertElements(inertedPageChildren);
            inertedPageChildren = [];
        }

        function setBarLinksInert(isInert) {
            if (!usesFullscreenSheet) {
                return;
            }

            if (isInert) {
                if (!inertedBarLinks.length) {
                    inertedBarLinks = makeElementsInert(header.querySelectorAll('[data-site-header-bar-link]'));
                }
                return;
            }

            restoreInertElements(inertedBarLinks);
            inertedBarLinks = [];
        }

        function setScrollLock(isLocked) {
            if (!usesFullscreenSheet || !document.body) {
                return;
            }

            document.documentElement.classList.toggle('nx-site-menu-open', isLocked);
            document.body.classList.toggle('nx-site-menu-open', isLocked);
        }

        function getPanelFocusables() {
            if (!usesFullscreenSheet) {
                return [];
            }

            return Array.prototype.filter.call(
                panel.querySelectorAll('a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'),
                function (element) {
                    return element.getAttribute('aria-hidden') !== 'true' && !element.hasAttribute('disabled');
                }
            );
        }

        function focusPanelStart() {
            var firstPanelLink = getPanelFocusables()[0];

            if (!firstPanelLink) {
                return;
            }

            window.requestAnimationFrame(function () {
                if (header.classList.contains('is-open')) {
                    firstPanelLink.focus({ preventScroll: true });
                }
            });
        }

        function updateToggleCopy(isOpen) {
            if (!toggle) {
                return;
            }

            toggle.setAttribute('aria-label', isOpen ? 'Navigation schließen' : 'Navigation öffnen');

            if (!toggleLabel) {
                return;
            }

            toggleLabel.textContent = isOpen
                ? toggleLabel.getAttribute('data-label-close') || 'Schließen'
                : toggleLabel.getAttribute('data-label-open') || 'Menü';
        }

        function setPanelState(isOpen, returnFocus) {
            if (!usesFullscreenSheet) {
                return;
            }

            var wasOpen = header.classList.contains('is-open');

            header.classList.toggle('is-open', isOpen);
            toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            updateToggleCopy(isOpen);

            if (isOpen) {
                panel.removeAttribute('inert');
                if (supportsInert) {
                    panel.inert = false;
                }
                panel.setAttribute('aria-hidden', 'false');
                setOutsideContentInert(true);
                setBarLinksInert(true);
                setScrollLock(true);
                showHeader(false);
                focusPanelStart();
            } else {
                panel.setAttribute('inert', '');
                if (supportsInert) {
                    panel.inert = true;
                }
                panel.setAttribute('aria-hidden', 'true');
                setOutsideContentInert(false);
                setBarLinksInert(false);
                setScrollLock(false);

                if (wasOpen && returnFocus !== false) {
                    toggle.focus({ preventScroll: true });
                }

                if (wasOpen) {
                    updateVisibility(false);
                }
            }

            queueHeaderHeightSync();
        }

        function closePanel() {
            setPanelState(false, true);
        }

        function trapPanelFocus(event) {
            if (
                !usesFullscreenSheet ||
                !header.classList.contains('is-open') ||
                event.key !== 'Tab'
            ) {
                return;
            }

            var focusables = [toggle].concat(getPanelFocusables());
            var first = focusables[0];
            var last = focusables[focusables.length - 1];
            var active = document.activeElement;

            if (!focusables.length) {
                event.preventDefault();
                return;
            }

            if (event.shiftKey && (active === first || focusables.indexOf(active) === -1)) {
                event.preventDefault();
                last.focus({ preventScroll: true });
            } else if (!event.shiftKey && active === last) {
                event.preventDefault();
                first.focus({ preventScroll: true });
            }
        }

        function updateFlightMode() {
            var nextCondensed = cachedScrollY > 36;

            if (isCondensed === nextCondensed) {
                return;
            }

            isCondensed = nextCondensed;
            header.classList.toggle('nexus-flight-mode', nextCondensed);
            queueHeaderHeightSync();
        }

        function queueScrollUpdate() {
            if (scrollRaf) {
                return;
            }

            scrollRaf = window.requestAnimationFrame(function () {
                scrollRaf = 0;
                readScrollY();

                var scrollDelta = cachedScrollY - lastScrollY;
                var nextCondensed = cachedScrollY > 36;

                lastScrollY = cachedScrollY;

                if (isCondensed !== nextCondensed) {
                    isCondensed = nextCondensed;
                    header.classList.toggle('nexus-flight-mode', nextCondensed);
                    queueHeaderHeightSync();
                }

                /* Vollflaechen-Leiste: Sichtbarkeit ist ein Zustand, kein Timer.
                   Oben immer sichtbar, beim Abwaertsscrollen weg, beim
                   Aufwaertsscrollen zurueck — und dann bleibt sie stehen, bis
                   wieder abwaerts gescrollt wird. */
                if (usesFullscreenSheet && scrollRevealEnabled) {
                    isInTopZone = cachedScrollY <= topZoneThreshold;

                    if (isInTopZone) {
                        isScrollRevealed = false;
                    } else if (scrollDelta <= -scrollRevealDelta) {
                        isScrollRevealed = true;
                    } else if (scrollDelta >= scrollHideDelta) {
                        isScrollRevealed = false;
                    }

                    if (shouldPinHeader()) {
                        showHeader(false);
                    } else {
                        hideHeader();
                    }

                    return;
                }

                if (shouldPinHeader()) {
                    showHeader(false);
                    return;
                }

                if (!scrollRevealEnabled) {
                    hideHeader();
                    return;
                }

                if (hoverMedia.matches) {
                    hideHeader();
                    return;
                }

                if (scrollDelta <= -scrollRevealDelta) {
                    showHeader(true);
                } else if (scrollDelta >= scrollHideDelta) {
                    hideHeader();
                }
            });
        }

        function applyPointerProximity(clientY) {
            var nextNearTopEdge = desktopMedia.matches && clientY <= topEdgeThreshold;

            if (isNearTopEdge === nextNearTopEdge) {
                return;
            }

            isNearTopEdge = nextNearTopEdge;

            if (nextNearTopEdge) {
                showHeader(false);
            } else {
                updateVisibility(false);
            }
        }

        function queuePointerProximity(clientY) {
            if (!desktopMedia.matches) {
                if (isNearTopEdge) {
                    isNearTopEdge = false;
                    updateVisibility(false);
                }
                return;
            }

            pendingPointerY = clientY;

            if (pointerMoveRaf) {
                return;
            }

            pointerMoveRaf = window.requestAnimationFrame(function () {
                pointerMoveRaf = 0;
                applyPointerProximity(pendingPointerY);
            });
        }

        if (usesFullscreenSheet) {
            toggle.addEventListener('click', function () {
                setPanelState(toggle.getAttribute('aria-expanded') !== 'true', true);
            });

            panel.querySelectorAll('a').forEach(function (link) {
                link.addEventListener('click', closePanel);
            });

            panel.addEventListener('click', function (event) {
                if (
                    event.target === panel ||
                    event.target.classList.contains('nx-site-header__sheet-grid')
                ) {
                    closePanel();
                }
            });

            document.addEventListener('click', function (event) {
                if (!header.contains(event.target)) {
                    closePanel();
                }
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && header.classList.contains('is-open')) {
                    event.preventDefault();
                    closePanel();
                    return;
                }

                trapPanelFocus(event);
            });

            setPanelState(false, false);
        }

        function handleDesktopMediaChange() {
            if (!desktopMedia.matches) {
                isNearTopEdge = false;
            }

            updateVisibility(false);
            queueHeaderHeightSync();
        }

        if (typeof desktopMedia.addEventListener === 'function') {
            desktopMedia.addEventListener('change', handleDesktopMediaChange);
        } else if (typeof desktopMedia.addListener === 'function') {
            desktopMedia.addListener(handleDesktopMediaChange);
        }

        header.addEventListener('nexus:header-pin', function () {
            updateVisibility(false);
        });

        header.addEventListener('mouseenter', function () {
            isPointerInside = true;
            showHeader(false);
        });

        header.addEventListener('mouseleave', function () {
            isPointerInside = false;
            updateVisibility(false);
        });

        header.addEventListener('focusin', function () {
            isFocusInside = true;
            showHeader(false);
        });

        header.addEventListener('focusout', function () {
            window.setTimeout(function () {
                isFocusInside = header.contains(document.activeElement);
                updateVisibility(false);
            }, 0);
        });

        document.addEventListener(pointerMoveEvent, function (event) {
            queuePointerProximity(event.clientY);
        }, { passive: true });

        document.addEventListener('mouseleave', function () {
            if (!isNearTopEdge) {
                return;
            }

            isNearTopEdge = false;
            updateVisibility(false);
        });

        var pageEnd = document.getElementById('footer');

        if (pageEnd && typeof window.IntersectionObserver === 'function') {
            new window.IntersectionObserver(function (entries) {
                var nextAtPageEnd = entries[0].isIntersecting;

                if (isAtPageEnd === nextAtPageEnd) {
                    return;
                }

                isAtPageEnd = nextAtPageEnd;
                updateVisibility(false);
            }, { rootMargin: '0px 0px -25% 0px' }).observe(pageEnd);
        }

        updateFlightMode();

        if (usesFullscreenSheet) {
            header.classList.add('is-auto-managed');
            isInTopZone = cachedScrollY <= topZoneThreshold;
            setHeaderVisibility(isInTopZone);
        } else {
            setHeaderVisibility(false);
        }

        syncHeaderHeight();

        window.addEventListener('scroll', queueScrollUpdate, { passive: true });
        window.addEventListener('resize', queueHeaderHeightSync, { passive: true });

        if (typeof window.ResizeObserver === 'function') {
            new window.ResizeObserver(queueHeaderHeightSync).observe(header);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSiteHeader);
    } else {
        initSiteHeader();
    }
}());
