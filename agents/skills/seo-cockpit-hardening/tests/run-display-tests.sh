#!/usr/bin/env bash
# Regression tests for SEO Cockpit display texts: DECAY reason and query mover position.

set -uo pipefail

TESTS_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
REPO_ROOT="$(cd "$TESTS_DIR/../../../.." && pwd)"
INSIGHTS_FILE="$REPO_ROOT/blocksy-child/inc/seo-cockpit/seo-cockpit-insights.php"

INSIGHTS_FILE="$INSIGHTS_FILE" php <<'PHP'
<?php
define( 'ABSPATH', '/' );

$passed = 0;

function absint( $value ) {
	return abs( (int) $value );
}

function number_format_i18n( $number, $decimals = 0 ) {
	return number_format( (float) $number, (int) $decimals, ',', '.' );
}

function expect_same( $label, $expected, $actual ) {
	global $passed;

	if ( $expected !== $actual ) {
		fwrite( STDERR, "FAIL: $label\n  erwartet: " . var_export( $expected, true ) . "\n  erhalten: " . var_export( $actual, true ) . "\n" );
		exit( 1 );
	}

	$passed++;
}

require getenv( 'INSIGHTS_FILE' );

$prefix = 'Gegenüber dem Vergleichsfenster deutlich gefallen: ';

// Server-Side Tracking, 28 Tage bis 22.09.2026: 0 → 0 Klicks, 220 → 114 Impressionen.
expect_same(
	'Nur Impressionen gefallen, Klicks 0 → 0',
	$prefix . 'Impressionen 220 → 114 (-48,2 %).',
	nexus_get_seo_cockpit_decay_reason( 0, 0, 114, 220 )
);

// B2B Solar Leads: 1 → 0 Klicks liegt unter der Klickschwelle und zaehlt nicht.
expect_same(
	'Ein verlorener Klick loest nichts aus, Impressionen schon',
	$prefix . 'Impressionen 654 → 136 (-79,2 %).',
	nexus_get_seo_cockpit_decay_reason( 0, 1, 136, 654 )
);

expect_same(
	'Beide Kennzahlen gefallen',
	$prefix . 'Klicks 10 → 3 (-70,0 %); Impressionen 1.200 → 400 (-66,7 %).',
	nexus_get_seo_cockpit_decay_reason( 3, 10, 400, 1200 )
);

expect_same( 'Genau 70 % bleibt ohne Befund', '', nexus_get_seo_cockpit_decay_reason( 7, 10, 70, 100 ) );
expect_same( 'Unter den Mindestwerten bleibt ohne Befund', '', nexus_get_seo_cockpit_decay_reason( 0, 4, 0, 49 ) );

expect_same(
	'Mover ohne aktuelle Impressionen zeigt keine Position 0',
	'aktuell ohne Impressionen',
	nexus_get_seo_cockpit_mover_position_label( [ 'impressions' => 0.0, 'position' => 0.0 ] )
);
expect_same(
	'Mover mit Impressionen zeigt die Position',
	'Pos. 6,9',
	nexus_get_seo_cockpit_mover_position_label( [ 'impressions' => 340.0, 'position' => 6.86 ] )
);
expect_same( 'Mover ohne Felder faellt sicher zurueck', 'aktuell ohne Impressionen', nexus_get_seo_cockpit_mover_position_label( [] ) );

echo "OK seo-cockpit display: $passed Pruefungen\n";
PHP
