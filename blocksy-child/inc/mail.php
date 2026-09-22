<?php
/**
 * Central Brevo mail transport for transactional emails.
 *
 * Provider order:
 * 1. Brevo API for global wp_mail routing.
 * 2. Brevo SMTP as a fallback when API is not configured.
 * 3. Native WordPress transport if neither is available.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resolve the first non-empty constant or environment value.
 *
 * @param array<int, string> $keys Candidate config keys.
 * @param string             $default Default value.
 * @return string
 */
function nexus_get_mail_runtime_value( $keys, $default = '' ) {
	foreach ( $keys as $key ) {
		if ( ! is_string( $key ) || '' === $key ) {
			continue;
		}

		if ( defined( $key ) ) {
			$value = constant( $key );
			if ( null !== $value && '' !== trim( (string) $value ) ) {
				return trim( (string) $value );
			}
		}

		$value = getenv( $key );
		if ( false !== $value && '' !== trim( (string) $value ) ) {
			return trim( (string) $value );
		}
	}

	return $default;
}

/**
 * Normalize loosely typed boolean configuration values.
 *
 * @param mixed $value Runtime config value.
 * @param bool  $default Default value.
 * @return bool
 */
function nexus_normalize_mail_bool( $value, $default = false ) {
	if ( is_bool( $value ) ) {
		return $value;
	}

	if ( null === $value || '' === $value ) {
		return $default;
	}

	$normalized = strtolower( trim( (string) $value ) );

	if ( in_array( $normalized, [ '1', 'true', 'yes', 'on' ], true ) ) {
		return true;
	}

	if ( in_array( $normalized, [ '0', 'false', 'no', 'off' ], true ) ) {
		return false;
	}

	return $default;
}

/**
 * Return the central Brevo mail settings.
 *
 * @return array<string, mixed>
 */
function nexus_get_brevo_mail_settings() {
	$from_email = nexus_get_mail_runtime_value(
		[
			'NEXUS_BREVO_FROM_EMAIL',
			'NEXUS_MAIL_FROM_EMAIL',
			'NEXUS_SMTP_FROM_EMAIL',
			'BREVO_FROM_EMAIL',
			'MAIL_FROM_EMAIL',
			'SMTP_FROM_EMAIL',
		],
		(string) get_option( 'admin_email' )
	);

	$from_name = nexus_get_mail_runtime_value(
		[
			'NEXUS_BREVO_FROM_NAME',
			'NEXUS_MAIL_FROM_NAME',
			'NEXUS_SMTP_FROM_NAME',
			'BREVO_FROM_NAME',
			'MAIL_FROM_NAME',
			'SMTP_FROM_NAME',
		],
		wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES )
	);

	$smtp_port = (int) nexus_get_mail_runtime_value(
		[
			'NEXUS_BREVO_SMTP_PORT',
			'NEXUS_SMTP_PORT',
			'BREVO_SMTP_PORT',
			'SMTP_PORT',
		],
		'587'
	);

	$settings = [
		'from_email'             => sanitize_email( $from_email ),
		'from_name'              => $from_name,
		'api_key'                => nexus_get_mail_runtime_value(
			[
				'NEXUS_BREVO_API_KEY',
				'NEXUS_MAIL_API_KEY',
				'BREVO_API_KEY',
				'BREVO_V3_API_KEY',
				'SENDINBLUE_API_KEY',
			]
		),
		'api_endpoint'           => nexus_get_mail_runtime_value(
			[
				'NEXUS_BREVO_API_ENDPOINT',
				'NEXUS_MAIL_API_ENDPOINT',
				'BREVO_API_ENDPOINT',
			],
			'https://api.brevo.com/v3/smtp/email'
		),
		'api_timeout'            => max(
			5,
			(int) nexus_get_mail_runtime_value(
				[
					'NEXUS_BREVO_API_TIMEOUT',
					'NEXUS_MAIL_API_TIMEOUT',
					'BREVO_API_TIMEOUT',
				],
				'15'
			)
		),
		'api_enabled'            => false,
		'api_fallback_to_wp_mail' => nexus_normalize_mail_bool(
			nexus_get_mail_runtime_value(
				[
					'NEXUS_BREVO_API_FALLBACK_TO_WP_MAIL',
					'NEXUS_MAIL_API_FALLBACK_TO_WP_MAIL',
					'BREVO_API_FALLBACK_TO_WP_MAIL',
				],
				''
			),
			false
		),
		'smtp_host'              => nexus_get_mail_runtime_value(
			[
				'NEXUS_BREVO_SMTP_HOST',
				'NEXUS_SMTP_HOST',
				'BREVO_SMTP_HOST',
				'SMTP_HOST',
			],
			'smtp-relay.brevo.com'
		),
		'smtp_port'              => $smtp_port > 0 ? $smtp_port : 587,
		'smtp_username'          => nexus_get_mail_runtime_value(
			[
				'NEXUS_BREVO_SMTP_USERNAME',
				'NEXUS_BREVO_SMTP_USER',
				'NEXUS_SMTP_USERNAME',
				'NEXUS_SMTP_USER',
				'BREVO_SMTP_USERNAME',
				'BREVO_SMTP_USER',
				'BREVO_SMTP_LOGIN',
				'SMTP_USERNAME',
				'SMTP_USER',
			]
		),
		'smtp_password'          => nexus_get_mail_runtime_value(
			[
				'NEXUS_BREVO_SMTP_PASSWORD',
				'NEXUS_BREVO_SMTP_PASS',
				'NEXUS_SMTP_PASSWORD',
				'NEXUS_SMTP_PASS',
				'BREVO_SMTP_PASSWORD',
				'BREVO_SMTP_PASS',
				'BREVO_SMTP_KEY',
				'SMTP_PASSWORD',
				'SMTP_PASS',
			]
		),
		'smtp_encryption'        => strtolower(
			nexus_get_mail_runtime_value(
				[
					'NEXUS_BREVO_SMTP_ENCRYPTION',
					'NEXUS_SMTP_ENCRYPTION',
					'BREVO_SMTP_ENCRYPTION',
					'SMTP_ENCRYPTION',
				],
				'auto'
			)
		),
		'smtp_enabled'           => false,
	];

	$settings['api_enabled'] = nexus_normalize_mail_bool(
		nexus_get_mail_runtime_value(
			[
				'NEXUS_BREVO_API_ENABLED',
				'NEXUS_MAIL_API_ENABLED',
				'BREVO_API_ENABLED',
			],
			''
		),
		'' !== $settings['api_key']
	);

	$settings['smtp_enabled'] = nexus_normalize_mail_bool(
		nexus_get_mail_runtime_value(
			[
				'NEXUS_BREVO_SMTP_ENABLED',
				'NEXUS_SMTP_ENABLED',
				'BREVO_SMTP_ENABLED',
				'SMTP_ENABLED',
			],
			''
		),
		'' !== $settings['smtp_username'] && '' !== $settings['smtp_password']
	);

	return (array) apply_filters( 'nexus_brevo_mail_settings', $settings );
}

/**
 * Check whether the Brevo API transport is enabled.
 *
 * @return bool
 */
function nexus_is_brevo_api_enabled() {
	$settings = nexus_get_brevo_mail_settings();

	return ! empty( $settings['api_enabled'] )
		&& ! empty( $settings['api_key'] )
		&& ! empty( $settings['api_endpoint'] )
		&& ! empty( $settings['from_email'] )
		&& is_email( $settings['from_email'] );
}

/**
 * Check whether the Brevo SMTP fallback transport is enabled.
 *
 * @return bool
 */
function nexus_is_brevo_mail_enabled() {
	$settings = nexus_get_brevo_mail_settings();

	return ! empty( $settings['smtp_enabled'] )
		&& ! empty( $settings['smtp_host'] )
		&& ! empty( $settings['smtp_username'] )
		&& ! empty( $settings['smtp_password'] );
}

/**
 * Return the option key used for mail diagnostics.
 *
 * @return string
 */
function nexus_get_mail_diagnostics_option_key() {
	return 'nexus_brevo_mail_diagnostics';
}

/**
 * Return a normalized count of intended recipients.
 *
 * @param array|string $to wp_mail recipient input.
 * @return int
 */
function nexus_get_mail_recipient_count( $to ) {
	return count( nexus_parse_mail_recipients( $to ) );
}

/**
 * Persist a safe diagnostics snapshot for the last mail event.
 *
 * @param string               $event Event name.
 * @param array<string, mixed> $context Event context.
 * @return void
 */
function nexus_record_mail_diagnostic_event( $event, $context = [] ) {
	$payload = [
		'event'      => (string) $event,
		'timestamp'  => gmdate( 'c' ),
		'provider'   => isset( $context['provider'] ) ? (string) $context['provider'] : '',
		'subject'    => isset( $context['subject'] ) ? wp_strip_all_tags( (string) $context['subject'] ) : '',
		'to_count'   => isset( $context['to_count'] ) ? (int) $context['to_count'] : 0,
		'status_code' => isset( $context['status_code'] ) ? (int) $context['status_code'] : 0,
		'error_code' => isset( $context['error_code'] ) ? (string) $context['error_code'] : '',
		'error_message' => isset( $context['error_message'] ) ? wp_strip_all_tags( (string) $context['error_message'] ) : '',
		'message_id' => isset( $context['message_id'] ) ? (string) $context['message_id'] : '',
	];

	update_option( nexus_get_mail_diagnostics_option_key(), $payload, false );

	if ( function_exists( 'error_log' ) ) {
		error_log( '[Nexus Mail] ' . wp_json_encode( $payload ) );
	}
}

/**
 * Return a live diagnostics snapshot for the current mail layer.
 *
 * @return array<string, mixed>
 */
function nexus_get_mail_diagnostics_snapshot() {
	$settings = nexus_get_brevo_mail_settings();
	$last     = get_option( nexus_get_mail_diagnostics_option_key(), [] );

	return [
		'mailer_loaded'         => true,
		'provider'              => nexus_is_brevo_api_enabled() ? 'brevo_api' : ( nexus_is_brevo_mail_enabled() ? 'brevo_smtp_fallback' : 'wordpress_default' ),
		'api_enabled'           => (bool) $settings['api_enabled'],
		'api_key_present'       => ! empty( $settings['api_key'] ),
		'api_endpoint'          => (string) $settings['api_endpoint'],
		'from_email'            => (string) $settings['from_email'],
		'from_name'             => (string) $settings['from_name'],
		'smtp_fallback_enabled' => (bool) $settings['smtp_enabled'],
		'last_event'            => is_array( $last ) ? $last : [],
	];
}

/**
 * Mask an email address for public diagnostics output.
 *
 * @param string $email Raw email address.
 * @return string
 */
function nexus_mask_diagnostic_email( $email ) {
	$email = sanitize_email( (string) $email );
	if ( ! is_email( $email ) ) {
		return '';
	}

	list( $local, $domain ) = explode( '@', $email, 2 );
	if ( strlen( $local ) <= 2 ) {
		$local = substr( $local, 0, 1 ) . '*';
	} else {
		$local = substr( $local, 0, 1 ) . str_repeat( '*', max( 1, strlen( $local ) - 2 ) ) . substr( $local, -1 );
	}

	return $local . '@' . $domain;
}

/**
 * Return a redacted public diagnostics snapshot.
 *
 * @return array<string, mixed>
 */
function nexus_get_public_mail_diagnostics_snapshot() {
	$snapshot   = nexus_get_mail_diagnostics_snapshot();
	$last_event = isset( $snapshot['last_event'] ) && is_array( $snapshot['last_event'] ) ? $snapshot['last_event'] : [];

	return [
		'mailer_loaded'         => ! empty( $snapshot['mailer_loaded'] ),
		'provider'              => (string) ( $snapshot['provider'] ?? '' ),
		'api_enabled'           => ! empty( $snapshot['api_enabled'] ),
		'api_key_present'       => ! empty( $snapshot['api_key_present'] ),
		'api_endpoint'          => (string) ( $snapshot['api_endpoint'] ?? '' ),
		'from_email_masked'     => nexus_mask_diagnostic_email( (string) ( $snapshot['from_email'] ?? '' ) ),
		'smtp_fallback_enabled' => ! empty( $snapshot['smtp_fallback_enabled'] ),
		// Kein error_message: Provider-Fehlertexte können Empfängeradressen
		// oder interne Details enthalten. Der Volltext steht nur im
		// Admin-Endpoint /mail-diagnostics.
		'last_event'            => [
			'event'       => isset( $last_event['event'] ) ? (string) $last_event['event'] : '',
			'timestamp'   => isset( $last_event['timestamp'] ) ? (string) $last_event['timestamp'] : '',
			'provider'    => isset( $last_event['provider'] ) ? (string) $last_event['provider'] : '',
			'status_code' => isset( $last_event['status_code'] ) ? (int) $last_event['status_code'] : 0,
			'error_code'  => isset( $last_event['error_code'] ) ? (string) $last_event['error_code'] : '',
			'message_id'  => isset( $last_event['message_id'] ) ? (string) $last_event['message_id'] : '',
		],
	];
}

/**
 * Register an admin-only diagnostics endpoint for the mail layer.
 *
 * @return void
 */
function nexus_register_mail_diagnostics_rest_route() {
	register_rest_route(
		'nexus/v1',
		'/mail-diagnostics',
		[
			'methods'             => 'GET',
			'callback'            => 'nexus_get_mail_diagnostics_rest_response',
			'permission_callback' => static function() {
				return current_user_can( 'manage_options' );
			},
		]
	);

	register_rest_route(
		'nexus/v1',
		'/mail-diagnostics-public',
		[
			'methods'             => 'GET',
			'callback'            => 'nexus_get_public_mail_diagnostics_rest_response',
			'permission_callback' => '__return_true',
		]
	);
}
add_action( 'rest_api_init', 'nexus_register_mail_diagnostics_rest_route' );

/**
 * Return the live mail diagnostics response.
 *
 * @return WP_REST_Response
 */
function nexus_get_mail_diagnostics_rest_response() {
	return new WP_REST_Response( nexus_get_mail_diagnostics_snapshot(), 200 );
}

/**
 * Return the public mail diagnostics response.
 *
 * @return WP_REST_Response
 */
function nexus_get_public_mail_diagnostics_rest_response() {
	return new WP_REST_Response( nexus_get_public_mail_diagnostics_snapshot(), 200 );
}

/**
 * Normalize and split a mail header list.
 *
 * @param array|string $headers Raw headers.
 * @return array<int, string>
 */
function nexus_normalize_mail_header_lines( $headers ) {
	if ( empty( $headers ) ) {
		return [];
	}

	if ( is_array( $headers ) ) {
		return array_values(
			array_filter(
				array_map(
					static function( $header ) {
						return trim( (string) $header );
					},
					$headers
				)
			)
		);
	}

	$headers = str_replace( "\r\n", "\n", (string) $headers );
	$headers = str_replace( "\r", "\n", $headers );

	return array_values(
		array_filter(
			array_map( 'trim', explode( "\n", $headers ) )
		)
	);
}

/**
 * Parse a single mailbox string.
 *
 * @param string $value Mailbox value.
 * @return array{email:string,name:string}|null
 */
function nexus_parse_mailbox_string( $value ) {
	$value = trim( (string) $value );
	if ( '' === $value ) {
		return null;
	}

	if ( preg_match( '/^(.*)<([^>]+)>$/', $value, $matches ) ) {
		$name  = trim( trim( (string) $matches[1] ), "\"' " );
		$email = sanitize_email( (string) $matches[2] );

		if ( is_email( $email ) ) {
			return [
				'email' => $email,
				'name'  => $name,
			];
		}
	}

	$email = sanitize_email( $value );
	if ( is_email( $email ) ) {
		return [
			'email' => $email,
			'name'  => '',
		];
	}

	return null;
}

/**
 * Parse one or many recipient strings.
 *
 * @param array|string $values Raw recipient input.
 * @return array<int, array{email:string,name:string}>
 */
function nexus_parse_mail_recipients( $values ) {
	$entries = [];

	if ( is_array( $values ) ) {
		foreach ( $values as $value ) {
			if ( is_array( $value ) ) {
				$email = isset( $value['email'] ) ? sanitize_email( (string) $value['email'] ) : '';
				$name  = isset( $value['name'] ) ? trim( (string) $value['name'] ) : '';

				if ( is_email( $email ) ) {
					$entries[] = [
						'email' => $email,
						'name'  => $name,
					];
				}

				continue;
			}

			foreach ( explode( ',', (string) $value ) as $fragment ) {
				$recipient = nexus_parse_mailbox_string( $fragment );
				if ( $recipient ) {
					$entries[] = $recipient;
				}
			}
		}
	} else {
		foreach ( explode( ',', (string) $values ) as $fragment ) {
			$recipient = nexus_parse_mailbox_string( $fragment );
			if ( $recipient ) {
				$entries[] = $recipient;
			}
		}
	}

	$unique = [];
	foreach ( $entries as $entry ) {
		$unique[ strtolower( $entry['email'] ) ] = $entry;
	}

	return array_values( $unique );
}

/**
 * Normalize Brevo recipient payloads and drop empty optional fields.
 *
 * @param array<int, array{email:string,name:string}> $recipients Parsed recipients.
 * @return array<int, array<string, string>>
 */
function nexus_prepare_brevo_recipients( $recipients ) {
	$prepared = [];

	foreach ( (array) $recipients as $recipient ) {
		$email = isset( $recipient['email'] ) ? sanitize_email( (string) $recipient['email'] ) : '';
		$name  = isset( $recipient['name'] ) ? trim( (string) $recipient['name'] ) : '';

		if ( ! is_email( $email ) ) {
			continue;
		}

		$entry = [
			'email' => $email,
		];

		if ( '' !== $name ) {
			$entry['name'] = $name;
		}

		$prepared[] = $entry;
	}

	return $prepared;
}

/**
 * Parse wp_mail headers for API transport.
 *
 * @param array|string $headers Raw wp_mail headers.
 * @return array<string, mixed>
 */
function nexus_parse_wp_mail_headers( $headers ) {
	$parsed = [
		'from'         => null,
		'reply_to'     => [],
		'cc'           => [],
		'bcc'          => [],
		'content_type' => 'text/plain',
		'charset'      => 'UTF-8',
		'custom'       => [],
		'tags'         => [],
		'transport'    => 'api',
	];

	foreach ( nexus_normalize_mail_header_lines( $headers ) as $header_line ) {
		if ( false === strpos( $header_line, ':' ) ) {
			continue;
		}

		list( $name, $value ) = explode( ':', $header_line, 2 );
		$name       = trim( (string) $name );
		$value      = trim( (string) $value );
		$lower_name = strtolower( $name );

		switch ( $lower_name ) {
			case 'from':
				$parsed['from'] = nexus_parse_mailbox_string( $value );
				break;

			case 'reply-to':
				$parsed['reply_to'] = nexus_parse_mail_recipients( $value );
				break;

			case 'cc':
				$parsed['cc'] = nexus_parse_mail_recipients( $value );
				break;

			case 'bcc':
				$parsed['bcc'] = nexus_parse_mail_recipients( $value );
				break;

			case 'content-type':
				$segments = array_map( 'trim', explode( ';', $value ) );
				if ( ! empty( $segments[0] ) ) {
					$parsed['content_type'] = strtolower( (string) $segments[0] );
				}

				foreach ( $segments as $segment ) {
					if ( 0 !== stripos( $segment, 'charset=' ) ) {
						continue;
					}

					$parsed['charset'] = trim( substr( $segment, 8 ), "\"' " );
				}
				break;

			case 'x-nexus-brevo-tags':
				$parsed['tags'] = array_values(
					array_filter(
						array_map(
							static function( $tag ) {
								return trim( (string) $tag );
							},
							explode( ',', $value )
						)
					)
				);
				break;

			case 'x-nexus-mail-transport':
				$parsed['transport'] = strtolower( trim( $value ) );
				break;

			case 'mime-version':
			case 'x-mailer':
				break;

			default:
				$parsed['custom'][ $name ] = $value;
				break;
		}
	}

	return $parsed;
}

/**
 * Build a plain text fallback from HTML content.
 *
 * @param string $message Message body.
 * @return string
 */
function nexus_get_mail_text_fallback( $message ) {
	$text = wp_strip_all_tags( (string) $message, true );
	$text = preg_replace( "/\n{3,}/", "\n\n", (string) $text );

	return trim( (string) $text );
}

/**
 * Convert local attachment paths into Brevo API attachments.
 *
 * @param array<int, string>|string $attachments Attachment list.
 * @return array<int, array{name:string,content:string}>|WP_Error
 */
function nexus_get_brevo_api_attachments( $attachments ) {
	$attachments = is_array( $attachments ) ? $attachments : [ $attachments ];
	$prepared    = [];

	foreach ( $attachments as $attachment ) {
		$path = trim( (string) $attachment );
		if ( '' === $path ) {
			continue;
		}

		if ( ! file_exists( $path ) || ! is_readable( $path ) ) {
			return new WP_Error(
				'nexus_brevo_attachment_missing',
				sprintf( 'Attachment could not be read: %s', $path )
			);
		}

		$content = file_get_contents( $path );
		if ( false === $content ) {
			return new WP_Error(
				'nexus_brevo_attachment_unreadable',
				sprintf( 'Attachment could not be loaded: %s', $path )
			);
		}

		$prepared[] = [
			'name'    => wp_basename( $path ),
			'content' => base64_encode( $content ),
		];
	}

	return $prepared;
}

/**
 * Build the Brevo API payload for a wp_mail call.
 *
 * @param array<string, mixed> $atts Parsed wp_mail arguments.
 * @return array<string, mixed>|WP_Error
 */
function nexus_build_brevo_api_payload( $atts ) {
	$settings = nexus_get_brevo_mail_settings();
	$headers  = nexus_parse_wp_mail_headers( $atts['headers'] ?? [] );
	$to       = nexus_prepare_brevo_recipients( nexus_parse_mail_recipients( $atts['to'] ?? [] ) );

	if ( empty( $to ) ) {
		return new WP_Error( 'nexus_brevo_missing_recipient', 'No valid recipient email was found.' );
	}

	$sender_email = $settings['from_email'];
	$sender_name  = $settings['from_name'];

	if ( ( ! $sender_email || ! is_email( $sender_email ) ) && ! empty( $headers['from'] ) && is_array( $headers['from'] ) ) {
		$sender_email = $headers['from']['email'];
		$sender_name  = $headers['from']['name'];
	}

	if ( ! $sender_email || ! is_email( $sender_email ) ) {
		return new WP_Error( 'nexus_brevo_missing_sender', 'No verified sender email is configured for Brevo.' );
	}

	$is_html = 'text/html' === strtolower( (string) $headers['content_type'] )
		|| ( false !== stripos( (string) ( $atts['message'] ?? '' ), '<html' ) )
		|| ( false !== stripos( (string) ( $atts['message'] ?? '' ), '<body' ) );

	$payload = [
		'sender'  => [
			'email' => (string) $sender_email,
		],
		'to'      => $to,
		'subject' => (string) ( $atts['subject'] ?? '' ),
	];

	if ( '' !== trim( (string) $sender_name ) ) {
		$payload['sender']['name'] = (string) $sender_name;
	}

	if ( $is_html ) {
		$payload['htmlContent'] = (string) ( $atts['message'] ?? '' );
	} else {
		$payload['textContent'] = (string) ( $atts['message'] ?? '' );
	}

	if ( ! empty( $headers['reply_to'][0]['email'] ) ) {
		$payload['replyTo'] = [
			'email' => (string) $headers['reply_to'][0]['email'],
		];

		if ( '' !== trim( (string) $headers['reply_to'][0]['name'] ) ) {
			$payload['replyTo']['name'] = (string) $headers['reply_to'][0]['name'];
		}
	}

	if ( ! empty( $headers['cc'] ) ) {
		$payload['cc'] = nexus_prepare_brevo_recipients( $headers['cc'] );
	}

	if ( ! empty( $headers['bcc'] ) ) {
		$payload['bcc'] = nexus_prepare_brevo_recipients( $headers['bcc'] );
	}

	if ( ! empty( $headers['custom'] ) ) {
		$payload['headers'] = $headers['custom'];
	}

	if ( ! empty( $headers['tags'] ) ) {
		$payload['tags'] = $headers['tags'];
	}

	$attachments = nexus_get_brevo_api_attachments( $atts['attachments'] ?? [] );
	if ( is_wp_error( $attachments ) ) {
		return $attachments;
	}

	if ( ! empty( $attachments ) ) {
		$payload['attachment'] = $attachments;
	}

	return (array) apply_filters( 'nexus_brevo_api_payload', $payload, $atts, $settings, $headers );
}

/**
 * Emit a standard wp_mail failure action.
 *
 * @param string               $code Error code.
 * @param string               $message Error message.
 * @param array<string, mixed> $data Error context.
 * @return WP_Error
 */
function nexus_create_wp_mail_failed_error( $code, $message, $data = [] ) {
	$error = new WP_Error( $code, $message, $data );
	do_action( 'wp_mail_failed', $error );

	return $error;
}

/**
 * Send a wp_mail payload through the Brevo API.
 *
 * @param array<string, mixed> $atts Parsed wp_mail arguments.
 * @return true|WP_Error
 */
function nexus_send_wp_mail_via_brevo_api( $atts ) {
	$settings = nexus_get_brevo_mail_settings();
	$payload  = nexus_build_brevo_api_payload( $atts );

	if ( is_wp_error( $payload ) ) {
		nexus_record_mail_diagnostic_event(
			'api_payload_error',
			[
				'provider'      => 'brevo_api',
				'subject'       => $atts['subject'] ?? '',
				'to_count'      => nexus_get_mail_recipient_count( $atts['to'] ?? [] ),
				'error_code'    => $payload->get_error_code(),
				'error_message' => $payload->get_error_message(),
			]
		);

		return nexus_create_wp_mail_failed_error(
			$payload->get_error_code(),
			$payload->get_error_message(),
			[
				'mail_data' => $atts,
			]
		);
	}

	$response = wp_remote_post(
		(string) $settings['api_endpoint'],
		[
			'timeout' => (int) $settings['api_timeout'],
			'headers' => [
				'Accept'       => 'application/json',
				'Content-Type' => 'application/json',
				'api-key'      => (string) $settings['api_key'],
			],
			'body'    => wp_json_encode( $payload ),
		]
	);

	if ( is_wp_error( $response ) ) {
		nexus_record_mail_diagnostic_event(
			'api_request_failed',
			[
				'provider'      => 'brevo_api',
				'subject'       => $atts['subject'] ?? '',
				'to_count'      => nexus_get_mail_recipient_count( $atts['to'] ?? [] ),
				'error_code'    => 'nexus_brevo_api_request_failed',
				'error_message' => $response->get_error_message(),
			]
		);

		return nexus_create_wp_mail_failed_error(
			'nexus_brevo_api_request_failed',
			$response->get_error_message(),
			[
				'mail_data' => $atts,
			]
		);
	}

	$status_code = (int) wp_remote_retrieve_response_code( $response );
	$body        = (string) wp_remote_retrieve_body( $response );
	$decoded     = json_decode( $body, true );

	if ( $status_code < 200 || $status_code >= 300 ) {
		$message = 'Brevo API rejected the transactional email request.';

		if ( is_array( $decoded ) ) {
			if ( ! empty( $decoded['message'] ) ) {
				$message = (string) $decoded['message'];
			} elseif ( ! empty( $decoded['code'] ) ) {
				$message = (string) $decoded['code'];
			}
		}

		nexus_record_mail_diagnostic_event(
			'api_rejected',
			[
				'provider'      => 'brevo_api',
				'subject'       => $atts['subject'] ?? '',
				'to_count'      => nexus_get_mail_recipient_count( $atts['to'] ?? [] ),
				'status_code'   => $status_code,
				'error_code'    => 'nexus_brevo_api_rejected',
				'error_message' => $message,
			]
		);

		return nexus_create_wp_mail_failed_error(
			'nexus_brevo_api_rejected',
			$message,
			[
				'status_code' => $status_code,
				'response'    => $decoded ?: $body,
				'mail_data'   => $atts,
			]
		);
	}

	nexus_record_mail_diagnostic_event(
		'api_success',
		[
			'provider'    => 'brevo_api',
			'subject'     => $atts['subject'] ?? '',
			'to_count'    => nexus_get_mail_recipient_count( $atts['to'] ?? [] ),
			'status_code' => $status_code,
			'message_id'  => is_array( $decoded ) && ! empty( $decoded['messageId'] ) ? (string) $decoded['messageId'] : '',
		]
	);

	do_action( 'nexus_brevo_api_mail_sent', $payload, $decoded, $atts );

	return true;
}

/**
 * Decide whether the current wp_mail call should use the Brevo API layer.
 *
 * @param array<string, mixed> $atts Parsed wp_mail arguments.
 * @return bool
 */
function nexus_should_route_wp_mail_via_brevo_api( $atts ) {
	if ( ! nexus_is_brevo_api_enabled() ) {
		return false;
	}

	$headers = nexus_parse_wp_mail_headers( $atts['headers'] ?? [] );
	if ( 'local' === $headers['transport'] || 'smtp' === $headers['transport'] ) {
		return false;
	}

	if ( 0 === strpos( (string) $headers['content_type'], 'multipart/' ) ) {
		return false;
	}

	return (bool) apply_filters( 'nexus_use_brevo_api_for_wp_mail', true, $atts, $headers );
}

/**
 * Intercept wp_mail and route it through the Brevo API when enabled.
 *
 * @param null|bool            $return Short-circuit value.
 * @param array<string, mixed> $atts Parsed wp_mail arguments.
 * @return null|bool
 */
function nexus_intercept_wp_mail_with_brevo_api( $return, $atts ) {
	if ( null !== $return ) {
		return $return;
	}

	if ( ! nexus_should_route_wp_mail_via_brevo_api( $atts ) ) {
		return null;
	}

	nexus_record_mail_diagnostic_event(
		'api_attempt',
		[
			'provider' => 'brevo_api',
			'subject'  => $atts['subject'] ?? '',
			'to_count' => nexus_get_mail_recipient_count( $atts['to'] ?? [] ),
		]
	);

	$result = nexus_send_wp_mail_via_brevo_api( $atts );
	if ( true === $result ) {
		return true;
	}

	$settings = nexus_get_brevo_mail_settings();
	if ( ! empty( $settings['api_fallback_to_wp_mail'] ) ) {
		return null;
	}

	return false;
}
add_filter( 'pre_wp_mail', 'nexus_intercept_wp_mail_with_brevo_api', 10, 2 );

/**
 * Resolve the PHPMailer encryption mode for the SMTP fallback.
 *
 * @param array<string, mixed> $settings Mail settings.
 * @return array{secure:string, auto_tls:bool}
 */
function nexus_get_brevo_mail_transport_mode( $settings ) {
	$port       = isset( $settings['smtp_port'] ) ? (int) $settings['smtp_port'] : 587;
	$encryption = isset( $settings['smtp_encryption'] ) ? strtolower( trim( (string) $settings['smtp_encryption'] ) ) : 'auto';

	if ( in_array( $encryption, [ 'ssl', 'smtps' ], true ) ) {
		return [
			'secure'   => 'ssl',
			'auto_tls' => false,
		];
	}

	if ( in_array( $encryption, [ 'tls', 'starttls' ], true ) ) {
		return [
			'secure'   => 'tls',
			'auto_tls' => true,
		];
	}

	if ( 465 === $port ) {
		return [
			'secure'   => 'ssl',
			'auto_tls' => false,
		];
	}

	return [
		'secure'   => '',
		'auto_tls' => true,
	];
}

/**
 * Configure the global WordPress mailer to use Brevo SMTP as a fallback.
 *
 * @param PHPMailer $phpmailer Mailer instance.
 * @return void
 */
function nexus_configure_brevo_phpmailer( $phpmailer ) {
	if ( nexus_is_brevo_api_enabled() || ! nexus_is_brevo_mail_enabled() ) {
		return;
	}

	$settings = nexus_get_brevo_mail_settings();
	$mode     = nexus_get_brevo_mail_transport_mode( $settings );

	$phpmailer->isSMTP();
	$phpmailer->Host        = (string) $settings['smtp_host'];
	$phpmailer->Port        = (int) $settings['smtp_port'];
	$phpmailer->SMTPAuth    = true;
	$phpmailer->Username    = (string) $settings['smtp_username'];
	$phpmailer->Password    = (string) $settings['smtp_password'];
	$phpmailer->CharSet     = 'UTF-8';
	$phpmailer->SMTPAutoTLS = (bool) $mode['auto_tls'];
	$phpmailer->SMTPSecure  = (string) $mode['secure'];

	if ( ! empty( $settings['from_email'] ) && is_email( $settings['from_email'] ) ) {
		$phpmailer->setFrom( (string) $settings['from_email'], (string) $settings['from_name'], false );
		$phpmailer->Sender = (string) $settings['from_email'];
	}
}
add_action( 'phpmailer_init', 'nexus_configure_brevo_phpmailer' );

/**
 * Force the global from address to the configured Brevo sender when active.
 *
 * @param string $from Existing from address.
 * @return string
 */
function nexus_filter_brevo_mail_from( $from ) {
	$settings = nexus_get_brevo_mail_settings();

	return ! empty( $settings['from_email'] ) && is_email( $settings['from_email'] )
		? (string) $settings['from_email']
		: $from;
}
add_filter( 'wp_mail_from', 'nexus_filter_brevo_mail_from' );

/**
 * Force the global from name to the configured Brevo sender name when active.
 *
 * @param string $name Existing from name.
 * @return string
 */
function nexus_filter_brevo_mail_from_name( $name ) {
	$settings = nexus_get_brevo_mail_settings();

	return ! empty( $settings['from_name'] ) ? (string) $settings['from_name'] : $name;
}
add_filter( 'wp_mail_from_name', 'nexus_filter_brevo_mail_from_name' );
