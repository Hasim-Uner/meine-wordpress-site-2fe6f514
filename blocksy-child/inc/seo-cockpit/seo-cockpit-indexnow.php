<?php
/**
 * SEO Cockpit IndexNow integration.
 *
 * Provides runtime key ownership verification, manual URL submission,
 * optional automatic notifications and a compact submission history.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the IndexNow admin page slug.
 *
 * @return string
 */
function nexus_indexnow_get_admin_slug() {
	return 'nexus-seo-cockpit-indexnow';
}

/**
 * Return a valid stored IndexNow key without creating one.
 *
 * @return string
 */
function nexus_indexnow_get_key() {
	$key = trim( (string) get_option( 'nexus_seo_cockpit_indexnow_key', '' ) );

	return preg_match( '/^[A-Za-z0-9-]{8,128}$/', $key ) ? $key : '';
}

/**
 * Create the runtime IndexNow key when needed.
 *
 * The key is stored only in WordPress. No credential is committed to Git.
 *
 * @return string
 */
function nexus_indexnow_ensure_key() {
	$key = nexus_indexnow_get_key();
	if ( '' !== $key ) {
		return $key;
	}

	$key = wp_generate_password( 32, false, false );
	update_option( 'nexus_seo_cockpit_indexnow_key', $key, false );

	return $key;
}

/**
 * Return the canonical site host used in IndexNow payloads.
 *
 * @return string
 */
function nexus_indexnow_get_site_host() {
	$host = wp_parse_url( home_url( '/' ), PHP_URL_HOST );

	return strtolower( trim( (string) $host ) );
}

/**
 * Return the public key-file URL.
 *
 * @return string
 */
function nexus_indexnow_get_key_location() {
	$key = nexus_indexnow_get_key();
	if ( '' === $key ) {
		return '';
	}

	return home_url( '/' . rawurlencode( $key ) . '.txt' );
}

/**
 * Register the root-level IndexNow key route.
 *
 * @return void
 */
function nexus_indexnow_register_key_route() {
	add_rewrite_rule( '^([A-Za-z0-9-]{8,128})\.txt$', 'index.php?nexus_indexnow_key=$matches[1]', 'top' );
}
add_action( 'init', 'nexus_indexnow_register_key_route', 1 );

/**
 * Add the key route query variable.
 *
 * @param array<int, string> $vars Existing public query vars.
 * @return array<int, string>
 */
function nexus_indexnow_register_query_var( $vars ) {
	$vars[] = 'nexus_indexnow_key';
	return $vars;
}
add_filter( 'query_vars', 'nexus_indexnow_register_query_var' );

/**
 * Serve the runtime key as a UTF-8 text file from the site root.
 *
 * @return void
 */
function nexus_indexnow_maybe_serve_key_file() {
	$requested = trim( (string) get_query_var( 'nexus_indexnow_key', '' ) );
	if ( '' === $requested ) {
		return;
	}

	$key = nexus_indexnow_get_key();
	if ( '' === $key || ! hash_equals( $key, $requested ) ) {
		return;
	}

	status_header( 200 );
	nocache_headers();
	header( 'Content-Type: text/plain; charset=utf-8' );
	echo esc_html( $key );
	exit;
}
add_action( 'template_redirect', 'nexus_indexnow_maybe_serve_key_file', 0 );

/**
 * Flush rewrite rules once when the IndexNow route is introduced.
 *
 * @return void
 */
function nexus_indexnow_maybe_flush_rewrite_rules() {
	if ( '1' === (string) get_option( 'nexus_seo_cockpit_indexnow_rewrite_version', '' ) ) {
		return;
	}

	nexus_indexnow_register_key_route();
	flush_rewrite_rules( false );
	update_option( 'nexus_seo_cockpit_indexnow_rewrite_version', '1', false );
}
add_action( 'admin_init', 'nexus_indexnow_maybe_flush_rewrite_rules', 5 );

/**
 * Determine whether a URL belongs to the configured IndexNow host.
 *
 * @param string $url Candidate URL.
 * @return bool
 */
function nexus_indexnow_url_belongs_to_site( $url ) {
	$url    = esc_url_raw( trim( (string) $url ) );
	$host   = strtolower( trim( (string) wp_parse_url( $url, PHP_URL_HOST ) ) );
	$scheme = strtolower( trim( (string) wp_parse_url( $url, PHP_URL_SCHEME ) ) );

	return '' !== $url
		&& in_array( $scheme, [ 'http', 'https' ], true )
		&& '' !== $host
		&& $host === nexus_indexnow_get_site_host();
}

/**
 * Return the bounded IndexNow submission history.
 *
 * @return array<int, array<string, mixed>>
 */
function nexus_indexnow_get_history() {
	$history = get_option( 'nexus_seo_cockpit_indexnow_history', [] );
	return is_array( $history ) ? array_values( $history ) : [];
}

/**
 * Persist one IndexNow submission result.
 *
 * @param string $url    Submitted URL.
 * @param string $source Submission source.
 * @param int    $code   HTTP response code, or 0 on transport failure.
 * @param string $status Result status.
 * @param string $message Compact result message.
 * @return void
 */
function nexus_indexnow_store_history_row( $url, $source, $code, $status, $message ) {
	$history = nexus_indexnow_get_history();
	array_unshift(
		$history,
		[
			'url'       => esc_url_raw( $url ),
			'source'    => sanitize_key( $source ),
			'code'      => absint( $code ),
			'status'    => sanitize_key( $status ),
			'message'   => sanitize_text_field( $message ),
			'timestamp' => time(),
		]
	);

	update_option( 'nexus_seo_cockpit_indexnow_history', array_slice( $history, 0, 50 ), false );
}

/**
 * Return a compact human-readable response message.
 *
 * @param int $code HTTP response code.
 * @return string
 */
function nexus_indexnow_get_response_message( $code ) {
	$messages = [
		200 => 'URL erfolgreich an IndexNow gemeldet.',
		202 => 'URL empfangen; Key-Verifikation läuft noch.',
		400 => 'Ungültiger IndexNow-Request.',
		403 => 'IndexNow-Key konnte nicht verifiziert werden.',
		422 => 'URL, Host oder Key-Schema passt nicht.',
		429 => 'IndexNow drosselt die Meldungen vorübergehend.',
	];

	return $messages[ absint( $code ) ] ?? 'Unerwartete Antwort von IndexNow.';
}

/**
 * Submit one or more site URLs to the shared IndexNow endpoint.
 *
 * @param array<int, string>|string $urls   URLs to submit.
 * @param string                    $source Submission source.
 * @return array<string, mixed>|WP_Error
 */
function nexus_indexnow_submit_urls( $urls, $source = 'manual' ) {
	$urls       = is_array( $urls ) ? $urls : [ $urls ];
	$clean_urls = [];

	foreach ( $urls as $url ) {
		$url = esc_url_raw( trim( (string) $url ) );
		if ( nexus_indexnow_url_belongs_to_site( $url ) ) {
			$clean_urls[] = $url;
		}
	}

	$clean_urls = array_values( array_unique( $clean_urls ) );
	if ( empty( $clean_urls ) ) {
		return new WP_Error( 'indexnow_invalid_url', 'Die URL muss zu dieser Website gehören.' );
	}

	$key          = nexus_indexnow_ensure_key();
	$key_location = nexus_indexnow_get_key_location();
	$host         = nexus_indexnow_get_site_host();

	if ( '' === $host || '' === $key_location ) {
		return new WP_Error( 'indexnow_setup_incomplete', 'IndexNow-Key oder Website-Host ist nicht verfügbar.' );
	}

	$body = wp_json_encode(
		[
			'host'        => $host,
			'key'         => $key,
			'keyLocation' => $key_location,
			'urlList'     => $clean_urls,
		]
	);

	if ( false === $body ) {
		return new WP_Error( 'indexnow_json_failed', 'IndexNow-Request konnte nicht aufgebaut werden.' );
	}

	$response = wp_remote_post(
		'https://api.indexnow.org/indexnow',
		[
			'timeout'     => 15,
			'redirection' => 2,
			'headers'     => [
				'Accept'       => 'application/json',
				'Content-Type' => 'application/json; charset=utf-8',
			],
			'body'        => $body,
			'data_format' => 'body',
		]
	);

	if ( is_wp_error( $response ) ) {
		$message = $response->get_error_message();
		foreach ( $clean_urls as $url ) {
			nexus_indexnow_store_history_row( $url, $source, 0, 'error', $message );
		}
		update_option(
			'nexus_seo_cockpit_indexnow_last_result',
			[
				'code'      => 0,
				'success'   => false,
				'message'   => sanitize_text_field( $message ),
				'timestamp' => time(),
			],
			false
		);
		return $response;
	}

	$code    = absint( wp_remote_retrieve_response_code( $response ) );
	$success = in_array( $code, [ 200, 202 ], true );
	$message = nexus_indexnow_get_response_message( $code );

	foreach ( $clean_urls as $url ) {
		nexus_indexnow_store_history_row( $url, $source, $code, $success ? 'success' : 'error', $message );
	}

	$result = [
		'code'      => $code,
		'success'   => $success,
		'message'   => $message,
		'timestamp' => time(),
		'count'     => count( $clean_urls ),
	];
	update_option( 'nexus_seo_cockpit_indexnow_last_result', $result, false );

	if ( ! $success ) {
		return new WP_Error( 'indexnow_http_' . $code, $message, $result );
	}

	return $result;
}

/**
 * Determine whether automatic IndexNow notifications are enabled.
 *
 * Defaults to enabled for this dedicated custom SEO stack.
 *
 * @return bool
 */
function nexus_indexnow_auto_enabled() {
	return '1' === (string) get_option( 'nexus_seo_cockpit_indexnow_auto', '1' );
}

/**
 * Queue an automatic notification without blocking an editor save request.
 *
 * @param string $url    URL to submit.
 * @param string $source Submission source.
 * @return void
 */
function nexus_indexnow_queue_url( $url, $source ) {
	$url = esc_url_raw( trim( (string) $url ) );
	if ( ! nexus_indexnow_url_belongs_to_site( $url ) ) {
		return;
	}

	$debounce_key = 'nexus_indexnow_' . md5( $source . '|' . $url );
	if ( get_transient( $debounce_key ) ) {
		return;
	}

	set_transient( $debounce_key, '1', 5 * MINUTE_IN_SECONDS );
	wp_schedule_single_event( time() + 20, 'nexus_indexnow_submit_scheduled', [ $url, sanitize_key( $source ) ] );
}

/**
 * Run one queued IndexNow submission.
 *
 * @param string $url    URL to submit.
 * @param string $source Submission source.
 * @return void
 */
function nexus_indexnow_run_scheduled_submission( $url, $source ) {
	nexus_indexnow_submit_urls( [ (string) $url ], (string) $source );
}
add_action( 'nexus_indexnow_submit_scheduled', 'nexus_indexnow_run_scheduled_submission', 10, 2 );

/**
 * Notify IndexNow when a public post is published or updated.
 *
 * @param string  $new_status New post status.
 * @param string  $old_status Previous post status.
 * @param WP_Post $post       Saved post.
 * @return void
 */
function nexus_indexnow_handle_post_transition( $new_status, $old_status, $post ) {
	if ( ! nexus_indexnow_auto_enabled() || 'publish' !== $new_status || ! ( $post instanceof WP_Post ) ) {
		return;
	}

	if ( wp_is_post_revision( $post->ID ) || wp_is_post_autosave( $post->ID ) ) {
		return;
	}

	$post_type = get_post_type_object( $post->post_type );
	if ( ! $post_type || empty( $post_type->public ) ) {
		return;
	}

	if ( ! apply_filters( 'nexus_indexnow_should_auto_submit', true, $post, $old_status ) ) {
		return;
	}

	$url = get_permalink( $post );
	if ( is_string( $url ) && '' !== $url ) {
		nexus_indexnow_queue_url( $url, 'publish' === $old_status ? 'auto-update' : 'auto-publish' );
	}
}
add_action( 'transition_post_status', 'nexus_indexnow_handle_post_transition', 20, 3 );

/**
 * Notify IndexNow before a previously public URL is deleted.
 *
 * @param int $post_id Post ID.
 * @return void
 */
function nexus_indexnow_handle_before_delete( $post_id ) {
	if ( ! nexus_indexnow_auto_enabled() || 'publish' !== get_post_status( $post_id ) ) {
		return;
	}

	$post = get_post( $post_id );
	if ( ! ( $post instanceof WP_Post ) ) {
		return;
	}

	$post_type = get_post_type_object( $post->post_type );
	if ( ! $post_type || empty( $post_type->public ) ) {
		return;
	}

	$url = get_permalink( $post );
	if ( is_string( $url ) && '' !== $url ) {
		nexus_indexnow_queue_url( $url, 'auto-delete' );
	}
}
add_action( 'before_delete_post', 'nexus_indexnow_handle_before_delete', 10, 1 );

/**
 * Return the IndexNow admin URL.
 *
 * @param array<string, mixed> $args Optional query args.
 * @return string
 */
function nexus_indexnow_get_admin_url( $args = [] ) {
	$url = admin_url( 'admin.php?page=' . nexus_indexnow_get_admin_slug() );
	return empty( $args ) ? $url : add_query_arg( $args, $url );
}

/**
 * Register the IndexNow submenu below SEO Cockpit.
 *
 * @return void
 */
function nexus_indexnow_register_admin_page() {
	if ( ! function_exists( 'nexus_get_seo_cockpit_menu_slug' ) || ! function_exists( 'nexus_get_seo_cockpit_manage_cap' ) ) {
		return;
	}

	add_submenu_page(
		nexus_get_seo_cockpit_menu_slug(),
		'IndexNow',
		'IndexNow',
		nexus_get_seo_cockpit_manage_cap(),
		nexus_indexnow_get_admin_slug(),
		'nexus_indexnow_render_admin_page'
	);
}
add_action( 'admin_menu', 'nexus_indexnow_register_admin_page', 31 );

/**
 * Enqueue the focused IndexNow admin stylesheet.
 *
 * @return void
 */
function nexus_indexnow_enqueue_admin_assets() {
	$page = isset( $_GET['page'] ) ? sanitize_key( (string) wp_unslash( $_GET['page'] ) ) : '';
	if ( nexus_indexnow_get_admin_slug() !== $page && ! ( function_exists( 'nexus_get_seo_cockpit_menu_slug' ) && nexus_get_seo_cockpit_menu_slug() === $page ) ) {
		return;
	}

	$path = get_stylesheet_directory() . '/assets/css/seo-cockpit-indexnow.css';
	if ( ! file_exists( $path ) ) {
		return;
	}

	wp_enqueue_style(
		'nexus-seo-cockpit-indexnow',
		get_stylesheet_directory_uri() . '/assets/css/seo-cockpit-indexnow.css',
		[],
		filemtime( $path )
	);
}
add_action( 'admin_enqueue_scripts', 'nexus_indexnow_enqueue_admin_assets', 30 );

/**
 * Redirect back to a safe cockpit page after an action.
 *
 * @param string $notice Notice key.
 * @param int    $code   Optional response code.
 * @return void
 */
function nexus_indexnow_redirect_after_action( $notice, $code = 0 ) {
	$redirect = isset( $_POST['redirect_to'] ) ? esc_url_raw( (string) wp_unslash( $_POST['redirect_to'] ) ) : '';
	$redirect = wp_validate_redirect( $redirect, nexus_indexnow_get_admin_url() );
	$redirect = add_query_arg(
		[
			'nexus_indexnow_notice' => sanitize_key( $notice ),
			'nexus_indexnow_code'   => absint( $code ),
		],
		$redirect
	);

	wp_safe_redirect( $redirect );
	exit;
}

/**
 * Handle manual IndexNow URL submission.
 *
 * @return void
 */
function nexus_indexnow_handle_manual_submit() {
	if ( ! function_exists( 'nexus_current_user_can_manage_seo_cockpit' ) || ! nexus_current_user_can_manage_seo_cockpit() ) {
		wp_die( esc_html__( 'Keine Berechtigung.', 'blocksy-child' ) );
	}

	check_admin_referer( 'nexus_indexnow_submit' );
	$url    = isset( $_POST['indexnow_url'] ) ? esc_url_raw( (string) wp_unslash( $_POST['indexnow_url'] ) ) : '';
	$result = nexus_indexnow_submit_urls( [ $url ], 'manual' );

	if ( is_wp_error( $result ) ) {
		$data = $result->get_error_data();
		$code = is_array( $data ) ? absint( $data['code'] ?? 0 ) : 0;
		nexus_indexnow_redirect_after_action( 'error', $code );
	}

	nexus_indexnow_redirect_after_action( 'success', absint( $result['code'] ?? 0 ) );
}
add_action( 'admin_post_nexus_indexnow_submit', 'nexus_indexnow_handle_manual_submit' );

/**
 * Save automatic IndexNow notification preference.
 *
 * @return void
 */
function nexus_indexnow_handle_settings_save() {
	if ( ! function_exists( 'nexus_current_user_can_manage_seo_cockpit' ) || ! nexus_current_user_can_manage_seo_cockpit() ) {
		wp_die( esc_html__( 'Keine Berechtigung.', 'blocksy-child' ) );
	}

	check_admin_referer( 'nexus_indexnow_settings' );
	update_option( 'nexus_seo_cockpit_indexnow_auto', isset( $_POST['indexnow_auto'] ) ? '1' : '0', false );
	nexus_indexnow_ensure_key();
	nexus_indexnow_redirect_after_action( 'settings_saved' );
}
add_action( 'admin_post_nexus_indexnow_settings', 'nexus_indexnow_handle_settings_save' );

/**
 * Rotate the runtime IndexNow key.
 *
 * @return void
 */
function nexus_indexnow_handle_key_regeneration() {
	if ( ! function_exists( 'nexus_current_user_can_manage_seo_cockpit' ) || ! nexus_current_user_can_manage_seo_cockpit() ) {
		wp_die( esc_html__( 'Keine Berechtigung.', 'blocksy-child' ) );
	}

	check_admin_referer( 'nexus_indexnow_regenerate_key' );
	delete_option( 'nexus_seo_cockpit_indexnow_key' );
	nexus_indexnow_ensure_key();
	nexus_indexnow_redirect_after_action( 'key_regenerated' );
}
add_action( 'admin_post_nexus_indexnow_regenerate_key', 'nexus_indexnow_handle_key_regeneration' );

/**
 * Render result notices on IndexNow and URL-detail cockpit screens.
 *
 * @return void
 */
function nexus_indexnow_render_notice() {
	$notice = isset( $_GET['nexus_indexnow_notice'] ) ? sanitize_key( (string) wp_unslash( $_GET['nexus_indexnow_notice'] ) ) : '';
	if ( '' === $notice ) {
		return;
	}

	$code = isset( $_GET['nexus_indexnow_code'] ) ? absint( $_GET['nexus_indexnow_code'] ) : 0;
	$map  = [
		'success'         => [ 'success', 0 < $code ? nexus_indexnow_get_response_message( $code ) . ' HTTP ' . $code : 'URL erfolgreich an IndexNow gemeldet.' ],
		'error'           => [ 'error', 0 < $code ? nexus_indexnow_get_response_message( $code ) . ' HTTP ' . $code : 'IndexNow-Meldung ist fehlgeschlagen. Details stehen im Verlauf.' ],
		'settings_saved'  => [ 'success', 'IndexNow-Einstellung gespeichert.' ],
		'key_regenerated' => [ 'success', 'Neuer IndexNow-Key wurde erzeugt.' ],
	];

	if ( empty( $map[ $notice ] ) ) {
		return;
	}

	printf(
		'<div class="notice notice-%1$s is-dismissible"><p>%2$s</p></div>',
		esc_attr( $map[ $notice ][0] ),
		esc_html( $map[ $notice ][1] )
	);
}
add_action( 'admin_notices', 'nexus_indexnow_render_notice' );

/**
 * Add a direct IndexNow button when a cockpit URL drilldown is open.
 *
 * @return void
 */
function nexus_indexnow_render_detail_action() {
	if ( ! function_exists( 'nexus_get_seo_cockpit_menu_slug' ) || ! function_exists( 'nexus_current_user_can_manage_seo_cockpit' ) || ! nexus_current_user_can_manage_seo_cockpit() ) {
		return;
	}

	$page       = isset( $_GET['page'] ) ? sanitize_key( (string) wp_unslash( $_GET['page'] ) ) : '';
	$detail_url = isset( $_GET['detail_url'] ) ? esc_url_raw( (string) wp_unslash( $_GET['detail_url'] ) ) : '';
	if ( nexus_get_seo_cockpit_menu_slug() !== $page || '' === $detail_url || ! nexus_indexnow_url_belongs_to_site( $detail_url ) ) {
		return;
	}
	?>
	<div class="nexus-indexnow-detail-action">
		<div><span class="dashicons dashicons-search" aria-hidden="true"></span><div><strong>Bing / IndexNow</strong><p>Diese URL nach Änderungen direkt an teilnehmende Suchmaschinen melden.</p></div></div>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="nexus_indexnow_submit">
			<input type="hidden" name="indexnow_url" value="<?php echo esc_attr( $detail_url ); ?>">
			<input type="hidden" name="redirect_to" value="<?php echo esc_attr( nexus_get_seo_cockpit_detail_url( $detail_url ) ); ?>">
			<?php wp_nonce_field( 'nexus_indexnow_submit' ); ?>
			<button class="button button-primary" type="submit">URL jetzt melden</button>
		</form>
	</div>
	<?php
}
add_action( 'admin_notices', 'nexus_indexnow_render_detail_action', 20 );

/**
 * Render the dedicated IndexNow cockpit page.
 *
 * @return void
 */
function nexus_indexnow_render_admin_page() {
	if ( ! function_exists( 'nexus_current_user_can_manage_seo_cockpit' ) || ! nexus_current_user_can_manage_seo_cockpit() ) {
		return;
	}

	$key          = nexus_indexnow_ensure_key();
	$key_location = nexus_indexnow_get_key_location();
	$history      = nexus_indexnow_get_history();
	$last_result  = get_option( 'nexus_seo_cockpit_indexnow_last_result', [] );
	$last_result  = is_array( $last_result ) ? $last_result : [];
	$auto_enabled = nexus_indexnow_auto_enabled();
	?>
	<div class="wrap nexus-indexnow-admin">
		<div class="nexus-indexnow-hero">
			<div><p class="nexus-indexnow-eyebrow">SEO Cockpit · Indexing Control</p><h1>Bing / IndexNow</h1><p>Neue, geänderte oder gelöschte URLs direkt melden – ohne Rank Math und ohne zusätzlichen Bing-OAuth-Flow.</p></div>
			<div class="nexus-indexnow-health is-ready"><span class="dashicons dashicons-yes-alt" aria-hidden="true"></span><div><strong>Bereit</strong><span>Key + Endpoint aktiv</span></div></div>
		</div>

		<div class="nexus-indexnow-grid nexus-indexnow-grid--top">
			<section class="nexus-indexnow-card nexus-indexnow-submit-card">
				<div class="nexus-indexnow-card__head"><span class="dashicons dashicons-search" aria-hidden="true"></span><div><h2>URL jetzt melden</h2><p>Für neue oder gerade aktualisierte Seiten.</p></div></div>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<input type="hidden" name="action" value="nexus_indexnow_submit">
					<input type="hidden" name="redirect_to" value="<?php echo esc_attr( nexus_indexnow_get_admin_url() ); ?>">
					<?php wp_nonce_field( 'nexus_indexnow_submit' ); ?>
					<label for="nexus-indexnow-url">URL dieser Website</label>
					<div class="nexus-indexnow-submit-row"><input id="nexus-indexnow-url" type="url" name="indexnow_url" value="<?php echo esc_attr( home_url( '/' ) ); ?>" required><button class="button button-primary button-hero" type="submit">URL jetzt melden</button></div>
				</form>
				<p class="nexus-indexnow-help">Eine erfolgreiche Meldung bedeutet: Suchmaschinen haben die Änderung erhalten. Sie garantiert keine Indexierung.</p>
			</section>

			<section class="nexus-indexnow-card">
				<div class="nexus-indexnow-card__head"><span class="dashicons dashicons-admin-network" aria-hidden="true"></span><div><h2>Systemstatus</h2><p>Eigener Runtime-Key, keine Repo-Credentials.</p></div></div>
				<dl class="nexus-indexnow-status-list">
					<div><dt>Host</dt><dd><?php echo esc_html( nexus_indexnow_get_site_host() ); ?></dd></div>
					<div><dt>Key-Datei</dt><dd><a href="<?php echo esc_url( $key_location ); ?>" target="_blank" rel="noreferrer noopener"><?php echo esc_html( wp_basename( $key_location ) ); ?></a></dd></div>
					<div><dt>Automatik</dt><dd><span class="nexus-indexnow-pill <?php echo $auto_enabled ? 'is-on' : 'is-off'; ?>"><?php echo esc_html( $auto_enabled ? 'Aktiv' : 'Aus' ); ?></span></dd></div>
					<div><dt>Letzte API-Antwort</dt><dd><?php echo esc_html( ! empty( $last_result['timestamp'] ) ? ( ( ! empty( $last_result['success'] ) ? 'OK' : 'Fehler' ) . ' · HTTP ' . absint( $last_result['code'] ?? 0 ) . ' · ' . wp_date( 'd.m.Y H:i', absint( $last_result['timestamp'] ) ) ) : 'Noch keine Meldung' ); ?></dd></div>
				</dl>
			</section>
		</div>

		<div class="nexus-indexnow-grid">
			<section class="nexus-indexnow-card">
				<div class="nexus-indexnow-card__head"><span class="dashicons dashicons-controls-repeat" aria-hidden="true"></span><div><h2>Automatische Meldung</h2><p>Bei Veröffentlichung, Aktualisierung und Löschung öffentlicher Inhalte.</p></div></div>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<input type="hidden" name="action" value="nexus_indexnow_settings">
					<input type="hidden" name="redirect_to" value="<?php echo esc_attr( nexus_indexnow_get_admin_url() ); ?>">
					<?php wp_nonce_field( 'nexus_indexnow_settings' ); ?>
					<label class="nexus-indexnow-toggle"><input type="checkbox" name="indexnow_auto" value="1" <?php checked( $auto_enabled ); ?>><span><strong>IndexNow automatisch informieren</strong><small>Die API läuft per WP-Cron nach dem Speichern und blockiert den Editor nicht.</small></span></label>
					<button class="button" type="submit">Einstellung speichern</button>
				</form>
			</section>

			<section class="nexus-indexnow-card">
				<div class="nexus-indexnow-card__head"><span class="dashicons dashicons-shield" aria-hidden="true"></span><div><h2>Ownership-Key</h2><p>Wird von Suchmaschinen über deine Domain verifiziert.</p></div></div>
				<p class="nexus-indexnow-key-location"><code><?php echo esc_html( $key_location ); ?></code></p>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" onsubmit="return confirm('IndexNow-Key wirklich neu erzeugen?');">
					<input type="hidden" name="action" value="nexus_indexnow_regenerate_key">
					<input type="hidden" name="redirect_to" value="<?php echo esc_attr( nexus_indexnow_get_admin_url() ); ?>">
					<?php wp_nonce_field( 'nexus_indexnow_regenerate_key' ); ?>
					<button class="button" type="submit">Key neu erzeugen</button>
				</form>
			</section>
		</div>

		<section class="nexus-indexnow-card nexus-indexnow-history">
			<div class="nexus-indexnow-card__head"><span class="dashicons dashicons-backup" aria-hidden="true"></span><div><h2>Letzte Meldungen</h2><p>Bis zu 50 lokale Einträge aus manuellen und automatischen Requests.</p></div></div>
			<?php if ( empty( $history ) ) : ?>
				<div class="nexus-indexnow-empty"><span class="dashicons dashicons-clock" aria-hidden="true"></span><p>Noch keine URL an IndexNow gemeldet.</p></div>
			<?php else : ?>
				<div class="nexus-indexnow-table-wrap"><table class="widefat striped"><thead><tr><th>Zeit</th><th>URL</th><th>Quelle</th><th>Status</th></tr></thead><tbody>
				<?php foreach ( array_slice( $history, 0, 20 ) as $row ) : ?>
					<tr><td><?php echo esc_html( ! empty( $row['timestamp'] ) ? wp_date( 'd.m.Y H:i', absint( $row['timestamp'] ) ) : '—' ); ?></td><td><a href="<?php echo esc_url( (string) ( $row['url'] ?? '' ) ); ?>" target="_blank" rel="noreferrer noopener"><?php echo esc_html( (string) ( $row['url'] ?? '' ) ); ?></a></td><td><?php echo esc_html( (string) ( $row['source'] ?? 'manual' ) ); ?></td><td><span class="nexus-indexnow-result is-<?php echo esc_attr( (string) ( $row['status'] ?? 'error' ) ); ?>"><?php echo esc_html( 0 < absint( $row['code'] ?? 0 ) ? 'HTTP ' . absint( $row['code'] ) : 'Transport' ); ?></span></td></tr>
				<?php endforeach; ?>
				</tbody></table></div>
			<?php endif; ?>
		</section>
	</div>
	<?php
}
