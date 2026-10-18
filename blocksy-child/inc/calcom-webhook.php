<?php
/**
 * Cal.com-Buchungen im CRM.
 *
 * Cal.com meldet Buchung, Verschiebung und Absage an
 * POST /wp-json/nexus/v1/calcom-webhook. Die Route prüft die HMAC-Signatur
 * (`x-cal-signature-256`, SHA-256 über den Rohtext mit dem Webhook-Geheimnis),
 * legt den Kontakt im CRM an oder ordnet ihn zu, schreibt die Timeline und
 * schickt eine interne Mail mit Quelle und Vorgeschichte.
 *
 * Die Quelle kommt aus der Buchungs-Metadata (`quelle`, `seite`), die
 * cal-embed.js beim Öffnen des Kalenders mitgibt. Ohne Geheimnis ist die Route
 * nicht registriert. Contract: docs/architecture/SYSTEM_MAP.md.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const HU_CALCOM_SOURCE        = 'calendar_booking';
const HU_CALCOM_SECRET_OPTION = 'hu_calcom_webhook_secret';
const HU_CALCOM_STATUS_OPTION = 'hu_calcom_webhook_status';
const HU_CALCOM_MAX_BODY      = 262144;

/** @return array<string, array{label: string, verb: string}> */
function hu_calcom_events(): array {
	return [
		'BOOKING_CREATED'     => [ 'label' => 'Termin gebucht', 'verb' => 'gebucht' ],
		'BOOKING_RESCHEDULED' => [ 'label' => 'Termin verschoben', 'verb' => 'verschoben' ],
		'BOOKING_CANCELLED'   => [ 'label' => 'Termin abgesagt', 'verb' => 'abgesagt' ],
	];
}

/** wp-config (`HU_CALCOM_WEBHOOK_SECRET`) vor der Admin-Einstellung. */
function hu_calcom_webhook_secret(): string {
	$secret = function_exists( 'nexus_get_mail_runtime_value' ) ? (string) nexus_get_mail_runtime_value( [ 'HU_CALCOM_WEBHOOK_SECRET' ] ) : '';
	if ( '' === $secret ) {
		$secret = (string) get_option( HU_CALCOM_SECRET_OPTION, '' );
	}
	return trim( $secret );
}

function hu_calcom_signature_valid( string $body, string $signature, string $secret ): bool {
	$signature = strtolower( trim( $signature ) );
	if ( '' === $secret || ! preg_match( '/\A[a-f0-9]{64}\z/', $signature ) ) {
		return false;
	}
	return hash_equals( hash_hmac( 'sha256', $body, $secret ), $signature );
}

/** Nur Zahlen und feste Kategorien für die Statusanzeige, keine Personendaten. */
function hu_calcom_record_status( string $result, string $trigger = '' ): void {
	update_option(
		HU_CALCOM_STATUS_OPTION,
		[
			'at'      => time(),
			'result'  => sanitize_key( $result ),
			'trigger' => preg_replace( '/[^A-Z_]/', '', $trigger ),
		],
		false
	);
}

/** Ein Wert aus `responses` (Objekt mit `value` oder direkter String). */
function hu_calcom_response_value( array $responses, string $key ): string {
	$value = $responses[ $key ] ?? '';
	if ( is_array( $value ) ) {
		$value = $value['value'] ?? '';
	}
	return is_scalar( $value ) ? (string) $value : '';
}

/**
 * Buchung aus dem Webhook normalisieren.
 *
 * @return array<string, mixed>|WP_Error
 */
function hu_calcom_normalize_booking( array $event ) {
	$trigger = is_string( $event['triggerEvent'] ?? null ) ? $event['triggerEvent'] : '';
	$payload = is_array( $event['payload'] ?? null ) ? $event['payload'] : [];
	$events  = hu_calcom_events();
	if ( ! isset( $events[ $trigger ] ) ) {
		return new WP_Error( 'ignored', 'Ereignis wird nicht verarbeitet.' );
	}
	$attendee  = is_array( $payload['attendees'][0] ?? null ) ? $payload['attendees'][0] : [];
	$responses = is_array( $payload['responses'] ?? null ) ? $payload['responses'] : [];
	$metadata  = is_array( $payload['metadata'] ?? null ) ? $payload['metadata'] : [];
	$email     = sanitize_email( (string) ( $attendee['email'] ?? hu_calcom_response_value( $responses, 'email' ) ) );
	$start     = strtotime( (string) ( $payload['startTime'] ?? '' ) );
	$uid       = preg_replace( '/[^A-Za-z0-9_-]/', '', (string) ( $payload['uid'] ?? '' ) );
	if ( '' === $email || ! is_email( $email ) || false === $start || '' === $uid ) {
		return new WP_Error( 'invalid_booking', 'Buchung ohne E-Mail, Startzeit oder Kennung.' );
	}
	$source = sanitize_key( (string) ( $metadata['quelle'] ?? '' ) );
	$page   = (string) ( $metadata['seite'] ?? '' );
	$page   = preg_match( '#\A/[A-Za-z0-9/_\-.~%]{0,199}\z#', $page ) ? $page : '';

	return [
		'trigger'  => $trigger,
		'label'    => $events[ $trigger ]['label'],
		'uid'      => $uid,
		'name'     => mb_substr( sanitize_text_field( (string) ( $attendee['name'] ?? hu_calcom_response_value( $responses, 'name' ) ) ), 0, 120 ),
		'email'    => $email,
		'start'    => $start,
		'title'    => mb_substr( sanitize_text_field( (string) ( $payload['title'] ?? $payload['type'] ?? '' ) ), 0, 160 ),
		'timezone' => sanitize_text_field( (string) ( $attendee['timeZone'] ?? '' ) ),
		'notes'    => mb_substr( sanitize_textarea_field( hu_calcom_response_value( $responses, 'notes' ) ), 0, 2000 ),
		'reason'   => mb_substr( sanitize_textarea_field( (string) ( $payload['cancellationReason'] ?? '' ) ), 0, 500 ),
		'source'   => '' !== $source ? mb_substr( $source, 0, 40 ) : 'direkt',
		'page'     => $page,
	];
}

function hu_calcom_source_label( string $source ): string {
	$labels = [
		'chat'   => 'KI-Assistent',
		'direkt' => 'Direkt in Cal.com (ohne Website-Kontext)',
	];
	return $labels[ $source ] ?? ( 'Website · ' . $source );
}

/** Vorgeschichte eines bestehenden Kontakts in einer Zeile. */
function hu_calcom_contact_history( int $contact_id ): string {
	if ( $contact_id <= 0 ) {
		return 'Neuer Kontakt.';
	}
	$parts   = [];
	$labels  = function_exists( 'nexus_get_crm_contact_source_labels' ) ? nexus_get_crm_contact_source_labels() : [];
	$sources = get_post_meta( $contact_id, '_nexus_contact_sources', true );
	foreach ( is_array( $sources ) ? $sources : [] as $source ) {
		if ( HU_CALCOM_SOURCE !== $source ) {
			$parts[] = $labels[ $source ] ?? $source;
		}
	}
	$type = (string) get_post_meta( $contact_id, '_nexus_contact_request_type_label', true );
	$line = $parts ? 'Bekannt aus: ' . implode( ', ', array_unique( $parts ) ) . '.' : 'Bestehender Kontakt.';
	return '' !== $type ? $line . ' Letzte Anfrage: ' . $type . '.' : $line;
}

/** @return int|WP_Error */
function hu_calcom_sync_contact( array $booking, bool $is_new ) {
	if ( ! function_exists( 'nexus_upsert_crm_contact' ) ) {
		return 0;
	}
	$meta = [
		'_nexus_contact_last_booking_at'    => $booking['start'],
		'_nexus_contact_last_booking_uid'   => $booking['uid'],
		'_nexus_contact_last_booking_state' => strtolower( str_replace( 'BOOKING_', '', $booking['trigger'] ) ),
		'_nexus_contact_last_inquiry_at'    => current_time( 'timestamp' ),
	];
	if ( '' !== $booking['name'] ) {
		$meta['_nexus_contact_name'] = $booking['name'];
	}
	return nexus_upsert_crm_contact(
		[
			'email'         => $booking['email'],
			'title'         => 'Termin · ' . ( '' !== $booking['name'] ? $booking['name'] : $booking['email'] ),
			'source'        => HU_CALCOM_SOURCE,
			'latest_source' => HU_CALCOM_SOURCE,
			// Bestehende Kontakte behalten ihren Status.
			'status'        => $is_new ? 'new' : '',
			'segments'      => [ 'contact_inquiry', HU_CALCOM_SOURCE ],
			'refresh_title' => false,
			'meta'          => $meta,
		]
	);
}

function hu_calcom_when( array $booking ): string {
	return wp_date( 'D, d.m.Y · H:i', $booking['start'] ) . ' Uhr';
}

function hu_calcom_activity_text( array $booking ): string {
	$lines = [
		'Termin: ' . hu_calcom_when( $booking ) . ( '' !== $booking['title'] ? ' · ' . $booking['title'] : '' ),
		'Quelle: ' . hu_calcom_source_label( $booking['source'] ),
	];
	if ( '' !== $booking['page'] ) {
		$lines[] = 'Seite: ' . $booking['page'];
	}
	if ( '' !== $booking['notes'] ) {
		$lines[] = '';
		$lines[] = 'Notiz des Besuchers: ' . $booking['notes'];
	}
	if ( '' !== $booking['reason'] ) {
		$lines[] = 'Absagegrund: ' . $booking['reason'];
	}
	$lines[] = 'Cal.com-Kennung: ' . $booking['uid'];
	return implode( "\n", $lines );
}

function hu_calcom_notification_email(): string {
	$default = function_exists( 'hu_get_contact_email' ) ? hu_get_contact_email() : (string) get_option( 'admin_email' );
	return (string) apply_filters( 'hu_calcom_notification_email', $default );
}

function hu_calcom_send_notification( array $booking, int $contact_id, string $history ): bool {
	$recipient = hu_calcom_notification_email();
	if ( ! $recipient || ! is_email( $recipient ) ) {
		return false;
	}
	$box = static function ( string $label, string $html ): string {
		return '<tr><td style="padding:14px 16px; border:1px solid rgba(255,255,255,0.08); border-radius:18px; background:rgba(255,255,255,0.03); font-family:Helvetica, Arial, sans-serif;">'
			. '<div style="font-size:11px; letter-spacing:0.08em; text-transform:uppercase; color:#9ea8b2; margin-bottom:8px;">' . esc_html( $label ) . '</div>'
			. '<div style="font-size:14px; line-height:1.8; color:#c5ced7;">' . $html . '</div></td></tr>';
	};
	$who = esc_html( '' !== $booking['name'] ? $booking['name'] : '–' ) . '<br>' . esc_html( $booking['email'] );
	$frame = esc_html( hu_calcom_when( $booking ) ) . ( '' !== $booking['title'] ? '<br>' . esc_html( $booking['title'] ) : '' )
		. ( '' !== $booking['timezone'] ? '<br>' . esc_html( 'Zeitzone des Gastes: ' . $booking['timezone'] ) : '' );
	$origin = esc_html( hu_calcom_source_label( $booking['source'] ) ) . ( '' !== $booking['page'] ? '<br>' . esc_html( home_url( $booking['page'] ) ) : '' );
	$rows   = $box( 'Wer', $who ) . $box( 'Wann', $frame ) . $box( 'Woher', $origin ) . $box( 'Vorgeschichte', esc_html( $history ) );
	if ( '' !== $booking['notes'] ) {
		$rows .= $box( 'Notiz des Besuchers', nl2br( esc_html( $booking['notes'] ) ) );
	}
	if ( '' !== $booking['reason'] ) {
		$rows .= $box( 'Absagegrund', nl2br( esc_html( $booking['reason'] ) ) );
	}
	$crm   = $contact_id > 0
		? '<a href="' . esc_url( admin_url( 'post.php?post=' . $contact_id . '&action=edit' ) ) . '" style="color:#f7f3ee;">Im CRM öffnen</a>'
		: 'Nicht im CRM gespeichert – bitte manuell anlegen.';
	$rows .= $box( 'CRM', wp_kses( $crm, [ 'a' => [ 'href' => true, 'style' => true ] ] ) );
	$content = '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin:0 0 18px 0; border-collapse:separate; border-spacing:0 10px;">' . $rows . '</table>';

	$args = [
		'preheader' => $booking['label'] . ': ' . hu_calcom_when( $booking ),
		'eyebrow'   => 'Termin',
		'headline'  => $booking['label'] . '.',
		'intro'     => ( '' !== $booking['name'] ? $booking['name'] : $booking['email'] ) . ' · ' . hu_calcom_when( $booking ),
		'content'   => $content,
		'footer'    => 'Antwort direkt auf diese Mail geht an den Gast.',
	];
	$html    = function_exists( 'nexus_get_transactional_email_shell' ) ? nexus_get_transactional_email_shell( $args ) : '<h1>' . esc_html( $args['headline'] ) . '</h1>' . $content;
	$subject = sprintf( '[Termin] %s — %s · %s', $booking['label'], '' !== $booking['name'] ? $booking['name'] : $booking['email'], wp_date( 'd.m. H:i', $booking['start'] ) );
	$headers = [ 'Reply-To: ' . $booking['email'] ];
	if ( function_exists( 'nexus_append_mail_tags_header' ) ) {
		$headers = nexus_append_mail_tags_header( $headers, [ HU_CALCOM_SOURCE, 'internal_notification' ] );
	}
	if ( function_exists( 'nexus_send_contact_html_mail' ) ) {
		return (bool) nexus_send_contact_html_mail( $recipient, $subject, $html, $headers );
	}
	$headers[] = 'Content-Type: text/html; charset=UTF-8';
	return (bool) wp_mail( $recipient, $subject, $html, $headers );
}

/**
 * Ein geprüftes Ereignis verarbeiten. Doppelte Zustellungen (Cal.com
 * wiederholt bei Fehlern) werden über Kennung und Ereignis erkannt.
 *
 * @return WP_REST_Response
 */
function hu_calcom_process_event( array $event ) {
	if ( 'PING' === ( $event['triggerEvent'] ?? '' ) ) {
		hu_calcom_record_status( 'ping', 'PING' );
		return new WP_REST_Response( [ 'ok' => true, 'message' => 'pong' ], 200 );
	}
	$booking = hu_calcom_normalize_booking( $event );
	if ( is_wp_error( $booking ) ) {
		hu_calcom_record_status( $booking->get_error_code(), is_string( $event['triggerEvent'] ?? null ) ? $event['triggerEvent'] : '' );
		// 200: Cal.com soll ein unbrauchbares Ereignis nicht endlos wiederholen.
		return new WP_REST_Response( [ 'ok' => true, 'ignored' => $booking->get_error_code() ], 200 );
	}
	$seen_key = 'hu_cal_seen_' . md5( $booking['uid'] . '|' . $booking['trigger'] . '|' . $booking['start'] );
	if ( get_transient( $seen_key ) ) {
		return new WP_REST_Response( [ 'ok' => true, 'duplicate' => true ], 200 );
	}

	$existing   = function_exists( 'nexus_find_contact_by_email' ) ? (int) nexus_find_contact_by_email( $booking['email'] ) : 0;
	$history    = hu_calcom_contact_history( $existing );
	$contact_id = hu_calcom_sync_contact( $booking, 0 === $existing );
	$contact_id = is_wp_error( $contact_id ) ? 0 : (int) $contact_id;
	if ( $contact_id > 0 && function_exists( 'nexus_record_inbound_inquiry_activity' ) ) {
		nexus_record_inbound_inquiry_activity( $contact_id, $booking['label'] . ' · ' . wp_date( 'd.m. H:i', $booking['start'] ), hu_calcom_activity_text( $booking ), HU_CALCOM_SOURCE );
	}
	$notified = hu_calcom_send_notification( $booking, $contact_id, $history );

	if ( 0 === $contact_id && ! $notified ) {
		error_log( '[Nexus Lead] Terminbuchung weder im CRM gespeichert noch zugestellt.' );
		hu_calcom_record_status( 'failed', $booking['trigger'] );
		// 500: Cal.com wiederholt die Zustellung.
		return new WP_REST_Response( [ 'ok' => false ], 500 );
	}
	if ( ! $notified && function_exists( 'nexus_record_lead_notification_failure' ) ) {
		nexus_record_lead_notification_failure( $contact_id, HU_CALCOM_SOURCE );
	}
	if ( 'BOOKING_CREATED' === $booking['trigger'] && function_exists( 'hu_record_inquiry_event' ) ) {
		$payload = '' !== $booking['page'] ? [ 'landing_page_url' => home_url( $booking['page'] ), 'utm_source' => $booking['source'] ] : [ 'utm_source' => $booking['source'] ];
		hu_record_inquiry_event( 'termin', $payload );
	}
	set_transient( $seen_key, 1, 14 * DAY_IN_SECONDS );
	hu_calcom_record_status( 'ok', $booking['trigger'] );
	return new WP_REST_Response( [ 'ok' => true ], 201 );
}

/** @return WP_REST_Response */
function hu_calcom_handle_webhook( WP_REST_Request $request ) {
	$body = (string) $request->get_body();
	if ( strlen( $body ) > HU_CALCOM_MAX_BODY || ! hu_calcom_signature_valid( $body, (string) $request->get_header( 'x_cal_signature_256' ), hu_calcom_webhook_secret() ) ) {
		hu_calcom_record_status( 'bad_signature' );
		return new WP_REST_Response( [ 'ok' => false ], 401 );
	}
	$event = json_decode( $body, true, 32 );
	if ( ! is_array( $event ) ) {
		hu_calcom_record_status( 'bad_payload' );
		return new WP_REST_Response( [ 'ok' => false ], 400 );
	}
	return hu_calcom_process_event( $event );
}

function hu_calcom_register_webhook_route(): void {
	if ( '' === hu_calcom_webhook_secret() ) {
		return;
	}
	register_rest_route( 'nexus/v1', '/calcom-webhook', [
		'methods'             => WP_REST_Server::CREATABLE,
		'callback'            => 'hu_calcom_handle_webhook',
		// Die Signatur ersetzt die Anmeldung; geprüft im Callback mit dem Rohtext.
		'permission_callback' => '__return_true',
	] );
}
add_action( 'rest_api_init', 'hu_calcom_register_webhook_route' );

/* ── Admin: Werkzeuge → Cal.com-Buchungen ─────────────────────────── */

function hu_calcom_admin_menu(): void {
	add_management_page( 'Cal.com-Buchungen', 'Cal.com-Buchungen', 'manage_options', 'hu-calcom', 'hu_calcom_admin_page' );
}
add_action( 'admin_menu', 'hu_calcom_admin_menu' );

function hu_calcom_admin_generate_secret(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'Nicht erlaubt.', 403 );
	}
	check_admin_referer( 'hu_calcom_secret' );
	update_option( HU_CALCOM_SECRET_OPTION, wp_generate_password( 48, false, false ), false );
	wp_safe_redirect( add_query_arg( [ 'page' => 'hu-calcom', 'secret' => 'new' ], admin_url( 'tools.php' ) ) );
	exit;
}
add_action( 'admin_post_hu_calcom_secret', 'hu_calcom_admin_generate_secret' );

function hu_calcom_admin_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$secret      = hu_calcom_webhook_secret();
	$from_config = function_exists( 'nexus_get_mail_runtime_value' ) && '' !== (string) nexus_get_mail_runtime_value( [ 'HU_CALCOM_WEBHOOK_SECRET' ] );
	$status      = get_option( HU_CALCOM_STATUS_OPTION, [] );
	$results     = [
		'ok'              => 'verarbeitet',
		'ping'            => 'Test-Ping empfangen',
		'bad_signature'   => 'Signatur falsch – Geheimnis in Cal.com prüfen',
		'bad_payload'     => 'unlesbare Daten',
		'ignored'         => 'Ereignis nicht benötigt',
		'invalid_booking' => 'Buchung ohne E-Mail oder Startzeit',
		'failed'          => 'weder CRM noch Mail – Cal.com wiederholt',
	];
	?>
	<div class="wrap">
		<h1>Cal.com-Buchungen</h1>
		<p>Jede Buchung, Verschiebung und Absage aus Cal.com landet im CRM, mit Quelle (KI-Assistent oder Seite), und du bekommst eine eigene Mail mit Vorgeschichte.</p>
		<table class="widefat striped" style="max-width:860px">
			<tbody>
				<tr><th style="width:200px">Webhook-Adresse</th><td><code><?php echo esc_html( rest_url( 'nexus/v1/calcom-webhook' ) ); ?></code></td></tr>
				<tr><th>Geheimnis</th><td>
					<?php if ( '' === $secret ) : ?>
						<strong>Noch keins.</strong> Ohne Geheimnis ist die Adresse abgeschaltet.
					<?php elseif ( $from_config ) : ?>
						Aus <code>wp-config.php</code> (<code>HU_CALCOM_WEBHOOK_SECRET</code>).
					<?php else : ?>
						<input type="text" readonly value="<?php echo esc_attr( $secret ); ?>" style="width:100%;max-width:520px;font-family:monospace" onclick="this.select()">
					<?php endif; ?>
				</td></tr>
				<tr><th>Letztes Ereignis</th><td>
					<?php
					if ( ! empty( $status['at'] ) ) {
						echo esc_html( wp_date( 'd.m.Y H:i', (int) $status['at'] ) . ' · ' . ( $status['trigger'] ?? '' ) . ' · ' . ( $results[ $status['result'] ?? '' ] ?? (string) ( $status['result'] ?? '' ) ) );
					} else {
						echo 'Noch keins empfangen.';
					}
					?>
				</td></tr>
			</tbody>
		</table>
		<?php if ( ! $from_config ) : ?>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin-top:12px">
				<input type="hidden" name="action" value="hu_calcom_secret">
				<?php wp_nonce_field( 'hu_calcom_secret' ); ?>
				<?php submit_button( '' === $secret ? 'Geheimnis erzeugen' : 'Neues Geheimnis erzeugen', '' === $secret ? 'primary' : 'secondary', 'submit', false ); ?>
				<?php if ( '' !== $secret ) : ?><span class="description">&nbsp;Danach muss das neue Geheimnis auch in Cal.com eingetragen werden.</span><?php endif; ?>
			</form>
		<?php endif; ?>
		<h2>In Cal.com einrichten</h2>
		<ol>
			<li>Cal.com → Einstellungen → Entwickler → Webhooks → <em>Neu</em>.</li>
			<li>Abonnenten-URL: die Webhook-Adresse oben.</li>
			<li>Ereignisse: <em>Buchung erstellt</em>, <em>Buchung verschoben</em>, <em>Buchung storniert</em>.</li>
			<li>Geheimnis: das Geheimnis oben einfügen. Speichern, dann „Ping testen“. Oben steht danach „Test-Ping empfangen“.</li>
		</ol>
	</div>
	<?php
}
