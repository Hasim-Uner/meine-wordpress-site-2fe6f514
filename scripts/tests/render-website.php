<?php
/** Render the real product and shared navigation with WordPress boundaries only. */
require __DIR__ . '/glossary-links-harness.php';
require_once get_stylesheet_directory() . '/inc/canon/reference-canon.php';
require_once get_stylesheet_directory() . '/inc/seo-meta.php';
$GLOBALS['nav_test'] = [ 'path' => '/wordpress-website-erstellen-lassen/', 'front' => false, 'page' => 'wordpress-website-erstellen-lassen', 'template' => 'page-wordpress-website-erstellen-lassen.php' ];
$_SERVER['REQUEST_URI'] = $GLOBALS['nav_test']['path'];
$asset = '/wp-content/themes/blocksy-child/';
?>
<!doctype html><html lang="de"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo esc_html( hu_get_forced_singular_seo_map()['wordpress-website-erstellen-lassen']['title'] ); ?></title>
<meta name="description" content="<?php echo esc_attr( hu_get_forced_singular_seo_map()['wordpress-website-erstellen-lassen']['description'] ); ?>">
<link rel="stylesheet" href="<?php echo esc_attr( $asset ); ?>fonts.css">
<link rel="stylesheet" href="<?php echo esc_attr( $asset ); ?>style.css">
<link rel="stylesheet" href="<?php echo esc_attr( $asset ); ?>assets/css/system.css">
<link rel="stylesheet" href="<?php echo esc_attr( $asset ); ?>assets/css/glossary-links.css">
<link rel="stylesheet" href="<?php echo esc_attr( $asset ); ?>assets/css/anfrage-website.css">
<link rel="stylesheet" href="<?php echo esc_attr( $asset ); ?>assets/css/accessibility-navigation.css">
<style>body{margin:0}*,*::before,*::after{box-sizing:border-box}</style></head><body class="nx-custom-header-active hu-website-offer-page">
<?php echo nav_test_render( 'template-parts/site-header.php' ); ?>
<main id="main"><?php echo nav_test_render( 'page-wordpress-website-erstellen-lassen.php' ); ?></main>
<?php echo nav_test_render( 'template-parts/site-footer.php' ); ?>
<script src="<?php echo esc_attr( $asset ); ?>assets/js/leiste.js"></script>
<script src="<?php echo esc_attr( $asset ); ?>assets/js/anfrage-website.js"></script>
<script src="<?php echo esc_attr( $asset ); ?>assets/js/glossary-links.js"></script>
</body></html>
