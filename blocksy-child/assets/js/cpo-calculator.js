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

	function calculateCohort(input) {
		var mediaCosts = Math.max(0, toNumber(input.mediaCosts));
		var setupCosts = Math.max(0, toNumber(input.setupCosts));
		var supportSoftware = Math.max(0, toNumber(input.supportSoftware));
		var leads = Math.max(0, toNumber(input.leads));
		var wonOrders = Math.max(0, toNumber(input.wonOrders));
		var salesMinutes = Math.max(0, toNumber(input.salesMinutes));
		var hourlyRate = Math.max(0, toNumber(input.hourlyRate));
		var mature = input.mature !== false;
		var salesHours = leads * (salesMinutes / 60);
		var salesCosts = salesHours * hourlyRate;
		var totalCosts = mediaCosts + setupCosts + supportSoftware + salesCosts;
		var fullCpo = wonOrders > 0 ? totalCosts / wonOrders : null;

		return {
			mediaCosts: mediaCosts,
			setupCosts: setupCosts,
			supportSoftware: supportSoftware,
			leads: leads,
			wonOrders: wonOrders,
			salesHours: salesHours,
			salesCosts: salesCosts,
			totalCosts: totalCosts,
			fullCpo: fullCpo,
			mature: mature
		};
	}

	window.HuCpoV2 = window.HuCpoV2 || {};
	window.HuCpoV2.calculateCohort = calculateCohort;

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
		var estimatedOrders = leads * closeRate;
		var leadSpend = leads * cpl;
		var result = calculateCohort({
			mediaCosts: leadSpend,
			setupCosts: 0,
			supportSoftware: monthlyCosts,
			leads: leads,
			wonOrders: estimatedOrders,
			salesMinutes: salesMinutes,
			hourlyRate: hourlyRate,
			mature: false
		});
		var grossMargin = orderValue * marginRate;
		var leadCpo = estimatedOrders > 0 ? leadSpend / estimatedOrders : null;
		var marginAfterAcq = result.fullCpo !== null ? grossMargin - result.fullCpo : null;
		var breakEvenRate = leads > 0 && grossMargin > 0 ? (result.totalCosts / (leads * grossMargin)) * 100 : 0;

		setText(scenario, 'readout', 'cpl', currencyFormatter.format(cpl));
		setText(scenario, 'readout', 'close_rate', numberFormatter.format(closeRatePct) + ' %');
		setText(scenario, 'hint', 'close_rate', '≈ ' + Math.round(closeRate * 100) + ' von 100 Anfragen werden modelliert als Auftrag');
		setText(scenario, 'output', 'full_cpo', result.fullCpo !== null ? currencyFormatter.format(result.fullCpo) : 'noch nicht bestimmbar');
		setText(scenario, 'output', 'orders', numberFormatter.format(estimatedOrders));
		setText(scenario, 'output', 'lead_cpo', leadCpo !== null ? currencyFormatter.format(leadCpo) : '–');
		setText(scenario, 'output', 'margin_after_acq', marginAfterAcq !== null ? currencyFormatter.format(marginAfterAcq) : '–');
		setText(scenario, 'output', 'break_even_rate', grossMargin > 0 ? numberFormatter.format(breakEvenRate) + ' %' : '–');

		return { orders: estimatedOrders, fullCpo: result.fullCpo };
	}

	function updatePortalCalculator(calculator) {
		var requireComplete = calculator.getAttribute('data-cpo-require-complete') === 'true';
		var inputs = Array.prototype.slice.call(calculator.querySelectorAll('[data-cpo-input]'));
		var validation = calculator.querySelector('[data-cpo-validation]');
		var emptyInputs = inputs.filter(function (input) { return String(input.value).trim() === ''; });
		var invalidInputs = inputs.filter(function (input) { return String(input.value).trim() !== '' && !input.checkValidity(); });
		var complete = inputs.every(function (input) { return String(input.value).trim() !== '' && input.checkValidity(); });
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
		var estimatedOrders = leads * closeRate;
		var result = calculateCohort({
			mediaCosts: monthlyLeadCost,
			setupCosts: 0,
			supportSoftware: 0,
			leads: leads,
			wonOrders: estimatedOrders,
			salesMinutes: salesMinutes,
			hourlyRate: hourlyRate,
			mature: false
		});

		setText(calculator, 'output', 'monthly_lead_cost', currencyFormatter.format(monthlyLeadCost));
		setText(calculator, 'output', 'orders', numberFormatter.format(estimatedOrders));
		setText(calculator, 'output', 'sales_hours', numberFormatter.format(result.salesHours) + ' Std.');
		setText(calculator, 'output', 'full_cpo', result.fullCpo !== null ? currencyFormatter.format(result.fullCpo) : 'noch nicht bestimmbar');
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

		if (!(currentResult.orders > 0) || !(targetResult.orders > 0) || currentResult.fullCpo === null || targetResult.fullCpo === null) {
			if (deltaOutput) {
				deltaOutput.textContent = '–';
			}
			if (verdictOutput) {
				verdictOutput.textContent = 'Für beide Seiten braucht die Planungsrechnung Anfragen und eine Abschlussquote größer als null.';
			}
			return;
		}

		var delta = currentResult.fullCpo - targetResult.fullCpo;
		var absDelta = Math.abs(delta);
		if (deltaOutput) {
			deltaOutput.textContent = (delta >= 0 ? '-' : '+') + currencyFormatter.format(absDelta);
		}
		if (verdictOutput) {
			if (delta > 0) {
				verdictOutput.textContent = 'Der eigene Anfrage-Weg ist in dieser Planungsrechnung um ' + currencyFormatter.format(absDelta) + ' pro Auftrag günstiger.';
			} else if (delta < 0) {
				verdictOutput.textContent = 'Die gekauften Anfragen sind hier um ' + currencyFormatter.format(absDelta) + ' pro Auftrag günstiger. Dann kann Zukauf als Übergang wirtschaftlich plausibel sein.';
			} else {
				verdictOutput.textContent = 'Beide Wege liegen in dieser Planungsrechnung gleichauf.';
			}
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
			updateTimer = window.setTimeout(function () { updateCalculator(calculator); }, 120);
		});
		updateCalculator(calculator);
	}

	function makeNumberField(id, label, value, step) {
		var wrap = document.createElement('div');
		wrap.className = 'eingabe';
		var labelNode = document.createElement('label');
		labelNode.setAttribute('for', id);
		labelNode.textContent = label;
		var field = document.createElement('span');
		field.className = 'feld';
		var input = document.createElement('input');
		input.id = id;
		input.type = 'number';
		input.min = '0';
		input.step = step || '1';
		input.inputMode = 'decimal';
		input.value = String(value);
		input.setAttribute('data-cpo-v2-input', id);
		field.appendChild(input);
		wrap.appendChild(labelNode);
		wrap.appendChild(field);
		return { wrap: wrap, input: input };
	}

	function relabelLegacyField(blatt, fieldId, label, unit, value) {
		var input = blatt.querySelector('[data-feld="' + fieldId + '"]');
		if (!input) {
			return null;
		}
		var wrap = input.closest('.eingabe');
		if (wrap) {
			var labelNode = wrap.querySelector('label');
			var unitNode = wrap.querySelector('.einheit');
			if (labelNode) {
				labelNode.textContent = label;
			}
			if (unitNode) {
				unitNode.textContent = unit || '';
			}
		}
		if (value !== undefined) {
			input.value = String(value);
		}
		return input;
	}

	function addCohortStatusField(weg, key) {
		var wrap = document.createElement('div');
		wrap.className = 'eingabe';
		var label = document.createElement('label');
		label.setAttribute('for', 'strecke-' + key + '-mature');
		label.textContent = 'Kohorte abgeschlossen';
		var checkbox = document.createElement('input');
		checkbox.id = 'strecke-' + key + '-mature';
		checkbox.type = 'checkbox';
		checkbox.checked = true;
		checkbox.setAttribute('data-cpo-v2-mature', key);
		wrap.appendChild(label);
		wrap.appendChild(checkbox);
		weg.insertBefore(wrap, weg.querySelector('.summe'));
		return checkbox;
	}

	function upgradeEnergyCalculator() {
		var blatt = document.querySelector('[data-strecke-rechner]');
		if (!blatt || blatt.hasAttribute('data-cpo-v2-upgraded')) {
			return;
		}
		blatt.setAttribute('data-cpo-v2-upgraded', 'true');
		var wege = blatt.querySelectorAll('.weg');
		if (wege.length < 2) {
			return;
		}

		var aufbau = toNumber(blatt.getAttribute('data-aufbau'));
		var monate = Math.max(1, toNumber(blatt.getAttribute('data-monate')) || 24);
		var hosting = toNumber(blatt.getAttribute('data-hosting'));

		var fields = {
			a1: relabelLegacyField(blatt, 'a1', 'Anfragen in dieser Kohorte', 'Stk', 25),
			a2: relabelLegacyField(blatt, 'a2', 'Preis pro Anfrage', '€', 80),
			a3: relabelLegacyField(blatt, 'a3', 'Tatsächlich gewonnene Aufträge', 'Stk', 1),
			b1: relabelLegacyField(blatt, 'b1', 'Media-/Werbekosten dieser Kohorte', '€', 2000),
			b2: relabelLegacyField(blatt, 'b2', 'Anfragen in dieser Kohorte', 'Stk', 44),
			b3: relabelLegacyField(blatt, 'b3', 'Tatsächlich gewonnene Aufträge', 'Stk', 5)
		};

		Object.keys(fields).forEach(function (key) {
			if (!fields[key]) {
				return;
			}
			fields[key].removeAttribute('max');
			fields[key].step = key === 'a2' || key === 'b1' ? '1' : '1';
		});

		var extra = { a: {}, b: {} };
		extra.a.setup = makeNumberField('strecke-a-setup', 'Zuordenbarer Setup-/Produktionsanteil', 0, '50');
		extra.a.support = makeNumberField('strecke-a-support', 'Software / Support dieser Kohorte', 0, '25');
		extra.a.salesMinutes = makeNumberField('strecke-a-sales-minutes', 'Vertriebszeit pro Anfrage (Min.)', 15, '5');
		extra.a.hourly = makeNumberField('strecke-a-hourly', 'Interner Vertriebsstundensatz', 60, '5');
		extra.b.setup = makeNumberField('strecke-b-setup', 'Zuordenbarer Setup-/Produktionsanteil', Math.round(aufbau / monate), '50');
		extra.b.support = makeNumberField('strecke-b-support', 'Software / Support dieser Kohorte', hosting, '25');
		extra.b.salesMinutes = makeNumberField('strecke-b-sales-minutes', 'Vertriebszeit pro Anfrage (Min.)', 10, '5');
		extra.b.hourly = makeNumberField('strecke-b-hourly', 'Interner Vertriebsstundensatz', 60, '5');

		['a', 'b'].forEach(function (key, index) {
			var summe = wege[index].querySelector('.summe');
			['setup', 'support', 'salesMinutes', 'hourly'].forEach(function (name) {
				wege[index].insertBefore(extra[key][name].wrap, summe);
			});
			extra[key].mature = addCohortStatusField(wege[index], key);

			var rows = summe ? summe.querySelectorAll('.z') : [];
			if (rows[0]) {
				rows[0].querySelector('span').textContent = 'Vollkosten der Kohorte';
			}
			if (rows[1]) {
				rows[1].querySelector('span').textContent = 'Tatsächlich gewonnene Aufträge';
			}
			var mainLabel = summe ? summe.querySelector('.haupt .k') : null;
			if (mainLabel) {
				mainLabel.textContent = 'Vollkosten je gewonnenem Auftrag';
			}
			var status = document.createElement('p');
			status.className = 'fuss';
			status.setAttribute('data-cpo-v2-status', key);
			summe.parentNode.insertBefore(status, summe.nextSibling);
		});

		var foot = blatt.parentNode.querySelector('.fuss');
		if (foot && !foot.hasAttribute('data-cpo-v2-status')) {
			foot.innerHTML = '<b>Vollkostenformel</b> — Lead-/Mediakosten + zuordenbarer Setup-/Produktionsanteil + Software/Support + interne Vertriebszeit, geteilt durch die tatsächlich gewonnenen Aufträge derselben Kohorte. Bei 0 Aufträgen lautet das Ergebnis bewusst „noch nicht bestimmbar“. Nicht abgeschlossene Kohorten werden als vorläufig markiert.';
		}

		function val(input) {
			return input ? Math.max(0, toNumber(input.value)) : 0;
		}

		function render(key) {
			var isA = key === 'a';
			var leads = isA ? val(fields.a1) : val(fields.b2);
			var mediaCosts = isA ? val(fields.a1) * val(fields.a2) : val(fields.b1);
			var wonOrders = isA ? val(fields.a3) : val(fields.b3);
			var result = calculateCohort({
				mediaCosts: mediaCosts,
				setupCosts: val(extra[key].setup.input),
				supportSoftware: val(extra[key].support.input),
				leads: leads,
				wonOrders: wonOrders,
				salesMinutes: val(extra[key].salesMinutes.input),
				hourlyRate: val(extra[key].hourly.input),
				mature: extra[key].mature.checked
			});
			var prefix = isA ? 'A' : 'B';
			var costOut = blatt.querySelector('[data-ausgabe="o' + prefix + '1"]');
			var ordersOut = blatt.querySelector('[data-ausgabe="o' + prefix + '2"]');
			var cpoOut = blatt.querySelector('[data-ausgabe="o' + prefix + '3"]');
			var statusOut = blatt.querySelector('[data-cpo-v2-status="' + key + '"]');
			if (costOut) {
				costOut.textContent = currencyFormatter.format(result.totalCosts);
			}
			if (ordersOut) {
				ordersOut.textContent = numberFormatter.format(result.wonOrders);
			}
			if (cpoOut) {
				if (result.fullCpo === null) {
					cpoOut.textContent = 'noch nicht bestimmbar';
				} else {
					cpoOut.textContent = (result.mature ? '' : 'vorläufig · ') + currencyFormatter.format(result.fullCpo);
				}
			}
			if (statusOut) {
				if (result.fullCpo === null) {
					statusOut.textContent = 'Noch kein gewonnener Auftrag in dieser Kohorte. Deshalb wird kein künstlicher CPO ausgegeben.';
				} else if (!result.mature) {
					statusOut.textContent = 'Vorläufig: Die Kohorte ist noch nicht abgeschlossen. Spätere Abschlüsse können den CPO noch verändern.';
				} else {
					statusOut.textContent = 'Abgeschlossene Kohorte · ' + numberFormatter.format(result.salesHours) + ' Vertriebsstunden eingerechnet.';
				}
			}
			return result;
		}

		function update() {
			var a = render('a');
			var b = render('b');
			blatt.setAttribute('data-cpo-v2-state', a.mature && b.mature ? 'mature' : 'preliminary');
		}

		Array.prototype.slice.call(blatt.querySelectorAll('input')).forEach(function (input) {
			input.addEventListener('input', update);
			input.addEventListener('change', update);
		});
		update();
	}

	function start() {
		document.querySelectorAll('[data-hu-cpo-calculator]').forEach(initCalculator);
		upgradeEnergyCalculator();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', start, { once: true });
	} else {
		start();
	}
}());
