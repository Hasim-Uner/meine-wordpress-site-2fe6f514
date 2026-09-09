<?php
/**
 * Resilient Google URL Inspection queue for the SEO Site Audit.
 *
 * The audit crawler may finish faster than dozens of external URL Inspection
 * requests can complete reliably inside option-save hooks. This queue fills
 * missing inspection data after the technical crawl has completed, in small
 * cached batches, without changing the deterministic technical score.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const NEXUS_SEO_AUDIT_INDEX_QUEUE_EVENT = 'nexus_seo_cockpit_audit_google_index_batch';
const NEXUS_SEO_AUDIT_INDEX_QUEUE_LOCK  = 'nexus_seo_cockpit_audit_google_index_lock';

/**
 * Count Google Inspection coverage inside the current audit state.
 *
 * Errors created by the old inline inspection path remain retryable until this
 * queue has attempted that URL once. That lets an already completed audit heal
 * itself after this module is deployed instead of permanently inheriting old
 * transient API failures.
 *
 * @param array<string,mixed> $state Audit state.
 * @return array{indexable:int,inspected:int,errors:int,pending:int}
 */
function nexus_seo_audit_index_queue_counts( $state ) {
	$counts = [
		'indexable' => 0,
		'inspected' => 0,
		'errors'    => 0,
		'pending'   => 0,
	];

	foreach ( (array) ( $state['pages'] ?? [] ) as $page ) {
		if ( ! is_array( $page ) || ! function_exists( 'nexus_seo_audit_page_is_indexable_scope' ) || ! nexus_seo_audit_page_is_indexable_scope( $page ) ) {
			continue;
		}

		$counts['indexable']++;
		if ( ! empty( $page['google_index'] ) && is_array( $page['google_index'] ) ) {
			$counts['inspected']++;
			continue;
		}

		if ( ! empty( $page['google_index_queue_attempted'] ) && ! empty( $page['google_index_error'] ) ) {
			$counts['errors']++;
			continue;
		}

		$counts['pending']++;
	}

	return $counts;
}

/**
 * Update queue metadata without touching the technical site score.
 *
 * @param array<string,mixed> $state Audit state.
 * @return array<string,mixed>
 */
function nexus_seo_audit_index_queue_refresh_summary( $state ) {
	$counts = nexus_seo_audit_index_queue_counts( $state );
	$status = 'complete';

	if ( $counts['pending'] > 0 ) {
		$status = 'running';
	} elseif ( $counts['errors'] > 0 ) {
		$status = 'partial';
	}

	$state['google_index'] = [
		'status'    => $status,
		'total'     => $counts['indexable'],
		'inspected' => $counts['inspected'],
		'errors'    => $counts['errors'],
		'pending'   => $counts['pending'],
		'message'   => $counts['errors'] > 0 ? 'Einzelne URL-Inspektionen konnten nicht geladen werden.' : '',
	];

	return $state;
}

/**
 * Rebuild Google findings and expose queue/API failures explicitly.
 *
 * @param array<string,mixed> $state Audit state.
 * @return array<string,mixed>
 */
function nexus_seo_audit_index_queue_refresh_findings( $state ) {
	if ( function_exists( 'nexus_seo_audit_intelligence_google_findings' ) ) {
		$state = nexus_seo_audit_intelligence_google_findings( $state );
	}

	if ( ! isset( $state['intelligence'] ) || ! is_array( $state['intelligence'] ) ) {
		$state['intelligence'] = [];
	}
	$findings = is_array( $state['intelligence']['google_findings'] ?? null ) ? $state['intelligence']['google_findings'] : [];

	foreach ( (array) ( $state['pages'] ?? [] ) as $page ) {
		if ( ! is_array( $page ) || empty( $page['google_index_queue_attempted'] ) || empty( $page['google_index_error'] ) || ! function_exists( 'nexus_seo_audit_page_is_indexable_scope' ) || ! nexus_seo_audit_page_is_indexable_scope( $page ) ) {
			continue;
		}

		if ( function_exists( 'nexus_seo_audit_issue' ) ) {
			$findings[] = nexus_seo_audit_issue(
				'google_inspection_error',
				'indexability',
				'medium',
				'Google URL Inspection konnte nicht geladen werden',
				'Search-Console-Verbindung, API-Quote und Berechtigung prüfen; beim nächsten Audit erneut versuchen.',
				(string) ( $page['url'] ?? '' ),
				0,
				[ 'message' => (string) $page['google_index_error'] ]
			);
		}
	}

	$state['intelligence']['google_findings'] = $findings;
	$state['intelligence']['generated_at']    = current_time( 'mysql' );

	return $state;
}

/**
 * Persist a queue update without re-running expensive completed-audit checks.
 *
 * The existing intelligence pre-update filter performs robots/sitemap checks on
 * every completed-state save. Queue ticks only change Google inspection data,
 * so temporarily removing that filter avoids unnecessary site-level requests.
 *
 * @param array<string,mixed> $state Audit state.
 * @return void
 */
function nexus_seo_audit_index_queue_save_state( $state ) {
	$filter     = 'nexus_seo_audit_intelligence_filter_state';
	$hook       = 'pre_update_option_' . NEXUS_SEO_AUDIT_STATE_OPTION;
	$had_filter = false !== has_filter( $hook, $filter );

	if ( $had_filter ) {
		remove_filter( $hook, $filter, 20 );
	}

	try {
		nexus_seo_audit_save_state( $state );
	} finally {
		if ( $had_filter ) {
			add_filter( $hook, $filter, 20, 3 );
		}
	}
}

/**
 * Schedule another background inspection batch when work remains.
 *
 * @param array<string,mixed>|null $state Optional known state.
 * @return void
 */
function nexus_seo_audit_index_queue_schedule( $state = null ) {
	$state = is_array( $state ) ? $state : nexus_seo_audit_get_state();
	if ( 'completed' !== (string) ( $state['status'] ?? '' ) ) {
		return;
	}

	$counts = nexus_seo_audit_index_queue_counts( $state );
	if ( $counts['pending'] <= 0 ) {
		return;
	}

	if ( ! wp_next_scheduled( NEXUS_SEO_AUDIT_INDEX_QUEUE_EVENT ) ) {
		wp_schedule_single_event( time() + 5, NEXUS_SEO_AUDIT_INDEX_QUEUE_EVENT );
	}

	if ( function_exists( 'spawn_cron' ) ) {
		spawn_cron();
	}
}

/**
 * Process a small number of missing URL Inspection results.
 *
 * @param int $limit Maximum API requests in this tick.
 * @return array{indexable:int,inspected:int,errors:int,pending:int}
 */
function nexus_seo_audit_index_queue_process( $limit = 2 ) {
	$state = nexus_seo_audit_get_state();
	if ( 'completed' !== (string) ( $state['status'] ?? '' ) || ! function_exists( 'nexus_get_seo_cockpit_url_inspection' ) || ! function_exists( 'nexus_seo_audit_intelligence_slim_inspection' ) ) {
		return nexus_seo_audit_index_queue_counts( $state );
	}

	if ( get_transient( NEXUS_SEO_AUDIT_INDEX_QUEUE_LOCK ) ) {
		return nexus_seo_audit_index_queue_counts( $state );
	}

	set_transient( NEXUS_SEO_AUDIT_INDEX_QUEUE_LOCK, '1', 2 * MINUTE_IN_SECONDS );
	$limit     = max( 1, min( 4, absint( $limit ) ) );
	$processed = 0;

	try {
		foreach ( (array) ( $state['pages'] ?? [] ) as $index => $page ) {
			if ( $processed >= $limit ) {
				break;
			}
			if ( ! is_array( $page ) || ! nexus_seo_audit_page_is_indexable_scope( $page ) || ! empty( $page['google_index'] ) || ! empty( $page['google_index_queue_attempted'] ) ) {
				continue;
			}

			$url = (string) ( $page['url'] ?? '' );
			if ( '' === $url ) {
				continue;
			}

			$result = nexus_get_seo_cockpit_url_inspection( $url, false );
			if ( is_wp_error( $result ) ) {
				$state['pages'][ $index ]['google_index_error']           = $result->get_error_message();
				$state['pages'][ $index ]['google_index_error_code']      = (string) $result->get_error_code();
				$state['pages'][ $index ]['google_index_queue_attempted'] = true;
			} else {
				$state['pages'][ $index ]['google_index'] = nexus_seo_audit_intelligence_slim_inspection( $result );
				unset(
					$state['pages'][ $index ]['google_index_error'],
					$state['pages'][ $index ]['google_index_error_code'],
					$state['pages'][ $index ]['google_index_queue_attempted']
				);
			}

			$processed++;
		}

		$state = nexus_seo_audit_index_queue_refresh_summary( $state );
		$state = nexus_seo_audit_index_queue_refresh_findings( $state );
		nexus_seo_audit_index_queue_save_state( $state );
	} finally {
		delete_transient( NEXUS_SEO_AUDIT_INDEX_QUEUE_LOCK );
	}

	$counts = nexus_seo_audit_index_queue_counts( $state );
	if ( $counts['pending'] > 0 ) {
		nexus_seo_audit_index_queue_schedule( $state );
	}

	return $counts;
}

/** Process one background queue batch. */
function nexus_seo_audit_index_queue_cron() {
	nexus_seo_audit_index_queue_process( 2 );
}
add_action( NEXUS_SEO_AUDIT_INDEX_QUEUE_EVENT, 'nexus_seo_audit_index_queue_cron' );

/**
 * Start/resume the queue after a completed audit state is persisted.
 *
 * @param string $option    Option name.
 * @param mixed  $old_value Previous value.
 * @param mixed  $value     New value.
 * @return void
 */
function nexus_seo_audit_index_queue_after_state_update( $option, $old_value, $value ) {
	if ( NEXUS_SEO_AUDIT_STATE_OPTION !== $option || ! is_array( $value ) || 'completed' !== (string) ( $value['status'] ?? '' ) ) {
		return;
	}

	nexus_seo_audit_index_queue_schedule( $value );
}
add_action( 'updated_option', 'nexus_seo_audit_index_queue_after_state_update', 20, 3 );

/** Resume an incomplete queue when wp-admin is opened after deployment. */
function nexus_seo_audit_index_queue_admin_resume() {
	if ( ! function_exists( 'nexus_current_user_can_view_seo_cockpit' ) || ! nexus_current_user_can_view_seo_cockpit() ) {
		return;
	}

	nexus_seo_audit_index_queue_schedule();
}
add_action( 'admin_init', 'nexus_seo_audit_index_queue_admin_resume', 30 );

/** Process one authenticated queue tick from the Site Audit screen. */
function nexus_seo_audit_index_queue_ajax_tick() {
	if ( ! function_exists( 'nexus_current_user_can_manage_seo_cockpit' ) || ! nexus_current_user_can_manage_seo_cockpit() ) {
		wp_send_json_error( [ 'message' => 'Nicht erlaubt.' ], 403 );
	}

	check_ajax_referer( 'nexus_seo_audit_index_queue_tick', 'nonce' );
	$counts = nexus_seo_audit_index_queue_process( 1 );
	wp_send_json_success( $counts );
}
add_action( 'wp_ajax_nexus_seo_audit_index_queue_tick', 'nexus_seo_audit_index_queue_ajax_tick' );

/**
 * Keep the post-audit inspection queue moving while the Site Audit screen is open.
 *
 * Requests run sequentially, one URL per tick. When the queue is finished the
 * page reloads once so Google Index Health and URL Intelligence show final data.
 *
 * @return void
 */
function nexus_seo_audit_index_queue_render_tick() {
	if ( ! function_exists( 'nexus_current_user_can_manage_seo_cockpit' ) || ! nexus_current_user_can_manage_seo_cockpit() ) {
		return;
	}
	if ( ! isset( $_GET['page'] ) || 'nexus-seo-cockpit-site-audit' !== sanitize_key( (string) wp_unslash( $_GET['page'] ) ) ) {
		return;
	}

	$state = nexus_seo_audit_get_state();
	if ( 'completed' !== (string) ( $state['status'] ?? '' ) ) {
		return;
	}
	$counts = nexus_seo_audit_index_queue_counts( $state );
	if ( $counts['pending'] <= 0 ) {
		return;
	}

	$endpoint = admin_url( 'admin-ajax.php' );
	$nonce    = wp_create_nonce( 'nexus_seo_audit_index_queue_tick' );
	?>
	<script>
	(function () {
		var endpoint = <?php echo wp_json_encode( $endpoint ); ?>;
		var nonce = <?php echo wp_json_encode( $nonce ); ?>;
		var stopped = false;

		function tick() {
			if (stopped) return;
			var data = new FormData();
			data.append('action', 'nexus_seo_audit_index_queue_tick');
			data.append('nonce', nonce);

			fetch(endpoint, {
				method: 'POST',
				body: data,
				credentials: 'same-origin'
			}).then(function (response) {
				return response.json();
			}).then(function (payload) {
				if (!payload || !payload.success || !payload.data) {
					stopped = true;
					return;
				}
				if (Number(payload.data.pending || 0) <= 0) {
					stopped = true;
					window.location.reload();
					return;
				}
				window.setTimeout(tick, 1200);
			}).catch(function () {
				stopped = true;
			});
		}

		window.setTimeout(tick, 600);
	}());
	</script>
	<?php
}
add_action( 'admin_footer', 'nexus_seo_audit_index_queue_render_tick', 3 );
