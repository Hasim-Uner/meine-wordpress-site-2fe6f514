<?php
/**
 * SEO Cockpit lead and attribution helpers.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return one normalized internal URL for lead attribution.
 *
 * @param string $url Raw URL.
 * @return string
 */
function nexus_get_seo_cockpit_internal_attribution_url( $url ) {
	$normalized = nexus_normalize_seo_cockpit_url( $url );

	if ( '' === $normalized ) {
		return '';
	}

	$host      = strtolower( (string) wp_parse_url( $normalized, PHP_URL_HOST ) );
	$home_host = strtolower( (string) wp_parse_url( home_url( '/' ), PHP_URL_HOST ) );

	if ( '' === $host || $host !== $home_host ) {
		return '';
	}

	$path = (string) wp_parse_url( $normalized, PHP_URL_PATH );
	$path = '/' . ltrim( $path, '/' );

	if ( '/' !== $path ) {
		$path = trailingslashit( $path );
	}

	return home_url( $path );
}

/**
 * Return one local timestamp for a review request post.
 *
 * @param WP_Post $post Review request post.
 * @return int
 */
function nexus_get_seo_cockpit_review_request_timestamp( $post ) {
	if ( ! ( $post instanceof WP_Post ) ) {
		return 0;
	}

	$datetime = date_create_immutable_from_format( 'Y-m-d H:i:s', (string) $post->post_date, wp_timezone() );

	return $datetime instanceof DateTimeImmutable ? $datetime->getTimestamp() : 0;
}

/**
 * Return one range boundary timestamp.
 *
 * @param string $date       Date string in Y-m-d format.
 * @param bool   $end_of_day Whether to use the end of the day.
 * @return int
 */
function nexus_get_seo_cockpit_lead_range_timestamp( $date, $end_of_day = false ) {
	$time     = $end_of_day ? '23:59:59' : '00:00:00';
	$datetime = date_create_immutable_from_format( 'Y-m-d H:i:s', trim( (string) $date ) . ' ' . $time, wp_timezone() );

	return $datetime instanceof DateTimeImmutable ? $datetime->getTimestamp() : 0;
}

/**
 * Return the reporting bucket for one lead timestamp.
 *
 * @param int                  $timestamp Lead timestamp.
 * @param array<string, mixed> $ranges    Snapshot date ranges.
 * @return string
 */
function nexus_get_seo_cockpit_lead_bucket_for_timestamp( $timestamp, $ranges ) {
	$timestamp      = (int) $timestamp;
	$current_start  = nexus_get_seo_cockpit_lead_range_timestamp( (string) ( $ranges['current_start'] ?? '' ) );
	$current_end    = nexus_get_seo_cockpit_lead_range_timestamp( (string) ( $ranges['current_end'] ?? '' ), true );
	$previous_start = nexus_get_seo_cockpit_lead_range_timestamp( (string) ( $ranges['previous_start'] ?? '' ) );
	$previous_end   = nexus_get_seo_cockpit_lead_range_timestamp( (string) ( $ranges['previous_end'] ?? '' ), true );

	if ( $current_start && $current_end && $timestamp >= $current_start && $timestamp <= $current_end ) {
		return 'current';
	}

	if ( $previous_start && $previous_end && $timestamp >= $previous_start && $timestamp <= $previous_end ) {
		return 'previous';
	}

	return '';
}

/**
 * Return the status keys that count as progressed.
 *
 * @return array<int, string>
 */
function nexus_get_seo_cockpit_progressed_review_statuses() {
	return [ 'in_review', 'sent', 'won' ];
}

/**
 * Return one empty overview bucket for lead metrics.
 *
 * @return array<string, int>
 */
function nexus_get_seo_cockpit_empty_lead_metrics() {
	return [
		'requests'         => 0,
		'mapped_requests'  => 0,
		'unmapped_requests' => 0,
		'progressed'       => 0,
		'won'              => 0,
		'mapped_pages'     => 0,
	];
}

/**
 * Return one human label for a lead source.
 *
 * @param string $source Source key.
 * @return string
 */
function nexus_get_seo_cockpit_lead_source_label( $source ) {
	$labels = [
		'growth_audit_funnel'   => 'Marktcheck',
		'energy_systems_landing' => 'Energie-Landing',
	];

	$source = sanitize_key( (string) $source );

	return $labels[ $source ] ?? ( '' !== $source ? $source : 'Unbekannt' );
}

/**
 * Return one human label for a review status.
 *
 * @param string $status Status key.
 * @return string
 */
function nexus_get_seo_cockpit_lead_status_label( $status ) {
	$labels = function_exists( 'nexus_get_review_status_options' ) ? nexus_get_review_status_options() : [];
	$status = sanitize_key( (string) $status );

	return isset( $labels[ $status ] ) ? (string) $labels[ $status ] : ( '' !== $status ? $status : 'Unbekannt' );
}

/**
 * Return one human label for a lead attribution mode.
 *
 * @param string $mode Attribution mode.
 * @return string
 */
function nexus_get_seo_cockpit_lead_mode_label( $mode ) {
	$labels = [
		'entry'    => 'Einstieg',
		'previous' => 'Assist',
		'landing'  => 'Landing',
	];

	$mode = sanitize_key( (string) $mode );

	return $labels[ $mode ] ?? ( '' !== $mode ? $mode : 'Unbekannt' );
}

/**
 * Return one prioritized attribution URL and its mode.
 *
 * @param string $entry_url    First internal page in session.
 * @param string $previous_url Previous internal page before form.
 * @param string $landing_url  Current form page.
 * @return array<string, string>
 */
function nexus_get_seo_cockpit_review_request_attribution_target( $entry_url, $previous_url, $landing_url ) {
	$entry_url    = nexus_get_seo_cockpit_internal_attribution_url( $entry_url );
	$previous_url = nexus_get_seo_cockpit_internal_attribution_url( $previous_url );
	$landing_url  = nexus_get_seo_cockpit_internal_attribution_url( $landing_url );

	if ( '' !== $entry_url ) {
		return [
			'url'  => $entry_url,
			'mode' => 'entry',
		];
	}

	if ( '' !== $previous_url ) {
		return [
			'url'  => $previous_url,
			'mode' => 'previous',
		];
	}

	if ( '' !== $landing_url ) {
		return [
			'url'  => $landing_url,
			'mode' => 'landing',
		];
	}

	return [
		'url'  => '',
		'mode' => '',
	];
}

/**
 * Return one page-level lead metrics bucket.
 *
 * @return array<string, int>
 */
function nexus_get_seo_cockpit_empty_page_lead_metrics() {
	return [
		'requests'   => 0,
		'progressed' => 0,
		'won'        => 0,
	];
}

/**
 * Return one empty lead page row.
 *
 * @param string $url Internal URL.
 * @return array<string, mixed>
 */
function nexus_get_seo_cockpit_empty_lead_page_row( $url ) {
	$context    = function_exists( 'nexus_get_seo_cockpit_wp_context_for_url' ) ? nexus_get_seo_cockpit_wp_context_for_url( $url ) : [];
	$page_role  = function_exists( 'nexus_get_seo_cockpit_page_role' ) ? nexus_get_seo_cockpit_page_role( $context, $url ) : '';
	$page_label = ! empty( $context['post_title'] ) ? (string) $context['post_title'] : $url;

	return [
		'url'               => $url,
		'label'             => $page_label,
		'page_role'         => $page_role,
		'page_role_label'   => function_exists( 'nexus_get_seo_cockpit_page_role_label' ) ? nexus_get_seo_cockpit_page_role_label( $page_role ) : '',
		'detail_url'        => function_exists( 'nexus_get_seo_cockpit_detail_url' ) ? nexus_get_seo_cockpit_detail_url( $url ) : '',
		'current'           => nexus_get_seo_cockpit_empty_page_lead_metrics(),
		'previous'          => nexus_get_seo_cockpit_empty_page_lead_metrics(),
		'lifetime'          => nexus_get_seo_cockpit_empty_page_lead_metrics(),
		'attribution_modes' => [
			'entry'    => 0,
			'previous' => 0,
			'landing'  => 0,
		],
		'sources'           => [],
		'last_request_at'   => 0,
		'last_status'       => '',
		'last_source'       => '',
	];
}

/**
 * Convert a keyed count map into a sorted list with labels.
 *
 * @param array<string, int>    $counts Count map.
 * @param callable|string|null  $label_callback Optional label resolver.
 * @param int                   $limit Max rows.
 * @return array<int, array<string, mixed>>
 */
function nexus_get_seo_cockpit_lead_ranked_counts( $counts, $label_callback = null, $limit = 5 ) {
	$counts = array_filter(
		(array) $counts,
		static function ( $count ) {
			return absint( $count ) > 0;
		}
	);

	if ( empty( $counts ) ) {
		return [];
	}

	arsort( $counts );
	$rows = [];

	foreach ( array_slice( $counts, 0, $limit, true ) as $key => $count ) {
		$label = $key;

		if ( is_callable( $label_callback ) ) {
			$label = (string) call_user_func( $label_callback, $key );
		} elseif ( is_string( $label_callback ) && function_exists( $label_callback ) ) {
			$label = (string) call_user_func( $label_callback, $key );
		}

		$rows[] = [
			'key'   => (string) $key,
			'label' => $label,
			'count' => (int) $count,
		];
	}

	return $rows;
}

/**
 * Build a lead snapshot from audit requests.
 *
 * @param array<string, mixed> $ranges Snapshot date ranges.
 * @return array<string, mixed>
 */
function nexus_get_seo_cockpit_lead_snapshot_data( $ranges ) {
	static $cache = [];

	$cache_key = md5( wp_json_encode( (array) $ranges ) );

	if ( isset( $cache[ $cache_key ] ) ) {
		return $cache[ $cache_key ];
	}

	if ( ! post_type_exists( 'nexus_review_request' ) ) {
		$cache[ $cache_key ] = [
			'available'    => false,
			'note'         => 'Der Audit-CRM-Post-Type ist auf dieser Instanz nicht verfügbar.',
			'overview'     => [
				'current'  => nexus_get_seo_cockpit_empty_lead_metrics(),
				'previous' => nexus_get_seo_cockpit_empty_lead_metrics(),
				'lifetime' => nexus_get_seo_cockpit_empty_lead_metrics(),
			],
			'status_rows'  => [],
			'source_rows'  => [],
			'top_pages'    => [],
			'page_map'     => [],
		];

		return $cache[ $cache_key ];
	}

	$posts        = get_posts(
		[
			'post_type'              => 'nexus_review_request',
			'post_status'            => 'private',
			'posts_per_page'         => -1,
			'orderby'                => 'date',
			'order'                  => 'DESC',
			'no_found_rows'          => true,
			'update_post_meta_cache' => true,
			'update_post_term_cache' => false,
		]
	);
	$overview     = [
		'current'  => nexus_get_seo_cockpit_empty_lead_metrics(),
		'previous' => nexus_get_seo_cockpit_empty_lead_metrics(),
		'lifetime' => nexus_get_seo_cockpit_empty_lead_metrics(),
	];
	$status_sets  = [
		'current'  => [],
		'previous' => [],
		'lifetime' => [],
	];
	$source_sets  = [
		'current'  => [],
		'previous' => [],
		'lifetime' => [],
	];
	$page_map     = [];
	$progressed   = nexus_get_seo_cockpit_progressed_review_statuses();

	foreach ( (array) $posts as $post ) {
		if ( ! ( $post instanceof WP_Post ) ) {
			continue;
		}

		$timestamp          = nexus_get_seo_cockpit_review_request_timestamp( $post );
		$bucket             = nexus_get_seo_cockpit_lead_bucket_for_timestamp( $timestamp, $ranges );
		$status             = sanitize_key( (string) get_post_meta( $post->ID, '_nexus_review_status', true ) );
		$source             = sanitize_key( (string) get_post_meta( $post->ID, '_nexus_review_source', true ) );
		$landing_page_url   = (string) get_post_meta( $post->ID, '_nexus_review_landing_page_url', true );
		$entry_page_url     = (string) get_post_meta( $post->ID, '_nexus_review_entry_page_url', true );
		$previous_page_url  = (string) get_post_meta( $post->ID, '_nexus_review_previous_internal_url', true );
		$attribution_target = nexus_get_seo_cockpit_review_request_attribution_target( $entry_page_url, $previous_page_url, $landing_page_url );
		$attributed_url     = (string) ( $attribution_target['url'] ?? '' );
		$attribution_mode   = sanitize_key( (string) ( $attribution_target['mode'] ?? '' ) );
		$windows            = [ 'lifetime' ];

		if ( '' !== $bucket ) {
			$windows[] = $bucket;
		}

		foreach ( $windows as $window ) {
			$overview[ $window ]['requests'] += 1;

			if ( '' !== $attributed_url ) {
				$overview[ $window ]['mapped_requests'] += 1;
			} else {
				$overview[ $window ]['unmapped_requests'] += 1;
			}

			if ( in_array( $status, $progressed, true ) ) {
				$overview[ $window ]['progressed'] += 1;
			}

			if ( 'won' === $status ) {
				$overview[ $window ]['won'] += 1;
			}

			if ( '' !== $status ) {
				$status_sets[ $window ][ $status ] = isset( $status_sets[ $window ][ $status ] )
					? (int) $status_sets[ $window ][ $status ] + 1
					: 1;
			}

			if ( '' !== $source ) {
				$source_sets[ $window ][ $source ] = isset( $source_sets[ $window ][ $source ] )
					? (int) $source_sets[ $window ][ $source ] + 1
					: 1;
			}
		}

		if ( '' === $attributed_url ) {
			continue;
		}

		if ( ! isset( $page_map[ $attributed_url ] ) ) {
			$page_map[ $attributed_url ] = nexus_get_seo_cockpit_empty_lead_page_row( $attributed_url );
		}

		foreach ( $windows as $window ) {
			$page_map[ $attributed_url ][ $window ]['requests'] += 1;

			if ( in_array( $status, $progressed, true ) ) {
				$page_map[ $attributed_url ][ $window ]['progressed'] += 1;
			}

			if ( 'won' === $status ) {
				$page_map[ $attributed_url ][ $window ]['won'] += 1;
			}
		}

		if ( '' !== $attribution_mode && isset( $page_map[ $attributed_url ]['attribution_modes'][ $attribution_mode ] ) ) {
			$page_map[ $attributed_url ]['attribution_modes'][ $attribution_mode ] += 1;
		}

		if ( '' !== $source ) {
			$page_map[ $attributed_url ]['sources'][ $source ] = isset( $page_map[ $attributed_url ]['sources'][ $source ] )
				? (int) $page_map[ $attributed_url ]['sources'][ $source ] + 1
				: 1;
		}

		if ( $timestamp > (int) $page_map[ $attributed_url ]['last_request_at'] ) {
			$page_map[ $attributed_url ]['last_request_at'] = $timestamp;
			$page_map[ $attributed_url ]['last_status']     = $status;
			$page_map[ $attributed_url ]['last_source']     = $source;
		}
	}

	foreach ( [ 'current', 'previous', 'lifetime' ] as $window ) {
		$mapped_pages = 0;

		foreach ( $page_map as $page ) {
			if ( (int) ( $page[ $window ]['requests'] ?? 0 ) > 0 ) {
				$mapped_pages += 1;
			}
		}

		$overview[ $window ]['mapped_pages'] = $mapped_pages;
	}

	$top_pages = array_values( $page_map );
	usort(
		$top_pages,
		static function ( $left, $right ) {
			$current_diff = (int) ( $right['current']['requests'] ?? 0 ) <=> (int) ( $left['current']['requests'] ?? 0 );

			if ( 0 !== $current_diff ) {
				return $current_diff;
			}

			$won_diff = (int) ( $right['lifetime']['won'] ?? 0 ) <=> (int) ( $left['lifetime']['won'] ?? 0 );

			if ( 0 !== $won_diff ) {
				return $won_diff;
			}

			return (int) ( $right['last_request_at'] ?? 0 ) <=> (int) ( $left['last_request_at'] ?? 0 );
		}
	);

	$cache[ $cache_key ] = [
		'available'   => true,
		'note'        => 'Audit-Leads werden aus dem internen CRM gezogen. Die interne Seitenzuordnung nutzt Einstieg, letzte interne Seite und Formular-Landing für künftige Attributionssignale.',
		'overview'    => $overview,
		'status_rows' => [
			'current'  => nexus_get_seo_cockpit_lead_ranked_counts( $status_sets['current'], 'nexus_get_seo_cockpit_lead_status_label' ),
			'previous' => nexus_get_seo_cockpit_lead_ranked_counts( $status_sets['previous'], 'nexus_get_seo_cockpit_lead_status_label' ),
			'lifetime' => nexus_get_seo_cockpit_lead_ranked_counts( $status_sets['lifetime'], 'nexus_get_seo_cockpit_lead_status_label', 8 ),
		],
		'source_rows' => [
			'current'  => nexus_get_seo_cockpit_lead_ranked_counts( $source_sets['current'], 'nexus_get_seo_cockpit_lead_source_label' ),
			'previous' => nexus_get_seo_cockpit_lead_ranked_counts( $source_sets['previous'], 'nexus_get_seo_cockpit_lead_source_label' ),
			'lifetime' => nexus_get_seo_cockpit_lead_ranked_counts( $source_sets['lifetime'], 'nexus_get_seo_cockpit_lead_source_label' ),
		],
		'top_pages'   => array_slice( $top_pages, 0, 8 ),
		'page_map'    => $page_map,
	];

	return $cache[ $cache_key ];
}

/**
 * Return detail lead data for one internal URL.
 *
 * @param string               $url    Internal frontend URL.
 * @param array<string, mixed> $ranges Snapshot date ranges.
 * @return array<string, mixed>
 */
function nexus_get_seo_cockpit_lead_detail_data( $url, $ranges ) {
	$url      = nexus_get_seo_cockpit_internal_attribution_url( $url );
	$snapshot = nexus_get_seo_cockpit_lead_snapshot_data( $ranges );

	if ( empty( $snapshot['available'] ) ) {
		return [
			'available' => false,
			'matched'   => false,
			'note'      => (string) ( $snapshot['note'] ?? 'Lead-Daten sind derzeit nicht verfügbar.' ),
		];
	}

	$page = isset( $snapshot['page_map'][ $url ] ) && is_array( $snapshot['page_map'][ $url ] )
		? $snapshot['page_map'][ $url ]
		: nexus_get_seo_cockpit_empty_lead_page_row( $url );

	$page['available'] = true;
	$page['matched']   = (int) ( $page['lifetime']['requests'] ?? 0 ) > 0;
	$page['note']      = $page['matched']
		? 'Diese URL hat bereits zugeordnete Audit-Leads im internen CRM.'
		: 'Für diese URL wurden bisher noch keine zugeordneten Audit-Leads gefunden.';

	return $page;
}

/**
 * Return one lead-signal score for prioritization.
 *
 * @param array<string, mixed> $lead_page Lead page payload.
 * @return int
 */
function nexus_get_seo_cockpit_lead_signal_score( $lead_page ) {
	$lead_page         = is_array( $lead_page ) ? $lead_page : [];
	$current_requests  = (int) ( $lead_page['current']['requests'] ?? 0 );
	$current_won       = (int) ( $lead_page['current']['won'] ?? 0 );
	$lifetime_requests = (int) ( $lead_page['lifetime']['requests'] ?? 0 );
	$lifetime_won      = (int) ( $lead_page['lifetime']['won'] ?? 0 );

	if ( $current_won > 0 ) {
		return 16;
	}

	if ( $current_requests >= 3 ) {
		return 14;
	}

	if ( $current_requests > 0 ) {
		return 10;
	}

	if ( $lifetime_won > 0 ) {
		return 9;
	}

	if ( $lifetime_requests >= 3 ) {
		return 7;
	}

	if ( $lifetime_requests > 0 ) {
		return 4;
	}

	return 0;
}
