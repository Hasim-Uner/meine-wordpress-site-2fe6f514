/* =============================================================
   Artikel-Bausteine (inc/editorial-bausteine.php)

   1. Die Kette zeichnet sich einmal, wenn sie ins Bild kommt. Steht sie
      beim Laden schon im Bild, bleibt sie fertig. Ohne JS, ohne
      IntersectionObserver oder bei prefers-reduced-motion passiert nichts.
   2. Pruefliste: Checkboxen, Stand "x von n geprueft", Fortschrittslinie,
      "Liste kopieren" (Zwischenablage, sonst Markieren) und
      "Zuruecksetzen". Nichts wird im Browser gespeichert.

   Das aktive Kapitel markiert das Inhaltsverzeichnis des Readers
   (nexus-core.js), nicht dieses Skript.
   ============================================================= */
(function () {
    'use strict';

    if (typeof document === 'undefined') return;

    var ruhig = !!(window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches);

    function initKette(kette) {
        if (ruhig || !('IntersectionObserver' in window)) return;
        if (kette.getBoundingClientRect().top < window.innerHeight) return;

        kette.classList.add('is-wartet');

        var beobachter = new IntersectionObserver(function (eintraege) {
            eintraege.forEach(function (eintrag) {
                if (!eintrag.isIntersecting) return;
                beobachter.disconnect();
                kette.classList.add('is-zeichnet');
                window.requestAnimationFrame(function () {
                    window.requestAnimationFrame(function () {
                        kette.classList.remove('is-wartet');
                    });
                });
            });
        }, { threshold: 0.35 });

        beobachter.observe(kette);
    }

    function textOf(node) {
        return (node.textContent || '').replace(/\s+/g, ' ').trim();
    }

    function initPruefliste(root, index) {
        var liste = root.querySelector('ol');
        var kopf = root.querySelector('.hu-pruef__kopf');
        var titel = root.querySelector('.hu-pruef__titel');
        if (!liste || !kopf) return;

        var punkte = Array.prototype.filter.call(liste.children, function (el) {
            return el.tagName === 'LI';
        });
        if (!punkte.length) return;

        var basis = root.id || ('pruefliste-' + (index + 1));
        var boxen = [];

        punkte.forEach(function (punkt, i) {
            var label = document.createElement('label');
            var box = document.createElement('input');
            var text = document.createElement('span');

            box.type = 'checkbox';
            box.id = basis + '-' + (i + 1);
            label.htmlFor = box.id;
            text.className = 'hu-pruef__text';

            while (punkt.firstChild) {
                text.appendChild(punkt.firstChild);
            }

            label.appendChild(box);
            label.appendChild(text);
            punkt.appendChild(label);
            boxen.push(box);
        });

        var stand = document.createElement('p');
        stand.className = 'hu-pruef__stand';
        stand.setAttribute('aria-live', 'polite');
        var zahl = document.createElement('b');
        zahl.textContent = '0';
        stand.appendChild(zahl);
        stand.appendChild(document.createTextNode(' von ' + boxen.length + ' geprüft'));
        kopf.appendChild(stand);

        var balken = document.createElement('div');
        balken.className = 'hu-pruef__balken';
        balken.setAttribute('aria-hidden', 'true');
        balken.appendChild(document.createElement('i'));
        kopf.parentNode.insertBefore(balken, kopf.nextSibling);

        var fuss = document.createElement('div');
        fuss.className = 'hu-pruef__fuss';

        var kopieren = document.createElement('button');
        kopieren.type = 'button';
        kopieren.className = 'hu-tun hu-tun--still';
        kopieren.textContent = 'Liste kopieren';

        var zurueck = document.createElement('button');
        zurueck.type = 'button';
        zurueck.className = 'hu-knopf-text';
        zurueck.textContent = 'Zurücksetzen';

        var hinweis = document.createElement('p');
        hinweis.className = 'hu-pruef__hinweis';
        hinweis.setAttribute('aria-live', 'polite');
        hinweis.textContent = 'Als Text für Ihr Projektwerkzeug.';

        fuss.appendChild(kopieren);
        fuss.appendChild(zurueck);
        fuss.appendChild(hinweis);
        root.appendChild(fuss);
        root.classList.add('is-aktiv');

        function aktualisieren() {
            var n = 0;
            boxen.forEach(function (box, i) {
                punkte[i].classList.toggle('is-geprueft', box.checked);
                if (box.checked) n += 1;
            });
            zahl.textContent = String(n);
            root.style.setProperty('--hu-pruef-anteil', String(n / boxen.length));
        }

        boxen.forEach(function (box) {
            box.addEventListener('change', aktualisieren);
        });

        zurueck.addEventListener('click', function () {
            boxen.forEach(function (box) {
                box.checked = false;
            });
            aktualisieren();
            hinweis.textContent = 'Zurückgesetzt.';
        });

        function markieren() {
            var auswahl = window.getSelection ? window.getSelection() : null;
            if (!auswahl || !document.createRange) {
                hinweis.textContent = 'Kopieren ist in diesem Browser nicht möglich.';
                return;
            }
            var bereich = document.createRange();
            bereich.selectNodeContents(liste);
            auswahl.removeAllRanges();
            auswahl.addRange(bereich);
            hinweis.textContent = 'Liste markiert. Mit Strg+C oder Cmd+C kopieren.';
        }

        kopieren.addEventListener('click', function () {
            var zeilen = [titel ? textOf(titel) : 'Prüfliste'];
            boxen.forEach(function (box, i) {
                var text = punkte[i].querySelector('.hu-pruef__text');
                zeilen.push((box.checked ? '[x] ' : '[ ] ') + (i + 1) + '. ' + textOf(text));
            });
            zeilen.push('', window.location.href.split('#')[0]);

            var inhalt = zeilen.join('\n');
            var fertig = 'Kopiert: ' + boxen.length + ' Punkte mit Ihrem Stand.';

            try {
                if (navigator.clipboard && navigator.clipboard.writeText && window.isSecureContext) {
                    navigator.clipboard.writeText(inhalt).then(function () {
                        hinweis.textContent = fertig;
                    }, markieren);
                } else {
                    markieren();
                }
            } catch (fehler) {
                markieren();
            }
        });

        aktualisieren();
    }

    function init() {
        var content = document.getElementById('article-content') || document;

        Array.prototype.forEach.call(content.querySelectorAll('[data-hu-kette]'), initKette);
        Array.prototype.forEach.call(content.querySelectorAll('[data-hu-pruef]'), initPruefliste);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
