<?php
/**
 * Template Name: WordPress Agentur Hannover
 *
 * Hybrid Dark/Light Design – powered by nexus design-system.css + agentur.css.
 * WGOS-Assets werden dynamisch aus der Registry (39 Assets, publish-only) gerendert.
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
$e3 = function_exists( 'hu_e3_canon' ) ? hu_e3_canon() : [];
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
		// Fallback: falls ein Asset einen nicht-standard Kernbereich hat
		$asset_groups[ $core_area ]   = isset( $asset_groups[ $core_area ] ) ? $asset_groups[ $core_area ] : [];
		$asset_groups[ $core_area ][] = $asset;
	}
}

get_header();
?>

<div class="wp-agentur-page-wrapper">

<!-- ═══════════════════════════════════════════════
     SECTION 01 — HERO (Dark)
     ═══════════════════════════════════════════════ -->
<section class="nx-section wp-agentur-hero" data-nx-theme="dark" id="hero">
	<div class="nx-container">
		<div class="wp-agentur-hero__grid">

			<div class="wp-agentur-hero__copy">
				<p class="wp-agentur-eyebrow">WordPress · SEO · Tracking · Conversion · Hannover</p>
				<h1 class="nx-hero__title wp-agentur-hero__title">
					WordPress-Wachstumssystem für anspruchsvolle B2B-Angebote.
				</h1>
				<p class="nx-hero__subtitle wp-agentur-hero__subtitle">
					Aus Hannover für den DACH-Raum. Ich verbinde WordPress-Entwicklung mit SEO, Tracking und Conversion-Führung — damit die Website nachvollziehbar qualifizierte Anfragen erzeugt, statt nur sichtbar zu sein.
				</p>

				<!-- E3 Proof Pills -->
				<div class="wp-agentur-hero__proof">
					<span>150€ → 22€ CPL</span>
					<span>1.750+ Anfragen</span>
					<span>12% Abschlussquote</span>
				</div>

				<div class="wp-agentur-actions wp-agentur-actions--hero">
					<a href="#projekt-pruefen" class="nx-btn nx-btn--primary wp-agentur-hero__primary" data-track-action="cta_hero_projekt_pruefen" data-track-category="lead_gen" data-track-section="hero">
						Projekt prüfen
						<svg width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true">
							<path d="M7 4L13 10L7 16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
					</a>
					<a href="#proof" class="wp-agentur-text-link" data-track-action="cta_hero_e3_case" data-track-category="navigation" data-track-section="hero">
						E3-Case ansehen →
					</a>
				</div>
			</div>

			<!-- Hero Card: was geprüft wird -->
			<div class="wp-agentur-hero-card">
				<span class="wp-agentur-hero-card__eyebrow">Vor Umsetzung</span>
				<h2 class="wp-agentur-hero-card__title">Ich prüfe vier Kauf-Signale</h2>
				<div class="wp-agentur-hero-card__items">
					<div class="wp-agentur-hero-card__item">
						<span class="wp-agentur-hero-card__label">01 Angebot</span>
						<p>Ist der Nutzen so klar, dass ein kaufnaher Besucher den nächsten Schritt versteht?</p>
					</div>
					<div class="wp-agentur-hero-card__item">
						<span class="wp-agentur-hero-card__label">02 Nachfrage</span>
						<p>Welche Suchanfragen oder Partnerpfade bringen Besucher mit echter Projektabsicht?</p>
					</div>
					<div class="wp-agentur-hero-card__item">
						<span class="wp-agentur-hero-card__label">03 Datenlage</span>
						<p>Sind GA4, Consent und Tracking belastbar genug für Entscheidungen?</p>
					</div>
					<div class="wp-agentur-hero-card__item">
						<span class="wp-agentur-hero-card__label">04 Anfragepfad</span>
						<p>Welche Information braucht der Besucher jetzt für einen qualifizierten Erstkontakt?</p>
					</div>
				</div>
				<p class="wp-agentur-hero-card__note">
					Ich prüfe nicht, ob eine neue Website schöner wäre. Ich prüfe, ob Angebot, Nachfrage, Daten und Anfragepfad als System zusammenpassen.
				</p>
			</div>

		</div>
	</div>
</section>

<!-- ═══════════════════════════════════════════════
     SECTION 02 — PROOF STRIP (Dark)
     ═══════════════════════════════════════════════ -->
<section class="nx-section wp-agentur-proof" data-nx-theme="dark">
	<div class="nx-container">
		<div class="wp-agentur-proof-header">
			<p class="wp-agentur-eyebrow">Proof · E3 New Energy</p>
			<h2 class="nx-headline-section">Keine leeren Versprechen.</h2>
			<p>Der E3-Case ist Referenz, keine pauschale Übertragbarkeitsgarantie. Er zeigt, warum Reihenfolge, Datenqualität und eigene Anfragepfade wichtiger sind als ein weiterer Relaunch.</p>
		</div>

		<div class="nx-grid nx-grid-4 wp-agentur-proof-grid">
			<div class="nx-card wp-agentur-proof-item">
				<div class="wp-agentur-proof-value">150€ → 22€</div>
				<div class="wp-agentur-proof-label">CPL gesenkt</div>
			</div>
			<div class="nx-card wp-agentur-proof-item">
				<div class="wp-agentur-proof-value">1.750+</div>
				<div class="wp-agentur-proof-label">Qualifizierte Anfragen</div>
			</div>
			<div class="nx-card wp-agentur-proof-item">
				<div class="wp-agentur-proof-value">12%</div>
				<div class="wp-agentur-proof-label">Abschlussquote</div>
			</div>
			<div class="nx-card wp-agentur-proof-item">
				<div class="wp-agentur-proof-value">85%</div>
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
     SECTION 03 — SPEZIALISIERUNG (Dark)
     ═══════════════════════════════════════════════ -->
<section class="nx-section wp-agentur-segment-switch" data-nx-theme="dark" id="spezialisierung">
	<div class="nx-container">
		<div class="nx-section-header">
			<p class="wp-agentur-eyebrow">Spezialisierung</p>
			<h2 class="nx-headline-section">Kein lokaler Allrounder. Ein Spezialist mit Hannover-Anker.</h2>
			<p class="nx-subheadline wp-agentur-section-intro">
				Diese Seite ist der SEO-Anker für WordPress Agentur Hannover. Die Zielgruppe wird fachlich bestimmt: komplexe B2B-Angebote, klare Messbarkeit und ein eigenes Anfrage-System.
			</p>
		</div>

		<div class="nx-grid nx-grid-3 wp-agentur-segment-grid">
			<div class="wp-agentur-segment-card wp-agentur-segment-card--focus">
				<span class="wp-agentur-segment-card__tag">Was</span>
				<h3>WordPress-Systeme, die nachvollziehbar qualifizierte Anfragen erzeugen</h3>
				<p>Angebotsseiten, technisches SEO, Tracking, GA4, Server-Side Tracking und Conversion Optimierung — WordPress als ein System.</p>
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
     SECTION 04 — WGOS METHODE (Light)
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

		<!-- 6 Kernbereiche als visuelles System-Diagramm -->
		<div class="wgos-steps wp-agentur-process-grid">
			<?php
			$phase_labels = [
				'Strategie'             => [ 'num' => '01', 'title' => 'Strategie', 'desc' => 'Welche Seite trägt welche Anfrage — und welche nicht.' ],
				'Technisches Fundament' => [ 'num' => '02', 'title' => 'Fundament', 'desc' => 'Schnell, stabil, wartbar — ohne dass jedes Plugin-Update zur Krise wird.' ],
				'Messbarkeit'           => [ 'num' => '03', 'title' => 'Messbarkeit', 'desc' => 'Sie wissen, welcher Kanal echte Projekte bringt — nicht nur Klicks.' ],
				'Sichtbarkeit'          => [ 'num' => '04', 'title' => 'Sichtbarkeit', 'desc' => 'Die Suchanfragen, die kaufnahe Besucher liefern.' ],
				'Conversion'            => [ 'num' => '05', 'title' => 'Conversion', 'desc' => 'Was auf der Seite passieren muss, damit der Besucher jetzt handelt.' ],
				'Weiterentwicklung'     => [ 'num' => '06', 'title' => 'Weiterentwicklung', 'desc' => 'Welche Änderung erzeugt als Nächstes Wirkung — datenbasiert.' ],
			];
			foreach ( $phase_labels as $area => $p ) :
				$count = count( $asset_groups[ $area ] ?? [] );
			?>
				<li>
					<span class="step-num"><?php echo esc_html( $p['num'] ); ?></span>
					<h3><?php echo esc_html( $p['title'] ); ?> <small style="font-weight:400;color:var(--nx-text-dim)">· <?php echo (int) $count; ?> Bausteine</small></h3>
					<p><?php echo esc_html( $p['desc'] ); ?></p>
				</li>
			<?php endforeach; ?>
		</div>

		<p class="wp-agentur-process-link">
			<a href="#asset-uebersicht">Alle 39 Bausteine in der Methodenbibliothek ansehen ↓</a>
		</p>
	</div>
</section>

<!-- ═══════════════════════════════════════════════
     SECTION 05 — METHODENBIBLIOTHEK (Dark)
     Dynamisch aus der WGOS Asset Registry
     ═══════════════════════════════════════════════ -->
<section class="nx-section" data-nx-theme="dark" id="asset-uebersicht">
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
				$meta   = $area_meta[ $area ] ?? [ 'color' => '#b46a3c', 'icon' => '📦', 'desc' => '' ];
				$count  = count( $assets );
				$area_slug = sanitize_title( $area );
				$area_label = function_exists( 'hue_kernbereich_label' )
					? hue_kernbereich_label( hue_get_wgos_kernbereich_key( $area ) )
					: $area;
			?>
				<div class="acc-item" data-acc="<?php echo esc_attr( $area_slug ); ?>">
					<button class="acc-trigger" aria-expanded="false" aria-controls="body-<?php echo esc_attr( $area_slug ); ?>">
						<div class="acc-trigger-left">
							<div class="acc-icon" style="background: <?php echo esc_attr( $meta['color'] ); ?>1a; color: <?php echo esc_attr( $meta['color'] ); ?>;"><?php echo esc_html( $meta['icon'] ); ?></div>
							<div class="acc-info">
								<div class="acc-title"><?php echo esc_html( $area_label ); ?></div>
								<div class="acc-desc"><?php echo esc_html( $meta['desc'] ); ?></div>
							</div>
						</div>
						<div class="acc-meta">
							<span class="acc-count"><?php echo (int) $count; ?> Bausteine</span>
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
		</div><!-- /accordion -->
	</div>
</section>

<!-- ═══════════════════════════════════════════════
     SECTION 06 — PROJEKTPRÜFUNG (Light)
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

		<div class="nx-grid nx-grid-4 wp-agentur-decision-grid">
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

		<div class="wp-agentur-actions wp-agentur-actions--center" style="margin-top: 2rem;">
			<a href="<?php echo esc_url( $contact_url ); ?>" class="nx-btn nx-btn--primary" data-track-action="cta_projekt_pruefen_mid" data-track-category="lead_gen" data-track-section="projektpruefung">
				Projekt prüfen
			</a>
		</div>
	</div>
</section>

<!-- ═══════════════════════════════════════════════
     SECTION 07 — PROOF E3 DEEP DIVE (Dark)
     ═══════════════════════════════════════════════ -->
<section class="nx-section" data-nx-theme="dark" id="proof">
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
					<div><strong>150€ → 22€</strong><span>CPL gesenkt</span></div>
					<div><strong>1.750+</strong><span>Qualifizierte Anfragen</span></div>
					<div><strong>12%</strong><span>Abschlussquote</span></div>
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
     SECTION 08 — ABGRENZUNG (Light)
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
			<div class="wp-agentur-pain-card nx-card">
				<div class="wp-agentur-pain-card__icon">×</div>
				<h3>One-Page-Visitenkarten</h3>
				<p>Keine Standard-Websites ohne relevanten Projektumfang.</p>
			</div>
			<div class="wp-agentur-pain-card nx-card">
				<div class="wp-agentur-pain-card__icon">×</div>
				<h3>E-Commerce (Shopify / WooCommerce)</h3>
				<p>Keine Projekte mit reinem Shop-Fokus ohne Lead-Logik.</p>
			</div>
			<div class="wp-agentur-pain-card nx-card">
				<div class="wp-agentur-pain-card__icon">×</div>
				<h3>Reine Design-Relaunches</h3>
				<p>Keine Relaunches ohne Lead-Logik, Tracking und kaufnahe Angebotsstruktur.</p>
			</div>
		</div>
	</div>
</section>

<!-- ═══════════════════════════════════════════════
     SECTION 09 — FOKUSMARKT ENERGIE (Dark)
     ═══════════════════════════════════════════════ -->
<section class="nx-section" data-nx-theme="dark" id="fokusmarkt">
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
				Zum Marktcheck für Solar & Wärmepumpe
			</a>
		</div>
	</div>
</section>

<!-- ═══════════════════════════════════════════════
     SECTION 10 — STANDORT (Light)
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

		<div class="nx-grid nx-grid-2 wp-agentur-local-grid">
			<div class="nx-card wp-agentur-local-card">
				<h3>Vor Ort in der Region Hannover</h3>
				<p>Persönliche Reviews, Workshops und Strategie-Sessions in Pattensen bei Hannover — oder remote im DACH-Raum.</p>
			</div>
			<div class="nx-card wp-agentur-local-card">
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
     SECTION 11 — FAQ (Light)
     ═══════════════════════════════════════════════ -->
<section class="nx-section" data-nx-theme="light" id="faq">
	<div class="nx-container">
		<div class="nx-section-header">
			<p class="wp-agentur-eyebrow">FAQ</p>
			<h2 class="nx-headline-section">Häufige Fragen</h2>
		</div>

		<div class="faq-list" id="faq-accordion">

			<div class="faq-item">
				<button class="faq-trigger" aria-expanded="false">
					<span class="faq-question">Welche WordPress Agentur in Hannover passt für anspruchsvolle B2B-Angebote?</span>
					<div class="faq-chevron" aria-hidden="true">
						<svg width="12" height="12" viewBox="0 0 12 12" fill="none">
							<path d="M3 4.5L6 7.5L9 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
					</div>
				</button>
				<div class="faq-body">
					<div class="faq-body-inner">
						<p class="faq-answer">Eine passende WordPress Agentur Hannover verbindet WordPress-Entwicklung, SEO, Tracking, GA4, Server-Side Tracking und Conversion-Führung. Genau darauf ist diese Seite ausgerichtet: WordPress als Nachfrage-System statt isolierter Einzelleistungen.</p>
					</div>
				</div>
			</div>

			<div class="faq-item">
				<button class="faq-trigger" aria-expanded="false">
					<span class="faq-question">Arbeiten Sie nur mit Unternehmen aus Hannover?</span>
					<div class="faq-chevron" aria-hidden="true">
						<svg width="12" height="12" viewBox="0 0 12 12" fill="none">
							<path d="M3 4.5L6 7.5L9 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
					</div>
				</button>
				<div class="faq-body">
					<div class="faq-body-inner">
						<p class="faq-answer">Nein. Der Standort ist Pattensen bei Hannover, persönliche Termine sind in der Region möglich. Die Umsetzung ist auf den DACH-Raum ausgelegt; Hannover ist Standortanker, keine Zielgruppen-Grenze.</p>
					</div>
				</div>
			</div>

			<div class="faq-item">
				<button class="faq-trigger" aria-expanded="false">
					<span class="faq-question">Ist Solar oder Wärmepumpe Voraussetzung?</span>
					<div class="faq-chevron" aria-hidden="true">
						<svg width="12" height="12" viewBox="0 0 12 12" fill="none">
							<path d="M3 4.5L6 7.5L9 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
					</div>
				</button>
				<div class="faq-body">
					<div class="faq-body-inner">
						<p class="faq-answer">Nein. Solar, Wärmepumpe und Speicher sind der Fokusmarkt, weil dort Anfragequalität, Vertriebsanschluss und Datenlage besonders schnell entscheidend werden. Die Arbeitsweise passt auch für anspruchsvolle B2B-Angebote mit ähnlicher Erklärungstiefe.</p>
					</div>
				</div>
			</div>

			<div class="faq-item">
				<button class="faq-trigger" aria-expanded="false">
					<span class="faq-question">Was passiert nach der Projektprüfung?</span>
					<div class="faq-chevron" aria-hidden="true">
						<svg width="12" height="12" viewBox="0 0 12 12" fill="none">
							<path d="M3 4.5L6 7.5L9 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
					</div>
				</button>
				<div class="faq-body">
					<div class="faq-body-inner">
						<p class="faq-answer">Ich prüfe Angebot, Website-Rolle, Messbarkeit und naheliegende Priorität. Wenn der Fit passt, folgt daraus eine saubere Empfehlung: Korrektur, Umsetzung, Weiterentwicklung oder bewusst kein gemeinsamer nächster Schritt.</p>
					</div>
				</div>
			</div>

			<div class="faq-item">
				<button class="faq-trigger" aria-expanded="false">
					<span class="faq-question">Brauche ich dafür einen Relaunch?</span>
					<div class="faq-chevron" aria-hidden="true">
						<svg width="12" height="12" viewBox="0 0 12 12" fill="none">
							<path d="M3 4.5L6 7.5L9 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
					</div>
				</button>
				<div class="faq-body">
					<div class="faq-body-inner">
						<p class="faq-answer">Nicht automatisch. Oft fehlt nicht der neue Look, sondern die richtige Reihenfolge zwischen Fundament, Daten, Sichtbarkeit und Conversion. Ein Relaunch ist nur sinnvoll, wenn die bestehende Struktur nicht mehr trägt.</p>
					</div>
				</div>
			</div>

			<div class="faq-item">
				<button class="faq-trigger" aria-expanded="false">
					<span class="faq-question">Was unterscheidet WGOS von einem klassischen Agentur-Projekt?</span>
					<div class="faq-chevron" aria-hidden="true">
						<svg width="12" height="12" viewBox="0 0 12 12" fill="none">
							<path d="M3 4.5L6 7.5L9 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
					</div>
				</button>
				<div class="faq-body">
					<div class="faq-body-inner">
						<p class="faq-answer">WGOS ordnet Strategie, Fundament, Messbarkeit, Sichtbarkeit, Conversion und Weiterentwicklung als zusammenhängende Methode. Entscheidend ist die Reihenfolge: Welche Seite trägt welche Anfrage, welcher Kanal liefert echte Projekte und welche Änderung erzeugt als Nächstes Wirkung.</p>
					</div>
				</div>
			</div>

			<div class="faq-item">
				<button class="faq-trigger" aria-expanded="false">
					<span class="faq-question">Für wen passt diese Seite nicht?</span>
					<div class="faq-chevron" aria-hidden="true">
						<svg width="12" height="12" viewBox="0 0 12 12" fill="none">
							<path d="M3 4.5L6 7.5L9 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
					</div>
				</button>
				<div class="faq-body">
					<div class="faq-body-inner">
						<p class="faq-answer">Nicht passend sind kleine One-Page-Visitenkarten, reine Design-Relaunches ohne Lead-Logik und E-Commerce-Projekte mit Shopify- oder WooCommerce-Fokus.</p>
					</div>
				</div>
			</div>

			<div class="faq-item">
				<button class="faq-trigger" aria-expanded="false">
					<span class="faq-question">Warum rankt eine WordPress-Seite, liefert aber keine Anfragen?</span>
					<div class="faq-chevron" aria-hidden="true">
						<svg width="12" height="12" viewBox="0 0 12 12" fill="none">
							<path d="M3 4.5L6 7.5L9 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
					</div>
				</button>
				<div class="faq-body">
					<div class="faq-body-inner">
						<p class="faq-answer">Ranking und Anfragequalität sind zwei verschiedene Probleme. Oft rankt eine Seite für Informationssuchen, während Proof, CTA-Führung und kaufnahe Argumentation fehlen. Genau dort verbindet diese Arbeit SEO, Struktur, Tracking und Conversion.</p>
					</div>
				</div>
			</div>

		</div><!-- /faq-list -->
	</div>
</section>

<!-- ═══════════════════════════════════════════════
     SECTION 12 — FINAL CTA (Dark)
     ═══════════════════════════════════════════════ -->
<section class="nx-section cta-section" data-nx-theme="dark" id="cta">
	<div class="cta-inner">
		<h2 class="cta-title">Ich prüfe, ob Ihr WordPress-System als Anfrage-System tragfähig ist.</h2>
		<p class="cta-subtitle">
			Wenn Ihr Projekt zu dieser Arbeitsweise passt, ist der nächste Schritt kein Standard-Pitch, sondern eine klare Einordnung von Angebot, Website, Messbarkeit und nächster Priorität.
		</p>
		<div class="cta-btns">
			<a href="<?php echo esc_url( $contact_url ); ?>" class="nx-btn nx-btn--primary" data-track-action="cta_final_projekt_pruefen" data-track-category="lead_gen" data-track-section="cta">
				Projekt prüfen
			</a>
			<a href="<?php echo esc_url( $e3_url ); ?>" class="nx-btn nx-btn--ghost" data-track-action="cta_final_e3_case" data-track-category="navigation" data-track-section="cta">
				E3-Case ansehen
			</a>
		</div>
	</div>
</section>

</div><!-- /wp-agentur-page-wrapper -->

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
</script>

<?php
get_footer();
