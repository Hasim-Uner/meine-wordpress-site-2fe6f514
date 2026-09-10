<?php
/**
 * Content Intelligence runtime state hardening.
 *
 * Keeps the last usable 28-day GSC query/page snapshot across transient/cache
 * flushes and ensures only the V1.1 Opportunities renderer is attached.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the persistent fallback option for Content Intelligence GSC data.
 *
 * @return string
 */
function nexus_ci_gsc_snapshot_option_name() {
	return 'nexus_content_intelligence_gsc_snapshot_v1';
}

/**
 * Keep only the Search Console fields Content Intelligence actually needs.
 *
 * @param array<string,mixed> $snapshot Full SEO Cockpit snapshot.
 * @return array<string,mixed>
 */
function nexus_ci_compact_gsc_snapshot( $snapshot ) {
	if ( ! array_key_exists( 'query_page_rows', $snapshot ) ) {
		return [];
	}

	return [
		'generated_at'    => absint( $snapshot['generated_at'] ?? 0 ),
		'property'        => sanitize_text_field( (string) ( $snapshot['property'] ?? '' ) ),
		'range_days'      => absint( $snapshot['range_days'] ?? 28 ),
		'ranges'          => is_array( $snapshot['ranges'] ?? null ) ? $snapshot['ranges'] : [],
		'query_page_rows' => is_array( $snapshot['query_page_rows'] ?? null ) ? $snapshot['query_page_rows'] : [],
	];
}

/**
 * Persist the last usable GSC context without autoloading it on frontend requests.
 *
 * @param array<string,mixed> $snapshot Full or compact snapshot.
 * @return bool
 */
function nexus_ci_store_gsc_snapshot( $snapshot ) {
	$compact = nexus_ci_compact_gsc_snapshot( $snapshot );
	if ( empty( $compact ) ) {
		return false;
	}

	if ( false === get_option( nexus_ci_gsc_snapshot_option_name(), false ) ) {
		return (bool) add_option( nexus_ci_gsc_snapshot_option_name(), $compact, '', false );
	}

	return (bool) update_option( nexus_ci_gsc_snapshot_option_name(), $compact, false );
}

/**
 * Restore the transient used by V1/V1.1 from the persistent fallback after a
 * deploy, object-cache flush or transient expiry. A live API call is never made
 * while merely rendering Opportunities.
 *
 * @return void
 */
function nexus_ci_restore_gsc_snapshot_for_opportunities() {
	$page = isset( $_GET['page'] ) ? sanitize_key( (string) wp_unslash( $_GET['page'] ) ) : '';
	if ( ! function_exists( 'nexus_ci_admin_slug' ) || nexus_ci_admin_slug() !== $page ) {
		return;
	}
	if ( ! function_exists( 'nexus_get_seo_cockpit_snapshot_cache_key' ) ) {
		return;
	}

	$cache_key = nexus_get_seo_cockpit_snapshot_cache_key( 28 );
	$current   = get_transient( $cache_key );
	if ( is_array( $current ) && array_key_exists( 'query_page_rows', $current ) ) {
		nexus_ci_store_gsc_snapshot( $current );
		return;
	}

	$stored = get_option( nexus_ci_gsc_snapshot_option_name(), [] );
	if ( ! is_array( $stored ) || ! array_key_exists( 'query_page_rows', $stored ) ) {
		return;
	}

	$ttl = function_exists( 'nexus_get_seo_cockpit_refresh_interval_seconds' )
		? nexus_get_seo_cockpit_refresh_interval_seconds()
		: 12 * HOUR_IN_SECONDS;
	set_transient( $cache_key, $stored, max( HOUR_IN_SECONDS, $ttl ) );
}
add_action( 'admin_init', 'nexus_ci_restore_gsc_snapshot_for_opportunities', 30 );

/**
 * Remove the V1 page callback after V1.1 has registered the same page slug.
 * remove_submenu_page() only removes the menu row; it does not detach the old
 * page-hook callback, which caused both renderers to print on one screen.
 *
 * @return void
 */
function nexus_ci_remove_legacy_renderer_callback() {
	if ( ! function_exists( 'get_plugin_page_hookname' ) || ! function_exists( 'nexus_ci_admin_slug' ) ) {
		return;
	}

	$hook = get_plugin_page_hookname( nexus_ci_admin_slug(), nexus_get_seo_cockpit_menu_slug() );
	if ( is_string( $hook ) && '' !== $hook ) {
		remove_action( $hook, 'nexus_ci_render_admin_page' );
	}
}
add_action( 'admin_menu', 'nexus_ci_remove_legacy_renderer_callback', 99 );

/**
 * Save a freshly generated Search Console snapshot after the normal GSC cron.
 *
 * @return void
 */
function nexus_ci_capture_gsc_snapshot_after_cron() {
	if ( ! function_exists( 'nexus_get_seo_cockpit_snapshot_cache_key' ) ) {
		return;
	}
	$snapshot = get_transient( nexus_get_seo_cockpit_snapshot_cache_key( 28 ) );
	if ( is_array( $snapshot ) ) {
		nexus_ci_store_gsc_snapshot( $snapshot );
	}
}
add_action( 'nexus_seo_cockpit_refresh_snapshot', 'nexus_ci_capture_gsc_snapshot_after_cron', 20 );

/**
 * Reliable explicit Opportunities refresh: refresh Research now and ensure a
 * usable Search Console snapshot exists. GSC is only queried live when the
 * transient is missing; otherwise the current snapshot is persisted as fallback.
 *
 * @return void
 */
function nexus_ci_handle_manual_refresh_with_gsc() {
	if ( ! nexus_current_user_can_manage_seo_cockpit() ) {
		wp_die( 'Nicht erlaubt.' );
	}

	check_admin_referer( 'nexus_ci_refresh' );
	$before = function_exists( 'nexus_ci_persisted_observation_count' ) ? nexus_ci_persisted_observation_count() : 0;

	delete_transient( 'nexus_seo_cockpit_research_schedule_lock' );
	delete_transient( 'nexus_seo_cockpit_research_refresh_lock' );
	wp_clear_scheduled_hook( 'nexus_seo_cockpit_research_background_refresh' );
	do_action( 'nexus_seo_cockpit_research_background_refresh' );

	$after  = function_exists( 'nexus_ci_persisted_observation_count' ) ? nexus_ci_persisted_observation_count() : 0;
	$notice = $after > 0 ? 'refresh_done' : 'refresh_no_data';
	if ( $after > $before ) {
		$notice = 'refresh_new_data';
	}

	$gsc_status = 'missing';
	if ( function_exists( 'nexus_get_seo_cockpit_snapshot_cache_key' ) ) {
		$cached = get_transient( nexus_get_seo_cockpit_snapshot_cache_key( 28 ) );
		if ( is_array( $cached ) && array_key_exists( 'query_page_rows', $cached ) ) {
			nexus_ci_store_gsc_snapshot( $cached );
			$gsc_status = 'cached';
		} elseif ( function_exists( 'nexus_run_seo_cockpit_sync' ) ) {
			$result = nexus_run_seo_cockpit_sync( 'content_intelligence', 28 );
			if ( is_wp_error( $result ) ) {
				$gsc_status = 'error';
			} elseif ( is_array( $result ) && nexus_ci_store_gsc_snapshot( $result ) ) {
				$gsc_status = 'updated';
			}
		}
	}

	wp_safe_redirect(
		add_query_arg(
			[
				'page'      => nexus_ci_admin_slug(),
				'ci_notice' => $notice,
				'ci_gsc'    => $gsc_status,
			],
			admin_url( 'admin.php' )
		)
	);
	exit;
}

remove_action( 'admin_post_nexus_ci_refresh', 'nexus_ci_handle_manual_refresh' );
add_action( 'admin_post_nexus_ci_refresh', 'nexus_ci_handle_manual_refresh_with_gsc' );

/**
 * Show only actionable GSC refresh diagnostics; cached state needs no extra box.
 *
 * @return void
 */
function nexus_ci_render_gsc_refresh_notice() {
	$page = isset( $_GET['page'] ) ? sanitize_key( (string) wp_unslash( $_GET['page'] ) ) : '';
	if ( ! function_exists( 'nexus_ci_admin_slug' ) || nexus_ci_admin_slug() !== $page ) {
		return;
	}

	$status = isset( $_GET['ci_gsc'] ) ? sanitize_key( (string) wp_unslash( $_GET['ci_gsc'] ) ) : '';
	if ( 'updated' === $status ) {
		echo '<div class="notice notice-success is-dismissible"><p>Search Console wurde ebenfalls aktualisiert und für Content Intelligence gespeichert.</p></div>';
	} elseif ( 'error' === $status ) {
		echo '<div class="notice notice-warning is-dismissible"><p>Research ist aktuell, aber Search Console konnte nicht synchronisiert werden. Prüfe im SEO Cockpit den Bereich Search Console.</p></div>';
	} elseif ( 'missing' === $status ) {
		echo '<div class="notice notice-warning is-dismissible"><p>Research ist aktuell, aber es steht noch kein Search-Console-Snapshot für Content Intelligence bereit.</p></div>';
	}
}
add_action( 'admin_notices', 'nexus_ci_render_gsc_refresh_notice', 20 );
