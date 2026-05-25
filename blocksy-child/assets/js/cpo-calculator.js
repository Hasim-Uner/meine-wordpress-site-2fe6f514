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

	function setOutput(scenario, key, value) {
		var output = scenario.querySelector('[data-cpo-output="' + key + '"]');
		if (output) {
			output.textContent = value;
		}
	}

	function calculateScenario(scenario) {
		var cpl = getInput(scenario, 'cpl');
		var leads = getInput(scenario, 'leads');
		var closeRate = getInput(scenario, 'close_rate') / 100;
		var orderValue = getInput(scenario, 'order_value');
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

		setOutput(scenario, 'orders', numberFormatter.format(orders));
		setOutput(scenario, 'lead_cpo', currencyFormatter.format(leadCpo));
		setOutput(scenario, 'full_cpo', currencyFormatter.format(fullCpo));
		setOutput(scenario, 'margin_after_acq', currencyFormatter.format(marginAfterAcq));
		setOutput(scenario, 'break_even_rate', numberFormatter.format(breakEvenRate) + ' %');

		return {
			orders: orders,
			fullCpo: fullCpo,
			marginAfterAcq: marginAfterAcq
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

		var currentResult = calculateScenario(current);
		var targetResult = calculateScenario(target);
		var delta = currentResult.fullCpo - targetResult.fullCpo;
		var absDelta = Math.abs(delta);
		var verdict;

		if (delta > 0) {
			verdict = 'Die eigene Strecke liegt in dieser Rechnung um ' + currencyFormatter.format(absDelta) + ' pro Auftrag besser.';
		} else if (delta < 0) {
			verdict = 'Die aktuelle Strecke liegt in dieser Rechnung um ' + currencyFormatter.format(absDelta) + ' pro Auftrag besser. Dann ist Portal-Zukauf als Übergang wirtschaftlich plausibel.';
		} else {
			verdict = 'Beide Strecken liegen aktuell gleichauf. Prüfen Sie Datenbesitz, Exklusivität und Skalierbarkeit als nächste Kriterien.';
		}

		if (deltaOutput) {
			deltaOutput.textContent = (delta >= 0 ? '-' : '+') + currencyFormatter.format(absDelta).replace(/\s/g, ' ');
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
