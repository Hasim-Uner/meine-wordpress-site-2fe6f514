<?php
/**
 * Render the real header, footer and 404 navigation without booting WordPress.
 *
 * Loads the actual routing, canon and helper modules and replaces only the
 * WordPress boundary: request context (path, front page, page slug,
 * template), escaping and URL helpers. Page lookups return nothing, so every
 * resolver uses its hardcoded default — the same defaults the live site
 * serves (see scripts/lint-entity-crawler-signals.php).
 *
 * Used by scripts/tests/navigation-contract.php (assertions) and
 * scripts/tests/render-navigation.php (HTML for navigation.spec.cjs).
 */

define( 'ABSPATH', __DIR__ );

$GLOBALS['nav_test'] = [];

/**
 * Request contexts the navigation distinguishes.
 *
 * @return array<string, array<string, mixed>>
 */
function nav_test_contexts() {
	return [
		'home'          => [ 'path' => '/', 'front' => true, 'page' => '', 'template' => '' ],
		'tracking'      => [ 'path' => '/ga4-tracking-setup/', 'front' => false, 'page' => '', 'template' => '' ],
		'server_side'   => [ 'path' => '/server-side-tracking-b2b/', 'front' => false, 'page' => 'server-side-tracking-b2b', 'template' => 'page-server-side-tracking-b2b.php' ],
		'case_study'    => [ 'path' => '/case-study-solar-leadgenerierung/', 'front' => false, 'page' => 'case-study-solar-leadgenerierung', 'template' => '' ],
		'about'         => [ 'path' => '/hasim-uener/', 'front' => false, 'page' => 'hasim-uener', 'template' => 'page-hasim-uener.php' ],
		'contact'       => [ 'path' => '/kontakt/', 'front' => false, 'page' => 'kontakt', 'template' => 'page-kontakt.php' ],
		'agentur_local' => [ 'path' => '/wordpress-agentur-hannover/', 'front' => false, 'page' => 'wordpress-agentur-hannover', 'template' => 'page-wordpress-agentur.php' ],
		'imprint'       => [ 'path' => '/impressum/', 'front' => false, 'page' => 'impressum', 'template' => 'page-impressum.php' ],
		'not_found'     => [ 'path' => '/gibt-es-nicht/', 'front' => false, 'page' => '', 'template' => '' ],
		'whitelabel'    => [ 'path' => '/whitelabel-retainer/', 'front' => false, 'page' => 'whitelabel-retainer', 'template' => 'page-whitelabel-retainer.php' ],
		// Solar-Seite: Leiste im Modus fokus (Wortmarke und Leiter der Seite).
		'solar'         => [ 'path' => '/solar-waermepumpen-leadgenerierung/', 'front' => false, 'page' => 'solar-waermepumpen-leadgenerierung', 'template' => 'page-solar-waermepumpen-leadgenerierung.php' ],
		// Einzelbeitraege: Slug und Kategorien bestimmen Dossier und Tuer.
		'portal'        => [ 'path' => '/blog/checkfox-solar-waermepumpe-einordnung/', 'front' => false, 'page' => '', 'template' => '', 'post' => [ 'slug' => 'checkfox-solar-waermepumpe-einordnung', 'categories' => [ 'leadgenerierung' ] ] ],
		'article_lead'  => [ 'path' => '/blog/anfragen-statt-portale/', 'front' => false, 'page' => '', 'template' => '', 'post' => [ 'slug' => 'anfragen-statt-portale', 'categories' => [ 'leadgenerierung' ] ] ],
		'article_track' => [ 'path' => '/blog/ga4-consent-mode/', 'front' => false, 'page' => '', 'template' => '', 'post' => [ 'slug' => 'ga4-consent-mode', 'categories' => [ 'tracking' ] ] ],
		// Beitrag ohne Dossier-Kategorie: der Pfad faellt auf "leadgenerierung" zurueck, die Tuer nicht.
		'article_plain' => [ 'path' => '/blog/ohne-dossier/', 'front' => false, 'page' => '', 'template' => '', 'post' => [ 'slug' => 'ohne-dossier', 'categories' => [ 'allgemein' ] ] ],
		'article_cro'   => [ 'path' => '/blog/b2b-landingpage-optimieren/', 'front' => false, 'page' => '', 'template' => '', 'post' => [ 'slug' => 'b2b-landingpage-optimieren', 'categories' => [ 'cro' ] ] ],
	];
}

/**
 * Switch the simulated request.
 *
 * @param string $key Context key from nav_test_contexts().
 * @return void
 */
function nav_test_use_context( $key ) {
	$contexts = nav_test_contexts();

	if ( ! isset( $contexts[ $key ] ) ) {
		throw new InvalidArgumentException( "Unknown navigation context: {$key}" );
	}

	$GLOBALS['nav_test']    = $contexts[ $key ];
	$_SERVER['REQUEST_URI'] = $contexts[ $key ]['path'];
}

// --- WordPress boundary ----------------------------------------------------

function home_url( $path = '/' ) {
	$path = (string) $path;
	return 'https://hasimuener.de' . ( '' === $path || '/' === $path[0] ? $path : '/' . $path );
}
function content_url( $path = '' ) { return 'https://hasimuener.de/wp-content' . (string) $path; }
function get_stylesheet_directory() { return dirname( __DIR__, 2 ) . '/blocksy-child'; }
function get_stylesheet_directory_uri() { return 'https://hasimuener.de/wp-content/themes/blocksy-child'; }
function trailingslashit( $value ) { return rtrim( (string) $value, '/\\' ) . '/'; }
function untrailingslashit( $value ) { return rtrim( (string) $value, '/\\' ); }
function wp_parse_url( $url, $component = -1 ) { return -1 === $component ? parse_url( (string) $url ) : parse_url( (string) $url, $component ); }
function add_query_arg( $args, $url ) {
	return $url . ( false === strpos( $url, '?' ) ? '?' : '&' ) . urldecode( http_build_query( (array) $args ) );
}
function add_action( ...$args ) {}
function add_filter( ...$args ) {}
function remove_action( ...$args ) {}
function apply_filters( $hook, $value ) { return $value; }
function do_action( ...$args ) {}
function __( $text, $domain = '' ) { return $text; }
function esc_html__( $text, $domain = '' ) { return esc_html( $text ); }
function esc_attr__( $text, $domain = '' ) { return esc_attr( $text ); }
function esc_html_e( $text, $domain = '' ) { echo esc_html( $text ); }
function esc_attr_e( $text, $domain = '' ) { echo esc_attr( $text ); }
function esc_html( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function esc_attr( $value ) { return esc_html( $value ); }
function esc_url( $value, $protocols = null ) { return esc_html( $value ); }
function esc_url_raw( $value ) { return (string) $value; }
function wp_kses_post( $value ) { return $value; }
function wp_json_encode( $value ) { return json_encode( $value ); }
function sanitize_key( $key ) { return preg_replace( '/[^a-z0-9_\-]/', '', strtolower( (string) $key ) ); }
function sanitize_title( $title ) { return trim( (string) preg_replace( '/[^a-z0-9]+/', '-', strtolower( (string) $title ) ), '-' ); }
function sanitize_html_class( $value ) { return preg_replace( '/[^A-Za-z0-9_-]/', '', (string) $value ); }
function absint( $value ) { return abs( (int) $value ); }
function wp_unslash( $value ) { return $value; }
function wp_strip_all_tags( $value ) { return trim( strip_tags( (string) $value ) ); }
function wp_get_theme() { return new class { public function get( $key ) { return '1.0'; } }; }
function get_option( $option, $default = false ) { return 'blog_charset' === $option ? 'UTF-8' : $default; }
function get_bloginfo( $key = '' ) { return 'Haşim Üner'; }
function get_page_by_path( ...$args ) { return null; }
function get_permalink( ...$args ) { return false; }
function get_posts( ...$args ) { return []; }
function get_post_status( ...$args ) { return false; }
function get_term_by( ...$args ) { return false; }
function get_term_link( ...$args ) { return false; }
function is_wp_error( $thing ) { return false; }
function wp_date( $format ) { return gmdate( $format, 1790000000 ); }
function is_admin() { return false; }
function is_feed() { return false; }
function wp_is_json_request() { return false; }
function wp_doing_ajax() { return false; }
function is_front_page() { return ! empty( $GLOBALS['nav_test']['front'] ); }
function is_home() { return false; }
function is_archive() { return false; }
function is_singular( $type = '' ) {
	if ( ! empty( $GLOBALS['nav_test']['post'] ) ) {
		return '' === $type || 'post' === $type;
	}
	return 'post' !== $type && '' !== ( $GLOBALS['nav_test']['page'] ?? '' );
}
function get_queried_object_id() { return 1; }
function get_post_field( $field, $post = null ) {
	return 'post_name' === $field ? (string) ( $GLOBALS['nav_test']['post']['slug'] ?? '' ) : '';
}
function get_the_category( $post = null ) {
	return array_map(
		static function ( $slug ) { return (object) [ 'slug' => $slug, 'name' => $slug, 'term_id' => 1 ]; },
		(array) ( $GLOBALS['nav_test']['post']['categories'] ?? [] )
	);
}
function wp_list_pluck( $list, $field ) {
	return array_map( static function ( $item ) use ( $field ) { return is_object( $item ) ? $item->$field : $item[ $field ]; }, (array) $list );
}
function is_page( $page = '' ) {
	$current = (string) ( $GLOBALS['nav_test']['page'] ?? '' );
	if ( '' === $page ) {
		return '' !== $current;
	}
	return '' !== $current && in_array( $current, array_map( 'strval', (array) $page ), true );
}
function is_page_template( $template = '' ) {
	return '' !== $template && ( $GLOBALS['nav_test']['template'] ?? '' ) === $template;
}
function get_header() {}
function get_footer() {}
function get_search_form() { echo '<form role="search"></form>'; }
function get_template_part( $slug, $name = null, $args = [] ) {
	if ( 'template-parts/breadcrumb' === $slug ) {
		return;
	}
	require get_stylesheet_directory() . '/' . $slug . '.php';
}

// hu_get_site_wordmark_text() lives in inc/theme-setup.php, which this harness does not load.
function hu_get_site_wordmark_text() { return 'HAŞIM ÜNER'; }

$theme = get_stylesheet_directory() . '/inc/';

foreach ( [
	'helpers.php',
	'feature-flags.php',
	'canon/messaging-canon.php',
	'canon/diagnose-canon.php',
	'canon/e3-proof-canon.php',
	'canon/pricing-canon.php',
	'wgos/wgos-cluster-pages.php',
	'header.php',
	'commercial-routing.php',
	'funnel-doors.php',
] as $module ) {
	require_once $theme . $module;
}

/**
 * Render one template part for the active context.
 *
 * @param string $file Theme-relative template path.
 * @return string
 */
function nav_test_render( $file ) {
	ob_start();
	require get_stylesheet_directory() . '/' . $file;
	return (string) ob_get_clean();
}
