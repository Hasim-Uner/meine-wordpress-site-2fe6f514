/* Startseite: die Strecke.
 *
 * Vier Aufgaben, ein Skript:
 * 1. Die Linie in der Randspalte fuellt sich beim Lesen (transform: scaleY,
 *    Update in requestAnimationFrame, Geometrie nur bei Groessenaenderung).
 * 2. Das Messprotokoll im Hero zeigt, was dieser Browser misst: Ladezeit,
 *    gesehene Abschnitte, Scrolltiefe, Klick auf einen Anfrage-Button.
 * 3. Die Stationen in Abschnitt 03 klappen einzeln auf.
 * 4. „Leistungen" in der Kopfleiste ist nur aktiv, solange #angebote im
 *    Blick ist.
 *
 * Harte Regel: Dieses Skript sendet nichts und speichert nichts. Kein
 * Netzwerkaufruf, kein Browser-Speicher, kein Cookie. Die Sperrliste in
 * scripts/canon-forbidden-values.txt (Regel strecke-js-privat) bricht den
 * Build, sobald hier einer dieser Aufrufe auftaucht.
 *
 * Ohne dieses Skript steht alles: Linie statisch, Stationen offen, das
 * Protokoll sagt, dass es leer bleibt.
 */
(function () {
    'use strict';

    var root = document.querySelector('[data-st]');
    if (!root) return;

    var reduced = window.matchMedia('(prefers-reduced-motion: reduce)');
    var desktop = window.matchMedia('(min-width: 1024px)');
    var hoverFein = window.matchMedia('(hover: hover) and (pointer: fine)');

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

    function text(el) {
        return (el && el.textContent || '').replace(/[→↗]/g, '').replace(/\s+/g, ' ').trim();
    }

    /* ── 2. Messprotokoll ───────────────────────────────────── */

    var protokoll = root.querySelector('[data-st-protokoll]');
    var werte = {};
    var verlauf = protokoll && protokoll.querySelector('[data-st-verlauf]');
    var ansage = protokoll && protokoll.querySelector('[data-st-ansage]');
    var letzteAnsage = -Infinity;
    var ANSAGE_ABSTAND = 10000;

    if (protokoll) {
        ['lcp', 'abschnitte', 'tiefe', 'klick'].forEach(function (key) {
            werte[key] = protokoll.querySelector('[data-st-wert="' + key + '"]');
        });
        var status = protokoll.querySelector('[data-st-status]');
        if (status) status.hidden = false;
    }

    function setzeWert(key, wert) {
        var dd = werte[key];
        if (!dd || dd.textContent === wert) return;
        dd.textContent = wert;
        dd.setAttribute('data-st-gemessen', '');
    }

    function protokolliere(ms, meldung) {
        if (!verlauf) return;
        var li = document.createElement('li');
        var zeit = document.createElement('time');
        zeit.textContent = uhr(ms);
        li.appendChild(zeit);
        li.appendChild(document.createTextNode(' ' + meldung));
        if (!reduced.matches) li.className = 'st-neu';
        verlauf.insertBefore(li, verlauf.firstChild);
        while (verlauf.children.length > 3) {
            verlauf.removeChild(verlauf.lastChild);
        }
    }

    /* aria-live nur polite und gedrosselt: hoechstens eine Ansage je zehn
       Sekunden, und nur fuer Ladezeit und Klick. Scrollen wird nie
       angesagt, sonst redet die Seite bei jeder Bewegung dazwischen. */
    function sage(meldung) {
        if (!ansage) return;
        var t = jetzt();
        if (t - letzteAnsage < ANSAGE_ABSTAND) return;
        letzteAnsage = t;
        ansage.textContent = meldung;
    }

    protokolliere(0, 'Seite aufgerufen');

    /* Ladezeit: LCP, wo der Browser es kennt, sonst Navigation Timing.
       Das Label nennt immer die Groesse, die tatsaechlich gemessen wurde. */
    var lcpWert = 0;
    var lcpFertig = false;
    var lcpKopien = root.querySelectorAll('[data-st-lcp-kopie]');

    function zeigeLadezeit(ms, abschliessen) {
        lcpWert = ms;
        setzeWert('lcp', sekunden(ms));
        Array.prototype.forEach.call(lcpKopien, function (el) {
            el.textContent = sekunden(ms);
            var huelle = el.closest('[data-st-nur-js]');
            if (huelle) huelle.hidden = false;
        });
        if (abschliessen && !lcpFertig) {
            lcpFertig = true;
            protokolliere(ms, 'Größtes Element gezeichnet');
            sage('Ladezeit dieses Aufrufs: ' + sekunden(ms).replace(' s', ' Sekunden') + '.');
        }
    }

    var kannLcp = 'PerformanceObserver' in window &&
        PerformanceObserver.supportedEntryTypes &&
        PerformanceObserver.supportedEntryTypes.indexOf('largest-contentful-paint') !== -1;

    if (kannLcp) {
        var lcpBeobachter = new PerformanceObserver(function (liste) {
            var eintraege = liste.getEntries();
            var letzter = eintraege[eintraege.length - 1];
            if (letzter) zeigeLadezeit(letzter.startTime, false);
        });
        lcpBeobachter.observe({ type: 'largest-contentful-paint', buffered: true });

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
        var label = protokoll && protokoll.querySelector('[data-st-lcp-label]');
        if (label) label.textContent = 'Ladezeit (Seite geladen)';
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

    /* Gesehene Abschnitte: ein Abschnitt zaehlt, sobald er durch das
       mittlere Band des Fensters laeuft. Das funktioniert auch fuer
       Abschnitte, die hoeher sind als der Bildschirm. */
    var abschnitte = root.querySelectorAll('[data-st-abschnitt]');
    var gesehen = {};
    var anzahlGesehen = 0;

    function markiereGesehen(abschnitt) {
        var nr = abschnitt.getAttribute('data-st-abschnitt');
        if (gesehen[nr]) return;
        gesehen[nr] = true;
        anzahlGesehen += 1;
        setzeWert('abschnitte', anzahlGesehen + ' von ' + abschnitte.length);
        if (nr !== '01') {
            protokolliere(jetzt(), 'Abschnitt ' + nr + ' ' + text(abschnitt.querySelector('.st-marke__name')) + ' im Blick');
        }
    }

    if ('IntersectionObserver' in window) {
        var band = new IntersectionObserver(function (eintraege) {
            eintraege.forEach(function (e) {
                if (e.isIntersecting) markiereGesehen(e.target);
            });
        }, { rootMargin: '-45% 0px -45% 0px' });
        Array.prototype.forEach.call(abschnitte, function (a) { band.observe(a); });
    }
    if (abschnitte[0]) markiereGesehen(abschnitte[0]);

    /* Klick auf einen Anfrage-Button. Die Navigation wird nie verzoegert;
       wer ueber die Zurueck-Taste zurueckkommt, sieht den Klick noch im
       Protokoll (bfcache). */
    var ersterKlick = true;
    document.addEventListener('click', function (event) {
        var link = event.target.closest && event.target.closest('a[data-track-category="lead_gen"]');
        if (!link || !root.contains(link)) return;
        var t = jetzt();
        setzeWert('klick', uhr(t));
        protokolliere(t, 'Klick: ' + text(link));
        if (ersterKlick) {
            ersterKlick = false;
            letzteAnsage = -Infinity;
        }
        sage('Klick auf ' + text(link) + ' protokolliert.');
    }, true);

    /* ── 1. Die Linie ───────────────────────────────────────── */

    var fuellungen = [];
    var punkte = [];
    var tiefeMax = 0;
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
        /* Lesepunkt: 60 % der Fensterhoehe. Dort liest man gerade. */
        var anker = y + h * 0.6;

        var tiefe = Math.min(100, Math.round((y + h) / Math.max(1, document.documentElement.scrollHeight) * 100));
        if (tiefe > tiefeMax) {
            tiefeMax = tiefe;
            setzeWert('tiefe', tiefeMax + ' %');
        }

        if (!reduced.matches) {
            fuellungen.forEach(function (f) {
                var anteil = Math.max(0, Math.min(1, (anker - f.oben) / f.hoehe));
                f.el.style.transform = 'scaleY(' + anteil.toFixed(4) + ')';
            });
            punkte.forEach(function (p) {
                var an = anker >= p.y;
                if (an !== p.an) {
                    p.an = an;
                    p.el.classList.toggle('st-erreicht', an);
                }
            });
        }

        var ende = punkte.length ? punkte[punkte.length - 1] : null;
        if (!endeErreicht && ende && anker >= ende.y) {
            endeErreicht = true;
            var t = jetzt();
            if (endeZeit) {
                endeZeit.textContent = 'erreicht nach ' + uhr(t);
                endeZeit.hidden = false;
            }
            protokolliere(t, 'Ende der Strecke erreicht');
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

    /* ── 3. Stationen ───────────────────────────────────────── */

    var stationen = root.querySelector('[data-st-stationen]');
    var knoepfe = [];
    var hoverTimer = 0;

    function oeffne(knopf, nurDieser) {
        knoepfe.forEach(function (k) {
            var an = k === knopf ? (nurDieser ? true : k.getAttribute('aria-expanded') !== 'true') : false;
            k.setAttribute('aria-expanded', an ? 'true' : 'false');
            var detail = document.getElementById(k.getAttribute('aria-controls'));
            if (detail) detail.hidden = !an;
        });
        /* Am Desktop bleibt immer eine Station offen. */
        if (desktop.matches && !knoepfe.some(function (k) { return k.getAttribute('aria-expanded') === 'true'; }) && knoepfe[0]) {
            oeffne(knoepfe[0], true);
            return;
        }
        neuVermessen();
    }

    if (stationen) {
        Array.prototype.forEach.call(stationen.querySelectorAll('[data-st-station-kopf]'), function (kopf, i) {
            var knopf = document.createElement('button');
            knopf.type = 'button';
            knopf.className = 'st-station__knopf';
            knopf.setAttribute('aria-controls', kopf.getAttribute('data-st-ziel'));
            knopf.setAttribute('aria-expanded', i === 0 ? 'true' : 'false');
            while (kopf.firstChild) knopf.appendChild(kopf.firstChild);
            kopf.appendChild(knopf);
            var detail = document.getElementById(kopf.getAttribute('data-st-ziel'));
            if (detail) detail.hidden = i !== 0;
            knoepfe.push(knopf);

            knopf.addEventListener('click', function () {
                window.clearTimeout(hoverTimer);
                oeffne(knopf, desktop.matches);
            });
            knopf.addEventListener('mouseenter', function () {
                if (!desktop.matches || !hoverFein.matches) return;
                window.clearTimeout(hoverTimer);
                hoverTimer = window.setTimeout(function () { oeffne(knopf, true); }, 80);
            });
            knopf.addEventListener('mouseleave', function () { window.clearTimeout(hoverTimer); });
        });

        var zeigeStationen = function () { stationen.setAttribute('data-st-gesehen', ''); };
        if (reduced.matches || !('IntersectionObserver' in window)) {
            zeigeStationen();
        } else {
            var sicht = new IntersectionObserver(function (eintraege) {
                if (eintraege.some(function (e) { return e.isIntersecting; })) {
                    zeigeStationen();
                    sicht.disconnect();
                }
            }, { threshold: 0.15 });
            sicht.observe(stationen);
        }

        var wechsel = function () {
            if (desktop.matches && !knoepfe.some(function (k) { return k.getAttribute('aria-expanded') === 'true'; }) && knoepfe[0]) {
                oeffne(knoepfe[0], true);
            }
        };
        if (desktop.addEventListener) desktop.addEventListener('change', wechsel);
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

    /* ── Start ──────────────────────────────────────────────── */

    root.setAttribute('data-st-bereit', '');
    vermessen();
    aktualisieren();

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
            punkte.forEach(function (p) { p.an = false; p.el.classList.remove('st-erreicht'); });
            planen();
        });
    }
})();
