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
		return input ? toNumber(input.value) : 0;
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

	function updateCalculator(calculator) {
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
		calculator.addEventListener('input', function () {
			updateCalculator(calculator);
		});
		updateCalculator(calculator);
	}

	document.addEventListener('DOMContentLoaded', function () {
		document.querySelectorAll('[data-hu-cpo-calculator]').forEach(initCalculator);
	});
}());
