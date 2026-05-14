<?php
/**
 * Template Name: KI-Integration
 * Description: Dachseite KI-Integration fuer WordPress – Positionierung, DSGVO-Story, Verlinkung zu KI-Assets
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Legacy-Seite: aus Index entkoppeln, damit keine Mischsignale zur neuen Solar/Wärmepumpen-Positionierung entstehen.
if ( ! headers_sent() ) {
	header( 'X-Robots-Tag: noindex, nofollow', true );
}
add_action( 'wp_head', static function () {
	echo '<meta name="robots" content="noindex,nofollow" />' . "\n";
}, 1 );

get_header();

$audit_url    = nexus_get_audit_url();
$calendar_url = function_exists( 'nexus_get_audit_calendar_url' ) ? nexus_get_audit_calendar_url() : home_url( '/kontakt/' );
$wgos_url     = function_exists( 'nexus_get_wgos_url' ) ? nexus_get_wgos_url() : home_url( '/wordpress-agentur-hannover/#wgos' );
$hub_url      = function_exists( 'nexus_get_wgos_asset_hub_url' ) ? nexus_get_wgos_asset_hub_url() : home_url( '/wordpress-agentur-hannover/#asset-uebersicht' );
$datenschutz_url = home_url( '/datenschutz/' );
$page_url     = get_permalink( get_queried_object_id() );

if ( ! $page_url ) {
	$page_url = home_url( '/ki-integration-wordpress/' );
}

$nav_items = [
	[
		'id'    => 'problem',
		'label' => 'Problem',
	],
	[
		'id'    => 'bausteine',
		'label' => 'Bausteine',
	],
	[
		'id'    => 'privacy',
		'label' => 'Privacy',
	],
	[
		'id'    => 'system',
		'label' => 'System',
	],
	[
		'id'    => 'faq',
		'label' => 'FAQ',
	],
	[
		'id'    => 'audit',
		'label' => 'Audit',
	],
];

$ki_assets = [
	[
		'title'      => 'KI-Assistent / Chatbot',
		'badge'      => 'Weiterentwicklung',
		'credits'    => '30',
		'excerpt'    => 'Ein Assistent, der Besuchern auf Basis Ihrer Inhalte antwortet – DSGVO-konform, auf eigener Infrastruktur.',
		'url'        => home_url( '/wgos-assets/ki-assistent-chatbot/' ),
	],
	[
		'title'      => 'KI-gestützte Lead-Qualifizierung',
		'badge'      => 'Weiterentwicklung',
		'credits'    => '20',
		'excerpt'    => 'Formulare, die verstehen statt nur sammeln. Automatische Vorselektion nach Relevanz und Passung.',
		'url'        => home_url( '/wgos-assets/ki-lead-qualifizierung/' ),
	],
	[
		'title'      => 'RAG-Wissenssuche',
		'badge'      => 'Weiterentwicklung',
		'credits'    => '25',
		'excerpt'    => 'Antworten aus Ihren Inhalten, nicht aus dem Internet. Semantische Suche mit Quellenangabe.',
		'url'        => home_url( '/wgos-assets/rag-wissenssuche/' ),
	],
	[
		'title'      => 'LLM-Workflow-Automatisierung',
		'badge'      => 'Weiterentwicklung',
		'credits'    => '20',
		'excerpt'    => 'Geschäftsprozesse mit KI-Schritten automatisieren – server-seitig auf n8n, dokumentiert und versioniert.',
		'url'        => home_url( '/wgos-assets/llm-workflow-automatisierung/' ),
	],
];

$problem_cards = [
	[
		'title' => 'DSGVO-Risiko',
		'text'  => 'Die meisten KI-Widgets senden jede Nutzereingabe an US-Server. Das ist kein Implementierungsdetail, sondern ein Compliance-Problem.',
	],
	[
		'title' => 'Vendor-Abhängigkeit',
		'text'  => 'Wer sein KI-Feature an einen SaaS-Anbieter koppelt, verliert Datenhoheit und Kontrolle über die eigene Infrastruktur.',
	],
	[
		'title' => 'Kein Bezug zur Website-Logik',
		'text'  => 'KI-Features ohne Anbindung an Angebotsstruktur, Funnel und Inhalte bleiben Spielerei statt Conversion-Hebel.',
	],
];

$privacy_points = [
	'Keine API-Calls an US-Provider aus dem Frontend.',
	'Inferenz auf eigenem Server oder europäischem Hosting.',
	'Kein Training mit Kundendaten.',
	'Consent-Layer und Datenschutz von Anfang an mitgedacht.',
	'Gleiche Privacy-Philosophie wie beim gesamten WGOS-Stack.',
];

$system_areas = [
	[
		'area'   => 'Weiterentwicklung',
		'assets' => 'KI-Assistent, Lead-Qualifizierung, RAG-Wissenssuche und LLM-Workflows',
		'text'   => 'KI- und Automatisierungs-Bausteine kontrolliert auf ein bestehendes System setzen.',
	],
];

$faq_items = [
	[
		'question' => 'Brauche ich dafür ein großes Budget?',
		'answer'   => 'Nein. Der kleinste KI-Baustein startet bei 20 Credits. Im Systemaufbau-Paket (60 Credits/Monat) ist das ab dem ersten Monat umsetzbar.',
	],
	[
		'question' => 'Funktioniert das mit meiner bestehenden WordPress-Seite?',
		'answer'   => 'Ja. Kein Relaunch nötig. Die KI-Bausteine werden in die bestehende Infrastruktur integriert.',
	],
	[
		'question' => 'Was passiert mit meinen Daten?',
		'answer'   => 'Nichts. Verarbeitung auf Ihrem Server oder europäischem Hosting. Kein Datenabfluss an Dritte. Keine Trainings mit Ihren Daten.',
	],
	[
		'question' => 'Ist das nicht einfach ein ChatGPT-Widget?',
		'answer'   => 'Nein. Ein Widget sendet jede Eingabe an OpenAI. Unsere Lösung verarbeitet alles server-seitig, auf Basis Ihrer eigenen Inhalte.',
	],
	[
		'question' => 'Welcher KI-Baustein ist der richtige für mich?',
		'answer'   => 'Das klärt die System-Diagnose. Die Empfehlung hängt davon ab, wo Ihre Website heute steht und welches Problem zuerst gelöst werden soll.',
	],
];

/* ── Schema: Service ── */
$service_schema = [
	'@context'    => 'https://schema.org',
	'@type'       => 'Service',
	'name'        => 'KI-Integration für WordPress',
	'description' => 'DSGVO-konforme Integration von KI-Features in WordPress-Websites: Chatbots, Lead-Qualifizierung, Wissenssuche und Prozessautomatisierung auf eigener Infrastruktur.',
	'provider'    => [
		'@type' => 'LocalBusiness',
		'@id'   => home_url( '/#organization' ),
	],
	'serviceType' => 'KI-Integration',
	'areaServed'  => [
		'@type'          => 'GeoShape',
		'addressCountry' => [ 'DE', 'AT', 'CH' ],
	],
	'hasOfferCatalog' => [
		'@type'           => 'OfferCatalog',
		'name'            => 'KI-Bausteine im WGOS',
		'itemListElement' => [
			[
				'@type'       => 'Offer',
				'itemOffered' => [
					'@type' => 'Service',
					'name'  => 'KI-Assistent / Chatbot',
					'url'   => home_url( '/wgos-assets/ki-assistent-chatbot/' ),
				],
			],
			[
				'@type'       => 'Offer',
				'itemOffered' => [
					'@type' => 'Service',
					'name'  => 'KI-gestützte Lead-Qualifizierung',
					'url'   => home_url( '/wgos-assets/ki-lead-qualifizierung/' ),
				],
			],
			[
				'@type'       => 'Offer',
				'itemOffered' => [
					'@type' => 'Service',
					'name'  => 'RAG-Wissenssuche',
					'url'   => home_url( '/wgos-assets/rag-wissenssuche/' ),
				],
			],
			[
				'@type'       => 'Offer',
				'itemOffered' => [
					'@type' => 'Service',
					'name'  => 'LLM-Workflow-Automatisierung',
					'url'   => home_url( '/wgos-assets/llm-workflow-automatisierung/' ),
				],
			],
		],
	],
	'url' => $page_url,
];

/* ── Schema: FAQPage ── */
$faq_schema = [
	'@context'   => 'https://schema.org',
	'@type'      => 'FAQPage',
	'@id'        => trailingslashit( $page_url ) . '#faq',
	'url'        => $page_url,
	'inLanguage' => 'de',
	'publisher'  => [
		'@id' => home_url( '/#organization' ),
	],
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
?>

<main id="main" class="site-main">
	<div class="wgos-wrapper">

		<nav class="wgos-smart-nav" id="wgos-nav" aria-label="Seitennavigation KI-Integration">
			<ul>
				<?php foreach ( $nav_items as $nav_item ) : ?>
					<li>
						<a href="#<?php echo esc_attr( $nav_item['id'] ); ?>">
							<span class="wgos-nav-dot"></span>
							<span class="wgos-nav-text"><?php echo esc_html( $nav_item['label'] ); ?></span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</nav>

		<!-- ── HERO ── -->
		<section class="wgos-hero">
			<div class="wgos-container">
				<div class="wgos-hero-grid">
					<div class="wgos-hero-copy">
						<span class="wgos-kicker">KI-Integration für WordPress</span>
						<h1 class="wgos-hero__title">KI-Features für Ihre WordPress-Website. DSGVO-konform. Auf Ihrer Infrastruktur.</h1>
						<p class="wgos-hero__subtitle">Chatbots, Wissenssuche, Lead-Qualifizierung und Prozessautomatisierung – ohne dass Kundendaten an US-Server fließen.</p>

						<ul class="wgos-hero__bullets">
							<li>Verarbeitung auf eigenem Server</li>
							<li>Kein Datenabfluss an Dritte</li>
							<li>Nahtlos in WordPress integriert</li>
						</ul>

						<div class="wgos-hero__actions">
							<a href="<?php echo esc_url( $audit_url ); ?>" class="wgos-btn wgos-btn--primary" data-track-action="cta_ki_hero_audit" data-track-category="lead_gen"><?php echo esc_html( nexus_get_audit_cta_label() ); ?></a>
							<a href="#bausteine" class="wgos-btn wgos-btn--ghost">KI-Bausteine ansehen</a>
						</div>

						<p class="wgos-hero__microcopy">Wir prüfen, ob und wo KI auf Ihrer Website Sinn macht.</p>
					</div>

					<aside class="wgos-hero-card" aria-label="KI-Bausteine Überblick">
						<span class="wgos-principle-kicker">4 Bausteine</span>
						<div class="wgos-phase-list">
							<article class="wgos-phase-list__item">
								<span class="wgos-phase-list__label">Weiterentwicklung</span>
								<h3>KI-Assistent + Lead-Qualifizierung</h3>
								<p>Besucher informieren, Anfragen vorqualifizieren und sauber weiterleiten.</p>
							</article>
							<article class="wgos-phase-list__item">
								<span class="wgos-phase-list__label">Weiterentwicklung</span>
								<h3>RAG-Wissenssuche</h3>
								<p>Semantische Antworten aus Ihren eigenen Inhalten aufbauen.</p>
							</article>
							<article class="wgos-phase-list__item">
								<span class="wgos-phase-list__label">Weiterentwicklung</span>
								<h3>LLM-Workflow-Automatisierung</h3>
								<p>Repetitive Prozesse mit KI-Schritten auf n8n automatisieren.</p>
							</article>
						</div>
						<p class="wgos-hero-card__note">Alle Bausteine laufen auf eigener Infrastruktur. Keine SaaS-Abhängigkeit.</p>
					</aside>
				</div>
			</div>
		</section>

		<!-- ── PROBLEM-FRAME ── -->
		<section id="problem" class="wgos-section wgos-section--white nx-reveal">
			<div class="wgos-container">
				<div class="wgos-section-head">
					<span class="wgos-principle-kicker">Problem</span>
					<h2 class="wgos-h2">Die meisten KI-Lösungen lösen ein Problem und schaffen zwei neue.</h2>
					<p class="wgos-section-intro">KI auf der Website klingt einfach. In der Praxis scheitert es an DSGVO-Risiken, Abhängigkeiten und fehlendem Bezug zur eigenen Geschäftslogik.</p>
				</div>

				<div class="wgos-failure-grid">
					<?php foreach ( $problem_cards as $problem_card ) : ?>
						<article class="wgos-failure-card">
							<span class="wgos-failure-card__eyebrow">Typisches Risiko</span>
							<h3><?php echo esc_html( $problem_card['title'] ); ?></h3>
							<p class="wgos-failure-card__result"><?php echo esc_html( $problem_card['text'] ); ?></p>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</section>

		<!-- ── DIE 4 KI-BAUSTEINE ── -->
		<section id="bausteine" class="wgos-section wgos-section--gray nx-reveal">
			<div class="wgos-container">
				<div class="wgos-section-head">
					<span class="wgos-principle-kicker">KI-Bausteine im WGOS</span>
					<h2 class="wgos-h2">Was KI auf WordPress konkret leisten kann – wenn das Fundament stimmt.</h2>
					<p class="wgos-section-intro">Vier Bausteine, die KI dort einsetzen, wo sie messbaren Geschäftswert erzeugt. Jeder Baustein ist ein WGOS-Asset mit klarem Credit-Wert im Kernbereich Weiterentwicklung.</p>
				</div>

				<div class="wgos-component-grid">
					<?php foreach ( $ki_assets as $ki_asset ) : ?>
						<article class="wgos-failure-card">
							<span class="wgos-failure-card__eyebrow"><?php echo esc_html( $ki_asset['badge'] ); ?> · <?php echo esc_html( $ki_asset['credits'] ); ?> Credits</span>
							<h3><?php echo esc_html( $ki_asset['title'] ); ?></h3>
							<p class="wgos-failure-card__result"><?php echo esc_html( $ki_asset['excerpt'] ); ?></p>
							<a href="<?php echo esc_url( $ki_asset['url'] ); ?>" class="wgos-link--arrow" data-track-action="cta_ki_asset_<?php echo esc_attr( sanitize_title( $ki_asset['title'] ) ); ?>" data-track-category="navigation">Asset ansehen</a>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</section>

		<!-- ── PRIVACY-FIRST ── -->
		<section id="privacy" class="wgos-section wgos-section--white nx-reveal">
			<div class="wgos-container">
				<div class="wgos-section-head">
					<span class="wgos-principle-kicker">Privacy-First</span>
					<h2 class="wgos-h2">DSGVO-Konformität ist kein Feature. Es ist die Architektur-Entscheidung.</h2>
					<p class="wgos-section-intro">Die KI-Bausteine im WGOS folgen derselben Privacy-Philosophie wie der gesamte Stack: Datenhoheit bleibt beim Kunden, Verarbeitung bleibt in Europa.</p>
				</div>

				<div class="wgos-overview-grid">
					<div class="wgos-principle-shell">
						<ul class="wgos-checklist">
							<?php foreach ( $privacy_points as $privacy_point ) : ?>
								<li><?php echo esc_html( $privacy_point ); ?></li>
							<?php endforeach; ?>
						</ul>
						<a href="<?php echo esc_url( $datenschutz_url ); ?>" class="wgos-link--arrow">Zur Datenschutz-Seite</a>
					</div>

					<div class="wgos-note-card">
						<h3>Was das in der Praxis bedeutet</h3>
						<p>Kein Besucher Ihrer Website löst einen API-Call an OpenAI, Google oder andere US-Provider aus. Die Inferenz läuft auf Ihrem eigenen Server oder europäischem Hosting. Ihre Daten werden nicht zum Training verwendet. Consent-Layer und Datenschutz sind von Anfang an Teil der Architektur.</p>
					</div>
				</div>
			</div>
		</section>

		<!-- ── EINORDNUNG IM WGOS ── -->
		<section id="system" class="wgos-section wgos-section--gray nx-reveal">
			<div class="wgos-container">
				<div class="wgos-section-head">
					<span class="wgos-principle-kicker">WGOS-Einordnung</span>
					<h2 class="wgos-h2">KI ist kein neues System. Es erweitert das bestehende.</h2>
					<p class="wgos-section-intro">Die KI-Bausteine liegen im WGOS jetzt gebündelt im Kernbereich Weiterentwicklung. Die Reihenfolge entscheidet weiterhin der Audit, nicht die Technologie.</p>
				</div>

				<div class="wgos-phase-grid" aria-label="KI-Bausteine nach Kernbereich">
					<?php
					$step_counter = 0;
					$step_labels  = [ '01', '02', '03' ];
					foreach ( $system_areas as $system_area ) :
						?>
						<article class="wgos-phase-card">
							<div class="wgos-phase-card__top">
								<span class="wgos-phase-card__step"><?php echo esc_html( $step_labels[ $step_counter ] ); ?></span>
								<div>
									<span class="wgos-phase-card__eyebrow"><?php echo esc_html( $system_area['area'] ); ?></span>
									<h3><?php echo esc_html( $system_area['assets'] ); ?></h3>
								</div>
							</div>
							<p><?php echo esc_html( $system_area['text'] ); ?></p>
						</article>
						<?php
						++$step_counter;
					endforeach;
					?>
				</div>

				<div class="wgos-asset-hub-bridge">
					<div class="wgos-note-card">
						<h3>WGOS verstehen</h3>
						<p>Die KI-Bausteine sind Teil eines größeren Systems mit sechs Modulen und drei Phasen. Die System-Diagnose klärt, welcher Baustein zuerst Sinn macht.</p>
						<a href="<?php echo esc_url( $wgos_url ); ?>" class="wgos-link--arrow">WordPress Growth Operating System ansehen</a>
					</div>
				</div>
			</div>
		</section>

		<!-- ── FAQ ── -->
		<section id="faq" class="wgos-section wgos-section--white nx-reveal">
			<div class="wgos-container">
				<div class="wgos-section-head">
					<span class="wgos-principle-kicker">Häufige Fragen</span>
					<h2 class="wgos-h2">Häufige Fragen zur KI-Integration</h2>
				</div>

				<div class="wgos-faq-list">
					<?php foreach ( $faq_items as $faq_item ) : ?>
						<details class="wgos-component-card nx-reveal">
							<summary class="wgos-component-card__top">
								<div>
									<h3><?php echo esc_html( $faq_item['question'] ); ?></h3>
								</div>
							</summary>
							<div class="wgos-component-card__details">
								<p><?php echo esc_html( $faq_item['answer'] ); ?></p>
							</div>
						</details>
					<?php endforeach; ?>
				</div>
			</div>
		</section>

		<!-- ── CTA / NÄCHSTER SCHRITT ── -->
		<section id="audit" class="wgos-section wgos-section--gray nx-reveal">
			<div class="wgos-container">
				<div class="wgos-audit-shell">
					<div class="wgos-audit-copy">
						<span class="wgos-principle-kicker">Nächster Schritt</span>
						<h2 class="wgos-h2">Prüfen wir, ob KI auf Ihrer WordPress-Seite heute Sinn macht.</h2>
						<div class="wgos-prose">
							<p>Die System-Diagnose zeigt, wo Ihre Website steht, welcher Engpass zuerst gelöst werden muss und ob KI dabei eine Rolle spielt. Persönliche Rückmeldung in 48 Stunden.</p>
						</div>

						<div class="wgos-hero__actions">
							<a href="<?php echo esc_url( $audit_url ); ?>" class="wgos-btn wgos-btn--primary" data-track-action="cta_ki_footer_audit" data-track-category="lead_gen"><?php echo esc_html( nexus_get_audit_cta_label() ); ?></a>
							<a href="<?php echo esc_url( $wgos_url ); ?>" class="wgos-btn wgos-btn--outline" data-track-action="cta_ki_footer_wgos" data-track-category="navigation">WGOS verstehen</a>
						</div>

						<p class="wgos-hero__microcopy">Lieber erst sprechen? <a href="<?php echo esc_url( $calendar_url ); ?>" data-track-action="cta_ki_footer_calendar" data-track-category="lead_gen">Strategiegespräch vereinbaren</a>.</p>
					</div>

					<div class="wgos-audit-aside">
						<div class="wgos-note-card">
							<h3>Weitere Einstiege</h3>
							<ul class="wgos-checklist">
								<li><a href="<?php echo esc_url( $hub_url ); ?>">Zur Asset-Landkarte</a></li>
								<li><a href="<?php echo esc_url( $wgos_url ); ?>">WGOS-System verstehen</a></li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</section>

	</div>
</main>

<script type="application/ld+json"><?php echo wp_json_encode( $service_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?></script>
<script type="application/ld+json"><?php echo wp_json_encode( $faq_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?></script>

<?php
get_footer();
