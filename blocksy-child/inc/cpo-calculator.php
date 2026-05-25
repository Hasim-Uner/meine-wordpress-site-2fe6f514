<?php
/**
 * CPO calculator shortcode for solar lead economics.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render a compact two-scenario CPO calculator.
 *
 * @return string
 */
function hu_cpo_calculator_shortcode() {
	$marktcheck_url = function_exists( 'hu_get_request_analysis_url' )
		? hu_get_request_analysis_url()
		: home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' );

	$scenarios = [
		'current' => [
			'title'         => 'Aktuelle Strecke',
			'label'         => 'Portal / Agentur-Funnel',
			'cpl'           => 90,
			'leads'         => 100,
			'close_rate'    => 5,
			'order_value'   => 18000,
			'margin_rate'   => 25,
			'sales_minutes' => 35,
			'hourly_rate'   => 60,
			'monthly_costs' => 0,
		],
		'target'  => [
			'title'         => 'Eigene Strecke',
			'label'         => 'First-Party-Anfragen',
			'cpl'           => 170,
			'leads'         => 60,
			'close_rate'    => 15,
			'order_value'   => 18000,
			'margin_rate'   => 25,
			'sales_minutes' => 25,
			'hourly_rate'   => 60,
			'monthly_costs' => 2500,
		],
	];

	ob_start();
	?>
	<section class="hu-cpo-calculator" data-hu-cpo-calculator data-track-section="cpo_calculator" aria-labelledby="hu-cpo-calculator-title">
		<div class="hu-cpo-calculator__intro">
			<p class="hu-cpo-calculator__eyebrow">Live-Rechnung</p>
			<h3 class="hu-cpo-calculator__title" id="hu-cpo-calculator-title">CPO-Rechner für Photovoltaik-Anfragen</h3>
			<p class="hu-cpo-calculator__text">
				Vergleichen Sie aktuelle Leadmiete mit einer eigenen Anfrage-Strecke. Entscheidend ist nicht der CPL, sondern der volle Akquisekostenblock pro gewonnenem Auftrag.
			</p>
		</div>

		<div class="hu-cpo-calculator__scenarios">
			<?php foreach ( $scenarios as $scenario_key => $scenario ) : ?>
				<div class="hu-cpo-calculator__scenario" data-cpo-scenario="<?php echo esc_attr( $scenario_key ); ?>">
					<div class="hu-cpo-calculator__scenario-head">
						<h4><?php echo esc_html( $scenario['title'] ); ?></h4>
						<input type="text" data-cpo-input="label" value="<?php echo esc_attr( $scenario['label'] ); ?>" aria-label="<?php echo esc_attr( $scenario['title'] . ' Bezeichnung' ); ?>">
					</div>

					<div class="hu-cpo-calculator__fields">
						<label>
							<span>CPL in Euro</span>
							<input type="number" min="0" step="5" data-cpo-input="cpl" value="<?php echo esc_attr( (string) $scenario['cpl'] ); ?>">
						</label>
						<label>
							<span>Leads pro Monat</span>
							<input type="number" min="0" step="1" data-cpo-input="leads" value="<?php echo esc_attr( (string) $scenario['leads'] ); ?>">
						</label>
						<label>
							<span>Abschlussquote in %</span>
							<input type="number" min="0" max="100" step="0.5" data-cpo-input="close_rate" value="<?php echo esc_attr( (string) $scenario['close_rate'] ); ?>">
						</label>
						<label>
							<span>Auftragswert in Euro</span>
							<input type="number" min="0" step="500" data-cpo-input="order_value" value="<?php echo esc_attr( (string) $scenario['order_value'] ); ?>">
						</label>
						<label>
							<span>Deckungsbeitrag in %</span>
							<input type="number" min="0" max="100" step="1" data-cpo-input="margin_rate" value="<?php echo esc_attr( (string) $scenario['margin_rate'] ); ?>">
						</label>
						<label>
							<span>Vertriebszeit pro Lead</span>
							<input type="number" min="0" step="5" data-cpo-input="sales_minutes" value="<?php echo esc_attr( (string) $scenario['sales_minutes'] ); ?>">
						</label>
						<label>
							<span>Stundensatz Vertrieb</span>
							<input type="number" min="0" step="5" data-cpo-input="hourly_rate" value="<?php echo esc_attr( (string) $scenario['hourly_rate'] ); ?>">
						</label>
						<label>
							<span>Monatskosten System</span>
							<input type="number" min="0" step="100" data-cpo-input="monthly_costs" value="<?php echo esc_attr( (string) $scenario['monthly_costs'] ); ?>">
						</label>
					</div>

					<div class="hu-cpo-calculator__results" aria-live="polite">
						<div>
							<span>Gewonnene Aufträge</span>
							<strong data-cpo-output="orders">-</strong>
						</div>
						<div>
							<span>Lead-CPO</span>
							<strong data-cpo-output="lead_cpo">-</strong>
						</div>
						<div>
							<span>Voller CPO</span>
							<strong data-cpo-output="full_cpo">-</strong>
						</div>
						<div>
							<span>DB nach Akquise</span>
							<strong data-cpo-output="margin_after_acq">-</strong>
						</div>
						<div>
							<span>Break-even Quote</span>
							<strong data-cpo-output="break_even_rate">-</strong>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>

		<div class="hu-cpo-calculator__summary">
			<div>
				<span>Differenz voller CPO</span>
				<strong data-cpo-summary="delta_cpo">-</strong>
			</div>
			<p data-cpo-summary="verdict">Passen Sie die Werte an, um den wirtschaftlichen Unterschied zu sehen.</p>
			<a class="hu-cpo-calculator__cta"
			   href="<?php echo esc_url( $marktcheck_url ); ?>"
			   data-track-action="cta_marktcheck"
			   data-track-category="cpo_calculator"
			   data-track-section="cpo_calculator">
				Marktcheck mit eigenen Zahlen starten
			</a>
		</div>
	</section>
	<?php

	return trim( (string) ob_get_clean() );
}

add_shortcode( 'hu_cpo_calculator', 'hu_cpo_calculator_shortcode' );
