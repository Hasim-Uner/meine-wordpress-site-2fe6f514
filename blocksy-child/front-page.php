<?php
/**
 * Front Page Template — Sovereign Command Center (Operational Overhaul).
 *
 * Architektur:
 *   - Hero: Leitmotiv links + animierte SVG-Pipeline (Blueprint) rechts.
 *   - Gateway-Band (3 Weichen) direkt unter dem Hero.
 *   - Sektionen 02–10: Verlust-Raster, Prozess-Kaskade, System-Phasen,
 *     E3-Proof, Portal-Chaos vs. Daten-Integrität, About, FAQ,
 *     Vertiefung, Final Routing.
 *
 * Schema/SEO: Title + Description kommen aus inc/seo-meta.php
 * (hu_get_homepage_title / hu_get_homepage_description). Organization +
 * hasOfferCatalog werden zentral aus inc/org-schema.php injiziert —
 * dieses Template emittiert kein eigenes JSON-LD.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ── Datenbindungen ────────────────────────────────────── */
$analysis_url      = function_exists( 'hu_get_request_analysis_url' ) ? hu_get_request_analysis_url() : home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' );
$e3_canon          = function_exists( 'hu_e3_canon' ) ? hu_e3_canon() : [];
$e3_case_url       = isset( $e3_canon['url'] ) ? (string) $e3_canon['url'] : home_url( '/e3-new-energy/' );
$e3_metrics        = isset( $e3_canon['metrics'] ) && is_array( $e3_canon['metrics'] ) ? $e3_canon['metrics'] : [];
$e3_lead_count     = $e3_metrics['lead_count']['display']        ?? '1.750+';
$e3_sales_conv     = $e3_metrics['sales_conversion']['display']  ?? '12 %';
$e3_cpl_reduction  = $e3_metrics['cpl_reduction']['display']     ?? 'über 85 %';
$e3_timeframe      = $e3_metrics['timeframe']['display']         ?? '6 Monate';
$e3_timeframe_dat  = $e3_metrics['timeframe']['display_dative']  ?? '6 Monaten';
$e3_cpl_before     = $e3_metrics['cpl_before']['display']        ?? '150 €';
$e3_cpl_after      = $e3_metrics['cpl_after']['display']         ?? '22 €';
$contact_url       = function_exists( 'nexus_get_contact_url' ) ? nexus_get_contact_url() : home_url( '/kontakt/' );
$agentur_hub_url   = home_url( '/wordpress-agentur-hannover/' );
$portrait_url      = get_stylesheet_directory_uri() . '/assets/img/hasim-portrait.png';

/* ── Routing-Tabelle: 3 Gateways ───────────────────────── */
$home_routing_gateways = [
	'marktcheck' => [
		'badge'   => 'G1',
		'kicker'  => 'Sofort-Qualifizierung',
		'title'   => 'Der 60-Sekunden-Marktcheck',
		'desc'    => 'Prüft, wo Portal-Abhängigkeit, Website, Tracking oder Vorqualifizierung aktuell qualifizierte Anfragen kosten.',
		'url'     => $analysis_url,
		'label'   => 'Leadkosten & Anfrage-System prüfen',
		'persona' => 'Für portalmüde Solar-/SHK-Anbieter',
		'action'  => 'gateway_marktcheck',
	],
	'agentur' => [
		'badge'   => 'G2',
		'kicker'  => 'Technischer Hub',
		'title'   => 'WordPress Agentur Hannover',
		'desc'    => 'Technisches Fundament für anspruchsvolle B2B-Systeme: Server-Side Tracking, technisches SEO und kontrollierte Weiterentwicklung.',
		'url'     => $agentur_hub_url,
		'label'   => 'B2B Agentur-Hub Hannover ansteuern',
		'persona' => 'Für Unternehmen mit Infrastruktur-Bedarf',
		'action'  => 'gateway_agentur',
	],
	'proof' => [
		'badge'   => 'G3',
		'kicker'  => 'Zahlen-Validierung',
		'title'   => 'Die E3-New-Energy-Methodik',
		'desc'    => sprintf(
			'Wie ein autarker Nachfrage-Funnel die Cost-per-Lead von %s auf %s gesenkt hat — dokumentiert mit echten Vertriebs-Zahlen.',
			$e3_cpl_before,
			$e3_cpl_after
		),
		'url'     => $e3_case_url,
		'label'   => 'Verifizierte E3-Case-Study analysieren',
		'persona' => 'Für skeptische Zahlen-Prüfer',
		'action'  => 'gateway_e3',
	],
];

/* ── 6 System-Phasen (strukturgleich zu page-wordpress-agentur.php) ── */
$home_system_phases = [
	[ 'num' => '01', 'title' => 'Strategie',         'desc' => 'Welche Seite trägt welche Anfrage — und welche nicht.' ],
	[ 'num' => '02', 'title' => 'Fundament',         'desc' => 'Schnell, stabil, wartbar — ohne dass jedes Plugin-Update zur Krise wird.' ],
	[ 'num' => '03', 'title' => 'Messbarkeit',       'desc' => 'GA4, Server-Side Tracking und CRM-Rückführung in einer Logik.' ],
	[ 'num' => '04', 'title' => 'Sichtbarkeit',      'desc' => 'Kaufnahe Suchintention abfangen — bevor der Wettbewerb antwortet.' ],
	[ 'num' => '05', 'title' => 'Conversion',        'desc' => 'Klare Nutzerführung im Anfrageprozess — effizienzoptimierter B2B-Datenpfad statt Tool-Standard.' ],
	[ 'num' => '06', 'title' => 'Weiterentwicklung', 'desc' => 'Datenbasierte Skalierung statt Bauchgefühl oder Pseudo-Relaunch.' ],
];

/* ── 3-stufige Prozess-Kaskade (Sektion 03) ────────────── */
$home_process_cascade = [
	[
		'num'    => '01',
		'kicker' => 'Untergrund lesen',
		'title'  => 'System-Auditing',
		'desc'   => 'Der bestehende Anfrage-Stack wird auf Daten-Integrität, Performance und Attribution geprüft. Befund statt Bauchgefühl, Belege statt Vermutungen.',
	],
	[
		'num'    => '02',
		'kicker' => 'Filter setzen',
		'title'  => 'Daten-Orchestrierung',
		'desc'   => 'Server-Side Tracking, gezielte Vorqualifizierung (ohne Streuverlust) und kontrollierte Werbekanal-Steuerung greifen als ein System ineinander.',
	],
	[
		'num'    => '03',
		'kicker' => 'Eigene Quelle besitzen',
		'title'  => 'Asset-Ownership',
		'desc'   => 'Money Page, Funnel und Tracking-Stack bleiben Eigentum des Betriebs — kein gemieteter Boden, kein Drittanbieter-Lock-in.',
	],
];

/* ── Themen-Hub: ausgelagertes Array (Token-Optimierung) ── */
$homepage_deeper_clusters = include get_stylesheet_directory() . '/inc/wgos/home-deeper-clusters.php';

get_header();
?>

<div class="hu-hp" id="top" data-track-section="homepage">

	<!-- ═══════════════════════════════════════════════════
	     01 / HERO — Architektonisches Leitmotiv + SVG-Pipeline
	     ═══════════════════════════════════════════════════ -->
	<section class="hu-hero hu-hero--command" id="hero" data-track-section="01">
		<div class="hu-hero__grid-bg hu-hero__grid-bg--blueprint" aria-hidden="true"></div>
		<div class="hu-container hu-hero__container">

			<!-- Left: Leitmotiv -->
			<div>
				<div class="hu-hero__eyebrow">
					<span class="hu-tag">
						<span class="hu-dot hu-dot--live"></span>
						<span class="hu-mono">ANFRAGE-SYSTEM · SOLAR · SHK · DACH</span>
					</span>
				</div>

				<h1 class="hu-display hu-hero__title">
					Weniger Portal-Leads.<br>
					Mehr eigene Anfragen.<br>
					<span class="hu-hero__title-2">Für Ihren Vertrieb.</span>
				</h1>

				<p class="hu-hero__sub">
					Für Solar-, Wärmepumpen- und Speicher-Anbieter im DACH-Raum, die weniger abhängig von Lead-Portalen werden wollen.
					Ich prüfe, wo Website, Vorqualifizierung, Tracking und Werbekanäle aktuell Anfragen verlieren — und welche Hebel zuerst greifen.
				</p>

				<div class="hu-hero__stats">
					<div>
						<div class="hu-stat-num"><?php echo esc_html( $e3_lead_count ); ?></div>
						<div class="hu-stat-label">Anfragen · E3</div>
					</div>
					<div class="hu-stat-divider"></div>
					<div>
						<div class="hu-stat-num"><?php echo esc_html( $e3_sales_conv ); ?></div>
						<div class="hu-stat-label">Abschlussquote · E3</div>
					</div>
					<div class="hu-stat-divider"></div>
					<div>
						<div class="hu-stat-num" style="color:var(--accent)"><?php echo esc_html( $e3_cpl_reduction ); ?></div>
						<div class="hu-stat-label">geringere Kosten/Anfrage · E3</div>
					</div>
				</div>

				<ul class="hu-hero__bullets">
					<li><span class="hu-bullet-dot"></span>Portal-Abhängigkeit, Leadkosten und Anfragequalität prüfen</li>
					<li><span class="hu-bullet-dot"></span>Website, Tracking und Vorqualifizierung als ein System bewerten</li>
					<li><span class="hu-bullet-dot"></span>Fokus: Solar, Wärmepumpe, Speicher und hohe Projektwerte</li>
				</ul>
			</div>

			<!-- Right: animated SVG infrastructure pipeline -->
			<div class="hu-pipeline-wrap" aria-hidden="true">
				<svg class="hu-pipeline" viewBox="0 0 420 560" xmlns="http://www.w3.org/2000/svg" role="img" aria-labelledby="hu-pipeline-title hu-pipeline-desc" preserveAspectRatio="xMidYMid meet">
					<title id="hu-pipeline-title">Nachfrage-Pipeline</title>
					<desc id="hu-pipeline-desc">Schematische Darstellung des autarken Nachfrage-Kraftwerks: Marktnachfrage fließt durch die eigene Filterstrecke in den Vertrieb (CRM).</desc>

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
	     01b / GATEWAY-BAND — Die 3 Routen
	     ═══════════════════════════════════════════════════ -->
	<section class="hu-section hu-section--gateways" id="gateways" data-track-section="01">
		<div class="hu-container">
			<div class="hu-gateways hu-gateways--band hu-reveal" data-track-section="01">
				<?php foreach ( $home_routing_gateways as $key => $gw ) : ?>
					<a class="hu-gateway hu-gateway--<?php echo esc_attr( $key ); ?>"
					   href="<?php echo esc_url( $gw['url'] ); ?>"
					   data-track-action="<?php echo esc_attr( $gw['action'] ); ?>"
					   data-track-category="navigation"
					   data-track-section="01">
						<div class="hu-gateway__head">
							<span class="hu-gateway__badge"><?php echo esc_html( $gw['badge'] ); ?></span>
							<span class="hu-gateway__kicker hu-mono"><?php echo esc_html( $gw['kicker'] ); ?></span>
						</div>
						<h2 class="hu-gateway__title"><?php echo esc_html( $gw['title'] ); ?></h2>
						<p class="hu-gateway__desc"><?php echo esc_html( $gw['desc'] ); ?></p>
						<div class="hu-gateway__foot">
							<span class="hu-gateway__persona"><?php echo esc_html( $gw['persona'] ); ?></span>
							<span class="hu-gateway__cta">
								<?php echo esc_html( $gw['label'] ); ?>
								<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
							</span>
						</div>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<!-- ═══════════════════════════════════════════════════
	     02 / SYSTEM-VERLUST-RASTER
	     ═══════════════════════════════════════════════════ -->
	<section class="hu-section hu-section--cream" id="verlust" data-track-section="02">
		<div class="hu-container">
			<div class="hu-proof-headline hu-reveal">
				<span class="hu-eyebrow">02 / System-Verlust-Raster</span>
				<h2 style="color:var(--ink)">Drei Stellen, an denen Solar- und SHK-Betriebe qualifizierte Anfragen verlieren.</h2>
				<p>Mehr Reichweite hilft wenig, wenn Tracking blind ist, Portal-Leads geteilt werden und kaufnahe Besucher keinen klaren nächsten Schritt finden.</p>
			</div>

			<div class="hu-loss-grid">
				<article class="hu-loss-card hu-reveal">
					<div class="hu-loss-card__num hu-mono">VERLUSTPUNKT 01</div>
					<div class="hu-loss-card__title">Taubes Tracking</div>
					<div class="hu-loss-card__bracket">Die Daten-Lücke</div>
					<p class="hu-loss-card__body">
						Standard-Analytics zählt Klicks — sagt aber nicht, welche Anfrage am Ende zum Auftrag wurde.
						Ergebnis: Budget wird blind auf falsche Kanäle verteilt.
					</p>
				</article>

				<article class="hu-loss-card hu-reveal">
					<div class="hu-loss-card__num hu-mono">VERLUSTPUNKT 02</div>
					<div class="hu-loss-card__title">Gemieteter Grund</div>
					<div class="hu-loss-card__bracket">Das Portal-Dilemma</div>
					<p class="hu-loss-card__body">
						Wer Leads exklusiv bei Drittanbieter-Portalen kauft, teilt sich den Kontakt mit drei
						Mitbewerbern, steht unter Margendruck und besitzt keinen eigenen digitalen Vermögenswert.
					</p>
				</article>

				<article class="hu-loss-card hu-reveal">
					<div class="hu-loss-card__num hu-mono">VERLUSTPUNKT 03</div>
					<div class="hu-loss-card__title">Überladene Website</div>
					<div class="hu-loss-card__bracket">Die Conversion-Bremse</div>
					<p class="hu-loss-card__body">
						Komplexe Themes und unkoordinierte Plugins machen die Website langsam und träge —
						und jagen kaufnahe Besucher in Sackgassen, statt Abschlüsse vorzubereiten.
					</p>
				</article>
			</div>

			<div style="text-align:center;margin-top:48px" class="hu-reveal">
				<a href="<?php echo esc_url( $analysis_url ); ?>" class="hu-btn hu-btn-primary"
				   data-track-action="cta_home_loss_grid_marktcheck" data-track-category="lead_gen" data-track-section="02">
					Diese Lecks am eigenen System prüfen
					<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
				</a>
			</div>
		</div>
	</section>

	<!-- ═══════════════════════════════════════════════════
	     03 / PROZESS-KASKADE — Audit → Orchestrierung → Ownership
	     ═══════════════════════════════════════════════════ -->
	<section class="hu-section" id="prozess" data-track-section="03">
		<div class="hu-container">
			<div class="hu-section-head hu-reveal">
				<span class="hu-eyebrow">03 / Prozess-Kaskade</span>
				<div>
					<h2>Drei Schritte, die das Anfrage-System tragen.</h2>
					<p class="hu-lead">Keine austauschbare Leistungsliste — die Reihenfolge entscheidet, ob aus Reichweite ein Asset wird.</p>
				</div>
			</div>

			<ol class="hu-cascade hu-reveal">
				<?php foreach ( $home_process_cascade as $step ) : ?>
					<li class="hu-cascade__step">
						<span class="hu-cascade__num hu-mono"><?php echo esc_html( $step['num'] ); ?></span>
						<span class="hu-cascade__kicker hu-mono"><?php echo esc_html( $step['kicker'] ); ?></span>
						<h3 class="hu-cascade__title"><?php echo esc_html( $step['title'] ); ?></h3>
						<p class="hu-cascade__desc"><?php echo esc_html( $step['desc'] ); ?></p>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
	</section>

	<!-- ═══════════════════════════════════════════════════
	     04 / 6 SYSTEM-PHASEN — strukturgleich Agentur-Hub
	     ═══════════════════════════════════════════════════ -->
	<section class="hu-section hu-section--cream" id="phasen" data-track-section="04">
		<div class="hu-container">
			<div class="hu-section-head hu-reveal">
				<span class="hu-eyebrow" style="color:var(--ink-2)">04 / System-Phasen</span>
				<div>
					<h2 style="color:var(--ink)">WordPress, SEO, Tracking und CRO — in der richtigen Reihenfolge.</h2>
					<p class="hu-lead" style="color:var(--ink-2)">Sechs Phasen, eine Methodik. Welche Phase zuerst greift, entscheidet der Marktcheck — nicht der Katalog.</p>
				</div>
			</div>

			<ol class="hu-phases hu-phases--light hu-reveal">
				<?php foreach ( $home_system_phases as $phase ) : ?>
					<li class="hu-phase">
						<span class="hu-phase__num hu-mono"><?php echo esc_html( $phase['num'] ); ?></span>
						<h3 class="hu-phase__title"><?php echo esc_html( $phase['title'] ); ?></h3>
						<p class="hu-phase__desc"><?php echo esc_html( $phase['desc'] ); ?></p>
					</li>
				<?php endforeach; ?>
			</ol>

			<div class="hu-phases__cta hu-reveal">
				<a href="<?php echo esc_url( $agentur_hub_url ); ?>" class="hu-btn hu-btn-link"
				   data-track-action="cta_home_phases_agentur" data-track-category="lead_gen" data-track-section="04">
					Vollständige Methodenbibliothek im Agentur-Hub
				</a>
			</div>
		</div>
	</section>

	<!-- ═══════════════════════════════════════════════════
	     05 / E3 PROOF — Validierungsschicht
	     ═══════════════════════════════════════════════════ -->
	<section class="hu-section" id="proof" data-track-section="05">
		<div class="hu-container">
			<div class="hu-proof-headline hu-reveal">
				<span class="hu-eyebrow">05 / Validierung</span>
				<h2>E3 New Energy: von eingekauften Leads zu eigenen qualifizierten Anfragen.</h2>
				<p>In <?php echo esc_html( $e3_timeframe_dat ); ?> sanken die Kosten pro Anfrage von <?php echo esc_html( $e3_cpl_before ); ?> auf <?php echo esc_html( $e3_cpl_after ); ?> — durch eigene Website-Strecke, Vorqualifizierung und belastbares Tracking.</p>
			</div>

			<div class="hu-proof-stats hu-reveal">
				<div class="hu-proof-stat">
					<div class="hu-proof-stat__num"><?php echo esc_html( $e3_timeframe ); ?></div>
					<div class="hu-proof-stat__lbl">Dauer</div>
				</div>
				<div class="hu-proof-stat">
					<div class="hu-proof-stat__num"><?php echo esc_html( $e3_lead_count ); ?></div>
					<div class="hu-proof-stat__lbl">Anfragen</div>
				</div>
				<div class="hu-proof-stat">
					<div class="hu-proof-stat__num"><?php echo esc_html( $e3_sales_conv ); ?></div>
					<div class="hu-proof-stat__lbl">Abschluss</div>
				</div>
				<div class="hu-proof-stat">
					<div class="hu-proof-stat__num" style="color:var(--accent)"><?php echo esc_html( $e3_cpl_reduction ); ?></div>
					<div class="hu-proof-stat__lbl">Kosten / Anfrage</div>
				</div>
			</div>

			<div style="text-align:center;margin-top:48px" class="hu-reveal">
				<a href="<?php echo esc_url( $e3_case_url ); ?>" class="hu-btn hu-btn-primary"
				   data-track-action="cta_home_proof_case_study" data-track-category="lead_gen" data-track-section="05">
					Vollständigen E3-Case analysieren
					<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
				</a>
			</div>
		</div>
	</section>

	<!-- ═══════════════════════════════════════════════════
	     06 / PORTAL-CHAOS vs. DATEN-INTEGRITÄT — visueller Schnitt
	     ═══════════════════════════════════════════════════ -->
	<section class="hu-section hu-system-visual-section" id="vergleich" data-track-section="06">
		<div class="hu-container">
			<div class="hu-proof-headline hu-reveal" style="margin-bottom:64px">
				<span class="hu-eyebrow">06 / Portal-Chaos vs. Daten-Integrität</span>
				<h2>Zwei Systeme. Zwei Ergebnisse.</h2>
				<p style="color:var(--fg-2);font-weight:400">Eine Strecke, an jedem Punkt belegbar — gegen ein Setup, das nur Klicks zählt.</p>
			</div>

			<div class="hu-system-flow hu-reveal">
				<div class="hu-sf-col hu-sf-col--bad">
					<div class="hu-sf-col-head">
						<div class="hu-sf-col-label">AKTUELL</div>
						<div class="hu-sf-col-title">Portal-Chaos</div>
					</div>
					<div class="hu-sf-row">
						<div class="hu-sf-row-icon" aria-hidden="true">×</div>
						<div class="hu-sf-row-content">
							<div class="hu-sf-row-t">Portal-Lead</div>
							<div class="hu-sf-row-d"><?php echo esc_html( $e3_cpl_before ); ?> · 3 Wettbewerber</div>
						</div>
					</div>
					<div class="hu-sf-row">
						<div class="hu-sf-row-icon" aria-hidden="true">×</div>
						<div class="hu-sf-row-content">
							<div class="hu-sf-row-t">Ads ohne Fit-Signal</div>
							<div class="hu-sf-row-d">240 € CPA · Blindflug ohne Attribution</div>
						</div>
					</div>
					<div class="hu-sf-row">
						<div class="hu-sf-row-icon" aria-hidden="true">×</div>
						<div class="hu-sf-row-content">
							<div class="hu-sf-row-t">SEO ohne Conversion</div>
							<div class="hu-sf-row-d">Traffic · 0 attribuierte Anfragen</div>
						</div>
					</div>
					<div class="hu-sf-cost">
						<div class="hu-sf-cost-label">KOSTEN / MONAT</div>
						<div class="hu-sf-cost-num">~ 4.800 €</div>
					</div>
				</div>

				<div class="hu-sf-arrow" aria-hidden="true">
					<svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
						<path d="M5 12h14M13 6l6 6-6 6"/>
					</svg>
				</div>

				<div class="hu-sf-col hu-sf-col--good">
					<div class="hu-sf-col-head">
						<div class="hu-sf-col-label">DATEN-INTEGRITÄT</div>
						<div class="hu-sf-col-title">Eigene Strecke</div>
					</div>
					<div class="hu-sf-row">
						<div class="hu-sf-row-icon hu-sf-row-icon--good">1</div>
						<div class="hu-sf-row-content">
							<div class="hu-sf-row-t">Money Page</div>
							<div class="hu-sf-row-d">Region · Angebot · Beweis</div>
						</div>
					</div>
					<div class="hu-sf-row">
						<div class="hu-sf-row-icon hu-sf-row-icon--good">2</div>
						<div class="hu-sf-row-content">
							<div class="hu-sf-row-t">Gezielte Vorqualifizierung</div>
							<div class="hu-sf-row-d">Effizienzoptimierter B2B-Datenpfad · ohne Streuverlust</div>
						</div>
					</div>
					<div class="hu-sf-row">
						<div class="hu-sf-row-icon hu-sf-row-icon--good">3</div>
						<div class="hu-sf-row-content">
							<div class="hu-sf-row-t">Attributionssicheres Tracking</div>
							<div class="hu-sf-row-d">First-Party-Daten · Server-Side · Consent</div>
						</div>
					</div>
					<div class="hu-sf-result">
						<div class="hu-sf-result-label">ERGEBNIS</div>
						<div class="hu-sf-result-stats">
							<div><span class="hu-sf-result-num"><?php echo esc_html( $e3_lead_count ); ?></span> Anfragen</div>
							<div><span class="hu-sf-result-num"><?php echo esc_html( $e3_sales_conv ); ?></span> Abschluss</div>
							<div><span class="hu-sf-result-num hu-sf-result-num--accent"><?php echo esc_html( $e3_cpl_reduction ); ?></span> Kosten</div>
						</div>
					</div>
				</div>
			</div>

			<div class="hu-sf-footer hu-reveal">
				<div class="hu-sf-footer-l">
					<div class="hu-eyebrow">ZEITRAUM</div>
					<div class="hu-sf-footer-t"><?php echo esc_html( $e3_timeframe ); ?> · E3 New Energy</div>
				</div>
				<div class="hu-sf-footer-r">
					<div class="hu-eyebrow">SETUP</div>
					<div class="hu-sf-footer-t">First-Party · Server-Side Tracking · Consent Mode v2</div>
				</div>
			</div>
		</div>
	</section>

	<!-- ═══════════════════════════════════════════════════
	     06b / KATEGORIE-BRUCH — Architektur statt Webdesign
	     ═══════════════════════════════════════════════════ -->
	<section class="hu-section hu-section--cream" id="kategorie" data-track-section="06b">
		<div class="hu-container" style="max-width:880px">
			<div class="hu-proof-headline hu-reveal">
				<span class="hu-eyebrow" style="color:var(--ink-2)">06b / Kategorie</span>
				<h2 style="color:var(--ink)">Warum dies kein Webdesign-Projekt ist.</h2>
			</div>

			<div class="hu-category-prose hu-reveal" style="max-width:680px;margin:32px auto 0;color:var(--ink-2);font-size:18px;line-height:1.62">
				<p style="margin:0 0 18px">
					Eine Standard-Agentur klickt Plugins und Page-Builder im WordPress-Backend zusammen. Bei jedem Theme- oder Plugin-Update zerschießt eine fremde Hand die LCP-Performance — und kein Mensch im Betrieb kann nachvollziehen, warum die Anfragen plötzlich einbrechen.
				</p>
				<p style="margin:0 0 18px">
					Hier wird anders gearbeitet: System-Logik und Daten-Pfade liegen versioniert im GitHub-Repository. WordPress dient nur als performantes Frontend — kein Page-Builder, kein Plugin-Stack als Software-Fassade. Jede Änderung ist nachvollziehbar, jedes Rollback in Minuten möglich.
				</p>
				<p style="margin:0">
					Das Resultat für den Betrieb: Sie mieten keine Agentur-Software, Sie besitzen den Code. Server-Side Tracking, CRM-Anbindung und Conversion-Pfad gehören Ihnen — übergebbar, prüfbar, unabhängig von mir.
				</p>
			</div>
		</div>
	</section>

	<!-- ═══════════════════════════════════════════════════
	     07 / ÜBER MICH — Trust-Anker (Metaphern-Leck korrigiert)
	     ═══════════════════════════════════════════════════ -->
	<section class="hu-section hu-section--cream" id="about" data-track-section="07">
		<div class="hu-container">
			<div class="hu-about-grid">
				<div class="hu-about-photo hu-reveal">
					<img src="<?php echo esc_url( $portrait_url ); ?>" alt="Haşim Üner" width="380" height="380" loading="lazy">
					<div class="hu-about-photo__tag hu-mono">HANNOVER · 2026</div>
				</div>
				<div class="hu-about-text hu-reveal">
					<span class="hu-eyebrow">07 / Wer steht dahinter</span>
					<h2>Ich baue Anfrage-Systeme,<br>die Vertrieb und Daten verbinden.</h2>
					<p class="hu-lead" style="color:var(--ink-2)">
						Für Betriebe, die nicht dauerhaft Leads mieten wollen, sondern wissen müssen,
						welcher Kanal echte Projekte bringt — und wem die Anfrage am Ende gehört.
					</p>
					<p style="color:var(--ink-2);margin-top:16px">
						Mein Zugang ist Medienwissenschaft, nicht Webdesign. Ich denke zuerst über Sprache,
						Entscheidung und Signal — und erst danach über Code. Seit E3 New Energy als erstem
						Solar-Case ist dokumentiert, wo diese Architektur am stärksten greift.
					</p>
					<ul class="hu-about-bullets">
						<li><span class="hu-about-bullet-dot"></span>Medienwissenschaftliche Architektur — Sprache, Signal, System vor Code</li>
						<li><span class="hu-about-bullet-dot"></span>Fokus Solar &amp; Wärmepumpen — verifizierte Daten-Integrität seit dem E3-Case</li>
						<li><span class="hu-about-bullet-dot"></span>Asset-Ownership statt Drittanbieter-Lock-in</li>
						<li><span class="hu-about-bullet-dot"></span>Founder seit 2026 · Hannover, remote</li>
						<li><span class="hu-about-bullet-dot"></span>Maximal 3 Founding-Partner pro Jahr</li>
					</ul>
					<a href="<?php echo esc_url( $analysis_url ); ?>" class="hu-btn hu-btn-primary"
					   style="margin-top:8px"
					   data-track-action="cta_home_about_marktcheck" data-track-category="lead_gen" data-track-section="07">
						Eigene Region jetzt prüfen
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
					</a>
				</div>
			</div>
		</div>
	</section>

	<!-- ═══════════════════════════════════════════════════
	     08 / FAQ
	     ═══════════════════════════════════════════════════ -->
	<section class="hu-section" id="faq" data-track-section="08">
		<div class="hu-container" style="max-width:880px">
			<div class="hu-proof-headline hu-reveal" style="margin-bottom:48px">
				<span class="hu-eyebrow">08 / FAQ</span>
				<h2>Was Geschäftsführer wirklich fragen.</h2>
			</div>

			<div class="hu-faq">

				<div class="hu-faq-item is-open">
					<button class="hu-faq-item__q" type="button" aria-expanded="true">
						<span>Wie läuft der Marktcheck konkret ab und wie lange dauert er?</span>
						<span class="hu-faq-item__icon" aria-hidden="true">−</span>
					</button>
					<div class="hu-faq-item__a">
						<div class="hu-faq-item__a-inner">Der händisch geprüfte Befund Ihrer Domain und Region kommt innerhalb von 48 Stunden per E-Mail. Keine automatischen Standard-PDFs, sondern eine strategische Einordnung mit klarer Empfehlung.</div>
					</div>
				</div>

				<div class="hu-faq-item">
					<button class="hu-faq-item__q" type="button" aria-expanded="false">
						<span>Bauen Sie nur eine Website oder kümmern Sie sich auch um den Traffic?</span>
						<span class="hu-faq-item__icon" aria-hidden="true">+</span>
					</button>
					<div class="hu-faq-item__a">
						<div class="hu-faq-item__a-inner">Beides — als ein System. Die Website ist nur die Mechanik. Tracking, gezielte Vorqualifizierung und Werbekanal-Steuerung gehören zur selben Architektur, sonst bleibt der Betrieb in Portal-Leads gefangen.</div>
					</div>
				</div>

				<div class="hu-faq-item">
					<button class="hu-faq-item__q" type="button" aria-expanded="false">
						<span>Was kostet das Ganze?</span>
						<span class="hu-faq-item__icon" aria-hidden="true">+</span>
					</button>
					<div class="hu-faq-item__a">
						<div class="hu-faq-item__a-inner">Der Marktcheck ist 0 €. Der Aufbau danach liegt — abhängig vom Setup — bei 13.200 – 19.200 € verteilt auf 24 Monate. Zum Vergleich: Portal-Leads in derselben Größenordnung kosten ca. 26.000 €. Weniger Kosten, dafür ein Asset, das bleibt.</div>
					</div>
				</div>

				<div class="hu-faq-item">
					<button class="hu-faq-item__q" type="button" aria-expanded="false">
						<span>Wie lange dauert es bis zu den ersten Anfragen?</span>
						<span class="hu-faq-item__icon" aria-hidden="true">+</span>
					</button>
					<div class="hu-faq-item__a">
						<div class="hu-faq-item__a-inner">Bei E3 New Energy: erste qualifizierte Anfragen nach 4–6 Wochen, voller Effekt nach 6 Monaten. Schnellere Versprechen sind unseriös — Abschlussquoten verlangen einen sauberen Trichter, nicht nur ein Logo-Update.</div>
					</div>
				</div>

				<div class="hu-faq-item">
					<button class="hu-faq-item__q" type="button" aria-expanded="false">
						<span>Bin ich gebunden?</span>
						<span class="hu-faq-item__icon" aria-hidden="true">+</span>
					</button>
					<div class="hu-faq-item__a">
						<div class="hu-faq-item__a-inner">Nein. Kein Knebelvertrag. Wir starten mit einer Analyse, dann entscheiden beide — Sie, ob es sich lohnt; ich, ob die Architektur passt. Founding-Cohort-Plätze sind auf drei pro Jahr begrenzt.</div>
					</div>
				</div>

				<div class="hu-faq-item">
					<button class="hu-faq-item__q" type="button" aria-expanded="false">
						<span>Arbeiten Sie auch mit bestehenden Websites?</span>
						<span class="hu-faq-item__icon" aria-hidden="true">+</span>
					</button>
					<div class="hu-faq-item__a">
						<div class="hu-faq-item__a-inner">Ja, wenn die Substanz reicht. Manchmal ist ein Money-Page-Slot auf einer bestehenden Domain der schnellere Hebel als ein kompletter Relaunch. Das klärt der Marktcheck.</div>
					</div>
				</div>

			</div>
		</div>
	</section>

	<!-- ═══════════════════════════════════════════════════
	     09 / VERTIEFUNG — Themen-Hub (Array ausgelagert)
	     ═══════════════════════════════════════════════════ -->
	<section class="hu-section hu-section--cream" id="deeper" data-track-section="09" aria-labelledby="hu-deeper-h">
		<div class="hu-container">
			<div class="hu-proof-headline hu-reveal" style="margin-bottom:48px;text-align:center">
				<span class="hu-eyebrow">09 / Vertiefung</span>
				<h2 id="hu-deeper-h">Themen-Hub für tiefere Recherche.</h2>
				<p style="max-width:62ch;margin:16px auto 0;color:var(--ink-2)">
					Acht thematische Seiten zu Strategie, Lead-Qualität, Funnel-Architektur und Markteinordnung. Jede Seite steht für sich, alle führen zurück zum Marktcheck.
				</p>
			</div>

			<div class="hu-deeper-clusters hu-reveal">
				<?php foreach ( $homepage_deeper_clusters as $cluster ) : ?>
					<div class="hu-deeper-cluster">
						<h3 class="hu-deeper-cluster__h"><?php echo esc_html( $cluster['group'] ); ?></h3>
						<ul class="hu-deeper-list">
							<?php foreach ( $cluster['items'] as $item ) : ?>
								<li class="hu-deeper-item">
									<a class="hu-deeper-link"
									   href="<?php echo esc_url( $item['url'] ); ?>"
									   data-track-action="homepage_deeper_link"
									   data-track-category="lead_gen"
									   data-track-section="09">
										<span class="hu-deeper-link__t"><?php echo esc_html( $item['t'] ); ?></span>
										<span class="hu-deeper-link__s"><?php echo esc_html( $item['s'] ); ?></span>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<!-- ═══════════════════════════════════════════════════
	     10 / FINAL ROUTING — die 3 Gateways noch einmal.
	     ═══════════════════════════════════════════════════ -->
	<section class="hu-section" id="cta" data-track-section="10">
		<div class="hu-container">
			<div class="hu-final-routing hu-reveal">
				<div class="hu-final-routing__head">
					<span class="hu-eyebrow" style="color:var(--accent)">10 / Nächster Schritt</span>
					<h2 class="hu-display">Finden Sie heraus, wo Ihr Anfrage-System zuerst Geld verliert.</h2>
					<p>Starten Sie mit dem Marktcheck, prüfen Sie den E3-Case oder gehen Sie direkt in die technische Umsetzung — je nachdem, wo Sie gerade stehen.</p>
				</div>

				<div class="hu-gateways hu-gateways--final" data-track-section="10">
					<?php foreach ( $home_routing_gateways as $key => $gw ) : ?>
						<a class="hu-gateway hu-gateway--<?php echo esc_attr( $key ); ?>"
						   href="<?php echo esc_url( $gw['url'] ); ?>"
						   data-track-action="final_<?php echo esc_attr( $gw['action'] ); ?>"
						   data-track-category="navigation"
						   data-track-section="10">
							<div class="hu-gateway__head">
								<span class="hu-gateway__badge"><?php echo esc_html( $gw['badge'] ); ?></span>
								<span class="hu-gateway__kicker hu-mono"><?php echo esc_html( $gw['kicker'] ); ?></span>
							</div>
							<h3 class="hu-gateway__title"><?php echo esc_html( $gw['title'] ); ?></h3>
							<p class="hu-gateway__desc"><?php echo esc_html( $gw['desc'] ); ?></p>
							<div class="hu-gateway__foot">
								<span class="hu-gateway__persona"><?php echo esc_html( $gw['persona'] ); ?></span>
								<span class="hu-gateway__cta">
									<?php echo esc_html( $gw['label'] ); ?>
									<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
								</span>
							</div>
						</a>
					<?php endforeach; ?>
				</div>

				<div class="hu-final-routing__alt">
					<a href="<?php echo esc_url( $contact_url ); ?>" class="hu-btn hu-btn-link"
					   data-track-action="cta_home_final_contact" data-track-category="lead_gen" data-track-section="10">
						Lieber direkt schreiben? Kontakt mit konkreter Frage
					</a>
				</div>

				<div class="hu-final-routing__signature">— <strong>Haşim Üner</strong> · persönlich, nicht durch ein Vertriebsteam</div>
			</div>
		</div>
	</section>

</div><!-- .hu-hp -->

<?php get_footer(); ?>
