<?php
/**
 * SEO Cockpit Site Audit runtime fallback.
 *
 * Keeps audit progress moving even when host-level WP-Cron is delayed or
 * disabled. While the Site Audit admin screen is open, the browser sends one
 * authenticated background tick per page load. The existing transient lock in
 * the audit engine prevents concurrent batches.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Limit admin-driven fallback ticks to one URL per request. */
function nexus_seo_audit_admin_tick_batch_size() {
	return 1;
}

/** Process one authenticated audit tick from wp-admin. */
function nexus_seo_audit_ajax_tick() {
	if ( ! function_exists( 'nexus_current_user_can_manage_seo_cockpit' ) || ! nexus_current_user_can_manage_seo_cockpit() ) {
		wp_send_json_error( [ 'message' => 'Nicht erlaubt.' ], 403 );
	}

	check_ajax_referer( 'nexus_seo_audit_tick', 'nonce' );

	$run_id = isset( $_POST['run_id'] ) ? sanitize_text_field( (string) wp_unslash( $_POST['run_id'] ) ) : '';
	$state  = nexus_seo_audit_get_state();
	$status = sanitize_key( (string) ( $state['status'] ?? '' ) );

	if ( '' === $run_id || (string) ( $state['run_id'] ?? '' ) !== $run_id ) {
		wp_send_json_error( [ 'message' => 'Audit-Lauf nicht gefunden.' ], 409 );
	}

	if ( ! in_array( $status, [ 'queued', 'running' ], true ) ) {
		wp_send_json_success(
			[
				'status'         => $status,
				'processed_urls' => absint( $state['processed_urls'] ?? 0 ),
				'total_urls'     => absint( $state['total_urls'] ?? 0 ),
			]
		);
	}

	add_filter( 'nexus_seo_site_audit_batch_size', 'nexus_seo_audit_admin_tick_batch_size', 999 );
	try {
		nexus_seo_audit_process_batch( $run_id );
	} finally {
		remove_filter( 'nexus_seo_site_audit_batch_size', 'nexus_seo_audit_admin_tick_batch_size', 999 );
	}

	$state = nexus_seo_audit_get_state();

	wp_send_json_success(
		[
			'status'         => sanitize_key( (string) ( $state['status'] ?? '' ) ),
			'processed_urls' => absint( $state['processed_urls'] ?? 0 ),
			'total_urls'     => absint( $state['total_urls'] ?? 0 ),
		]
	);
}
add_action( 'wp_ajax_nexus_seo_audit_tick', 'nexus_seo_audit_ajax_tick' );

/**
 * Fire one keepalive tick whenever an active Site Audit screen is rendered.
 *
 * The existing audit UI already reloads itself every five seconds. sendBeacon
 * survives that navigation, so even a slower self-request can finish instead
 * of being cancelled by the refresh.
 */
function nexus_seo_audit_render_runtime_tick() {
	if ( ! function_exists( 'nexus_current_user_can_manage_seo_cockpit' ) || ! nexus_current_user_can_manage_seo_cockpit() ) {
		return;
	}

	if ( ! isset( $_GET['page'] ) || 'nexus-seo-cockpit-site-audit' !== sanitize_key( (string) wp_unslash( $_GET['page'] ) ) ) {
		return;
	}

	$state  = nexus_seo_audit_get_state();
	$status = sanitize_key( (string) ( $state['status'] ?? '' ) );
	$run_id = (string) ( $state['run_id'] ?? '' );

	if ( '' === $run_id || ! in_array( $status, [ 'queued', 'running' ], true ) ) {
		return;
	}

	$endpoint = admin_url( 'admin-ajax.php' );
	$nonce    = wp_create_nonce( 'nexus_seo_audit_tick' );
	?>
	<script>
	(function () {
		var data = new FormData();
		data.append('action', 'nexus_seo_audit_tick');
		data.append('nonce', <?php echo wp_json_encode( $nonce ); ?>);
		data.append('run_id', <?php echo wp_json_encode( $run_id ); ?>);

		if (navigator.sendBeacon) {
			navigator.sendBeacon(<?php echo wp_json_encode( $endpoint ); ?>, data);
			return;
		}

		fetch(<?php echo wp_json_encode( $endpoint ); ?>, {
			method: 'POST',
			body: data,
			credentials: 'same-origin',
			keepalive: true
		}).catch(function () {});
	}());
	</script>
	<?php
}
add_action( 'admin_footer', 'nexus_seo_audit_render_runtime_tick', 1 );
