/* Startseite: die Strecke.
 *
 * Fuenf Aufgaben, ein Skript:
 * 1. Die Linie in der Randspalte fuellt sich beim Lesen (transform: scaleY,
 *    Update in requestAnimationFrame, Geometrie nur bei Groessenaenderung).
 *    Die Lesekante liegt bei 45 % der Fensterhoehe; am Ende ist alles erreicht.
 * 2. Die Ladezeit dieses Aufrufs steht im Pruefstand (Station 05) und in der
 *    Station "Seite", gemessen im Browser.
 * 3. Native Stationen klappen exklusiv auf; die Messtafel erscheint einmal.
 * 4. „Leistungen" in der Kopfleiste ist nur aktiv, solange #angebote im
 *    Blick ist.
 * 5. Auf Seiten mit data-st-einblenden blenden Bloecke unter dem ersten
 *    Bildschirm beim ersten Sichtkontakt ein.
 *
 * Das Besuchsprotokoll im Hero (Herkunft, Abschnitte, Scrolltiefe, Klick,
 * Etikett und Signal) ist am 19.11.2026 entfallen.
 *
 * Harte Regel: Dieses Skript sendet nichts und speichert nichts. Kein
 * Netzwerkaufruf, kein Browser-Speicher, kein Cookie. Die Sperrliste in
 * scripts/canon-forbidden-values.txt (Regel strecke-js-privat) bricht den
 * Build, sobald hier einer dieser Aufrufe auftaucht.
 *
 * Ohne dieses Skript steht alles: Linie statisch, Stationen nativ bedienbar.
 */
(function () {
    'use strict';

    var root = document.querySelector('[data-st]');
    if (!root) return;

    var reduced = window.matchMedia('(prefers-reduced-motion: reduce)');

    /* ── Hilfen ─────────────────────────────────────────────── */

    function jetzt() {
        return window.performance && performance.now ? performance.now() : 0;
    }

    function uhr(ms) {
        var s = Math.max(0, Math.floor(ms / 1000));
        var m = Math.floor(s / 60);
        s = s % 60;
        return (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;
    }

    function sekunden(ms) {
        return (ms / 1000).toFixed(2).replace('.', ',') + ' s';
    }

    /* ── 2. Ladezeit ────────────────────────────────────────── */

    /* Ladezeit: LCP, wo der Browser es kennt, sonst Navigation Timing.
       Das Label nennt immer die Groesse, die tatsaechlich gemessen wurde. */
    var lcpWert = 0;
    var lcpFertig = false;
    var lcpKopien = root.querySelectorAll('[data-st-lcp-kopie]');
    /* Googles Schwelle fuer eine gute LCP steht nur neben einer echten LCP,
       nicht neben der Ladezeit aus Navigation Timing. Ihre Zeile ist von
       Anfang an reserviert (visibility), damit nichts springt. */
    var lcpGrenzen = root.querySelectorAll('[data-st-lcp-grenze]');

    function zeigeLadezeit(ms, abschliessen) {
        lcpWert = ms;
        Array.prototype.forEach.call(lcpKopien, function (el) {
            el.textContent = sekunden(ms);
            var huelle = el.closest('[data-st-nur-js]');
            if (huelle) huelle.hidden = false;
        });
        if (kannLcp) {
            Array.prototype.forEach.call(lcpGrenzen, function (el) { el.setAttribute('data-st-an', ''); });
        }
        if (abschliessen) lcpFertig = true;
    }

    var kannLcp = typeof PerformanceObserver === 'function' &&
        PerformanceObserver.supportedEntryTypes &&
        PerformanceObserver.supportedEntryTypes.indexOf('largest-contentful-paint') !== -1;

    var lcpBeobachter;
    if (kannLcp) try {
        lcpBeobachter = new PerformanceObserver(function (liste) {
            var eintraege = liste.getEntries();
            var letzter = eintraege[eintraege.length - 1];
            // Nach dem Abschluss bleibt der Wert stehen: Spaeter nachgeladene
            // Bilder sind keine Ladezeit dieses Aufrufs mehr.
            if (letzter && !lcpFertig) zeigeLadezeit(letzter.startTime, false);
        });
        lcpBeobachter.observe({ type: 'largest-contentful-paint', buffered: true });
    } catch (e) { kannLcp = false; }

    if (kannLcp) {
        /* LCP steht fest, sobald die Seite geladen ist und der Browser
           kurz Ruhe hatte, spaetestens bei der ersten Eingabe. */
        var lcpAbschluss = function () {
            if (lcpFertig || !lcpWert) return;
            lcpBeobachter.takeRecords().forEach(function (e) { lcpWert = e.startTime; });
            zeigeLadezeit(lcpWert, true);
        };
        ['keydown', 'pointerdown', 'scroll'].forEach(function (typ) {
            window.addEventListener(typ, lcpAbschluss, { once: true, passive: true, capture: true });
        });
        var nachLaden = function () { window.setTimeout(lcpAbschluss, 1500); };
        if (document.readyState === 'complete') nachLaden();
        else window.addEventListener('load', nachLaden, { once: true });
    } else {
        Array.prototype.forEach.call(root.querySelectorAll('[data-st-lcp-label]'), function (label) {
            label.textContent = 'Ladezeit (Seite geladen)';
        });
        var ausNavigation = function () {
            var nav = performance.getEntriesByType && performance.getEntriesByType('navigation')[0];
            var ms = nav && nav.loadEventEnd ? nav.loadEventEnd : 0;
            if (!ms && performance.timing && performance.timing.loadEventEnd) {
                ms = performance.timing.loadEventEnd - performance.timing.navigationStart;
            }
            if (ms > 0) zeigeLadezeit(ms, true);
        };
        if (document.readyState === 'complete') window.setTimeout(ausNavigation, 0);
        else window.addEventListener('load', function () { window.setTimeout(ausNavigation, 0); }, { once: true });
    }

    /* ── 3. Stationen ───────────────────────────────────────── */

    // Auch Direktlinks und Zurueck/Vorwaerts oeffnen die richtige Station.
    // Der native Fragment-Sprung bleibt erhalten; Tastaturfokus geht an summary.
    function oeffneStation(hash, fokus) {
        var summary = hash && document.getElementById(hash.slice(1));
        if (!summary || !summary.matches('#angebot-funnel summary')) return;
        summary.closest('details').open = true;
        if (fokus) summary.focus({ preventScroll: true });
        neuVermessen();
    }
    window.addEventListener('hashchange', function () { oeffneStation(window.location.hash, true); });
    oeffneStation(window.location.hash, false);

    /* ── 1. Die Linie ───────────────────────────────────────── */

    var fuellungen = [];
    var punkte = [];
    var endeZeit = root.querySelector('[data-st-ende-zeit]');
    var endeErreicht = false;
    var geplant = false;

    function vermessen() {
        var y = window.scrollY || window.pageYOffset || 0;
        fuellungen = Array.prototype.map.call(root.querySelectorAll('[data-st-fuellung]'), function (el) {
            var rail = el.parentNode.getBoundingClientRect();
            return { el: el, oben: rail.top + y + el.offsetTop, hoehe: el.offsetHeight || 1 };
        });
        punkte = Array.prototype.map.call(root.querySelectorAll('.st-rail__punkt, [data-st-punkt]'), function (el) {
            var r = el.getBoundingClientRect();
            return { el: el, y: r.top + y + r.height / 2, an: el.classList.contains('st-erreicht') };
        });

    }

    function aktualisieren() {
        geplant = false;
        var y = window.scrollY || window.pageYOffset || 0;
        var h = window.innerHeight || document.documentElement.clientHeight;
        var max = Math.max(0, document.documentElement.scrollHeight - h);
        var unten = y >= max - 4;
        var anker = y + (unten ? h : h * 0.45);
        // Direkte Transform-Updates auch bei reduzierter Bewegung, ohne Uebergang.
        fuellungen.forEach(function (f) {
            var anteil = unten ? 1 : Math.max(0, Math.min(1, (anker - f.oben) / f.hoehe));
            f.el.style.transform = 'scaleY(' + anteil.toFixed(4) + ')';
        });
        punkte.forEach(function (p) {
            if (p.an || (!unten && anker < p.y)) return;
            p.an = true;
            p.el.classList.add('st-erreicht');
        });

        var ende = punkte.length ? punkte[punkte.length - 1] : null;
        if (!endeErreicht && ende && anker >= ende.y) {
            endeErreicht = true;
            var t = jetzt();
            if (endeZeit) {
                endeZeit.textContent = 'erreicht nach ' + uhr(t);
                endeZeit.hidden = false;
            }
        }
    }

    function planen() {
        if (geplant) return;
        geplant = true;
        window.requestAnimationFrame(aktualisieren);
    }

    function neuVermessen() {
        vermessen();
        planen();
    }

    /* ── Native Akkordeons und Messtafel ─────────────────────── */
    var stationen = root.querySelector('[data-st-stationen]');
    if (stationen) {
        var details = stationen.querySelectorAll('details[name="station"]');
        Array.prototype.forEach.call(details, function (station) {
            station.addEventListener('toggle', function () {
                // Fallback fuer Browser ohne exklusive details-name-Unterstuetzung.
                if (station.open) Array.prototype.forEach.call(details, function (andere) {
                    if (andere !== station) andere.open = false;
                });
                neuVermessen();
            });
        });
    }
    var messtafel = root.querySelector('[data-st-messtafel]');
    if (messtafel) {
        if (reduced.matches || !('IntersectionObserver' in window)) {
            messtafel.setAttribute('data-st-gesehen', '');
        } else {
            var messSicht = new IntersectionObserver(function (eintraege) {
                if (!eintraege.some(function (e) { return e.isIntersecting; })) return;
                messtafel.setAttribute('data-st-gesehen', '');
                messSicht.disconnect();
            }, { threshold: 0.2 });
            messSicht.observe(messtafel);
        }
    }

    /* ── 4. Kopfleiste ───────────────────────────────────────── */

    /* „Leistungen" zeigt auf #angebote dieser Seite und kommt ohne
       aria-current aus dem Header (ein Anker ist nicht die Seite). Aktiv ist
       der Link hier nur, solange der Abschnitt im mittleren Band des
       Fensters steht. Das erste Entfernen faengt HTML aus dem Seiten-Cache
       ab, das noch den frueheren Wert traegt. */
    var angebote = document.getElementById('angebote');
    var leistungsLinks = document.querySelectorAll('.leiste a[href$="#angebote"]');
    if (angebote && leistungsLinks.length && 'IntersectionObserver' in window) {
        Array.prototype.forEach.call(leistungsLinks, function (link) { link.removeAttribute('aria-current'); });
        new IntersectionObserver(function (eintraege) {
            var imBlick = eintraege[eintraege.length - 1].isIntersecting;
            Array.prototype.forEach.call(leistungsLinks, function (link) {
                if (imBlick) link.setAttribute('aria-current', 'location');
                else link.removeAttribute('aria-current');
            });
        }, { rootMargin: '-45% 0px -45% 0px' }).observe(angebote);
    }

    /* ── 5. Einblenden ──────────────────────────────────────── */

    /* Nur Bloecke, die beim Start unter dem Fenster liegen: Was schon zu
       sehen ist, verschwindet nicht nachtraeglich. Die Tafeln selbst
       stehen, ihr Inhalt blendet ein; die Stationen haben ihren eigenen
       Auftritt. */
    function einblenden() {
        if (!root.hasAttribute('data-st-einblenden') || reduced.matches || !('IntersectionObserver' in window)) return;
        var fenster = window.innerHeight || document.documentElement.clientHeight;
        var auftritt = new IntersectionObserver(function (eintraege) {
            eintraege.forEach(function (e) {
                if (!e.isIntersecting) return;
                e.target.setAttribute('data-st-gesehen', '');
                auftritt.unobserve(e.target);
            });
        }, { rootMargin: '0px 0px -8% 0px' });
        /* Erst alle Positionen lesen, dann schreiben: Abwechselnd gelesen
           und geschrieben, rechnete der Browser je Block den Stil neu. */
        var ziele = Array.prototype.filter.call(root.querySelectorAll(
            '.st-abschnitt:not(.st-hero) .st-inhalt > :not([data-st-stationen]):not(.tafel):not(.st-pruefungen), ' +
            '.st-tafel__kopf, .st-pruefungen > li, .st-schluss > .st-anfrage__h2, .st-einstiege > *'
        ), function (el) {
            return el.getBoundingClientRect().top >= fenster;
        });
        ziele.forEach(function (el) {
            el.classList.add('st-auftritt');
            auftritt.observe(el);
        });
    }

    /* ── Start ──────────────────────────────────────────────── */

    root.setAttribute('data-st-bereit', '');
    vermessen();
    aktualisieren();
    /* Einblenden betrifft nur Bloecke unter dem ersten Bildschirm und
       wartet deshalb, bis der Browser Luft hat: nicht im Ladeweg. */
    if ('requestIdleCallback' in window) window.requestIdleCallback(einblenden, { timeout: 1500 });
    else window.setTimeout(einblenden, 300);

    window.addEventListener('scroll', planen, { passive: true });
    window.addEventListener('resize', neuVermessen, { passive: true });
    window.addEventListener('load', neuVermessen, { once: true });
    if ('ResizeObserver' in window) {
        new ResizeObserver(function () { neuVermessen(); }).observe(root);
    }
    if (document.fonts && document.fonts.ready) {
        document.fonts.ready.then(neuVermessen);
    }
    if (reduced.addEventListener) {
        reduced.addEventListener('change', function () {
            if (reduced.matches && messtafel) messtafel.setAttribute('data-st-gesehen', '');
            planen();
        });
    }
})();
