<?php
/**
 * Runtime diagnostics and permission preflight for Search Console sitemap writes.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Determine whether one HTTP request targets Search Console sitemap submit.
 *
 * @param string               $url  Request URL.
 * @param array<string, mixed> $args Request args.
 * @return bool
 */
function nexus_is_seo_cockpit_sitemap_submit_request( $url, $args = [] ) {
	$parts  = wp_parse_url( (string) $url );
	$method = strtoupper( (string) ( $args['method'] ?? '' ) );

	if ( ! is_array( $parts ) || 'PUT' !== $method ) {
		return false;
	}

	$host = strtolower( (string) ( $parts['host'] ?? '' ) );
	$path = (string) ( $parts['path'] ?? '' );

	if ( 'www.googleapis.com' !== $host ) {
		return false;
	}

	return false !== strpos( $path, '/webmasters/v3/sites/' ) && false !== strpos( $path, '/sitemaps/' );
}

/**
 * Return the Search Console permission level for the configured property.
 *
 * @param bool $force Force a fresh sites.list request.
 * @return string|WP_Error
 */
function nexus_get_seo_cockpit_property_permission_level( $force = false ) {
	$property = nexus_get_seo_cockpit_property();
	if ( '' === $property ) {
		return new WP_Error( 'nexus_seo_missing_property', 'Es ist noch keine Search-Console-Property hinterlegt.' );
	}

	$sites = nexus_get_seo_cockpit_sites( $force );
	if ( is_wp_error( $sites ) ) {
		return $sites;
	}

	foreach ( (array) $sites as $site ) {
		if ( ! is_array( $site ) ) {
			continue;
		}

		if ( $property === (string) ( $site['siteUrl'] ?? '' ) ) {
			return sanitize_key( (string) ( $site['permissionLevel'] ?? '' ) );
		}
	}

	return new WP_Error(
		'nexus_seo_property_not_visible',
		'Die konfigurierte Property wurde im Search-Console-Konto nicht gefunden.'
	);
}

/**
 * Give opaque non-JSON Search Console errors a visible HTTP status.
 *
 * Google documents this endpoint as a bodyless PUT. This helper deliberately
 * does not alter the request transport; it only improves the response signal.
 *
 * @param array<string, mixed>|WP_Error $response HTTP response.
 * @param array<string, mixed>          $args     Request args.
 * @param string                        $url      Request URL.
 * @return array<string, mixed>|WP_Error
 */
function nexus_normalize_seo_cockpit_sitemap_error_response( $response, $args, $url ) {
	if ( is_wp_error( $response ) || ! nexus_is_seo_cockpit_sitemap_submit_request( $url, $args ) ) {
		return $response;
	}

	$status = (int) wp_remote_retrieve_response_code( $response );
	if ( $status >= 200 && $status < 300 ) {
		return $response;
	}

	$body    = (string) wp_remote_retrieve_body( $response );
	$decoded = json_decode( $body, true );

	if ( is_array( $decoded ) && ! empty( $decoded['error']['message'] ) ) {
		return $response;
	}

	$response_message = trim( (string) wp_remote_retrieve_response_message( $response ) );
	$detail           = trim( wp_strip_all_tags( $body ) );
	if ( function_exists( 'mb_substr' ) ) {
		$detail = mb_substr( $detail, 0, 300 );
	} else {
		$detail = substr( $detail, 0, 300 );
	}

	$message = sprintf( 'Search Console API: HTTP %d', $status );
	if ( '' !== $response_message ) {
		$message .= ' ' . $response_message;
	}
	if ( '' !== $detail ) {
		$message .= ' — ' . $detail;
	}

	$response['body'] = wp_json_encode(
		[
			'error' => [
				'code'    => $status,
				'message' => $message,
			],
		]
	);

	return $response;
}
add_filter( 'http_response', 'nexus_normalize_seo_cockpit_sitemap_error_response', 20, 3 );

/**
 * Store sanitized runtime details for the last sitemap API request.
 *
 * No access token, Authorization header or client secret is persisted here.
 *
 * @param mixed                $response HTTP response or WP_Error.
 * @param string               $context  HTTP API debug context.
 * @param string               $class    Transport class.
 * @param array<string, mixed> $args     Parsed request args.
 * @param string               $url      Request URL.
 * @return void
 */
function nexus_capture_seo_cockpit_sitemap_http_debug( $response, $context, $class, $args, $url ) {
	unset( $context, $class );

	if ( ! nexus_is_seo_cockpit_sitemap_submit_request( $url, $args ) ) {
		return;
	}

	$state = nexus_get_seo_cockpit_gsc_control_state();
	$state['last_api_checked_at'] = current_time( 'timestamp' );
	$state['last_api_url']        = esc_url_raw( (string) $url );

	if ( is_wp_error( $response ) ) {
		$state['last_http_status']       = 0;
		$state['last_http_message']      = sanitize_text_field( $response->get_error_message() );
		$state['last_http_body_excerpt'] = '';
	} else {
		$status  = (int) wp_remote_retrieve_response_code( $response );
		$message = trim( (string) wp_remote_retrieve_response_message( $response ) );
		$body    = trim( wp_strip_all_tags( (string) wp_remote_retrieve_body( $response ) ) );

		if ( function_exists( 'mb_substr' ) ) {
			$body = mb_substr( $body, 0, 500 );
		} else {
			$body = substr( $body, 0, 500 );
		}

		$state['last_http_status']       = $status;
		$state['last_http_message']      = sanitize_text_field( $message );
		$state['last_http_body_excerpt'] = sanitize_textarea_field( $body );
	}

	nexus_update_seo_cockpit_gsc_control_state( $state );
}
add_action( 'http_api_debug', 'nexus_capture_seo_cockpit_sitemap_http_debug', 20, 5 );

/**
 * Stop a manual write early when Search Console exposes only read-level access.
 *
 * Full users are allowed through because Search Console permission behavior can
 * vary by feature; a real API rejection is then captured with its HTTP status.
 *
 * @return void
 */
function nexus_preflight_seo_cockpit_sitemap_permission() {
	if ( ! nexus_current_user_can_manage_seo_cockpit() ) {
		return;
	}

	$permission = nexus_get_seo_cockpit_property_permission_level( true );
	if ( is_wp_error( $permission ) ) {
		return;
	}

	if ( ! in_array( $permission, [ 'siterestricteduser', 'siteunverifieduser' ], true ) ) {
		return;
	}

	$sitemap = nexus_get_seo_cockpit_managed_sitemap_url();
	nexus_record_seo_cockpit_sitemap_submit(
		'manual',
		'error',
		'Search Console Property-Berechtigung: ' . $permission . '. Diese Rolle darf die Sitemap nicht einreichen.',
		$sitemap,
		[
			'last_property_permission' => $permission,
		]
	);

	wp_safe_redirect(
		add_query_arg(
			'nexus_gsc_notice',
			'submit_failed',
			nexus_get_seo_cockpit_search_console_control_url()
		)
	);
	exit;
}
add_action( 'admin_post_nexus_seo_cockpit_submit_sitemap', 'nexus_preflight_seo_cockpit_sitemap_permission', 1 );

/**
 * Show property permission and the raw HTTP result on the control page.
 *
 * @return void
 */
function nexus_render_seo_cockpit_sitemap_runtime_notice() {
	$page = isset( $_GET['page'] ) ? sanitize_key( (string) wp_unslash( $_GET['page'] ) ) : '';
	if ( 'nexus-seo-cockpit-search-console' !== $page || ! nexus_current_user_can_manage_seo_cockpit() ) {
		return;
	}

	$permission = nexus_get_seo_cockpit_property_permission_level( false );
	$state      = nexus_get_seo_cockpit_gsc_control_state();
	$level      = is_wp_error( $permission ) ? $permission->get_error_message() : (string) $permission;
	$status     = absint( $state['last_http_status'] ?? 0 );
	$message    = (string) ( $state['last_http_message'] ?? '' );
	$body       = (string) ( $state['last_http_body_excerpt'] ?? '' );
	?>
	<div class="notice notice-info">
		<p><strong>Search-Console-Diagnose:</strong> Property-Berechtigung: <code><?php echo esc_html( $level ); ?></code>.</p>
		<?php if ( array_key_exists( 'last_http_status', $state ) ) : ?>
			<p>Letzter Sitemap-API-Aufruf: <strong><?php echo 0 === $status ? 'Transportfehler' : 'HTTP ' . esc_html( (string) $status ); ?></strong><?php echo '' !== $message ? ' · ' . esc_html( $message ) : ''; ?>.</p>
			<?php if ( '' !== $body ) : ?>
				<p><code style="white-space: pre-wrap;"><?php echo esc_html( $body ); ?></code></p>
			<?php endif; ?>
		<?php endif; ?>
	</div>
	<?php
}
add_action( 'admin_notices', 'nexus_render_seo_cockpit_sitemap_runtime_notice', 20 );
