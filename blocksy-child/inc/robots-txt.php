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
 * Crawler groups addressed explicitly in robots.txt, split by what the operator
 * actually does with the content.
 *
 * The vendors document three distinct purposes and ship a separate user agent
 * for each. Naming them individually is what makes the policy per-purpose
 * decidable later: today all three are allowed, but blocking training without
 * losing search visibility is only possible if the tokens are already separated.
 *
 * Two rules govern edits here:
 *
 * 1. A named group makes that agent ignore the `*` group entirely, so every
 *    group below must repeat the full rule set. Do not add a directive to `*`
 *    only and expect the named agents to honour it.
 * 2. Only tokens from current vendor documentation belong here. Google and Bing
 *    serve their AI surfaces (AI Overviews, AI Mode, Copilot) from the ordinary
 *    Googlebot/Bingbot crawl, so they stay in the `*` group. There is no
 *    separate Google or Bing "AI crawler" to address.
 *
 * @return array<int, array<string, mixed>>
 */
function nexus_get_robots_txt_agent_groups() {
	return [
		[
			'comment' => 'AI search and retrieval — indexes pages so assistants can surface and cite them.',
			'agents'  => [ 'OAI-SearchBot', 'Claude-SearchBot', 'PerplexityBot' ],
		],
		[
			'comment' => 'User-initiated fetching — a person asked an assistant about a specific page.',
			'agents'  => [ 'ChatGPT-User', 'Claude-User', 'Perplexity-User' ],
		],
		[
			'comment' => 'Model training corpora.',
			'agents'  => [ 'GPTBot', 'ClaudeBot' ],
		],
		[
			'comment' => 'Everything else, including Googlebot and Bingbot.',
			'agents'  => [ '*' ],
		],
	];
}

/**
 * Return the flat list of user agents mentioned in robots.txt.
 *
 * @return array<int, string>
 */
function nexus_get_robots_txt_user_agents() {
	$agents = [];

	foreach ( nexus_get_robots_txt_agent_groups() as $group ) {
		foreach ( (array) ( $group['agents'] ?? [] ) as $agent ) {
			$agents[] = (string) $agent;
		}
	}

	return $agents;
}

/**
 * Rule set repeated for every group.
 *
 * `Allow: /` already covers /llms.txt and /wp-sitemap.xml; listing them again
 * added six identical redundant lines without changing what any crawler may
 * fetch.
 *
 * @return array<int, string>
 */
function nexus_get_robots_txt_rules() {
	return [
		'Allow: /',
		'Disallow: /wp-admin/',
		'Allow: /wp-admin/admin-ajax.php',
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
		'# Full route index for AI agents: ' . home_url( '/llms.txt' ),
		'',
	];

	$rules = nexus_get_robots_txt_rules();

	foreach ( nexus_get_robots_txt_agent_groups() as $group ) {
		$comment = isset( $group['comment'] ) ? (string) $group['comment'] : '';

		if ( '' !== $comment ) {
			$lines[] = '# ' . $comment;
		}

		foreach ( (array) ( $group['agents'] ?? [] ) as $user_agent ) {
			$lines[] = 'User-agent: ' . (string) $user_agent;
		}

		foreach ( $rules as $rule ) {
			$lines[] = $rule;
		}

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
