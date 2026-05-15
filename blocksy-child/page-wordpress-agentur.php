<?php
/**
 * Template Name: Agentur Service (Hannover)
 * Description: Lokale SEO-Landingpage für WordPress Growth Architect Hannover
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$primary_urls = function_exists( 'nexus_get_primary_public_url_map' ) ? nexus_get_primary_public_url_map() : [];
$agentur_url = $primary_urls['agentur'] ?? home_url( '/wordpress-agentur-hannover/' );
$wgos_url = $primary_urls['wgos'] ?? trailingslashit( $agentur_url ) . '#wgos';
$asset_overview_url = $primary_urls['wgos_assets'] ?? trailingslashit( $agentur_url ) . '#asset-uebersicht';
$analysis_url = function_exists( 'hu_get_request_analysis_url' ) ? hu_get_request_analysis_url() : ( $primary_urls['request'] ?? home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' ) );
$marketcheck_url = $analysis_url;
$about_url = $primary_urls['about'] ?? nexus_get_page_url( [ 'uber-mich' ] );
$contact_url = function_exists( 'nexus_get_contact_url' ) ? nexus_get_contact_url() : ( $primary_urls['contact'] ?? home_url( '/kontakt/' ) );
$implementation_contact_url = add_query_arg(
	[
		'type' => 'implementation',
	],
	$contact_url
);
$e3_canon = function_exists( 'hu_e3_canon' ) ? hu_e3_canon() : [];
$e3_url = isset( $e3_canon['url'] ) ? (string) $e3_canon['url'] : ( $primary_urls['e3'] ?? nexus_get_page_url( [ 'e3-new-energy' ] ) );
$wartung_url = nexus_get_primary_public_url( 'wartung', home_url( '/wordpress-wartung-hannover/' ) );
$measurement_url = function_exists( 'nexus_get_wgos_asset_anchor_url' ) ? nexus_get_wgos_asset_anchor_url( 'tracking-audit' ) : $wgos_url;
$cro_url = $primary_urls['cro'] ?? $wgos_url;
$e3_cpl_before = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'cpl_before', 'display', '150 €' ) : '150 €';
$e3_cpl_after = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'cpl_after', 'display', '22 €' ) : '22 €';
$e3_cpl_reduction = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'cpl_reduction', 'display', 'über 85 %' ) : 'über 85 %';
$e3_lead_count = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'lead_count', 'display', '1.750+' ) : '1.750+';
$e3_sales_conversion = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'sales_conversion', 'display', '12 %' ) : '12 %';
$canonical_ownership_sentence = function_exists( 'nexus_get_public_ownership_sentence' ) ? nexus_get_public_ownership_sentence() : 'Code, Inhalte, Zugänge und Setups bleiben bei Ihnen. Laufende Zusammenarbeit bedeutet Weiterentwicklung, nicht Abhängigkeit.';
$website_potential_cta_label = 'Website-Potenzial prüfen';
$marketcheck_cta_label = function_exists( 'nexus_get_primary_request_cta_label' ) ? nexus_get_primary_request_cta_label() : 'Marktcheck starten';
$e3_cta_label = 'E3-Case ansehen';
$hero_microcopy = 'Klarheit vor Relaunch · SEO, Tracking, Conversion · kein generischer Agentur-Pitch';
$e3_cpl_delta_label = sprintf(
	/* translators: 1: old CPL, 2: new CPL. */
	'CPL von %1$s auf %2$s gesenkt',
	$e3_cpl_before,
	$e3_cpl_after
);
$e3_cpl_reduction_label = sprintf(
	/* translators: %s: percentage reduction. */
	'%s niedrigere Kosten pro Anfrage',
	$e3_cpl_reduction
);
$hero_proof_items = [
	$e3_cpl_delta_label,
	$e3_cpl_reduction_label,
	$e3_lead_count . ' qualifizierte Anfragen',
	$e3_sales_conversion . ' Abschlussquote',
];

$pain_cards = [
	[
		'icon'  => '01',
		'title' => 'Sichtbarkeit ohne Richtung',
		'text'  => 'Es gibt Inhalte, aber keine saubere Verbindung zwischen Suchintention, Angebotsseite und nächstem Schritt. Genau dort greifen <a href="#technisches-seo">technisches SEO</a> und Angebotslogik ineinander.',
	],
	[
		'icon'  => '02',
		'title' => 'Daten ohne Entscheidungswert',
		'text'  => 'Tracking ist installiert, aber nicht belastbar. Consent, Events und Attribution erzeugen Rauschen statt Klarheit. Deshalb ist <a href="' . esc_url( $measurement_url ) . '">privacy-first Measurement</a> Fundament und kein Add-on.',
	],
	[
		'icon'  => '03',
		'title' => 'Seiten ohne Conversion-Führung',
		'text'  => 'Kontaktformulare am Seitenende sind keine Funnel-Logik. Wenn Proof, CTA-Reihenfolge und Einwandabbau fehlen, verliert die Seite Nachfrage genau dann, wenn sie wertvoll werden könnte. Dort setzt <a href="' . esc_url( $cro_url ) . '">Conversion-Architektur</a> an.',
	],
];

$case_teaser_cards = [
	[
		'eyebrow' => 'Ausgangslage',
		'title'   => 'Hohe Kosten pro Anfrage, schwache Daten, Reibung nach dem Klick',
		'text'    => 'E3 New Energy kaufte Leads teuer ein, ohne saubere Leadqualität und ohne robuste Conversion-Führung auf der Website.',
	],
	[
		'eyebrow' => 'Maßnahme',
		'title'   => 'Erst Fundament, dann Aktivierung',
		'text'    => 'Speed, Tracking, Seitenstruktur und Conversion-Pfade wurden geordnet, bevor neue Skalierung auf das Setup geschaltet wurde.',
	],
];

$wgos_core_blocks = [
	[
		'area'    => 'strategie',
		'compact' => 'Welche Seite soll welche Anfrage erzeugen?',
		'text'    => 'Angebot, Zielgruppe, Suchintention und nächster Schritt werden so geordnet, dass jede kaufnahe Seite eine klare Rolle hat.',
	],
	[
		'area'    => 'fundament',
		'compact' => 'Lädt, hält, lässt sich warten.',
		'text'    => 'Performance, Sicherheit, Updates und Theme-Struktur müssen den Betrieb tragen, bevor mehr Traffic auf die Seite gelenkt wird.',
	],
	[
		'area'    => 'messbarkeit',
		'compact' => 'Welche Quellen erzeugen echte Anfragen?',
		'text'    => 'Tracking, GA4, Consent und Server-Side Tracking werden so geprüft, dass aus Daten Entscheidungen statt Report-Rauschen entstehen.',
	],
	[
		'area'    => 'sichtbarkeit',
		'compact' => 'Welche Suchintentionen bringen kaufnahe Besucher?',
		'text'    => 'WordPress SEO Hannover, DACH-Sichtbarkeit und interne Verlinkung werden auf Nachfrage ausgerichtet, nicht auf reine Themenbreite.',
	],
	[
		'area'    => 'conversion',
		'compact' => 'Warum sollte der Besucher jetzt handeln?',
		'text'    => 'Proof, Einwandabbau, CTA-Reihenfolge und Formulare führen Besucher von Interesse zu einer qualifizierten Anfrage.',
	],
	[
		'area'    => 'weiterentwicklung',
		'compact' => 'Welche Änderung erzeugt als Nächstes Wirkung?',
		'text'    => 'Nach dem ersten Eingriff wird priorisiert, welche Anpassung bei SEO, Tracking, Conversion oder Betrieb den nächsten Hebel liefert.',
	],
];

$fit_items = [
	'WordPress ist ein echter Geschäftskanal und nicht nur ein Nebenprojekt.',
	'Es gibt ein belastbares Leistungsversprechen und kaufnahe Nachfrage.',
	'Sie wollen Prioritäten für Angebotsseiten, Tracking, Performance und Conversion statt Maßnahmensammlung.',
	'Messbarkeit, Ownership und kontrollierte Weiterentwicklung sind wichtiger als reine Kosmetik.',
];

$service_items = [
	'WordPress-Websites, die qualifizierte B2B-Anfragen erzeugen - nicht nur gut aussehen',
	'Technisches WordPress SEO für lokale und kaufnahe Sichtbarkeit in Hannover und DACH',
	'Tracking-, GA4-, GTM- und Server-Side-Tracking-Setups, die echte Entscheidungen ermöglichen',
	'Conversion-Optimierung für WordPress-Landingpages, Angebotsseiten und Anfragepfade',
	'Laufende Weiterentwicklung mit klarer Priorisierung statt Relaunch-Zyklen',
];

$faq_items = function_exists( 'nexus_get_agentur_faq_items' ) ? nexus_get_agentur_faq_items() : [];
$asset_registry = function_exists( 'hue_get_wgos_asset_registry' ) ? hue_get_wgos_asset_registry() : [];
$published_assets = array_filter(
	$asset_registry,
	static function ( $asset ) {
		return is_array( $asset ) && 'published' === (string) ( $asset['status'] ?? '' );
	}
);
$grouped_assets = [];
foreach ( $published_assets as $asset ) {
	$area_key = isset( $asset['kernbereich'] ) ? (string) $asset['kernbereich'] : '';
	if ( '' === $area_key ) {
		continue;
	}
	$grouped_assets[ $area_key ][] = $asset;
}
$ordered_asset_areas = [ 'strategie', 'fundament', 'messbarkeit', 'sichtbarkeit', 'conversion', 'weiterentwicklung' ];

get_header();
?>

<main id="main" class="site-main">
	<div class="cs-page wp-agentur-page-wrapper" data-track-section="agentur_landing">

		<section id="hero" class="nx-section nx-hero wp-agentur-hero">
			<div class="nx-container">
				<div class="wp-agentur-hero__content">
					<span class="nx-badge nx-badge--gold">WordPress Agentur Hannover · SEO · Tracking · Conversion</span>
					<h1 class="nx-hero__title">WordPress Agentur Hannover für Websites, die messbar Anfragen erzeugen.</h1>
					<p class="nx-hero__subtitle">
						Ich verbinde WordPress-Entwicklung, technisches SEO, Tracking und Conversion-Führung — damit Ihre Website nicht nur sichtbar ist, sondern nachvollziehbar qualifizierte Anfragen bringt.
					</p>
					<div class="wp-agentur-actions wp-agentur-actions--hero">
						<a href="<?php echo esc_url( $analysis_url ); ?>" class="nx-btn nx-btn--primary wp-agentur-hero__primary" data-track-action="cta_agentur_hero_potential" data-track-category="lead_gen"><?php echo esc_html( $website_potential_cta_label ); ?></a>
						<a href="<?php echo esc_url( $e3_url ); ?>" class="wp-agentur-text-link" data-track-action="cta_agentur_hero_e3" data-track-category="trust"><?php echo esc_html( $e3_cta_label ); ?></a>
					</div>
					<p class="nx-cta-microcopy"><?php echo esc_html( $hero_microcopy ); ?></p>
					<p class="wp-agentur-hero-support">
						Spezialisiert auf WordPress-Systeme für erklärungsbedürftige B2B-Angebote — mit besonderem Fokus auf Solar-, Wärmepumpen- und Energieanbieter.
					</p>
					<div class="wp-agentur-hero__proof" role="list" aria-label="Proof-Signale">
						<?php foreach ( $hero_proof_items as $hero_proof_item ) : ?>
							<span role="listitem"><?php echo esc_html( $hero_proof_item ); ?></span>
						<?php endforeach; ?>
					</div>
					<figure class="wp-agentur-hero-portrait">
						<img
							src="<?php echo esc_url( hu_get_profile_image_url() ); ?>"
							alt="Haşim Üner – WordPress Growth Architect Hannover"
							loading="eager"
							fetchpriority="high"
							width="120"
							height="148"
						/>
						<figcaption>Haşim Üner · Growth Architect · Hannover</figcaption>
					</figure>
				</div>
			</div>
		</section>

		<section id="einstieg" class="nx-section wp-agentur-segment-switch" data-track-section="agentur_segment_switch">
			<div class="nx-container">
				<div class="nx-section-header">
					<span class="wp-agentur-eyebrow">Einstieg wählen</span>
					<h2 class="nx-headline-section">Der nächste Schritt hängt vom Markt ab.</h2>
					<p class="wp-agentur-section-intro">Die Seite bedient den lokalen Intent WordPress Agentur Hannover. Der Einstieg bleibt trotzdem sauber getrennt: Fokusmarkt Solar und Wärmepumpe führt in den Marktcheck, andere B2B-Unternehmen in die Potenzialprüfung.</p>
				</div>
				<div class="wp-agentur-segment-grid">
					<article class="wp-agentur-segment-card wp-agentur-segment-card--focus">
						<span class="wp-agentur-segment-card__tag">Fokusmarkt</span>
						<h3>Für Solar, Wärmepumpe &amp; Speicher</h3>
						<p>Wenn Sie heute Portal-Leads kaufen oder nicht wissen, welcher Kanal echte Projekte bringt, führt der richtige Einstieg zum eigenen Anfrage-System.</p>
						<a href="<?php echo esc_url( $marketcheck_url ); ?>" class="nx-btn nx-btn--primary" data-track-action="cta_agentur_segment_energy" data-track-category="lead_gen">Zum Anfrage-System</a>
					</article>
					<article class="wp-agentur-segment-card">
						<span class="wp-agentur-segment-card__tag">B2B</span>
						<h3>Für andere B2B-Unternehmen</h3>
						<p>Wenn WordPress bereits ein relevanter Kanal ist, prüfe ich zuerst, ob SEO, Tracking, Angebotsseiten oder Conversion-Führung den größten Hebel liefern.</p>
						<a href="<?php echo esc_url( $analysis_url ); ?>" class="nx-btn nx-btn--ghost" data-track-action="cta_agentur_segment_b2b" data-track-category="lead_gen"><?php echo esc_html( $website_potential_cta_label ); ?></a>
					</article>
				</div>
			</div>
		</section>

		<section id="problem" class="nx-section">
			<div class="nx-container">
				<div class="nx-section-header">
					<h2 class="nx-headline-section">Warum viele WordPress-Seiten trotz Sichtbarkeit keine belastbaren Anfragen liefern</h2>
					<p class="wp-agentur-section-intro">Das Problem ist selten nur Design. Meist fehlt die Verbindung zwischen Angebotsseiten, sauberer Messung, Proof und dem nächsten sinnvollen Schritt.</p>
				</div>
				<div class="wp-agentur-pain-grid">
					<?php foreach ( $pain_cards as $pain_card ) : ?>
						<article class="wp-agentur-pain-card nx-card">
							<span class="wp-agentur-pain-card__icon" aria-hidden="true"><?php echo esc_html( $pain_card['icon'] ); ?></span>
							<h3><?php echo esc_html( $pain_card['title'] ); ?></h3>
							<p><?php echo wp_kses_post( $pain_card['text'] ); ?></p>
						</article>
					<?php endforeach; ?>
				</div>
				<div class="wp-agentur-solution-card">
					<span class="wp-agentur-solution-card__eyebrow">Die Lösung</span>
					<h3>Erst die Bremsen ordnen. Dann erst über Umsetzungstiefe sprechen.</h3>
					<p>Wenn Angebotsseiten, Datensignale, Proof und CTA-Führung wieder zusammenarbeiten, entsteht kein schöneres Webprojekt, sondern eine Website mit klarerem Nachfrageweg.</p>
					<ul>
						<li>klare Prioritäten statt Relaunch-Reflex</li>
						<li>belastbare Signale statt Tool-Rauschen</li>
						<li>bessere Anfrageführung auf kaufnahen Seiten</li>
					</ul>
					<p><?php echo esc_html( $canonical_ownership_sentence ); ?></p>
				</div>
			</div>
		</section>

		<section id="vergleich" class="nx-section">
			<div class="nx-container">
				<div class="nx-section-header">
					<h2 class="nx-headline-section">Was hier anders läuft.</h2>
				</div>
				<div class="nx-prose wp-agentur-prose">
					<div class="wp-agentur-table-wrap">
						<table class="wp-agentur-table">
							<thead>
								<tr>
									<th></th>
									<th>Agentur-Logik</th>
									<th>WGOS-Logik</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>Ziel</td>
									<td>Website liefern</td>
									<td>WordPress als Nachfrage-System aufbauen</td>
								</tr>
								<tr>
									<td>Lieferbild</td>
									<td>Seitenpaket und Übergabe</td>
									<td>Angebotsseiten, Datenebene, KPI-Klarheit und Weiterentwicklung</td>
								</tr>
								<tr>
									<td>Reihenfolge</td>
									<td>Design, dann später Optimierung</td>
									<td>Diagnose, Priorisierung, dann Umsetzung</td>
								</tr>
								<tr>
									<td>SEO</td>
									<td>Basis-Setup und Grundoptimierung</td>
									<td>IA, kaufnahe Seiten, Proof und Suchintention als Verbund</td>
								</tr>
								<tr>
									<td>Tracking</td>
									<td>Einrichtung für Reports</td>
									<td>Conversion-Signale und Klarheit für echte Entscheidungen</td>
								</tr>
								<tr>
									<td>Conversion</td>
									<td>CTA am Ende</td>
									<td>Argumentationsstruktur über die ganze Seite</td>
								</tr>
								<tr>
									<td>Betrieb</td>
									<td>Lose Erweiterungen mit hoher Abhängigkeit</td>
									<td>Kontrollierter Stack, nachvollziehbare Änderungen, Ownership</td>
								</tr>
								<tr>
									<td>Nach Go-Live</td>
									<td>Projekt abgeschlossen</td>
									<td>Gezielte Iteration auf die größten Hebel</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</section>

		<section id="leistungen" class="nx-section">
			<div class="nx-container">
				<div class="nx-section-header">
					<h2 class="nx-headline-section">Was ich für B2B-Unternehmen umsetze.</h2>
				</div>
				<div class="wp-agentur-solution-card">
					<ul>
						<?php foreach ( $service_items as $service_item ) : ?>
							<li><?php echo esc_html( $service_item ); ?></li>
						<?php endforeach; ?>
					</ul>
					<div class="wp-agentur-actions wp-agentur-actions--center">
						<a href="<?php echo esc_url( $analysis_url ); ?>" class="nx-btn nx-btn--primary" data-track-action="cta_agentur_services_potential" data-track-category="lead_gen"><?php echo esc_html( $website_potential_cta_label ); ?></a>
						<a href="<?php echo esc_url( $wgos_url ); ?>" class="nx-btn nx-btn--ghost" data-track-action="cta_agentur_services_wgos" data-track-category="navigation">Arbeitsweise ansehen</a>
					</div>
				</div>
			</div>
		</section>

		<section id="fit" class="nx-section">
			<div class="nx-container">
				<div class="nx-section-header">
					<h2 class="nx-headline-section">Für wen ich arbeite.</h2>
				</div>
				<div class="nx-prose wp-agentur-prose">
					<p>Die Zusammenarbeit passt für B2B-Unternehmen, die WordPress bereits einsetzen oder bewusst als Kernsystem nutzen wollen und deren Website Anfragen liefern soll, nicht nur Präsenz. Besonders klar ist der Fit bei Solar-, Wärmepumpen- und Energieanbietern, weil dort Kanalqualität, Anfragekosten und Vertriebsanschluss schnell entscheidend werden.</p>
					<ul>
						<?php foreach ( $fit_items as $fit_item ) : ?>
							<li><?php echo esc_html( $fit_item ); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
		</section>

		<section id="wgos" class="nx-section wgos-explainer">
			<div class="nx-container">
				<div class="nx-section-header">
					<span class="wp-agentur-eyebrow">WGOS · Arbeitsweise</span>
					<h2 class="nx-headline-section">Nicht mehr machen. Sondern das Richtige in der richtigen Reihenfolge.</h2>
					<p class="wp-agentur-section-intro">WGOS ist die Methode hinter der Umsetzung: WordPress, SEO, Tracking und Conversion werden nach der Frage geordnet, welche Änderung zuerst mehr qualifizierte Anfrageklarheit bringt.</p>
				</div>
				<ol class="wgos-steps" aria-label="Sechs Kernbereiche in fester Reihenfolge">
					<?php foreach ( $wgos_core_blocks as $block_position => $wgos_core_block ) : ?>
						<li>
							<span class="step-num"><?php echo esc_html( sprintf( '%02d', (int) $block_position + 1 ) ); ?></span>
							<h3><?php echo esc_html( hue_kernbereich_label( $wgos_core_block['area'] ) ); ?></h3>
							<p><strong><?php echo esc_html( $wgos_core_block['compact'] ); ?></strong></p>
							<p><?php echo esc_html( $wgos_core_block['text'] ); ?></p>
						</li>
					<?php endforeach; ?>
				</ol>

				<div class="wgos-vs">
					<div class="wgos-vs__side wgos-vs__side--muted">
						<h3>Klassische Umsetzung</h3>
						<ul>
							<li>Leistungen werden einzeln beauftragt und nebeneinander umgesetzt</li>
							<li>SEO, Tracking und Conversion folgen verschiedenen Logiken</li>
							<li>Erfolg hängt an Einzelaktionen statt am System</li>
							<li>Die Website bleibt Präsenz, nicht Nachfrage-Struktur</li>
						</ul>
					</div>
					<div class="wgos-vs__side wgos-vs__side--accent">
						<h3>WGOS</h3>
						<ul>
							<li>Alle Bausteine werden über das gewünschte Anfrage-System geordnet</li>
							<li>Die Reihenfolge entscheidet, welcher Eingriff zuerst Wirkung erzeugen kann</li>
							<li>Messbarkeit und Nutzerführung gehören zum Fundament</li>
							<li>Die Website wird zu einem nachvollziehbaren Nachfrage-System</li>
						</ul>
					</div>
				</div>
				<p class="wp-agentur-process-link">
					<a href="<?php echo esc_url( $asset_overview_url ); ?>" data-track-action="cta_agentur_wgos_assets" data-track-category="navigation">Asset-Übersicht ansehen</a>
				</p>
			</div>
		</section>

		<section id="asset-uebersicht" class="nx-section wgos-asset-grid-section">
			<div class="nx-container">
				<div class="nx-section-header">
					<span class="wp-agentur-eyebrow">Bausteine · Asset-Übersicht</span>
					<h2 class="nx-headline-section">6 Kernbausteine, eine klare Priorität.</h2>
					<p class="wp-agentur-section-intro">Nicht jeder Baustein wird gebaut. Die Analyse entscheidet, welcher zuerst Wirkung erzeugt.</p>
				</div>
				<div class="asset-core-grid" aria-label="Kompakte Übersicht der WGOS-Kernbausteine">
					<?php foreach ( $wgos_core_blocks as $block_position => $wgos_core_block ) : ?>
						<article class="asset-core-card">
							<span class="asset-core-card__num" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', (int) $block_position + 1 ) ); ?></span>
							<h3><?php echo esc_html( hue_kernbereich_label( $wgos_core_block['area'] ) ); ?></h3>
							<p><?php echo esc_html( $wgos_core_block['compact'] ); ?></p>
						</article>
					<?php endforeach; ?>
				</div>
				<div class="asset-hybrid">
					<div class="asset-hybrid__copy">
						<span class="asset-hybrid__kicker">Methodenbibliothek</span>
						<h3>WordPress bleibt Bedienoberfläche. Die wiederholbaren Bausteine liegen im Repo.</h3>
						<p>Die Liste darunter ist keine Einkaufsliste. Sie zeigt, welche geprüften Assets für Strategie, Fundament, Messbarkeit, Sichtbarkeit, Conversion und Weiterentwicklung bereitstehen, wenn die Analyse den Bedarf zeigt.</p>
					</div>
					<ul class="asset-hybrid__checks" aria-label="Arbeitsweise">
						<li>WordPress als Schnittstelle für Betrieb und Pflege</li>
						<li>Bausteine als prüfbarer Code statt lose Konfiguration</li>
						<li>Änderungen über Git, PHP-Lint und deploybaren Theme-Code</li>
					</ul>
				</div>
				<div class="asset-library-head">
					<span class="wp-agentur-eyebrow">Publizierte Assets</span>
					<h3>Methodenbibliothek für Detailseiten, Prüfungen und technische Eingriffe</h3>
				</div>
				<div class="wgos-asset-grid">
					<?php foreach ( $ordered_asset_areas as $area_position => $area ) : ?>
						<?php if ( empty( $grouped_assets[ $area ] ) ) : ?>
							<?php continue; ?>
						<?php endif; ?>
						<?php
						$asset_count       = count( $grouped_assets[ $area ] );
						$asset_count_label = 1 === $asset_count ? '1 Baustein' : sprintf( '%d Bausteine', $asset_count );
						?>
						<section class="asset-area" aria-labelledby="<?php echo esc_attr( 'asset-area-' . $area ); ?>">
							<div class="asset-area__head">
								<span class="asset-area__num" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', (int) $area_position + 1 ) ); ?></span>
								<div>
									<h3 id="<?php echo esc_attr( 'asset-area-' . $area ); ?>" class="area-title"><?php echo esc_html( hue_kernbereich_label( $area ) ); ?></h3>
									<span class="asset-area__count"><?php echo esc_html( $asset_count_label ); ?></span>
								</div>
							</div>
							<ul class="asset-list">
								<?php foreach ( $grouped_assets[ $area ] as $asset ) : ?>
									<li>
										<a href="<?php echo esc_url( (string) $asset['url'] ); ?>" data-track-action="cta_agentur_asset_detail" data-track-category="navigation">
											<span class="asset-name"><?php echo esc_html( (string) $asset['title'] ); ?></span>
											<?php if ( ! empty( $asset['short'] ) ) : ?>
												<span class="asset-short"><?php echo esc_html( (string) $asset['short'] ); ?></span>
											<?php endif; ?>
											<span class="asset-arrow" aria-hidden="true">→</span>
										</a>
									</li>
								<?php endforeach; ?>
							</ul>
						</section>
					<?php endforeach; ?>
				</div>
			</div>
		</section>

		<section id="technisches-seo" class="nx-section seo-cluster">
			<div class="nx-container">
				<div class="nx-section-header">
					<span class="wp-agentur-eyebrow">Sichtbarkeit · SEO</span>
					<h2 class="nx-headline-section">Technisches WordPress-SEO für Hannover und DACH</h2>
					<p class="wp-agentur-section-intro">SEO ist hier kein Meta-Feintuning, sondern Arbeit an Crawlability, Seitenstruktur, Local-Signalen und Rich-Result-fähigem Markup. WordPress SEO Hannover wird nach kaufnahen Seitentypen priorisiert.</p>
				</div>
				<div class="seo-subclusters">
					<article class="seo-sub" id="technisches-seo-foundation">
						<h3>1 · Technisches Fundament</h3>
						<ul>
							<li>LCP-Ziel &lt; 2,5 s auf kaufnahen Seiten</li>
							<li>Crawl-Budget auf relevante Angebotsseiten konzentrieren</li>
							<li>Thin Content deindexieren, wenn er keine Such- oder Nutzerrolle hat</li>
							<li>Interne Verlinkung als Themen-Graph statt Footer-Liste aufbauen</li>
							<li>Server-Tuning prüfen, wenn Theme und Erweiterungssetup an Grenzen stoßen</li>
						</ul>
					</article>
					<article class="seo-sub" id="technisches-seo-local">
						<h3>2 · Local SEO Hannover</h3>
						<ul>
							<li>Google Business Profile sauber kategorisieren und Service-Felder vollständig pflegen</li>
							<li>Maps-Pack-Sichtbarkeit für „WordPress Agentur Hannover“ und verwandte Suchanfragen prüfen</li>
							<li>Lokale Citations und NAP-Konsistenz auf relevanten Verzeichnissen absichern</li>
							<li>Bewertungen als Trust-Layer nutzen, nicht als Schaufenster</li>
						</ul>
					</article>
					<article class="seo-sub" id="technisches-seo-schema">
						<h3>3 · Schema &amp; Rich Results</h3>
						<ul>
							<li>
								<span>
									JSON-LD sauber für die relevanten Typen:
									<span class="seo-code-list" aria-label="Organization, LocalBusiness, Service, FAQPage und BreadcrumbList">
										<code>Organization</code>
										<code>LocalBusiness</code>
										<code>Service</code>
										<code>FAQPage</code>
										<code>BreadcrumbList</code>
									</span>
								</span>
							</li>
							<li>Markup für Reviews und Case Studies nur einsetzen, wenn der Inhalt das trägt</li>
							<li>Schema-Validierung über die Rich Results-Prüfung statt über einzelne Häkchen absichern</li>
							<li>Strukturierte Daten als Sichtbarkeits-Hebel einsetzen, nicht als Pflichtfeld</li>
						</ul>
					</article>
				</div>
				<p class="seo-cta">
					Ob technisches SEO, Tracking oder Conversion-Optimierung bei Ihrer Website heute den größten Hebel hat, zeigt die <a href="<?php echo esc_url( $analysis_url ); ?>" data-track-action="cta_agentur_seo_potential" data-track-category="lead_gen">Potenzialprüfung</a>.
				</p>
			</div>
		</section>

		<section id="wordpress-wartung" class="nx-section">
			<div class="nx-container">
				<div class="nx-section-header">
					<h2 class="nx-headline-section">WordPress Wartung und Wartungsvertrag in Hannover</h2>
					<p class="wp-agentur-section-intro">
						Wartung ist hier kein Ticket-System, sondern der Betriebsblock aus Updates, Sicherheit, Backups, Performance und klaren Rollback-Prozessen — Fundament für alles, was darauf an SEO, Conversion und bezahlter Nachfrage aufbaut.
					</p>
				</div>
				<div class="nx-prose wp-agentur-prose">
					<h3>Was ein belastbarer WordPress-Wartungsvertrag abdeckt</h3>
					<ul class="premium-list">
						<li><span class="check-icon">✓</span><div>Security Hardening: Zugriffe, Rechte und Wiederherstellungswege abgesichert.</div></li>
						<li><span class="check-icon">✓</span><div>Update-Management: planbar, prüfbar, rollback-fähig — kein Ad-hoc-Risiko.</div></li>
						<li><span class="check-icon">✓</span><div>Backups und Monitoring mit verifizierter Wiederherstellung, nicht nur Dump-Dateien.</div></li>
						<li><span class="check-icon">✓</span><div>Erweiterungsprüfung: Reduktion von Wartungslast und Konflikten.</div></li>
						<li><span class="check-icon">✓</span><div>Performance-Diagnose und bei Bedarf Server-Tuning.</div></li>
					</ul>

					<h3>Warum das kein generischer Wartungsvertrag ist</h3>
					<ul class="premium-list">
						<li><span class="check-icon">→</span><div>Direkter Ansprechpartner statt Support-Pipeline.</div></li>
						<li><span class="check-icon">→</span><div>Wartung als Fundament — erst stabiler Betrieb, dann SEO, Conversion und Ads.</div></li>
						<li><span class="check-icon">→</span><div>Für Unternehmen mit relevanter Website und regelmäßigem Traffic, nicht für Low-Traffic-Projekte.</div></li>
					</ul>
					<p class="wp-agentur-section-intro">
						WordPress Wartung Hannover bleibt ein sekundärer Pfad: über den <a href="<?php echo esc_url( $contact_url ); ?>" data-track-action="cta_agentur_wartung_contact" data-track-category="navigation">Kontaktpfad</a> oder direkt in der <a href="<?php echo esc_url( $analysis_url ); ?>" data-track-action="cta_agentur_wartung_potential" data-track-category="lead_gen">Potenzialprüfung</a> einordnen.
					</p>
				</div>
			</div>
		</section>

		<section id="case-e3" class="nx-section">
			<div class="nx-container">
				<div class="nx-section-header">
					<h2 class="nx-headline-section">WordPress Agentur Hannover: Was passiert, wenn die Reihenfolge stimmt</h2>
					<p class="wp-agentur-section-intro">E3 New Energy zeigt, wie ein Anfrage-System wirkt, wenn WordPress-Fundament, Tracking und Conversion-Führung zusammenarbeiten. Die Zahlen sind Case-Kontext, keine Übertragbarkeitsgarantie.</p>
				</div>
				<div class="wp-agentur-case-grid">
					<?php foreach ( $case_teaser_cards as $case_teaser_card ) : ?>
						<article class="wp-agentur-case-card">
							<span class="wp-agentur-case-card__eyebrow"><?php echo esc_html( $case_teaser_card['eyebrow'] ); ?></span>
							<h3><?php echo esc_html( $case_teaser_card['title'] ); ?></h3>
							<p><?php echo esc_html( $case_teaser_card['text'] ); ?></p>
						</article>
					<?php endforeach; ?>
					<article class="wp-agentur-case-card wp-agentur-case-card--result">
						<span class="wp-agentur-case-card__eyebrow">Ergebnis</span>
						<h3><?php echo esc_html( $e3_cpl_delta_label ); ?></h3>
						<div class="wp-agentur-case-card__metrics" role="list" aria-label="Case Kennzahlen">
							<div role="listitem">
								<strong><?php echo esc_html( $e3_cpl_before . ' auf ' . $e3_cpl_after ); ?></strong>
								<span>Kosten pro Anfrage gesenkt</span>
							</div>
							<div role="listitem">
								<strong><?php echo esc_html( $e3_cpl_reduction ); ?></strong>
								<span>niedrigere Kosten pro Anfrage</span>
							</div>
							<div role="listitem">
								<strong><?php echo esc_html( $e3_lead_count ); ?></strong>
								<span>qualifizierte Anfragen</span>
							</div>
							<div role="listitem">
								<strong><?php echo esc_html( $e3_sales_conversion ); ?></strong>
								<span>Abschlussquote</span>
							</div>
						</div>
					</article>
					<article class="wp-agentur-case-card wp-agentur-case-card--cta">
						<span class="wp-agentur-case-card__eyebrow">Vertiefung</span>
						<h3>Die Fallstudie im Detail lesen</h3>
						<p>Wenn Sie sehen wollen, wie Reihenfolge, Tracking und Conversion-Pfad zusammengewirkt haben, gehen Sie in den offenen E3-Case.</p>
						<a href="<?php echo esc_url( $e3_url ); ?>" class="nx-btn nx-btn--ghost" data-track-action="cta_agentur_case_e3" data-track-category="trust"><?php echo esc_html( $e3_cta_label ); ?></a>
						<p class="wp-agentur-case-card__support"><a href="<?php echo esc_url( $analysis_url ); ?>" data-track-action="cta_agentur_case_marketcheck" data-track-category="lead_gen"><?php echo esc_html( $marketcheck_cta_label ); ?></a></p>
					</article>
				</div>
			</div>
		</section>

		<section id="standort" class="nx-section">
			<div class="nx-container">
				<div class="nx-section-header">
					<h2 class="nx-headline-section">Standort Hannover. Arbeitsgebiet DACH.</h2>
				</div>
				<p class="wp-agentur-location-note">Persönliche Termine, Workshops und Reviews sind in Hannover, Pattensen und der Region Hannover jederzeit möglich. Die Zusammenarbeit funktioniert genauso sauber remote.</p>
				<p class="wp-agentur-location-note"><strong>Standort:</strong> Pattensen bei Hannover. Das hilft für lokale Abstimmung, ohne die Arbeit künstlich auf Stadtgrenzen zu begrenzen.</p>
			</div>
		</section>

		<section id="faq" class="nx-section">
			<div class="nx-container">
				<div class="nx-section-header">
					<h2 class="nx-headline-section">Häufige Fragen</h2>
				</div>
				<div class="nx-faq wp-faq">
					<?php foreach ( $faq_items as $index => $item ) : ?>
						<details class="nx-faq__item"<?php echo 0 === $index ? ' open' : ''; ?>>
							<summary><?php echo esc_html( $item['question'] ); ?></summary>
							<div class="nx-faq__content"><?php echo esc_html( $item['answer'] ); ?></div>
						</details>
					<?php endforeach; ?>
				</div>
			</div>
		</section>

		<section id="cta" class="nx-section">
			<div class="nx-container">
				<div class="nx-cta-box wp-agentur-cta-box">
					<h2>Ich prüfe, an welcher Stelle Ihr WordPress-System heute Nachfrage verliert.</h2>
					<p>Der Marktcheck zeigt, ob Angebotsseiten, Datenlage, CTA-Führung oder technische Reibung zuerst angegangen werden sollten und ob ein Relaunch überhaupt sinnvoll ist.</p>
					<div class="wp-agentur-actions wp-agentur-actions--center">
						<a href="<?php echo esc_url( $analysis_url ); ?>" class="nx-btn nx-btn--primary" data-track-action="cta_agentur_final_marketcheck" data-track-category="lead_gen"><?php echo esc_html( $marketcheck_cta_label ); ?></a>
						<a href="<?php echo esc_url( $e3_url ); ?>" class="nx-btn nx-btn--ghost" data-track-action="cta_agentur_final_e3" data-track-category="trust"><?php echo esc_html( $e3_cta_label ); ?></a>
					</div>
					<p class="wp-cta-desc mt-1">Kein Pitch. Klare Priorisierung. Wenn fachlich sinnvoll, kann daraus als nächster Schritt eine vertiefte Analyse, eine fokussierte Korrektur oder eine laufende Weiterentwicklung entstehen.</p>
					<p class="wp-cta-desc mb-0">
						<a href="<?php echo esc_url( $about_url ); ?>" data-track-action="cta_agentur_final_about" data-track-category="navigation">Mehr über meine Arbeitsweise</a>
						<span aria-hidden="true"> · </span>
						<a href="<?php echo esc_url( $wgos_url ); ?>" data-track-action="cta_agentur_final_wgos" data-track-category="navigation">WGOS ansehen</a>
						<span aria-hidden="true"> · </span>
						Wenn der Scope schon klar ist:
						<a href="<?php echo esc_url( $implementation_contact_url ); ?>" data-track-action="cta_agentur_final_contact" data-track-category="navigation">direkt Kontakt</a>
						<span aria-hidden="true"> · </span>
						Für Betrieb und Stabilisierung:
						<a href="<?php echo esc_url( $wartung_url ); ?>" data-track-action="cta_agentur_final_wartung" data-track-category="navigation">WordPress Wartung Hannover</a>
					</p>
				</div>
			</div>
		</section>

	</div>
</main>

<?php
get_footer();
