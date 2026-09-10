<?php
/**
 * Nexus CRM sales operations: opportunities, follow-ups and activity timeline.
 *
 * Keeps pre-sales work in WordPress while the client portal remains the
 * post-sale delivery system. External messaging providers can append inbound
 * activity through nexus_record_crm_activity() without changing CRM storage.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the sales-pipeline stages in their canonical order.
 *
 * @return array<string, array{label:string,probability:int,open:bool}>
 */
function nexus_get_crm_sales_stages() {
	return [
		'new'       => [ 'label' => 'Neu', 'probability' => 10, 'open' => true ],
		'qualified' => [ 'label' => 'Qualifiziert', 'probability' => 25, 'open' => true ],
		'contacted' => [ 'label' => 'Kontakt aufgenommen', 'probability' => 35, 'open' => true ],
		'meeting'   => [ 'label' => 'Gespräch', 'probability' => 50, 'open' => true ],
		'proposal'  => [ 'label' => 'Angebot', 'probability' => 70, 'open' => true ],
		'follow_up' => [ 'label' => 'Follow-up', 'probability' => 80, 'open' => true ],
		'nurture'   => [ 'label' => 'Später / Nurture', 'probability' => 15, 'open' => true ],
		'won'       => [ 'label' => 'Gewonnen', 'probability' => 100, 'open' => false ],
		'lost'      => [ 'label' => 'Verloren', 'probability' => 0, 'open' => false ],
	];
}

/**
 * Return a public label for one stage.
 *
 * @param string $stage Stage key.
 * @return string
 */
function nexus_get_crm_sales_stage_label( $stage ) {
	$stages = nexus_get_crm_sales_stages();
	$stage  = sanitize_key( (string) $stage );

	return isset( $stages[ $stage ] ) ? $stages[ $stage ]['label'] : 'Unbekannt';
}

/**
 * Check whether a stage is still an active sales opportunity.
 *
 * @param string $stage Stage key.
 * @return bool
 */
function nexus_is_crm_sales_stage_open( $stage ) {
	$stages = nexus_get_crm_sales_stages();
	$stage  = sanitize_key( (string) $stage );

	return isset( $stages[ $stage ] ) && ! empty( $stages[ $stage ]['open'] );
}

/**
 * Register hidden storage post types. Their data is managed through Nexus CRM.
 *
 * @return void
 */
function nexus_register_crm_sales_post_types() {
	register_post_type(
		'nexus_opportunity',
		[
			'labels' => [
				'name'          => 'Sales-Chancen',
				'singular_name' => 'Sales-Chance',
			],
			'public'              => false,
			'publicly_queryable'  => false,
			'show_ui'             => false,
			'show_in_menu'        => false,
			'exclude_from_search' => true,
			'has_archive'         => false,
			'rewrite'             => false,
			'query_var'           => false,
			'map_meta_cap'        => true,
			'supports'            => [ 'title' ],
		]
	);

	register_post_type(
		'nexus_crm_activity',
		[
			'labels' => [
				'name'          => 'CRM-Aktivitäten',
				'singular_name' => 'CRM-Aktivität',
			],
			'public'              => false,
			'publicly_queryable'  => false,
			'show_ui'             => false,
			'show_in_menu'        => false,
			'exclude_from_search' => true,
			'has_archive'         => false,
			'rewrite'             => false,
			'query_var'           => false,
			'map_meta_cap'        => true,
			'supports'            => [ 'title', 'editor' ],
		]
	);
}
add_action( 'init', 'nexus_register_crm_sales_post_types' );

/**
 * Return the admin URL for the sales workspace.
 *
 * @param array<string, scalar> $args Optional query arguments.
 * @return string
 */
function nexus_get_crm_sales_admin_url( $args = [] ) {
	$url = admin_url( 'admin.php?page=nexus-crm-sales' );

	return ! empty( $args ) ? add_query_arg( $args, $url ) : $url;
}

/**
 * Return the admin URL for the communication feed.
 *
 * @param array<string, scalar> $args Optional query arguments.
 * @return string
 */
function nexus_get_crm_inbox_admin_url( $args = [] ) {
	$url = admin_url( 'admin.php?page=nexus-crm-inbox' );

	return ! empty( $args ) ? add_query_arg( $args, $url ) : $url;
}

/**
 * Register sales and communication submenus below Nexus CRM.
 *
 * @return void
 */
function nexus_register_crm_sales_menu() {
	$parent_slug = function_exists( 'nexus_get_crm_menu_slug' ) ? nexus_get_crm_menu_slug() : 'nexus-crm';

	add_submenu_page(
		$parent_slug,
		'Vertrieb',
		'Vertrieb',
		'edit_pages',
		'nexus-crm-sales',
		'nexus_render_crm_sales_page'
	);

	add_submenu_page(
		$parent_slug,
		'Kommunikation',
		'Kommunikation',
		'edit_pages',
		'nexus-crm-inbox',
		'nexus_render_crm_inbox_page'
	);
}
add_action( 'admin_menu', 'nexus_register_crm_sales_menu', 20 );

/**
 * Enqueue the isolated admin stylesheet for the sales workspaces.
 *
 * @param string $hook_suffix Current admin page hook.
 * @return void
 */
function nexus_enqueue_crm_sales_admin_assets( $hook_suffix ) {
	if ( false === strpos( (string) $hook_suffix, 'nexus-crm-sales' ) && false === strpos( (string) $hook_suffix, 'nexus-crm-inbox' ) ) {
		return;
	}

	$relative = '/assets/css/nexus-crm-sales-admin.css';
	$path     = get_stylesheet_directory() . $relative;
	$version  = file_exists( $path ) ? (string) filemtime( $path ) : null;

	wp_enqueue_style(
		'nexus-crm-sales-admin',
		get_stylesheet_directory_uri() . $relative,
		[],
		$version
	);
}
add_action( 'admin_enqueue_scripts', 'nexus_enqueue_crm_sales_admin_assets' );

/**
 * Parse a decimal Euro amount into integer cents.
 *
 * @param mixed $value Raw form value.
 * @return int
 */
function nexus_crm_sales_parse_value_cents( $value ) {
	$value = preg_replace( '/\s+/', '', trim( (string) $value ) );
	if ( ! is_string( $value ) || '' === $value ) {
		return 0;
	}

	$has_comma = false !== strpos( $value, ',' );
	$has_dot   = false !== strpos( $value, '.' );

	if ( $has_comma && $has_dot ) {
		$value = str_replace( '.', '', $value );
		$value = str_replace( ',', '.', $value );
	} elseif ( $has_comma ) {
		$value = str_replace( ',', '.', $value );
	} elseif ( $has_dot && preg_match( '/\.\d{3}(?:\.|$)/', $value ) ) {
		$value = str_replace( '.', '', $value );
	}

	if ( ! is_numeric( $value ) ) {
		return 0;
	}

	return max( 0, (int) round( (float) $value * 100 ) );
}

/**
 * Format integer cents as Euro amount.
 *
 * @param int $cents Amount in cents.
 * @return string
 */
function nexus_crm_sales_format_money( $cents ) {
	$cents = max( 0, (int) $cents );

	return number_format( $cents / 100, 0 === $cents % 100 ? 0 : 2, ',', '.' ) . ' €';
}

/**
 * Convert a datetime-local form value to a Unix timestamp in site timezone.
 *
 * @param string $value Form value.
 * @return int
 */
function nexus_crm_sales_parse_local_datetime( $value ) {
	$value = trim( sanitize_text_field( (string) $value ) );
	if ( '' === $value ) {
		return 0;
	}

	try {
		$date = new DateTimeImmutable( $value, wp_timezone() );
	} catch ( Exception $e ) {
		return 0;
	}

	return $date->getTimestamp();
}

/**
 * Format a stored timestamp for a datetime-local input.
 *
 * @param int $timestamp Unix timestamp.
 * @return string
 */
function nexus_crm_sales_datetime_input_value( $timestamp ) {
	$timestamp = (int) $timestamp;

	return $timestamp > 0 ? wp_date( 'Y-m-d\TH:i', $timestamp ) : '';
}

/**
 * Return normalized opportunity data.
 *
 * @param int $opportunity_id Opportunity post ID.
 * @return array<string, mixed>|null
 */
function nexus_get_crm_opportunity( $opportunity_id ) {
	$post = get_post( (int) $opportunity_id );
	if ( ! ( $post instanceof WP_Post ) || 'nexus_opportunity' !== $post->post_type || 'private' !== $post->post_status ) {
		return null;
	}

	return [
		'id'                => (int) $post->ID,
		'title'             => (string) $post->post_title,
		'contact_id'        => (int) get_post_meta( $post->ID, '_nexus_opportunity_contact_id', true ),
		'review_request_id' => (int) get_post_meta( $post->ID, '_nexus_opportunity_review_request_id', true ),
		'stage'             => sanitize_key( (string) get_post_meta( $post->ID, '_nexus_opportunity_stage', true ) ) ?: 'new',
		'service'           => (string) get_post_meta( $post->ID, '_nexus_opportunity_service', true ),
		'value_cents'       => (int) get_post_meta( $post->ID, '_nexus_opportunity_value_cents', true ),
		'source'            => sanitize_key( (string) get_post_meta( $post->ID, '_nexus_opportunity_source', true ) ),
		'next_action'       => (string) get_post_meta( $post->ID, '_nexus_opportunity_next_action', true ),
		'next_action_at'    => (int) get_post_meta( $post->ID, '_nexus_opportunity_next_action_at', true ),
		'notes'             => (string) get_post_meta( $post->ID, '_nexus_opportunity_notes', true ),
		'loss_reason'       => (string) get_post_meta( $post->ID, '_nexus_opportunity_loss_reason', true ),
		'won_at'            => (int) get_post_meta( $post->ID, '_nexus_opportunity_won_at', true ),
		'lost_at'           => (int) get_post_meta( $post->ID, '_nexus_opportunity_lost_at', true ),
		'created_at'        => (int) get_post_meta( $post->ID, '_nexus_opportunity_created_at', true ),
		'updated_at'        => (int) get_post_meta( $post->ID, '_nexus_opportunity_updated_at', true ),
	];
}

/**
 * Return all opportunities, newest updated first.
 *
 * @return array<int, array<string, mixed>>
 */
function nexus_get_crm_opportunities() {
	$posts = get_posts(
		[
			'post_type'              => 'nexus_opportunity',
			'post_status'            => 'private',
			'posts_per_page'         => -1,
			'orderby'                => 'modified',
			'order'                  => 'DESC',
			'no_found_rows'          => true,
			'update_post_meta_cache' => true,
			'update_post_term_cache' => false,
		]
	);

	$opportunities = [];
	foreach ( $posts as $post ) {
		$item = nexus_get_crm_opportunity( $post->ID );
		if ( null !== $item ) {
			$opportunities[] = $item;
		}
	}

	return $opportunities;
}

/**
 * Find an existing open opportunity for a contact.
 *
 * @param int $contact_id Contact post ID.
 * @return int
 */
function nexus_find_open_crm_opportunity_for_contact( $contact_id ) {
	$contact_id = (int) $contact_id;
	if ( $contact_id <= 0 ) {
		return 0;
	}

	$ids = get_posts(
		[
			'post_type'              => 'nexus_opportunity',
			'post_status'            => 'private',
			'posts_per_page'         => 20,
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'update_post_meta_cache' => true,
			'update_post_term_cache' => false,
			'meta_query'             => [
				[
					'key'   => '_nexus_opportunity_contact_id',
					'value' => $contact_id,
					'type'  => 'NUMERIC',
				],
			],
		]
	);

	foreach ( $ids as $id ) {
		$stage = (string) get_post_meta( (int) $id, '_nexus_opportunity_stage', true );
		if ( nexus_is_crm_sales_stage_open( $stage ?: 'new' ) ) {
			return (int) $id;
		}
	}

	return 0;
}

/**
 * Create an opportunity and write the contact-level snapshot used in lists.
 *
 * @param int                  $contact_id Contact post ID.
 * @param array<string, mixed> $args Opportunity fields.
 * @return int|WP_Error
 */
function nexus_create_crm_opportunity( $contact_id, $args = [] ) {
	$contact_id = (int) $contact_id;
	$contact    = get_post( $contact_id );
	if ( ! ( $contact instanceof WP_Post ) || 'nexus_contact' !== $contact->post_type ) {
		return new WP_Error( 'invalid_contact', 'Der CRM-Kontakt wurde nicht gefunden.' );
	}

	$args = wp_parse_args(
		$args,
		[
			'stage'             => 'new',
			'service'           => '',
			'value_cents'       => 0,
			'source'            => '',
			'next_action'       => '',
			'next_action_at'    => 0,
			'notes'             => '',
			'review_request_id' => 0,
		]
	);

	$stages = nexus_get_crm_sales_stages();
	$stage  = sanitize_key( (string) $args['stage'] );
	if ( ! isset( $stages[ $stage ] ) ) {
		$stage = 'new';
	}

	$company = (string) get_post_meta( $contact_id, '_nexus_contact_company', true );
	$name    = (string) get_post_meta( $contact_id, '_nexus_contact_name', true );
	$email   = (string) get_post_meta( $contact_id, '_nexus_contact_email', true );
	$title   = $company ?: $name ?: $email ?: $contact->post_title;
	$service = sanitize_text_field( (string) $args['service'] );
	if ( '' !== $service ) {
		$title .= ' · ' . $service;
	}

	$opportunity_id = wp_insert_post(
		[
			'post_type'   => 'nexus_opportunity',
			'post_status' => 'private',
			'post_title'  => $title,
		],
		true
	);

	if ( is_wp_error( $opportunity_id ) ) {
		return $opportunity_id;
	}

	$now = current_time( 'timestamp', true );
	update_post_meta( $opportunity_id, '_nexus_opportunity_contact_id', $contact_id );
	update_post_meta( $opportunity_id, '_nexus_opportunity_stage', $stage );
	update_post_meta( $opportunity_id, '_nexus_opportunity_service', $service );
	update_post_meta( $opportunity_id, '_nexus_opportunity_value_cents', max( 0, (int) $args['value_cents'] ) );
	update_post_meta( $opportunity_id, '_nexus_opportunity_source', sanitize_key( (string) $args['source'] ) );
	update_post_meta( $opportunity_id, '_nexus_opportunity_next_action', sanitize_text_field( (string) $args['next_action'] ) );
	update_post_meta( $opportunity_id, '_nexus_opportunity_next_action_at', max( 0, (int) $args['next_action_at'] ) );
	update_post_meta( $opportunity_id, '_nexus_opportunity_notes', sanitize_textarea_field( (string) $args['notes'] ) );
	update_post_meta( $opportunity_id, '_nexus_opportunity_created_at', $now );
	update_post_meta( $opportunity_id, '_nexus_opportunity_updated_at', $now );

	$review_request_id = (int) $args['review_request_id'];
	if ( $review_request_id > 0 ) {
		update_post_meta( $opportunity_id, '_nexus_opportunity_review_request_id', $review_request_id );
	}

	nexus_sync_crm_contact_sales_snapshot( $contact_id, $opportunity_id );
	nexus_record_crm_activity(
		[
			'contact_id'     => $contact_id,
			'opportunity_id' => $opportunity_id,
			'type'           => 'opportunity_created',
			'channel'        => 'crm',
			'direction'      => 'internal',
			'subject'        => 'Sales-Chance angelegt',
			'body'           => nexus_get_crm_sales_stage_label( $stage ),
		]
	);

	return (int) $opportunity_id;
}

/**
 * Write denormalized sales fields onto a contact for fast list filtering.
 *
 * @param int $contact_id Contact post ID.
 * @param int $opportunity_id Opportunity post ID.
 * @return void
 */
function nexus_sync_crm_contact_sales_snapshot( $contact_id, $opportunity_id ) {
	$opportunity = nexus_get_crm_opportunity( $opportunity_id );
	if ( null === $opportunity || (int) $opportunity['contact_id'] !== (int) $contact_id ) {
		return;
	}

	update_post_meta( $contact_id, '_nexus_contact_sales_opportunity_id', (int) $opportunity['id'] );
	update_post_meta( $contact_id, '_nexus_contact_sales_stage', (string) $opportunity['stage'] );
	update_post_meta( $contact_id, '_nexus_contact_pipeline_value_cents', (int) $opportunity['value_cents'] );
	update_post_meta( $contact_id, '_nexus_contact_next_action', (string) $opportunity['next_action'] );
	update_post_meta( $contact_id, '_nexus_contact_next_action_at', (int) $opportunity['next_action_at'] );
}
