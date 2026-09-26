<?php
/**
 * 301 für Beitragsadressen mit angehängtem Autoren-Slug.
 *
 * Im September 2026 stand die Permalink-Struktur im Admin zeitweise auf einer
 * Variante mit %author%. Beiträge liefen dadurch unter /beitrag/hasim/;
 * Canonical, Sitemap und REST-API folgten, und Google hat diese Adressen
 * aufgenommen. Sobald die Struktur wieder ohne %author% läuft, führt diese
 * Weiterleitung sie auf den Beitrag zurück. Ohne sie würde WordPress /beitrag/hasim/
 * als Anhang lesen und per 404-Rateversuch womöglich auf /hasim-uener/ schicken.
 *
 * Solange %author% noch in der Struktur steht, greift sie nicht: Dann ist
 * /beitrag/hasim/ die echte Adresse, und eine Weiterleitung würde gegen den
 * eigenen Canonical arbeiten. Die Reihenfolge von Deploy und Admin-Umstellung
 * ist deshalb egal.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the post permalink for /post-slug/author-slug/, or an empty string.
 *
 * @param string $request_path Request path with leading and trailing slash.
 * @return string
 */
function hu_get_author_suffixed_post_redirect_url( $request_path ) {
	if ( false !== strpos( (string) get_option( 'permalink_structure' ), '%author%' ) ) {
		return '';
	}

	$segments = array_values(
		array_filter(
			explode( '/', trim( (string) $request_path, '/' ) ),
			static function ( string $segment ): bool {
				return '' !== $segment;
			}
		)
	);
	if ( 2 !== count( $segments ) ) {
		return '';
	}

	// Autor zuerst: Die meisten zweiteiligen Pfade (/glossar/…/, /category/…/)
	// scheitern hier, bevor ein Beitrag gesucht wird.
	$author = get_user_by( 'slug', rawurldecode( $segments[1] ) );
	if ( ! $author ) {
		return '';
	}

	$post = get_page_by_path( $segments[0], 'OBJECT', 'post' );
	if ( ! $post instanceof WP_Post || 'publish' !== $post->post_status || (int) $post->post_author !== (int) $author->ID ) {
		return '';
	}

	$target = (string) get_permalink( $post );
	if ( '' === $target ) {
		return '';
	}

	$target_path  = trailingslashit( '/' . ltrim( (string) wp_parse_url( $target, PHP_URL_PATH ), '/' ) );
	$current_path = trailingslashit( '/' . ltrim( (string) $request_path, '/' ) );

	return $target_path === $current_path ? '' : $target;
}

/**
 * Redirect /post-slug/author-slug/ to the post's current permalink.
 *
 * Prioritaet 1 laeuft vor redirect_canonical und dessen 404-Rateversuch.
 *
 * @return void
 */
function hu_redirect_author_suffixed_post_paths() {
	if ( is_admin() || wp_doing_ajax() || is_feed() ) {
		return;
	}

	$target_url = hu_get_author_suffixed_post_redirect_url( nexus_get_current_request_path() );
	if ( '' === $target_url ) {
		return;
	}

	wp_safe_redirect( nexus_append_current_query_to_redirect_url( $target_url ), 301 );
	exit;
}
add_action( 'template_redirect', 'hu_redirect_author_suffixed_post_paths', 1 );
