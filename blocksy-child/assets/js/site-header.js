(function () {
    function initSiteHeader() {
        var header = document.querySelector('[data-site-header]');

        if (!header) {
            return;
        }

        var toggle = header.querySelector('[data-site-header-toggle]');
        var panel = header.querySelector('[data-site-header-panel]');
        var desktopMedia = window.matchMedia('(min-width: 1101px)');
        // Ein echter Zeiger, der die obere Kante ansteuern kann. Ein Handy hat
        // den nicht -- dort braucht es einen anderen Weg zur Navigation.
        var hoverMedia = window.matchMedia('(hover: hover) and (pointer: fine)');
        var isCondensed = null;
        var isVisible = null;
        var isPointerInside = false;
        var isFocusInside = false;
        var isNearTopEdge = false;
        var isAtPageEnd = false;
        var hideTimer = 0;
        var idleHideDelay = 1700;
        var scrollRevealDelta = 5;
        var scrollHideDelta = 8;
        var topEdgeThreshold = 76;
        var headerHeightRaf = 0;
        var scrollRaf = 0;
        var pointerMoveRaf = 0;
        var pendingPointerY = 0;
        var pointerMoveEvent = window.PointerEvent ? 'pointermove' : 'mousemove';

        function syncHeaderHeight() {
            var height = Math.max(76, Math.ceil(header.getBoundingClientRect().height + 12));
            document.documentElement.style.setProperty('--nx-header-height', height + 'px');
        }

        function queueHeaderHeightSync() {
            if (headerHeightRaf) {
                return;
            }

            // Double-rAF: Read getBoundingClientRect after browser layout pass
            // to avoid forced reflow when called after classList writes.
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

        function shouldPinHeader() {
            return isPointerInside || isFocusInside || isNearTopEdge || isAtPageEnd ||
                header.classList.contains('is-open') ||
                header.hasAttribute('data-site-header-pin');
        }

        // Eine Seite kann den Header selbst einblenden — etwa am Seitenende, wenn
        // ihre eigene Sprungnavigation die Orientierung wieder abgibt.
        header.addEventListener('nexus:header-pin', function () {
            updateVisibility(false);
        });

        // Cache scrollY um Forced Reflow nach DOM-Writes zu vermeiden.
        var cachedScrollY = window.scrollY;
        var lastScrollY = cachedScrollY;

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

        function setPanelState(isOpen) {
            if (!toggle || !panel) {
                return;
            }

            header.classList.toggle('is-open', isOpen);
            toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            panel.hidden = !isOpen;
            updateVisibility(isOpen);

            queueHeaderHeightSync();
        }

        function closePanel() {
            setPanelState(false);
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
                // Batch-Read: scrollY einmal lesen, bevor DOM-Writes passieren.
                readScrollY();
                var scrollDelta = cachedScrollY - lastScrollY;
                lastScrollY = cachedScrollY;
                var nextCondensed = cachedScrollY > 36;

                if (isCondensed !== nextCondensed) {
                    isCondensed = nextCondensed;
                    header.classList.toggle('nexus-flight-mode', nextCondensed);
                    queueHeaderHeightSync();
                }

                if (shouldPinHeader()) {
                    showHeader(false);
                    return;
                }

                // Seiten mit eigener sticky Sprungnavigation schalten die
                // Scroll-Einblendung ab, damit nicht zwei Leisten konkurrieren.
                if (!scrollRevealEnabled) {
                    hideHeader();
                    return;
                }

                // Am Zeigergeraet blendet nur die obere Bildschirmkante ein.
                // Auf Touch gibt es die nicht, und der Menue-Knopf steckt im
                // versteckten Header -- ohne diese Einblendung waere die
                // Navigation dort nur am Seitenende erreichbar.
                if (hoverMedia.matches) {
                    hideHeader();
                    return;
                }

                if (scrollDelta <= -scrollRevealDelta) {
                    showHeader(true);
                    return;
                }

                if (scrollDelta >= scrollHideDelta) {
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
                return;
            }

            updateVisibility(false);
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

        if (toggle && panel) {
            toggle.addEventListener('click', function () {
                setPanelState(toggle.getAttribute('aria-expanded') !== 'true');
            });

            panel.querySelectorAll('a').forEach(function (link) {
                link.addEventListener('click', closePanel);
            });

            document.addEventListener('click', function (event) {
                if (header.contains(event.target)) {
                    return;
                }

                closePanel();
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    closePanel();
                }
            });

            if (typeof desktopMedia.addEventListener === 'function') {
                desktopMedia.addEventListener('change', function (event) {
                    if (event.matches) {
                        closePanel();
                    }

                    if (!event.matches) {
                        isNearTopEdge = false;
                    }

                    updateVisibility(false);
                    queueHeaderHeightSync();
                });
            } else if (typeof desktopMedia.addListener === 'function') {
                desktopMedia.addListener(function (event) {
                    if (event.matches) {
                        closePanel();
                    }

                    if (!event.matches) {
                        isNearTopEdge = false;
                    }

                    updateVisibility(false);
                    queueHeaderHeightSync();
                });
            }
        }

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

        // Der Footer ist der zweite Weg zur Navigation: wer unten ankommt, hat
        // zu Ende gelesen und will weiter. Das ist zugleich der einzige Reveal,
        // der auf Touch und am Zeigergeraet gleich funktioniert -- und der
        // einzige, ueber den ein Erstbesucher ueberhaupt erfaehrt, dass es ein
        // Menue gibt, wenn er nie an die obere Kante faehrt.
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

        readScrollY();
        lastScrollY = cachedScrollY;
        updateFlightMode();
        // Bewusst kein Einblenden beim Laden: das Aufblitzen ueber dem
        // Hero-Bereich war der Grund fuer diese Aenderung.
        setHeaderVisibility(false);
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
})();
