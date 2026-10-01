<?php
/**
 * Render one real single post (template-parts/single-reader.php) with the real
 * header and footer, for visual comparisons without booting WordPress.
 *
 * Usage: php scripts/tests/render-article.php <slug> <css,css,...>
 *
 * <slug>  a post slug known to the navigation harness (portal contexts below)
 * <css>   comma-separated stylesheet paths below blocksy-child/, in load order
 *         (the route's real enqueue order, see inc/enqueue.php and
 *         inc/article-reader-toc.php)
 *
 * The four Portal-Einordnungen come from inc/blog-provider-posts.php (seed
 * markdown) or the slug-specific decision cockpits; the post body of a seeded
 * post is the real converted markdown. Everything else is a test double.
 */

require __DIR__ . '/navigation-harness.php';

$slug   = $argv[1] ?? 'checkfox-solar-waermepumpe-einordnung';
$sheets = array_filter( explode( ',', $argv[2] ?? '' ) );

$GLOBALS['nav_test'] = [
	'path'     => '/blog/' . $slug . '/',
	'front'    => false,
	'page'     => '',
	'template' => '',
	'post'     => [ 'slug' => $slug, 'categories' => [ 'leadgenerierung' ] ],
];
$_SERVER['REQUEST_URI'] = $GLOBALS['nav_test']['path'];

require_once get_stylesheet_directory() . '/inc/blog-provider-posts.php';

$article = null;
foreach ( hu_get_lead_provider_posts_seed_data() as $seed ) {
	if ( $slug === $seed['slug'] ) {
		$article = $seed;
	}
}
$GLOBALS['article_test'] = [
	'title'   => $article['title'] ?? 'Einordnung',
	'excerpt' => $article['excerpt'] ?? '',
	'content' => $article ? hu_lead_provider_markdown_to_html( $article['markdown_content'] ) : '',
	'served'  => false,
];

foreach ( [ 'wp_enqueue_style', 'wp_enqueue_script', 'get_header', 'get_footer', 'the_post_thumbnail', 'comments_template' ] as $noop ) {
	if ( ! function_exists( $noop ) ) {
		eval( "function {$noop}() {}" ); // phpcs:ignore Squiz.PHP.Eval.Discouraged -- test double.
	}
}
$doubles = [
	'get_post' => static function ( ...$a ) { return null; },
	'wp_parse_args' => static function ( $args, $defaults = [] ) { return array_merge( (array) $defaults, (array) $args ); },
	'wp_create_nonce' => static function ( ...$a ) { return ''; },
	'set_query_var' => static function ( ...$a ) { return ''; },
	'get_query_var' => static function ( ...$a ) { return ''; },
	'wp_reset_postdata' => static function ( ...$a ) { return ''; },
	'is_single'             => static function ( $slug = '' ) { return '' === $slug || $slug === $GLOBALS['nav_test']['post']['slug']; },
	'have_posts'            => static function () { return ! $GLOBALS['article_test']['served']; },
	'the_post'              => static function () { $GLOBALS['article_test']['served'] = true; },
	'get_the_ID'            => static function () { return 1; },
	'has_post_thumbnail'    => static function () { return false; },
	'get_the_title'         => static function () { return $GLOBALS['article_test']['title']; },
	'get_the_excerpt'       => static function () { return $GLOBALS['article_test']['excerpt']; },
	'get_the_content'       => static function () { return $GLOBALS['article_test']['content']; },
	'the_content'           => static function () { echo $GLOBALS['article_test']['content']; }, // phpcs:ignore
	'get_the_date'          => static function () { return '1. September 2026'; },
	'get_the_modified_date' => static function () { return '1. September 2026'; },
	'get_the_author'        => static function () { return 'Haşim Üner'; },
	'get_the_author_meta'   => static function () { return ''; },
	'get_avatar'            => static function () { return ''; },
	'get_category_link'     => static function () { return home_url( '/blog/' ); },
];
foreach ( $doubles as $name => $impl ) {
	if ( ! function_exists( $name ) ) {
		$GLOBALS['article_doubles'][ $name ] = $impl;
		eval( "function {$name}( ...\$a ) { return (\$GLOBALS['article_doubles']['{$name}'])( ...\$a ); }" ); // phpcs:ignore Squiz.PHP.Eval.Discouraged -- test double.
	}
}

// related-content.php queries siblings; the fixture has none.
if ( ! class_exists( 'WP_Query' ) ) {
	class WP_Query {
		public $posts = [];
		public $found_posts = 0;
		public function __construct( $args = [] ) {}
		public function have_posts() { return false; }
		public function the_post() {}
	}
}

$theme = '/wp-content/themes/blocksy-child';

ob_start();
try {
	require get_stylesheet_directory() . '/template-parts/single-reader.php';
} catch ( Throwable $error ) {
	fwrite( STDERR, 'Template failed: ' . $error->getMessage() . ' at ' . $error->getFile() . ':' . $error->getLine() . "\n" );
	exit( 1 );
}
$body = (string) ob_get_clean();
?>
<!doctype html>
<html lang="de">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Article fixture</title>
<link rel="stylesheet" href="<?php echo esc_attr( $theme ); ?>/fonts.css">
<?php foreach ( $sheets as $sheet ) : ?>
<link rel="stylesheet" href="<?php echo esc_attr( $theme . '/' . $sheet ); ?>">
<?php endforeach; ?>
<style>body { margin: 0; } *, *::before, *::after { box-sizing: border-box; }</style>
</head>
<body class="single single-post nx-custom-header-active">
<?php echo $body; // phpcs:ignore -- rendered template. ?>
<?php echo nav_test_render( 'template-parts/site-footer.php' ); // phpcs:ignore -- rendered template. ?>
</body>
</html>
