/**
 * Rechner „Was kostet ein Formular, das still ausfällt?“ ([hu_wartung_rechner]).
 * Spiegelt hu_wartung_rechner_compute() in inc/wartung-rechner.php. Ohne JS
 * bleibt das serverseitig gerenderte Ergebnis der Voreinstellungen stehen.
 */
(function () {
	'use strict';

	var eur = new Intl.NumberFormat('de-DE', { style: 'currency', currency: 'EUR', maximumFractionDigits: 0 });
	var eins = new Intl.NumberFormat('de-DE', { maximumFractionDigits: 1 });
	var NBSP = ' ';

	function anzeige(key, v) {
		if (key === 'quote') { return v + NBSP + '%'; }
		if (key === 'db' || key === 'wartung') { return eur.format(v).replace(/\s€$/, NBSP + '€'); }
		if (key === 'tage') { return v + (v === 1 ? ' Tag' : ' Tage'); }
		return String(v);
	}

	function init(root) {
		var felder = root.querySelectorAll('[data-wr-feld]');
		var out = {};
		root.querySelectorAll('[data-wr-out]').forEach(function (el) { out[el.getAttribute('data-wr-out')] = el; });

		function wert(key) {
			var el = root.querySelector('[data-wr-feld="' + key + '"]');
			return el ? Number(el.value) : 0;
		}

		function rechne() {
			var a = wert('anfragen'), q = wert('quote') / 100, db = wert('db'), t = wert('tage'), w = wert('wartung');
			var proTag = a / 30 * q * db, req = a / 30 * t, jobs = req * q, verlust = proTag * t, jahr = w * 12;
			var satz;

			if (w <= 0) {
				satz = 'Ohne Wartungskosten gibt es nichts gegenzurechnen.';
			} else {
				var be = Math.ceil(jahr / proTag);
				if (be > 365) {
					satz = 'Mit diesen Zahlen trägt sich die Wartung über stille Ausfälle nicht. Dann zählen eher Sicherheit und Ihre eingesparte Zeit.';
				} else {
					satz = 'Die Wartung hat sich bezahlt gemacht, wenn sie einmal im Jahr ' + be + (be === 1 ? ' Tag' : ' Tage') + ' stillen Ausfall verhindert.';
					if (be <= t) { satz += ' Dieser eine Ausfall kostet mehr als ein Jahr Wartung.'; }
				}
			}

			out.verlust.textContent = eur.format(verlust);
			out.anfragen.textContent = eins.format(req);
			out.auftraege.textContent = eins.format(jobs);
			out.jahr.textContent = eur.format(jahr);
			out.satz.textContent = satz;

			felder.forEach(function (el) {
				var key = el.getAttribute('data-wr-feld');
				var ziel = root.querySelector('[data-wr-anzeige="' + key + '"]');
				if (ziel) { ziel.textContent = anzeige(key, Number(el.value)); }
			});
		}

		felder.forEach(function (el) { el.addEventListener('input', rechne); });
		rechne();
	}

	function start() {
		document.querySelectorAll('[data-hu-wartung-rechner]').forEach(init);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', start);
	} else {
		start();
	}
})();
