/**
 * Fallstudie: genau drei Bewegungen, je einmal.
 *
 * 1. Strecke  — Stationen blenden ein, dann zeichnet sich die Rueckkopplung.
 * 2. Balken   — der Balken der eigenen Anfrage schrumpft auf seinen Anteil.
 * 3. Zaehler  — Anfragen und Abschlussquote zaehlen einmal hoch.
 *
 * Progressive Enhancement: Das Markup steht im Endzustand. Startzustaende
 * setzt erst dieses Script, und nur wenn Bewegung erlaubt ist und der
 * IntersectionObserver existiert.
 */
(function () {
	'use strict';

	// FAQ: nur eine Antwort offen. Browser mit <details name> erledigen das
	// selbst; fuer aeltere schliesst dieses Stueck die uebrigen.
	var faq = document.querySelectorAll('.fs-faq details');
	if (faq.length && !('name' in HTMLDetailsElement.prototype)) {
		Array.prototype.forEach.call(faq, function (d) {
			d.addEventListener('toggle', function () {
				if (!d.open) {
					return;
				}
				Array.prototype.forEach.call(faq, function (o) {
					if (o !== d) {
						o.open = false;
					}
				});
			});
		});
	}

	var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	if (reduce || !('IntersectionObserver' in window)) {
		return;
	}

	var strecke = document.getElementById('fs-strecke');
	var beweis = document.getElementById('fs-beweis');
	var zaehler = beweis ? beweis.querySelectorAll('.fs-zaehler') : [];

	function format(el, n) {
		return (el.dataset.format === 'de' ? n.toLocaleString('de-DE') : String(n)) + (el.dataset.suffix || '');
	}

	function zaehle(el) {
		var ziel = Number(el.dataset.ziel);
		if (!ziel) {
			return;
		}
		var start = null;
		var dauer = 1100;
		function schritt(t) {
			if (start === null) {
				start = t;
			}
			var x = Math.min(1, (t - start) / dauer);
			el.textContent = format(el, Math.round(ziel * (1 - Math.pow(1 - x, 4))));
			if (x < 1) {
				window.requestAnimationFrame(schritt);
			}
		}
		el.textContent = format(el, 0);
		window.requestAnimationFrame(schritt);
	}

	var io = new IntersectionObserver(function (eintraege) {
		eintraege.forEach(function (e) {
			if (!e.isIntersecting) {
				return;
			}
			e.target.classList.add('is-los');
			if (e.target === beweis) {
				Array.prototype.forEach.call(zaehler, zaehle);
			}
			io.unobserve(e.target);
		});
	}, { threshold: 0.35 });

	// Die Strecke wird nur vorbereitet, wenn sie beim Laden unterhalb des
	// ersten Screens liegt — sonst bliebe sichtbarer Inhalt kurz gedimmt.
	if (strecke && strecke.getBoundingClientRect().top > window.innerHeight * 0.85) {
		strecke.querySelectorAll('.fs-rueck').forEach(function (p) {
			if (typeof p.getTotalLength === 'function') {
				p.style.setProperty('--len', String(Math.ceil(p.getTotalLength())));
			}
		});
		strecke.classList.add('fs-zieht');
		io.observe(strecke);
	}

	if (beweis) {
		beweis.classList.add('fs-schrumpft');
		window.requestAnimationFrame(function () {
			window.requestAnimationFrame(function () {
				io.observe(beweis);
			});
		});
	}
}());
