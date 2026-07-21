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
$e3_url         = home_url( '/case-study-solar-leadgenerierung/' );
$marktcheck_url = home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' );
$psi_url        = 'https://pagespeed.web.dev/analysis?url=' . rawurlencode( home_url( '/wordpress-agentur-hannover/' ) );

// ═══ Vertiefungs-Links für Fokusmarkt-Solar-Brücke ═══
$agentur_solar_deeper = [
	[
		't'   => 'Solar Leads kaufen? CPL-Rechnung pro Anfrage',
		's'   => 'Markteinordnung der Lead-Anbieter und warum eigene Anfrage-Systeme den CPL senken.',
		'url' => home_url( '/solar-leads-kaufen-alternative/' ),
	],
	[
		't'   => 'Server-Side Tracking für B2B',
		's'   => 'GA4, Meta CAPI und Consent Mode v2 — DSGVO-konform auf eigenem Server, Grundlage sauberer Attribution.',
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
$e3                = function_exists( 'hu_e3_canon' ) ? hu_e3_canon() : [];
$e3_metrics        = $e3['metrics'] ?? [];
$e3_cpl_before     = $e3_metrics['cpl_before']['display'] ?? '150 €';
$e3_cpl_after      = $e3_metrics['cpl_after']['display'] ?? '22 €';
$e3_cpl_reduction  = $e3_metrics['cpl_reduction']['display'] ?? 'über 85 %';
$e3_cpl_red_count  = $e3_metrics['cpl_reduction']['counter_target'] ?? '85';
$e3_lead_count     = $e3_metrics['lead_count']['display'] ?? '1.750+';
$e3_lead_counter   = $e3_metrics['lead_count']['counter_target'] ?? '1750';
$e3_sales_conv     = $e3_metrics['sales_conversion']['display'] ?? '12 %';
$e3_conv_counter   = $e3_metrics['sales_conversion']['counter_target'] ?? '12';
$e3_timeframe      = $e3_metrics['timeframe']['display'] ?? '6 Monate';
$e3_timeframe_dat  = $e3_metrics['timeframe']['display_dative'] ?? '6 Monaten';
$e3_time_counter   = $e3_metrics['timeframe']['counter_target'] ?? '6';

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

// ═══ Icon-Set (SVG statt Emoji) — Stroke-Style konsistent zur übrigen Site ═══
$wp_agentur_icon_paths = [
	'Strategie'             => '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="4.5"/><circle cx="12" cy="12" r="1.3" fill="currentColor" stroke="none"/>',
	'Technisches Fundament' => '<path d="M13 2 4 14h6l-1 8 9-12h-6z"/>',
	'Messbarkeit'           => '<path d="M4 4v16h16"/><path d="M8 13.5l3-3 3 2 4-6"/>',
	'Sichtbarkeit'          => '<circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/>',
	'Conversion'            => '<path d="M9 18h6"/><path d="M10 21.5h4"/><path d="M12 2.5a6.5 6.5 0 0 0-3.8 11.8c.5.4.8 1 .8 1.7h6c0-.7.3-1.3.8-1.7A6.5 6.5 0 0 0 12 2.5z"/>',
	'Weiterentwicklung'     => '<path d="M3 16l6-6 4 4 8-8"/><path d="M17 6h4v4"/>',
];

if ( ! function_exists( 'hu_agentur_icon_svg' ) ) {
	/**
	 * Rendert ein Methoden-Icon als Inline-SVG (Stroke, currentColor).
	 */
	function hu_agentur_icon_svg( $inner, $size = 24 ) {
		if ( '' === (string) $inner ) {
			return '';
		}
		return '<svg width="' . (int) $size . '" height="' . (int) $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $inner . '</svg>';
	}
}

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

<script>
/* Motion-Gates vor dem ersten Paint der Sektionen:
   .ag-js  = JS vorhanden (Accordion/Quali/Filter dürfen kollabieren),
   .ag-anim = Bewegung erlaubt (kein Reduced Motion, IntersectionObserver da).
   Ohne diese Klassen bleibt jeder Inhalt im sichtbaren Endzustand. */
(function (root) {
	root.classList.add('ag-js');
	try {
		if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches && 'IntersectionObserver' in window) {
			root.classList.add('ag-anim');
		}
	} catch (e) {}
})(document.documentElement);
</script>

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
						<span class="ag-hero__title-line">WordPress Agentur Hannover</span>
						<span class="ag-hero__title-line ag-hero__title-line--em">für messbare B2B-Anfragen.</span>
					</h1>
					<p class="ag-hero__lede">
						Ich prüfe zuerst vier Kauf-Signale — Angebot, Nachfrage, Datenlage, Anfragepfad. Erst dann wird gebaut.
					</p>

					<p class="ag-hero__proofline">
						Diese Seite: PageSpeed 95+ mobil, 100/100 Barrierefreiheit — gebaut mit derselben Methode.
					</p>

					<div class="ag-hero__actions">
						<a href="<?php echo esc_url( $contact_url ); ?>" class="ag-btn ag-btn--primary" data-track-action="cta_hero_projekt_pruefen" data-track-category="lead_gen" data-track-section="hero">
							<span>Anfrage-System prüfen</span>
							<svg width="18" height="18" viewBox="0 0 20 20" fill="none" aria-hidden="true">
								<path d="M7 4L13 10L7 16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
						</a>
						<a href="#proof" class="ag-btn ag-btn--ghost" data-track-action="cta_hero_case_study" data-track-category="navigation" data-track-section="hero">
							Case Study ansehen
						</a>
					</div>

					<p class="ag-hero__fineprint">
						30&nbsp;Min · kein Standard-Pitch · klare Priorität vor Relaunch, SEO oder Umsetzung. Solar, Wärmepumpe oder Speicher? <a href="<?php echo esc_url( $marktcheck_url ); ?>" data-track-action="cta_hero_to_energy_marktcheck" data-track-category="lead_gen" data-track-section="hero">Direkt zum Marktcheck</a>.
					</p>
				</div>

				<aside class="ag-hero-viz" aria-label="Referenzfall mittelständischer PV-Installationsbetrieb – Kosten pro qualifizierter Anfrage">
					<header class="ag-hero-viz__head">
						<span class="ag-hero-viz__live">
							<span class="ag-hero-viz__live-dot" aria-hidden="true"></span>
							Verifizierter Referenzfall · Solar Case Study
						</span>
						<h2 class="ag-hero-viz__title">CPL-Senkung in <?php echo esc_html( $e3_timeframe_dat ); ?></h2>
						<p class="ag-hero-viz__sub">Kosten pro qualifizierter B2B-Anfrage — vor und nach dem eigenen Anfrage-System.</p>
					</header>

					<div class="ag-hero-viz__chart" role="img" aria-label="Balkenchart: Kosten pro qualifizierter Anfrage fielen von 150 Euro auf 22 Euro, ein Rückgang um 85 Prozent.">
						<div class="ag-bar ag-bar--before">
							<span class="ag-bar__num"><?php echo esc_html( $e3_cpl_before ); ?></span>
							<span class="ag-bar__track">
								<span class="ag-bar__fill" style="--ag-bar-height: 92%;"></span>
							</span>
							<span class="ag-bar__label">Vorher · Portal-Leads</span>
						</div>
						<div class="ag-bar__arrow" aria-hidden="true">
							<span class="ag-bar__delta">−<?php echo esc_html( $e3_cpl_red_count ); ?> %</span>
							<svg width="36" height="72" viewBox="0 0 36 72" fill="none">
								<path class="ag-bar__arrow-path" d="M6 6 C 22 22, 6 40, 30 64" stroke="currentColor" stroke-width="2" stroke-linecap="round" fill="none"/>
								<path class="ag-bar__arrow-head" d="M22 58 L30 64 L24 70" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
							</svg>
						</div>
						<div class="ag-bar ag-bar--after">
							<span class="ag-bar__num"><?php echo esc_html( $e3_cpl_after ); ?></span>
							<span class="ag-bar__track">
								<span class="ag-bar__fill" style="--ag-bar-height: 14%;"></span>
							</span>
							<span class="ag-bar__label">Nachher · eigenes System</span>
						</div>
					</div>

					<dl class="ag-hero-viz__foot" aria-label="Kennzahlen aus der Case Study">
						<div>
							<dt><span class="ag-counter"><?php echo esc_html( $e3_lead_count ); ?></span></dt>
							<dd>Qualifizierte Anfragen</dd>
						</div>
						<div>
							<dt><span class="ag-counter"><?php echo esc_html( $e3_sales_conv ); ?></span></dt>
							<dd>Abschlussquote</dd>
						</div>
						<div>
							<dt><span class="ag-counter"><?php echo esc_html( $e3_time_counter ); ?>&nbsp;Mon.</span></dt>
							<dd>Zeitraum</dd>
						</div>
					</dl>
				</aside>
			</div>
		</div>
	</div>
</section>

<!-- ═══════════════════════════════════════════════
     SECTION 01B — LEBENDER BEWEIS (Selbst-Proof, live nachmessbar)
     ═══════════════════════════════════════════════ -->
<section class="nx-section ag-liveproof" data-nx-theme="light" id="lebender-beweis">
	<div class="nx-container">
		<div class="nx-section-header">
			<p class="wp-agentur-eyebrow">Lebender Beweis</p>
			<h2 class="nx-headline-section">Diese Seite praktiziert, was sie verspricht.</h2>
			<p class="nx-subheadline wp-agentur-section-intro">
				Keine gekauften Siegel, keine geliehenen Logos. Stattdessen: diese Seite selbst — jederzeit von Ihnen nachmessbar.
			</p>
		</div>

		<div class="ag-liveproof__grid">
			<div class="ag-liveproof__tile" data-ag-reveal style="--agr-d: 0ms;">
				<span class="ag-liveproof__gauge" aria-hidden="true">
					<svg viewBox="0 0 36 36" width="44" height="44" fill="none">
						<circle cx="18" cy="18" r="15.9" stroke="currentColor" stroke-opacity="0.16" stroke-width="3"/>
						<circle class="ag-liveproof__gauge-arc" cx="18" cy="18" r="15.9" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-dasharray="100 100" transform="rotate(-90 18 18)"/>
					</svg>
				</span>
				<span class="ag-liveproof__value"><span class="ag-counter">100</span><span class="ag-liveproof__max">/100</span></span>
				<span class="ag-liveproof__label">Barrierefreiheit</span>
			</div>

			<div class="ag-liveproof__tile" data-ag-reveal style="--agr-d: 70ms;">
				<span class="ag-liveproof__gauge" aria-hidden="true">
					<svg viewBox="0 0 36 36" width="44" height="44" fill="none">
						<circle cx="18" cy="18" r="15.9" stroke="currentColor" stroke-opacity="0.16" stroke-width="3"/>
						<circle class="ag-liveproof__gauge-arc" cx="18" cy="18" r="15.9" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-dasharray="100 100" transform="rotate(-90 18 18)"/>
					</svg>
				</span>
				<span class="ag-liveproof__value"><span class="ag-counter">100</span><span class="ag-liveproof__max">/100</span></span>
				<span class="ag-liveproof__label">SEO &amp; Best Practices</span>
			</div>

			<div class="ag-liveproof__tile" data-ag-reveal style="--agr-d: 140ms;">
				<span class="ag-liveproof__gauge" aria-hidden="true">
					<svg viewBox="0 0 36 36" width="44" height="44" fill="none">
						<circle cx="18" cy="18" r="15.9" stroke="currentColor" stroke-opacity="0.16" stroke-width="3"/>
						<circle class="ag-liveproof__gauge-arc" cx="18" cy="18" r="15.9" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-dasharray="95 100" transform="rotate(-90 18 18)"/>
					</svg>
				</span>
				<span class="ag-liveproof__value"><span class="ag-counter">95</span><span class="ag-liveproof__max">+</span></span>
				<span class="ag-liveproof__label">PageSpeed mobil, konstant</span>
				<span class="ag-liveproof__note">(zuletzt 96/100, Juli 2026)</span>
			</div>

			<div class="ag-liveproof__tile" data-ag-reveal style="--agr-d: 210ms;">
				<span class="ag-liveproof__gauge ag-liveproof__gauge--live" aria-hidden="true">
					<span class="ag-liveproof__ping"></span>
					<span class="ag-liveproof__ping-core"></span>
				</span>
				<span class="ag-liveproof__value">Aktiv</span>
				<span class="ag-liveproof__label">Server-Side Tracking</span>
				<span class="ag-liveproof__note">(GA4 + sGTM, eigener Server)</span>
			</div>
		</div>

		<div class="ag-liveproof__cta" data-ag-reveal style="--agr-d: 280ms;">
			<a href="<?php echo esc_url( $psi_url ); ?>" class="nx-btn nx-btn--ghost" target="_blank" rel="noopener">
				Diese Seite bei PageSpeed Insights testen
				<svg width="16" height="16" viewBox="0 0 20 20" fill="none" aria-hidden="true">
					<path d="M8 4H4.5A1.5 1.5 0 0 0 3 5.5v10A1.5 1.5 0 0 0 4.5 17h10a1.5 1.5 0 0 0 1.5-1.5V12M12 3h5m0 0v5m0-5-8 8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</a>
			<p class="ag-liveproof__closing">
				Wer Ihnen Ladezeit, Tracking und SEO verkauft, sollte es auf der eigenen Seite beweisen können. Prüfen Sie meine — und danach die Ihrer aktuellen Agentur.
			</p>
		</div>
	</div>
</section>

<!-- ═══════════════════════════════════════════════
     SECTION 02 — PROOF STRIP (white, 4 metrics)
     ═══════════════════════════════════════════════ -->
<section class="nx-section wp-agentur-proof" data-nx-theme="light" id="zahlen">
	<div class="nx-container">
		<div class="wp-agentur-proof-header">
			<p class="wp-agentur-eyebrow">Proof · Solar Case Study</p>
			<h2 class="nx-headline-section">Keine leeren Versprechen.</h2>
			<p>Diese Case Study ist Referenz, keine pauschale Übertragbarkeitsgarantie. Sie zeigt, warum Reihenfolge, Datenqualität und eigene Anfragepfade wichtiger sind als ein weiterer Relaunch.</p>
		</div>

		<div class="wp-agentur-proof-grid">
			<div class="wp-agentur-proof-item">
				<div class="wp-agentur-proof-value"><?php echo esc_html( $e3_cpl_before ); ?> → <?php echo esc_html( $e3_cpl_after ); ?></div>
				<div class="wp-agentur-proof-label">Kosten pro Anfrage</div>
			</div>
			<div class="wp-agentur-proof-item">
				<div class="wp-agentur-proof-value"><?php echo esc_html( $e3_lead_count ); ?></div>
				<div class="wp-agentur-proof-label">Qualifizierte Anfragen</div>
			</div>
			<div class="wp-agentur-proof-item">
				<div class="wp-agentur-proof-value"><?php echo esc_html( $e3_sales_conv ); ?></div>
				<div class="wp-agentur-proof-label">Abschlussquote</div>
			</div>
			<div class="wp-agentur-proof-item">
				<div class="wp-agentur-proof-value"><?php echo esc_html( $e3_cpl_reduction ); ?></div>
				<div class="wp-agentur-proof-label">Niedrigere Kosten pro Anfrage</div>
			</div>
		</div>

		<div class="wp-agentur-proof-cta">
			<p><?php echo esc_html( sprintf( '%s, %s qualifizierte Anfragen, %s niedrigere Kosten pro Anfrage.', $e3_timeframe, $e3_lead_count, $e3_cpl_reduction ) ); ?></p>
			<a href="<?php echo esc_url( $e3_url ); ?>" class="nx-btn nx-btn--ghost" data-track-action="cta_proof_strip_case_study" data-track-category="navigation" data-track-section="proof_strip">
				Case Study im Detail ansehen
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
				<h3>Mit einer Anfrage-System-Methode, validiert an der Case Study</h3>
				<p><?php echo esc_html( sprintf( 'CPL von %s auf %s gesenkt, %s qualifizierte Anfragen in %s und %s Abschlussquote.', $e3_cpl_before, $e3_cpl_after, $e3_lead_count, $e3_timeframe_dat, $e3_sales_conv ) ); ?></p>
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
				<p>WordPress-Suchmaschinenoptimierung für Seiten, die bereits Nachfrage sehen, aber bei Position, Klickrate oder Seitentiefe hängen bleiben.</p>
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
						<span class="step-icon" aria-hidden="true"><?php echo hu_agentur_icon_svg( $wp_agentur_icon_paths[ $area ] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
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

		<!-- Vorher / Nachher Vergleich (mit JS: umschaltbare Bühne, ohne JS: beide Spalten) -->
		<div class="wp-agentur-vs" aria-label="Vorher Nachher Vergleich" data-vs>
			<div class="wp-agentur-vs__head">
				<h3>Der Unterschied</h3>
				<p>Was sich ändert, wenn WordPress als System funktioniert.</p>
			</div>

			<div class="wp-agentur-vs__toggle" data-vs-toggle hidden>
				<div class="wp-agentur-vs__seg" role="group" aria-label="Vergleich umschalten: Vorher oder Nachher">
					<span class="wp-agentur-vs__thumb" aria-hidden="true"></span>
					<button type="button" class="wp-agentur-vs__tab is-active" data-vs-view="before" aria-pressed="true" aria-controls="vs-panel-before">Vorher</button>
					<button type="button" class="wp-agentur-vs__tab" data-vs-view="after" aria-pressed="false" aria-controls="vs-panel-after">Nachher</button>
				</div>
			</div>

			<div class="wp-agentur-vs__cols">
				<div class="wp-agentur-vs__col wp-agentur-vs__col--before" id="vs-panel-before">
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
				<div class="wp-agentur-vs__col wp-agentur-vs__col--after" id="vs-panel-after">
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
			<a href="#asset-uebersicht" data-track-action="cta_method_to_library" data-track-category="navigation" data-track-section="methode">Alle <span class="ag-counter"><?php echo (int) $total_assets; ?></span> Bausteine in der Methodenbibliothek ansehen ↓</a>
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

		<div class="ag-lib-filter" data-lib-filter hidden>
			<div class="ag-lib-filter__chips" role="group" aria-label="Methodenbibliothek nach Kernbereich filtern">
				<button type="button" class="ag-lib-chip is-active" data-lib-cat="all" aria-pressed="true">
					Alle
					<span class="ag-lib-chip__n"><?php echo (int) $total_assets; ?></span>
				</button>
				<?php foreach ( $asset_groups as $area => $assets ) :
					if ( empty( $assets ) ) {
						continue;
					}
					$chip_slug  = sanitize_title( $area );
					$chip_label = function_exists( 'hue_kernbereich_label' )
						? hue_kernbereich_label( hue_get_wgos_kernbereich_key( $area ) )
						: $area;
				?>
					<button type="button" class="ag-lib-chip" data-lib-cat="<?php echo esc_attr( $chip_slug ); ?>" aria-pressed="false">
						<?php echo esc_html( $chip_label ); ?>
						<span class="ag-lib-chip__n"><?php echo (int) count( $assets ); ?></span>
					</button>
				<?php endforeach; ?>
			</div>
			<p class="ag-lib-filter__status" role="status" data-lib-status><?php echo (int) $total_assets; ?> von <?php echo (int) $total_assets; ?> Bausteinen sichtbar</p>
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
							<div class="acc-icon" style="background: <?php echo esc_attr( $meta['color'] ); ?>15; color: <?php echo esc_attr( $meta['color'] ); ?>;"><?php echo hu_agentur_icon_svg( $wp_agentur_icon_paths[ $area ] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
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
					<div class="acc-body" id="body-<?php echo esc_attr( $area_slug ); ?>">
						<div class="acc-body-inner">
							<div class="assets-grid">
								<?php foreach ( $assets as $asset_i => $asset ) :
									$asset_title = esc_html( $asset['title'] ?? '' );
									$asset_desc  = esc_html( $asset['excerpt'] ?? '' );
									$asset_url   = esc_url( function_exists( 'nexus_get_wgos_asset_detail_url' )
										? ( nexus_get_wgos_asset_detail_url( $asset ) ?: nexus_get_wgos_asset_anchor_url( $asset['slug'] ?? '' ) )
										: '#asset-uebersicht'
									);
								?>
									<a href="<?php echo $asset_url; // raw-ok pre-escaped via esc_url at assignment ?>" class="asset-card" style="--agc-i: <?php echo (int) min( (int) $asset_i, 9 ); ?>;" data-track-action="cta_asset_card" data-track-category="navigation" data-track-section="methodenbibliothek">
										<div class="asset-header">
											<div class="asset-icon" style="color: <?php echo esc_attr( $meta['color'] ); ?>;"><?php echo hu_agentur_icon_svg( '<path d="M7 3h7l4 4v14H7z"/><path d="M14 3v4h4"/><path d="M9.5 12h5M9.5 15.5h5"/>', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
											<div class="asset-title"><?php echo $asset_title; // raw-ok pre-escaped via esc_html at assignment ?></div>
										</div>
										<p class="asset-desc"><?php echo $asset_desc; // raw-ok pre-escaped via esc_html at assignment ?></p>
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

		<?php
		// Kauf-Signal-Strecke als Inline-SVG (Desktop horizontal, Mobil vertikal).
		// Endzustand steht vollständig im Markup; .is-live startet nur die Choreografie.
		$agsg_signals = [
			[ 'num' => '01', 'label' => 'Angebot' ],
			[ 'num' => '02', 'label' => 'Nachfrage' ],
			[ 'num' => '03', 'label' => 'Datenlage' ],
			[ 'num' => '04', 'label' => 'Anfragepfad' ],
		];
		$agsg_aria = 'Vier Kauf-Signale als zusammenhängendes System: 01 Angebot, 02 Nachfrage, 03 Datenlage, 04 Anfragepfad — erst wenn alle vier tragen, wird gebaut.';
		?>
		<div class="ag-signal" data-ag-signal>
			<svg class="ag-signal__svg ag-signal__svg--wide" viewBox="0 0 1000 230" role="img" aria-label="<?php echo esc_attr( $agsg_aria ); ?>" focusable="false">
				<?php foreach ( [ [ 154, 122 ], [ 364, 122 ], [ 574, 122 ], [ 784, 24 ] ] as $i => $seg ) : ?>
					<rect class="agsg-seg agsg-seg--x agsg-seg--<?php echo (int) ( $i + 1 ); ?>" x="<?php echo (int) $seg[0]; ?>" y="116.75" width="<?php echo (int) $seg[1]; ?>" height="2.5" rx="1.25"/>
				<?php endforeach; ?>
				<path class="agsg-head" d="M804 110 L812 118 L804 126"/>
				<?php foreach ( $agsg_signals as $i => $sig ) :
					$cx = 110 + ( $i * 210 );
				?>
					<g class="agsg-node agsg-node--<?php echo (int) ( $i + 1 ); ?>">
						<circle class="agsg-node__ring" cx="<?php echo (int) $cx; ?>" cy="118" r="36"/>
						<text class="agsg-node__num" x="<?php echo (int) $cx; ?>" y="125" text-anchor="middle"><?php echo esc_html( $sig['num'] ); ?></text>
						<text class="agsg-node__label" x="<?php echo (int) $cx; ?>" y="192" text-anchor="middle"><?php echo esc_html( $sig['label'] ); ?></text>
					</g>
					<g class="agsg-tick agsg-tick--<?php echo (int) ( $i + 1 ); ?>" transform="translate(<?php echo (int) ( $cx + 27 ); ?> 91)">
						<g class="agsg-tick__in">
							<circle class="agsg-tick__bg" cx="0" cy="0" r="12"/>
							<path class="agsg-tick__check" d="M-4.6 0.2 L-1.4 3.4 L4.8 -3.2"/>
						</g>
					</g>
				<?php endforeach; ?>
				<g class="agsg-chip">
					<rect x="818" y="90" width="168" height="56" rx="28"/>
					<text x="902" y="123.5" text-anchor="middle">Erst dann wird gebaut.</text>
				</g>
				<circle class="agsg-dot agsg-dot--x" cx="154" cy="118" r="5"/>
			</svg>

			<svg class="ag-signal__svg ag-signal__svg--stack" viewBox="0 0 380 640" role="img" aria-label="<?php echo esc_attr( $agsg_aria ); ?>" focusable="false">
				<?php foreach ( [ [ 126, 48 ], [ 258, 48 ], [ 390, 48 ], [ 522, 28 ] ] as $i => $seg ) : ?>
					<rect class="agsg-seg agsg-seg--y agsg-seg--<?php echo (int) ( $i + 1 ); ?>" x="62.75" y="<?php echo (int) $seg[0]; ?>" width="2.5" height="<?php echo (int) $seg[1]; ?>" rx="1.25"/>
				<?php endforeach; ?>
				<path class="agsg-head" d="M56 546 L64 554 L72 546"/>
				<?php foreach ( $agsg_signals as $i => $sig ) :
					$cy = 84 + ( $i * 132 );
				?>
					<g class="agsg-node agsg-node--<?php echo (int) ( $i + 1 ); ?>">
						<circle class="agsg-node__ring" cx="64" cy="<?php echo (int) $cy; ?>" r="34"/>
						<text class="agsg-node__num" x="64" y="<?php echo (int) ( $cy + 7 ); ?>" text-anchor="middle"><?php echo esc_html( $sig['num'] ); ?></text>
						<text class="agsg-node__label" x="124" y="<?php echo (int) ( $cy + 7 ); ?>" text-anchor="start"><?php echo esc_html( $sig['label'] ); ?></text>
					</g>
					<g class="agsg-tick agsg-tick--<?php echo (int) ( $i + 1 ); ?>" transform="translate(88 <?php echo (int) ( $cy - 24 ); ?>)">
						<g class="agsg-tick__in">
							<circle class="agsg-tick__bg" cx="0" cy="0" r="11"/>
							<path class="agsg-tick__check" d="M-4.2 0.2 L-1.3 3 L4.4 -2.9"/>
						</g>
					</g>
				<?php endforeach; ?>
				<g class="agsg-chip">
					<rect x="40" y="560" width="300" height="54" rx="27"/>
					<text x="190" y="592" text-anchor="middle">Erst dann wird gebaut.</text>
				</g>
				<circle class="agsg-dot agsg-dot--y" cx="64" cy="126" r="5"/>
			</svg>
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
     SECTION 08 — PROOF CASE STUDY DEEP DIVE
     ═══════════════════════════════════════════════ -->
<section class="nx-section" data-nx-theme="light" id="proof">
	<div class="nx-container">
		<div class="nx-section-header">
			<p class="wp-agentur-eyebrow">Proof · Solar Case Study</p>
			<h2 class="nx-headline-section">Was passiert, wenn WordPress, Tracking und Anfrageführung zusammenarbeiten.</h2>
		</div>

		<div class="wp-agentur-case-grid">
			<div class="wp-agentur-case-card">
				<span class="wp-agentur-case-card__eyebrow">Referenz</span>
				<h3>Mittelständischer PV-Installationsbetrieb</h3>
				<p>Ein Energie-Anbieter, der von eingekauften Portal-Leads auf ein eigenes Anfrage-System umgestellt hat — WordPress, Server-Side Tracking und Vorqualifizierung als ein zusammenhängendes System statt als Einzelmaßnahmen.</p>
			</div>
			<div class="wp-agentur-case-card wp-agentur-case-card--result">
				<span class="wp-agentur-case-card__eyebrow">Ergebnis</span>
				<div class="wp-agentur-case-card__metrics">
					<div><strong><?php echo esc_html( $e3_cpl_before ); ?> → <?php echo esc_html( $e3_cpl_after ); ?></strong><span>Kosten pro Anfrage</span></div>
					<div><strong><?php echo esc_html( $e3_lead_count ); ?></strong><span>Qualifizierte Anfragen</span></div>
					<div><strong><?php echo esc_html( $e3_sales_conv ); ?></strong><span>Abschlussquote</span></div>
				</div>
			</div>
			<div class="wp-agentur-case-card">
				<span class="wp-agentur-case-card__eyebrow">Zeitraum</span>
				<h3><?php echo esc_html( $e3_timeframe ); ?></h3>
				<p>In <?php echo esc_html( $e3_timeframe_dat ); ?> von portalabhängiger Leadbeschaffung zu einem eigenen, messbaren Anfrage-System.</p>
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
				<h3>WordPress-Wartung in Hannover</h3>
				<p>Für Bestandskunden mit etabliertem WordPress-System: Wartungsvertrag mit Updates, Monitoring und Weiterentwicklung im Rahmen laufender Mandate.</p>
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
			<h2 class="nx-headline-section">Häufige Fragen vor der Projektprüfung.</h2>
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
				<a href="<?php echo esc_url( $e3_url ); ?>" class="ag-close__textlink" data-track-action="cta_final_case_study" data-track-category="navigation" data-track-section="cta">
					Case Study im Detail ansehen →
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
	// Motion-Gate: gesetzt im Kopf des Wrappers, nur ohne Reduced Motion + mit IO.
	var AG_MOTION = document.documentElement.classList.contains('ag-anim');

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

	// ─── Count-up: Endwert steht serverseitig im Markup ("1.750+", "12 %", "39"),
	//     JS liest ihn, zählt hoch und stellt den Originaltext exakt wieder her. ───
	(function () {
		var counters = document.querySelectorAll('.ag-counter');
		if (!counters.length || !AG_MOTION || typeof window.requestAnimationFrame !== 'function') return;

		function animate(el) {
			var original = el.textContent;
			var match = /^([^0-9]*)([0-9][0-9.,]*)(.*)$/.exec(original.trim());
			if (!match) return;
			var target = parseInt(match[2].replace(/[.,]/g, ''), 10);
			if (!isFinite(target) || target <= 0) return;
			var prefix = match[1];
			var suffix = match[3];
			var t0 = null;
			var duration = 1200;
			function tick(now) {
				if (t0 === null) t0 = now;
				var p = Math.min(1, (now - t0) / duration);
				var eased = 1 - Math.pow(1 - p, 3);
				if (p < 1) {
					el.textContent = prefix + Math.round(target * eased).toLocaleString('de-DE') + suffix;
					window.requestAnimationFrame(tick);
				} else {
					el.textContent = original;
				}
			}
			window.requestAnimationFrame(tick);
		}

		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (!entry.isIntersecting) return;
				io.unobserve(entry.target);
				animate(entry.target);
			});
		}, { threshold: 0.4 });
		counters.forEach(function (el) { io.observe(el); });
	})();

	// ─── Scroll-Reveal [data-ag-reveal] + Kauf-Signal-Choreografie ───
	//     Versteckte Ausgangszustände existieren nur unter html.ag-anim;
	//     bereits sichtbare Elemente werden vor dem Beobachten markiert,
	//     damit ein Reload mitten auf der Seite nicht blinkt.
	(function () {
		if (!AG_MOTION) return;
		var els = [].slice.call(document.querySelectorAll('[data-ag-reveal]'));
		var signal = document.querySelector('[data-ag-signal]');
		var vh = window.innerHeight || document.documentElement.clientHeight || 0;
		var pending = [];

		els.forEach(function (el) {
			if (el.getBoundingClientRect().top < vh * 0.88) el.classList.add('is-in');
			else pending.push(el);
		});

		var signalPending = false;
		if (signal) {
			if (signal.getBoundingClientRect().top < vh * 0.85) signal.classList.add('is-live');
			else signalPending = true;
		}

		if (!pending.length && !signalPending) return;
		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (!entry.isIntersecting) return;
				io.unobserve(entry.target);
				entry.target.classList.add(entry.target === signal ? 'is-live' : 'is-in');
			});
		}, { rootMargin: '0px 0px -10% 0px' });
		pending.forEach(function (el) { io.observe(el); });
		if (signalPending) io.observe(signal);
	})();

	// ─── „Der Unterschied": Vorher/Nachher-Bühne (Progressive Enhancement) ───
	(function () {
		var root = document.querySelector('[data-vs]');
		if (!root) return;
		var toggle = root.querySelector('[data-vs-toggle]');
		var tabs = [].slice.call(root.querySelectorAll('[data-vs-view]'));
		var panels = {
			before: root.querySelector('#vs-panel-before'),
			after: root.querySelector('#vs-panel-after')
		};
		if (!toggle || tabs.length !== 2 || !panels.before || !panels.after) return;

		function setView(view) {
			tabs.forEach(function (tab) {
				var active = tab.getAttribute('data-vs-view') === view;
				tab.classList.toggle('is-active', active);
				tab.setAttribute('aria-pressed', active ? 'true' : 'false');
			});
			Object.keys(panels).forEach(function (key) {
				var active = key === view;
				panels[key].classList.toggle('is-active', active);
				panels[key].setAttribute('aria-hidden', active ? 'false' : 'true');
			});
			root.classList.toggle('is-after', view === 'after');
		}

		toggle.hidden = false;
		root.classList.add('is-enhanced');
		setView('before');

		tabs.forEach(function (tab) {
			tab.addEventListener('click', function () {
				setView(tab.getAttribute('data-vs-view'));
			});
			tab.addEventListener('keydown', function (ev) {
				if (ev.key !== 'ArrowLeft' && ev.key !== 'ArrowRight') return;
				ev.preventDefault();
				var next = ev.key === 'ArrowRight' ? 'after' : 'before';
				setView(next);
				(next === 'after' ? tabs[1] : tabs[0]).focus();
			});
		});
	})();

	// ─── Methodenbibliothek: Kategorie-Filter + Status (aria-live) ───
	(function () {
		var filter = document.querySelector('[data-lib-filter]');
		var acc = document.getElementById('asset-accordion');
		if (!filter || !acc) return;
		var chips = [].slice.call(filter.querySelectorAll('[data-lib-cat]'));
		var items = [].slice.call(acc.querySelectorAll('.acc-item'));
		var status = filter.querySelector('[data-lib-status]');
		if (!chips.length || !items.length) return;

		var total = 0;
		var counts = {};
		items.forEach(function (item) {
			var n = item.querySelectorAll('.asset-card').length;
			counts[item.getAttribute('data-acc')] = n;
			total += n;
		});

		function openItem(item) {
			if (item.classList.contains('is-open')) return;
			item.classList.add('is-open');
			var trigger = item.querySelector('.acc-trigger');
			if (trigger) trigger.setAttribute('aria-expanded', 'true');
		}

		function apply(cat) {
			var visible = 0;
			chips.forEach(function (chip) {
				var active = chip.getAttribute('data-lib-cat') === cat;
				chip.classList.toggle('is-active', active);
				chip.setAttribute('aria-pressed', active ? 'true' : 'false');
			});
			items.forEach(function (item) {
				var slug = item.getAttribute('data-acc');
				var show = cat === 'all' || slug === cat;
				item.hidden = !show;
				if (show) visible += counts[slug] || 0;
				if (show && cat !== 'all') openItem(item);
			});
			if (status) status.textContent = visible + ' von ' + total + ' Bausteinen sichtbar';
		}

		// Deeplinks (#acc-…) dürfen nie in einen weggefilterten Bereich laufen.
		function ensureHashVisible() {
			var hash = window.location.hash;
			if (!hash || hash.indexOf('#acc-') !== 0) return;
			var target;
			try { target = document.querySelector(hash); } catch (e) { return; }
			if (target && target.hidden) apply('all');
		}

		filter.hidden = false;
		chips.forEach(function (chip) {
			chip.addEventListener('click', function () {
				apply(chip.getAttribute('data-lib-cat'));
			});
		});
		window.addEventListener('hashchange', ensureHashVisible);
		ensureHashVisible();
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
