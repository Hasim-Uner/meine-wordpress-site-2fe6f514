<?php
/**
 * Canonical anonymized case-study proof metrics.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'HU_E3_CASE_LABEL', 'mittelständischer PV-Installationsbetrieb' );
// Gebeugte Fassung fuer Akkusativ-Kontexte ("Fuer einen ..."). Dasselbe Muster
// wie display_dative bei den Metriken: die Beugung steht im Canon, damit sie
// nicht als Literal in die Templates wandert.
define( 'HU_E3_CASE_LABEL_ACCUSATIVE', 'mittelständischen PV-Installationsbetrieb' );
define( 'HU_E3_CPL_BEFORE', 150 );
define( 'HU_E3_CPL_AFTER', 22 );
define( 'HU_E3_CPL_REDUCTION_PERCENT', 85 );
define( 'HU_E3_LEAD_COUNT', 1750 );
define( 'HU_E3_SALES_CONVERSION_PERCENT', 15 );
define( 'HU_E3_SALES_CONVERSION_BEFORE_LOW', 1 );
define( 'HU_E3_SALES_CONVERSION_BEFORE_HIGH', 5 );
define( 'HU_E3_TIMEFRAME_MONTHS', 6 );

// Voreinstellungen des Vergleichsrechners auf der Money Page. Bewusst
// vorsichtiger als der dokumentierte Fall: der Rechner soll nicht mit dem
// besten gemessenen Wert anlaufen.
//
// Sie stehen hier, weil sie bis 2026-09 als nackte Literale neben der
// Abschlussquote des Falls im Template standen — "12 % Abschlussquote" ohne
// Rahmen war von der Fallzahl nicht zu unterscheiden und las sich als
// Widerspruch zu den 15 %. Wer sie aendert, aendert eine Rechenannahme, nicht
// den Fall. Umgekehrt gilt dasselbe: HU_E3_SALES_CONVERSION_PERCENT ist die
// gemessene Abschlussquote und nie eine Rechenannahme.
define( 'HU_E3_CALC_CPL_CONSERVATIVE', 45 );
define( 'HU_E3_CALC_SALES_CONVERSION_CONSERVATIVE', 12 );

// Zwischenwerte der Strecke. Standen bis 2026-08 als Literale in
// page-case-study-solar.php und waren damit weder prüfbar noch mitpflegbar.
define( 'HU_E3_CPL_RAMP_LOW', 70 );
define( 'HU_E3_CPL_RAMP_HIGH', 100 );
define( 'HU_E3_PORTAL_CONVERSION_AVG', 3 );
define( 'HU_E3_PORTAL_COST_PER_DEAL', 5000 );
define( 'HU_E3_BUILD_MONTHS', 1 );
define( 'HU_E3_TUNING_MONTHS', 3 );

/**
 * Return the canonical E3 proof data.
 *
 * @return array<string, mixed>
 */
function hu_e3_canon() {
	$cpl_reached_month = 3;

	return [
		'case_label'            => HU_E3_CASE_LABEL,
		'case_label_accusative' => HU_E3_CASE_LABEL_ACCUSATIVE,
		'url'        => home_url( '/case-study-solar-leadgenerierung/' ),
		'cpl_reached_month' => $cpl_reached_month,
		'cpl_reached_label' => sprintf( 'ab Monat %d', $cpl_reached_month ),
		'homepage_build_label' => sprintf( 'Monat %d–%d', HU_E3_BUILD_MONTHS, $cpl_reached_month ),
		'metrics'    => [
			'cpl_before'       => [
				'value'   => HU_E3_CPL_BEFORE,
				'display' => '150 €',
				'label'   => 'Kosten pro gekaufter Anfrage vorher',
			],
			'cpl_after'        => [
				'value'   => HU_E3_CPL_AFTER,
				'display' => '22 €',
				'label'   => 'Erreichter Wert pro eigener Anfrage',
			],
			// Labtest-Werte dieser Website. Sie leben hier, damit jede sichtbare
			// Kennzahl denselben Canon-Zugriff nutzt: Startseite und
			// Ergebnisse-Hub zeigen sonst zwei Staende derselben Messung.
			// Lighthouse ist ein Labormesswert und ersetzt keine Felddaten —
			// sichtbare Copy muss das mitfuehren.
			'site_lighthouse_performance' => [
				'value'   => 99,
				'display' => '99',
				'max'     => '/100',
				'label'   => 'PageSpeed mobil',
			],
			'site_lighthouse_accessibility' => [
				'value'   => 100,
				'display' => '100',
				'max'     => '/100',
				'label'   => 'Barrierefreiheit',
			],
			'site_lighthouse_seo' => [
				'value'   => 100,
				'display' => '100',
				'max'     => '/100',
				'label'   => 'SEO',
			],
			'site_lighthouse_best_practices' => [
				'value'   => 100,
				'display' => '100',
				'max'     => '/100',
				'label'   => 'Best Practices',
			],
			'cpl_reduction'    => [
				'value'                => HU_E3_CPL_REDUCTION_PERCENT,
				'display'              => 'über 85 %',
				'conservative_display' => 'über 85 %',
				'counter_target'       => '85',
				'label'                => 'Kosten pro Anfrage',
			],
			'lead_count'       => [
				'value'          => HU_E3_LEAD_COUNT,
				'display'        => '1.750',
				'counter_target' => '1750',
				'label'          => 'Anfragen insgesamt',
			],
			'sales_conversion' => [
				'value'          => HU_E3_SALES_CONVERSION_PERCENT,
				'display'        => '15 %',
				'counter_target' => '15',
				'label'          => 'Abschlussquote der vorqualifizierten CRM-Leads',
			],
			// Die Vorher-Quote ist die einzige Zahl dieses Falls, die nicht
			// gemessen wurde: sie ist eine Marktannahme ueber gekaufte
			// Portal-Leads. Oberflaechen verwenden `display_hedged` und sagen
			// dazu, dass es eine Annahme ist (Regel e3-vorher-quote in
			// scripts/canon-forbidden-values.txt). `display` bleibt fuer den
			// Kanon selbst und fuer Texte, die die Spanne als Marktwert mit
			// Quelle zitieren. Seit 2026-09-26 gibt es kein Vorher-Nachher-Feld
			// mehr: "1 – 5 % → 15 %" und "3× bis 15×" rechneten mit der
			// Annahme, als waere sie gemessen.
			'sales_conversion_before' => [
				'value'           => HU_E3_SALES_CONVERSION_BEFORE_LOW,
				'value_high'      => HU_E3_SALES_CONVERSION_BEFORE_HIGH,
				'display'         => '1 – 5 %',
				'display_hedged'  => 'einstellig',
				'label'           => 'Abschlussquote vorher (gekaufte Portal-Leads)',
			],
			'sales_conversion_after' => [
				'value'          => HU_E3_SALES_CONVERSION_PERCENT,
				'display'        => '15 %',
				'counter_target' => '15',
				'label'          => 'Abschlussquote der vorqualifizierten CRM-Leads',
			],
			'timeframe'        => [
				'value'          => HU_E3_TIMEFRAME_MONTHS,
				'display'        => '6 Monate',
				'display_dative' => '6 Monaten',
				'counter_target' => '6',
				'label'          => 'Zeitraum',
			],
			'cpl_ramp'         => [
				'value'      => HU_E3_CPL_RAMP_LOW,
				'value_high' => HU_E3_CPL_RAMP_HIGH,
				'display'    => '70 – 100 €',
				'label'      => 'Kosten pro Anfrage in den ersten vier Kampagnenwochen',
			],
			'portal_conversion_avg' => [
				'value'   => HU_E3_PORTAL_CONVERSION_AVG,
				'display' => '3 %',
				'label'   => 'durchschnittliche Abschlussquote auf Portal-Leads',
			],
			// Rechenannahmen, keine Messwerte. `label` sagt das ausdruecklich,
			// damit eine Copy, die den Wert ausgibt, den Rahmen mitnehmen kann.
			'calc_cpl_conservative' => [
				'value'   => HU_E3_CALC_CPL_CONSERVATIVE,
				'display' => HU_E3_CALC_CPL_CONSERVATIVE . ' €',
				'input'   => (string) HU_E3_CALC_CPL_CONSERVATIVE,
				'label'   => 'vorsichtig angesetzte Kosten pro Anfrage (Rechenannahme)',
			],
			'calc_sales_conversion_conservative' => [
				'value'   => HU_E3_CALC_SALES_CONVERSION_CONSERVATIVE,
				'display' => HU_E3_CALC_SALES_CONVERSION_CONSERVATIVE . ' %',
				'input'   => (string) HU_E3_CALC_SALES_CONVERSION_CONSERVATIVE,
				'label'   => 'vorsichtig angesetzte Abschlussquote (Rechenannahme)',
			],
			'portal_cost_per_deal'  => [
				'value'   => HU_E3_PORTAL_COST_PER_DEAL,
				'display' => '5.000 €',
				'label'   => 'reine Anfrage-Kosten pro Abschluss vorher',
			],
			'build_months'     => [
				'value'   => HU_E3_BUILD_MONTHS,
				'display' => 'rund vier Wochen',
				'label'   => 'Vorbereitung vor Kampagnenstart',
			],
			// Anlaufzeit bis zur ersten qualifizierten Anfrage im dokumentierten
			// Fall. Erfahrungswert aus diesem einen Projekt, kein Marktversprechen
			// — die Copy muss den Fallbezug immer mitfuehren.
			'first_requests_weeks' => [
				'display' => '4–6 Wochen',
				'label'   => 'bis zu den ersten qualifizierten Anfragen (dokumentierter Fall)',
			],
			'tuning_months'    => [
				'value'   => HU_E3_TUNING_MONTHS,
				'display' => '3 Monate',
				'label'   => 'Optimierung',
			],
		],
		// Betreiberklärung vom 27.09.2026: Projektmonate inklusive Vorbereitung.
		// Keine monatlichen Rohdaten: keine interpolierte Messkurve oder Mittelwerte.
		'timeline' => [
			'preparation_label' => 'Projektmonat 1 · Vorbereitung',
			'preparation' => 'Rund vier Wochen für Strategie, Audit und den Aufbau neuer Landingpages. Technisches SEO begann bereits in dieser Phase; eigene Kampagnen liefen noch nicht.',
			'campaign_label' => 'Projektmonat 2 · Kampagnenstart',
			'campaign' => 'In den ersten vier Kampagnenwochen lagen die Kosten pro Anfrage bei etwa 70–100 €. Anzeigenvarianten und Platzierungen wurden getestet und laufend optimiert.',
			'optimization_label' => 'Ab Projektmonat 3 · Optimierung',
			'optimization' => 'Erfolgreiche Anzeigenvarianten und Platzierungen wurden stärker ausgespielt. Hinzu kamen Server-Side-Tracking und die Anbindung an Bitrix24: Anfragen wurden mit verfügbarer Herkunft nach Photovoltaik, Wärmepumpe oder Kombination segmentiert. Die Anfragekosten sanken zunächst auf etwa 30–50 €, anschließend wurden rund 22 € erreicht.',
			'followup' => 'In den Projektmonaten 4–6 lagen die Anfragekosten überwiegend zwischen 22 und 30 €, teilweise darunter. Die 22 € sind ein erreichter Wert, kein belegter Durchschnitt des gesamten Projektzeitraums.',
			'compact' => 'Rund vier Wochen Vorbereitung, Kampagnenstart im zweiten Projektmonat, rund 22 € ab dem dritten Projektmonat erreicht.',
			'result_label' => 'Ab Projektmonat 3 erreichter Wert',
			'comparison' => 'Portal-Einkauf vorher: 150 € pro Anfrage. In der eigenen Kampagne wurden ab dem dritten Projektmonat rund 22 € erreicht. Vergleich zweier Anfragequellen, keine monatliche Messkurve.',
		],
		// Betreiberklärung vom 28.09.2026: Mengen und Quoten haben eigene Bezugsgrößen.
		'summary'    => [
			'definitions' => 'In sechs Monaten wurden insgesamt 1.750 Leads gewonnen, hauptsächlich über Meta Ads, organische Google-Suche und Google Ads. Etwa 95 % der Leads wurden in Bitrix24 mit Produktinteresse und Herkunft erfasst. Die Formulare qualifizierten nach Solar, Wärmepumpe oder Kombination vor. Unter diesen vorqualifizierten CRM-Leads lag die Abschlussquote bei rund 15 %.',
			'compact'    => '150 € auf 22 € Kosten pro Anfrage, 1.750 Anfragen insgesamt und 15 % Abschlussquote der vorqualifizierten CRM-Leads, 6 Monate.',
			'proof'      => 'Referenz mittelständischer PV-Installationsbetrieb: 1.750 Anfragen insgesamt, 15 % Abschlussquote der vorqualifizierten CRM-Leads und über 85 % weniger Kosten pro Anfrage.',
			'conversion' => 'Im selben Projekt lag die Abschlussquote der vorqualifizierten CRM-Leads bei 15 %; daran hatte der Vertrieb einen wesentlichen Anteil.',
		],
	];
}

/**
 * Return one display field from the E3 canon.
 *
 * @param string $metric Metric key.
 * @param string $field  Field key.
 * @param string $fallback Fallback value.
 * @return string
 */
function hu_e3_metric( $metric, $field = 'display', $fallback = '' ) {
	$canon   = hu_e3_canon();
	$metrics = isset( $canon['metrics'] ) && is_array( $canon['metrics'] ) ? $canon['metrics'] : [];

	if ( ! isset( $metrics[ $metric ] ) || ! is_array( $metrics[ $metric ] ) ) {
		return $fallback;
	}

	if ( ! array_key_exists( $field, $metrics[ $metric ] ) ) {
		return $fallback;
	}

	return (string) $metrics[ $metric ][ $field ];
}

/**
 * Return a canonical E3 summary sentence.
 *
 * @param string $variant Summary variant.
 * @return string
 */
function hu_e3_summary( $variant = 'proof' ) {
	$canon     = hu_e3_canon();
	$summaries = isset( $canon['summary'] ) && is_array( $canon['summary'] ) ? $canon['summary'] : [];

	if ( isset( $summaries[ $variant ] ) ) {
		return (string) $summaries[ $variant ];
	}

	return isset( $summaries['proof'] ) ? (string) $summaries['proof'] : '';
}
