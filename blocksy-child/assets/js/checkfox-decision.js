(function () {
	'use strict';

	function pushTrack(node) {
		var action = node && node.getAttribute('data-track-action');

		if (!action) {
			return;
		}

		try {
			window.dataLayer = window.dataLayer || [];
			window.dataLayer.push({
				event: action,
				event_category: node.getAttribute('data-track-category') || 'engagement',
				event_section: node.getAttribute('data-track-section') || 'checkfox_decision_page'
			});
		} catch (error) {
			// Tracking must never block the decision tools.
		}
	}

	function initIntent(root) {
		var intent = root.querySelector('[data-checkfox-intent]');

		if (!intent) {
			return;
		}

		var controls = intent.querySelectorAll('input[type="radio"]');
		var answers = intent.querySelectorAll('[data-checkfox-intent-answer]');

		intent.addEventListener('change', function (event) {
			var control = event.target;

			if (!control.matches('input[type="radio"]')) {
				return;
			}

			answers.forEach(function (answer) {
				answer.hidden = answer.getAttribute('data-checkfox-intent-answer') !== control.value;
			});
		});

		controls.forEach(function (control) {
			control.setAttribute('aria-controls', 'checkfox-intent-answer');
		});
	}

	function initAnchorFocus(root) {
		root.querySelectorAll('a[href^="#"]').forEach(function (link) {
			link.addEventListener('click', function () {
				var target = document.querySelector(link.getAttribute('href'));

				if (!target) {
					return;
				}

				window.setTimeout(function () {
					target.focus({ preventScroll: true });
				}, 0);
			});
		});
	}

	function initCalculatorTracking(root) {
		var calculator = root.querySelector('[data-hu-cpo-calculator][data-cpo-mode="portal"]');
		var results = calculator && calculator.querySelector('[data-cpo-results]');

		if (!calculator || !results) {
			return;
		}

		var interacted = false;
		var resultVisible = false;
		var resultTracked = false;

		function trackResultView() {
			if (!interacted || !resultVisible || resultTracked) {
				return;
			}

			resultTracked = true;
			pushTrack(results);
		}

		calculator.addEventListener('input', function () {
			if (!interacted) {
				interacted = true;
				pushTrack(calculator);
			}

			trackResultView();
		});

		if (typeof window.IntersectionObserver === 'function') {
			var observer = new window.IntersectionObserver(function (entries) {
				entries.forEach(function (entry) {
					if (!entry.isIntersecting) {
						return;
					}

					resultVisible = true;
					trackResultView();
				});
			}, { threshold: 0.55 });

			observer.observe(results);
			return;
		}

		resultVisible = true;
	}

	function initContract(root) {
		var contract = root.querySelector('[data-checkfox-contract]');

		if (!contract) {
			return;
		}

		var rows = contract.querySelectorAll('[data-contract-row]');
		var result = contract.querySelector('[data-contract-result]');
		var detail = contract.querySelector('[data-contract-detail]');
		var resultBox = result && result.closest('.hu-checkfox__contract-result');
		var touchedRows = new Set();
		var completionTracked = false;

		function updateResult() {
			var clarified = 0;
			var unclear = 0;
			var notApplicable = 0;

			rows.forEach(function (row) {
				var selected = row.querySelector('input[type="radio"]:checked');

				if (!selected) {
					return;
				}

				if (selected.value === 'geklaert') {
					clarified += 1;
				} else if (selected.value === 'nicht-zutreffend') {
					notApplicable += 1;
				} else {
					unclear += 1;
				}
			});

			if (unclear === 0 && clarified >= 5) {
				result.textContent = 'Weitgehend geklärt';
				detail.textContent = 'Die wesentlichen Angaben sind dokumentiert. Prüfen Sie jetzt, ob die Kosten pro Auftrag zu Marge und Vertriebskapazität passen.';
			} else if (unclear <= 3 && clarified + notApplicable >= 5) {
				result.textContent = 'Vor Vertragsabschluss nachschärfen';
				detail.textContent = unclear + ' Punkt' + (unclear === 1 ? ' ist' : 'e sind') + ' noch ungeklärt. Schließen Sie diese Lücken vor der wirtschaftlichen Entscheidung.';
			} else {
				result.textContent = 'Wirtschaftliche Entscheidung noch nicht belastbar';
				detail.textContent = unclear + ' Punkte sind noch ungeklärt. Klären Sie zuerst die Grundlagen und rechnen Sie danach mit belastbaren Werten.';
			}

			if (!completionTracked && touchedRows.size === rows.length) {
				completionTracked = true;
				pushTrack(resultBox);
			}
		}

		contract.addEventListener('change', function (event) {
			var control = event.target;

			if (!control.matches('[data-contract-check]')) {
				return;
			}

			var row = control.closest('[data-contract-row]');

			if (row) {
				touchedRows.add(row.getAttribute('data-contract-row'));
			}

			updateResult();
		});
	}

	function initCockpit(root) {
		initIntent(root);
		initAnchorFocus(root);
		initCalculatorTracking(root);
		initContract(root);
	}

	document.addEventListener('DOMContentLoaded', function () {
		document.querySelectorAll('[data-checkfox-cockpit]').forEach(initCockpit);
	});
}());
