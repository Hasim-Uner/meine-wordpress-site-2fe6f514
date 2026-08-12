(function () {
	'use strict';

	var currencyFormatter = new Intl.NumberFormat('de-DE', {
		style: 'currency',
		currency: 'EUR',
		maximumFractionDigits: 0
	});

	var numberFormatter = new Intl.NumberFormat('de-DE', {
		maximumFractionDigits: 1
	});

	function toNumber(value) {
		var parsed = parseFloat(String(value).replace(',', '.'));
		return Number.isFinite(parsed) ? parsed : 0;
	}

	function getInput(scenario, key) {
		var input = scenario.querySelector('[data-cpo-input="' + key + '"]');

		if (!input) {
			return 0;
		}

		var value = toNumber(input.value);
		var min = input.hasAttribute('min') ? toNumber(input.getAttribute('min')) : Number.NEGATIVE_INFINITY;
		var max = input.hasAttribute('max') ? toNumber(input.getAttribute('max')) : Number.POSITIVE_INFINITY;

		return Math.min(max, Math.max(min, value));
	}

	function getShared(calculator, key, fallback) {
		var input = calculator.querySelector('[data-cpo-shared="' + key + '"]');
		return input ? toNumber(input.value) : fallback;
	}

	function setText(scenario, attr, key, value) {
		var node = scenario.querySelector('[data-cpo-' + attr + '="' + key + '"]');
		if (node) {
			node.textContent = value;
		}
	}

	function calculateScenario(scenario, orderValue) {
		var cpl = getInput(scenario, 'cpl');
		var leads = getInput(scenario, 'leads');
		var closeRatePct = getInput(scenario, 'close_rate');
		var closeRate = closeRatePct / 100;
		var marginRate = getInput(scenario, 'margin_rate') / 100;
		var salesMinutes = getInput(scenario, 'sales_minutes');
		var hourlyRate = getInput(scenario, 'hourly_rate');
		var monthlyCosts = getInput(scenario, 'monthly_costs');

		var orders = leads * closeRate;
		var leadSpend = leads * cpl;
		var salesCost = leads * (salesMinutes / 60) * hourlyRate;
		var totalAcq = leadSpend + salesCost + monthlyCosts;
		var grossMargin = orderValue * marginRate;
		var leadCpo = orders > 0 ? leadSpend / orders : 0;
		var fullCpo = orders > 0 ? totalAcq / orders : 0;
		var marginAfterAcq = grossMargin - fullCpo;
		var breakEvenRate = leads > 0 && grossMargin > 0 ? (totalAcq / (leads * grossMargin)) * 100 : 0;

		// Live-Anzeigen an den Slidern.
		setText(scenario, 'readout', 'cpl', currencyFormatter.format(cpl));
		setText(scenario, 'readout', 'close_rate', numberFormatter.format(closeRatePct) + ' %');
		setText(scenario, 'hint', 'close_rate', '≈ ' + Math.round(closeRate * 100) + ' von 100 Anfragen werden Auftrag');

		// Hero-Zahl + Detail-Kennzahlen.
		setText(scenario, 'output', 'full_cpo', orders > 0 ? currencyFormatter.format(fullCpo) : '–');
		setText(scenario, 'output', 'orders', numberFormatter.format(orders));
		setText(scenario, 'output', 'lead_cpo', orders > 0 ? currencyFormatter.format(leadCpo) : '–');
		setText(scenario, 'output', 'margin_after_acq', orders > 0 ? currencyFormatter.format(marginAfterAcq) : '–');
		setText(scenario, 'output', 'break_even_rate', grossMargin > 0 ? numberFormatter.format(breakEvenRate) + ' %' : '–');

		return {
			orders: orders,
			fullCpo: fullCpo
		};
	}

	function updatePortalCalculator(calculator) {
		var requireComplete = calculator.getAttribute('data-cpo-require-complete') === 'true';
		var inputs = Array.prototype.slice.call(calculator.querySelectorAll('[data-cpo-input]'));
		var validation = calculator.querySelector('[data-cpo-validation]');
		var emptyInputs = inputs.filter(function (input) {
			return String(input.value).trim() === '';
		});
		var invalidInputs = inputs.filter(function (input) {
			return String(input.value).trim() !== '' && !input.checkValidity();
		});
		var complete = inputs.every(function (input) {
			return String(input.value).trim() !== '' && input.checkValidity();
		});
		complete = complete && getInput(calculator, 'leads') > 0 && getInput(calculator, 'close_rate') > 0;

		inputs.forEach(function (input) {
			var isInvalid = input.hasAttribute('data-cpo-touched') && (
				!input.checkValidity() ||
				(input.getAttribute('data-cpo-input') === 'leads' && getInput(calculator, 'leads') <= 0) ||
				(input.getAttribute('data-cpo-input') === 'close_rate' && getInput(calculator, 'close_rate') <= 0)
			);
			if (isInvalid) {
				input.setAttribute('aria-invalid', 'true');
			} else {
				input.removeAttribute('aria-invalid');
			}
		});

		if (requireComplete && !complete) {
			if (validation) {
				if (invalidInputs.length) {
					validation.textContent = 'Mindestens ein Wert liegt außerhalb des erlaubten Bereichs. Prüfen Sie die markierten Felder.';
				} else if (!emptyInputs.length) {
					validation.textContent = 'Anfragen und Abschlussquote müssen größer als null sein.';
				} else {
					validation.textContent = '* Pflichtfelder. Tragen Sie alle fünf Werte ein; Anfragen und Abschlussquote müssen größer als null sein.';
				}
			}
			setText(calculator, 'output', 'monthly_lead_cost', '–');
			setText(calculator, 'output', 'orders', '–');
			setText(calculator, 'output', 'sales_hours', '–');
			setText(calculator, 'output', 'full_cpo', '–');
			return;
		}

		if (validation) {
			validation.textContent = 'Berechnung aktualisiert. Es werden keine Werte gespeichert oder übertragen.';
		}

		var cpl = getInput(calculator, 'cpl');
		var leads = getInput(calculator, 'leads');
		var closeRate = getInput(calculator, 'close_rate') / 100;
		var salesMinutes = getInput(calculator, 'sales_minutes');
		var hourlyRate = getInput(calculator, 'hourly_rate');
		var monthlyLeadCost = leads * cpl;
		var orders = leads * closeRate;
		var salesHours = leads * (salesMinutes / 60);
		var salesCost = salesHours * hourlyRate;
		var fullCpo = orders > 0 ? (monthlyLeadCost + salesCost) / orders : 0;

		setText(calculator, 'output', 'monthly_lead_cost', currencyFormatter.format(monthlyLeadCost));
		setText(calculator, 'output', 'orders', numberFormatter.format(orders));
		setText(calculator, 'output', 'sales_hours', numberFormatter.format(salesHours) + ' Std.');
		setText(calculator, 'output', 'full_cpo', orders > 0 ? currencyFormatter.format(fullCpo) : '–');
	}

	function updateCalculator(calculator) {
		if (calculator.getAttribute('data-cpo-mode') === 'portal') {
			updatePortalCalculator(calculator);
			return;
		}

		var current = calculator.querySelector('[data-cpo-scenario="current"]');
		var target = calculator.querySelector('[data-cpo-scenario="target"]');
		var deltaOutput = calculator.querySelector('[data-cpo-summary="delta_cpo"]');
		var verdictOutput = calculator.querySelector('[data-cpo-summary="verdict"]');

		if (!current || !target) {
			return;
		}

		var orderValue = getShared(calculator, 'order_value', 0);
		var currentResult = calculateScenario(current, orderValue);
		var targetResult = calculateScenario(target, orderValue);

		if (!(currentResult.orders > 0) || !(targetResult.orders > 0)) {
			if (deltaOutput) {
				deltaOutput.textContent = '–';
			}
			if (verdictOutput) {
				verdictOutput.textContent = 'Tragen Sie für beide Seiten Anfragen und Abschlussquote ein, um den Unterschied zu sehen.';
			}
			return;
		}

		var delta = currentResult.fullCpo - targetResult.fullCpo;
		var absDelta = Math.abs(delta);
		var verdict;

		if (delta > 0) {
			verdict = 'Der eigene Anfrage-Weg ist in dieser Rechnung um ' + currencyFormatter.format(absDelta) + ' pro Auftrag günstiger.';
		} else if (delta < 0) {
			verdict = 'Die gekauften Anfragen sind hier um ' + currencyFormatter.format(absDelta) + ' pro Auftrag günstiger. Dann ist Zukauf als Übergang wirtschaftlich plausibel.';
		} else {
			verdict = 'Beide Wege liegen gleichauf. Prüfen Sie Datenbesitz, Exklusivität und Planbarkeit als nächste Kriterien.';
		}

		if (deltaOutput) {
			deltaOutput.textContent = (delta >= 0 ? '-' : '+') + currencyFormatter.format(absDelta);
		}

		if (verdictOutput) {
			verdictOutput.textContent = verdict;
		}
	}

	function initCalculator(calculator) {
		var updateTimer = 0;

		calculator.addEventListener('blur', function (event) {
			if (!event.target.matches('[data-cpo-input]')) {
				return;
			}

			event.target.setAttribute('data-cpo-touched', '');
			updateCalculator(calculator);
		}, true);

		calculator.addEventListener('input', function () {
			if (calculator.getAttribute('data-cpo-mode') !== 'portal') {
				updateCalculator(calculator);
				return;
			}

			window.clearTimeout(updateTimer);
			updateTimer = window.setTimeout(function () {
				updateCalculator(calculator);
			}, 120);
		});
		updateCalculator(calculator);
	}

	document.addEventListener('DOMContentLoaded', function () {
		document.querySelectorAll('[data-hu-cpo-calculator]').forEach(initCalculator);
	});
}());
