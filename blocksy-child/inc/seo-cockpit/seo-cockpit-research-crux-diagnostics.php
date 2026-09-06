<?php
/**
 * SEO Cockpit CrUX coverage diagnostics.
 *
 * Device-specific CrUX records can be unavailable even when the origin has
 * enough samples across all form factors. This module performs a cached,
 * background-only aggregate-origin check and explains the result in Research.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the option that stores the last aggregate CrUX coverage check.
 *
 * @return string
 */
function nexus_get_seo_cockpit_crux_diagnostic_option_name() {
	return 'nexus_seo_cockpit_crux_coverage_diagnostic';
}

/**
 * Query CrUX for the complete origin without a form-factor filter.
 *
 * @param bool $history Whether to use the History API.
 * @return array<string,mixed>|WP_Error
 */
function nexus_get_seo_cockpit_crux_aggregate_record( $history = false ) {
	$api_key = function_exists( 'nexus_get_seo_cockpit_crux_api_key' ) ? nexus_get_seo_cockpit_crux_api_key() : '';
	$origin  = function_exists( 'nexus_get_seo_cockpit_crux_origin' ) ? nexus_get_seo_cockpit_crux_origin() : '';

	if ( '' === $api_key ) {
		return new WP_Error( 'nexus_crux_missing_key', 'CrUX API-Key fehlt.' );
	}
	if ( '' === $origin ) {
		return new WP_Error( 'nexus_crux_origin', 'Die Website-Origin konnte nicht ermittelt werden.' );
	}

	$cache_key = function_exists( 'nexus_get_seo_cockpit_cache_key' )
		? nexus_get_seo_cockpit_cache_key( 'crux', [ $origin, 'ALL', $history ? 'history' : 'current' ] )
		: 'nexus_crux_' . md5( $origin . '|ALL|' . ( $history ? 'history' : 'current' ) );

	$cached = get_transient( $cache_key );
	if ( is_array( $cached ) ) {
		return $cached;
	}

	$payload = [
		'origin'  => $origin,
		'metrics' => array_keys( nexus_get_seo_cockpit_crux_metric_definitions() ),
	];
	if ( $history ) {
		$payload['collectionPeriodCount'] = 40;
	}

	$endpoint = add_query_arg( 'key', $api_key, nexus_get_seo_cockpit_crux_endpoint( $history ) );
	$response = wp_remote_post(
		$endpoint,
		[
			'timeout' => 15,
			'headers' => [
				'Accept'       => 'application/json',
				'Content-Type' => 'application/json',
			],
			'body' => wp_json_encode( $payload ),
		]
	);

	if ( is_wp_error( $response ) ) {
		return new WP_Error( 'nexus_crux_request', 'CrUX konnte nicht erreicht werden: ' . $response->get_error_message() );
	}

	$status = (int) wp_remote_retrieve_response_code( $response );
	$body   = json_decode( (string) wp_remote_retrieve_body( $response ), true );
	$body   = is_array( $body ) ? $body : [];

	if ( $status < 200 || $status >= 300 ) {
		$message = sanitize_text_field( (string) ( $body['error']['message'] ?? 'Unbekannte API-Antwort.' ) );
		return new WP_Error( 'nexus_crux_http', sprintf( 'CrUX antwortet mit HTTP %1$d: %2$s', $status, $message ) );
	}

	if ( empty( $body['record'] ) || ! is_array( $body['record'] ) ) {
		return new WP_Error( 'nexus_crux_empty', 'Für die gesamte Origin liegen aktuell keine CrUX-Felddaten vor.' );
	}

	set_transient( $cache_key, $body, $history ? 12 * HOUR_IN_SECONDS : 6 * HOUR_IN_SECONDS );
	return $body;
}

/**
 * Store a compact aggregate CrUX diagnosis during the existing background run.
 *
 * @return void
 */
function nexus_refresh_seo_cockpit_crux_coverage_diagnostic() {
	$lock_key = 'nexus_crux_coverage_diagnostic_lock';
	if ( get_transient( $lock_key ) ) {
		return;
	}
	set_transient( $lock_key, '1', 5 * MINUTE_IN_SECONDS );

	try {
		$current = nexus_get_seo_cockpit_crux_aggregate_record( false );
		$history = is_wp_error( $current ) ? $current : nexus_get_seo_cockpit_crux_aggregate_record( true );
		$result  = [
			'checked_at' => time(),
			'has_data'   => ! is_wp_error( $current ),
			'message'    => '',
			'metrics'    => [],
		];

		if ( is_wp_error( $current ) ) {
			$message = (string) $current->get_error_message();
			if ( 'nexus_crux_http' === $current->get_error_code() && false !== stripos( $message, 'HTTP 404' ) ) {
				$result['message'] = 'Die CrUX API ist erreichbar, aber auch für die gesamte Origin reicht die anonymisierte Chrome-Stichprobe aktuell nicht aus.';
			} else {
				$result['message'] = $message;
			}
		} else {
			foreach ( nexus_get_seo_cockpit_crux_metric_definitions() as $metric => $definition ) {
				$value = nexus_get_seo_cockpit_crux_current_p75( $current, $metric );
				$range = nexus_get_seo_cockpit_crux_history_p75_range( $history, $metric );
				$result['metrics'][ $metric ] = [
					'label'  => sanitize_text_field( (string) ( $definition['label'] ?? $metric ) ),
					'value'  => $value,
					'unit'   => sanitize_text_field( (string) ( $definition['unit'] ?? '' ) ),
					'first'  => $range['first'],
					'latest' => $range['latest'],
				];
			}
			$result['message'] = 'CrUX-Gesamtdaten sind vorhanden. Falls Mobil oder Desktop leer bleiben, reicht die Stichprobe nur für die geräteübergreifende Origin-Auswertung.';
		}

		update_option( nexus_get_seo_cockpit_crux_diagnostic_option_name(), $result, false );
	} finally {
		delete_transient( $lock_key );
	}
}
add_action( 'nexus_seo_cockpit_research_background_refresh', 'nexus_refresh_seo_cockpit_crux_coverage_diagnostic', 30 );

/**
 * Show the latest CrUX coverage diagnosis on the Research page.
 *
 * @return void
 */
function nexus_render_seo_cockpit_crux_coverage_notice() {
	$page = isset( $_GET['page'] ) ? sanitize_key( (string) wp_unslash( $_GET['page'] ) ) : '';
	if ( ! function_exists( 'nexus_get_seo_cockpit_research_slug' ) || nexus_get_seo_cockpit_research_slug() !== $page ) {
		return;
	}

	$status = get_option( nexus_get_seo_cockpit_crux_diagnostic_option_name(), [] );
	if ( ! is_array( $status ) || empty( $status['checked_at'] ) ) {
		return;
	}

	$has_data = ! empty( $status['has_data'] );
	$message  = sanitize_text_field( (string) ( $status['message'] ?? '' ) );
	$class    = $has_data ? 'notice notice-success' : 'notice notice-info';

	echo '<div class="' . esc_attr( $class ) . '"><p><strong>CrUX:</strong> ' . esc_html( $message );

	if ( $has_data && ! empty( $status['metrics'] ) && is_array( $status['metrics'] ) ) {
		$parts = [];
		foreach ( $status['metrics'] as $metric ) {
			if ( ! is_array( $metric ) || ! isset( $metric['value'] ) || ! is_numeric( $metric['value'] ) ) {
				continue;
			}
			$value = number_format_i18n( (float) $metric['value'], 'CLS' === (string) ( $metric['label'] ?? '' ) ? 2 : 0 );
			$unit  = trim( (string) ( $metric['unit'] ?? '' ) );
			$parts[] = sanitize_text_field( (string) ( $metric['label'] ?? '' ) ) . ' ' . $value . ( '' !== $unit ? ' ' . $unit : '' );
		}
		if ( ! empty( $parts ) ) {
			echo ' <strong>Gesamt:</strong> ' . esc_html( implode( ' · ', $parts ) );
		}
	}

	echo '</p></div>';
}
add_action( 'admin_notices', 'nexus_render_seo_cockpit_crux_coverage_notice' );
