<?php
/**
 * /solar-waermepumpen-leadgenerierung/
 *
 * Ingenieur-Ästhetik. Kein Dekor. Nur Argument.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$request_url = function_exists( 'hu_get_request_analysis_url' ) ? hu_get_request_analysis_url() : home_url( '/anfrage-system-analyse/' );
$page_url    = function_exists( 'nexus_get_energy_systems_url' ) ? nexus_get_energy_systems_url() : home_url( '/solar-waermepumpen-leadgenerierung/' );
$e3_url      = home_url( '/e3-new-energy/' );
$e3_canon    = function_exists( 'hu_e3_canon' ) ? hu_e3_canon() : [];
$e3_metrics  = isset( $e3_canon['metrics'] ) && is_array( $e3_canon['metrics'] ) ? $e3_canon['metrics'] : [];

$e3_case_label       = isset( $e3_canon['case_label'] ) ? (string) $e3_canon['case_label'] : 'E3 New Energy';
$e3_lead_count       = $e3_metrics['lead_count']['display'] ?? '1.750+';
$e3_sales_conversion = $e3_metrics['sales_conversion']['display'] ?? '12 %';
$e3_cpl_reduction    = $e3_metrics['cpl_reduction']['display'] ?? '-85,3 %';
$e3_cpl_before       = $e3_metrics['cpl_before']['display'] ?? '150 €';
$e3_cpl_after        = $e3_metrics['cpl_after']['display'] ?? '22 €';
$e3_timeframe        = $e3_metrics['timeframe']['display'] ?? '9 Monate';
$e3_timeframe_dative = $e3_metrics['timeframe']['display_dative'] ?? '9 Monaten';

$cost_rows = [
	[ 'label' => 'Durchschnittspreis pro Lead (Portal)', 'value' => '80–150 €' ],
	[ 'label' => 'Davon ans Telefon bekommen', 'value' => '~50 %' ],
	[ 'label' => 'Zeitaufwand Vertrieb pro Lead (inkl. Nachfassen)', 'value' => '~45 min' ],
	[ 'label' => 'Storno-Quote (falsche Erwartung, kein Budget)', 'value' => '20–30 %' ],
	[ 'label' => 'Versteckte Monatskosten bei 30 Leads', 'value' => '~4.800 €' ],
];

$system_stages = [
	[ 'num' => '01', 'title' => 'Landingpage', 'text' => 'Besucher qualifiziert sich selbst in 60 Sekunden. Kein langes Formular. Keine Pflichtfelder.' ],
	[ 'num' => '02', 'title' => 'Segmentierung', 'text' => 'n8n routet den Lead nach Region, Gewerk und Dringlichkeit in den richtigen CRM-Topf.' ],
	[ 'num' => '03', 'title' => 'Benachrichtigung', 'text' => 'Vertrieb sieht den Lead in Echtzeit — mit allen relevanten Daten aus der Qualifizierung.' ],
	[ 'num' => '04', 'title' => 'Bewertung', 'text' => 'Scoring priorisiert: heiß, warm, kalt. Sie rufen zuerst den an, der heute kaufen will.' ],
	[ 'num' => '05', 'title' => 'Übergabe', 'text' => 'CRM-ready. Keine Daten gehen verloren. Kein Lock-in. Alles in Ihrem Eigentum.' ],
];

$faq_items = [
	[
		'question' => 'Was kostet der Aufbau?',
		'answer'   => '12.000–18.000 € einmalig. Laufend ca. 50 €/Monat für Hosting. TCO über 24 Monate: 13.200–19.200 €. Eine Performance-Agentur mit Paket „Regio+" kostet im gleichen Zeitraum rund 26.000 € — und Sie besitzen am Ende nichts.',
	],
	[
		'question' => 'Funktioniert das auch für kleinere Betriebe?',
		'answer'   => 'Belastbar ab 10 Mitarbeitern und mindestens 20 qualifizierten Anfragen pro Monat. Darunter trägt die Investition den TCO-Vorteil nicht.',
	],
	[
		'question' => 'Warum nicht einfach mehr Google Ads schalten?',
		'answer'   => 'Mehr Budget auf eine Seite ohne Qualifizierung und Tracking heißt: mehr Geld für dieselben unqualifizierten Anfragen. Erst wenn Seite, Funnel und serverseitiges Tracking sauber arbeiten, lohnt sich mehr Reichweite.',
	],
	[
		'question' => 'Brauchen wir eine komplett neue Website?',
		'answer'   => 'Meistens nicht. Was Sie brauchen: hardcoded WordPress, serverseitiges Tracking, Conversion-Pfad ohne Mietsysteme. Ob das ein Teil-Umbau oder ein sauberer Erstaufbau wird, zeigt der erste Schritt.',
	],
	[
		'question' => 'Wir haben schon eine Agentur. Warum wechseln?',
		'answer'   => 'Drei Prüffragen: Wem gehört der Code Ihrer Landingpage? Wem gehört das CRM mit Ihren Leads? Wem gehört der Tracking-Account? Wenn die Antwort dreimal „der Agentur" ist, mieten Sie ein System — Sie besitzen es nicht.',
	],
	[
		'question' => 'Wie schnell sieht man Ergebnisse?',
		'answer'   => 'Erste Verbesserungen oft nach den ersten Optimierungen an Tracking und Conversion-Pfaden. Belastbare Skalierung braucht mehrere Wochen bis Monate — weil Daten, Tests und Kanäle zusammenspielen müssen.',
	],
];

$service_schema = [
	'@context'    => 'https://schema.org',
	'@type'       => 'Service',
	'@id'         => trailingslashit( $page_url ) . '#service',
	'name'        => 'Aufbau eigener Anfrage-Systeme für Solar- und Wärmepumpen-Anbieter',
	'serviceType' => 'System-Architektur für eigene Lead-Infrastruktur: WordPress, Tracking, Qualifizierung, CRM-Übergabe',
	'url'         => $page_url,
	'description' => sprintf( 'Eigenes Anfrage-System für Solar- und Wärmepumpen-Betriebe. Referenz %1$s: %2$s weniger Kosten pro Anfrage in %3$s.', $e3_case_label, $e3_cpl_reduction, $e3_timeframe_dative ),
	'provider'    => [
		'@type' => 'Person',
		'name'  => 'Haşim Üner',
		'url'   => home_url( '/' ),
	],
	'audience'    => [
		'@type'        => 'Audience',
		'audienceType' => 'Solar-, Wärmepumpen-, Speicher- und Energie-Anbieter im DACH-Raum',
	],
	'areaServed'  => [
		[ '@type' => 'Country', 'name' => 'Deutschland' ],
	],
	'offers'      => [
		'@type'         => 'Offer',
		'price'         => '0',
		'priceCurrency' => 'EUR',
		'description'   => 'Diagnostische System-Einordnung. Keine E-Mail-Pflicht im Default-Pfad.',
	],
];

$faq_schema = [
	'@context'   => 'https://schema.org',
	'@type'      => 'FAQPage',
	'@id'        => trailingslashit( $page_url ) . '#faq',
	'url'        => trailingslashit( $page_url ) . '#faq',
	'mainEntity' => [],
];

foreach ( $faq_items as $faq_item ) {
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
<main id="main" class="site-main">
	<div class="energy-page-wrapper" data-track-section="energy_service_landing">

		<section class="nx-section energy-hero" id="hero">
			<div class="nx-container">
				<h1 class="energy-hero__title">Ihre Website kann mehr als eine Broschüre. Sie kann Anfragen produzieren.</h1>
				<p class="energy-hero__subtitle">Ich baue Solar- und Wärmepumpen-Betrieben ein eigenes Anfrage-System. Kein Portal. Keine gemieteten Leads. Keine Agentur-Abhängigkeit. Eine Infrastruktur, die Ihnen gehört und die Ihre Vertriebskosten senkt — messbar.</p>
				<div class="energy-hero__actions">
					<a href="<?php echo esc_url( $request_url ); ?>" class="nx-btn nx-btn--primary" data-track-action="cta_energy_hero" data-track-category="lead_gen">System prüfen</a>
				</div>
				<p class="energy-hero__micro">8 Schritte. Lokales Ergebnis. Keine E-Mail im Default-Pfad.</p>
			</div>
		</section>

		<section class="nx-section energy-section" id="kosten" aria-labelledby="kosten-title">
			<div class="nx-container">
				<h2 id="kosten-title" class="energy-section__title">Was Portal-Leads wirklich kosten.</h2>
				<p class="energy-section__lead">Die Rechnung, die Ihnen kein Portal zeigt.</p>

				<table class="energy-table" aria-describedby="kosten-title">
					<caption class="screen-reader-text">Kostenaufstellung Portal-Leads pro Monat bei 30 Leads.</caption>
					<tbody>
						<?php foreach ( $cost_rows as $row ) : ?>
							<tr>
								<th scope="row"><?php echo esc_html( $row['label'] ); ?></th>
								<td><?php echo esc_html( $row['value'] ); ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>

				<p class="energy-section__note">Die 4.800 € sind kein theoretischer Wert. Das ist der blinde Posten, den Betriebe monatlich in ineffiziente Vertriebszeit und verpasste Abschlüsse stecken — zusätzlich zu den Lead-Kosten.</p>
			</div>
		</section>

		<section class="nx-section energy-section" id="system" aria-labelledby="system-title">
			<div class="nx-container">
				<h2 id="system-title" class="energy-section__title">Was ein eigenes Anfrage-System leistet.</h2>
				<p class="energy-section__lead">Fünf Stufen. Keine Magie. Nur Technik, die funktioniert.</p>

				<ol class="energy-system-list">
					<?php foreach ( $system_stages as $stage ) : ?>
						<li class="energy-system-item">
							<span class="energy-system-item__num"><?php echo esc_html( $stage['num'] ); ?></span>
							<div>
								<strong><?php echo esc_html( $stage['title'] ); ?></strong>
								<span><?php echo esc_html( $stage['text'] ); ?></span>
							</div>
						</li>
					<?php endforeach; ?>
				</ol>
			</div>
		</section>

		<section class="nx-section energy-section" id="proof" aria-labelledby="proof-title">
			<div class="nx-container">
				<h2 id="proof-title" class="energy-section__title">Das ist kein Konzept. Das lief bereits.</h2>

				<div class="energy-proof-strip">
					<div class="energy-proof-stat">
						<strong><?php echo esc_html( $e3_lead_count ); ?></strong>
						<span>qualifizierte Anfragen</span>
					</div>
					<div class="energy-proof-stat">
						<strong><?php echo esc_html( $e3_cpl_reduction ); ?></strong>
						<span>Kosten pro Anfrage</span>
					</div>
					<div class="energy-proof-stat">
						<strong><?php echo esc_html( $e3_sales_conversion ); ?></strong>
						<span>Abschlussquote</span>
					</div>
				</div>

				<div class="energy-proof-body">
					<p><strong>Vorher:</strong> <?php echo esc_html( $e3_case_label ); ?> kaufte Leads für ø <?php echo esc_html( $e3_cpl_before ); ?>. Die Hälfte war nicht erreichbar. Kein Überblick, welcher Kanal Abschlüsse brachte.</p>
					<p><strong>Nach <?php echo esc_html( $e3_timeframe_dative ); ?>:</strong> Tracking-Fundament aufgebaut. Anfragepfade optimiert. Vorqualifizierung eingeführt. Ergebnis: <?php echo esc_html( sprintf( '%1$s Anfragen, %2$s Abschlussquote, Kosten auf %3$s gesenkt.', $e3_lead_count, $e3_sales_conversion, $e3_cpl_after ) ); ?></p>

					<blockquote class="energy-proof-quote">
						„Seit dem System sehen wir endlich, welche Kanäle wirklich Anfragen und Abschlüsse bringen. Wir konnten den teuren Lead-Einkauf massiv reduzieren und haben jetzt eine eigene Pipeline."
					</blockquote>

					<a href="<?php echo esc_url( $e3_url ); ?>" class="energy-text-link" data-track-action="cta_energy_proof" data-track-category="trust">Vollständige Case Study →</a>
				</div>
			</div>
		</section>

		<section class="nx-section energy-section" id="faq" aria-labelledby="faq-title">
			<div class="nx-container">
				<h2 id="faq-title" class="energy-section__title">Fragen, die vor dem ersten Schritt kommen.</h2>

				<div class="energy-faq">
					<?php foreach ( $faq_items as $index => $faq_item ) : ?>
						<details class="energy-faq__item"<?php echo 0 === $index ? ' open' : ''; ?>>
							<summary><?php echo esc_html( $faq_item['question'] ); ?></summary>
							<div class="energy-faq__content"><?php echo esc_html( $faq_item['answer'] ); ?></div>
						</details>
					<?php endforeach; ?>
				</div>
			</div>
		</section>

		<section class="nx-section energy-section" id="abschluss">
			<div class="nx-container">
				<div class="energy-cta-box">
					<h2>Passt das für Ihren Betrieb?</h2>
					<p>Die Anfrage-System-Analyse gibt Ihnen eine lokale Ampel-Einordnung mit Begründung und nächstem Schritt. Bei Nicht-Eignung bremst der Funnel — statt Sie in ein generisches Gespräch zu drücken.</p>
					<div class="energy-cta-box__actions">
						<a href="<?php echo esc_url( $request_url ); ?>" class="nx-btn nx-btn--primary" data-track-action="cta_energy_footer" data-track-category="lead_gen">Analyse starten</a>
					</div>
					<p class="energy-cta-box__micro">Lokale Fit-Einordnung. Keine personenbezogenen Daten im Default-Pfad. Begrenzte Slots pro Quartal.</p>
				</div>
			</div>
		</section>

	</div>
</main>

<script type="application/ld+json"><?php echo wp_json_encode( $service_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?></script>
<script type="application/ld+json"><?php echo wp_json_encode( $faq_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?></script>

<?php get_footer(); ?>
