<?php
/**
 * Template Name: Wärmepumpen Leads – Alternative (Intercept)
 * Description: Intercept-Landingpage für Suchintent "Wärmepumpen Leads",
 *              "Wärmepumpen Leads kaufen", "Leadgenerierung Wärmepumpe".
 *              Argumentation: Portal-Leads vs. eigenes Anfrage-System (CPL-Senkung),
 *              mit wärmepumpen-spezifischer Vorqualifizierung (Bestandsheizung,
 *              Gebäude, Förderung, Zeithorizont).
 *              Primärer Pfad: Marktcheck auf /solar-waermepumpen-leadgenerierung/#marktcheck.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ── URLs ───────────────────────────────────────────────────────
$page_url        = home_url( '/waermepumpen-leads/' );
$solar_money_url = function_exists( 'nexus_get_energy_systems_url' )
	? nexus_get_energy_systems_url()
	: home_url( '/solar-waermepumpen-leadgenerierung/' );
$marktcheck_url  = trailingslashit( $solar_money_url ) . '#marktcheck';
$e3_url          = home_url( '/case-study-solar-leadgenerierung/' );

$linked_assets = [
	[
		't'   => 'Photovoltaik & Solar Leads kaufen – die Alternative',
		's'   => 'Die gleiche Rechnung für PV-Anfragen: Markteinordnung der Lead-Anbieter und CPL-Vergleich pro Anfrage.',
		'url' => home_url( '/solar-leads-kaufen-alternative/' ),
	],
	[
		't'   => 'Strategischer TCO-Vergleich über 24 Monate',
		's'   => 'Portal-Leads gegen eigenes Anfrage-System mit CAPEX-vs-OPEX-Logik und Asset-Eigentum.',
		'url' => home_url( '/eigene-leadgenerierung-vs-portale/' ),
	],
	[
		't'   => 'Cost per Lead Photovoltaik',
		's'   => 'Drei CPL-Szenarien im Vergleich und welche versteckten Kostentreiber den CPL hochtreiben.',
		'url' => home_url( '/cost-per-lead-photovoltaik/' ),
	],
	[
		't'   => 'Qualifizierte Anfragen erkennen',
		's'   => 'Vier Merkmale, an denen sich eine wirtschaftliche Anfrage erkennen lässt – vor dem ersten Anruf.',
		'url' => home_url( '/qualifizierte-pv-anfragen/' ),
	],
];

// ── E3-Proof-Canon ─────────────────────────────────────────────
$e3_canon            = function_exists( 'hu_e3_canon' ) ? hu_e3_canon() : [];
$e3_metrics          = isset( $e3_canon['metrics'] ) && is_array( $e3_canon['metrics'] ) ? $e3_canon['metrics'] : [];
$e3_case_label       = isset( $e3_canon['case_label'] ) ? (string) $e3_canon['case_label'] : 'mittelständischer PV-Installationsbetrieb';
$e3_lead_count       = $e3_metrics['lead_count']['display'] ?? '1.750+';
$e3_sales_conversion = $e3_metrics['sales_conversion']['display'] ?? '12 %';
$e3_conv_uplift      = $e3_metrics['sales_conversion_uplift']['display'] ?? '1 – 5 % → 12 %';
$e3_conv_before      = $e3_metrics['sales_conversion_before']['display'] ?? '1 – 5 %';
$e3_cpl_reduction    = $e3_metrics['cpl_reduction']['display'] ?? 'über 85 %';
$e3_cpl_before       = $e3_metrics['cpl_before']['display'] ?? '150 €';
$e3_cpl_after        = $e3_metrics['cpl_after']['display'] ?? '22 €';
$e3_timeframe        = $e3_metrics['timeframe']['display'] ?? '6 Monate';

// ── Inhalt: Portal vs. eigenes System (Problem → Lösung) ──────
$portal_facts = [
	[
		'k' => '60 – 120 €',
		'l' => 'CPL für „exklusive" Wärmepumpen-Leads im Portal-Markt',
	],
	[
		'k' => 'bis zu 5×',
		'l' => 'Mehrfachverkauf desselben Datensatzes an Wettbewerber',
	],
	[
		'k' => $e3_conv_before,
		'l' => 'typische Abschlussquote auf gekaufte Portal-Leads',
	],
	[
		'k' => '0 % Asset',
		'l' => 'kein Code, kein Tracking, keine Daten verbleiben im Betrieb',
	],
];

$own_facts = [
	[
		'k' => $e3_cpl_after,
		'l' => 'Cost per Lead im eigenen Anfrage-System (Case-Study-Referenz)',
	],
	[
		'k' => '100 %',
		'l' => 'exklusive Anfragen – kein Parallelversand an Wettbewerber',
	],
	[
		'k' => $e3_conv_uplift,
		'l' => 'Sprung der Abschlussquote — von Portal-Leads zu eigenem System (Case Study)',
	],
	[
		'k' => '100 % Asset',
		'l' => 'Code, Tracking und Daten bleiben im Betrieb',
	],
];

$why_portals_fail = [
	[
		't' => 'Heizungstausch ist beratungsintensiv',
		's' => 'Eine Wärmepumpe ist kein Spontankauf: Bestandsheizung, Gebäude und Förderung entscheiden mit. Portale verkaufen den Erstklick – die teure Beratungsarbeit danach bleibt komplett bei Ihnen.',
	],
	[
		't' => 'Mehrfachverkauf zerstört Margen',
		's' => 'Drei bis fünf Betriebe bieten gegen denselben Datensatz. Die Conversion sinkt, der Preisdruck steigt, der Vertrieb verbrennt Zeit an Vergleichs-Shopper.',
	],
	[
		't' => 'Keine Vorqualifizierung',
		's' => 'Heizart, Gebäudetyp, Sanierungsstand und Budget bleiben offen – Sie qualifizieren am Telefon nach und sortieren Öl-Nostalgiker und Unsanierbare selbst aus.',
	],
	[
		't' => 'Daten gehören dem Portal',
		's' => 'Sie bezahlen pro Datensatz, ohne das System zu besitzen, das ihn erzeugt. Kündigung = Anfrage-Stopp.',
	],
];

$market_models = [
	[
		't' => 'Geteilte Datensätze',
		's' => 'Eine Endkundenanfrage wird parallel an mehrere SHK-Betriebe verkauft. Marktbreit etablierte Mengenmodelle (z. B. Aroundhome, DAA, Wattfox oder Checkfox in Teilen). Preis pro Datensatz niedriger, Wettbewerb höher.',
	],
	[
		't' => 'Exklusive Datensätze',
		's' => 'Anfrage wird nur an einen Anbieter weitergegeben. Selteneres Modell, höherer Preis pro Datensatz (oft 80 – 150 €). Abschlussquote tendenziell höher, Qualität bleibt anbieterabhängig.',
	],
	[
		't' => 'Regional erzeugte Leads',
		's' => 'Anfragen werden über lokale Kampagnen im Namen des Fachbetriebs eingesammelt. Markenwirkung bleibt beim Betrieb, Volumen abhängig von der lokalen Kampagne.',
	],
	[
		't' => 'Eigene Anfrage-Infrastruktur',
		's' => 'Money Page, Server-Side-Tracking und Vorqualifizierung im eigenen Eigentum. Anfragen sind per Definition exklusiv. Setup einmalig, Asset im Betrieb. Vergleich: siehe Case-Study-Referenz.',
	],
];

$wp_prequal_criteria = [
	[
		'k' => 'Bestandsheizung & Energieträger',
		'l' => 'Öl, Gas, Nachtspeicher oder bereits Hybrid? Der Ausgangspunkt bestimmt Aufwand, Förderfähigkeit und Projektwert – und gehört vor den ersten Anruf, nicht ins dritte Telefonat.',
	],
	[
		'k' => 'Gebäude & Sanierungsstand',
		'l' => 'Baujahr, Dämmung, Heizkörper oder Flächenheizung: Ohne diese Angaben ist keine seriöse Ersteinschätzung möglich – gekaufte Datensätze liefern sie fast nie mit.',
	],
	[
		'k' => 'Fördersituation',
		'l' => 'Ob und wie ein Endkunde von BEG-Förderung profitiert, entscheidet über Budget und Zeitpunkt. Eine eigene Strecke fragt das strukturiert ab, bevor der Vertrieb Zeit investiert.',
	],
	[
		'k' => 'Zeithorizont & Budget',
		'l' => 'Defekte Heizung mit Handlungsdruck oder „irgendwann mal"-Interesse? Die Dringlichkeit trennt kaufbereite Anfragen von Informationssammlern.',
	],
];

$own_system_layers = [
	[
		't' => 'Money Page mit Wärmepumpen-Vorqualifizierung',
		's' => 'Bestandsheizung, Gebäude, Fördersituation und Zeithorizont – strukturiert abgefragt, bevor der Vertrieb anruft.',
	],
	[
		't' => 'Server-Side-Tracking (CAPI)',
		's' => 'Eigener Container in Frankfurt, DSGVO-konform. Ad-Blocker, ITP und Cookieless brechen die Attribution nicht mehr.',
	],
	[
		't' => 'CRM-Anschluss & Lead-Scoring',
		's' => 'Anfragen laufen sortiert in den Vertrieb – grün, gelb, rot. Sie rufen die kaufbereiten zuerst zurück.',
	],
	[
		't' => 'Eine Strecke für Wärmepumpe und Photovoltaik',
		's' => 'Dieselbe Architektur trägt beide Produkte – Vorqualifizierung pro Produkt gewichtet, System-Logik identisch. Cross-Selling inklusive.',
	],
];

$objections = [
	[
		'question' => 'Was kosten Wärmepumpen-Leads im Einkauf?',
		'answer'   => 'Geteilte Datensätze liegen im Markt typischerweise bei 60 – 120 € pro Anfrage, exklusive bei 80 – 150 €. Entscheidend ist der Preis pro Auftrag: Bei 1 – 5 % Abschlussquote und beratungsintensiven Heizungstausch-Projekten kostet ein gewonnener Auftrag schnell das Zwanzigfache des Lead-Preises.',
	],
	[
		'question' => 'Wo kann man Wärmepumpen-Leads kaufen – und worauf ist zu achten?',
		'answer'   => 'Der Markt unterteilt sich in geteilte Datensätze, exklusive Datensätze, regional erzeugte Kampagnen-Leads und eigene Anfrage-Infrastruktur. Prüfen Sie vor jedem Kauf: Wie viele Wettbewerber erhalten denselben Datensatz, welche Angaben zu Bestandsheizung und Gebäude liegen vor, und wie alt ist die Anfrage bei Übermittlung? Die Markteinordnung weiter oben zeigt alle vier Wege ohne Wertung.',
	],
	[
		'question' => 'Warum schließen gekaufte Wärmepumpen-Leads so selten ab?',
		'answer'   => 'Weil der Heizungstausch beratungsintensiv ist und der Datensatz mehrfach verkauft wird: Der Endkunde spricht parallel mit drei Betrieben, während Bestandsheizung, Sanierungsstand und Fördersituation ungeklärt sind. Typische Abschlussquoten liegen bei 1 – 5 %. Im eigenen System stieg die Quote bei einem mittelständischen PV-Installationsbetrieb auf 12 %, weil die Vorqualifizierung vor dem ersten Anruf passiert.',
	],
	[
		'question' => 'Funktioniert ein eigenes Anfrage-System für Wärmepumpe und Photovoltaik zusammen?',
		'answer'   => sprintf( 'Ja – das ist der Regelfall. Die gleiche Architektur trägt Wärmepumpe, Photovoltaik und Speicher; nur die Vorqualifizierung wird pro Produkt anders gewichtet. Referenz %1$s: %2$s qualifizierte Anfragen in %3$s, CPL von %4$s auf %5$s gesenkt.', $e3_case_label, $e3_lead_count, $e3_timeframe, $e3_cpl_before, $e3_cpl_after ),
	],
	[
		'question' => 'Wie schnell liefert ein eigenes System Wärmepumpen-Anfragen?',
		'answer'   => 'Portal-Leads kommen sofort, ein eigenes System braucht 6–12 Wochen Aufbau. Danach liefert es exklusive Anfragen zu deutlich niedrigeren Stückkosten – und das Asset gehört Ihnen. Viele Betriebe fahren die Übergangszeit zweigleisig und drosseln den Zukauf, sobald die eigene Strecke trägt.',
	],
	[
		'question' => 'Verkaufen Sie selbst Wärmepumpen-Leads?',
		'answer'   => 'Nein. Es werden keine Datensätze weiterverkauft. Aufgebaut wird die Infrastruktur, die in Ihrem Eigentum bleibt: Code, Tracking, CRM-Anbindung und Datenhoheit. Was Sie damit anfangen, bleibt Ihre Entscheidung.',
	],
];

// ── Schema.org: Service + FAQPage + BreadcrumbList ────────────
$author_person   = function_exists( 'hu_get_canonical_author_person' ) ? hu_get_canonical_author_person() : [ '@type' => 'Person', 'name' => 'Haşim Üner', 'url' => home_url( '/' ) ];
$breadcrumb_schema = function_exists( 'hu_get_solar_subpage_breadcrumb_schema' )
	? hu_get_solar_subpage_breadcrumb_schema( $page_url, 'Wärmepumpen Leads – Alternative' )
	: [];

$service_schema = [
	'@context'    => 'https://schema.org',
	'@type'       => 'Service',
	'@id'         => trailingslashit( $page_url ) . '#service',
	'name'        => 'Eigenes Anfrage-System statt Wärmepumpen-Leads kaufen',
	'serviceType' => 'Alternative zu Lead-Portalen: Aufbau eigener Wärmepumpen-Anfrage-Infrastruktur',
	'url'         => $page_url,
	'description' => sprintf( 'Aufbau eines eigenen Anfrage-Systems für Wärmepumpen- und SHK-Betriebe im DACH-Raum. Referenz %1$s: %2$s niedrigere Cost per Lead in %3$s.', $e3_case_label, $e3_cpl_reduction, $e3_timeframe ),
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

foreach ( $objections as $faq_item ) {
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

<main id="primary" class="hu-intercept" role="main" data-track-page="waermepumpen-leads">

	<section class="hu-intercept__hero" id="hero" aria-labelledby="hu-intercept-hero-title">
		<div class="hu-intercept__container">
			<p class="hu-intercept__eyebrow">Für Wärmepumpen-, SHK- und Heizungsbau-Betriebe im DACH-Mittelstand</p>
			<h1 class="hu-intercept__title" id="hu-intercept-hero-title">
				Sie wollen Wärmepumpen-Leads kaufen? Eigene Anfragen senken den CPL um <?php echo esc_html( $e3_cpl_reduction ); ?>.
			</h1>
			<p class="hu-intercept__lead">
				Gekaufte Wärmepumpen-Leads kosten <strong>60 – 120 €</strong>, werden parallel an mehrere Betriebe verkauft und schließen typischerweise nur bei <strong><?php echo esc_html( $e3_conv_before ); ?></strong>. Bei <?php echo esc_html( $e3_case_label ); ?> stieg die Abschlussquote im eigenen System auf <strong><?php echo esc_html( $e3_sales_conversion ); ?></strong> — der Cost per Lead fiel parallel von <strong><?php echo esc_html( $e3_cpl_before ); ?></strong> auf <strong><?php echo esc_html( $e3_cpl_after ); ?></strong> in <strong><?php echo esc_html( $e3_timeframe ); ?></strong>.
			</p>
			<?php get_template_part( 'template-parts/seo-subpage-byline', null, [ 'template_path' => __FILE__ ] ); ?>
			<div class="hu-intercept__cta">
				<a class="hu-intercept__cta-primary"
				   href="<?php echo esc_url( $marktcheck_url ); ?>"
				   data-track-action="cta_marktcheck"
				   data-track-category="intercept_waermepumpen_leads"
				   data-track-section="hero">
					Marktcheck mit Fit-Entscheid starten
				</a>
				<a class="hu-intercept__cta-secondary"
				   href="<?php echo esc_url( $e3_url ); ?>"
				   data-track-action="cta_case_study"
				   data-track-category="intercept_waermepumpen_leads"
				   data-track-section="hero">
					Case Study lesen (<?php echo esc_html( $e3_lead_count ); ?> Anfragen, <?php echo esc_html( $e3_sales_conversion ); ?> Abschlussquote)
				</a>
			</div>
			<p class="hu-intercept__hero-related" style="margin-top:18px;font-size:13.5px;opacity:.75;">
				Für Photovoltaik-Anfragen: <a
					href="<?php echo esc_url( home_url( '/solar-leads-kaufen-alternative/' ) ); ?>"
					data-track-action="related_to_solar_intercept"
					data-track-category="internal_link_hierarchy"
					data-track-section="hero">Photovoltaik & Solar Leads kaufen — die Alternative im CPL-Vergleich</a>
			</p>
		</div>
	</section>

	<section class="hu-intercept__compare" id="vergleich" aria-labelledby="hu-intercept-compare-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-intercept-compare-title">Wärmepumpen-Leads kaufen vs. eigenes Anfrage-System</h2>
			<div class="hu-intercept__grid hu-intercept__grid--two">
				<div class="hu-intercept__panel hu-intercept__panel--negative">
					<h3 class="hu-intercept__panel-title">Portal-Leads kaufen</h3>
					<ul class="hu-intercept__facts">
						<?php foreach ( $portal_facts as $fact ) : ?>
							<li>
								<span class="hu-intercept__fact-key"><?php echo esc_html( $fact['k'] ); ?></span>
								<span class="hu-intercept__fact-label"><?php echo esc_html( $fact['l'] ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
				<div class="hu-intercept__panel hu-intercept__panel--positive">
					<h3 class="hu-intercept__panel-title">Eigenes Anfrage-System</h3>
					<ul class="hu-intercept__facts">
						<?php foreach ( $own_facts as $fact ) : ?>
							<li>
								<span class="hu-intercept__fact-key"><?php echo esc_html( $fact['k'] ); ?></span>
								<span class="hu-intercept__fact-label"><?php echo esc_html( $fact['l'] ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
			<p class="hu-intercept__compare-note">
				Quelle der eigenen Zahlen: Methodik-Case <a href="<?php echo esc_url( $e3_url ); ?>"><?php echo esc_html( $e3_case_label ); ?></a> – Photovoltaik- und Wärmepumpen-Anbieter im DACH-Mittelstand.
			</p>
		</div>
	</section>

	<section class="hu-intercept__why" id="warum-portale" aria-labelledby="hu-intercept-why-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-intercept-why-title">Warum der Zukauf von Wärmepumpen-Leads das Wachstum bremst</h2>
			<div class="hu-intercept__grid hu-intercept__grid--four">
				<?php foreach ( $why_portals_fail as $item ) : ?>
					<article class="hu-intercept__card">
						<h3 class="hu-intercept__card-title"><?php echo esc_html( $item['t'] ); ?></h3>
						<p class="hu-intercept__card-text"><?php echo esc_html( $item['s'] ); ?></p>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="hu-intercept__why" id="markt-modelle" aria-labelledby="hu-intercept-markt-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-intercept-markt-title">Wärmepumpen-Leads-Anbieter im Vergleich: sachliche Markteinordnung</h2>
			<p class="hu-intercept__section-lead">
				Der deutsche Markt für Wärmepumpen-Anfragen unterteilt sich in vier strukturell unterschiedliche Modelle. Eigene Markteinordnung – keine Empfehlung, keine Wertung.
			</p>
			<div class="hu-intercept__grid hu-intercept__grid--four">
				<?php foreach ( $market_models as $model ) : ?>
					<article class="hu-intercept__card">
						<h3 class="hu-intercept__card-title"><?php echo esc_html( $model['t'] ); ?></h3>
						<p class="hu-intercept__card-text"><?php echo esc_html( $model['s'] ); ?></p>
					</article>
				<?php endforeach; ?>
			</div>
			<p class="hu-intercept__section-lead" style="margin-top:24px;">
				Einzelne Anbieter im Detail: <a href="<?php echo esc_url( home_url( '/aroundhome-solar-einordnung/' ) ); ?>" data-track-action="provider_aroundhome" data-track-category="internal_link_hierarchy" data-track-section="markt-modelle">Aroundhome-Erfahrungen für Handwerker</a> · <a href="<?php echo esc_url( home_url( '/wattfox-solar-leads-einordnung/' ) ); ?>" data-track-action="provider_wattfox" data-track-category="internal_link_hierarchy" data-track-section="markt-modelle">Wattfox-Erfahrungen &amp; Kosten</a> · <a href="<?php echo esc_url( home_url( '/daa-photovoltaik-leads-einordnung/' ) ); ?>" data-track-action="provider_daa" data-track-category="internal_link_hierarchy" data-track-section="markt-modelle">DAA Leads</a> · <a href="<?php echo esc_url( home_url( '/checkfox-solar-waermepumpe-einordnung/' ) ); ?>" data-track-action="provider_checkfox" data-track-category="internal_link_hierarchy" data-track-section="markt-modelle">Checkfox: seriös?</a> · <a href="<?php echo esc_url( home_url( '/leadfluss-pv-leads-einordnung/' ) ); ?>" data-track-action="provider_leadfluss" data-track-category="internal_link_hierarchy" data-track-section="markt-modelle">Leadfluss</a>
			</p>
		</div>
	</section>

	<section class="hu-intercept__compare" id="vorqualifizierung" aria-labelledby="hu-intercept-quality-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-intercept-quality-title">Vier Angaben, die eine Wärmepumpen-Anfrage wirtschaftlich machen</h2>
			<p class="hu-intercept__section-lead">
				Der Heizungstausch ist das beratungsintensivste Produkt im Energie-Vertrieb. Ob eine Anfrage Geld verdient oder Zeit verbrennt, entscheidet sich an vier Angaben – vor dem ersten Anruf.
			</p>
			<div class="hu-intercept__panel hu-intercept__panel--positive">
				<ul class="hu-intercept__facts">
					<?php foreach ( $wp_prequal_criteria as $crit ) : ?>
						<li>
							<span class="hu-intercept__fact-key"><?php echo esc_html( $crit['k'] ); ?></span>
							<span class="hu-intercept__fact-label"><?php echo esc_html( $crit['l'] ); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
	</section>

	<section class="hu-intercept__system" id="system" aria-labelledby="hu-intercept-system-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-intercept-system-title">So sieht Leadgenerierung für Wärmepumpen-Anbieter im eigenen System aus</h2>
			<p class="hu-intercept__section-lead">
				Vier Bausteine – jeder einzeln messbar, gemeinsam ergeben sie eine Strecke, die <strong>exklusive Wärmepumpen-Anfragen</strong> produziert, statt sie zu mieten.
			</p>
			<ol class="hu-intercept__layers">
				<?php foreach ( $own_system_layers as $i => $layer ) : ?>
					<li class="hu-intercept__layer">
						<span class="hu-intercept__layer-index"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
						<div class="hu-intercept__layer-body">
							<h3 class="hu-intercept__layer-title"><?php echo esc_html( $layer['t'] ); ?></h3>
							<p class="hu-intercept__layer-text"><?php echo esc_html( $layer['s'] ); ?></p>
						</div>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
	</section>

	<section class="hu-intercept__why" id="vertiefung" aria-labelledby="hu-intercept-linked-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-intercept-linked-title">Vertiefende Bausteine im Anfrage-System</h2>
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

	<section class="hu-intercept__faq" id="faq" aria-labelledby="hu-intercept-faq-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-intercept-faq-title">Häufige Fragen zu Wärmepumpen-Leads</h2>
			<div class="hu-intercept__faq-list">
				<?php foreach ( $objections as $item ) : ?>
					<details class="hu-intercept__faq-item">
						<summary class="hu-intercept__faq-q"><?php echo esc_html( $item['question'] ); ?></summary>
						<p class="hu-intercept__faq-a"><?php echo esc_html( $item['answer'] ); ?></p>
					</details>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="hu-intercept__final" id="final-cta" aria-labelledby="hu-intercept-final-title">
		<div class="hu-intercept__container hu-intercept__container--centered">
			<h2 class="hu-intercept__h2" id="hu-intercept-final-title">Statt Leads zu kaufen: Marktcheck mit Fit-Entscheid starten</h2>
			<p class="hu-intercept__final-text">
				Manueller Marktcheck statt Software-Einheitsbrei. Sie erhalten eine händische Einordnung Ihrer Region, Lead-Quellen und Anfragequalität — mit klarer Aussage, ob ein eigenes Anfrage-System für Ihren Betrieb wirtschaftlicher ist als der Zukauf von Wärmepumpen-Leads. Ohne Pflicht-Call, ohne Newsletter.
			</p>
			<div class="hu-intercept__cta">
				<a class="hu-intercept__cta-primary"
				   href="<?php echo esc_url( $marktcheck_url ); ?>"
				   data-track-action="cta_marktcheck"
				   data-track-category="intercept_waermepumpen_leads"
				   data-track-section="final">
					Marktcheck mit Fit-Entscheid starten
				</a>
				<a class="hu-intercept__cta-secondary"
				   href="<?php echo esc_url( $solar_money_url ); ?>"
				   data-track-action="cta_money_page"
				   data-track-category="intercept_waermepumpen_leads"
				   data-track-section="final">
					Methode und System ansehen
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
		'track_category' => 'intercept_waermepumpen_leads',
	]
);

get_footer();
