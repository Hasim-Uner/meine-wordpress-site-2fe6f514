<?php
/**
 * SEO Cockpit CSV export.
 *
 * Streams the cached query-page and page-total snapshot (current + previous
 * range, enriched with WordPress page context) as an Excel-compatible CSV.
 * The handler is admin-only, nonce-protected, and strictly read-only: it never
 * mutates the snapshot, changes frontend behavior, or sets cookies.
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
		'previous_start',
		'previous_end',
		'row_scope',
		'period_presence',
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
 * Index Search Console metric rows by page or by page and query.
 *
 * @param array<int, mixed> $rows          Raw metric rows.
 * @param bool              $include_query Whether the query key is part of the identity.
 * @return array<string, array<string, mixed>>
 */
function nexus_index_seo_cockpit_export_metric_rows( $rows, $include_query = true ) {
	$indexed = [];

	foreach ( (array) $rows as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$page  = nexus_get_seo_cockpit_row_key( $row, 0 );
		$query = $include_query ? nexus_get_seo_cockpit_row_key( $row, 1 ) : '';

		if ( '' === $page || ( $include_query && '' === $query ) ) {
			continue;
		}

		$key             = $include_query ? $page . '|' . $query : $page;
		$indexed[ $key ] = $row;
	}

	return $indexed;
}

/**
 * Resolve the WordPress context for an exported URL once per request.
 *
 * @param string                             $page          Search Console URL.
 * @param array<string, array<string, mixed>> $contexts      Precomputed page contexts.
 * @param array<string, array<string, mixed>> $context_cache Per-export lookup cache.
 * @return array<string, mixed>
 */
function nexus_get_seo_cockpit_export_page_context( $page, $contexts, &$context_cache ) {
	$normalized = nexus_normalize_seo_cockpit_url( $page );

	if ( array_key_exists( $normalized, $context_cache ) ) {
		return $context_cache[ $normalized ];
	}

	if ( isset( $contexts[ $normalized ] ) && is_array( $contexts[ $normalized ] ) ) {
		$context = $contexts[ $normalized ];
	} else {
		$context = function_exists( 'nexus_get_seo_cockpit_wp_context_for_url' )
			? nexus_get_seo_cockpit_wp_context_for_url( $normalized )
			: [];
	}

	$context_cache[ $normalized ] = is_array( $context ) ? $context : [];

	return $context_cache[ $normalized ];
}

/**
 * Build one normalized export row for a current/previous metric pair.
 *
 * Missing-period CTR and position values stay blank. Writing zero there would
 * turn a disappeared query into a fictitious position improvement.
 *
 * @param string                             $scope         query_page or page_total.
 * @param array<string, mixed>|null           $current_row   Current-period metric row.
 * @param array<string, mixed>|null           $previous_row  Previous-period metric row.
 * @param array<string, mixed>                $snapshot      Cockpit snapshot.
 * @param array<string, array<string, mixed>> $contexts      Precomputed page contexts.
 * @param array<string, array<string, mixed>> $context_cache Per-export lookup cache.
 * @return array<string, mixed>
 */
function nexus_build_seo_cockpit_export_metric_row( $scope, $current_row, $previous_row, $snapshot, $contexts, &$context_cache ) {
	$has_current  = is_array( $current_row );
	$has_previous = is_array( $previous_row );
	$source_row   = $has_current ? $current_row : $previous_row;

	if ( ! is_array( $source_row ) ) {
		return [];
	}

	$page  = nexus_get_seo_cockpit_row_key( $source_row, 0 );
	$query = 'query_page' === $scope ? nexus_get_seo_cockpit_row_key( $source_row, 1 ) : '';

	if ( '' === $page || ( 'query_page' === $scope && '' === $query ) ) {
		return [];
	}

	$current_clicks       = $has_current ? (int) round( (float) ( $current_row['clicks'] ?? 0 ) ) : 0;
	$current_impressions  = $has_current ? (int) round( (float) ( $current_row['impressions'] ?? 0 ) ) : 0;
	$previous_clicks      = $has_previous ? (int) round( (float) ( $previous_row['clicks'] ?? 0 ) ) : 0;
	$previous_impressions = $has_previous ? (int) round( (float) ( $previous_row['impressions'] ?? 0 ) ) : 0;
	$current_position     = $has_current ? (float) ( $current_row['position'] ?? 0 ) : null;
	$previous_position    = $has_previous ? (float) ( $previous_row['position'] ?? 0 ) : null;

	if ( $has_current && $has_previous ) {
		$period_presence = 'both';
	} elseif ( $has_current ) {
		$period_presence = 'current_only';
	} else {
		$period_presence = 'previous_only';
	}

	$context       = nexus_get_seo_cockpit_export_page_context( $page, $contexts, $context_cache );
	$is_non_target = 'query_page' === $scope
		&& function_exists( 'nexus_is_seo_cockpit_non_target_query' )
		&& nexus_is_seo_cockpit_non_target_query( $query );
	$ranges        = isset( $snapshot['ranges'] ) && is_array( $snapshot['ranges'] ) ? $snapshot['ranges'] : [];

	return [
		'range_days'           => absint( $snapshot['range_days'] ?? 0 ),
		'current_start'        => (string) ( $ranges['current_start'] ?? '' ),
		'current_end'          => (string) ( $ranges['current_end'] ?? '' ),
		'previous_start'       => (string) ( $ranges['previous_start'] ?? '' ),
		'previous_end'         => (string) ( $ranges['previous_end'] ?? '' ),
		'row_scope'            => $scope,
		'period_presence'      => $period_presence,
		'page'                 => $page,
		'query'                => $query,
		'clicks'               => $current_clicks,
		'impressions'          => $current_impressions,
		'ctr'                  => $has_current ? nexus_seo_cockpit_export_decimal( $current_row['ctr'] ?? 0, 4 ) : '',
		'position'             => $has_current ? nexus_seo_cockpit_export_decimal( $current_position, 2 ) : '',
		'previous_clicks'      => $previous_clicks,
		'previous_impressions' => $previous_impressions,
		'previous_ctr'         => $has_previous ? nexus_seo_cockpit_export_decimal( $previous_row['ctr'] ?? 0, 4 ) : '',
		'previous_position'    => $has_previous ? nexus_seo_cockpit_export_decimal( $previous_position, 2 ) : '',
		'delta_clicks'         => $current_clicks - $previous_clicks,
		'delta_impressions'    => $current_impressions - $previous_impressions,
		'delta_position'       => $has_current && $has_previous ? nexus_seo_cockpit_export_decimal( $current_position - $previous_position, 2 ) : '',
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

/**
 * Build the export rows from a cockpit snapshot.
 *
 * Exports the union of current and previous query-page rows so complete losses
 * remain visible. Page-total rows are appended as a separate row_scope and let
 * consumers distinguish missing query detail from missing page visibility.
 *
 * @param array<string, mixed> $snapshot Cockpit snapshot.
 * @return array<int, array<string, mixed>>
 */
function nexus_build_seo_cockpit_export_rows( $snapshot ) {
	$contexts      = isset( $snapshot['page_contexts'] ) && is_array( $snapshot['page_contexts'] ) ? $snapshot['page_contexts'] : [];
	$context_cache = [];
	$export        = [];

	$scopes = [
		'query_page' => [
			'current'       => $snapshot['query_page_rows'] ?? [],
			'previous'      => $snapshot['previous_query_page_rows'] ?? [],
			'include_query' => true,
		],
		'page_total' => [
			'current'       => $snapshot['current_page_rows'] ?? [],
			'previous'      => $snapshot['previous_page_rows'] ?? [],
			'include_query' => false,
		],
	];

	foreach ( $scopes as $scope => $config ) {
		$current  = nexus_index_seo_cockpit_export_metric_rows( $config['current'], $config['include_query'] );
		$previous = nexus_index_seo_cockpit_export_metric_rows( $config['previous'], $config['include_query'] );
		$keys     = array_values( array_unique( array_merge( array_keys( $current ), array_keys( $previous ) ) ) );

		foreach ( $keys as $key ) {
			$export_row = nexus_build_seo_cockpit_export_metric_row(
				$scope,
				$current[ $key ] ?? null,
				$previous[ $key ] ?? null,
				$snapshot,
				$contexts,
				$context_cache
			);

			if ( ! empty( $export_row ) ) {
				$export[] = $export_row;
			}
		}
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
