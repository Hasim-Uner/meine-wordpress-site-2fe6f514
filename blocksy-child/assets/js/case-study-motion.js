/**
 * Fallstudie: das Messgeraet folgt dem Leser.
 *
 * Jedes Kapitel ([data-kapitel]) traegt den Zustand des Geraets in data-*:
 * wert (Anzeige), zahl (Zahl fuer Skala und Zaehler bei einer Spanne), von
 * (Startwert der Zaehlung), phase, monat, stationen, schleife, blitz, ziel,
 * ende. Das Kapitel in der Bildmitte setzt den Zustand.
 *
 * Progressive Enhancement: Ohne dieses Script zeigt das Geraet den
 * Endzustand. Bei reduzierter Bewegung wechseln die Zustaende ohne
 * Animation. Ausserdem: Formular-Schema -> CRM-Eintrag und FAQ-Fallback.
 */
(function () {
	'use strict';

	var geraet = document.getElementById('fs-geraet');
	var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	var each = function (list, fn) { Array.prototype.forEach.call(list, fn); };

	// FAQ: nur eine Antwort offen. Browser mit <details name> erledigen das selbst.
	var faq = document.querySelectorAll('.fs-faq details');
	if (faq.length && !('name' in HTMLDetailsElement.prototype)) {
		each(faq, function (d) {
			d.addEventListener('toggle', function () {
				if (d.open) {
					each(faq, function (o) { if (o !== d) { o.open = false; } });
				}
			});
		});
	}

	// Formular-Schema: die Auswahl fuellt den CRM-Eintrag.
	var opts = document.querySelectorAll('.fs-opt');
	var crmProdukt = document.querySelector('[data-crm-produkt]');
	var crmSeite = document.querySelector('[data-crm-seite]');
	each(opts, function (b) {
		b.addEventListener('click', function () {
			each(opts, function (o) { o.setAttribute('aria-pressed', o === b ? 'true' : 'false'); });
			if (crmProdukt && crmSeite) {
				crmProdukt.textContent = b.dataset.produkt;
				crmSeite.textContent = 'Landingpage ' + b.dataset.produkt;
				[crmProdukt, crmSeite].forEach(function (v) {
					v.classList.add('is-neu');
					window.setTimeout(function () { v.classList.remove('is-neu'); }, 700);
				});
			}
		});
	});

	if (!geraet || !('IntersectionObserver' in window)) {
		return;
	}

	var zahl = geraet.querySelector('[data-geraet-zahl]');
	var einheit = geraet.querySelector('[data-geraet-einheit]');
	var phase = geraet.querySelector('[data-geraet-phase]');
	var fuell = geraet.querySelector('[data-geraet-fuell]');
	var punkt = geraet.querySelector('[data-geraet-punkt]');
	var hof = geraet.querySelector('[data-geraet-hof]');
	var schleife = geraet.querySelector('[data-geraet-schleife]');
	var kapitel = Array.prototype.slice.call(document.querySelectorAll('[data-kapitel]'));
	if (!zahl || !kapitel.length) {
		return;
	}

	var MAX = 150;
	var PUNKT_X = 246;
	var stationY = { 1: 31, 2: 105, 3: 179, 4: 253, 5: 327 };
	var aktuell = Number(zahl.textContent) || 0;
	var aktiv = -1;
	var zaehlerRaf = 0;
	var punktRaf = 0;
	var timer = [];

	if (schleife && typeof schleife.getTotalLength === 'function') {
		schleife.style.setProperty('--len', String(Math.ceil(schleife.getTotalLength())));
	}

	function spaeter(fn, ms) { timer.push(window.setTimeout(fn, ms)); }
	function stopAlles() {
		timer.forEach(window.clearTimeout);
		timer = [];
		window.cancelAnimationFrame(zaehlerRaf);
		window.cancelAnimationFrame(punktRaf);
	}

	function setzePunkt(x, y) {
		if (!punkt) { return; }
		[punkt, hof].forEach(function (c) {
			if (c) { c.setAttribute('cx', String(x)); c.setAttribute('cy', String(y)); }
		});
	}

	function skala(n) { if (fuell) { fuell.style.setProperty('--anteil', String(Math.max(0, Math.min(1, n / MAX)))); } }

	function zaehle(von, bis, text) {
		window.cancelAnimationFrame(zaehlerRaf);
		if (reduce || von === bis) { zahl.textContent = text || String(bis); return; }
		var start = null;
		var dauer = 900;
		function schritt(t) {
			if (start === null) { start = t; }
			var x = Math.min(1, (t - start) / dauer);
			zahl.textContent = String(Math.round(von + (bis - von) * (1 - Math.pow(1 - x, 4))));
			if (x < 1) { zaehlerRaf = window.requestAnimationFrame(schritt); } else if (text) { zahl.textContent = text; }
		}
		zaehlerRaf = window.requestAnimationFrame(schritt);
	}

	function fahre(y) {
		window.cancelAnimationFrame(punktRaf);
		if (reduce || !punkt) { setzePunkt(PUNKT_X, y); return; }
		var x0 = Number(punkt.getAttribute('cx'));
		var y0 = Number(punkt.getAttribute('cy'));
		var start = null;
		function schritt(t) {
			if (start === null) { start = t; }
			var x = Math.min(1, (t - start) / 650);
			var e = 1 - Math.pow(1 - x, 3);
			setzePunkt(x0 + (PUNKT_X - x0) * e, y0 + (y - y0) * e);
			if (x < 1) { punktRaf = window.requestAnimationFrame(schritt); }
		}
		punktRaf = window.requestAnimationFrame(schritt);
	}

	function schleifeFahren() {
		if (!schleife || typeof schleife.getPointAtLength !== 'function') { fahre(stationY[1]); return; }
		var laenge = schleife.getTotalLength();
		if (reduce) { setzePunkt(PUNKT_X, stationY[1]); return; }
		setzePunkt(PUNKT_X, stationY[4]);
		spaeter(function () {
			var start = null;
			function schritt(t) {
				if (start === null) { start = t; }
				var x = Math.min(1, (t - start) / 1400);
				var e = x < 0.5 ? 2 * x * x : 1 - Math.pow(-2 * x + 2, 2) / 2;
				var p = schleife.getPointAtLength(laenge * e);
				setzePunkt(p.x, p.y);
				if (x < 1) { punktRaf = window.requestAnimationFrame(schritt); } else { fahre(stationY[1]); }
			}
			punktRaf = window.requestAnimationFrame(schritt);
		}, 450);
	}

	function aktiviere(i) {
		if (i === aktiv) { return; }
		var vorher = aktiv;
		var d = kapitel[i].dataset;
		var warSchleife = geraet.dataset.schleife === '1';
		aktiv = i;
		stopAlles();

		phase.textContent = d.phase || '';
		geraet.dataset.ziel = d.ziel || '0';
		geraet.dataset.ende = d.ende || '0';
		geraet.dataset.schleife = d.schleife || '0';
		each(geraet.querySelectorAll('.fs-monate span'), function (s) { s.classList.toggle('is-an', s.dataset.m === d.monat); });

		if (d.wert === '–') {
			zahl.textContent = '–';
			einheit.hidden = true;
			skala(0);
			aktuell = 0;
		} else {
			einheit.hidden = false;
			var ziel = Number(d.zahl || d.wert);
			var text = d.zahl ? d.wert : '';
			var von = d.von ? Number(d.von) : aktuell;
			if (d.von && !reduce && von !== aktuell) {
				zaehle(aktuell || von, von);
				skala(von);
				spaeter(function () { zaehle(von, ziel, text); skala(ziel); }, 950);
			} else {
				zaehle(aktuell || ziel, ziel, text);
				skala(ziel);
			}
			aktuell = ziel;
		}

		var stationen = (d.stationen || '').split(',').filter(Boolean).map(Number);
		each(geraet.querySelectorAll('.fs-st'), function (g) { g.classList.toggle('is-an', stationen.indexOf(Number(g.dataset.st)) > -1); });
		each(geraet.querySelectorAll('.fs-fl'), function (f) {
			var n = Number(f.dataset.fl);
			f.classList.toggle('is-an', stationen.indexOf(n) > -1 && stationen.indexOf(n + 1) > -1);
		});
		geraet.classList.toggle('is-ohne-punkt', stationen.length === 0);

		if (d.schleife === '1' && !warSchleife) {
			schleifeFahren();
		} else if (stationen.length && d.schleife !== '1') {
			fahre(stationY[Math.max.apply(null, stationen)]);
		}

		if (d.blitz === '1' && vorher < i && !reduce) {
			geraet.classList.add('is-blitz');
			spaeter(function () { geraet.classList.remove('is-blitz'); }, 2600);
		}
	}

	var io = new IntersectionObserver(function (eintraege) {
		eintraege.forEach(function (e) {
			if (e.isIntersecting) { aktiviere(kapitel.indexOf(e.target)); }
		});
	}, { rootMargin: '-45% 0px -50% 0px' });
	kapitel.forEach(function (k) { io.observe(k); });

	// Startzustand ohne Animation, danach die Uebergaenge freigeben.
	var merke = reduce;
	reduce = true;
	geraet.dataset.schleife = '0';
	aktiviere(0);
	reduce = merke;
	window.requestAnimationFrame(function () {
		window.requestAnimationFrame(function () { geraet.classList.add('is-bereit'); });
	});
}());
