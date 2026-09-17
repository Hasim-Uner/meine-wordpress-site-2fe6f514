/**
 * Kopfleiste.
 *
 * Zwei Aufgaben, mehr nicht: das Klappblatt auf schmalen Schirmen oeffnen
 * und schliessen, und die gemessene Hoehe der Leiste als --leiste-h
 * veroeffentlichen, damit Sprungziele und seitenlokale sticky-Elemente
 * dagegen rechnen koennen.
 *
 * Progressive Enhancement: CSS haelt das Klappblatt bei aktivem Scripting
 * bereits beim ersten Paint geschlossen. Ohne Scripting zeigt die
 * @media-(scripting:none)-Regel die Navigation weiterhin offen an.
 */
(function () {
    'use strict';

    var BREITE_AB = 1081;

    function init() {
        var leiste = document.querySelector('[data-leiste]');

        if (!leiste || leiste.hasAttribute('data-leiste-bereit')) {
            return;
        }

        var klappe = leiste.querySelector('[data-leiste-klappe]');
        var blatt = leiste.querySelector('[data-leiste-blatt]');

        beobachteHoehe(leiste);

        if (!klappe || !blatt) {
            leiste.setAttribute('data-leiste-bereit', '');
            return;
        }

        /* Erst ab hier ist das Klappblatt eine Klappe. CSS hat den schmalen
           Zustand bereits vor JavaScript aus dem Layout genommen; das hidden-
           Attribut ist deshalb nur noch der semantische/interaktive Zustand
           und verursacht keinen spaeten Layoutsprung mehr. */
        leiste.setAttribute('data-leiste-bereit', '');
        schliessen(leiste, klappe, blatt, false);

        klappe.addEventListener('click', function () {
            if (leiste.classList.contains('offen')) {
                schliessen(leiste, klappe, blatt, true);
            } else {
                oeffnen(leiste, klappe, blatt);
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key !== 'Escape' || !leiste.classList.contains('offen')) {
                return;
            }

            schliessen(leiste, klappe, blatt, true);
        });

        document.addEventListener('click', function (event) {
            if (!leiste.classList.contains('offen') || leiste.contains(event.target)) {
                return;
            }

            schliessen(leiste, klappe, blatt, false);
        });

        /* Wenn Tastaturnutzer die geoeffnete Navigation verlassen, bleibt
           kein grosses Blatt ueber dem Inhalt stehen. Der Fokus ist bereits
           am neuen Ziel, deshalb wird er hier nicht zur Klappe zurueckgezogen. */
        document.addEventListener('focusin', function (event) {
            if (!leiste.classList.contains('offen') || leiste.contains(event.target)) {
                return;
            }

            schliessen(leiste, klappe, blatt, false);
        });

        /* Ein Klick im Blatt navigiert weiter. Das Blatt muss dann zu sein,
           damit es auf der Zielroute bzw. am Fragment nicht offen bleibt. */
        blatt.addEventListener('click', function (event) {
            if (event.target.closest('a')) {
                schliessen(leiste, klappe, blatt, false);
            }
        });

        window.addEventListener('resize', function () {
            if (window.innerWidth < BREITE_AB || !leiste.classList.contains('offen')) {
                return;
            }

            /* Ein Viewportwechsel darf den aktuell fokussierten Link nicht in
               einem hidden gesetzten Blatt einschliessen. */
            schliessen(leiste, klappe, blatt, blatt.contains(document.activeElement));
        }, { passive: true });
    }

    function oeffnen(leiste, klappe, blatt) {
        blatt.hidden = false;
        leiste.classList.add('offen');
        klappe.setAttribute('aria-expanded', 'true');
        klappe.setAttribute('aria-label', 'Navigation schließen');
        beschriften(klappe, 'close');
    }

    function schliessen(leiste, klappe, blatt, fokusZurueck) {
        /* Fokus zuerst aus dem Panel ziehen. Ein fokussierter Nachfahre darf
           nicht erst unsichtbar werden und dann auf document.body fallen. */
        if (fokusZurueck && blatt.contains(document.activeElement)) {
            klappe.focus();
        }

        blatt.hidden = true;
        leiste.classList.remove('offen');
        klappe.setAttribute('aria-expanded', 'false');
        klappe.setAttribute('aria-label', 'Navigation öffnen');
        beschriften(klappe, 'open');

        if (fokusZurueck && document.activeElement !== klappe) {
            klappe.focus();
        }
    }

    function beschriften(klappe, zustand) {
        var wort = klappe.querySelector('[data-leiste-wort]');

        if (!wort) {
            return;
        }

        wort.textContent = 'close' === zustand
            ? wort.getAttribute('data-wort-zu') || 'Schließen'
            : wort.getAttribute('data-wort-auf') || 'Menü';
    }

    function veroeffentlicheHoehe(hoehe) {
        hoehe = Math.round(Number(hoehe) || 0);

        if (hoehe > 0) {
            document.documentElement.style.setProperty('--leiste-h', hoehe + 'px');
        }
    }

    function messenUndVeroeffentlichen(zeile) {
        veroeffentlicheHoehe(zeile.getBoundingClientRect().height);
    }

    function beobachteHoehe(leiste) {
        var zeile = leiste.querySelector('[data-leiste-zeile]') || leiste;

        if ('ResizeObserver' in window) {
            new window.ResizeObserver(function (entries) {
                var entry = entries && entries[0];
                if (!entry) return;

                var borderSize = entry.borderBoxSize;
                var blockSize = 0;

                if (borderSize) {
                    blockSize = Array.isArray(borderSize)
                        ? borderSize[0] && borderSize[0].blockSize
                        : borderSize.blockSize;
                }

                veroeffentlicheHoehe(blockSize || (entry.contentRect && entry.contentRect.height));
            }).observe(zeile);
            return;
        }

        /* Nur fuer alte Browser ohne ResizeObserver. Die Messung laeuft in
           einem Frame statt synchron im Init-Pfad. */
        window.requestAnimationFrame(function () {
            messenUndVeroeffentlichen(zeile);
        });

        window.addEventListener('resize', function () {
            window.requestAnimationFrame(function () {
                messenUndVeroeffentlichen(zeile);
            });
        }, { passive: true });
    }

    if ('loading' === document.readyState) {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
