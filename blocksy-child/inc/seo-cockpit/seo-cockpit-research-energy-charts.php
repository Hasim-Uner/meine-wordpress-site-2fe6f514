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
	$allowed  = array_keys( nexus_get_seo_cockpit_energy_charts_queries() );
	$endpoint = sanitize_key( (string) $endpoint );

	if ( ! in_array( $endpoint, $allowed, true ) ) {
		return new WP_Error( 'nexus_energy_charts_endpoint', 'Nicht freigegebener Energy-Charts-Endpunkt.' );
	}

	$query     = is_array( $query ) ? $query : [];
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
 * Return a display/search string for one series descriptor.
 *
 * @param string               $id     Stable series id.
 * @param array<string, mixed> $series Series descriptor.
 * @return string
 */
function nexus_get_seo_cockpit_energy_charts_series_search_text( $id, $series ) {
	$parts = [
		(string) $id,
		(string) ( $series['name'] ?? '' ),
		(string) ( $series['label'] ?? '' ),
		(string) ( $series['title'] ?? '' ),
		(string) ( $series['description'] ?? '' ),
	];

	return strtolower( implode( ' ', array_filter( array_map( 'trim', $parts ) ) ) );
}

/**
 * Return whether a series descriptor represents a planned/target series.
 *
 * @param string               $id     Stable series id.
 * @param array<string, mixed> $series Series descriptor.
 * @return bool
 */
function nexus_is_seo_cockpit_energy_charts_planned_series( $id, $series ) {
	$text = nexus_get_seo_cockpit_energy_charts_series_search_text( $id, $series );

	foreach ( [ 'planned', 'plan', 'target', 'eeg', 'ziel' ] as $needle ) {
		if ( false !== strpos( $text, $needle ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Return the installed-solar series used as the actual PV stock.
 *
 * Energy-Charts publishes Solar DC, Solar AC and future EEG target series in
 * the same response. DC is preferred because it is the commonly cited module
 * capacity. AC is a fallback. Planned series are never treated as installed.
 *
 * @param array<string, mixed>|WP_Error $response Installed-power response.
 * @return string
 */
function nexus_get_seo_cockpit_energy_charts_installed_solar_series_id( $response ) {
	$map = nexus_get_seo_cockpit_energy_charts_series_map( $response );

	foreach ( [ 'solar_dc', 'photovoltaics_dc', 'photovoltaic_dc', 'pv_dc' ] as $candidate ) {
		if ( isset( $map[ $candidate ] ) && ! nexus_is_seo_cockpit_energy_charts_planned_series( $candidate, $map[ $candidate ] ) ) {
			return $candidate;
		}
	}

	foreach ( $map as $id => $series ) {
		$text = nexus_get_seo_cockpit_energy_charts_series_search_text( $id, $series );
		if ( false !== strpos( $text, 'solar' ) && false !== strpos( $text, 'dc' ) && ! nexus_is_seo_cockpit_energy_charts_planned_series( $id, $series ) ) {
			return $id;
		}
	}

	if ( isset( $map['solar'] ) && ! nexus_is_seo_cockpit_energy_charts_planned_series( 'solar', $map['solar'] ) ) {
		return 'solar';
	}

	foreach ( $map as $id => $series ) {
		$text = nexus_get_seo_cockpit_energy_charts_series_search_text( $id, $series );
		if ( false !== strpos( $text, 'solar' ) && ! nexus_is_seo_cockpit_energy_charts_planned_series( $id, $series ) ) {
			return $id;
		}
	}

	return '';
}

/**
 * Keep backwards compatibility with the original helper while returning only
 * one real installed-PV series instead of summing AC/DC/target series.
 *
 * @param array<string, mixed>|WP_Error $response Installed-power response.
 * @return array<int, string>
 */
function nexus_get_seo_cockpit_energy_charts_solar_series_ids( $response ) {
	$id = nexus_get_seo_cockpit_energy_charts_installed_solar_series_id( $response );

	return '' !== $id ? [ $id ] : [];
}

/**
 * Return the planned solar target series, if one is explicitly described.
 *
 * @param array<string, mixed>|WP_Error $response Installed-power response.
 * @return string
 */
function nexus_get_seo_cockpit_energy_charts_solar_target_series_id( $response ) {
	$map = nexus_get_seo_cockpit_energy_charts_series_map( $response );

	foreach ( $map as $id => $series ) {
		$text = nexus_get_seo_cockpit_energy_charts_series_search_text( $id, $series );
		if ( false !== strpos( $text, 'solar' ) && nexus_is_seo_cockpit_energy_charts_planned_series( $id, $series ) ) {
			return $id;
		}
	}

	return '';
}

/**
 * Return a human-readable series name.
 *
 * @param array<string, mixed>|WP_Error $response Provider response.
 * @param string                        $series_id Stable series id.
 * @return string
 */
function nexus_get_seo_cockpit_energy_charts_series_name( $response, $series_id ) {
	$map = nexus_get_seo_cockpit_energy_charts_series_map( $response );
	if ( isset( $map[ $series_id ]['name'] ) && '' !== trim( (string) $map[ $series_id ]['name'] ) ) {
		return sanitize_text_field( (string) $map[ $series_id ]['name'] );
	}

	return '' !== $series_id ? ucwords( str_replace( '_', ' ', $series_id ) ) : '';
}

/**
 * Return chronological summed values for selected series ids.
 *
 * @param array<string, mixed>|WP_Error $response Provider response.
 * @param array<int, string>            $series_ids Series ids to sum.
 * @param int|null                      $max_year Optional maximum reporting year.
 * @return array<int, array{timestamp:string,value:float}>
 */
function nexus_get_seo_cockpit_energy_charts_summed_rows( $response, $series_ids, $max_year = null ) {
	if ( is_wp_error( $response ) || ! is_array( $response ) || empty( $series_ids ) ) {
		return [];
	}

	$rows = [];
	foreach ( (array) ( $response['data'] ?? [] ) as $row ) {
		if ( ! is_array( $row ) || ! is_array( $row['values'] ?? null ) ) {
			continue;
		}

		$timestamp = sanitize_text_field( (string) ( $row['timestamp'] ?? '' ) );
		$year      = absint( substr( $timestamp, 0, 4 ) );
		if ( null !== $max_year && $year > (int) $max_year ) {
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
				'timestamp' => $timestamp,
				'value'     => $sum,
			];
		}
	}

	return $rows;
}

/**
 * Pick the most likely solar-share series from a daily-average response.
 *
 * The endpoint may expose only one percentage series and its stable id can
 * differ from the generic intraday solar endpoint. Descriptor/unit fallbacks
 * therefore come before a final first-numeric-series fallback.
 *
 * @param array<string, mixed>|WP_Error $response Provider response.
 * @return string
 */
function nexus_get_seo_cockpit_energy_charts_solar_share_series_id( $response ) {
	$map = nexus_get_seo_cockpit_energy_charts_series_map( $response );
	foreach ( [ 'solar_share_of_load', 'solar_share', 'solar_share_daily_avg', 'share' ] as $candidate ) {
		if ( isset( $map[ $candidate ] ) ) {
			return $candidate;
		}
	}

	foreach ( $map as $id => $series ) {
		$text = nexus_get_seo_cockpit_energy_charts_series_search_text( $id, $series );
		if ( false !== strpos( $text, 'solar' ) && false !== strpos( $text, 'share' ) ) {
			return $id;
		}
	}

	if ( 1 === count( $map ) ) {
		return (string) array_key_first( $map );
	}

	foreach ( $map as $id => $series ) {
		$unit = (string) ( $series['unit'] ?? '' );
		if ( '%' === trim( $unit ) ) {
			return $id;
		}
	}

	foreach ( (array) ( is_array( $response ) ? ( $response['data'] ?? [] ) : [] ) as $row ) {
		if ( ! is_array( $row ) || ! is_array( $row['values'] ?? null ) ) {
			continue;
		}

		$numeric_ids = [];
		foreach ( $row['values'] as $id => $value ) {
			if ( is_numeric( $value ) ) {
				$numeric_ids[] = sanitize_key( (string) $id );
			}
		}
		if ( 1 === count( $numeric_ids ) ) {
			return $numeric_ids[0];
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

	$current_year = (int) wp_date( 'Y' );
	$solar_ids    = nexus_get_seo_cockpit_energy_charts_solar_series_ids( $installed );
	$solar_rows   = nexus_get_seo_cockpit_energy_charts_summed_rows( $installed, $solar_ids, $current_year );
	$latest       = ! empty( $solar_rows ) ? $solar_rows[ count( $solar_rows ) - 1 ] : null;
	$previous     = count( $solar_rows ) > 1 ? $solar_rows[ count( $solar_rows ) - 2 ] : null;
	$solar_id     = ! empty( $solar_ids ) ? $solar_ids[0] : '';
	$solar_unit   = '' !== $solar_id ? nexus_get_seo_cockpit_energy_charts_series_unit( $installed, $solar_id ) : '';
	$solar_name   = '' !== $solar_id ? nexus_get_seo_cockpit_energy_charts_series_name( $installed, $solar_id ) : '';
	$solar_delta  = null;
	$solar_growth_pct = null;

	if ( is_array( $latest ) && is_array( $previous ) ) {
		$solar_delta = (float) $latest['value'] - (float) $previous['value'];
		if ( 0.0 !== (float) $previous['value'] ) {
			$solar_growth_pct = ( $solar_delta / (float) $previous['value'] ) * 100;
		}
	}

	$target_id   = nexus_get_seo_cockpit_energy_charts_solar_target_series_id( $installed );
	$target_rows = '' !== $target_id ? nexus_get_seo_cockpit_energy_charts_summed_rows( $installed, [ $target_id ] ) : [];
	$target      = ! empty( $target_rows ) ? $target_rows[ count( $target_rows ) - 1 ] : null;
	$target_unit = '' !== $target_id ? nexus_get_seo_cockpit_energy_charts_series_unit( $installed, $target_id ) : '';

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

	if ( ! is_wp_error( $installed ) && ( '' === $solar_id || ! is_array( $latest ) ) ) {
		$errors['installed_power'] = 'Die aktuelle Solar-Ist-Serie konnte nicht eindeutig aus der Energy-Charts-Antwort gelesen werden.';
	}
	if ( ! is_wp_error( $solar_avg ) && ( '' === $share_id || empty( $share_values ) ) ) {
		$errors['solar_share'] = 'Die tägliche Solaranteil-Serie konnte nicht eindeutig aus der Energy-Charts-Antwort gelesen werden.';
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
			'value'        => is_array( $latest ) ? (float) $latest['value'] : null,
			'unit'         => $solar_unit,
			'period'       => is_array( $latest ) ? (string) $latest['timestamp'] : '',
			'delta'        => $solar_delta,
			'growth_pct'   => $solar_growth_pct,
			'series_id'    => $solar_id,
			'series_label' => $solar_name,
		],
		'solar_target' => [
			'value'      => is_array( $target ) ? (float) $target['value'] : null,
			'unit'       => $target_unit,
			'period'     => is_array( $target ) ? (string) $target['timestamp'] : '',
			'series_id'  => $target_id,
		],
		'solar_share_30d' => [
			'value'     => $share_window['current'],
			'previous'  => $share_window['previous'],
			'unit'      => '%',
			'series_id' => $share_id,
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
