<?php
/**
 * Generated article title visual.
 *
 * Used when a post has no WordPress featured image, so archive cards and
 * article heroes still keep a strong editorial visual without media uploads.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$args    = is_array( $args ?? null ) ? $args : [];
$post_id = isset( $args['post_id'] ) ? (int) $args['post_id'] : get_the_ID();
$variant = isset( $args['variant'] ) ? sanitize_html_class( (string) $args['variant'] ) : 'card';

if ( $post_id <= 0 ) {
	return;
}

$title      = get_the_title( $post_id );
$categories = get_the_category( $post_id );
$cat_slugs  = ! empty( $categories ) && ! is_wp_error( $categories ) ? wp_list_pluck( $categories, 'slug' ) : [];
$cat_names  = ! empty( $categories ) && ! is_wp_error( $categories ) ? wp_list_pluck( $categories, 'name' ) : [];
$title_key  = function_exists( 'remove_accents' ) ? remove_accents( strtolower( $title ) ) : strtolower( $title );
$style      = 'growth';

if (
	in_array( 'solar-waermepumpen-anfrage-systeme', $cat_slugs, true )
	|| preg_match( '/solar|pv|photovoltaik|waermepumpe|wärmepumpe|aroundhome|wattfox|daa|leadfluss|portal|lead/i', $title_key )
) {
	$style = 'solar';
} elseif (
	in_array( 'sichtbarkeit-daten-conversion', $cat_slugs, true )
	|| in_array( 'seo', $cat_slugs, true )
	|| in_array( 'tracking', $cat_slugs, true )
	|| preg_match( '/seo|tracking|analytics|daten|server|core web vitals|performance/i', $title_key )
) {
	$style = 'data';
} elseif (
	in_array( 'cro', $cat_slugs, true )
	|| preg_match( '/conversion|cro|ux|design|landingpage|formular/i', $title_key )
) {
	$style = 'conversion';
} elseif (
	in_array( 'wordpress-growth-agentur', $cat_slugs, true )
	|| preg_match( '/wordpress|agentur|wartung|cms/i', $title_key )
) {
	$style = 'wordpress';
}

$visuals = [
	'solar'      => [
		'eyebrow' => 'Solar / SHK',
		'title'   => 'Eigene Anfrage-Infrastruktur',
		'meta'    => 'Portal-Alternative',
		'nodes'   => [ 'SEO', 'Tracking', 'Vorqualifizierung' ],
	],
	'data'       => [
		'eyebrow' => 'SEO / Daten',
		'title'   => 'Sichtbarkeit wird messbar',
		'meta'    => 'Technik + Attribution',
		'nodes'   => [ 'Crawl', 'Events', 'CRO' ],
	],
	'conversion' => [
		'eyebrow' => 'CRO / UX',
		'title'   => 'Aus Besuchern werden Anfragen',
		'meta'    => 'Message Match',
		'nodes'   => [ 'Intent', 'Proof', 'CTA' ],
	],
	'wordpress'  => [
		'eyebrow' => 'WordPress',
		'title'   => 'Website als Anfrage-System',
		'meta'    => 'Architektur + Betrieb',
		'nodes'   => [ 'Speed', 'SEO', 'Wartung' ],
	],
	'growth'     => [
		'eyebrow' => ! empty( $cat_names[0] ) ? (string) $cat_names[0] : 'Analyse',
		'title'   => 'Strategischer Wachstumshebel',
		'meta'    => 'Einordnung',
		'nodes'   => [ 'Problem', 'Hebel', 'Priorität' ],
	],
];

$visual = $visuals[ $style ] ?? $visuals['growth'];
?>

<div class="hu-post-visual hu-post-visual--<?php echo esc_attr( $style ); ?> hu-post-visual--<?php echo esc_attr( $variant ); ?>" aria-hidden="true">
	<div class="hu-post-visual__grid"></div>
	<div class="hu-post-visual__frame">
		<span class="hu-post-visual__eyebrow"><?php echo esc_html( $visual['eyebrow'] ); ?></span>
		<strong class="hu-post-visual__title"><?php echo esc_html( $visual['title'] ); ?></strong>
		<span class="hu-post-visual__meta"><?php echo esc_html( $visual['meta'] ); ?></span>
		<div class="hu-post-visual__flow">
			<?php foreach ( $visual['nodes'] as $node ) : ?>
				<span class="hu-post-visual__node"><?php echo esc_html( $node ); ?></span>
			<?php endforeach; ?>
		</div>
	</div>
</div>
