<?php
/**
 * Template Name: Anfrage-System-Analyse
 * Description: Staged React shell for the Anfrage-System-Analyse.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'HU_FEATURE_REQUEST_ANALYSIS_ROUTE' ) || ! HU_FEATURE_REQUEST_ANALYSIS_ROUTE ) {
	global $wp_query;

	if ( $wp_query instanceof WP_Query ) {
		$wp_query->set_404();
	}

	status_header( 404 );
	nocache_headers();

	get_header();
	?>
	<main id="main" class="site-main" data-track-section="request_analysis_disabled">
		<section class="wp-section">
			<div class="wp-container">
				<span class="wp-badge"><?php esc_html_e( '404', 'blocksy-child' ); ?></span>
				<h1><?php esc_html_e( 'Diese Seite ist noch nicht verfügbar.', 'blocksy-child' ); ?></h1>
				<p><?php esc_html_e( 'Der neue Diagnose-Einstieg wird vorbereitet.', 'blocksy-child' ); ?></p>
			</div>
		</section>
	</main>
	<?php
	get_footer();
	return;
}

$dist_path     = trailingslashit( get_stylesheet_directory() ) . 'readiness/dist/';
$dist_uri      = trailingslashit( get_stylesheet_directory_uri() ) . 'readiness/dist/';
$manifest_path = $dist_path . '.vite/manifest.json';
$entry         = null;

if ( file_exists( $manifest_path ) ) {
	$manifest = json_decode( (string) file_get_contents( $manifest_path ), true );
	if ( is_array( $manifest ) && isset( $manifest['src/main.tsx'] ) && is_array( $manifest['src/main.tsx'] ) ) {
		$entry = $manifest['src/main.tsx'];
	}
}

$hu_analysis_config = [
	'submitUrl'  => esc_url_raw( rest_url( 'nexus/v1/analysis-submit' ) ),
	'submitEnabled' => defined( 'HU_FEATURE_READINESS_SUBMIT' ) && HU_FEATURE_READINESS_SUBMIT,
	'calcomUrl'  => function_exists( 'hu_get_analysis_calcom_base_url' ) ? hu_get_analysis_calcom_base_url() : 'https://cal.com/hasim-uener/30min?overlayCalendar=true',
	'contactUrl' => function_exists( 'nexus_get_contact_url' ) ? nexus_get_contact_url() : home_url( '/kontakt/' ),
	'e3Stats'    => function_exists( 'hu_e3_canon' ) ? array_map(
		static function ( $metric ) { return is_array( $metric ) ? ( $metric['display'] ?? '' ) : ''; },
		hu_e3_canon()['metrics'] ?? []
	) : [],
];

get_header();
?>
<script>window.HU_ANALYSIS_CONFIG = <?php echo wp_json_encode( $hu_analysis_config ); ?>;</script>

<main id="main" class="site-main readiness-diagnosis-page" data-track-section="request_analysis">
	<div id="readiness-root" data-track-action="request_analysis_view" data-track-category="lead_funnel" data-track-section="request_analysis" data-track-funnel-stage="request_analysis_view">
		<section class="wp-section">
			<div class="wp-container">
				<span class="wp-badge"><?php esc_html_e( 'Anfrage-System-Analyse', 'blocksy-child' ); ?></span>
				<h1><?php esc_html_e( 'Anfrage-System-Analyse wird geladen.', 'blocksy-child' ); ?></h1>
			</div>
		</section>
	</div>

	<?php if ( empty( $entry['file'] ) ) : ?>
		<section class="wp-section" data-track-section="request_analysis_build_missing">
			<div class="wp-container">
				<p><?php esc_html_e( 'Der Analyse-Build fehlt. Bitte Theme-Distribution neu bauen.', 'blocksy-child' ); ?></p>
			</div>
		</section>
	<?php endif; ?>
</main>

<?php
if ( ! empty( $entry['css'] ) && is_array( $entry['css'] ) ) {
	foreach ( $entry['css'] as $css_file ) {
		if ( is_string( $css_file ) && '' !== $css_file ) {
			printf( '<link rel="stylesheet" href="%s">' . "\n", esc_url( $dist_uri . ltrim( $css_file, '/' ) ) );
		}
	}
}

if ( ! empty( $entry['file'] ) && is_string( $entry['file'] ) ) {
	printf( '<script type="module" src="%s"></script>' . "\n", esc_url( $dist_uri . ltrim( $entry['file'], '/' ) ) );
}

get_footer();
