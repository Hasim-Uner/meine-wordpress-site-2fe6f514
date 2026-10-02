<?php
/** Real intake/CRM/mail builders; isolated storage and fixture recipients only. */
require __DIR__ . '/intake-wordpress-doubles.php';
foreach ( [ 'canon/messaging-canon', 'canon/pricing-canon', 'crm', 'contact-page' ] as $module ) {
    require __DIR__ . '/../../blocksy-child/inc/' . $module . '.php';
}
intake_reset();
$payload = json_decode( stream_get_contents( STDIN ), true, 512, JSON_THROW_ON_ERROR );
$result = nexus_handle_contact_request_submission( new WP_REST_Request( $payload ) );
echo json_encode( [ 'status' => $result->status, 'data' => $result->data, 'meta' => $GLOBALS['intake_test']['meta'], 'mails' => $GLOBALS['intake_test']['mails'] ], JSON_THROW_ON_ERROR );
