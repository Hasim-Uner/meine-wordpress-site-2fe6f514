<?php
/**
 * Front Page Template — Sovereign Command Center
 *
 * Die Startseite ist Router, nicht Verkaufsfläche. Sie verteilt den
 * B2B-Entscheider strikt über drei architektonische Gateways:
 *   G1  Marktcheck      → /solar-waermepumpen-leadgenerierung/#marktcheck
 *   G2  Agentur-Hub     → /wordpress-agentur-hannover/
 *   G3  E3-Methodik     → /e3-new-energy/
 *
 * SEO-Title/Description werden zentral aus inc/seo-meta.php gesteuert
 * (hu_get_homepage_title / hu_get_homepage_description). JSON-LD läuft
 * zentral über inc/org-schema.php (Organization + hasOfferCatalog) —
 * dieses Template injiziert bewusst kein konkurrierendes Schema.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ── PHP-Datenbindungen ──────────────────────────────────── */
$analysis_url      = function_exists( 'hu_get_request_analysis_url' ) ? hu_get_request_analysis_url() : home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' );
$e3_canon          = function_exists( 'hu_e3_canon' ) ? hu_e3_canon() : [];
$e3_case_url       = isset( $e3_canon['url'] ) ? (string) $e3_canon['url'] : home_url( '/e3-new-energy/' );
$e3_metrics        = isset( $e3_canon['metrics'] ) && is_array( $e3_canon['metrics'] ) ? $e3_canon['metrics'] : [];
$e3_lead_count     = $e3_metrics['lead_count']['display']        ?? '1.750+';
$e3_sales_conv     = $e3_metrics['sales_conversion']['display']  ?? '12 %';
$e3_cpl_reduction  = $e3_metrics['cpl_reduction']['display']     ?? 'über 85 %';
$e3_timeframe      = $e3_metrics['timeframe']['display']         ?? '9 Monate';
$e3_timeframe_dat  = $e3_metrics['timeframe']['display_dative']  ?? '9 Monaten';
$e3_cpl_before     = $e3_metrics['cpl_before']['display']        ?? '150 €';
$e3_cpl_after      = $e3_metrics['cpl_after']['display']         ?? '22 €';
$contact_url       = function_exists( 'nexus_get_contact_url' ) ? nexus_get_contact_url() : home_url( '/kontakt/' );
$agentur_hub_url   = home_url( '/wordpress-agentur-hannover/' );
$portrait_url      = get_stylesheet_directory_uri() . '/assets/img/hasim-portrait.png';

/* ── Routing-Tabelle: 3 Gateways ─────────────────────────── */
$home_routing_gateways = [
	'marktcheck' => [
		'badge'   => 'G1',
		'kicker'  => 'Kanal-Steuerung & Audit',
		'title'   => 'Der 60-Sekunden-Marktcheck',
		'desc'    => 'Identifiziert die unsichtbaren Anfragebremsen auf der aktuellen B2B-Website. Persönliche Rückmeldung statt automatisiertem Tool-Score.',
		'url'     => $analysis_url,
		'label'   => 'Infrastruktur prüfen',
		'persona' => 'Für portalmüde Solar-/SHK-Anbieter',
		'action'  => 'gateway_marktcheck',
	],
	'agentur' => [
		'badge'   => 'G2',
		'kicker'  => 'System-Architektur',
		'title'   => 'WordPress Agentur Hannover',
		'desc'    => 'Technisches Fundament für anspruchsvolle B2B-Systeme: Technisches SEO, Server-Side Tracking und kontrollierte Weiterentwicklung.',
		'url'     => $agentur_hub_url,
		'label'   => 'Agentur-Hub ansteuern',
		'persona' => 'Für B2B-Unternehmen mit Infrastruktur-Bedarf',
		'action'  => 'gateway_agentur',
	],
	'proof' => [
		'badge'   => 'G3',
		'kicker'  => 'Verifizierte Validierung',
		'title'   => 'Die E3-New-Energy-Methodik',
		'desc'    => sprintf(
			'Wie ein eigener, autarker Nachfrage-Funnel die Cost-per-Lead von %s auf %s gesenkt hat — dokumentiert mit echten Vertriebs-Zahlen.',
			$e3_cpl_before,
			$e3_cpl_after
		),
		'url'     => $e3_case_url,
		'label'   => 'Case Study analysieren',
		'persona' => 'Für skeptische Zahlen-Prüfer',
		'action'  => 'gateway_proof',
	],
];

/* ── 6 System-Phasen (strukturgleich zu page-wordpress-agentur.php) ── */
$home_system_phases = [
	[ 'num' => '01', 'title' => 'Strategie',         'desc' => 'Welche Seite trägt welche Anfrage — und welche nicht.' ],
	[ 'num' => '02', 'title' => 'Fundament',         'desc' => 'Schnell, stabil, wartbar — ohne dass jedes Plugin-Update zur Krise wird.' ],
	[ 'num' => '03', 'title' => 'Messbarkeit',       'desc' => 'GA4, Server-Side Tracking und CRM-Rückführung in einer Logik.' ],
	[ 'num' => '04', 'title' => 'Sichtbarkeit',      'desc' => 'Kaufnahe Suchintention abfangen — bevor der Wettbewerb antwortet.' ],
	[ 'num' => '05', 'title' => 'Conversion',        'desc' => 'Klare Nutzerführung im Anfrageprozess — kein Formularballast.' ],
	[ 'num' => '06', 'title' => 'Weiterentwicklung', 'desc' => 'Datenbasierte Skalierung statt Bauchgefühl und Pseudo-Relaunch.' ],
];

/* ── Homepage-Bridge: Themen-Cluster für SEO-Sub-Pages ───── */
$homepage_deeper_clusters = [
	[
		'group' => 'Strategie & Vergleich',
		'items' => [
			[ 't' => 'Solar Leads kaufen – Alternative',     's' => 'Markteinordnung der Lead-Anbieter und Modelle.',           'url' => home_url( '/solar-leads-kaufen-alternative/' ) ],
			[ 't' => 'Eigene Leadgenerierung vs. Portale',   's' => 'TCO-Überschlag über 24/36 Monate und 8-Kriterien-Matrix.', 'url' => home_url( '/eigene-leadgenerierung-vs-portale/' ) ],
		],
	],
	[
		'group' => 'Lead-Qualität & CPL',
		'items' => [
			[ 't' => 'Cost per Lead Photovoltaik',           's' => 'Drei Szenarien im CPL-Vergleich und versteckte Kostentreiber.',     'url' => home_url( '/cost-per-lead-photovoltaik/' ) ],
			[ 't' => 'Qualifizierte PV-Anfragen',            's' => 'Vier Merkmale für hochwertige Solar-Anfragen plus Warnsignale.',   'url' => home_url( '/qualifizierte-pv-anfragen/' ) ],
		],
	],
	[
		'group' => 'Funnel & Tracking',
		'items' => [
			[ 't' => 'Lead-Funnel Solar',                    's' => 'Fünf Stufen einer belastbaren Solar-Funnel-Architektur.',          'url' => home_url( '/lead-funnel-solar/' ) ],
			[ 't' => 'Server-Side Tracking für B2B',         's' => 'GA4, Meta CAPI und Consent Mode v2 auf eigenem Server.',           'url' => home_url( '/server-side-tracking-b2b/' ) ],
		],
	],
	[
		'group' => 'Zielgruppen & Marktbild',
		'items' => [
			[ 't' => 'B2B Solar Leads (Gewerbe)',            's' => 'Buying-Center-Funnel für gewerbliche Photovoltaik-Projekte.',       'url' => home_url( '/b2b-solar-leads/' ) ],
			[ 't' => 'Kunden gewinnen für Solarteure',       's' => 'Mythen-Aufklärung und fünf systematische Hebel im DACH-Mittelstand.', 'url' => home_url( '/kunden-gewinnen-solarteure/' ) ],
		],
	],
];

get_header();
?>

<div class="hu-hp" id="top" data-track-section="homepage">

	<!-- ═══════════════════════════════════════════════════
	     HERO — Sovereign Command Center
	     Links: architektonisches Leitmotiv. Rechts: 3 Gateway-Karten.
	     ═══════════════════════════════════════════════════ -->
	<section class="hu-hero hu-hero--command" id="hero" data-track-section="homepage_hero">
		<div class="hu-hero__grid-bg hu-hero__grid-bg--blueprint" aria-hidden="true"></div>
		<div class="hu-container hu-hero__container">

			<!-- Left: architectural leitmotiv -->
			<div>
				<div class="hu-hero__eyebrow">
					<span class="hu-tag">
						<span class="hu-dot hu-dot--live"></span>
						<span class="hu-mono">INFRASTRUKTUR-ECOSYSTEM · HANNOVER · 2026</span>
					</span>
				</div>

				<h1 class="hu-display hu-hero__title">
					Infrastruktur für<br>
					eigene B2B-Anfragen.<br>
					<span class="hu-hero__title-2">Drei Routen, eine Methodik.</span>
				</h1>

				<p class="hu-hero__claim">
					Eine Startseite, die nicht verkauft — sondern routet.
				</p>

				<p class="hu-hero__sub">
					Eigene Anfragen statt gemieteter Portal-Leads — für Solar-, Wärmepumpen- und Speicher-Anbieter.
					Wählen Sie rechts den Einstieg, der zu Ihrem aktuellen Reifegrad passt: Audit, Architektur oder Validierung.
				</p>

				<div class="hu-hero__stats">
					<div>
						<div class="hu-stat-num"><?php echo esc_html( $e3_lead_count ); ?></div>
						<div class="hu-stat-label">Anfragen · E3</div>
					</div>
					<div class="hu-stat-divider"></div>
					<div>
						<div class="hu-stat-num"><?php echo esc_html( $e3_sales_conv ); ?></div>
						<div class="hu-stat-label">Abschlussquote</div>
					</div>
					<div class="hu-stat-divider"></div>
					<div>
						<div class="hu-stat-num" style="color:var(--accent)"><?php echo esc_html( $e3_cpl_reduction ); ?></div>
						<div class="hu-stat-label">Kosten / Anfrage</div>
					</div>
				</div>

				<ul class="hu-hero__bullets">
					<li><span class="hu-bullet-dot"></span>Persönlich geprüfter Marktcheck statt Software-Score</li>
					<li><span class="hu-bullet-dot"></span>Befund deiner Region innerhalb von 48 Stunden</li>
					<li><span class="hu-bullet-dot"></span>Für Solar, Wärmepumpe und Speicher</li>
				</ul>
			</div>

			<!-- Right: 3 Gateway routing cards -->
			<div class="hu-gateways" aria-label="Drei Einstiege ins Infrastruktur-Ecosystem" data-track-section="homepage_gateway">
				<?php foreach ( $home_routing_gateways as $key => $gw ) : ?>
					<a class="hu-gateway hu-gateway--<?php echo esc_attr( $key ); ?>"
					   href="<?php echo esc_url( $gw['url'] ); ?>"
					   data-track-action="<?php echo esc_attr( $gw['action'] ); ?>"
					   data-track-category="lead_gen"
					   data-track-section="homepage_gateway">
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

		</div><!-- .hu-hero__container -->
	</section>

	<!-- ═══════════════════════════════════════════════════
	     01 / SYSTEM-VERLUST-RASTER
	     Die drei Verlustpunkte, die die Zielgruppe selten in Euro misst.
	     ═══════════════════════════════════════════════════ -->
	<section class="hu-section hu-section--cream" id="verlust" data-track-section="homepage_loss_grid">
		<div class="hu-container">
			<div class="hu-proof-headline hu-reveal">
				<span class="hu-eyebrow">01 / System-Verlust-Raster</span>
				<h2 style="color:var(--ink)">Drei Lecks, die jedes Wachstums-Budget aufzehren.</h2>
				<p>Bevor mehr Reichweite hilft, müssen diese drei Stellen schließen — sonst skaliert nur der Verlust.</p>
			</div>

			<div class="hu-loss-grid">
				<article class="hu-loss-card hu-reveal">
					<div class="hu-loss-card__num hu-mono">VERLUSTPUNKT 01</div>
					<div class="hu-loss-card__title">Taubes Tracking</div>
					<div class="hu-loss-card__bracket">Die Daten-Lücke</div>
					<p class="hu-loss-card__body">
						Standard-Analytics zählt Klicks — aber nicht, welche Anfrage der Vertrieb am Ende wirklich abschließt.
						Ergebnis: Budget wird blind auf falsche Kanäle verteilt.
					</p>
				</article>

				<article class="hu-loss-card hu-reveal">
					<div class="hu-loss-card__num hu-mono">VERLUSTPUNKT 02</div>
					<div class="hu-loss-card__title">Gemieteter Grund</div>
					<div class="hu-loss-card__bracket">Das Portal-Dilemma</div>
					<p class="hu-loss-card__body">
						Wer Leads exklusiv bei Drittanbieter-Portalen kauft, teilt sich den Kontakt mit drei Mitbewerbern,
						steht unter Margendruck und besitzt keinen eigenen digitalen Vermögenswert.
					</p>
				</article>

				<article class="hu-loss-card hu-reveal">
					<div class="hu-loss-card__num hu-mono">VERLUSTPUNKT 03</div>
					<div class="hu-loss-card__title">Funnel-Bloat</div>
					<div class="hu-loss-card__bracket">Die Conversion-Bremse</div>
					<p class="hu-loss-card__body">
						Komplexe Themes und unkoordinierte Plugins verlangsamen die WordPress-Performance (INP/LCP-Verfall)
						und jagen kaufnahe Besucher in Sackgassen — statt Abschlüsse vorzubereiten.
					</p>
				</article>
			</div>

			<div style="text-align:center;margin-top:48px" class="hu-reveal">
				<a href="<?php echo esc_url( $analysis_url ); ?>" class="hu-btn hu-btn-primary"
				   data-track-action="cta_home_loss_grid_marktcheck" data-track-category="lead_gen" data-track-section="homepage_loss_grid">
					Diese Lecks am eigenen System prüfen
					<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
				</a>
			</div>
		</div>
	</section>

	<!-- ═══════════════════════════════════════════════════
	     02 / 6 SYSTEM-PHASEN
	     Strukturgleich zu page-wordpress-agentur.php — kein redundanter Text.
	     ═══════════════════════════════════════════════════ -->
	<section class="hu-section" id="phasen" data-track-section="homepage_phases">
		<div class="hu-container">
			<div class="hu-section-head hu-reveal">
				<span class="hu-eyebrow">02 / System-Phasen</span>
				<div>
					<h2>WordPress, SEO, Tracking und CRO — in der richtigen Reihenfolge.</h2>
					<p class="hu-lead">Sechs Phasen, eine Methodik. Welche Phase zuerst greift, entscheidet der Marktcheck — nicht der Katalog.</p>
				</div>
			</div>

			<ol class="hu-phases hu-reveal">
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
				   data-track-action="cta_home_phases_agentur" data-track-category="lead_gen" data-track-section="homepage_phases">
					Vollständige Methodenbibliothek im Agentur-Hub
				</a>
			</div>
		</div>
	</section>

	<!-- ═══════════════════════════════════════════════════
	     03 / E3 PROOF — kompakt, validiert Gateway 3
	     ═══════════════════════════════════════════════════ -->
	<section class="hu-section" id="proof" data-track-section="homepage_proof">
		<div class="hu-container">
			<div class="hu-proof-headline hu-reveal">
				<span class="hu-eyebrow">03 / Validierung</span>
				<h2>Vom Lead-Einkauf zur eigenen Pipeline.</h2>
				<p><?php echo esc_html( $e3_timeframe ); ?> · E3 New Energy. Eine Referenz, die nicht auf Folien steht.</p>
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
				   data-track-action="cta_home_proof_case_study" data-track-category="lead_gen" data-track-section="homepage_proof">
					Vollständigen E3-Case analysieren
					<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
				</a>
			</div>
		</div>
	</section>

	<!-- ═══════════════════════════════════════════════════
	     04 / ÜBER MICH — Trust-Anker
	     ═══════════════════════════════════════════════════ -->
	<section class="hu-section hu-section--cream" id="about" data-track-section="homepage_about">
		<div class="hu-container">
			<div class="hu-about-grid">
				<div class="hu-about-photo hu-reveal">
					<img src="<?php echo esc_url( $portrait_url ); ?>" alt="Haşim Üner" width="380" height="380" loading="lazy">
					<div class="hu-about-photo__tag hu-mono">HANNOVER · 2026</div>
				</div>
				<div class="hu-about-text hu-reveal">
					<span class="hu-eyebrow">04 / Wer steht dahinter</span>
					<h2>Ich bohre Brunnen.<br>Digital.</h2>
					<p class="hu-lead" style="color:var(--ink-2)">
						Für Solar- und Wärmepumpen-Betriebe, die ihre Anfragen nicht dauerhaft über Portale
						mieten wollen — sondern eine eigene Nachfrage-Infrastruktur aufbauen.
					</p>
					<p style="color:var(--ink-2);margin-top:16px">
						Mein Zugang ist Medienwissenschaft, nicht Webdesign. Ich denke zuerst über Sprache,
						Entscheidung und Signal nach — und erst danach über Code. Seit E3 New Energy als
						erstem Solar-Case weiß ich, wo diese Methode am stärksten greift.
					</p>
					<ul class="hu-about-bullets">
						<li><span class="hu-about-bullet-dot"></span>Medienwissenschaftlicher Hintergrund — Sprache vor Code</li>
						<li><span class="hu-about-bullet-dot"></span>Fokus auf Solar &amp; Wärmepumpen seit dem E3-Case</li>
						<li><span class="hu-about-bullet-dot"></span>Founder seit 2026 · Hannover, remote</li>
						<li><span class="hu-about-bullet-dot"></span>Nimmt 2026 maximal 3 Founding-Partner auf</li>
					</ul>
					<a href="<?php echo esc_url( $analysis_url ); ?>" class="hu-btn hu-btn-primary"
					   style="margin-top:8px"
					   data-track-action="cta_home_about_marktcheck" data-track-category="lead_gen" data-track-section="homepage_about">
						Eigene Region jetzt prüfen
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
					</a>
				</div>
			</div>
		</div>
	</section>

	<!-- ═══════════════════════════════════════════════════
	     05 / FAQ — Einwand-Behandlung vor dem letzten Routing.
	     ═══════════════════════════════════════════════════ -->
	<section class="hu-section" id="faq" data-track-section="homepage_faq">
		<div class="hu-container" style="max-width:880px">
			<div class="hu-proof-headline hu-reveal" style="margin-bottom:48px">
				<span class="hu-eyebrow">05 / FAQ</span>
				<h2>Was Geschäftsführer wirklich fragen.</h2>
			</div>

			<div class="hu-faq">

				<div class="hu-faq-item is-open">
					<button class="hu-faq-item__q" type="button" aria-expanded="true">
						<span>Wie läuft der Marktcheck konkret ab und wie lange dauert er?</span>
						<span class="hu-faq-item__icon" aria-hidden="true">−</span>
					</button>
					<div class="hu-faq-item__a">
						<div class="hu-faq-item__a-inner">Der händisch geprüfte Befund deiner Domain und Region kommt innerhalb von 48 Stunden per E-Mail. Keine automatischen Standard-PDFs, sondern eine strategische Einordnung.</div>
					</div>
				</div>

				<div class="hu-faq-item">
					<button class="hu-faq-item__q" type="button" aria-expanded="false">
						<span>Bauen Sie nur eine Website oder kümmern Sie sich auch um den Traffic?</span>
						<span class="hu-faq-item__icon" aria-hidden="true">+</span>
					</button>
					<div class="hu-faq-item__a">
						<div class="hu-faq-item__a-inner">Beides. Die Website ist nur der Motor. Tracking, Vorqualifizierung und Steuerung der Werbekanäle gehören dazu — sonst bleibt der Betrieb in Portal-Leads gefangen.</div>
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
						<div class="hu-faq-item__a-inner">Bei E3 New Energy: erste qualifizierte Anfragen nach 4–6 Wochen, voller Effekt nach 9 Monaten. Schnellere Versprechen sind unseriös — Leadkosten brauchen einen sauberen Trichter, nicht nur ein Logo-Update.</div>
					</div>
				</div>

				<div class="hu-faq-item">
					<button class="hu-faq-item__q" type="button" aria-expanded="false">
						<span>Bin ich gebunden?</span>
						<span class="hu-faq-item__icon" aria-hidden="true">+</span>
					</button>
					<div class="hu-faq-item__a">
						<div class="hu-faq-item__a-inner">Nein. Es gibt keinen Knebelvertrag. Wir starten mit einer Analyse, dann entscheiden beide — Sie, ob es sich lohnt; ich, ob ich passe. Founding-Cohort-Plätze sind aktuell auf drei pro Jahr begrenzt.</div>
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
	     06 / VERTIEFUNG — Themen-Hub für SEO-Sub-Pages
	     ═══════════════════════════════════════════════════ -->
	<section class="hu-section hu-section--cream" id="deeper" data-track-section="homepage_deeper" aria-labelledby="hu-deeper-h">
		<div class="hu-container">
			<div class="hu-proof-headline hu-reveal" style="margin-bottom:48px;text-align:center">
				<span class="hu-eyebrow">06 / Vertiefung</span>
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
									   data-track-section="homepage_deeper">
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
	     07 / FINAL ROUTING — die 3 Gateways noch einmal.
	     ═══════════════════════════════════════════════════ -->
	<section class="hu-section" id="cta" data-track-section="homepage_final_routing">
		<div class="hu-container">
			<div class="hu-final-routing hu-reveal">
				<div class="hu-final-routing__head">
					<span class="hu-eyebrow" style="color:var(--accent)">07 / Nächster Schritt</span>
					<h2 class="hu-display">Wählen Sie die Route, die zu Ihrem Reifegrad passt.</h2>
					<p>Kein Pitch. Drei klare Einstiege — jede führt zu einem konkreten, prüfbaren Schritt.</p>
				</div>

				<div class="hu-gateways hu-gateways--final" data-track-section="homepage_final_routing">
					<?php foreach ( $home_routing_gateways as $key => $gw ) : ?>
						<a class="hu-gateway hu-gateway--<?php echo esc_attr( $key ); ?>"
						   href="<?php echo esc_url( $gw['url'] ); ?>"
						   data-track-action="final_<?php echo esc_attr( $gw['action'] ); ?>"
						   data-track-category="lead_gen"
						   data-track-section="homepage_final_routing">
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
					   data-track-action="cta_home_final_contact" data-track-category="lead_gen" data-track-section="homepage_final_routing">
						Lieber direkt schreiben? Kontakt mit konkreter Frage
					</a>
				</div>

				<div class="hu-final-routing__signature">— <strong>Haşim Üner</strong> · persönlich, nicht durch ein Vertriebsteam</div>
			</div>
		</div>
	</section>

</div><!-- .hu-hp -->

<?php get_footer(); ?>
