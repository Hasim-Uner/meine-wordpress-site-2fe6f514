<?php
/**
 * Print a minimal page with the real header and footer for one context.
 *
 * Usage: php scripts/tests/render-navigation.php <context>
 * Assets are referenced by their live theme paths; navigation.spec.cjs serves
 * them from the working tree.
 */

require __DIR__ . '/navigation-harness.php';

nav_test_use_context( $argv[1] ?? 'imprint' );

$theme = '/wp-content/themes/blocksy-child';
?>
<!doctype html>
<html lang="de">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Navigation fixture</title>
<link rel="stylesheet" href="<?php echo esc_attr( $theme ); ?>/fonts.css">
<link rel="stylesheet" href="<?php echo esc_attr( $theme ); ?>/assets/css/system.css">
<?php if ( is_front_page() ) : ?>
<link rel="stylesheet" href="<?php echo esc_attr( $theme ); ?>/assets/css/startseite-strecke.css">
<?php endif; ?>
<script src="<?php echo esc_attr( $theme ); ?>/assets/js/leiste.js" defer></script>
<style>
/* The Blocksy parent theme sets both on the live site. */
body { margin: 0; }
*, *::before, *::after { box-sizing: border-box; }
</style>
</head>
<body class="nx-custom-header-active">
<?php echo nav_test_render( 'template-parts/site-header.php' ); // phpcs:ignore -- rendered template. ?>
<main id="main" style="min-height:150vh;padding:2rem"><p>Inhalt</p><p id="angebote">Leistungen</p></main>
<?php echo nav_test_render( 'template-parts/site-footer.php' ); // phpcs:ignore -- rendered template. ?>
</body>
</html>
