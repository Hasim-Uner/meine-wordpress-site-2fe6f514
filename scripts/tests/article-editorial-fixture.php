<?php
/** WordPress boundary for editorial seed articles and a dated live layout snapshot, never live data. */
function add_shortcode( $tag, $callback ) { $GLOBALS['article_shortcodes'][ $tag ] = $callback; }
function shortcode_atts( $defaults, $atts, $name = '' ) { return array_merge( $defaults, (array) $atts ); }
function wp_parse_args( $args, $defaults = [] ) { return array_merge( $defaults, (array) $args ); }
function sanitize_text_field( $value ) { return strip_tags( (string) $value ); }
function number_format_i18n( $value, $decimals = 0 ) { return number_format( $value, $decimals, ',', '.' ); }
function hu_faq_schema_skip_attribute() { return 'data-hu-schema-skip'; }
function locate_template( $file ) { $path = get_stylesheet_directory() . '/' . $file; return is_file( $path ) ? $path : ''; }
function do_shortcode( $content ) {
	// Only the registered, quoted attributes in the repo seeds are supported.
	return preg_replace_callback( '~\[(hu_[a-z_]+)([^\]]*)\](?:(.*?)\[/\1\])?~s', static function ( $m ) {
		if ( ! isset( $GLOBALS['article_shortcodes'][ $m[1] ] ) ) { throw new RuntimeException( 'Unknown fixture shortcode: ' . $m[1] ); }
		preg_match_all( '~([a-z_]+)="([^"]*)"~', $m[2], $attrs, PREG_SET_ORDER );
		$atts = [];
		foreach ( $attrs as $attr ) { $atts[ $attr[1] ] = $attr[2]; }
		return ( $GLOBALS['article_shortcodes'][ $m[1] ] )( $atts, $m[3] ?? null );
	}, $content );
}
function the_post_thumbnail( $size = '', $attrs = [] ) {
	$image = $GLOBALS['article_test'];
	$src = $image['image_src'] ?? get_stylesheet_directory_uri() . '/assets/img/blog/' . $image['image'];
	echo '<img width="' . (int) ( $image['image_width'] ?? 2400 ) . '" height="' . (int) ( $image['image_height'] ?? 1260 ) . '" src="' . esc_url( $src ) . '" alt="' . esc_attr( $GLOBALS['article_test']['title'] ) . '"';
	foreach ( $attrs as $key => $value ) { echo ' ' . esc_attr( $key ) . '="' . esc_attr( $value ) . '"'; }
	echo '>';
}
foreach ( [ 'canon/market-canon', 'shortcodes', 'editorial-bausteine', 'wartung-rechner', 'article-website-relaunch' ] as $module ) {
	require_once get_stylesheet_directory() . '/inc/' . $module . '.php';
}
if ( in_array( $slug, [ 'wordpress-projekte-auslagern', 'b2b-landingpage-optimieren' ], true ) ) {
	$agency = 'wordpress-projekte-auslagern' === $slug;
	require_once get_stylesheet_directory() . '/inc/article-agency-outsourcing.php';
	require_once get_stylesheet_directory() . '/inc/article-content-hygiene-landingpage.php';
	$source = file_get_contents( $agency
		? get_stylesheet_directory() . '/assets/content/blog/wordpress-projekte-auslagern.html'
		: __DIR__ . '/../../docs/briefings/blog-agency-landingpage/landingpage-live-2026-11-01.html' );
	$GLOBALS['article_test'] = [
		'title' => $agency ? 'WordPress-Projekte auslagern: Klare Prozesse für Agenturen und Freelancer' : 'B2B-Landingpage optimieren: Struktur, Proof und CTA systematisch verbessern',
		'excerpt' => $agency ? 'WordPress-Projekte extern umsetzen lassen: So organisieren Agenturen Scope, Handoff, Staging, QA, Tracking, Deployment und Handover mit Freelancern.' : hu_landingpage_article_reader_excerpt(),
		'content' => $source,
		'image' => true,
		'image_src' => 'https://hasimuener.de/__article_fixture__/' . ( $agency ? 'agency' : 'landing' ) . '-cover.png',
		'image_width' => $agency ? 1672 : 1200,
		'image_height' => $agency ? 941 : 1200,
		'served' => false,
		'scripts' => [ 'leiste', 'nexus-core', 'single-editorial', 'article-reader-toc' ],
	];
	$GLOBALS['nav_test']['path'] = '/' . $slug . '/';
	$GLOBALS['nav_test']['post']['categories'] = [ $agency ? 'wordpress-performance' : 'cro' ];
	return;
}

$wartung = 'wordpress-wartung-kosten' === $slug;
$seed_path = get_stylesheet_directory() . '/inc/article-' . ( $wartung ? 'wordpress-wartung-kosten' : 'website-relaunch' ) . '.php';
$seed = file_get_contents( $seed_path );
preg_match( "~'post_excerpt'\\s*=>\\s*'([^']*)'~", $seed, $excerpt );
$GLOBALS['article_test'] = [
	'title' => $wartung ? 'WordPress-Wartung: Was sie kostet und was es kostet, wenn sie fehlt' : 'Website-Relaunch: Wann die neue Website wirklich besser ist',
	'excerpt' => $excerpt[1],
	'content' => do_shortcode( file_get_contents( get_stylesheet_directory() . '/assets/content/blog/' . $slug . '.html' ) ),
	'source' => file_get_contents( get_stylesheet_directory() . '/assets/content/blog/' . $slug . '.html' ),
	'image' => $wartung ? 'wordpress-wartung-kosten-hero.png' : 'website-relaunch-hero-v2.png',
	'date' => $wartung ? '26. Oktober 2026' : '24. September 2026',
	'served' => false,
	'scripts' => [ 'leiste', 'nexus-core', 'single-editorial', 'article-reader-toc', 'editorial-bausteine', 'wartung-rechner' ],
];
$GLOBALS['nav_test']['path'] = '/' . $slug . '/';
$GLOBALS['nav_test']['post']['categories'] = [ $wartung ? 'wordpress-performance' : 'cro' ];
