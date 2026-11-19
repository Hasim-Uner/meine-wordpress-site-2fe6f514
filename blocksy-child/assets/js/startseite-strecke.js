/* Startseite: die Strecke.
 *
 * Sieben Aufgaben, ein Skript:
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
 * 6. Der Hero zeigt eine Herkunftszeile („Ihr Besuch kam über …"), nur
 *    wenn document.referrer oder utm_source eine Quelle nennt.
 * 7. Die Preise wechseln als Tabs zwischen Neubau und Bestand.
 *
 * Das Besuchsprotokoll im Hero (Abschnitte, Scrolltiefe, Klick, Etikett und
 * Signal) ist am 19.11.2026 entfallen; nur die Herkunft kam als Zeile zurueck.
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

    /* ── 6. Herkunftszeile im Hero ──────────────────────────── */

    /* Eine Zeile unter den Sonderwegen, nur wenn eine Quelle erkannt wird.
       Direktaufruf, leerer Verweis und die eigene Domain zeigen nichts.
       Kampagnenparameter haben Vorrang vor dem Verweis. Nur Textausgabe:
       auch untrusted Query-Werte werden weder HTML noch Link oder Anfrage.
       Das Element entsteht erst hier: ohne Skript gibt es keine Zeile. */
    var heroAktionen = root.querySelector('[data-st-hero] [data-st-aktionen]');
    var QUELLEN = [
        [/(^|\.)gemini\.google\.com$/, 'Gemini'],
        [/(^|\.)google\.[a-z.]+$/, 'Google'],
        [/(^|\.)bing\.com$/, 'Bing'],
        [/(^|\.)duckduckgo\.com$/, 'DuckDuckGo'],
        [/(^|\.)ecosia\.org$/, 'Ecosia'],
        [/(^|\.)(linkedin\.com|lnkd\.in)$/, 'LinkedIn'],
        [/(^|\.)(chatgpt\.com|openai\.com)$/, 'ChatGPT'],
        [/(^|\.)perplexity\.ai$/, 'Perplexity']
    ];

    function quellName(wert) {
        var klein = wert.toLowerCase();
        var name = '';
        QUELLEN.some(function (paar) {
            // utm_source=chatgpt.com oder utm_source=linkedin: beides ist dieselbe Quelle.
            if (!paar[0].test(klein) && paar[1].toLowerCase() !== klein) return false;
            name = paar[1];
            return true;
        });
        return name;
    }

    function herkunft() {
        var parameter = new URLSearchParams(window.location.search);
        var quelle = (parameter.get('utm_source') || '').trim();
        if (quelle) {
            var medium = (parameter.get('utm_medium') || '').trim();
            return quellName(quelle) || 'Kampagne: ' + quelle + (medium ? ' / ' + medium : '');
        }
        var host = '';
        try { host = document.referrer ? new URL(document.referrer).hostname.replace(/^www\./, '') : ''; } catch (e) {}
        if (!host || host === window.location.hostname.replace(/^www\./, '')) return '';
        return quellName(host) || host;
    }

    if (heroAktionen) {
        var quelleText = herkunft().replace(/[\x00-\x1f\x7f]/g, ' ').replace(/\s+/g, ' ').trim();
        if (quelleText) {
            if (quelleText.length > 48) quelleText = quelleText.slice(0, 47) + '…';
            var zeile = document.createElement('p');
            var wert = document.createElement('span');
            zeile.className = 'st-klein-label st-hero__herkunft';
            wert.className = 'st-hero__quelle';
            wert.textContent = quelleText;
            zeile.appendChild(document.createTextNode('Ihr Besuch kam über '));
            zeile.appendChild(wert);
            zeile.appendChild(document.createTextNode(' · zugeordnet ohne Cookie'));
            heroAktionen.appendChild(zeile);
        }
    }

    /* ── 7. Ausgangslage: Neubau | Bestand als Tabs ─────────── */

    /* WAI-ARIA Tabs mit automatischer Aktivierung: Pfeiltasten, Pos1, Ende.
       Ohne Skript bleibt die Tableiste hidden und beide Gruppen stehen
       untereinander. Die inaktive Gruppe bleibt im DOM, nur hidden. */
    var ausgangslage = root.querySelector('[data-st-ausgangslage]');
    var tabs = ausgangslage ? Array.prototype.slice.call(ausgangslage.querySelectorAll('[role="tab"]')) : [];
    var tafeln = tabs.map(function (tab) { return document.getElementById(tab.getAttribute('aria-controls')); });
    var hoehenAnimation;

    function zeigeTab(index, optionen) {
        optionen = optionen || {};
        var vorher = ausgangslage.offsetHeight;
        var schonAktiv = tabs[index].getAttribute('aria-selected') === 'true' && !tafeln[index].hidden;
        tabs.forEach(function (tab, i) {
            var an = i === index;
            tab.setAttribute('aria-selected', an ? 'true' : 'false');
            tab.tabIndex = an ? 0 : -1;
            tafeln[i].hidden = !an;
            tafeln[i].classList.remove('is-einblenden');
        });
        if (optionen.fokus) tabs[index].focus();
        if (!optionen.animieren || schonAktiv || reduced.matches) return;
        // Kurzer Crossfade der neuen Tafel (CSS) und eine weiche Hoehe, damit
        // die Folgesektion nicht springt. Nur transform/opacity in CSS; die
        // Hoehe laeuft einmal ueber die Web Animations API.
        void tafeln[index].offsetWidth;
        tafeln[index].classList.add('is-einblenden');
        var nachher = ausgangslage.offsetHeight;
        if (hoehenAnimation) hoehenAnimation.cancel();
        if (ausgangslage.animate && vorher !== nachher) {
            // Waehrend die Hoehe waechst, ragt die neue Tafel nicht in die Preisnotiz.
            ausgangslage.style.overflow = 'hidden';
            hoehenAnimation = ausgangslage.animate([{ height: vorher + 'px' }, { height: nachher + 'px' }],
                { duration: 240, easing: 'cubic-bezier(0.22, 1, 0.36, 1)' });
            var dieseAnimation = hoehenAnimation;
            dieseAnimation.onfinish = dieseAnimation.oncancel = function () {
                if (hoehenAnimation === dieseAnimation) ausgangslage.style.overflow = '';
            };
        }
    }

    function tafelVonZiel(hash) {
        if (!hash || hash.length < 2) return -1;
        var ziel;
        try { ziel = document.getElementById(decodeURIComponent(hash.slice(1))); } catch (e) { return -1; }
        if (!ziel) return -1;
        for (var i = 0; i < tafeln.length; i++) {
            if (tafeln[i] && tafeln[i].contains(ziel)) return i;
        }
        return -1;
    }

    // Ein Anker in eine verborgene Gruppe oeffnet erst ihren Tab, dann springt
    // der Browser bzw. dieses Skript an das Ziel.
    function oeffneTabFuerHash(hash, springen) {
        var i = tafelVonZiel(hash);
        if (i < 0 || !tafeln[i].hidden) return;
        zeigeTab(i);
        if (springen) {
            var ziel = document.getElementById(decodeURIComponent(hash.slice(1)));
            if (ziel) ziel.scrollIntoView({ block: 'start' });
        }
    }

    if (ausgangslage && tabs.length === 2 && tafeln.every(Boolean)) {
        tafeln.forEach(function (tafel, i) {
            tafel.setAttribute('role', 'tabpanel');
            tafel.setAttribute('aria-labelledby', tabs[i].id);
        });
        ausgangslage.querySelector('[role="tablist"]').hidden = false;
        ausgangslage.setAttribute('data-st-tabs-an', '');
        var start = tafelVonZiel(window.location.hash);
        zeigeTab(start < 0 ? 0 : start);
        if (start > 0) {
            var startZiel = document.getElementById(decodeURIComponent(window.location.hash.slice(1)));
            if (startZiel) window.requestAnimationFrame(function () { startZiel.scrollIntoView({ block: 'start' }); });
        }

        tabs.forEach(function (tab, i) {
            tab.addEventListener('click', function () { zeigeTab(i, { animieren: true }); });
            tab.addEventListener('keydown', function (event) {
                var ziel = { ArrowRight: i + 1, ArrowLeft: i - 1, Home: 0, End: tabs.length - 1 }[event.key];
                if (ziel === undefined) return;
                event.preventDefault();
                zeigeTab((ziel + tabs.length) % tabs.length, { animieren: true, fokus: true });
            });
        });
        // Klick auf einen Seitenanker in die verborgene Gruppe: vor dem
        // nativen Sprung umschalten, damit der Browser ein sichtbares Ziel hat.
        document.addEventListener('click', function (event) {
            var link = event.target.closest && event.target.closest('a[href^="#"]');
            if (link) oeffneTabFuerHash(link.getAttribute('href'), false);
        }, true);
        window.addEventListener('hashchange', function () { oeffneTabFuerHash(window.location.hash, true); });
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
