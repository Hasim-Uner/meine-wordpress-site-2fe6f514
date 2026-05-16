<?php
/**
 * Template Name: Solar Leads kaufen – Alternative (Intercept)
 * Description: Intercept-Landingpage für Suchintent "Solar Leads kaufen",
 *              "Photovoltaik Leads kaufen", "Wärmepumpen Leads kaufen".
 *              Argumentation: Portal-Leads vs. eigenes Anfrage-System (CPL-Senkung).
 *              Primärer Pfad: Marktcheck auf /solar-waermepumpen-leadgenerierung/#marktcheck.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ── URLs ───────────────────────────────────────────────────────
$page_url        = home_url( '/solar-leads-kaufen-alternative/' );
$solar_money_url = function_exists( 'nexus_get_energy_systems_url' )
	? nexus_get_energy_systems_url()
	: home_url( '/solar-waermepumpen-leadgenerierung/' );
$marktcheck_url  = trailingslashit( $solar_money_url ) . '#marktcheck';
$e3_url          = home_url( '/e3-new-energy/' );
$contact_url     = home_url( '/kontakt/' );

// ── E3-Proof-Canon ─────────────────────────────────────────────
$e3_canon            = function_exists( 'hu_e3_canon' ) ? hu_e3_canon() : [];
$e3_metrics          = isset( $e3_canon['metrics'] ) && is_array( $e3_canon['metrics'] ) ? $e3_canon['metrics'] : [];
$e3_case_label       = isset( $e3_canon['case_label'] ) ? (string) $e3_canon['case_label'] : 'E3 New Energy';
$e3_lead_count       = $e3_metrics['lead_count']['display'] ?? '1.750+';
$e3_sales_conversion = $e3_metrics['sales_conversion']['display'] ?? '12 %';
$e3_cpl_reduction    = $e3_metrics['cpl_reduction']['display'] ?? 'über 85 %';
$e3_cpl_before       = $e3_metrics['cpl_before']['display'] ?? '150 €';
$e3_cpl_after        = $e3_metrics['cpl_after']['display'] ?? '22 €';
$e3_timeframe        = $e3_metrics['timeframe']['display'] ?? '9 Monate';

// ── Inhalt: Portal vs. eigenes System (Problem → Lösung) ──────
$portal_facts = [
	[
		'k' => '60 – 120 €',
		'l' => 'CPL für „exklusive" PV-/WP-Leads im Portal-Markt',
	],
	[
		'k' => 'bis zu 5×',
		'l' => 'Mehrfachverkauf desselben Datensatzes an Wettbewerber',
	],
	[
		'k' => 'sinkende Quote',
		'l' => 'genervte Endkunden, fallende Conversion in der Telefonie',
	],
	[
		'k' => '0 % Asset',
		'l' => 'kein Code, kein Tracking, keine Daten verbleiben im Betrieb',
	],
];

$own_facts = [
	[
		'k' => $e3_cpl_after,
		'l' => 'Cost per Lead im eigenen Anfrage-System (E3-Referenz)',
	],
	[
		'k' => '100 %',
		'l' => 'exklusive Anfragen – kein Parallelversand an Wettbewerber',
	],
	[
		'k' => $e3_sales_conversion,
		'l' => 'Abschlussquote bei qualifizierten Anfragen (E3)',
	],
	[
		'k' => '100 % Asset',
		'l' => 'Code, Tracking und Daten bleiben im Betrieb',
	],
];

$why_portals_fail = [
	[
		't' => 'Mehrfachverkauf zerstört Margen',
		's' => 'Drei bis fünf Anbieter bieten gegen denselben Datensatz. Die Conversion sinkt, der Preisdruck steigt, der Vertrieb verbrennt Zeit.',
	],
	[
		't' => 'Keine Vorqualifizierung',
		's' => 'Portale verkaufen Klick-Interesse, kein Projektinteresse. Region, Dach, Heizart und Budget bleiben offen – Sie qualifizieren am Telefon nach.',
	],
	[
		't' => 'Daten gehören dem Portal',
		's' => 'Sie bezahlen pro Datensatz, ohne das System zu besitzen, das ihn erzeugt. Kündigung = Anfrage-Stopp.',
	],
	[
		't' => 'CPL steigt mit dem Markt',
		's' => 'Je mehr Anbieter sich um dieselbe Anzahl an Endkunden drängen, desto teurer der Datensatz – ohne Qualitätsgewinn.',
	],
];

$market_models = [
	[
		't' => 'Geteilte Datensätze',
		's' => 'Eine Endkundenanfrage wird parallel an 3–5 Solarteure verkauft. Marktbreit etablierte Mengenmodelle (z. B. Aroundhome, DAA, Wattfox in Teilen). Preis pro Datensatz niedriger, Wettbewerb höher.',
	],
	[
		't' => 'Exklusive Datensätze',
		's' => 'Anfrage wird nur an einen Anbieter weitergegeben. Selteneres Modell, höherer Preis pro Datensatz (oft 80 – 150 €). Faktor Geschwindigkeit weniger entscheidend, Abschlussquote tendenziell höher.',
	],
	[
		't' => 'Regional erzeugte Leads',
		's' => 'Anfragen werden über lokale Kampagnen oder Videowerbung im Namen des Fachbetriebs eingesammelt (z. B. Leadfluss-Modell). Markenwirkung bleibt beim Fachbetrieb, Volumen abhängig von der lokalen Kampagne.',
	],
	[
		't' => 'Eigene Anfrage-Infrastruktur',
		's' => 'Money Page, Server-Side-Tracking und Vorqualifizierung im eigenen Eigentum. Anfragen sind per Definition exklusiv. Setup einmalig, Asset im Betrieb. Vergleich: siehe E3-Referenz.',
	],
];

$buyer_check_criteria = [
	[
		'k' => 'Intent-Stärke',
		'l' => 'Was hat der Endkunde geklickt – ein Angebotsvergleich, eine Förder-Frage, eine konkrete Investitionsentscheidung? Je näher am Kauf, desto wertvoller die Anfrage.',
	],
	[
		'k' => 'Exklusivität',
		'l' => 'Wie viele Wettbewerber bekommen denselben Datensatz? Bei drei oder mehr ist die Anfrage faktisch ein Bieter-Wettlauf, kein Verkaufsgespräch.',
	],
	[
		'k' => 'Vorqualifizierung',
		'l' => 'Sind Region, Dachfläche, Heizart und Projektwert vor dem Anruf bekannt? Ohne diese Daten qualifizieren Sie selbst nach – und verlieren Zeit an Nicht-Passende.',
	],
	[
		'k' => 'Echtzeit-Übermittlung',
		'l' => 'Kommt die Anfrage innerhalb von Minuten nach Endkundenklick? Leads, die Stunden oder Tage alt sind, sind faktisch tot – der Endkunde hat längst andere Angebote.',
	],
];

$own_system_layers = [
	[
		't' => 'Money Page mit Vorqualifizierung',
		's' => 'Solar/Wärmepumpen-spezifisch, mit 60-Sekunden-Strecke. Region, Heizart, Projektwert – vor dem Anruf.',
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
		't' => 'Skalierung über eigene Kanäle',
		's' => 'Google Ads, Meta Ads und SEO laufen erst dann profitabel, wenn die Strecke darunter sauber misst und konvertiert.',
	],
];

$objections = [
	[
		'question' => 'Aber Portal-Leads liefern doch sofort Anfragen.',
		'answer'   => 'Sofort ja, aber teuer und mehrfach verkauft. Ein eigenes System braucht 6–12 Wochen Aufbau, liefert dann jedoch exklusive Anfragen zu deutlich niedrigeren Stückkosten. Wer dauerhaft skalieren will, kommt am eigenen System nicht vorbei.',
	],
	[
		'question' => 'Was kostet der Aufbau eines eigenen Anfrage-Systems?',
		'answer'   => 'Initiales Setup im B2B-Mittelstand: rund 12.000 – 18.000 € einmalig, laufend ca. 50 €/Monat Hochleistungs-Hosting. Über 24 Monate liegt die Gesamtkostenrechnung unter dem, was viele Anbieter heute für Portal-Leads ausgeben – nur dass am Ende ein Asset im Eigentum steht.',
	],
	[
		'question' => 'Wie lange dauert es, bis sich das rechnet?',
		'answer'   => sprintf( 'Bei E3 New Energy hat sich das System nach %1$s amortisiert: %2$s qualifizierte Anfragen, %3$s Abschlussquote und der CPL fiel von %4$s auf %5$s.', $e3_timeframe, $e3_lead_count, $e3_sales_conversion, $e3_cpl_before, $e3_cpl_after ),
	],
	[
		'question' => 'Verkaufen Sie selbst Leads?',
		'answer'   => 'Nein. Es werden keine Datensätze weiterverkauft. Aufgebaut wird die Infrastruktur, die in Ihrem Eigentum bleibt: Code, Tracking, CRM-Anbindung und Datenhoheit. Was Sie damit anfangen, bleibt Ihre Entscheidung.',
	],
	[
		'question' => 'Funktioniert das auch für Wärmepumpen und Speicher?',
		'answer'   => 'Ja. Die gleiche Architektur trägt Photovoltaik, Wärmepumpe und Speicher. Die Vorqualifizierung wird pro Produkt anders gewichtet (Heizart, Dachfläche, Bestandsanlage), die System-Logik bleibt identisch.',
	],
	[
		'question' => 'Wir nutzen schon DAA/Aroundhome/Check24. Was ist anders?',
		'answer'   => 'Sie mieten dort Nachfrage. Hier bauen Sie eigene Nachfrage. Drei Prüffragen: Wem gehört die Landingpage, das Tracking, das CRM? Wenn dreimal „dem Portal" steht – mieten Sie ein System, das morgen abgeschaltet werden kann.',
	],
];

// ── Schema.org: Service + FAQPage ─────────────────────────────
$service_schema = [
	'@context'    => 'https://schema.org',
	'@type'       => 'Service',
	'@id'         => trailingslashit( $page_url ) . '#service',
	'name'        => 'Eigenes Anfrage-System statt Portal-Leads für Solar, Wärmepumpe und Speicher',
	'serviceType' => 'Alternative zu Lead-Portalen: Aufbau eigener B2B-Anfrage-Infrastruktur',
	'url'         => $page_url,
	'description' => sprintf( 'Aufbau eines eigenen Anfrage-Systems für Solar-, Wärmepumpen- und Speicher-Anbieter im DACH-Raum. Referenz %1$s: %2$s niedrigere Cost per Lead in %3$s.', $e3_case_label, $e3_cpl_reduction, $e3_timeframe ),
	'provider'    => [
		'@type' => 'Person',
		'name'  => 'Haşim Üner',
		'url'   => home_url( '/' ),
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

<main id="primary" class="hu-intercept" role="main" data-track-page="solar-leads-kaufen-alternative">

	<section class="hu-intercept__hero" id="hero" aria-labelledby="hu-intercept-hero-title">
		<div class="hu-intercept__container">
			<p class="hu-intercept__eyebrow">Für Solar-, Wärmepumpen- und Speicher-Anbieter im DACH-Mittelstand</p>
			<h1 class="hu-intercept__title" id="hu-intercept-hero-title">
				Sie wollen Solar Leads kaufen? Eigene Anfragen senken den CPL um <?php echo esc_html( $e3_cpl_reduction ); ?>.
			</h1>
			<p class="hu-intercept__lead">
				Portal-Leads für Photovoltaik und Wärmepumpe kosten heute <strong>60 – 120 €</strong> – und werden bis zu fünfmal an Wettbewerber weiterverkauft. Bei <?php echo esc_html( $e3_case_label ); ?> hat ein eigenes Anfrage-System den Cost per Lead von <strong><?php echo esc_html( $e3_cpl_before ); ?></strong> auf <strong><?php echo esc_html( $e3_cpl_after ); ?></strong> in <strong><?php echo esc_html( $e3_timeframe ); ?></strong> gesenkt.
			</p>
			<div class="hu-intercept__cta">
				<a class="hu-intercept__cta-primary"
				   href="<?php echo esc_url( $marktcheck_url ); ?>"
				   data-track-action="cta_marktcheck"
				   data-track-category="intercept_solar_leads"
				   data-track-section="hero">
					60-Sekunden-Marktcheck starten
				</a>
				<a class="hu-intercept__cta-secondary"
				   href="<?php echo esc_url( $e3_url ); ?>"
				   data-track-action="cta_e3_case"
				   data-track-category="intercept_solar_leads"
				   data-track-section="hero">
					E3-Case lesen (<?php echo esc_html( $e3_lead_count ); ?> Anfragen, <?php echo esc_html( $e3_sales_conversion ); ?> Abschlussquote)
				</a>
			</div>
		</div>
	</section>

	<section class="hu-intercept__compare" id="vergleich" aria-labelledby="hu-intercept-compare-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-intercept-compare-title">Portal-Leads kaufen vs. eigenes Anfrage-System</h2>
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
			<h2 class="hu-intercept__h2" id="hu-intercept-why-title">Warum der Zukauf von Photovoltaik- und Wärmepumpen-Leads das Wachstum bremst</h2>
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
			<h2 class="hu-intercept__h2" id="hu-intercept-markt-title">Anbieter-Modelle im Lead-Markt: sachliche Markteinordnung</h2>
			<p class="hu-intercept__section-lead">
				Der deutsche Markt für Photovoltaik- und Wärmepumpen-Anfragen unterteilt sich in vier strukturell unterschiedliche Modelle. Eigene Markteinordnung – keine Empfehlung, keine Wertung.
			</p>
			<div class="hu-intercept__grid hu-intercept__grid--four">
				<?php foreach ( $market_models as $model ) : ?>
					<article class="hu-intercept__card">
						<h3 class="hu-intercept__card-title"><?php echo esc_html( $model['t'] ); ?></h3>
						<p class="hu-intercept__card-text"><?php echo esc_html( $model['s'] ); ?></p>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="hu-intercept__compare" id="qualitaet" aria-labelledby="hu-intercept-quality-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-intercept-quality-title">Vier Kriterien, an denen sich ein hochwertiger Lead erkennen lässt</h2>
			<p class="hu-intercept__section-lead">
				Unabhängig vom gewählten Modell – ob Portal, eigene Strecke oder regionale Kampagne – entscheiden vier Merkmale, ob eine Anfrage wirtschaftlich ist.
			</p>
			<div class="hu-intercept__panel hu-intercept__panel--positive">
				<ul class="hu-intercept__facts">
					<?php foreach ( $buyer_check_criteria as $crit ) : ?>
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
			<h2 class="hu-intercept__h2" id="hu-intercept-system-title">So sieht ein eigenes Anfrage-System für Solar und Wärmepumpe aus</h2>
			<p class="hu-intercept__section-lead">
				Vier Bausteine – jeder einzeln messbar, gemeinsam ergeben sie eine Strecke, die <strong>qualifizierte Photovoltaik-Anfragen</strong> und <strong>exklusive Wärmepumpen-Leads</strong> produziert, statt sie zu mieten.
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

	<section class="hu-intercept__faq" id="faq" aria-labelledby="hu-intercept-faq-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-intercept-faq-title">Häufige Fragen zur Alternative zum Lead-Kauf</h2>
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
			<h2 class="hu-intercept__h2" id="hu-intercept-final-title">Statt Leads zu kaufen: Marktcheck starten</h2>
			<p class="hu-intercept__final-text">
				Fünf Fragen, 60 Sekunden. Sie bekommen eine persönliche Ersteinschätzung, ob ein eigenes Anfrage-System für Ihren Betrieb wirtschaftlicher ist als der Weiterkauf von Portal-Leads – ohne Pflicht-Call, ohne Newsletter.
			</p>
			<div class="hu-intercept__cta">
				<a class="hu-intercept__cta-primary"
				   href="<?php echo esc_url( $marktcheck_url ); ?>"
				   data-track-action="cta_marktcheck"
				   data-track-category="intercept_solar_leads"
				   data-track-section="final">
					60-Sekunden-Marktcheck starten
				</a>
				<a class="hu-intercept__cta-secondary"
				   href="<?php echo esc_url( $solar_money_url ); ?>"
				   data-track-action="cta_money_page"
				   data-track-category="intercept_solar_leads"
				   data-track-section="final">
					Methode und System ansehen
				</a>
			</div>
		</div>
	</section>

	<script type="application/ld+json"><?php echo wp_json_encode( $service_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?></script>
	<script type="application/ld+json"><?php echo wp_json_encode( $faq_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?></script>
</main>

<?php
get_footer();
