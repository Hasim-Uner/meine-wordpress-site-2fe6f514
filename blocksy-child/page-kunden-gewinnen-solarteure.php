<?php
/**
 * Template Name: Kunden gewinnen für Solarteure – Pillar
 * Description: TOFU Pillar-Page für problem-getriebene Suchintent
 *              („Kunden gewinnen Photovoltaik", „Neukunden gewinnen Solar").
 *              Strikt B2B-qualifiziert, um Klein-/Einzelbetriebe nicht
 *              irrtümlich anzuziehen.
 *              Primärer CTA: Marktcheck.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$page_url        = home_url( '/kunden-gewinnen-solarteure/' );
$solar_money_url = function_exists( 'nexus_get_energy_systems_url' )
	? nexus_get_energy_systems_url()
	: home_url( '/solar-waermepumpen-leadgenerierung/' );
$marktcheck_url  = trailingslashit( $solar_money_url ) . '#marktcheck';
$e3_url          = home_url( '/e3-new-energy/' );
$intercept_url   = home_url( '/solar-leads-kaufen-alternative/' );
$vs_url          = home_url( '/eigene-leadgenerierung-vs-portale/' );

$e3_canon            = function_exists( 'hu_e3_canon' ) ? hu_e3_canon() : [];
$e3_metrics          = isset( $e3_canon['metrics'] ) && is_array( $e3_canon['metrics'] ) ? $e3_canon['metrics'] : [];
$e3_case_label       = isset( $e3_canon['case_label'] ) ? (string) $e3_canon['case_label'] : 'E3 New Energy';
$e3_cpl_reduction    = $e3_metrics['cpl_reduction']['display'] ?? 'über 85 %';
$e3_lead_count       = $e3_metrics['lead_count']['display'] ?? '1.750+';
$e3_sales_conversion = $e3_metrics['sales_conversion']['display'] ?? '12 %';
$e3_timeframe        = $e3_metrics['timeframe']['display'] ?? '6 Monate';

$myths = [
	[
		't' => 'Mythos: „Mehr Werbebudget bringt mehr Kunden"',
		's' => 'Mehr Budget auf unsaubere Funnel verstärkt nur die bestehenden Lecks. Erst Funnel und Tracking sauber, dann Budget skalieren.',
	],
	[
		't' => 'Mythos: „Social Media bringt schnell Anfragen"',
		's' => 'Im B2B-Solar-Mittelstand ist Social Media ein Trust-Layer, kein Pipeline-Generator. Die Aufträge kommen aus organischer Suche, Google Ads und Empfehlungen.',
	],
	[
		't' => 'Mythos: „Lead-Portale sparen Marketing-Kosten"',
		's' => 'Portal-Leads sind teuer und mehrfach verkauft. Über 24 Monate liegt die TCO oft deutlich höher als der Aufbau eines eigenen Systems.',
	],
	[
		't' => 'Mythos: „Eine moderne Website reicht"',
		's' => 'Eine Website ohne Vorqualifizierung, ohne Tracking und ohne CRM-Anbindung ist eine digitale Visitenkarte – kein Anfrage-System.',
	],
];

$systematic_levers = [
	[
		't' => 'Eigene Money-Page',
		's' => 'Eine spezifische Solar- oder Wärmepumpen-Seite mit Vorqualifizierung, Proof, FAQ – nicht eine generische Startseite.',
	],
	[
		't' => 'Vorqualifizierung im Funnel',
		's' => 'Region, Heizart, Dach, Projektwert werden abgefragt, bevor Kontaktdaten verlangt werden. Wer das nicht durchläuft, ist kein passender Kunde.',
	],
	[
		't' => 'Server-Side Tracking',
		's' => 'Saubere Attribution trotz Cookieless und Ad-Blockern. Reports zeigen, welcher Kanal welchen Auftrag produziert.',
	],
	[
		't' => 'CRM-Anschluss & Reaktionszeit',
		's' => 'Anfragen landen sortiert im CRM. Die Reaktionszeit (oft < 24 h) ist der häufigste Hebel zwischen Anfrage und Auftrag.',
	],
	[
		't' => 'Wiederkehrender Reporting-Rhythmus',
		's' => 'Wöchentliches Reporting zwingt Marketing und Vertrieb in eine gemeinsame Sprache. Ohne Rhythmus stagniert das System.',
	],
];

$fit_yes = [
	[ 't' => 'Solarteur mit eigenem Vertrieb', 's' => 'Mindestens 1 – 2 Personen im aktiven Vertrieb, nicht reine Vermittlung.' ],
	[ 't' => 'Klarer Zielraum', 's' => 'Bundesland, Region oder mehrere PLZ-Cluster – kein „bundesweit, alles".' ],
	[ 't' => 'Projektwert ab 15.000 €', 's' => 'B2C ab ~15 k €, B2B ab ~50 k € pro Projekt – sonst rechnet sich der Aufbau nicht.' ],
	[ 't' => '24-Monate-Horizont', 's' => 'Bereit, Infrastruktur aufzubauen statt nur Datensätze einzukaufen.' ],
];

$fit_no = [
	[ 't' => 'Einzelbetrieb ohne Vertriebskapazität', 's' => 'Wenn die Geschäftsführung selbst auf der Baustelle steht, fehlt Zeit für Anfrage-Bearbeitung.' ],
	[ 't' => '„Nächste Woche brauchen wir Aufträge."', 's' => 'Pipelines wachsen über Monate, nicht Tage. Wer akut Aufträge braucht, ist im Portal-Markt richtiger.' ],
	[ 't' => 'Kein Tracking, keine Daten, kein CRM', 's' => 'Ohne Datenbasis fehlen die Grundlagen, auf denen ein System aufsetzt.' ],
];

$linked_assets = [
	[ 't' => 'Lead-Funnel Solar (Pillar)', 's' => 'Die 5 Funnel-Stufen und die häufigsten Fehler.', 'url' => home_url( '/lead-funnel-solar/' ) ],
	[ 't' => 'TCO über 24 Monate: Portal-Leads vs. eigenes System', 's' => 'Strategische Vergleichsmatrix mit Asset-Eigentum-Logik.', 'url' => $vs_url ],
	[ 't' => 'Solar Leads kaufen? CPL-Rechnung pro Anfrage', 's' => 'Warum Portal-Leads das Wachstum bremsen — Kosten pro Anfrage im Detail.', 'url' => $intercept_url ],
	[ 't' => 'E3-Methodik-Case', 's' => sprintf( '%1$s Anfragen, %2$s Abschlussquote.', $e3_lead_count, $e3_sales_conversion ), 'url' => $e3_url ],
];

$faq = [
	[
		'question' => 'Funktioniert das auch für kleinere Solar-Betriebe?',
		'answer'   => 'Der Aufbau eines eigenen Anfrage-Systems lohnt sich erfahrungsgemäß ab einem monatlichen Marketing-Budget von rund 1.500 € und einem Mindest-Projektwert von 15.000 €. Für reine Einzelbetriebe ohne Vertriebskapazität ist der Hebel zu klein.',
	],
	[
		'question' => 'Wie unterscheidet sich das von Social Media Marketing?',
		'answer'   => 'Social Media ist im B2B-Solar-Mittelstand ein Trust-Layer (LinkedIn, fachliche Sichtbarkeit), aber kein Pipeline-Generator. Die wirtschaftlich relevanten Anfragen kommen aus organischer Suche, gezielten Google Ads und Empfehlungen – nicht aus Facebook- oder Instagram-Posts.',
	],
	[
		'question' => 'Wie viele neue Kunden sind realistisch pro Monat?',
		'answer'   => 'Das hängt von Region, Projektwert und Ad-Budget ab. Im DACH-Mittelstand sind 8–25 zusätzliche qualifizierte Anfragen pro Monat realistisch, bei Abschlussquoten von 8–15 % – siehe E3-Referenz mit 12 % Abschlussquote.',
	],
	[
		'question' => 'Brauche ich dafür eine neue Website?',
		'answer'   => 'Meistens nicht im Komplettumfang. Was nötig ist: hardcoded WordPress oder vergleichbares System ohne Page-Builder-Last, Server-Side-Tracking, Conversion-Pfad ohne Mietsysteme. Ob Teil-Umbau oder Erstaufbau, zeigt sich im ersten Schritt.',
	],
	[
		'question' => 'Was kostet das System für einen mittelständischen Solar-Betrieb?',
		'answer'   => 'Initiales Setup: 12.000 – 18.000 € einmalig. Laufend ca. 50 €/Monat Hochleistungs-Hosting. Über 24 Monate liegt die TCO meist unter dem, was viele Betriebe heute für Portal-Leads ausgeben – und das Asset bleibt im Eigentum.',
	],
];

$author_person     = function_exists( 'hu_get_canonical_author_person' ) ? hu_get_canonical_author_person() : [ '@type' => 'Person', 'name' => 'Haşim Üner', 'url' => home_url( '/' ) ];
$breadcrumb_schema = function_exists( 'hu_get_solar_subpage_breadcrumb_schema' )
	? hu_get_solar_subpage_breadcrumb_schema( $page_url, 'Kunden gewinnen für Solarteure' )
	: [];

$service_schema = [
	'@context'    => 'https://schema.org',
	'@type'       => 'Service',
	'@id'         => trailingslashit( $page_url ) . '#service',
	'name'        => 'Kunden gewinnen für Solarteure ohne Portal-Leads',
	'serviceType' => 'Systematische Kundenakquise für Photovoltaik-, Wärmepumpen- und Speicher-Anbieter',
	'url'         => $page_url,
	'description' => sprintf( 'Anfrage-System für Solarteure im DACH-Mittelstand. Referenz %1$s: %2$s niedrigere Cost per Lead in %3$s.', $e3_case_label, $e3_cpl_reduction, $e3_timeframe ),
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

<main id="primary" class="hu-intercept" role="main" data-track-page="kunden-gewinnen-solarteure">

	<section class="hu-intercept__hero" id="hero" aria-labelledby="hu-kg-hero-title">
		<div class="hu-intercept__container">
			<p class="hu-intercept__eyebrow">Für Solar-, Wärmepumpen- und Speicher-Anbieter im DACH-Mittelstand</p>
			<h1 class="hu-intercept__title" id="hu-kg-hero-title">
				Kunden gewinnen für Solarteure – ohne DAA, Aroundhome oder Check24
			</h1>
			<p class="hu-intercept__lead">
				Der Photovoltaik-Markt normalisiert sich. Wer 2026 systematisch wachsen will, braucht keine teureren Portal-Leads – sondern ein eigenes Anfrage-System, das qualifizierte Anfragen produziert und im Betrieb bleibt.
			</p>
			<?php get_template_part( 'template-parts/seo-subpage-byline', null, [ 'template_path' => __FILE__ ] ); ?>
			<div class="hu-intercept__cta">
				<a class="hu-intercept__cta-primary"
				   href="<?php echo esc_url( $marktcheck_url ); ?>"
				   data-track-action="cta_marktcheck"
				   data-track-category="kunden_gewinnen_solarteure"
				   data-track-section="hero">
					Kostenfreien Marktcheck starten
				</a>
				<a class="hu-intercept__cta-secondary"
				   href="<?php echo esc_url( $e3_url ); ?>"
				   data-track-action="cta_e3_case"
				   data-track-category="kunden_gewinnen_solarteure"
				   data-track-section="hero">
					E3-Case lesen (<?php echo esc_html( $e3_lead_count ); ?> Anfragen, <?php echo esc_html( $e3_sales_conversion ); ?> Abschlussquote)
				</a>
			</div>
		</div>
	</section>

	<section class="hu-intercept__why" id="mythen" aria-labelledby="hu-kg-myths-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-kg-myths-title">Vier Mythen, die der Solar-Branche teuer zu stehen kommen</h2>
			<div class="hu-intercept__grid hu-intercept__grid--four">
				<?php foreach ( $myths as $item ) : ?>
					<article class="hu-intercept__card">
						<h3 class="hu-intercept__card-title"><?php echo esc_html( $item['t'] ); ?></h3>
						<p class="hu-intercept__card-text"><?php echo esc_html( $item['s'] ); ?></p>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="hu-intercept__system" id="hebel" aria-labelledby="hu-kg-levers-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-kg-levers-title">Die 5 Hebel der systematischen Kundengewinnung</h2>
			<p class="hu-intercept__section-lead">
				Statt einzelner Taktiken ein zusammenhängendes System. Jeder Hebel ist messbar, jeder zahlt auf den nächsten ein.
			</p>
			<ol class="hu-intercept__layers">
				<?php foreach ( $systematic_levers as $i => $lever ) : ?>
					<li class="hu-intercept__layer">
						<span class="hu-intercept__layer-index"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
						<div class="hu-intercept__layer-body">
							<h3 class="hu-intercept__layer-title"><?php echo esc_html( $lever['t'] ); ?></h3>
							<p class="hu-intercept__layer-text"><?php echo esc_html( $lever['s'] ); ?></p>
						</div>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
	</section>

	<section class="hu-intercept__compare" id="fit" aria-labelledby="hu-kg-fit-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-kg-fit-title">Für welche Solar-Betriebe der Ansatz passt</h2>
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

	<section class="hu-intercept__why" id="vertiefung" aria-labelledby="hu-kg-linked-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-kg-linked-title">Vertiefende Ressourcen</h2>
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

	<section class="hu-intercept__faq" id="faq" aria-labelledby="hu-kg-faq-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-kg-faq-title">Häufige Fragen zur Kundengewinnung in der Solar-Branche</h2>
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

	<section class="hu-intercept__final" id="final-cta" aria-labelledby="hu-kg-final-title">
		<div class="hu-intercept__container hu-intercept__container--centered">
			<h2 class="hu-intercept__h2" id="hu-kg-final-title">Den passenden Hebel im Marktcheck identifizieren</h2>
			<p class="hu-intercept__final-text">
				Manueller, tiefer Marktcheck statt Software-Einheitsbrei. Händische Analyse Ihrer Region innerhalb von 48 Stunden per E-Mail — mit klarer Aussage, welcher der 5 Hebel für Ihren Betrieb den schnellsten Effekt hat. Ohne Pflicht-Call, ohne Newsletter.
			</p>
			<div class="hu-intercept__cta">
				<a class="hu-intercept__cta-primary"
				   href="<?php echo esc_url( $marktcheck_url ); ?>"
				   data-track-action="cta_marktcheck"
				   data-track-category="kunden_gewinnen_solarteure"
				   data-track-section="final">
					Kostenfreien Marktcheck starten
				</a>
				<a class="hu-intercept__cta-secondary"
				   href="<?php echo esc_url( $solar_money_url ); ?>"
				   data-track-action="cta_money_page"
				   data-track-category="kunden_gewinnen_solarteure"
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
		'track_category' => 'kunden_gewinnen_solarteure',
	]
);

get_footer();
