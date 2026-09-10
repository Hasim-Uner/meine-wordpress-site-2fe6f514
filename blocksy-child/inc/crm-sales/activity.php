<?php
/** Nexus CRM sales operations module. @package Blocksy_Child */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Store one normalized activity for timeline/inbox use.
 *
 * @param array<string, mixed> $args Activity fields.
 * @return int|WP_Error
 */
function nexus_record_crm_activity( $args ) {
	$args = wp_parse_args(
		$args,
		[
			'contact_id'     => 0,
			'opportunity_id' => 0,
			'type'           => 'note',
			'channel'        => 'crm',
			'direction'      => 'internal',
			'subject'        => '',
			'body'           => '',
			'provider'       => '',
			'provider_id'    => '',
			'status'         => 'recorded',
			'occurred_at'    => current_time( 'timestamp', true ),
		]
	);

	$contact_id = (int) $args['contact_id'];
	if ( $contact_id <= 0 || 'nexus_contact' !== get_post_type( $contact_id ) ) {
		return new WP_Error( 'invalid_activity_contact', 'CRM-Aktivität benötigt einen gültigen Kontakt.' );
	}

	$subject = sanitize_text_field( (string) $args['subject'] );
	$type    = sanitize_key( (string) $args['type'] ) ?: 'note';
	$title   = $subject ?: ucfirst( str_replace( '_', ' ', $type ) );

	$activity_id = wp_insert_post(
		[
			'post_type'    => 'nexus_crm_activity',
			'post_status'  => 'private',
			'post_title'   => $title,
			'post_content' => sanitize_textarea_field( (string) $args['body'] ),
		],
		true
	);

	if ( is_wp_error( $activity_id ) ) {
		return $activity_id;
	}

	update_post_meta( $activity_id, '_nexus_activity_contact_id', $contact_id );
	update_post_meta( $activity_id, '_nexus_activity_opportunity_id', max( 0, (int) $args['opportunity_id'] ) );
	update_post_meta( $activity_id, '_nexus_activity_type', $type );
	update_post_meta( $activity_id, '_nexus_activity_channel', sanitize_key( (string) $args['channel'] ) ?: 'crm' );
	update_post_meta( $activity_id, '_nexus_activity_direction', sanitize_key( (string) $args['direction'] ) ?: 'internal' );
	update_post_meta( $activity_id, '_nexus_activity_provider', sanitize_key( (string) $args['provider'] ) );
	update_post_meta( $activity_id, '_nexus_activity_provider_id', sanitize_text_field( (string) $args['provider_id'] ) );
	update_post_meta( $activity_id, '_nexus_activity_status', sanitize_key( (string) $args['status'] ) ?: 'recorded' );
	update_post_meta( $activity_id, '_nexus_activity_occurred_at', max( 0, (int) $args['occurred_at'] ) );
	update_post_meta( $activity_id, '_nexus_activity_created_by', get_current_user_id() );

	return (int) $activity_id;
}

/**
 * Return activities for an opportunity or the global communication feed.
 *
 * @param int $opportunity_id Optional opportunity ID.
 * @param int $limit Maximum rows.
 * @return array<int, WP_Post>
 */
function nexus_get_crm_activities( $opportunity_id = 0, $limit = 50 ) {
	$args = [
		'post_type'              => 'nexus_crm_activity',
		'post_status'            => 'private',
		'posts_per_page'         => max( 1, min( 200, (int) $limit ) ),
		'orderby'                => 'date',
		'order'                  => 'DESC',
		'no_found_rows'          => true,
		'update_post_meta_cache' => true,
		'update_post_term_cache' => false,
	];

	if ( (int) $opportunity_id > 0 ) {
		$args['meta_query'] = [
			[
				'key'   => '_nexus_activity_opportunity_id',
				'value' => (int) $opportunity_id,
				'type'  => 'NUMERIC',
			],
		];
	}

	return get_posts( $args );
}

/**
 * Ensure the lightweight follow-up runner is scheduled.
 *
 * Automatic lead emails are opt-in per opportunity; the cron only executes
 * schedules explicitly enabled in the sales detail view.
 *
 * @return void
 */
function nexus_schedule_crm_sales_followup_cron() {
	if ( ! wp_next_scheduled( 'nexus_crm_sales_followup_cron' ) ) {
		wp_schedule_event( time() + ( 10 * MINUTE_IN_SECONDS ), 'hourly', 'nexus_crm_sales_followup_cron' );
	}
}
add_action( 'init', 'nexus_schedule_crm_sales_followup_cron', 30 );

/**
 * Remove the theme-owned follow-up schedule when this theme is switched out.
 *
 * @return void
 */
function nexus_clear_crm_sales_followup_cron() {
	$timestamp = wp_next_scheduled( 'nexus_crm_sales_followup_cron' );
	while ( $timestamp ) {
		wp_unschedule_event( $timestamp, 'nexus_crm_sales_followup_cron' );
		$timestamp = wp_next_scheduled( 'nexus_crm_sales_followup_cron' );
	}
}
add_action( 'switch_theme', 'nexus_clear_crm_sales_followup_cron', 10, 0 );

/**
 * Execute due one-shot automated sales follow-ups.
 *
 * The sender uses the existing wp_mail/Brevo route. A transient lock prevents
 * duplicate sends when two cron requests overlap. Failed sends retry up to
 * three times before the schedule is disabled for manual review.
 *
 * @return void
 */
function nexus_run_crm_sales_followup_cron() {
	$ids = get_posts(
		[
			'post_type'      => 'nexus_opportunity',
			'post_status'    => 'private',
			'posts_per_page' => 50,
			'fields'         => 'ids',
			'no_found_rows'  => true,
			'meta_query'     => [
				[
					'key'   => '_nexus_opportunity_auto_followup_enabled',
					'value' => 1,
				],
			],
		]
	);

	$now = current_time( 'timestamp', true );
	foreach ( $ids as $opportunity_id ) {
		$opportunity_id = (int) $opportunity_id;
		$opportunity    = nexus_get_crm_opportunity( $opportunity_id );
		if ( null === $opportunity || ! nexus_is_crm_sales_stage_open( (string) $opportunity['stage'] ) ) {
			update_post_meta( $opportunity_id, '_nexus_opportunity_auto_followup_enabled', 0 );
			continue;
		}

		$send_at = (int) get_post_meta( $opportunity_id, '_nexus_opportunity_auto_followup_at', true );
		if ( $send_at <= 0 || $send_at > $now ) {
			continue;
		}

		$lock_key = 'nexus_crm_followup_' . $opportunity_id;
		if ( get_transient( $lock_key ) ) {
			continue;
		}
		set_transient( $lock_key, 1, 10 * MINUTE_IN_SECONDS );

		$contact = nexus_get_crm_sales_contact_data( (int) $opportunity['contact_id'] );
		$email   = sanitize_email( $contact['email'] );
		$subject = (string) get_post_meta( $opportunity_id, '_nexus_opportunity_auto_followup_subject', true );
		$body    = (string) get_post_meta( $opportunity_id, '_nexus_opportunity_auto_followup_body', true );
		$sent    = false;

		if ( '' !== $email && is_email( $email ) && '' !== trim( $subject ) && '' !== trim( $body ) ) {
			$sent = wp_mail( $email, $subject, $body, [ 'Content-Type: text/plain; charset=UTF-8' ] );
		}

		$attempts = (int) get_post_meta( $opportunity_id, '_nexus_opportunity_auto_followup_attempts', true ) + 1;
		update_post_meta( $opportunity_id, '_nexus_opportunity_auto_followup_attempts', $attempts );
		nexus_record_crm_activity(
			[
				'contact_id'     => (int) $opportunity['contact_id'],
				'opportunity_id' => $opportunity_id,
				'type'           => 'automated_followup',
				'channel'        => 'email',
				'direction'      => 'outbound',
				'subject'        => sanitize_text_field( $subject ?: 'Automatisches Follow-up' ),
				'body'           => sanitize_textarea_field( $body ),
				'provider'       => function_exists( 'nexus_is_brevo_api_enabled' ) && nexus_is_brevo_api_enabled() ? 'brevo' : 'wp_mail',
				'status'         => $sent ? 'sent' : 'failed',
			]
		);

		if ( $sent ) {
			update_post_meta( $opportunity_id, '_nexus_opportunity_auto_followup_enabled', 0 );
			update_post_meta( $opportunity_id, '_nexus_opportunity_last_auto_followup_at', $now );
			update_post_meta( $opportunity_id, '_nexus_opportunity_last_contact_at', $now );
		} elseif ( $attempts >= 3 ) {
			update_post_meta( $opportunity_id, '_nexus_opportunity_auto_followup_enabled', 0 );
		}

		delete_transient( $lock_key );
	}
}
add_action( 'nexus_crm_sales_followup_cron', 'nexus_run_crm_sales_followup_cron' );

/**
 * Get compact contact data for the sales UI.
 *
 * @param int $contact_id Contact post ID.
 * @return array<string, string>
 */
function nexus_get_crm_sales_contact_data( $contact_id ) {
	$contact_id = (int) $contact_id;
	$post       = get_post( $contact_id );

	return [
		'name'    => (string) get_post_meta( $contact_id, '_nexus_contact_name', true ) ?: ( $post instanceof WP_Post ? $post->post_title : '' ),
		'company' => (string) get_post_meta( $contact_id, '_nexus_contact_company', true ),
		'email'   => (string) get_post_meta( $contact_id, '_nexus_contact_email', true ),
		'phone'   => (string) get_post_meta( $contact_id, '_nexus_contact_phone', true ),
	];
}
