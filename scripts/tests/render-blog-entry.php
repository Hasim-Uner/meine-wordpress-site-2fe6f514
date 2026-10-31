<?php
/** Render actual archive templates with a deterministic WordPress loop boundary. */
define( 'ABSPATH', __DIR__ );
$mode = $argv[1] ?? 'home';
$GLOBALS['blog_fixture_mode'] = $mode;
$GLOBALS['blog_fixture_index'] = -1;
class WP_Term {
	public $term_id;
	public $slug;
	public $name;
	public function __construct( $id, $slug, $name ) { $this->term_id = $id; $this->slug = $slug; $this->name = $name; }
}
$GLOBALS['blog_fixture_terms'] = [
	new WP_Term( 1, 'wordpress-performance', 'WordPress & technisches SEO' ),
	new WP_Term( 2, 'tracking', 'Tracking & Attribution' ),
	new WP_Term( 3, 'cro', 'Conversion & Landingpages' ),
	new WP_Term( 4, 'leadgenerierung', 'Leadökonomie & Energie' ),
];
$GLOBALS['blog_fixture_posts'] = 'empty' === $mode ? [] : [
	[ 15831, 'wordpress-wartung-kosten', 'WordPress-Wartung: Was sie kostet und was es kostet, wenn sie fehlt', 'wordpress-wartung-kosten-hero.png', '2026-10-26' ],
	[ 15730, 'agentic-coding', 'Agentic Coding: Was es bedeutet und warum es Softwareentwicklung verändert', 'agentic-coding-hero.png', '2026-10-18' ],
	[ 15367, 'website-relaunch', 'Website-Relaunch: Wann die neue Website wirklich besser ist', 'website-relaunch-hero-v2.png', '2026-09-24' ],
	[ 15226, 'wordpress-ttfb-google-ads-ladezeit', 'WordPress TTFB optimieren: Was Server-Antwortzeit für Google Ads wirklich bedeutet', '', '2026-05-25' ],
];
function esc_html( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES, 'UTF-8' ); }
function esc_attr( $s ) { return esc_html( $s ); }
function esc_url( $s ) { return esc_html( $s ); }
function esc_url_raw( $s ) { return $s; }
function __( $s, $domain = '' ) { return $s; }
function esc_html__( $s, $domain = '' ) { return esc_html( $s ); }
function esc_attr__( $s, $domain = '' ) { return esc_attr( $s ); }
function esc_html_e( $s, $domain = '' ) { echo esc_html( $s ); }
function esc_attr_e( $s, $domain = '' ) { echo esc_attr( $s ); }
function home_url( $s = '/' ) { return 'https://hasimuener.de' . $s; }
function rest_url( $s = '' ) { return home_url( '/wp-json/' . $s ); }
function trailingslashit( $s ) { return rtrim( $s, '/' ) . '/'; }
function add_query_arg( $args, $url ) { return $url . '?' . http_build_query( $args ); }
function get_option( $key ) { return 'page_for_posts' === $key ? 17 : false; }
function get_permalink( $id = null ) { return home_url( 17 === $id ? '/blog/' : '/' . $GLOBALS['blog_fixture_posts'][ $GLOBALS['blog_fixture_index'] ][1] . '/' ); }
function get_categories( $args = [] ) { return $GLOBALS['blog_fixture_terms']; }
function get_category_by_slug( $slug ) { foreach ( get_categories() as $term ) { if ( $term->slug === $slug ) { return $term; } } return false; }
function get_category_link( $id ) { foreach ( get_categories() as $term ) { if ( $term->term_id === $id ) { return home_url( '/category/' . $term->slug . '/' ); } } return ''; }
function get_queried_object() { return get_categories()[0]; }
function get_the_archive_title() { return 'WordPress & technisches SEO'; }
function category_description( $id ) { return 'WordPress-Architektur, technisches SEO, Performance, Core Web Vitals, Relaunch und belastbare Übergabe.'; }
function sanitize_title( $s ) { return strtolower( preg_replace( '/[^a-zA-Z0-9-]+/', '-', $s ) ); }
function is_wp_error( $v ) { return false; }
function is_paged() { return 'paged' === $GLOBALS['blog_fixture_mode']; }
function get_the_ID() { return $GLOBALS['blog_fixture_posts'][ $GLOBALS['blog_fixture_index'] ][0]; }
function get_the_category( $id = null ) { return [ get_categories()[0] ]; }
function get_the_excerpt() { return 'Praxiswissen zu WordPress, Messung und Anfragen. Technische Zusammenhänge, nachvollziehbare Entscheidungen und der nächste sinnvolle Schritt für die eigene Website.'; }
function wp_strip_all_tags( $s ) { return strip_tags( $s ); }
function wp_trim_words( $s, $n, $more = '' ) { $words = preg_split( '/\s+/', $s ); return implode( ' ', array_slice( $words, 0, $n ) ) . ( count( $words ) > $n ? $more : '' ); }
function wp_create_nonce( $s ) { return 'fixture'; }
function wp_localize_script( ...$args ) {}
function get_header() {}
function get_footer() {}
function get_the_title() { return $GLOBALS['blog_fixture_posts'][ $GLOBALS['blog_fixture_index'] ][2]; }
function the_permalink() { echo esc_url( get_permalink() ); }
function get_the_date( $format ) { return ( new DateTimeImmutable( $GLOBALS['blog_fixture_posts'][ $GLOBALS['blog_fixture_index'] ][4] ) )->format( $format ); }
function nexus_get_reading_time( $id = null ) { return 7; }
function have_posts() { return $GLOBALS['blog_fixture_index'] + 1 < count( $GLOBALS['blog_fixture_posts'] ); }
function the_post() { $GLOBALS['blog_fixture_index']++; }
function get_the_post_thumbnail( $id, $size, $attrs ) {
	$image = $GLOBALS['blog_fixture_posts'][ $GLOBALS['blog_fixture_index'] ][3];
	if ( ! $image ) { return ''; }
	$attributes = '';
	foreach ( $attrs as $key => $value ) { $attributes .= ' ' . esc_attr( $key ) . '="' . esc_attr( $value ) . '"'; }
	return '<img width="2400" height="1260" src="/wp-content/themes/blocksy-child/assets/img/blog/' . esc_attr( $image ) . '"' . $attributes . '>';
}
function the_posts_pagination( $args ) { echo '<div class="nav-links"><a class="page-numbers" href="/blog/page/2/">Weiter</a></div>'; }
function get_template_part( $slug, $name = null, $args = [] ) {
	if ( 'template-parts/site-header' === $slug ) { return; }
	require dirname( __DIR__, 2 ) . '/blocksy-child/' . $slug . '.php';
}
$asset = '/wp-content/themes/blocksy-child/';
?>
<!doctype html><html lang="de"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Blog entry fixture</title>
<link rel="stylesheet" href="<?php echo $asset; ?>fonts.css">
<link rel="stylesheet" href="<?php echo $asset; ?>assets/css/system.css">
<link rel="stylesheet" href="<?php echo $asset; ?>assets/css/blog-archive.css">
<link rel="stylesheet" href="<?php echo $asset; ?>assets/css/category-hub.css">
<style>body{margin:0}*,*::before,*::after{box-sizing:border-box}h1,h2,h3,p{margin:0}.fixture-header{height:52px;border-bottom:1px solid #dedad3;padding:16px 20px;font:14px sans-serif}</style>
</head><body>
<header class="fixture-header">HAŞIM ÜNER. / WISSEN</header>
<main id="main"><?php require dirname( __DIR__, 2 ) . '/blocksy-child/' . ( 'category' === $mode ? 'category.php' : 'home.php' ); ?></main>
<script src="<?php echo $asset; ?>assets/js/blog-archive.js"></script>
</body></html>
