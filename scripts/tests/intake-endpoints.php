<?php
/** Execute real endpoint, validation, CRM and mail-building code with isolated WP boundaries. */
require __DIR__ . '/intake-wordpress-doubles.php';
foreach ( [ 'canon/messaging-canon', 'canon/pricing-canon', 'canon/e3-proof-canon', 'crm', 'contact-page', 'whitelabel-request', 'review-crm', 'blog-notify' ] as $module ) {
	require __DIR__ . '/../../blocksy-child/inc/' . $module . '.php';
}
function check( $condition, $message ) { if ( ! $condition ) throw new RuntimeException( $message ); }
function run_case( $name, $callback ) { intake_reset(); $callback(); echo "PASS $name\n"; }
function call_intake( $kind, $payload ) {
	$callbacks = [ 'contact' => 'nexus_handle_contact_request_submission', 'whitelabel' => 'hu_handle_whitelabel_request_submission', 'marketcheck' => 'nexus_handle_review_request_submission', 'blog' => 'nexus_handle_blog_subscribe_submission' ];
	return $callbacks[ $kind ]( new WP_REST_Request( $payload ) );
}
$contact = [ 'name' => 'Fixture Person', 'email' => 'fixture@example.test', 'request_type' => 'project', 'focus' => 'tracking', 'message' => 'Bitte das bestehende Tracking prüfen.', 'consent' => '1' ];
$whitelabel = [ 'email' => 'fixture@example.test', 'task' => 'Bitte das bestehende Tracking prüfen.', 'case' => 'angebotsphase' ];
$marketcheck = [ 'intake_variant' => 'energy_systems', 'audit_type' => 'b2b_system_intake', 'solution_focus' => 'photovoltaik', 'business_fit' => 'founder_led_regional', 'sales_team_size' => 'one', 'project_timing' => 'sofort', 'name' => 'Fixture Person', 'company' => 'Fixture GmbH', 'position' => 'Inhaber', 'email' => 'fixture@example.test', 'postal_code' => '30159', 'consent_privacy' => 'accepted' ];
$blog = [ 'email' => 'fixture@example.test', 'nonce' => 'fixture-nonce' ];

foreach ( [ 'contact' => $contact, 'whitelabel' => $whitelabel ] as $kind => $payload ) {
	foreach ( [ false, true ] as $storage_fail ) {
		foreach ( [ false, true ] as $internal_mail ) {
			foreach ( [ false, true ] as $confirmation_mail ) {
				run_case( "$kind storage_fail=$storage_fail internal=$internal_mail confirmation=$confirmation_mail", static function () use ( $kind, $payload, $storage_fail, $internal_mail, $confirmation_mail ) {
					intake_reset( compact( 'storage_fail', 'internal_mail', 'confirmation_mail' ) );
					$r = call_intake( $kind, $payload );
					$success = ! $storage_fail || $internal_mail;
					check( $r->data['ok'] === $success && $r->status === ( $success ? 201 : 500 ), 'CRM OR mail acceptance contract' );
					check( count( $GLOBALS['intake_test']['posts'] ) === ( $storage_fail ? 0 : 1 ), 'Expected stored contacts' );
					check( count( $GLOBALS['intake_test']['mails'] ) === ( $success ? 2 : 1 ), 'Confirmation only after acceptance' );
					check( $GLOBALS['intake_test']['events'][0] === 'insert:nexus_contact', 'Storage precedes mail' );
					if ( $success && ! $internal_mail ) {
						check( count( nexus_get_recent_lead_notification_failures() ) === 1, 'Existing notification failure notice retained' );
						check( get_post_meta( 1, '_nexus_contact_notification_failed_at' ) > 0, 'Failure recorded on contact' );
					}
					if ( $kind === 'contact' && $storage_fail && $internal_mail ) check( $r->data['contactId'] === 0, 'Mail fallback has no CRM id' );
				} );
			}
		}
	}
	foreach ( [ true, false ] as $storage_fail ) {
		run_case( "$kind missing internal recipient, storage_fail=$storage_fail", static function () use ( $kind, $payload, $storage_fail ) {
			intake_reset( [ 'recipient' => '', 'storage_fail' => $storage_fail ] );
			$r = call_intake( $kind, $payload );
			check( $r->data['ok'] === ! $storage_fail, 'Missing recipient preserves CRM fallback' );
		} );
	}
}

foreach ( [ 'contact' => $contact, 'whitelabel' => $whitelabel, 'marketcheck' => $marketcheck, 'blog' => $blog ] as $kind => $payload ) {
	run_case( "$kind honeypot", static function () use ( $kind, $payload ) {
		$payload[ $kind === 'blog' ? 'website' : 'company_website' ] = 'bot';
		$r = call_intake( $kind, $payload );
		check( $r->status === 200 && $r->data['ok'] === true, 'Honeypot acknowledges' );
		check( ! $GLOBALS['intake_test']['events'], 'Honeypot has no storage or mail' );
	} );
	run_case( "$kind rate limit", static function () use ( $kind, $payload ) {
		$prefix = $kind === 'marketcheck' ? 'nexus_review_rl_' : ( $kind === 'blog' ? 'nexus_blog_notify_rl_' : 'nexus_contact_rl_' );
		set_transient( $prefix . md5( '192.0.2.1' . gmdate( 'YmdH' ) ), 10, HOUR_IN_SECONDS );
		$r = call_intake( $kind, $payload );
		check( $r->status === 429 && $r->data['ok'] === false && ! $GLOBALS['intake_test']['events'], 'Rate limit rejects before side effects' );
	} );
	run_case( "$kind invalid email", static function () use ( $kind, $payload ) {
		$payload['email'] = 'invalid';
		$r = call_intake( $kind, $payload );
		check( $r->status === 400 && $r->data['ok'] === false && ! $GLOBALS['intake_test']['events'], 'Validation rejects before side effects' );
	} );
}

$invalid_contact = [
	'missing_name' => [ 'name' => '' ], 'missing_request_type' => [ 'request_type' => 'unknown' ],
	'invalid_website' => [ 'website_url' => 'https://' ], 'invalid_linkedin' => [ 'linkedin_url' => 'https://' ],
	'missing_focus' => [ 'focus' => '' ], 'invalid_focus_type' => [ 'focus' => 'question' ],
	'invalid_timeline' => [ 'timeline' => 'unknown' ], 'invalid_budget' => [ 'budget' => 'unknown' ],
	'invalid_ad_budget' => [ 'ad_budget' => 'unknown' ], 'message_too_short' => [ 'message' => 'short' ], 'missing_consent' => [ 'consent' => '' ],
];
foreach ( $invalid_contact as $code => $changes ) {
	run_case( "contact validation $code", static function () use ( $contact, $code, $changes ) {
		$r = call_intake( 'contact', array_merge( $contact, $changes ) );
		check( $r->status === 400 && $r->data['error_code'] === $code && ! $GLOBALS['intake_test']['events'], 'Correct field error without side effects' );
	} );
}
run_case( 'assessment URL required, optional goal, canonical mail prefix', static function () use ( $contact ) {
	$p = array_merge( $contact, [ 'request_type' => 'ersteinschaetzung', 'focus' => 'ersteinschaetzung', 'message' => '' ] );
	$r = call_intake( 'contact', $p );
	check( $r->status === 400 && $r->data['error_code'] === 'missing_website', 'Assessment needs URL' );
	$p['website_url'] = 'https://example.test/';
	$r = call_intake( 'contact', $p );
	check( $r->data['ok'] === true, 'Empty assessment goal remains valid' );
	check( get_post_meta( 1, '_nexus_contact_source' ) === 'general_inquiry', 'Existing assessment source unchanged' );
	foreach ( $GLOBALS['intake_test']['mails'] as $mail ) check( strpos( $mail['subject'], hu_first_assessment_text( 'subject_prefix' ) ) === 0, 'Experiment prefix unchanged' );
} );
foreach ( array_merge( array_keys( hu_whitelabel_request_cases() ), [ 'unbekannt' ] ) as $case ) {
	run_case( "whitelabel case $case", static function () use ( $whitelabel, $case ) {
		$r        = call_intake( 'whitelabel', array_merge( $whitelabel, [ 'case' => $case ] ) );
		$expected = isset( hu_whitelabel_request_cases()[ $case ] ) ? $case : 'aufgabe';
		$texts    = hu_whitelabel_request_cases()[ $expected ];
		check( $r->status === 201 && $r->data['case'] === $expected, 'Unknown case falls back to aufgabe' );
		check( $r->data['message'] === sprintf( $texts['reply'], hu_response_promise( 'window' ) ), 'Reply follows case' );
		check( strpos( $GLOBALS['intake_test']['mails'][0]['subject'], $texts['label'] ) !== false, 'Internal subject names case' );
		check( strpos( $GLOBALS['intake_test']['mails'][1]['body'], esc_html( $texts['next'] ) ) !== false, 'Confirmation names next step' );
	} );
}
foreach ( [ 'missing_task' => [ 'task' => '' ], 'invalid_access' => [ 'access' => 'unknown' ] ] as $code => $changes ) {
	run_case( "whitelabel validation $code", static function () use ( $whitelabel, $code, $changes ) {
		$r = call_intake( 'whitelabel', array_merge( $whitelabel, $changes ) );
		check( $r->status === 400 && $r->data['error_code'] === $code && ! $GLOBALS['intake_test']['events'], 'White-Label validation before side effects' );
	} );
}
foreach ( [ 'unsupported_contract_version' => [ 'contract_version' => 'unknown' ], 'missing_consent_privacy' => [ 'consent_privacy' => '' ] ] as $code => $changes ) {
	run_case( "marketcheck validation $code", static function () use ( $marketcheck, $code, $changes ) {
		$r = call_intake( 'marketcheck', array_merge( $marketcheck, $changes ) );
		check( $r->status === 400 && $r->data['error_code'] === $code && ! $GLOBALS['intake_test']['events'], 'Marketcheck validation before side effects' );
	} );
}
run_case( 'marketcheck freemail is accepted and flagged', static function () use ( $marketcheck ) {
	$payload          = $marketcheck;
	$payload['email'] = 'fixture@gmx.de';
	$r                = call_intake( 'marketcheck', $payload );
	check( 201 === $r->status && true === $r->data['ok'], 'Freemail is accepted' );
	check( 1 === get_post_meta( 1, '_nexus_review_email_freemail' ), 'Freemail attribute persisted' );
	check( '' === get_post_meta( 1, '_nexus_review_domain' ), 'Provider is not stored as business domain' );
} );
$order_base = [
	'company' => 'TEST Fixture GmbH', 'email' => 'fixture@gmx.de', 'phone' => '0301234567',
	'request_sources' => [ 'aroundhome', 'eigene_website' ], 'consent_privacy' => 'accepted',
	'company_website' => '', 'contract_version' => nexus_get_review_request_contract_version(),
];
run_case( 'sofortkontakt order reaches audit endpoint and persists variant', static function () use ( $order_base ) {
	$payload = array_merge( $order_base, [
		'intake_variant' => 'sofortkontakt', 'audit_type' => 'sofortkontakt_setup',
		'lead_volume' => '20_50', 'lead_destination' => 'crm', 'crm_name' => 'TEST CRM',
		'callback_name' => 'TEST Fixture Person', 'callback_mobile' => '01761234567', 'desired_start' => 'diese_woche',
	] );
	$r = call_intake( 'marketcheck', $payload );
	check( 201 === $r->status && true === $r->data['ok'], 'Setup accepted' );
	check( 'sofortkontakt' === get_post_meta( 1, '_nexus_review_intake_variant' ), 'Setup variant persisted' );
	check( 'TEST CRM' === get_post_meta( 1, '_nexus_review_order_crm_name' ), 'Setup details persisted' );
	check( 1 === get_post_meta( 1, '_nexus_review_email_freemail' ), 'Freemail marked' );
	check( 2 === count( $GLOBALS['intake_test']['mails'] ), 'Internal and confirmation mail sent' );
	check( 'Angekommen. Ich melde mich ' . hu_response_promise( 'window' ) . '.' === $r->data['message'], 'Canonical response promise' );
} );
run_case( 'analyse order reaches audit endpoint and persists variant', static function () use ( $order_base ) {
	$payload = array_merge( $order_base, [
		'intake_variant' => 'analyse', 'audit_type' => 'anfragesystem_analyse',
		'page_url' => 'https://example.test/', 'analysis_question' => 'TEST Anfrageweg prüfen', 'crm_name' => 'TEST CRM',
	] );
	$r = call_intake( 'marketcheck', $payload );
	check( 201 === $r->status && true === $r->data['ok'], 'Analysis accepted' );
	check( 'analyse' === get_post_meta( 1, '_nexus_review_intake_variant' ), 'Analysis variant persisted' );
	check( 'TEST Anfrageweg prüfen' === get_post_meta( 1, '_nexus_review_order_analysis_question' ), 'Analysis question persisted' );
	check( 2 === count( $GLOBALS['intake_test']['mails'] ), 'Internal and confirmation mail sent' );
} );
run_case( 'order variants reject missing consent and invalid sources', static function () use ( $order_base ) {
	$payload = array_merge( $order_base, [ 'intake_variant' => 'analyse', 'page_url' => 'https://example.test/', 'request_sources' => [ 'unknown' ] ] );
	$r = call_intake( 'marketcheck', $payload );
	check( 400 === $r->status && 'invalid_request_sources' === $r->data['error_code'], 'Unknown source rejected' );
	$payload['request_sources'] = [ 'daa' ];
	$payload['consent_privacy'] = '';
	$r = call_intake( 'marketcheck', $payload );
	check( 400 === $r->status && 'missing_consent_privacy' === $r->data['error_code'], 'Consent required' );
	check( ! $GLOBALS['intake_test']['events'], 'Rejected order writes nothing' );
} );
run_case( 'blog stale nonce', static function () use ( $blog ) {
	$blog['nonce'] = 'expired';
	$r = call_intake( 'blog', $blog );
	check( $r->status === 400 && ! $GLOBALS['intake_test']['events'], 'Expired nonce retains current rejection contract' );
} );
run_case( 'contact consent, attribution, source and repeated activity', static function () use ( $contact ) {
	$p = $contact + [ 'landing_page_url' => 'https://example.test/kontakt/?utm_source=fixture', 'referrer_url' => 'https://referrer.test/path?private=1', 'ads_source' => 'fixture', 'referral_source' => 'empfehlung' ];
	call_intake( 'contact', $p );
	call_intake( 'contact', $p );
	check( count( $GLOBALS['intake_test']['posts'] ) === 1 && count( $GLOBALS['intake_test']['activities'] ) === 2, 'Email upsert is not request deduplication' );
	check( get_post_meta( 1, '_nexus_contact_consent_contact_request' ) === 1, 'Consent retained' );
	check( get_post_meta( 1, '_nexus_contact_source' ) === 'project_request', 'Source retained' );
	check( in_array( 'project_request', nexus_get_contact_segments( 1 ), true ), 'Segment retained' );
	check( get_post_meta( 1, '_nexus_contact_ads_source' ) === 'fixture', 'Attribution retained' );
	check( get_post_meta( 1, '_nexus_contact_referrer_url' ) === 'https://referrer.test/path', 'Referrer query removed' );
} );
foreach ( [ false, true ] as $storage_fail ) {
	run_case( "marketcheck mail failure, storage_fail=$storage_fail", static function () use ( $marketcheck, $storage_fail ) {
		intake_reset( [ 'storage_fail' => $storage_fail, 'internal_mail' => false, 'confirmation_mail' => false ] );
		$r = call_intake( 'marketcheck', $marketcheck );
		check( $r->data['ok'] === ! $storage_fail && $r->status === ( $storage_fail ? 500 : 201 ), 'Marketcheck success depends on request storage' );
		check( count( $GLOBALS['intake_test']['mails'] ) === ( $storage_fail ? 0 : 2 ), 'No mail before stored review' );
	} );
}
run_case( 'blog storage failure', static function () use ( $blog ) {
	intake_reset( [ 'storage_fail' => true ] );
	$r = call_intake( 'blog', $blog );
	check( $r->status === 500 && $r->data['ok'] === false && ! $GLOBALS['intake_test']['mails'], 'Failed DOI intent cannot succeed' );
} );
foreach ( [ false, true ] as $confirmation_mail ) {
	run_case( "blog DOI mail accepted=$confirmation_mail", static function () use ( $blog, $confirmation_mail ) {
		intake_reset( compact( 'confirmation_mail' ) );
		$r = call_intake( 'blog', $blog );
		check( $r->data['ok'] === $confirmation_mail && $r->status === ( $confirmation_mail ? 200 : 500 ), 'DOI mail must be accepted' );
		check( $GLOBALS['intake_test']['posts'][1]['post_type'] === 'nexus_blog_notify', 'Pending intent, no CRM contact before DOI' );
	} );
}
echo "Endpoint behavior passed; WP persistence and actual mail delivery are not exercised.\n";

run_case( 'website scope reaches actual endpoint, both mails and CRM', static function () use ( $contact ) {
	$p = array_merge( $contact, [ 'focus' => 'website', 'seiten' => '5', 'art' => 'relaunch', 'tracking' => '1', 'website_price' => 1, 'website_weeks' => 1, 'website_days' => 1, 'website_calc_version' => 'forged' ] );
	$r = call_intake( 'contact', $p );
	check( 201 === $r->status && true === $r->data['ok'], 'Product accepted' );
	$v = nexus_validate_contact_request_payload( $p );
	check( 3540 === $v['website_price'] && 10 === $v['website_days'] && 2 === $v['website_weeks'] && 'forged' !== $v['website_calc_version'], 'Price, working days and rule version are recalculated, tampering ignored' );
	check( 5 === get_post_meta( 1, '_nexus_contact_website_pages' ) && 'relaunch' === get_post_meta( 1, '_nexus_contact_website_kind' ), 'Structured CRM scope persisted' );
	check( 1 === get_post_meta( 1, '_nexus_contact_website_tracking' ), 'Tracking persisted' );
	check( str_contains( nexus_get_contact_request_activity_summary( $v ), '3.540' ), 'CRM activity contains calculated scope' );
	check( 2 === count( $GLOBALS['intake_test']['mails'] ), 'Both mails built' );
	foreach ( $GLOBALS['intake_test']['mails'] as $mail ) {
		check( str_contains( $mail['body'], '5 Seiten' ) && str_contains( $mail['body'], 'Relaunch' ) && str_contains( $mail['body'], '3.540' ), 'Mails carry the scope' );
	}
} );
run_case( 'website boundaries, invalid configuration and generic compatibility', static function () use ( $contact ) {
	foreach ( [ [1, 'neubau', 0, 1490, 3], [2, 'relaunch', 0, 1780, 6], [10, 'relaunch', 1, 4990, 15] ] as $case ) {
		$v = nexus_validate_contact_request_payload( array_merge( $contact, [ 'focus' => 'website', 'seiten' => (string) $case[0], 'art' => $case[1], 'tracking' => $case[2] ] ) );
		check( ! is_wp_error( $v ) && $case[3] === $v['website_price'] && $case[4] === $v['website_days'], 'Boundary price/time' );
	}
	foreach ( [ '0', '11', '-1', '1.5', '2oops', ['2'] ] as $pages ) {
		$r = call_intake( 'contact', array_merge( $contact, [ 'focus' => 'website', 'seiten' => $pages ] ) );
		check( 400 === $r->status && 'invalid_website_scope' === $r->data['error_code'], 'Invalid pages rejected' );
	}
	foreach ( [ ['art' => 'unknown'], ['tracking' => 'yes'], ['design' => 'unknown'], ['design' => [ 'neu' ] ], ['design' => 'basis', 'screendesign' => '1'], ['design' => 'neu', 'design_layouts' => '0'], ['design_layouts' => '4'], ['design_layouts' => [ '1' ] ] ] as $bad ) {
		$v = nexus_validate_contact_request_payload( array_merge( $contact, [ 'focus' => 'website', 'seiten' => '3' ], $bad ) );
		check( is_wp_error( $v ), 'Invalid kind/tracking rejected' );
	}
	check( ! is_wp_error( nexus_validate_contact_request_payload( $contact ) ), 'Other contact enquiries stay compatible' );
} );
