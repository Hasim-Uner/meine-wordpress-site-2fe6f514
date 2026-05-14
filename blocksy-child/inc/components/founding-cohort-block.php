<?php
/**
 * Reusable Founding Cohort 2026 block.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Format Euro values from canonical pricing.
 *
 * @param int|float|string $amount Numeric amount.
 * @return string
 */
function hu_format_founding_money( $amount ) {
	return number_format_i18n( (float) $amount, 0 ) . ' €';
}

/**
 * Render a Founding Cohort block variant.
 *
 * @param array<string, mixed> $args Render arguments.
 * @return string
 */
function hu_render_founding_cohort_block( $args = [] ) {
	$args = wp_parse_args(
		$args,
		[
			'variant' => 'compact',
			'id'      => 'founding-cohort',
		]
	);

	$variant = sanitize_key( (string) $args['variant'] );
	if ( ! in_array( $variant, [ 'compact', 'full', 'about' ], true ) ) {
		$variant = 'compact';
	}

	$founding = function_exists( 'hu_founding_canon' ) ? hu_founding_canon() : [];
	$pricing  = function_exists( 'hu_pricing_canon' ) ? hu_pricing_canon() : [];

	$label           = (string) ( $founding['label'] ?? 'Founding Cohort 2026' );
	$slots_total     = max( 1, (int) ( $founding['slots_total'] ?? 3 ) );
	$slots_remaining = max( 0, min( $slots_total, (int) ( $founding['slots_remaining'] ?? $slots_total ) ) );
	$end_date_raw    = (string) ( $founding['end_date'] ?? '2026-09-30' );
	$end_timestamp   = strtotime( $end_date_raw );
	$end_label       = $end_timestamp ? date_i18n( 'd.m.Y', $end_timestamp ) : $end_date_raw;

	$foundation_standard     = hu_format_founding_money( $pricing['foundation_price_standard'] ?? 14900 );
	$foundation_founding     = hu_format_founding_money( $pricing['foundation_price_founding'] ?? 9900 );
	$performance_standard    = hu_format_founding_money( $pricing['performance_retainer_price'] ?? 1500 );
	$performance_founding    = hu_format_founding_money( $pricing['performance_founding_retainer'] ?? 1000 );
	$performance_months      = (int) ( $pricing['performance_founding_months'] ?? 6 );
	$discount_percent        = (int) ( $pricing['founding_discount_percent'] ?? 33 );
	$analysis_url            = function_exists( 'hu_get_request_analysis_url' ) ? hu_get_request_analysis_url() : home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' );
	$section_id              = sanitize_html_class( (string) $args['id'] );
	$slot_line               = sprintf( '%1$d von %2$d Plätzen offen', $slots_remaining, $slots_total );

	ob_start();
	?>
	<?php if ( 'compact' === $variant ) : ?>
		<aside
			id="<?php echo esc_attr( $section_id ); ?>"
			class="hu-founding hu-founding--compact"
			aria-label="<?php echo esc_attr( $label ); ?>"
			data-track-action="founding_cohort_section_view"
			data-track-category="lead_funnel"
			data-track-section="founding_cohort"
			data-track-funnel-stage="founding_cohort_section_view"
		>
			<div class="hu-founding__compact-copy">
				<span class="hu-founding__eyebrow"><?php echo esc_html( $label ); ?></span>
				<strong><?php echo esc_html( $slot_line ); ?></strong>
				<span>Founding-Konditionen bis <?php echo esc_html( $end_label ); ?> oder bis 3/3 vergeben sind.</span>
			</div>
			<a
				class="hu-founding__link"
				href="<?php echo esc_url( $analysis_url ); ?>"
				data-track-action="founding_cohort_cta_click"
				data-track-category="lead_gen"
				data-track-section="founding_cohort"
				data-track-funnel-stage="request_analysis"
			>Analyse anfragen</a>
		</aside>
	<?php elseif ( 'about' === $variant ) : ?>
		<section
			id="<?php echo esc_attr( $section_id ); ?>"
			class="hu-founding hu-founding--about nx-section"
			aria-labelledby="<?php echo esc_attr( $section_id . '-title' ); ?>"
			data-track-action="founding_cohort_section_view"
			data-track-category="lead_funnel"
			data-track-section="founding_cohort"
			data-track-funnel-stage="founding_cohort_section_view"
		>
			<div class="nx-container">
				<div class="hu-founding__about-inner">
					<span class="hu-founding__eyebrow"><?php echo esc_html( $label ); ?></span>
					<h2 id="<?php echo esc_attr( $section_id . '-title' ); ?>">E3 New Energy war der erste Case, nicht die Grenze.</h2>
					<p>Die Cohort erweitert diese Arbeitsweise auf maximal drei passende Solar- oder Wärmepumpen-Betriebe. Der Einstieg bleibt der Marktcheck, damit vor einer Umsetzung klar ist, ob Markt, Budget und Tracking-Realität zusammenpassen.</p>
					<div class="hu-founding__about-footer">
						<span><?php echo esc_html( $slot_line ); ?></span>
						<a
							class="nx-btn nx-btn--primary"
							href="<?php echo esc_url( $analysis_url ); ?>"
							data-track-action="founding_cohort_cta_click"
							data-track-category="lead_gen"
							data-track-section="founding_cohort"
							data-track-funnel-stage="request_analysis"
						>Analyse anfragen</a>
					</div>
				</div>
			</div>
		</section>
	<?php else : ?>
		<section
			id="<?php echo esc_attr( $section_id ); ?>"
			class="hu-founding hu-founding--full nx-section"
			aria-labelledby="<?php echo esc_attr( $section_id . '-title' ); ?>"
			data-track-action="founding_cohort_section_view"
			data-track-category="lead_funnel"
			data-track-section="founding_cohort"
			data-track-funnel-stage="founding_cohort_section_view"
		>
			<div class="nx-container">
				<div class="hu-founding__head">
					<span class="hu-founding__eyebrow"><?php echo esc_html( $label ); ?></span>
					<h2 id="<?php echo esc_attr( $section_id . '-title' ); ?>">Founding-Konditionen für die ersten drei passenden Betriebe.</h2>
					<p><?php echo esc_html( $slot_line ); ?>. Bewerbungsschluss ist der <?php echo esc_html( $end_label ); ?> oder früher, sobald alle Plätze vergeben sind.</p>
				</div>

				<div class="hu-founding__grid">
					<div class="hu-founding__panel hu-founding__panel--table">
						<table class="hu-founding__table">
							<caption class="screen-reader-text">Vergleich Standard 2027 und Founding Cohort 2026</caption>
							<thead>
								<tr>
									<th scope="col">Punkt</th>
									<th scope="col">Standard ab 2027</th>
									<th scope="col">Founding 2026</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<th scope="row">Anfrage-System-Umsetzung</th>
									<td><?php echo esc_html( $foundation_standard ); ?> fix</td>
									<td><strong><?php echo esc_html( $foundation_founding ); ?> fix</strong></td>
								</tr>
								<tr>
									<th scope="row">Performance optional</th>
									<td><?php echo esc_html( $performance_standard ); ?> / Mt</td>
									<td><?php echo esc_html( $performance_founding ); ?> / Mt für die ersten <?php echo esc_html( (string) $performance_months ); ?> Monate</td>
								</tr>
								<tr>
									<th scope="row">Preisvorteil</th>
									<td>Reguläre Konditionen</td>
									<td>ca. <?php echo esc_html( (string) $discount_percent ); ?> % auf das Umsetzungs-Setup</td>
								</tr>
								<tr>
									<th scope="row">Gegenleistung</th>
									<td>Keine öffentliche Referenzpflicht</td>
									<td>Case Study mit Zahlen, Video-Statement nach 90 Tagen, Logo-Freigabe</td>
								</tr>
								<tr>
									<th scope="row">Selektion</th>
									<td>Fit nach Diagnose</td>
									<td>Solar/Wärmepumpe DACH, 10-25 MA, mind. 5.000 € Werbebudget / Mt</td>
								</tr>
							</tbody>
						</table>
					</div>

					<div class="hu-founding__panel hu-founding__panel--faq">
						<h3>Häufige Fragen</h3>
						<details open>
							<summary>Warum Founding Cohort?</summary>
							<p>Die ersten drei Partner bekommen bessere Konditionen, weil sie belastbare Referenzdaten und Freigaben für den öffentlichen Proof liefern.</p>
						</details>
						<details>
							<summary>Was bekomme ich anders als später?</summary>
							<p>Die Umsetzung bleibt inhaltlich gleich. Anders sind Preis, Performance-Rate in den ersten sechs Monaten und die gemeinsame Referenzarbeit.</p>
						</details>
						<details>
							<summary>Was ist die Gegenleistung?</summary>
							<p>Zahlenbasierte Case Study, Video-Statement nach 90 Tagen und Logo-Freigabe, wenn das System sauber live ist.</p>
						</details>
						<a
							class="nx-btn nx-btn--primary hu-founding__cta"
							href="<?php echo esc_url( $analysis_url ); ?>"
							data-track-action="founding_cohort_cta_click"
							data-track-category="lead_gen"
							data-track-section="founding_cohort"
							data-track-funnel-stage="request_analysis"
						>Analyse anfragen</a>
					</div>
				</div>
			</div>
		</section>
	<?php endif; ?>
	<?php
	return (string) ob_get_clean();
}
