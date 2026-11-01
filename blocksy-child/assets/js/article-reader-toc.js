/* =============================================================
   Article Reader TOC V2 — "Kapitelskala"

   NexusCore (nexus-core.js) fuellt #toc-list mit allen H2 und H3 und
   vergibt fehlende IDs. Dieses Skript baut daraus das Inhaltsverzeichnis,
   das der Leser tatsaechlich braucht:

   - Nur die H2 sind Kapitel, nummeriert 01 bis n. Die H3 stehen nur unter
     dem aktiven Kapitel. So passt das Verzeichnis ohne eigene Scrollleiste
     in die Fensterhoehe.
   - Eine Haarlinie links zeigt den Lesefortschritt, der Kopf zeigt
     "03 / 10", der Fuss die restliche Lesezeit.
   - Ein H2 mit data-toc="…" erscheint mit dieser Kurzbezeichnung, sonst
     mit seinem Text (auf zwei Zeilen begrenzt).
   - Unter 981 px wird das Verzeichnis zur schmalen Leiste unter der
     Kopfleiste: "03 / 10 · Kapitel", ein Tipp oeffnet die Kapitel.

   Der Essay mit eigenem Lesepfad (.hu-design-toc) und die Entscheidungs-
   beitraege ohne #article-content behalten das Klappverhalten aus
   Version 1. Ohne JS bleibt die Liste von NexusCore, ohne
   IntersectionObserver oder bei prefers-reduced-motion entfallen nur die
   Uebergaenge. Nichts wird gespeichert, nichts gesendet.
   ============================================================= */
(function () {
    'use strict';

    if (typeof document === 'undefined') return;

    var MOBIL = '(max-width: 980px)';
    var WOERTER_PRO_MINUTE = 200;

    function zweistellig(n) {
        return (n < 10 ? '0' : '') + n;
    }

    function text(node) {
        return (node.textContent || '').replace(/\s+/g, ' ').trim();
    }

    /* ---------------------------------------------------------
       Version 1 (Klappverhalten), nur fuer Essay und Entscheidungsbeitraege.
       --------------------------------------------------------- */
    function initLegacy(shell, tocList) {
        var toggle = document.createElement('button');
        toggle.type = 'button';
        toggle.className = 'nexus-reader-toc__toggle';
        toggle.setAttribute('aria-expanded', 'false');
        toggle.setAttribute('aria-controls', tocList.id || 'toc-list');
        toggle.innerHTML = '<span class="nexus-reader-toc__toggle-copy"><span class="nexus-reader-toc__toggle-label">Inhalt</span><span class="nexus-reader-toc__toggle-count" aria-live="polite"></span></span><span class="nexus-reader-toc__toggle-mark" aria-hidden="true">+</span>';
        shell.insertBefore(toggle, shell.firstChild);

        function hydrate() {
            var anzahl = 0;
            Array.prototype.forEach.call(tocList.children, function (item) {
                var unter = parseFloat(item.style.marginLeft || '0') > 0;
                item.classList.toggle('is-subsection', unter);
                item.style.marginLeft = '';
                item.style.fontSize = '';
                if (!unter) anzahl += 1;
            });
            var count = toggle.querySelector('.nexus-reader-toc__toggle-count');
            if (count) count.textContent = anzahl ? '· ' + anzahl + ' Abschnitte' : '';
        }

        toggle.addEventListener('click', function () {
            var offen = !shell.classList.contains('is-mobile-open');
            shell.classList.toggle('is-mobile-open', offen);
            toggle.setAttribute('aria-expanded', offen ? 'true' : 'false');
        });

        new MutationObserver(hydrate).observe(tocList, { childList: true });
        hydrate();
    }

    /* ---------------------------------------------------------
       Version 2
       --------------------------------------------------------- */

    /* Ueberschriften ohne ID bekommen einen lesbaren Anker aus ihrem Text
       ("die-rueckkopplung-macht-den-unterschied"), damit Kapitel teilbar
       sind. NexusCore vergibt danach keine eigenen mehr, weil es nur
       fehlende IDs setzt. */
    function idsVergeben(content) {
        var vergeben = {};
        Array.prototype.forEach.call(document.querySelectorAll('[id]'), function (n) { vergeben[n.id] = true; });
        Array.prototype.forEach.call(content.querySelectorAll('h2:not([id]), h3:not([id])'), function (h) {
            var basis = text(h).toLowerCase()
                .replace(/ä/g, 'ae').replace(/ö/g, 'oe').replace(/ü/g, 'ue').replace(/ß/g, 'ss')
                .replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '').slice(0, 60) || 'abschnitt';
            var id = basis;
            var i = 2;
            while (vergeben[id]) { id = basis + '-' + i; i += 1; }
            vergeben[id] = true;
            h.id = id;
        });
    }
    function initKapitelskala(aside, shell, tocList, content) {
        var media = window.matchMedia ? window.matchMedia(MOBIL) : null;
        var bewegung = window.matchMedia ? window.matchMedia('(prefers-reduced-motion: reduce)') : null;
        var kopfleiste = document.querySelector('[data-leiste]');

        var kapitel = [];
        var aktivIndex = -2;
        var aktivUnter = null;
        var woerter = 0;
        var gebaut = false;
        var wartet = false;

        var liste, stand, rest, leiste, leisteNr, leisteTitel, fuellung;

        function istMobil() {
            return media ? media.matches : window.innerWidth <= 980;
        }

        function bauen() {
            idsVergeben(content);
            var ueberschriften = content.querySelectorAll('h2[id], h3[id]');
            if (!ueberschriften.length) return false;

            kapitel = [];
            Array.prototype.forEach.call(ueberschriften, function (h) {
                if (h.closest('[data-hu-schema-skip]')) return;
                if (h.tagName === 'H2') {
                    kapitel.push({ h: h, titel: h.getAttribute('data-toc') || text(h), unter: [] });
                } else if (kapitel.length) {
                    kapitel[kapitel.length - 1].unter.push({ h: h, titel: h.getAttribute('data-toc') || text(h) });
                }
            });
            if (!kapitel.length) return false;

            woerter = text(content).split(' ').length;

            aside.classList.add('hu-toc-aside');
            shell.classList.add('hu-toc');

            /* Kopf */
            var kopf = document.createElement('div');
            kopf.className = 'hu-toc__kopf';
            kopf.innerHTML = '<span class="hu-toc__label">Inhalt</span><span class="hu-toc__stand" aria-hidden="true"></span>';
            stand = kopf.querySelector('.hu-toc__stand');

            /* Mobile Leiste */
            leiste = document.createElement('button');
            leiste.type = 'button';
            leiste.className = 'hu-toc__leiste';
            leiste.setAttribute('aria-expanded', 'false');
            leiste.setAttribute('aria-controls', 'hu-toc-liste');
            leiste.innerHTML = '<span class="hu-toc__leiste-nr" aria-hidden="true"></span><span class="hu-toc__leiste-titel"></span><span class="hu-toc__leiste-mark" aria-hidden="true"></span><span class="hu-toc__leiste-linie" aria-hidden="true"><span></span></span>';
            leisteNr = leiste.querySelector('.hu-toc__leiste-nr');
            leisteTitel = leiste.querySelector('.hu-toc__leiste-titel');

            /* Liste */
            var skala = document.createElement('div');
            skala.className = 'hu-toc__skala';
            skala.innerHTML = '<span class="hu-toc__schiene" aria-hidden="true"><span class="hu-toc__fuellung"></span></span>';
            fuellung = skala.querySelector('.hu-toc__fuellung');

            liste = document.createElement('ol');
            liste.className = 'hu-toc__liste';
            liste.id = 'hu-toc-liste';

            kapitel.forEach(function (k, i) {
                var li = document.createElement('li');
                li.className = 'hu-toc__kapitel';

                var a = document.createElement('a');
                a.className = 'hu-toc__link';
                a.href = '#' + k.h.id;
                a.innerHTML = '<span class="hu-toc__nr" aria-hidden="true">' + zweistellig(i + 1) + '</span><span class="hu-toc__text"></span>';
                a.querySelector('.hu-toc__text').textContent = k.titel;
                if (k.titel !== text(k.h)) a.setAttribute('title', text(k.h));
                li.appendChild(a);
                k.li = li;
                k.a = a;

                if (k.unter.length) {
                    var huelle = document.createElement('div');
                    huelle.className = 'hu-toc__unter-huelle';
                    var unter = document.createElement('ol');
                    unter.className = 'hu-toc__unter';
                    k.unter.forEach(function (u) {
                        var uli = document.createElement('li');
                        var ua = document.createElement('a');
                        ua.className = 'hu-toc__unterlink';
                        ua.href = '#' + u.h.id;
                        ua.textContent = u.titel;
                        uli.appendChild(ua);
                        unter.appendChild(uli);
                        u.a = ua;
                    });
                    huelle.appendChild(unter);
                    li.appendChild(huelle);
                    k.huelle = huelle;
                }

                liste.appendChild(li);
            });

            skala.appendChild(liste);

            var fuss = document.createElement('p');
            fuss.className = 'hu-toc__fuss';
            fuss.innerHTML = '<span class="hu-toc__rest" aria-live="off"></span>';
            rest = fuss.querySelector('.hu-toc__rest');

            var label = shell.querySelector(':scope > h2');
            if (label) label.classList.add('hu-toc__alt');
            tocList.hidden = true;

            shell.appendChild(kopf);
            shell.appendChild(leiste);
            shell.appendChild(skala);
            shell.appendChild(fuss);

            leiste.addEventListener('click', function () {
                setzeOffen(!shell.classList.contains('is-offen'));
            });

            liste.addEventListener('click', function (e) {
                var link = e.target.closest('a');
                if (!link) return;
                var ziel = document.getElementById(link.getAttribute('href').slice(1));
                if (!ziel) return;
                e.preventDefault();
                setzeOffen(false);
                ziel.scrollIntoView({ behavior: bewegung && bewegung.matches ? 'instant' : 'smooth', block: 'start' });
                if (window.history && window.history.replaceState) {
                    window.history.replaceState(null, '', '#' + ziel.id);
                }
                ziel.setAttribute('tabindex', '-1');
                ziel.focus({ preventScroll: true });
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && shell.classList.contains('is-offen')) {
                    setzeOffen(false);
                    leiste.focus();
                }
            });

            document.addEventListener('click', function (e) {
                if (shell.classList.contains('is-offen') && !shell.contains(e.target)) setzeOffen(false);
            });

            if (media && typeof media.addEventListener === 'function') {
                media.addEventListener('change', function () { setzeOffen(false); messen(); });
            }

            gebaut = true;
            return true;
        }

        function setzeOffen(offen) {
            shell.classList.toggle('is-offen', offen);
            leiste.setAttribute('aria-expanded', offen ? 'true' : 'false');
        }

        // Visually collapsed subsections must also leave the tab order and
        // accessibility tree. Never hide a focused link while reading scrolls.
        function unterpunkteAbgleichen() {
            kapitel.forEach(function (k, i) {
                if (!k.huelle) return;
                var verborgen = istMobil() || (i !== aktivIndex && !k.huelle.contains(document.activeElement));
                k.huelle.hidden = verborgen;
            });
        }

        var oben = 0;

        function messen() {
            oben = kopfleiste ? Math.max(0, Math.round(kopfleiste.getBoundingClientRect().bottom)) : 0;
            if (kopfleiste && window.getComputedStyle(kopfleiste).position !== 'sticky' && window.getComputedStyle(kopfleiste).position !== 'fixed') {
                oben = 0;
            }
            document.documentElement.style.setProperty('--hu-toc-oben', oben + 'px');
            if (!gebaut) return;
            unterpunkteAbgleichen();
            kapitel.forEach(function (k) {
                k.y = k.h.getBoundingClientRect().top + window.scrollY;
                k.unter.forEach(function (u) { u.y = u.h.getBoundingClientRect().top + window.scrollY; });
            });
            aktualisieren();
        }

        function aktualisieren() {
            wartet = false;
            if (!gebaut) return;

            var linie = window.scrollY + oben + (istMobil() ? 120 : 48);
            var neu = -1;
            kapitel.forEach(function (k, i) { if (linie >= k.y) neu = i; });

            var neuUnter = null;
            if (neu >= 0) {
                kapitel[neu].unter.forEach(function (u) { if (linie >= u.y) neuUnter = u; });
            }

            if (neu !== aktivIndex) {
                aktivIndex = neu;
                kapitel.forEach(function (k, i) {
                    k.li.classList.toggle('is-aktiv', i === neu);
                    k.li.classList.toggle('is-gelesen', i < neu);
                    if (i === neu) {
                        k.a.setAttribute('aria-current', 'location');
                    } else {
                        k.a.removeAttribute('aria-current');
                    }
                });
                var i = Math.max(0, neu);
                stand.textContent = zweistellig(neu + 1 > 0 ? neu + 1 : 0) + ' / ' + zweistellig(kapitel.length);
                leisteNr.textContent = neu >= 0 ? zweistellig(neu + 1) + ' / ' + zweistellig(kapitel.length) : zweistellig(kapitel.length) + ' Kapitel';
                leisteTitel.textContent = neu >= 0 ? kapitel[i].titel : 'Inhalt';
                if (!istMobil()) sichtbarHalten(kapitel[i].li);
                unterpunkteAbgleichen();
            }

            if (neuUnter !== aktivUnter) {
                if (aktivUnter && aktivUnter.a) aktivUnter.a.classList.remove('is-aktiv');
                aktivUnter = neuUnter;
                if (aktivUnter && aktivUnter.a) aktivUnter.a.classList.add('is-aktiv');
            }

            var start = content.getBoundingClientRect().top + window.scrollY - oben;
            var hoehe = Math.max(1, content.offsetHeight - window.innerHeight * 0.6);
            var anteil = Math.min(1, Math.max(0, (window.scrollY - start) / hoehe));
            shell.style.setProperty('--hu-toc-fortschritt', anteil.toFixed(4));

            var minuten = Math.ceil((woerter * (1 - anteil)) / WOERTER_PRO_MINUTE);
            rest.textContent = anteil > 0.98 ? 'Zu Ende gelesen' : (anteil < 0.01 ? minuten + ' Min. Lesezeit' : 'Noch ' + minuten + ' Min.');
        }

        function sichtbarHalten(li) {
            if (!liste || liste.scrollHeight <= liste.clientHeight) return;
            var rahmen = liste.getBoundingClientRect();
            var box = li.getBoundingClientRect();
            if (box.top < rahmen.top || box.bottom > rahmen.bottom) {
                liste.scrollTop += box.top - rahmen.top - rahmen.height / 3;
            }
        }

        function vormerken() {
            if (wartet) return;
            wartet = true;
            window.requestAnimationFrame(aktualisieren);
        }

        function starten() {
            if (gebaut || !bauen()) return;
            messen();
            liste.addEventListener('focusout', function () {
                window.requestAnimationFrame(unterpunkteAbgleichen);
            });
            window.addEventListener('scroll', vormerken, { passive: true });
            window.addEventListener('resize', messen, { passive: true });
            window.addEventListener('load', messen, { once: true });
            if ('ResizeObserver' in window) new ResizeObserver(messen).observe(content);
        }

        starten();
    }

    function init() {
        var tocList = document.querySelector('.nexus-sidebar .sticky-toc #toc-list');
        var content = document.getElementById('article-content');
        if (!document.querySelector('[data-article-system]') || !tocList) return;

        var shell = tocList.closest('.sticky-toc');
        var aside = shell ? shell.closest('.nexus-sidebar') : null;
        if (!shell || !aside) return;

        aside.classList.add('nexus-reader-toc');
        shell.classList.add('nexus-reader-toc__shell');

        if (aside.classList.contains('hu-design-toc') || !content) {
            initLegacy(shell, tocList);
            return;
        }

        initKapitelskala(aside, shell, tocList, content);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
