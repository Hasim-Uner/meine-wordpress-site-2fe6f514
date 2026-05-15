<?php
/**
 * Template Name: Agentur Service (Hannover)
 * Description: Spezialisten-Seite fuer WordPress-Wachstumssysteme mit Hannover-Standortanker.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$primary_urls = function_exists( 'nexus_get_primary_public_url_map' ) ? nexus_get_primary_public_url_map() : [];
$agentur_url  = $primary_urls['agentur'] ?? home_url( '/wordpress-agentur-hannover/' );
$asset_url    = $primary_urls['wgos_assets'] ?? trailingslashit( $agentur_url ) . '#asset-uebersicht';
$form_url     = trailingslashit( $agentur_url ) . '#projekt-pruefen';
$marketcheck_url = function_exists( 'hu_get_request_analysis_url' )
	? hu_get_request_analysis_url()
	: ( $primary_urls['request'] ?? home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' ) );

$e3_canon = function_exists( 'hu_e3_canon' ) ? hu_e3_canon() : [];
$e3_url   = isset( $e3_canon['url'] ) ? (string) $e3_canon['url'] : ( $primary_urls['e3'] ?? nexus_get_page_url( [ 'e3-new-energy' ] ) );
$e3_case_label = isset( $e3_canon['case_label'] ) ? (string) $e3_canon['case_label'] : 'E3 New Energy';
$e3_metrics = isset( $e3_canon['metrics'] ) && is_array( $e3_canon['metrics'] ) ? $e3_canon['metrics'] : [];

$e3_cpl_before       = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'cpl_before', 'display', '' ) : (string) ( $e3_metrics['cpl_before']['display'] ?? '' );
$e3_cpl_after        = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'cpl_after', 'display', '' ) : (string) ( $e3_metrics['cpl_after']['display'] ?? '' );
$e3_cpl_reduction    = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'cpl_reduction', 'display', '' ) : (string) ( $e3_metrics['cpl_reduction']['display'] ?? '' );
$e3_lead_count       = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'lead_count', 'display', '' ) : (string) ( $e3_metrics['lead_count']['display'] ?? '' );
$e3_sales_conversion = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'sales_conversion', 'display', '' ) : (string) ( $e3_metrics['sales_conversion']['display'] ?? '' );
$e3_timeframe        = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'timeframe', 'display_dative', '' ) : (string) ( $e3_metrics['timeframe']['display_dative'] ?? '' );

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
$e3_lead_label = sprintf(
	/* translators: 1: lead count, 2: timeframe. */
	'%1$s qualifizierte Anfragen in %2$s',
	$e3_lead_count,
	$e3_timeframe
);

$project_cta_label = 'Projekt prüfen';
$e3_cta_label      = 'E3-Case ansehen';
$hero_microcopy    = 'Klarheit vor Relaunch · für komplexe B2B-Angebote · kein Standard-Agentur-Pitch';
$privacy_url       = function_exists( 'nexus_get_page_url' ) ? nexus_get_page_url( [ 'datenschutz' ], home_url( '/datenschutz/' ) ) : home_url( '/datenschutz/' );
$rest_endpoint     = rest_url( 'nexus/v1/contact-request' );

$trust_cards = [
	[
		'icon'  => '01',
		'title' => 'Was',
		'text'  => 'WordPress-Systeme, die nachvollziehbar qualifizierte Anfragen erzeugen: Angebotsseiten, technisches SEO, Tracking, GA4, Server-Side Tracking und Conversion Optimierung WordPress als ein System.',
	],
	[
		'icon'  => '02',
		'title' => 'Für wen',
		'text'  => 'Anspruchsvolle B2B-Unternehmen mit erklärungsbedürftigem Angebot. Der Fokusmarkt ist Solar, Wärmepumpe und Speicher; die Arbeitsweise ist nicht auf Hannover begrenzt.',
	],
	[
		'icon'  => '03',
		'title' => 'Womit',
		'text'  => sprintf(
			'Mit der WGOS-Methode, validiert am Referenzkontext %1$s: %2$s, %3$s und %4$s.',
			$e3_case_label,
			$e3_cpl_delta_label,
			$e3_lead_label,
			$e3_sales_conversion . ' Abschlussquote'
		),
	],
];

$wgos_core_blocks = [
	[
		'area' => 'strategie',
		'text' => 'Welche Seite trägt welche Anfrage — und welche nicht.',
	],
	[
		'area' => 'fundament',
		'text' => 'Schnell, stabil, wartbar — ohne dass jedes Plugin-Update zur Krise wird.',
	],
	[
		'area' => 'messbarkeit',
		'text' => 'Sie wissen, welcher Kanal echte Projekte bringt — nicht nur Klicks.',
	],
	[
		'area' => 'sichtbarkeit',
		'text' => 'Die Suchanfragen, die kaufnahe Besucher liefern.',
	],
	[
		'area' => 'conversion',
		'text' => 'Was auf der Seite passieren muss, damit der Besucher jetzt handelt.',
	],
	[
		'area' => 'weiterentwicklung',
		'text' => 'Welche Änderung erzeugt als Nächstes Wirkung — datenbasiert, nicht aus dem Bauch.',
	],
];

$case_teaser_cards = [
	[
		'eyebrow' => 'Ausgangslage',
		'title'   => 'Hohe Anfragekosten und zu wenig Klarheit nach dem Klick',
		'text'    => 'Bei E3 New Energy war nicht ein einzelner Kanal das Problem. Entscheidend war die Reihenfolge aus WordPress-Fundament, Datenlage, Anfragepfad und Vertriebsanschluss.',
	],
	[
		'eyebrow' => 'Eingriff',
		'title'   => 'Erst Systemordnung, dann Aktivierung',
		'text'    => 'Seitenstruktur, Performance, Tracking und Formularlogik wurden so verbunden, dass Anfragen nicht nur entstehen, sondern im Vertrieb nutzbar werden.',
	],
];

$not_fit_items = [
	'One-Page-Visitenkarten und Standard-Websites ohne relevanten Projektumfang.',
	'E-Commerce-Projekte mit Shopify- oder WooCommerce-Fokus.',
	'Reine Design-Relaunches ohne Lead-Logik, Tracking und kaufnahe Angebotsstruktur.',
];

$faq_items      = function_exists( 'nexus_get_agentur_faq_items' ) ? nexus_get_agentur_faq_items() : [];
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

$focus_options    = function_exists( 'nexus_get_contact_focus_options' ) ? nexus_get_contact_focus_options() : [];
$budget_options   = function_exists( 'nexus_get_contact_budget_options' ) ? nexus_get_contact_budget_options() : [];
$timeline_options = function_exists( 'nexus_get_contact_timeline_options' ) ? nexus_get_contact_timeline_options() : [];
$project_focus_options = array_filter(
	$focus_options,
	static function ( $definition ) {
		$types = isset( $definition['types'] ) ? (array) $definition['types'] : [];
		return in_array( 'project', $types, true );
	}
);

get_header();
?>

<main id="main" class="site-main">
	<div class="cs-page wp-agentur-page-wrapper" data-track-section="agentur_landing">

		<section id="hero" class="nx-section nx-hero wp-agentur-hero">
			<div class="nx-container">
				<div class="wp-agentur-hero__content">
					<span class="nx-badge nx-badge--gold">WordPress · SEO · Tracking · Conversion · Hannover</span>
					<h1 class="nx-hero__title">WordPress-Wachstumssystem für anspruchsvolle B2B-Angebote.</h1>
					<p class="nx-hero__subtitle">
						Schwerpunkt Solar, Wärmepumpe und erklärungsbedürftige Energieprodukte. Aus Hannover für den DACH-Raum. Ich verbinde WordPress-Entwicklung mit SEO, Tracking und Conversion-Führung — damit die Website nachvollziehbar qualifizierte Anfragen erzeugt, statt nur sichtbar zu sein.
					</p>
					<div class="wp-agentur-actions wp-agentur-actions--hero">
						<a href="<?php echo esc_url( $form_url ); ?>" class="nx-btn nx-btn--primary wp-agentur-hero__primary" data-track-action="cta_agentur_hero_project" data-track-category="lead_gen" data-track-section="hero"><?php echo esc_html( $project_cta_label ); ?></a>
						<a href="<?php echo esc_url( $e3_url ); ?>" class="wp-agentur-text-link" data-track-action="cta_agentur_hero_e3" data-track-category="trust" data-track-section="hero"><?php echo esc_html( $e3_cta_label ); ?></a>
					</div>
					<p class="nx-cta-microcopy"><?php echo esc_html( $hero_microcopy ); ?></p>
					<div class="wp-agentur-hero__proof" role="list" aria-label="E3 Kennzahlen im Kontext">
						<span role="listitem"><?php echo esc_html( $e3_cpl_delta_label ); ?></span>
						<span role="listitem"><?php echo esc_html( $e3_cpl_reduction_label ); ?></span>
						<span role="listitem"><?php echo esc_html( $e3_lead_label ); ?></span>
						<span role="listitem"><?php echo esc_html( $e3_sales_conversion . ' Abschlussquote' ); ?></span>
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
						<figcaption>Haşim Üner · Growth Architect · Standort Hannover</figcaption>
					</figure>
				</div>
			</div>
		</section>

		<section id="spezialisierung" class="nx-section" data-track-section="agentur_specialization">
			<div class="nx-container">
				<div class="nx-section-header">
					<span class="wp-agentur-eyebrow">Spezialisierung</span>
					<h2 class="nx-headline-section">Kein lokaler Allrounder. Ein Spezialist mit Hannover-Anker.</h2>
					<p class="wp-agentur-section-intro">Diese Seite ist der SEO-Anker für WordPress Agentur Hannover. WordPress SEO Hannover bleibt als Standortsignal sichtbar; die Zielgruppe wird fachlich bestimmt: komplexe B2B-Angebote, klare Messbarkeit und ein eigenes Anfrage-System.</p>
				</div>
				<div class="wp-agentur-pain-grid">
					<?php foreach ( $trust_cards as $trust_card ) : ?>
						<article class="wp-agentur-pain-card nx-card">
							<span class="wp-agentur-pain-card__icon" aria-hidden="true"><?php echo esc_html( $trust_card['icon'] ); ?></span>
							<h3><?php echo esc_html( $trust_card['title'] ); ?></h3>
							<p><?php echo esc_html( $trust_card['text'] ); ?></p>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</section>

		<section id="wgos" class="nx-section wgos-explainer" data-track-section="agentur_wgos">
			<div class="nx-container">
				<div class="nx-section-header">
					<span class="wp-agentur-eyebrow">WGOS · Methode</span>
					<h2 class="nx-headline-section">WordPress, SEO, Tracking und CRO in der richtigen Reihenfolge.</h2>
					<p class="wp-agentur-section-intro">WGOS ist die Methode hinter dem eigenen Anfrage-System. Nicht die Asset-Liste entscheidet, sondern die Frage, welcher Eingriff zuerst mehr kaufnahe Klarheit erzeugt.</p>
				</div>
				<ol class="wgos-steps" aria-label="Sechs WGOS-Kernbereiche als Käufer-Outcomes">
					<?php foreach ( $wgos_core_blocks as $block_position => $wgos_core_block ) : ?>
						<li>
							<span class="step-num"><?php echo esc_html( sprintf( '%02d', (int) $block_position + 1 ) ); ?></span>
							<h3><?php echo esc_html( hue_kernbereich_label( $wgos_core_block['area'] ) ); ?></h3>
							<p><strong><?php echo esc_html( $wgos_core_block['text'] ); ?></strong></p>
						</li>
					<?php endforeach; ?>
				</ol>
				<p class="wp-agentur-process-link">
					<a href="<?php echo esc_url( $asset_url ); ?>" data-track-action="cta_agentur_wgos_library" data-track-category="navigation" data-track-section="wgos">Methodenbibliothek ansehen</a>
				</p>
			</div>
		</section>

		<section id="asset-uebersicht" class="nx-section wgos-asset-grid-section" data-track-section="agentur_method_library">
			<div class="nx-container">
				<div class="nx-section-header">
					<span class="wp-agentur-eyebrow">Methodenbibliothek</span>
					<h2 class="nx-headline-section">Die Bausteine hinter der WGOS-Methode.</h2>
					<p class="wp-agentur-section-intro">Diese Bausteine bilden die WGOS-Methode. Welche zuerst gebaut werden, entscheidet die Analyse — nicht der Katalog.</p>
				</div>
				<div class="asset-core-grid" aria-label="Kompakte Übersicht der WGOS-Kernbereiche">
					<?php foreach ( $wgos_core_blocks as $block_position => $wgos_core_block ) : ?>
						<article class="asset-core-card">
							<span class="asset-core-card__num" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', (int) $block_position + 1 ) ); ?></span>
							<h3><?php echo esc_html( hue_kernbereich_label( $wgos_core_block['area'] ) ); ?></h3>
							<p><?php echo esc_html( $wgos_core_block['text'] ); ?></p>
						</article>
					<?php endforeach; ?>
				</div>
				<div class="asset-hybrid">
					<div class="asset-hybrid__copy">
						<span class="asset-hybrid__kicker">Arbeitsbibliothek</span>
						<h3>WordPress bleibt Bedienoberfläche. Die wiederholbaren Eingriffe liegen in prüfbaren Bausteinen.</h3>
						<p>Die Liste darunter ist keine Einkaufsliste. Sie zeigt, welche Assets für Strategie, Fundament, Messbarkeit, Sichtbarkeit, Conversion und Weiterentwicklung bereitstehen, wenn die Diagnose den Bedarf zeigt.</p>
					</div>
					<ul class="asset-hybrid__checks" aria-label="Prinzipien der Methodenbibliothek">
						<li>Priorität vor Umfang</li>
						<li>Messbarkeit vor Meinung</li>
						<li>Wartbarer Theme-Code vor loser Konfiguration</li>
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
										<a href="<?php echo esc_url( (string) $asset['url'] ); ?>" data-track-action="cta_agentur_asset_detail" data-track-category="navigation" data-track-section="method_library">
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

		<section id="case-e3" class="nx-section" data-track-section="agentur_e3_proof">
			<div class="nx-container">
				<div class="nx-section-header">
					<span class="wp-agentur-eyebrow">Proof · <?php echo esc_html( $e3_case_label ); ?></span>
					<h2 class="nx-headline-section">Was passiert, wenn WordPress, Tracking und Anfrageführung zusammenarbeiten.</h2>
					<p class="wp-agentur-section-intro">Der E3-Case ist Referenz, keine pauschale Übertragbarkeitsgarantie. Er zeigt, warum Reihenfolge, Datenqualität und eigene Anfragepfade wichtiger sind als ein weiterer Relaunch.</p>
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
								<span>CPL gesenkt</span>
							</div>
							<div role="listitem">
								<strong><?php echo esc_html( $e3_cpl_reduction_label ); ?></strong>
								<span>gegenüber Lead-Einkauf</span>
							</div>
							<div role="listitem">
								<strong><?php echo esc_html( $e3_lead_count . ' in ' . $e3_timeframe ); ?></strong>
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
						<p>Wenn Sie sehen wollen, wie Seitenstruktur, Tracking und Anfragepfad zusammengewirkt haben, ist der offene E3-Case der richtige nächste Kontext.</p>
						<a href="<?php echo esc_url( $e3_url ); ?>" class="nx-btn nx-btn--ghost" data-track-action="cta_agentur_case_e3" data-track-category="trust" data-track-section="e3_proof"><?php echo esc_html( $e3_cta_label ); ?></a>
					</article>
				</div>
			</div>
		</section>

		<section id="nicht-passend" class="nx-section" data-track-section="agentur_not_fit">
			<div class="nx-container">
				<div class="wp-agentur-solution-card">
					<span class="wp-agentur-solution-card__eyebrow">Abgrenzung</span>
					<h2 class="nx-headline-section">Für wen diese Seite nicht passt.</h2>
					<p>Die Seite ist kein Sammelpunkt für kleine Standard-Websites. Sie passt, wenn WordPress ein relevanter B2B-Kanal werden oder bleiben soll.</p>
					<ul>
						<?php foreach ( $not_fit_items as $not_fit_item ) : ?>
							<li><?php echo esc_html( $not_fit_item ); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
		</section>

		<section id="solar-marktcheck" class="nx-section" data-track-section="agentur_solar_bridge">
			<div class="nx-container">
				<div class="nx-section-header">
					<span class="wp-agentur-eyebrow">Fokusmarkt Energie</span>
					<h2 class="nx-headline-section">Solar, Wärmepumpe oder Speicher?</h2>
					<p class="wp-agentur-section-intro">Wenn Ihr Angebot Solar, Wärmepumpe oder Speicher ist, gibt es einen präziseren Einstieg: den Marktcheck, der gezielt auf Lead-Generierung in dieser Branche zugeschnitten ist.</p>
				</div>
				<p class="wp-agentur-location-note">
					<a href="<?php echo esc_url( $marketcheck_url ); ?>" data-track-action="cta_agentur_solar_bridge_marketcheck" data-track-category="navigation" data-track-section="solar_bridge">Zum Marktcheck für Solar &amp; Wärmepumpe</a>
				</p>
			</div>
		</section>

		<section id="standort" class="nx-section" data-track-section="agentur_location">
			<div class="nx-container">
				<div class="nx-section-header">
					<span class="wp-agentur-eyebrow">Standort</span>
					<h2 class="nx-headline-section">Aus Hannover für den DACH-Raum.</h2>
				</div>
				<p class="wp-agentur-location-note">Persönliche Termine, Workshops und Reviews sind in Hannover, Pattensen und der Region Hannover möglich. Die Umsetzung funktioniert genauso sauber remote.</p>
				<p class="wp-agentur-location-note wp-agentur-location-note--muted">Bestandskunden mit etabliertem WordPress-System: WordPress Wartung Hannover und Weiterentwicklung im Rahmen laufender Mandate.</p>
			</div>
		</section>

		<?php if ( ! empty( $faq_items ) ) : ?>
			<section id="faq" class="nx-section" data-track-section="agentur_faq">
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
		<?php endif; ?>

		<section id="projekt-pruefen" class="nx-section wp-agentur-project-form" data-track-section="agentur_project_form">
			<div class="nx-container">
				<div class="nx-cta-box wp-agentur-cta-box">
					<h2>Ich prüfe, ob Ihr WordPress-System als Anfrage-System tragfähig ist.</h2>
					<p>Wenn Ihr Projekt zu dieser Arbeitsweise passt, ist der nächste Schritt kein Standard-Pitch, sondern eine klare Einordnung von Angebot, Website, Messbarkeit und nächster Priorität.</p>
					<div class="wp-agentur-actions wp-agentur-actions--center">
						<a href="#agentur-projektformular" class="nx-btn nx-btn--primary" data-track-action="cta_agentur_final_project" data-track-category="lead_gen" data-track-section="final_cta"><?php echo esc_html( $project_cta_label ); ?></a>
						<a href="<?php echo esc_url( $e3_url ); ?>" class="nx-btn nx-btn--ghost" data-track-action="cta_agentur_final_e3" data-track-category="trust" data-track-section="final_cta"><?php echo esc_html( $e3_cta_label ); ?></a>
					</div>
				</div>

				<div id="agentur-projektformular" class="contact-form-panel contact-superflow wp-agentur-contact-form" aria-labelledby="agentur-form-title">
					<div class="contact-section-head">
						<span class="contact-section-head__eyebrow">Projektprüfung</span>
						<h2 id="agentur-form-title">Kurz einordnen. Danach prüfe ich den Fit.</h2>
						<p>Kein Solar-Marktcheck und kein generisches Kontaktformular. Dieses Formular ist für anspruchsvolle B2B-Projekte rund um WordPress, SEO, Tracking und Conversion.</p>
					</div>

					<div class="contact-error-summary is-hidden" role="alert" aria-live="assertive" data-contact-error-summary>
						<p class="contact-error-summary__title">Bitte prüfen Sie folgende Felder:</p>
						<ul class="contact-error-summary__list" data-contact-error-list></ul>
					</div>

					<form
						class="contact-form contact-form--superflow"
						data-contact-form
						action="<?php echo esc_url( $rest_endpoint ); ?>"
						method="post"
						novalidate
					>
						<div class="contact-form__honeypot" aria-hidden="true">
							<label for="contact-company-website">Website</label>
							<input id="contact-company-website" type="text" name="company_website" tabindex="-1" autocomplete="off">
						</div>

						<input type="hidden" name="ads_source" id="ads_source" value="">
						<input type="hidden" name="ads_keyword" id="ads_keyword" value="">
						<input type="hidden" name="utm_medium" id="utm_medium" value="">
						<input type="hidden" name="utm_campaign" id="utm_campaign" value="">
						<input type="hidden" name="gclid" id="gclid" value="">
						<input type="hidden" name="matchtype" id="matchtype" value="">

						<div class="contact-flow-progress" aria-label="Kontakt-Fortschritt">
							<span data-contact-step-label>Schritt 1 von 3</span>
							<strong data-contact-progress-value>33%</strong>
							<div class="contact-flow-progress__bar" aria-hidden="true">
								<span data-contact-progress-fill></span>
							</div>
						</div>

						<div class="contact-flow-stage">
							<section class="contact-flow-step" data-contact-step="type" data-contact-step-label="Anfragetyp" data-contact-step-skip="true">
								<fieldset class="contact-intent contact-intent--collapsed" data-contact-intent>
									<legend>Anfrageart</legend>
									<div class="contact-intent__grid">
										<label class="contact-intent__option" for="contact-type-project">
											<input id="contact-type-project" type="radio" name="request_type" value="project" checked required data-contact-type-input>
											<span class="contact-intent__card">
												<strong>Projektprüfung</strong>
												<span>WordPress, SEO, Tracking und Conversion für anspruchsvolle B2B-Angebote.</span>
											</span>
										</label>
									</div>
								</fieldset>
							</section>

							<section class="contact-flow-step" data-contact-step="focus" data-contact-step-label="Thema">
								<div class="contact-field" data-contact-field="focus">
									<label for="contact-focus" data-contact-focus-label>Welcher Bereich soll zuerst geprüft werden?</label>
									<p id="contact-focus-help" class="contact-field__help" data-contact-focus-help>Wählen Sie den Bereich, in dem aktuell die größte geschäftliche Unklarheit liegt.</p>
									<select id="contact-focus" name="focus" required data-contact-focus-select aria-describedby="contact-focus-help contact-focus-error">
										<option value="" selected disabled>Bitte auswählen</option>
										<?php foreach ( $project_focus_options as $focus_key => $focus_definition ) : ?>
											<option
												value="<?php echo esc_attr( $focus_key ); ?>"
												data-types="<?php echo esc_attr( implode( ',', array_map( 'sanitize_key', (array) $focus_definition['types'] ) ) ); ?>"
											>
												<?php echo esc_html( (string) $focus_definition['label'] ); ?>
											</option>
										<?php endforeach; ?>
									</select>
									<p class="contact-field__error is-hidden" id="contact-focus-error" aria-live="polite"></p>
								</div>
							</section>

							<section class="contact-flow-step" data-contact-step="message" data-contact-step-label="Kurzbeschreibung">
								<div class="contact-field" data-contact-field="message">
									<label for="contact-message" data-contact-message-label>Kurzbeschreibung</label>
									<p id="contact-message-help" class="contact-field__help" data-contact-message-help>Welche URL ist relevant? Was ist das Angebot? Wo verliert das System heute Anfragen oder Klarheit?</p>
									<textarea
										id="contact-message"
										name="message"
										rows="5"
										required
										minlength="24"
										aria-describedby="contact-message-help contact-message-error"
										placeholder="<?php echo esc_attr( "1. Website: Welche URL ist relevant?\n2. Angebot: Was verkaufen Sie und an wen?\n3. Engpass: Was soll die Seite besser leisten?" ); ?>"
										data-contact-message
									></textarea>
									<p class="contact-field__error is-hidden" id="contact-message-error" aria-live="polite"></p>
								</div>
							</section>

							<section class="contact-flow-step" data-contact-step="identity" data-contact-step-label="Kontakt">
								<div class="contact-form__row">
									<div class="contact-field" data-contact-field="name">
										<label for="contact-name">Name</label>
										<input id="contact-name" name="name" type="text" autocomplete="name" required aria-describedby="contact-name-error">
										<p class="contact-field__error is-hidden" id="contact-name-error" aria-live="polite"></p>
									</div>

									<div class="contact-field" data-contact-field="email">
										<label for="contact-email">E-Mail</label>
										<input id="contact-email" name="email" type="email" autocomplete="email" required aria-describedby="contact-email-error">
										<p class="contact-field__error is-hidden" id="contact-email-error" aria-live="polite"></p>
									</div>
								</div>

								<details class="contact-optional" data-contact-optional>
									<summary class="contact-optional__toggle">
										<span class="contact-optional__label">Mehr Kontext <span class="contact-optional__hint">(optional)</span></span>
										<svg class="contact-optional__icon" width="18" height="18" viewBox="0 0 18 18" aria-hidden="true"><path d="M5 7l4 4 4-4" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
									</summary>
									<div class="contact-optional__body">
										<div class="contact-form__row">
											<div class="contact-field">
												<label for="contact-website">Website URL <span>optional</span></label>
												<input id="contact-website" name="website_url" type="url" autocomplete="url" inputmode="url" placeholder="https://example.de">
											</div>

											<div class="contact-field">
												<label for="contact-linkedin">LinkedIn <span>optional</span></label>
												<input id="contact-linkedin" name="linkedin_url" type="url" autocomplete="url" inputmode="url" placeholder="https://linkedin.com/in/...">
											</div>
										</div>

										<div class="contact-form__row">
											<div class="contact-field is-hidden" data-contact-context-field="budget">
												<label for="contact-budget">Budget <span>optional</span></label>
												<select id="contact-budget" name="budget">
													<option value="" selected>Optional auswählen</option>
													<?php foreach ( $budget_options as $budget_key => $budget_label ) : ?>
														<option value="<?php echo esc_attr( $budget_key ); ?>"><?php echo esc_html( $budget_label ); ?></option>
													<?php endforeach; ?>
												</select>
											</div>

											<div class="contact-field" data-contact-context-field="timeline">
												<label for="contact-timeline" data-contact-timeline-label>Zeitfenster <span>optional</span></label>
												<select id="contact-timeline" name="timeline" data-contact-timeline-select>
													<option value="" selected>Optional auswählen</option>
													<?php foreach ( $timeline_options as $timeline_key => $timeline_label ) : ?>
														<option value="<?php echo esc_attr( $timeline_key ); ?>"><?php echo esc_html( $timeline_label ); ?></option>
													<?php endforeach; ?>
												</select>
											</div>
										</div>
									</div>
								</details>

								<label class="contact-consent" data-contact-field="consent">
									<input type="checkbox" name="consent" value="1" required aria-describedby="contact-consent-error">
									<span>
										Ich stimme zu, dass meine Angaben zur Bearbeitung meiner Anfrage verarbeitet werden.
										Mehr dazu in der <a href="<?php echo esc_url( $privacy_url ); ?>">Datenschutzerklärung</a>.
									</span>
									<p class="contact-field__error is-hidden" id="contact-consent-error" aria-live="polite"></p>
								</label>
							</section>
						</div>

						<div class="contact-form__actions contact-form__actions--flow">
							<button class="contact-btn contact-btn--ghost" type="button" data-contact-prev hidden>Zurück</button>
							<button class="contact-btn contact-btn--primary" type="button" data-contact-next hidden>Weiter</button>
							<button class="contact-submit" type="submit" data-contact-submit data-track-action="contact_submit_agentur_project" data-track-category="lead_gen" data-track-section="agentur_project_form"><?php echo esc_html( $project_cta_label ); ?></button>
						</div>

						<div class="contact-form__feedback" data-contact-feedback aria-live="polite" role="status"></div>
					</form>

					<div class="contact-form__postcopy">
						<p class="contact-postcopy__lead">Ich prüfe die Angaben persönlich und antworte in der Regel innerhalb von 24 Stunden.</p>
					</div>
				</div>
			</div>
		</section>

	</div>
</main>

<?php
get_footer();
