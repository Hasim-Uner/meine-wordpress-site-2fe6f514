/**
 * White-Label Retainer — page-whitelabel-retainer.php
 * Sticky-CTA, Arbeitsmodus-Toggle und Fit-Check (3 Klickfragen → Termin).
 * Wird nur auf Whitelabel-Routen geladen (inc/enqueue.php, Block P2).
 */
(function () {
	'use strict';

	// ─── Sticky Mobile CTA visibility ───
	var sticky = document.getElementById('wl-sticky-cta');
	var hero   = document.getElementById('hero');
	var cta    = document.getElementById('fit-check');

	if (sticky && hero) {
		var updateSticky = function () {
			var heroBottom = hero.getBoundingClientRect().bottom;
			var ctaTop     = cta ? cta.getBoundingClientRect().top : Infinity;
			var shouldShow = heroBottom < 0 && ctaTop > window.innerHeight - 80;
			sticky.classList.toggle('is-visible', shouldShow);
			sticky.setAttribute('aria-hidden', shouldShow ? 'false' : 'true');
			document.body.classList.toggle('has-sticky-wl-cta', shouldShow);
		};
		window.addEventListener('scroll', updateSticky, { passive: true });
		window.addEventListener('resize', updateSticky);
		updateSticky();
	}

	// ─── Arbeitsmodus-Toggle ───
	// JS setzt data-wl-mode als primären CSS-Schlüssel; die :has()-Regeln in
	// whitelabel.css decken nur den No-JS-Fall ab (Radio-Zustand direkt).
	var mode = document.getElementById('wl-mode');
	if (mode) {
		mode.addEventListener('change', function (e) {
			var radio = e.target;
			if (!radio || radio.name !== 'wl-mode' || !radio.checked) {
				return;
			}
			mode.setAttribute('data-wl-mode', radio.value);
		});
	}

	// ─── Fit-Check: 3 Klickfragen → Termin ───
	// Rein clientseitig, keine Speicherung: Antworten sind Enum-Werte und
	// fließen nur in die bestehende data-track-Delegation plus als Vorlage
	// in den mailto-Link. SSR zeigt die Ergebnis-CTAs (No-JS-Pfad); JS
	// invertiert beim Enhancen auf Quiz-first.
	var fitcheck = document.querySelector('[data-fitcheck]');
	if (fitcheck) {
		var quiz   = fitcheck.querySelector('[data-fitcheck-quiz]');
		var result = fitcheck.querySelector('[data-fitcheck-result]');
		var note   = fitcheck.querySelector('[data-fitcheck-note]');
		var skip   = fitcheck.querySelector('[data-fitcheck-skip]');
		var mail   = fitcheck.querySelector('[data-fitcheck-mail]');
		var book   = fitcheck.querySelector('[data-fitcheck-book]');
		var steps  = quiz ? Array.prototype.slice.call(quiz.querySelectorAll('[data-fitcheck-step]')) : [];
		var answers = {};

		if (quiz && result && steps.length) {
			quiz.hidden   = false;
			result.hidden = true;
			if (skip) {
				skip.hidden = false;
			}

			var setPrimaryAction = function (primary) {
				if (!book || !mail) {
					return;
				}
				book.classList.toggle('nx-btn--primary', primary === 'book');
				book.classList.toggle('nx-btn--ghost', primary !== 'book');
				mail.classList.toggle('nx-btn--primary', primary === 'mail');
				mail.classList.toggle('nx-btn--ghost', primary !== 'mail');
			};

			var getRecommendation = function () {
				if (answers.anlass === 'spaeter') {
					return {
						outcome: 'later',
						note: 'Noch kein konkretes Projekt? Dann ist ein Termin meist zu früh. Schildert mir Anlass und gewünschten Zeitraum kurz per E-Mail; ich sage ehrlich, ob eine Abstimmung jetzt sinnvoll ist.',
						primary: 'mail',
						bookLabel: 'Trotzdem Termin wählen',
						mailLabel: 'Bedarf kurz schriftlich schildern'
					};
				}

				if (answers.vorhaben === 'retainer') {
					return {
						outcome: 'retainer_after_project',
						note: 'Laufende Kapazität startet nicht direkt als Retainer. Wir wählen zuerst ein klar abgegrenztes Erstprojekt, an dem beide Seiten die Zusammenarbeit prüfen.',
						primary: 'book',
						bookLabel: 'Passendes Erstprojekt besprechen',
						mailLabel: 'Vorhaben schriftlich schildern'
					};
				}

				if (answers.klarheit === 'einordnen') {
					return {
						outcome: 'scope_first',
						note: 'Der Scope ist noch nicht klar genug für ein Festpreisangebot. Im Fit-Gespräch klären wir Ziel, vorhandene Grundlagen und den nächsten sinnvollen Schritt; erst danach wird ein Projekt angeboten.',
						primary: 'book',
						bookLabel: 'Scope im Fit-Gespräch klären',
						mailLabel: 'Vorhaben schriftlich schildern'
					};
				}

				if (answers.vorhaben === 'testsprint') {
					return {
						outcome: 'test_sprint',
						note: 'Das passt grundsätzlich zum WordPress-Test-Sprint: eine klar abgegrenzte Aufgabe für 590 € netto. Vor Start bestätige ich schriftlich, ob Aufgabe und Umfang in den Sprint passen.',
						primary: 'mail',
						bookLabel: 'Test-Sprint kurz besprechen',
						mailLabel: 'Aufgabe schriftlich senden'
					};
				}

				return {
					outcome: 'larger_project',
					note: 'Das klingt nach einem größeren Erstprojekt. Im Gespräch klären wir Ziel, Scope, Zugänge und Delivery-Fenster; danach folgt ein Festpreisangebot.',
					primary: 'book',
					bookLabel: 'Erstprojekt im Fit-Gespräch klären',
					mailLabel: 'Vorhaben schriftlich schildern'
				};
			};

			var finishFitcheck = function () {
				if (mail) {
					var lines = [];
					steps.forEach(function (stepEl) {
						var chosen = stepEl.querySelector('.wl-fitcheck__opt.is-selected');
						if (chosen) {
							var question = stepEl.querySelector('.wl-fitcheck__q');
							lines.push((question ? question.textContent.trim() : chosen.getAttribute('data-fitcheck-key')) + '\n' + chosen.getAttribute('data-fitcheck-label'));
						}
					});
					var base = (mail.getAttribute('href') || '').split('?')[0];
					if (base) {
						mail.setAttribute(
							'href',
							base
								+ '?subject=' + encodeURIComponent('White-Label Projekt-Fit')
								+ '&body=' + encodeURIComponent('Kurz zu uns:\n' + lines.join('\n') + '\n\n')
						);
					}
				}

				var recommendation = getRecommendation();
				fitcheck.setAttribute('data-fitcheck-outcome', recommendation.outcome);
				if (note) {
					note.textContent = recommendation.note;
				}
				if (book) {
					book.textContent = recommendation.bookLabel;
				}
				if (mail) {
					mail.textContent = recommendation.mailLabel;
				}
				setPrimaryAction(recommendation.primary);

				quiz.hidden   = true;
				result.hidden = false;
				if (note) {
					note.hidden = false;
				}
				if (skip) {
					skip.hidden = true;
				}
				try { result.focus({ preventScroll: true }); } catch (e) { /* Fokus optional */ }
			};

			quiz.addEventListener('click', function (e) {
				var opt = e.target.closest ? e.target.closest('.wl-fitcheck__opt') : null;
				if (!opt) {
					return;
				}
				var stepEl = opt.closest('[data-fitcheck-step]');
				if (!stepEl) {
					return;
				}

				stepEl.querySelectorAll('.wl-fitcheck__opt').forEach(function (btn) {
					btn.classList.remove('is-selected');
				});
				opt.classList.add('is-selected');
				answers[opt.getAttribute('data-fitcheck-key')] = opt.getAttribute('data-fitcheck-value');

				var next = steps[steps.indexOf(stepEl) + 1];
				if (!next) {
					finishFitcheck();
					return;
				}
				stepEl.hidden = true;
				next.hidden   = false;
				var question = next.querySelector('.wl-fitcheck__q');
				if (question) {
					question.setAttribute('tabindex', '-1');
					try { question.focus({ preventScroll: true }); } catch (e) { /* Fokus optional */ }
				}
			});
		}
	}
})();
