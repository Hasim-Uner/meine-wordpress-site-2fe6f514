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
?>

<main id="main" class="site-main nexus-404-container">

	<?php get_template_part( 'template-parts/breadcrumb' ); ?>

	<section class="nexus-404" data-track-section="error_404">
		<div class="nexus-container">

			<span class="nexus-badge"><?php esc_html_e( '404 – Seite nicht gefunden', 'blocksy-child' ); ?></span>

			<h1 class="nexus-404__title">
				<?php esc_html_e( 'Diese Seite existiert nicht.', 'blocksy-child' ); ?>
			</h1>

			<p class="nexus-404__text">
				<?php esc_html_e( 'Die angeforderte Seite wurde verschoben, umbenannt oder existierte nie. Nutzen Sie die Suche oder navigieren Sie zu unseren Top-Seiten.', 'blocksy-child' ); ?>
			</p>

			<!-- Suche -->
			<div class="nexus-404__search">
				<?php get_search_form(); ?>
			</div>

			<!-- Top-Seiten-Links -->
			<div class="nexus-404__links">
				<h2><?php esc_html_e( 'Beliebte Seiten', 'blocksy-child' ); ?></h2>
				<nav aria-label="<?php esc_attr_e( 'Beliebte Seiten', 'blocksy-child' ); ?>">
					<ul class="nexus-404__link-list">
						<li>
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>"
							   data-track-action="404_nav_home"
							   data-track-category="error_recovery">
								<?php esc_html_e( 'Startseite', 'blocksy-child' ); ?>
							</a>
						</li>
						<li>
							<a href="<?php echo esc_url( nexus_get_audit_url() ); ?>"
							   data-track-action="404_nav_audit"
							   data-track-category="error_recovery">
								<?php esc_html_e( 'System-Diagnose', 'blocksy-child' ); ?>
							</a>
						</li>
						<li>
							<a href="<?php echo esc_url( $primary_urls['seo'] ?? home_url( '/wordpress-agentur-hannover/#technisches-seo' ) ); ?>"
							   data-track-action="404_nav_seo"
							   data-track-category="error_recovery">
								<?php esc_html_e( 'WordPress SEO Hannover', 'blocksy-child' ); ?>
							</a>
						</li>
						<li>
							<a href="<?php echo esc_url( $primary_urls['blog'] ?? home_url( '/blog/' ) ); ?>"
							   data-track-action="404_nav_blog"
							   data-track-category="error_recovery">
								<?php esc_html_e( 'Blog – Strategische Impulse', 'blocksy-child' ); ?>
							</a>
						</li>
					</ul>
				</nav>
			</div>

		</div>
	</section>

</main>

<?php
get_footer();
