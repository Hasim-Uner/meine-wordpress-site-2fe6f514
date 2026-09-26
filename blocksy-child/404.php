<?php
/**
 * 404 Error Template
 *
 * Crawling-Hygiene: Suchfunktion + Top-Seiten-Links.
 * noindex wird zentral in inc/seo-meta.php gesteuert.
 *
 * [SEO] 404.php: Suchfunktion + Top-Seiten-Links
 *
 * @package Blocksy_Child
 */

get_header();

$primary_urls = function_exists( 'nexus_get_primary_public_url_map' ) ? nexus_get_primary_public_url_map() : [];

/*
 * Die Wege zurueck sind dieselben wie im Kopf: Startseite, die vier Routen
 * aus hu_get_site_header_navigation_contract() und der Blog. Frueher stand
 * hier eine eigene Liste mit dem Marktcheck als seitenweitem Einstieg und
 * einem SEO-Anker, den die Zielseite nicht mehr hat. Eine 404 ist kein Ort
 * fuer einen Solar-Einstieg; wer ihn sucht, findet ihn ueber Solar &
 * Waermepumpe.
 */
$nav_contract   = function_exists( 'hu_get_site_header_navigation_contract' ) ? hu_get_site_header_navigation_contract() : [];
$recovery_links = [
	[
		'label' => __( 'Startseite', 'blocksy-child' ),
		'url'   => home_url( '/' ),
		'track' => '404_nav_home',
	],
];

foreach ( (array) ( $nav_contract['routes'] ?? [] ) as $nav_route ) {
	$recovery_links[] = [
		'label' => (string) ( $nav_route['label'] ?? '' ),
		'url'   => (string) ( $nav_route['url'] ?? home_url( '/' ) ),
		'track' => '404_nav_' . sanitize_key( str_replace( 'nav_header_', '', (string) ( $nav_route['track'] ?? '' ) ) ),
	];
}

$recovery_links[] = [
	'label' => __( 'Blog', 'blocksy-child' ),
	'url'   => $primary_urls['blog'] ?? home_url( '/blog/' ),
	'track' => '404_nav_blog',
];
?>

<div class="site-main nexus-404-container">

	<?php get_template_part( 'template-parts/breadcrumb' ); ?>

	<section class="nexus-404" data-track-section="error_404">
		<div class="nexus-container">

			<span class="nexus-badge"><?php esc_html_e( '404 – Seite nicht gefunden', 'blocksy-child' ); ?></span>

			<h1 class="nexus-404__title">
				<?php esc_html_e( 'Diese Seite existiert nicht.', 'blocksy-child' ); ?>
			</h1>

			<p class="nexus-404__text">
				<?php esc_html_e( 'Die angeforderte Seite wurde verschoben, umbenannt oder existierte nie. Nutzen Sie die Suche oder einen der Wege darunter.', 'blocksy-child' ); ?>
			</p>

			<!-- Suche -->
			<div class="nexus-404__search">
				<?php get_search_form(); ?>
			</div>

			<!-- Wege zurueck: dieselben Ziele wie der Kopf -->
			<div class="nexus-404__links">
				<h2 id="nexus-404-wege"><?php esc_html_e( 'Wichtige Seiten', 'blocksy-child' ); ?></h2>
				<nav aria-labelledby="nexus-404-wege">
					<ul class="nexus-404__link-list">
						<?php foreach ( $recovery_links as $recovery_link ) : ?>
							<li>
								<a href="<?php echo esc_url( $recovery_link['url'] ); ?>"
								   data-track-action="<?php echo esc_attr( $recovery_link['track'] ); ?>"
								   data-track-category="error_recovery">
									<?php echo esc_html( $recovery_link['label'] ); ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</nav>
			</div>

		</div>
	</section>

</div>

<?php
get_footer();
