#!/usr/bin/env bash
# Regression tests for the SEO Cockpit CSV row contract.

set -uo pipefail

TESTS_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
REPO_ROOT="$(cd "$TESTS_DIR/../../../.." && pwd)"
EXPORT_FILE="$REPO_ROOT/blocksy-child/inc/seo-cockpit/seo-cockpit-export.php"

EXPORT_FILE="$EXPORT_FILE" php <<'PHP'
<?php
define( 'ABSPATH', '/' );

$fallback_calls = 0;
$passed         = 0;

function absint( $value ) {
	return abs( (int) $value );
}

function add_action() {}

function nexus_get_seo_cockpit_row_key( $row, $index ) {
	return isset( $row['keys'][ $index ] ) ? (string) $row['keys'][ $index ] : '';
}

function nexus_normalize_seo_cockpit_url( $url ) {
	return (string) $url;
}

function nexus_get_seo_cockpit_wp_context_for_url( $url ) {
	global $fallback_calls;
	$fallback_calls++;

	return [
		'post_id'    => 22,
		'post_title' => 'Previous only',
		'page_type'  => 'page',
		'canonical'  => $url,
		'in_sitemap' => true,
	];
}

function nexus_is_seo_cockpit_non_target_query( $query ) {
	return 'irrelevant' === $query;
}

function expect_same( $label, $expected, $actual ) {
	global $passed;

	if ( $expected !== $actual ) {
		fwrite( STDERR, sprintf( "FAIL %s\n  expected: %s\n  actual:   %s\n", $label, var_export( $expected, true ), var_export( $actual, true ) ) );
		exit( 1 );
	}

	$passed++;
	echo "ok  $label\n";
}

require getenv( 'EXPORT_FILE' );

$url_a = 'https://hasimuener.de/a/';
$url_b = 'https://hasimuener.de/b/';
$snapshot = [
	'range_days' => 28,
	'ranges'     => [
		'current_start'  => '2026-01-04',
		'current_end'    => '2026-01-31',
		'previous_start' => '2025-12-07',
		'previous_end'   => '2026-01-03',
	],
	'query_page_rows' => [
		[ 'keys' => [ $url_a, 'alpha' ], 'clicks' => 2, 'impressions' => 10, 'ctr' => 0.2, 'position' => 5 ],
		[ 'keys' => [ $url_a, 'new' ], 'clicks' => 1, 'impressions' => 4, 'ctr' => 0.25, 'position' => 8 ],
	],
	'previous_query_page_rows' => [
		[ 'keys' => [ $url_a, 'alpha' ], 'clicks' => 1, 'impressions' => 8, 'ctr' => 0.125, 'position' => 6 ],
		[ 'keys' => [ $url_b, 'gone' ], 'clicks' => 3, 'impressions' => 20, 'ctr' => 0.15, 'position' => 4 ],
	],
	'current_page_rows' => [
		[ 'keys' => [ $url_a ], 'clicks' => 3, 'impressions' => 14, 'ctr' => 0.2142, 'position' => 5.8 ],
	],
	'previous_page_rows' => [
		[ 'keys' => [ $url_a ], 'clicks' => 1, 'impressions' => 8, 'ctr' => 0.125, 'position' => 6 ],
		[ 'keys' => [ $url_b ], 'clicks' => 3, 'impressions' => 20, 'ctr' => 0.15, 'position' => 4 ],
	],
	'page_contexts' => [
		$url_a => [
			'post_id'      => 11,
			'post_title'   => 'Current page',
			'page_type'    => 'page',
			'canonical'    => $url_a,
			'in_sitemap'   => true,
			'word_count'   => 600,
		],
	],
];

$rows  = nexus_build_seo_cockpit_export_rows( $snapshot );
$index = [];
foreach ( $rows as $row ) {
	$index[ $row['row_scope'] . '|' . $row['page'] . '|' . $row['query'] ] = $row;
}

expect_same( 'five union rows', 5, count( $rows ) );
expect_same( 'export columns carry explicit scope', true, in_array( 'row_scope', nexus_get_seo_cockpit_export_columns(), true ) );
expect_same( 'comparison window is explicit', '2025-12-07', $index[ 'query_page|' . $url_a . '|alpha' ]['previous_start'] );
expect_same( 'matched query is both', 'both', $index[ 'query_page|' . $url_a . '|alpha' ]['period_presence'] );
expect_same( 'matched position delta', '-1,00', $index[ 'query_page|' . $url_a . '|alpha' ]['delta_position'] );
expect_same( 'new query is current only', 'current_only', $index[ 'query_page|' . $url_a . '|new' ]['period_presence'] );
expect_same( 'missing previous position stays blank', '', $index[ 'query_page|' . $url_a . '|new' ]['previous_position'] );
expect_same( 'lost query is exported', 'previous_only', $index[ 'query_page|' . $url_b . '|gone' ]['period_presence'] );
expect_same( 'lost current position stays blank', '', $index[ 'query_page|' . $url_b . '|gone' ]['position'] );
expect_same( 'lost impressions are negative', -20, $index[ 'query_page|' . $url_b . '|gone' ]['delta_impressions'] );
expect_same( 'page total has no query', '', $index[ 'page_total|' . $url_b . '|' ]['query'] );
expect_same( 'fallback context cached across scopes', 1, $fallback_calls );

echo "=== $passed assertions passed ===\n";
PHP
