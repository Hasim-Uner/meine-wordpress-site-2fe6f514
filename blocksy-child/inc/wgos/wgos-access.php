<?php
/**
 * Internal WGOS client dashboard access control.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const HU_WGOS_ACCESS_VERSION = '2026-05-19-1';
const HU_WGOS_CLIENT_ROLE    = 'wgos_client';
const HU_WGOS_DASHBOARD_CAP  = 'view_wgos_dashboard';

/**
 * Register the minimal client role and dashboard capability.
 *
 * @return void
 */
function hu_register_wgos_client_access() {
	$allowed_caps = [
		'read'                 => true,
		HU_WGOS_DASHBOARD_CAP => true,
	];
	$role = get_role( HU_WGOS_CLIENT_ROLE );

	if ( ! $role ) {
		add_role(
			HU_WGOS_CLIENT_ROLE,
			'WGOS Client',
			$allowed_caps
		);
		return;
	}

	foreach ( array_keys( (array) $role->capabilities ) as $capability ) {
		if ( ! isset( $allowed_caps[ $capability ] ) ) {
			$role->remove_cap( $capability );
		}
	}

	foreach ( $allowed_caps as $capability => $grant ) {
		if ( ! $role->has_cap( $capability ) ) {
			$role->add_cap( $capability, $grant );
		}
	}
}

/**
 * Run role/cap registration once per access-version on existing installs.
 *
 * @return void
 */
function hu_maybe_register_wgos_client_access() {
	if ( HU_WGOS_ACCESS_VERSION === get_option( 'hu_wgos_access_version' ) ) {
		return;
	}

	hu_register_wgos_client_access();
	update_option( 'hu_wgos_access_version', HU_WGOS_ACCESS_VERSION, false );
}
add_action( 'after_switch_theme', 'hu_register_wgos_client_access' );
add_action( 'init', 'hu_maybe_register_wgos_client_access', 5 );

/**
 * Return the internal dashboard URL.
 *
 * @return string
 */
function hu_get_wgos_dashboard_url() {
	$page_id = function_exists( 'nexus_get_wgos_page_id' ) ? nexus_get_wgos_page_id() : 0;

	if ( $page_id ) {
		$url = get_permalink( $page_id );

		if ( $url ) {
			return $url;
		}
	}

	return home_url( '/wgos/' );
}

/**
 * Whether the current user may view the internal dashboard.
 *
 * @return bool
 */
function hu_current_user_can_view_wgos_dashboard() {
	return current_user_can( 'manage_options' ) || current_user_can( HU_WGOS_DASHBOARD_CAP );
}

/**
 * Enforce dashboard access for page templates.
 *
 * @param string $redirect_url URL used after successful login.
 * @return void
 */
function hu_require_wgos_dashboard_access( $redirect_url = '' ) {
	$redirect_url = '' !== $redirect_url ? $redirect_url : hu_get_wgos_dashboard_url();

	if ( ! is_user_logged_in() ) {
		wp_safe_redirect( wp_login_url( $redirect_url ) );
		exit;
	}

	if ( hu_current_user_can_view_wgos_dashboard() ) {
		return;
	}

	wp_safe_redirect( home_url( '/' ) );
	exit;
}

/**
 * Keep client users out of the normal WordPress backend.
 *
 * @return void
 */
function hu_redirect_wgos_clients_from_admin() {
	if ( ! is_user_logged_in() || current_user_can( 'manage_options' ) || ! current_user_can( HU_WGOS_DASHBOARD_CAP ) ) {
		return;
	}

	if ( wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return;
	}

	wp_safe_redirect( hu_get_wgos_dashboard_url() );
	exit;
}
add_action( 'admin_init', 'hu_redirect_wgos_clients_from_admin' );

/**
 * Hide the admin bar for dashboard clients.
 *
 * @param bool $show Whether to show the admin bar.
 * @return bool
 */
function hu_hide_wgos_client_admin_bar( $show ) {
	if ( is_user_logged_in() && current_user_can( HU_WGOS_DASHBOARD_CAP ) && ! current_user_can( 'manage_options' ) ) {
		return false;
	}

	return $show;
}
add_filter( 'show_admin_bar', 'hu_hide_wgos_client_admin_bar' );
