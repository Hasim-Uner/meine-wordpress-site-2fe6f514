/**
 * Kopfleiste.
 *
 * Zwei Aufgaben, mehr nicht: das Klappblatt auf schmalen Schirmen oeffnen
 * und schliessen, und die gemessene Hoehe der Leiste als --leiste-h
 * veroeffentlichen, damit Sprungziele und seitenlokale sticky-Elemente
 * dagegen rechnen koennen.
 *
 * Progressive Enhancement: ohne dieses Skript bleibt das Klappblatt offen
 * im Dokument stehen und die Klappe unsichtbar. Navigation, die an einem
 * Skript haengt, ist auf einem schmalen Schirm keine Navigation.
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

        messenUndVeroeffentlichen(leiste);
        beobachteHoehe(leiste);

        if (!klappe || !blatt) {
            leiste.setAttribute('data-leiste-bereit', '');
            return;
        }

        /* Erst ab hier ist das Klappblatt eine Klappe. Das Attribut schaltet
           in system.css gleichzeitig die Schaltflaeche sichtbar und das
           versteckte Blatt aus — beides zusammen, damit nie ein Zustand
           entsteht, in dem die Navigation weder offen noch erreichbar ist. */
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

        /* Ein Klick im Blatt fuehrt auf ein Sprungziel derselben Seite. Das
           Blatt muss dann zu sein, sonst landet der Sprung hinter ihm. */
        blatt.addEventListener('click', function (event) {
            if (event.target.closest('a')) {
                schliessen(leiste, klappe, blatt, false);
            }
        });

        window.addEventListener('resize', function () {
            if (window.innerWidth >= BREITE_AB && leiste.classList.contains('offen')) {
                schliessen(leiste, klappe, blatt, false);
            }
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
        blatt.hidden = true;
        leiste.classList.remove('offen');
        klappe.setAttribute('aria-expanded', 'false');
        klappe.setAttribute('aria-label', 'Navigation öffnen');
        beschriften(klappe, 'open');

        if (fokusZurueck) {
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

    function messenUndVeroeffentlichen(leiste) {
        /* Nur die Zeile selbst, nicht das geoeffnete Blatt: --leiste-h ist
           der Abstand, den ein Sprungziel unter der Leiste braucht, und der
           aendert sich nicht dadurch, dass jemand das Menue aufklappt. */
        var zeile = leiste.querySelector('[data-leiste-zeile]') || leiste;
        var hoehe = Math.round(zeile.getBoundingClientRect().height);

        if (hoehe > 0) {
            document.documentElement.style.setProperty('--leiste-h', hoehe + 'px');
        }
    }

    function beobachteHoehe(leiste) {
        var zeile = leiste.querySelector('[data-leiste-zeile]') || leiste;

        if ('ResizeObserver' in window) {
            new window.ResizeObserver(function () {
                messenUndVeroeffentlichen(leiste);
            }).observe(zeile);
            return;
        }

        window.addEventListener('resize', function () {
            messenUndVeroeffentlichen(leiste);
        }, { passive: true });
    }

    if ('loading' === document.readyState) {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
