<?php
/**
 * Minimal reader header for the first Article System V1 pilot posts.
 *
 * Keeps article reading separate from the commercial site navigation: one
 * brand link, one route back to the Werkstatt and one dossier link. No CTA,
 * dropdown or sticky full-site menu competes with the article itself.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$args = wp_parse_args(
	$args ?? [],
	[
		'slug' => '',
	]
);

$slug         = sanitize_title( (string) $args['slug'] );
$primary_urls = function_exists( 'nexus_get_primary_public_url_map' ) ? nexus_get_primary_public_url_map() : [];
$home_url     = $primary_urls['home'] ?? home_url( '/' );
$blog_url     = $primary_urls['blog'] ?? home_url( '/blog/' );
$dossier_url  = function_exists( 'nexus_get_category_url' )
	? nexus_get_category_url( 'leadgenerierung', $blog_url )
	: $blog_url;
$brand_text   = function_exists( 'hu_get_site_wordmark_text' ) ? hu_get_site_wordmark_text() : 'HAŞIM ÜNER';
$author_name  = get_the_author();
$reading_time = function_exists( 'nexus_get_reading_time' ) ? (int) nexus_get_reading_time() : 0;

$updated_label = get_the_modified_date( 'd. F Y' );
$template_map  = [
	'aroundhome-solar-einordnung'           => get_stylesheet_directory() . '/template-parts/aroundhome-decision-cockpit.php',
	'checkfox-solar-waermepumpe-einordnung' => get_stylesheet_directory() . '/template-parts/checkfox-decision-cockpit.php',
];

if ( isset( $template_map[ $slug ] ) && function_exists( 'hu_get_subpage_last_updated_label' ) ) {
	$template_updated_label = hu_get_subpage_last_updated_label( $template_map[ $slug ] );
	if ( '' !== $template_updated_label ) {
		$updated_label = $template_updated_label;
	}
}

$home_label = sprintf(
	/* translators: %s: site or brand name. */
	__( 'Startseite - %s', 'blocksy-child' ),
	$brand_text
);
?>

<header class="nexus-article-reader-header" role="banner" data-article-system-v1>
	<div class="nx-container nexus-article-reader-header__inner">
		<a
			class="nexus-article-reader-header__brand site-logo"
			href="<?php echo esc_url( $home_url ); ?>"
			rel="home"
			aria-label="<?php echo esc_attr( $home_label ); ?>"
		>
			<?php echo esc_html( $brand_text ); ?>
		</a>

		<nav class="nexus-article-reader-header__trail" aria-label="<?php esc_attr_e( 'Artikelpfad', 'blocksy-child' ); ?>">
			<a href="<?php echo esc_url( $blog_url ); ?>" data-track-action="article_reader_back_blog" data-track-category="navigation" data-track-section="article_reader_header">Werkstatt</a>
			<span aria-hidden="true">/</span>
			<a href="<?php echo esc_url( $dossier_url ); ?>" data-track-action="article_reader_open_dossier" data-track-category="navigation" data-track-section="article_reader_header">Eigene Anfragen &amp; Leadökonomie</a>
		</nav>

		<div class="nexus-article-reader-header__meta" aria-label="<?php esc_attr_e( 'Artikelmetadaten', 'blocksy-child' ); ?>">
			<span class="nexus-article-reader-header__author"><?php echo esc_html( $author_name ); ?></span>
			<?php if ( '' !== $updated_label ) : ?>
				<span><?php echo esc_html( sprintf( 'Aktualisiert %s', $updated_label ) ); ?></span>
			<?php endif; ?>
			<?php if ( $reading_time > 0 ) : ?>
				<span><?php echo esc_html( sprintf( '%d Min.', $reading_time ) ); ?></span>
			<?php endif; ?>
		</div>
	</div>
</header>
