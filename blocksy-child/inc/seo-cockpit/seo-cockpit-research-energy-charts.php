<?php
/**
 * SEO Cockpit Research Intelligence: Energy-Charts provider.
 *
 * Fetches a small, cached set of public Fraunhofer ISE Energy-Charts v2
 * datasets for the admin-only Research Intelligence layer. No frontend
 * requests and no API key are required for this public provider.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the Energy-Charts v2 API base URL.
 *
 * @return string
 */
function nexus_get_seo_cockpit_energy_charts_api_base_url() {
	return 'https://api.energy-charts.info/v2';
}

/**
 * Return the exact provider queries used by Research Intelligence.
 *
 * Keeping this small makes rate usage predictable and cache invalidation exact.
 *
 * @return array<string, array<string, mixed>>
 */
function nexus_get_seo_cockpit_energy_charts_queries() {
	return [
		'installed_power' => [
			'country'                   => 'de',
			'time_step'                 => 'yearly',
			'installation_decommission' => 'false',
		],
		'solar_share_daily_avg' => [
			'country' => 'de',
			'year'    => -1,
		],
		'price_current' => [
			'bzn' => 'DE-LU',
		],
	];
}

/**
 * Build one provider-specific transient key.
 *
 * @param string               $endpoint Endpoint name without /v2/.
 * @param array<string, mixed> $query    Query arguments.
 * @return string
 */
function nexus_get_seo_cockpit_energy_charts_cache_key( $endpoint, $query = [] ) {
	ksort( $query );

	return 'nexus_energy_charts_' . md5( sanitize_key( (string) $endpoint ) . '|' . wp_json_encode( $query ) );
}

/**
 * Fetch one Energy-Charts v2 response.
 *
 * @param string               $endpoint Endpoint name from the provider allowlist.
 * @param array<string, mixed> $query    Query arguments.
 * @param int                  $ttl      Cache lifetime in seconds.
 * @param bool                 $force    Skip transient cache.
 * @return array<string, mixed>|WP_Error
 */
function nexus_get_seo_cockpit_energy_charts_response( $endpoint, $query = [], $ttl = 21600, $force = false ) {
	$allowed = array_keys( nexus_get_seo_cockpit_energy_charts_queries() );
	$endpoint = sanitize_key( (string) $endpoint );

	if ( ! in_array( $endpoint, $allowed, true ) ) {
		return new WP_Error( 'nexus_energy_charts_endpoint', 'Nicht freigegebener Energy-Charts-Endpunkt.' );
	}

	$query = is_array( $query ) ? $query : [];
	$cache_key = nexus_get_seo_cockpit_energy_charts_cache_key( $endpoint, $query );

	if ( ! $force ) {
		$cached = get_transient( $cache_key );
		if ( is_array( $cached ) ) {
			return $cached;
		}
	}

	$url = trailingslashit( nexus_get_seo_cockpit_energy_charts_api_base_url() ) . $endpoint;
	if ( ! empty( $query ) ) {
		$url = add_query_arg( $query, $url );
	}

	$response = wp_remote_get(
		$url,
		[
			'timeout' => 12,
			'headers' => [
				'Accept'     => 'application/json',
				'User-Agent' => 'hasimuener.de SEO Cockpit Research Intelligence',
			],
		]
	);

	if ( is_wp_error( $response ) ) {
		return new WP_Error( 'nexus_energy_charts_request', 'Energy-Charts konnte nicht erreicht werden: ' . $response->get_error_message() );
	}

	$status = (int) wp_remote_retrieve_response_code( $response );
	$body   = json_decode( (string) wp_remote_retrieve_body( $response ), true );
	$body   = is_array( $body ) ? $body : [];

	if ( $status < 200 || $status >= 300 ) {
		$message = sanitize_text_field( (string) ( $body['detail'] ?? 'Unbekannte API-Antwort.' ) );
		return new WP_Error(
			'nexus_energy_charts_http',
			sprintf( 'Energy-Charts antwortet mit HTTP %1$d: %2$s', $status, $message )
		);
	}

	if ( empty( $body['data'] ) || ! is_array( $body['data'] ) || empty( $body['series'] ) || ! is_array( $body['series'] ) ) {
		return new WP_Error( 'nexus_energy_charts_empty', 'Energy-Charts liefert für diese Abfrage aktuell keine verwertbaren Daten.' );
	}

	set_transient( $cache_key, $body, max( 300, absint( $ttl ) ) );

	return $body;
}

/**
 * Clear the exact Energy-Charts transients used by this provider.
 *
 * @return void
 */
function nexus_delete_seo_cockpit_energy_charts_cache() {
	foreach ( nexus_get_seo_cockpit_energy_charts_queries() as $endpoint => $query ) {
		delete_transient( nexus_get_seo_cockpit_energy_charts_cache_key( $endpoint, $query ) );
	}
}

/**
 * Return series descriptors keyed by stable series id.
 *
 * @param array<string, mixed>|WP_Error $response Provider response.
 * @return array<string, array<string, mixed>>
 */
function nexus_get_seo_cockpit_energy_charts_series_map( $response ) {
	if ( is_wp_error( $response ) || ! is_array( $response ) ) {
		return [];
	}

	$map = [];
	foreach ( (array) ( $response['series'] ?? [] ) as $series ) {
		if ( ! is_array( $series ) ) {
			continue;
		}
		$id = sanitize_key( (string) ( $series['id'] ?? '' ) );
		if ( '' !== $id ) {
			$map[ $id ] = $series;
		}
	}

	return $map;
}

/**
 * Return a unit for one series, falling back to the response-level unit.
 *
 * @param array<string, mixed>|WP_Error $response Provider response.
 * @param string                        $series_id Stable series id.
 * @return string
 */
function nexus_get_seo_cockpit_energy_charts_series_unit( $response, $series_id ) {
	if ( is_wp_error( $response ) || ! is_array( $response ) ) {
		return '';
	}

	$map = nexus_get_seo_cockpit_energy_charts_series_map( $response );
	if ( isset( $map[ $series_id ]['unit'] ) && '' !== (string) $map[ $series_id ]['unit'] ) {
		return sanitize_text_field( (string) $map[ $series_id ]['unit'] );
	}

	return sanitize_text_field( (string) ( $response['unit'] ?? '' ) );
}

/**
 * Pick installed-solar series without double counting aggregate and AC/DC data.
 *
 * @param array<string, mixed>|WP_Error $response Installed-power response.
 * @return array<int, string>
 */
function nexus_get_seo_cockpit_energy_charts_solar_series_ids( $response ) {
	$map = nexus_get_seo_cockpit_energy_charts_series_map( $response );
	if ( isset( $map['solar'] ) ) {
		return [ 'solar' ];
	}

	$ids = [];
	foreach ( array_keys( $map ) as $id ) {
		if ( 0 === strpos( $id, 'solar_' ) ) {
			$ids[] = $id;
		}
	}

	return $ids;
}

/**
 * Return chronological summed values for selected series ids.
 *
 * @param array<string, mixed>|WP_Error $response Provider response.
 * @param array<int, string>            $series_ids Series ids to sum.
 * @return array<int, array{timestamp:string,value:float}>
 */
function nexus_get_seo_cockpit_energy_charts_summed_rows( $response, $series_ids ) {
	if ( is_wp_error( $response ) || ! is_array( $response ) || empty( $series_ids ) ) {
		return [];
	}

	$rows = [];
	foreach ( (array) ( $response['data'] ?? [] ) as $row ) {
		if ( ! is_array( $row ) || ! is_array( $row['values'] ?? null ) ) {
			continue;
		}

		$sum   = 0.0;
		$found = false;
		foreach ( $series_ids as $series_id ) {
			$value = $row['values'][ $series_id ] ?? null;
			if ( is_numeric( $value ) ) {
				$sum  += (float) $value;
				$found = true;
			}
		}

		if ( $found ) {
			$rows[] = [
				'timestamp' => sanitize_text_field( (string) ( $row['timestamp'] ?? '' ) ),
				'value'     => $sum,
			];
		}
	}

	return $rows;
}

/**
 * Pick the most likely solar-share series from a daily-average response.
 *
 * @param array<string, mixed>|WP_Error $response Provider response.
 * @return string
 */
function nexus_get_seo_cockpit_energy_charts_solar_share_series_id( $response ) {
	$map = nexus_get_seo_cockpit_energy_charts_series_map( $response );
	foreach ( [ 'solar_share_of_load', 'solar_share', 'solar' ] as $candidate ) {
		if ( isset( $map[ $candidate ] ) ) {
			return $candidate;
		}
	}

	foreach ( array_keys( $map ) as $id ) {
		if ( false !== strpos( $id, 'solar' ) ) {
			return $id;
		}
	}

	return '';
}

/**
 * Return numeric values from one series in chronological order.
 *
 * @param array<string, mixed>|WP_Error $response Provider response.
 * @param string                        $series_id Stable series id.
 * @return array<int, float>
 */
function nexus_get_seo_cockpit_energy_charts_series_values( $response, $series_id ) {
	if ( is_wp_error( $response ) || ! is_array( $response ) || '' === $series_id ) {
		return [];
	}

	$values = [];
	foreach ( (array) ( $response['data'] ?? [] ) as $row ) {
		$value = is_array( $row ) && is_array( $row['values'] ?? null ) ? ( $row['values'][ $series_id ] ?? null ) : null;
		if ( is_numeric( $value ) ) {
			$values[] = (float) $value;
		}
	}

	return $values;
}

/**
 * Average the last N values and optionally the N values before them.
 *
 * @param array<int, float> $values Numeric values.
 * @param int               $window Window size.
 * @return array{current:float|null,previous:float|null}
 */
function nexus_get_seo_cockpit_energy_charts_window_averages( $values, $window = 30 ) {
	$window = max( 1, absint( $window ) );
	$count  = count( $values );

	if ( 0 === $count ) {
		return [ 'current' => null, 'previous' => null ];
	}

	$current_slice = array_slice( $values, -1 * min( $window, $count ) );
	$current       = array_sum( $current_slice ) / count( $current_slice );
	$previous      = null;

	if ( $count > $window ) {
		$previous_slice = array_slice( $values, -2 * $window, min( $window, $count - $window ) );
		if ( ! empty( $previous_slice ) ) {
			$previous = array_sum( $previous_slice ) / count( $previous_slice );
		}
	}

	return [
		'current'  => $current,
		'previous' => $previous,
	];
}

/**
 * Return the first numeric series/value from a single-record response.
 *
 * @param array<string, mixed>|WP_Error $response Provider response.
 * @return array{id:string,value:float|null,unit:string}
 */
function nexus_get_seo_cockpit_energy_charts_first_value( $response ) {
	$empty = [ 'id' => '', 'value' => null, 'unit' => '' ];
	if ( is_wp_error( $response ) || ! is_array( $response ) ) {
		return $empty;
	}

	foreach ( (array) ( $response['data'] ?? [] ) as $row ) {
		if ( ! is_array( $row ) || ! is_array( $row['values'] ?? null ) ) {
			continue;
		}
		foreach ( $row['values'] as $id => $value ) {
			$id = sanitize_key( (string) $id );
			if ( '' !== $id && is_numeric( $value ) ) {
				return [
					'id'    => $id,
					'value' => (float) $value,
					'unit'  => nexus_get_seo_cockpit_energy_charts_series_unit( $response, $id ),
				];
			}
		}
	}

	return $empty;
}

/**
 * Build the compact Energy-Charts summary consumed by the Research UI.
 *
 * @param bool $force Skip provider caches.
 * @return array<string, mixed>
 */
function nexus_get_seo_cockpit_energy_charts_summary( $force = false ) {
	$queries = nexus_get_seo_cockpit_energy_charts_queries();

	$installed = nexus_get_seo_cockpit_energy_charts_response( 'installed_power', $queries['installed_power'], 12 * HOUR_IN_SECONDS, $force );
	$solar_avg = nexus_get_seo_cockpit_energy_charts_response( 'solar_share_daily_avg', $queries['solar_share_daily_avg'], 6 * HOUR_IN_SECONDS, $force );
	$price     = nexus_get_seo_cockpit_energy_charts_response( 'price_current', $queries['price_current'], 30 * MINUTE_IN_SECONDS, $force );

	$solar_ids   = nexus_get_seo_cockpit_energy_charts_solar_series_ids( $installed );
	$solar_rows  = nexus_get_seo_cockpit_energy_charts_summed_rows( $installed, $solar_ids );
	$latest      = ! empty( $solar_rows ) ? $solar_rows[ count( $solar_rows ) - 1 ] : null;
	$previous    = count( $solar_rows ) > 1 ? $solar_rows[ count( $solar_rows ) - 2 ] : null;
	$solar_unit  = ! empty( $solar_ids ) ? nexus_get_seo_cockpit_energy_charts_series_unit( $installed, $solar_ids[0] ) : '';
	$solar_delta = null;
	$solar_growth_pct = null;

	if ( is_array( $latest ) && is_array( $previous ) ) {
		$solar_delta = (float) $latest['value'] - (float) $previous['value'];
		if ( 0.0 !== (float) $previous['value'] ) {
			$solar_growth_pct = ( $solar_delta / (float) $previous['value'] ) * 100;
		}
	}

	$share_id     = nexus_get_seo_cockpit_energy_charts_solar_share_series_id( $solar_avg );
	$share_values = nexus_get_seo_cockpit_energy_charts_series_values( $solar_avg, $share_id );
	$share_window = nexus_get_seo_cockpit_energy_charts_window_averages( $share_values, 30 );
	$price_value  = nexus_get_seo_cockpit_energy_charts_first_value( $price );

	$errors = [];
	foreach ( [ 'installed_power' => $installed, 'solar_share' => $solar_avg, 'price' => $price ] as $key => $response ) {
		if ( is_wp_error( $response ) ) {
			$errors[ $key ] = $response->get_error_message();
		}
	}

	$license = '';
	foreach ( [ $installed, $solar_avg, $price ] as $response ) {
		if ( ! is_wp_error( $response ) && is_array( $response ) && '' !== (string) ( $response['license'] ?? '' ) ) {
			$license = sanitize_text_field( (string) $response['license'] );
			if ( false !== stripos( $license, 'CC BY 4.0' ) ) {
				break;
			}
		}
	}

	return [
		'is_available' => count( $errors ) < 3,
		'errors'       => $errors,
		'generated_at' => ! is_wp_error( $installed ) ? sanitize_text_field( (string) ( $installed['generated_at'] ?? '' ) ) : '',
		'license'      => $license,
		'solar_installed' => [
			'value'      => is_array( $latest ) ? (float) $latest['value'] : null,
			'unit'       => $solar_unit,
			'period'     => is_array( $latest ) ? (string) $latest['timestamp'] : '',
			'delta'      => $solar_delta,
			'growth_pct' => $solar_growth_pct,
		],
		'solar_share_30d' => [
			'value'    => $share_window['current'],
			'previous' => $share_window['previous'],
			'unit'     => '%' ,
		],
		'price_current' => $price_value,
	];
}

/**
 * Clear Energy-Charts caches and return to the Research page.
 *
 * @return void
 */
function nexus_handle_seo_cockpit_energy_charts_refresh() {
	if ( ! function_exists( 'nexus_current_user_can_manage_seo_cockpit' ) || ! nexus_current_user_can_manage_seo_cockpit() ) {
		wp_die( 'Nicht erlaubt.' );
	}

	check_admin_referer( 'nexus_seo_cockpit_energy_charts_refresh' );
	nexus_delete_seo_cockpit_energy_charts_cache();

	$slug = function_exists( 'nexus_get_seo_cockpit_research_slug' ) ? nexus_get_seo_cockpit_research_slug() : 'nexus-seo-cockpit-research';
	wp_safe_redirect( admin_url( 'admin.php?page=' . $slug . '&research_notice=energy_refresh' ) );
	exit;
}
add_action( 'admin_post_nexus_seo_cockpit_energy_charts_refresh', 'nexus_handle_seo_cockpit_energy_charts_refresh' );
