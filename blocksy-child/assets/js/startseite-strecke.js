/* Startseite: die Strecke.
 *
 * Sechs Aufgaben, ein Skript:
 * 1. Die Linie in der Randspalte fuellt sich beim Lesen (transform: scaleY,
 *    Update in requestAnimationFrame, Geometrie nur bei Groessenaenderung).
 *    Die Lesekante liegt bei 45 % der Fensterhoehe; am Ende ist alles erreicht.
 * 2. Das Messprotokoll im Hero zeigt, was dieser Browser misst: Ladezeit,
 *    gesehene Abschnitte, Scrolltiefe, Klick auf einen Anfrage-Button.
 * 3. Der Hero zeigt Herkunftsetikett und Signal auf der Bahn, nach den Schriften.
 * 4. Native Stationen klappen exklusiv auf; die Messtafel erscheint einmal.
 * 5. „Leistungen" in der Kopfleiste ist nur aktiv, solange #angebote im
 *    Blick ist.
 * 6. Auf Seiten mit data-st-einblenden blenden Bloecke unter dem ersten
 *    Bildschirm beim ersten Sichtkontakt ein.
 *
 * Die Hero-Choreografie laeuft einmal, die Balken beim ersten Sichtkontakt.
 * Bei reduzierter Bewegung steht der Hero sofort im gemessenen Endzustand;
 * die Linie folgt ohne Uebergaenge dem Scrollstand.
 *
 * Harte Regel: Dieses Skript sendet nichts und speichert nichts. Kein
 * Netzwerkaufruf, kein Browser-Speicher, kein Cookie. Die Sperrliste in
 * scripts/canon-forbidden-values.txt (Regel strecke-js-privat) bricht den
 * Build, sobald hier einer dieser Aufrufe auftaucht.
 *
 * Ohne dieses Skript steht alles: Linie statisch, Stationen nativ bedienbar, das
 * Protokoll sagt, dass es leer bleibt.
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

    function text(el) {
        return (el && el.textContent || '').replace(/[→↗]/g, '').replace(/\s+/g, ' ').trim();
    }

    /* ── 2. Messprotokoll ───────────────────────────────────── */

    var protokoll = root.querySelector('[data-st-protokoll]');
    var werte = {};
    var ansage = protokoll && protokoll.querySelector('[data-st-ansage]');
    var letzteAnsage = -Infinity;
    var ANSAGE_ABSTAND = 10000;

    if (protokoll) {
        ['herkunft', 'lcp', 'abschnitte', 'tiefe', 'klick'].forEach(function (key) {
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

    /* Kampagnenparameter haben Vorrang vor dem Verweis. Nur Textausgabe:
       auch untrusted Query-Werte werden weder HTML noch Link oder Anfrage. */
    if (protokoll) {
        var parameter = new URLSearchParams(window.location.search);
        var quelle = parameter.get('utm_source');
        var medium = parameter.get('utm_medium');
        var herkunft = '';
        if (quelle) {
            herkunft = 'Kampagne: ' + quelle + (medium ? ' / ' + medium : '');
        } else {
            var host = '';
            try { host = document.referrer ? new URL(document.referrer).hostname.replace(/^www\./, '') : ''; } catch (e) {}
            var namen = [
                [/(^|\.)google\.[a-z.]+$/, 'Google-Suche'],
                [/(^|\.)bing\.com$/, 'Bing-Suche'],
                [/(^|\.)duckduckgo\.com$/, 'DuckDuckGo'],
                [/(^|\.)ecosia\.org$/, 'Ecosia'],
                [/(^|\.)(linkedin\.com|lnkd\.in)$/, 'LinkedIn'],
                [/(^|\.)(chatgpt\.com|openai\.com)$/, 'ChatGPT'],
                [/(^|\.)perplexity\.ai$/, 'Perplexity']
            ];
            herkunft = !host ? 'Direkt aufgerufen' : host === window.location.hostname.replace(/^www\./, '') ? 'Diese Website' : host;
            namen.some(function (paar) {
                if (!paar[0].test(host)) return false;
                herkunft = paar[1];
                return true;
            });
        }
        // Der volle Wert bleibt in Station 01 fuer Maus und Hilfstechnik da.
        herkunft = herkunft.replace(/[\x00-\x1f\x7f]/g, ' ').slice(0, 200);
        setzeWert('herkunft', herkunft);
        if (werte.herkunft) werte.herkunft.title = herkunft;
    }

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
            sage('Ladezeit dieses Aufrufs: ' + sekunden(ms).replace(' s', ' Sekunden') + '.');
        }
    }

    var kannLcp = typeof PerformanceObserver === 'function' &&
        PerformanceObserver.supportedEntryTypes &&
        PerformanceObserver.supportedEntryTypes.indexOf('largest-contentful-paint') !== -1;

    var lcpBeobachter;
    if (kannLcp) try {
        lcpBeobachter = new PerformanceObserver(function (liste) {
            var eintraege = liste.getEntries();
            var letzter = eintraege[eintraege.length - 1];
            if (letzter) zeigeLadezeit(letzter.startTime, false);
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

    /* Erreichte Abschnitte und Strahl verwenden dieselbe Lesekante. */
    var abschnitte = root.querySelectorAll('[data-st-abschnitt]');
    var gesehen = {};
    var anzahlGesehen = 0;
    function markiereGesehen(abschnitt) {
        var nr = abschnitt.getAttribute('data-st-abschnitt');
        if (gesehen[nr]) return;
        gesehen[nr] = true;
        anzahlGesehen += 1;
        setzeWert('abschnitte', anzahlGesehen + ' von ' + abschnitte.length);
    }
    setzeWert('abschnitte', '0 von ' + abschnitte.length);
    setzeWert('tiefe', '0 %');
    setzeWert('klick', 'noch offen');

    /* Klick auf einen Anfrage-Button. Die Navigation wird nie verzoegert;
       wer ueber die Zurueck-Taste zurueckkommt, sieht den Klick noch im
       Protokoll (bfcache). */
    var ersterKlick = true;
    document.addEventListener('click', function (event) {
        var link = event.target.closest && event.target.closest('[data-track-category="lead_gen"]');
        if (!link) return;
        var ort = { header: 'Kopf', hero: 'Einstieg', beweis: 'Fall', angebote: 'Preise', abschluss: 'Ende' }[link.dataset.trackSection];
        if (!ort) return;
        setzeWert('klick', 'geöffnet · ' + ort);
        var formularPunkt = root.querySelector('[data-st-spur-station="2"]');
        if (formularPunkt) formularPunkt.classList.add('is-reached');
        if (ersterKlick) {
            ersterKlick = false;
            letzteAnsage = -Infinity;
        }
        sage('Klick auf ' + text(link) + ' protokolliert.');
    }, true);

    /* ── Hero: Signal und Herkunftsetikett, rein lokal ───────── */
    var hero = root.querySelector('[data-st-hero]');
    var titel = hero && hero.querySelector('[data-st-titel]');
    var quellWort = hero && hero.querySelector('[data-st-wort-quelle]');
    var etikett = hero && hero.querySelector('[data-st-etikett]');
    var bahn = hero && hero.querySelector('[data-st-bahn]');
    var signal = hero && hero.querySelector('[data-st-signal]');
    var spurLinie = hero && hero.querySelector('[data-st-spur-linie]');
    var spurFuell = hero && hero.querySelector('[data-st-spur-fuell]');
    var spurStationen = hero ? Array.prototype.slice.call(hero.querySelectorAll('[data-st-spur-station]')) : [];
    var stand = -1;
    var lauf = 0;
    var signalAnimation;
    var fuellAnimation;
    var spurPunkte = [];
    var fuellStand = 0;
    var etikettGeometrie = '';
    // Gecko-Korrektur: die abgestimmte Platzierung anderer Engines bleibt.
    var firefoxEtikett = window.CSS && CSS.supports && CSS.supports('-moz-appearance', 'none');

    function senkrecht() { return window.innerWidth < 768; }

    function etikettMasse() {
        return [titel.clientWidth, quellWort.offsetLeft, quellWort.offsetTop,
            quellWort.offsetWidth, quellWort.offsetHeight,
            etikett.offsetWidth, etikett.offsetHeight, senkrecht()].join(':');
    }

    function legeEtikett() {
        if (!etikett || !quellWort || !titel) return;
        etikett.classList.remove('st-etikett--unten');
        titel.style.paddingBottom = '';
        etikett.style.left = '';
        etikett.style.top = '';
        etikettGeometrie = etikettMasse();
        if (senkrecht()) return; // Im Fluss unter der H1, keine Leitlinie.
        var t = titel.getBoundingClientRect();
        var w = quellWort.getBoundingClientRect();
        // CSS-Positionen und Messwerte bleiben im selben Koordinatensystem.
        // Skalierte Viewport-Rechtecke duerfen nicht mit offsetWidth und
        // CSS-Pixeln gemischt werden: sonst wandert das Etikett in die CTA.
        // Die Maske selbst wird nicht animiert: ihr y ist auch waehrend
        // des Aufstiegs die endgueltige Lage der vierten Zeile.
        var zeile = quellWort.closest('.st-messzeile');
        var z = zeile.getBoundingClientRect();
        var breite = etikett.offsetWidth;
        var hoehe = etikett.offsetHeight;
        var wortEnde = firefoxEtikett ? quellWort.offsetLeft + quellWort.offsetWidth : w.right - t.left;
        var zeilenTop = firefoxEtikett ? zeile.offsetTop : z.top - t.top;
        var zeilenHoehe = firefoxEtikett ? zeile.offsetHeight : z.height;
        var titelBreite = firefoxEtikett ? titel.clientWidth : t.width;
        if (window.innerWidth >= 1280 && wortEnde + 34 + breite <= titelBreite) {
            etikett.style.left = (wortEnde + 34) + 'px';
            etikett.style.top = (zeilenTop + (zeilenHoehe - hoehe) / 2) + 'px';
        } else {
            etikett.classList.add('st-etikett--unten');
            etikett.style.left = Math.max(0, wortEnde - breite) + 'px';
            etikett.style.top = (zeilenTop + zeilenHoehe + 26) + 'px';
            // Auch der tiefste Ausschlag bleibt oberhalb der Hauptaktion.
            var schwung = !firefoxEtikett || reduced.matches ? 0 : Math.ceil(Math.max(0, breite - 28) * Math.sin(24 * Math.PI / 180));
            titel.style.paddingBottom = (hoehe + 26 + schwung) + 'px';
        }
    }

    function vermesseSpur() {
        if (!bahn || !spurStationen.length) return;
        var b = bahn.getBoundingClientRect();
        spurPunkte = spurStationen.map(function (station) {
            var p = station.querySelector('.st-spur__punkt').getBoundingClientRect();
            return { x: p.left - b.left + p.width / 2, y: p.top - b.top + p.height / 2 };
        });
        var erster = spurPunkte[0];
        var letzter = spurPunkte[spurPunkte.length - 1];
        spurLinie.style.left = erster.x + 'px';
        spurLinie.style.top = erster.y + 'px';
        spurLinie.style.width = senkrecht() ? '1px' : (letzter.x - erster.x) + 'px';
        spurLinie.style.height = senkrecht() ? (letzter.y - erster.y) + 'px' : '1px';
    }

    function signalLage(i) {
        var p = spurPunkte[i];
        return 'translate(' + (p.x - 6.5) + 'px, ' + (p.y - 6.5) + 'px)';
    }

    function fuellLage(wert) { return (senkrecht() ? 'scaleY(' : 'scaleX(') + wert + ')'; }

    function stoppeSignal() {
        if (signalAnimation) signalAnimation.cancel();
        if (fuellAnimation) fuellAnimation.cancel();
        signalAnimation = fuellAnimation = null;
    }

    function setzeSignal(i, dauer) {
        if (!signal || !spurPunkte[i]) return Promise.resolve();
        var von = stand < 0 ? i : stand;
        var achse = senkrecht() ? 'y' : 'x';
        var start = spurPunkte[0][achse];
        var gesamt = spurPunkte[spurPunkte.length - 1][achse] - start;
        var anteil = Math.max(0, Math.min(1, (spurPunkte[i][achse] - start) / Math.max(1, gesamt)));
        var vorher = fuellStand;
        stoppeSignal();
        stand = i;
        fuellStand = anteil;
        signal.style.transform = signalLage(i);
        spurFuell.style.transform = fuellLage(anteil);
        if (!dauer || reduced.matches || !signal.animate) return Promise.resolve();
        // Der Endzustand steht bereits inline. Keine fill-forwards-Schicht,
        // die nach resize die neu vermessene Lage ueberschreibt.
        signalAnimation = signal.animate([{ transform: signalLage(von) }, { transform: signalLage(i) }],
            { duration: dauer, easing: 'cubic-bezier(.65, 0, .35, 1)' });
        fuellAnimation = spurFuell.animate([{ transform: fuellLage(vorher) }, { transform: fuellLage(anteil) }],
            { duration: dauer, easing: 'cubic-bezier(.65, 0, .35, 1)' });
        return signalAnimation.finished.catch(function () {}); // resize darf abbrechen.
    }

    function haengeEtikett() {
        if (!etikett) return;
        etikett.classList.add('is-hung');
        hero.classList.add('is-assigned');
    }

    function heroEndzustand() {
        if (!hero || !spurPunkte.length) return;
        lauf += 1;
        stoppeSignal();
        hero.classList.add('is-started');
        haengeEtikett();
        spurStationen[0].classList.add('is-reached');
        spurStationen[1].classList.add('is-reached');
        signal.hidden = false;
        signal.classList.add('is-visible', 'is-here');
        signal.classList.remove('is-pulsing');
        setzeSignal(2, 0);
    }

    function heroNeuVermessen() {
        if (!hero || !hero.hasAttribute('data-st-hero-bereit')) return;
        legeEtikett();
        vermesseSpur();
        // Ein Groessenwechsel unterbricht die Choreografie und zeigt den
        // Endzustand. Es bleibt nie eine Animation auf der alten Achse liegen.
        if (stand >= 0) heroEndzustand();
    }

    function warte(ms) { return new Promise(function (resolve) { window.setTimeout(resolve, ms); }); }

    async function starteHero() {
        if (!hero || !titel || !etikett || !signal || spurStationen.length < 3) return;
        // Erst wenn die Messung eingerichtet ist, uebernimmt CSS die Maske.
        // Beim Ausfall von JS bleiben alle vier Zeilen voll lesbar.
        etikett.hidden = false;
        etikett.querySelector('[data-st-etikett-wert]').textContent = werte.herkunft ? werte.herkunft.textContent : '…';
        etikett.querySelector('[data-st-etikett-fuss]').textContent = 'zugeordnet ' + new Date().toLocaleTimeString('de-DE', { hour: '2-digit', minute: '2-digit' }) + ' · ohne Cookie';
        hero.setAttribute('data-st-hero-bereit', '');
        legeEtikett();
        vermesseSpur();
        neuVermessen();
        if (reduced.matches) {
            await warte(200);
            heroEndzustand();
            return;
        }
        var dieserLauf = ++lauf;
        // Ein Frame fuer den Masken-Startzustand, dann erst die Transition.
        await new Promise(function (resolve) { window.requestAnimationFrame(function () { window.requestAnimationFrame(resolve); }); });
        if (lauf !== dieserLauf) return;
        hero.classList.add('is-started');
        await warte(450);
        if (lauf !== dieserLauf) return;
        spurStationen[0].classList.add('is-reached');
        signal.hidden = false;
        setzeSignal(0, 0);
        signal.classList.add('is-visible');
        await warte(350);
        if (lauf !== dieserLauf) return;
        haengeEtikett();
        await warte(500);
        if (lauf !== dieserLauf) return;
        await setzeSignal(1, 500);
        if (lauf !== dieserLauf) return;
        spurStationen[1].classList.add('is-reached');
        await setzeSignal(2, 600);
        if (lauf !== dieserLauf) return;
        signal.classList.add('is-here', 'is-pulsing');
    }

    // Auch Direktlinks und Zurueck/Vorwaerts oeffnen die richtige Station.
    // Der native Fragment-Sprung bleibt erhalten; Tastaturfokus geht an summary.
    function oeffneStation(hash, fokus) {
        var summary = hash && document.getElementById(hash.slice(1));
        if (!summary || !summary.matches('#angebot-funnel summary')) return;
        summary.closest('details').open = true;
        if (fokus) summary.focus({ preventScroll: true });
        neuVermessen();
    }
    document.addEventListener('click', function (event) {
        var link = event.target.closest && event.target.closest('[data-st-station-link]');
        if (link) oeffneStation(link.hash, true);
    });
    window.addEventListener('hashchange', function () { oeffneStation(window.location.hash, true); });
    oeffneStation(window.location.hash, false);

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
            return { el: el, y: r.top + y + r.height / 2, an: el.classList.contains('st-erreicht'), abschnitt: el.closest('[data-st-abschnitt]') };
        });

    }

    function aktualisieren() {
        geplant = false;
        var y = window.scrollY || window.pageYOffset || 0;
        var h = window.innerHeight || document.documentElement.clientHeight;
        var max = Math.max(0, document.documentElement.scrollHeight - h);
        var unten = y >= max - 4;
        var anker = y + (unten ? h : h * 0.45);
        var tiefe = unten || !max ? 100 : Math.max(0, Math.min(100, Math.round(y / max * 100)));
        if (tiefe > tiefeMax) {
            tiefeMax = tiefe;
            setzeWert('tiefe', tiefeMax + ' %');
        }
        // Direkte Transform-Updates auch bei reduzierter Bewegung, ohne Uebergang.
        fuellungen.forEach(function (f) {
            var anteil = unten ? 1 : Math.max(0, Math.min(1, (anker - f.oben) / f.hoehe));
            f.el.style.transform = 'scaleY(' + anteil.toFixed(4) + ')';
        });
        punkte.forEach(function (p) {
            if (p.an || (!unten && anker < p.y)) return;
            p.an = true;
            p.el.classList.add('st-erreicht');
            if (p.abschnitt) markiereGesehen(p.abschnitt);
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

    /* ── 3. Native Akkordeons und Messtafel ──────────────────── */
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
       Auftritt (3.). */
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
            '.st-tafel__kopf, .st-pruefungen > li, .st-schluss > .st-anfrage__h2, .st-einstiege > *, .st-schluss > .st-portrait'
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
    window.addEventListener('resize', function () { neuVermessen(); heroNeuVermessen(); }, { passive: true });
    window.addEventListener('load', neuVermessen, { once: true });
    if ('ResizeObserver' in window) {
        new ResizeObserver(function () { neuVermessen(); }).observe(root);
        if (firefoxEtikett && titel && quellWort && etikett) {
            var etikettBeobachter = new ResizeObserver(function () {
                // Spaete Schrift-/Layoutwechsel brauchen keinen Fenster-Resize.
                // Die Platzreserve und Transform-Animationen veraendern diese
                // Messwerte nicht und starten keine Beobachterschleife.
                if (etikettGeometrie && etikettMasse() !== etikettGeometrie) heroNeuVermessen();
            });
            [titel, quellWort, etikett].forEach(function (el) { etikettBeobachter.observe(el); });
        }
    }
    if (document.fonts && document.fonts.ready) {
        document.fonts.ready.then(function () { neuVermessen(); starteHero(); });
    } else starteHero();
    if (reduced.addEventListener) {
        reduced.addEventListener('change', function () {
            if (reduced.matches && messtafel) messtafel.setAttribute('data-st-gesehen', '');
            if (reduced.matches && hero && hero.hasAttribute('data-st-hero-bereit')) heroEndzustand();
            planen();
        });
    }
})();
