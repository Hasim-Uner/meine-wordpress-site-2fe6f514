<?php
/** Render the actual homepage, canon and global navigation without WordPress. */
if ( 'off' === ( $argv[1] ?? '' ) ) {
	define( 'HU_EXPERIMENT_ERSTEINSCHAETZUNG', false );
}
require __DIR__ . '/navigation-harness.php';
require_once get_stylesheet_directory() . '/inc/canon/reference-canon.php';
require_once get_stylesheet_directory() . '/inc/seo-meta.php';
nav_test_use_context( 'home' );
function hu_enqueue_css( ...$args ) {}
function hu_enqueue_js( ...$args ) {}
$asset = '/wp-content/themes/blocksy-child/';
?>
<!doctype html>
<html lang="de">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo esc_html( hu_get_homepage_title() ); ?></title>
<meta name="description" content="<?php echo esc_attr( hu_get_homepage_description() ); ?>">
<link rel="stylesheet" href="<?php echo esc_attr( $asset ); ?>fonts.css">
<link rel="stylesheet" href="<?php echo esc_attr( $asset ); ?>assets/css/system.css">
<link rel="stylesheet" href="<?php echo esc_attr( $asset ); ?>assets/css/startseite-strecke.css">
<style>body{margin:0}*,*::before,*::after{box-sizing:border-box}</style>
</head>
<body class="nx-custom-header-active">
<?php echo nav_test_render( 'template-parts/site-header.php' ); ?>
<main id="main"><?php echo nav_test_render( 'front-page.php' ); ?></main>
<?php echo nav_test_render( 'template-parts/site-footer.php' ); ?>
<script src="<?php echo esc_attr( $asset ); ?>assets/js/leiste.js"></script>
<script src="<?php echo esc_attr( $asset ); ?>assets/js/startseite-strecke.js"></script>
</body>
</html>
