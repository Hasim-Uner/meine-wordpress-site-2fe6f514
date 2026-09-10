<?php
/** Nexus CRM sales operations module. @package Blocksy_Child */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Decide whether a contact belongs in the sales pipeline.
 *
 * @param int $contact_id Contact post ID.
 * @return bool
 */
function nexus_crm_contact_is_sales_relevant( $contact_id ) {
	$source   = sanitize_key( (string) get_post_meta( $contact_id, '_nexus_contact_latest_source', true ) );
	$segments = function_exists( 'nexus_get_contact_segments' ) ? nexus_get_contact_segments( $contact_id ) : [];

	if ( in_array( $source, [ 'project_request', 'general_inquiry', 'request_analysis' ], true ) ) {
		return true;
	}

	return (bool) array_intersect( [ 'contact_inquiry', 'project_request', 'general_inquiry', 'analysis_lead' ], (array) $segments );
}

/**
 * Create one open opportunity when a sales-relevant contact is updated.
 *
 * @param int $contact_id Contact post ID.
 * @return int
 */
function nexus_maybe_create_crm_opportunity_for_contact( $contact_id ) {
	$contact_id = (int) $contact_id;
	if ( $contact_id <= 0 || ! nexus_crm_contact_is_sales_relevant( $contact_id ) ) {
		return 0;
	}

	$existing = nexus_find_open_crm_opportunity_for_contact( $contact_id );
	if ( $existing > 0 ) {
		return $existing;
	}

	$source  = sanitize_key( (string) get_post_meta( $contact_id, '_nexus_contact_latest_source', true ) );
	$service = (string) get_post_meta( $contact_id, '_nexus_contact_focus_label', true );
	if ( '' === $service ) {
		$service = (string) get_post_meta( $contact_id, '_nexus_contact_request_type_label', true );
	}

	$result = nexus_create_crm_opportunity(
		$contact_id,
		[
			'stage'   => 'new',
			'source'  => $source,
			'service' => $service,
		]
	);

	return is_wp_error( $result ) ? 0 : (int) $result;
}

/**
 * Observe the final shared-contact update marker and extend it into sales data.
 *
 * @param int    $meta_id Meta ID.
 * @param int    $object_id Post ID.
 * @param string $meta_key Meta key.
 * @param mixed  $meta_value Meta value.
 * @return void
 */
function nexus_crm_sales_on_contact_meta_update( $meta_id, $object_id, $meta_key, $meta_value ) {
	unset( $meta_id, $meta_value );
	if ( '_nexus_contact_updated_at' !== $meta_key || 'nexus_contact' !== get_post_type( $object_id ) ) {
		return;
	}

	nexus_maybe_create_crm_opportunity_for_contact( (int) $object_id );
}
add_action( 'added_post_meta', 'nexus_crm_sales_on_contact_meta_update', 20, 4 );
add_action( 'updated_post_meta', 'nexus_crm_sales_on_contact_meta_update', 20, 4 );

/**
 * Sync one specialized Audit/Marktcheck request into the shared sales layer.
 *
 * @param int $review_request_id Audit request post ID.
 * @return int Opportunity ID or 0.
 */
function nexus_sync_review_request_to_crm_sales( $review_request_id ) {
	$review_request_id = (int) $review_request_id;
	if ( $review_request_id <= 0 || 'nexus_review_request' !== get_post_type( $review_request_id ) || ! function_exists( 'nexus_upsert_crm_contact' ) ) {
		return 0;
	}

	$email = (string) get_post_meta( $review_request_id, '_nexus_review_email', true );
	if ( '' === $email || ! is_email( $email ) ) {
		return 0;
	}

	$company = (string) get_post_meta( $review_request_id, '_nexus_review_company', true );
	$name    = (string) get_post_meta( $review_request_id, '_nexus_review_name', true );
	$phone   = (string) get_post_meta( $review_request_id, '_nexus_review_phone', true );
	$domain  = (string) get_post_meta( $review_request_id, '_nexus_review_domain', true );
	$service = (string) get_post_meta( $review_request_id, '_nexus_review_audit_type_label', true );
	$qual    = sanitize_key( (string) get_post_meta( $review_request_id, '_nexus_review_qualification_status', true ) );
	$stage   = 'qualified' === $qual ? 'qualified' : ( 'nurture' === $qual ? 'nurture' : 'new' );

	$contact_id = nexus_upsert_crm_contact(
		[
			'email'         => $email,
			'title'         => $company ?: $name ?: $email,
			'source'        => 'request_analysis',
			'latest_source' => 'request_analysis',
			'status'        => 'new',
			'segments'      => [ 'analysis_lead', 'contact_inquiry' ],
			'refresh_title' => true,
			'meta'          => [
				'_nexus_contact_name'        => $name,
				'_nexus_contact_company'     => $company,
				'_nexus_contact_phone'       => $phone,
				'_nexus_contact_website_url' => '' !== $domain ? esc_url_raw( 'https://' . $domain ) : '',
			],
		]
	);

	if ( is_wp_error( $contact_id ) ) {
		return 0;
	}

	$opportunity_id = nexus_find_open_crm_opportunity_for_contact( (int) $contact_id );
	if ( $opportunity_id <= 0 ) {
		$result = nexus_create_crm_opportunity(
			(int) $contact_id,
			[
				'stage'             => $stage,
				'source'            => 'request_analysis',
				'service'           => $service ?: 'Marktcheck',
				'review_request_id' => $review_request_id,
			]
		);
		$opportunity_id = is_wp_error( $result ) ? 0 : (int) $result;
	} else {
		update_post_meta( $opportunity_id, '_nexus_opportunity_review_request_id', $review_request_id );
		$current_stage = (string) get_post_meta( $opportunity_id, '_nexus_opportunity_stage', true );
		$rank          = array_flip( array_keys( nexus_get_crm_sales_stages() ) );
		if ( isset( $rank[ $stage ], $rank[ $current_stage ] ) && $rank[ $stage ] > $rank[ $current_stage ] && nexus_is_crm_sales_stage_open( $current_stage ) ) {
			update_post_meta( $opportunity_id, '_nexus_opportunity_stage', $stage );
		}
		update_post_meta( $opportunity_id, '_nexus_opportunity_updated_at', current_time( 'timestamp', true ) );
		nexus_sync_crm_contact_sales_snapshot( (int) $contact_id, $opportunity_id );
	}

	if ( $opportunity_id > 0 ) {
		update_post_meta( $review_request_id, '_nexus_review_contact_id', (int) $contact_id );
		update_post_meta( $review_request_id, '_nexus_review_opportunity_id', $opportunity_id );
	}

	return $opportunity_id;
}

/**
 * Sync Audit request after its qualification result has been persisted.
 *
 * @param int    $meta_id Meta ID.
 * @param int    $object_id Post ID.
 * @param string $meta_key Meta key.
 * @param mixed  $meta_value Meta value.
 * @return void
 */
function nexus_crm_sales_on_review_qualification( $meta_id, $object_id, $meta_key, $meta_value ) {
	unset( $meta_id, $meta_value );
	if ( '_nexus_review_qualification_status' !== $meta_key || 'nexus_review_request' !== get_post_type( $object_id ) ) {
		return;
	}

	nexus_sync_review_request_to_crm_sales( (int) $object_id );
}
add_action( 'added_post_meta', 'nexus_crm_sales_on_review_qualification', 20, 4 );
add_action( 'updated_post_meta', 'nexus_crm_sales_on_review_qualification', 20, 4 );
