<?php
/**
 * Template Name: WordPress Agentur Hannover
 *
 * Champions-League Light-Layout (Cream + Copper) mit dunklem Hero & finalem CTA.
 * Design-Quelle: Bundle "monepage-wordpress-agentur-hannover" (Claude Design).
 * Styles werden vollstaendig via `assets/css/agentur.css` geladen — kein eingebettetes CSS.
 * Methodenbausteine werden dynamisch aus der Registry (publish-only) gerendert.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$contact_url    = add_query_arg(
	[
		'type'  => 'project',
		'focus' => 'followup_scope',
	],
	home_url( '/kontakt/' )
);
$e3_url         = home_url( '/e3-new-energy/' );
$marktcheck_url = home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' );

// ═══ Vertiefungs-Links für Fokusmarkt-Solar-Brücke ═══
$agentur_solar_deeper = [
	[
		't'   => 'Solar Leads kaufen – Alternative',
		's'   => 'Markteinordnung der Lead-Anbieter und warum eigene Anfrage-Systeme den CPL senken.',
		'url' => home_url( '/solar-leads-kaufen-alternative/' ),
	],
	[
		't'   => 'Server-Side Tracking für B2B',
		's'   => 'GA4, Meta CAPI und Consent Mode v2 auf eigenem Server – Grundlage sauberer Attribution.',
		'url' => home_url( '/server-side-tracking-b2b/' ),
	],
	[
		't'   => 'B2B Solar Leads für Gewerbe',
		's'   => 'Buying-Center-Funnel für gewerbliche Photovoltaik-Projekte ab 50.000 €.',
		'url' => home_url( '/b2b-solar-leads/' ),
	],
	[
		't'   => 'Cost per Lead Photovoltaik',
		's'   => 'Drei Szenarien im CPL-Vergleich und versteckte Kostentreiber bei Portal-Leads.',
		'url' => home_url( '/cost-per-lead-photovoltaik/' ),
	],
	[
		't'   => 'Kunden gewinnen für Solarteure',
		's'   => 'Fünf systematische Hebel für Solar-, Wärmepumpen- und Speicher-Betriebe im DACH-Markt.',
		'url' => home_url( '/kunden-gewinnen-solarteure/' ),
	],
];

// ═══ E3 Proof Canon ═══
$e3            = function_exists( 'hu_e3_canon' ) ? hu_e3_canon() : [];
$e3_metrics    = $e3['metrics'] ?? [];
$e3_cpl_before = $e3_metrics['cpl_before']['display'] ?? '150 €';
$e3_cpl_after  = $e3_metrics['cpl_after']['display'] ?? '22 €';

// ═══ Methodenbausteine aus der Asset Registry ═══
$wgos_assets = function_exists( 'nexus_get_wgos_asset_registry' ) ? nexus_get_wgos_asset_registry() : [];

// Gruppiere Assets nach core_area (nur publish)
$asset_groups = [
	'Strategie'             => [],
	'Technisches Fundament' => [],
	'Messbarkeit'           => [],
	'Sichtbarkeit'          => [],
	'Conversion'            => [],
	'Weiterentwicklung'     => [],
];

$area_meta = [
	'Strategie' => [
		'color' => '#D97757',
		'icon'  => '🎯',
		'desc'  => 'Welche Seite trägt welche Anfrage — und welche nicht.',
	],
	'Technisches Fundament' => [
		'color' => '#1F8A5B',
		'icon'  => '⚡',
		'desc'  => 'Schnell, stabil, wartbar — ohne dass jedes Plugin-Update zur Krise wird.',
	],
	'Messbarkeit' => [
		'color' => '#2A6FDB',
		'icon'  => '📈',
		'desc'  => 'Sie wissen, welcher Kanal echte Projekte bringt — nicht nur Klicks.',
	],
	'Sichtbarkeit' => [
		'color' => '#C05D3F',
		'icon'  => '🔎',
		'desc'  => 'Die Suchanfragen, die kaufnahe Besucher liefern.',
	],
	'Conversion' => [
		'color' => '#8B4789',
		'icon'  => '💡',
		'desc'  => 'Was auf der Seite passieren muss, damit der Besucher jetzt handelt.',
	],
	'Weiterentwicklung' => [
		'color' => '#1F8A5B',
		'icon'  => '🚀',
		'desc'  => 'Welche Änderung erzeugt als Nächstes Wirkung — datenbasiert, nicht aus dem Bauch.',
	],
];

foreach ( $wgos_assets as $slug => $asset ) {
	if ( 'publish' !== ( $asset['status'] ?? '' ) ) {
		continue;
	}
	$core_area = $asset['core_area'] ?? '';
	if ( isset( $asset_groups[ $core_area ] ) ) {
		$asset_groups[ $core_area ][] = $asset;
	} else {
		$asset_groups[ $core_area ]   = isset( $asset_groups[ $core_area ] ) ? $asset_groups[ $core_area ] : [];
		$asset_groups[ $core_area ][] = $asset;
	}
}

// Gesamt-Anzahl Bausteine (publish) fuer Hero-Microcopy
$total_assets = 0;
foreach ( $asset_groups as $items ) {
	$total_assets += count( $items );
}

// ═══ FAQ-Daten (canonical aus helpers.php; JSON-LD schema kommt via inc/org-schema.php) ═══
$faqs = function_exists( 'nexus_get_agentur_faq_items' ) ? nexus_get_agentur_faq_items() : [];

get_header();
?>

<div class="wp-agentur-page-wrapper">

<!-- ═══════════════════════════════════════════════
     SECTION 01 — HERO (Editorial, ruhig, Case-Brief rechts)
     ═══════════════════════════════════════════════ -->
<section class="nx-section wp-agentur-hero wp-agentur-hero--editorial" data-nx-theme="dark" id="hero">
	<div class="ag-hero-bg" aria-hidden="true">
		<div class="ag-hero-bg__warmth"></div>
		<div class="ag-hero-bg__vignette"></div>
		<div class="ag-hero-bg__grain"></div>
	</div>

	<div class="nx-container">
		<div class="ag-hero">
			<header class="ag-hero__top">
				<span class="ag-hero__mark">
					<span class="ag-hero__mark-rule" aria-hidden="true"></span>
					WordPress Agentur Hannover
				</span>
				<span class="ag-hero__meta-top">
					<span class="ag-status-dot" aria-hidden="true"></span>
					Founding&nbsp;Cohort&nbsp;2026
				</span>
			</header>

			<div class="ag-hero__grid">
				<div class="ag-hero__copy">
					<h1 class="ag-hero__title">
						<span class="ag-hero__title-line">WordPress Agentur</span>
						<span class="ag-hero__title-line">Hannover.</span>
						<span class="ag-hero__title-line ag-hero__title-line--em">Für B2B-Anfragen.</span>
					</h1>
					<p class="ag-hero__lede">
						WordPress-Wachstumssystem für anspruchsvolle B2B-Angebote — aus Hannover für den DACH-Raum. Technisches SEO, Core Web Vitals, Tracking und Conversion-Führung als ein System, nicht als lose Agenturleistung.
					</p>

					<div class="ag-hero__actions">
						<a href="<?php echo esc_url( $contact_url ); ?>" class="ag-btn ag-btn--primary" data-track-action="cta_hero_projekt_pruefen" data-track-category="lead_gen" data-track-section="hero">
							<span>Projekt prüfen</span>
							<svg width="18" height="18" viewBox="0 0 20 20" fill="none" aria-hidden="true">
								<path d="M7 4L13 10L7 16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
						</a>
						<a href="#proof" class="ag-btn ag-btn--ghost" data-track-action="cta_hero_e3_case" data-track-category="navigation" data-track-section="hero">
							E3-Case ansehen
						</a>
					</div>

					<p class="ag-hero__fineprint">
						30&nbsp;Min · kein Standard-Pitch · klare Priorität vor Relaunch, SEO oder Umsetzung.
					</p>
				</div>

				<aside class="ag-hero-viz" aria-label="Referenzfall E3 New Energy – Kosten pro qualifizierter Anfrage">
					<header class="ag-hero-viz__head">
						<span class="ag-hero-viz__live">
							<span class="ag-hero-viz__live-dot" aria-hidden="true"></span>
							Verifizierter Referenzfall · E3 New Energy
						</span>
						<h2 class="ag-hero-viz__title">CPL-Senkung in 6 Monaten</h2>
						<p class="ag-hero-viz__sub">Kosten pro qualifizierter B2B-Anfrage — vor und nach dem eigenen Anfrage-System.</p>
					</header>

					<div class="ag-hero-viz__chart" role="img" aria-label="Balkenchart: Kosten pro qualifizierter Anfrage fielen von 150 Euro auf 22 Euro, ein Rückgang um 85 Prozent.">
						<div class="ag-bar ag-bar--before">
							<span class="ag-bar__num">150&nbsp;€</span>
							<span class="ag-bar__track">
								<span class="ag-bar__fill" style="--ag-bar-height: 92%;"></span>
							</span>
							<span class="ag-bar__label">Vorher · Portal-Leads</span>
						</div>
						<div class="ag-bar__arrow" aria-hidden="true">
							<span class="ag-bar__delta">−85 %</span>
							<svg width="36" height="72" viewBox="0 0 36 72" fill="none">
								<path class="ag-bar__arrow-path" d="M6 6 C 22 22, 6 40, 30 64" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-dasharray="120" stroke-dashoffset="120" fill="none"/>
								<path class="ag-bar__arrow-head" d="M22 58 L30 64 L24 70" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
							</svg>
						</div>
						<div class="ag-bar ag-bar--after">
							<span class="ag-bar__num">22&nbsp;€</span>
							<span class="ag-bar__track">
								<span class="ag-bar__fill" style="--ag-bar-height: 14%;"></span>
							</span>
							<span class="ag-bar__label">Nachher · eigenes System</span>
						</div>
					</div>

					<dl class="ag-hero-viz__foot" aria-label="Kennzahlen aus dem E3-Case">
						<div>
							<dt><span class="ag-counter" data-counter-target="1750" data-counter-suffix="+">0</span></dt>
							<dd>Qualifizierte Anfragen</dd>
						</div>
						<div>
							<dt><span class="ag-counter" data-counter-target="12" data-counter-suffix=" %">0</span></dt>
							<dd>Abschlussquote</dd>
						</div>
						<div>
							<dt><span class="ag-counter" data-counter-target="9" data-counter-suffix=" Mon.">0</span></dt>
							<dd>Zeitraum</dd>
						</div>
					</dl>
				</aside>
			</div>
		</div>
	</div>
</section>

<!-- ═══════════════════════════════════════════════
     SECTION 02 — PROOF STRIP (white, 4 metrics)
     ═══════════════════════════════════════════════ -->
<section class="nx-section wp-agentur-proof" data-nx-theme="light" id="zahlen">
	<div class="nx-container">
		<div class="wp-agentur-proof-header">
			<p class="wp-agentur-eyebrow">Proof · E3 New Energy</p>
			<h2 class="nx-headline-section">Keine leeren Versprechen.</h2>
			<p>Der E3-Case ist Referenz, keine pauschale Übertragbarkeitsgarantie. Er zeigt, warum Reihenfolge, Datenqualität und eigene Anfragepfade wichtiger sind als ein weiterer Relaunch.</p>
		</div>

		<div class="wp-agentur-proof-grid">
			<div class="wp-agentur-proof-item">
				<div class="wp-agentur-proof-value">150&nbsp;€ → 22&nbsp;€</div>
				<div class="wp-agentur-proof-label">Kosten pro Anfrage</div>
			</div>
			<div class="wp-agentur-proof-item">
				<div class="wp-agentur-proof-value">1.750+</div>
				<div class="wp-agentur-proof-label">Qualifizierte Anfragen</div>
			</div>
			<div class="wp-agentur-proof-item">
				<div class="wp-agentur-proof-value">12&nbsp;%</div>
				<div class="wp-agentur-proof-label">Abschlussquote</div>
			</div>
			<div class="wp-agentur-proof-item">
				<div class="wp-agentur-proof-value">85&nbsp;%</div>
				<div class="wp-agentur-proof-label">Niedrigere Kosten pro Anfrage</div>
			</div>
		</div>

		<div class="wp-agentur-proof-cta">
			<p>6 Monate, 1.750+ qualifizierte Anfragen, messbare Reduktion der Leadkosten.</p>
			<a href="<?php echo esc_url( $e3_url ); ?>" class="nx-btn nx-btn--ghost" data-track-action="cta_proof_strip_e3" data-track-category="navigation" data-track-section="proof_strip">
				E3-Case im Detail ansehen
			</a>
		</div>
	</div>
</section>

<!-- ═══════════════════════════════════════════════
     SECTION 03 — SPEZIALISIERUNG (3 cards)
     ═══════════════════════════════════════════════ -->
<section class="nx-section wp-agentur-segment-switch" data-nx-theme="light" id="spezialisierung">
	<div class="nx-container">
		<div class="nx-section-header">
			<p class="wp-agentur-eyebrow">Spezialisierung</p>
			<h2 class="nx-headline-section">Keine lokale Allround-Agentur. Ein Spezialist für WordPress als Anfrage-System.</h2>
			<p class="nx-subheadline wp-agentur-section-intro">
				Für Unternehmen in Hannover und im DACH-Raum, deren WordPress-Website nicht nur gut aussehen, sondern kaufnahe Besucher in messbare Anfragen führen soll.
			</p>
		</div>

		<div class="wp-agentur-segment-grid">
			<div class="wp-agentur-segment-card wp-agentur-segment-card--focus">
				<span class="wp-agentur-segment-card__tag">Was</span>
				<h3>WordPress-Systeme, die nachvollziehbar qualifizierte Anfragen erzeugen</h3>
				<p>Angebotsseiten, technisches SEO, Tracking, GA4, Server-Side Tracking, Conversion-Optimierung, Wartung und Ladezeiten-Optimierung — WordPress als ein System, nicht als Baustellensammlung.</p>
			</div>
			<div class="wp-agentur-segment-card">
				<span class="wp-agentur-segment-card__tag">Für wen</span>
				<h3>Anspruchsvolle B2B-Unternehmen mit erklärungsbedürftigem Angebot</h3>
				<p>Der Fokusmarkt ist Solar, Wärmepumpe und Speicher; die Arbeitsweise ist nicht auf Hannover begrenzt.</p>
			</div>
			<div class="wp-agentur-segment-card">
				<span class="wp-agentur-segment-card__tag">Womit</span>
				<h3>Mit einer Anfrage-System-Methode, validiert am E3-Case</h3>
				<p>CPL von 150 € auf 22 € gesenkt, 1.750+ qualifizierte Anfragen in 6 Monaten und 12 % Abschlussquote.</p>
			</div>
		</div>
	</div>
</section>

<!-- ═══════════════════════════════════════════════
     SECTION 04 — SUCHINTENTION (Hannover service fit)
     ═══════════════════════════════════════════════ -->
<section class="nx-section wp-agentur-intent" data-nx-theme="light" id="wordpress-agentur-hannover">
	<div class="nx-container">
		<div class="nx-section-header">
			<p class="wp-agentur-eyebrow">WordPress Agentur Hannover</p>
			<h2 class="nx-headline-section">Wenn die Website Anfragen tragen muss, nicht nur Inhalte.</h2>
			<p class="nx-subheadline wp-agentur-section-intro">
				Der sinnvollste Einstieg ist keine pauschale Relaunch-Schätzung. Zuerst wird geklärt, ob Suchintention, Proof, Tracking und Anfragepfad zusammenpassen.
			</p>
		</div>

		<div class="wp-agentur-intent-grid" aria-label="Leistungsfokus WordPress Agentur Hannover">
			<a class="wp-agentur-intent-card" href="#technisches-seo" data-track-action="cta_intent_technical_seo" data-track-category="navigation" data-track-section="wordpress_agentur_hannover">
				<span class="wp-agentur-intent-card__kicker">SEO &amp; Technik</span>
				<h3>Technisches SEO, Core Web Vitals und saubere Seitenarchitektur</h3>
				<p>Für WordPress-Seiten, die bereits Nachfrage sehen, aber bei Position, Klickrate oder Seitentiefe hängen bleiben.</p>
			</a>
			<a class="wp-agentur-intent-card" href="<?php echo esc_url( home_url( '/server-side-tracking-b2b/' ) ); ?>" data-track-action="cta_intent_tracking" data-track-category="navigation" data-track-section="wordpress_agentur_hannover">
				<span class="wp-agentur-intent-card__kicker">Messbarkeit</span>
				<h3>GA4, Server-Side Tracking und belastbare Attribution</h3>
				<p>Damit Entscheidungen nicht nur auf Rankings, sondern auf qualifizierten Anfragen und echten Projektchancen basieren.</p>
			</a>
			<a class="wp-agentur-intent-card" href="#projekt-pruefen" data-track-action="cta_intent_conversion" data-track-category="lead_gen" data-track-section="wordpress_agentur_hannover">
				<span class="wp-agentur-intent-card__kicker">CRO &amp; Anfragepfad</span>
				<h3>Conversion-Führung für erklärungsbedürftige B2B-Angebote</h3>
				<p>Headline, Proof, Einwände und CTA werden so geordnet, dass kaufnahe Besucher den nächsten Schritt verstehen.</p>
			</a>
			<a class="wp-agentur-intent-card" href="#standort" data-track-action="cta_intent_hannover" data-track-category="navigation" data-track-section="wordpress_agentur_hannover">
				<span class="wp-agentur-intent-card__kicker">Region</span>
				<h3>Reviews in Hannover, Umsetzung remote im DACH-Raum</h3>
				<p>Persönliche Workshops sind in Pattensen und der Region Hannover möglich; die Umsetzung bleibt systematisch und messbar.</p>
			</a>
		</div>
	</div>
</section>

<!-- ═══════════════════════════════════════════════
     SECTION 05 — ANFRAGE-SYSTEM-METHODE (6 Phasen + Vorher/Nachher)
     ═══════════════════════════════════════════════ -->
<span class="wp-agentur-anchor" id="wgos" aria-hidden="true"></span>
<section class="nx-section" data-nx-theme="light" id="methode">
	<div class="nx-container">
		<span class="wp-agentur-anchor" id="technisches-seo" aria-hidden="true"></span>
		<div class="nx-section-header">
			<p class="wp-agentur-eyebrow">Methode</p>
			<h2 class="nx-headline-section">WordPress, SEO, Tracking und CRO in der richtigen Reihenfolge.</h2>
			<p class="nx-subheadline wp-agentur-section-intro">
				Die Methode hinter dem eigenen Anfrage-System ordnet WordPress, SEO, Tracking und CRO nach Wirkung. Nicht die Baustein-Liste entscheidet, sondern die Frage, welcher Eingriff zuerst mehr kaufnahe Klarheit erzeugt.
			</p>
		</div>

		<ol class="wgos-steps wp-agentur-process-grid wp-agentur-process-grid--flow">
			<?php
			$phase_labels = [
				'Strategie'             => [ 'num' => '01', 'title' => 'Strategie', 'icon' => '🎯', 'desc' => 'Welche Seite trägt welche Anfrage — und welche nicht.' ],
				'Technisches Fundament' => [ 'num' => '02', 'title' => 'Fundament', 'icon' => '⚡', 'desc' => 'Schnell, stabil, wartbar — ohne dass jedes Plugin-Update zur Krise wird.' ],
				'Messbarkeit'           => [ 'num' => '03', 'title' => 'Messbarkeit', 'icon' => '📈', 'desc' => 'Sie wissen, welcher Kanal echte Projekte bringt — nicht nur Klicks.' ],
				'Sichtbarkeit'          => [ 'num' => '04', 'title' => 'Sichtbarkeit', 'icon' => '🔎', 'desc' => 'Die Suchanfragen, die kaufnahe Besucher liefern.' ],
				'Conversion'            => [ 'num' => '05', 'title' => 'Conversion', 'icon' => '💡', 'desc' => 'Was auf der Seite passieren muss, damit der Besucher jetzt handelt.' ],
				'Weiterentwicklung'     => [ 'num' => '06', 'title' => 'Weiterentwicklung', 'icon' => '🚀', 'desc' => 'Welche Änderung erzeugt als Nächstes Wirkung — datenbasiert.' ],
			];
			foreach ( $phase_labels as $area => $p ) :
				$count         = count( $asset_groups[ $area ] ?? [] );
				$phase_slug    = sanitize_title( $area );
				$track_section = 'methode_' . $phase_slug;
			?>
				<li>
					<a class="wgos-steps__link" href="#acc-<?php echo esc_attr( $phase_slug ); ?>"
					   data-track-action="cta_method_phase_open"
					   data-track-category="navigation"
					   data-track-section="<?php echo esc_attr( $track_section ); ?>">
						<span class="step-num"><?php echo esc_html( $p['num'] ); ?></span>
						<span class="step-icon" aria-hidden="true"><?php echo esc_html( $p['icon'] ?? '' ); ?></span>
						<h3>
							<?php echo esc_html( $p['title'] ); ?>
							<small><?php echo (int) $count; ?> Bausteine</small>
						</h3>
						<p><?php echo esc_html( $p['desc'] ); ?></p>
						<span class="wgos-steps__more" aria-hidden="true">Bausteine ansehen →</span>
					</a>
				</li>
			<?php endforeach; ?>
		</ol>

		<!-- Vorher / Nachher Vergleich -->
		<div class="wp-agentur-vs" aria-label="Vorher Nachher Vergleich">
			<div class="wp-agentur-vs__head">
				<h3>Der Unterschied</h3>
				<p>Was sich ändert, wenn WordPress als System funktioniert.</p>
			</div>
			<div class="wp-agentur-vs__cols">
				<div class="wp-agentur-vs__col wp-agentur-vs__col--before">
					<div class="wp-agentur-vs__col-head">
						<span class="wp-agentur-vs__badge" aria-hidden="true">×</span>
						<h4>Vorher</h4>
					</div>
					<ul class="wp-agentur-vs__list">
						<li>Viele Besucher, wenig qualifizierte Anfragen.</li>
						<li>Tracking unvollständig oder schlicht falsch.</li>
						<li>Keine Klarheit darüber, welcher Kanal trägt.</li>
						<li>Relaunch ohne messbare Verbesserung.</li>
						<li>Jede Änderung ist Bauchgefühl.</li>
					</ul>
				</div>
				<div class="wp-agentur-vs__col wp-agentur-vs__col--after">
					<div class="wp-agentur-vs__col-head">
						<span class="wp-agentur-vs__badge" aria-hidden="true">✓</span>
						<h4>Nachher</h4>
					</div>
					<ul class="wp-agentur-vs__list">
						<li>Qualifizierte Anfragen messbar gestiegen.</li>
						<li>Saubere Datenbasis für Entscheidungen.</li>
						<li>Klare Zuordnung: Kanal → Anfrage → Projekt.</li>
						<li>Änderungen basieren auf Daten.</li>
						<li>Kontinuierliche Optimierung statt Neubau.</li>
					</ul>
				</div>
			</div>
		</div>

		<p class="wp-agentur-process-link">
			<a href="#asset-uebersicht" data-track-action="cta_method_to_library" data-track-category="navigation" data-track-section="methode">Alle <?php echo (int) $total_assets; ?> Bausteine in der Methodenbibliothek ansehen ↓</a>
		</p>
	</div>
</section>

<!-- ═══════════════════════════════════════════════
     SECTION 06 — METHODENBIBLIOTHEK (Accordion, dynamisch)
     ═══════════════════════════════════════════════ -->
<section class="nx-section" data-nx-theme="light" id="asset-uebersicht">
	<div class="nx-container">
		<div class="nx-section-header">
			<p class="wp-agentur-eyebrow">Methodenbibliothek</p>
			<h2 class="nx-headline-section">Die Bausteine hinter der Anfrage-System-Methode.</h2>
			<p class="nx-subheadline wp-agentur-section-intro">
				Diese Bausteine bilden die Anfrage-System-Methode. Welche zuerst gebaut werden, entscheidet die Analyse — nicht der Katalog.
			</p>
		</div>

		<div class="accordion" id="asset-accordion">
			<?php foreach ( $asset_groups as $area => $assets ) :
				if ( empty( $assets ) ) {
					continue;
				}
				$meta       = $area_meta[ $area ] ?? [ 'color' => '#D97757', 'icon' => '📦', 'desc' => '' ];
				$count      = count( $assets );
				$area_slug  = sanitize_title( $area );
				$area_label = function_exists( 'hue_kernbereich_label' )
					? hue_kernbereich_label( hue_get_wgos_kernbereich_key( $area ) )
					: $area;
			?>
				<div class="acc-item" id="acc-<?php echo esc_attr( $area_slug ); ?>" data-acc="<?php echo esc_attr( $area_slug ); ?>">
					<button class="acc-trigger" aria-expanded="false" aria-controls="body-<?php echo esc_attr( $area_slug ); ?>" data-track-action="toggle_methodenbibliothek" data-track-category="engagement" data-track-section="<?php echo esc_attr( $area_slug ); ?>">
						<div class="acc-trigger-left">
							<div class="acc-icon" style="background: <?php echo esc_attr( $meta['color'] ); ?>15; color: <?php echo esc_attr( $meta['color'] ); ?>;"><?php echo esc_html( $meta['icon'] ); ?></div>
							<div class="acc-info">
								<div class="acc-title"><?php echo esc_html( $area_label ); ?></div>
								<div class="acc-desc"><?php echo esc_html( $meta['desc'] ); ?></div>
							</div>
						</div>
						<div class="acc-meta">
							<span class="acc-count"><?php echo (int) $count; ?> <?php echo 1 === $count ? 'Baustein' : 'Bausteine'; ?></span>
							<div class="acc-chevron" aria-hidden="true">
								<svg width="16" height="16" viewBox="0 0 16 16" fill="none">
									<path d="M4 6L8 10L12 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								</svg>
							</div>
						</div>
					</button>
					<div class="acc-body" id="body-<?php echo esc_attr( $area_slug ); ?>" role="region">
						<div class="acc-body-inner">
							<div class="assets-grid">
								<?php foreach ( $assets as $asset ) :
									$asset_title = esc_html( $asset['title'] ?? '' );
									$asset_desc  = esc_html( $asset['excerpt'] ?? '' );
									$asset_url   = esc_url( function_exists( 'nexus_get_wgos_asset_detail_url' )
										? ( nexus_get_wgos_asset_detail_url( $asset ) ?: nexus_get_wgos_asset_anchor_url( $asset['slug'] ?? '' ) )
										: '#asset-uebersicht'
									);
								?>
									<a href="<?php echo $asset_url; ?>" class="asset-card" data-track-action="cta_asset_card" data-track-category="navigation" data-track-section="methodenbibliothek">
										<div class="asset-header">
											<div class="asset-icon" style="color: <?php echo esc_attr( $meta['color'] ); ?>;">📋</div>
											<div class="asset-title"><?php echo $asset_title; ?></div>
										</div>
										<p class="asset-desc"><?php echo $asset_desc; ?></p>
									</a>
								<?php endforeach; ?>
							</div>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<!-- ═══════════════════════════════════════════════
     SECTION 07 — PROJEKTPRÜFUNG (4 Kauf-Signale)
     ═══════════════════════════════════════════════ -->
<section class="nx-section wp-agentur-project-form" data-nx-theme="light" id="projekt-pruefen">
	<div class="nx-container">
		<div class="nx-section-header wp-agentur-cta-box">
			<p class="wp-agentur-eyebrow">Projektprüfung</p>
			<h2 class="nx-headline-section">Vor Umsetzung prüfe ich vier Kauf-Signale.</h2>
			<p class="nx-subheadline wp-agentur-section-intro">
				Ich prüfe nicht, ob eine neue Website schöner wäre. Ich prüfe, ob Angebot, Nachfrage, Daten und Anfragepfad als System zusammenpassen.
			</p>
		</div>

		<div class="wp-agentur-decision-grid">
			<div class="wp-agentur-decision-card">
				<span class="wp-agentur-decision-card__num">01</span>
				<h3>Angebot</h3>
				<p>Ist der Nutzen so klar, dass ein kaufnaher Besucher den nächsten Schritt versteht?</p>
			</div>
			<div class="wp-agentur-decision-card">
				<span class="wp-agentur-decision-card__num">02</span>
				<h3>Nachfrage</h3>
				<p>Welche Suchanfragen, Kampagnen oder Partnerpfade bringen Besucher mit echter Projektabsicht?</p>
			</div>
			<div class="wp-agentur-decision-card">
				<span class="wp-agentur-decision-card__num">03</span>
				<h3>Datenlage</h3>
				<p>Sind GA4, Consent, Tracking und Server-Side Tracking belastbar genug für Entscheidungen?</p>
			</div>
			<div class="wp-agentur-decision-card">
				<span class="wp-agentur-decision-card__num">04</span>
				<h3>Anfragepfad</h3>
				<p>Welche Information braucht der Besucher jetzt, damit aus Interesse ein qualifizierter Erstkontakt wird?</p>
			</div>
		</div>

		<div class="wp-agentur-quali" id="quali" aria-label="Schneller Kontextcheck zur Vorqualifizierung" data-quali-base="<?php echo esc_attr( $contact_url ); ?>">
			<div class="wp-agentur-quali__step" data-quali-step="1" aria-hidden="false">
				<p class="wp-agentur-quali__kicker">30-Sekunden-Vorqualifizierung · Schritt 1 von 2</p>
				<h3 class="wp-agentur-quali__q">Über welchen Kanal generieren Sie aktuell die meisten qualifizierten B2B-Anfragen?</h3>
				<div class="wp-agentur-quali__options">
					<button type="button" class="wp-agentur-quali__opt" data-quali-channel="seo"
					        data-track-action="quali_channel_seo" data-track-category="lead_gen" data-track-section="quali_step_1">
						<strong>Organische Suche / SEO</strong>
						<span>Google, technisches SEO, kaufnahe Suchanfragen</span>
					</button>
					<button type="button" class="wp-agentur-quali__opt" data-quali-channel="paid"
					        data-track-action="quali_channel_paid" data-track-category="lead_gen" data-track-section="quali_step_1">
						<strong>Bezahlte Kampagnen</strong>
						<span>Google Ads, Meta Ads, LinkedIn Ads</span>
					</button>
					<button type="button" class="wp-agentur-quali__opt" data-quali-channel="portals"
					        data-track-action="quali_channel_portals" data-track-category="lead_gen" data-track-section="quali_step_1">
						<strong>Portale &amp; Lead-Anbieter</strong>
						<span>Portal-Leads, Vermittler, Marktplätze</span>
					</button>
					<button type="button" class="wp-agentur-quali__opt" data-quali-channel="referral"
					        data-track-action="quali_channel_referral" data-track-category="lead_gen" data-track-section="quali_step_1">
						<strong>Empfehlung &amp; Netzwerk</strong>
						<span>Bestandskunden, Partner, persönliches Netzwerk</span>
					</button>
				</div>
				<p class="wp-agentur-quali__hint">Eine Klick-Antwort, kein Pflichtfeld. Im nächsten Schritt nur das, was Sinn ergibt.</p>
			</div>

			<div class="wp-agentur-quali__step" data-quali-step="2" aria-hidden="true" hidden>
				<p class="wp-agentur-quali__kicker">Letzter Schritt · Schritt 2 von 2</p>
				<h3 class="wp-agentur-quali__q">Was ist gerade der größte Engpass?</h3>
				<div class="wp-agentur-quali__options">
					<button type="button" class="wp-agentur-quali__opt" data-quali-pain="quantity"
					        data-track-action="quali_pain_quantity" data-track-category="lead_gen" data-track-section="quali_step_2">
						<strong>Zu wenige Anfragen</strong>
						<span>Reichweite, Sichtbarkeit, Nachfrage</span>
					</button>
					<button type="button" class="wp-agentur-quali__opt" data-quali-pain="quality"
					        data-track-action="quali_pain_quality" data-track-category="lead_gen" data-track-section="quali_step_2">
						<strong>Anfragen, aber unqualifiziert</strong>
						<span>Falsche Zielgruppe, hohe Absprungrate</span>
					</button>
					<button type="button" class="wp-agentur-quali__opt" data-quali-pain="cpl"
					        data-track-action="quali_pain_cpl" data-track-category="lead_gen" data-track-section="quali_step_2">
						<strong>Kosten pro Anfrage zu hoch</strong>
						<span>CPL steigt, Skalierung wird unrentabel</span>
					</button>
					<button type="button" class="wp-agentur-quali__opt" data-quali-pain="tracking"
					        data-track-action="quali_pain_tracking" data-track-category="lead_gen" data-track-section="quali_step_2">
						<strong>Keine belastbaren Daten</strong>
						<span>Tracking, Attribution, GA4 unklar</span>
					</button>
				</div>
				<p class="wp-agentur-quali__hint">Letzte Antwort — direkt im Anschluss landen Sie auf einem vorausgefüllten Kontaktformular.</p>
			</div>

			<div class="wp-agentur-quali__step wp-agentur-quali__step--done" data-quali-step="3" aria-hidden="true" hidden>
				<p class="wp-agentur-quali__kicker">Bereit zur Auswertung</p>
				<h3 class="wp-agentur-quali__q">Ihr Kontext ist erfasst. Im letzten Schritt ordnet Haşim Üner persönlich ein.</h3>
				<p class="wp-agentur-quali__hint">Ihre Antworten werden an das Formular übergeben. Nur geschäftliche E-Mail &amp; Name sind dort verpflichtend.</p>
				<a href="<?php echo esc_url( $contact_url ); ?>" class="nx-btn nx-btn--primary wp-agentur-quali__cta"
				   data-track-action="cta_quali_to_contact" data-track-category="lead_gen" data-track-section="quali_step_3">
					Auswertung anfordern
					<svg width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true">
						<path d="M7 4L13 10L7 16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
				</a>
			</div>
		</div>

		<div class="wp-agentur-actions wp-agentur-actions--center" style="margin-top: 2.5rem;">
			<a href="<?php echo esc_url( $contact_url ); ?>" class="nx-btn nx-btn--ghost" data-track-action="cta_projekt_pruefen_mid" data-track-category="navigation" data-track-section="projektpruefung">
				Lieber direkt zum klassischen Formular
				<svg width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true">
					<path d="M7 4L13 10L7 16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</a>
		</div>
	</div>
</section>

<!-- ═══════════════════════════════════════════════
     SECTION 08 — PROOF E3 DEEP DIVE
     ═══════════════════════════════════════════════ -->
<section class="nx-section" data-nx-theme="light" id="proof">
	<div class="nx-container">
		<div class="nx-section-header">
			<p class="wp-agentur-eyebrow">Proof · E3 New Energy</p>
			<h2 class="nx-headline-section">Was passiert, wenn WordPress, Tracking und Anfrageführung zusammenarbeiten.</h2>
		</div>

		<div class="wp-agentur-case-grid">
			<div class="wp-agentur-case-card">
				<span class="wp-agentur-case-card__eyebrow">Referenz</span>
				<h3>E3 New Energy</h3>
				<p>Der E3-Case ist Referenz, keine pauschale Übertragbarkeitsgarantie. Er zeigt, warum Reihenfolge, Datenqualität und eigene Anfragepfade wichtiger sind als ein weiterer Relaunch.</p>
			</div>
			<div class="wp-agentur-case-card wp-agentur-case-card--result">
				<span class="wp-agentur-case-card__eyebrow">Ergebnis</span>
				<div class="wp-agentur-case-card__metrics">
					<div><strong>150&nbsp;€ → 22&nbsp;€</strong><span>Kosten pro Anfrage</span></div>
					<div><strong>1.750+</strong><span>Qualifizierte Anfragen</span></div>
					<div><strong>12&nbsp;%</strong><span>Abschlussquote</span></div>
				</div>
			</div>
			<div class="wp-agentur-case-card">
				<span class="wp-agentur-case-card__eyebrow">Zeitraum</span>
				<h3>6 Monate</h3>
				<p>In sechs Monaten von portalabhängiger Leadbeschaffung zu einem eigenen, messbaren Anfrage-System.</p>
			</div>
			<div class="wp-agentur-case-card wp-agentur-case-card--cta">
				<span class="wp-agentur-case-card__eyebrow">Nächster Schritt</span>
				<h3>Der ganze Case</h3>
				<p class="wp-agentur-case-card__support">
					Alle Zahlen, die Systemlogik und was sich daraus für Ihre Situation ableiten lässt.
				</p>
				<a href="<?php echo esc_url( $e3_url ); ?>" class="nx-btn nx-btn--ghost wp-agentur-case-card__btn" data-track-action="cta_proof_e3_deep" data-track-category="navigation" data-track-section="proof">
					Methodik im Detail
					<span aria-hidden="true" class="wp-agentur-case-card__btn-icon">→</span>
				</a>
			</div>
		</div>
	</div>
</section>

<!-- ═══════════════════════════════════════════════
     SECTION 09 — ABGRENZUNG (3 negative cards)
     ═══════════════════════════════════════════════ -->
<section class="nx-section" data-nx-theme="light" id="abgrenzung">
	<div class="nx-container">
		<div class="nx-section-header">
			<p class="wp-agentur-eyebrow">Abgrenzung</p>
			<h2 class="nx-headline-section">Für wen diese Seite nicht passt.</h2>
			<p class="nx-subheadline wp-agentur-section-intro">
				Die Seite ist kein Sammelpunkt für kleine Standard-Websites. Sie passt, wenn WordPress ein relevanter B2B-Kanal werden oder bleiben soll.
			</p>
		</div>

		<div class="wp-agentur-pain-grid">
			<div class="wp-agentur-pain-card">
				<div class="wp-agentur-pain-card__icon" aria-hidden="true">×</div>
				<h3>One-Page-Visitenkarten</h3>
				<p>Keine Standard-Websites ohne relevanten Projektumfang.</p>
			</div>
			<div class="wp-agentur-pain-card">
				<div class="wp-agentur-pain-card__icon" aria-hidden="true">×</div>
				<h3>Reine Shop-Projekte</h3>
				<p>Keine Projekte mit reinem Warenkorb-Fokus ohne Lead-Logik, Angebotsstruktur oder B2B-Anfragepfad.</p>
			</div>
			<div class="wp-agentur-pain-card">
				<div class="wp-agentur-pain-card__icon" aria-hidden="true">×</div>
				<h3>Reine Design-Relaunches</h3>
				<p>Keine Relaunches ohne Lead-Logik, Tracking und kaufnahe Angebotsstruktur.</p>
			</div>
		</div>
	</div>
</section>

<!-- ═══════════════════════════════════════════════
     SECTION 10 — FOKUSMARKT ENERGIE
     ═══════════════════════════════════════════════ -->
<section class="nx-section" data-nx-theme="light" id="fokusmarkt">
	<div class="nx-container">
		<div class="nx-section-header">
			<p class="wp-agentur-eyebrow">Fokusmarkt Energie</p>
			<h2 class="nx-headline-section">Solar, Wärmepumpe oder Speicher?</h2>
			<p class="nx-subheadline wp-agentur-section-intro">
				Wenn Ihr Angebot Solar, Wärmepumpe oder Speicher ist, gibt es einen präziseren Einstieg: den Marktcheck, der gezielt auf Lead-Generierung in dieser Branche zugeschnitten ist.
			</p>
		</div>
		<div class="wp-agentur-actions wp-agentur-actions--center">
			<a href="<?php echo esc_url( $marktcheck_url ); ?>" class="nx-btn nx-btn--primary" data-track-action="cta_fokusmarkt_marktcheck" data-track-category="lead_gen" data-track-section="fokusmarkt">
				Zum Marktcheck für Solar &amp; Wärmepumpe
			</a>
		</div>

		<div class="wp-agentur-deeper" aria-label="Vertiefende Themen Solar und Wärmepumpe">
			<p class="wp-agentur-deeper__lead">
				Oder direkt in die Methodik einsteigen:
			</p>
			<ul class="wp-agentur-deeper__list">
				<?php foreach ( $agentur_solar_deeper as $deeper_item ) : ?>
					<li class="wp-agentur-deeper__item">
						<a class="wp-agentur-deeper__link"
						   href="<?php echo esc_url( $deeper_item['url'] ); ?>"
						   data-track-action="cta_fokusmarkt_deeper_link"
						   data-track-category="navigation"
						   data-track-section="fokusmarkt">
							<span class="wp-agentur-deeper__t"><?php echo esc_html( $deeper_item['t'] ); ?></span>
							<span class="wp-agentur-deeper__s"><?php echo esc_html( $deeper_item['s'] ); ?></span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
</section>

<!-- ═══════════════════════════════════════════════
     SECTION 11 — STANDORT (2 cards)
     ═══════════════════════════════════════════════ -->
<section class="nx-section" data-nx-theme="light" id="standort">
	<div class="nx-container">
		<div class="nx-section-header">
			<p class="wp-agentur-eyebrow">Standort</p>
			<h2 class="nx-headline-section">Aus Hannover für den DACH-Raum.</h2>
			<p class="nx-subheadline wp-agentur-section-intro">
				Persönliche Termine, Workshops und Reviews sind in Hannover, Pattensen und der Region Hannover möglich. Die Umsetzung funktioniert genauso sauber remote.
			</p>
		</div>

		<div class="wp-agentur-local-grid">
			<div class="wp-agentur-local-card">
				<h3>Vor Ort in der Region Hannover</h3>
				<p>Persönliche Reviews, Workshops und Strategie-Sessions in Pattensen bei Hannover — oder remote im DACH-Raum.</p>
			</div>
			<div class="wp-agentur-local-card" id="wordpress-wartung">
				<h3>Laufende WordPress-Betreuung</h3>
				<p>Für Bestandskunden mit etabliertem WordPress-System: Wartung, Updates und Weiterentwicklung im Rahmen laufender Mandate.</p>
			</div>
		</div>

		<dl class="wp-agentur-location-facts" aria-label="Standortsignale">
			<div>
				<dt>Standort</dt>
				<dd>Pattensen bei Hannover</dd>
			</div>
			<div>
				<dt>Region</dt>
				<dd>Hannover · Niedersachsen</dd>
			</div>
			<div>
				<dt>Arbeitsweise</dt>
				<dd>Vor Ort oder remote im DACH-Raum</dd>
			</div>
		</dl>

		<p class="wp-agentur-location-note">
			Der Standort Hannover erleichtert persönliche Reviews, Workshops und Strategie-Sessions. Die Umsetzung ist nicht auf regionale Projekte begrenzt.
		</p>
	</div>
</section>

<!-- ═══════════════════════════════════════════════
     SECTION 12 — FAQ (Accordion)
     ═══════════════════════════════════════════════ -->
<section class="nx-section" data-nx-theme="light" id="faq">
	<div class="nx-container">
		<div class="nx-section-header">
			<p class="wp-agentur-eyebrow">FAQ</p>
			<h2 class="nx-headline-section">Häufige Fragen</h2>
		</div>

		<div class="faq-list" id="faq-accordion">
			<?php foreach ( $faqs as $idx => $faq ) :
				$faq_q = $faq['question'] ?? ( $faq['q'] ?? '' );
				$faq_a = $faq['answer']   ?? ( $faq['a'] ?? '' );
			?>
				<div class="faq-item">
					<button class="faq-trigger" aria-expanded="false" data-track-action="toggle_faq" data-track-category="engagement" data-track-section="faq_<?php echo (int) $idx; ?>">
						<span class="faq-question"><?php echo esc_html( $faq_q ); ?></span>
						<div class="faq-chevron" aria-hidden="true">
							<svg width="12" height="12" viewBox="0 0 12 12" fill="none">
								<path d="M3 4.5L6 7.5L9 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
						</div>
					</button>
					<div class="faq-body">
						<div class="faq-body-inner">
							<p class="faq-answer"><?php echo esc_html( $faq_a ); ?></p>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<!-- ═══════════════════════════════════════════════
     SECTION 13 — FINAL CTA (Editorial, ruhig, eine Aufforderung)
     ═══════════════════════════════════════════════ -->
<section class="nx-section ag-close" data-nx-theme="dark" id="cta">
	<div class="ag-close-bg" aria-hidden="true">
		<div class="ag-close-bg__warmth"></div>
		<div class="ag-close-bg__vignette"></div>
		<div class="ag-close-bg__grain"></div>
	</div>

	<div class="nx-container">
		<div class="ag-close__inner">
			<span class="ag-close__kicker">
				<span class="ag-close__kicker-rule" aria-hidden="true"></span>
				Projektprüfung
			</span>

			<h2 class="ag-close__title">
				Klarheit in 30&nbsp;Minuten.
				<span class="ag-close__title-em">Oder ein ehrliches&nbsp;„nein".</span>
			</h2>

			<p class="ag-close__lede">
				Eine Diagnose entlang vier Kauf-Signale — Angebot, Nachfrage, Datenlage, Anfragepfad. Daraus folgt eine klare Empfehlung: Korrektur, Umsetzung, Weiterentwicklung — oder bewusst kein gemeinsamer nächster Schritt.
			</p>

			<div class="ag-close__actions">
				<a href="<?php echo esc_url( $contact_url ); ?>" class="ag-btn ag-btn--primary ag-btn--xl" data-track-action="cta_final_projekt_pruefen" data-track-category="lead_gen" data-track-section="cta">
					<span>Projekt prüfen</span>
					<svg width="18" height="18" viewBox="0 0 20 20" fill="none" aria-hidden="true">
						<path d="M7 4L13 10L7 16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
				</a>
				<a href="<?php echo esc_url( $e3_url ); ?>" class="ag-close__textlink" data-track-action="cta_final_e3_case" data-track-category="navigation" data-track-section="cta">
					E3-Case im Detail ansehen →
				</a>
			</div>

			<footer class="ag-close__sig">
				<span class="ag-close__sig-line" aria-hidden="true"></span>
				<span class="ag-close__sig-name">Haşim Üner</span>
				<span class="ag-close__sig-dot" aria-hidden="true">·</span>
				<span class="ag-close__sig-meta">Pattensen bei Hannover</span>
				<span class="ag-close__sig-dot" aria-hidden="true">·</span>
				<span class="ag-close__sig-meta">
					<span class="ag-status-dot ag-status-dot--inline" aria-hidden="true"></span>
					Verfügbarkeit 2026
				</span>
			</footer>
		</div>
	</div>
</section>

<!-- Sticky Mobile CTA -->
<div class="wp-agentur-sticky-cta" id="wp-agentur-sticky-cta" aria-hidden="true">
	<div class="wp-agentur-sticky-cta__inner">
		<div class="wp-agentur-sticky-cta__label">
			<strong>WordPress als Anfrage-System</strong>
			<span>Projektprüfung in 30 Min · kein Standard-Pitch</span>
		</div>
		<a href="<?php echo esc_url( $contact_url ); ?>" class="nx-btn nx-btn--primary" data-track-action="cta_sticky_projekt_pruefen" data-track-category="lead_gen" data-track-section="sticky_mobile">
			Projekt prüfen
		</a>
	</div>
</div>

</div><!-- /wp-agentur-page-wrapper -->

<?php /* FAQPage JSON-LD wird zentral in inc/org-schema.php aus nexus_get_agentur_faq_items() emittiert. */ ?>

<script>
	// ─── Generic accordion factory ───
	function initAccordion(containerSelector, itemSelector, triggerSelector, bodySelector) {
		var container = document.querySelector(containerSelector);
		if (!container) return;

		container.querySelectorAll(itemSelector).forEach(function(item) {
			var trigger = item.querySelector(triggerSelector);
			var body    = item.querySelector(bodySelector);
			if (!trigger || !body) return;

			trigger.addEventListener('click', function() {
				var isOpen = item.classList.contains('is-open');
				item.classList.toggle('is-open', !isOpen);
				trigger.setAttribute('aria-expanded', String(!isOpen));
			});
		});
	}

	initAccordion('#asset-accordion', '.acc-item',  '.acc-trigger',  '.acc-body');
	initAccordion('#faq-accordion',   '.faq-item',  '.faq-trigger',  '.faq-body');

	// ─── Auto-open accordion target via hash (#acc-<slug>) ───
	(function () {
		function openFromHash() {
			var hash = window.location.hash;
			if (!hash || hash.indexOf('#acc-') !== 0) return;
			var target = document.querySelector(hash);
			if (!target || !target.classList.contains('acc-item')) return;
			var trigger = target.querySelector('.acc-trigger');
			if (!target.classList.contains('is-open')) {
				target.classList.add('is-open');
				if (trigger) trigger.setAttribute('aria-expanded', 'true');
			}
			// Smooth scroll respecting any sticky header offset
			setTimeout(function () {
				target.scrollIntoView({ behavior: 'smooth', block: 'start' });
			}, 60);
		}
		window.addEventListener('hashchange', openFromHash);
		if (window.location.hash.indexOf('#acc-') === 0) {
			// Defer to allow layout to settle
			setTimeout(openFromHash, 120);
		}
	})();

	// ─── Multi-Step Vorqualifizierung (Micro-Commitment) ───
	(function () {
		var root = document.getElementById('quali');
		if (!root) return;

		var base   = root.getAttribute('data-quali-base') || '';
		var state  = { channel: '', pain: '' };
		var steps  = root.querySelectorAll('.wp-agentur-quali__step');

		function show(stepNum) {
			steps.forEach(function (el) {
				var n        = el.getAttribute('data-quali-step');
				var isActive = String(n) === String(stepNum);
				el.hidden   = !isActive;
				el.setAttribute('aria-hidden', isActive ? 'false' : 'true');
				if (isActive) {
					var heading = el.querySelector('.wp-agentur-quali__q');
					if (heading && typeof heading.focus === 'function') {
						heading.setAttribute('tabindex', '-1');
						try { heading.focus({ preventScroll: true }); } catch (e) { heading.focus(); }
					}
				}
			});
		}

		function appendParam(url, key, val) {
			if (!val) return url;
			var sep = url.indexOf('?') === -1 ? '?' : '&';
			return url + sep + encodeURIComponent(key) + '=' + encodeURIComponent(val);
		}

		function finalizeUrl() {
			var url = base;
			url = appendParam(url, 'channel', state.channel);
			url = appendParam(url, 'pain', state.pain);
			var ctaLink = root.querySelector('.wp-agentur-quali__cta');
			if (ctaLink) ctaLink.setAttribute('href', url);
			return url;
		}

		root.querySelectorAll('[data-quali-channel]').forEach(function (btn) {
			btn.addEventListener('click', function () {
				state.channel = btn.getAttribute('data-quali-channel') || '';
				root.querySelectorAll('[data-quali-channel]').forEach(function (b) {
					b.classList.toggle('is-selected', b === btn);
				});
				show(2);
			});
		});

		root.querySelectorAll('[data-quali-pain]').forEach(function (btn) {
			btn.addEventListener('click', function () {
				state.pain = btn.getAttribute('data-quali-pain') || '';
				root.querySelectorAll('[data-quali-pain]').forEach(function (b) {
					b.classList.toggle('is-selected', b === btn);
				});
				finalizeUrl();
				show(3);
			});
		});
	})();

	// ─── Sticky Mobile CTA ───
	(function () {
		var sticky = document.getElementById('wp-agentur-sticky-cta');
		var hero   = document.getElementById('hero');
		var cta    = document.getElementById('cta');
		if (!sticky || !hero) return;

		function update() {
			var heroBottom = hero.getBoundingClientRect().bottom;
			var ctaTop     = cta ? cta.getBoundingClientRect().top : Infinity;
			// Show after the hero is fully out of view, hide before the final CTA appears
			var shouldShow = heroBottom < 0 && ctaTop > window.innerHeight - 80;
			sticky.classList.toggle('is-visible', shouldShow);
			sticky.setAttribute('aria-hidden', shouldShow ? 'false' : 'true');
			document.body.classList.toggle('has-sticky-agentur-cta', shouldShow);
		}

		window.addEventListener('scroll', update, { passive: true });
		window.addEventListener('resize', update);
		update();
	})();

	// ─── Reveal-on-scroll for marked elements ───
	(function () {
		if (!('IntersectionObserver' in window)) return;
		var els = document.querySelectorAll('.wp-agentur-reveal');
		if (!els.length) return;
		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (entry.isIntersecting) {
					entry.target.classList.add('is-revealed');
					io.unobserve(entry.target);
				}
			});
		}, { rootMargin: '0px 0px -80px 0px', threshold: 0.05 });
		els.forEach(function (el) { io.observe(el); });
	})();

	// ─── Animated Counter (Hero-Datenkarte) ───
	(function () {
		var counters = document.querySelectorAll('.ag-counter');
		if (!counters.length) return;

		var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

		function format(n, target) {
			// thousands grouping (de-DE: 1.750)
			var s = String(Math.round(n));
			if (target >= 1000) s = s.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
			return s;
		}

		function run(el) {
			var target = parseInt(el.getAttribute('data-counter-target') || '0', 10);
			var suffix = el.getAttribute('data-counter-suffix') || '';
			if (reduceMotion || !('requestAnimationFrame' in window)) {
				el.textContent = format(target, target) + suffix;
				return;
			}
			var duration = 1400;
			var start = null;
			function tick(t) {
				if (start === null) start = t;
				var p = Math.min(1, (t - start) / duration);
				// easeOutCubic
				var eased = 1 - Math.pow(1 - p, 3);
				el.textContent = format(target * eased, target) + suffix;
				if (p < 1) requestAnimationFrame(tick);
				else el.textContent = format(target, target) + suffix;
			}
			requestAnimationFrame(tick);
		}

		if (!('IntersectionObserver' in window)) {
			counters.forEach(run);
			return;
		}

		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (entry.isIntersecting) {
					run(entry.target);
					io.unobserve(entry.target);
				}
			});
		}, { threshold: 0.4 });
		counters.forEach(function (el) { io.observe(el); });
	})();

	// ─── Hero-Viz Animationstrigger (Balken & Pfeil) ───
	(function () {
		var viz = document.querySelector('.ag-hero-viz');
		if (!viz) return;
		if (!('IntersectionObserver' in window)) { viz.classList.add('is-played'); return; }
		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (entry.isIntersecting) {
					viz.classList.add('is-played');
					io.unobserve(viz);
				}
			});
		}, { threshold: 0.3 });
		io.observe(viz);
	})();
</script>

<?php
get_footer();
