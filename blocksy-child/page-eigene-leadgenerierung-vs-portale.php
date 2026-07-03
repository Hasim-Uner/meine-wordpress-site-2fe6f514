<?php
/**
 * Template Name: Eigene Leadgenerierung vs. Portal-Leads
 * Description: Vergleichsseite Mieten vs. Besitzen. Bedient Cluster D
 *              (Vergleichs-Keywords). Argument: TCO, Exklusivität,
 *              Datenhoheit. Mit überschlägiger Beispielrechnung auf
 *              Basis der canonical CAPEX/OPEX-Zahlen.
 *              Primärer CTA: Marktcheck.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ── URLs ───────────────────────────────────────────────────────
$page_url        = home_url( '/eigene-leadgenerierung-vs-portale/' );
$solar_money_url = function_exists( 'nexus_get_energy_systems_url' )
	? nexus_get_energy_systems_url()
	: home_url( '/solar-waermepumpen-leadgenerierung/' );
$marktcheck_url  = trailingslashit( $solar_money_url ) . '#marktcheck';
$e3_url          = home_url( '/e3-new-energy/' );

$linked_assets = [
	[
		't'   => 'CPL pro Anfrage: Solar Leads kaufen oder eigenes System?',
		's'   => 'Die konkrete Kosten-pro-Anfrage-Rechnung mit E3-Benchmark und Portal-Preisspannen.',
		'url' => home_url( '/solar-leads-kaufen-alternative/' ),
	],
	[
		't'   => 'Lead-Funnel Solar (Pillar)',
		's'   => 'Funnel-Architektur fürs eigene Anfrage-System — was nach der Entscheidung gegen Portale folgt.',
		'url' => home_url( '/lead-funnel-solar/' ),
	],
	[
		't'   => 'Cost per Lead Photovoltaik',
		's'   => 'CPL-Drill-down mit drei Szenarien — die Zahlen-Tiefe hinter dem strategischen TCO-Vergleich.',
		'url' => home_url( '/cost-per-lead-photovoltaik/' ),
	],
	[
		't'   => 'B2B Solar Leads für gewerbliche Projekte',
		's'   => 'Buying-Center-Funnel für gewerbliche Photovoltaik ab 50.000 € — Pendant zum B2C-Vergleich.',
		'url' => home_url( '/b2b-solar-leads/' ),
	],
];

// ── E3-Canon ──────────────────────────────────────────────────
$e3_canon            = function_exists( 'hu_e3_canon' ) ? hu_e3_canon() : [];
$e3_metrics          = isset( $e3_canon['metrics'] ) && is_array( $e3_canon['metrics'] ) ? $e3_canon['metrics'] : [];
$e3_case_label       = isset( $e3_canon['case_label'] ) ? (string) $e3_canon['case_label'] : 'E3 New Energy';
$e3_cpl_before       = $e3_metrics['cpl_before']['display'] ?? '150 €';
$e3_cpl_after        = $e3_metrics['cpl_after']['display'] ?? '22 €';
$e3_cpl_reduction    = $e3_metrics['cpl_reduction']['display'] ?? 'über 85 %';
$e3_timeframe        = $e3_metrics['timeframe']['display'] ?? '6 Monate';
$e3_sales_conversion = $e3_metrics['sales_conversion']['display'] ?? '12 %';
$e3_lead_count       = $e3_metrics['lead_count']['display'] ?? '1.750+';
$e3_conv_before      = $e3_metrics['sales_conversion_before']['display'] ?? '1 – 5 %';
$e3_conv_uplift      = $e3_metrics['sales_conversion_uplift']['display'] ?? '1 – 5 % → 12 %';

// ── Inhalte ───────────────────────────────────────────────────
$rent_facts = [
	[ 'k' => '60 – 120 €', 'l' => 'Cost per Lead pro Datensatz (Photovoltaik & Wärmepumpe)' ],
	[ 'k' => '3 – 5×', 'l' => 'Mehrfachverkauf desselben Datensatzes' ],
	[ 'k' => $e3_conv_before, 'l' => 'typische Abschlussquote auf gekaufte Portal-Leads' ],
	[ 'k' => 'OPEX', 'l' => 'reine Mietkosten, kein Asset im Eigentum' ],
];

$own_facts = [
	[ 'k' => $e3_cpl_after, 'l' => 'Cost per Lead im eigenen System (E3-Referenz)' ],
	[ 'k' => '1× exklusiv', 'l' => 'jede Anfrage gehört nur Ihrem Betrieb' ],
	[ 'k' => $e3_sales_conversion, 'l' => 'Abschlussquote bei eigenen Anfragen (E3)' ],
	[ 'k' => 'CAPEX', 'l' => 'investiv in Eigentum, bilanzierbar und planbar' ],
];

$comparison_rows = [
	[
		'criterion' => 'Exklusivität der Anfrage',
		'rent'      => 'drei Wettbewerber bekommen denselben Datensatz',
		'own'       => '100 % exklusiv – nur Ihr Betrieb erhält die Anfrage',
	],
	[
		'criterion' => 'Kostenmodell',
		'rent'      => 'OPEX – laufende Kosten ohne Eigentumsaufbau',
		'own'       => 'CAPEX einmalig + niedrige Betriebskosten',
	],
	[
		'criterion' => 'Datenhoheit',
		'rent'      => 'Daten gehören dem Portal',
		'own'       => 'Daten, Code, Tracking und CRM bleiben im Betrieb',
	],
	[
		'criterion' => 'Skalierbarkeit',
		'rent'      => 'linear – mehr Volumen = proportional mehr Kosten',
		'own'       => 'degressiv – mehr Anfragen ohne proportionale Kostensteigerung',
	],
	[
		'criterion' => 'Anfrage-Qualität',
		'rent'      => 'undifferenziert, oft Preis-Vergleicher ohne Projekt',
		'own'       => 'vorqualifiziert nach Region, Heizart, Projektwert',
	],
	[
		'criterion' => 'Abschlussquote',
		'rent'      => sprintf( 'typisch %s — Endkunde wurde parallel an Wettbewerber verkauft', $e3_conv_before ),
		'own'       => sprintf( '%s bei E3 — Faktor 6× bis 12× durch Exklusivität und Vorqualifizierung', $e3_sales_conversion ),
	],
	[
		'criterion' => 'Attribution',
		'rent'      => 'Black-Box, Portal-Reports ohne Vertriebs-Anschluss',
		'own'       => 'Server-Side Tracking, sauber bis zum Auftragswert',
	],
	[
		'criterion' => 'Markenwirkung',
		'rent'      => 'keine – Endkunde sieht das Portal als Marke',
		'own'       => 'organischer Markenaufbau, Wiederkehr- und Empfehlungseffekt',
	],
	[
		'criterion' => 'Vertrags-Logik',
		'rent'      => 'Kündigung = Anfrage-Stopp innerhalb von Stunden',
		'own'       => 'monatlich kündbar; Code und CRM bleiben',
	],
];

$tco_scenarios = [
	[
		'scope'    => '24 Monate (typisch B2B-Mittelstand)',
		'rent'     => '~ 1.080 € / Monat × 24 Mon. ≈ 26.000 € Lead-Einkauf',
		'own'      => '12.000 – 18.000 € Setup + ~ 50 € / Monat Hosting ≈ 13.200 – 19.200 €',
		'delta'    => 'Eigenes System schont das Marketing-Budget – und das Asset bleibt nach Monat 24 vollständig im Betrieb.',
	],
	[
		'scope'    => '36 Monate (mit Skalierung)',
		'rent'     => '~ 1.080 € / Monat × 36 Mon. ≈ 39.000 € Lead-Einkauf bei sinkender Qualität',
		'own'      => '12.000 – 18.000 € Setup + ~ 50 € / Monat Hosting ≈ 14.160 – 20.160 €',
		'delta'    => 'Über 36 Monate beträgt die TCO-Differenz häufig den fünffachen Setup-Wert – plus dauerhaftes Eigentum am Anfrage-System.',
	],
];

$when_rent_makes_sense = [
	[
		't' => 'Sehr kurzfristiger Engpass',
		's' => '14-Tage-Auslastungslücke im Vertrieb? Dann ist gezielter Datensatz-Zukauf situativ vertretbar – aber nicht als Dauerstrategie.',
	],
	[
		't' => 'Markteintritt ohne Marke',
		's' => 'Wer im neuen Markt startet und kurzfristig Pipeline braucht, kann Portale als Übergangslösung nutzen, parallel zum Aufbau des eigenen Systems.',
	],
	[
		't' => 'Testmarkt-Validierung',
		's' => 'Neues Produkt oder neue Region: Portal-Leads als kurzfristiger Marktcheck – nicht als langfristiger Funnel.',
	],
];

$faq = [
	[
		'question' => 'Wann lohnt sich der Umstieg vom Portal auf das eigene System?',
		'answer'   => sprintf( 'Faustregel: ab einem monatlichen Portal-Budget von 800 – 1.000 € amortisiert sich der Aufbau eines eigenen Anfrage-Systems im B2B-Mittelstand binnen 12 – 24 Monaten. Bei E3 New Energy lag die Amortisation in %1$s; gleichzeitig sank der CPL %2$s.', $e3_timeframe, $e3_cpl_reduction ),
	],
	[
		'question' => 'Kann ich beide Wege parallel fahren?',
		'answer'   => 'Ja, das ist sogar die übliche Übergangsstrategie. Im Aufbau-Zeitraum (4–10 Wochen) laufen Portal-Leads weiter, das eigene System geht parallel live. Sobald das eigene System stabil Anfragen liefert, werden die Portal-Verträge sukzessive heruntergefahren.',
	],
	[
		'question' => 'Was passiert mit den bestehenden Portal-Daten?',
		'answer'   => 'Bestehende CRM-Daten bleiben in Ihrem System. Was Sie verlieren, ist nur der Strom künftiger Portal-Leads – nicht das, was Sie bereits aufgebaut haben. Die Migration ins eigene CRM ist Teil der Setup-Phase.',
	],
	[
		'question' => 'Wie unterscheidet sich das von einer klassischen Performance-Agentur?',
		'answer'   => 'Klassische Performance-Agenturen bauen meist auf eigenen Konten und eigenen Tracking-Setups – das CRM, der Code und das Tracking gehören ihnen, nicht Ihnen. Bei Vertragsende verlieren Sie wieder die Datenebene. Das eigene Anfrage-System gehört vollständig dem Betrieb, inklusive Code, CRM-Anbindung und Server.',
	],
	[
		'question' => 'Gibt es einen konkreten Vergleichs-ROI auf Basis der eigenen Daten?',
		'answer'   => sprintf( 'Die belastbarste Referenz ist %1$s: Cost per Lead von %2$s auf %3$s (%4$s), 12 %% Abschlussquote, Asset im vollständigen Eigentum. Im Marktcheck wird auf Basis Ihrer konkreten Budgets eine individuelle Einordnung erstellt.', $e3_case_label, $e3_cpl_before, $e3_cpl_after, $e3_cpl_reduction ),
	],
];

// ── Schema.org ────────────────────────────────────────────────
$author_person     = function_exists( 'hu_get_canonical_author_person' ) ? hu_get_canonical_author_person() : [ '@type' => 'Person', 'name' => 'Haşim Üner', 'url' => home_url( '/' ) ];
$breadcrumb_schema = function_exists( 'hu_get_solar_subpage_breadcrumb_schema' )
	? hu_get_solar_subpage_breadcrumb_schema( $page_url, 'Eigene Leadgenerierung vs. Portale' )
	: [];

$service_schema = [
	'@context'    => 'https://schema.org',
	'@type'       => 'Service',
	'@id'         => trailingslashit( $page_url ) . '#service',
	'name'        => 'Eigene Leadgenerierung als Alternative zu Lead-Portalen',
	'serviceType' => 'Anfrage-System für Solar, Wärmepumpe und Speicher – Alternative zu DAA, Aroundhome, Check24',
	'url'         => $page_url,
	'description' => sprintf( 'Vergleich Portal-Leads vs. eigenes Anfrage-System für Solar- und Wärmepumpen-Anbieter. Referenz %1$s: %2$s niedrigere Cost per Lead in %3$s.', $e3_case_label, $e3_cpl_reduction, $e3_timeframe ),
	'provider'    => [ '@id' => home_url( '/#organization' ) ],
	'author'      => $author_person,
	'areaServed'  => [
		[ '@type' => 'Country', 'name' => 'Deutschland' ],
		[ '@type' => 'Country', 'name' => 'Österreich' ],
		[ '@type' => 'Country', 'name' => 'Schweiz' ],
	],
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
		'acceptedAnswer' => [
			'@type' => 'Answer',
			'text'  => $faq_item['answer'],
		],
	];
}

get_header();
?>

<main id="primary" class="hu-intercept" role="main" data-track-page="eigene-leadgenerierung-vs-portale">

	<section class="hu-intercept__hero" id="hero" aria-labelledby="hu-vs-hero-title">
		<div class="hu-intercept__container">
			<p class="hu-intercept__eyebrow">Strategischer Vergleich · CAPEX vs. OPEX · 24-Monats-Horizont</p>
			<h1 class="hu-intercept__title" id="hu-vs-hero-title">
				Portal-Leads vs. eigenes Anfrage-System — TCO-Vergleich über 24 Monate
			</h1>
			<p class="hu-intercept__lead">
				Portal-Leads sind <strong>OPEX</strong> – laufende Miete für mehrfach verkaufte Datensätze mit typisch <strong><?php echo esc_html( $e3_conv_before ); ?></strong> Abschlussquote. Eigene Anfrage-Systeme sind <strong>CAPEX</strong> – investiv aufgebaute Infrastruktur, die im Betrieb bleibt. Bei <?php echo esc_html( $e3_case_label ); ?>: <strong><?php echo esc_html( $e3_cpl_reduction ); ?></strong> niedrigerer CPL und Abschlussquote auf <strong><?php echo esc_html( $e3_sales_conversion ); ?></strong> in <strong><?php echo esc_html( $e3_timeframe ); ?></strong>.
			</p>
			<?php get_template_part( 'template-parts/seo-subpage-byline', null, [ 'template_path' => __FILE__ ] ); ?>
			<div class="hu-intercept__cta">
				<a class="hu-intercept__cta-primary"
				   href="<?php echo esc_url( $marktcheck_url ); ?>"
				   data-track-action="cta_marktcheck"
				   data-track-category="vergleich_portale"
				   data-track-section="hero">
					Marktcheck mit Fit-Entscheid starten
				</a>
				<a class="hu-intercept__cta-secondary"
				   href="<?php echo esc_url( $e3_url ); ?>"
				   data-track-action="cta_e3_case"
				   data-track-category="vergleich_portale"
				   data-track-section="hero">
					E3-Case lesen (<?php echo esc_html( $e3_lead_count ); ?> Anfragen, <?php echo esc_html( $e3_sales_conversion ); ?> Abschlussquote)
				</a>
			</div>
			<p class="hu-intercept__hero-related" style="margin-top:18px;font-size:13.5px;opacity:.75;">
				Konkrete CPL-Rechnung: <a
					href="<?php echo esc_url( home_url( '/solar-leads-kaufen-alternative/' ) ); ?>"
					data-track-action="related_to_cpl_rechnung"
					data-track-category="internal_link_hierarchy"
					data-track-section="hero">Photovoltaik-Leads kaufen oder eigene Anfragen? CPL-Vergleich pro Anfrage</a>
			</p>
		</div>
	</section>

	<section class="hu-intercept__compare" id="kurzvergleich" aria-labelledby="hu-vs-quick-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-vs-quick-title">Kurzvergleich: Portal mieten vs. eigenes System besitzen</h2>
			<div class="hu-intercept__grid hu-intercept__grid--two">
				<div class="hu-intercept__panel hu-intercept__panel--negative">
					<h3 class="hu-intercept__panel-title">Portal-Leads mieten</h3>
					<ul class="hu-intercept__facts">
						<?php foreach ( $rent_facts as $f ) : ?>
							<li>
								<span class="hu-intercept__fact-key"><?php echo esc_html( $f['k'] ); ?></span>
								<span class="hu-intercept__fact-label"><?php echo esc_html( $f['l'] ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
				<div class="hu-intercept__panel hu-intercept__panel--positive">
					<h3 class="hu-intercept__panel-title">Eigenes System besitzen</h3>
					<ul class="hu-intercept__facts">
						<?php foreach ( $own_facts as $f ) : ?>
							<li>
								<span class="hu-intercept__fact-key"><?php echo esc_html( $f['k'] ); ?></span>
								<span class="hu-intercept__fact-label"><?php echo esc_html( $f['l'] ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
		</div>
	</section>

	<section class="hu-intercept__system" id="matrix" aria-labelledby="hu-vs-matrix-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-vs-matrix-title">Vergleichsmatrix: 8 Kriterien im Detail</h2>
			<div class="hu-intercept__matrix" role="table" aria-label="Vergleichsmatrix Portal-Leads vs. eigenes System">
				<div class="hu-intercept__matrix-head" role="row">
					<span role="columnheader">Kriterium</span>
					<span role="columnheader">Portal mieten</span>
					<span role="columnheader">Eigenes System</span>
				</div>
				<?php foreach ( $comparison_rows as $row ) : ?>
					<div class="hu-intercept__matrix-row" role="row">
						<span class="hu-intercept__matrix-criterion" role="cell"><?php echo esc_html( $row['criterion'] ); ?></span>
						<span class="hu-intercept__matrix-rent" role="cell"><?php echo esc_html( $row['rent'] ); ?></span>
						<span class="hu-intercept__matrix-own" role="cell"><?php echo esc_html( $row['own'] ); ?></span>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="hu-intercept__why" id="tco" aria-labelledby="hu-vs-tco-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-vs-tco-title">TCO-Überschlag: 24 und 36 Monate</h2>
			<p class="hu-intercept__section-lead">
				Beispielrechnung für einen mittelständischen Solar-/Wärmepumpen-Betrieb mit etwa ~ 20 zusätzlichen Anfragen pro Monat. Werte beruhen auf typischen Marktdaten und der E3-Referenz.
			</p>
			<div class="hu-intercept__grid hu-intercept__grid--two">
				<?php foreach ( $tco_scenarios as $scenario ) : ?>
					<article class="hu-intercept__card">
						<h3 class="hu-intercept__card-title"><?php echo esc_html( $scenario['scope'] ); ?></h3>
						<p class="hu-intercept__card-text"><strong>Mieten:</strong> <?php echo esc_html( $scenario['rent'] ); ?></p>
						<p class="hu-intercept__card-text"><strong>Besitzen:</strong> <?php echo esc_html( $scenario['own'] ); ?></p>
						<p class="hu-intercept__card-text"><em><?php echo esc_html( $scenario['delta'] ); ?></em></p>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="hu-intercept__why" id="wann-mieten" aria-labelledby="hu-vs-rent-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-vs-rent-title">Wann Portal-Leads trotzdem Sinn ergeben</h2>
			<p class="hu-intercept__section-lead">
				Portal-Leads sind nicht grundsätzlich falsch. Sie sind falsch als <em>Dauerstrategie</em>. In drei Situationen sind sie nachvollziehbar – als Übergang, nicht als System.
			</p>
			<div class="hu-intercept__grid hu-intercept__grid--four">
				<?php foreach ( $when_rent_makes_sense as $item ) : ?>
					<article class="hu-intercept__card">
						<h3 class="hu-intercept__card-title"><?php echo esc_html( $item['t'] ); ?></h3>
						<p class="hu-intercept__card-text"><?php echo esc_html( $item['s'] ); ?></p>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="hu-intercept__why" id="vertiefung" aria-labelledby="hu-vs-linked-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-vs-linked-title">Vertiefende Bausteine: vom Vergleich zur Umsetzung</h2>
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

	<section class="hu-intercept__faq" id="faq" aria-labelledby="hu-vs-faq-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-vs-faq-title">Häufige Fragen zum Vergleich</h2>
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

	<section class="hu-intercept__final" id="final-cta" aria-labelledby="hu-vs-final-title">
		<div class="hu-intercept__container hu-intercept__container--centered">
			<h2 class="hu-intercept__h2" id="hu-vs-final-title">TCO im Marktcheck einordnen</h2>
			<p class="hu-intercept__final-text">
				Manueller, tiefer Marktcheck statt Software-Einheitsbrei. Händische Analyse Ihrer Region innerhalb von 48 Stunden per E-Mail — mit klarer Aussage, ab welchem monatlichen Lead-Budget sich der Umstieg vom Portal-Modell auf ein eigenes Anfrage-System wirtschaftlich rechnet.
			</p>
			<div class="hu-intercept__cta">
				<a class="hu-intercept__cta-primary"
				   href="<?php echo esc_url( $marktcheck_url ); ?>"
				   data-track-action="cta_marktcheck"
				   data-track-category="vergleich_portale"
				   data-track-section="final">
					Marktcheck mit Fit-Entscheid starten
				</a>
				<a class="hu-intercept__cta-secondary"
				   href="<?php echo esc_url( $solar_money_url ); ?>"
				   data-track-action="cta_money_page"
				   data-track-category="vergleich_portale"
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
		'track_category' => 'vergleich_portale',
	]
);

get_footer();
