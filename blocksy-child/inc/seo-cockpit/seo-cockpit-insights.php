<?php
/**
 * SEO Cockpit insight rules, WordPress context and drilldown helpers.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return one Search Console row key by index.
 *
 * @param array<string, mixed> $row   Search Console row.
 * @param int                  $index Key index.
 * @return string
 */
function nexus_get_seo_cockpit_row_key( $row, $index = 0 ) {
	return isset( $row['keys'][ $index ] ) ? (string) $row['keys'][ $index ] : '';
}

/**
 * Return one table row cell value from a Search Console row.
 *
 * @param array<string, mixed> $row Search Console row.
 * @return string
 */
function nexus_get_seo_cockpit_row_label( $row ) {
	return nexus_get_seo_cockpit_row_key( $row, 0 );
}

/**
 * Resolve a cluster slug from one frontend URL if the path matches a cluster route.
 *
 * @param string $url Frontend URL.
 * @return string
 */
function nexus_get_seo_cockpit_cluster_slug_from_url( $url ) {
	$path = wp_parse_url( $url, PHP_URL_PATH );
	$slug = sanitize_title( basename( trim( (string) $path, '/' ) ) );

	if ( '' === $slug || ! function_exists( 'nexus_get_wgos_cluster_page' ) ) {
		return '';
	}

	return nexus_get_wgos_cluster_page( $slug ) ? $slug : '';
}

/**
 * Resolve one known legacy redirect target for a frontend URL.
 *
 * @param string $url Frontend URL.
 * @return string
 */
function nexus_get_seo_cockpit_redirect_target_for_url( $url ) {
	if ( ! function_exists( 'nexus_get_legacy_offer_redirect_map' ) ) {
		return '';
	}

	$path = wp_parse_url( $url, PHP_URL_PATH );
	$path = trailingslashit( '/' . ltrim( (string) $path, '/' ) );
	$map  = nexus_get_legacy_offer_redirect_map();

	if ( empty( $map[ $path ] ) ) {
		return '';
	}

	return (string) $map[ $path ];
}

/**
 * Return a default internal-link payload.
 *
 * @param string $status Status key.
 * @param string $note   Human note.
 * @return array<string, mixed>
 */
function nexus_get_seo_cockpit_default_internal_links( $status = 'pending', $note = '' ) {
	return [
		'status'               => sanitize_key( (string) $status ),
		'incoming_links'       => 0,
		'incoming_documents'   => 0,
		'outgoing_links'       => 0,
		'outgoing_unique_urls' => 0,
		'top_sources'          => [],
		'top_targets'          => [],
		'context'              => [
			'incoming_links'       => 0,
			'incoming_documents'   => 0,
			'outgoing_links'       => 0,
			'outgoing_unique_urls' => 0,
			'top_sources'          => [],
			'top_targets'          => [],
		],
		'sitewide'             => [
			'incoming_links'       => 0,
			'incoming_sources'     => 0,
			'top_sources'          => [],
			'outgoing_links'       => 0,
			'outgoing_unique_urls' => 0,
			'top_targets'          => [],
			'shell'                => '',
			'shell_label'          => '',
			'sources'              => [],
		],
		'totals'               => [
			'incoming_links'       => 0,
			'incoming_sources'     => 0,
			'outgoing_links'       => 0,
			'outgoing_unique_urls' => 0,
		],
		'note'                 => '' !== $note ? $note : 'Interne Link-Zählung ist noch nicht verfügbar.',
	];
}

/**
 * Resolve one frontend URL to a local WordPress object where possible.
 *
 * @param string $url Frontend URL.
 * @return array<string, mixed>
 */
function nexus_get_seo_cockpit_wp_context_for_url( $url ) {
	static $cache = [];

	$url = nexus_normalize_seo_cockpit_url( $url );

	if ( isset( $cache[ $url ] ) ) {
		return $cache[ $url ];
	}

	$front_page_id = absint( get_option( 'page_on_front' ) );
	$posts_page_id = absint( get_option( 'page_for_posts' ) );
	$resolved_id   = url_to_postid( $url );
	$cluster_slug  = nexus_get_seo_cockpit_cluster_slug_from_url( $url );
	$redirect_url  = nexus_get_seo_cockpit_redirect_target_for_url( $url );

	if ( 0 === $resolved_id && home_url( '/' ) === $url ) {
		$resolved_id = $front_page_id;
	}

	if ( 0 === $resolved_id && $posts_page_id && nexus_normalize_seo_cockpit_url( get_permalink( $posts_page_id ) ) === $url ) {
		$resolved_id = $posts_page_id;
	}

	$context = [
		'resolved'               => false,
		'url'                    => $url,
		'post_id'                => 0,
		'post_title'             => '',
		'post_type'              => '',
		'post_status'            => '',
		'page_type'              => '',
		'template'               => '',
		'modified_at'            => 0,
		'seo_title'              => '',
		'seo_description'        => '',
		'seo_title_present'      => false,
		'seo_description_present' => false,
		'title_source'           => '',
		'description_source'     => '',
		'canonical'              => '',
		'canonical_present'      => false,
		'noindex'                => false,
		'in_sitemap'             => false,
		'word_count'             => 0,
		'internal_links'         => nexus_get_seo_cockpit_default_internal_links( 'pending', 'Interne Link-Zählung ist für eine spätere Stufe vorbereitet.' ),
		'edit_link'              => '',
		'frontend_link'          => $url,
		'snippet_issues'         => [],
	];

	if ( '' !== $redirect_url ) {
		$context = [
			'resolved'                => true,
			'url'                     => $url,
			'post_id'                 => 0,
			'post_title'              => 'Legacy Redirect',
			'post_type'               => '',
			'post_status'             => 'redirect',
			'page_type'               => 'legacy_redirect',
			'template'                => '',
			'modified_at'             => 0,
			'seo_title'               => '',
			'seo_description'         => '',
			'seo_title_present'       => false,
			'seo_description_present' => false,
			'title_source'            => '',
			'description_source'      => '',
			'canonical'               => $redirect_url,
			'canonical_present'       => true,
			'noindex'                 => false,
			'in_sitemap'              => false,
			'word_count'              => 0,
			'internal_links'          => nexus_get_seo_cockpit_default_internal_links( 'n/a', 'Diese URL wird serverseitig auf ein kanonisches Ziel weitergeleitet.' ),
			'edit_link'               => '',
			'frontend_link'           => $redirect_url,
			'snippet_issues'          => [],
		];

		$cache[ $url ] = $context;

		return $context;
	}

	if ( $resolved_id ) {
		$post = get_post( $resolved_id );

		if ( $post instanceof WP_Post ) {
			if ( '' !== $cluster_slug && 'publish' !== (string) $post->post_status ) {
				$cluster_defaults = function_exists( 'nexus_get_wgos_cluster_page_seo_defaults' ) ? nexus_get_wgos_cluster_page_seo_defaults( $cluster_slug ) : null;
				$context          = [
					'resolved'                => true,
					'url'                     => $url,
					'post_id'                 => $resolved_id,
					'post_title'              => get_the_title( $resolved_id ),
					'post_type'               => (string) $post->post_type,
					'post_status'             => (string) $post->post_status,
					'page_type'               => 'virtual_cluster',
					'template'                => 'page-wgos-pillar.php',
					'modified_at'             => (int) get_post_modified_time( 'U', true, $resolved_id ),
					'seo_title'               => (string) ( $cluster_defaults['title'] ?? '' ),
					'seo_description'         => (string) ( $cluster_defaults['description'] ?? '' ),
					'seo_title_present'       => '' !== (string) ( $cluster_defaults['title'] ?? '' ),
					'seo_description_present' => '' !== (string) ( $cluster_defaults['description'] ?? '' ),
					'title_source'            => 'virtual_cluster',
					'description_source'      => 'virtual_cluster',
					'canonical'               => home_url( '/' . $cluster_slug . '/' ),
					'canonical_present'       => true,
					'noindex'                 => false,
					'in_sitemap'              => false,
					'word_count'              => nexus_get_seo_cockpit_post_word_count( $resolved_id ),
					'internal_links'          => nexus_get_seo_cockpit_default_internal_links( 'pending', 'Interne Link-Zählung ist für eine spätere Stufe vorbereitet.' ),
					'edit_link'               => (string) get_edit_post_link( $resolved_id, 'raw' ),
					'frontend_link'           => home_url( '/' . $cluster_slug . '/' ),
					'snippet_issues'          => nexus_get_seo_cockpit_snippet_issues(
						[
							'title'       => (string) ( $cluster_defaults['title'] ?? '' ),
							'description' => (string) ( $cluster_defaults['description'] ?? '' ),
						]
					),
				];

				$cache[ $url ] = $context;

				return $context;
			}

			$seo_context = nexus_get_seo_cockpit_post_seo_context( $resolved_id );
			$template    = 'page' === $post->post_type ? ( get_page_template_slug( $resolved_id ) ?: 'default' ) : $post->post_type;
			$page_type   = $post->post_type;

			if ( $front_page_id === $resolved_id ) {
				$page_type = 'front_page';
			} elseif ( $posts_page_id === $resolved_id ) {
				$page_type = 'blog_index';
			}

			$context = [
				'resolved'                => true,
				'url'                     => $url,
				'post_id'                 => $resolved_id,
				'post_title'              => get_the_title( $resolved_id ),
				'post_type'               => (string) $post->post_type,
				'post_status'             => (string) $post->post_status,
				'page_type'               => $page_type,
				'template'                => (string) $template,
				'modified_at'             => (int) get_post_modified_time( 'U', true, $resolved_id ),
				'seo_title'               => (string) ( $seo_context['title'] ?? '' ),
				'seo_description'         => (string) ( $seo_context['description'] ?? '' ),
				'seo_title_present'       => '' !== (string) ( $seo_context['title'] ?? '' ),
				'seo_description_present' => '' !== (string) ( $seo_context['description'] ?? '' ),
				'title_source'            => (string) ( $seo_context['title_source'] ?? '' ),
				'description_source'      => (string) ( $seo_context['description_source'] ?? '' ),
				'canonical'               => (string) ( $seo_context['canonical'] ?? '' ),
				'canonical_present'       => '' !== (string) ( $seo_context['canonical'] ?? '' ),
				'noindex'                 => ! empty( $seo_context['noindex'] ),
				'in_sitemap'              => nexus_is_seo_cockpit_post_in_sitemap( $post, ! empty( $seo_context['noindex'] ) ),
				'word_count'              => nexus_get_seo_cockpit_post_word_count( $resolved_id ),
				'internal_links'          => nexus_get_seo_cockpit_default_internal_links( 'pending', 'Interne Link-Zählung ist für eine spätere Stufe vorbereitet.' ),
				'edit_link'               => (string) get_edit_post_link( $resolved_id, 'raw' ),
				'frontend_link'           => (string) get_permalink( $resolved_id ),
				'snippet_issues'          => nexus_get_seo_cockpit_snippet_issues( $seo_context ),
			];
		}
	}

	if ( 0 === $resolved_id && '' !== $cluster_slug ) {
		$cluster_defaults = function_exists( 'nexus_get_wgos_cluster_page_seo_defaults' ) ? nexus_get_wgos_cluster_page_seo_defaults( $cluster_slug ) : null;
		$context          = [
			'resolved'                => true,
			'url'                     => $url,
			'post_id'                 => 0,
			'post_title'              => function_exists( 'nexus_get_wgos_cluster_page' ) && is_array( nexus_get_wgos_cluster_page( $cluster_slug ) ) ? (string) nexus_get_wgos_cluster_page( $cluster_slug )['title'] : '',
			'post_type'               => '',
			'post_status'             => 'virtual',
			'page_type'               => 'virtual_cluster',
			'template'                => 'page-wgos-pillar.php',
			'modified_at'             => 0,
			'seo_title'               => (string) ( $cluster_defaults['title'] ?? '' ),
			'seo_description'         => (string) ( $cluster_defaults['description'] ?? '' ),
			'seo_title_present'       => '' !== (string) ( $cluster_defaults['title'] ?? '' ),
			'seo_description_present' => '' !== (string) ( $cluster_defaults['description'] ?? '' ),
			'title_source'            => 'virtual_cluster',
			'description_source'      => 'virtual_cluster',
			'canonical'               => home_url( '/' . $cluster_slug . '/' ),
			'canonical_present'       => true,
			'noindex'                 => false,
			'in_sitemap'              => false,
			'word_count'              => 0,
			'internal_links'          => nexus_get_seo_cockpit_default_internal_links( 'pending', 'Interne Link-Zählung ist für eine spätere Stufe vorbereitet.' ),
			'edit_link'               => '',
			'frontend_link'           => home_url( '/' . $cluster_slug . '/' ),
			'snippet_issues'          => nexus_get_seo_cockpit_snippet_issues(
				[
					'title'       => (string) ( $cluster_defaults['title'] ?? '' ),
					'description' => (string) ( $cluster_defaults['description'] ?? '' ),
				]
			),
		];
	}

	if ( function_exists( 'nexus_get_seo_cockpit_internal_link_context' ) ) {
		$context['internal_links'] = nexus_get_seo_cockpit_internal_link_context(
			(string) ( $context['frontend_link'] ?: $url ),
			$context
		);
	}

	$cache[ $url ] = $context;

	return $context;
}

/**
 * Build a WordPress context map for one page report.
 *
 * @param array<int, array<string, mixed>> $rows Page rows.
 * @return array<string, array<string, mixed>>
 */
function nexus_get_seo_cockpit_page_context_map( $rows ) {
	$urls = [];

	foreach ( (array) $rows as $row ) {
		$url = nexus_normalize_seo_cockpit_url( nexus_get_seo_cockpit_row_key( $row, 0 ) );
		if ( '' !== $url ) {
			$urls[ $url ] = $url;
		}
	}

	$contexts = [];
	foreach ( $urls as $url ) {
		$contexts[ $url ] = nexus_get_seo_cockpit_wp_context_for_url( $url );
	}

	return $contexts;
}

/**
 * Return the effective SEO context for one post.
 *
 * @param int $post_id Post ID.
 * @return array<string, mixed>
 */
function nexus_get_seo_cockpit_post_seo_context( $post_id ) {
	$post_id = absint( $post_id );

	if ( function_exists( 'hu_get_singular_post_seo_context' ) ) {
		$context = hu_get_singular_post_seo_context( $post_id );
		if ( is_array( $context ) ) {
			return $context;
		}
	}

	$post = get_post( $post_id );
	if ( ! ( $post instanceof WP_Post ) ) {
		return [];
	}

	$forced           = function_exists( 'hu_get_forced_singular_seo' ) ? hu_get_forced_singular_seo( $post_id ) : [];
	$cluster_defaults = function_exists( 'nexus_get_wgos_cluster_page_seo_defaults' ) ? nexus_get_wgos_cluster_page_seo_defaults( $post ) : null;
	$stored_title     = function_exists( 'hu_get_stored_seo_value' ) ? hu_get_stored_seo_value( $post_id, 'seo_title', 'rank_math_title' ) : '';
	$stored_desc      = function_exists( 'hu_get_stored_seo_value' ) ? hu_get_stored_seo_value( $post_id, 'seo_description', 'rank_math_description' ) : '';
	$title            = $stored_title;
	$description      = $stored_desc;
	$title_source     = '' !== $stored_title ? 'stored' : 'fallback';
	$desc_source      = '' !== $stored_desc ? 'stored' : 'fallback';

	if ( ! empty( $forced['title'] ) ) {
		$title        = (string) $forced['title'];
		$title_source = 'forced';
	} elseif ( '' === $title && 'post' === $post->post_type && function_exists( 'hu_get_post_title_pattern' ) ) {
		$title        = hu_get_post_title_pattern( $post_id );
		$title_source = 'post_pattern';
	} elseif ( '' === $title && is_array( $cluster_defaults ) && ! empty( $cluster_defaults['title'] ) ) {
		$title        = (string) $cluster_defaults['title'];
		$title_source = 'cluster_default';
	} elseif ( '' === $title ) {
		$title = get_the_title( $post_id );
	}

	if ( ! empty( $forced['description'] ) ) {
		$description = (string) $forced['description'];
		$desc_source = 'forced';
	} elseif ( '' === $description && is_array( $cluster_defaults ) && ! empty( $cluster_defaults['description'] ) ) {
		$description = (string) $cluster_defaults['description'];
		$desc_source = 'cluster_default';
	} elseif ( '' === $description ) {
		$excerpt     = get_post_field( 'post_excerpt', $post_id );
		$description = '' !== trim( $excerpt ) ? wp_trim_words( wp_strip_all_tags( $excerpt ), 25, '…' ) : '';
	}

	$acf_noindex          = function_exists( 'get_field' ) ? get_field( 'seo_noindex', $post_id ) : false;
	$legacy_robots_meta   = get_post_meta( $post_id, 'rank_math_robots', true );
	$legacy_noindex       = is_array( $legacy_robots_meta ) ? in_array( 'noindex', $legacy_robots_meta, true ) : 'noindex' === $legacy_robots_meta;
	$noindex              = (bool) ( $acf_noindex || $legacy_noindex );

	return [
		'title'              => trim( wp_strip_all_tags( (string) $title ) ),
		'description'        => trim( wp_strip_all_tags( (string) $description ) ),
		'canonical'          => (string) get_permalink( $post_id ),
		'robots'             => $noindex ? 'noindex, nofollow' : 'index, follow',
		'noindex'            => $noindex,
		'title_source'       => $title_source,
		'description_source' => $desc_source,
	];
}

/**
 * Return estimated word count for one post.
 *
 * @param int $post_id Post ID.
 * @return int
 */
function nexus_get_seo_cockpit_post_word_count( $post_id ) {
	$content = (string) get_post_field( 'post_content', $post_id );
	$text    = wp_strip_all_tags( strip_shortcodes( $content ) );

	if ( '' === $text ) {
		return 0;
	}

	preg_match_all( '/[\p{L}\p{N}\']+/u', $text, $matches );

	return ! empty( $matches[0] ) ? count( $matches[0] ) : 0;
}

/**
 * Determine whether a post should be expected in the native sitemap layer.
 *
 * @param WP_Post $post    Post object.
 * @param bool    $noindex Whether the page is noindex.
 * @return bool
 */
function nexus_is_seo_cockpit_post_in_sitemap( $post, $noindex = false ) {
	if ( ! ( $post instanceof WP_Post ) ) {
		return false;
	}

	$type_object = get_post_type_object( $post->post_type );

	if ( 'publish' !== $post->post_status || ! $type_object || ! $type_object->public || $noindex ) {
		return false;
	}

	if ( function_exists( 'wp_sitemaps_get_server' ) ) {
		return true;
	}

	return false;
}

/**
 * Return snippet weakness flags for one SEO context.
 *
 * @param array<string, mixed> $seo_context SEO context.
 * @return array<int, string>
 */
function nexus_get_seo_cockpit_snippet_issues( $seo_context ) {
	$title       = trim( (string) ( $seo_context['title'] ?? '' ) );
	$description = trim( (string) ( $seo_context['description'] ?? '' ) );
	$issues      = [];

	$title_length = mb_strlen( $title );
	$desc_length  = mb_strlen( $description );

	if ( '' === $title ) {
		$issues[] = 'title_missing';
	} elseif ( $title_length < 35 ) {
		$issues[] = 'title_short';
	} elseif ( $title_length > 60 ) {
		$issues[] = 'title_long';
	}

	if ( '' === $description ) {
		$issues[] = 'description_missing';
	} elseif ( $desc_length < 90 ) {
		$issues[] = 'description_short';
	} elseif ( $desc_length > 160 ) {
		$issues[] = 'description_long';
	}

	return $issues;
}

/**
 * Return one sortable severity score.
 *
 * @param string $severity Severity label.
 * @return int
 */
function nexus_get_seo_cockpit_severity_score( $severity ) {
	$map = [
		'critical' => 400,
		'high'     => 300,
		'medium'   => 200,
		'low'      => 100,
	];

	return $map[ sanitize_key( (string) $severity ) ] ?? 0;
}

/**
 * Return one normalized path for cockpit role mapping.
 *
 * @param string $url Frontend URL.
 * @return string
 */
function nexus_get_seo_cockpit_url_path( $url ) {
	$url = nexus_normalize_seo_cockpit_url( $url );

	if ( '' === $url ) {
		return '/';
	}

	$path = (string) wp_parse_url( $url, PHP_URL_PATH );
	$path = '/' . ltrim( $path, '/' );

	return '/' === $path ? '/' : trailingslashit( $path );
}

/**
 * Return the semantic page role for one cockpit URL context.
 *
 * @param array<string, mixed> $context Optional WordPress context.
 * @param string               $url     Optional frontend URL.
 * @return string
 */
function nexus_get_seo_cockpit_page_role( $context = [], $url = '' ) {
	$context   = is_array( $context ) ? $context : [];
	$url       = '' !== $url ? $url : (string) ( $context['frontend_link'] ?? $context['url'] ?? '' );
	$path      = nexus_get_seo_cockpit_url_path( $url );
	$page_type = (string) ( $context['page_type'] ?? '' );
	$post_type = (string) ( $context['post_type'] ?? '' );
	$paths     = [];

	if ( function_exists( 'nexus_get_primary_public_url_map' ) ) {
		foreach ( (array) nexus_get_primary_public_url_map() as $key => $mapped_url ) {
			$paths[ $key ] = nexus_get_seo_cockpit_url_path( (string) $mapped_url );
		}
	}

	if ( 'legacy_redirect' === $page_type ) {
		return 'legacy';
	}

	if ( 'front_page' === $page_type || '/' === $path ) {
		return 'home';
	}

	if ( ! empty( $paths['audit'] ) && $paths['audit'] === $path ) {
		return 'audit';
	}

	if ( ! empty( $paths['contact'] ) && $paths['contact'] === $path ) {
		return 'contact';
	}

	if ( ! empty( $paths['about'] ) && $paths['about'] === $path ) {
		return 'about';
	}

	if ( in_array( $path, array_filter( [ $paths['results'] ?? '', $paths['e3'] ?? '', $paths['domdar'] ?? '', $paths['whitelabel'] ?? '' ] ), true ) ) {
		return 'results';
	}

	if ( in_array( $path, array_filter( [ $paths['seo'] ?? '', $paths['wartung'] ?? '', $paths['tracking'] ?? '', $paths['cwv'] ?? '', $paths['cro'] ?? '', $paths['performance_marketing'] ?? '', $paths['agentur'] ?? '' ] ), true ) ) {
		return 'service';
	}

	if ( in_array( $path, array_filter( [ $paths['wgos'] ?? '', $paths['glossary'] ?? '', $paths['tools'] ?? '', $paths['performance_analysis'] ?? '' ] ), true ) ) {
		return 'system';
	}

	if ( in_array( $path, array_filter( [ $paths['impressum'] ?? '', $paths['datenschutz'] ?? '' ] ), true ) ) {
		return 'legal';
	}

	if ( 'post' === $post_type ) {
		return 'blog';
	}

	if ( 'blog_index' === $page_type || 0 === strpos( $path, '/category/' ) || 0 === strpos( $path, '/tag/' ) || 0 === strpos( $path, '/author/' ) || '/blog/' === $path ) {
		return 'hub';
	}

	if ( ! empty( $context['noindex'] ) ) {
		return 'utility';
	}

	if ( 'page' === $post_type ) {
		return 'page';
	}

	return 'unknown';
}

/**
 * Return one human label for a cockpit page role.
 *
 * @param string $role Page role.
 * @return string
 */
function nexus_get_seo_cockpit_page_role_label( $role ) {
	$labels = [
		'audit'   => 'Audit',
		'service' => 'Service',
		'results' => 'Proof',
		'system'  => 'System',
		'contact' => 'Kontakt',
		'home'    => 'Startseite',
		'hub'     => 'Hub',
		'blog'    => 'Blog',
		'about'   => 'About',
		'legal'   => 'Rechtlich',
		'utility' => 'Utility',
		'page'    => 'Seite',
		'legacy'  => 'Legacy',
		'unknown' => 'Sonstiges',
	];

	return $labels[ sanitize_key( (string) $role ) ] ?? 'Sonstiges';
}

/**
 * Determine whether one page role is business-critical.
 *
 * @param string $role Page role.
 * @return bool
 */
function nexus_is_seo_cockpit_high_value_role( $role ) {
	return in_array( sanitize_key( (string) $role ), [ 'audit', 'service', 'results', 'system', 'contact', 'home' ], true );
}

/**
 * Return business-value and funnel-proximity scores for a page role.
 *
 * @param string $role Page role.
 * @return array<string, int>
 */
function nexus_get_seo_cockpit_page_role_scores( $role ) {
	$map = [
		'audit'   => [ 'business' => 20, 'funnel' => 15 ],
		'service' => [ 'business' => 19, 'funnel' => 13 ],
		'contact' => [ 'business' => 18, 'funnel' => 15 ],
		'results' => [ 'business' => 16, 'funnel' => 12 ],
		'system'  => [ 'business' => 15, 'funnel' => 11 ],
		'home'    => [ 'business' => 14, 'funnel' => 10 ],
		'hub'     => [ 'business' => 10, 'funnel' => 8 ],
		'blog'    => [ 'business' => 7, 'funnel' => 5 ],
		'about'   => [ 'business' => 4, 'funnel' => 2 ],
		'page'    => [ 'business' => 6, 'funnel' => 4 ],
		'utility' => [ 'business' => 2, 'funnel' => 1 ],
		'legal'   => [ 'business' => 0, 'funnel' => 0 ],
		'legacy'  => [ 'business' => 1, 'funnel' => 0 ],
		'unknown' => [ 'business' => 5, 'funnel' => 3 ],
	];

	return $map[ sanitize_key( (string) $role ) ] ?? $map['unknown'];
}

/**
 * Return one priority label from a priority bucket.
 *
 * @param string $bucket Priority bucket.
 * @return string
 */
function nexus_get_seo_cockpit_priority_label( $bucket ) {
	$labels = [
		'critical' => 'P1',
		'high'     => 'P2',
		'medium'   => 'P3',
		'low'      => 'P4',
	];

	return $labels[ sanitize_key( (string) $bucket ) ] ?? 'P4';
}

/**
 * Return one actionability score for an insight type.
 *
 * @param string $type Insight type.
 * @return int
 */
function nexus_get_seo_cockpit_actionability_score( $type ) {
	$map = [
		'INDEXING_MISMATCH'         => 14,
		'QUICK_WIN'                 => 12,
		'CTR_OPPORTUNITY'           => 11,
		'MONEY_PAGE_UNDERPERFORMING' => 12,
		'ORPHAN_VALUE_PAGE'         => 10,
		'WEAK_FUNNEL_BRIDGE'        => 10,
		'SNIPPET_WEAKNESS'          => 9,
		'DECAY'                     => 8,
		'POSSIBLE_CANNIBALIZATION'  => 8,
		'LOW_SIGNAL'                => 6,
	];

	return $map[ strtoupper( (string) $type ) ] ?? 6;
}

/**
 * Return one demand score from impression volume.
 *
 * @param float $impressions Impression count.
 * @return int
 */
function nexus_get_seo_cockpit_demand_score( $impressions ) {
	$impressions = (float) $impressions;

	if ( $impressions >= 500 ) {
		return 20;
	}

	if ( $impressions >= 250 ) {
		return 17;
	}

	if ( $impressions >= 120 ) {
		return 14;
	}

	if ( $impressions >= 60 ) {
		return 10;
	}

	if ( $impressions >= 20 ) {
		return 6;
	}

	return 2;
}

/**
 * Return one confidence score for an insight.
 *
 * @param float                $impressions Impressions.
 * @param float                $clicks      Clicks.
 * @param array<string, mixed> $context     WordPress context.
 * @param array<string, mixed> $koko_page   Koko page payload.
 * @return int
 */
function nexus_get_seo_cockpit_confidence_score( $impressions, $clicks, $context = [], $koko_page = [] ) {
	$score       = 0;
	$impressions = (float) $impressions;
	$clicks      = (float) $clicks;
	$context     = is_array( $context ) ? $context : [];
	$koko_page   = is_array( $koko_page ) ? $koko_page : [];

	if ( ! empty( $context['resolved'] ) ) {
		$score += 3;
	}

	if ( $impressions >= 80 ) {
		$score += 3;
	} elseif ( $impressions >= 20 ) {
		$score += 1;
	}

	if ( $clicks >= 10 ) {
		$score += 2;
	} elseif ( $clicks > 0 ) {
		$score += 1;
	}

	if ( ! empty( $koko_page ) ) {
		$score += 2;
	}

	return min( 10, $score );
}

/**
 * Return one page-level lead payload from the snapshot.
 *
 * @param array<string, mixed> $snapshot Snapshot payload.
 * @param string               $url      Frontend URL.
 * @return array<string, mixed>
 */
function nexus_get_seo_cockpit_lead_page_for_url( $snapshot, $url ) {
	$url = nexus_get_seo_cockpit_internal_attribution_url( $url );

	if ( '' === $url ) {
		return [];
	}

	return isset( $snapshot['leads']['page_map'][ $url ] ) && is_array( $snapshot['leads']['page_map'][ $url ] )
		? $snapshot['leads']['page_map'][ $url ]
		: [];
}

/**
 * Return one priority bucket from a numeric score.
 *
 * @param int $score Priority score.
 * @return string
 */
function nexus_get_seo_cockpit_priority_bucket( $score ) {
	$score = absint( $score );

	if ( $score >= 75 ) {
		return 'critical';
	}

	if ( $score >= 58 ) {
		return 'high';
	}

	if ( $score >= 40 ) {
		return 'medium';
	}

	return 'low';
}

/**
 * Return one current page row by URL from the snapshot.
 *
 * @param array<string, mixed> $snapshot Snapshot payload.
 * @param string               $url      Frontend URL.
 * @return array<string, mixed>
 */
function nexus_get_seo_cockpit_current_page_row_for_url( $snapshot, $url ) {
	$url = nexus_normalize_seo_cockpit_url( $url );

	foreach ( (array) ( $snapshot['current_page_rows'] ?? [] ) as $row ) {
		if ( $url === nexus_normalize_seo_cockpit_url( nexus_get_seo_cockpit_row_key( $row, 0 ) ) ) {
			return is_array( $row ) ? $row : [];
		}
	}

	return [];
}

/**
 * Enrich one insight with business-aware priority data.
 *
 * @param array<string, mixed> $insight  Insight payload.
 * @param array<string, mixed> $snapshot Snapshot payload.
 * @return array<string, mixed>
 */
function nexus_enrich_seo_cockpit_insight_priority( $insight, $snapshot ) {
	$url          = nexus_normalize_seo_cockpit_url( (string) ( $insight['url'] ?? '' ) );
	$page_context = isset( $snapshot['page_contexts'][ $url ] ) && is_array( $snapshot['page_contexts'][ $url ] ) ? $snapshot['page_contexts'][ $url ] : [];
	$page_row     = nexus_get_seo_cockpit_current_page_row_for_url( $snapshot, $url );
	$koko_page    = isset( $snapshot['koko']['page_map'][ $url ] ) && is_array( $snapshot['koko']['page_map'][ $url ] ) ? $snapshot['koko']['page_map'][ $url ] : [];
	$lead_page    = nexus_get_seo_cockpit_lead_page_for_url( $snapshot, $url );
	$page_role    = nexus_get_seo_cockpit_page_role( $page_context, $url );
	$role_scores  = nexus_get_seo_cockpit_page_role_scores( $page_role );
	$type         = strtoupper( (string) ( $insight['type'] ?? '' ) );
	$severity     = sanitize_key( (string) ( $insight['severity'] ?? 'low' ) );
	$impressions  = (float) ( $insight['metrics']['impressions'] ?? $insight['metrics']['total_impressions'] ?? $page_row['impressions'] ?? 0 );
	$clicks       = (float) ( $insight['metrics']['clicks'] ?? $insight['metrics']['current_clicks'] ?? $page_row['clicks'] ?? 0 );
	$severity_map = [
		'critical' => 24,
		'high'     => 18,
		'medium'   => 12,
		'low'      => 6,
	];
	$components   = [
		'severity'      => $severity_map[ $severity ] ?? 6,
		'demand'        => nexus_get_seo_cockpit_demand_score( $impressions ),
		'business'      => (int) ( $role_scores['business'] ?? 0 ),
		'funnel'        => (int) ( $role_scores['funnel'] ?? 0 ),
		'actionability' => nexus_get_seo_cockpit_actionability_score( $type ),
		'lead_signal'   => function_exists( 'nexus_get_seo_cockpit_lead_signal_score' ) ? nexus_get_seo_cockpit_lead_signal_score( $lead_page ) : 0,
		'confidence'    => nexus_get_seo_cockpit_confidence_score( $impressions, $clicks, $page_context, $koko_page ),
	];
	$score        = min( 100, array_sum( $components ) );
	$bucket       = nexus_get_seo_cockpit_priority_bucket( $score );

	return array_merge(
		$insight,
		[
			'page_role'        => $page_role,
			'page_role_label'  => nexus_get_seo_cockpit_page_role_label( $page_role ),
			'priority_score'   => $score,
			'priority_bucket'  => $bucket,
			'priority_label'   => nexus_get_seo_cockpit_priority_label( $bucket ),
			'priority_parts'   => $components,
			'koko_visitors'    => (float) ( $koko_page['visitors'] ?? 0 ),
			'koko_pageviews'   => (float) ( $koko_page['pageviews'] ?? 0 ),
			'lead_requests_current' => (int) ( $lead_page['current']['requests'] ?? 0 ),
			'lead_requests_lifetime' => (int) ( $lead_page['lifetime']['requests'] ?? 0 ),
			'lead_won_lifetime' => (int) ( $lead_page['lifetime']['won'] ?? 0 ),
		]
	);
}

/**
 * Create a normalized insight payload.
 *
 * @param array<string, mixed> $insight Raw insight data.
 * @return array<string, mixed>
 */
function nexus_build_seo_cockpit_insight( $insight ) {
	return [
		'type'               => strtoupper( str_replace( '-', '_', sanitize_key( (string) ( $insight['type'] ?? 'unknown' ) ) ) ),
		'severity'           => sanitize_key( (string) ( $insight['severity'] ?? 'low' ) ),
		'label'              => trim( wp_strip_all_tags( (string) ( $insight['label'] ?? '' ) ) ),
		'reason'             => trim( wp_strip_all_tags( (string) ( $insight['reason'] ?? '' ) ) ),
		'url'                => nexus_normalize_seo_cockpit_url( (string) ( $insight['url'] ?? '' ) ),
		'query'              => trim( wp_strip_all_tags( (string) ( $insight['query'] ?? '' ) ) ),
		'metrics'            => is_array( $insight['metrics'] ?? null ) ? $insight['metrics'] : [],
		'recommended_action' => trim( wp_strip_all_tags( (string) ( $insight['recommended_action'] ?? '' ) ) ),
	];
}

/**
 * Build the full insight list for one snapshot.
 *
 * @param array<string, mixed> $snapshot Snapshot payload.
 * @return array<int, array<string, mixed>>
 */
function nexus_get_seo_cockpit_insights( $snapshot ) {
	$insights     = [];
	$overall_ctr  = (float) ( $snapshot['overview']['current']['ctr'] ?? 0 );
	$page_context = isset( $snapshot['page_contexts'] ) && is_array( $snapshot['page_contexts'] ) ? $snapshot['page_contexts'] : [];
	$seen         = [];

	foreach ( (array) ( $snapshot['query_page_rows'] ?? [] ) as $row ) {
		$url         = nexus_normalize_seo_cockpit_url( nexus_get_seo_cockpit_row_key( $row, 0 ) );
		$query       = nexus_get_seo_cockpit_row_key( $row, 1 );
		$impressions = (float) ( $row['impressions'] ?? 0 );
		$ctr         = (float) ( $row['ctr'] ?? 0 );
		$position    = (float) ( $row['position'] ?? 0 );

		if ( '' === $url || '' === $query ) {
			continue;
		}

		if ( $position >= 8 && $position <= 20 && $impressions >= 25 ) {
			$insights[] = nexus_build_seo_cockpit_insight(
				[
					'type'               => 'QUICK_WIN',
					'severity'           => $position <= 12 && $impressions >= 80 ? 'high' : 'medium',
					'label'              => sprintf( 'Quick Win für "%s"', $query ),
					'reason'             => sprintf( 'Die URL rankt bereits auf Position %.1f bei %.0f Impressionen.', $position, $impressions ),
					'url'                => $url,
					'query'              => $query,
					'metrics'            => [
						'position'    => $position,
						'impressions' => $impressions,
						'ctr'         => $ctr,
					],
					'recommended_action' => 'Title, Description und interne Links dieser Seite zuerst nachschärfen.',
				]
			);
		}

		if ( $position <= 12 && $impressions >= 120 && $ctr < max( 0.01, $overall_ctr * 0.65 ) ) {
			$insights[] = nexus_build_seo_cockpit_insight(
				[
					'type'               => 'CTR_OPPORTUNITY',
					'severity'           => $impressions >= 300 ? 'high' : 'medium',
					'label'              => sprintf( 'CTR-Chance für "%s"', $query ),
					'reason'             => sprintf( 'Viele Impressionen (%.0f), aber nur %.1f%% CTR bei Position %.1f.', $impressions, $ctr * 100, $position ),
					'url'                => $url,
					'query'              => $query,
					'metrics'            => [
						'position'    => $position,
						'impressions' => $impressions,
						'ctr'         => $ctr,
						'baseline_ctr' => $overall_ctr,
					],
					'recommended_action' => 'Snippet gezielt auf Suchintention und Proof dieser Query ausrichten.',
				]
			);
		}

		if ( $position > 20 && $position <= 40 && $impressions >= 15 ) {
			$insights[] = nexus_build_seo_cockpit_insight(
				[
					'type'               => 'LOW_SIGNAL',
					'severity'           => $impressions >= 60 ? 'medium' : 'low',
					'label'              => sprintf( 'Schwaches Signal für "%s"', $query ),
					'reason'             => sprintf( 'Die URL erscheint bereits, liegt aber mit Position %.1f noch außerhalb der Top 20.', $position ),
					'url'                => $url,
					'query'              => $query,
					'metrics'            => [
						'position'    => $position,
						'impressions' => $impressions,
					],
					'recommended_action' => 'Cluster-Links, Suchintention und Seitentiefe dieser URL schrittweise stärken.',
				]
			);
		}
	}

	$current_pages  = [];
	$previous_pages = [];

	foreach ( (array) ( $snapshot['current_page_rows'] ?? [] ) as $row ) {
		$current_pages[ nexus_normalize_seo_cockpit_url( nexus_get_seo_cockpit_row_key( $row, 0 ) ) ] = $row;
	}

	foreach ( (array) ( $snapshot['previous_page_rows'] ?? [] ) as $row ) {
		$previous_pages[ nexus_normalize_seo_cockpit_url( nexus_get_seo_cockpit_row_key( $row, 0 ) ) ] = $row;
	}

	foreach ( $current_pages as $url => $row ) {
		$current_clicks      = (float) ( $row['clicks'] ?? 0 );
		$current_impressions = (float) ( $row['impressions'] ?? 0 );
		$current_ctr         = (float) ( $row['ctr'] ?? 0 );
		$current_position    = (float) ( $row['position'] ?? 0 );
		$previous_row        = $previous_pages[ $url ] ?? [];
		$previous_clicks     = (float) ( $previous_row['clicks'] ?? 0 );
		$previous_impressions = (float) ( $previous_row['impressions'] ?? 0 );
		$context             = $page_context[ $url ] ?? [];
		$page_role           = nexus_get_seo_cockpit_page_role( $context, $url );
		$role_label          = nexus_get_seo_cockpit_page_role_label( $page_role );
		$is_high_value       = nexus_is_seo_cockpit_high_value_role( $page_role );
		$link_context        = isset( $context['internal_links'] ) && is_array( $context['internal_links'] ) ? $context['internal_links'] : [];
		$context_links       = isset( $link_context['context'] ) && is_array( $link_context['context'] ) ? $link_context['context'] : [];
		$total_links         = isset( $link_context['totals'] ) && is_array( $link_context['totals'] ) ? $link_context['totals'] : [];
		$incoming_documents  = (int) ( $context_links['incoming_documents'] ?? 0 );
		$total_incoming      = (int) ( $total_links['incoming_sources'] ?? 0 );
		$outgoing_unique     = (int) ( $context_links['outgoing_unique_urls'] ?? 0 );
		$indexing_flags      = [];
		$is_virtual_context  = in_array( (string) ( $context['page_type'] ?? '' ), [ 'virtual_cluster', 'legacy_redirect' ], true );

		if ( ( $previous_clicks >= 5 && $current_clicks < ( $previous_clicks * 0.7 ) ) || ( $previous_impressions >= 50 && $current_impressions < ( $previous_impressions * 0.7 ) ) ) {
			$drop = $previous_clicks > 0 ? ( ( $current_clicks - $previous_clicks ) / $previous_clicks ) * 100 : 0;

			$insights[] = nexus_build_seo_cockpit_insight(
				[
					'type'               => 'DECAY',
					'severity'           => $drop <= -50 ? 'high' : 'medium',
					'label'              => 'Traffic-Rückgang auf dieser URL',
					'reason'             => sprintf( 'Klicks oder Impressionen sind gegenüber dem Vergleichsfenster deutlich gefallen (%.1f%%).', $drop ),
					'url'                => $url,
					'query'              => '',
					'metrics'            => [
						'current_clicks'       => $current_clicks,
						'previous_clicks'      => $previous_clicks,
						'current_impressions'  => $current_impressions,
						'previous_impressions' => $previous_impressions,
					],
					'recommended_action' => 'Ändere diese Seite zuerst nicht blind. Prüfe Query-Verschiebungen, Snippet und interne Verlinkung.',
				]
			);
		}

		if ( ! empty( $context['snippet_issues'] ) && $current_impressions >= 30 ) {
			$insights[] = nexus_build_seo_cockpit_insight(
				[
					'type'               => 'SNIPPET_WEAKNESS',
					'severity'           => $current_impressions >= 120 ? 'high' : 'medium',
					'label'              => 'Snippet-Schwäche auf dieser URL',
					'reason'             => sprintf( 'Die Seite sammelt Impressionen, aber Title/Description zeigen Lücken: %s.', implode( ', ', (array) $context['snippet_issues'] ) ),
					'url'                => $url,
					'query'              => '',
					'metrics'            => [
						'impressions'    => $current_impressions,
						'snippet_issues' => $context['snippet_issues'],
					],
					'recommended_action' => 'SEO-Title und Description gegen Suchintention, Klarheit und Länge nachschleifen.',
				]
			);
		}

		if ( $is_high_value && $current_impressions >= 40 && ( $current_position > 12 || ( $current_position <= 12 && $current_ctr < max( 0.01, $overall_ctr * 0.55 ) ) ) ) {
			$insights[] = nexus_build_seo_cockpit_insight(
				[
					'type'               => 'MONEY_PAGE_UNDERPERFORMING',
					'severity'           => $current_impressions >= 120 || 'audit' === $page_role ? 'high' : 'medium',
					'label'              => sprintf( '%s mit Nachfrage, aber unter Zielwert', $role_label ),
					'reason'             => sprintf( 'Die %s-Seite sammelt %.0f Impressionen, liegt aber bei Position %.1f und %.1f%% CTR.', strtolower( $role_label ), $current_impressions, $current_position, $current_ctr * 100 ),
					'url'                => $url,
					'query'              => '',
					'metrics'            => [
						'clicks'      => $current_clicks,
						'impressions' => $current_impressions,
						'ctr'         => $current_ctr,
						'position'    => $current_position,
					],
					'recommended_action' => 'Snippet, Proof-Layer und interne Links dieser kaufnahen Seite zuerst schärfen.',
				]
			);
		}

		if ( $is_high_value && $incoming_documents <= 1 && $total_incoming <= 3 ) {
			$insights[] = nexus_build_seo_cockpit_insight(
				[
					'type'               => 'ORPHAN_VALUE_PAGE',
					'severity'           => ( $current_impressions >= 80 || 'audit' === $page_role ) ? 'high' : 'medium',
					'label'              => sprintf( '%s bekommt zu wenig Kontextlinks', $role_label ),
					'reason'             => sprintf( 'Die Seite ist kaufnah, hat aber nur %d kontextuelle Eingangsdokumente und insgesamt %d verlinkende Quellen.', $incoming_documents, $total_incoming ),
					'url'                => $url,
					'query'              => '',
					'metrics'            => [
						'impressions'        => $current_impressions,
						'context_documents'  => $incoming_documents,
						'total_sources'      => $total_incoming,
					],
					'recommended_action' => 'Aus Hubs, Blog-Bridges und angrenzenden Service-Seiten gezielt Kontextlinks aufbauen.',
				]
			);
		}

		if ( in_array( $page_role, [ 'blog', 'hub' ], true ) && $current_impressions >= 100 && $outgoing_unique <= 1 ) {
			$insights[] = nexus_build_seo_cockpit_insight(
				[
					'type'               => 'WEAK_FUNNEL_BRIDGE',
					'severity'           => $current_impressions >= 250 ? 'high' : 'medium',
					'label'              => sprintf( '%s mit Nachfrage, aber schwacher Funnel-Bridge', $role_label ),
					'reason'             => sprintf( 'Die Seite sammelt %.0f Impressionen, führt im Inhalt aber nur auf %d eindeutige interne Ziele weiter.', $current_impressions, $outgoing_unique ),
					'url'                => $url,
					'query'              => '',
					'metrics'            => [
						'impressions'         => $current_impressions,
						'outgoing_unique_urls' => $outgoing_unique,
					],
					'recommended_action' => 'Im Content 2 bis 3 klare Brücken zu Audit, Service oder Proof ergänzen.',
				]
			);
		}

		if ( ! empty( $context['noindex'] ) ) {
			$indexing_flags[] = 'noindex';
		}

		if ( empty( $context['in_sitemap'] ) && ! $is_virtual_context ) {
			$indexing_flags[] = 'not_in_sitemap';
		}

		if ( empty( $context['canonical_present'] ) ) {
			$indexing_flags[] = 'canonical_missing';
		}

		if ( $is_high_value && ! empty( $indexing_flags ) ) {
			$insights[] = nexus_build_seo_cockpit_insight(
				[
					'type'               => 'INDEXING_MISMATCH',
					'severity'           => ! empty( $context['noindex'] ) ? 'critical' : 'high',
					'label'              => sprintf( '%s mit Indexierungs-Lücke', $role_label ),
					'reason'             => sprintf( 'Die Seite ist kaufnah, hat aber technische SEO-Signale mit Reibung: %s.', implode( ', ', $indexing_flags ) ),
					'url'                => $url,
					'query'              => '',
					'metrics'            => [
						'impressions'    => $current_impressions,
						'indexing_flags' => $indexing_flags,
					],
					'recommended_action' => 'noindex, Canonical und Sitemap-Status für diese Seite zuerst bereinigen.',
				]
			);
		}
	}

	$grouped_queries = [];

	foreach ( (array) ( $snapshot['query_page_rows'] ?? [] ) as $row ) {
		$query       = nexus_get_seo_cockpit_row_key( $row, 1 );
		$normalized  = nexus_normalize_seo_cockpit_query( $query );
		$url         = nexus_normalize_seo_cockpit_url( nexus_get_seo_cockpit_row_key( $row, 0 ) );
		$impressions = (float) ( $row['impressions'] ?? 0 );

		if ( '' === $normalized || '' === $url || $impressions < 10 ) {
			continue;
		}

		if ( ! isset( $grouped_queries[ $normalized ] ) ) {
			$grouped_queries[ $normalized ] = [
				'query'           => $query,
				'total_impressions' => 0.0,
				'urls'            => [],
			];
		}

		$grouped_queries[ $normalized ]['total_impressions'] += $impressions;
		$grouped_queries[ $normalized ]['urls'][ $url ] = [
			'url'         => $url,
			'impressions' => $impressions,
			'position'    => (float) ( $row['position'] ?? 0 ),
		];
	}

	foreach ( $grouped_queries as $group ) {
		if ( count( $group['urls'] ) < 2 || $group['total_impressions'] < 50 ) {
			continue;
		}

		$urls = array_values( $group['urls'] );
		usort(
			$urls,
			static function ( $left, $right ) {
				return ( $right['impressions'] <=> $left['impressions'] );
			}
		);

		$top_urls = array_slice( $urls, 0, 5 );

		$insights[] = nexus_build_seo_cockpit_insight(
			[
				'type'               => 'POSSIBLE_CANNIBALIZATION',
				'severity'           => count( $urls ) >= 3 ? 'high' : 'medium',
				'label'              => sprintf( 'Mögliche Kannibalisierung für "%s"', $group['query'] ),
				'reason'             => sprintf( 'Mehrere URLs sammeln für dieselbe Query Impressionen (gesamt %.0f).', $group['total_impressions'] ),
				'url'                => (string) ( $top_urls[0]['url'] ?? '' ),
				'query'              => (string) $group['query'],
				'metrics'            => [
					'urls'              => $top_urls,
					'url_count'         => count( $urls ),
					'total_impressions' => $group['total_impressions'],
				],
				'recommended_action' => 'Primärseite festlegen und interne Links sowie Snippets auf diese URL konzentrieren.',
			]
		);
	}

	$deduped = [];

	foreach ( $insights as $insight ) {
		$key = implode(
			'|',
			[
				(string) $insight['type'],
				(string) $insight['url'],
				(string) $insight['query'],
			]
		);

		if ( isset( $seen[ $key ] ) ) {
			continue;
		}

		$seen[ $key ] = true;
		$deduped[]    = nexus_enrich_seo_cockpit_insight_priority( $insight, $snapshot );
	}

	usort(
		$deduped,
		static function ( $left, $right ) {
			$priority_diff = (int) ( $right['priority_score'] ?? 0 ) <=> (int) ( $left['priority_score'] ?? 0 );

			if ( 0 !== $priority_diff ) {
				return $priority_diff;
			}

			$severity_diff = nexus_get_seo_cockpit_severity_score( (string) ( $right['severity'] ?? '' ) ) <=> nexus_get_seo_cockpit_severity_score( (string) ( $left['severity'] ?? '' ) );

			if ( 0 !== $severity_diff ) {
				return $severity_diff;
			}

			$left_impressions  = (float) ( $left['metrics']['impressions'] ?? $left['metrics']['total_impressions'] ?? 0 );
			$right_impressions = (float) ( $right['metrics']['impressions'] ?? $right['metrics']['total_impressions'] ?? 0 );

			return $right_impressions <=> $left_impressions;
		}
	);

	return array_slice( $deduped, 0, 20 );
}

/**
 * Build a compact problem-page table from snapshot insights and contexts.
 *
 * @param array<string, mixed> $snapshot Snapshot payload.
 * @return array<int, array<string, mixed>>
 */
function nexus_get_seo_cockpit_problem_pages( $snapshot ) {
	$pages         = [];
	$page_contexts = isset( $snapshot['page_contexts'] ) && is_array( $snapshot['page_contexts'] ) ? $snapshot['page_contexts'] : [];

	foreach ( (array) ( $snapshot['current_page_rows'] ?? [] ) as $row ) {
		$url = nexus_normalize_seo_cockpit_url( nexus_get_seo_cockpit_row_key( $row, 0 ) );
		if ( '' === $url ) {
			continue;
		}

		$pages[ $url ] = [
			'url'           => $url,
			'row'           => $row,
			'context'       => $page_contexts[ $url ] ?? nexus_get_seo_cockpit_wp_context_for_url( $url ),
			'insights'      => [],
			'primary'       => null,
			'detail_url'    => nexus_get_seo_cockpit_detail_url( $url ),
		];
	}

	foreach ( (array) ( $snapshot['insights'] ?? [] ) as $insight ) {
		$url = nexus_normalize_seo_cockpit_url( (string) ( $insight['url'] ?? '' ) );
		if ( '' === $url || ! isset( $pages[ $url ] ) ) {
			continue;
		}

		$pages[ $url ]['insights'][] = $insight;

		if ( null === $pages[ $url ]['primary'] || (int) ( $insight['priority_score'] ?? 0 ) > (int) ( $pages[ $url ]['primary']['priority_score'] ?? 0 ) ) {
			$pages[ $url ]['primary'] = $insight;
		}
	}

	$pages = array_values(
		array_filter(
			$pages,
			static function ( $page ) {
				return ! empty( $page['primary'] );
			}
		)
	);

	usort(
		$pages,
		static function ( $left, $right ) {
			$priority_diff = (int) ( $right['primary']['priority_score'] ?? 0 ) <=> (int) ( $left['primary']['priority_score'] ?? 0 );

			if ( 0 !== $priority_diff ) {
				return $priority_diff;
			}

			return (float) ( $right['row']['impressions'] ?? 0 ) <=> (float) ( $left['row']['impressions'] ?? 0 );
		}
	);

	return array_slice( $pages, 0, 10 );
}

/**
 * Return the detail payload for one URL drilldown.
 *
 * @param string   $url       Frontend URL.
 * @param bool     $force     Force refresh.
 * @param int|null $range_days Optional selected range.
 * @return array<string, mixed>|WP_Error
 */
function nexus_get_seo_cockpit_url_detail( $url, $force = false, $range_days = null ) {
	$property   = nexus_get_seo_cockpit_property();
	$url        = nexus_normalize_seo_cockpit_url( $url );
	$range_days = null === $range_days ? nexus_get_seo_cockpit_requested_range_days() : absint( $range_days );
	$ranges     = nexus_get_seo_cockpit_date_ranges( $range_days );
	$query_cap  = nexus_get_seo_cockpit_row_cap( 'detail_queries' );
	$device_cap = nexus_get_seo_cockpit_row_cap( 'detail_devices' );

	if ( '' === $property || '' === $url ) {
		return new WP_Error( 'nexus_seo_missing_detail_context', 'Für den URL-Drilldown fehlt Property oder URL.' );
	}

	$cache_key = nexus_get_seo_cockpit_cache_key( 'detail', [ $property, $range_days, $url ] );
	$cached    = get_transient( $cache_key );

	if ( ! $force && is_array( $cached ) ) {
		return $cached;
	}

	$filters = [
		[
			'dimension'  => 'page',
			'expression' => $url,
		],
	];

	$current = nexus_get_seo_cockpit_aggregate_metrics( $property, $ranges['current_start'], $ranges['current_end'], $filters );
	if ( is_wp_error( $current ) ) {
		return $current;
	}

	$previous = nexus_get_seo_cockpit_aggregate_metrics( $property, $ranges['previous_start'], $ranges['previous_end'], $filters );
	if ( is_wp_error( $previous ) ) {
		return $previous;
	}

	$trend = nexus_get_seo_cockpit_date_series( $property, $ranges['current_start'], $ranges['current_end'], $filters );
	if ( is_wp_error( $trend ) ) {
		return $trend;
	}

	$queries = nexus_get_seo_cockpit_report_rows(
		$property,
		$ranges['current_start'],
		$ranges['current_end'],
		[ 'query' ],
		$filters,
		(int) $query_cap['limit'],
		$query_cap
	);
	if ( is_wp_error( $queries ) ) {
		return $queries;
	}

	$previous_queries = nexus_get_seo_cockpit_report_rows(
		$property,
		$ranges['previous_start'],
		$ranges['previous_end'],
		[ 'query' ],
		$filters,
		(int) $query_cap['limit'],
		$query_cap
	);
	if ( is_wp_error( $previous_queries ) ) {
		return $previous_queries;
	}

	$devices = nexus_get_seo_cockpit_report_rows(
		$property,
		$ranges['current_start'],
		$ranges['current_end'],
		[ 'device' ],
		$filters,
		(int) $device_cap['limit'],
		$device_cap
	);
	if ( is_wp_error( $devices ) ) {
		return $devices;
	}

	$snapshot   = nexus_get_seo_cockpit_snapshot( false, $range_days );
	$insights   = [];
	$context    = nexus_get_seo_cockpit_wp_context_for_url( $url );
	$inspection = function_exists( 'nexus_get_seo_cockpit_cached_url_inspection' ) ? nexus_get_seo_cockpit_cached_url_inspection( $url ) : null;
	$koko       = function_exists( 'nexus_get_seo_cockpit_koko_detail_data' ) ? nexus_get_seo_cockpit_koko_detail_data( $url, $context, $ranges ) : [];
	$leads      = function_exists( 'nexus_get_seo_cockpit_lead_detail_data' ) ? nexus_get_seo_cockpit_lead_detail_data( $url, $ranges ) : [];
	$diagnostics = function_exists( 'nexus_get_seo_cockpit_diagnostics' ) ? nexus_get_seo_cockpit_diagnostics( $url ) : [];

	if ( ! is_wp_error( $snapshot ) ) {
		foreach ( (array) ( $snapshot['insights'] ?? [] ) as $insight ) {
			if ( $url === nexus_normalize_seo_cockpit_url( (string) ( $insight['url'] ?? '' ) ) ) {
				$insights[] = $insight;
			}
		}
	}

	$detail = [
		'generated_at'     => current_time( 'timestamp' ),
		'property'         => $property,
		'url'              => $url,
		'range_days'       => $range_days,
		'ranges'           => $ranges,
		'overview'         => [
			'current'  => $current,
			'previous' => $previous,
		],
		'trend'            => $trend,
		'top_queries'      => $queries,
		'previous_queries' => $previous_queries,
		'devices'          => $devices,
		'context'          => $context,
		'insights'         => $insights,
		'inspection'       => $inspection,
		'koko'             => $koko,
		'leads'            => $leads,
		'diagnostics'      => $diagnostics,
	];

	set_transient( $cache_key, $detail, nexus_get_seo_cockpit_refresh_interval_seconds() );

	return $detail;
}
