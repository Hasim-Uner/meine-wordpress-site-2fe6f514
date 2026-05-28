<?php
/**
 * Template Name: Cost per Lead Photovoltaik – Was Solar-Anfragen wirklich kosten
 * Description: CPL-Rechnung fuer Solar/SHK. Bezug zur E3-Referenz.
 *              Primaerer CTA: Marktcheck.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$page_url        = home_url( '/cost-per-lead-photovoltaik/' );
$solar_money_url = function_exists( 'nexus_get_energy_systems_url' )
	? nexus_get_energy_systems_url()
	: home_url( '/solar-waermepumpen-leadgenerierung/' );
$marktcheck_url  = trailingslashit( $solar_money_url ) . '#marktcheck';
$e3_url          = home_url( '/e3-new-energy/' );
$quality_url     = home_url( '/qualifizierte-pv-anfragen/' );
$vs_url          = home_url( '/eigene-leadgenerierung-vs-portale/' );
$intercept_url   = home_url( '/solar-leads-kaufen-alternative/' );

$e3_canon            = function_exists( 'hu_e3_canon' ) ? hu_e3_canon() : [];
$e3_metrics          = isset( $e3_canon['metrics'] ) && is_array( $e3_canon['metrics'] ) ? $e3_canon['metrics'] : [];
$e3_case_label       = isset( $e3_canon['case_label'] ) ? (string) $e3_canon['case_label'] : 'E3 New Energy';
$e3_cpl_before       = $e3_metrics['cpl_before']['display'] ?? '150 €';
$e3_cpl_after        = $e3_metrics['cpl_after']['display'] ?? '22 €';
$e3_cpl_reduction    = $e3_metrics['cpl_reduction']['display'] ?? 'über 85 %';
$e3_lead_count       = $e3_metrics['lead_count']['display'] ?? '1.750+';
$e3_sales_conversion = $e3_metrics['sales_conversion']['display'] ?? '12 %';
$e3_conv_uplift      = $e3_metrics['sales_conversion_uplift']['display'] ?? '1 – 2 % → 12 %';
$e3_conv_before      = $e3_metrics['sales_conversion_before']['display'] ?? '1 – 2 %';
$e3_timeframe        = $e3_metrics['timeframe']['display'] ?? '6 Monate';

$cpl_quick_facts = [
	[ 'k' => '25 – 150 €', 'l' => 'Marktbreite Preisspanne für einen einzelnen PV-Datensatz' ],
	[ 'k' => $e3_cpl_reduction, 'l' => sprintf( 'Senkung Cost per Lead in %s bei %s', $e3_timeframe, $e3_case_label ) ],
	[ 'k' => $e3_conv_uplift, 'l' => 'Sprung der Abschlussquote (Portal-Leads vs. eigenes System)' ],
	[ 'k' => '~ 15× weniger', 'l' => 'Cost per Auftrag — CPL × Quote zusammen wirken multiplikativ' ],
];

$scenarios = [
	[
		'title'      => 'Szenario A – Portal-Lead-Einkauf (Standard-Mittelstand)',
		'cpl'        => '~ 80 €',
		'close'      => '3 %',
		'cpa'        => '~ 2.667 €',
		'note'       => 'Cost per Auftrag = CPL ÷ Abschlussquote. Bei 80 € CPL und 3 % Quote liegen die reinen Lead-Kosten je Auftrag bei rund 2.667 € – vor Vertriebskosten, Materialkosten und Marge.',
	],
	[
		'title'      => 'Szenario B – Exklusiver Portal-Lead',
		'cpl'        => '~ 150 €',
		'close'      => '5 %',
		'cpa'        => '~ 3.000 €',
		'note'       => 'Höherer Stückpreis, etwas bessere Quote, aber die absoluten Kosten pro Auftrag steigen weiter. Wirtschaftlich nur tragbar bei Projektwerten ab 30.000 €.',
	],
	[
		'title'      => sprintf( 'Szenario C – Eigenes System (%s)', $e3_case_label ),
		'cpl'        => $e3_cpl_after,
		'close'      => $e3_sales_conversion,
		'cpa'        => '~ 183 €',
		'note'       => sprintf( 'Bei %1$s CPL und %2$s Abschlussquote liegen die reinen Lead-Kosten je Auftrag bei rund 183 € – ein Bruchteil der Portal-Szenarien. Asset bleibt im Eigentum.', $e3_cpl_after, $e3_sales_conversion ),
	],
];

$hidden_cost_drivers = [
	[
		't' => 'Tote Leads',
		's' => 'Datensätze, bei denen der Endkunde Stunden oder Tage später kontaktiert wird, schließen kaum noch ab. Der CPL bleibt – die Conversion fällt aus.',
	],
	[
		't' => 'Bieter-Wettlauf',
		's' => 'Bei 3–5 parallel verkauften Datensätzen drücken Wettbewerber den Preis. Selbst wenn Sie zuschlagen, sinkt die Marge.',
	],
	[
		't' => 'Nachqualifizierung',
		's' => 'Wenn Portal-Leads ohne Vorqualifizierung kommen, qualifiziert der Vertrieb am Telefon nach – verlorene Zeit pro Anfrage, die in der CPL-Rechnung nicht auftaucht.',
	],
	[
		't' => 'Kein Markenaufbau',
		's' => 'Portale liefern Anfragen, aber Endkunden erinnern sich an das Portal – nicht an Ihren Betrieb. Wiederkehr- und Empfehlungs-Effekt entfallen.',
	],
];

$linked_assets = [
	[ 't' => 'Qualifizierte PV-Anfragen – 4 Merkmale', 's' => 'Wie man hochwertige von schlechten Leads unterscheidet.', 'url' => $quality_url ],
	[ 't' => 'TCO 24/36 Monate: Portal vs. eigenes System', 's' => 'Strategischer 8-Kriterien-Vergleich mit Asset-Eigentum-Logik.', 'url' => $vs_url ],
	[ 't' => 'Solar Leads kaufen? CPL-Rechnung pro Anfrage', 's' => 'Markteinordnung und konkrete Kosten pro Anfrage im Lead-Markt.', 'url' => $intercept_url ],
	[ 't' => 'E3 New Energy – Methodik-Case', 's' => sprintf( '%1$s, %2$s Abschlussquote, %3$s.', $e3_lead_count, $e3_sales_conversion, $e3_timeframe ), 'url' => $e3_url ],
];

$faq = [
	[
		'question' => 'Wie hoch ist der echte Cost per Lead bei Photovoltaik-Portalen?',
		'answer'   => 'Die marktbreite Spanne liegt zwischen 25 € (geteilte Anfragen, niedrige Vorqualifizierung) und 150 € (exklusive Anfragen mit Vorqualifizierung). Wirtschaftlich relevanter als der reine Stückpreis ist der Cost per Auftrag – also CPL geteilt durch Abschlussquote. Bei 80 € CPL und 3 % Quote sind das rund 2.667 € reine Lead-Kosten je Auftrag.',
	],
	[
		'question' => 'Was ist der wirtschaftliche Vergleichsmaßstab?',
		'answer'   => 'Cost per Lead allein sagt wenig. Entscheidend sind drei Kennzahlen: Cost per Lead (CPL), Abschlussquote und daraus abgeleitet Cost per Auftrag (CPA). Dazu kommt die Asset-Frage: Bleibt die Infrastruktur im Eigentum oder ist sie monatliche Miete?',
	],
	[
		'question' => sprintf( 'Wie hat %s den CPL gesenkt?', $e3_case_label ),
		'answer'   => sprintf( 'Durch Aufbau eines eigenen Anfrage-Systems mit Money Page, Server-Side-Tracking, Vorqualifizierung und CRM-Anbindung. Ergebnis: CPL %1$s → %2$s (%3$s), %4$s qualifizierte Anfragen in %5$s, %6$s Abschlussquote.', $e3_cpl_before, $e3_cpl_after, $e3_cpl_reduction, $e3_lead_count, $e3_timeframe, $e3_sales_conversion ),
	],
	[
		'question' => 'Ab welchem Lead-Budget lohnt sich der Aufbau eines eigenen Systems?',
		'answer'   => 'Faustregel: ab einem monatlichen Portal-Budget von 800 – 1.000 € amortisiert sich der Aufbau eines eigenen Systems im B2B-Mittelstand binnen 12 – 24 Monaten. Bei höheren Budgets entsprechend schneller.',
	],
	[
		'question' => 'Welche versteckten Kostentreiber gibt es bei Portal-Leads?',
		'answer'   => 'Vier Treiber: tote Leads (späte Kontaktierung), Bieter-Wettlauf bei geteilten Datensätzen, manuelle Nachqualifizierung am Telefon und der fehlende Markenaufbau. Diese Faktoren erscheinen nicht in der CPL-Rechnung, drücken aber die wirtschaftliche Wirksamkeit.',
	],
];

$author_person     = function_exists( 'hu_get_canonical_author_person' ) ? hu_get_canonical_author_person() : [ '@type' => 'Person', 'name' => 'Haşim Üner', 'url' => home_url( '/' ) ];
$breadcrumb_schema = function_exists( 'hu_get_solar_subpage_breadcrumb_schema' )
	? hu_get_solar_subpage_breadcrumb_schema( $page_url, 'Cost per Lead Photovoltaik' )
	: [];

$service_schema = [
	'@context'    => 'https://schema.org',
	'@type'       => 'Service',
	'@id'         => trailingslashit( $page_url ) . '#service',
	'name'        => 'CPL-Optimierung für Photovoltaik- und Wärmepumpen-Anbieter',
	'serviceType' => 'Cost per Lead Senkung durch eigene Anfrage-Systeme',
	'url'         => $page_url,
	'description' => sprintf( 'Cost per Lead Analyse und Senkung für Solar-, Wärmepumpen- und Speicher-Anbieter. Referenz %1$s: %2$s niedrigere Kosten pro Anfrage in %3$s.', $e3_case_label, $e3_cpl_reduction, $e3_timeframe ),
	'provider'    => $author_person,
	'author'      => $author_person,
];

$faq_schema = [
	'@context'   => 'https://schema.org',
	'@type'      => 'FAQPage',
	'@id'        => trailingslashit( $page_url ) . '#faq',
	'url'        => trailingslashit( $page_url ) . '#faq',
	'mainEntity' => [],
];

foreach ( $faq as $faq_item ) {
	$faq_schema['mainEntity'][] = [
		'@type'          => 'Question',
		'name'           => $faq_item['question'],
		'acceptedAnswer' => [ '@type' => 'Answer', 'text' => $faq_item['answer'] ],
	];
}

get_header();
?>

<main id="primary" class="hu-intercept" role="main" data-track-page="cost-per-lead-photovoltaik">

	<section class="hu-intercept__hero" id="hero" aria-labelledby="hu-cpl-hero-title">
		<div class="hu-intercept__container">
			<p class="hu-intercept__eyebrow">CPL-Analyse für Photovoltaik, Wärmepumpe & Speicher</p>
			<h1 class="hu-intercept__title" id="hu-cpl-hero-title">
				Cost per Lead Photovoltaik: Was Solar-Anfragen wirklich kosten
			</h1>
			<p class="hu-intercept__lead">
				Der reine Stückpreis für eine PV-Anfrage liegt zwischen <strong>25 €</strong> und <strong>150 €</strong>. Wirtschaftlich entscheidend ist aber der <strong>Cost per Auftrag</strong> — also CPL geteilt durch Abschlussquote. Bei <?php echo esc_html( $e3_case_label ); ?> wurde nicht nur der CPL gesenkt, sondern auch die Abschlussquote von <strong><?php echo esc_html( $e3_conv_before ); ?></strong> auf <strong><?php echo esc_html( $e3_sales_conversion ); ?></strong> gehoben. Beide Hebel zusammen multiplizieren sich.
			</p>
			<?php get_template_part( 'template-parts/seo-subpage-byline', null, [ 'template_path' => __FILE__ ] ); ?>
			<div class="hu-intercept__cta">
				<a class="hu-intercept__cta-primary"
				   href="<?php echo esc_url( $marktcheck_url ); ?>"
				   data-track-action="cta_marktcheck"
				   data-track-category="cost_per_lead_photovoltaik"
				   data-track-section="hero">
					Kostenfreien Marktcheck starten
				</a>
				<a class="hu-intercept__cta-secondary"
				   href="<?php echo esc_url( $e3_url ); ?>"
				   data-track-action="cta_e3_case"
				   data-track-category="cost_per_lead_photovoltaik"
				   data-track-section="hero">
					E3-Case lesen (<?php echo esc_html( $e3_lead_count ); ?> Anfragen, <?php echo esc_html( $e3_sales_conversion ); ?> Abschlussquote)
				</a>
			</div>
		</div>
	</section>

	<section class="hu-intercept__compare" id="fakten" aria-labelledby="hu-cpl-facts-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-cpl-facts-title">Die wichtigsten Zahlen im Überblick</h2>
			<div class="hu-intercept__grid hu-intercept__grid--four">
				<?php foreach ( $cpl_quick_facts as $fact ) : ?>
					<article class="hu-intercept__card">
						<h3 class="hu-intercept__card-title"><?php echo esc_html( $fact['k'] ); ?></h3>
						<p class="hu-intercept__card-text"><?php echo esc_html( $fact['l'] ); ?></p>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="hu-intercept__system" id="szenarien" aria-labelledby="hu-cpl-scenarios-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-cpl-scenarios-title">Drei Szenarien: Portal-Standard, Portal-Exklusiv, Eigenes System</h2>
			<p class="hu-intercept__section-lead">
				Vergleichsrechnung auf identischer Annahme-Basis (10 Anfragen pro Monat, B2C-Mittelstand). Werte beruhen auf marktüblichen Spannen und der E3-Referenz.
			</p>
			<ol class="hu-intercept__layers">
				<?php foreach ( $scenarios as $i => $s ) : ?>
					<li class="hu-intercept__layer">
						<span class="hu-intercept__layer-index"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
						<div class="hu-intercept__layer-body">
							<h3 class="hu-intercept__layer-title"><?php echo esc_html( $s['title'] ); ?></h3>
							<p class="hu-intercept__layer-text">
								<strong>CPL:</strong> <?php echo esc_html( $s['cpl'] ); ?> · <strong>Abschlussquote:</strong> <?php echo esc_html( $s['close'] ); ?> · <strong>Cost per Auftrag:</strong> <?php echo esc_html( $s['cpa'] ); ?>
							</p>
							<p class="hu-intercept__layer-text"><?php echo esc_html( $s['note'] ); ?></p>
						</div>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
	</section>

	<section class="hu-intercept__why" id="versteckte-kosten" aria-labelledby="hu-cpl-hidden-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-cpl-hidden-title">Versteckte Kostentreiber, die in keiner CPL-Rechnung stehen</h2>
			<div class="hu-intercept__grid hu-intercept__grid--four">
				<?php foreach ( $hidden_cost_drivers as $item ) : ?>
					<article class="hu-intercept__card">
						<h3 class="hu-intercept__card-title"><?php echo esc_html( $item['t'] ); ?></h3>
						<p class="hu-intercept__card-text"><?php echo esc_html( $item['s'] ); ?></p>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="hu-intercept__why" id="vertiefung" aria-labelledby="hu-cpl-linked-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-cpl-linked-title">Vertiefende Ressourcen</h2>
			<div class="hu-intercept__grid hu-intercept__grid--four">
				<?php foreach ( $linked_assets as $item ) : ?>
					<article class="hu-intercept__card">
						<h3 class="hu-intercept__card-title"><a href="<?php echo esc_url( $item['url'] ); ?>"><?php echo esc_html( $item['t'] ); ?></a></h3>
						<p class="hu-intercept__card-text"><?php echo esc_html( $item['s'] ); ?></p>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="hu-intercept__faq" id="faq" aria-labelledby="hu-cpl-faq-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-cpl-faq-title">Häufige Fragen zum Cost per Lead in der Solar-Branche</h2>
			<div class="hu-intercept__faq-list">
				<?php foreach ( $faq as $item ) : ?>
					<details class="hu-intercept__faq-item">
						<summary class="hu-intercept__faq-q"><?php echo esc_html( $item['question'] ); ?></summary>
						<p class="hu-intercept__faq-a"><?php echo esc_html( $item['answer'] ); ?></p>
					</details>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="hu-intercept__final" id="final-cta" aria-labelledby="hu-cpl-final-title">
		<div class="hu-intercept__container hu-intercept__container--centered">
			<h2 class="hu-intercept__h2" id="hu-cpl-final-title">Eigenen CPL im Marktcheck einordnen</h2>
			<p class="hu-intercept__final-text">
				Manueller, tiefer Marktcheck statt Software-Einheitsbrei. Händische Analyse Ihrer Region innerhalb von 48 Stunden per E-Mail — mit klarer Einordnung Ihrer aktuellen Lead-Kosten und der Hebel, mit denen sich der CPL signifikant senken lässt.
			</p>
			<div class="hu-intercept__cta">
				<a class="hu-intercept__cta-primary"
				   href="<?php echo esc_url( $marktcheck_url ); ?>"
				   data-track-action="cta_marktcheck"
				   data-track-category="cost_per_lead_photovoltaik"
				   data-track-section="final">
					Kostenfreien Marktcheck starten
				</a>
				<a class="hu-intercept__cta-secondary"
				   href="<?php echo esc_url( $solar_money_url ); ?>"
				   data-track-action="cta_money_page"
				   data-track-category="cost_per_lead_photovoltaik"
				   data-track-section="final">
					System & Methode ansehen
				</a>
			</div>
		</div>
	</section>

	<script type="application/ld+json"><?php echo wp_json_encode( $service_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?></script>
	<script type="application/ld+json"><?php echo wp_json_encode( $faq_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?></script>
	<?php if ( ! empty( $breadcrumb_schema ) ) : ?>
	<script type="application/ld+json"><?php echo wp_json_encode( $breadcrumb_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?></script>
	<?php endif; ?>
</main>

<?php
get_template_part(
	'template-parts/seo-subpage-sticky-cta',
	null,
	[
		'marktcheck_url' => $marktcheck_url,
		'track_category' => 'cost_per_lead_photovoltaik',
	]
);

get_footer();
