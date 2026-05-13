<?php
/**
 * NEXUS Helper Functions
 *
 * Wiederverwendbare Utility-Funktionen für das gesamte Theme.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Calculate estimated reading time for a post.
 *
 * @param  int|null $post_id  Post ID (defaults to current post).
 * @param  int      $wpm      Words per minute.
 * @return int      Reading time in minutes (minimum 1).
 */
function nexus_get_reading_time( $post_id = null, $wpm = 200 ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	$content    = get_post_field( 'post_content', $post_id );
	$word_count = str_word_count( wp_strip_all_tags( $content ) );
	$minutes    = max( 1, (int) ceil( $word_count / $wpm ) );

	return $minutes;
}

/**
 * Get an ACF field with a safe fallback.
 *
 * Wraps get_field() so templates never break if ACF is inactive.
 *
 * @param  string     $field_name  ACF field name.
 * @param  mixed      $default     Default value if field is empty.
 * @param  int|false  $post_id     Post ID or false for current post.
 * @return mixed
 */
function nexus_get_field( $field_name, $default = '', $post_id = false ) {
	if ( ! function_exists( 'get_field' ) ) {
		return $default;
	}

	$value = get_field( $field_name, $post_id );
	return ( $value !== null && $value !== '' && $value !== false ) ? $value : $default;
}

/**
 * Return the default profile image URL (Profilbild).
 *
 * @return string Absolute URL.
 */
function hu_get_profile_image_url() {
	return home_url( '/wp-content/uploads/2024/10/Profilbild_Hasim-Uener.webp' );
}

/**
 * Return the portrait image URL (Startseite/Whitelabel variant).
 *
 * @return string Absolute URL.
 */
function hu_get_portrait_image_url() {
	return home_url( '/wp-content/uploads/2026/01/Hasim-Uener-Prtraeit_Startseite.webp' );
}

/**
 * Return the canonical public proof and vocabulary set for public pages.
 *
 * @return array<string, mixed>
 */
function nexus_get_public_proof_data() {
	static $proof = null;

	if ( null !== $proof ) {
		return $proof;
	}

	$e3_metrics = function_exists( 'hu_e3_canon' ) ? hu_e3_canon()['metrics'] : [];

	$proof = [
		'metrics'             => [
			'lead_count'       => [
				'value'   => $e3_metrics['lead_count']['display'] ?? '1.750+',
				'label'   => 'qualifizierte Anfragen',
				'context' => 'im aufgebauten System',
			],
			'sales_conversion' => [
				'value'   => $e3_metrics['sales_conversion']['display'] ?? '12 %',
				'label'   => 'Abschlussquote',
				'context' => 'vom Lead bis zum Abschluss',
			],
			'cpl_reduction'    => [
				'value'   => $e3_metrics['cpl_reduction']['display'] ?? 'über 85 %',
				'label'   => 'Kosten pro Anfrage',
				'context' => 'gegenüber Lead-Einkauf',
			],
		],
		'ownership_sentence'  => 'Code, Inhalte, Zugänge und Setups bleiben bei Ihnen. Laufende Zusammenarbeit bedeutet Weiterentwicklung, nicht Abhängigkeit.',
		'primary_term'        => 'Eigenes Anfrage-System für Solar- und Wärmepumpen-Anbieter',
		'framework_label'     => 'WGOS = WordPress Growth Operating System',
		'framework_long'      => 'WordPress Growth Operating System',
		'allowed_subterms'    => [
			'System-Diagnose',
			'priorisierte Folgeanalyse',
			'kontrollierte Weiterentwicklung',
			'planbare Growth-Kapazität',
		],
	];

	return $proof;
}

/**
 * Return selected canonical public proof metrics in display order.
 *
 * @param array<int, string> $keys Metric keys.
 * @return array<int, array<string, string>>
 */
function nexus_get_public_proof_metric_list( $keys ) {
	$proof   = nexus_get_public_proof_data();
	$metrics = isset( $proof['metrics'] ) && is_array( $proof['metrics'] ) ? $proof['metrics'] : [];
	$list    = [];

	foreach ( (array) $keys as $key ) {
		if ( isset( $metrics[ $key ] ) && is_array( $metrics[ $key ] ) ) {
			$list[] = $metrics[ $key ];
		}
	}

	return $list;
}

/**
 * Return the public ownership sentence used on kaufnahen Seiten.
 *
 * @return string
 */
function nexus_get_public_ownership_sentence() {
	$proof = nexus_get_public_proof_data();

	return isset( $proof['ownership_sentence'] ) ? (string) $proof['ownership_sentence'] : '';
}

/**
 * Return the primary public market category.
 *
 * @return string
 */
function nexus_get_public_primary_term() {
	$proof = nexus_get_public_proof_data();

	return isset( $proof['primary_term'] ) ? (string) $proof['primary_term'] : '';
}

/**
 * Return the expanded public framework label.
 *
 * @return string
 */
function nexus_get_public_framework_label() {
	$proof = nexus_get_public_proof_data();

	return isset( $proof['framework_label'] ) ? (string) $proof['framework_label'] : '';
}

/**
 * Return the canonical entry CTA copy for the System-Diagnose.
 *
 * Keep purchase-intent copy in one place so the primary CTA label and its
 * supporting promise stay aligned across key surfaces.
 *
 * @return array<string, mixed>
 */
function nexus_get_audit_cta_copy() {
	static $copy = null;

	if ( null !== $copy ) {
		return $copy;
	}

	$copy = [
		'label'             => 'System-Diagnose starten',
		'compact_microcopy' => '60 Sek. · priorisierte Hebel · keine E-Mail',
		'header_meta_items' => [
			'60-Sekunden-Diagnose',
			'Fokus: Solar, Wärmepumpe, Speicher',
		],
		'footer_note'       => 'System-Diagnose: In ca. 60 Sekunden sehen, wo Performance, Tracking, SEO und Content Anfragen verlieren — mit priorisierten Hebeln statt generischem Score.',
	];

	return $copy;
}

/**
 * Return the canonical primary CTA label for the System-Diagnose.
 *
 * @return string
 */
function nexus_get_audit_cta_label() {
	$copy = nexus_get_audit_cta_copy();

	return isset( $copy['label'] ) ? (string) $copy['label'] : 'System-Diagnose starten';
}

/**
 * Return the compact System-Diagnose CTA microcopy.
 *
 * @return string
 */
function nexus_get_audit_compact_microcopy() {
	$copy = nexus_get_audit_cta_copy();

	return isset( $copy['compact_microcopy'] ) ? (string) $copy['compact_microcopy'] : '';
}

/**
 * Return the compact metadata items used in the System-Diagnose header.
 *
 * @return array<int, string>
 */
function nexus_get_audit_header_meta_items() {
	$copy = nexus_get_audit_cta_copy();
	$items = isset( $copy['header_meta_items'] ) && is_array( $copy['header_meta_items'] ) ? $copy['header_meta_items'] : [];

	return array_values(
		array_filter(
			array_map(
				static function ( $item ) {
					return trim( (string) $item );
				},
				$items
			)
		)
	);
}

/**
 * Return the full System-Diagnose footer note.
 *
 * @return string
 */
function nexus_get_audit_footer_note() {
	$copy = nexus_get_audit_cta_copy();

	return isset( $copy['footer_note'] ) ? (string) $copy['footer_note'] : '';
}

/**
 * Render a tracking-ready CTA button.
 *
 * Outputs an <a> element with proper data-track-* attributes
 * for GTM Server-Side event capture.
 *
 * @param array $args {
 *     @type string $url       Link URL.
 *     @type string $text      Button text.
 *     @type string $action    Tracking action name (e.g. 'cta_audit_hero').
 *     @type string $category  Tracking category (default: 'lead_gen').
 *     @type string $class     CSS classes (default: 'btn btn-primary').
 *     @type bool   $new_tab   Open in new tab (default: false).
 * }
 * @return string HTML.
 */
function nexus_cta_button( $args = [] ) {
	$defaults = [
		'url'      => '#',
		'text'     => __( 'Jetzt starten', 'blocksy-child' ),
		'action'   => 'cta_generic',
		'category' => 'lead_gen',
		'class'    => 'btn btn-primary',
		'new_tab'  => false,
	];

	$args   = wp_parse_args( $args, $defaults );
	$target = $args['new_tab'] ? ' target="_blank" rel="noopener noreferrer"' : '';

	return sprintf(
		'<a href="%s" class="%s" data-track-action="%s" data-track-category="%s"%s>%s</a>',
		esc_url( $args['url'] ),
		esc_attr( $args['class'] ),
		esc_attr( $args['action'] ),
		esc_attr( $args['category'] ),
		$target,
		esc_html( $args['text'] )
	);
}

/**
 * Truncate a string to a maximum length at word boundaries.
 *
 * @param  string $text    Input text.
 * @param  int    $length  Maximum character length.
 * @param  string $suffix  Suffix to append when truncated.
 * @return string
 */
function nexus_truncate( $text, $length = 155, $suffix = '…' ) {
	$text = wp_strip_all_tags( $text );

	if ( mb_strlen( $text ) <= $length ) {
		return $text;
	}

	$truncated = mb_substr( $text, 0, $length );
	$last_space = mb_strrpos( $truncated, ' ' );

	if ( $last_space !== false ) {
		$truncated = mb_substr( $truncated, 0, $last_space );
	}

	return $truncated . $suffix;
}

/**
 * Get theme asset URL (shortcut for frequent use).
 *
 * @param  string $path Relative path within /assets/.
 * @return string Full URL.
 */
function nexus_asset_url( $path ) {
	return get_stylesheet_directory_uri() . '/assets/' . ltrim( $path, '/' );
}

/**
 * Get theme asset absolute path.
 *
 * @param  string $path Relative path within /assets/.
 * @return string Absolute file path.
 */
function nexus_asset_path( $path ) {
	return get_stylesheet_directory() . '/assets/' . ltrim( $path, '/' );
}

/**
 * Resolve a page ID from one or more possible slugs.
 *
 * @param string|array $paths Candidate page paths, ordered by preference.
 * @return int
 */
function nexus_get_page_id( $paths ) {
	$paths = (array) $paths;

	foreach ( $paths as $path ) {
		$page = get_page_by_path( trim( (string) $path, '/' ) );
		if ( $page instanceof WP_Post ) {
			return (int) $page->ID;
		}
	}

	return 0;
}

/**
 * Resolve a page ID by assigned page template.
 *
 * Cached per request so repeated CTA helpers stay cheap.
 *
 * @param string $template Page template filename.
 * @return int
 */
function nexus_get_page_id_by_template( $template ) {
	static $cache = [];

	$template = (string) $template;

	if ( isset( $cache[ $template ] ) ) {
		return $cache[ $template ];
	}

	$page_ids = get_posts(
		[
			'post_type'              => 'page',
			'post_status'            => 'publish',
			'posts_per_page'         => 1,
			'orderby'                => 'menu_order title',
			'order'                  => 'ASC',
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'meta_key'               => '_wp_page_template',
			'meta_value'             => $template,
		]
	);

	$cache[ $template ] = ! empty( $page_ids ) ? (int) $page_ids[0] : 0;

	return $cache[ $template ];
}

/**
 * Resolve a permalink from one or more possible slugs with a sane fallback.
 *
 * @param string|array $paths    Candidate page paths, ordered by preference.
 * @param string       $fallback Optional fallback URL.
 * @return string
 */
function nexus_get_page_url( $paths, $fallback = '' ) {
	$paths   = (array) $paths;
	$page_id = nexus_get_page_id( $paths );

	if ( $page_id ) {
		return get_permalink( $page_id );
	}

	if ( $fallback ) {
		return $fallback;
	}

	if ( ! empty( $paths ) ) {
		return home_url( '/' . trim( (string) $paths[0], '/' ) . '/' );
	}

	return home_url( '/' );
}

/**
 * Resolve a category archive URL by slug with a deterministic fallback.
 *
 * @param string $slug     Category slug.
 * @param string $fallback Optional fallback URL.
 * @return string
 */
function nexus_get_category_url( $slug, $fallback = '' ) {
	$slug = sanitize_title( (string) $slug );

	if ( '' === $slug ) {
		return $fallback ? $fallback : home_url( '/category/' );
	}

	$term = get_term_by( 'slug', $slug, 'category' );

	if ( $term instanceof WP_Term ) {
		$url = get_term_link( $term );

		if ( ! is_wp_error( $url ) ) {
			return $url;
		}
	}

	if ( $fallback ) {
		return $fallback;
	}

	return home_url( '/category/' . $slug . '/' );
}

/**
 * Return the canonical public URL for one versioned WGOS cluster route.
 *
 * Cluster routes stay on stable public slugs even if legacy editor pages with
 * shorter aliases still exist in the database.
 *
 * @param string $slug     Canonical cluster slug.
 * @param string $fallback Optional fallback URL.
 * @return string
 */
function nexus_get_wgos_cluster_route_url( $slug, $fallback = '' ) {
	$slug = sanitize_title( (string) $slug );

	if ( '' === $slug ) {
		return $fallback ? $fallback : home_url( '/' );
	}

	return home_url( '/' . $slug . '/' );
}

/**
 * Resolve the versioned set of primary public URLs used for SEO and internal linking.
 *
 * Keep all canonical internal targets in one place so template, footer, cockpit and
 * editorial bridges point to the same primary URLs.
 *
 * @return array<string, string>
 */
function nexus_get_primary_public_url_map() {
	static $urls = null;

	if ( is_array( $urls ) ) {
		return $urls;
	}

	$request_url = function_exists( 'hu_get_request_analysis_url' ) ? hu_get_request_analysis_url() : home_url( '/system-diagnose/' );

	$urls = [
		'home'                 => home_url( '/' ),
		'blog'                 => function_exists( 'nexus_get_blog_posts_url' ) ? nexus_get_blog_posts_url() : home_url( '/blog/' ),
		'audit'                => $request_url,
		'audit_linkedin'       => function_exists( 'nexus_get_audit_linkedin_url' ) ? nexus_get_audit_linkedin_url() : home_url( '/audit-linkedin/' ),
		'results'              => function_exists( 'nexus_get_results_url' ) ? nexus_get_results_url() : home_url( '/ergebnisse/' ),
		'wgos'                 => nexus_get_page_url(
			[ 'wordpress-growth-operating-system', 'wgos' ],
			home_url( '/wordpress-growth-operating-system/' )
		),
		'glossary'             => nexus_get_page_url(
			[ 'glossar' ],
			home_url( '/glossar/' )
		),
		'agentur'              => nexus_get_page_url(
			[ 'wordpress-agentur-hannover', 'wordpress-agentur' ],
			home_url( '/wordpress-agentur-hannover/' )
		),
		'seo'                  => nexus_get_wgos_cluster_route_url(
			'wordpress-seo-hannover',
			home_url( '/wordpress-seo-hannover/' )
		),
		'wartung'              => nexus_get_wgos_cluster_route_url(
			'wordpress-wartung-hannover',
			home_url( '/wordpress-wartung-hannover/' )
		),
		'tracking'             => nexus_get_wgos_cluster_route_url(
			'ga4-tracking-setup',
			home_url( '/ga4-tracking-setup/' )
		),
		'cwv'                  => nexus_get_wgos_cluster_route_url(
			'core-web-vitals',
			home_url( '/core-web-vitals/' )
		),
		'cro'                  => nexus_get_wgos_cluster_route_url(
			'conversion-rate-optimization',
			home_url( '/conversion-rate-optimization/' )
		),
		'performance_marketing'=> nexus_get_wgos_cluster_route_url(
			'performance-marketing',
			home_url( '/performance-marketing/' )
		),
		'tools'                => $request_url,
		'performance_analysis' => $request_url,
		'about'                => nexus_get_page_url(
			[ 'uber-mich' ],
			home_url( '/uber-mich/' )
		),
		'contact'              => function_exists( 'nexus_get_contact_url' ) ? nexus_get_contact_url() : home_url( '/kontakt/' ),
		'impressum'            => nexus_get_page_url(
			[ 'impressum' ],
			home_url( '/impressum/' )
		),
		'datenschutz'          => nexus_get_page_url(
			[ 'datenschutz' ],
			home_url( '/datenschutz/' )
		),
		'e3'                   => nexus_get_page_url(
			[ 'e3-new-energy', 'case-studies/e3-new-energy', 'case-e3' ],
			home_url( '/e3-new-energy/' )
		),
		'energy'               => function_exists( 'nexus_get_energy_systems_url' ) ? nexus_get_energy_systems_url() : home_url( '/solar-waermepumpen-leadgenerierung/' ),
		'request'              => $request_url,
		'domdar'               => nexus_get_page_url(
			[ 'case-study-domdar', 'domdar' ],
			home_url( '/case-study-domdar/' )
		),
		'whitelabel'           => function_exists( 'nexus_get_whitelabel_page_url' ) ? nexus_get_whitelabel_page_url() : home_url( '/whitelabel-retainer/' ),
		'seo_category'         => nexus_get_category_url( 'seo', home_url( '/category/seo/' ) ),
		'tracking_category'    => nexus_get_category_url( 'tracking', home_url( '/category/tracking/' ) ),
		'cro_category'         => nexus_get_category_url( 'cro', home_url( '/category/cro/' ) ),
		'performance_category' => nexus_get_category_url( 'wordpress-performance', home_url( '/category/wordpress-performance/' ) ),
	];

	return $urls;
}

/**
 * Resolve a single primary public URL by semantic key.
 *
 * @param string $key      URL key from nexus_get_primary_public_url_map().
 * @param string $fallback Optional fallback URL.
 * @return string
 */
function nexus_get_primary_public_url( $key, $fallback = '' ) {
	$key = (string) $key;
	$map = nexus_get_primary_public_url_map();

	if ( isset( $map[ $key ] ) && '' !== (string) $map[ $key ] ) {
		return (string) $map[ $key ];
	}

	return $fallback ? $fallback : home_url( '/' );
}

/**
 * Resolve the primary audit page ID while supporting legacy slugs.
 *
 * @return int
 */
function nexus_get_audit_page_id() {
	$template_page_id = nexus_get_page_id_by_template( 'page-audit.php' );

	if ( $template_page_id ) {
		return $template_page_id;
	}

	return nexus_get_page_id( [ 'growth-audit', 'audit', 'customer-journey-audit', '360-audit' ] );
}

/**
 * Resolve the former audit page URL.
 *
 * The standalone Growth Audit is retired; keep this helper as a compatibility
 * alias so older CTA surfaces route into the System-Diagnose.
 *
 * @return string
 */
function nexus_get_audit_url() {
	return function_exists( 'hu_get_request_analysis_url' ) ? hu_get_request_analysis_url() : home_url( '/system-diagnose/' );
}

/**
 * Determine whether the current request is the audit landing page.
 *
 * @return bool
 */
function nexus_is_audit_page() {
	$audit_page_id = nexus_get_audit_page_id();

	if ( $audit_page_id && is_page( $audit_page_id ) ) {
		return true;
	}

	return is_page_template( 'page-audit.php' )
		|| is_page( 'growth-audit' )
		|| is_page( 'audit' )
		|| is_page( 'customer-journey-audit' );
}

/**
 * Resolve the canonical energy systems landing page URL.
 *
 * @return string
 */
function nexus_get_energy_systems_url() {
	$page_id = nexus_get_page_id( [ 'solar-waermepumpen-leadgenerierung' ] );

	if ( $page_id ) {
		return get_permalink( $page_id );
	}

	$template_page_id = nexus_get_page_id_by_template( 'page-solar-waermepumpen-leadgenerierung.php' );

	if ( $template_page_id ) {
		$permalink = get_permalink( $template_page_id );
		$path      = trailingslashit( '/' . ltrim( (string) wp_parse_url( $permalink, PHP_URL_PATH ), '/' ) );

		if ( '/website-fuer-solar-und-waermepumpen-anbieter/' !== $path ) {
			return $permalink;
		}
	}

	return home_url( '/solar-waermepumpen-leadgenerierung/' );
}

/**
 * Resolve the canonical URL for the retired dedicated request page.
 *
 * The public /anfrage/ intake was retired in favor of the evidence-based
 * System-Diagnose. Keep this helper as a compatibility alias so old
 * templates and editor links cannot route users back into the old form.
 *
 * @return string
 */
function nexus_get_anfrage_url() {
	return function_exists( 'hu_get_request_analysis_url' ) ? hu_get_request_analysis_url() : home_url( '/system-diagnose/' );
}

/**
 * Resolve the canonical hard-CTA URL for qualified request analysis.
 *
 * @return string
 */
function nexus_get_primary_request_url() {
	return function_exists( 'hu_get_request_analysis_url' ) ? hu_get_request_analysis_url() : home_url( '/system-diagnose/' );
}

/**
 * Return the canonical hard-CTA label for the solar inquiry flow.
 *
 * @return string
 */
function nexus_get_primary_request_cta_label() {
	return 'System-Diagnose starten';
}

/**
 * Resolve the primary tools hub page ID while supporting legacy slugs.
 *
 * @return int
 */
function nexus_get_tools_page_id() {
	$template_page_id = nexus_get_page_id_by_template( 'page-tools.php' );

	if ( $template_page_id ) {
		return $template_page_id;
	}

	return nexus_get_page_id( [ 'kostenlose-tools', 'tools' ] );
}

/**
 * Determine whether the current request is the tools hub page.
 *
 * @return bool
 */
function nexus_is_tools_page() {
	$tools_page_id = nexus_get_tools_page_id();

	if ( $tools_page_id && is_page( $tools_page_id ) ) {
		return true;
	}

	return is_page_template( 'page-tools.php' )
		|| is_page( 'kostenlose-tools' )
		|| is_page( 'tools' );
}

/**
 * Resolve the primary results hub page ID.
 *
 * @return int
 */
function nexus_get_results_page_id() {
	$canonical_page_id = nexus_get_page_id( [ 'ergebnisse' ] );

	if ( $canonical_page_id ) {
		return $canonical_page_id;
	}

	$template_page_id = nexus_get_page_id_by_template( 'page-case-studies-e-commerce.php' );

	if ( $template_page_id ) {
		return $template_page_id;
	}

	return nexus_get_page_id( [ 'case-studies-e-commerce', 'case-studies' ] );
}

/**
 * Resolve the primary results hub URL.
 *
 * @return string
 */
function nexus_get_results_url() {
	$page_id = nexus_get_results_page_id();

	if ( $page_id ) {
		return get_permalink( $page_id );
	}

	return home_url( '/ergebnisse/' );
}

/**
 * Resolve the whitelabel proof page ID.
 *
 * @return int
 */
function nexus_get_whitelabel_page_id() {
	$template_page_id = nexus_get_page_id_by_template( 'page-whitelabel-retainer.php' );

	if ( $template_page_id ) {
		return $template_page_id;
	}

	return nexus_get_page_id( [ 'whitelabel-retainer', 'whitelabel-retainer-proof', 'whitelabel' ] );
}

/**
 * Resolve the whitelabel proof page URL.
 *
 * @return string
 */
function nexus_get_whitelabel_page_url() {
	$page_id = nexus_get_whitelabel_page_id();

	if ( $page_id ) {
		return get_permalink( $page_id );
	}

	return home_url( '/whitelabel-retainer/' );
}

/**
 * Determine whether the current request is inside the public results area.
 *
 * @return bool
 */
function nexus_is_results_context() {
	$results_page_id = nexus_get_results_page_id();

	if ( $results_page_id && is_page( $results_page_id ) ) {
		return true;
	}

	return is_page( 'case-studies-e-commerce' )
		|| is_page( 'case-studies' )
		|| is_page( 'ergebnisse' )
		|| is_page( 'e3-new-energy' )
		|| is_page( 'case-study-domdar' )
		|| is_page( 'domdar' )
		|| is_page( 'whitelabel-retainer' )
		|| is_page( 'whitelabel-retainer-proof' )
		|| is_page( 'whitelabel' )
		|| is_page_template( 'page-case-e3.php' )
		|| is_page_template( 'page-case-study-domdar.php' )
		|| is_page_template( 'page-case-studies-e-commerce.php' )
		|| is_page_template( 'page-whitelabel-retainer.php' );
}

/**
 * Return the current request path with leading and trailing slash.
 *
 * @return string
 */
function nexus_get_current_request_path() {
	$request_uri  = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '/';
	$request_path = wp_parse_url( $request_uri, PHP_URL_PATH );

	return trailingslashit( '/' . ltrim( (string) $request_path, '/' ) );
}

/**
 * Ensure the energy systems landing page exists on the canonical slug.
 *
 * @return void
 */
function nexus_maybe_ensure_energy_systems_page() {
	if ( wp_installing() || wp_doing_ajax() || wp_doing_cron() ) {
		return;
	}

	$page_id = nexus_get_page_id( [ 'solar-waermepumpen-leadgenerierung' ] );

	if ( ! $page_id ) {
		$legacy_page = get_page_by_path( 'website-fuer-solar-und-waermepumpen-anbieter' );

		if ( $legacy_page instanceof WP_Post ) {
			$page_id = wp_update_post(
				wp_slash(
					[
						'ID'        => (int) $legacy_page->ID,
						'post_name' => 'solar-waermepumpen-leadgenerierung',
					]
				),
				true
			);

			if ( is_wp_error( $page_id ) ) {
				return;
			}
		} else {
			$page_id = wp_insert_post(
				wp_slash(
					[
						'post_type'    => 'page',
						'post_status'  => 'publish',
						'post_title'   => 'Leadgenerierung für Solar- und Wärmepumpen-Anbieter',
						'post_name'    => 'solar-waermepumpen-leadgenerierung',
						'post_content' => '',
						'post_excerpt' => 'B2B-Landingpage für Solar-, Wärmepumpen- und Speicher-Anbieter mit Website-, Tracking- und Conversion-Fokus.',
					]
				),
				true
			);

			if ( is_wp_error( $page_id ) ) {
				return;
			}
		}
	}

	update_post_meta( (int) $page_id, '_wp_page_template', 'page-solar-waermepumpen-leadgenerierung.php' );
}
add_action( 'init', 'nexus_maybe_ensure_energy_systems_page', 27 );

/**
 * Map deprecated service, cluster and tool slugs to their canonical targets.
 *
 * @return array<string, string>
 */
function nexus_get_legacy_offer_redirect_map() {
	$agentur_url = nexus_get_primary_public_url( 'agentur', home_url( '/wordpress-agentur-hannover/' ) );
	$request_url = nexus_get_primary_request_url();

	return [
		// Audit- und Tool-Routen sind in der neuen Positionierung retired.
		'/growth-audit/'               => $request_url,
		'/audit/'                      => $request_url,
		'/customer-journey-audit/'     => $request_url,
		'/360-audit/'                  => $request_url,
		'/wordpress-tech-audit/'       => $request_url,
		'/meta-ads/'                   => $request_url,
		'/kostenlose-tools/'           => $request_url,
		'/tools/'                      => $request_url,
		'/website-performance-analyse/' => $request_url,
		'/kostenlose-tools/website-performance-analyse/' => $request_url,
		'/tools/website-performance-analyse/' => $request_url,
		'/roi-rechner/'                => $request_url,
		// SEO- und Wartungs-Seiten konsolidiert auf die Agentur-Money-Page (Anker-Sektionen).
		// Löst Kannibalisierung für "wordpress agentur hannover" + transferiert SEO-Equity.
		'/wordpress-seo-hannover/'     => trailingslashit( $agentur_url ) . '#technisches-seo',
		'/wordpress-wartung-hannover/' => trailingslashit( $agentur_url ) . '#wordpress-wartung',
		'/seo/'                        => trailingslashit( $agentur_url ) . '#technisches-seo',
		'/wordpress-agentur/'          => $agentur_url,
	];
}

add_action( 'template_redirect', 'nexus_redirect_legacy_offer_paths', 2 );
/**
 * Redirect deprecated service and tool slugs to their current canonical destinations.
 *
 * @return void
 */
function nexus_redirect_legacy_offer_paths() {
	if ( is_admin() || wp_doing_ajax() || is_feed() ) {
		return;
	}

	$current_path = nexus_get_current_request_path();
	$redirect_map = nexus_get_legacy_offer_redirect_map();

	if ( empty( $redirect_map[ $current_path ] ) ) {
		return;
	}

	$target_url  = $redirect_map[ $current_path ];
	$target_path = trailingslashit( '/' . ltrim( (string) wp_parse_url( $target_url, PHP_URL_PATH ), '/' ) );

	if ( $target_path === $current_path ) {
		return;
	}

	wp_safe_redirect( $target_url, 301 );
	exit;
}

/**
 * Resolve the primary contact page ID while supporting the legacy slug.
 *
 * @return int
 */
function nexus_get_contact_page_id() {
	$template_page_id = nexus_get_page_id_by_template( 'page-kontakt.php' );

	if ( $template_page_id ) {
		return $template_page_id;
	}

	return nexus_get_page_id( [ 'kontakt', 'kontaktiere-mich' ] );
}

/**
 * Resolve the primary contact URL while enforcing the new /kontakt/ path.
 *
 * @return string
 */
function nexus_get_contact_url() {
	$page_id = nexus_get_contact_page_id();

	if ( $page_id ) {
		$permalink = get_permalink( $page_id );
		$path      = trailingslashit( '/' . ltrim( (string) wp_parse_url( $permalink, PHP_URL_PATH ), '/' ) );

		if ( '/kontaktiere-mich/' !== $path ) {
			return $permalink;
		}
	}

	return home_url( '/kontakt/' );
}

/**
 * Determine whether the current request is the public contact page.
 *
 * @return bool
 */
function nexus_is_contact_page() {
	$contact_page_id = nexus_get_contact_page_id();
	$contact_path    = trailingslashit( '/' . ltrim( (string) wp_parse_url( home_url( '/kontakt/' ), PHP_URL_PATH ), '/' ) );

	if ( $contact_page_id && is_page( $contact_page_id ) ) {
		return true;
	}

	return $contact_path === nexus_get_current_request_path()
		|| is_page_template( 'page-kontakt.php' )
		|| is_page( 'kontakt' )
		|| is_page( 'kontaktiere-mich' );
}

/**
 * Determine whether the large footer audit CTA should be hidden on the current request.
 *
 * Hide the button on pages that already end with a dedicated audit CTA block.
 *
 * @return bool
 */
function nexus_should_hide_footer_primary_cta() {
	if ( (bool) get_query_var( 'nexus_hide_footer_primary_cta', false ) ) {
		return true;
	}

	if ( is_home() ) {
		return true;
	}

	if ( function_exists( 'nexus_is_wgos_cluster_page' ) && nexus_is_wgos_cluster_page() ) {
		return true;
	}

	$results_page_id = nexus_get_results_page_id();

	if ( $results_page_id && is_page( $results_page_id ) ) {
		return true;
	}

	$page_templates = [
		'template-about.php',
		'page-loesungen.php',
		'page-wordpress-agentur.php',
		'page-wordpress-agentur-hannover.php',
		'page-case-studies-e-commerce.php',
		'page-e3-new-energy.php',
		'page-case-e3.php',
		'page-case-study-domdar.php',
		'page-wgos.php',
		'page-wgos-assets.php',
		'page-ki-integration.php',
		'page-solar-waermepumpen-leadgenerierung.php',
	];

	foreach ( $page_templates as $page_template ) {
		if ( is_page_template( $page_template ) ) {
			return true;
		}
	}

	return is_page(
		[
			'uber-mich',
			'wordpress-agentur',
			'wordpress-agentur-hannover',
			'case-studies-e-commerce',
			'case-studies',
			'ergebnisse',
			'e3-new-energy',
			'case-study-domdar',
			'domdar',
			'wgos',
			'wordpress-growth-operating-system',
			'wgos-systemlandkarte',
			'wgos-asset-hub',
			'systemlandkarte',
			'ki-integration-wordpress',
			'solar-waermepumpen-leadgenerierung',
			'website-fuer-solar-und-waermepumpen-anbieter',
			'loesungen',
		]
	);
}

add_action( 'template_redirect', 'nexus_redirect_legacy_results_path', 1 );
/**
 * Redirect legacy proof overview paths to the canonical results hub.
 *
 * @return void
 */
function nexus_redirect_legacy_results_path() {
	if ( is_admin() || wp_doing_ajax() || is_feed() ) {
		return;
	}

	$current_path = nexus_get_current_request_path();
	$legacy_paths = [
		'/case-studies/',
		'/case-studies-e-commerce/',
	];

	if ( ! in_array( $current_path, $legacy_paths, true ) ) {
		return;
	}

	$target_url = nexus_get_results_url();
	$target_path = trailingslashit( '/' . ltrim( (string) wp_parse_url( $target_url, PHP_URL_PATH ), '/' ) );

	if ( $target_path === $current_path ) {
		return;
	}

	wp_safe_redirect( $target_url, 301 );
	exit;
}

add_action( 'template_redirect', 'nexus_redirect_legacy_energy_systems_path', 1 );
/**
 * Redirect the deprecated energy landing slug to the canonical path.
 *
 * @return void
 */
function nexus_redirect_legacy_energy_systems_path() {
	if ( is_admin() || wp_doing_ajax() || is_feed() ) {
		return;
	}

	$current_path = nexus_get_current_request_path();

	if ( '/website-fuer-solar-und-waermepumpen-anbieter/' !== $current_path ) {
		return;
	}

	$target_url  = nexus_get_energy_systems_url();
	$target_path = trailingslashit( '/' . ltrim( (string) wp_parse_url( $target_url, PHP_URL_PATH ), '/' ) );

	if ( $target_path === $current_path ) {
		return;
	}

	wp_safe_redirect( $target_url, 301 );
	exit;
}

add_filter( 'template_include', 'nexus_force_results_route_templates', 98 );
/**
 * Force route-specific proof templates even if WordPress pages use another template in admin.
 *
 * @param string $template Resolved template path.
 * @return string
 */
function nexus_force_results_route_templates( $template ) {
	if ( is_admin() || ! is_page() ) {
		return $template;
	}

	$route_templates = [
		'case-studies-e-commerce'   => get_stylesheet_directory() . '/page-case-studies-e-commerce.php',
		'ergebnisse'                => get_stylesheet_directory() . '/page-case-studies-e-commerce.php',
		'whitelabel-retainer'       => get_stylesheet_directory() . '/page-whitelabel-retainer.php',
		'whitelabel-retainer-proof' => get_stylesheet_directory() . '/page-whitelabel-retainer.php',
		'whitelabel'                => get_stylesheet_directory() . '/page-whitelabel-retainer.php',
	];

	foreach ( $route_templates as $slug => $forced_template ) {
		if ( ! is_page( $slug ) ) {
			continue;
		}

		if ( file_exists( $forced_template ) ) {
			return $forced_template;
		}
	}

	return $template;
}

add_filter( 'template_include', 'nexus_force_energy_systems_route_template', 98 );
/**
 * Force the energy systems route templates even if the page template was changed in admin.
 *
 * @param string $template Resolved template path.
 * @return string
 */
function nexus_force_energy_systems_route_template( $template ) {
	if ( is_admin() || ! is_page() ) {
		return $template;
	}

	$route_templates = [
		'solar-waermepumpen-leadgenerierung'             => get_stylesheet_directory() . '/page-solar-waermepumpen-leadgenerierung.php',
		'website-fuer-solar-und-waermepumpen-anbieter' => get_stylesheet_directory() . '/page-website-fuer-solar-und-waermepumpen-anbieter.php',
	];

	foreach ( $route_templates as $slug => $forced_template ) {
		if ( ! is_page( $slug ) ) {
			continue;
		}

		if ( file_exists( $forced_template ) ) {
			return $forced_template;
		}
	}

	return $template;
}
