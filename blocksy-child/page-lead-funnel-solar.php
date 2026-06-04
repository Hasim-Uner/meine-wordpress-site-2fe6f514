<?php
/**
 * Template Name: Lead-Funnel Solar – Pillar Page
 * Description: MOFU/BOFU Pillar-Page für interne Marketing-Manager und
 *              Vertriebsleiter in Solar-/Wärmepumpen-Unternehmen.
 *              Erklärt die Funnel-Architektur ohne Lehrbuch-Ton.
 *              Primärer CTA: Marktcheck.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$page_url        = home_url( '/lead-funnel-solar/' );
$solar_money_url = function_exists( 'nexus_get_energy_systems_url' )
	? nexus_get_energy_systems_url()
	: home_url( '/solar-waermepumpen-leadgenerierung/' );
$marktcheck_url  = trailingslashit( $solar_money_url ) . '#marktcheck';
$e3_url          = home_url( '/e3-new-energy/' );
$sst_url         = home_url( '/server-side-tracking-b2b/' );
$vs_url          = home_url( '/eigene-leadgenerierung-vs-portale/' );

$e3_canon            = function_exists( 'hu_e3_canon' ) ? hu_e3_canon() : [];
$e3_metrics          = isset( $e3_canon['metrics'] ) && is_array( $e3_canon['metrics'] ) ? $e3_canon['metrics'] : [];
$e3_case_label       = isset( $e3_canon['case_label'] ) ? (string) $e3_canon['case_label'] : 'E3 New Energy';
$e3_cpl_reduction    = $e3_metrics['cpl_reduction']['display'] ?? 'über 85 %';
$e3_lead_count       = $e3_metrics['lead_count']['display'] ?? '1.750+';
$e3_sales_conversion = $e3_metrics['sales_conversion']['display'] ?? '12 %';
$e3_timeframe        = $e3_metrics['timeframe']['display'] ?? '6 Monate';

$stages = [
	[
		't' => 'TOFU – Sichtbarkeit',
		's' => 'SEO-Sichtbarkeit auf relevante Suchintentionen (Photovoltaik-Leadgenerierung, Wärmepumpen-Förderung, Speicher-Wirtschaftlichkeit), Google Ads für lokale Hochintent-Begriffe, organische LinkedIn-Präsenz. Ziel: qualifizierter Traffic, nicht Reichweite.',
	],
	[
		't' => 'MOFU – Vorqualifizierung',
		's' => 'Manueller Marktcheck statt 5-Felder-Formular. Region, Heizart, Dach, Projektwert werden vor der Kontaktaufnahme abgefragt — der Befund kommt händisch per E-Mail innerhalb von 48 Stunden. Wer das Marktcheck nicht durchläuft, ist kein passendes Match.',
	],
	[
		't' => 'BOFU – Anfrage & Termin',
		's' => 'Kontaktdaten kommen erst nach grünem Fit. Direkt im Anschluss: händisch geprüfter Befund per E-Mail innerhalb von 48 Stunden, optionaler Cal.com-Termin, automatischer CRM-Eintrag inklusive Lead-Score.',
	],
	[
		't' => 'Vertrieb – Anschluss',
		's' => 'Der Vertrieb bekommt Anfragen sortiert nach Lead-Score und Projektwert. Grün/gelb/rot statt unsortierter Excel-Liste. Wer nicht zurückruft, sieht den Drop-off im Reporting.',
	],
	[
		't' => 'Tracking – Attribution',
		's' => 'Server-Side Tracking misst, welcher Kanal die Anfragen mit Auftragswert > X € produziert. Skalierungs-Entscheidungen werden datenbasiert getroffen, nicht aus dem Bauch.',
	],
];

$funnel_pitfalls = [
	[
		't' => 'Funnel ohne Sales-Anschluss',
		's' => 'Eine perfekte Funnel-Strecke nützt nichts, wenn der Vertrieb Anfragen nicht zurückruft. Die Reaktionszeit ist der häufigste Funnel-Killer im B2B.',
	],
	[
		't' => 'Tracking als Nachgedanke',
		's' => 'Wenn das Tracking erst nach dem Funnel aufgesetzt wird, fehlen Daten in der entscheidenden Anfangsphase – und Reports zeigen falsche Kanal-Performance.',
	],
	[
		't' => '5-Felder-Formular vor der Qualifizierung',
		's' => 'Wer Kontaktdaten verlangt, bevor er den Fit klärt, verliert Interessenten. Vorqualifizierung kommt zuerst, Kontaktdaten danach.',
	],
	[
		't' => 'Keine Differenzierung B2C/B2B',
		's' => 'Ein einziger Funnel für Privatkunden und gewerbliche Anfragen verfehlt beide Zielgruppen. Buying-Center brauchen mehrstufige Funnel, B2C nicht.',
	],
];

$linked_assets = [
	[ 't' => 'Server-Side Tracking für B2B', 's' => 'GA4, Meta CAPI, Consent Mode v2 – die Daten-Schicht unter dem Funnel.', 'url' => $sst_url ],
	[ 't' => 'TCO über 24 Monate: Portal-Leads vs. eigenes System', 's' => 'Strategischer 8-Kriterien-Vergleich mit Asset-Eigentum-Logik.', 'url' => $vs_url ],
	[ 't' => 'E3-Methodik-Case', 's' => sprintf( '%1$s qualifizierte Anfragen, %2$s Abschlussquote, %3$s niedrigere Cost per Lead.', $e3_lead_count, $e3_sales_conversion, $e3_cpl_reduction ), 'url' => $e3_url ],
];

$faq = [
	[
		'question' => 'Was unterscheidet einen B2B-Lead-Funnel von einem klassischen Marketing-Funnel?',
		'answer'   => 'B2B-Funnel im Solar-/Wärmepumpen-Geschäft müssen Buying-Center, längere Sales-Zyklen und höhere Ticketgrößen tragen. Das heißt: mehrstufige Vorqualifizierung statt einfacher Lead-Magnet, Stakeholder-Mapping im CRM statt Einzel-Anfrage, Tracking auf Auftragswert statt Klick-Reports.',
	],
	[
		'question' => 'Wie lang dauert der Aufbau einer eigenen Funnel-Strecke?',
		'answer'   => 'Für eine vollständige Funnel-Strecke mit Money Page, Vorqualifizierung, Tracking und CRM-Anbindung sind 6–10 Wochen Aufbauzeit realistisch. Die ersten Anfragen kommen typischerweise innerhalb der ersten 4–8 Wochen nach Live-Gang.',
	],
	[
		'question' => 'Welche KPIs sind im Solar-/Wärmepumpen-Funnel relevant?',
		'answer'   => 'Top-of-Funnel: organische Sichtbarkeit auf relevante Begriffe, Click-Through-Rate aus den SERPs, Cost per Click aus Ads. Mid-Funnel: Vorqualifizierungs-Conversion, Abbruchrate, Lead-Score-Verteilung. Bottom-of-Funnel: Cost per qualifizierter Anfrage, Abschlussquote, Auftragswert pro Quelle.',
	],
	[
		'question' => 'Wie wird der Funnel an das CRM angebunden?',
		'answer'   => 'Über Webhooks vom Server-Side-Tracking-Container ins CRM (HubSpot, Brevo, Pipedrive, Nexus). Jede Anfrage kommt mit Lead-Score, Quelle, Kampagne und Vorqualifizierungs-Antworten an. Keine manuelle Excel-Übergabe, keine Zapier-Lücken.',
	],
];

$author_person     = function_exists( 'hu_get_canonical_author_person' ) ? hu_get_canonical_author_person() : [ '@type' => 'Person', 'name' => 'Haşim Üner', 'url' => home_url( '/' ) ];
$breadcrumb_schema = function_exists( 'hu_get_solar_subpage_breadcrumb_schema' )
	? hu_get_solar_subpage_breadcrumb_schema( $page_url, 'Lead-Funnel Solar' )
	: [];

$service_schema = [
	'@context'    => 'https://schema.org',
	'@type'       => 'Service',
	'@id'         => trailingslashit( $page_url ) . '#service',
	'name'        => 'Lead-Funnel-Architektur für Solar- und Wärmepumpen-Anbieter',
	'serviceType' => 'Funnel-Aufbau von TOFU bis Sales-Anschluss',
	'url'         => $page_url,
	'description' => sprintf( 'Lead-Funnel-Architektur für Photovoltaik- und Wärmepumpen-Anbieter im DACH-Mittelstand. Referenz %1$s: %2$s niedrigere Cost per Lead in %3$s.', $e3_case_label, $e3_cpl_reduction, $e3_timeframe ),
	'provider'    => [ '@id' => home_url( '/#organization' ) ],
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

<main id="primary" class="hu-intercept" role="main" data-track-page="lead-funnel-solar">

	<section class="hu-intercept__hero" id="hero" aria-labelledby="hu-funnel-hero-title">
		<div class="hu-intercept__container">
			<p class="hu-intercept__eyebrow">Für Solar-, Wärmepumpen- und Speicher-Anbieter im DACH-Mittelstand</p>
			<h1 class="hu-intercept__title" id="hu-funnel-hero-title">
				Lead-Funnel für Solar und Wärmepumpe: 5 Stufen, eine Strecke
			</h1>
			<p class="hu-intercept__lead">
				Ein Lead-Funnel ist keine Sammlung von Tools – sondern eine durchgehende Strecke vom ersten Suchwort bis zum Auftragsabschluss. Die fünf Stufen unten zeigen die Architektur, die im DACH-Mittelstand für Photovoltaik-, Wärmepumpen- und Speicher-Anbieter funktioniert.
			</p>
			<?php get_template_part( 'template-parts/seo-subpage-byline', null, [ 'template_path' => __FILE__ ] ); ?>
			<div class="hu-intercept__cta">
				<a class="hu-intercept__cta-primary"
				   href="<?php echo esc_url( $marktcheck_url ); ?>"
				   data-track-action="cta_marktcheck"
				   data-track-category="lead_funnel_solar"
				   data-track-section="hero">
					Marktcheck mit Fit-Entscheid starten
				</a>
				<a class="hu-intercept__cta-secondary"
				   href="<?php echo esc_url( $e3_url ); ?>"
				   data-track-action="cta_e3_case"
				   data-track-category="lead_funnel_solar"
				   data-track-section="hero">
					E3-Case lesen (<?php echo esc_html( $e3_lead_count ); ?> Anfragen, <?php echo esc_html( $e3_sales_conversion ); ?> Abschlussquote)
				</a>
			</div>
		</div>
	</section>

	<section class="hu-intercept__system" id="stufen" aria-labelledby="hu-funnel-stages-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-funnel-stages-title">Die 5 Stufen eines belastbaren Solar-/Wärmepumpen-Funnels</h2>
			<p class="hu-intercept__section-lead">
				Jede Stufe ist einzeln messbar. Gemeinsam ergeben sie die Strecke, auf der aus Suchwörtern Aufträge werden.
			</p>
			<ol class="hu-intercept__layers">
				<?php foreach ( $stages as $i => $stage ) : ?>
					<li class="hu-intercept__layer">
						<span class="hu-intercept__layer-index"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
						<div class="hu-intercept__layer-body">
							<h3 class="hu-intercept__layer-title"><?php echo esc_html( $stage['t'] ); ?></h3>
							<p class="hu-intercept__layer-text"><?php echo esc_html( $stage['s'] ); ?></p>
						</div>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
	</section>

	<section class="hu-intercept__why" id="fehler" aria-labelledby="hu-funnel-pitfalls-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-funnel-pitfalls-title">Die häufigsten Funnel-Fehler in der Energiebranche</h2>
			<div class="hu-intercept__grid hu-intercept__grid--four">
				<?php foreach ( $funnel_pitfalls as $item ) : ?>
					<article class="hu-intercept__card">
						<h3 class="hu-intercept__card-title"><?php echo esc_html( $item['t'] ); ?></h3>
						<p class="hu-intercept__card-text"><?php echo esc_html( $item['s'] ); ?></p>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="hu-intercept__why" id="vertiefung" aria-labelledby="hu-funnel-linked-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-funnel-linked-title">Vertiefende Bausteine</h2>
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

	<section class="hu-intercept__faq" id="faq" aria-labelledby="hu-funnel-faq-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-funnel-faq-title">Häufige Fragen zur Funnel-Architektur</h2>
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

	<section class="hu-intercept__final" id="final-cta" aria-labelledby="hu-funnel-final-title">
		<div class="hu-intercept__container hu-intercept__container--centered">
			<h2 class="hu-intercept__h2" id="hu-funnel-final-title">Funnel-Setup im Marktcheck einordnen</h2>
			<p class="hu-intercept__final-text">
				Manueller, tiefer Marktcheck statt Software-Einheitsbrei. Händische Analyse Ihrer Region innerhalb von 48 Stunden per E-Mail — mit klarer Aussage, an welcher Funnel-Stufe Ihre größte Verlustquelle liegt und welcher Hebel den schnellsten Effekt hat.
			</p>
			<div class="hu-intercept__cta">
				<a class="hu-intercept__cta-primary"
				   href="<?php echo esc_url( $marktcheck_url ); ?>"
				   data-track-action="cta_marktcheck"
				   data-track-category="lead_funnel_solar"
				   data-track-section="final">
					Marktcheck mit Fit-Entscheid starten
				</a>
				<a class="hu-intercept__cta-secondary"
				   href="<?php echo esc_url( $solar_money_url ); ?>"
				   data-track-action="cta_money_page"
				   data-track-category="lead_funnel_solar"
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
		'track_category' => 'lead_funnel_solar',
	]
);

get_footer();
