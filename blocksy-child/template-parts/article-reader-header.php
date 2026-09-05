<?php
/**
 * Minimal reader header for Article System posts.
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
$brand_text   = function_exists( 'hu_get_site_wordmark_text' ) ? hu_get_site_wordmark_text() : 'HAŞIM ÜNER';
$author_name  = get_the_author();
$reading_time = function_exists( 'nexus_get_reading_time' ) ? (int) nexus_get_reading_time() : 0;

// Prefer the canonical Werkstatt taxonomy. A small route map keeps the reader
// context intentional for the flagship articles even when an older WordPress
// category assignment still reflects the previous blog model.
$dossier_priority = [
	'wordpress-performance',
	'tracking',
	'cro',
	'leadgenerierung',
];
$dossier_labels = [
	'leadgenerierung'       => 'Eigene Anfragen & Leadökonomie',
	'wordpress-performance' => 'WordPress & Performance',
	'tracking'              => 'Tracking & Messbarkeit',
	'cro'                   => 'Conversion & Anfragearchitektur',
];
$slug_dossier_overrides = [
	'aroundhome-solar-einordnung'           => 'leadgenerierung',
	'checkfox-solar-waermepumpe-einordnung' => 'leadgenerierung',
	'wattfox-solar-leads-einordnung'         => 'leadgenerierung',
	'wordpress-ttfb-google-ads-ladezeit'     => 'wordpress-performance',
	'server-side-tracking-gtm'               => 'tracking',
	'b2b-landingpage-optimieren'              => 'cro',
	'wordpress-seo-keine-anfragen'            => 'cro',
];

if ( function_exists( 'hu_get_positioned_blog_dossier_taxonomy' ) ) {
	$canonical_dossiers = hu_get_positioned_blog_dossier_taxonomy();
	foreach ( $canonical_dossiers as $dossier_key => $dossier_data ) {
		if ( ! empty( $dossier_data['name'] ) ) {
			$dossier_labels[ (string) $dossier_key ] = (string) $dossier_data['name'];
		}
	}
}

$post_categories = get_the_category();
$post_cat_slugs  = ! empty( $post_categories ) && ! is_wp_error( $post_categories )
	? array_map( 'strval', wp_list_pluck( $post_categories, 'slug' ) )
	: [];
$dossier_slug = $slug_dossier_overrides[ $slug ] ?? '';

if ( '' === $dossier_slug ) {
	foreach ( $dossier_priority as $candidate_slug ) {
		if ( in_array( $candidate_slug, $post_cat_slugs, true ) ) {
			$dossier_slug = $candidate_slug;
			break;
		}
	}
}

if ( '' === $dossier_slug ) {
	$dossier_slug = 'leadgenerierung';
}

$dossier_label = $dossier_labels[ $dossier_slug ] ?? 'Werkstatt';
$dossier_url   = function_exists( 'nexus_get_category_url' )
	? nexus_get_category_url( $dossier_slug, $blog_url )
	: $blog_url;

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

// The reader partial is rendered after wp_head(), so its small, route-specific
// presentation layer is emitted inline here instead of being enqueued too late.
$reader_body_css_path = get_stylesheet_directory() . '/assets/css/article-reader-body.css';
$reader_body_css      = is_readable( $reader_body_css_path ) ? file_get_contents( $reader_body_css_path ) : '';
$reader_share_js_url  = get_stylesheet_directory_uri() . '/assets/js/article-reader-share.js';
$reader_share_js_path = get_stylesheet_directory() . '/assets/js/article-reader-share.js';
$reader_share_version = function_exists( 'hu_get_asset_version' ) ? hu_get_asset_version( $reader_share_js_path ) : wp_get_theme()->get( 'Version' );
?>

<?php if ( is_string( $reader_body_css ) && '' !== $reader_body_css ) : ?>
	<style id="nexus-article-reader-body-css"><?php echo $reader_body_css; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted local theme CSS. ?></style>
<?php endif; ?>

<header
	class="nexus-article-reader-header"
	role="banner"
	data-article-system-v1
	data-article-system="v2"
	data-reader-dossier="<?php echo esc_attr( $dossier_slug ); ?>"
>
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
			<a href="<?php echo esc_url( $dossier_url ); ?>" data-track-action="article_reader_open_dossier" data-track-category="navigation" data-track-section="article_reader_header"><?php echo esc_html( $dossier_label ); ?></a>
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

<script id="nexus-article-reader-share-loader" src="<?php echo esc_url( add_query_arg( 'ver', rawurlencode( (string) $reader_share_version ), $reader_share_js_url ) ); ?>"></script>

<?php if ( 'leadgenerierung' !== $dossier_slug ) : ?>
	<style id="nexus-article-reader-dossier-label">
		.nexus-article-reader-header ~ .nexus-single-container .nexus-article-hero--editorial::before {
			content: <?php echo wp_json_encode( $dossier_label ); ?>;
		}

		/* The generic single template still carries an Energy-first author CTA
		   and next-step fallback. Until that global contract is refactored, keep
		   those surfaces out of non-Energy reader posts instead of showing a
		   Solar Marktcheck below a WordPress, Tracking or CRO article. */
		.nexus-article-reader-header ~ .nexus-single-container .nexus-author-bio,
		.nexus-article-reader-header ~ .nexus-single-container .nexus-article-next {
			display: none !important;
		}
	</style>
<?php endif; ?>