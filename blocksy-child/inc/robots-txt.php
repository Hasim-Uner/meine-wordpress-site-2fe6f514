<?php
/**
 * Dynamic robots.txt route for search and AI crawlers.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the canonical request path for robots.txt.
 *
 * @return string
 */
function nexus_get_robots_txt_request_path() {
	return trailingslashit( '/robots.txt' );
}

/**
 * Check whether the current request targets robots.txt.
 *
 * @return bool
 */
function nexus_is_robots_txt_request() {
	return nexus_get_current_request_path() === nexus_get_robots_txt_request_path();
}

/**
 * Return the explicit search and AI user agents we want to mention in robots.txt.
 *
 * @return array<int, string>
 */
function nexus_get_robots_txt_user_agents() {
	return [
		'OAI-SearchBot',
		'GPTBot',
		'ChatGPT-User',
		'ClaudeBot',
		'PerplexityBot',
		'*',
	];
}

/**
 * Build the plain-text robots response.
 *
 * @return string
 */
function nexus_get_robots_txt_content() {
	$lines = [
		'# Crawl directives for search engines and AI user agents.',
		'# llms.txt: ' . home_url( '/llms.txt' ),
		'',
	];

	foreach ( nexus_get_robots_txt_user_agents() as $user_agent ) {
		$lines[] = 'User-agent: ' . $user_agent;
		$lines[] = 'Allow: /';
		$lines[] = 'Allow: /llms.txt';
		$lines[] = 'Allow: /wp-sitemap.xml';
		$lines[] = 'Disallow: /wp-admin/';
		$lines[] = 'Allow: /wp-admin/admin-ajax.php';
		$lines[] = '';
	}

	$lines[] = 'Sitemap: ' . home_url( '/wp-sitemap.xml' );

	return implode( "\n", $lines ) . "\n";
}

/**
 * Prevent canonical redirects from interfering with robots.txt.
 *
 * @param string|false $redirect_url Redirect target.
 * @return string|false
 */
function nexus_disable_canonical_redirect_for_robots_txt( $redirect_url ) {
	if ( nexus_is_robots_txt_request() ) {
		return false;
	}

	return $redirect_url;
}
add_filter( 'redirect_canonical', 'nexus_disable_canonical_redirect_for_robots_txt' );

/**
 * Render the robots.txt payload directly from WordPress.
 *
 * @return void
 */
function nexus_render_robots_txt() {
	if ( is_admin() || wp_doing_ajax() || ! nexus_is_robots_txt_request() ) {
		return;
	}

	nocache_headers();
	status_header( 200 );
	header( 'Content-Type: text/plain; charset=' . get_option( 'blog_charset' ) );
	echo nexus_get_robots_txt_content(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	exit;
}
add_action( 'template_redirect', 'nexus_render_robots_txt', 0 );
