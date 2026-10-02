<?php
/**
 * Print a minimal page with the real header and footer for one context.
 *
 * Usage: php scripts/tests/render-navigation.php <context>
 * Assets are referenced by their live theme paths; navigation.spec.cjs serves
 * them from the working tree.
 */

require __DIR__ . '/navigation-harness.php';

// "<kontext>:own" rendert statt des globalen Kopfs den eigenen Kopf der White-Label-Seite.
$request_parts = explode( ':', $argv[1] ?? 'imprint' );
nav_test_use_context( $request_parts[0] );
$own_header = 'own' === ( $request_parts[1] ?? '' );

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
<?php if ( is_front_page() || $own_header ) : ?>
<link rel="stylesheet" href="<?php echo esc_attr( $theme ); ?>/assets/css/startseite-strecke.css">
<?php endif; ?>
<?php if ( $own_header ) : ?>
<link rel="stylesheet" href="<?php echo esc_attr( $theme ); ?>/assets/css/whitelabel.css">
<?php endif; ?>
<script src="<?php echo esc_attr( $theme ); ?>/assets/js/leiste.js" defer></script>
<style>
/* The Blocksy parent theme sets both on the live site. */
body { margin: 0; }
*, *::before, *::after { box-sizing: border-box; }
</style>
</head>
<body class="nx-custom-header-active">
<?php
if ( $own_header ) {
	ob_start();
	get_template_part(
		'template-parts/whitelabel-header',
		null,
		[
			'home_url'   => home_url( '/' ),
			'brand'      => hu_get_site_wordmark_text(),
			'home_label' => 'Startseite - ' . hu_get_site_wordmark_text(),
			'form_url'   => home_url( '/whitelabel-retainer/?case=aufgabe#aufgabe' ),
			'nav'        => [
				[ '#lieferfelder', 'Leistungen', 'nav_whitelabel_services' ],
				[ '#proof', 'Belege', 'nav_whitelabel_proof' ],
				[ '#einstieg', 'Preise', 'nav_whitelabel_pricing' ],
				[ '#zusammenarbeit', 'Ablauf', 'nav_whitelabel_process' ],
				[ '#faq', 'Fragen', 'nav_whitelabel_faq' ],
			],
		]
	);
	echo ob_get_clean(); // phpcs:ignore -- rendered template.
} else {
	echo nav_test_render( 'template-parts/site-header.php' ); // phpcs:ignore -- rendered template.
}
?>
<main id="main" style="min-height:150vh;padding:2rem"><p>Inhalt</p><p id="angebote">Leistungen</p></main>
<?php echo nav_test_render( 'template-parts/site-footer.php' ); // phpcs:ignore -- rendered template. ?>
</body>
</html>
