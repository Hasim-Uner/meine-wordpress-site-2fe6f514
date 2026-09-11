/* ══════════════════════════════════════════════════════════════
   ANFRAGESTRECKE — Rechner, zwei Bewegungen, Kapitelmarke
   /solar-waermepumpen-leadgenerierung/

   Bewusst klein und ohne Abhaengigkeiten. Der Marktcheck selbst
   (Formular, Validierung, REST) liegt unveraendert in
   solar-leadgenerierung-solara.js — diese Datei fasst ihn nicht an.

   Ohne JavaScript bleibt die Seite vollstaendig: der Rechner zeigt
   dann seine Platzhalter, beide Grafiken stehen fertig da.
   ══════════════════════════════════════════════════════════════ */

(function () {
  'use strict';

  var wurzel = document.querySelector('.strecke-doc');
  if (!wurzel) {
    return;
  }

  var ruhig = false;
  try {
    ruhig = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  } catch (e) {
    ruhig = false;
  }

  /* ── Rechner ───────────────────────────────────────────────────
     Cost per Order, nicht Cost per Lead. Beide Spalten starten am
     gleichen Monatsbudget, damit der Vergleich nicht an der
     Budgethoehe haengt.

     Die Konstanten stehen im Markup (data-Attribute am Rechenblatt)
     und kommen dort aus dem Pricing-Canon. Sie hier zu wiederholen
     hiesse, den Aufbaupreis an zwei Orten zu pflegen. */

  function rechner() {
    var blatt = wurzel.querySelector('[data-strecke-rechner]');
    if (!blatt) {
      return;
    }

    var euro = new Intl.NumberFormat('de-DE', {
      style: 'currency',
      currency: 'EUR',
      maximumFractionDigits: 0
    });
    var stueck = new Intl.NumberFormat('de-DE', { maximumFractionDigits: 2 });

    function konstante(name, ersatz) {
      var roh = parseFloat(blatt.getAttribute('data-' + name));
      return isFinite(roh) && roh > 0 ? roh : ersatz;
    }

    var AUFBAU = konstante('aufbau', 14900);
    var MONATE = konstante('monate', 24);
    var HOSTING = konstante('hosting', 50);

    var felder = {};
    ['a1', 'a2', 'a3', 'b1', 'b2', 'b3'].forEach(function (id) {
      felder[id] = blatt.querySelector('[data-feld="' + id + '"]');
    });

    var ausgaben = {};
    ['oA1', 'oA2', 'oA3', 'oB1', 'oB2', 'oB3'].forEach(function (id) {
      ausgaben[id] = blatt.querySelector('[data-ausgabe="' + id + '"]');
    });

    function wert(id) {
      var feld = felder[id];
      if (!feld) {
        return 0;
      }
      var zahl = parseFloat(feld.value);
      return isFinite(zahl) && zahl >= 0 ? zahl : 0;
    }

    /* Schreibt nur, wenn sich etwas geaendert hat. Sonst liefe die
       Aufblende-Animation bei jedem Tastendruck erneut, auch wenn der
       Wert gleich bleibt. */
    function setze(id, text) {
      var el = ausgaben[id];
      if (!el || el.textContent === text) {
        return;
      }

      el.textContent = text;

      if (!el.classList.contains('w') || ruhig) {
        return;
      }

      el.classList.remove('frisch');
      void el.offsetWidth; // Neustart der Animation erzwingen
      el.classList.add('frisch');
    }

    /* Division durch null gibt einen Gedankenstrich, nicht NaN und nicht
       Unendlich. Wer die Abschlussquote auf 0 stellt, bekommt keinen
       unendlich teuren Auftrag angezeigt, sondern keine Aussage. */
    function jeAuftrag(kosten, auftraege) {
      return auftraege > 0 ? euro.format(kosten / auftraege) : '–';
    }

    function rechne() {
      var anfragenA = wert('a1');
      var preisA = wert('a2');
      var quoteA = wert('a3') / 100;

      var einkauf = anfragenA * preisA;
      var auftraegeA = anfragenA * quoteA;

      setze('oA1', euro.format(einkauf) + ' / Mon.');
      setze('oA2', stueck.format(auftraegeA));
      setze('oA3', jeAuftrag(einkauf, auftraegeA));

      var budgetB = wert('b1');
      var cplB = wert('b2');
      var quoteB = wert('b3') / 100;

      var kostenB = budgetB + AUFBAU / MONATE + HOSTING;
      var anfragenB = cplB > 0 ? budgetB / cplB : 0;
      var auftraegeB = anfragenB * quoteB;

      setze('oB1', euro.format(kostenB) + ' / Mon.');
      setze('oB2', stueck.format(auftraegeB));
      setze('oB3', jeAuftrag(kostenB, auftraegeB));
    }

    Object.keys(felder).forEach(function (id) {
      var feld = felder[id];
      if (!feld) {
        return;
      }
      feld.addEventListener('input', rechne);
      feld.addEventListener('change', rechne);
    });

    rechne();
  }

  /* ── Zwei Aufbau-Bewegungen, mehr nicht ────────────────────────
     Scharfgeschaltet wird nur, was beim Laden unterhalb des Sichtfelds
     liegt. Was schon sichtbar ist, bleibt sichtbar — eine Grafik, die
     beim Seitenaufruf erst verschwindet, um sich dann aufzubauen, haelt
     den Leser auf, statt ihm etwas zu erklaeren. */

  function einmalig(el, schwelle, nachlauf) {
    if (!el || ruhig || !('IntersectionObserver' in window)) {
      return;
    }

    if (el.getBoundingClientRect().top < window.innerHeight * 0.85) {
      return;
    }

    el.classList.add('armed');

    var ausgeloest = false;

    function aufdecken() {
      if (ausgeloest) {
        return;
      }
      ausgeloest = true;

      el.classList.add('lauf');
      beobachter.disconnect();

      if (nachlauf) {
        window.setTimeout(function () {
          el.classList.add('fertig');
        }, nachlauf);
      }
    }

    var beobachter = new IntersectionObserver(function (eintraege) {
      eintraege.forEach(function (eintrag) {
        if (eintrag.isIntersecting) {
          aufdecken();
        }
      });
    }, { threshold: schwelle });

    beobachter.observe(el);

    // Reissleine. `armed` deckt die Grafik zu, bis der Beobachter meldet.
    // Meldet er nie — weil das Element hoeher als das Sichtfeld ist und die
    // Schwelle nie erreicht wird, weil ein Vorfahre es aus dem Fluss nimmt,
    // oder weil ein Fehler den Beobachter stillgelegt hat —, bliebe die
    // Hauptgrafik dauerhaft unsichtbar. Eine zugedeckte Grafik ist der
    // schlimmere Fehler als eine, die ohne Aufbau dasteht.
    window.setTimeout(aufdecken, 6000);
  }

  /* ── Kapitelmarke ──────────────────────────────────────────────
     Der aktive Abschnitt faerbt seine Nummer in der Randspalte. Das
     einzige Element der Seite, das mitlaeuft. */

  function kapitelmarke() {
    var abschnitte = [].slice.call(wurzel.querySelectorAll('section[id]'));
    if (!abschnitte.length || !('IntersectionObserver' in window)) {
      return;
    }

    var marke = new IntersectionObserver(function (eintraege) {
      eintraege.forEach(function (eintrag) {
        eintrag.target.classList.toggle('aktiv', eintrag.isIntersecting);
      });
    }, { rootMargin: '-45% 0px -45% 0px' });

    abschnitte.forEach(function (abschnitt) {
      marke.observe(abschnitt);
    });
  }

  /* ── Kapitelleiste ─────────────────────────────────────────────
     Markiert den Eintrag, dessen Abschnitt gerade oben steht. */

  function leiste() {
    var nav = wurzel.querySelector('[data-strecke-leiste]');
    if (!nav) {
      return;
    }

    var links = [].slice.call(nav.querySelectorAll('a[href^="#"]'));
    if (!links.length) {
      return;
    }

    var ziele = links.map(function (a) {
      var hash = a.getAttribute('href');
      try {
        return hash && hash.length > 1 ? document.querySelector(hash) : null;
      } catch (e) {
        return null;
      }
    });

    function markiere() {
      var linie = window.scrollY + window.innerHeight * 0.3;
      var aktiv = -1;

      ziele.forEach(function (ziel, i) {
        if (ziel && ziel.getBoundingClientRect().top + window.scrollY <= linie) {
          aktiv = i;
        }
      });

      links.forEach(function (a, i) {
        if (i === aktiv) {
          a.setAttribute('aria-current', 'true');
        } else {
          a.removeAttribute('aria-current');
        }
      });
    }

    var geplant = false;
    window.addEventListener('scroll', function () {
      if (geplant) {
        return;
      }
      geplant = true;
      window.requestAnimationFrame(function () {
        markiere();
        geplant = false;
      });
    }, { passive: true });

    markiere();
  }

  function start() {
    rechner();
    einmalig(wurzel.querySelector('.buehne'), 0.25, 1400);
    einmalig(wurzel.querySelector('.schrieb'), 0.3, 0);
    kapitelmarke();
    leiste();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', start, { once: true });
  } else {
    start();
  }
})();
