<?php
/**
 * Native WordPress-Sitemap: Legacy-Redirect, noindex-Ausschluss, keine Users-Sitemap.
 *
 * Aus functions.php ausgelagert; functions.php laedt nur Module.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// --- 4b. SITEMAP ---
// Die native WordPress-Sitemap (/wp-sitemap.xml) ist aktiv.
// Legacy-Redirect: /sitemap_index.xml (fruehere Plugin-URL) leitet auf die
// native Sitemap weiter, damit externe Backlinks und GSC-Eintraege erhalten bleiben.
add_action(
	'template_redirect',
	function() {
		if ( is_admin() || wp_doing_ajax() ) {
			return;
		}

		$request_uri  = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '/';
		$request_path = wp_parse_url( $request_uri, PHP_URL_PATH );
		$request_path = '/' . ltrim( (string) $request_path, '/' );

		if ( '/sitemap_index.xml' !== untrailingslashit( $request_path ) ) {
			return;
		}

		nocache_headers();
		wp_safe_redirect( home_url( '/wp-sitemap.xml' ), 301 );
		exit;
	},
	1
);

// --- 4c. SITEMAP: noindex-Seiten ausschließen ---
// Entfernt Posts aus der Sitemap, die per ACF oder Legacy-Meta als noindex markiert sind.
add_filter( 'wp_sitemaps_posts_query_args', function ( $args, $post_type ) {
	$args['meta_query'] = isset( $args['meta_query'] ) ? $args['meta_query'] : [];

	$args['meta_query'][] = [
		'relation' => 'AND',
		[
			'relation' => 'OR',
			[
				'key'     => 'seo_noindex',
				'compare' => 'NOT EXISTS',
			],
			[
				'key'     => 'seo_noindex',
				'value'   => '1',
				'compare' => '!=',
			],
		],
		[
			'relation' => 'OR',
			[
				'key'     => 'rank_math_robots',
				'compare' => 'NOT EXISTS',
			],
			[
				'key'     => 'rank_math_robots',
				'value'   => 'noindex',
				'compare' => 'NOT LIKE',
			],
		],
	];

	return $args;
}, 10, 2 );

// Entferne die native Users-Sitemap (z.B. /wp-sitemap-users-1.xml).
// Gründe: die Seite ist eine persönliche Autoren-/User-Seite ("Über mich")
// und soll nicht als eigenständige Sitemap-Quelle ausgegeben werden.
// Wir versuchen hier robust zwei Ebenen: den Sitemap-Provider entfernen
// und zusätzlich direkte Anfragen an die Users-Sitemap mit 410 beantworten.
add_filter( 'wp_sitemaps_add_provider', function ( $provider, $name ) {
	if ( 'users' === $name ) {
		return false;
	}

	return $provider;
}, 10, 2 );

add_filter( 'wp_sitemaps_register_providers', function ( $providers ) {
	if ( isset( $providers['users'] ) ) {
		unset( $providers['users'] );
	}

	return $providers;
} );

// Fallback: blockiere direkte Aufrufe an die users-Sitemap (sicherheits-/hygienegrund).
add_action( 'template_redirect', function() {
	if ( ! isset( $_SERVER['REQUEST_URI'] ) ) {
		return;
	}

	$request_path = wp_parse_url( wp_unslash( $_SERVER['REQUEST_URI'] ), PHP_URL_PATH );
	$request_path = '/' . ltrim( (string) $request_path, '/' );

	if ( preg_match( '#^/wp-sitemap-users(?:-\d+)?\.xml$#', untrailingslashit( $request_path ) ) ) {
		// Gone — signalisiert Crawlern, dass die Ressource nicht (mehr) existiert.
		status_header( 410 );
		nocache_headers();
		exit;
	}
}, 0 );
