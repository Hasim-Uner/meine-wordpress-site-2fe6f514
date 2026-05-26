<?php
/**
 * Observability Light: Nexus API Telemetry
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Filter REST API responses to capture failures in Nexus API.
 *
 * @param mixed           $response The response object.
 * @param WP_REST_Server  $server   The server instance.
 * @param WP_REST_Request $request  The request used to generate the response.
 * @return mixed
 */
function nexus_api_telemetry_capture( $response, $server, $request ) {
	$route = $request->get_route();

	// Only track nexus/v1 routes.
	if ( strpos( $route, '/nexus/v1/' ) !== 0 ) {
		return $response;
	}

	$status = 200;
	if ( is_wp_error( $response ) ) {
		$error_data = $response->get_error_data();
		$status     = isset( $error_data['status'] ) ? (int) $error_data['status'] : 500;
	} elseif ( $response instanceof WP_HTTP_Response || $response instanceof WP_REST_Response ) {
		$status = $response->get_status();
	}

	// Only log errors (HTTP >= 400).
	if ( $status < 400 ) {
		return $response;
	}

	$data = [];
	if ( $response instanceof WP_HTTP_Response || $response instanceof WP_REST_Response ) {
		$data = $response->get_data();
	}

	$error_code = 'unknown_error';
	if ( is_wp_error( $response ) ) {
		$error_code = $response->get_error_code();
	} elseif ( is_array( $data ) && ! empty( $data['error_code'] ) ) {
		$error_code = $data['error_code'];
	} elseif ( is_array( $data ) && ! empty( $data['code'] ) ) {
		$error_code = $data['code'];
	}

	$trace_id = 'unknown';
	if ( is_array( $data ) && ! empty( $data['traceId'] ) && is_string( $data['traceId'] ) ) {
		$trace_id = $data['traceId'];
	} else {
		$headers = $request->get_headers();
		if ( ! empty( $headers['x_nexus_trace_id'] ) ) {
			$trace_id = is_array( $headers['x_nexus_trace_id'] ) ? $headers['x_nexus_trace_id'][0] : $headers['x_nexus_trace_id'];
		}
	}

	$payload_keys = [];
	$params       = $request->get_json_params();
	if ( ! is_array( $params ) ) {
		$params = $request->get_body_params();
	}
	if ( is_array( $params ) ) {
		$payload_keys = array_keys( $params );
	}

	$ip      = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '127.0.0.1';
	$salt    = defined( 'HU_SALT' ) ? HU_SALT : ( defined( 'AUTH_SALT' ) ? AUTH_SALT : 'fallback_salt' );
	$ip_hash = md5( $ip . $salt );

	$log_entry = [
		'timestamp'    => current_time( 'timestamp' ),
		'route'        => $route,
		'traceId'      => $trace_id,
		'error_code'   => $error_code,
		'http_status'  => $status,
		'ip_hash'      => $ip_hash,
		'payload_keys' => $payload_keys,
	];

	nexus_api_telemetry_append_log( $log_entry );

	return $response;
}
add_filter( 'rest_post_dispatch', 'nexus_api_telemetry_capture', 10, 3 );

/**
 * Append an entry to the Ring-Buffer.
 *
 * @param array $entry The log entry.
 * @return void
 */
function nexus_api_telemetry_append_log( $entry ) {
	$transient_key = 'nexus_api_telemetry_logs';
	$logs          = get_transient( $transient_key );
	if ( ! is_array( $logs ) ) {
		$logs = [];
	}

	array_unshift( $logs, $entry );

	// Keep only the last 100 entries.
	if ( count( $logs ) > 100 ) {
		$logs = array_slice( $logs, 0, 100 );
	}

	// Store for 30 days.
	set_transient( $transient_key, $logs, 30 * DAY_IN_SECONDS );
}

/**
 * Register submenu under Nexus CRM.
 *
 * @return void
 */
function nexus_api_telemetry_menu() {
	$parent_slug = function_exists( 'nexus_get_crm_menu_slug' ) ? nexus_get_crm_menu_slug() : 'nexus-crm';

	add_submenu_page(
		$parent_slug,
		'API Telemetrie',
		'API Fehler',
		'edit_pages',
		'nexus-api-telemetry',
		'nexus_api_telemetry_admin_page'
	);
}
add_action( 'admin_menu', 'nexus_api_telemetry_menu', 60 );

/**
 * Render the Admin Page.
 *
 * @return void
 */
function nexus_api_telemetry_admin_page() {
	if ( ! current_user_can( 'edit_pages' ) ) {
		wp_die( esc_html__( 'Sorry, you are not allowed to access this page.', 'blocksy-child' ) );
	}

	// Action to clear logs.
	if ( isset( $_POST['nexus_clear_telemetry_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nexus_clear_telemetry_nonce'] ) ), 'nexus_clear_telemetry' ) ) {
		delete_transient( 'nexus_api_telemetry_logs' );
		echo '<div class="notice notice-success is-dismissible"><p>Logs wurden geleert.</p></div>';
	}

	$logs = get_transient( 'nexus_api_telemetry_logs' );
	if ( ! is_array( $logs ) ) {
		$logs = [];
	}

	?>
	<div class="wrap">
		<h1 class="wp-heading-inline">Nexus API Observability (Light)</h1>
		<form method="post" action="" style="display:inline-block; margin-left:10px;">
			<?php wp_nonce_field( 'nexus_clear_telemetry', 'nexus_clear_telemetry_nonce' ); ?>
			<input type="submit" class="button" value="Logs leeren" onclick="return confirm('Möchten Sie alle Logs wirklich löschen?');">
		</form>

		<p>Ein schlanker Ring-Buffer für die letzten 100 fehlgeschlagenen REST API Requests (HTTP &gt;= 400).</p>

		<table class="wp-list-table widefat fixed striped">
			<thead>
				<tr>
					<th>Datum</th>
					<th>Route</th>
					<th>HTTP Status</th>
					<th>Error Code</th>
					<th>Trace ID</th>
					<th>IP Hash</th>
					<th>Payload Keys</th>
				</tr>
			</thead>
			<tbody>
				<?php if ( empty( $logs ) ) : ?>
					<tr>
						<td colspan="7">Keine fehlgeschlagenen Requests protokolliert. Alles läuft reibungslos! 🎉</td>
					</tr>
				<?php else : ?>
					<?php foreach ( $logs as $log ) : ?>
						<tr>
							<td><?php echo esc_html( wp_date( 'Y-m-d H:i:s', $log['timestamp'] ) ); ?></td>
							<td><code><?php echo esc_html( $log['route'] ); ?></code></td>
							<td>
								<span style="display:inline-block; padding:3px 8px; border-radius:3px; font-weight:bold; color:#fff; background: <?php echo $log['http_status'] >= 500 ? '#d63638' : '#e27730'; ?>;">
									<?php echo esc_html( $log['http_status'] ); ?>
								</span>
							</td>
							<td><code><?php echo esc_html( $log['error_code'] ); ?></code></td>
							<td><code style="font-size:11px;"><?php echo esc_html( $log['traceId'] ); ?></code></td>
							<td><code style="font-size:11px;" title="MD5 Hash (IP + Salt)"><?php echo esc_html( $log['ip_hash'] ); ?></code></td>
							<td>
								<?php if ( ! empty( $log['payload_keys'] ) && is_array( $log['payload_keys'] ) ) : ?>
									<code><?php echo esc_html( implode( ', ', $log['payload_keys'] ) ); ?></code>
								<?php else : ?>
									<em>Keine</em>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
	<?php
}
