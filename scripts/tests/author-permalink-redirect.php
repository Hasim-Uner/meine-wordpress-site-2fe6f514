<?php
/** Prüft die 301 von /beitrag/<autor>/ auf den Beitrag gegen isolierte WordPress-Doubles. */
define( 'ABSPATH', __DIR__ . '/' );
define( 'OBJECT', 'OBJECT' );
class WP_Post { public function __construct( array $data ) { foreach ( $data as $key => $value ) { $this->$key = $value; } } }
$GLOBALS['permalink_test'] = [];
function permalink_reset( $structure = '/%postname%/' ) {
	$GLOBALS['permalink_test'] = [
		'structure' => $structure,
		'users'     => [ 'hasim' => (object) [ 'ID' => 1 ], 'gast' => (object) [ 'ID' => 2 ] ],
		'posts'     => [
			'checkfox-solar-waermepumpe-einordnung' => new WP_Post( [ 'ID' => 15276, 'post_name' => 'checkfox-solar-waermepumpe-einordnung', 'post_status' => 'publish', 'post_author' => 1 ] ),
			'entwurf'                               => new WP_Post( [ 'ID' => 20, 'post_name' => 'entwurf', 'post_status' => 'draft', 'post_author' => 1 ] ),
		],
	];
}
function add_action() {}
function get_option( $name ) { return 'permalink_structure' === $name ? $GLOBALS['permalink_test']['structure'] : false; }
function get_user_by( $field, $value ) { return 'slug' === $field ? ( $GLOBALS['permalink_test']['users'][ $value ] ?? false ) : false; }
function get_page_by_path( $path, $output, $post_type ) { return 'post' === $post_type ? ( $GLOBALS['permalink_test']['posts'][ $path ] ?? null ) : null; }
function get_permalink( $post ) { return str_replace( [ '%postname%', '%author%' ], [ $post->post_name, 'hasim' ], 'https://hasimuener.de' . $GLOBALS['permalink_test']['structure'] ); }
function wp_parse_url( $url, $component = -1 ) { return parse_url( $url, $component ); }
function trailingslashit( $value ) { return rtrim( $value, '/\\' ) . '/'; }
require __DIR__ . '/../../blocksy-child/inc/post-permalink-author-redirect.php';

function check( $condition, $message ) { if ( ! $condition ) throw new RuntimeException( $message ); }
function run_case( $name, $structure, $path, $expected ) {
	permalink_reset( $structure );
	$actual = hu_get_author_suffixed_post_redirect_url( $path );
	check( $actual === $expected, "$name: erwartet '$expected', erhalten '$actual'" );
	echo "PASS $name\n";
}

$post_url = 'https://hasimuener.de/checkfox-solar-waermepumpe-einordnung/';
run_case( 'Autoren-Suffix fuehrt auf den Beitrag', '/%postname%/', '/checkfox-solar-waermepumpe-einordnung/hasim/', $post_url );
run_case( 'Ohne Schlussstrich ebenso', '/%postname%/', '/checkfox-solar-waermepumpe-einordnung/hasim', $post_url );
run_case( 'Struktur mit %author% bleibt unangetastet', '/%postname%/%author%/', '/checkfox-solar-waermepumpe-einordnung/hasim/', '' );
run_case( 'Beitrag selbst leitet nicht weiter', '/%postname%/', '/checkfox-solar-waermepumpe-einordnung/', '' );
run_case( 'Fremder Autor leitet nicht weiter', '/%postname%/', '/checkfox-solar-waermepumpe-einordnung/gast/', '' );
run_case( 'Unbekanntes Segment leitet nicht weiter', '/%postname%/', '/checkfox-solar-waermepumpe-einordnung/2/', '' );
run_case( 'Entwurf leitet nicht weiter', '/%postname%/', '/entwurf/hasim/', '' );
run_case( 'Unbekannter Beitrag leitet nicht weiter', '/%postname%/', '/glossar/hasim/', '' );
run_case( 'Drei Segmente leiten nicht weiter', '/%postname%/', '/blog/checkfox-solar-waermepumpe-einordnung/hasim/', '' );
run_case( 'Blog-Basis im Ziel bleibt erhalten', '/blog/%postname%/', '/checkfox-solar-waermepumpe-einordnung/hasim/', 'https://hasimuener.de/blog/checkfox-solar-waermepumpe-einordnung/' );
echo "OK author-permalink-redirect\n";
