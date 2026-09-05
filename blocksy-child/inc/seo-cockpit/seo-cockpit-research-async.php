<?php
/**
 * SEO Cockpit Research Intelligence: non-blocking admin renderer.
 *
 * Research provider APIs are intentionally never fetched synchronously while
 * the admin page is rendering. Existing transients are shown immediately and
 * missing/expired provider caches are refreshed through a background cron run.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the provider hosts that may be short-circuited during page render.
 *
 * @return array<int, string>
 */
function nexus_get_seo_cockpit_research_provider_hosts_async() {
	return [
		'chromeuxreport.googleapis.com',
		'api.energy-charts.info',
		'genesis.destatis.de',
		'ec.europa.eu',
	];
}

/**
 * Prevent live provider HTTP calls only while the Research page is rendering.
 *
 * Cached provider responses are returned before WordPress reaches this filter,
 * so this only converts cache misses into a fast, explicit empty state.
 *
 * @param mixed               $preempt Existing preempted response.
 * @param array<string,mixed> $parsed_args HTTP request args.
 * @param string              $url Request URL.
 * @return mixed
 */
function nexus_preempt_seo_cockpit_research_provider_http( $preempt, $parsed_args, $url ) {
	unset( $parsed_args );

	if ( empty( $GLOBALS['nexus_seo_cockpit_research_cache_only'] ) ) {
		return $preempt;
	}

	$host = strtolower( (string) wp_parse_url( (string) $url, PHP_URL_HOST ) );
	if ( '' === $host || ! in_array( $host, nexus_get_seo_cockpit_research_provider_hosts_async(), true ) ) {
		return $preempt;
	}

	return new WP_Error(
		'nexus_research_cache_miss',
		'Noch keine Cache-Daten. Die Quelle wird im Hintergrund aktualisiert; bitte die Research-Seite in einigen Sekunden neu laden.'
	);
}

/**
 * Queue one background refresh without delaying the admin request.
 *
 * @return void
 */
function nexus_schedule_seo_cockpit_research_background_refresh() {
	$hook = 'nexus_seo_cockpit_research_background_refresh';

	if ( false === wp_next_scheduled( $hook ) ) {
		wp_schedule_single_event( time(), $hook );
	}

	// The loopback request is non-blocking. If WP-Cron is disabled, the page
	// still renders instantly and the next normal cron runner can pick it up.
	if ( function_exists( 'spawn_cron' ) ) {
		spawn_cron( time() );
	}
}

/**
 * Populate missing or expired Research provider caches in the background.
 *
 * Existing provider functions retain their own TTLs and therefore make no
 * remote request while their transient is still valid.
 *
 * @return void
 */
function nexus_run_seo_cockpit_research_background_refresh() {
	$lock_key = 'nexus_seo_cockpit_research_refresh_lock';
	if ( get_transient( $lock_key ) ) {
		return;
	}

	set_transient( $lock_key, '1', 5 * MINUTE_IN_SECONDS );

	try {
		$api_key = function_exists( 'nexus_get_seo_cockpit_crux_api_key' )
			? nexus_get_seo_cockpit_crux_api_key()
			: '';

		if ( '' !== $api_key && function_exists( 'nexus_get_seo_cockpit_crux_record' ) ) {
			nexus_get_seo_cockpit_crux_record( 'PHONE', false );
			nexus_get_seo_cockpit_crux_record( 'PHONE', true );
			nexus_get_seo_cockpit_crux_record( 'DESKTOP', false );
			nexus_get_seo_cockpit_crux_record( 'DESKTOP', true );
		}

		if ( function_exists( 'nexus_get_seo_cockpit_energy_charts_summary' ) ) {
			nexus_get_seo_cockpit_energy_charts_summary();
		}

		$destatis_token = function_exists( 'nexus_get_seo_cockpit_destatis_api_token' )
			? nexus_get_seo_cockpit_destatis_api_token()
			: '';

		if ( '' !== $destatis_token && function_exists( 'nexus_get_seo_cockpit_destatis_summary' ) ) {
			nexus_get_seo_cockpit_destatis_summary();
		}

		if ( function_exists( 'nexus_get_seo_cockpit_eurostat_summary' ) ) {
			nexus_get_seo_cockpit_eurostat_summary();
		}

		update_option( 'nexus_seo_cockpit_research_last_background_refresh', time(), false );
	} finally {
		delete_transient( $lock_key );
	}
}
add_action( 'nexus_seo_cockpit_research_background_refresh', 'nexus_run_seo_cockpit_research_background_refresh' );

/**
 * Register the fast Research page renderer.
 *
 * @return void
 */
function nexus_register_seo_cockpit_research_page_async() {
	add_submenu_page(
		nexus_get_seo_cockpit_menu_slug(),
		'Research Intelligence',
		'Research',
		nexus_get_seo_cockpit_view_cap(),
		nexus_get_seo_cockpit_research_slug(),
		'nexus_render_seo_cockpit_research_page_async'
	);
}
remove_action( 'admin_menu', 'nexus_register_seo_cockpit_research_page_v3', 40 );
add_action( 'admin_menu', 'nexus_register_seo_cockpit_research_page_async', 40 );

/**
 * Render Research immediately from cache and refresh providers in background.
 *
 * @return void
 */
function nexus_render_seo_cockpit_research_page_async() {
	if ( ! nexus_current_user_can_view_seo_cockpit() ) {
		wp_die( 'Nicht erlaubt.' );
	}

	if ( ! function_exists( 'nexus_render_seo_cockpit_research_page_v3' ) ) {
		wp_die( 'Research Intelligence ist nicht vollständig geladen.' );
	}

	nexus_schedule_seo_cockpit_research_background_refresh();

	$GLOBALS['nexus_seo_cockpit_research_cache_only'] = true;
	add_filter( 'pre_http_request', 'nexus_preempt_seo_cockpit_research_provider_http', 10, 3 );

	try {
		nexus_render_seo_cockpit_research_page_v3();
	} finally {
		remove_filter( 'pre_http_request', 'nexus_preempt_seo_cockpit_research_provider_http', 10 );
		unset( $GLOBALS['nexus_seo_cockpit_research_cache_only'] );
	}
}
