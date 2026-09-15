<?php
/**
 * Cost-per-Order calculators for Solar/SHK decision surfaces.
 *
 * The canonical V2 formula is cohort based:
 * media/lead costs + attributable setup/production + support/software
 * + internal sales time, divided by actually won orders from the same cohort.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Load the shared CPO engine on the energy money page as well.
 *
 * The page keeps its existing calculator markup. cpo-calculator.js upgrades that
 * markup after anfragestrecke.js and becomes the final calculation authority.
 *
 * @return void
 */
function hu_enqueue_energy_cpo_v2() {
	if ( ! ( is_page( 'solar-waermepumpen-leadgenerierung' ) || is_page_template( 'page-solar-waermepumpen-leadgenerierung.php' ) ) ) {
		return;
	}

	if ( function_exists( 'hu_enqueue_js' ) ) {
		hu_enqueue_js( 'nexus-cpo-calculator-js', 'cpo-calculator.js', [ 'nexus-anfragestrecke-js' ] );
	}
}
add_action( 'wp_enqueue_scripts', 'hu_enqueue_energy_cpo_v2', 30 );

/**
 * Render the single-scenario portal calculator used by provider decision pages.
 *
 * This mode intentionally remains estimate based because several provider pages
 * ask users to model a prospective contract. Its arithmetic still runs through
 * the same V2 cohort engine in JavaScript.
 *
 * @param array<string, mixed> $args Provider-specific copy, tracking and defaults.
 * @return string
 */
function hu_render_portal_cpo_calculator( $args = [] ) {
	static $instance = 0;
	++$instance;

	$calculator_id  = 'hu-cpo-portal-' . $instance;
	$marktcheck_url = function_exists( 'hu_get_request_analysis_url' )
		? hu_get_request_analysis_url()
		: home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' );

	$default_values = [
		'cpl'           => 90,
		'leads'         => 50,
		'close_rate'    => 5,
		'sales_minutes' => 35,
		'hourly_rate'   => 60,
	];

	$args = wp_parse_args(
		(array) $args,
		[
			'provider_label'   => 'Checkfox',
			'tracking_prefix'  => 'checkfox',
			'tracking_section' => 'checkfox_portal_calculator',
			'defaults'         => $default_values,
			'require_complete' => false,
			'heading'          => 'Was kostet der Portal-Kanal pro gewonnenem Auftrag?',
			'description'      => 'Tragen Sie Ihre echten Vertriebswerte ein. Der Rechner verbindet Anfragepreis, Abschlussquote und interne Vertriebszeit.',
			'example_text'     => '',
			'cta_label'        => 'Mit meinen Zahlen Marktcheck starten',
			'cta_note'         => 'Im Marktcheck werden Region, Projektwert, Abschlussquote und Portalabhängigkeit händisch eingeordnet.',
		]
	);

	$provider_label   = sanitize_text_field( (string) $args['provider_label'] );
	$tracking_prefix  = sanitize_key( (string) $args['tracking_prefix'] );
	$tracking_section = sanitize_key( (string) $args['tracking_section'] );
	$defaults         = wp_parse_args( is_array( $args['defaults'] ) ? $args['defaults'] : [], $default_values );
	$require_complete = (bool) $args['require_complete'];
	$heading          = wp_strip_all_tags( (string) $args['heading'] );
	$description      = wp_strip_all_tags( (string) $args['description'] );
	$example_text     = trim( wp_strip_all_tags( (string) $args['example_text'] ) );
	$cta_label        = wp_strip_all_tags( (string) $args['cta_label'] );
	$cta_note         = wp_strip_all_tags( (string) $args['cta_note'] );

	if ( '' === $tracking_prefix ) {
		$tracking_prefix = 'portal';
	}
	if ( '' === $tracking_section ) {
		$tracking_section = $tracking_prefix . '_portal_calculator';
	}
	if ( '' === $example_text ) {
		$example_text = sprintf(
			/* translators: %s: provider name */
			'Beispielwerte – keine %s-Konditionen. Die voreingestellten Werte dienen nur als Rechenbeispiel.',
			$provider_label
		);
	}

	$required_keys = [ 'cpl', 'leads', 'close_rate', 'sales_minutes', 'hourly_rate' ];
	$has_values    = true;
	foreach ( $required_keys as $required_key ) {
		if ( '' === trim( (string) $defaults[ $required_key ] ) ) {
			$has_values = false;
			break;
		}
	}

	$cpl           = max( 0, (float) $defaults['cpl'] );
	$leads         = max( 0, (float) $defaults['leads'] );
	$close_rate    = min( 100, max( 0, (float) $defaults['close_rate'] ) );
	$sales_minutes = max( 0, (float) $defaults['sales_minutes'] );
	$hourly_rate   = max( 0, (float) $defaults['hourly_rate'] );
	$lead_spend    = $cpl * $leads;
	$orders        = $leads * ( $close_rate / 100 );
	$sales_hours   = $leads * ( $sales_minutes / 60 );
	$sales_costs   = $sales_hours * $hourly_rate;
	$has_result    = ( ! $require_complete || $has_values ) && $orders > 0;
	$full_cpo      = $has_result ? ( $lead_spend + $sales_costs ) / $orders : 0;
	$describedby   = $calculator_id . '-example' . ( $require_complete ? ' ' . $calculator_id . '-validation' : '' );

	ob_start();
	?>
	<section
		class="hu-cpo-calculator hu-cpo-calculator--portal"
		data-hu-cpo-calculator
		data-cpo-mode="portal"
		data-cpo-require-complete="<?php echo esc_attr( $require_complete ? 'true' : 'false' ); ?>"
		data-track-action="<?php echo esc_attr( 'interact_' . $tracking_prefix . '_calculator_first' ); ?>"
		data-track-category="engagement"
		data-track-section="<?php echo esc_attr( $tracking_section ); ?>"
		aria-labelledby="<?php echo esc_attr( $calculator_id . '-title' ); ?>"
	>
		<div class="hu-cpo-calculator__intro">
			<p class="hu-cpo-calculator__eyebrow">Live-Rechnung</p>
			<h3 class="hu-cpo-calculator__title" id="<?php echo esc_attr( $calculator_id . '-title' ); ?>"><?php echo esc_html( $heading ); ?></h3>
			<p class="hu-cpo-calculator__text"><?php echo esc_html( $description ); ?></p>
			<p class="hu-cpo-calculator__example" id="<?php echo esc_attr( $calculator_id . '-example' ); ?>"><?php echo esc_html( $example_text ); ?></p>
		</div>

		<fieldset class="hu-cpo-calculator__portal-fields">
			<legend class="screen-reader-text">Eigene Werte für die Kosten-pro-Auftrag-Rechnung</legend>
			<?php
			$fields = [
				'cpl'           => [ 'Preis pro Anfrage (€)', 'number', '0', 'any', 'decimal' ],
				'leads'         => [ 'Anfragen pro Monat', 'number', '0', '1', 'numeric' ],
				'close_rate'    => [ 'Abschlussquote (%)', 'number', '0', 'any', 'decimal' ],
				'sales_minutes' => [ 'Vertriebszeit pro Anfrage (Min.)', 'number', '0', 'any', 'decimal' ],
				'hourly_rate'   => [ 'Interner Vertriebsstundensatz (€)', 'number', '0', 'any', 'decimal' ],
			];
			foreach ( $fields as $key => $field ) :
				$field_id = $calculator_id . '-' . str_replace( '_', '-', $key );
				?>
				<label class="hu-cpo-calculator__field" for="<?php echo esc_attr( $field_id ); ?>">
					<span class="hu-cpo-calculator__field-label"><?php echo esc_html( $field[0] . ( $require_complete ? ' *' : '' ) ); ?></span>
					<input
						id="<?php echo esc_attr( $field_id ); ?>"
						type="<?php echo esc_attr( $field[1] ); ?>"
						min="<?php echo esc_attr( $field[2] ); ?>"
						<?php echo 'close_rate' === $key ? 'max="100"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static attribute ?>
						step="<?php echo esc_attr( $field[3] ); ?>"
						inputmode="<?php echo esc_attr( $field[4] ); ?>"
						autocomplete="off"
						data-cpo-input="<?php echo esc_attr( $key ); ?>"
						value="<?php echo esc_attr( (string) $defaults[ $key ] ); ?>"
						aria-describedby="<?php echo esc_attr( $describedby ); ?>"
						<?php echo $require_complete ? 'required' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- boolean HTML attribute ?>
					>
				</label>
			<?php endforeach; ?>
		</fieldset>

		<?php if ( $require_complete ) : ?>
			<p class="hu-cpo-calculator__validation" id="<?php echo esc_attr( $calculator_id . '-validation' ); ?>" data-cpo-validation role="status" aria-live="polite" aria-atomic="true">* Pflichtfelder. Tragen Sie alle fünf Werte ein; Anfragen und Abschlussquote müssen größer als null sein.</p>
		<?php endif; ?>

		<div class="hu-cpo-calculator__portal-results" data-cpo-results>
			<div><span>Monatliche Anfragekosten</span><strong data-cpo-output="monthly_lead_cost"><?php echo esc_html( $has_result ? number_format_i18n( $lead_spend, 0 ) . ' €' : '–' ); ?></strong></div>
			<div><span>Voraussichtliche Aufträge</span><strong data-cpo-output="orders"><?php echo esc_html( $has_result ? number_format_i18n( $orders, 1 ) : '–' ); ?></strong></div>
			<div><span>Vertriebsstunden</span><strong data-cpo-output="sales_hours"><?php echo esc_html( $has_result ? number_format_i18n( $sales_hours, 1 ) . ' Std.' : '–' ); ?></strong></div>
			<div class="hu-cpo-calculator__portal-result-main"><span>Anfrage- und Vertriebskosten pro Auftrag</span><strong data-cpo-output="full_cpo" aria-live="polite" aria-atomic="true"><?php echo esc_html( $has_result ? number_format_i18n( $full_cpo, 0 ) . ' €' : '–' ); ?></strong></div>
		</div>

		<div class="hu-cpo-calculator__portal-next">
			<a class="hu-cpo-calculator__cta" href="<?php echo esc_url( $marktcheck_url ); ?>" data-track-action="<?php echo esc_attr( 'cta_' . $tracking_prefix . '_calculator_marktcheck' ); ?>" data-track-category="lead_gen" data-track-section="<?php echo esc_attr( $tracking_section ); ?>"><?php echo esc_html( $cta_label ); ?></a>
			<p><?php echo esc_html( $cta_note ); ?></p>
		</div>
	</section>
	<?php
	return trim( (string) ob_get_clean() );
}

/**
 * Render the two-scenario comparison calculator used in editorial content.
 *
 * This surface remains a planning model. The money-page upgrade uses actual
 * won-order counts and the same JS engine for the final CPO.
 *
 * @param array<string, mixed> $atts Shortcode attributes.
 * @return string
 */
function hu_cpo_calculator_shortcode( $atts = [] ) {
	$atts = shortcode_atts( [ 'mode' => 'comparison' ], (array) $atts, 'hu_cpo_calculator' );
	if ( 'portal' === sanitize_key( (string) $atts['mode'] ) ) {
		return hu_render_portal_cpo_calculator();
	}

	$marktcheck_url = function_exists( 'hu_get_request_analysis_url' )
		? hu_get_request_analysis_url()
		: home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' );

	$scenarios = [
		'current' => [
			'title'         => 'Gekaufte Anfragen',
			'label'         => 'Portal / Agentur',
			'cpl'           => 90,
			'leads'         => 100,
			'close_rate'    => 5,
			'margin_rate'   => 25,
			'sales_minutes' => 35,
			'hourly_rate'   => 60,
			'monthly_costs' => 0,
		],
		'target' => [
			'title'         => 'Eigene Anfragen',
			'label'         => 'Eigener Anfrage-Weg',
			'cpl'           => 170,
			'leads'         => 60,
			'close_rate'    => 15,
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
			<p class="hu-cpo-calculator__eyebrow">Planungsrechnung</p>
			<h3 class="hu-cpo-calculator__title" id="hu-cpo-calculator-title">Was kostet Sie am Ende ein Auftrag?</h3>
			<p class="hu-cpo-calculator__text">Für eine Planung können Sie weiterhin mit Abschlussquoten modellieren. Die Money Page verwendet für die belastbare Vollkostenrechnung dagegen tatsächlich gewonnene Aufträge derselben Kohorte.</p>
		</div>

		<div class="hu-cpo-calculator__shared">
			<label><span>Durchschnittlicher Auftragswert</span><input type="number" min="0" step="500" data-cpo-shared="order_value" value="18000" aria-label="Durchschnittlicher Auftragswert in Euro"></label>
		</div>

		<div class="hu-cpo-calculator__scenarios">
			<?php foreach ( $scenarios as $scenario_key => $scenario ) : ?>
				<div class="hu-cpo-calculator__scenario" data-cpo-scenario="<?php echo esc_attr( $scenario_key ); ?>">
					<div class="hu-cpo-calculator__scenario-head">
						<h4><?php echo esc_html( $scenario['title'] ); ?></h4>
						<input type="text" data-cpo-input="label" value="<?php echo esc_attr( $scenario['label'] ); ?>" aria-label="<?php echo esc_attr( $scenario['title'] . ' Bezeichnung' ); ?>">
					</div>
					<div class="hu-cpo-calculator__fields">
						<label class="hu-cpo-calculator__field hu-cpo-calculator__field--slider"><span class="hu-cpo-calculator__field-label">Preis pro Anfrage <b data-cpo-readout="cpl"></b></span><input type="range" min="0" max="400" step="5" data-cpo-input="cpl" value="<?php echo esc_attr( (string) $scenario['cpl'] ); ?>" aria-label="Preis pro Anfrage in Euro"></label>
						<label class="hu-cpo-calculator__field"><span class="hu-cpo-calculator__field-label">Anfragen pro Monat</span><input type="number" min="0" step="1" data-cpo-input="leads" value="<?php echo esc_attr( (string) $scenario['leads'] ); ?>"></label>
						<label class="hu-cpo-calculator__field hu-cpo-calculator__field--slider"><span class="hu-cpo-calculator__field-label">Abschlussquote <b data-cpo-readout="close_rate"></b></span><input type="range" min="0" max="30" step="0.5" data-cpo-input="close_rate" value="<?php echo esc_attr( (string) $scenario['close_rate'] ); ?>" aria-label="Abschlussquote in Prozent"><small class="hu-cpo-calculator__hint" data-cpo-hint="close_rate"></small></label>
					</div>
					<div class="hu-cpo-calculator__hero" aria-live="polite"><span>Modellierte Kosten pro Auftrag</span><strong data-cpo-output="full_cpo">–</strong></div>
					<details class="hu-cpo-calculator__details">
						<summary>Details &amp; erweiterte Annahmen</summary>
						<div class="hu-cpo-calculator__fields hu-cpo-calculator__fields--advanced">
							<label class="hu-cpo-calculator__field"><span class="hu-cpo-calculator__field-label">Deckungsbeitrag in %</span><input type="number" min="0" max="100" step="1" data-cpo-input="margin_rate" value="<?php echo esc_attr( (string) $scenario['margin_rate'] ); ?>"></label>
							<label class="hu-cpo-calculator__field"><span class="hu-cpo-calculator__field-label">Vertriebszeit pro Anfrage (Min.)</span><input type="number" min="0" step="5" data-cpo-input="sales_minutes" value="<?php echo esc_attr( (string) $scenario['sales_minutes'] ); ?>"></label>
							<label class="hu-cpo-calculator__field"><span class="hu-cpo-calculator__field-label">Stundensatz Vertrieb (€)</span><input type="number" min="0" step="5" data-cpo-input="hourly_rate" value="<?php echo esc_attr( (string) $scenario['hourly_rate'] ); ?>"></label>
							<label class="hu-cpo-calculator__field"><span class="hu-cpo-calculator__field-label">Software / Support / System (€)</span><input type="number" min="0" step="100" data-cpo-input="monthly_costs" value="<?php echo esc_attr( (string) $scenario['monthly_costs'] ); ?>"></label>
						</div>
						<div class="hu-cpo-calculator__results" aria-live="polite">
							<div><span>Modellierte Aufträge</span><strong data-cpo-output="orders">–</strong></div>
							<div><span>Nur Anfragekosten pro Auftrag</span><strong data-cpo-output="lead_cpo">–</strong></div>
							<div><span>Was pro Auftrag übrig bleibt</span><strong data-cpo-output="margin_after_acq">–</strong></div>
							<div><span>Break-even-Abschlussquote</span><strong data-cpo-output="break_even_rate">–</strong></div>
						</div>
					</details>
				</div>
			<?php endforeach; ?>
		</div>

		<div class="hu-cpo-calculator__summary">
			<div><span>Unterschied pro Auftrag</span><strong data-cpo-summary="delta_cpo">–</strong></div>
			<p data-cpo-summary="verdict">Stellen Sie die Werte ein, um den Unterschied zu sehen.</p>
			<a class="hu-cpo-calculator__cta" href="<?php echo esc_url( $marktcheck_url ); ?>" data-track-action="cta_marktcheck" data-track-category="cpo_calculator" data-track-section="cpo_calculator">Jetzt Region prüfen</a>
		</div>
	</section>
	<?php
	return trim( (string) ob_get_clean() );
}

add_shortcode( 'hu_cpo_calculator', 'hu_cpo_calculator_shortcode' );
