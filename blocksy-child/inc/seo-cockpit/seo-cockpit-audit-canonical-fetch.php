<?php
/**
 * SEO Cockpit audit canonical fetch URLs.
 *
 * The audit graph intentionally normalizes URLs without a trailing slash so
 * `/foo` and `/foo/` compare as the same node. WordPress, however, redirects
 * the slashless request to the canonical permalink before rendering the page.
 * With redirect following disabled this made healthy pages look like 301s and
 * removed them from the link graph.
 *
 * This transport adapter keeps the normalized graph identity but fetches the
 * canonical WordPress permalink. Genuine application redirects still surface
 * because the canonical permalink itself is requested with redirection=0.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the canonical fetch URL for one internal audit URL.
 *
 * @param string $url Requested URL.
 * @return string
 */
function nexus_seo_audit_canonical_fetch_url( $url ) {
	$parts = wp_parse_url( (string) $url );

	if ( ! is_array( $parts ) || empty( $parts['host'] ) ) {
		return (string) $url;
	}

	$home_host = strtolower( (string) wp_parse_url( home_url( '/' ), PHP_URL_HOST ) );
	$url_host  = strtolower( (string) $parts['host'] );

	if ( '' === $home_host || $home_host !== $url_host ) {
		return (string) $url;
	}

	$path = (string) ( $parts['path'] ?? '/' );
	if ( '/' === $path || '' === $path ) {
		return (string) $url;
	}

	$canonical_path = user_trailingslashit( '/' . ltrim( $path, '/' ), 'single' );
	if ( $canonical_path === $path ) {
		return (string) $url;
	}

	$scheme = (string) ( $parts['scheme'] ?? wp_parse_url( home_url( '/' ), PHP_URL_SCHEME ) );
	$port   = isset( $parts['port'] ) ? ':' . absint( $parts['port'] ) : '';
	$query  = isset( $parts['query'] ) && '' !== (string) $parts['query'] ? '?' . (string) $parts['query'] : '';

	return $scheme . '://' . (string) $parts['host'] . $port . $canonical_path . $query;
}

/**
 * Fetch normalized audit nodes through their canonical permalink URL.
 *
 * @param false|array|WP_Error $preempt     Preempted response or false.
 * @param array<string,mixed>  $parsed_args HTTP request arguments.
 * @param string               $url         Requested URL.
 * @return false|array|WP_Error
 */
function nexus_seo_audit_preempt_slash_redirect( $preempt, $parsed_args, $url ) {
	if ( false !== $preempt ) {
		return $preempt;
	}

	$user_agent = isset( $parsed_args['user-agent'] ) ? (string) $parsed_args['user-agent'] : '';
	if ( 0 !== strpos( $user_agent, 'Nexus SEO Cockpit Site Audit/' ) ) {
		return false;
	}

	$canonical_url = nexus_seo_audit_canonical_fetch_url( $url );
	if ( $canonical_url === $url ) {
		return false;
	}

	// Avoid recursion while preserving the audit's timeout, headers and
	// redirection=0 semantics for the canonical request itself.
	remove_filter( 'pre_http_request', 'nexus_seo_audit_preempt_slash_redirect', 10 );
	try {
		return wp_remote_request( $canonical_url, $parsed_args );
	} finally {
		add_filter( 'pre_http_request', 'nexus_seo_audit_preempt_slash_redirect', 10, 3 );
	}
}
add_filter( 'pre_http_request', 'nexus_seo_audit_preempt_slash_redirect', 10, 3 );
