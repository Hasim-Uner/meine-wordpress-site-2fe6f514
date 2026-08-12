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
				event_section: node.getAttribute('data-track-section') || 'aroundhome_decision_page'
			});
		} catch (error) {
			// Analytics must never block the decision tools.
		}
	}

	function focusFromHash(root, hash) {
		var target;

		if (!hash || hash.length < 2) {
			return;
		}

		try {
			target = root.querySelector(hash);
		} catch (error) {
			return;
		}

		if (!target) {
			return;
		}

		window.setTimeout(function () {
			target.focus({ preventScroll: true });
			target.scrollIntoView({ block: 'start', behavior: 'auto' });
		}, 0);
	}

	function initAnchorFocus(root) {
		root.querySelectorAll('a[href^="#"]').forEach(function (link) {
			link.addEventListener('click', function (event) {
				var hash = link.getAttribute('href');
				event.preventDefault();

				if (hash && window.location.hash !== hash && window.history && window.history.pushState) {
					window.history.pushState(null, '', hash);
				}

				focusFromHash(root, hash);
			});
		});

		window.addEventListener('hashchange', function () {
			focusFromHash(root, window.location.hash);
		});

		if (window.location.hash) {
			focusFromHash(root, window.location.hash);
		}
	}

	function initContractCheck(root) {
		var ledger = root.querySelector('[data-aroundhome-contract]');

		if (!ledger) {
			return;
		}

		var checks = Array.prototype.slice.call(ledger.querySelectorAll('[data-contract-check]'));
		var result = ledger.querySelector('[data-contract-result]');
		var detail = ledger.querySelector('[data-contract-detail]');
		var completionTracked = false;

		if (!checks.length || !result || !detail) {
			return;
		}

		function update() {
			var clarified = checks.filter(function (check) {
				return check.checked;
			}).length;

			result.textContent = clarified + ' von ' + checks.length + ' schriftlich geklärt';

			if (clarified === checks.length) {
				detail.textContent = 'Die Entscheidungsgrundlage ist dokumentiert. Prüfen Sie jetzt, ob der berechnete CPO zu Deckungsbeitrag und Vertriebskapazität passt.';
			} else if (clarified >= Math.ceil(checks.length / 2)) {
				detail.textContent = 'Die Basis steht. Schließen Sie die offenen Punkte, bevor Annahmen zu Kosten oder Risiko werden.';
			} else {
				detail.textContent = 'Solange Grundlagen offen sind, rechnet der Kostenvergleich mit Annahmen statt mit vertraglich geklärten Werten.';
			}

			if (!completionTracked && clarified === checks.length) {
				completionTracked = true;
				pushTrack(detail);
			}
		}

		ledger.addEventListener('change', function (event) {
			if (event.target.matches('[data-contract-check]')) {
				update();
			}
		});

		// Browsers may restore checkbox states on back-navigation.
		update();
	}

	function initCalculatorTracking(root) {
		var calculator = root.querySelector('[data-hu-cpo-calculator][data-cpo-mode="portal"]');
		var results = calculator && calculator.querySelector('[data-cpo-results]');
		var interacted = false;
		var resultVisible = false;
		var resultTracked = false;

		if (!calculator || !results) {
			return;
		}

		function isComplete() {
			var inputs = Array.prototype.slice.call(calculator.querySelectorAll('[data-cpo-input]'));
			var leads = calculator.querySelector('[data-cpo-input="leads"]');
			var closeRate = calculator.querySelector('[data-cpo-input="close_rate"]');

			return inputs.length > 0 && inputs.every(function (input) {
				return String(input.value).trim() !== '' && input.checkValidity();
			}) && Number(leads && leads.value) > 0 && Number(closeRate && closeRate.value) > 0;
		}

		function trackResultView() {
			if (!interacted || !resultVisible || resultTracked || !isComplete()) {
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
					if (entry.isIntersecting) {
						resultVisible = true;
						trackResultView();
					}
				});
			}, { threshold: 0.55 });

			observer.observe(results);
			return;
		}

		resultVisible = true;
	}

	function initFaqTracking(root) {
		root.querySelectorAll('.hu-aroundhome__faq-list details').forEach(function (item) {
			item.addEventListener('toggle', function () {
				if (item.open) {
					pushTrack(item);
				}
			});
		});
	}

	function init(root) {
		initAnchorFocus(root);
		initContractCheck(root);
		initCalculatorTracking(root);
		initFaqTracking(root);
	}

	document.addEventListener('DOMContentLoaded', function () {
		document.querySelectorAll('[data-aroundhome-decision]').forEach(init);
	});
}());
