/* ══════════════════════════════════════════════════════════════
   ANFRAGESTRECKE — Rechner, gezielte Bewegungen, Kapitelmarke
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

  /* Der Marktcheck besitzt seit 09/2026 einen eigenen visuellen Layer.
     Er bleibt als separate Datei wartbar, wird aber nur auf dieser Route
     geladen. Neben der Script-Version traegt das CSS eine eigene Revision,
     damit reine CSS-Aenderungen nicht an einem alten Browser-/CDN-Cache
     haengen bleiben. */
  function marktcheckStyles() {
    if (document.querySelector('link[data-strecke-marketcheck-style]')) {
      return;
    }

    var script = document.currentScript;
    if (!script || !script.src || script.src.indexOf('/assets/js/anfragestrecke.js') === -1) {
      var scripts = document.querySelectorAll('script[src*="/assets/js/anfragestrecke.js"]');
      script = scripts.length ? scripts[scripts.length - 1] : null;
    }

    if (!script || !script.src) {
      return;
    }

    var href = script.src.replace(
      '/assets/js/anfragestrecke.js',
      '/assets/css/anfragestrecke-marketcheck.css'
    );
    href += (href.indexOf('?') === -1 ? '?' : '&') + 'mc=e4d2c1c';

    var link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = href;
    link.setAttribute('data-strecke-marketcheck-style', '');
    document.head.appendChild(link);
  }

  marktcheckStyles();

  var ruhig = false;
  try {
    ruhig = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  } catch (e) {
    ruhig = false;
  }

  /* ── Energy-Header + Kapitelregister ───────────────────────────
     Der globale Header-Controller besitzt bereits einen Pin-Vertrag.
     Die Money-Page nutzt ihn explizit: der Header bleibt sichtbar und
     seine tatsaechlich gemessene Hoehe wird zum Offset fuer Register,
     Kapitelmarken und Sprungziele. Damit konkurrieren nicht mehr ein
     64-px-Annahmewert und ein hoeherer Energy-Header miteinander.

     Auf dem Telefon bleibt das Register bewusst im Dokumentfluss. Eine
     zweite sticky Leiste unter dem festen Header kostet dort zu viel
     Nutzflaeche und fuehrt beim Ankersprung zu einem doppelten Offset. */

  function kopfUndRegister() {
    var header = document.querySelector('[data-site-header].nx-site-header--energy');
    var register = wurzel.querySelector('[data-strecke-leiste]');
    var mobil = null;
    var breit = null;

    try {
      mobil = window.matchMedia('(max-width: 768px)');
      breit = window.matchMedia('(min-width: 1280px) and (min-height: 620px) and (hover: hover) and (pointer: fine)');
    } catch (e) {
      mobil = null;
      breit = null;
    }

    if (header) {
      header.setAttribute('data-site-header-pin', '');
      header.classList.add('is-visible');

      try {
        header.dispatchEvent(new CustomEvent('nexus:header-pin'));
      } catch (e) {
        try {
          header.dispatchEvent(new Event('nexus:header-pin'));
        } catch (ignored) {}
      }
    }

    function sync() {
      var headerHoehe = 64;

      if (header) {
        var rect = header.getBoundingClientRect();
        var top = 0;
        try {
          top = parseFloat(window.getComputedStyle(header).top) || 0;
        } catch (e) {
          top = 0;
        }
        headerHoehe = Math.max(64, Math.ceil(rect.height + Math.max(0, top)));
      }

      wurzel.style.setProperty('--nx-site-header-height', headerHoehe + 'px');

      if (register && mobil) {
        if (mobil.matches) {
          register.style.setProperty('position', 'static', 'important');
          register.style.setProperty('top', 'auto', 'important');
        } else {
          register.style.removeProperty('position');
          register.style.removeProperty('top');
        }
      }

      var sprungZusatz = 64;
      if (mobil && mobil.matches) {
        sprungZusatz = 16;
      } else if (breit && breit.matches) {
        sprungZusatz = 24;
      }

      [].slice.call(wurzel.querySelectorAll('[id]')).forEach(function (ziel) {
        ziel.style.scrollMarginTop = (headerHoehe + sprungZusatz) + 'px';
      });
    }

    sync();
    window.requestAnimationFrame(sync);
    window.addEventListener('resize', sync, { passive: true });

    if (mobil) {
      if (typeof mobil.addEventListener === 'function') {
        mobil.addEventListener('change', sync);
      } else if (typeof mobil.addListener === 'function') {
        mobil.addListener(sync);
      }
    }

    if (breit) {
      if (typeof breit.addEventListener === 'function') {
        breit.addEventListener('change', sync);
      } else if (typeof breit.addListener === 'function') {
        breit.addListener(sync);
      }
    }

    if (header && 'ResizeObserver' in window) {
      new ResizeObserver(sync).observe(header);
    }
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
      void el.offsetWidth;
      el.classList.add('frisch');
    }

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

  /* ── Zwei Aufbau-Bewegungen, mehr nicht ──────────────────────── */

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
    window.setTimeout(aufdecken, 6000);
  }

  /* ── Marktcheck-Schrittwechsel ───────────────────────────────── */

  function marktcheckBewegung() {
    var mount = wurzel.querySelector('#sol-quiz-mount');
    if (!mount || ruhig || !('MutationObserver' in window)) {
      return;
    }

    var letzter = null;
    var initialisiert = false;
    var geplant = false;

    function aktuellesElement() {
      return mount.querySelector('.sol-quiz, .sol-quiz-success');
    }

    function bewegen() {
      geplant = false;
      var aktuell = aktuellesElement();

      if (!aktuell || aktuell === letzter) {
        return;
      }

      letzter = aktuell;

      if (!initialisiert) {
        initialisiert = true;
        return;
      }

      if (typeof aktuell.animate !== 'function') {
        return;
      }

      aktuell.animate(
        [
          { opacity: 0, transform: 'translateY(8px)' },
          { opacity: 1, transform: 'translateY(0)' }
        ],
        {
          duration: 200,
          easing: 'cubic-bezier(0.23, 1, 0.32, 1)'
        }
      );
    }

    function planen() {
      if (geplant) {
        return;
      }
      geplant = true;
      window.requestAnimationFrame(bewegen);
    }

    var beobachter = new MutationObserver(planen);
    beobachter.observe(mount, { childList: true, subtree: true });
    planen();
  }

  /* ── Kapitelmarke ────────────────────────────────────────────── */

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

  /* ── Kapitelleiste ───────────────────────────────────────────── */

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
    kopfUndRegister();
    rechner();
    einmalig(wurzel.querySelector('.buehne'), 0.25, 1400);
    einmalig(wurzel.querySelector('.schrieb'), 0.3, 0);
    marktcheckBewegung();
    kapitelmarke();
    leiste();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', start, { once: true });
  } else {
    start();
  }
})();
