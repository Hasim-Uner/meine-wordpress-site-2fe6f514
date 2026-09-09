<?php
/**
 * Reliable manual refresh for Content Intelligence.
 *
 * The regular Research page remains asynchronous. The explicit
 * "Research aktualisieren" button in Opportunities, however, must work even
 * when WP-Cron/loopback execution is unavailable on the host. Therefore this
 * handler executes the already-registered Research refresh action in the
 * current authenticated admin request and redirects with an explicit result.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Count persisted Content Intelligence observations.
 *
 * @return int
 */
function nexus_ci_persisted_observation_count() {
	if ( ! function_exists( 'nexus_ci_get_history' ) ) {
		return 0;
	}

	$count = 0;
	foreach ( nexus_ci_get_history() as $rows ) {
		$count += is_array( $rows ) ? count( $rows ) : 0;
	}
	return $count;
}

/**
 * Replace the queue-only button handler with a deterministic manual refresh.
 *
 * @return void
 */
function nexus_ci_handle_manual_refresh() {
	if ( ! nexus_current_user_can_manage_seo_cockpit() ) {
		wp_die( 'Nicht erlaubt.' );
	}

	check_admin_referer( 'nexus_ci_refresh' );

	$before = nexus_ci_persisted_observation_count();

	// A previously queued cron event may still exist. The explicit admin action
	// is authoritative, so remove the stale queue/locks before running once now.
	delete_transient( 'nexus_seo_cockpit_research_schedule_lock' );
	delete_transient( 'nexus_seo_cockpit_research_refresh_lock' );
	wp_clear_scheduled_hook( 'nexus_seo_cockpit_research_background_refresh' );

	// This action already has the provider refresh at priority 10 and the
	// Content Intelligence snapshot capture at priority 20. Running the action
	// directly preserves exactly the same pipeline without depending on WP-Cron.
	do_action( 'nexus_seo_cockpit_research_background_refresh' );

	$after  = nexus_ci_persisted_observation_count();
	$notice = $after > 0 ? 'refresh_done' : 'refresh_no_data';
	if ( $after > $before ) {
		$notice = 'refresh_new_data';
	}

	wp_safe_redirect( admin_url( 'admin.php?page=' . nexus_ci_admin_slug() . '&ci_notice=' . $notice ) );
	exit;
}

remove_action( 'admin_post_nexus_ci_refresh', 'nexus_ci_handle_refresh' );
add_action( 'admin_post_nexus_ci_refresh', 'nexus_ci_handle_manual_refresh' );

/**
 * Explain the refresh result directly on the Opportunities screen.
 *
 * @return void
 */
function nexus_ci_render_refresh_notice() {
	$page = isset( $_GET['page'] ) ? sanitize_key( (string) wp_unslash( $_GET['page'] ) ) : '';
	if ( nexus_ci_admin_slug() !== $page ) {
		return;
	}

	$notice = isset( $_GET['ci_notice'] ) ? sanitize_key( (string) wp_unslash( $_GET['ci_notice'] ) ) : '';
	if ( '' === $notice ) {
		return;
	}

	$class   = 'notice notice-info is-dismissible';
	$message = '';

	if ( 'refresh_new_data' === $notice ) {
		$class   = 'notice notice-success is-dismissible';
		$message = 'Research wurde aktualisiert und neue Primärdaten wurden als Snapshots gespeichert.';
	} elseif ( 'refresh_done' === $notice ) {
		$class   = 'notice notice-success is-dismissible';
		$message = 'Research wurde erfolgreich ausgeführt. Es lagen keine neuen Werte gegenüber dem letzten Snapshot vor.';
	} elseif ( 'refresh_no_data' === $notice ) {
		$class   = 'notice notice-warning is-dismissible';
		$message = 'Der Refresh wurde ausgeführt, aber keine verwertbaren Primärdaten konnten gespeichert werden. Prüfe im Bereich Research die Provider-Statusmeldungen für Energy-Charts, Destatis und Eurostat.';
	} elseif ( 'queued' === $notice ) {
		$message = 'Ein älterer asynchroner Refresh wurde nur eingeplant. Mit der aktuellen Version führt der Button den Refresh direkt aus und ist nicht mehr von WP-Cron abhängig.';
	}

	if ( '' !== $message ) {
		printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
	}
}
add_action( 'admin_notices', 'nexus_ci_render_refresh_notice' );
