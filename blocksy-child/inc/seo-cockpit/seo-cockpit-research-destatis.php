<?php
/**
 * SEO Cockpit Destatis GENESIS provider.
 *
 * Pulls a deliberately small building-stock dataset from the official
 * GENESIS-Online REST/JSON API. The provider stays admin/background-only and
 * keeps the API token outside the repository.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the effective GENESIS API token.
 *
 * @return string
 */
function nexus_get_seo_cockpit_destatis_api_token() {
	if ( defined( 'NEXUS_DESTATIS_API_TOKEN' ) && NEXUS_DESTATIS_API_TOKEN ) {
		return trim( (string) NEXUS_DESTATIS_API_TOKEN );
	}

	$settings = function_exists( 'nexus_get_seo_cockpit_research_settings' )
		? nexus_get_seo_cockpit_research_settings()
		: [];

	return trim( (string) ( $settings['destatis_api_token'] ?? '' ) );
}

/**
 * Determine whether the Destatis token comes from runtime config.
 *
 * @return bool
 */
function nexus_seo_cockpit_destatis_uses_constant() {
	return defined( 'NEXUS_DESTATIS_API_TOKEN' ) && (bool) NEXUS_DESTATIS_API_TOKEN;
}

/**
 * Return the official GENESIS data/table endpoint.
 *
 * @return string
 */
function nexus_get_seo_cockpit_destatis_endpoint() {
	return 'https://genesis.destatis.de/genesisWS/rest/2020/data/table';
}

/**
 * Return the small allowlist of tables used by Research Intelligence.
 *
 * @return array<string, string>
 */
function nexus_get_seo_cockpit_destatis_tables() {
	return [
		'de_buildings' => '31231-0005',
		'state_buildings' => '31231-0014',
	];
}

/**
 * Return the rolling year window used for slow-moving building-stock data.
 *
 * @return array{start: int, end: int}
 */
function nexus_get_seo_cockpit_destatis_year_window() {
	$end = (int) gmdate( 'Y' );

	return [
		'start' => max( 1995, $end - 2 ),
		'end'   => $end,
	];
}

/**
 * Build the cache key for one GENESIS table request.
 *
 * @param string $table_code GENESIS table code.
 * @return string
 */
function nexus_get_seo_cockpit_destatis_cache_key( $table_code ) {
	$window = nexus_get_seo_cockpit_destatis_year_window();

	return function_exists( 'nexus_get_seo_cockpit_cache_key' )
		? nexus_get_seo_cockpit_cache_key( 'destatis', [ $table_code, $window['start'], $window['end'] ] )
		: 'nexus_destatis_' . md5( $table_code . '|' . $window['start'] . '|' . $window['end'] );
}

/**
 * Fetch one allowlisted GENESIS table.
 *
 * GENESIS expects POST requests with application/x-www-form-urlencoded. When
 * an API token is used it is sent in the username header and password stays
 * empty, matching the official Destatis examples.
 *
 * @param string $table_code GENESIS table code.
 * @param bool   $force Whether to bypass the transient cache.
 * @return array<string, mixed>|WP_Error
 */
function nexus_get_seo_cockpit_destatis_table( $table_code, $force = false ) {
	$table_code = sanitize_text_field( (string) $table_code );
	$allowed    = array_values( nexus_get_seo_cockpit_destatis_tables() );

	if ( ! in_array( $table_code, $allowed, true ) ) {
		return new WP_Error( 'nexus_destatis_table', 'Diese GENESIS-Tabelle ist nicht für das Research Cockpit freigegeben.' );
	}

	$token = nexus_get_seo_cockpit_destatis_api_token();
	if ( '' === $token ) {
		return new WP_Error( 'nexus_destatis_missing_token', 'Destatis GENESIS ist noch nicht konfiguriert.' );
	}

	$cache_key = nexus_get_seo_cockpit_destatis_cache_key( $table_code );
	if ( ! $force ) {
		$cached = get_transient( $cache_key );
		if ( is_array( $cached ) ) {
			return $cached;
		}
	}

	$window   = nexus_get_seo_cockpit_destatis_year_window();
	$response = wp_remote_post(
		nexus_get_seo_cockpit_destatis_endpoint(),
		[
			'timeout' => 20,
			'headers' => [
				'Accept'       => 'application/json',
				'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
				'username'     => $token,
				'password'     => '',
			],
			'body'    => [
				'name'                 => $table_code,
				'area'                 => 'free',
				'structureinformation' => 'false',
				'compress'             => 'false',
				'transpose'            => 'false',
				'startyear'            => (string) $window['start'],
				'endyear'              => (string) $window['end'],
				'job'                  => 'false',
				'language'             => 'de',
			],
		]
	);

	if ( is_wp_error( $response ) ) {
		return new WP_Error( 'nexus_destatis_request', 'Destatis GENESIS konnte nicht erreicht werden: ' . $response->get_error_message() );
	}

	$status = (int) wp_remote_retrieve_response_code( $response );
	$body   = json_decode( (string) wp_remote_retrieve_body( $response ), true );
	$body   = is_array( $body ) ? $body : [];

	if ( $status < 200 || $status >= 300 ) {
		$message = sanitize_text_field( (string) ( $body['Status']['Content'] ?? $body['Content'] ?? 'Unbekannte API-Antwort.' ) );

		return new WP_Error(
			'nexus_destatis_http',
			sprintf( 'Destatis GENESIS antwortet mit HTTP %1$d: %2$s', $status, $message )
		);
	}

	$content = (string) ( $body['Object']['Content'] ?? '' );
	if ( '' === trim( $content ) ) {
		$code    = (string) ( $body['Status']['Code'] ?? '' );
		$message = sanitize_text_field( (string) ( $body['Status']['Content'] ?? 'Die Tabelle enthält keine auswertbaren Daten.' ) );

		return new WP_Error(
			'nexus_destatis_empty',
			'' !== $code ? sprintf( 'GENESIS Status %1$s: %2$s', $code, $message ) : $message
		);
	}

	set_transient( $cache_key, $body, DAY_IN_SECONDS );

	return $body;
}

/**
 * Delete only Destatis research transients.
 *
 * @return void
 */
function nexus_delete_seo_cockpit_destatis_cache() {
	foreach ( nexus_get_seo_cockpit_destatis_tables() as $table_code ) {
		delete_transient( nexus_get_seo_cockpit_destatis_cache_key( $table_code ) );
	}
}

/**
 * Convert GENESIS table content into semicolon-delimited rows.
 *
 * @param string $content Raw Object.Content string.
 * @return array<int, array<int, string>>
 */
function nexus_parse_seo_cockpit_destatis_rows( $content ) {
	$lines = preg_split( '/\r\n|\n|\r/', (string) $content );
	$rows  = [];

	foreach ( (array) $lines as $line ) {
		$line = trim( (string) $line );
		if ( '' === $line ) {
			continue;
		}

		$cells = str_getcsv( $line, ';', '"', '\\' );
		$cells = array_map(
			static function ( $cell ) {
				return trim( wp_strip_all_tags( (string) $cell ) );
			},
			(array) $cells
		);

		if ( ! empty( $cells ) ) {
			$rows[] = $cells;
		}
	}

	return $rows;
}

/**
 * Parse one German-formatted GENESIS number.
 *
 * @param string $value Raw cell value.
 * @return float|null
 */
function nexus_parse_seo_cockpit_destatis_number( $value ) {
	$value = trim( str_replace( [ "\xC2\xA0", ' ' ], '', (string) $value ) );
	if ( '' === $value || in_array( $value, [ '-', '.', '...', '/', 'x' ], true ) ) {
		return null;
	}

	$value = str_replace( '.', '', $value );
	$value = str_replace( ',', '.', $value );
	$value = preg_replace( '/[^0-9.\-]/', '', (string) $value );

	return is_numeric( $value ) ? (float) $value : null;
}

/**
 * Return numeric cells after a given cell index.
 *
 * @param array<int, string> $row Row cells.
 * @param int                $after_index Ignore cells through this index.
 * @return array<int, float>
 */
function nexus_get_seo_cockpit_destatis_numeric_tail( $row, $after_index ) {
	$values = [];
	foreach ( (array) $row as $index => $cell ) {
		if ( $index <= $after_index ) {
			continue;
		}

		$number = nexus_parse_seo_cockpit_destatis_number( $cell );
		if ( null !== $number ) {
			$values[] = $number;
		}
	}

	return count( $values ) >= 15 ? array_slice( $values, -15 ) : [];
}

/**
 * Extract the latest Germany row from table 31231-0005.
 *
 * @param array<int, array<int, string>> $rows Parsed rows.
 * @return array{year: int, values: array<int, float>}|null
 */
function nexus_get_seo_cockpit_destatis_latest_de_row( $rows ) {
	$latest = null;

	foreach ( $rows as $row ) {
		foreach ( $row as $index => $cell ) {
			if ( 1 !== preg_match( '/^31\.12\.(\d{4})$/', trim( (string) $cell ), $matches ) ) {
				continue;
			}

			$values = nexus_get_seo_cockpit_destatis_numeric_tail( $row, (int) $index );
			$year   = (int) $matches[1];
			if ( count( $values ) < 15 ) {
				continue;
			}

			if ( null === $latest || $year > $latest['year'] ) {
				$latest = [
					'year'   => $year,
					'values' => $values,
				];
			}
		}
	}

	return $latest;
}

/**
 * Extract the latest Niedersachsen row from table 31231-0014.
 *
 * @param array<int, array<int, string>> $rows Parsed rows.
 * @return array{year: int, values: array<int, float>}|null
 */
function nexus_get_seo_cockpit_destatis_latest_ni_row( $rows ) {
	$latest       = null;
	$current_year = 0;

	foreach ( $rows as $row ) {
		foreach ( $row as $cell ) {
			if ( 1 === preg_match( '/^31\.12\.(\d{4})$/', trim( (string) $cell ), $matches ) ) {
				$current_year = (int) $matches[1];
				break;
			}
		}

		$ni_index = array_search( 'Niedersachsen', $row, true );
		if ( false === $ni_index ) {
			continue;
		}

		$values = nexus_get_seo_cockpit_destatis_numeric_tail( $row, (int) $ni_index );
		if ( count( $values ) < 15 ) {
			continue;
		}

		$year = $current_year;
		if ( $year < 1995 ) {
			foreach ( $row as $cell ) {
				if ( 1 === preg_match( '/(\d{4})/', (string) $cell, $matches ) ) {
					$year = (int) $matches[1];
					break;
				}
			}
		}

		if ( $year < 1995 ) {
			continue;
		}

		if ( null === $latest || $year > $latest['year'] ) {
			$latest = [
				'year'   => $year,
				'values' => $values,
			];
		}
	}

	return $latest;
}

/**
 * Convert a 15-value building-stock row into the metrics used by the UI.
 *
 * GENESIS table 31231-0005 / -0014 groups each housing class into three
 * values: buildings, dwellings and floor area. The final group is "Insgesamt".
 *
 * @param array{year: int, values: array<int, float>}|null $row Extracted row.
 * @return array<string, float|int|null>
 */
function nexus_get_seo_cockpit_destatis_building_metrics( $row ) {
	if ( ! is_array( $row ) || count( (array) ( $row['values'] ?? [] ) ) < 15 ) {
		return [
			'year'          => null,
			'total'         => null,
			'one_two'       => null,
			'one_two_share' => null,
		];
	}

	$values  = array_values( $row['values'] );
	$one_two = (float) $values[0] + (float) $values[3];
	$total   = (float) $values[12];

	return [
		'year'          => (int) $row['year'],
		'total'         => $total,
		'one_two'       => $one_two,
		'one_two_share' => $total > 0 ? ( $one_two / $total ) * 100 : null,
	];
}

/**
 * Build the Destatis building-stock summary.
 *
 * @param bool $force Whether to bypass provider caches.
 * @return array<string, mixed>
 */
function nexus_get_seo_cockpit_destatis_summary( $force = false ) {
	$tables = nexus_get_seo_cockpit_destatis_tables();
	$de     = nexus_get_seo_cockpit_destatis_table( $tables['de_buildings'], $force );
	$states = nexus_get_seo_cockpit_destatis_table( $tables['state_buildings'], $force );
	$errors = [];

	if ( is_wp_error( $de ) ) {
		$errors['Deutschland'] = $de->get_error_message();
	}
	if ( is_wp_error( $states ) ) {
		$errors['Bundesländer'] = $states->get_error_message();
	}

	$de_row = is_wp_error( $de )
		? null
		: nexus_get_seo_cockpit_destatis_latest_de_row(
			nexus_parse_seo_cockpit_destatis_rows( (string) ( $de['Object']['Content'] ?? '' ) )
		);
	$ni_row = is_wp_error( $states )
		? null
		: nexus_get_seo_cockpit_destatis_latest_ni_row(
			nexus_parse_seo_cockpit_destatis_rows( (string) ( $states['Object']['Content'] ?? '' ) )
		);

	$de_metrics = nexus_get_seo_cockpit_destatis_building_metrics( $de_row );
	$ni_metrics = nexus_get_seo_cockpit_destatis_building_metrics( $ni_row );

	if ( ! is_wp_error( $de ) && null === $de_metrics['total'] ) {
		$errors['Deutschland'] = 'Die GENESIS-Tabelle wurde geladen, aber das aktuelle Tabellenformat konnte nicht sicher ausgewertet werden.';
	}
	if ( ! is_wp_error( $states ) && null === $ni_metrics['total'] ) {
		$errors['Niedersachsen'] = 'Die Bundesländer-Tabelle wurde geladen, aber Niedersachsen konnte im aktuellen Tabellenformat nicht sicher ausgewertet werden.';
	}

	$copyright = '';
	foreach ( [ $de, $states ] as $response ) {
		if ( is_array( $response ) && '' !== trim( (string) ( $response['Copyright'] ?? '' ) ) ) {
			$copyright = trim( (string) $response['Copyright'] );
			break;
		}
	}

	return [
		'is_available' => null !== $de_metrics['total'] || null !== $ni_metrics['total'],
		'de'           => $de_metrics,
		'ni'           => $ni_metrics,
		'errors'       => $errors,
		'copyright'    => $copyright,
		'tables'       => $tables,
	];
}

/**
 * Save or clear the GENESIS API token.
 *
 * @return void
 */
function nexus_handle_seo_cockpit_destatis_save() {
	if ( ! nexus_current_user_can_manage_seo_cockpit() ) {
		wp_die( 'Nicht erlaubt.' );
	}

	check_admin_referer( 'nexus_seo_cockpit_destatis_save' );

	if ( nexus_seo_cockpit_destatis_uses_constant() ) {
		wp_safe_redirect( admin_url( 'admin.php?page=' . nexus_get_seo_cockpit_research_slug() . '&research_notice=destatis_constant' ) );
		exit;
	}

	$settings = nexus_get_seo_cockpit_research_settings();
	if ( ! empty( $_POST['clear_destatis_api_token'] ) ) {
		$settings['destatis_api_token'] = '';
	} else {
		$new_token = isset( $_POST['destatis_api_token'] ) ? sanitize_text_field( (string) wp_unslash( $_POST['destatis_api_token'] ) ) : '';
		if ( '' !== $new_token ) {
			$settings['destatis_api_token'] = $new_token;
		}
	}

	update_option( nexus_get_seo_cockpit_research_option_name(), $settings, false );
	nexus_delete_seo_cockpit_destatis_cache();

	wp_safe_redirect( admin_url( 'admin.php?page=' . nexus_get_seo_cockpit_research_slug() . '&research_notice=destatis_saved' ) );
	exit;
}
add_action( 'admin_post_nexus_seo_cockpit_destatis_save', 'nexus_handle_seo_cockpit_destatis_save' );

/**
 * Clear Destatis transients so the next page render pulls fresh source data.
 *
 * @return void
 */
function nexus_handle_seo_cockpit_destatis_refresh() {
	if ( ! nexus_current_user_can_manage_seo_cockpit() ) {
		wp_die( 'Nicht erlaubt.' );
	}

	check_admin_referer( 'nexus_seo_cockpit_destatis_refresh' );
	nexus_delete_seo_cockpit_destatis_cache();

	wp_safe_redirect( admin_url( 'admin.php?page=' . nexus_get_seo_cockpit_research_slug() . '&research_notice=destatis_refresh' ) );
	exit;
}
add_action( 'admin_post_nexus_seo_cockpit_destatis_refresh', 'nexus_handle_seo_cockpit_destatis_refresh' );
