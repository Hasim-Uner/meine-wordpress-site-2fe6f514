<?php
/**
 * Template Name: Glossar Hub
 * Description: Durchsuchbares Glossar für WordPress, SEO und Tracking.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$terms       = nexus_get_glossary_directory_items();
$areas       = nexus_get_glossary_area_catalog();
$project_url = hu_get_commercial_route( 'project_request', home_url( '/kontakt/' ) );
?>
<div class="site-main doku glossary">
	<div class="blatt">
		<header class="glossary-head">
			<p class="glossary-eyebrow">Glossar</p>
			<h1>WordPress, SEO und Tracking<br class="glossary-wide-break"> einfach erklärt<span class="stempelfarbe">.</span></h1>
			<p class="glossary-intro">Was bedeutet der Begriff? Wofür ist er wichtig? Kurze Erklärungen und Beispiele aus der Website-Praxis.</p>
		</header>
		<div class="glossary-directory" data-glossary-directory>
			<div class="glossary-controls" data-glossary-controls hidden>
				<div class="glossary-search">
					<label for="glossary-search">Begriff suchen</label>
					<input id="glossary-search" type="search" placeholder="Zum Beispiel LCP oder Kampagnenquelle" autocomplete="off" aria-controls="glossary-list" data-glossary-search>
				</div>
				<div class="glossary-filters" role="group" aria-label="Nach Thema filtern">
					<button type="button" class="glossary-filter" aria-pressed="true" aria-controls="glossary-list" data-glossary-filter="">Alle Themen</button>
					<?php foreach ( $areas as $area ) : ?>
						<button type="button" class="glossary-filter" aria-pressed="false" aria-controls="glossary-list" data-glossary-filter="<?php echo esc_attr( $area['id'] ); ?>"><?php echo esc_html( $area['label'] ); ?></button>
					<?php endforeach; ?>
				</div>
			</div>
			<div class="glossary-list-head">
				<h2 class="glossary-eyebrow">Begriffe von A–Z</h2>
				<p role="status" aria-live="polite" aria-atomic="true" data-glossary-count><?php echo esc_html( (string) count( $terms ) ); ?> Begriffe</p>
			</div>
			<ul id="glossary-list" class="glossary-list">
				<?php foreach ( $terms as $term ) : ?>
					<li class="glossary-item" data-glossary-item data-area="<?php echo esc_attr( $term['area_id'] ); ?>" data-search="<?php echo esc_attr( $term['search'] ); ?>">
						<a class="glossary-entry" href="<?php echo esc_url( $term['url'] ); ?>">
							<div class="glossary-entry-name">
								<span class="glossary-topic"><?php echo esc_html( $term['area_label'] ); ?></span>
								<h3><?php echo esc_html( $term['title'] ); ?></h3>
							</div>
							<div class="glossary-entry-copy">
								<p><?php echo esc_html( $term['excerpt'] ); ?></p>
								<?php if ( $term['is_primary'] ) : ?>
									<span class="glossary-destination">Weiter zur Themenseite</span>
								<?php endif; ?>
							</div>
							<span class="glossary-arrow" aria-hidden="true">↗</span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
			<div class="glossary-empty" data-glossary-empty hidden>
				<h3>Kein passender Begriff gefunden.</h3>
				<p>Versuchen Sie eine andere Schreibweise oder wählen Sie alle Themen.</p>
				<button type="button" class="tun still" data-glossary-reset>Suche zurücksetzen</button>
			</div>
		</div>
		<footer class="glossary-close">
			<p>Sie möchten etwas an Ihrer Website verbessern?</p>
			<a class="satzlink" href="<?php echo esc_url( $project_url ); ?>" data-track-action="cta_glossary_hub_project" data-track-category="lead_gen" data-track-section="glossary_close">Projekt anfragen →</a>
		</footer>
	</div>
</div>
<?php get_footer(); ?>
