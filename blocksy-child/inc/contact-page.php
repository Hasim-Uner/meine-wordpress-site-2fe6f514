<?php
/**
 * Contact page route, lean form handling and email notifications.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the canonical request path for the public contact page.
 *
 * @return string
 */
function nexus_get_contact_request_path() {
	return trailingslashit( '/' . ltrim( (string) wp_parse_url( home_url( '/kontakt/' ), PHP_URL_PATH ), '/' ) );
}

/**
 * Return legacy contact paths that should redirect to /kontakt/.
 *
 * @return array<int, string>
 */
function nexus_get_contact_legacy_paths() {
	return [
		trailingslashit( '/' . ltrim( (string) wp_parse_url( home_url( '/kontaktiere-mich/' ), PHP_URL_PATH ), '/' ) ),
	];
}

/**
 * Check whether the current request targets the canonical contact path.
 *
 * @return bool
 */
function nexus_is_contact_request_path() {
	return nexus_get_current_request_path() === nexus_get_contact_request_path();
}

/**
 * Redirect the previous contact slug to the canonical /kontakt/ URL.
 *
 * @return void
 */
function nexus_redirect_legacy_contact_path() {
	if ( is_admin() || wp_doing_ajax() ) {
		return;
	}

	$request_path = nexus_get_current_request_path();

	if ( ! in_array( $request_path, nexus_get_contact_legacy_paths(), true ) ) {
		return;
	}

	nocache_headers();
	wp_safe_redirect( nexus_get_contact_url(), 301 );
	exit;
}
add_action( 'template_redirect', 'nexus_redirect_legacy_contact_path', 6 );

/**
 * Prevent canonical redirects from fighting the virtual /kontakt/ route.
 *
 * @param string|false $redirect_url Redirect target.
 * @return string|false
 */
function nexus_disable_canonical_redirect_for_contact( $redirect_url ) {
	if ( nexus_is_contact_request_path() ) {
		return false;
	}

	return $redirect_url;
}
add_filter( 'redirect_canonical', 'nexus_disable_canonical_redirect_for_contact' );

/**
 * Turn the contact request into a virtual page when no real page owns /kontakt/.
 *
 * @param bool     $preempt  Existing preempt flag.
 * @param WP_Query $wp_query Current query.
 * @return bool
 */
function nexus_preempt_contact_404( $preempt, $wp_query ) {
	if ( is_admin() || wp_doing_ajax() || ! ( $wp_query instanceof WP_Query ) ) {
		return $preempt;
	}

	// `pre_handle_404` runs before WordPress flips the main query to 404.
	// The route must therefore key off the request path, not `is_404()`.
	if ( ! nexus_is_contact_request_path() ) {
		return $preempt;
	}

	$wp_query->is_404             = false;
	$wp_query->is_page            = true;
	$wp_query->is_singular        = true;
	$wp_query->is_home            = false;
	$wp_query->is_archive         = false;
	$wp_query->is_posts_page      = false;
	$wp_query->queried_object     = null;
	$wp_query->queried_object_id  = 0;
	$wp_query->query_vars['pagename'] = 'kontakt';
	unset( $wp_query->query['error'], $wp_query->query_vars['error'] );

	status_header( 200 );

	return true;
}
add_filter( 'pre_handle_404', 'nexus_preempt_contact_404', 10, 2 );

/**
 * Use the native contact template for the canonical /kontakt/ route.
 *
 * @param string $template Resolved template path.
 * @return string
 */
function nexus_use_virtual_contact_template( $template ) {
	if ( ! nexus_is_contact_request_path() ) {
		return $template;
	}

	$virtual_template = get_stylesheet_directory() . '/page-kontakt.php';

	if ( file_exists( $virtual_template ) ) {
		return $virtual_template;
	}

	return $template;
}
add_filter( 'template_include', 'nexus_use_virtual_contact_template', 99 );

/**
 * Remove 404 body classes for the virtual contact route.
 *
 * @param array<int, string> $classes Existing body classes.
 * @return array<int, string>
 */
function nexus_add_virtual_contact_body_class( $classes ) {
	if ( ! nexus_is_contact_request_path() ) {
		return $classes;
	}

	$classes   = array_diff( $classes, [ 'error404' ] );
	$classes[] = 'page';
	$classes[] = 'page-kontakt';
	$classes[] = 'page-template-default';

	return array_values( array_unique( $classes ) );
}
add_filter( 'body_class', 'nexus_add_virtual_contact_body_class', 20 );

/**
 * Ensure the canonical /kontakt/ page exists as a published WordPress page.
 *
 * This keeps the public route compatible with native sitemap discovery and
 * page resolution helpers while the actual rendering stays template-driven.
 *
 * @return void
 */
function nexus_maybe_ensure_contact_page() {
	if ( wp_installing() || wp_doing_ajax() || wp_doing_cron() ) {
		return;
	}

	$page_id = nexus_get_page_id( [ 'kontakt' ] );

	if ( ! $page_id ) {
		$legacy_page = get_page_by_path( 'kontaktiere-mich' );

		if ( $legacy_page instanceof WP_Post ) {
			$page_id = wp_update_post(
				wp_slash(
					[
						'ID'          => (int) $legacy_page->ID,
						'post_name'   => 'kontakt',
						'post_status' => 'publish',
					]
				),
				true
			);

			if ( is_wp_error( $page_id ) ) {
				return;
			}
		} else {
			$page_id = wp_insert_post(
				wp_slash(
					[
						'post_type'    => 'page',
						'post_status'  => 'publish',
						'post_title'   => 'Kontakt',
						'post_name'    => 'kontakt',
						'post_content' => '',
						'post_excerpt' => 'Kontakt für System-Diagnose, Website-Analyse, Umsetzung und laufende Weiterentwicklung.',
					]
				),
				true
			);

			if ( is_wp_error( $page_id ) ) {
				return;
			}
		}
	}

	$page_id = (int) $page_id;

	update_post_meta( $page_id, '_wp_page_template', 'page-kontakt.php' );
	delete_post_meta( $page_id, 'seo_noindex' );
	delete_post_meta( $page_id, 'rank_math_robots' );

	$current_excerpt = (string) get_post_field( 'post_excerpt', $page_id );

	if ( '' === trim( $current_excerpt ) ) {
		wp_update_post(
			[
				'ID'           => $page_id,
				'post_excerpt' => 'Kontakt für System-Diagnose, Website-Analyse, Umsetzung und laufende Weiterentwicklung.',
			]
		);
	}

	if ( '' === trim( (string) get_post_meta( $page_id, 'seo_title', true ) ) ) {
		update_post_meta( $page_id, 'seo_title', 'Kontakt für WordPress, SEO und CRO | Haşim Üner' );
	}

	if ( '' === trim( (string) get_post_meta( $page_id, 'seo_description', true ) ) ) {
		update_post_meta( $page_id, 'seo_description', 'Kontakt für System-Diagnose, Website-Analyse, Umsetzung und laufende Weiterentwicklung: direkter Einstieg ohne Sales-Team.' );
	}
}
add_action( 'init', 'nexus_maybe_ensure_contact_page', 28 );

/**
 * Return the available contact request type options.
 *
 * @return array<string, array<string, string>>
 */
function nexus_get_contact_request_type_options() {
	return [
		'audit' => [
			'label'       => 'Erstdiagnose / System-Diagnose',
			'description' => 'Der saubere Diagnose-Einstieg, wenn zuerst Klarheit und Priorisierung gebraucht werden.',
		],
		'analysis' => [
			'label'       => 'Website-Analyse',
			'description' => 'Für B2B-Websites, bei denen vor Relaunch, SEO oder Optimierung zuerst die Priorität geklärt werden soll.',
		],
		'implementation' => [
			'label'       => 'Umsetzung / Optimierung',
			'description' => 'Für konkrete Korrekturen, Optimierungen oder einen klar umrissenen Umsetzungsbedarf.',
		],
		'ongoing' => [
			'label'       => 'Laufende Weiterentwicklung',
			'description' => 'Für planbare Growth-Kapazität und kontrollierte Weiterentwicklung ohne Abhängigkeit.',
		],
		'general' => [
			'label'       => 'Allgemeine Anfrage',
			'description' => 'Fragen, Kooperationen oder kurze Abstimmungen ohne Projektrahmen.',
		],
		'client'  => [
			'label'       => 'Bestehender Kunde',
			'description' => 'Laufende Themen, Priorisierung oder nächste Schritte im aktuellen Setup.',
		],
	];
}

/**
 * Return the selectable contact request type labels.
 *
 * @return array<string, string>
 */
function nexus_get_contact_request_type_labels() {
	$labels = [];

	foreach ( nexus_get_contact_request_type_options() as $type_key => $definition ) {
		$labels[ $type_key ] = isset( $definition['label'] ) ? (string) $definition['label'] : (string) $type_key;
	}

	return $labels;
}

/**
 * Return the available contact focus options.
 *
 * @return array<string, array<string, mixed>>
 */
function nexus_get_contact_focus_options() {
	return [
		'audit_scope'      => [
			'label' => 'Erstdiagnose / System-Diagnose',
			'types' => [ 'audit' ],
		],
		'followup_scope'   => [
			'label' => 'Website-Analyse / Priorisierung',
			'types' => [ 'analysis' ],
		],
		'implementation_scope' => [
			'label' => 'Umsetzung / Optimierung',
			'types' => [ 'implementation' ],
		],
		'ongoing_scope'    => [
			'label' => 'Laufende Weiterentwicklung',
			'types' => [ 'ongoing', 'client' ],
		],
		'website_strategy' => [
			'label' => 'Positionierung / Seitenbotschaft',
			'types' => [ 'audit', 'analysis', 'implementation', 'ongoing', 'client' ],
		],
		'seo'              => [
			'label' => 'SEO',
			'types' => [ 'audit', 'analysis', 'implementation', 'ongoing', 'client' ],
		],
		'performance'      => [
			'label' => 'Performance',
			'types' => [ 'audit', 'analysis', 'implementation', 'ongoing', 'client' ],
		],
		'tracking'         => [
			'label' => 'Tracking & Analytics',
			'types' => [ 'audit', 'analysis', 'implementation', 'ongoing', 'client' ],
		],
		'conversion'       => [
			'label' => 'Conversion & CRO',
			'types' => [ 'audit', 'analysis', 'implementation', 'ongoing', 'client' ],
		],
		'relaunch'         => [
			'label' => 'Relaunch / Neue Seite',
			'types' => [ 'analysis', 'implementation' ],
		],
		'support'          => [
			'label' => 'Support / Weiterentwicklung',
			'types' => [ 'implementation', 'ongoing', 'client' ],
		],
		'existing_client'  => [
			'label' => 'Laufendes Projekt / Kundenanliegen',
			'types' => [ 'client' ],
		],
		'question'         => [
			'label' => 'Allgemeine Frage',
			'types' => [ 'general' ],
		],
		'cooperation'      => [
			'label' => 'Kooperation / Netzwerk / Sonstiges',
			'types' => [ 'general' ],
		],
	];
}

/**
 * Return the selectable contact focus labels.
 *
 * @return array<string, string>
 */
function nexus_get_contact_focus_labels() {
	$labels = [];

	foreach ( nexus_get_contact_focus_options() as $focus_key => $definition ) {
		$labels[ $focus_key ] = isset( $definition['label'] ) ? (string) $definition['label'] : (string) $focus_key;
	}

	return $labels;
}

/**
 * Return the available budget options for implementation and ongoing requests.
 *
 * @return array<string, string>
 */
function nexus_get_contact_budget_options() {
	return [
		'under_2000' => 'unter 2.000 EUR',
		'2000_5000'  => '2.000 - 5.000 EUR',
		'5000_10000' => '5.000 - 10.000 EUR',
		'10000_plus' => '10.000 EUR+',
	];
}

/**
 * Return the available timing options for follow-up, implementation and client requests.
 *
 * @return array<string, string>
 */
function nexus_get_contact_timeline_options() {
	return [
		'this_week'         => 'Heute / diese Woche',
		'two_to_four_weeks' => 'In 2-4 Wochen',
		'this_quarter'      => 'In diesem Quartal',
		'flexible'          => 'Noch offen',
	];
}

/**
 * Resolve the internal recipient for contact requests.
 *
 * @return string
 */
function nexus_get_contact_notification_email() {
	$default = function_exists( 'nexus_get_audit_notification_email' )
		? nexus_get_audit_notification_email()
		: (string) get_option( 'admin_email' );

	return (string) apply_filters( 'nexus_contact_notification_email', $default );
}

/**
 * Resolve a label for response and email copy based on request type.
 *
 * @param string $request_type Current request type.
 * @return string
 */
function nexus_get_contact_request_response_label( $request_type ) {
	$labels = [
		'audit'          => 'System-Diagnose Anfrage',
		'analysis'       => 'Website-Analyse',
		'implementation' => 'Umsetzungsanfrage',
		'ongoing'        => 'Weiterentwicklungsanfrage',
		'general'        => 'Anfrage',
		'client'         => 'Kundenanliegen',
	];

	return isset( $labels[ $request_type ] ) ? $labels[ $request_type ] : 'Anfrage';
}

/**
 * Return Brevo/mail tags for a contact request type.
 *
 * @param string $request_type Current request type.
 * @return array<int, string>
 */
function nexus_get_contact_request_mail_tags( $request_type ) {
	$map = [
		'audit'          => [ 'contact_request', 'growth_audit_request' ],
		'analysis'       => [ 'contact_request', 'website_analysis_request' ],
		'implementation' => [ 'contact_request', 'implementation_request' ],
		'ongoing'        => [ 'contact_request', 'ongoing_development_request' ],
		'general'        => [ 'contact_request', 'general_inquiry' ],
		'client'         => [ 'contact_request', 'client_request' ],
	];

	return $map[ $request_type ] ?? [ 'contact_request' ];
}

/**
 * Return the third step copy for the confirmation mail.
 *
 * @param string $request_type Current request type.
 * @return string
 */
function nexus_get_contact_confirmation_step_three( $request_type ) {
	if ( 'client' === $request_type ) {
		return 'Wenn es schneller geht, ziehen wir das Thema direkt in die laufende Priorisierung.';
	}

	if ( 'general' === $request_type ) {
		return 'Wenn ein Termin sinnvoller ist als E-Mail-Pingpong, schlage ich direkt den passenden nächsten Schritt vor.';
	}

	if ( 'audit' === $request_type ) {
		return 'Sie erhalten die Bestätigung und die diagnostische Rückmeldung per E-Mail.';
	}

	if ( 'analysis' === $request_type ) {
		return 'Wenn der Fit klar ist, verdichten wir die Website-Hebel und den sinnvollsten nächsten Schritt sauber.';
	}

	if ( 'ongoing' === $request_type ) {
		return 'Wenn die Zusammenarbeit passt, entsteht daraus planbare Weiterentwicklung statt loses Ticket-Pingpong.';
	}

	return 'Wenn der Fit klar ist, folgt daraus ein sauber umrissener Umsetzungs- oder Optimierungsschritt.';
}

/**
 * Validate and normalize an optional URL field.
 *
 * @param string $value         Raw user value.
 * @param string $error_code    Error code.
 * @param string $error_message Error message.
 * @return string|WP_Error
 */
function nexus_validate_contact_optional_url( $value, $error_code, $error_message ) {
	$value = trim( (string) $value );

	if ( '' === $value ) {
		return '';
	}

	if ( ! preg_match( '#^https?://#i', $value ) ) {
		$value = 'https://' . ltrim( $value, '/' );
	}

	$normalized = esc_url_raw( $value );

	if ( '' === $normalized || ! wp_http_validate_url( $normalized ) ) {
		return new WP_Error( $error_code, $error_message );
	}

	return $normalized;
}

/**
 * Register the public REST route for the lean contact form.
 *
 * @return void
 */
function nexus_register_contact_rest_routes() {
	register_rest_route(
		'nexus/v1',
		'/contact-request',
		[
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => 'nexus_handle_contact_request_submission',
			'permission_callback' => '__return_true',
		]
	);
}
add_action( 'rest_api_init', 'nexus_register_contact_rest_routes' );

/**
 * Handle public contact form submissions.
 *
 * @param WP_REST_Request $request REST request.
 * @return WP_REST_Response
 */
function nexus_handle_contact_request_submission( WP_REST_Request $request ) {
	$payload = $request->get_json_params();
	if ( ! is_array( $payload ) || empty( $payload ) ) {
		$payload = $request->get_body_params();
	}

	$honeypot = isset( $payload['company_website'] ) ? trim( (string) $payload['company_website'] ) : '';
	if ( '' !== $honeypot ) {
		return new WP_REST_Response(
			[
				'ok'      => true,
				'message' => 'Nachricht eingegangen.',
			],
			200
		);
	}

	$rate_limit_error = nexus_validate_contact_request_rate_limit();
	if ( is_wp_error( $rate_limit_error ) ) {
		return new WP_REST_Response(
			[
				'ok'    => false,
				'error' => $rate_limit_error->get_error_message(),
			],
			429
		);
	}

	$validated = nexus_validate_contact_request_payload( $payload );
	if ( is_wp_error( $validated ) ) {
		return new WP_REST_Response(
			[
				'ok'         => false,
				'error'      => $validated->get_error_message(),
				'error_code' => $validated->get_error_code(),
			],
			400
		);
	}

	$contact_id = function_exists( 'nexus_sync_contact_request_to_crm' )
		? nexus_sync_contact_request_to_crm( $validated )
		: 0;

	if ( is_wp_error( $contact_id ) ) {
		return new WP_REST_Response(
			[
				'ok'    => false,
				'error' => 'Die Anfrage konnte gerade nicht sauber im CRM gespeichert werden. Bitte versuchen Sie es erneut.',
			],
			500
		);
	}

	nexus_send_contact_request_admin_notification( $validated );
	nexus_send_contact_request_confirmation( $validated );

	return new WP_REST_Response(
		[
			'ok'      => true,
			'contactId' => $contact_id,
			'message' => sprintf(
				'Danke. Ihre %s ist eingegangen. Sie erhalten innerhalb von 24 Stunden eine Rückmeldung.',
				nexus_get_contact_request_response_label( $validated['request_type'] )
			),
		],
		201
	);
}

/**
 * Validate and sanitize the public contact form payload.
 *
 * @param array $payload Raw payload.
 * @return array|WP_Error
 */
function nexus_validate_contact_request_payload( $payload ) {
	$request_type_options = nexus_get_contact_request_type_options();
	$request_type_labels  = nexus_get_contact_request_type_labels();
	$focus_options        = nexus_get_contact_focus_options();
	$focus_labels         = nexus_get_contact_focus_labels();
	$budget_options       = nexus_get_contact_budget_options();
	$timeline_options     = nexus_get_contact_timeline_options();
	$name                 = isset( $payload['name'] ) ? sanitize_text_field( (string) $payload['name'] ) : '';
	$email                = isset( $payload['email'] ) ? sanitize_email( (string) $payload['email'] ) : '';
	$request_type         = isset( $payload['request_type'] ) ? sanitize_key( (string) $payload['request_type'] ) : '';
	$website_url_raw      = isset( $payload['website_url'] ) ? (string) $payload['website_url'] : '';
	$linkedin_url_raw     = isset( $payload['linkedin_url'] ) ? (string) $payload['linkedin_url'] : '';
	$focus                = isset( $payload['focus'] ) ? sanitize_key( (string) $payload['focus'] ) : '';
	$timeline             = isset( $payload['timeline'] ) ? sanitize_key( (string) $payload['timeline'] ) : '';
	$message              = isset( $payload['message'] ) ? sanitize_textarea_field( (string) $payload['message'] ) : '';
	$budget               = isset( $payload['budget'] ) ? sanitize_key( (string) $payload['budget'] ) : '';
	$consent              = ! empty( $payload['consent'] );
	$minimum_message_len  = 'general' === $request_type ? 18 : 24;

	if ( '' === $name ) {
		return new WP_Error( 'missing_name', 'Bitte Ihren Namen angeben.' );
	}

	if ( '' === $email || ! is_email( $email ) ) {
		return new WP_Error( 'invalid_email', 'Bitte eine gültige E-Mail-Adresse angeben.' );
	}

	if ( ! isset( $request_type_options[ $request_type ] ) ) {
		return new WP_Error( 'missing_request_type', 'Bitte auswählen, worum es geht.' );
	}

	$website_url = nexus_validate_contact_optional_url(
		$website_url_raw,
		'invalid_website',
		'Bitte eine gültige Website-URL angeben.'
	);
	if ( is_wp_error( $website_url ) ) {
		return $website_url;
	}

	$linkedin_url = nexus_validate_contact_optional_url(
		$linkedin_url_raw,
		'invalid_linkedin',
		'Bitte einen gültigen LinkedIn-Link angeben.'
	);
	if ( is_wp_error( $linkedin_url ) ) {
		return $linkedin_url;
	}

	if ( ! isset( $focus_options[ $focus ] ) ) {
		return new WP_Error( 'missing_focus', 'Bitte ein passendes Thema auswählen.' );
	}

	$focus_types = isset( $focus_options[ $focus ]['types'] ) ? (array) $focus_options[ $focus ]['types'] : [];
	if ( empty( $focus_types ) || ! in_array( $request_type, $focus_types, true ) ) {
		return new WP_Error( 'invalid_focus_type', 'Bitte ein Thema wählen, das zu Ihrer Anfrage passt.' );
	}

	if ( in_array( $request_type, [ 'analysis', 'implementation', 'ongoing', 'client' ], true ) ) {
		if ( '' !== $timeline && ! isset( $timeline_options[ $timeline ] ) ) {
			return new WP_Error( 'invalid_timeline', 'Bitte ein gültiges Zeitfenster auswählen.' );
		}
	} else {
		$timeline = '';
	}

	if ( in_array( $request_type, [ 'implementation', 'ongoing' ], true ) ) {
		if ( '' !== $budget && ! isset( $budget_options[ $budget ] ) ) {
			return new WP_Error( 'invalid_budget', 'Bitte ein gültiges Budget auswählen.' );
		}
	} else {
		$budget = '';
	}

	if ( '' === trim( $message ) || mb_strlen( trim( $message ) ) < $minimum_message_len ) {
		return new WP_Error( 'message_too_short', 'Bitte Ihr Anliegen kurz und konkret beschreiben.' );
	}

	if ( ! $consent ) {
		return new WP_Error( 'missing_consent', 'Bitte der Verarbeitung Ihrer Nachricht zustimmen.' );
	}

	$ads_source   = isset( $payload['ads_source'] ) ? sanitize_text_field( (string) $payload['ads_source'] ) : '';
	$ads_keyword  = isset( $payload['ads_keyword'] ) ? sanitize_text_field( (string) $payload['ads_keyword'] ) : '';
	$utm_medium   = isset( $payload['utm_medium'] ) ? sanitize_text_field( (string) $payload['utm_medium'] ) : '';
	$utm_campaign = isset( $payload['utm_campaign'] ) ? sanitize_text_field( (string) $payload['utm_campaign'] ) : '';
	$gclid        = isset( $payload['gclid'] ) ? sanitize_text_field( (string) $payload['gclid'] ) : '';
	$matchtype    = isset( $payload['matchtype'] ) ? sanitize_text_field( (string) $payload['matchtype'] ) : '';

	return [
		'name'               => $name,
		'email'              => $email,
		'request_type'       => $request_type,
		'request_type_label' => $request_type_labels[ $request_type ],
		'website_url'        => $website_url,
		'linkedin_url'       => $linkedin_url,
		'focus'              => $focus,
		'focus_label'        => $focus_labels[ $focus ],
		'timeline'           => $timeline,
		'timeline_label'     => '' !== $timeline ? $timeline_options[ $timeline ] : '',
		'message'            => $message,
		'budget'             => $budget,
		'budget_label'       => '' !== $budget ? $budget_options[ $budget ] : '',
		'ads_source'         => $ads_source,
		'ads_keyword'        => $ads_keyword,
		'utm_medium'         => $utm_medium,
		'utm_campaign'       => $utm_campaign,
		'gclid'              => $gclid,
		'matchtype'          => $matchtype,
	];
}

/**
 * Rate-limit public contact form submissions per IP and hour.
 *
 * @return true|WP_Error
 */
function nexus_validate_contact_request_rate_limit() {
	$ip_address = function_exists( 'nexus_get_review_request_ip' ) ? nexus_get_review_request_ip() : '';
	if ( '' === $ip_address ) {
		return true;
	}

	$key   = 'nexus_contact_rl_' . md5( $ip_address . gmdate( 'YmdH' ) );
	$count = (int) get_transient( $key );

	if ( $count >= 8 ) {
		return new WP_Error( 'rate_limited', 'Zu viele Nachrichten in kurzer Zeit. Bitte später erneut versuchen.' );
	}

	set_transient( $key, $count + 1, HOUR_IN_SECONDS );

	return true;
}

/**
 * Send an HTML mail with the shared branded shell when available.
 *
 * @param string $recipient Recipient email.
 * @param string $subject   Email subject.
 * @param string $html      Email HTML body.
 * @param array  $headers   Optional headers.
 * @return void
 */
function nexus_send_contact_html_mail( $recipient, $subject, $html, $headers = [] ) {
	if ( function_exists( 'nexus_send_transactional_html_mail' ) ) {
		nexus_send_transactional_html_mail( $recipient, $subject, $html, $headers );
		return;
	}

	if ( function_exists( 'nexus_send_audit_html_mail' ) ) {
		nexus_send_audit_html_mail( $recipient, $subject, $html, $headers );
		return;
	}

	$headers   = (array) $headers;
	$headers[] = 'Content-Type: text/html; charset=UTF-8';
	wp_mail( $recipient, $subject, $html, $headers );
}

/**
 * Wrap a contact email in the shared branded shell when available.
 *
 * @param array $args Mail shell arguments.
 * @return string
 */
function nexus_get_contact_email_shell( $args = [] ) {
	if ( function_exists( 'nexus_get_transactional_email_shell' ) ) {
		return nexus_get_transactional_email_shell( $args );
	}

	if ( function_exists( 'nexus_get_audit_email_shell' ) ) {
		return nexus_get_audit_email_shell( $args );
	}

	return (string) ( $args['content'] ?? '' );
}

/**
 * Send the internal contact notification.
 *
 * @param array $payload Validated payload.
 * @return void
 */
function nexus_send_contact_request_admin_notification( $payload ) {
	$recipient = nexus_get_contact_notification_email();
	if ( ! $recipient || ! is_email( $recipient ) ) {
		return;
	}

	$subject = sprintf(
		'[%s] Neue %s - %s',
		wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ),
		$payload['request_type_label'],
		$payload['name']
	);
	$headers = [];

	if ( ! empty( $payload['email'] ) && is_email( $payload['email'] ) ) {
		$headers[] = 'Reply-To: ' . $payload['email'];
	}

	if ( function_exists( 'nexus_append_mail_tags_header' ) ) {
		$headers = nexus_append_mail_tags_header(
			$headers,
			array_merge(
				nexus_get_contact_request_mail_tags( $payload['request_type'] ),
				[ 'internal_notification' ]
			)
		);
	}

	$meta_rows = sprintf(
		'<strong style="color:#f7f3ee;">Anfragetyp:</strong> %1$s<br><strong style="color:#f7f3ee;">Thema:</strong> %2$s',
		esc_html( $payload['request_type_label'] ),
		esc_html( $payload['focus_label'] )
	);

	if ( '' !== $payload['timeline_label'] ) {
		$meta_rows .= sprintf(
			'<br><strong style="color:#f7f3ee;">Zeitfenster:</strong> %s',
			esc_html( $payload['timeline_label'] )
		);
	}

	if ( '' !== $payload['budget_label'] ) {
		$meta_rows .= sprintf(
			'<br><strong style="color:#f7f3ee;">Budget:</strong> %s',
			esc_html( $payload['budget_label'] )
		);
	}

	if ( '' !== $payload['website_url'] ) {
		$meta_rows .= sprintf(
			'<br><strong style="color:#f7f3ee;">Website:</strong> <a href="%1$s" style="color:#f7f3ee;">%2$s</a>',
			esc_url( $payload['website_url'] ),
			esc_html( $payload['website_url'] )
		);
	}

	if ( '' !== $payload['linkedin_url'] ) {
		$meta_rows .= sprintf(
			'<br><strong style="color:#f7f3ee;">LinkedIn:</strong> <a href="%1$s" style="color:#f7f3ee;">%2$s</a>',
			esc_url( $payload['linkedin_url'] ),
			esc_html( $payload['linkedin_url'] )
		);
	}

	$ads_source_label  = '' !== $payload['ads_source'] ? $payload['ads_source'] : 'Organisch/Direkt';
	$ads_keyword_label = '' !== $payload['ads_keyword'] ? $payload['ads_keyword'] : 'Keines';
	$utm_medium_label  = '' !== $payload['utm_medium'] ? $payload['utm_medium'] : '';
	$utm_campaign_label = '' !== $payload['utm_campaign'] ? $payload['utm_campaign'] : '';
	$gclid_label       = '' !== $payload['gclid'] ? $payload['gclid'] : '';
	$matchtype_label   = '' !== $payload['matchtype'] ? $payload['matchtype'] : '';

	$content = sprintf(
		'<table role="presentation" width="100%%" cellspacing="0" cellpadding="0" border="0" style="margin:0 0 18px 0; border-collapse:separate; border-spacing:0 10px;">
			<tr>
				<td style="padding:14px 16px; border:1px solid rgba(255,255,255,0.08); border-radius:18px; background:rgba(255,255,255,0.03); font-family:Helvetica, Arial, sans-serif;">
					<div style="font-size:11px; letter-spacing:0.08em; text-transform:uppercase; color:#9ea8b2; margin-bottom:8px;">Kontaktanfrage</div>
					<div style="font-size:14px; line-height:1.75; color:#c5ced7;">
						<strong style="color:#f7f3ee;">Name:</strong> %1$s<br>
						<strong style="color:#f7f3ee;">E-Mail:</strong> %2$s<br>
						%3$s
					</div>
				</td>
			</tr>
			<tr>
				<td style="padding:16px; border:1px solid rgba(255,255,255,0.08); border-radius:18px; background:rgba(255,255,255,0.03); font-family:Helvetica, Arial, sans-serif;">
					<div style="font-size:11px; letter-spacing:0.08em; text-transform:uppercase; color:#9ea8b2; margin-bottom:8px;">Nachricht</div>
					<div style="font-size:14px; line-height:1.85; color:#c5ced7;">%4$s</div>
				</td>
			</tr>
			<tr>
				<td style="padding:14px 16px; border:1px solid rgba(255,255,255,0.08); border-radius:18px; background:rgba(255,255,255,0.03); font-family:Helvetica, Arial, sans-serif;">
					<div style="font-size:11px; letter-spacing:0.08em; text-transform:uppercase; color:#9ea8b2; margin-bottom:8px;">Tracking Info</div>
					<div style="font-size:14px; line-height:1.75; color:#c5ced7;">
						<strong style="color:#f7f3ee;">Quelle:</strong> %5$s<br>
						<strong style="color:#f7f3ee;">Keyword:</strong> %6$s' .
		( '' !== $utm_medium_label ? '<br><strong style="color:#f7f3ee;">Medium:</strong> ' . esc_html( $utm_medium_label ) : '' ) .
		( '' !== $utm_campaign_label ? '<br><strong style="color:#f7f3ee;">Kampagne:</strong> ' . esc_html( $utm_campaign_label ) : '' ) .
		( '' !== $gclid_label ? '<br><strong style="color:#f7f3ee;">GCLID:</strong> ' . esc_html( $gclid_label ) : '' ) .
		( '' !== $matchtype_label ? '<br><strong style="color:#f7f3ee;">Matchtype:</strong> ' . esc_html( $matchtype_label ) : '' ) . '
					</div>
				</td>
			</tr>
		</table>',
		esc_html( $payload['name'] ),
		esc_html( $payload['email'] ),
		$meta_rows,
		nl2br( esc_html( $payload['message'] ) ),
		esc_html( $ads_source_label ),
		esc_html( $ads_keyword_label )
	);

	$html = nexus_get_contact_email_shell(
		[
			'preheader' => 'Neue ' . strtolower( $payload['request_type_label'] ) . ' von ' . $payload['name'],
			'eyebrow'   => $payload['request_type_label'],
			'headline'  => 'Neue Anfrage eingegangen',
			'intro'     => 'Eine neue qualifizierte Anfrage ist über die Kontaktseite eingegangen.',
			'content'   => $content,
			'footer'    => 'Sie können direkt auf diese E-Mail antworten. Reply-To zeigt bereits auf die anfragende Person.',
		]
	);

	nexus_send_contact_html_mail( $recipient, $subject, $html, $headers );
}

/**
 * Send a short confirmation email to the requester.
 *
 * @param array $payload Validated payload.
 * @return void
 */
function nexus_send_contact_request_confirmation( $payload ) {
	if ( empty( $payload['email'] ) || ! is_email( $payload['email'] ) ) {
		return;
	}

	$reply_to  = nexus_get_contact_notification_email();
	$subject   = sprintf( '[%s] %s eingegangen', wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ), $payload['request_type_label'] );
	$meta_rows = sprintf(
		'<strong style="color:#f7f3ee;">Anfragetyp:</strong> %1$s<br><strong style="color:#f7f3ee;">Thema:</strong> %2$s',
		esc_html( $payload['request_type_label'] ),
		esc_html( $payload['focus_label'] )
	);

	if ( '' !== $payload['timeline_label'] ) {
		$meta_rows .= sprintf(
			'<br><strong style="color:#f7f3ee;">Zeitfenster:</strong> %s',
			esc_html( $payload['timeline_label'] )
		);
	}

	if ( '' !== $payload['budget_label'] ) {
		$meta_rows .= sprintf(
			'<br><strong style="color:#f7f3ee;">Budget:</strong> %s',
			esc_html( $payload['budget_label'] )
		);
	}

	if ( '' !== $payload['website_url'] ) {
		$meta_rows .= sprintf(
			'<br><strong style="color:#f7f3ee;">Website:</strong> <a href="%1$s" style="color:#f7f3ee;">%2$s</a>',
			esc_url( $payload['website_url'] ),
			esc_html( $payload['website_url'] )
		);
	}

	if ( '' !== $payload['linkedin_url'] ) {
		$meta_rows .= sprintf(
			'<br><strong style="color:#f7f3ee;">LinkedIn:</strong> <a href="%1$s" style="color:#f7f3ee;">%2$s</a>',
			esc_url( $payload['linkedin_url'] ),
			esc_html( $payload['linkedin_url'] )
		);
	}

	$content = sprintf(
		'<table role="presentation" width="100%%" cellspacing="0" cellpadding="0" border="0" style="margin:0 0 18px 0; border-collapse:separate; border-spacing:0 10px;">
			<tr>
				<td style="padding:14px 16px; border:1px solid rgba(255,255,255,0.08); border-radius:18px; background:rgba(255,255,255,0.03); font-family:Helvetica, Arial, sans-serif;">
					<div style="font-size:11px; letter-spacing:0.08em; text-transform:uppercase; color:#9ea8b2; margin-bottom:8px;">Was jetzt passiert</div>
					<div style="font-size:14px; line-height:1.8; color:#c5ced7;">
						<strong style="color:#f7f3ee;">1.</strong> Ihre Nachricht ist sauber eingegangen.<br>
						<strong style="color:#f7f3ee;">2.</strong> Sie erhalten innerhalb von 24 Stunden eine persönliche Rückmeldung.<br>
						<strong style="color:#f7f3ee;">3.</strong> %1$s
					</div>
				</td>
			</tr>
			<tr>
				<td style="padding:14px 16px; border:1px solid rgba(255,255,255,0.08); border-radius:18px; background:rgba(255,255,255,0.03); font-family:Helvetica, Arial, sans-serif;">
					<div style="font-size:11px; letter-spacing:0.08em; text-transform:uppercase; color:#9ea8b2; margin-bottom:8px;">Ihre Angaben</div>
					<div style="font-size:14px; line-height:1.8; color:#c5ced7;">
						%2$s<br>
						<strong style="color:#f7f3ee;">Nachricht:</strong> %3$s
					</div>
				</td>
			</tr>
		</table>',
		esc_html( nexus_get_contact_confirmation_step_three( $payload['request_type'] ) ),
		$meta_rows,
		esc_html( wp_trim_words( $payload['message'], 18, '...' ) )
	);

	$html = nexus_get_contact_email_shell(
		[
			'preheader' => 'Ihre Anfrage ist eingegangen.',
			'eyebrow'   => $payload['request_type_label'],
			'headline'  => 'Ihre Anfrage ist eingegangen.',
			'intro'     => 'Danke, ' . $payload['name'] . '. Ich prüfe Ihre Angaben persönlich und melde mich zeitnah zurück.',
			'content'   => $content,
			'footer'    => 'Viele Grüße, Haşim Üner',
		]
	);

	$headers = [];
	if ( $reply_to && is_email( $reply_to ) ) {
		$headers[] = 'Reply-To: ' . $reply_to;
	}

	if ( function_exists( 'nexus_append_mail_tags_header' ) ) {
		$headers = nexus_append_mail_tags_header(
			$headers,
			array_merge(
				nexus_get_contact_request_mail_tags( $payload['request_type'] ),
				[ 'lead_confirmation' ]
			)
		);
	}

	nexus_send_contact_html_mail( $payload['email'], $subject, $html, $headers );
}
