<?php
/**
 * Antwortfrist-Wächter für neue Anfragen.
 *
 * Jede Bestätigungsmail und jede Anfrageseite verspricht eine persönliche
 * Antwort innerhalb von HU_RESPONSE_HOURS Werktagsstunden. Der Wächter prüft
 * stündlich (am vorhandenen Event nexus_crm_sales_followup_cron), ob eine
 * Sales-Chance kurz vor Fristende noch unbearbeitet ist, und schickt dann eine
 * interne Sammel-Erinnerung an die Benachrichtigungsadresse. Der Lead selbst
 * bekommt nichts.
 *
 * Unbearbeitet heißt: keine ausgehende Mail über das CRM und kein
 * Stufenwechsel durch einen Menschen seit Anlage. Die Stufe allein reicht
 * nicht, weil Marktcheck-Chancen automatisch auf "Qualifiziert" oder
 * "Später" landen. Wer außerhalb des CRM geantwortet hat, bekommt die
 * Erinnerung trotzdem einmal – der Stufenwechsel beendet sie.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Werktagsstunden vor Fristende, zu denen erinnert wird. */
const NEXUS_CRM_RESPONSE_REMINDER_LEAD_HOURS = 6;

/** Ältere Chancen bleiben außen vor, damit der erste Lauf keine Altfälle meldet. */
const NEXUS_CRM_RESPONSE_REMINDER_MAX_AGE_DAYS = 14;

/** Fehlversuche, nach denen eine Erinnerung als gescheitert gilt. */
const NEXUS_CRM_RESPONSE_REMINDER_MAX_ATTEMPTS = 3;

/**
 * Return the promised deadline and the reminder point for one opportunity.
 *
 * Both use nexus_compute_intake_response_deadline(), so weekends are skipped
 * exactly like in the promise shown to the lead.
 *
 * @param int $created_at GMT Unix timestamp of the opportunity.
 * @return array{deadline:int,remind_at:int,human:string}
 */
function nexus_get_crm_response_window( $created_at ) {
	$created_at = (int) $created_at;
	$deadline   = nexus_compute_intake_response_deadline( $created_at );
	$remind     = nexus_compute_intake_response_deadline( $created_at, max( 1, HU_RESPONSE_HOURS - NEXUS_CRM_RESPONSE_REMINDER_LEAD_HOURS ) );

	return [
		'deadline'  => (int) strtotime( $deadline['iso'] ),
		'remind_at' => (int) strtotime( $remind['iso'] ),
		'human'     => $deadline['human'],
	];
}

/**
 * Check whether someone already worked on an opportunity since it was created.
 *
 * @param int $opportunity_id Opportunity post ID.
 * @param int $created_at     GMT Unix timestamp of the opportunity.
 * @return bool
 */
function nexus_crm_opportunity_has_response( $opportunity_id, $created_at ) {
	$last_contact = (int) get_post_meta( $opportunity_id, '_nexus_opportunity_last_contact_at', true );

	if ( $last_contact >= (int) $created_at && $last_contact > 0 ) {
		return true;
	}

	$handled = get_posts(
		[
			'post_type'      => 'nexus_crm_activity',
			'post_status'    => 'private',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'no_found_rows'  => true,
			'meta_query'     => [
				'relation' => 'AND',
				[
					'key'   => '_nexus_activity_opportunity_id',
					'value' => (int) $opportunity_id,
					'type'  => 'NUMERIC',
				],
				[
					'relation' => 'OR',
					[
						'key'   => '_nexus_activity_direction',
						'value' => 'outbound',
					],
					[
						'key'   => '_nexus_activity_type',
						'value' => 'stage_change',
					],
				],
			],
		]
	);

	return ! empty( $handled );
}

/**
 * Collect opportunities whose response deadline is close or already passed.
 *
 * @param int $now GMT Unix timestamp.
 * @return array<int, array<string, mixed>>
 */
function nexus_get_crm_opportunities_awaiting_response( $now ) {
	$ids = get_posts(
		[
			'post_type'      => 'nexus_opportunity',
			'post_status'    => 'private',
			'posts_per_page' => 50,
			'fields'         => 'ids',
			'no_found_rows'  => true,
			'meta_query'     => [
				'relation' => 'AND',
				[
					'key'     => '_nexus_opportunity_stage',
					'value'   => [ 'new', 'qualified', 'nurture' ],
					'compare' => 'IN',
				],
				[
					'key'     => '_nexus_opportunity_created_at',
					'value'   => (int) $now - ( NEXUS_CRM_RESPONSE_REMINDER_MAX_AGE_DAYS * DAY_IN_SECONDS ),
					'compare' => '>=',
					'type'    => 'NUMERIC',
				],
				[
					'key'     => '_nexus_opportunity_response_reminder_at',
					'compare' => 'NOT EXISTS',
				],
			],
		]
	);

	$due = [];

	foreach ( $ids as $opportunity_id ) {
		$opportunity = nexus_get_crm_opportunity( (int) $opportunity_id );

		// Selbst angelegte Leads brauchen keine Erinnerung an die eigene Antwort.
		if ( null === $opportunity || 'manual' === $opportunity['source'] || (int) $opportunity['created_at'] <= 0 ) {
			continue;
		}

		$window = nexus_get_crm_response_window( (int) $opportunity['created_at'] );

		if ( $window['remind_at'] <= 0 || (int) $now < $window['remind_at'] ) {
			continue;
		}

		if ( nexus_crm_opportunity_has_response( (int) $opportunity['id'], (int) $opportunity['created_at'] ) ) {
			continue;
		}

		$opportunity['deadline']       = $window['deadline'];
		$opportunity['deadline_human'] = $window['human'];
		$due[]                         = $opportunity;
	}

	return $due;
}

/**
 * Build the internal reminder mail for a list of due opportunities.
 *
 * @param array<int, array<string, mixed>> $due Due opportunities.
 * @param int                              $now GMT Unix timestamp.
 * @return array{subject:string,html:string}
 */
function nexus_build_crm_response_reminder_mail( $due, $now ) {
	$source_labels = function_exists( 'nexus_get_crm_contact_source_labels' ) ? nexus_get_crm_contact_source_labels() : [];
	$rows          = '';

	foreach ( $due as $opportunity ) {
		$overdue = (int) $now > (int) $opportunity['deadline'];
		$source  = (string) ( $source_labels[ $opportunity['source'] ] ?? $opportunity['source'] );
		$details = array_filter(
			[
				'' !== $source ? $source : '',
				(string) $opportunity['service'],
				nexus_get_crm_sales_stage_label( (string) $opportunity['stage'] ),
			]
		);

		$rows .= sprintf(
			'<tr><td style="padding:14px 16px; border:1px solid rgba(255,255,255,0.08); border-radius:18px; background:rgba(255,255,255,0.03); font-family:Helvetica, Arial, sans-serif;">
				<div style="font-size:15px; font-weight:600; color:#f7f3ee; margin-bottom:6px;">%1$s</div>
				<div style="font-size:14px; line-height:1.8; color:#c5ced7;">%2$s<br><strong style="color:#f7f3ee;">Frist:</strong> %3$s%4$s<br><a href="%5$s" style="color:#f7f3ee;">In der Pipeline öffnen</a></div>
			</td></tr>',
			esc_html( (string) $opportunity['title'] ),
			esc_html( implode( ' · ', $details ) ),
			esc_html( (string) $opportunity['deadline_human'] ),
			$overdue ? ' <strong style="color:#f0a071;">(abgelaufen)</strong>' : '',
			esc_url( nexus_get_crm_sales_admin_url( [ 'opportunity' => (int) $opportunity['id'] ] ) )
		);
	}

	$count   = count( $due );
	$subject = sprintf(
		'[%s] Antwortfrist: %s',
		wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ),
		1 === $count ? '1 Anfrage wartet auf Rückmeldung' : $count . ' Anfragen warten auf Rückmeldung'
	);
	$content = '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin:0 0 18px 0; border-collapse:separate; border-spacing:0 10px;">' . $rows . '</table>';
	$intro   = sprintf(
		'Zugesagt ist eine persönliche Antwort %s. Für diese Anfragen gibt es seit Eingang weder eine Mail aus dem CRM noch einen Statuswechsel.',
		hu_response_promise( 'window' )
	);

	$html = function_exists( 'nexus_get_contact_email_shell' )
		? nexus_get_contact_email_shell(
			[
				'preheader' => 1 === $count ? 'Eine Anfrage wartet auf Rückmeldung.' : $count . ' Anfragen warten auf Rückmeldung.',
				'eyebrow'   => 'Antwortfrist',
				'headline'  => 1 === $count ? 'Eine Anfrage wartet auf Rückmeldung.' : $count . ' Anfragen warten auf Rückmeldung.',
				'intro'     => $intro,
				'content'   => $content,
				'footer'    => 'Schon außerhalb des CRM beantwortet? Dann den Status in der Pipeline setzen; jede Anfrage wird nur einmal gemeldet.',
			]
		)
		: $content;

	return [
		'subject' => $subject,
		'html'    => $html,
	];
}

/**
 * Send one internal reminder for all opportunities that reached their reminder point.
 *
 * Runs on the hourly nexus_crm_sales_followup_cron event. A transient lock
 * prevents overlapping runs; each opportunity is reported once. Failed sends
 * retry on the next run and give up after NEXUS_CRM_RESPONSE_REMINDER_MAX_ATTEMPTS.
 *
 * @return void
 */
function nexus_run_crm_response_watchdog() {
	$lock_key = 'nexus_crm_response_watchdog_lock';

	if ( get_transient( $lock_key ) ) {
		return;
	}

	set_transient( $lock_key, 1, 10 * MINUTE_IN_SECONDS );

	$now = current_time( 'timestamp', true );
	$due = nexus_get_crm_opportunities_awaiting_response( $now );

	if ( empty( $due ) ) {
		delete_transient( $lock_key );
		return;
	}

	$recipient = function_exists( 'nexus_get_contact_notification_email' ) ? nexus_get_contact_notification_email() : (string) get_option( 'admin_email' );
	$sent      = false;

	if ( '' !== $recipient && is_email( $recipient ) ) {
		$mail    = nexus_build_crm_response_reminder_mail( $due, $now );
		$headers = function_exists( 'nexus_append_mail_tags_header' )
			? nexus_append_mail_tags_header( [], [ 'crm_response_watchdog', 'internal_notification' ] )
			: [];
		$sent    = function_exists( 'nexus_send_contact_html_mail' )
			? nexus_send_contact_html_mail( $recipient, $mail['subject'], $mail['html'], $headers )
			: (bool) wp_mail( $recipient, $mail['subject'], $mail['html'], array_merge( [ 'Content-Type: text/html; charset=UTF-8' ], $headers ) );
	}

	foreach ( $due as $opportunity ) {
		$opportunity_id = (int) $opportunity['id'];
		$attempts       = (int) get_post_meta( $opportunity_id, '_nexus_opportunity_response_reminder_attempts', true ) + 1;

		update_post_meta( $opportunity_id, '_nexus_opportunity_response_reminder_attempts', $attempts );

		if ( ! $sent && $attempts < NEXUS_CRM_RESPONSE_REMINDER_MAX_ATTEMPTS ) {
			continue;
		}

		update_post_meta( $opportunity_id, '_nexus_opportunity_response_reminder_at', $now );
		nexus_record_crm_activity(
			[
				'contact_id'     => (int) $opportunity['contact_id'],
				'opportunity_id' => $opportunity_id,
				'type'           => 'response_reminder',
				'channel'        => 'email',
				'direction'      => 'internal',
				'subject'        => $sent ? 'Antwortfrist-Erinnerung verschickt' : 'Antwortfrist-Erinnerung gescheitert',
				'body'           => 'Frist: ' . (string) $opportunity['deadline_human'],
				'status'         => $sent ? 'sent' : 'failed',
			]
		);
	}

	if ( ! $sent ) {
		// Keine personenbezogenen Daten im Log, nur die Anzahl.
		error_log( sprintf( '[Nexus CRM] Antwortfrist-Erinnerung für %d Anfrage(n) konnte nicht versendet werden.', count( $due ) ) );
	}

	delete_transient( $lock_key );
}
add_action( 'nexus_crm_sales_followup_cron', 'nexus_run_crm_response_watchdog', 20 );
