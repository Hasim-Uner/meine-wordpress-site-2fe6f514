<?php
/** Batch canonical quotes for browser/server parity checks. */
define( 'ABSPATH', __DIR__ );
require __DIR__ . '/../../blocksy-child/inc/canon/pricing-canon.php';
$cases = json_decode( stream_get_contents( STDIN ), true, 512, JSON_THROW_ON_ERROR );
echo json_encode( array_map( static function ( $case ) {
    return hu_website_quote( $case['seiten'], $case['art'], $case['tracking'], $case );
}, $cases ), JSON_THROW_ON_ERROR );
