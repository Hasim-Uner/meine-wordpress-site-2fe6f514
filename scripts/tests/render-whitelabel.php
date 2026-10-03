<?php
/** Render the actual White-Label template, header and central schema offline. */
require __DIR__ . '/glossary-links-harness.php';
require_once get_stylesheet_directory() . '/inc/canon/reference-canon.php';
require_once get_stylesheet_directory() . '/inc/whitelabel-request.php';
require_once get_stylesheet_directory() . '/inc/seo-meta.php';
require_once get_stylesheet_directory() . '/inc/org-schema.php';
nav_test_use_context( 'whitelabel' );
function rest_url( $path ) { return home_url( '/wp-json/' . $path ); }
function wp_footer() {}
ob_start();
require get_stylesheet_directory() . '/page-whitelabel-retainer.php';
$wl_body = (string) ob_get_clean();
$asset = '/wp-content/themes/blocksy-child/';
?>
<!doctype html>
<html lang="de">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>White-Label WordPress &amp; Tracking für Agenturen · Haşim Üner</title>
<link rel="stylesheet" href="<?php echo esc_attr( $asset ); ?>fonts.css">
<link rel="stylesheet" href="<?php echo esc_attr( $asset ); ?>assets/css/system.css">
<link rel="stylesheet" href="<?php echo esc_attr( $asset ); ?>assets/css/startseite-strecke.css">
<link rel="stylesheet" href="<?php echo esc_attr( $asset ); ?>assets/css/whitelabel.css">
<link rel="stylesheet" href="<?php echo esc_attr( $asset ); ?>assets/css/glossary-links.css">
<style>body{margin:0}*,*::before,*::after{box-sizing:border-box}</style>
<?php foreach ( hu_get_whitelabel_schema_nodes() as $node ) : ?>
<script type="application/ld+json"><?php echo wp_json_encode( $node ); ?></script>
<?php endforeach; ?>
<script defer src="<?php echo esc_attr( $asset ); ?>assets/js/nexus-core.js"></script>
<script defer src="<?php echo esc_attr( $asset ); ?>assets/js/startseite-strecke.js"></script>
<script defer src="<?php echo esc_attr( $asset ); ?>assets/js/whitelabel.js"></script>
<script defer src="<?php echo esc_attr( $asset ); ?>assets/js/glossary-links.js"></script>
</head>
<body class="nx-custom-header-active">
<div id="main-container">
<?php
$args = [ 'home_url' => $wl_home_url, 'brand' => $wl_brand_text, 'home_label' => $wl_home_label, 'form_url' => $wl_form_task_url, 'nav' => $wl_nav ];
require get_stylesheet_directory() . '/template-parts/whitelabel-header.php';
?>
<main id="main"><?php echo $wl_body; // Contains the unchanged template's closing tags. ?>
