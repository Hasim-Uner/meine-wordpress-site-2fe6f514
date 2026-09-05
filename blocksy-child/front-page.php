<?php
/**
 * Front Page Template — Verteiler mit drei Türen.
 *
 * Die Startseite verkauft kein Solar mehr. Sie ist die fachliche Klammer plus
 * Verteiler auf drei Wege; jede Conversion findet auf der Zielseite statt.
 * Vorher konkurrierte sie mit /solar-waermepumpen-leadgenerierung/ um dieselbe
 * Suchanfrage — dieselbe Zielgruppe, dieselben Zahlen, dieselbe Blockfolge,
 * aber ohne eigenes Formular.
 *
 * Architektur (6 Blöcke):
 *   1 Hero            — Klammer, kein Button
 *   2 Drei Türen      — Solar-Marktcheck · WordPress Freelancer · White-Label
 *   3 Lebender Beweis — an dieser Seite nachmessbar (Übernahme Agentur-Seite)
 *   4 Beleg           — E3-Case, genau einmal auf der Seite
 *   5 Abgrenzung      — Selbstqualifizierung statt vergrabener FAQ
 *   6 Abschluss       — für alle, die keinen der drei Wege wählen
 *
 * Genau eine Wegewahl. Der frühere Block 2 "Einstieg" stellte dieselbe
 * Entscheidung ein zweites Mal und sortierte dabei nach dem Kanal, während er
 * nach dem Segment routete; er ist entfallen und die Türen sind nach oben
 * gerückt.
 *
 * Der Marktcheck bleibt auf der Solar-Money-Page: diese Seite verteilt nur,
 * sie trägt kein zweites vollständiges Formular.
 *
 * Schema/SEO: Title + Description kommen aus inc/seo-meta.php
 * (hu_get_homepage_title / hu_get_homepage_description). Organization +
 * hasOfferCatalog werden zentral aus inc/org-schema.php injiziert —
 * dieses Template emittiert kein eigenes JSON-LD. Das FAQPage-Schema der
 * Startseite ist mit der FAQ entfallen; Solar- und Agentur-Seite tragen
 * weiterhin eigenes FAQ-Schema.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ── Datenbindungen ────────────────────────────────────── */
// Absolut auf die Solar-Money-Page inklusive #marktcheck. Bleibt absolut —
// nicht in einen relativen Anker umschreiben, hier existiert kein Formular.
$analysis_url     = function_exists( 'hu_get_request_analysis_url' ) ? hu_get_request_analysis_url() : home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' );

$e3_canon      = function_exists( 'hu_e3_canon' ) ? hu_e3_canon() : [];
$e3_case_url   = isset( $e3_canon['url'] ) ? (string) $e3_canon['url'] : home_url( '/case-study-solar-leadgenerierung/' );
$e3_case_label = isset( $e3_canon['case_label'] ) ? (string) $e3_canon['case_label'] : '';
$e3_metric     = static function ( $key, $field = 'display' ) {
	return function_exists( 'hu_e3_metric' ) ? hu_e3_metric( $key, $field ) : '';
};
$e3_case_metrics = [
	[
		'value' => trim( $e3_metric( 'cpl_before' ) . ' → ' . $e3_metric( 'cpl_after' ) ),
		'label' => 'Kosten pro qualifizierter Anfrage',
	],
	[
		'value' => $e3_metric( 'lead_count' ),
		'label' => 'qualifizierte Anfragen',
	],
	[
		'value' => $e3_metric( 'sales_conversion' ),
		'label' => 'Abschlussquote',
	],
];

$primary_urls     = function_exists( 'nexus_get_primary_public_url_map' ) ? nexus_get_primary_public_url_map() : [];
$about_url        = $primary_urls['about'] ?? home_url( '/hasim-uener/' );
$contact_base_url = function_exists( 'nexus_get_contact_url' ) ? nexus_get_contact_url() : home_url( '/kontakt/' );
$contact_fallback = add_query_arg(
	[
		'type'  => 'project',
		'focus' => 'implementation_scope',
	],
	$contact_base_url
);
$contact_url      = function_exists( 'hu_get_commercial_route' )
	? hu_get_commercial_route( 'project_request', $contact_fallback )
	: ( function_exists( 'hu_get_navigation_project_request_url' ) ? hu_get_navigation_project_request_url() : $contact_fallback );
$tracking_url     = function_exists( 'hu_get_commercial_route' )
	? hu_get_commercial_route( 'tracking_b2b', home_url( '/server-side-tracking-b2b/' ) )
	: home_url( '/server-side-tracking-b2b/' );
$freelancer_url   = home_url( '/wordpress-freelancer-hannover/' );
$whitelabel_url   = function_exists( 'nexus_get_whitelabel_page_url' ) ? nexus_get_whitelabel_page_url() : home_url( '/whitelabel-retainer/' );
$psi_url          = 'https://pagespeed.web.dev/analysis?url=' . rawurlencode( home_url( '/' ) );

/*
 * PageSpeed-Wert des lebenden Beweises (Block 3).
 *
 * ACHTUNG — vor dem Deploy neu messen und hier eintragen. Der Wert unten ist
 * der Referenzstand der Startseite aus dem Briefing (14.08.2026), nicht der
 * Wert der Agentur-Seite und keine Schätzung. Ein Block, der zum Nachmessen
 * auffordert, darf keine Zahl zeigen, die der Besucher widerlegen kann.
 */
$home_psi_mobile  = '99';

/* ── Block 2 · Die drei Türen ─────────────────────────────
 *
 * Jede Tür ist über die Situation des Besuchers benannt, nicht über die
 * Leistung — der Besucher sucht sich, nicht das Angebot. Die E3-Zahlen stehen
 * bewusst in keiner Tür: sie gehören genau einmal auf die Seite, in Block 4.
 *
 * Der frühere Block "Einstieg" (eine Klick-Frage, fünf Antworten) stand hier
 * davor und stellte dieselbe Routing-Entscheidung ein zweites Mal. Er sortierte
 * zusätzlich nach dem falschen Kriterium: gefragt war der Kanal ("Woher kommen
 * Ihre Anfragen heute?"), geroutet wurde nach dem Segment. Ein Solarbetrieb mit
 * organischen Anfragen landete damit im Freelancer-Angebot und eine
 * Nicht-Solar-Firma mit Paid-Kampagnen im Solar-Marktcheck. Die drei Türen sind
 * jetzt die einzige Wegewahl der Seite; die `home_entry_*`-Events entfallen,
 * `home_door_*` bleibt und wird dadurch erstmals sauber interpretierbar.
 */
$home_doors = [
	[
		'badge'   => '01',
		'kicker'  => 'Solar · Wärmepumpe · Speicher',
		'title'   => 'Sie kaufen Anfragen ein und wollen eigene aufbauen.',
		'desc'    => 'Portal-Leads werden mehrfach verkauft, und am Monatsende haben Sie nichts Eigenes aufgebaut. Der Marktcheck sagt Ihnen in 48 Stunden, ob sich ein eigenes System für Ihre Region rechnet — oder noch nicht.',
		'url'     => $analysis_url,
		'label'   => 'Marktcheck starten',
		'action'  => 'home_door_marktcheck',
	],
	[
		'badge'   => '02',
		'kicker'  => 'Direkte Zusammenarbeit · WordPress',
		'title'   => "Sie brauchen WordPress-Kompetenz, direkt\u{00A0}umgesetzt.",
		'desc'    => 'Website, Tracking und Funnel sollen technisch zusammenpassen — und Sie wollen direkt mit der Person arbeiten, die strukturiert, entwickelt und deployt.',
		'url'     => $freelancer_url,
		'label'   => 'WordPress Freelancer ansehen',
		'action'  => 'home_door_freelancer',
	],
	[
		'badge'   => '03',
		'kicker'  => 'Für Agenturen',
		'title'   => 'Sie verkaufen die Projekte. Die Umsetzung fehlt.',
		'desc'    => 'WordPress, Tracking und Automation unter Ihrem Namen. NDA standardmäßig, fixer Scope, Preis vor dem Start.',
		'url'     => $whitelabel_url,
		'label'   => 'White-Label ansehen',
		'action'  => 'home_door_whitelabel',
	],
];

/* ── Block 3 · Kacheln des lebenden Beweises ──────────────
 * Belegbar ohne fremde Quelle: drei Lighthouse-Werte dieser Seite plus der
 * Tracking-Aufbau. Keine gekauften Siegel, keine geliehenen Logos.
 */
$home_proof_tiles = [
	[ 'value' => '100', 'max' => '/100', 'label' => 'Barrierefreiheit',    'note' => '' ],
	[ 'value' => '100', 'max' => '/100', 'label' => 'SEO & Best Practices', 'note' => '' ],
	[ 'value' => $home_psi_mobile, 'max' => '', 'label' => 'PageSpeed mobil', 'note' => '(Lighthouse, mobil — nicht aufgerundet)' ],
	[
		'value'  => 'Aktiv',
		'max'    => '',
		'label'  => 'Server-Side Tracking',
		'note'   => '(GA4 + sGTM, eigener Server)',
		'url'    => $tracking_url,
		'action' => 'home_proof_tracking_page',
	],
];

get_header();
?>

<div class="hu-hp hu-hp--hub" id="top" data-track-section="homepage">

	<!-- ═══════════════════════════════════════════════════
	     01 / HERO — fachliche Klammer, kein Button
	     ═══════════════════════════════════════════════════ -->
	<section class="hu-hero hu-hero--hub" id="hero" data-track-section="hero">
		<div class="hu-hero__grid-bg hu-hero__grid-bg--blueprint" aria-hidden="true"></div>
		<div class="hu-container hu-hero__container">

			<div class="hu-hero__lede">
				<div class="hu-hero__eyebrow">
					<span class="hu-tag">
						<span class="hu-dot hu-dot--live"></span>
						<span class="hu-mono">WORDPRESS · SEO · TRACKING · CONVERSION</span>
					</span>
				</div>

				<h1 class="hu-display hu-hero__title">
					Websites, die Anfragen produzieren.<br>
					<span class="hu-hero__title-2">Und Technik, die Wirkung messbar macht.</span>
				</h1>

				<p class="hu-hero__sub">
					WordPress, technisches SEO, Tracking und Conversion als ein System — nicht als vier Rechnungen. Aus Pattensen bei Hannover, für den DACH-Raum.
				</p>

				<?php
				// Kein CTA im Hero: die Wegewahl steht direkt darunter. Der Anker ist
				// nur eine Sprungmarke fuer Tastatur und Screenreader, kein Button.
				?>
				<a class="hu-hero__cue" href="#wege"
				   data-track-action="home_hero_to_routes" data-track-category="navigation" data-track-section="hero">
					<span>Drei Wege — wählen Sie den, der Ihre Situation beschreibt</span>
					<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 5v14M6 13l6 6 6-6"/></svg>
				</a>
			</div>

			<!-- Right: animated SVG infrastructure pipeline -->
			<div class="hu-pipeline-wrap" aria-hidden="true">
				<svg class="hu-pipeline" viewBox="0 0 420 560" xmlns="http://www.w3.org/2000/svg" role="img" aria-labelledby="hu-pipeline-title hu-pipeline-desc" preserveAspectRatio="xMidYMid meet">
					<title id="hu-pipeline-title">Nachfrage-Pipeline</title>
					<desc id="hu-pipeline-desc">Schematische Darstellung: Marktnachfrage fließt durch die eigene Filterstrecke in den Vertrieb (CRM).</desc>

					<defs>
						<pattern id="hu-pipe-grid" width="20" height="20" patternUnits="userSpaceOnUse">
							<path d="M20 0 L0 0 0 20" fill="none" stroke="rgba(255,255,255,.05)" stroke-width="1"/>
						</pattern>
						<pattern id="hu-pipe-grid-major" width="100" height="100" patternUnits="userSpaceOnUse">
							<path d="M100 0 L0 0 0 100" fill="none" stroke="rgba(224,138,60,.10)" stroke-width="1"/>
						</pattern>
						<linearGradient id="hu-pipe-glow" x1="0" y1="0" x2="0" y2="1">
							<stop offset="0%" stop-color="rgba(224,138,60,.6)"/>
							<stop offset="100%" stop-color="rgba(224,138,60,0)"/>
						</linearGradient>
						<filter id="hu-pipe-dot-glow" x="-100%" y="-100%" width="300%" height="300%">
							<feGaussianBlur stdDeviation="2.4" result="blur"/>
							<feMerge>
								<feMergeNode in="blur"/>
								<feMergeNode in="SourceGraphic"/>
							</feMerge>
						</filter>
					</defs>

					<!-- Background grid (Millimeterpapier) -->
					<rect width="420" height="560" fill="url(#hu-pipe-grid)"/>
					<rect width="420" height="560" fill="url(#hu-pipe-grid-major)"/>

					<!-- Schematic frame -->
					<rect x="6" y="6" width="408" height="548" rx="10" fill="none" stroke="rgba(224,138,60,.18)" stroke-dasharray="1 4"/>
					<text x="18" y="22" font-family="'JetBrains Mono', monospace" font-size="9" letter-spacing="2" fill="rgba(224,138,60,.6)">SCHEMATIC · NACHFRAGE-PIPELINE · REV.2026</text>

					<!-- Connectors with pin-terminals -->
					<g stroke="rgba(224,138,60,.42)" stroke-width="1.4" fill="none" stroke-linecap="round">
						<path d="M210 130 L210 218"/>
						<path d="M210 358 L210 446"/>
					</g>

					<!-- Side rails (decorative pins) -->
					<g stroke="rgba(255,255,255,.10)" stroke-width="1" stroke-dasharray="2 4">
						<path d="M40 288 L78 288"/>
						<path d="M342 288 L380 288"/>
						<path d="M40 268 L66 268"/>
						<path d="M354 268 L380 268"/>
						<path d="M40 308 L66 308"/>
						<path d="M354 308 L380 308"/>
					</g>

					<!-- Pin terminals -->
					<g fill="rgba(224,138,60,.7)">
						<circle cx="210" cy="130" r="3.5"/>
						<circle cx="210" cy="218" r="3.5"/>
						<circle cx="210" cy="358" r="3.5"/>
						<circle cx="210" cy="446" r="3.5"/>
					</g>

					<!-- Animated flow dots (rendered BEFORE filter node so they pass under it) -->
					<g class="hu-pipe-dots" filter="url(#hu-pipe-dot-glow)" fill="#E08A3C">
						<circle r="3.4" cx="210" cy="130">
							<animate attributeName="cy" from="130" to="446" dur="4s" repeatCount="indefinite"/>
							<animate attributeName="opacity" values="0;1;1;1;0" dur="4s" repeatCount="indefinite"/>
						</circle>
						<circle r="3.4" cx="210" cy="130">
							<animate attributeName="cy" from="130" to="446" begin="1.33s" dur="4s" repeatCount="indefinite"/>
							<animate attributeName="opacity" values="0;1;1;1;0" begin="1.33s" dur="4s" repeatCount="indefinite"/>
						</circle>
						<circle r="3.4" cx="210" cy="130">
							<animate attributeName="cy" from="130" to="446" begin="2.66s" dur="4s" repeatCount="indefinite"/>
							<animate attributeName="opacity" values="0;1;1;1;0" begin="2.66s" dur="4s" repeatCount="indefinite"/>
						</circle>
					</g>

					<!-- Filter glow ring (drawn behind filter rect for ambient effect) -->
					<rect x="42" y="220" width="336" height="140" rx="12" fill="rgba(224,138,60,.05)" stroke="rgba(224,138,60,.14)"/>

					<!-- Node 1: Quelle / Marktnachfrage -->
					<g transform="translate(60 56)">
						<rect width="300" height="74" rx="9" fill="rgba(11,15,18,.92)" stroke="rgba(255,255,255,.10)"/>
						<rect x="0" y="0" width="6" height="74" rx="3" fill="rgba(224,138,60,.55)"/>
						<text x="20" y="26" font-family="'JetBrains Mono', monospace" font-size="9" letter-spacing="2" fill="#8A8478">QUELLE · 01</text>
						<text x="20" y="48" font-family="'Figtree', 'Inter Tight', sans-serif" font-weight="700" font-size="16" fill="#F2EBDD">Marktnachfrage</text>
						<text x="20" y="64" font-family="'JetBrains Mono', monospace" font-size="10" fill="#8A8478">Region · Bedarf · Projektwert</text>
					</g>

					<!-- Node 2: Filter / Eigene Strecke (central, accented) -->
					<g transform="translate(50 220)">
						<rect width="320" height="140" rx="11" fill="rgba(17,22,26,.98)" stroke="rgba(224,138,60,.55)" stroke-width="1.4"/>
						<rect x="-1" y="-1" width="322" height="142" rx="12" fill="none" stroke="rgba(224,138,60,.14)"/>
						<text x="22" y="30" font-family="'JetBrains Mono', monospace" font-size="9" letter-spacing="2" fill="#E08A3C">FILTER · 02 · ACTIVE</text>
						<text x="22" y="58" font-family="'Figtree', 'Inter Tight', sans-serif" font-weight="800" font-size="22" fill="#F2EBDD">Eigene Strecke</text>
						<g font-family="'JetBrains Mono', monospace" font-size="10.5" fill="#C8C0B0">
							<text x="22" y="88">+ Money Page · Region · Beweis</text>
							<text x="22" y="106">+ Architektonischer Marktcheck</text>
							<text x="22" y="124">+ Server-Side Tracking · Attribution</text>
						</g>
						<!-- Active LED -->
						<circle cx="290" cy="26" r="4" fill="#6BA17A">
							<animate attributeName="opacity" values="1;.3;1" dur="1.6s" repeatCount="indefinite"/>
						</circle>
					</g>

					<!-- Node 3: Vertrieb / CRM -->
					<g transform="translate(60 446)">
						<rect width="300" height="74" rx="9" fill="rgba(224,138,60,.06)" stroke="rgba(224,138,60,.42)"/>
						<rect x="0" y="0" width="6" height="74" rx="3" fill="var(--accent, #E08A3C)" fill-opacity="0.85"/>
						<text x="20" y="26" font-family="'JetBrains Mono', monospace" font-size="9" letter-spacing="2" fill="#E08A3C">VERTRIEB · 03</text>
						<text x="20" y="48" font-family="'Figtree', 'Inter Tight', sans-serif" font-weight="700" font-size="16" fill="#F2EBDD">CRM · Telefon · Termin</text>
						<text x="20" y="64" font-family="'JetBrains Mono', monospace" font-size="10" fill="#8A8478">Attribuierte Anfrage · Fit-Score</text>
					</g>
				</svg>
			</div>

		</div><!-- .hu-hero__container -->
	</section>

	<!-- ═══════════════════════════════════════════════════
	     02 / DIE DREI TÜREN — benannt über die Situation, nicht die Leistung
	     ═══════════════════════════════════════════════════ -->
	<section class="hu-section hu-section--first" id="wege" data-track-section="tueren" aria-labelledby="hu-doors-h">
		<div class="hu-container">
			<div class="hu-section-heading hu-section-heading--solo hu-reveal">
				<div class="hu-section-heading__primary">
					<span class="hu-eyebrow">01 / Drei Wege</span>
					<h2 id="hu-doors-h">Drei Wege. Wählen Sie den, der Ihre Situation&nbsp;beschreibt.</h2>
				</div>
			</div>

			<div class="hu-gateways hu-gateways--doors hu-reveal" data-track-section="tueren">
				<?php foreach ( $home_doors as $door ) : ?>
					<a class="hu-gateway"
					   href="<?php echo esc_url( $door['url'] ); ?>"
					   data-track-action="<?php echo esc_attr( $door['action'] ); ?>"
					   data-track-category="navigation"
					   data-track-section="tueren">
						<div class="hu-gateway__head">
							<span class="hu-gateway__badge"><?php echo esc_html( $door['badge'] ); ?></span>
							<span class="hu-gateway__kicker hu-mono"><?php echo esc_html( $door['kicker'] ); ?></span>
						</div>
						<h3 class="hu-gateway__title"><?php echo esc_html( $door['title'] ); ?></h3>
						<p class="hu-gateway__desc"><?php echo esc_html( $door['desc'] ); ?></p>
						<div class="hu-gateway__foot">
							<span class="hu-gateway__cta">
								<?php echo esc_html( $door['label'] ); ?>
								<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
							</span>
						</div>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<!-- ═══════════════════════════════════════════════════
	     03 / LEBENDER BEWEIS — an dieser Seite nachmessbar
	     ═══════════════════════════════════════════════════ -->
	<section class="hu-section hu-section--cream" id="lebender-beweis" data-track-section="beweis" aria-labelledby="hu-proof-h">
		<div class="hu-container">
			<div class="hu-section-heading hu-reveal">
				<div class="hu-section-heading__primary">
					<span class="hu-eyebrow hu-on-cream">02 / Lebender Beweis</span>
					<h2 id="hu-proof-h" class="hu-on-cream--strong">Ich verkaufe Ladezeit, Tracking und SEO. Prüfen Sie es an dieser Seite.</h2>
				</div>
				<p class="hu-section-heading__intro">Keine gekauften Siegel, keine geliehenen Logos. Diese Seite selbst — jederzeit von Ihnen nachmessbar.</p>
			</div>

			<div class="hu-liveproof hu-reveal">
				<?php foreach ( $home_proof_tiles as $tile ) : ?>
					<?php $is_linked_tile = ! empty( $tile['url'] ); ?>
					<?php if ( $is_linked_tile ) : ?>
					<a class="hu-liveproof__tile hu-liveproof__tile--link"
					   href="<?php echo esc_url( (string) $tile['url'] ); ?>"
					   data-track-action="<?php echo esc_attr( (string) ( $tile['action'] ?? '' ) ); ?>"
					   data-track-category="proof"
					   data-track-section="beweis">
					<?php else : ?>
					<div class="hu-liveproof__tile">
					<?php endif; ?>
						<span class="hu-liveproof__value">
							<?php echo esc_html( $tile['value'] ); ?><?php if ( '' !== $tile['max'] ) : ?><span class="hu-liveproof__max"><?php echo esc_html( $tile['max'] ); ?></span><?php endif; ?>
						</span>
						<span class="hu-liveproof__label"><?php echo esc_html( $tile['label'] ); ?></span>
						<?php if ( '' !== $tile['note'] ) : ?>
							<span class="hu-liveproof__note"><?php echo esc_html( $tile['note'] ); ?></span>
						<?php endif; ?>
					<?php if ( $is_linked_tile ) : ?>
					</a>
					<?php else : ?>
					</div>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>

			<div class="hu-liveproof__cta hu-reveal">
				<a href="<?php echo esc_url( $psi_url ); ?>" class="hu-btn hu-btn-link" target="_blank" rel="noopener"
				   data-track-action="home_proof_pagespeed" data-track-category="proof" data-track-section="beweis">
					Diese Seite bei PageSpeed Insights testen
					<svg width="16" height="16" viewBox="0 0 20 20" fill="none" aria-hidden="true">
						<path d="M8 4H4.5A1.5 1.5 0 0 0 3 5.5v10A1.5 1.5 0 0 0 4.5 17h10a1.5 1.5 0 0 0 1.5-1.5V12M12 3h5m0 0v5m0-5-8 8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
				</a>
				<p class="hu-liveproof__closing">
					Wer Ihnen Ladezeit, Tracking und SEO verkauft, sollte es auf der eigenen Seite beweisen können. Prüfen Sie meine — und danach die Ihrer aktuellen Agentur.
				</p>
			</div>
		</div>
	</section>


	<!-- ═══════════════════════════════════════════════════
	     04 / BELEG — genau einmal auf der Seite
	     ═══════════════════════════════════════════════════ -->
	<section class="hu-section" id="beleg" data-track-section="beleg" aria-labelledby="hu-case-h">
		<div class="hu-container">
			<div class="hu-section-heading hu-reveal">
				<div class="hu-section-heading__primary">
					<span class="hu-eyebrow">03 / Beleg</span>
					<h2 id="hu-case-h">Was dabei herauskommt, wenn Website, Tracking und Vertrieb gemeinsam&nbsp;wirken.</h2>
				</div>
				<p class="hu-section-heading__intro">Der übertragbare Mechanismus: Landingpage, Messung und Vorqualifizierung arbeiten als zusammenhängende Strecke. Dokumentierter Referenzfall: <?php echo esc_html( $e3_case_label ); ?>.</p>
			</div>

			<dl class="hu-case-metrics hu-reveal">
				<?php foreach ( $e3_case_metrics as $metric ) : ?>
					<div class="hu-case-metric">
						<dt class="hu-case-metric__value"><?php echo esc_html( $metric['value'] ); ?></dt>
						<dd class="hu-case-metric__label"><?php echo esc_html( $metric['label'] ); ?></dd>
					</div>
				<?php endforeach; ?>
			</dl>

			<div class="hu-case-note hu-reveal">
				<small class="hu-case-note__disclaimer">Referenzfall, keine pauschale Übertragbarkeitsgarantie.</small>
				<a href="<?php echo esc_url( $e3_case_url ); ?>" class="hu-btn hu-btn-link"
				   data-track-action="home_case_study" data-track-category="proof" data-track-section="beleg">
					Vollständige Case Study
				</a>
			</div>
		</div>
	</section>

	<!-- ═══════════════════════════════════════════════════
	     05 / ABGRENZUNG — Selbstqualifizierung, nicht im Aufklapper
	     ═══════════════════════════════════════════════════ -->
	<section class="hu-section" id="abgrenzung" data-track-section="abgrenzung" aria-labelledby="hu-fit-h">
		<div class="hu-container hu-container--narrow">
			<div class="hu-section-heading hu-section-heading--solo hu-reveal">
				<div class="hu-section-heading__primary">
					<span class="hu-eyebrow">04 / Abgrenzung</span>
					<h2 id="hu-fit-h">Wann ich der falsche Weg bin.</h2>
				</div>
			</div>

			<div class="hu-selfqual hu-reveal">
				<p>
					Wenn Sie eine hübschere Website brauchen, ist eine Agentur die richtige Wahl und der günstigere Weg. Ich bin teurer, weil hier die Verbindung zwischen Website, Messung und Vertrieb die Leistung ist — nicht das Design.
				</p>
				<?php
				// Traegt das Argument des geloeschten Kategorie-Blocks weiter: die
				// Systemlogik liegt versioniert im Repository, nicht in einem Backend.
				?>
				<p>
					Dazu gehört, wem am Ende was gehört: Die Systemlogik liegt versioniert in Ihrem Repository, Tracking- und Datenzugänge bleiben bei Ihnen. Wenn wir auseinandergehen, läuft das System weiter — das ist der Teil, den ein Page-Builder-Backend nicht liefert.
				</p>
				<p class="hu-selfqual__list-intro">Ebenfalls nicht passend:</p>
				<ul class="hu-selfqual__list">
					<li>One-Page-Visitenkarten</li>
					<li>reine Design-Relaunches ohne Lead-Logik</li>
					<li>reine Shop-Projekte</li>
				</ul>
				<a href="<?php echo esc_url( $contact_url ); ?>" class="hu-btn hu-btn-link hu-selfqual__cta"
				   data-track-action="home_fit_project" data-track-category="lead_gen" data-track-section="abgrenzung">
					Sie suchen genau diese Verbindung aus Website, Messung und Vertrieb?
					<span aria-hidden="true">→</span> Projekt anfragen
				</a>
			</div>
		</div>
	</section>

	<!-- ═══════════════════════════════════════════════════
	     06 / ABSCHLUSS — für alle, die keinen der drei Wege wählen
	     ═══════════════════════════════════════════════════ -->
	<section class="hu-section hu-section--close" id="cta" data-track-section="abschluss" aria-labelledby="hu-close-h">
		<div class="hu-container">
			<div class="hu-close hu-reveal">
				<span class="hu-eyebrow hu-on-accent">05 / Nächster Schritt</span>
				<h2 id="hu-close-h" class="hu-display">Sie wissen noch nicht, welcher Weg passt?</h2>
				<p class="hu-close__sub">
					Dann schreiben Sie mir in zwei Sätzen, woran es gerade hakt. <?php echo esc_html( hu_response_promise( 'sentence' ) ); ?>
				</p>

				<div class="hu-close__actions">
					<a href="<?php echo esc_url( $contact_url ); ?>" class="hu-btn hu-btn-primary"
					   data-track-action="home_close_contact" data-track-category="lead_gen" data-track-section="abschluss">
						Direkt schreiben
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
					</a>
				</div>

				<div class="hu-close__signature">— <strong>Haşim Üner</strong> · persönlich, nicht durch ein Vertriebsteam</div>
				<a href="<?php echo esc_url( $about_url ); ?>" class="hu-close__about hu-btn hu-btn-link"
				   data-track-action="home_close_about" data-track-category="trust" data-track-section="abschluss">
					Über Haşim
				</a>
			</div>
		</div>
	</section>

</div><!-- .hu-hp -->

<?php get_footer(); ?>
