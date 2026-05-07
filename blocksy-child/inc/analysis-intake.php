<?php
/**
 * Anfrage-System-Analyse intake.
 *
 * Receives the React-Funnel result + lean contact block, persists into the
 * shared CRM (nexus_contact) and notifies the admin and the lead via Brevo.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const HU_ANALYSIS_FIELD_LABELS = [
	'industry'               => 'Branche',
	'offerType'              => 'Angebotsart',
	'employeeRange'          => 'Teamgröße',
	'country'                => 'Land',
	'plzRegion'              => 'PLZ / Region',
	'offerFocus'             => 'Hauptleistung',
	'averageOrderValueRange' => 'Auftragswert',
	'adBudgetRange'          => 'Werbebudget / Monat',
	'websiteUrl'             => 'Website',
	'cms'                    => 'CMS',
	'pixelPresent'           => 'Werbepixel',
	'gtmPresent'             => 'Google Tag Manager',
	'consentMode'            => 'Consent Mode',
	'metaCapi'               => 'Meta CAPI',
	'crmPresent'             => 'CRM',
	'responseTime'           => 'Antwortzeit',
	'responsible'            => 'Verantwortlich',
	'targetRegion'           => 'Zielregion',
	'expectedChannelMix'     => 'Hauptkanal',
	'competition'            => 'Wettbewerb',
];

const HU_ANALYSIS_SIGNAL_LABELS = [
	'green'  => 'Grün',
	'yellow' => 'Gelb',
	'red'    => 'Rot',
];

const HU_ANALYSIS_RATE_LIMIT_PER_HOUR = 6;

/**
 * Resolve the Cal.com URL used after a successful submission. Filterable.
 */
function hu_get_analysis_calcom_base_url() {
	$default = function_exists( 'nexus_get_audit_calendar_url' )
		? nexus_get_audit_calendar_url()
		: 'https://cal.com/hasim-uener/30min?overlayCalendar=true';

	return (string) apply_filters( 'hu_analysis_calcom_url', $default );
}

/**
 * Build a Cal.com URL prefilled with the lead's data and analysis result.
 *
 * @param array $payload Validated payload.
 */
function hu_build_analysis_calcom_url( array $payload ) {
	$base   = hu_get_analysis_calcom_base_url();
	$signal = (string) ( $payload['signal'] ?? '' );
	$signal_label = HU_ANALYSIS_SIGNAL_LABELS[ $signal ] ?? '';
	$notes  = sprintf(
		'Anfrage-System-Analyse · Signal %s · Score %s/100',
		$signal_label,
		(int) ( $payload['score'] ?? 0 )
	);

	$query = [
		'name'           => $payload['name'] ?? '',
		'email'          => $payload['email'] ?? '',
		'notes'          => $notes,
		'metadata[firma]' => $payload['company'] ?? '',
		'metadata[signal]' => $signal,
		'metadata[score]' => (string) ( $payload['score'] ?? '' ),
	];

	$query = array_filter( $query, static function ( $value ) { return '' !== (string) $value; } );

	if ( empty( $query ) ) {
		return $base;
	}

	return $base . ( false === strpos( $base, '?' ) ? '?' : '&' ) . http_build_query( $query );
}

/**
 * Register the public REST route for the analysis-intake submission.
 */
function hu_register_analysis_intake_routes() {
	if ( ! defined( 'HU_FEATURE_READINESS_SUBMIT' ) || ! HU_FEATURE_READINESS_SUBMIT ) {
		return;
	}

	register_rest_route(
		'nexus/v1',
		'/analysis-submit',
		[
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => 'hu_handle_analysis_submit',
			'permission_callback' => '__return_true',
		]
	);
}
add_action( 'rest_api_init', 'hu_register_analysis_intake_routes' );

/**
 * Handle a POST to /wp-json/nexus/v1/analysis-submit.
 *
 * @param WP_REST_Request $request REST request.
 * @return WP_REST_Response
 */
function hu_handle_analysis_submit( WP_REST_Request $request ) {
	$payload = $request->get_json_params();
	if ( ! is_array( $payload ) || empty( $payload ) ) {
		$payload = $request->get_body_params();
	}

	$honeypot = isset( $payload['company_website'] ) ? trim( (string) $payload['company_website'] ) : '';
	if ( '' !== $honeypot ) {
		return new WP_REST_Response(
			[
				'ok'        => true,
				'calcomUrl' => hu_get_analysis_calcom_base_url(),
			],
			200
		);
	}

	$rate_limit = hu_validate_analysis_rate_limit();
	if ( is_wp_error( $rate_limit ) ) {
		return new WP_REST_Response(
			[ 'ok' => false, 'error' => $rate_limit->get_error_message() ],
			429
		);
	}

	$validated = hu_validate_analysis_payload( $payload );
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

	$contact_id = hu_sync_analysis_to_crm( $validated );
	if ( is_wp_error( $contact_id ) ) {
		return new WP_REST_Response(
			[ 'ok' => false, 'error' => 'Die Analyse konnte nicht im CRM gespeichert werden.' ],
			500
		);
	}

	hu_send_analysis_admin_notification( $validated, (int) $contact_id );
	hu_send_analysis_lead_confirmation( $validated );

	return new WP_REST_Response(
		[
			'ok'        => true,
			'contactId' => (int) $contact_id,
			'signal'    => $validated['signal'],
			'calcomUrl' => hu_build_analysis_calcom_url( $validated ),
		],
		201
	);
}

/**
 * Per-IP, per-hour rate-limit for the public analysis intake.
 *
 * @return true|WP_Error
 */
function hu_validate_analysis_rate_limit() {
	$ip = function_exists( 'nexus_get_review_request_ip' ) ? nexus_get_review_request_ip() : '';
	if ( '' === $ip ) {
		return true;
	}

	$key   = 'hu_analysis_rl_' . md5( $ip . gmdate( 'YmdH' ) );
	$count = (int) get_transient( $key );

	if ( $count >= HU_ANALYSIS_RATE_LIMIT_PER_HOUR ) {
		return new WP_Error( 'rate_limited', 'Zu viele Einsendungen in kurzer Zeit. Bitte später erneut versuchen.' );
	}

	set_transient( $key, $count + 1, HOUR_IN_SECONDS );

	return true;
}

/**
 * Validate and sanitize the analysis submission payload.
 *
 * @param array $payload Raw payload.
 * @return array|WP_Error
 */
function hu_validate_analysis_payload( $payload ) {
	$name    = isset( $payload['name'] ) ? sanitize_text_field( (string) $payload['name'] ) : '';
	$company = isset( $payload['company'] ) ? sanitize_text_field( (string) $payload['company'] ) : '';
	$email   = isset( $payload['email'] ) ? sanitize_email( (string) $payload['email'] ) : '';
	$consent = ! empty( $payload['consent'] );
	$signal  = isset( $payload['signal'] ) ? sanitize_key( (string) $payload['signal'] ) : '';
	$score   = isset( $payload['score'] ) ? (int) $payload['score'] : -1;

	if ( '' === $name ) {
		return new WP_Error( 'missing_name', 'Bitte Ihren Namen angeben.' );
	}

	if ( '' === $company ) {
		return new WP_Error( 'missing_company', 'Bitte den Betriebsnamen angeben.' );
	}

	if ( '' === $email || ! is_email( $email ) ) {
		return new WP_Error( 'invalid_email', 'Bitte eine gültige E-Mail-Adresse angeben.' );
	}

	if ( ! $consent ) {
		return new WP_Error( 'missing_consent', 'Bitte der Verarbeitung Ihrer Angaben zustimmen.' );
	}

	if ( ! isset( HU_ANALYSIS_SIGNAL_LABELS[ $signal ] ) ) {
		return new WP_Error( 'invalid_signal', 'Ungültiges Analyse-Signal.' );
	}

	if ( $score < 0 || $score > 100 ) {
		return new WP_Error( 'invalid_score', 'Ungültiger Score.' );
	}

	$raw_answers   = is_array( $payload['answers'] ?? null ) ? $payload['answers'] : [];
	$answers_clean = [];
	foreach ( HU_ANALYSIS_FIELD_LABELS as $field => $label ) {
		$value = isset( $raw_answers[ $field ] ) ? sanitize_text_field( (string) $raw_answers[ $field ] ) : '';
		$answers_clean[ $field ] = $value;
	}

	$reasons = [];
	if ( is_array( $payload['reasons'] ?? null ) ) {
		foreach ( $payload['reasons'] as $reason ) {
			$clean = sanitize_text_field( (string) $reason );
			if ( '' !== $clean ) {
				$reasons[] = $clean;
			}
		}
	}
	$reasons = array_slice( $reasons, 0, 12 );

	$action_plan_label = isset( $payload['action_plan_label'] ) ? sanitize_text_field( (string) $payload['action_plan_label'] ) : '';

	$ads_source   = isset( $payload['ads_source'] ) ? sanitize_text_field( (string) $payload['ads_source'] ) : '';
	$ads_keyword  = isset( $payload['ads_keyword'] ) ? sanitize_text_field( (string) $payload['ads_keyword'] ) : '';
	$utm_medium   = isset( $payload['utm_medium'] ) ? sanitize_text_field( (string) $payload['utm_medium'] ) : '';
	$utm_campaign = isset( $payload['utm_campaign'] ) ? sanitize_text_field( (string) $payload['utm_campaign'] ) : '';
	$gclid        = isset( $payload['gclid'] ) ? sanitize_text_field( (string) $payload['gclid'] ) : '';

	$website_url = '';
	$website_raw = isset( $payload['answers']['websiteUrl'] ) ? trim( (string) $payload['answers']['websiteUrl'] ) : '';
	if ( '' !== $website_raw ) {
		$normalized  = preg_match( '#^https?://#i', $website_raw ) ? $website_raw : ( 'https://' . ltrim( $website_raw, '/' ) );
		$website_url = esc_url_raw( $normalized );
	}

	return [
		'name'              => $name,
		'company'           => $company,
		'email'             => strtolower( $email ),
		'signal'            => $signal,
		'signal_label'      => HU_ANALYSIS_SIGNAL_LABELS[ $signal ],
		'score'             => $score,
		'answers'           => $answers_clean,
		'reasons'           => $reasons,
		'action_plan_label' => $action_plan_label,
		'website_url'       => $website_url,
		'ads_source'        => $ads_source,
		'ads_keyword'       => $ads_keyword,
		'utm_medium'        => $utm_medium,
		'utm_campaign'      => $utm_campaign,
		'gclid'             => $gclid,
	];
}

/**
 * Persist the analysis lead into the shared CRM.
 *
 * @param array $payload Validated payload.
 * @return int|WP_Error Contact post ID.
 */
function hu_sync_analysis_to_crm( array $payload ) {
	$title = trim( $payload['name'] . ' · ' . $payload['company'] );

	$meta = [
		'_nexus_contact_name'                    => $payload['name'],
		'_nexus_contact_request_type'            => 'analysis',
		'_nexus_contact_request_type_label'      => 'Anfrage-System-Analyse',
		'_nexus_contact_website_url'             => $payload['website_url'],
		'_nexus_contact_consent_contact_request' => 1,
		'_nexus_contact_last_inquiry_at'         => current_time( 'timestamp' ),
		'_nexus_contact_ads_source'              => $payload['ads_source'],
		'_nexus_contact_ads_keyword'             => $payload['ads_keyword'],
		'_nexus_analysis_company'                => $payload['company'],
		'_nexus_analysis_signal'                 => $payload['signal'],
		'_nexus_analysis_score'                  => (string) $payload['score'],
		'_nexus_analysis_answers'                => $payload['answers'],
		'_nexus_analysis_reasons'                => $payload['reasons'],
		'_nexus_analysis_action_plan'            => $payload['action_plan_label'],
		'_nexus_analysis_completed_at'           => current_time( 'timestamp' ),
	];

	return nexus_upsert_crm_contact(
		[
			'email'         => $payload['email'],
			'title'         => '' !== $title ? $title : $payload['email'],
			'source'        => 'request_analysis',
			'latest_source' => 'request_analysis',
			'status'        => 'new',
			'segments'      => [ 'analysis_lead' ],
			'refresh_title' => true,
			'meta'          => $meta,
		]
	);
}

/**
 * Render the per-question table HTML used in admin + lead emails.
 */
function hu_render_analysis_answers_table( array $answers ) {
	$rows = '';
	foreach ( HU_ANALYSIS_FIELD_LABELS as $field => $label ) {
		$value = $answers[ $field ] ?? '';
		if ( '' === $value ) {
			continue;
		}
		$rows .= sprintf(
			'<tr><td style="padding:6px 10px; border-bottom:1px solid rgba(255,255,255,0.06); color:#9ea8b2; font-size:12px; letter-spacing:.04em; text-transform:uppercase;">%1$s</td><td style="padding:6px 10px; border-bottom:1px solid rgba(255,255,255,0.06); color:#f7f3ee; font-size:14px;">%2$s</td></tr>',
			esc_html( $label ),
			esc_html( (string) $value )
		);
	}

	if ( '' === $rows ) {
		return '';
	}

	return '<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse:collapse; margin-top:8px;">' . $rows . '</table>';
}

/**
 * Send the internal admin notification for a new analysis lead.
 */
function hu_send_analysis_admin_notification( array $payload, $contact_id ) {
	$recipient = function_exists( 'nexus_get_contact_notification_email' )
		? nexus_get_contact_notification_email()
		: get_option( 'admin_email' );

	if ( ! $recipient || ! is_email( $recipient ) ) {
		return;
	}

	$subject = sprintf(
		'[%s] Analyse · %s · %s/100 — %s',
		wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ),
		strtoupper( $payload['signal_label'] ),
		(int) $payload['score'],
		$payload['company']
	);

	$headers = [];
	if ( ! empty( $payload['email'] ) && is_email( $payload['email'] ) ) {
		$headers[] = 'Reply-To: ' . $payload['email'];
	}
	if ( function_exists( 'nexus_append_mail_tags_header' ) ) {
		$headers = nexus_append_mail_tags_header( $headers, [ 'request_analysis', 'internal_notification', 'signal_' . $payload['signal'] ] );
	}

	$reasons_html = '';
	if ( ! empty( $payload['reasons'] ) ) {
		$reasons_html = '<ul style="margin:0; padding:0 0 0 18px; color:#c5ced7; font-size:14px; line-height:1.7;">';
		foreach ( $payload['reasons'] as $reason ) {
			$reasons_html .= '<li>' . esc_html( $reason ) . '</li>';
		}
		$reasons_html .= '</ul>';
	}

	$crm_link = '';
	if ( $contact_id > 0 ) {
		$crm_url  = admin_url( 'post.php?post=' . $contact_id . '&action=edit' );
		$crm_link = '<a href="' . esc_url( $crm_url ) . '" style="color:#f7f3ee;">CRM-Eintrag öffnen</a>';
	}

	$website_link = '';
	if ( '' !== $payload['website_url'] ) {
		$website_link = '<a href="' . esc_url( $payload['website_url'] ) . '" style="color:#f7f3ee;">' . esc_html( $payload['website_url'] ) . '</a>';
	}

	$content = sprintf(
		'<table role="presentation" width="100%%" cellspacing="0" cellpadding="0" border="0" style="margin:0 0 18px 0; border-collapse:separate; border-spacing:0 10px;">
			<tr><td style="padding:14px 16px; border:1px solid rgba(255,255,255,0.08); border-radius:18px; background:rgba(255,255,255,0.03); font-family:Helvetica, Arial, sans-serif;">
				<div style="font-size:11px; letter-spacing:0.08em; text-transform:uppercase; color:#9ea8b2; margin-bottom:8px;">Lead</div>
				<div style="font-size:14px; line-height:1.75; color:#c5ced7;">
					<strong style="color:#f7f3ee;">Name:</strong> %1$s<br>
					<strong style="color:#f7f3ee;">Firma:</strong> %2$s<br>
					<strong style="color:#f7f3ee;">E-Mail:</strong> %3$s<br>
					%4$s%5$s
				</div>
			</td></tr>
			<tr><td style="padding:14px 16px; border:1px solid rgba(255,255,255,0.08); border-radius:18px; background:rgba(255,255,255,0.03); font-family:Helvetica, Arial, sans-serif;">
				<div style="font-size:11px; letter-spacing:0.08em; text-transform:uppercase; color:#9ea8b2; margin-bottom:8px;">Ergebnis</div>
				<div style="font-size:14px; line-height:1.85; color:#c5ced7;">
					<strong style="color:#f7f3ee;">Signal:</strong> %6$s<br>
					<strong style="color:#f7f3ee;">Score:</strong> %7$d / 100<br>
					%8$s%9$s
				</div>
			</td></tr>
			<tr><td style="padding:14px 16px; border:1px solid rgba(255,255,255,0.08); border-radius:18px; background:rgba(255,255,255,0.03); font-family:Helvetica, Arial, sans-serif;">
				<div style="font-size:11px; letter-spacing:0.08em; text-transform:uppercase; color:#9ea8b2; margin-bottom:8px;">Antworten</div>
				%10$s
			</td></tr>
		</table>',
		esc_html( $payload['name'] ),
		esc_html( $payload['company'] ),
		esc_html( $payload['email'] ),
		'' !== $website_link ? '<strong style="color:#f7f3ee;">Website:</strong> ' . $website_link . '<br>' : '',
		'' !== $crm_link ? '<br>' . $crm_link : '',
		esc_html( $payload['signal_label'] ),
		(int) $payload['score'],
		'' !== $payload['action_plan_label'] ? '<strong style="color:#f7f3ee;">Empfehlung:</strong> ' . esc_html( $payload['action_plan_label'] ) . '<br>' : '',
		'' !== $reasons_html ? '<div style="margin-top:8px;"><strong style="color:#f7f3ee;">Begründung:</strong></div>' . $reasons_html : '',
		hu_render_analysis_answers_table( $payload['answers'] )
	);

	$html = function_exists( 'nexus_get_contact_email_shell' )
		? nexus_get_contact_email_shell(
			[
				'preheader' => sprintf( 'Analyse · %s · %d/100 · %s', $payload['signal_label'], (int) $payload['score'], $payload['company'] ),
				'eyebrow'   => 'Anfrage-System-Analyse',
				'headline'  => 'Neuer qualifizierter Analyse-Lead',
				'intro'     => 'Vorqualifiziert über alle 20 Funnel-Schritte. Reply-To geht direkt an die Person.',
				'content'   => $content,
				'footer'    => 'CRM-Eintrag und Antworten sind im Backend hinterlegt.',
			]
		)
		: $content;

	if ( function_exists( 'nexus_send_contact_html_mail' ) ) {
		nexus_send_contact_html_mail( $recipient, $subject, $html, $headers );
	} else {
		wp_mail( $recipient, $subject, $html, array_merge( [ 'Content-Type: text/html; charset=UTF-8' ], $headers ) );
	}
}

/**
 * Send the lead-facing confirmation email after a successful submission.
 */
function hu_send_analysis_lead_confirmation( array $payload ) {
	if ( empty( $payload['email'] ) || ! is_email( $payload['email'] ) ) {
		return;
	}

	$subject = sprintf(
		'[%s] Ihre Anfrage-System-Analyse — %s / %d Punkte',
		wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ),
		$payload['signal_label'],
		(int) $payload['score']
	);

	$calcom_url = hu_build_analysis_calcom_url( $payload );

	$content = sprintf(
		'<table role="presentation" width="100%%" cellspacing="0" cellpadding="0" border="0" style="margin:0 0 18px 0; border-collapse:separate; border-spacing:0 10px;">
			<tr><td style="padding:14px 16px; border:1px solid rgba(255,255,255,0.08); border-radius:18px; background:rgba(255,255,255,0.03); font-family:Helvetica, Arial, sans-serif;">
				<div style="font-size:11px; letter-spacing:0.08em; text-transform:uppercase; color:#9ea8b2; margin-bottom:8px;">Ihr Ergebnis</div>
				<div style="font-size:14px; line-height:1.8; color:#c5ced7;">
					<strong style="color:#f7f3ee;">Signal:</strong> %1$s<br>
					<strong style="color:#f7f3ee;">Score:</strong> %2$d / 100<br>
					%3$s
				</div>
			</td></tr>
			<tr><td style="padding:14px 16px; border:1px solid rgba(255,255,255,0.08); border-radius:18px; background:rgba(255,255,255,0.03); font-family:Helvetica, Arial, sans-serif;">
				<div style="font-size:11px; letter-spacing:0.08em; text-transform:uppercase; color:#9ea8b2; margin-bottom:8px;">Was jetzt passiert</div>
				<div style="font-size:14px; line-height:1.85; color:#c5ced7;">
					<strong style="color:#f7f3ee;">1.</strong> Ihre Analyse ist gespeichert — ich sehe alle Antworten und das Signal.<br>
					<strong style="color:#f7f3ee;">2.</strong> Sie können direkt einen 30-Min-Slot buchen — ich bereite das Gespräch mit Ihren Daten vor.<br>
					<strong style="color:#f7f3ee;">3.</strong> Falls Sie nichts buchen, melde ich mich persönlich innerhalb 24 Stunden.
				</div>
				<div style="margin-top:14px;">
					<a href="%4$s" style="display:inline-block; background:#E08A3C; color:#1A0F05; padding:12px 22px; border-radius:999px; font-weight:600; text-decoration:none;">Termin buchen</a>
				</div>
			</td></tr>
		</table>',
		esc_html( $payload['signal_label'] ),
		(int) $payload['score'],
		'' !== $payload['action_plan_label'] ? '<strong style="color:#f7f3ee;">Empfehlung:</strong> ' . esc_html( $payload['action_plan_label'] ) : '',
		esc_url( $calcom_url )
	);

	$html = function_exists( 'nexus_get_contact_email_shell' )
		? nexus_get_contact_email_shell(
			[
				'preheader' => 'Ihre Analyse ist eingegangen — nächster Schritt: Termin buchen.',
				'eyebrow'   => 'Anfrage-System-Analyse',
				'headline'  => 'Danke, ' . $payload['name'] . '.',
				'intro'     => 'Ihre 20 Antworten sind eingegangen. Hier ist das Ergebnis und der direkte Weg in ein 30-Minuten-Gespräch.',
				'content'   => $content,
				'footer'    => 'Viele Grüße, Haşim Üner',
			]
		)
		: $content;

	$headers = [];
	$reply_to = function_exists( 'nexus_get_contact_notification_email' ) ? nexus_get_contact_notification_email() : '';
	if ( $reply_to && is_email( $reply_to ) ) {
		$headers[] = 'Reply-To: ' . $reply_to;
	}
	if ( function_exists( 'nexus_append_mail_tags_header' ) ) {
		$headers = nexus_append_mail_tags_header( $headers, [ 'request_analysis', 'lead_confirmation', 'signal_' . $payload['signal'] ] );
	}

	if ( function_exists( 'nexus_send_contact_html_mail' ) ) {
		nexus_send_contact_html_mail( $payload['email'], $subject, $html, $headers );
	} else {
		wp_mail( $payload['email'], $subject, $html, array_merge( [ 'Content-Type: text/html; charset=UTF-8' ], $headers ) );
	}
}

