<?php
/**
 * Template Name: Energie Fahrplan Demo
 * Description: Eingebettete EnergieFahrplan-Showroom-Demo.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$dist_path     = trailingslashit( get_stylesheet_directory() ) . 'energie-fahrplan/dist/';
$dist_uri      = trailingslashit( get_stylesheet_directory_uri() ) . 'energie-fahrplan/dist/';
$manifest_path = $dist_path . '.vite/manifest.json';
$entry         = null;
$analysis_url  = function_exists( 'hu_get_request_analysis_url' ) ? hu_get_request_analysis_url() : home_url( '/anfrage-system-analyse/' );

if ( file_exists( $manifest_path ) ) {
	$manifest = json_decode( (string) file_get_contents( $manifest_path ), true );
	if ( is_array( $manifest ) && isset( $manifest['src/main.tsx'] ) && is_array( $manifest['src/main.tsx'] ) ) {
		$entry = $manifest['src/main.tsx'];
	}
}

get_header();

if ( ! empty( $entry['css'] ) && is_array( $entry['css'] ) ) {
	foreach ( $entry['css'] as $css_file ) {
		if ( is_string( $css_file ) && '' !== $css_file ) {
			printf( '<link rel="stylesheet" href="%s">' . "\n", esc_url( $dist_uri . ltrim( $css_file, '/' ) ) );
		}
	}
}
?>

<main id="main" class="site-main energie-fahrplan-demo-page" data-track-section="energie_fahrplan_demo">
	<div id="energie-fahrplan-root" data-analysis-url="<?php echo esc_url( $analysis_url ); ?>" data-track-action="demo_view" data-track-category="lead_funnel" data-track-section="energie_fahrplan_demo" data-track-funnel-stage="demo_view">
		<noscript>
			<section style="padding:40px 20px; max-width:720px; margin:0 auto;">
				<h1><?php esc_html_e( 'EnergieFahrplan-Demo', 'blocksy-child' ); ?></h1>
				<p><?php esc_html_e( 'Diese interaktive Demo benötigt JavaScript. Es werden dabei keine Daten an CRM, n8n oder externe Systeme gesendet.', 'blocksy-child' ); ?></p>
			</section>
		</noscript>
	</div>

	<?php if ( empty( $entry['file'] ) ) : ?>
		<section style="padding:40px 20px; max-width:720px; margin:0 auto;">
			<h1><?php esc_html_e( 'EnergieFahrplan-Demo', 'blocksy-child' ); ?></h1>
			<p><?php esc_html_e( 'Der Demo-Build fehlt. Bitte Theme-Distribution neu bauen.', 'blocksy-child' ); ?></p>
		</section>
	<?php endif; ?>
</main>

<?php
if ( ! empty( $entry['file'] ) && is_string( $entry['file'] ) ) {
	printf( '<script type="module" src="%s"></script>' . "\n", esc_url( $dist_uri . ltrim( $entry['file'], '/' ) ) );
}
?>

<?php get_footer(); ?>
