<?php
/**
 * Handover route: POST /nexus/v1/chat/handover.
 *
 * Called only by the chat window after the visitor clicked "Anfrage senden"
 * on the prefilled card. Same sequence as the White-Label intake: CRM contact,
 * timeline activity, internal mail, failure marker, inquiry event.
 * The recipient is fixed (canonical contact address); no request field lets
 * the visitor or the model choose an address. No confirmation mail goes out,
 * otherwise the route would relay model-written text to arbitrary inboxes.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const HU_CHAT_HANDOVER_SOURCE      = 'chat_handover';
const HU_CHAT_HANDOVER_HOURLY      = 5;
const HU_CHAT_HANDOVER_LOG_CHARS   = 12000;

/** @return array<string, string> */
function hu_chat_route_labels(): array {
	return [
		'projekt'    => 'Projekt',
		'whitelabel' => 'White-Label',
		'energie'    => 'Energie',
		'sonstiges'  => 'Sonstiges',
	];
}

/**
 * Validate the card. Only the listed keys are read; a recipient field, if
 * sent, is ignored by construction.
 *
 * @return array<string, mixed>|WP_Error
 */
function hu_chat_validate_handover( $payload ) {
	if ( ! is_array( $payload ) ) {
		return new WP_Error( 'invalid_payload', 'Die Anfrage ist unvollständig.' );
	}
	$name    = hu_chat_limit_text( $payload['name'] ?? '', 120 );
	$email   = sanitize_email( is_string( $payload['email'] ?? null ) ? $payload['email'] : '' );
	$summary = hu_chat_limit_text( $payload['zusammenfassung'] ?? '', 1500, true );
	$routes  = hu_chat_route_labels();
	$route   = is_string( $payload['weg'] ?? null ) && isset( $routes[ $payload['weg'] ] ) ? $payload['weg'] : 'sonstiges';

	if ( mb_strlen( $name, 'UTF-8' ) < 2 ) {
		return new WP_Error( 'missing_name', 'Bitte einen Namen angeben.' );
	}
	if ( '' === $email || ! is_email( $email ) ) {
		return new WP_Error( 'invalid_email', 'Bitte eine gültige E-Mail-Adresse angeben.' );
	}
	if ( mb_strlen( $summary, 'UTF-8' ) < 10 ) {
		return new WP_Error( 'missing_summary', 'Bitte das Anliegen kurz beschreiben.' );
	}

	$transcript = '';
	if ( true === ( $payload['verlauf_mitsenden'] ?? false ) ) {
		$messages = hu_chat_normalize_transcript( $payload['verlauf'] ?? null );
		foreach ( $messages as $message ) {
			$transcript .= ( 'user' === $message['role'] ? 'Besucher' : 'Assistent' ) . ': ' . $message['text'] . "\n\n";
		}
		$transcript = mb_substr( trim( $transcript ), 0, HU_CHAT_HANDOVER_LOG_CHARS, 'UTF-8' );
	}

	return [
		'name'              => $name,
		'email'             => $email,
		'firma'             => hu_chat_limit_text( $payload['firma'] ?? '', 160 ),
		'weg'               => $route,
		'weg_label'         => $routes[ $route ],
		'zusammenfassung'   => $summary,
		'naechster_schritt' => hu_chat_limit_text( $payload['naechster_schritt'] ?? '', 300 ),
		'page'              => hu_chat_normalize_page( $payload['page'] ?? '/' ),
		'verlauf'           => $transcript,
	];
}

/**
 * Transcript for the internal mail only, opt-in by the visitor.
 *
 * @return array<int, array{role: string, text: string}>
 */
function hu_chat_normalize_transcript( $raw ): array {
	if ( ! is_array( $raw ) || ! array_is_list( $raw ) ) {
		return [];
	}
	$out = [];
	foreach ( array_slice( $raw, -50 ) as $item ) {
		if ( ! is_array( $item ) || ! in_array( $item['role'] ?? '', [ 'user', 'assistant' ], true ) ) {
			continue;
		}
		$text = hu_chat_limit_text( $item['text'] ?? '', 'user' === $item['role'] ? HU_CHAT_MAX_USER_CHARS : HU_CHAT_MAX_ASSISTANT_CHARS, true );
		if ( '' !== $text ) {
			$out[] = [ 'role' => $item['role'], 'text' => $text ];
		}
	}
	return $out;
}

/** @return int|WP_Error */
function hu_chat_sync_handover_to_crm( array $lead ) {
	if ( ! function_exists( 'nexus_upsert_crm_contact' ) ) {
		return 0;
	}
	$title = 'Chat · ' . ( '' !== $lead['firma'] ? $lead['firma'] : $lead['name'] );
	return nexus_upsert_crm_contact(
		[
			'email'         => $lead['email'],
			'title'         => $title,
			'source'        => HU_CHAT_HANDOVER_SOURCE,
			'latest_source' => HU_CHAT_HANDOVER_SOURCE,
			'status'        => 'new',
			'segments'      => [ 'contact_inquiry', HU_CHAT_HANDOVER_SOURCE ],
			'refresh_title' => false,
			'meta'          => [
				'_nexus_contact_name'               => $lead['name'],
				'_nexus_contact_company'            => $lead['firma'],
				'_nexus_contact_request_type'       => 'chat',
				'_nexus_contact_request_type_label' => 'Chat-Anfrage',
				'_nexus_contact_focus'              => 'chat_' . $lead['weg'],
				'_nexus_contact_focus_label'        => 'Chat · ' . $lead['weg_label'],
				'_nexus_contact_message'            => $lead['zusammenfassung'],
				'_nexus_contact_last_inquiry_at'    => current_time( 'timestamp' ),
			],
		]
	);
}

function hu_chat_handover_activity_text( array $lead ): string {
	$lines = [
		'Weg: ' . $lead['weg_label'],
		'Name: ' . $lead['name'],
	];
	if ( '' !== $lead['firma'] ) {
		$lines[] = 'Firma: ' . $lead['firma'];
	}
	$lines[] = 'Seite: ' . $lead['page'];
	$lines[] = '';
	$lines[] = $lead['zusammenfassung'];
	if ( '' !== $lead['naechster_schritt'] ) {
		$lines[] = '';
		$lines[] = 'Nächster Schritt: ' . $lead['naechster_schritt'];
	}
	return implode( "\n", $lines );
}

/**
 * Recipient of the internal notification: the canonical contact address. The
 * server-side filter mirrors the other intake routes (tests, staging); nothing
 * from the request or the model reaches it.
 */
function hu_chat_handover_notification_email(): string {
	return (string) apply_filters( 'hu_chat_handover_notification_email', hu_get_contact_email() );
}

/** Internal notification to the canonical contact address. */
function hu_chat_send_handover_notification( array $lead, int $contact_id ): bool {
	$recipient = hu_chat_handover_notification_email();
	if ( ! $recipient || ! is_email( $recipient ) ) {
		return false;
	}
	$crm = $contact_id > 0
		? '<a href="' . esc_url( admin_url( 'post.php?post=' . $contact_id . '&action=edit' ) ) . '" style="color:#f7f3ee;">Im CRM öffnen</a>'
		: 'Nicht im CRM gespeichert – bitte manuell anlegen.';
	$box = static function ( string $label, string $html ): string {
		return '<tr><td style="padding:14px 16px; border:1px solid rgba(255,255,255,0.08); border-radius:18px; background:rgba(255,255,255,0.03); font-family:Helvetica, Arial, sans-serif;">'
			. '<div style="font-size:11px; letter-spacing:0.08em; text-transform:uppercase; color:#9ea8b2; margin-bottom:8px;">' . esc_html( $label ) . '</div>'
			. '<div style="font-size:14px; line-height:1.8; color:#c5ced7;">' . $html . '</div></td></tr>';
	};
	$frame = esc_html( 'Weg: ' . $lead['weg_label'] ) . '<br>' . esc_html( 'Name: ' . $lead['name'] )
		. ( '' !== $lead['firma'] ? '<br>' . esc_html( 'Firma: ' . $lead['firma'] ) : '' )
		. '<br>' . esc_html( 'E-Mail: ' . $lead['email'] )
		. '<br>' . esc_html( 'Seite: ' . home_url( $lead['page'] ) )
		. '<br>' . esc_html( 'Zeit: ' . wp_date( 'd.m.Y H:i' ) );
	$rows = $box( 'Rahmen', $frame ) . $box( 'Zusammenfassung des Assistenten', nl2br( esc_html( $lead['zusammenfassung'] ) ) );
	if ( '' !== $lead['naechster_schritt'] ) {
		$rows .= $box( 'Nächster Schritt', esc_html( $lead['naechster_schritt'] ) );
	}
	$rows .= $box( 'Gesprächsverlauf', '' !== $lead['verlauf'] ? nl2br( esc_html( $lead['verlauf'] ) ) : 'Nicht mitgesendet.' );
	$rows .= $box( 'CRM', wp_kses( $crm, [ 'a' => [ 'href' => true, 'style' => true ] ] ) );
	$content = '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin:0 0 18px 0; border-collapse:separate; border-spacing:0 10px;">' . $rows . '</table>';

	$args = [
		'preheader' => 'Neue Anfrage aus dem Chat.',
		'eyebrow'   => 'KI-Assistent',
		'headline'  => 'Neue Anfrage aus dem Chat.',
		'intro'     => 'Von ' . $lead['name'] . ' (' . $lead['email'] . '). Die Zusammenfassung stammt vom Assistenten und wurde vom Besucher geprüft.',
		'content'   => $content,
		'footer'    => 'Antwort direkt auf diese Mail geht an den Besucher.',
	];
	$html    = function_exists( 'nexus_get_transactional_email_shell' ) ? nexus_get_transactional_email_shell( $args ) : '<h1>' . esc_html( $args['headline'] ) . '</h1>' . $content;
	$subject = sprintf( '[Chat] %s — %s', $lead['weg_label'], $lead['email'] );
	$headers = [ 'Reply-To: ' . $lead['email'] ];
	if ( function_exists( 'nexus_append_mail_tags_header' ) ) {
		$headers = nexus_append_mail_tags_header( $headers, [ HU_CHAT_HANDOVER_SOURCE, 'internal_notification' ] );
	}
	if ( function_exists( 'nexus_send_contact_html_mail' ) ) {
		return (bool) nexus_send_contact_html_mail( $recipient, $subject, $html, $headers );
	}
	$headers[] = 'Content-Type: text/html; charset=UTF-8';
	return (bool) wp_mail( $recipient, $subject, $html, $headers );
}

/** @return WP_REST_Response */
function hu_chat_handle_handover( WP_REST_Request $request ) {
	$payload = $request->get_json_params();
	$payload = is_array( $payload ) ? $payload : [];

	// Honeypot: answered like a success so bots learn nothing.
	if ( '' !== trim( (string) ( $payload['company_website'] ?? '' ) ) ) {
		return new WP_REST_Response( [ 'ok' => true, 'message' => 'Anfrage eingegangen.' ], 200 );
	}
	$limited = ! hu_chat_rate_allow( 'handover', HU_CHAT_HANDOVER_HOURLY );
	if ( ! $limited && function_exists( 'nexus_validate_contact_request_rate_limit' ) ) {
		$limited = is_wp_error( nexus_validate_contact_request_rate_limit() );
	}
	if ( $limited ) {
		return new WP_REST_Response( [ 'ok' => false, 'error' => 'Zu viele Anfragen in kurzer Zeit. Bitte später erneut versuchen oder direkt an ' . hu_get_contact_email() . ' schreiben.' ], 429 );
	}
	$lead = hu_chat_validate_handover( $payload );
	if ( is_wp_error( $lead ) ) {
		return new WP_REST_Response( [ 'ok' => false, 'error' => $lead->get_error_message(), 'error_code' => $lead->get_error_code() ], 400 );
	}

	$contact_id = hu_chat_sync_handover_to_crm( $lead );
	$contact_id = is_wp_error( $contact_id ) ? 0 : (int) $contact_id;
	if ( $contact_id > 0 && function_exists( 'nexus_record_inbound_inquiry_activity' ) ) {
		nexus_record_inbound_inquiry_activity( $contact_id, 'Chat-Anfrage · ' . $lead['weg_label'], hu_chat_handover_activity_text( $lead ), HU_CHAT_HANDOVER_SOURCE );
	}
	$notified = hu_chat_send_handover_notification( $lead, $contact_id );

	if ( 0 === $contact_id && ! $notified ) {
		error_log( '[Nexus Lead] Chat-Anfrage weder im CRM gespeichert noch zugestellt.' );
		return new WP_REST_Response( [ 'ok' => false, 'error' => 'Die Anfrage konnte gerade nicht übermittelt werden. Bitte schreiben Sie direkt an ' . hu_get_contact_email() . '.' ], 500 );
	}
	if ( ! $notified && function_exists( 'nexus_record_lead_notification_failure' ) ) {
		nexus_record_lead_notification_failure( $contact_id, HU_CHAT_HANDOVER_SOURCE );
	}
	if ( function_exists( 'hu_record_inquiry_event' ) ) {
		hu_record_inquiry_event( 'chat', [ 'landing_page_url' => home_url( $lead['page'] ) ] );
	}

	return new WP_REST_Response(
		[
			'ok'      => true,
			'message' => 'Danke, Ihre Anfrage ist angekommen. ' . hu_response_promise( 'sentence' ),
		],
		201
	);
}

function hu_chat_register_handover_route(): void {
	if ( ! hu_chat_assistant_enabled() ) {
		return;
	}
	register_rest_route( 'nexus/v1', '/chat/handover', [
		'methods'             => WP_REST_Server::CREATABLE,
		'callback'            => 'hu_chat_handle_handover',
		'permission_callback' => 'hu_chat_conversation_permission',
	] );
}
add_action( 'rest_api_init', 'hu_chat_register_handover_route' );
