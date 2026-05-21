<?php
/**
 * Template Name: B2B Solar Leads – Gewerbliche Photovoltaik
 * Description: Money-Page für gewerbliche PV-Leadgenerierung. Buying-Center,
 *              lange Sales-Zyklen, hohe Ticketgrößen. Abgrenzung zum
 *              B2C-Privatmarkt.
 *              Primärer CTA: Marktcheck auf /solar-waermepumpen-leadgenerierung/#marktcheck.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ── URLs ───────────────────────────────────────────────────────
$page_url        = home_url( '/b2b-solar-leads/' );
$solar_money_url = function_exists( 'nexus_get_energy_systems_url' )
	? nexus_get_energy_systems_url()
	: home_url( '/solar-waermepumpen-leadgenerierung/' );
$marktcheck_url  = trailingslashit( $solar_money_url ) . '#marktcheck';
$e3_url          = home_url( '/e3-new-energy/' );

// ── E3-Canon ──────────────────────────────────────────────────
$e3_canon            = function_exists( 'hu_e3_canon' ) ? hu_e3_canon() : [];
$e3_metrics          = isset( $e3_canon['metrics'] ) && is_array( $e3_canon['metrics'] ) ? $e3_canon['metrics'] : [];
$e3_case_label       = isset( $e3_canon['case_label'] ) ? (string) $e3_canon['case_label'] : 'E3 New Energy';
$e3_cpl_reduction    = $e3_metrics['cpl_reduction']['display'] ?? 'über 85 %';
$e3_lead_count       = $e3_metrics['lead_count']['display'] ?? '1.750+';
$e3_sales_conversion = $e3_metrics['sales_conversion']['display'] ?? '12 %';
$e3_timeframe        = $e3_metrics['timeframe']['display'] ?? '9 Monate';

// ── Inhalte ───────────────────────────────────────────────────
$b2b_facts = [
	[ 'k' => '50.000 €+', 'l' => 'durchschnittlicher Auftragswert pro gewerblichem PV-Projekt' ],
	[ 'k' => '100 – 180 Tage', 'l' => 'typische Sales-Zyklen mit Buying-Center und CFO-Freigabe' ],
	[ 'k' => '3 – 6', 'l' => 'Entscheider und Influencer pro gewerblicher Anfrage' ],
	[ 'k' => '< 5 %', 'l' => 'Anteil qualifizierter Gewerbe-Anfragen in B2C-Lead-Portalen' ],
];

$why_b2c_funnel_fails = [
	[
		't' => 'Falsche Sprache',
		's' => 'B2C-Portale werben mit „Solaranlage Kosten" – ein gewerblicher Einkäufer sucht nach „PPA", „Eigenverbrauchsoptimierung" oder „Hallendach Photovoltaik". Die Anfrage ist da, das Funnel-Vokabular fehlt.',
	],
	[
		't' => 'Buying-Center wird ignoriert',
		's' => 'Im Gewerbe entscheiden Geschäftsführung, CFO, Energie-Manager und Technik gemeinsam. Ein 5-Felder-Formular passt nicht zu einem Beschaffungsprozess mit drei Freigaben.',
	],
	[
		't' => 'Förder- und Finanzierungslogik fehlt',
		's' => 'KfW-Programme, IAB, Sonderabschreibung, PPA-Modelle, Investitions-Contracting – das gehört in die Anfragestrecke, nicht in ein FAQ am Seitenende.',
	],
	[
		't' => 'Geteilte Datensätze',
		's' => 'Ein Gewerbe-Lead, der bei drei Wettbewerbern liegt, wird nicht angenommen. Einkäufer wollen mit einem Anbieter sprechen, der ihre Sprache spricht – nicht im Bieterkrieg landen.',
	],
];

$gewerbe_layers = [
	[
		't' => 'Money Page für Gewerbe-PV',
		's' => 'Hallendächer, Quartiere, PPA, Industrieanlagen – jeweils mit eigener Story, eigenen Referenzen und eigener Vorqualifizierung. Keine B2C-Maske mit „auch für Gewerbe".',
	],
	[
		't' => 'Buying-Center-taugliche Anfrage-Strecke',
		's' => 'Mehrstufiger Funnel: Erstanfrage von Energie-Manager, Vertiefung mit CFO/Geschäftsführung, technischer Termin. Status pro Stakeholder im CRM.',
	],
	[
		't' => 'Anfrage-Qualifizierung nach Projektwert',
		's' => 'Dachfläche, Anschlussleistung, geplanter Eigenverbrauch, vorhandene Trafostation, EEG-Status – Anfragen werden grün/gelb/rot sortiert, bevor der Vertrieb anruft.',
	],
	[
		't' => 'CRM-Anschluss mit Stakeholder-Mapping',
		's' => 'HubSpot, Pipedrive oder Nexus-CRM bekommt das komplette Buying-Center inklusive Funktion, Status und Verantwortlichkeit. Keine isolierte Einzelanfrage.',
	],
	[
		't' => 'Tracking auf Auftragswert, nicht auf Klick',
		's' => 'Server-Side Tracking misst, welcher Kanal Anfragen mit Projektwert > 50.000 € produziert – nicht nur, welcher Kanal billige Klicks liefert.',
	],
];

$fit_yes = [
	[ 't' => 'Gewerbliche PV-Anbieter', 's' => 'Mit Fokus auf Hallendächer, Quartiere, Industrieanlagen oder PPA-Modelle.' ],
	[ 't' => 'Speicher-Lösungsanbieter', 's' => 'Großspeicher für Gewerbe, Eigenverbrauchsoptimierung, Spitzenlastmanagement.' ],
	[ 't' => 'EPC-Unternehmen', 's' => 'Engineering, Procurement & Construction für gewerbliche Energieprojekte.' ],
	[ 't' => 'Energie-Contractoren', 's' => 'Anbieter mit PPA-, Mietmodell- oder Investitions-Contracting-Strecken.' ],
];

$fit_no = [
	[ 't' => 'Reine B2C-Solarteure', 's' => 'Wer Privathäuser bestückt, braucht andere Funnel-Logik – siehe Branchen-Money-Page.' ],
	[ 't' => 'Eintägige Mengen-Verkäufer', 's' => '„100 Anfragen morgen" ist im Gewerbe weder realistisch noch profitabel.' ],
	[ 't' => 'Reine Vermittler', 's' => 'Wer Anfragen nur weiterverkauft, braucht kein eigenes B2B-System.' ],
];

$faq = [
	[
		'question' => 'Warum reicht eine B2C-Solar-Seite nicht für Gewerbe-PV?',
		'answer'   => 'B2C- und B2B-PV haben unterschiedliche Sprache, unterschiedliche Entscheider und unterschiedliche Vertragsstrukturen. Ein gewerblicher Einkäufer mit 800 kWp Anschlussleistung und PPA-Bedarf erkennt sich in einer Hausbesitzer-Seite mit „CO₂-Fußabdruck" nicht wieder. Die Anfragequalität fällt drastisch.',
	],
	[
		'question' => 'Wie viele Buying-Center-Stakeholder werden im Funnel abgebildet?',
		'answer'   => 'Typischerweise drei bis sechs: Energie-Manager (technische Erstprüfung), Geschäftsführung (strategische Freigabe), CFO (Finanzierung), Einkauf (Vertragsbedingungen), ggf. Facility Management und Nachhaltigkeitsbeauftragte. Jeder Stakeholder bekommt seinen eigenen Funnel-Schritt – nicht alles wird auf eine Person abgewälzt.',
	],
	[
		'question' => sprintf( 'Funktioniert die E3-Referenz auch im B2B-Gewerbe?', $e3_case_label ),
		'answer'   => sprintf( 'Die E3-Referenz (%1$s qualifizierte Anfragen, %2$s Abschlussquote, %3$s niedrigere Cost per Lead in %4$s) deckt B2C-Wärmepumpen und B2C/B2B-Photovoltaik ab. Im reinen Gewerbe-PV sind die Ticketgrößen größer, die Abschlussquoten ähnlich, die Sales-Zyklen länger – die System-Logik bleibt identisch.', $e3_lead_count, $e3_sales_conversion, $e3_cpl_reduction, $e3_timeframe ),
	],
	[
		'question' => 'Wie passt das mit DAA, Aroundhome oder Check24 zusammen?',
		'answer'   => 'Gar nicht. Diese Portale liefern überwiegend B2C-Anfragen, oft mehrfach verkauft, oft ohne Projektsubstanz. Für gewerbliche PV-Anbieter sind sie weder qualitativ noch wirtschaftlich tragfähig. Das B2B-Solar-System ist explizit als Alternative gebaut.',
	],
	[
		'question' => 'Wie lange dauert der Aufbau eines B2B-Solar-Systems?',
		'answer'   => 'Für ein vollständiges System mit Money Page, Vorqualifizierung, Buying-Center-Funnel, Tracking und CRM-Anbindung sind 6–10 Wochen Aufbauzeit realistisch. Die ersten qualifizierten Anfragen kommen typischerweise innerhalb der ersten 4–8 Wochen nach Live-Gang.',
	],
];

// ── Schema.org ────────────────────────────────────────────────
$author_person     = function_exists( 'hu_get_canonical_author_person' ) ? hu_get_canonical_author_person() : [ '@type' => 'Person', 'name' => 'Haşim Üner', 'url' => home_url( '/' ) ];
$breadcrumb_schema = function_exists( 'hu_get_solar_subpage_breadcrumb_schema' )
	? hu_get_solar_subpage_breadcrumb_schema( $page_url, 'B2B Solar Leads (Gewerbe)' )
	: [];

$service_schema = [
	'@context'    => 'https://schema.org',
	'@type'       => 'Service',
	'@id'         => trailingslashit( $page_url ) . '#service',
	'name'        => 'B2B-Leadgenerierung für gewerbliche Photovoltaik',
	'serviceType' => 'Anfrage-System für gewerbliche PV-, Speicher- und PPA-Anbieter',
	'url'         => $page_url,
	'description' => sprintf( 'Buying-Center-taugliche Anfrage-Architektur für gewerbliche Photovoltaik-Projekte. Referenz %1$s: %2$s niedrigere Cost per Lead in %3$s.', $e3_case_label, $e3_cpl_reduction, $e3_timeframe ),
	'provider'    => $author_person,
	'author'      => $author_person,
	'audience'    => [
		'@type'        => 'Audience',
		'audienceType' => 'Gewerbliche Photovoltaik-, Speicher- und EPC-Anbieter im DACH-Raum',
	],
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

<main id="primary" class="hu-intercept" role="main" data-track-page="b2b-solar-leads">

	<section class="hu-intercept__hero" id="hero" aria-labelledby="hu-b2b-hero-title">
		<div class="hu-intercept__container">
			<p class="hu-intercept__eyebrow">Gewerbliche Photovoltaik · Speicher · PPA</p>
			<h1 class="hu-intercept__title" id="hu-b2b-hero-title">
				B2B Solar Leads: Anfrage-Systeme für gewerbliche PV-Projekte ab 50.000 €
			</h1>
			<p class="hu-intercept__lead">
				Gewerbliche Photovoltaik braucht keine B2C-Leadportale. Sie braucht eine Anfrage-Architektur, die <strong>Buying-Center</strong>, <strong>lange Sales-Zyklen</strong> und <strong>komplexe Förderlogik</strong> abbildet – und Anfragen liefert, die wirtschaftlich bleiben.
			</p>
			<?php get_template_part( 'template-parts/seo-subpage-byline', null, [ 'template_path' => __FILE__ ] ); ?>
			<div class="hu-intercept__cta">
				<a class="hu-intercept__cta-primary"
				   href="<?php echo esc_url( $marktcheck_url ); ?>"
				   data-track-action="cta_marktcheck"
				   data-track-category="b2b_solar_leads"
				   data-track-section="hero">
					Kostenfreien Marktcheck starten
				</a>
				<a class="hu-intercept__cta-secondary"
				   href="<?php echo esc_url( $e3_url ); ?>"
				   data-track-action="cta_e3_case"
				   data-track-category="b2b_solar_leads"
				   data-track-section="hero">
					E3-Case ansehen
				</a>
			</div>
		</div>
	</section>

	<section class="hu-intercept__compare" id="fakten" aria-labelledby="hu-b2b-facts-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-b2b-facts-title">Warum gewerbliches PV-Lead-Geschäft anders funktioniert</h2>
			<div class="hu-intercept__grid hu-intercept__grid--four">
				<?php foreach ( $b2b_facts as $fact ) : ?>
					<article class="hu-intercept__card">
						<h3 class="hu-intercept__card-title"><?php echo esc_html( $fact['k'] ); ?></h3>
						<p class="hu-intercept__card-text"><?php echo esc_html( $fact['l'] ); ?></p>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="hu-intercept__why" id="warum-b2c-funnel" aria-labelledby="hu-b2b-why-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-b2b-why-title">Warum klassische B2C-Funnel im Gewerbe scheitern</h2>
			<div class="hu-intercept__grid hu-intercept__grid--four">
				<?php foreach ( $why_b2c_funnel_fails as $item ) : ?>
					<article class="hu-intercept__card">
						<h3 class="hu-intercept__card-title"><?php echo esc_html( $item['t'] ); ?></h3>
						<p class="hu-intercept__card-text"><?php echo esc_html( $item['s'] ); ?></p>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="hu-intercept__system" id="architektur" aria-labelledby="hu-b2b-arch-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-b2b-arch-title">Die Architektur für gewerbliche PV-Anfragen</h2>
			<p class="hu-intercept__section-lead">
				Fünf Schichten, die das B2B-Gewerbe-Geschäft tragen – von der Sprache der Money-Page bis zur Stakeholder-Mapping im CRM.
			</p>
			<ol class="hu-intercept__layers">
				<?php foreach ( $gewerbe_layers as $i => $layer ) : ?>
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

	<section class="hu-intercept__compare" id="fit" aria-labelledby="hu-b2b-fit-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-b2b-fit-title">Für wen das B2B-Solar-System passt</h2>
			<div class="hu-intercept__grid hu-intercept__grid--two">
				<div class="hu-intercept__panel hu-intercept__panel--positive">
					<h3 class="hu-intercept__panel-title">Passt</h3>
					<ul class="hu-intercept__facts">
						<?php foreach ( $fit_yes as $f ) : ?>
							<li>
								<span class="hu-intercept__fact-key"><?php echo esc_html( $f['t'] ); ?></span>
								<span class="hu-intercept__fact-label"><?php echo esc_html( $f['s'] ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
				<div class="hu-intercept__panel hu-intercept__panel--negative">
					<h3 class="hu-intercept__panel-title">Passt nicht</h3>
					<ul class="hu-intercept__facts">
						<?php foreach ( $fit_no as $f ) : ?>
							<li>
								<span class="hu-intercept__fact-key"><?php echo esc_html( $f['t'] ); ?></span>
								<span class="hu-intercept__fact-label"><?php echo esc_html( $f['s'] ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
		</div>
	</section>

	<section class="hu-intercept__faq" id="faq" aria-labelledby="hu-b2b-faq-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-b2b-faq-title">Häufige Fragen zur gewerblichen Solar-Leadgenerierung</h2>
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

	<section class="hu-intercept__final" id="final-cta" aria-labelledby="hu-b2b-final-title">
		<div class="hu-intercept__container hu-intercept__container--centered">
			<h2 class="hu-intercept__h2" id="hu-b2b-final-title">Gewerbe-PV-Strecke einordnen</h2>
			<p class="hu-intercept__final-text">
				Im Marktcheck zeigt sich, ob Ihre heutige Anfrage-Architektur Gewerbe-Buying-Center tragen kann – oder ob sie als B2C-Maske an gewerblichen Anfragen vorbei rennt.
			</p>
			<div class="hu-intercept__cta">
				<a class="hu-intercept__cta-primary"
				   href="<?php echo esc_url( $marktcheck_url ); ?>"
				   data-track-action="cta_marktcheck"
				   data-track-category="b2b_solar_leads"
				   data-track-section="final">
					Kostenfreien Marktcheck starten
				</a>
				<a class="hu-intercept__cta-secondary"
				   href="<?php echo esc_url( $solar_money_url ); ?>"
				   data-track-action="cta_money_page"
				   data-track-category="b2b_solar_leads"
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
		'track_category' => 'b2b_solar_leads',
	]
);

get_footer();
