<?php
/**
 * HTTP compatibility helpers for Search Console write requests.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Determine whether one HTTP request targets Search Console sitemap submit.
 *
 * @param string               $url  Request URL.
 * @param array<string, mixed> $args Request args.
 * @return bool
 */
function nexus_is_seo_cockpit_sitemap_submit_request( $url, $args = [] ) {
	$parts  = wp_parse_url( (string) $url );
	$method = strtoupper( (string) ( $args['method'] ?? '' ) );

	if ( ! is_array( $parts ) || 'PUT' !== $method ) {
		return false;
	}

	$host = strtolower( (string) ( $parts['host'] ?? '' ) );
	$path = (string) ( $parts['path'] ?? '' );

	if ( ! in_array( $host, [ 'www.googleapis.com', 'searchconsole.googleapis.com' ], true ) ) {
		return false;
	}

	return false !== strpos( $path, '/sites/' ) && false !== strpos( $path, '/sitemaps/' );
}

/**
 * Force an explicit zero-length request body for sitemap PUT requests.
 *
 * Google documents sitemap submission as a bodyless PUT. Some HTTP transports
 * omit Content-Length entirely for such requests, which can lead to an empty
 * 4xx response. Sending Content-Length: 0 removes that ambiguity.
 *
 * @param array<string, mixed> $args Request args.
 * @param string               $url  Request URL.
 * @return array<string, mixed>
 */
function nexus_prepare_seo_cockpit_sitemap_put_request( $args, $url ) {
	if ( ! nexus_is_seo_cockpit_sitemap_submit_request( $url, $args ) ) {
		return $args;
	}

	$headers = isset( $args['headers'] ) && is_array( $args['headers'] ) ? $args['headers'] : [];
	$headers['Content-Length'] = '0';

	$args['headers'] = $headers;
	$args['body']    = '';

	return $args;
}
add_filter( 'http_request_args', 'nexus_prepare_seo_cockpit_sitemap_put_request', 20, 2 );

/**
 * Turn opaque non-JSON Search Console errors into actionable messages.
 *
 * @param array<string, mixed>|WP_Error $response HTTP response.
 * @param array<string, mixed>          $args     Request args.
 * @param string                        $url      Request URL.
 * @return array<string, mixed>|WP_Error
 */
function nexus_normalize_seo_cockpit_sitemap_error_response( $response, $args, $url ) {
	if ( is_wp_error( $response ) || ! nexus_is_seo_cockpit_sitemap_submit_request( $url, $args ) ) {
		return $response;
	}

	$status = (int) wp_remote_retrieve_response_code( $response );
	if ( $status >= 200 && $status < 300 ) {
		return $response;
	}

	$body    = (string) wp_remote_retrieve_body( $response );
	$decoded = json_decode( $body, true );

	if ( is_array( $decoded ) && isset( $decoded['error']['message'] ) ) {
		return $response;
	}

	$detail = trim( wp_strip_all_tags( $body ) );
	if ( function_exists( 'mb_substr' ) ) {
		$detail = mb_substr( $detail, 0, 240 );
	} else {
		$detail = substr( $detail, 0, 240 );
	}

	$message = sprintf( 'Search Console API antwortete mit HTTP %d.', $status );
	if ( '' !== $detail ) {
		$message .= ' ' . $detail;
	}

	$response['body'] = wp_json_encode(
		[
			'error' => [
				'code'    => $status,
				'message' => $message,
			],
		]
	);

	return $response;
}
add_filter( 'http_response', 'nexus_normalize_seo_cockpit_sitemap_error_response', 20, 3 );
