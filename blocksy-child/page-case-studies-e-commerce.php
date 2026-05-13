<?php
/**
 * Template Name: Ergebnisse Hub
 * Description: Flache Proof-Seite für Solar- und Wärmepumpen-Anbieter.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$request_url    = function_exists( 'nexus_get_primary_request_url' ) ? nexus_get_primary_request_url() : home_url( '/anfrage-system-analyse/' );
$request_cta    = function_exists( 'nexus_get_primary_request_cta_label' ) ? nexus_get_primary_request_cta_label() : 'Anfrage stellen';
$e3_url         = function_exists( 'nexus_get_primary_public_url' ) ? nexus_get_primary_public_url( 'e3', home_url( '/e3-new-energy/' ) ) : home_url( '/e3-new-energy/' );
$e3_cpl_before  = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'cpl_before', 'display', '150 €' ) : '150 €';
$e3_cpl_after   = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'cpl_after', 'display', '22 €' ) : '22 €';
$e3_lead_count  = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'lead_count', 'display', '1.750+' ) : '1.750+';
$e3_sales_rate  = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'sales_conversion', 'display', '12 %' ) : '12 %';
?>

<main id="main" class="site-main results-hub" data-track-section="results_hub">

	<!-- 1. Hero -->
	<section class="nx-section results-hero">
		<div class="nx-container">
			<div class="results-hero__inner">
				<h1 class="results-hero__title">Ergebnisse für Solar- und Wärmepumpen-Anbieter.</h1>
				<p class="results-hero__subtitle">
					Kein Showcase mit Nebenschauplätzen, sondern Proof aus genau der Nische, für die das Formular gedacht ist.
				</p>

				<div class="results-metrics" role="list" aria-label="Kennzahlen im Überblick">
					<div class="results-metric" role="listitem">
						<strong><?php echo esc_html( $e3_cpl_before ); ?></strong>
						<span>CPL vorher</span>
					</div>
					<div class="results-metric" role="listitem">
						<strong><?php echo esc_html( $e3_cpl_after ); ?></strong>
						<span>CPL nachher</span>
					</div>
					<div class="results-metric" role="listitem">
						<strong><?php echo esc_html( $e3_lead_count ); ?></strong>
						<span>Leads</span>
					</div>
					<div class="results-metric" role="listitem">
						<strong><?php echo esc_html( $e3_sales_rate ); ?></strong>
						<span>Sales-Conversion</span>
					</div>
				</div>

				<div class="results-hero__actions">
					<a href="<?php echo esc_url( $request_url ); ?>" class="nx-btn nx-btn--primary" data-track-action="cta_results_hero_request" data-track-category="lead_gen"><?php echo esc_html( $request_cta ); ?></a>
				</div>
			</div>
		</div>
	</section>

	<!-- 2. E3 New Energy -->
	<section class="nx-section results-case">
		<div class="nx-container">
			<article class="results-case-card results-case-card--success">
				<div class="results-case-card__content">
					<span class="results-case-card__kicker">Öffentlicher Methodik-Case · Solar &amp; Wärmepumpe</span>
					<h2 class="results-case-card__title">E3 New Energy</h2>
					<p class="results-case-card__context">
						Zwei parallele Anfrage-Quellen: Portal-Leads und Viessmann-Partner-Anfragen. Die Methodik im Detail.
					</p>

					<div class="results-case-card__stats">
						<span class="results-case-card__stat">Intent</span>
						<span class="results-case-card__stat">Exklusivität</span>
						<span class="results-case-card__stat">Vorqualifizierung</span>
						<span class="results-case-card__stat">Echtzeit</span>
					</div>

					<ul class="results-bullet-list">
						<li>Naturexperiment aus zwei Anfrage-Quellen</li>
						<li>Vier Eigenschaften strukturell rekonstruiert</li>
						<li>Drei Monate Implementierung, sechs Monate Optimierung</li>
					</ul>

					<a href="<?php echo esc_url( $e3_url ); ?>" class="nx-btn nx-btn--primary" data-track-action="cta_results_e3_methodology" data-track-category="trust">
						Methodik-Case lesen
					</a>
				</div>
			</article>
		</div>
	</section>

	<!-- 3. Laufender Proof -->
	<section class="nx-section results-case">
		<div class="nx-container">
			<article class="results-case-card results-case-card--gold">
				<div class="results-case-card__content">
					<span class="results-case-card__kicker">Laufender Proof · Solar / Wärmepumpe</span>
					<h2 class="results-case-card__title">Was sich in laufenden Mandaten wiederholt.</h2>
					<p class="results-case-card__context">
						Nicht mehr Leads um jeden Preis, sondern bessere Entscheidungssignale, niedrigere Kosten pro Anfrage und ein Vertrieb, der mit passenderen Kontakten arbeitet.
					</p>

					<div class="results-case-card__stats">
						<span class="results-case-card__stat"><?php echo esc_html( sprintf( '%1$s → %2$s CPL', $e3_cpl_before, $e3_cpl_after ) ); ?></span>
						<span class="results-case-card__stat"><?php echo esc_html( $e3_lead_count ); ?> Anfragen</span>
						<span class="results-case-card__stat">Bitrix24 + GTM SS</span>
					</div>

					<ul class="results-bullet-list">
						<li>Landingpages nach Entscheidungslogik statt nach Produktkategorien gebaut</li>
						<li>Consent, Tracking und CRM-Attribution bis zum Abschluss verbunden</li>
						<li>Die Seite lernt wieder, welche Anfrage tatsächlich Umsatz bringt</li>
					</ul>
				</div>
			</article>
		</div>
	</section>

	<!-- 5. Finaler CTA -->
	<section class="nx-section results-cta">
		<div class="nx-container">
			<div class="results-final-cta">
				<h2 class="results-final-cta__title">Prüfen wir Ihren Status&nbsp;quo.</h2>
				<p class="results-final-cta__copy">
					Wenn der Proof passt, ist der nächste sinnvolle Schritt die Anfrage-System-Analyse.
				</p>
				<div class="results-final-cta__actions">
					<a href="<?php echo esc_url( $request_url ); ?>" class="nx-btn nx-btn--primary" data-track-action="cta_results_footer_request" data-track-category="lead_gen"><?php echo esc_html( $request_cta ); ?></a>
				</div>
			</div>
		</div>
	</section>

</main>

<?php get_footer(); ?>
