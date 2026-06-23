<?php
/**
 * SEO Cockpit CSV export.
 *
 * Streams the cached query-page snapshot (current + previous range, enriched
 * with WordPress page context) as an Excel-compatible CSV. The handler is
 * admin-only, nonce-protected, and strictly read-only: it never mutates the
 * snapshot, changes frontend behavior, or sets cookies.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Column order for the SEO Cockpit CSV export.
 *
 * @return array<int, string>
 */
function nexus_get_seo_cockpit_export_columns() {
	return [
		'range_days',
		'current_start',
		'current_end',
		'page',
		'query',
		'clicks',
		'impressions',
		'ctr',
		'position',
		'previous_clicks',
		'previous_impressions',
		'previous_ctr',
		'previous_position',
		'delta_clicks',
		'delta_impressions',
		'delta_position',
		'post_id',
		'post_title',
		'page_type',
		'seo_title',
		'seo_description',
		'canonical',
		'in_sitemap',
		'noindex',
		'word_count',
		'non_target_query',
	];
}

/**
 * Format a float for German Excel (comma decimal, no thousands separator).
 *
 * @param mixed $value    Numeric value.
 * @param int   $decimals Decimal places.
 * @return string
 */
function nexus_seo_cockpit_export_decimal( $value, $decimals = 2 ) {
	return number_format( (float) $value, max( 0, absint( $decimals ) ), ',', '' );
}

/**
 * Build the export rows from a cockpit snapshot.
 *
 * Joins the current query-page rows against the previous range (by page|query,
 * mirroring the insights join) and enriches each row with the precomputed
 * WordPress page context, falling back to a live lookup for pages outside the
 * top-pages map. Non-target queries are flagged, not filtered.
 *
 * @param array<string, mixed> $snapshot Cockpit snapshot.
 * @return array<int, array<string, mixed>>
 */
function nexus_build_seo_cockpit_export_rows( $snapshot ) {
	$rows = isset( $snapshot['query_page_rows'] ) && is_array( $snapshot['query_page_rows'] ) ? $snapshot['query_page_rows'] : [];

	if ( empty( $rows ) ) {
		return [];
	}

	$range_days    = absint( $snapshot['range_days'] ?? 0 );
	$current_start = (string) ( $snapshot['ranges']['current_start'] ?? '' );
	$current_end   = (string) ( $snapshot['ranges']['current_end'] ?? '' );
	$contexts      = isset( $snapshot['page_contexts'] ) && is_array( $snapshot['page_contexts'] ) ? $snapshot['page_contexts'] : [];

	// Index the previous range by page|query, matching nexus_get_seo_cockpit_quick_wins().
	$previous = [];
	foreach ( (array) ( $snapshot['previous_query_page_rows'] ?? [] ) as $prev_row ) {
		if ( ! is_array( $prev_row ) ) {
			continue;
		}
		$prev_page  = nexus_get_seo_cockpit_row_key( $prev_row, 0 );
		$prev_query = nexus_get_seo_cockpit_row_key( $prev_row, 1 );
		if ( '' === $prev_page || '' === $prev_query ) {
			continue;
		}
		$previous[ $prev_page . '|' . $prev_query ] = $prev_row;
	}

	$context_cache = [];
	$export        = [];

	foreach ( $rows as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$page  = nexus_get_seo_cockpit_row_key( $row, 0 );
		$query = nexus_get_seo_cockpit_row_key( $row, 1 );

		if ( '' === $page && '' === $query ) {
			continue;
		}

		$clicks      = (int) round( (float) ( $row['clicks'] ?? 0 ) );
		$impressions = (int) round( (float) ( $row['impressions'] ?? 0 ) );
		$ctr         = (float) ( $row['ctr'] ?? 0 );
		$position    = (float) ( $row['position'] ?? 0 );

		$prev_row         = $previous[ $page . '|' . $query ] ?? [];
		$has_prev         = ! empty( $prev_row );
		$prev_clicks      = (int) round( (float) ( $prev_row['clicks'] ?? 0 ) );
		$prev_impressions = (int) round( (float) ( $prev_row['impressions'] ?? 0 ) );
		$prev_ctr         = (float) ( $prev_row['ctr'] ?? 0 );
		$prev_position    = (float) ( $prev_row['position'] ?? 0 );

		// Resolve WP context from the precomputed map; fall back to a live lookup once per URL.
		$normalized = nexus_normalize_seo_cockpit_url( $page );
		if ( array_key_exists( $normalized, $context_cache ) ) {
			$context = $context_cache[ $normalized ];
		} elseif ( isset( $contexts[ $normalized ] ) && is_array( $contexts[ $normalized ] ) ) {
			$context                      = $contexts[ $normalized ];
			$context_cache[ $normalized ] = $context;
		} else {
			$context                      = function_exists( 'nexus_get_seo_cockpit_wp_context_for_url' ) ? nexus_get_seo_cockpit_wp_context_for_url( $normalized ) : [];
			$context_cache[ $normalized ] = $context;
		}

		$is_non_target = '' !== $query && function_exists( 'nexus_is_seo_cockpit_non_target_query' ) && nexus_is_seo_cockpit_non_target_query( $query );

		$export[] = [
			'range_days'           => $range_days,
			'current_start'        => $current_start,
			'current_end'          => $current_end,
			'page'                 => $page,
			'query'                => $query,
			'clicks'               => $clicks,
			'impressions'          => $impressions,
			'ctr'                  => nexus_seo_cockpit_export_decimal( $ctr, 4 ),
			'position'             => nexus_seo_cockpit_export_decimal( $position, 2 ),
			'previous_clicks'      => $prev_clicks,
			'previous_impressions' => $prev_impressions,
			'previous_ctr'         => nexus_seo_cockpit_export_decimal( $prev_ctr, 4 ),
			'previous_position'    => nexus_seo_cockpit_export_decimal( $prev_position, 2 ),
			'delta_clicks'         => $clicks - $prev_clicks,
			'delta_impressions'    => $impressions - $prev_impressions,
			'delta_position'       => $has_prev ? nexus_seo_cockpit_export_decimal( $position - $prev_position, 2 ) : '',
			'post_id'              => absint( $context['post_id'] ?? 0 ),
			'post_title'           => (string) ( $context['post_title'] ?? '' ),
			'page_type'            => (string) ( $context['page_type'] ?? '' ),
			'seo_title'            => (string) ( $context['seo_title'] ?? '' ),
			'seo_description'      => (string) ( $context['seo_description'] ?? '' ),
			'canonical'            => (string) ( $context['canonical'] ?? '' ),
			'in_sitemap'           => ! empty( $context['in_sitemap'] ) ? 1 : 0,
			'noindex'              => ! empty( $context['noindex'] ) ? 1 : 0,
			'word_count'           => absint( $context['word_count'] ?? 0 ),
			'non_target_query'     => $is_non_target ? 1 : 0,
		];
	}

	return $export;
}

/**
 * Stream the SEO Cockpit snapshot as an Excel-compatible CSV download.
 *
 * @return void
 */
function nexus_handle_seo_cockpit_export_action() {
	if ( ! nexus_current_user_can_manage_seo_cockpit() ) {
		wp_die( 'Nicht erlaubt.' );
	}

	check_admin_referer( 'nexus_seo_cockpit_export' );

	$range    = isset( $_POST['range'] ) ? absint( wp_unslash( $_POST['range'] ) ) : 28;
	$snapshot = nexus_get_seo_cockpit_snapshot( false, $range );

	if ( is_wp_error( $snapshot ) || ! is_array( $snapshot ) ) {
		wp_safe_redirect(
			nexus_get_seo_cockpit_dashboard_url(
				[
					'range'            => $range,
					'nexus_seo_notice' => 'export_failed',
				]
			)
		);
		exit;
	}

	$export_rows = nexus_build_seo_cockpit_export_rows( $snapshot );

	if ( empty( $export_rows ) ) {
		wp_safe_redirect(
			nexus_get_seo_cockpit_dashboard_url(
				[
					'range'            => $range,
					'nexus_seo_notice' => 'export_empty',
				]
			)
		);
		exit;
	}

	$range_days = absint( $snapshot['range_days'] ?? $range );
	$stamp      = (string) ( $snapshot['ranges']['current_end'] ?? wp_date( 'Y-m-d' ) );
	$filename   = sprintf( 'seo-cockpit-export-%dd-%s.csv', $range_days, sanitize_file_name( $stamp ) );

	nocache_headers();
	header( 'Content-Type: text/csv; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename="' . $filename . '"' );

	// UTF-8 BOM so Excel detects the encoding; semicolon delimiter for German Excel.
	echo "\xEF\xBB\xBF";

	$columns = nexus_get_seo_cockpit_export_columns();
	$output  = fopen( 'php://output', 'w' );

	fputcsv( $output, $columns, ';', '"', '' );

	foreach ( $export_rows as $export_row ) {
		$line = [];
		foreach ( $columns as $column ) {
			$line[] = $export_row[ $column ] ?? '';
		}
		fputcsv( $output, $line, ';', '"', '' );
	}

	fclose( $output );
	exit;
}
add_action( 'admin_post_nexus_seo_cockpit_export', 'nexus_handle_seo_cockpit_export_action' );
