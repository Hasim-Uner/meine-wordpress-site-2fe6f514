<?php
/**
 * Blog area header fallback.
 *
 * Rendered on blog index, archive and single post views when the global
 * Blocksy header is intentionally disabled for the blog section.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$primary_urls       = function_exists( 'nexus_get_primary_public_url_map' ) ? nexus_get_primary_public_url_map() : [];
$home_url           = $primary_urls['home'] ?? home_url( '/' );
$blog_url           = $primary_urls['blog'] ?? home_url( '/blog/' );
$energy_url         = $primary_urls['energy'] ?? ( function_exists( 'nexus_get_energy_systems_url' ) ? nexus_get_energy_systems_url() : home_url( '/solar-waermepumpen-leadgenerierung/' ) );
$agentur_url        = $primary_urls['agentur'] ?? home_url( '/wordpress-agentur-hannover/' );
$cases_url          = $primary_urls['results'] ?? ( function_exists( 'nexus_get_results_url' ) ? nexus_get_results_url() : home_url( '/ergebnisse/' ) );
$about_url          = $primary_urls['about'] ?? home_url( '/uber-mich/' );
$audit_url          = $primary_urls['audit'] ?? ( function_exists( 'nexus_get_audit_url' ) ? nexus_get_audit_url() : home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' ) );
$brand_text         = function_exists( 'hu_get_site_wordmark_text' ) ? hu_get_site_wordmark_text() : 'HAŞIM ÜNER';
$panel_id           = 'nx-blog-header-panel';
$about_page_id      = function_exists( 'nexus_get_page_id' ) ? nexus_get_page_id( [ 'uber-mich' ] ) : 0;
$is_blog_area       = is_home() || is_archive() || is_singular( 'post' );
$is_energy_context  = function_exists( 'nexus_is_energy_systems_context' ) && nexus_is_energy_systems_context();
$is_agency_context  = is_page( 'wordpress-agentur-hannover' ) || is_page_template( 'page-wordpress-agentur.php' );
$is_results_context = function_exists( 'nexus_is_results_context' ) && nexus_is_results_context();
$home_label         = sprintf(
	/* translators: %s: site or brand name. */
	__( 'Startseite - %s', 'blocksy-child' ),
	$brand_text
);

$context_title = __( 'Blog', 'blocksy-child' );
$context_text  = __( 'Analysen zu Anfrage-Systemen, Portal-Kosten, Tracking und Conversion.', 'blocksy-child' );
$context_links = [
	[
		'label'  => __( 'Alle Analysen', 'blocksy-child' ),
		'url'    => $blog_url,
		'active' => is_home(),
	],
];

if ( is_category() ) {
	$queried_term  = get_queried_object();
	$context_title = $queried_term instanceof WP_Term && function_exists( 'hu_get_public_category_label' ) ? hu_get_public_category_label( $queried_term ) : single_cat_title( '', false );
	$context_text  = __( 'Beiträge zu einem Thema, mit Rückweg zur Übersicht und direktem nächsten Schritt.', 'blocksy-child' );
	$context_links[] = [
		'label'  => $context_title,
		'url'    => get_category_link( get_queried_object_id() ),
		'active' => true,
	];
} elseif ( is_tag() ) {
	$context_title = single_tag_title( '', false );
	$context_text  = __( 'Tag-Archiv mit Rückweg zur Übersicht und klarer Hauptnavigation.', 'blocksy-child' );
	$context_links[] = [
		'label'  => single_tag_title( '', false ),
		'url'    => get_tag_link( get_queried_object_id() ),
		'active' => true,
	];
} elseif ( is_archive() && ! is_home() ) {
	$context_title = get_the_archive_title();
	$context_text  = __( 'Archivansicht mit Überblick, Lesefluss und nächstem Schritt.', 'blocksy-child' );
	$context_links[] = [
		'label'  => get_the_archive_title(),
		'url'    => get_post_type_archive_link( 'post' ) ?: $blog_url,
		'active' => true,
	];
} elseif ( is_singular( 'post' ) ) {
	$context_title = __( 'Artikel', 'blocksy-child' );
	$context_text  = __( 'Zurück zur Übersicht, passende Kategorie öffnen oder Marktcheck starten.', 'blocksy-child' );

	$post_categories = get_the_category();

	if ( ! empty( $post_categories ) && ! is_wp_error( $post_categories ) ) {
		$primary_category = $post_categories[0];
		$context_links[]  = [
			'label'  => function_exists( 'hu_get_public_category_label' ) ? hu_get_public_category_label( $primary_category ) : $primary_category->name,
			'url'    => get_category_link( $primary_category->term_id ),
			'active' => false,
		];
	}
}

$primary_items = [
	[
		'label'   => __( 'Blog', 'blocksy-child' ),
		'url'     => $blog_url,
		'active'  => $is_blog_area,
		'current' => is_home(),
	],
	[
		'label'   => __( 'Solar & Wärmepumpen', 'blocksy-child' ),
		'url'     => $energy_url,
		'active'  => $is_energy_context,
		'current' => $is_energy_context,
	],
	[
		'label'   => __( 'WordPress Agentur', 'blocksy-child' ),
		'url'     => $agentur_url,
		'active'  => $is_agency_context,
		'current' => $is_agency_context,
	],
	[
		'label'   => __( 'Ergebnisse', 'blocksy-child' ),
		'url'     => $cases_url,
		'active'  => $is_results_context,
		'current' => $is_results_context,
	],
	[
		'label'   => __( 'Über Haşim', 'blocksy-child' ),
		'url'     => $about_url,
		'active'  => $about_page_id ? is_page( $about_page_id ) : false,
		'current' => $about_page_id ? is_page( $about_page_id ) : false,
	],
];
?>

<header class="nexus-blog-header" data-site-header role="banner">
	<div class="nx-container nexus-blog-header__frame">
		<div class="nexus-blog-header__shell">
			<div class="nexus-blog-header__brand-block">
				<a
					class="nexus-blog-header__brand site-logo"
					href="<?php echo esc_url( $home_url ); ?>"
					rel="home"
					aria-label="<?php echo esc_attr( $home_label ); ?>"
				>
					<?php echo esc_html( $brand_text ); ?>
				</a>

				<div class="nexus-blog-header__intro" aria-label="<?php esc_attr_e( 'Blog-Kontext', 'blocksy-child' ); ?>">
					<span class="nexus-blog-header__eyebrow"><?php esc_html_e( 'Analysen', 'blocksy-child' ); ?></span>
					<p class="nexus-blog-header__title"><?php echo esc_html( $context_title ); ?></p>
				</div>
			</div>

			<nav class="nexus-blog-header__nav" aria-label="<?php esc_attr_e( 'Primäre Blog-Navigation', 'blocksy-child' ); ?>">
				<ul class="nexus-blog-header__menu">
					<?php foreach ( $primary_items as $item ) : ?>
						<?php
						$is_active  = ! empty( $item['active'] );
						$is_current = ! empty( $item['current'] );
						?>
						<li class="nexus-blog-header__menu-item">
							<a
								class="nexus-blog-header__menu-link<?php echo esc_attr( $is_active ? ' is-active' : '' ); ?>"
								href="<?php echo esc_url( $item['url'] ); ?>"
								<?php echo $is_current ? 'aria-current="page"' : ''; // raw-ok -- static attribute ?>
							>
								<?php echo esc_html( $item['label'] ); ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</nav>

			<div class="nexus-blog-header__actions">
				<a
					class="nexus-blog-header__cta nexus-blog-header__desktop-cta"
					href="<?php echo esc_url( $audit_url ); ?>"
					data-track-action="cta_blog_header_marktcheck"
					data-track-category="lead_gen"
				>
					<?php esc_html_e( 'Marktcheck', 'blocksy-child' ); ?>
				</a>

				<button
					type="button"
					class="nexus-blog-header__toggle"
					data-site-header-toggle
					aria-expanded="false"
					aria-controls="<?php echo esc_attr( $panel_id ); ?>"
					aria-label="<?php esc_attr_e( 'Navigation öffnen', 'blocksy-child' ); ?>"
				>
					<span class="nexus-blog-header__toggle-lines" aria-hidden="true">
						<span class="nexus-blog-header__toggle-line"></span>
						<span class="nexus-blog-header__toggle-line"></span>
						<span class="nexus-blog-header__toggle-line"></span>
					</span>
					<span class="nexus-blog-header__toggle-label"><?php esc_html_e( 'Menü', 'blocksy-child' ); ?></span>
				</button>
			</div>
		</div>

		<div id="<?php echo esc_attr( $panel_id ); ?>" class="nexus-blog-header__mobile-panel" data-site-header-panel hidden>
			<p class="nexus-blog-header__mobile-context"><?php echo esc_html( $context_text ); ?></p>

			<nav class="nexus-blog-header__mobile-nav" aria-label="<?php esc_attr_e( 'Mobiles Blog-Menü', 'blocksy-child' ); ?>">
				<?php foreach ( $primary_items as $item ) : ?>
					<?php
					$is_active  = ! empty( $item['active'] );
					$is_current = ! empty( $item['current'] );
					?>
					<a
						class="nexus-blog-header__mobile-link<?php echo esc_attr( $is_active ? ' is-active' : '' ); ?>"
						href="<?php echo esc_url( $item['url'] ); ?>"
						<?php echo $is_current ? 'aria-current="page"' : ''; // raw-ok -- static attribute ?>
					>
						<?php echo esc_html( $item['label'] ); ?>
					</a>
				<?php endforeach; ?>
			</nav>

			<div class="nexus-blog-header__mobile-actions">
				<a
					class="nexus-blog-header__cta"
					href="<?php echo esc_url( $audit_url ); ?>"
					data-track-action="cta_blog_header_mobile_marktcheck"
					data-track-category="lead_gen"
				>
					<?php esc_html_e( 'Marktcheck starten', 'blocksy-child' ); ?>
				</a>
			</div>
		</div>
	</div>

	<?php if ( count( $context_links ) > 1 ) : ?>
		<nav class="nx-container nexus-blog-header__context-links" aria-label="<?php esc_attr_e( 'Blog-Kontext', 'blocksy-child' ); ?>">
			<?php foreach ( $context_links as $link ) : ?>
				<a
					class="nexus-blog-header__context-link<?php echo ! empty( $link['active'] ) ? ' is-active' : ''; ?>"
					href="<?php echo esc_url( $link['url'] ); ?>"
					<?php echo ! empty( $link['active'] ) ? 'aria-current="page"' : ''; ?>
				>
					<?php echo esc_html( $link['label'] ); ?>
				</a>
			<?php endforeach; ?>
		</nav>
	<?php endif; ?>
</header>
