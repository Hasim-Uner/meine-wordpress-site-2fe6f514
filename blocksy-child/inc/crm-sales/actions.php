<?php
/** Nexus CRM sales operations module. @package Blocksy_Child */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Redirect back to one sales page after a mutation.
 *
 * @param string $message Notice text.
 * @param array<string, scalar> $args Extra query args.
 * @return never
 */
function nexus_crm_sales_redirect( $message, $args = [] ) {
	$args['crm_notice'] = sanitize_text_field( (string) $message );
	wp_safe_redirect( nexus_get_crm_sales_admin_url( $args ) );
	exit;
}

/**
 * Create a lead/contact and opportunity manually from the sales workspace.
 *
 * @return void
 */
function nexus_handle_crm_sales_create() {
	if ( ! current_user_can( 'edit_pages' ) ) {
		wp_die( 'Keine Berechtigung.' );
	}
	check_admin_referer( 'nexus_crm_sales_create' );

	$email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$name    = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$company = isset( $_POST['company'] ) ? sanitize_text_field( wp_unslash( $_POST['company'] ) ) : '';
	$service = isset( $_POST['service'] ) ? sanitize_text_field( wp_unslash( $_POST['service'] ) ) : '';

	if ( '' === $email || ! is_email( $email ) || ! function_exists( 'nexus_upsert_crm_contact' ) ) {
		nexus_crm_sales_redirect( 'Bitte eine gültige E-Mail-Adresse angeben.' );
	}

	$contact_id = nexus_upsert_crm_contact(
		[
			'email'         => $email,
			'title'         => $company ?: $name ?: $email,
			'source'        => 'general_inquiry',
			'latest_source' => 'general_inquiry',
			'status'        => 'new',
			'segments'      => [ 'contact_inquiry', 'general_inquiry' ],
			'refresh_title' => true,
			'meta'          => [
				'_nexus_contact_name'    => $name,
				'_nexus_contact_company' => $company,
			],
		]
	);

	if ( is_wp_error( $contact_id ) ) {
		nexus_crm_sales_redirect( 'Lead konnte nicht angelegt werden.' );
	}

	$value_cents    = nexus_crm_sales_parse_value_cents( $_POST['value'] ?? '' );
	$opportunity_id = nexus_find_open_crm_opportunity_for_contact( (int) $contact_id );
	if ( $opportunity_id <= 0 ) {
		$result = nexus_create_crm_opportunity(
			(int) $contact_id,
			[
				'service'     => $service,
				'value_cents' => $value_cents,
				'source'      => 'manual',
			]
		);
		$opportunity_id = is_wp_error( $result ) ? 0 : (int) $result;
	} else {
		if ( '' !== $service ) {
			update_post_meta( $opportunity_id, '_nexus_opportunity_service', $service );
		}
		if ( $value_cents > 0 ) {
			update_post_meta( $opportunity_id, '_nexus_opportunity_value_cents', $value_cents );
		}
		update_post_meta( $opportunity_id, '_nexus_opportunity_source', 'manual' );
		update_post_meta( $opportunity_id, '_nexus_opportunity_updated_at', current_time( 'timestamp', true ) );
		nexus_sync_crm_contact_sales_snapshot( (int) $contact_id, $opportunity_id );
	}

	if ( $opportunity_id <= 0 ) {
		nexus_crm_sales_redirect( 'Kontakt ist angelegt, aber die Sales-Chance konnte nicht erstellt werden.' );
	}

	nexus_crm_sales_redirect( 'Lead angelegt.', [ 'opportunity' => $opportunity_id ] );
}
add_action( 'admin_post_nexus_crm_sales_create', 'nexus_handle_crm_sales_create' );

/**
 * Persist edits to one opportunity.
 *
 * @return void
 */
function nexus_handle_crm_sales_save() {
	if ( ! current_user_can( 'edit_pages' ) ) {
		wp_die( 'Keine Berechtigung.' );
	}
	check_admin_referer( 'nexus_crm_sales_save' );

	$opportunity_id = isset( $_POST['opportunity_id'] ) ? absint( $_POST['opportunity_id'] ) : 0;
	$opportunity    = nexus_get_crm_opportunity( $opportunity_id );
	if ( null === $opportunity ) {
		nexus_crm_sales_redirect( 'Sales-Chance wurde nicht gefunden.' );
	}

	$stages      = nexus_get_crm_sales_stages();
	$old_stage   = (string) $opportunity['stage'];
	$new_stage   = isset( $_POST['stage'] ) ? sanitize_key( wp_unslash( $_POST['stage'] ) ) : $old_stage;
	$new_stage   = isset( $stages[ $new_stage ] ) ? $new_stage : $old_stage;
	$service     = isset( $_POST['service'] ) ? sanitize_text_field( wp_unslash( $_POST['service'] ) ) : '';
	$next        = isset( $_POST['next_action'] ) ? sanitize_text_field( wp_unslash( $_POST['next_action'] ) ) : '';
	$notes       = isset( $_POST['notes'] ) ? sanitize_textarea_field( wp_unslash( $_POST['notes'] ) ) : '';
	$loss_reason = isset( $_POST['loss_reason'] ) ? sanitize_text_field( wp_unslash( $_POST['loss_reason'] ) ) : '';
	$value       = nexus_crm_sales_parse_value_cents( $_POST['value'] ?? '' );
	$next_at     = nexus_crm_sales_parse_local_datetime( $_POST['next_action_at'] ?? '' );
	$now         = current_time( 'timestamp', true );
	$auto_enabled = ! empty( $_POST['auto_followup_enabled'] );
	$auto_at      = nexus_crm_sales_parse_local_datetime( $_POST['auto_followup_at'] ?? '' );
	$auto_subject = isset( $_POST['auto_followup_subject'] ) ? sanitize_text_field( wp_unslash( $_POST['auto_followup_subject'] ) ) : '';
	$auto_body    = isset( $_POST['auto_followup_body'] ) ? sanitize_textarea_field( wp_unslash( $_POST['auto_followup_body'] ) ) : '';
	$auto_enabled = $auto_enabled && $auto_at > $now && '' !== $auto_subject && '' !== $auto_body && nexus_is_crm_sales_stage_open( $new_stage );

	update_post_meta( $opportunity_id, '_nexus_opportunity_stage', $new_stage );
	update_post_meta( $opportunity_id, '_nexus_opportunity_service', $service );
	update_post_meta( $opportunity_id, '_nexus_opportunity_value_cents', $value );
	update_post_meta( $opportunity_id, '_nexus_opportunity_next_action', $next );
	update_post_meta( $opportunity_id, '_nexus_opportunity_next_action_at', $next_at );
	update_post_meta( $opportunity_id, '_nexus_opportunity_notes', $notes );
	update_post_meta( $opportunity_id, '_nexus_opportunity_loss_reason', $loss_reason );
	update_post_meta( $opportunity_id, '_nexus_opportunity_auto_followup_enabled', $auto_enabled ? 1 : 0 );
	update_post_meta( $opportunity_id, '_nexus_opportunity_auto_followup_at', $auto_at );
	update_post_meta( $opportunity_id, '_nexus_opportunity_auto_followup_subject', $auto_subject );
	update_post_meta( $opportunity_id, '_nexus_opportunity_auto_followup_body', $auto_body );
	if ( $auto_enabled ) {
		update_post_meta( $opportunity_id, '_nexus_opportunity_auto_followup_attempts', 0 );
	}
	update_post_meta( $opportunity_id, '_nexus_opportunity_updated_at', $now );

	if ( 'won' === $new_stage && 'won' !== $old_stage ) {
		update_post_meta( $opportunity_id, '_nexus_opportunity_won_at', $now );
		delete_post_meta( $opportunity_id, '_nexus_opportunity_lost_at' );
	} elseif ( 'lost' === $new_stage && 'lost' !== $old_stage ) {
		update_post_meta( $opportunity_id, '_nexus_opportunity_lost_at', $now );
		delete_post_meta( $opportunity_id, '_nexus_opportunity_won_at' );
	} elseif ( nexus_is_crm_sales_stage_open( $new_stage ) ) {
		delete_post_meta( $opportunity_id, '_nexus_opportunity_won_at' );
		delete_post_meta( $opportunity_id, '_nexus_opportunity_lost_at' );
	}

	nexus_sync_crm_contact_sales_snapshot( (int) $opportunity['contact_id'], $opportunity_id );

	if ( $new_stage !== $old_stage ) {
		nexus_record_crm_activity(
			[
				'contact_id'     => (int) $opportunity['contact_id'],
				'opportunity_id' => $opportunity_id,
				'type'           => 'stage_change',
				'channel'        => 'crm',
				'direction'      => 'internal',
				'subject'        => 'Pipeline-Status geändert',
				'body'           => nexus_get_crm_sales_stage_label( $old_stage ) . ' → ' . nexus_get_crm_sales_stage_label( $new_stage ),
			]
		);
	}

	nexus_crm_sales_redirect( 'Sales-Chance gespeichert.', [ 'opportunity' => $opportunity_id ] );
}
add_action( 'admin_post_nexus_crm_sales_save', 'nexus_handle_crm_sales_save' );

/**
 * Send a manual sales email via the existing central WordPress/Brevo mail layer.
 *
 * @return void
 */
function nexus_handle_crm_sales_email() {
	if ( ! current_user_can( 'edit_pages' ) ) {
		wp_die( 'Keine Berechtigung.' );
	}
	check_admin_referer( 'nexus_crm_sales_email' );

	$opportunity_id = isset( $_POST['opportunity_id'] ) ? absint( $_POST['opportunity_id'] ) : 0;
	$opportunity    = nexus_get_crm_opportunity( $opportunity_id );
	if ( null === $opportunity ) {
		nexus_crm_sales_redirect( 'Sales-Chance wurde nicht gefunden.' );
	}

	$contact = nexus_get_crm_sales_contact_data( (int) $opportunity['contact_id'] );
	$email   = sanitize_email( $contact['email'] );
	$subject = isset( $_POST['subject'] ) ? sanitize_text_field( wp_unslash( $_POST['subject'] ) ) : '';
	$body    = isset( $_POST['body'] ) ? sanitize_textarea_field( wp_unslash( $_POST['body'] ) ) : '';

	if ( '' === $email || ! is_email( $email ) || '' === $subject || '' === $body ) {
		nexus_crm_sales_redirect( 'Empfänger, Betreff und Nachricht sind erforderlich.', [ 'opportunity' => $opportunity_id ] );
	}

	$sent = wp_mail( $email, $subject, $body, [ 'Content-Type: text/plain; charset=UTF-8' ] );
	nexus_record_crm_activity(
		[
			'contact_id'     => (int) $opportunity['contact_id'],
			'opportunity_id' => $opportunity_id,
			'type'           => 'email',
			'channel'        => 'email',
			'direction'      => 'outbound',
			'subject'        => $subject,
			'body'           => $body,
			'provider'       => function_exists( 'nexus_is_brevo_api_enabled' ) && nexus_is_brevo_api_enabled() ? 'brevo' : 'wp_mail',
			'status'         => $sent ? 'sent' : 'failed',
		]
	);

	if ( $sent ) {
		update_post_meta( $opportunity_id, '_nexus_opportunity_last_contact_at', current_time( 'timestamp', true ) );
	}

	nexus_crm_sales_redirect( $sent ? 'E-Mail versendet und protokolliert.' : 'E-Mail konnte nicht versendet werden.', [ 'opportunity' => $opportunity_id ] );
}
add_action( 'admin_post_nexus_crm_sales_email', 'nexus_handle_crm_sales_email' );

/**
 * Explicitly backfill existing contacts and Audit requests into the sales layer.
 *
 * @return void
 */
function nexus_handle_crm_sales_backfill() {
	if ( ! current_user_can( 'edit_pages' ) ) {
		wp_die( 'Keine Berechtigung.' );
	}
	check_admin_referer( 'nexus_crm_sales_backfill' );

	$created_before = count( nexus_get_crm_opportunities() );
	$contacts       = get_posts(
		[
			'post_type'      => 'nexus_contact',
			'post_status'    => 'private',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'no_found_rows'  => true,
		]
	);
	foreach ( $contacts as $contact_id ) {
		nexus_maybe_create_crm_opportunity_for_contact( (int) $contact_id );
	}

	$reviews = get_posts(
		[
			'post_type'      => 'nexus_review_request',
			'post_status'    => 'private',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'no_found_rows'  => true,
		]
	);
	foreach ( $reviews as $review_id ) {
		nexus_sync_review_request_to_crm_sales( (int) $review_id );
	}

	$created_after = count( nexus_get_crm_opportunities() );
	$new_count     = max( 0, $created_after - $created_before );
	nexus_crm_sales_redirect( sprintf( '%d bestehende Sales-Chancen ergänzt.', $new_count ) );
}
add_action( 'admin_post_nexus_crm_sales_backfill', 'nexus_handle_crm_sales_backfill' );
