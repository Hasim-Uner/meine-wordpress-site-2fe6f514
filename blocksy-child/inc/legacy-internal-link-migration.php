<?php
/**
 * One-time cleanup for legacy internal links inside editor-owned blog posts.
 *
 * Redirect and 410 rules stay untouched. This migration only updates known
 * links in known published posts so rendered internal links point directly to
 * the current destination. WordPress remains the live owner of post_content.
 *
 * After production verification this module can be removed; the updated post
 * content remains in WordPress and normal post revisions provide rollback.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the migration version stored after a successful pass.
 *
 * @return string
 */
function hu_legacy_internal_link_migration_version() {
	return '2026-08-17-1';
}

/**
 * Build an absolute internal URL from a canonical path, preserving fragments.
 *
 * @param string $target Canonical site-relative target.
 * @return string
 */
function hu_legacy_internal_link_target_url( $target ) {
	$target   = trim( (string) $target );
	$fragment = '';

	if ( false !== strpos( $target, '#' ) ) {
		list( $target, $fragment ) = array_pad( explode( '#', $target, 2 ), 2, '' );
	}

	$url = home_url( '/' . ltrim( $target, '/' ) );

	return '' !== $fragment ? $url . '#' . rawurlencode( $fragment ) : $url;
}

/**
 * Resolve a link href to a normalized internal path, or empty for external URLs.
 *
 * @param string $href Raw href value.
 * @return string
 */
function hu_legacy_internal_link_path( $href ) {
	$href = html_entity_decode( trim( (string) $href ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );

	if ( '' === $href || 0 === strpos( $href, '#' ) ) {
		return '';
	}

	$parts = wp_parse_url( $href );
	if ( false === $parts ) {
		return '';
	}

	$home_host = strtolower( (string) wp_parse_url( home_url( '/' ), PHP_URL_HOST ) );
	$link_host = isset( $parts['host'] ) ? strtolower( (string) $parts['host'] ) : '';
	$home_host = preg_replace( '/^www\./', '', $home_host );
	$link_host = preg_replace( '/^www\./', '', $link_host );

	if ( '' !== $link_host && $link_host !== $home_host ) {
		return '';
	}

	$path = isset( $parts['path'] ) ? (string) $parts['path'] : '';
	if ( '' === $path ) {
		return '/';
	}

	return trailingslashit( '/' . ltrim( $path, '/' ) );
}

/**
 * Replace href values on anchors without rebuilding the surrounding HTML.
 *
 * Only quoted href attributes are touched, which matches WordPress editor
 * output and avoids DOM serialization changing unrelated editor markup.
 *
 * @param string               $content  Post content.
 * @param array<string,string> $link_map Old normalized path => new target.
 * @return string
 */
function hu_legacy_internal_link_rewrite_hrefs( $content, $link_map ) {
	if ( '' === (string) $content || empty( $link_map ) ) {
		return (string) $content;
	}

	$updated = preg_replace_callback(
		'~(<a\b[^>]*\bhref\s*=\s*)(["\'])([^"\']*)\2([^>]*>)~i',
		static function ( $matches ) use ( $link_map ) {
			$path = hu_legacy_internal_link_path( $matches[3] );
			if ( '' === $path || ! isset( $link_map[ $path ] ) ) {
				return $matches[0];
			}

			$target = hu_legacy_internal_link_target_url( $link_map[ $path ] );

			return $matches[1]
				. $matches[2]
				. esc_url_raw( $target )
				. $matches[2]
				. $matches[4];
		},
		(string) $content
	);

	return is_string( $updated ) ? $updated : (string) $content;
}

/**
 * Remove links to retired tools when no honest equivalent destination exists.
 *
 * The anchor contents stay visible; only the misleading clickable destination
 * is removed.
 *
 * @param string        $content      Post content.
 * @param array<int,string> $unlink_paths Normalized paths to unlink.
 * @return string
 */
function hu_legacy_internal_link_unlink_paths( $content, $unlink_paths ) {
	if ( '' === (string) $content || empty( $unlink_paths ) ) {
		return (string) $content;
	}

	$unlink_paths = array_fill_keys( array_map( 'trailingslashit', $unlink_paths ), true );
	$updated      = preg_replace_callback(
		'~<a\b[^>]*\bhref\s*=\s*(["\'])([^"\']*)\1[^>]*>(.*?)</a>~is',
		static function ( $matches ) use ( $unlink_paths ) {
			$path = hu_legacy_internal_link_path( $matches[2] );

			return ( '' !== $path && isset( $unlink_paths[ $path ] ) ) ? $matches[3] : $matches[0];
		},
		(string) $content
	);

	return is_string( $updated ) ? $updated : (string) $content;
}

/**
 * Return the source-scoped cleanup plan.
 *
 * Redirect targets intentionally mirror current production behavior. The only
 * exception is the retired/protected WGOS route: public links now point to the
 * current WordPress inquiry-system route and the legacy product label is
 * replaced in the same known source posts.
 *
 * 410 routes are mapped only where a current topical owner exists. The retired
 * ROI calculator is unlinked rather than redirected to an unrelated page.
 *
 * @return array<string,array<string,mixed>>
 */
function hu_get_legacy_internal_link_cleanup_plan() {
	$redirect_targets = [
		'/case-studies/'                     => '/ergebnisse/',
		'/customer-journey-audit/'           => '/solar-waermepumpen-leadgenerierung/#marktcheck',
		'/datenhoheit-mit-server-side-gtm/'  => '/server-side-tracking-gtm/',
		'/e3-new-energy/'                    => '/case-study-solar-leadgenerierung/',
		'/growth-audit/'                     => '/solar-waermepumpen-leadgenerierung/#marktcheck',
		'/uber-mich/'                        => '/hasim-uener/',
		'/wordpress-tech-audit/'             => '/solar-waermepumpen-leadgenerierung/#marktcheck',
		'/wordpress-growth-operating-system/' => '/wordpress-agentur-hannover/',
	];
	$topic_targets = [
		'/core-web-vitals/'       => '/core-web-vitals-wachstum-seo-und-roas/',
		'/wordpress-seo-hannover/' => '/wordpress-agentur-hannover/',
	];
	$wgos_text = [
		'WordPress Growth Operating System (WGOS)' => 'WordPress-Anfragesystem',
		'WordPress Growth Operating System'        => 'WordPress-Anfragesystem',
	];

	return [
		'meta-ads-fuer-b2b' => [
			'links' => array_merge(
				$redirect_targets,
				$topic_targets,
				[ '/conversion-rate-optimization/' => '/b2b-landingpage-optimieren/' ]
			),
			'unlink' => [],
			'text'   => array_merge(
				$wgos_text,
				[ 'Growth Architect' => 'Architekt für eigene Anfragesysteme' ]
			),
		],
		'owned-leads-statt-ad-miete' => [
			'links'  => array_merge( $redirect_targets, $topic_targets ),
			'unlink' => [ '/roi-rechner/' ],
			'text'   => [],
		],
		'server-side-tracking-gtm' => [
			'links'  => array_merge( $redirect_targets, $topic_targets ),
			'unlink' => [],
			'text'   => $wgos_text,
		],
		'wordpress-seo-keine-anfragen' => [
			'links'  => array_merge( $redirect_targets, $topic_targets ),
			'unlink' => [],
			'text'   => $wgos_text,
		],
		'b2b-landingpage-optimieren' => [
			'links' => array_merge(
				$redirect_targets,
				$topic_targets,
				[ '/conversion-rate-optimization/' => '/wordpress-agentur-hannover/' ]
			),
			'unlink' => [ '/roi-rechner/' ],
			'text'   => [],
		],
		'design-ist-mehr-als-aesthetik' => [
			'links' => array_merge(
				$redirect_targets,
				$topic_targets,
				[ '/conversion-rate-optimization/' => '/b2b-landingpage-optimieren/' ]
			),
			'unlink' => [],
			'text'   => [],
		],
		'solar-leads-kaufen-lohnt-sich' => [
			'links'  => $redirect_targets,
			'unlink' => [],
			'text'   => [],
		],
		'wordpress-ttfb-google-ads-ladezeit' => [
			'links'  => $redirect_targets,
			'unlink' => [],
			'text'   => [],
		],
	];
}

/**
 * Run the narrowly scoped editor-content migration once.
 *
 * @return void
 */
function hu_maybe_migrate_legacy_internal_links() {
	if ( wp_installing() || wp_doing_ajax() || wp_doing_cron() ) {
		return;
	}

	$version    = hu_legacy_internal_link_migration_version();
	$option_key = 'hu_legacy_internal_link_migration_version';

	if ( $version === (string) get_option( $option_key ) ) {
		return;
	}

	$all_done = true;

	foreach ( hu_get_legacy_internal_link_cleanup_plan() as $slug => $plan ) {
		$post = get_page_by_path( (string) $slug, 'OBJECT', 'post' );

		// Missing or unpublished legacy content needs no retry loop on every hit.
		if ( ! ( $post instanceof WP_Post ) || 'publish' !== (string) $post->post_status ) {
			continue;
		}

		$before = (string) $post->post_content;
		$after  = hu_legacy_internal_link_unlink_paths( $before, (array) ( $plan['unlink'] ?? [] ) );
		$after  = hu_legacy_internal_link_rewrite_hrefs( $after, (array) ( $plan['links'] ?? [] ) );

		foreach ( (array) ( $plan['text'] ?? [] ) as $old_text => $new_text ) {
			$after = str_replace( (string) $old_text, (string) $new_text, $after );
		}

		if ( $after === $before ) {
			update_post_meta( $post->ID, '_hu_legacy_internal_link_migration_version', $version );
			continue;
		}

		$result = wp_update_post(
			wp_slash(
				[
					'ID'           => $post->ID,
					'post_content' => $after,
				]
			),
			true
		);

		if ( is_wp_error( $result ) || ! $result ) {
			$all_done = false;
			continue;
		}

		update_post_meta( $post->ID, '_hu_legacy_internal_link_migration_version', $version );
	}

	if ( $all_done ) {
		update_option( $option_key, $version, false );
	}
}
add_action( 'init', 'hu_maybe_migrate_legacy_internal_links', 36 );
