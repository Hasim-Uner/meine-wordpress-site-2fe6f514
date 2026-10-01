<?php
/**
 * Render one real page template with the real header and footer, for visual
 * and computed-style comparisons without booting WordPress.
 *
 * Usage: php scripts/tests/render-page.php <context> <template-file> <css,css,...>
 *
 * <context>  key from nav_test_contexts() (request path, page slug)
 * <template> file name below blocksy-child/ (page-server-side-tracking-b2b.php)
 * <css>      comma-separated stylesheet paths below blocksy-child/, in load order
 *            (the route's real enqueue order, see inc/enqueue.php)
 *
 * The WordPress boundary is the same set of doubles as the navigation tests
 * (scripts/tests/navigation-harness.php); page lookups return nothing, so
 * every resolver uses its hardcoded default.
 */

require __DIR__ . '/navigation-harness.php';

$context  = $argv[1] ?? 'imprint';
$template = $argv[2] ?? '';
$sheets   = array_filter( explode( ',', $argv[3] ?? '' ) );

nav_test_use_context( $context );

if ( ! function_exists( 'rest_url' ) ) {
	function rest_url( $path ) { return home_url( '/wp-json/' . $path ); }
}

$theme = '/wp-content/themes/blocksy-child';

ob_start();
try {
	require get_stylesheet_directory() . '/' . $template;
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
<title>Page fixture</title>
<link rel="stylesheet" href="<?php echo esc_attr( $theme ); ?>/fonts.css">
<?php foreach ( $sheets as $sheet ) : ?>
<link rel="stylesheet" href="<?php echo esc_attr( $theme . '/' . $sheet ); ?>">
<?php endforeach; ?>
<style>body { margin: 0; } *, *::before, *::after { box-sizing: border-box; }</style>
</head>
<body class="nx-custom-header-active">
<?php echo nav_test_render( 'template-parts/site-header.php' ); // phpcs:ignore -- rendered template. ?>
<main id="main"><?php echo $body; // phpcs:ignore -- rendered template. ?></main>
<?php echo nav_test_render( 'template-parts/site-footer.php' ); // phpcs:ignore -- rendered template. ?>
</body>
</html>
