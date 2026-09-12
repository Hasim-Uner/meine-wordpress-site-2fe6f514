/**
 * Startseite.
 *
 * Eine Aufgabe: die laufende Kapitelnummer in der Randspalte faerbt sich,
 * sobald ihr Abschnitt im Blickfeld steht. Das ist das einzige Element der
 * Seite, das mitlaeuft, und es beantwortet die Frage "wo bin ich".
 *
 * Ohne dieses Skript oder ohne IntersectionObserver bleibt die Nummer grau.
 * Die Seite verliert dadurch nichts ausser dieser Markierung — alle Inhalte
 * und Links sind ohne JavaScript vollstaendig nutzbar.
 *
 * Unter prefers-reduced-motion laeuft die Markierung weiter: sie ist ein
 * Farbwechsel, keine Bewegung, und system.css schaltet die Uebergangsdauer
 * dort ohnehin hart.
 */
(function () {
    'use strict';

    function init() {
        var wurzel = document.querySelector('.startseite');

        if (!wurzel || !('IntersectionObserver' in window)) {
            return;
        }

        var abschnitte = Array.prototype.slice.call(wurzel.querySelectorAll('section[id]'));

        if (!abschnitte.length) {
            return;
        }

        /* Das mittlere Zehntel des Sichtfelds. Enger als eine halbe Seite,
           damit bei zwei sichtbaren Abschnitten nicht beide leuchten. */
        var marke = new window.IntersectionObserver(function (eintraege) {
            eintraege.forEach(function (eintrag) {
                eintrag.target.classList.toggle('aktiv', eintrag.isIntersecting);
            });
        }, { rootMargin: '-45% 0px -45% 0px' });

        abschnitte.forEach(function (abschnitt) {
            marke.observe(abschnitt);
        });
    }

    if ('loading' === document.readyState) {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
