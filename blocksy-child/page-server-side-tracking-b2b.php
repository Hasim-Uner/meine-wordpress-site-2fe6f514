<?php
/**
 * Template Name: Server-Side Tracking für B2B-Leadgenerierung
 * Description: Tech-Feature-Page für GA4, Meta CAPI, Consent Mode v2 auf eigenem
 *              Server. Argumentation: Cookieless Future, ITP, Ad-Blocker.
 *              Primärer CTA: Marktcheck auf /solar-waermepumpen-leadgenerierung/#marktcheck.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ── URLs ───────────────────────────────────────────────────────
$page_url        = home_url( '/server-side-tracking-b2b/' );
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
$problem_facts = [
	[
		'k' => '40 – 60 %',
		'l' => 'Datenverlust durch Ad-Blocker, ITP und Cookie-Restriktionen bei klassischem Client-Tracking',
	],
	[
		'k' => 'Cookieless',
		'l' => 'Drittanbieter-Cookies werden in den führenden Browsern ausgephast – Standard-Pixel verlieren Reichweite',
	],
	[
		'k' => 'falsche CPLs',
		'l' => 'Marketing-Reports zeigen verzerrte Kanal-Performance; Skalierung läuft in die falschen Kanäle',
	],
	[
		'k' => 'Black-Box',
		'l' => 'Agenturen bauen Tracking in ihren Konten – bei Kündigung bricht die Datenebene weg',
	],
];

$solution_layers = [
	[
		't' => 'Eigener Server in Frankfurt',
		's' => 'Server-Side-GTM-Container läuft auf eigenem Hosting in Deutschland. Keine Daten verlassen den vom Betrieb kontrollierten Server-Perimeter.',
	],
	[
		't' => 'GA4 + Meta Conversion API',
		's' => 'GA4 Measurement Protocol und Meta CAPI laufen serverseitig. Conversions kommen auch dann an, wenn der Browser blockt – sauber attribuiert auf Quelle, Kampagne und Anzeigengruppe.',
	],
	[
		't' => 'Consent Mode v2',
		's' => 'Google Consent Mode v2 ist eingebaut. Bei abgelehntem Consent werden pseudonymisierte Cookieless-Pings gesendet; bei Zustimmung läuft volles Tracking – DSGVO-konform.',
	],
	[
		't' => 'CRM-Anbindung',
		's' => 'Vorqualifizierte Anfragen laufen direkt vom Server ins CRM (HubSpot, Brevo, Pipedrive) inklusive Lead-Score, Quelle und Kampagne. Keine manuelle Excel-Übergabe.',
	],
	[
		't' => 'Custom Events & Lead-Scoring',
		's' => 'Eigene Events für Mikro-Conversions: Funnel-Step, Vorqualifizierungs-Antworten, Scroll-Tiefe. Lead-Score läuft in den CRM-Status, nicht nur in das Analytics-Konto.',
	],
];

$tech_stack = [
	[
		't' => 'GTM Server-Side Container',
		's' => 'Eigener sGTM-Container auf Frankfurt-Server. Alle Pixel-Calls laufen über die First-Party-Domain.',
	],
	[
		't' => 'GA4 Measurement Protocol',
		's' => 'Events kommen serverseitig in GA4 an – auch dann, wenn der Client durch Ad-Blocker geblockt wird.',
	],
	[
		't' => 'Meta Conversions API (CAPI)',
		's' => 'Lead-, Purchase- und Custom-Conversions laufen via CAPI in den Werbekontenmanager – vollständig und de-duplicated.',
	],
	[
		't' => 'Consent Mode v2',
		's' => 'DSGVO-konform: bei Zustimmung volles Tracking, bei Ablehnung pseudonymisierte Cookieless-Pings.',
	],
	[
		't' => 'CRM-Webhooks',
		's' => 'Direkter Webhook vom Server ins CRM. Keine manuellen Zapier-Brücken oder verlorene Anfragen.',
	],
	[
		't' => 'Reporting-Layer',
		's' => 'Wöchentliche Reports zeigen, welcher Kanal welche Abschluss-Aufträge produziert hat – nicht nur Klicks.',
	],
];

$faq = [
	[
		'question' => 'Was bringt Server-Side Tracking gegenüber Standard-GTM?',
		'answer'   => 'Standard-Client-Tracking verliert je nach Branche und Zielgruppe 30–60 % der Conversions an Ad-Blocker, ITP, Consent-Ablehnung und Browser-Restriktionen. Server-Side Tracking läuft als First-Party-Request über Ihre eigene Domain – Conversions kommen vollständig an, die Attribution wird sauber, und Skalierungs-Entscheidungen werden datenbasiert statt geraten.',
	],
	[
		'question' => 'Ist Server-Side Tracking DSGVO-konform?',
		'answer'   => 'Ja, bei richtiger Implementierung sogar deutlich konformer als reines Client-Tracking. Daten laufen über einen Server in der EU (hier: Frankfurt), Personal Identifiable Information wird vor der Weitergabe an Google/Meta gehashed, Consent Mode v2 unterscheidet zwischen vollem Tracking und Cookieless-Pings je nach Nutzerentscheidung.',
	],
	[
		'question' => 'Was kostet ein Server-Side-Tracking-Setup?',
		'answer'   => 'Ein vollständiges Setup mit eigenem sGTM-Container, GA4, Meta CAPI, Consent Mode v2 und CRM-Anbindung liegt im B2B-Mittelstand bei 4.000 – 7.000 € einmalig, plus rund 50 €/Monat Server-Hosting. Als Modul innerhalb eines kompletten Anfrage-Systems ist es preislich integriert.',
	],
	[
		'question' => 'Funktioniert das auch ohne eigenes Hosting?',
		'answer'   => 'Technisch ja, strategisch nein. Wenn der sGTM-Container bei der Agentur läuft, gehört die Datenebene wieder der Agentur – das ist genau das Problem, das Server-Side eigentlich lösen soll. Der Server gehört zum Anfrage-System und damit zum Betrieb.',
	],
	[
		'question' => 'Welche CRMs werden unterstützt?',
		'answer'   => 'Standardmäßig HubSpot, Brevo, Pipedrive und das hauseigene Nexus-CRM. Andere CRMs mit REST-API oder Webhook-Endpunkt lassen sich integrieren – das ist Teil der Integrationsphase.',
	],
];

// ── Schema.org: Service + FAQPage + BreadcrumbList ───────────
$author_person     = function_exists( 'hu_get_canonical_author_person' ) ? hu_get_canonical_author_person() : [ '@type' => 'Person', 'name' => 'Haşim Üner', 'url' => home_url( '/' ) ];
$breadcrumb_schema = function_exists( 'hu_get_solar_subpage_breadcrumb_schema' )
	? hu_get_solar_subpage_breadcrumb_schema( $page_url, 'Server-Side Tracking für B2B' )
	: [];

$service_schema = [
	'@context'    => 'https://schema.org',
	'@type'       => 'Service',
	'@id'         => trailingslashit( $page_url ) . '#service',
	'name'        => 'Server-Side Tracking für B2B-Leadgenerierung',
	'serviceType' => 'Tracking-Architektur: GA4, Meta CAPI, Consent Mode v2 auf eigenem Server',
	'url'         => $page_url,
	'description' => sprintf( 'Aufbau Server-Side-Tracking für B2B-Anfrage-Systeme in Solar, Wärmepumpe und SHK. Referenz %1$s: %2$s niedrigere Cost per Lead in %3$s.', $e3_case_label, $e3_cpl_reduction, $e3_timeframe ),
	'provider'    => $author_person,
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

<main id="primary" class="hu-intercept" role="main" data-track-page="server-side-tracking-b2b">

	<section class="hu-intercept__hero" id="hero" aria-labelledby="hu-sst-hero-title">
		<div class="hu-intercept__container">
			<p class="hu-intercept__eyebrow">Tracking-Architektur für B2B-Leadgenerierung</p>
			<h1 class="hu-intercept__title" id="hu-sst-hero-title">
				Server-Side Tracking: Volle Attribution trotz Cookieless, ITP und Ad-Blockern
			</h1>
			<p class="hu-intercept__lead">
				Klassisches Client-Tracking verliert <strong>40 – 60 %</strong> der Conversions. Server-Side Tracking auf eigenem Server in Frankfurt liefert vollständige Daten an GA4 und Meta CAPI – DSGVO-konform mit Consent Mode v2. Grundlage für jede ehrliche Marketing-Entscheidung im B2B.
			</p>
			<?php get_template_part( 'template-parts/seo-subpage-byline', null, [ 'template_path' => __FILE__ ] ); ?>
			<div class="hu-intercept__cta">
				<a class="hu-intercept__cta-primary"
				   href="<?php echo esc_url( $marktcheck_url ); ?>"
				   data-track-action="cta_marktcheck"
				   data-track-category="server_side_tracking_b2b"
				   data-track-section="hero">
					60-Sekunden-Marktcheck starten
				</a>
				<a class="hu-intercept__cta-secondary"
				   href="<?php echo esc_url( $e3_url ); ?>"
				   data-track-action="cta_e3_case"
				   data-track-category="server_side_tracking_b2b"
				   data-track-section="hero">
					E3-Case ansehen (<?php echo esc_html( $e3_cpl_reduction ); ?> CPL-Senkung)
				</a>
			</div>
		</div>
	</section>

	<section class="hu-intercept__why" id="problem" aria-labelledby="hu-sst-problem-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-sst-problem-title">Warum Standard-Tracking im B2B nicht mehr reicht</h2>
			<div class="hu-intercept__grid hu-intercept__grid--four">
				<?php foreach ( $problem_facts as $item ) : ?>
					<article class="hu-intercept__card">
						<h3 class="hu-intercept__card-title"><?php echo esc_html( $item['k'] ); ?></h3>
						<p class="hu-intercept__card-text"><?php echo esc_html( $item['l'] ); ?></p>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="hu-intercept__system" id="loesung" aria-labelledby="hu-sst-solution-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-sst-solution-title">Die Architektur: 5 Schichten, in eigenem Eigentum</h2>
			<p class="hu-intercept__section-lead">
				Server-Side Tracking ist kein Plug-in. Es ist eine Architektur, die in den Anfrage-Funnel eingebaut wird – und im Betrieb verbleibt. Code, Container, Server, Daten gehören Ihnen.
			</p>
			<ol class="hu-intercept__layers">
				<?php foreach ( $solution_layers as $i => $layer ) : ?>
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

	<section class="hu-intercept__why" id="stack" aria-labelledby="hu-sst-stack-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-sst-stack-title">Tech-Stack im Detail</h2>
			<div class="hu-intercept__grid hu-intercept__grid--four">
				<?php foreach ( $tech_stack as $item ) : ?>
					<article class="hu-intercept__card">
						<h3 class="hu-intercept__card-title"><?php echo esc_html( $item['t'] ); ?></h3>
						<p class="hu-intercept__card-text"><?php echo esc_html( $item['s'] ); ?></p>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="hu-intercept__faq" id="faq" aria-labelledby="hu-sst-faq-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-sst-faq-title">Häufige Fragen zu Server-Side Tracking</h2>
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

	<section class="hu-intercept__final" id="final-cta" aria-labelledby="hu-sst-final-title">
		<div class="hu-intercept__container hu-intercept__container--centered">
			<h2 class="hu-intercept__h2" id="hu-sst-final-title">Tracking-Lücken in 60 Sekunden einordnen</h2>
			<p class="hu-intercept__final-text">
				Im Marktcheck zeigt sich schnell, ob die heutige Attribution Ihrer Marketing-Kanäle belastbar ist – oder ob entscheidende Conversions im Standard-Setup verloren gehen.
			</p>
			<div class="hu-intercept__cta">
				<a class="hu-intercept__cta-primary"
				   href="<?php echo esc_url( $marktcheck_url ); ?>"
				   data-track-action="cta_marktcheck"
				   data-track-category="server_side_tracking_b2b"
				   data-track-section="final">
					60-Sekunden-Marktcheck starten
				</a>
				<a class="hu-intercept__cta-secondary"
				   href="<?php echo esc_url( $solar_money_url ); ?>"
				   data-track-action="cta_money_page"
				   data-track-category="server_side_tracking_b2b"
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
		'track_category' => 'server_side_tracking_b2b',
	]
);

get_footer();
