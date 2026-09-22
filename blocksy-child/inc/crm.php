<?php
/**
 * Shared CRM foundation for contacts, blog subscribers and project inquiries.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the admin slug for the shared CRM area.
 *
 * @return string
 */
function nexus_get_crm_menu_slug() {
	return 'nexus-crm';
}

/**
 * Return supported contact source labels.
 *
 * @return array<string, string>
 */
function nexus_get_crm_contact_source_labels() {
	return [
		'blog_subscriber'    => 'Blog-Abo',
		'project_request'    => 'Projektanfrage',
		'general_inquiry'    => 'Allgemeine Anfrage',
		'client_request'     => 'Kundenanliegen',
		'request_analysis'   => 'Marktcheck',
		'whitelabel_request' => 'White-Label-Anfrage',
	];
}

/**
 * Return supported CRM contact statuses.
 *
 * @return array<string, string>
 */
function nexus_get_crm_contact_status_options() {
	return [
		'new'          => 'Neu',
		'pending'      => 'Wartet auf Bestätigung',
		'active'       => 'Aktiv',
		'unsubscribed' => 'Abgemeldet',
		'archived'     => 'Archiviert',
	];
}

/**
 * Return supported CRM contact segments.
 *
 * @return array<string, string>
 */
function nexus_get_crm_contact_segment_labels() {
	return [
		'blog_notify'        => 'Neue Artikel per E-Mail',
		'contact_inquiry'    => 'Kontaktanfrage',
		'project_request'    => 'Projektanfrage',
		'general_inquiry'    => 'Allgemeine Anfrage',
		'client_request'     => 'Kundenanliegen',
		'analysis_lead'      => 'Analyse-Lead',
		'whitelabel_request' => 'White-Label-Anfrage',
	];
}

/**
 * Return supported analysis signal labels.
 *
 * @return array<string, string>
 */
function nexus_get_crm_analysis_signal_labels() {
	return [
		'green'  => 'Grün',
		'yellow' => 'Gelb',
		'red'    => 'Rot',
	];
}

/**
 * Normalize a contact email to a stable lowercase value.
 *
 * @param string $email Raw email address.
 * @return string
 */
function nexus_normalize_contact_email( $email ) {
	$email = sanitize_email( (string) $email );

	if ( '' === $email || ! is_email( $email ) ) {
		return '';
	}

	return strtolower( $email );
}

/**
 * Generate a token for confirmation or unsubscribe flows.
 *
 * @return string
 */
function nexus_generate_contact_token() {
	return wp_generate_password( 40, false, false );
}

/**
 * Register the shared CRM post type for contacts.
 *
 * @return void
 */
function nexus_register_contact_post_type() {
	register_post_type(
		'nexus_contact',
		[
			'labels' => [
				'name'               => 'CRM-Kontakte',
				'singular_name'      => 'CRM-Kontakt',
				'menu_name'          => 'CRM-Kontakte',
				'name_admin_bar'     => 'CRM-Kontakt',
				'add_new'            => 'Neu',
				'add_new_item'       => 'Neuen CRM-Kontakt anlegen',
				'edit_item'          => 'CRM-Kontakt bearbeiten',
				'new_item'           => 'Neuer CRM-Kontakt',
				'view_item'          => 'CRM-Kontakt ansehen',
				'search_items'       => 'CRM-Kontakte suchen',
				'not_found'          => 'Keine CRM-Kontakte gefunden.',
				'not_found_in_trash' => 'Keine CRM-Kontakte im Papierkorb.',
				'all_items'          => 'Alle CRM-Kontakte',
			],
			'public'              => false,
			'publicly_queryable'  => false,
			'show_ui'             => true,
			'show_in_menu'        => nexus_get_crm_menu_slug(),
			'show_in_admin_bar'   => false,
			'show_in_nav_menus'   => false,
			'exclude_from_search' => true,
			'has_archive'         => false,
			'rewrite'             => false,
			'query_var'           => false,
			'map_meta_cap'        => true,
			'menu_icon'           => 'dashicons-id',
			'supports'            => [ 'title' ],
		]
	);
}
add_action( 'init', 'nexus_register_contact_post_type' );

/**
 * Add filtered shortcuts for the shared CRM post type.
 *
 * @return void
 */
function nexus_register_crm_contact_shortcuts() {
	add_submenu_page(
		nexus_get_crm_menu_slug(),
		'Blog-Abos',
		'Blog-Abos',
		'edit_pages',
		'edit.php?post_type=nexus_contact&nexus_contact_segment=blog_notify'
	);

	add_submenu_page(
		nexus_get_crm_menu_slug(),
		'Projektanfragen',
		'Projektanfragen',
		'edit_pages',
		'edit.php?post_type=nexus_contact&nexus_contact_segment=project_request'
	);

	add_submenu_page(
		nexus_get_crm_menu_slug(),
		'White-Label-Anfragen',
		'White-Label-Anfragen',
		'edit_pages',
		'edit.php?post_type=nexus_contact&nexus_contact_segment=whitelabel_request'
	);
}
add_action( 'admin_menu', 'nexus_register_crm_contact_shortcuts', 30 );

/**
 * Find a CRM contact by normalized email.
 *
 * @param string $email Raw or normalized email.
 * @return int
 */
function nexus_find_contact_by_email( $email ) {
	$email = nexus_normalize_contact_email( $email );

	if ( '' === $email ) {
		return 0;
	}

	$post_ids = get_posts(
		[
			'post_type'              => 'nexus_contact',
			'post_status'            => 'private',
			'posts_per_page'         => 1,
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'meta_query'             => [
				[
					'key'   => '_nexus_contact_email',
					'value' => $email,
				],
			],
		]
	);

	return ! empty( $post_ids ) ? (int) $post_ids[0] : 0;
}

/**
 * Return the segments stored for a CRM contact.
 *
 * @param int $post_id Contact post ID.
 * @return array<int, string>
 */
function nexus_get_contact_segments( $post_id ) {
	$segments = get_post_meta( $post_id, '_nexus_contact_segments', true );

	if ( ! is_array( $segments ) ) {
		return [];
	}

	$segments = array_map( 'sanitize_key', $segments );
	$segments = array_filter( $segments );

	return array_values( array_unique( $segments ) );
}

/**
 * Persist segment list and segment flags on a CRM contact.
 *
 * @param int   $post_id  Contact post ID.
 * @param array $segments Segment keys.
 * @return void
 */
function nexus_set_contact_segments( $post_id, $segments ) {
	$known_segments = array_keys( nexus_get_crm_contact_segment_labels() );
	$segments       = array_map( 'sanitize_key', (array) $segments );
	$segments       = array_values( array_unique( array_filter( $segments ) ) );

	update_post_meta( $post_id, '_nexus_contact_segments', $segments );

	foreach ( $known_segments as $known_segment ) {
		$meta_key = '_nexus_contact_segment_' . $known_segment;

		if ( in_array( $known_segment, $segments, true ) ) {
			update_post_meta( $post_id, $meta_key, 1 );
		} else {
			delete_post_meta( $post_id, $meta_key );
		}
	}
}

/**
 * Create or update a CRM contact record.
 *
 * @param array $args Contact payload.
 * @return int|WP_Error
 */
function nexus_upsert_crm_contact( $args ) {
	$args = wp_parse_args(
		$args,
		[
			'email'         => '',
			'title'         => '',
			'source'        => '',
			'latest_source' => '',
			'status'        => '',
			'segments'      => [],
			'meta'          => [],
			'refresh_title' => false,
		]
	);

	$email = nexus_normalize_contact_email( $args['email'] );

	if ( '' === $email ) {
		return new WP_Error( 'invalid_email', 'Die E-Mail-Adresse ist ungueltig.' );
	}

	$contact_id = nexus_find_contact_by_email( $email );
	$is_new     = 0 === $contact_id;
	$timestamp  = current_time( 'timestamp' );
	$title      = sanitize_text_field( (string) $args['title'] );

	if ( '' === $title ) {
		$title = $email;
	}

	if ( $is_new ) {
		$contact_id = wp_insert_post(
			[
				'post_type'   => 'nexus_contact',
				'post_status' => 'private',
				'post_title'  => $title,
			],
			true
		);

		if ( is_wp_error( $contact_id ) ) {
			return $contact_id;
		}

		update_post_meta( $contact_id, '_nexus_contact_email', $email );
		update_post_meta( $contact_id, '_nexus_contact_created_at', $timestamp );
	} elseif ( ! empty( $args['refresh_title'] ) ) {
		wp_update_post(
			[
				'ID'         => $contact_id,
				'post_title' => $title,
			]
		);
	}

	$source        = sanitize_key( (string) $args['source'] );
	$latest_source = sanitize_key( (string) ( $args['latest_source'] ?: $source ) );

	if ( $source && '' === (string) get_post_meta( $contact_id, '_nexus_contact_source', true ) ) {
		update_post_meta( $contact_id, '_nexus_contact_source', $source );
	}

	if ( $latest_source ) {
		update_post_meta( $contact_id, '_nexus_contact_latest_source', $latest_source );
	}

	$sources = get_post_meta( $contact_id, '_nexus_contact_sources', true );
	$sources = is_array( $sources ) ? array_map( 'sanitize_key', $sources ) : [];

	if ( $source ) {
		$sources[] = $source;
	}

	if ( $latest_source ) {
		$sources[] = $latest_source;
	}

	if ( ! empty( $sources ) ) {
		$sources = array_values( array_unique( array_filter( $sources ) ) );
		update_post_meta( $contact_id, '_nexus_contact_sources', $sources );
	}

	if ( '' !== (string) $args['status'] ) {
		update_post_meta( $contact_id, '_nexus_contact_status', sanitize_key( (string) $args['status'] ) );
	}

	$current_segments = nexus_get_contact_segments( $contact_id );
	$merged_segments  = array_merge( $current_segments, (array) $args['segments'] );
	nexus_set_contact_segments( $contact_id, $merged_segments );

	$meta = is_array( $args['meta'] ) ? $args['meta'] : [];
	foreach ( $meta as $meta_key => $meta_value ) {
		if ( '' === $meta_key || 0 !== strpos( $meta_key, '_nexus_contact_' ) ) {
			continue;
		}

		if ( null === $meta_value ) {
			delete_post_meta( $contact_id, $meta_key );
			continue;
		}

		update_post_meta( $contact_id, $meta_key, $meta_value );
	}

	update_post_meta( $contact_id, '_nexus_contact_updated_at', $timestamp );

	return (int) $contact_id;
}

/**
 * Self-reported answers to "Wie sind Sie auf mich aufmerksam geworden?".
 *
 * Optional and qualitative. It covers referrals, LinkedIn and personal
 * outreach, which no analytics tool sees. Technical attribution (entry page,
 * referrer, campaign) stays invisible in the payload.
 *
 * @return array<string, string>
 */
function nexus_get_inquiry_referral_options() {
	return [
		'empfehlung'   => 'Empfehlung',
		'linkedin'     => 'LinkedIn',
		'suche'        => 'Google oder andere Suche',
		'ki_assistent' => 'KI-Assistent (z. B. ChatGPT)',
		'nachricht'    => 'Nachricht von mir',
		'sonstiges'    => 'Anders',
	];
}

/**
 * Sanitize the attribution part of a public inquiry payload.
 *
 * Internal URLs are reduced to paths on this site and the referrer to origin
 * and path, with the same rules as the Marktcheck. An unknown referral answer
 * is dropped instead of failing the request.
 *
 * @param array $payload Raw payload.
 * @return array<string, string>
 */
function nexus_sanitize_inquiry_attribution( $payload ) {
	$payload  = is_array( $payload ) ? $payload : [];
	$internal = static function ( $key ) use ( $payload ) {
		return function_exists( 'nexus_sanitize_review_request_internal_url' )
			? nexus_sanitize_review_request_internal_url( $payload[ $key ] ?? '' )
			: '';
	};
	$text     = static function ( $key, $length ) use ( $payload ) {
		return mb_substr( sanitize_text_field( (string) ( $payload[ $key ] ?? '' ) ), 0, $length );
	};

	$referral_options = nexus_get_inquiry_referral_options();
	$referral         = sanitize_key( (string) ( $payload['referral_source'] ?? '' ) );
	$referral         = isset( $referral_options[ $referral ] ) ? $referral : '';

	return [
		'landing_page_url'      => $internal( 'landing_page_url' ),
		'entry_page_url'        => $internal( 'entry_page_url' ),
		'previous_internal_url' => $internal( 'previous_internal_url' ),
		'referrer_url'          => function_exists( 'nexus_sanitize_review_request_referrer_url' )
			? nexus_sanitize_review_request_referrer_url( $payload['referrer_url'] ?? '' )
			: '',
		'ads_source'            => $text( 'ads_source', 120 ),
		'ads_keyword'           => $text( 'ads_keyword', 180 ),
		'utm_medium'            => $text( 'utm_medium', 120 ),
		'utm_campaign'          => $text( 'utm_campaign', 180 ),
		'referral_source'       => $referral,
		'referral_source_label' => '' !== $referral ? $referral_options[ $referral ] : '',
	];
}

/**
 * Map sanitized attribution onto CRM contact meta (latest inquiry wins).
 *
 * @param array $attribution Output of nexus_sanitize_inquiry_attribution().
 * @return array<string, string>
 */
function nexus_get_inquiry_attribution_meta( $attribution ) {
	$map = [
		'landing_page_url'      => '_nexus_contact_landing_page_url',
		'entry_page_url'        => '_nexus_contact_entry_page_url',
		'previous_internal_url' => '_nexus_contact_previous_page_url',
		'referrer_url'          => '_nexus_contact_referrer_url',
		'ads_source'            => '_nexus_contact_ads_source',
		'ads_keyword'           => '_nexus_contact_ads_keyword',
		'utm_medium'            => '_nexus_contact_utm_medium',
		'utm_campaign'          => '_nexus_contact_utm_campaign',
		'referral_source'       => '_nexus_contact_referral_source',
		'referral_source_label' => '_nexus_contact_referral_source_label',
	];
	$meta = [];

	foreach ( $map as $key => $meta_key ) {
		$meta[ $meta_key ] = (string) ( $attribution[ $key ] ?? '' );
	}

	return $meta;
}

/**
 * Read the stored attribution of a contact back into payload keys.
 *
 * @param int $post_id Contact post ID.
 * @return array<string, string>
 */
function nexus_get_contact_attribution_from_meta( $post_id ) {
	$attribution = [];

	foreach ( nexus_get_inquiry_attribution_meta( [] ) as $meta_key => $unused ) {
		$key                 = str_replace( [ '_nexus_contact_', 'previous_page_url' ], [ '', 'previous_internal_url' ], $meta_key );
		$attribution[ $key ] = (string) get_post_meta( (int) $post_id, $meta_key, true );
	}

	return $attribution;
}

/**
 * Return the non-empty attribution values as label => value, in reading order.
 *
 * @param array $attribution Sanitized attribution.
 * @return array<string, string>
 */
function nexus_get_inquiry_attribution_pairs( $attribution ) {
	$labels = [
		'referral_source_label' => 'Selbstauskunft',
		'entry_page_url'        => 'Einstiegsseite',
		'previous_internal_url' => 'Vorige Seite',
		'landing_page_url'      => 'Formularseite',
		'referrer_url'          => 'Referrer',
		'ads_source'            => 'Quelle',
		'utm_medium'            => 'Medium',
		'utm_campaign'          => 'Kampagne',
		'ads_keyword'           => 'Suchbegriff',
	];
	$pairs  = [];

	foreach ( $labels as $key => $label ) {
		$value = trim( (string) ( $attribution[ $key ] ?? '' ) );

		if ( '' !== $value ) {
			$pairs[ $label ] = $value;
		}
	}

	return $pairs;
}

/**
 * Render attribution rows for an internal notification mail.
 *
 * @param array $attribution Sanitized attribution.
 * @return string Escaped HTML, or a plain "direct" note.
 */
function nexus_get_inquiry_attribution_mail_rows( $attribution ) {
	$pairs = nexus_get_inquiry_attribution_pairs( $attribution );

	if ( empty( $pairs ) ) {
		return esc_html( 'Keine Angaben (direkter Aufruf oder Browser ohne Sitzungsspeicher).' );
	}

	$rows = [];

	foreach ( $pairs as $label => $value ) {
		$rows[] = sprintf( '<strong style="color:#f7f3ee;">%1$s:</strong> %2$s', esc_html( $label ), esc_html( $value ) );
	}

	return implode( '<br>', $rows );
}

/**
 * Sync a public contact request into the shared CRM.
 *
 * @param array $payload Validated contact request payload.
 * @return int|WP_Error
 */
function nexus_sync_contact_request_to_crm( $payload ) {
	$request_type = sanitize_key( (string) ( $payload['request_type'] ?? '' ) );
	$source_map   = [
		'project' => 'project_request',
		'general' => 'general_inquiry',
		'client'  => 'client_request',
	];
	$source       = $source_map[ $request_type ] ?? 'general_inquiry';
	$title_parts  = array_filter(
		[
			sanitize_text_field( (string) ( $payload['name'] ?? '' ) ),
			sanitize_text_field( (string) ( $payload['request_type_label'] ?? '' ) ),
		]
	);

	return nexus_upsert_crm_contact(
		[
			'email'         => (string) ( $payload['email'] ?? '' ),
			'title'         => ! empty( $title_parts ) ? implode( ' - ', $title_parts ) : (string) ( $payload['email'] ?? '' ),
			'source'        => $source,
			'latest_source' => $source,
			'status'        => 'new',
			'segments'      => [ 'contact_inquiry', $source ],
			'refresh_title' => true,
			'meta'          => nexus_get_inquiry_attribution_meta( $payload ) + [
				'_nexus_contact_name'                    => sanitize_text_field( (string) ( $payload['name'] ?? '' ) ),
				'_nexus_contact_request_type'            => $request_type,
				'_nexus_contact_request_type_label'      => sanitize_text_field( (string) ( $payload['request_type_label'] ?? '' ) ),
				'_nexus_contact_focus'                   => sanitize_key( (string) ( $payload['focus'] ?? '' ) ),
				'_nexus_contact_focus_label'             => sanitize_text_field( (string) ( $payload['focus_label'] ?? '' ) ),
				'_nexus_contact_timeline'                => sanitize_key( (string) ( $payload['timeline'] ?? '' ) ),
				'_nexus_contact_timeline_label'          => sanitize_text_field( (string) ( $payload['timeline_label'] ?? '' ) ),
				'_nexus_contact_budget'                  => sanitize_key( (string) ( $payload['budget'] ?? '' ) ),
				'_nexus_contact_budget_label'            => sanitize_text_field( (string) ( $payload['budget_label'] ?? '' ) ),
				'_nexus_contact_website_url'             => esc_url_raw( (string) ( $payload['website_url'] ?? '' ) ),
				'_nexus_contact_linkedin_url'            => esc_url_raw( (string) ( $payload['linkedin_url'] ?? '' ) ),
				'_nexus_contact_company'                 => sanitize_text_field( (string) ( $payload['company'] ?? '' ) ),
				'_nexus_contact_ad_platforms'            => sanitize_text_field( (string) ( $payload['ad_platforms'] ?? '' ) ),
				'_nexus_contact_ad_budget'               => sanitize_key( (string) ( $payload['ad_budget'] ?? '' ) ),
				'_nexus_contact_ad_budget_label'         => sanitize_text_field( (string) ( $payload['ad_budget_label'] ?? '' ) ),
				'_nexus_contact_tracking_setup'          => sanitize_textarea_field( (string) ( $payload['tracking_setup'] ?? '' ) ),
				'_nexus_contact_consent_tool'            => sanitize_text_field( (string) ( $payload['consent_tool'] ?? '' ) ),
				'_nexus_contact_message'                 => sanitize_textarea_field( (string) ( $payload['message'] ?? '' ) ),
				'_nexus_contact_consent_contact_request' => 1,
				'_nexus_contact_last_inquiry_at'         => current_time( 'timestamp' ),
			],
		]
	);
}

/**
 * Sync a White-Label request into the shared CRM.
 *
 * The form asks for task and e-mail only, so the contact carries no name.
 * An existing contact keeps its title; a new one is titled by the sender's
 * domain, or by the address itself for freemail senders.
 *
 * @param array $payload Validated White-Label payload.
 * @return int|WP_Error
 */
function nexus_sync_whitelabel_request_to_crm( $payload ) {
	$email      = (string) ( $payload['email'] ?? '' );
	$case       = sanitize_key( (string) ( $payload['case'] ?? 'aufgabe' ) );
	$cases      = function_exists( 'hu_whitelabel_request_cases' ) ? hu_whitelabel_request_cases() : [];
	$case_label = isset( $cases[ $case ]['label'] ) ? (string) $cases[ $case ]['label'] : 'Konkrete Aufgabe';
	$domain     = strtolower( (string) substr( (string) strrchr( $email, '@' ), 1 ) );
	$is_free    = function_exists( 'nexus_is_review_request_freemail_address' ) && nexus_is_review_request_freemail_address( $email );
	$title      = 'White-Label · ' . ( '' !== $domain && ! $is_free ? $domain : $email );

	return nexus_upsert_crm_contact(
		[
			'email'         => $email,
			'title'         => $title,
			'source'        => 'whitelabel_request',
			'latest_source' => 'whitelabel_request',
			'status'        => 'new',
			'segments'      => [ 'contact_inquiry', 'whitelabel_request' ],
			'refresh_title' => false,
			'meta'          => nexus_get_inquiry_attribution_meta( $payload ) + [
				'_nexus_contact_request_type'       => 'whitelabel',
				'_nexus_contact_request_type_label' => 'White-Label-Anfrage',
				'_nexus_contact_focus'              => 'whitelabel_' . $case,
				'_nexus_contact_focus_label'        => 'White-Label · ' . $case_label,
				'_nexus_contact_timeline'           => '',
				'_nexus_contact_timeline_label'     => sanitize_text_field( (string) ( $payload['timeframe'] ?? '' ) ),
				'_nexus_contact_whitelabel_access'  => sanitize_text_field( (string) ( $payload['access_label'] ?? '' ) ),
				'_nexus_contact_message'            => sanitize_textarea_field( (string) ( $payload['task'] ?? '' ) ),
				'_nexus_contact_last_inquiry_at'    => current_time( 'timestamp' ),
			],
		]
	);
}

/**
 * Record one inbound web inquiry on the CRM timeline.
 *
 * nexus_upsert_crm_contact() keeps one record per e-mail address and
 * overwrites message, topic and budget with the latest inquiry. The activity
 * keeps every inquiry readable, including repeat inquiries of one person.
 * Runs after the upsert, so the sales hook has already opened the opportunity.
 *
 * @param int    $contact_id Contact post ID.
 * @param string $subject    Short activity title.
 * @param string $body       Plain-text summary of the inquiry.
 * @param string $source     CRM source key of the form.
 * @return int Activity ID, or 0 when the activity layer is unavailable.
 */
function nexus_record_inbound_inquiry_activity( $contact_id, $subject, $body, $source ) {
	$contact_id = (int) $contact_id;

	if ( $contact_id <= 0 || ! function_exists( 'nexus_record_crm_activity' ) ) {
		return 0;
	}

	$opportunity_id = function_exists( 'nexus_find_open_crm_opportunity_for_contact' )
		? nexus_find_open_crm_opportunity_for_contact( $contact_id )
		: 0;

	$activity_id = nexus_record_crm_activity(
		[
			'contact_id'     => $contact_id,
			'opportunity_id' => $opportunity_id,
			'type'           => 'inbound_inquiry',
			'channel'        => 'website',
			'direction'      => 'inbound',
			'subject'        => $subject,
			'body'           => $body,
			'provider'       => $source,
		]
	);

	return is_wp_error( $activity_id ) ? 0 : (int) $activity_id;
}

/**
 * Remember an internal lead notification that wp_mail() did not accept.
 *
 * The request itself is stored in the CRM; the failure is logged without
 * personal data, written to the contact timeline and surfaced as an admin
 * notice for seven days, so a missing mail cannot hide a new lead.
 *
 * @param int    $contact_id Contact post ID, 0 when the CRM write failed too.
 * @param string $source     CRM source key of the form.
 * @return void
 */
function nexus_record_lead_notification_failure( $contact_id, $source ) {
	$contact_id = (int) $contact_id;
	$source     = sanitize_key( (string) $source );

	error_log( '[Nexus Lead] Interne Benachrichtigung nicht zugestellt: ' . wp_json_encode( [ 'source' => $source, 'contact_id' => $contact_id ] ) );

	$failures   = nexus_get_recent_lead_notification_failures();
	$failures[] = [
		'contact_id' => $contact_id,
		'source'     => $source,
		'failed_at'  => time(),
	];
	update_option( 'nexus_lead_notification_failures', array_slice( $failures, -10 ), false );

	if ( $contact_id <= 0 ) {
		return;
	}

	update_post_meta( $contact_id, '_nexus_contact_notification_failed_at', current_time( 'timestamp' ) );

	if ( function_exists( 'nexus_record_crm_activity' ) ) {
		nexus_record_crm_activity(
			[
				'contact_id'     => $contact_id,
				'opportunity_id' => function_exists( 'nexus_find_open_crm_opportunity_for_contact' ) ? nexus_find_open_crm_opportunity_for_contact( $contact_id ) : 0,
				'type'           => 'internal_notification',
				'channel'        => 'email',
				'direction'      => 'internal',
				'subject'        => 'Interne Benachrichtigung nicht zugestellt',
				'body'           => 'Die Anfrage ist gespeichert. Die Benachrichtigungs-Mail an das Postfach wurde nicht angenommen; Details in der Mail-Diagnose.',
				'status'         => 'failed',
			]
		);
	}
}

/**
 * Return notification failures of the last seven days.
 *
 * @return array<int, array{contact_id:int, source:string, failed_at:int}>
 */
function nexus_get_recent_lead_notification_failures() {
	$failures = get_option( 'nexus_lead_notification_failures', [] );
	$cutoff   = time() - ( 7 * DAY_IN_SECONDS );
	$recent   = [];

	foreach ( is_array( $failures ) ? $failures : [] as $failure ) {
		if ( ! is_array( $failure ) || (int) ( $failure['failed_at'] ?? 0 ) < $cutoff ) {
			continue;
		}

		$recent[] = [
			'contact_id' => (int) ( $failure['contact_id'] ?? 0 ),
			'source'     => sanitize_key( (string) ( $failure['source'] ?? '' ) ),
			'failed_at'  => (int) $failure['failed_at'],
		];
	}

	return $recent;
}

/**
 * Show recent notification failures on the dashboard and the CRM screens.
 *
 * @return void
 */
function nexus_render_lead_notification_failure_notice() {
	if ( ! current_user_can( 'edit_pages' ) ) {
		return;
	}

	$screen    = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	$screen_id = $screen instanceof WP_Screen ? (string) $screen->id : '';
	$is_crm    = 'dashboard' === $screen_id
		|| ( $screen instanceof WP_Screen && 'nexus_contact' === $screen->post_type )
		|| false !== strpos( $screen_id, 'nexus-crm' );

	if ( ! $is_crm ) {
		return;
	}

	$failures = nexus_get_recent_lead_notification_failures();

	if ( empty( $failures ) ) {
		return;
	}

	$source_labels = nexus_get_crm_contact_source_labels();
	$items         = [];

	foreach ( array_reverse( $failures ) as $failure ) {
		$label = $source_labels[ $failure['source'] ] ?? ( 'contact_request' === $failure['source'] ? 'Kontaktanfrage' : 'Anfrage' );
		$when  = wp_date( 'd.m.Y H:i', $failure['failed_at'] );
		$items[] = $failure['contact_id'] > 0
			? sprintf( '<a href="%1$s">%2$s vom %3$s</a>', esc_url( admin_url( 'post.php?post=' . $failure['contact_id'] . '&action=edit' ) ), esc_html( $label ), esc_html( $when ) )
			: sprintf( '%1$s vom %2$s (nicht im CRM, siehe Server-Log)', esc_html( $label ), esc_html( $when ) );
	}

	printf(
		'<div class="notice notice-warning"><p><strong>%1$s</strong> %2$s</p></div>',
		esc_html__( 'Anfrage-Benachrichtigung nicht zugestellt.', 'blocksy-child' ),
		wp_kses( implode( ' · ', $items ), [ 'a' => [ 'href' => true ] ] )
	);
}
add_action( 'admin_notices', 'nexus_render_lead_notification_failure_notice' );

/**
 * Return a badge class for CRM contact statuses.
 *
 * @param string $status Status value.
 * @return string
 */
function nexus_get_crm_contact_status_badge_class( $status ) {
	$status = sanitize_key( (string) $status );

	if ( '' === $status ) {
		return 'nexus-review-badge-archived';
	}

	return 'nexus-review-badge-' . $status;
}

/**
 * Register meta boxes for the shared CRM contact post type.
 *
 * @return void
 */
function nexus_register_contact_meta_boxes() {
	add_meta_box(
		'nexus-contact-details',
		'Kontakt',
		'nexus_render_contact_details_meta_box',
		'nexus_contact',
		'normal',
		'high'
	);

	add_meta_box(
		'nexus-contact-workflow',
		'CRM-Workflow',
		'nexus_render_contact_workflow_meta_box',
		'nexus_contact',
		'side',
		'high'
	);
}
add_action( 'add_meta_boxes_nexus_contact', 'nexus_register_contact_meta_boxes' );

/**
 * Render the read-only contact detail meta box.
 *
 * @param WP_Post $post Current contact post.
 * @return void
 */
function nexus_render_contact_details_meta_box( $post ) {
	$email             = (string) get_post_meta( $post->ID, '_nexus_contact_email', true );
	$name              = (string) get_post_meta( $post->ID, '_nexus_contact_name', true );
	$source            = (string) get_post_meta( $post->ID, '_nexus_contact_latest_source', true );
	$source_fallback   = (string) get_post_meta( $post->ID, '_nexus_contact_source', true );
	$status            = (string) get_post_meta( $post->ID, '_nexus_contact_status', true );
	$segments          = nexus_get_contact_segments( $post->ID );
	$source_labels     = nexus_get_crm_contact_source_labels();
	$segment_labels    = nexus_get_crm_contact_segment_labels();
	$request_type      = (string) get_post_meta( $post->ID, '_nexus_contact_request_type_label', true );
	$focus_label       = (string) get_post_meta( $post->ID, '_nexus_contact_focus_label', true );
	$timeline_label    = (string) get_post_meta( $post->ID, '_nexus_contact_timeline_label', true );
	$budget_label      = (string) get_post_meta( $post->ID, '_nexus_contact_budget_label', true );
	$website_url       = (string) get_post_meta( $post->ID, '_nexus_contact_website_url', true );
	$linkedin_url      = (string) get_post_meta( $post->ID, '_nexus_contact_linkedin_url', true );
	$message           = (string) get_post_meta( $post->ID, '_nexus_contact_message', true );
	$blog_consent      = (string) get_post_meta( $post->ID, '_nexus_contact_consent_blog_email', true );
	$blog_status       = (string) get_post_meta( $post->ID, '_nexus_contact_blog_status', true );
	$confirmed_at      = (int) get_post_meta( $post->ID, '_nexus_contact_double_opt_in_confirmed_at', true );
	$unsubscribed_at   = (int) get_post_meta( $post->ID, '_nexus_contact_unsubscribed_at', true );
	$created_at        = (int) get_post_meta( $post->ID, '_nexus_contact_created_at', true );
	$updated_at        = (int) get_post_meta( $post->ID, '_nexus_contact_updated_at', true );
	$resolved_source   = $source ?: $source_fallback;
	?>
	<div class="nexus-review-meta">
		<div class="nexus-review-meta-group">
			<strong>E-Mail</strong>
			<p><a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ?: 'Nicht vorhanden' ); ?></a></p>
		</div>
		<?php if ( '' !== $name ) : ?>
			<div class="nexus-review-meta-group">
				<strong>Name</strong>
				<p><?php echo esc_html( $name ); ?></p>
			</div>
		<?php endif; ?>
		<div class="nexus-review-meta-group">
			<strong>Quelle</strong>
			<p><?php echo esc_html( $source_labels[ $resolved_source ] ?? $resolved_source ?: 'Unbekannt' ); ?></p>
		</div>
		<div class="nexus-review-meta-group">
			<strong>Status</strong>
			<p><?php echo esc_html( nexus_get_crm_contact_status_options()[ $status ] ?? 'Unbekannt' ); ?></p>
		</div>
		<?php if ( ! empty( $segments ) ) : ?>
			<div class="nexus-review-meta-group">
				<strong>Segmente</strong>
				<p><?php echo esc_html( implode( ', ', array_map( static function ( $segment ) use ( $segment_labels ) { return $segment_labels[ $segment ] ?? $segment; }, $segments ) ) ); ?></p>
			</div>
		<?php endif; ?>
		<?php if ( '' !== $request_type || '' !== $focus_label || '' !== $timeline_label || '' !== $budget_label ) : ?>
			<div class="nexus-review-meta-group">
				<strong>Anfragekontext</strong>
				<p>
					<?php if ( '' !== $request_type ) : ?>
						<?php echo esc_html( $request_type ); ?><br>
					<?php endif; ?>
					<?php if ( '' !== $focus_label ) : ?>
						Thema: <?php echo esc_html( $focus_label ); ?><br>
					<?php endif; ?>
					<?php if ( '' !== $timeline_label ) : ?>
						Zeitfenster: <?php echo esc_html( $timeline_label ); ?><br>
					<?php endif; ?>
					<?php if ( '' !== $budget_label ) : ?>
						Budget: <?php echo esc_html( $budget_label ); ?>
					<?php endif; ?>
				</p>
			</div>
		<?php endif; ?>
		<?php if ( '' !== $website_url || '' !== $linkedin_url ) : ?>
			<div class="nexus-review-meta-group">
				<strong>Links</strong>
				<p>
					<?php if ( '' !== $website_url ) : ?>
						<a href="<?php echo esc_url( $website_url ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $website_url ); ?></a><br>
					<?php endif; ?>
					<?php if ( '' !== $linkedin_url ) : ?>
						<a href="<?php echo esc_url( $linkedin_url ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $linkedin_url ); ?></a>
					<?php endif; ?>
				</p>
			</div>
		<?php endif; ?>
		<?php if ( '' !== $message ) : ?>
			<div class="nexus-review-meta-group">
				<strong>Nachricht</strong>
				<p><?php echo nl2br( esc_html( $message ) ); ?></p>
			</div>
		<?php endif; ?>
		<?php $whitelabel_access = (string) get_post_meta( $post->ID, '_nexus_contact_whitelabel_access', true ); ?>
		<?php if ( '' !== $whitelabel_access ) : ?>
			<div class="nexus-review-meta-group">
				<strong>Zugänge (White-Label)</strong>
				<p><?php echo esc_html( $whitelabel_access ); ?></p>
			</div>
		<?php endif; ?>
		<?php $attribution_pairs = nexus_get_inquiry_attribution_pairs( nexus_get_contact_attribution_from_meta( $post->ID ) ); ?>
		<?php if ( ! empty( $attribution_pairs ) ) : ?>
			<div class="nexus-review-meta-group">
				<strong>Herkunft der letzten Anfrage</strong>
				<p>
					<?php foreach ( $attribution_pairs as $attribution_label => $attribution_value ) : ?>
						<?php echo esc_html( $attribution_label ); ?>: <?php echo esc_html( $attribution_value ); ?><br>
					<?php endforeach; ?>
				</p>
			</div>
		<?php endif; ?>
		<?php if ( '' !== $blog_consent || '' !== $blog_status || $confirmed_at || $unsubscribed_at ) : ?>
			<div class="nexus-review-meta-group">
				<strong>Blog-Consent</strong>
				<p>
					<?php if ( '' !== $blog_status ) : ?>
						Blog-Status: <?php echo esc_html( $blog_status ); ?><br>
					<?php endif; ?>
					<?php echo esc_html( $blog_consent ?: 'Nicht gesetzt' ); ?><br>
					<?php if ( $confirmed_at ) : ?>
						DOI bestaetigt: <?php echo esc_html( wp_date( 'd.m.Y H:i', $confirmed_at ) ); ?><br>
					<?php endif; ?>
					<?php if ( $unsubscribed_at ) : ?>
						Abgemeldet: <?php echo esc_html( wp_date( 'd.m.Y H:i', $unsubscribed_at ) ); ?>
					<?php endif; ?>
				</p>
			</div>
		<?php endif; ?>
		<?php
		$analysis_signal       = (string) get_post_meta( $post->ID, '_nexus_analysis_signal', true );
		$analysis_score        = (string) get_post_meta( $post->ID, '_nexus_analysis_score', true );
		$analysis_company      = (string) get_post_meta( $post->ID, '_nexus_analysis_company', true );
		$analysis_completed_at = (int) get_post_meta( $post->ID, '_nexus_analysis_completed_at', true );
		$analysis_answers      = get_post_meta( $post->ID, '_nexus_analysis_answers', true );
		$analysis_reasons      = get_post_meta( $post->ID, '_nexus_analysis_reasons', true );
		$analysis_action_plan  = (string) get_post_meta( $post->ID, '_nexus_analysis_action_plan', true );
		$signal_labels         = nexus_get_crm_analysis_signal_labels();
		?>
		<?php if ( '' !== $analysis_signal || '' !== $analysis_company ) : ?>
			<div class="nexus-review-meta-group">
				<strong>Analyse-Ergebnis</strong>
				<p>
					<?php if ( '' !== $analysis_company ) : ?>
						Firma: <?php echo esc_html( $analysis_company ); ?><br>
					<?php endif; ?>
					<?php if ( '' !== $analysis_signal ) : ?>
						Signal:
						<span class="nexus-analysis-signal nexus-analysis-signal--<?php echo esc_attr( $analysis_signal ); ?>"><?php echo esc_html( $signal_labels[ $analysis_signal ] ?? $analysis_signal ); ?></span>
						<?php if ( '' !== $analysis_score ) : ?>
							· Score: <?php echo esc_html( $analysis_score ); ?>/100
						<?php endif; ?>
						<br>
					<?php endif; ?>
					<?php if ( '' !== $analysis_action_plan ) : ?>
						Empfehlung: <?php echo esc_html( $analysis_action_plan ); ?><br>
					<?php endif; ?>
					<?php if ( $analysis_completed_at ) : ?>
						Abgeschlossen: <?php echo esc_html( wp_date( 'd.m.Y H:i', $analysis_completed_at ) ); ?>
					<?php endif; ?>
				</p>
			</div>
		<?php endif; ?>
		<?php if ( is_array( $analysis_reasons ) && ! empty( $analysis_reasons ) ) : ?>
			<div class="nexus-review-meta-group">
				<strong>Begründung der Ampel</strong>
				<ul style="margin:6px 0 0 18px;">
					<?php foreach ( $analysis_reasons as $reason ) : ?>
						<li><?php echo esc_html( (string) $reason ); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>
		<?php if ( is_array( $analysis_answers ) && ! empty( $analysis_answers ) ) : ?>
			<div class="nexus-review-meta-group">
				<strong>Analyse-Antworten</strong>
				<table class="widefat striped" style="margin-top:6px;">
					<tbody>
					<?php foreach ( $analysis_answers as $field => $value ) : ?>
						<?php if ( '' === (string) $value ) { continue; } ?>
						<tr>
							<th scope="row" style="width:40%;"><?php echo esc_html( (string) $field ); ?></th>
							<td><?php echo esc_html( (string) $value ); ?></td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		<?php endif; ?>
		<div class="nexus-review-meta-group">
			<strong>Zeitstempel</strong>
			<p>
				Angelegt: <?php echo esc_html( $created_at ? wp_date( 'd.m.Y H:i', $created_at ) : 'n/a' ); ?><br>
				Aktualisiert: <?php echo esc_html( $updated_at ? wp_date( 'd.m.Y H:i', $updated_at ) : 'n/a' ); ?>
			</p>
		</div>
	</div>
	<style>
		.nexus-analysis-signal { display:inline-block; padding:2px 8px; border-radius:999px; font-size:11px; font-weight:600; letter-spacing:.04em; text-transform:uppercase; }
		.nexus-analysis-signal--green  { background:#e2f1e6; color:#1f5b32; }
		.nexus-analysis-signal--yellow { background:#fbf1cf; color:#7b5b00; }
		.nexus-analysis-signal--red    { background:#f8d9d3; color:#8a2e1f; }
	</style>
	<?php
}

/**
 * Render the editable workflow box for CRM contacts.
 *
 * @param WP_Post $post Current contact post.
 * @return void
 */
function nexus_render_contact_workflow_meta_box( $post ) {
	$status_options = nexus_get_crm_contact_status_options();
	$current_status = (string) get_post_meta( $post->ID, '_nexus_contact_status', true );
	$internal_notes = (string) get_post_meta( $post->ID, '_nexus_contact_internal_notes', true );

	wp_nonce_field( 'nexus_save_contact_workflow', 'nexus_contact_workflow_nonce' );
	?>
	<p>
		<label for="nexus-contact-status"><strong>Status</strong></label><br>
		<select id="nexus-contact-status" name="nexus_contact_status" class="widefat">
			<?php foreach ( $status_options as $value => $label ) : ?>
				<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $current_status, $value ); ?>><?php echo esc_html( $label ); ?></option>
			<?php endforeach; ?>
		</select>
	</p>
	<p>
		<label for="nexus-contact-internal-notes"><strong>Interne Notizen</strong></label><br>
		<textarea id="nexus-contact-internal-notes" name="nexus_contact_internal_notes" class="widefat" rows="8"><?php echo esc_textarea( $internal_notes ); ?></textarea>
	</p>
	<?php
}

/**
 * Persist the CRM contact workflow fields.
 *
 * @param int $post_id Contact post ID.
 * @return void
 */
function nexus_save_contact_workflow_meta( $post_id ) {
	if ( empty( $_POST['nexus_contact_workflow_nonce'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nexus_contact_workflow_nonce'] ) ), 'nexus_save_contact_workflow' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$status_options = nexus_get_crm_contact_status_options();
	$new_status     = isset( $_POST['nexus_contact_status'] ) ? sanitize_key( (string) wp_unslash( $_POST['nexus_contact_status'] ) ) : '';
	$internal_notes = isset( $_POST['nexus_contact_internal_notes'] ) ? sanitize_textarea_field( (string) wp_unslash( $_POST['nexus_contact_internal_notes'] ) ) : '';

	if ( isset( $status_options[ $new_status ] ) ) {
		update_post_meta( $post_id, '_nexus_contact_status', $new_status );

		if ( 'unsubscribed' === $new_status ) {
			update_post_meta( $post_id, '_nexus_contact_consent_blog_email', 'revoked' );
			update_post_meta( $post_id, '_nexus_contact_unsubscribed_at', current_time( 'timestamp' ) );
		}
	}

	update_post_meta( $post_id, '_nexus_contact_internal_notes', $internal_notes );
	update_post_meta( $post_id, '_nexus_contact_updated_at', current_time( 'timestamp' ) );
}
add_action( 'save_post_nexus_contact', 'nexus_save_contact_workflow_meta' );

/**
 * Customize the CRM contact list table columns.
 *
 * @param array $columns Default columns.
 * @return array
 */
function nexus_filter_contact_columns( $columns ) {
	return [
		'cb'                => $columns['cb'],
		'title'             => 'Kontakt',
		'contact_email'     => 'E-Mail',
		'contact_source'    => 'Quelle',
		'contact_segments'  => 'Segmente',
		'contact_signal'    => 'Signal',
		'contact_status'    => 'Status',
		'contact_updated'   => 'Aktualisiert',
		'date'              => $columns['date'],
	];
}
add_filter( 'manage_nexus_contact_posts_columns', 'nexus_filter_contact_columns' );

/**
 * Render custom CRM contact columns.
 *
 * @param string $column  Column key.
 * @param int    $post_id Current contact post ID.
 * @return void
 */
function nexus_render_contact_columns( $column, $post_id ) {
	$source_labels  = nexus_get_crm_contact_source_labels();
	$segment_labels = nexus_get_crm_contact_segment_labels();

	switch ( $column ) {
		case 'contact_email':
			$email = (string) get_post_meta( $post_id, '_nexus_contact_email', true );
			echo $email ? '<a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a>' : 'n/a';
			break;

		case 'contact_source':
			$source = (string) get_post_meta( $post_id, '_nexus_contact_latest_source', true );
			if ( '' === $source ) {
				$source = (string) get_post_meta( $post_id, '_nexus_contact_source', true );
			}
			echo esc_html( $source_labels[ $source ] ?? $source ?: 'Unbekannt' );
			break;

		case 'contact_segments':
			$segments = nexus_get_contact_segments( $post_id );
			if ( empty( $segments ) ) {
				echo 'n/a';
				break;
			}
			echo esc_html( implode( ', ', array_map( static function ( $segment ) use ( $segment_labels ) { return $segment_labels[ $segment ] ?? $segment; }, $segments ) ) );
			break;

		case 'contact_signal':
			$signal = (string) get_post_meta( $post_id, '_nexus_analysis_signal', true );
			$score  = (string) get_post_meta( $post_id, '_nexus_analysis_score', true );
			if ( '' === $signal ) {
				echo '—';
				break;
			}
			$signal_labels = nexus_get_crm_analysis_signal_labels();
			printf(
				'<span class="nexus-analysis-signal nexus-analysis-signal--%1$s">%2$s</span>%3$s',
				esc_attr( $signal ),
				esc_html( $signal_labels[ $signal ] ?? $signal ),
				'' !== $score ? ' · ' . esc_html( $score ) . '/100' : ''
			);
			break;

		case 'contact_status':
			$status = (string) get_post_meta( $post_id, '_nexus_contact_status', true );
			printf(
				'<span class="nexus-review-badge %1$s">%2$s</span>',
				esc_attr( nexus_get_crm_contact_status_badge_class( $status ) ),
				esc_html( nexus_get_crm_contact_status_options()[ $status ] ?? 'Unbekannt' )
			);
			break;

		case 'contact_updated':
			$updated_at = (int) get_post_meta( $post_id, '_nexus_contact_updated_at', true );
			echo esc_html( $updated_at ? wp_date( 'd.m.Y H:i', $updated_at ) : 'n/a' );
			break;
	}
}
add_action( 'manage_nexus_contact_posts_custom_column', 'nexus_render_contact_columns', 10, 2 );

/**
 * Add CRM contact filters above the list table.
 *
 * @param string $post_type Current post type.
 * @return void
 */
function nexus_render_contact_filters( $post_type ) {
	if ( 'nexus_contact' !== $post_type ) {
		return;
	}

	$current_source  = isset( $_GET['nexus_contact_source'] ) ? sanitize_key( (string) wp_unslash( $_GET['nexus_contact_source'] ) ) : '';
	$current_status  = isset( $_GET['nexus_contact_status'] ) ? sanitize_key( (string) wp_unslash( $_GET['nexus_contact_status'] ) ) : '';
	$current_blog_status = isset( $_GET['nexus_contact_blog_status'] ) ? sanitize_key( (string) wp_unslash( $_GET['nexus_contact_blog_status'] ) ) : '';
	$current_segment = isset( $_GET['nexus_contact_segment'] ) ? sanitize_key( (string) wp_unslash( $_GET['nexus_contact_segment'] ) ) : '';
	$current_signal  = isset( $_GET['nexus_contact_signal'] ) ? sanitize_key( (string) wp_unslash( $_GET['nexus_contact_signal'] ) ) : '';
	?>
	<select name="nexus_contact_source">
		<option value="">Alle Quellen</option>
		<?php foreach ( nexus_get_crm_contact_source_labels() as $value => $label ) : ?>
			<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $current_source, $value ); ?>><?php echo esc_html( $label ); ?></option>
		<?php endforeach; ?>
	</select>
	<select name="nexus_contact_status">
		<option value="">Alle Status</option>
		<?php foreach ( nexus_get_crm_contact_status_options() as $value => $label ) : ?>
			<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $current_status, $value ); ?>><?php echo esc_html( $label ); ?></option>
		<?php endforeach; ?>
	</select>
	<select name="nexus_contact_blog_status">
		<option value="">Alle Blog-Status</option>
		<?php foreach ( nexus_get_crm_contact_status_options() as $value => $label ) : ?>
			<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $current_blog_status, $value ); ?>><?php echo esc_html( $label ); ?></option>
		<?php endforeach; ?>
	</select>
	<select name="nexus_contact_segment">
		<option value="">Alle Segmente</option>
		<?php foreach ( nexus_get_crm_contact_segment_labels() as $value => $label ) : ?>
			<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $current_segment, $value ); ?>><?php echo esc_html( $label ); ?></option>
		<?php endforeach; ?>
	</select>
	<select name="nexus_contact_signal">
		<option value="">Alle Signale</option>
		<?php foreach ( nexus_get_crm_analysis_signal_labels() as $value => $label ) : ?>
			<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $current_signal, $value ); ?>>Signal: <?php echo esc_html( $label ); ?></option>
		<?php endforeach; ?>
	</select>
	<?php
}
add_action( 'restrict_manage_posts', 'nexus_render_contact_filters' );

/**
 * Apply CRM contact filters to the admin query.
 *
 * @param WP_Query $query Query object.
 * @return void
 */
function nexus_filter_contact_admin_query( $query ) {
	if ( ! is_admin() || ! $query->is_main_query() ) {
		return;
	}

	if ( 'nexus_contact' !== $query->get( 'post_type' ) ) {
		return;
	}

	$meta_query = (array) $query->get( 'meta_query' );

	if ( ! empty( $_GET['nexus_contact_source'] ) ) {
		$meta_query[] = [
			'key'   => '_nexus_contact_latest_source',
			'value' => sanitize_key( (string) wp_unslash( $_GET['nexus_contact_source'] ) ),
		];
	}

	if ( ! empty( $_GET['nexus_contact_status'] ) ) {
		$meta_query[] = [
			'key'   => '_nexus_contact_status',
			'value' => sanitize_key( (string) wp_unslash( $_GET['nexus_contact_status'] ) ),
		];
	}

	if ( ! empty( $_GET['nexus_contact_blog_status'] ) ) {
		$meta_query[] = [
			'key'   => '_nexus_contact_blog_status',
			'value' => sanitize_key( (string) wp_unslash( $_GET['nexus_contact_blog_status'] ) ),
		];
	}

	if ( ! empty( $_GET['nexus_contact_segment'] ) ) {
		$segment      = sanitize_key( (string) wp_unslash( $_GET['nexus_contact_segment'] ) );
		$meta_query[] = [
			'key'   => '_nexus_contact_segment_' . $segment,
			'value' => 1,
		];
	}

	if ( ! empty( $_GET['nexus_contact_signal'] ) ) {
		$meta_query[] = [
			'key'   => '_nexus_analysis_signal',
			'value' => sanitize_key( (string) wp_unslash( $_GET['nexus_contact_signal'] ) ),
		];
	}

	if ( ! empty( $meta_query ) ) {
		$query->set( 'meta_query', $meta_query );
	}

	$query->set( 'orderby', 'date' );
	$query->set( 'order', 'DESC' );
}
add_action( 'pre_get_posts', 'nexus_filter_contact_admin_query' );

/**
 * Count CRM contacts via a meta query.
 *
 * @param array $meta_query Meta query clauses.
 * @return int
 */
function nexus_count_crm_contacts( $meta_query = [] ) {
	$query = new WP_Query(
		[
			'post_type'      => 'nexus_contact',
			'post_status'    => 'private',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_query'     => $meta_query,
		]
	);

	return (int) $query->found_posts;
}

/**
 * Count active blog subscribers in the shared CRM.
 *
 * @return int
 */
function nexus_count_active_blog_subscribers() {
	return nexus_count_crm_contacts(
		[
			'relation' => 'AND',
			[
				'key'   => '_nexus_contact_segment_blog_notify',
				'value' => 1,
			],
			[
				'key'   => '_nexus_contact_blog_status',
				'value' => 'active',
			],
			[
				'key'   => '_nexus_contact_consent_blog_email',
				'value' => 'confirmed',
			],
		]
	);
}

/**
 * Count pending blog subscribers waiting for DOI confirmation.
 *
 * @return int
 */
function nexus_count_pending_blog_subscribers() {
	if ( function_exists( 'nexus_count_pending_blog_notify_intents' ) ) {
		return nexus_count_pending_blog_notify_intents();
	}

	return nexus_count_crm_contacts(
		[
			'relation' => 'AND',
			[
				'key'   => '_nexus_contact_segment_blog_notify',
				'value' => 1,
			],
			[
				'key'   => '_nexus_contact_blog_status',
				'value' => 'pending',
			],
		]
	);
}

/**
 * Count project requests stored in the shared CRM.
 *
 * @return int
 */
function nexus_count_project_requests() {
	return nexus_count_crm_contacts(
		[
			[
				'key'   => '_nexus_contact_segment_project_request',
				'value' => 1,
			],
		]
	);
}

/**
 * Return recent CRM contacts with optional meta constraints.
 *
 * @param array $args Query overrides.
 * @return array<int, WP_Post>
 */
function nexus_get_recent_crm_contacts( $args = [] ) {
	$defaults = [
		'post_type'              => 'nexus_contact',
		'post_status'            => 'private',
		'posts_per_page'         => 5,
		'orderby'                => 'date',
		'order'                  => 'DESC',
		'no_found_rows'          => true,
		'update_post_meta_cache' => true,
		'update_post_term_cache' => false,
	];

	return get_posts( wp_parse_args( $args, $defaults ) );
}
