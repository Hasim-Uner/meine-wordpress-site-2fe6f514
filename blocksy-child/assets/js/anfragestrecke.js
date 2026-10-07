/* ══════════════════════════════════════════════════════════════
   ANFRAGESTRECKE — Rechner, gezielte Bewegungen, Kapitelmarke
   /solar-waermepumpen-leadgenerierung/

   Bewusst klein und ohne Abhaengigkeiten. Die Seite ist formularfrei;
   Marktcheck und Sofortkontakt werden auf separate Intake-Wege uebergeben.

   Ohne JavaScript bleibt die Seite vollstaendig: Rechner und Konfigurator
   zeigen serverseitig gerenderte Ausgangswerte und funktionierende Links.
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

  /* Die schmale Kopfleiste bleibt im Dokumentfluss. Das Register
     behält seine eigenen Sprungabstände, ohne einen festen Header-Offset. */

  function kopfUndRegister() {
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

    function sync() {
      var headerHoehe = 0;

      wurzel.style.setProperty('--strecke-header-height', headerHoehe + 'px');

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

    var AUFBAU = konstante('aufbau', 9999);
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

    var angezeigt = null;
    var ziel = null;
    var frame = 0;
    var summen = blatt.querySelectorAll('.summe');
    var balken = {};
    ['A', 'B'].forEach(function (weg) {
      balken[weg] = blatt.querySelector('[data-balken="' + weg + '"]');
    });

    function formatiere(id, zahl) {
      if (zahl === null) {
        return '–';
      }
      if (id === 'oA2' || id === 'oB2') {
        return stueck.format(zahl);
      }
      return euro.format(zahl) + (id === 'oA1' || id === 'oB1' ? ' / Mon.' : '');
    }

    function zeichne(werte) {
      angezeigt = werte;
      Object.keys(ausgaben).forEach(function (id) {
        var text = formatiere(id, werte[id]);
        if (ausgaben[id] && ausgaben[id].textContent !== text) {
          ausgaben[id].textContent = text;
        }
      });

      var massstab = Math.max(werte.oA3 || 0, werte.oB3 || 0, 1);
      ['A', 'B'].forEach(function (weg) {
        var zeile = balken[weg];
        if (!zeile) {
          return;
        }
        var kosten = werte['o' + weg + '3'];
        var anteil = kosten === null ? 0 : kosten / massstab;
        var spur = zeile.querySelector('.balken-spur');
        var marke = zeile.querySelector('.balken-wert');
        zeile.querySelector('.balken-flaeche').style.transform = 'scaleX(' + anteil + ')';
        marke.querySelector('b').textContent = formatiere('o' + weg + '3', kosten);
        var versatz = (anteil - 1) * spur.clientWidth + (anteil < 0.25 ? marke.offsetWidth : 0);
        marke.style.transform = 'translateX(' + versatz + 'px)';
      });
    }

    function beschaeftigt(status) {
      Array.prototype.forEach.call(summen, function (summe) {
        summe.setAttribute('aria-busy', status ? 'true' : 'false');
      });
    }

    function rechne() {
      var anfragenA = wert('a1');
      var einkauf = anfragenA * wert('a2');
      var auftraegeA = anfragenA * wert('a3') / 100;
      var budgetB = wert('b1');
      var cplB = wert('b2');
      var kostenB = budgetB + AUFBAU / MONATE + HOSTING;
      var auftraegeB = cplB > 0 ? budgetB / cplB * wert('b3') / 100 : 0;
      var neu = {
        oA1: einkauf,
        oA2: auftraegeA,
        oA3: auftraegeA > 0 ? einkauf / auftraegeA : null,
        oB1: kostenB,
        oB2: auftraegeB,
        oB3: auftraegeB > 0 ? kostenB / auftraegeB : null
      };
      if (ziel && Object.keys(neu).every(function (id) { return neu[id] === ziel[id]; })) {
        return;
      }
      ziel = neu;
      window.cancelAnimationFrame(frame);
      if (ruhig || !angezeigt) {
        zeichne(neu);
        beschaeftigt(false);
        return;
      }

      var vorher = angezeigt;
      var beginn = window.performance.now();
      beschaeftigt(true);
      function zaehle(zeit) {
        var fortschritt = Math.min(1, (zeit - beginn) / 300);
        var easeOut = 1 - Math.pow(1 - fortschritt, 3);
        var zwischen = {};
        Object.keys(neu).forEach(function (id) {
          zwischen[id] = neu[id] === null || vorher[id] === null
            ? neu[id]
            : vorher[id] + (neu[id] - vorher[id]) * easeOut;
        });
        zeichne(zwischen);
        if (fortschritt < 1) {
          frame = window.requestAnimationFrame(zaehle);
        } else {
          zeichne(neu);
          beschaeftigt(false);
        }
      }
      frame = window.requestAnimationFrame(zaehle);
    }

    window.addEventListener('resize', function () {
      if (angezeigt) {
        zeichne(angezeigt);
      }
    }, { passive: true });

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

  /* ── Produktkonfigurator ──────────────────────────────────────
     Eine Produktstrecke ist im Grundpreis enthalten. Jede weitere
     erweitert den Preis um den Canon-Wert aus dem Markup. Die Ziel-URL
     traegt nur die ausgewaehlten Produktkennungen, keine Personendaten. */

  function konfigurator() {
    var box = wurzel.querySelector('[data-system-configurator]');
    if (!box) {
      return;
    }

    var controls = [].slice.call(box.querySelectorAll('[data-system-product]'));
    var priceNode = box.querySelector('[data-config-price]');
    var selectionNode = box.querySelector('[data-config-selection]');
    var cta = box.querySelector('[data-system-configurator-cta]');
    var base = parseFloat(box.getAttribute('data-base-price')) || 9999;
    var extra = parseFloat(box.getAttribute('data-extra-price')) || 1000;
    var euro = new Intl.NumberFormat('de-DE', {
      style: 'currency',
      currency: 'EUR',
      maximumFractionDigits: 0
    });

    if (priceNode) {
      priceNode.setAttribute('aria-live', 'polite');
    }

    function activeControls() {
      return controls.filter(function (control) { return control.checked; });
    }

    function labelFor(control) {
      var label = control.closest('label');
      var strong = label ? label.querySelector('.produktwahl__text strong') : null;
      return strong ? strong.textContent.trim() : control.value;
    }

    function animateValue(node) {
      if (!node || ruhig || typeof node.animate !== 'function') {
        return;
      }
      node.animate(
        [
          { opacity: 0.45, transform: 'translateY(5px)' },
          { opacity: 1, transform: 'translateY(0)' }
        ],
        { duration: 180, easing: 'cubic-bezier(0.23, 1, 0.32, 1)' }
      );
    }

    function sync(changed) {
      var active = activeControls();
      if (!active.length && changed) {
        changed.checked = true;
        active = [changed];
      }
      if (!active.length && controls.length) {
        controls[0].checked = true;
        active = [controls[0]];
      }

      var total = base + Math.max(0, active.length - 1) * extra;
      if (priceNode) {
        var nextPrice = euro.format(total);
        if (priceNode.textContent !== nextPrice) {
          priceNode.textContent = nextPrice;
          animateValue(priceNode);
        }
      }
      if (selectionNode) {
        selectionNode.textContent = active.map(labelFor).join(' + ');
      }

      var activeKeys = active.map(function (control) { return control.value; });
      [].slice.call(box.querySelectorAll('[data-system-path]')).forEach(function (path) {
        path.classList.toggle('ist-aktiv', activeKeys.indexOf(path.getAttribute('data-system-path')) >= 0);
      });

      if (cta) {
        try {
          var baseHref = cta.getAttribute('data-base-href') || cta.getAttribute('href');
          var url = new URL(baseHref, window.location.href);
          url.searchParams.set('produkte', activeKeys.join(','));
          cta.setAttribute('href', url.pathname + url.search + url.hash);
        } catch (error) {
          // Der serverseitige Default-Link bleibt funktionsfaehig.
        }
      }
    }

    controls.forEach(function (control) {
      control.addEventListener('change', function () { sync(control); });
    });

    sync(null);
  }

  /* Bandbreiten bauen sich beim ersten Sichtkontakt auf. Die drei
     Fallphasen verbinden Hover und Tastaturfokus mit der Grafik. */
  function bandtreppe() {
    var treppe = wurzel.querySelector('.bandtreppe');
    if (!treppe) {
      return;
    }
    if (!ruhig && 'IntersectionObserver' in window) {
      treppe.classList.add('armed');
      var beobachter = new IntersectionObserver(function (eintraege) {
        if (eintraege.some(function (eintrag) { return eintrag.isIntersecting; })) {
          treppe.classList.add('lauf');
          beobachter.disconnect();
        }
      }, { threshold: 0.2 });
      beobachter.observe(treppe);
    }

    var hover = null;
    var fokus = null;
    function hervorheben() {
      var aktiv = hover || fokus;
      Array.prototype.forEach.call(treppe.querySelectorAll('[data-treppenphase]'), function (stufe) {
        stufe.classList.toggle('gedimmt', !!aktiv && stufe.getAttribute('data-treppenphase') !== aktiv);
      });
    }
    Array.prototype.forEach.call(wurzel.querySelectorAll('[data-fallphase]'), function (phase) {
      var name = phase.getAttribute('data-fallphase');
      phase.addEventListener('mouseenter', function () { hover = name; hervorheben(); });
      phase.addEventListener('mouseleave', function () { hover = null; hervorheben(); });
      phase.addEventListener('focusin', function () { fokus = name; hervorheben(); });
      phase.addEventListener('focusout', function () { fokus = null; hervorheben(); });
    });
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
    konfigurator();
    einmalig(wurzel.querySelector('.buehne'), 0.25, 1400);
    bandtreppe();
    kapitelmarke();
    leiste();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', start, { once: true });
  } else {
    start();
  }
})();
