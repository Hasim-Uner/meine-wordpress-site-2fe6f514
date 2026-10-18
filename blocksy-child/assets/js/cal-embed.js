/**
 * Progressive enhancement for Cal.com booking links.
 *
 * Existing anchors keep their normal href as fallback.
 * When JS is available, matching links open the Cal modal on-page.
 */
(function () {
    'use strict';

    var config = window.NexusCalEmbedConfig || {};
    var loadPromise = null;
    var initialized = false;
    var preloaded = false;
    var observedRoot = null;

    function ensureBootstrapNamespace(cal, namespace) {
        var namespaceApi = cal.ns[namespace];

        if (typeof namespaceApi === 'function') {
            namespaceApi.q = namespaceApi.q || [];
            return namespaceApi;
        }

        namespaceApi = function () {
            namespaceApi.q.push(Array.prototype.slice.call(arguments));
        };

        namespaceApi.q = namespaceApi.q || [];
        cal.ns[namespace] = namespaceApi;

        return namespaceApi;
    }

    function ensureCalBootstrap() {
        var existingCal = window.Cal;

        if (existingCal && typeof existingCal === 'function' && typeof existingCal.version === 'string') {
            return existingCal;
        }

        if (existingCal && typeof existingCal === 'function' && existingCal.q && existingCal.ns) {
            return existingCal;
        }

        existingCal = function () {
            var args = Array.prototype.slice.call(arguments);

            if ('init' === args[0] && typeof args[1] === 'string') {
                ensureBootstrapNamespace(existingCal, args[1]).q.push(args);
                return;
            }

            existingCal.q.push(args);
        };

        existingCal.q = existingCal.q || [];
        existingCal.ns = existingCal.ns || {};
        window.Cal = existingCal;

        return existingCal;
    }

    function normalizePath(path) {
        var value = String(path || '').trim();

        if (!value) {
            return '';
        }

        return '/' + value.replace(/^\/+|\/+$/g, '');
    }

    function parseUrl(url) {
        try {
            return new URL(url, window.location.origin);
        } catch (error) {
            return null;
        }
    }

    function getConfiguredOrigin() {
        var configuredOrigin = String(config.calOrigin || '').trim();

        if (configuredOrigin) {
            return configuredOrigin;
        }

        var parsedBookingUrl = parseUrl(config.bookingUrl);
        return parsedBookingUrl ? parsedBookingUrl.origin : '';
    }

    function getConfiguredPath() {
        var configuredPath = normalizePath(config.calLink);

        if (configuredPath) {
            return configuredPath;
        }

        var parsedBookingUrl = parseUrl(config.bookingUrl);
        return parsedBookingUrl ? normalizePath(parsedBookingUrl.pathname) : '';
    }

    function getConfiguredBookingPaths() {
        var entries = Array.isArray(config.bookingLinks) ? config.bookingLinks : [];
        var seen = {};
        var paths = [];
        var index;
        var entry;
        var entryPath;

        if (calPath) {
            seen[calPath] = true;
            paths.push(calPath);
        }

        for (index = 0; index < entries.length; index += 1) {
            entry = entries[index] || {};
            entryPath = normalizePath(entry.calLink || entry.cal_link || '');

            if (!entryPath || seen[entryPath]) {
                continue;
            }

            seen[entryPath] = true;
            paths.push(entryPath);
        }

        return paths;
    }

    var calOrigin = getConfiguredOrigin();
    var calPath = getConfiguredPath();
    var allowedCalPaths = getConfiguredBookingPaths();

    if (!calOrigin || !calPath || !config.embedScriptUrl || !config.namespace) {
        return;
    }

    function isModifiedClick(event) {
        return event.defaultPrevented
            || event.button !== 0
            || event.metaKey
            || event.ctrlKey
            || event.shiftKey
            || event.altKey;
    }

    function getAnchorUrl(link) {
        if (!link) {
            return null;
        }

        return parseUrl(link.getAttribute('href'));
    }

    function getLinkCalPath(link) {
        var linkUrl = getAnchorUrl(link);

        if (!linkUrl || linkUrl.origin !== calOrigin) {
            return '';
        }

        return normalizePath(linkUrl.pathname);
    }

    function isMatchingBookingLink(link) {
        var linkPath = getLinkCalPath(link);

        return !!linkPath && allowedCalPaths.indexOf(linkPath) !== -1;
    }

    function markBookingLink(link) {
        if (!isMatchingBookingLink(link)) {
            return false;
        }

        link.setAttribute('aria-haspopup', 'dialog');
        link.setAttribute('data-cal-booking-link', 'true');

        return true;
    }

    function handleBoundLinkClick(event) {
        var link = event.currentTarget;

        if (!link || !markBookingLink(link) || isModifiedClick(event)) {
            return;
        }

        event.preventDefault();
        openModal(link);
    }

    function handleBoundLinkPreload(event) {
        var link = event.currentTarget;

        if (!link || !markBookingLink(link)) {
            return;
        }

        preloadModal(link);
    }

    function bindBookingLink(link) {
        if (!link || !markBookingLink(link)) {
            return false;
        }

        if ('true' === link.getAttribute('data-cal-embed-bound')) {
            return true;
        }

        link.setAttribute('data-cal-embed-bound', 'true');
        link.addEventListener('click', handleBoundLinkClick);
        // Kein Vorladen bei Hover oder Fokus: Cal.com erfährt erst beim Klick
        // von diesem Besuch (Datenschutzerklärung, Abschnitt Terminbuchung).
        link.addEventListener('pointerdown', handleBoundLinkPreload, { passive: true, once: true });

        return true;
    }

    function decorateScope(scope) {
        var links;
        var index;

        if (!scope) {
            return;
        }

        if (scope.nodeType === 1 && scope.tagName === 'A') {
            bindBookingLink(scope);
            return;
        }

        if (!scope.querySelectorAll) {
            return;
        }

        links = scope.querySelectorAll('a[href]');

        for (index = 0; index < links.length; index += 1) {
            bindBookingLink(links[index]);
        }
    }

    function loadEmbedScript() {
        ensureCalBootstrap();

        if (window.Cal && typeof window.Cal === 'function' && typeof window.Cal.version === 'string') {
            return Promise.resolve(window.Cal);
        }

        if (loadPromise) {
            return loadPromise;
        }

        loadPromise = new Promise(function (resolve, reject) {
            var script = document.createElement('script');

            script.src = String(config.embedScriptUrl);
            script.async = true;
            script.onload = function () {
                if (window.Cal && typeof window.Cal === 'function' && typeof window.Cal.version === 'string') {
                    resolve(window.Cal);
                    return;
                }

                loadPromise = null;
                reject(new Error('Cal embed API unavailable after script load.'));
            };
            script.onerror = function () {
                loadPromise = null;
                reject(new Error('Failed to load Cal embed script.'));
            };

            document.head.appendChild(script);
        });

        return loadPromise;
    }

    function resolveNamespaceApi(resolve, reject, attempt) {
        var namespaceApi = window.Cal && typeof window.Cal.version === 'string' && window.Cal.ns ? window.Cal.ns[config.namespace] : null;

        if (typeof namespaceApi === 'function') {
            resolve(namespaceApi);
            return;
        }

        if (attempt >= 8) {
            reject(new Error('Cal namespace API unavailable.'));
            return;
        }

        window.setTimeout(function () {
            resolveNamespaceApi(resolve, reject, attempt + 1);
        }, 50);
    }

    function getNamespaceApi() {
        var Cal = ensureCalBootstrap();

        if (!initialized) {
            ensureBootstrapNamespace(Cal, String(config.namespace));
            Cal('init', String(config.namespace), { origin: calOrigin });
            initialized = true;
        }

        return loadEmbedScript().then(function () {
            return new Promise(function (resolve, reject) {
                resolveNamespaceApi(resolve, reject, 0);
            });
        });
    }

    function fallbackToLink(link) {
        var href = link ? link.getAttribute('href') : '';
        var target = link && link.getAttribute('target') ? String(link.getAttribute('target')).toLowerCase() : '';

        if (!href) {
            return;
        }

        if ('_blank' === target) {
            window.open(href, '_blank', 'noopener,noreferrer');
            return;
        }

        window.location.assign(href);
    }

    function preloadModal(link) {
        var linkPath = getLinkCalPath(link) || calPath;

        if (preloaded === linkPath) {
            return;
        }

        preloaded = linkPath;

        getNamespaceApi()
            .then(function (calApi) {
                calApi('preload', {
                    calLink: String(linkPath),
                    calOrigin: calOrigin,
                    type: 'modal'
                });
            })
            .catch(function () {
                preloaded = '';
            });
    }

    /*
     * Quelle der Buchung fuer das CRM: Cal.com legt die Metadata an der
     * Buchung ab und schickt sie im Webhook mit (inc/calcom-webhook.php).
     * Nur feste Kuerzel und der Pfad, keine Personendaten.
     */
    function bookingMetadata(link) {
        var source = 'website';
        var section = link && link.getAttribute ? String(link.getAttribute('data-track-section') || '') : '';

        if (link && link.closest && link.closest('.hu-chat')) {
            source = 'chat';
        } else if (/^[a-z0-9_-]{1,40}$/.test(section)) {
            source = section;
        }

        return {
            'metadata[quelle]': source,
            'metadata[seite]': String(window.location.pathname || '/').slice(0, 200)
        };
    }

    function openModal(link) {
        var linkPath = getLinkCalPath(link) || calPath;

        getNamespaceApi()
            .then(function (calApi) {
                calApi('modal', {
                    calLink: String(linkPath),
                    calOrigin: calOrigin,
                    config: bookingMetadata(link)
                });
            })
            .catch(function () {
                fallbackToLink(link);
            });
    }

    function observeDynamicLinks() {
        if (!window.MutationObserver || !document.body || observedRoot) {
            return;
        }

        observedRoot = new MutationObserver(function (mutations) {
            var mutationIndex;
            var nodeIndex;
            var mutation;
            var node;

            for (mutationIndex = 0; mutationIndex < mutations.length; mutationIndex += 1) {
                mutation = mutations[mutationIndex];

                for (nodeIndex = 0; nodeIndex < mutation.addedNodes.length; nodeIndex += 1) {
                    node = mutation.addedNodes[nodeIndex];
                    decorateScope(node);
                }
            }
        });

        observedRoot.observe(document.body, {
            childList: true,
            subtree: true
        });
    }

    function init() {
        decorateScope(document);
        observeDynamicLinks();
    }

    if ('loading' === document.readyState) {
        document.addEventListener('DOMContentLoaded', init, { once: true });
        return;
    }

    init();
}());
