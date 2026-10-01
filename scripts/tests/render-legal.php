<?php
/** Render the actual legal templates with the existing WordPress boundary. */
require __DIR__ . '/navigation-harness.php';

function have_posts() { return empty( $GLOBALS['legal_test_post_read'] ); }
function the_post() { $GLOBALS['legal_test_post_read'] = true; }

$slug = $argv[1] ?? 'impressum';
if ( ! in_array( $slug, [ 'impressum', 'datenschutz' ], true ) ) {
	exit( 1 );
}
nav_test_use_context( 'imprint' );
require get_stylesheet_directory() . '/page-' . $slug . '.php';
