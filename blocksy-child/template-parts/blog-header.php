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

$primary_urls = function_exists( 'nexus_get_primary_public_url_map' ) ? nexus_get_primary_public_url_map() : [];
$home_url     = $primary_urls['home'] ?? home_url( '/' );
$blog_url     = $primary_urls['blog'] ?? home_url( '/blog/' );
$wgos_url     = $primary_urls['wgos'] ?? home_url( '/wordpress-agentur-hannover/#wgos' );
$cases_url    = $primary_urls['results'] ?? nexus_get_results_url();
$about_url    = $primary_urls['about'] ?? home_url( '/uber-mich/' );
$audit_url    = $primary_urls['audit'] ?? nexus_get_audit_url();
$brand_text   = function_exists( 'hu_get_site_wordmark_text' ) ? hu_get_site_wordmark_text() : 'HAŞIM ÜNER';
$panel_id     = 'nx-blog-header-panel';
$home_label   = sprintf(
	/* translators: %s: site or brand name. */
	__( 'Startseite - %s', 'blocksy-child' ),
	$brand_text
);

$context_title = __( 'Insights Hub', 'blocksy-child' );
$context_text  = __( 'Erst verstehen, dann priorisieren: Insights lesen, Proof sehen, Audit starten.', 'blocksy-child' );
$context_links = [
	[
		'label'  => __( 'Alle Insights', 'blocksy-child' ),
		'url'    => $blog_url,
		'active' => is_home(),
	],
];

if ( is_category() ) {
	$context_title = single_cat_title( '', false );
	$context_text  = __( 'Kategorie-Ansicht mit direktem Weg zur Übersicht und zum nächsten sinnvollen Schritt.', 'blocksy-child' );
	$context_links[] = [
		'label'  => single_cat_title( '', false ),
		'url'    => get_category_link( get_queried_object_id() ),
		'active' => true,
	];
} elseif ( is_tag() ) {
	$context_title = single_tag_title( '', false );
	$context_text  = __( 'Tag-Archiv mit Rückweg zur Blog-Übersicht und klarer Hauptnavigation.', 'blocksy-child' );
	$context_links[] = [
		'label'  => single_tag_title( '', false ),
		'url'    => get_tag_link( get_queried_object_id() ),
		'active' => true,
	];
} elseif ( is_archive() && ! is_home() ) {
	$context_title = get_the_archive_title();
	$context_text  = __( 'Archivansicht mit Fokus auf Lesefluss, Überblick und nächstem Schritt.', 'blocksy-child' );
	$context_links[] = [
		'label'  => get_the_archive_title(),
		'url'    => get_post_type_archive_link( 'post' ) ?: $blog_url,
		'active' => true,
	];
} elseif ( is_singular( 'post' ) ) {
	$context_title = __( 'Artikel lesen', 'blocksy-child' );
	$context_text  = __( 'Zurück zur Übersicht oder direkt in die passende Kategorie wechseln.', 'blocksy-child' );

	$post_categories = get_the_category();

	if ( ! empty( $post_categories ) && ! is_wp_error( $post_categories ) ) {
		$primary_category = $post_categories[0];
		$context_links[]  = [
			'label'  => $primary_category->name,
			'url'    => get_category_link( $primary_category->term_id ),
			'active' => false,
		];
	}
}

$primary_items = [
	[
		'label'  => __( 'Start', 'blocksy-child' ),
		'url'    => $home_url,
		'active' => is_front_page(),
	],
	[
		'label'  => __( 'System', 'blocksy-child' ),
		'url'    => $wgos_url,
		'active' => is_page( nexus_get_page_id( [ 'wordpress-growth-operating-system', 'wgos' ] ) ),
	],
	[
		'label'  => __( 'Ergebnisse', 'blocksy-child' ),
		'url'    => $cases_url,
		'active' => nexus_is_results_context(),
	],
	[
		'label'  => __( 'Über Haşim', 'blocksy-child' ),
		'url'    => $about_url,
		'active' => is_page( nexus_get_page_id( [ 'uber-mich' ] ) ),
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

				<div class="nexus-blog-header__intro">
					<span class="nexus-blog-header__eyebrow"><?php esc_html_e( 'Blog Navigation', 'blocksy-child' ); ?></span>
					<p class="nexus-blog-header__title"><?php echo esc_html( $context_title ); ?></p>
					<p class="nexus-blog-header__text"><?php echo esc_html( $context_text ); ?></p>
				</div>
			</div>

			<nav class="nexus-blog-header__nav" aria-label="<?php esc_attr_e( 'Primäre Blog-Navigation', 'blocksy-child' ); ?>">
				<ul class="nexus-blog-header__menu">
					<?php foreach ( $primary_items as $item ) : ?>
						<li class="nexus-blog-header__menu-item">
							<a
								class="nexus-blog-header__menu-link<?php echo esc_attr( $item['active'] ? ' is-active' : '' ); ?>"
								href="<?php echo esc_url( $item['url'] ); ?>"
								<?php echo $item['active'] ? 'aria-current="page"' : ''; // raw-ok -- static attribute ?>
							>
								<?php echo esc_html( $item['label'] ); ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</nav>

			<div class="nexus-blog-header__actions">
				<a class="nexus-blog-header__cta nexus-blog-header__desktop-cta" href="<?php echo esc_url( $audit_url ); ?>">
					<?php esc_html_e( 'Audit starten', 'blocksy-child' ); ?>
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
					<a
						class="nexus-blog-header__mobile-link<?php echo esc_attr( $item['active'] ? ' is-active' : '' ); ?>"
						href="<?php echo esc_url( $item['url'] ); ?>"
						<?php echo $item['active'] ? 'aria-current="page"' : ''; // raw-ok -- static attribute ?>
					>
						<?php echo esc_html( $item['label'] ); ?>
					</a>
				<?php endforeach; ?>
			</nav>

			<div class="nexus-blog-header__mobile-actions">
				<a class="nexus-blog-header__cta" href="<?php echo esc_url( $audit_url ); ?>">
					<?php esc_html_e( 'Audit starten', 'blocksy-child' ); ?>
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
