<?php
/**
 * DataForSEO client and credential/runtime layer for SEO Cockpit.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** @return string */
function nexus_dataforseo_settings_option_name() {
	return 'nexus_dataforseo_settings_v1';
}

/** @return string */
function nexus_dataforseo_runtime_option_name() {
	return 'nexus_dataforseo_runtime_v1';
}

/**
 * Return persisted provider settings.
 *
 * Credentials may be stored here for local/admin convenience, but production
 * should prefer NEXUS_DATAFORSEO_LOGIN and NEXUS_DATAFORSEO_PASSWORD.
 *
 * @return array<string, string>
 */
function nexus_get_dataforseo_settings() {
	$settings = get_option( nexus_dataforseo_settings_option_name(), [] );
	$settings = is_array( $settings ) ? $settings : [];

	return wp_parse_args(
		$settings,
		[
			'api_login'               => '',
			'api_password'            => '',
			'location_name'           => 'Germany',
			'language_code'           => 'de',
			'local_location_name'     => 'Hanover,Lower Saxony,Germany',
			'local_business_name'     => 'Haşim Üner',
			'watch_keywords'          => '',
			'auto_refresh'            => '1',
			'ranked_limit'            => '100',
			'competitor_limit'        => '15',
			'keyword_overview_limit'  => '40',
			'monthly_auto_budget_usd' => '2.00',
		]
	);
}

/**
 * Persist provider settings without exposing the password back into the UI.
 *
 * @param array<string, string> $settings Settings.
 * @return void
 */
function nexus_update_dataforseo_settings( $settings ) {
	update_option( nexus_dataforseo_settings_option_name(), $settings, false );
}

/** @return bool */
function nexus_dataforseo_uses_constant_credentials() {
	return defined( 'NEXUS_DATAFORSEO_LOGIN' )
		&& defined( 'NEXUS_DATAFORSEO_PASSWORD' )
		&& '' !== trim( (string) NEXUS_DATAFORSEO_LOGIN )
		&& '' !== trim( (string) NEXUS_DATAFORSEO_PASSWORD );
}

/**
 * Return effective API credentials.
 *
 * @return array{login:string,password:string}
 */
function nexus_get_dataforseo_credentials() {
	if ( nexus_dataforseo_uses_constant_credentials() ) {
		return [
			'login'    => trim( (string) NEXUS_DATAFORSEO_LOGIN ),
			'password' => trim( (string) NEXUS_DATAFORSEO_PASSWORD ),
		];
	}

	$settings = nexus_get_dataforseo_settings();

	return [
		'login'    => trim( (string) ( $settings['api_login'] ?? '' ) ),
		'password' => trim( (string) ( $settings['api_password'] ?? '' ) ),
	];
}

/** @return bool */
function nexus_dataforseo_has_credentials() {
	$credentials = nexus_get_dataforseo_credentials();

	return '' !== $credentials['login'] && '' !== $credentials['password'];
}

/** @return string */
function nexus_dataforseo_target_domain() {
	$host = strtolower( (string) wp_parse_url( home_url( '/' ), PHP_URL_HOST ) );
	$host = preg_replace( '/^www\./i', '', $host );

	return is_string( $host ) ? $host : '';
}

/**
 * Return normalized provider configuration.
 *
 * @return array<string, mixed>
 */
function nexus_get_dataforseo_config() {
	$settings = nexus_get_dataforseo_settings();

	return [
		'target'                  => nexus_dataforseo_target_domain(),
		'location_name'           => trim( (string) ( $settings['location_name'] ?? 'Germany' ) ),
		'language_code'           => strtolower( trim( (string) ( $settings['language_code'] ?? 'de' ) ) ),
		'local_location_name'     => trim( (string) ( $settings['local_location_name'] ?? '' ) ),
		'local_business_name'     => trim( (string) ( $settings['local_business_name'] ?? '' ) ),
		'watch_keywords'          => (string) ( $settings['watch_keywords'] ?? '' ),
		'auto_refresh'            => '1' === (string) ( $settings['auto_refresh'] ?? '1' ),
		'ranked_limit'            => max( 20, min( 250, absint( $settings['ranked_limit'] ?? 100 ) ) ),
		'competitor_limit'        => max( 5, min( 50, absint( $settings['competitor_limit'] ?? 15 ) ) ),
		'keyword_overview_limit'  => max( 10, min( 100, absint( $settings['keyword_overview_limit'] ?? 40 ) ) ),
		'monthly_auto_budget_usd' => max( 0.25, min( 100.0, (float) ( $settings['monthly_auto_budget_usd'] ?? 2.0 ) ) ),
	];
}

/**
 * Read runtime diagnostics and monthly usage.
 *
 * @return array<string, mixed>
 */
function nexus_get_dataforseo_runtime() {
	$runtime = get_option( nexus_dataforseo_runtime_option_name(), [] );
	$runtime = is_array( $runtime ) ? $runtime : [];
	$month   = wp_date( 'Y-m' );

	if ( (string) ( $runtime['usage_month'] ?? '' ) !== $month ) {
		$runtime['usage_month']         = $month;
		$runtime['cost_month_usd']      = 0.0;
		$runtime['auto_cost_month_usd'] = 0.0;
		$runtime['calls_month']         = 0;
		$runtime['auto_calls_month']    = 0;
	}

	return wp_parse_args(
		$runtime,
		[
			'usage_month'         => $month,
			'cost_month_usd'      => 0.0,
			'auto_cost_month_usd' => 0.0,
			'calls_month'         => 0,
			'auto_calls_month'    => 0,
			'last_call_at'        => 0,
			'last_endpoint'       => '',
			'last_http_status'    => 0,
			'last_api_status'     => 0,
			'last_cost_usd'       => 0.0,
			'last_error'          => '',
		]
	);
}

/**
 * Persist provider runtime.
 *
 * @param array<string, mixed> $runtime Runtime payload.
 * @return void
 */
function nexus_update_dataforseo_runtime( $runtime ) {
	update_option( nexus_dataforseo_runtime_option_name(), $runtime, false );
}

/**
 * Determine whether another automatic paid request is allowed this month.
 *
 * @return bool
 */
function nexus_dataforseo_auto_budget_available() {
	$config  = nexus_get_dataforseo_config();
	$runtime = nexus_get_dataforseo_runtime();

	return (float) ( $runtime['auto_cost_month_usd'] ?? 0.0 ) < (float) $config['monthly_auto_budget_usd'];
}

/**
 * Record one API call in the runtime ledger.
 *
 * @param string $path       API path.
 * @param int    $http       HTTP status.
 * @param int    $api_status DataForSEO status code.
 * @param float  $cost       Reported request cost in USD.
 * @param string $error      Error message.
 * @param bool   $automatic  Whether this was a scheduled automatic call.
 * @return void
 */
function nexus_record_dataforseo_call( $path, $http, $api_status, $cost, $error, $automatic ) {
	$runtime = nexus_get_dataforseo_runtime();

	$runtime['calls_month']      = absint( $runtime['calls_month'] ?? 0 ) + 1;
	$runtime['cost_month_usd']   = (float) ( $runtime['cost_month_usd'] ?? 0.0 ) + max( 0.0, $cost );
	$runtime['last_call_at']     = time();
	$runtime['last_endpoint']    = sanitize_text_field( $path );
	$runtime['last_http_status'] = $http;
	$runtime['last_api_status']  = $api_status;
	$runtime['last_cost_usd']    = max( 0.0, $cost );
	$runtime['last_error']       = sanitize_text_field( $error );

	if ( $automatic ) {
		$runtime['auto_calls_month']    = absint( $runtime['auto_calls_month'] ?? 0 ) + 1;
		$runtime['auto_cost_month_usd'] = (float) ( $runtime['auto_cost_month_usd'] ?? 0.0 ) + max( 0.0, $cost );
	}

	nexus_update_dataforseo_runtime( $runtime );
}

/**
 * Make one authenticated DataForSEO v3 POST request.
 *
 * The API expects a generic task array, therefore a single task payload is
 * wrapped in an outer array. Automatic calls obey the monthly budget guard.
 *
 * @param string               $path      Relative v3 path.
 * @param array<string, mixed> $task      One DataForSEO task.
 * @param bool                 $automatic Whether this is background refresh traffic.
 * @return array<string, mixed>|WP_Error
 */
function nexus_dataforseo_request( $path, $task, $automatic = false ) {
	$path = ltrim( trim( (string) $path ), '/' );

	if ( '' === $path || 0 !== strpos( $path, 'v3/' ) ) {
		return new WP_Error( 'nexus_dataforseo_path', 'Ungültiger DataForSEO-Endpunkt.' );
	}

	if ( ! nexus_dataforseo_has_credentials() ) {
		return new WP_Error( 'nexus_dataforseo_credentials', 'DataForSEO ist noch nicht konfiguriert.' );
	}

	if ( $automatic && ! nexus_dataforseo_auto_budget_available() ) {
		return new WP_Error( 'nexus_dataforseo_budget', 'Das automatische DataForSEO-Monatsbudget ist erreicht.' );
	}

	$credentials = nexus_get_dataforseo_credentials();
	$response    = wp_remote_post(
		'https://api.dataforseo.com/' . $path,
		[
			'timeout' => 30,
			'headers' => [
				'Accept'        => 'application/json',
				'Authorization' => 'Basic ' . base64_encode( $credentials['login'] . ':' . $credentials['password'] ),
				'Content-Type'  => 'application/json; charset=utf-8',
			],
			'body'    => wp_json_encode( [ $task ] ),
		]
	);

	if ( is_wp_error( $response ) ) {
		nexus_record_dataforseo_call( $path, 0, 0, 0.0, $response->get_error_message(), $automatic );
		return new WP_Error( 'nexus_dataforseo_transport', 'DataForSEO konnte nicht erreicht werden: ' . $response->get_error_message() );
	}

	$http = (int) wp_remote_retrieve_response_code( $response );
	$body = json_decode( (string) wp_remote_retrieve_body( $response ), true );
	$body = is_array( $body ) ? $body : [];
	$cost = isset( $body['cost'] ) && is_numeric( $body['cost'] ) ? (float) $body['cost'] : 0.0;
	$api  = absint( $body['status_code'] ?? 0 );

	if ( $http < 200 || $http >= 300 ) {
		$message = sanitize_text_field( (string) ( $body['status_message'] ?? 'Unbekannte HTTP-Antwort.' ) );
		nexus_record_dataforseo_call( $path, $http, $api, $cost, $message, $automatic );
		return new WP_Error( 'nexus_dataforseo_http', sprintf( 'DataForSEO antwortet mit HTTP %1$d: %2$s', $http, $message ) );
	}

	if ( 20000 !== $api ) {
		$message = sanitize_text_field( (string) ( $body['status_message'] ?? 'Unbekannter API-Status.' ) );
		nexus_record_dataforseo_call( $path, $http, $api, $cost, $message, $automatic );
		return new WP_Error( 'nexus_dataforseo_api', sprintf( 'DataForSEO-Status %1$d: %2$s', $api, $message ) );
	}

	$tasks = is_array( $body['tasks'] ?? null ) ? $body['tasks'] : [];
	$task0 = ! empty( $tasks ) && is_array( $tasks[0] ) ? $tasks[0] : [];
	$task_status = absint( $task0['status_code'] ?? 0 );
	if ( 20000 !== $task_status ) {
		$message = sanitize_text_field( (string) ( $task0['status_message'] ?? 'Task ohne verwertbares Ergebnis.' ) );
		nexus_record_dataforseo_call( $path, $http, $task_status, $cost, $message, $automatic );
		return new WP_Error( 'nexus_dataforseo_task', sprintf( 'DataForSEO-Task %1$d: %2$s', $task_status, $message ) );
	}

	nexus_record_dataforseo_call( $path, $http, $api, $cost, '', $automatic );

	return $body;
}

/**
 * Extract the first task result object from a DataForSEO response.
 *
 * @param array<string, mixed>|WP_Error $response API response.
 * @return array<string, mixed>|WP_Error
 */
function nexus_dataforseo_first_result( $response ) {
	if ( is_wp_error( $response ) ) {
		return $response;
	}

	$tasks  = is_array( $response['tasks'] ?? null ) ? $response['tasks'] : [];
	$task   = ! empty( $tasks ) && is_array( $tasks[0] ) ? $tasks[0] : [];
	$result = is_array( $task['result'] ?? null ) ? $task['result'] : [];

	if ( empty( $result ) || ! is_array( $result[0] ?? null ) ) {
		return new WP_Error( 'nexus_dataforseo_empty', 'DataForSEO hat für diese Abfrage keine Daten geliefert.' );
	}

	return $result[0];
}

/**
 * Extract item rows from the first task result.
 *
 * @param array<string, mixed>|WP_Error $response API response.
 * @return array<int, array<string, mixed>>|WP_Error
 */
function nexus_dataforseo_items( $response ) {
	$result = nexus_dataforseo_first_result( $response );
	if ( is_wp_error( $result ) ) {
		return $result;
	}

	$items = is_array( $result['items'] ?? null ) ? $result['items'] : [];

	return array_values(
		array_filter(
			$items,
			static function ( $item ) {
				return is_array( $item );
			}
		)
	);
}
