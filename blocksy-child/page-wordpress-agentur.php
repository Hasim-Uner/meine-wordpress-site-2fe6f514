<?php
/**
 * Template Name: WordPress Agentur Hannover
 *
 * Champions-League Light-Layout (Cream + Copper) mit dunklem Hero & finalem CTA.
 * Design-Quelle: Bundle "monepage-wordpress-agentur-hannover" (Claude Design).
 * Styles werden vollstaendig via `assets/css/agentur.css` geladen — kein eingebettetes CSS.
 * WGOS-Assets werden dynamisch aus der Registry (publish-only) gerendert.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$contact_url    = home_url( '/kontakt/' );
$e3_url         = home_url( '/e3-new-energy/' );
$marktcheck_url = home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' );

// ═══ E3 Proof Canon ═══
$e3         = function_exists( 'hu_e3_canon' ) ? hu_e3_canon() : [];
$e3_metrics = $e3['metrics'] ?? [];

// ═══ WGOS Asset Registry ═══
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
						<span class="ag-hero__title-line">Mehr qualifizierte</span>
						<span class="ag-hero__title-line">Anfragen.</span>
						<span class="ag-hero__title-line ag-hero__title-line--em">Ohne neue Website.</span>
					</h1>
					<p class="ag-hero__lede">
						WordPress-Wachstumssystem für anspruchsvolle B2B-Angebote — aus Hannover für den DACH-Raum. Strategie, technisches Fundament, Tracking und Conversion-Führung als ein System, nicht als Baustellensammlung.
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
						30&nbsp;Min · kein Standard-Pitch · ehrliche Diagnose, auch wenn die Antwort „nein" ist.
					</p>
				</div>

				<aside class="ag-brief" aria-label="Referenzfall E3 New Energy">
					<div class="ag-brief__head">
						<span class="ag-brief__kicker">Referenzfall · verifiziert</span>
						<div class="ag-brief__title">
							<h2>E3 New Energy</h2>
							<span class="ag-brief__sector">Solar · Wärmepumpe · Speicher</span>
						</div>
					</div>

					<div class="ag-brief__hero">
						<span class="ag-brief__label">Kosten pro qualifizierter Anfrage</span>
						<div class="ag-brief__numbers">
							<span class="ag-brief__before"><s>150&nbsp;€</s></span>
							<span class="ag-brief__arrow" aria-hidden="true">
								<svg width="22" height="22" viewBox="0 0 22 22" fill="none">
									<path d="M4 11h14M14 6l5 5-5 5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
								</svg>
							</span>
							<span class="ag-brief__after">22&nbsp;€</span>
						</div>
						<span class="ag-brief__delta">−85 % in 9 Monaten</span>
					</div>

					<dl class="ag-brief__table">
						<div>
							<dt>Anfragen</dt>
							<dd><span class="ag-counter" data-counter-target="1750" data-counter-suffix="+">0</span></dd>
						</div>
						<div>
							<dt>Abschlussquote</dt>
							<dd><span class="ag-counter" data-counter-target="12" data-counter-suffix=" %">0</span></dd>
						</div>
						<div>
							<dt>Zeitraum</dt>
							<dd><span class="ag-counter" data-counter-target="9" data-counter-suffix="&nbsp;Mon.">0</span></dd>
						</div>
					</dl>

					<footer class="ag-brief__foot">
						<span>Stand 2024–2025 · keine pauschale Übertragbarkeitsgarantie.</span>
					</footer>
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
			<p>9 Monate, 1.750+ qualifizierte Anfragen, messbare Reduktion der Leadkosten.</p>
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
			<h2 class="nx-headline-section">Kein lokaler Allrounder. Ein Spezialist mit Hannover-Anker.</h2>
			<p class="nx-subheadline wp-agentur-section-intro">
				Diese Seite ist der SEO-Anker für WordPress Agentur Hannover. Die Zielgruppe wird fachlich bestimmt: komplexe B2B-Angebote, klare Messbarkeit und ein eigenes Anfrage-System.
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
				<h3>Mit der WGOS-Methode, validiert am E3-Case</h3>
				<p>CPL von 150 € auf 22 € gesenkt, 1.750+ qualifizierte Anfragen in 9 Monaten und 12 % Abschlussquote.</p>
			</div>
		</div>
	</div>
</section>

<!-- ═══════════════════════════════════════════════
     SECTION 04 — WGOS METHODE (6 Phasen + Vorher/Nachher)
     ═══════════════════════════════════════════════ -->
<section class="nx-section" data-nx-theme="light" id="wgos">
	<div class="nx-container">
		<div class="nx-section-header">
			<p class="wp-agentur-eyebrow">WGOS · Methode</p>
			<h2 class="nx-headline-section">WordPress, SEO, Tracking und CRO in der richtigen Reihenfolge.</h2>
			<p class="nx-subheadline wp-agentur-section-intro">
				WGOS ist die Methode hinter dem eigenen Anfrage-System. Nicht die Asset-Liste entscheidet, sondern die Frage, welcher Eingriff zuerst mehr kaufnahe Klarheit erzeugt.
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
				$count = count( $asset_groups[ $area ] ?? [] );
			?>
				<li>
					<span class="step-num"><?php echo esc_html( $p['num'] ); ?></span>
					<span class="step-icon" aria-hidden="true"><?php echo esc_html( $p['icon'] ?? '' ); ?></span>
					<h3>
						<?php echo esc_html( $p['title'] ); ?>
						<small><?php echo (int) $count; ?> Bausteine</small>
					</h3>
					<p><?php echo esc_html( $p['desc'] ); ?></p>
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
			<a href="#asset-uebersicht" data-track-action="cta_method_to_library" data-track-category="navigation" data-track-section="wgos">Alle <?php echo (int) $total_assets; ?> Bausteine in der Methodenbibliothek ansehen ↓</a>
		</p>
	</div>
</section>

<!-- ═══════════════════════════════════════════════
     SECTION 05 — METHODENBIBLIOTHEK (Accordion, dynamisch)
     ═══════════════════════════════════════════════ -->
<section class="nx-section" data-nx-theme="light" id="asset-uebersicht">
	<div class="nx-container">
		<div class="nx-section-header">
			<p class="wp-agentur-eyebrow">Methodenbibliothek</p>
			<h2 class="nx-headline-section">Die Bausteine hinter der WGOS-Methode.</h2>
			<p class="nx-subheadline wp-agentur-section-intro">
				Diese Bausteine bilden die WGOS-Methode. Welche zuerst gebaut werden, entscheidet die Analyse — nicht der Katalog.
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
				<div class="acc-item" data-acc="<?php echo esc_attr( $area_slug ); ?>">
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
     SECTION 06 — PROJEKTPRÜFUNG (4 Kauf-Signale)
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

		<div class="wp-agentur-actions wp-agentur-actions--center" style="margin-top: 2.5rem;">
			<a href="<?php echo esc_url( $contact_url ); ?>" class="nx-btn nx-btn--primary" data-track-action="cta_projekt_pruefen_mid" data-track-category="lead_gen" data-track-section="projektpruefung">
				Projekt prüfen
				<svg width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true">
					<path d="M7 4L13 10L7 16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</a>
		</div>
	</div>
</section>

<!-- ═══════════════════════════════════════════════
     SECTION 07 — PROOF E3 DEEP DIVE
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
				<h3>9 Monate</h3>
				<p>In neun Monaten von portalabhängiger Leadbeschaffung zu einem eigenen, messbaren Anfrage-System.</p>
			</div>
			<div class="wp-agentur-case-card wp-agentur-case-card--cta">
				<span class="wp-agentur-case-card__eyebrow">Nächster Schritt</span>
				<h3>Der ganze Case</h3>
				<p class="wp-agentur-case-card__support">
					Alle Zahlen, die Systemlogik und was sich daraus für Ihre Situation ableiten lässt: <a href="<?php echo esc_url( $e3_url ); ?>">E3 New Energy im Detail →</a>
				</p>
				<a href="<?php echo esc_url( $e3_url ); ?>" class="nx-btn nx-btn--ghost" data-track-action="cta_proof_e3_deep" data-track-category="navigation" data-track-section="proof">
					E3-Case ansehen
				</a>
			</div>
		</div>
	</div>
</section>

<!-- ═══════════════════════════════════════════════
     SECTION 08 — ABGRENZUNG (3 negative cards)
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
				<h3>E-Commerce (Shopify / WooCommerce)</h3>
				<p>Keine Projekte mit reinem Shop-Fokus ohne Lead-Logik.</p>
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
     SECTION 09 — FOKUSMARKT ENERGIE
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
	</div>
</section>

<!-- ═══════════════════════════════════════════════
     SECTION 10 — STANDORT (2 cards)
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
			<div class="wp-agentur-local-card">
				<h3>Laufende WordPress-Betreuung</h3>
				<p>Für Bestandskunden mit etabliertem WordPress-System: Wartung, Updates und Weiterentwicklung im Rahmen laufender Mandate.</p>
			</div>
		</div>

		<p class="wp-agentur-location-note">
			WordPress Agentur Hannover ist der SEO-Anker dieser Seite — kein Hinweis auf eine regionale Begrenzung der Arbeitsweise.
		</p>
	</div>
</section>

<!-- ═══════════════════════════════════════════════
     SECTION 11 — FAQ (Accordion)
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
     SECTION 12 — FINAL CTA (Editorial, ruhig, eine Aufforderung)
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
