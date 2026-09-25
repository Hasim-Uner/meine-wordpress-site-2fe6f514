<?php
/**
 * White-Label-Anfrage: eigener REST-Endpunkt, Mailversand und Feldkatalog.
 *
 * Der Sekundaer-CTA der Route war bis hierher ein `mailto:` mit vorformuliertem
 * Body. Am Firmenrechner oeffnet das ein Mailprogramm, das viele nicht nutzen,
 * oder gar nichts — und ein Klick auf einen mailto-Link ist kein Abschluss,
 * sondern nur ein Klick. Damit war die Conversion dieser Route nicht messbar.
 *
 * Bewusst ein eigener Endpunkt und nicht `nexus/v1/contact-request`: die
 * Kontaktroute verlangt Name, Anfragetyp, Thema und Einwilligungsfeld und
 * validiert gegen ihre eigenen Enum-Listen. Dieses Formular hat vier Felder,
 * keinen Namen und eine eigene Zielgruppe. Eine gemeinsame Route haette
 * entweder die Kontaktvalidierung aufgeweicht oder hier Pflichtfelder erzwungen,
 * die die Agentur nicht ausfuellen will.
 *
 * Der getrennte Endpunkt ist zugleich die Voraussetzung dafuer, dass die
 * Conversion dieser einen Seite isoliert messbar wird: das Erfolgs-Event heisst
 * `whitelabel_request_submit` und wird von keinem anderen Formular der Website
 * gefeuert.
 *
 * Reihenfolge seit 2026-09-22: erst CRM (`nexus_contact`, Quelle
 * `whitelabel_request`, daraus eine Sales-Chance), dann interne Mail, dann
 * Bestaetigung. Die Anfrage gilt als angekommen, sobald CRM oder interne Mail
 * sie haben; scheitert beides, bekommt die Agentur einen Fehler statt eines
 * falschen Danke.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Zulaessige Auslegungen des Formulars.
 *
 * Die Route belegt dasselbe Formular ueber `?case=` vor, damit die drei Wege im
 * Abschluss-CTA im Posteingang unterscheidbar bleiben. Ein unbekannter oder
 * fehlender Wert faellt auf `aufgabe` zurueck; der Parameter darf nie mehr
 * entscheiden als die Betreffzeile.
 *
 * @return array<string, array<string, string>>
 */
function hu_whitelabel_request_cases() {
	return [
		'aufgabe'       => [
			'label'   => 'Konkrete Aufgabe',
			'subject' => 'White-Label: konkrete Aufgabe',
		],
		'angebotsphase' => [
			'label'   => 'Angebotsphase',
			'subject' => 'White-Label: Angebotsphase',
		],
	];
}

/**
 * Normalisiere einen Case-Schluessel aus URL-Parameter oder Payload.
 *
 * @param string $value Roher Wert.
 * @return string
 */
function hu_whitelabel_request_normalize_case( $value ) {
	$value = sanitize_key( (string) $value );
	$cases = hu_whitelabel_request_cases();

	return isset( $cases[ $value ] ) ? $value : 'aufgabe';
}

/**
 * Zulaessige Antworten auf die Zugangsfrage.
 *
 * @return array<string, string>
 */
function hu_whitelabel_request_access_options() {
	return [
		'ja'        => 'Ja',
		'teilweise' => 'Teilweise',
		'nein'      => 'Nein',
	];
}

/**
 * Empfaenger der internen Benachrichtigung.
 *
 * Die Adresse kommt aus dem Messaging-Canon, damit sie nicht zum vierten
 * sichtbaren Kontaktweg der Website wird.
 *
 * @return string
 */
function hu_whitelabel_request_notification_email() {
	$default = function_exists( 'hu_get_contact_email' )
		? hu_get_contact_email()
		: (string) get_option( 'admin_email' );

	return (string) apply_filters( 'hu_whitelabel_request_notification_email', $default );
}

/**
 * Registriere die oeffentliche REST-Route der White-Label-Anfrage.
 *
 * @return void
 */
function hu_register_whitelabel_request_rest_route() {
	register_rest_route(
		'nexus/v1',
		'/whitelabel-request',
		[
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => 'hu_handle_whitelabel_request_submission',
			'permission_callback' => '__return_true',
		]
	);
}
add_action( 'rest_api_init', 'hu_register_whitelabel_request_rest_route' );

/**
 * Pruefe und normalisiere die vier Formularfelder.
 *
 * @param array $payload Roher Payload.
 * @return array|WP_Error
 */
function hu_validate_whitelabel_request_payload( $payload ) {
	$access_options = hu_whitelabel_request_access_options();

	$task      = isset( $payload['task'] ) ? sanitize_textarea_field( (string) $payload['task'] ) : '';
	$email     = isset( $payload['email'] ) ? sanitize_email( (string) $payload['email'] ) : '';
	$timeframe = isset( $payload['timeframe'] ) ? sanitize_text_field( (string) $payload['timeframe'] ) : '';
	$access    = isset( $payload['access'] ) ? sanitize_key( (string) $payload['access'] ) : '';
	$case      = hu_whitelabel_request_normalize_case( $payload['case'] ?? '' );

	$task = trim( $task );

	if ( mb_strlen( $task ) < 12 ) {
		return new WP_Error( 'missing_task', 'Bitte die Aufgabe kurz beschreiben. Vier Zeilen genügen.' );
	}

	if ( '' === $email || ! is_email( $email ) ) {
		return new WP_Error( 'invalid_email', 'Bitte eine gültige E-Mail-Adresse angeben.' );
	}

	if ( '' !== $access && ! isset( $access_options[ $access ] ) ) {
		return new WP_Error( 'invalid_access', 'Bitte eine der drei Antworten zu den Zugängen wählen.' );
	}

	// Herkunft und Selbstauskunft sind optional und brechen die Anfrage nie ab.
	$attribution = function_exists( 'nexus_sanitize_inquiry_attribution' ) ? nexus_sanitize_inquiry_attribution( $payload ) : [];

	return $attribution + [
		'task'         => mb_substr( $task, 0, 4000 ),
		'email'        => $email,
		'timeframe'    => mb_substr( $timeframe, 0, 160 ),
		'access'       => $access,
		'access_label' => '' !== $access ? $access_options[ $access ] : '',
		'case'         => $case,
	];
}

/**
 * Nimm eine White-Label-Anfrage entgegen.
 *
 * @param WP_REST_Request $request REST-Request.
 * @return WP_REST_Response
 */
function hu_handle_whitelabel_request_submission( WP_REST_Request $request ) {
	$payload = $request->get_json_params();
	if ( ! is_array( $payload ) || empty( $payload ) ) {
		$payload = $request->get_body_params();
	}

	if ( ! is_array( $payload ) ) {
		$payload = [];
	}

	// Honeypot: fuer Bots sichtbar, fuer Menschen nicht. Eine gefuellte
	// Zeile beantwortet 200, damit der Absender keinen Treffer erkennt.
	$honeypot = isset( $payload['company_website'] ) ? trim( (string) $payload['company_website'] ) : '';
	if ( '' !== $honeypot ) {
		return new WP_REST_Response(
			[
				'ok'      => true,
				'message' => 'Anfrage eingegangen.',
			],
			200
		);
	}

	if ( function_exists( 'nexus_validate_contact_request_rate_limit' ) ) {
		$rate_limit = nexus_validate_contact_request_rate_limit();

		if ( is_wp_error( $rate_limit ) ) {
			return new WP_REST_Response(
				[
					'ok'    => false,
					'error' => $rate_limit->get_error_message(),
				],
				429
			);
		}
	}

	$validated = hu_validate_whitelabel_request_payload( $payload );

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

	$contact_id = function_exists( 'nexus_sync_whitelabel_request_to_crm' )
		? nexus_sync_whitelabel_request_to_crm( $validated )
		: 0;
	$contact_id = is_wp_error( $contact_id ) ? 0 : (int) $contact_id;

	if ( $contact_id > 0 && function_exists( 'nexus_record_inbound_inquiry_activity' ) ) {
		$cases = hu_whitelabel_request_cases();
		nexus_record_inbound_inquiry_activity(
			$contact_id,
			'White-Label-Anfrage · ' . $cases[ $validated['case'] ]['label'],
			hu_whitelabel_request_activity_summary( $validated ),
			'whitelabel_request'
		);
	}

	$notified = hu_send_whitelabel_request_notification( $validated, $contact_id );

	if ( 0 === $contact_id && ! $notified ) {
		error_log( '[Nexus Lead] White-Label-Anfrage weder im CRM gespeichert noch zugestellt.' );

		return new WP_REST_Response(
			[
				'ok'    => false,
				'error' => 'Die Anfrage konnte gerade nicht übermittelt werden. Bitte schickt sie direkt per E-Mail – die Adresse steht unter dem Formular.',
			],
			500
		);
	}

	if ( 0 === $contact_id ) {
		error_log( '[Nexus Lead] White-Label-Anfrage nicht im CRM gespeichert; interne Mail zugestellt.' );
	}

	if ( ! $notified && function_exists( 'nexus_record_lead_notification_failure' ) ) {
		nexus_record_lead_notification_failure( $contact_id, 'whitelabel_request' );
	}

	hu_send_whitelabel_request_confirmation( $validated );

	$response_promise = hu_response_promise( 'window' );

	return new WP_REST_Response(
		[
			'ok'      => true,
			'case'    => $validated['case'],
			'message' => sprintf(
				'Danke. Die Aufgabe ist da. Ich antworte %s mit Einschätzung, Aufwand und Preis.',
				$response_promise
			),
		],
		201
	);
}

/**
 * Baue die Angaben-Tabelle beider Mails.
 *
 * @param array $payload Geprüfter Payload.
 * @return string
 */
function hu_whitelabel_request_detail_rows( $payload ) {
	$cases = hu_whitelabel_request_cases();
	$rows  = sprintf(
		'<strong style="color:#f7f3ee;">Weg:</strong> %s',
		esc_html( $cases[ $payload['case'] ]['label'] )
	);

	if ( '' !== $payload['timeframe'] ) {
		$rows .= sprintf(
			'<br><strong style="color:#f7f3ee;">Gewünschter Zeitraum:</strong> %s',
			esc_html( $payload['timeframe'] )
		);
	}

	if ( '' !== $payload['access_label'] ) {
		$rows .= sprintf(
			'<br><strong style="color:#f7f3ee;">Zugänge (WordPress, GA4, GTM):</strong> %s',
			esc_html( $payload['access_label'] )
		);
	}

	return $rows;
}

/**
 * Klartext-Zusammenfassung fuer die CRM-Timeline.
 *
 * @param array $payload Geprüfter Payload.
 * @return string
 */
function hu_whitelabel_request_activity_summary( $payload ) {
	$cases = hu_whitelabel_request_cases();
	$lines = [ 'Weg: ' . $cases[ $payload['case'] ]['label'] ];

	if ( '' !== $payload['timeframe'] ) {
		$lines[] = 'Gewünschter Zeitraum: ' . $payload['timeframe'];
	}

	if ( '' !== $payload['access_label'] ) {
		$lines[] = 'Zugänge: ' . $payload['access_label'];
	}

	$lines[] = '';
	$lines[] = 'Aufgabe:';
	$lines[] = $payload['task'];

	$attribution = function_exists( 'nexus_get_inquiry_attribution_pairs' ) ? nexus_get_inquiry_attribution_pairs( $payload ) : [];

	if ( ! empty( $attribution ) ) {
		$lines[] = '';
		$lines[] = 'Herkunft:';

		foreach ( $attribution as $label => $value ) {
			$lines[] = $label . ': ' . $value;
		}
	}

	return implode( "\n", $lines );
}

/**
 * Baue eine Mail im gemeinsamen Transaktions-Layout.
 *
 * @param array $args Shell-Argumente.
 * @return string
 */
function hu_whitelabel_request_mail_shell( $args ) {
	if ( function_exists( 'nexus_get_transactional_email_shell' ) ) {
		return nexus_get_transactional_email_shell( $args );
	}

	return sprintf(
		'<h1>%1$s</h1><p>%2$s</p>%3$s',
		esc_html( (string) ( $args['headline'] ?? '' ) ),
		esc_html( (string) ( $args['intro'] ?? '' ) ),
		(string) ( $args['content'] ?? '' )
	);
}

/**
 * Verschicke eine HTML-Mail ueber den gemeinsamen Router.
 *
 * @param string   $recipient Empfaenger.
 * @param string   $subject   Betreff.
 * @param string   $html      HTML-Body.
 * @param string[] $headers   Kopfzeilen.
 * @return bool Ob wp_mail() die Nachricht angenommen hat.
 */
function hu_whitelabel_request_send_mail( $recipient, $subject, $html, $headers = [] ) {
	if ( function_exists( 'nexus_send_contact_html_mail' ) ) {
		return (bool) nexus_send_contact_html_mail( $recipient, $subject, $html, $headers );
	}

	$headers[] = 'Content-Type: text/html; charset=UTF-8';

	return (bool) wp_mail( $recipient, $subject, $html, $headers );
}

/**
 * Interne Benachrichtigung an kontakt@.
 *
 * @param array $payload    Geprüfter Payload.
 * @param int   $contact_id CRM-Kontakt, 0 wenn die Speicherung gescheitert ist.
 * @return bool Ob die Mail angenommen wurde.
 */
function hu_send_whitelabel_request_notification( $payload, $contact_id = 0 ) {
	$recipient = hu_whitelabel_request_notification_email();

	if ( ! $recipient || ! is_email( $recipient ) ) {
		return false;
	}

	$crm_note = (int) $contact_id > 0
		? sprintf(
			'<a href="%s" style="color:#f7f3ee;">Im CRM öffnen</a>',
			esc_url( admin_url( 'post.php?post=' . (int) $contact_id . '&action=edit' ) )
		)
		: 'Nicht im CRM gespeichert – bitte manuell anlegen.';

	$cases   = hu_whitelabel_request_cases();
	$subject = sprintf( '[White-Label] %s — %s', $cases[ $payload['case'] ]['label'], $payload['email'] );
	$headers = [ 'Reply-To: ' . $payload['email'] ];

	if ( function_exists( 'nexus_append_mail_tags_header' ) ) {
		$headers = nexus_append_mail_tags_header( $headers, [ 'whitelabel_request', 'internal_notification' ] );
	}

	$content = sprintf(
		'<table role="presentation" width="100%%" cellspacing="0" cellpadding="0" border="0" style="margin:0 0 18px 0; border-collapse:separate; border-spacing:0 10px;">
			<tr>
				<td style="padding:14px 16px; border:1px solid rgba(255,255,255,0.08); border-radius:18px; background:rgba(255,255,255,0.03); font-family:Helvetica, Arial, sans-serif;">
					<div style="font-size:11px; letter-spacing:0.08em; text-transform:uppercase; color:#9ea8b2; margin-bottom:8px;">Rahmen</div>
					<div style="font-size:14px; line-height:1.8; color:#c5ced7;">%1$s</div>
				</td>
			</tr>
			<tr>
				<td style="padding:14px 16px; border:1px solid rgba(255,255,255,0.08); border-radius:18px; background:rgba(255,255,255,0.03); font-family:Helvetica, Arial, sans-serif;">
					<div style="font-size:11px; letter-spacing:0.08em; text-transform:uppercase; color:#9ea8b2; margin-bottom:8px;">Aufgabe</div>
					<div style="font-size:14px; line-height:1.8; color:#c5ced7;">%2$s</div>
				</td>
			</tr>
			<tr>
				<td style="padding:14px 16px; border:1px solid rgba(255,255,255,0.08); border-radius:18px; background:rgba(255,255,255,0.03); font-family:Helvetica, Arial, sans-serif;">
					<div style="font-size:11px; letter-spacing:0.08em; text-transform:uppercase; color:#9ea8b2; margin-bottom:8px;">Herkunft</div>
					<div style="font-size:14px; line-height:1.8; color:#c5ced7;">%4$s</div>
				</td>
			</tr>
			<tr>
				<td style="padding:14px 16px; border:1px solid rgba(255,255,255,0.08); border-radius:18px; background:rgba(255,255,255,0.03); font-family:Helvetica, Arial, sans-serif;">
					<div style="font-size:11px; letter-spacing:0.08em; text-transform:uppercase; color:#9ea8b2; margin-bottom:8px;">CRM</div>
					<div style="font-size:14px; line-height:1.8; color:#c5ced7;">%3$s</div>
				</td>
			</tr>
		</table>',
		hu_whitelabel_request_detail_rows( $payload ),
		nl2br( esc_html( $payload['task'] ) ),
		wp_kses( $crm_note, [ 'a' => [ 'href' => true, 'style' => true ] ] ),
		function_exists( 'nexus_get_inquiry_attribution_mail_rows' ) ? nexus_get_inquiry_attribution_mail_rows( $payload ) : ''
	);

	$html = hu_whitelabel_request_mail_shell(
		[
			'preheader' => 'Neue White-Label-Anfrage.',
			'eyebrow'   => 'White-Label',
			'headline'  => 'Neue White-Label-Anfrage.',
			'intro'     => 'Von ' . $payload['email'] . '.',
			'content'   => $content,
			'footer'    => 'Antwort direkt auf diese Mail geht an die Agentur.',
		]
	);

	return hu_whitelabel_request_send_mail( $recipient, $subject, $html, $headers );
}

/**
 * Bestaetigung an die anfragende Agentur.
 *
 * @param array $payload Geprüfter Payload.
 * @return void
 */
function hu_send_whitelabel_request_confirmation( $payload ) {
	$cases            = hu_whitelabel_request_cases();
	$contact_email    = function_exists( 'hu_get_contact_email' ) ? hu_get_contact_email() : '';
	$response_promise = hu_response_promise( 'window' );

	$headers = [];

	if ( $contact_email && is_email( $contact_email ) ) {
		$headers[] = 'Reply-To: ' . $contact_email;
	}

	if ( function_exists( 'nexus_append_mail_tags_header' ) ) {
		$headers = nexus_append_mail_tags_header( $headers, [ 'whitelabel_request', 'lead_confirmation' ] );
	}

	$next_step = 'angebotsphase' === $payload['case']
		? 'Ich sage euch, ob es machbar ist und was es kostet, bevor ihr eurem Kunden etwas zusagt.'
		: 'Ihr bekommt Einschätzung, Aufwand und Preis — und danach entscheidet ihr.';

	$content = sprintf(
		'<table role="presentation" width="100%%" cellspacing="0" cellpadding="0" border="0" style="margin:0 0 18px 0; border-collapse:separate; border-spacing:0 10px;">
			<tr>
				<td style="padding:14px 16px; border:1px solid rgba(255,255,255,0.08); border-radius:18px; background:rgba(255,255,255,0.03); font-family:Helvetica, Arial, sans-serif;">
					<div style="font-size:11px; letter-spacing:0.08em; text-transform:uppercase; color:#9ea8b2; margin-bottom:8px;">Was jetzt passiert</div>
					<div style="font-size:14px; line-height:1.8; color:#c5ced7;">
						<strong style="color:#f7f3ee;">1.</strong> Eure Anfrage ist eingegangen.<br>
						<strong style="color:#f7f3ee;">2.</strong> Antwort %1$s, von mir persönlich.<br>
						<strong style="color:#f7f3ee;">3.</strong> %2$s
					</div>
				</td>
			</tr>
			<tr>
				<td style="padding:14px 16px; border:1px solid rgba(255,255,255,0.08); border-radius:18px; background:rgba(255,255,255,0.03); font-family:Helvetica, Arial, sans-serif;">
					<div style="font-size:11px; letter-spacing:0.08em; text-transform:uppercase; color:#9ea8b2; margin-bottom:8px;">Eure Angaben</div>
					<div style="font-size:14px; line-height:1.8; color:#c5ced7;">
						%3$s<br>
						<strong style="color:#f7f3ee;">Aufgabe:</strong> %4$s
					</div>
				</td>
			</tr>
		</table>',
		esc_html( $response_promise ),
		esc_html( $next_step ),
		hu_whitelabel_request_detail_rows( $payload ),
		esc_html( wp_trim_words( $payload['task'], 24, '…' ) )
	);

	$html = hu_whitelabel_request_mail_shell(
		[
			'preheader' => 'Eure White-Label-Anfrage ist eingegangen.',
			'eyebrow'   => $cases[ $payload['case'] ]['label'],
			'headline'  => 'Eure Anfrage ist eingegangen.',
			'intro'     => 'Danke. Ich lese das persönlich — kein Vertriebsteam, kein Ticketsystem.',
			'content'   => $content,
			'footer'    => 'Viele Grüße, Haşim Üner',
		]
	);

	hu_whitelabel_request_send_mail(
		$payload['email'],
		'Eure White-Label-Anfrage ist eingegangen',
		$html,
		$headers
	);
}
