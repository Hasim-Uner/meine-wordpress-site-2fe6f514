<?php
/**
 * Search Console write controls and sitemap synchronization for the SEO Cockpit.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the Search Console scope required for sitemap management.
 *
 * @return string
 */
function nexus_get_seo_cockpit_write_scope() {
	return 'https://www.googleapis.com/auth/webmasters';
}

/**
 * Return the state option used by Search Console control actions.
 *
 * @return string
 */
function nexus_get_seo_cockpit_gsc_control_option_name() {
	return 'nexus_seo_cockpit_gsc_control';
}

/**
 * Return persisted Search Console control state.
 *
 * @return array<string, mixed>
 */
function nexus_get_seo_cockpit_gsc_control_state() {
	$state = get_option( nexus_get_seo_cockpit_gsc_control_option_name(), [] );

	return is_array( $state ) ? $state : [];
}

/**
 * Persist Search Console control state.
 *
 * @param array<string, mixed> $state State payload.
 * @return void
 */
function nexus_update_seo_cockpit_gsc_control_state( $state ) {
	update_option( nexus_get_seo_cockpit_gsc_control_option_name(), (array) $state, false );
}

/**
 * Determine whether the stored token includes Search Console write access.
 *
 * @return bool
 */
function nexus_seo_cockpit_has_write_scope() {
	$tokens = nexus_get_seo_cockpit_tokens();
	$scope  = trim( (string) ( $tokens['scope'] ?? '' ) );

	if ( '' === $scope ) {
		return false;
	}

	$scopes = preg_split( '/\s+/', $scope );
	$scopes = is_array( $scopes ) ? $scopes : [];

	return in_array( nexus_get_seo_cockpit_write_scope(), $scopes, true );
}

/**
 * Use the write-capable Search Console scope for the existing connect action.
 *
 * This deliberately reuses the established OAuth state/callback/token flow.
 * Existing read-only tokens continue to work for reports until the connection
 * is authorized again.
 *
 * @return void
 */
function nexus_handle_seo_cockpit_connect_with_write_scope() {
	if ( ! nexus_current_user_can_manage_seo_cockpit() ) {
		wp_die( 'Nicht erlaubt.' );
	}

	check_admin_referer( 'nexus_seo_cockpit_connect' );

	if ( ! nexus_has_seo_cockpit_search_console_credentials() ) {
		wp_safe_redirect( nexus_get_seo_cockpit_dashboard_url( [ 'nexus_seo_notice' => 'missing_credentials' ] ) );
		exit;
	}

	$config = nexus_get_seo_cockpit_search_console_config();
	$state  = nexus_create_seo_cockpit_oauth_state();
	$params = [
		'client_id'              => $config['client_id'],
		'redirect_uri'           => $config['redirect_uri'],
		'response_type'          => 'code',
		'scope'                  => nexus_get_seo_cockpit_write_scope(),
		'access_type'            => 'offline',
		'include_granted_scopes' => 'true',
		'prompt'                 => 'consent',
		'state'                  => $state,
	];

	wp_redirect( 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query( $params, '', '&', PHP_QUERY_RFC3986 ) );
	exit;
}

remove_action( 'admin_post_nexus_seo_cockpit_connect', 'nexus_handle_seo_cockpit_connect_action' );
add_action( 'admin_post_nexus_seo_cockpit_connect', 'nexus_handle_seo_cockpit_connect_with_write_scope' );

/**
 * Return the canonical WordPress sitemap URL managed by the cockpit.
 *
 * @return string
 */
function nexus_get_seo_cockpit_managed_sitemap_url() {
	$url = home_url( '/wp-sitemap.xml' );

	/**
	 * Filter the sitemap URL submitted through Search Console.
	 *
	 * @param string $url Default native WordPress sitemap URL.
	 */
	$filtered = apply_filters( 'nexus_seo_cockpit_managed_sitemap_url', $url );
	$filtered = esc_url_raw( (string) $filtered );

	return '' !== $filtered ? $filtered : esc_url_raw( $url );
}

/**
 * Record the outcome of a sitemap submission.
 *
 * @param string        $source Submission source.
 * @param string        $status Status key.
 * @param string        $message Human-readable detail.
 * @param string        $sitemap Sitemap URL.
 * @param array<string, mixed> $extra Optional additional state.
 * @return void
 */
function nexus_record_seo_cockpit_sitemap_submit( $source, $status, $message, $sitemap, $extra = [] ) {
	$state = nexus_get_seo_cockpit_gsc_control_state();
	$state = array_merge(
		$state,
		[
			'last_submit_at'      => current_time( 'timestamp' ),
			'last_submit_source'  => sanitize_key( (string) $source ),
			'last_submit_status'  => sanitize_key( (string) $status ),
			'last_submit_message' => sanitize_text_field( (string) $message ),
			'last_sitemap_url'    => esc_url_raw( (string) $sitemap ),
		],
		(array) $extra
	);

	nexus_update_seo_cockpit_gsc_control_state( $state );
}

/**
 * Submit the native WordPress sitemap to Search Console.
 *
 * @param string $source Submission source: manual, content, or deploy.
 * @return true|WP_Error
 */
function nexus_submit_seo_cockpit_sitemap( $source = 'manual' ) {
	$property = nexus_get_seo_cockpit_property();
	$sitemap  = nexus_get_seo_cockpit_managed_sitemap_url();

	if ( '' === $property ) {
		return new WP_Error( 'nexus_seo_missing_property', 'Es ist noch keine Search-Console-Property hinterlegt.' );
	}

	if ( ! nexus_seo_cockpit_has_write_scope() ) {
		return new WP_Error( 'nexus_seo_write_scope_required', 'Search Console ist nur lesend verbunden. Bitte Google einmal neu autorisieren.' );
	}

	if ( '' === $sitemap ) {
		return new WP_Error( 'nexus_seo_missing_sitemap', 'Die WordPress-Sitemap konnte nicht bestimmt werden.' );
	}

	$lock_key = 'nexus_seo_cockpit_sitemap_submit_lock';
	if ( get_transient( $lock_key ) ) {
		return new WP_Error( 'nexus_seo_sitemap_submit_locked', 'Eine Sitemap-Synchronisierung läuft bereits.' );
	}

	set_transient( $lock_key, 1, 2 * MINUTE_IN_SECONDS );

	$response = nexus_seo_cockpit_search_console_request(
		'PUT',
		'/sites/' . rawurlencode( $property ) . '/sitemaps/' . rawurlencode( $sitemap )
	);

	delete_transient( $lock_key );

	if ( is_wp_error( $response ) ) {
		nexus_record_seo_cockpit_sitemap_submit(
			$source,
			'error',
			$response->get_error_message(),
			$sitemap
		);

		return $response;
	}

	$control_state = nexus_get_seo_cockpit_gsc_control_state();
	$extra         = [
		'pending_content_at' => 0,
		'pending_post_id'    => 0,
	];

	if ( ! empty( $control_state['pending_deploy_sha'] ) ) {
		$extra['last_deploy_sha']    = sanitize_text_field( (string) $control_state['pending_deploy_sha'] );
		$extra['pending_deploy_sha'] = '';
	}

	nexus_record_seo_cockpit_sitemap_submit(
		$source,
		'success',
		'Sitemap erfolgreich an Search Console übergeben.',
		$sitemap,
		$extra
	);

	delete_transient( nexus_get_seo_cockpit_cache_key( 'sitemaps', [ $property ] ) );

	return true;
}

/**
 * Handle a manual sitemap submission from WordPress admin.
 *
 * @return void
 */
function nexus_handle_seo_cockpit_submit_sitemap_action() {
	if ( ! nexus_current_user_can_manage_seo_cockpit() ) {
		wp_die( 'Nicht erlaubt.' );
	}

	check_admin_referer( 'nexus_seo_cockpit_submit_sitemap' );

	$result = nexus_submit_seo_cockpit_sitemap( 'manual' );
	$notice = is_wp_error( $result ) ? 'submit_failed' : 'submit_success';

	wp_safe_redirect(
		add_query_arg(
			'nexus_gsc_notice',
			$notice,
			nexus_get_seo_cockpit_search_console_control_url()
		)
	);
	exit;
}
add_action( 'admin_post_nexus_seo_cockpit_submit_sitemap', 'nexus_handle_seo_cockpit_submit_sitemap_action' );

/**
 * Return the Search Console control submenu URL.
 *
 * @return string
 */
function nexus_get_seo_cockpit_search_console_control_url() {
	return admin_url( 'admin.php?page=nexus-seo-cockpit-search-console' );
}

/**
 * Mark the sitemap dirty after a public content transition.
 *
 * @param string  $new_status New post status.
 * @param string  $old_status Previous post status.
 * @param WP_Post $post Post object.
 * @return void
 */
function nexus_mark_seo_cockpit_sitemap_dirty( $new_status, $old_status, $post ) {
	if ( ! ( $post instanceof WP_Post ) ) {
		return;
	}

	if ( wp_is_post_revision( $post->ID ) || wp_is_post_autosave( $post->ID ) ) {
		return;
	}

	if ( 'publish' !== $new_status && 'publish' !== $old_status ) {
		return;
	}

	$post_type = get_post_type_object( $post->post_type );
	if ( ! $post_type || ! is_post_type_viewable( $post_type ) ) {
		return;
	}

	$state                       = nexus_get_seo_cockpit_gsc_control_state();
	$state['pending_content_at'] = current_time( 'timestamp' );
	$state['pending_post_id']    = absint( $post->ID );
	nexus_update_seo_cockpit_gsc_control_state( $state );

	if ( ! wp_next_scheduled( 'nexus_seo_cockpit_submit_sitemap_event' ) ) {
		wp_schedule_single_event( time() + ( 5 * MINUTE_IN_SECONDS ), 'nexus_seo_cockpit_submit_sitemap_event' );
	}
}
add_action( 'transition_post_status', 'nexus_mark_seo_cockpit_sitemap_dirty', 20, 3 );

/**
 * Process a queued sitemap submission.
 *
 * @return void
 */
function nexus_run_seo_cockpit_sitemap_submit_event() {
	$state = nexus_get_seo_cockpit_gsc_control_state();

	if ( empty( $state['pending_content_at'] ) && empty( $state['pending_deploy_sha'] ) ) {
		return;
	}

	if ( ! nexus_seo_cockpit_has_write_scope() ) {
		return;
	}

	$source = ! empty( $state['pending_deploy_sha'] ) ? 'deploy' : 'content';
	$result = nexus_submit_seo_cockpit_sitemap( $source );

	if ( is_wp_error( $result ) && ! wp_next_scheduled( 'nexus_seo_cockpit_submit_sitemap_event' ) ) {
		wp_schedule_single_event( time() + HOUR_IN_SECONDS, 'nexus_seo_cockpit_submit_sitemap_event' );
	}
}
add_action( 'nexus_seo_cockpit_submit_sitemap_event', 'nexus_run_seo_cockpit_sitemap_submit_event' );

/**
 * Ensure pending work resumes after Search Console write access becomes available.
 *
 * @return void
 */
function nexus_maybe_schedule_pending_seo_cockpit_sitemap_submit() {
	if ( ! is_admin() || ! nexus_seo_cockpit_has_write_scope() ) {
		return;
	}

	$state = nexus_get_seo_cockpit_gsc_control_state();
	if ( empty( $state['pending_content_at'] ) && empty( $state['pending_deploy_sha'] ) ) {
		return;
	}

	if ( ! wp_next_scheduled( 'nexus_seo_cockpit_submit_sitemap_event' ) ) {
		wp_schedule_single_event( time() + MINUTE_IN_SECONDS, 'nexus_seo_cockpit_submit_sitemap_event' );
	}
}
add_action( 'admin_init', 'nexus_maybe_schedule_pending_seo_cockpit_sitemap_submit', 30 );

/**
 * Read the deployment marker shipped with the built child theme.
 *
 * @return string
 */
function nexus_get_seo_cockpit_deploy_marker_sha() {
	$path = trailingslashit( get_stylesheet_directory() ) . '.nexus-deploy-sha';

	if ( ! is_readable( $path ) ) {
		return '';
	}

	$sha = strtolower( trim( (string) file_get_contents( $path ) ) );

	return preg_match( '/^[a-f0-9]{7,40}$/', $sha ) ? $sha : '';
}

/**
 * Detect a newly deployed theme revision during WP-Cron and sync its sitemap.
 *
 * @return void
 */
function nexus_maybe_sync_seo_cockpit_after_deploy() {
	if ( ! wp_doing_cron() ) {
		return;
	}

	$sha = nexus_get_seo_cockpit_deploy_marker_sha();
	if ( '' === $sha ) {
		return;
	}

	$state = nexus_get_seo_cockpit_gsc_control_state();
	if ( (string) ( $state['last_deploy_sha'] ?? '' ) === $sha ) {
		return;
	}

	$state['pending_deploy_sha'] = $sha;
	nexus_update_seo_cockpit_gsc_control_state( $state );

	if ( nexus_seo_cockpit_has_write_scope() ) {
		nexus_submit_seo_cockpit_sitemap( 'deploy' );
	}
}
add_action( 'init', 'nexus_maybe_sync_seo_cockpit_after_deploy', 40 );

/**
 * Register the Search Console control submenu.
 *
 * @return void
 */
function nexus_register_seo_cockpit_search_console_control_page() {
	add_submenu_page(
		nexus_get_seo_cockpit_menu_slug(),
		'Search Console Steuerung',
		'Search Console',
		nexus_get_seo_cockpit_manage_cap(),
		'nexus-seo-cockpit-search-console',
		'nexus_render_seo_cockpit_search_console_control_page'
	);
}
add_action( 'admin_menu', 'nexus_register_seo_cockpit_search_console_control_page', 30 );

/**
 * Render the Search Console control page.
 *
 * @return void
 */
function nexus_render_seo_cockpit_search_console_control_page() {
	if ( ! nexus_current_user_can_manage_seo_cockpit() ) {
		wp_die( 'Nicht erlaubt.' );
	}

	$setup       = nexus_get_seo_cockpit_setup_state();
	$tokens      = nexus_get_seo_cockpit_tokens();
	$state       = nexus_get_seo_cockpit_gsc_control_state();
	$has_write   = nexus_seo_cockpit_has_write_scope();
	$sitemap_url = nexus_get_seo_cockpit_managed_sitemap_url();
	$notice      = isset( $_GET['nexus_gsc_notice'] ) ? sanitize_key( (string) wp_unslash( $_GET['nexus_gsc_notice'] ) ) : '';
	$last_submit = absint( $state['last_submit_at'] ?? 0 );
	$scope       = trim( (string) ( $tokens['scope'] ?? '' ) );
	?>
	<div class="wrap">
		<h1>Search Console Steuerung</h1>
		<p>Das SEO Cockpit nutzt die bestehende Google-Verbindung. Performance-Daten und URL Inspection bleiben unverändert; zusätzlich kann die WordPress-Sitemap kontrolliert an Search Console übergeben werden.</p>

		<?php if ( 'submit_success' === $notice ) : ?>
			<div class="notice notice-success is-dismissible"><p>Sitemap wurde erfolgreich an Search Console übergeben.</p></div>
		<?php elseif ( 'submit_failed' === $notice ) : ?>
			<div class="notice notice-error is-dismissible"><p>Sitemap konnte nicht übergeben werden. Den letzten Fehler findest du unten im Status.</p></div>
		<?php endif; ?>

		<table class="widefat striped" style="max-width: 980px; margin-top: 20px;">
			<tbody>
				<tr>
					<th style="width: 220px;">Property</th>
					<td><?php echo esc_html( (string) ( $setup['config']['property'] ?? '' ) ); ?></td>
				</tr>
				<tr>
					<th>Google-Verbindung</th>
					<td><?php echo ! empty( $setup['is_connected'] ) ? 'verbunden' : 'nicht verbunden'; ?></td>
				</tr>
				<tr>
					<th>Schreibzugriff</th>
					<td><strong><?php echo $has_write ? 'aktiv' : 'nicht aktiv'; ?></strong><?php echo '' !== $scope ? ' · ' . esc_html( $scope ) : ''; ?></td>
				</tr>
				<tr>
					<th>Verwaltete Sitemap</th>
					<td><code><?php echo esc_html( $sitemap_url ); ?></code></td>
				</tr>
				<tr>
					<th>Letzte Übergabe</th>
					<td>
						<?php
						if ( $last_submit > 0 ) {
							echo esc_html( wp_date( 'd.m.Y H:i', $last_submit ) );
							echo ' · ' . esc_html( (string) ( $state['last_submit_status'] ?? '' ) );
							echo ' · ' . esc_html( (string) ( $state['last_submit_source'] ?? '' ) );
						} else {
							echo 'noch keine';
						}
						?>
					</td>
				</tr>
				<tr>
					<th>Letzte Meldung</th>
					<td><?php echo esc_html( (string) ( $state['last_submit_message'] ?? '—' ) ); ?></td>
				</tr>
				<tr>
					<th>Ausstehend</th>
					<td>
						<?php
						$pending = [];
						if ( ! empty( $state['pending_content_at'] ) ) {
							$pending[] = 'Content-Änderung';
						}
						if ( ! empty( $state['pending_deploy_sha'] ) ) {
							$pending[] = 'Deploy ' . substr( (string) $state['pending_deploy_sha'], 0, 8 );
						}
						echo esc_html( empty( $pending ) ? 'nichts' : implode( ', ', $pending ) );
						?>
					</td>
				</tr>
			</tbody>
		</table>

		<div style="margin-top: 24px; display: flex; gap: 10px; flex-wrap: wrap;">
			<?php if ( ! $has_write ) : ?>
				<a class="button button-primary" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=nexus_seo_cockpit_connect' ), 'nexus_seo_cockpit_connect' ) ); ?>">Google neu autorisieren</a>
			<?php else : ?>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<input type="hidden" name="action" value="nexus_seo_cockpit_submit_sitemap">
					<?php wp_nonce_field( 'nexus_seo_cockpit_submit_sitemap' ); ?>
					<button type="submit" class="button button-primary">Sitemap jetzt an Google senden</button>
				</form>
			<?php endif; ?>
			<a class="button" href="<?php echo esc_url( nexus_get_seo_cockpit_dashboard_url() ); ?>">Zurück zum SEO Cockpit</a>
		</div>

		<p style="max-width: 980px; margin-top: 20px;">Automatik: Veröffentlichte Inhalte werden gebündelt nach wenigen Minuten synchronisiert. Nach einem erfolgreichen Theme-Deploy erkennt das Cockpit den ausgelieferten Commit und übergibt die Sitemap ebenfalls. Die URL-Inspection-API prüft weiterhin nur den Google-Indexstatus; sie stellt keinen allgemeinen API-Endpunkt für „Indexierung beantragen“ bereit.</p>
	</div>
	<?php
}
