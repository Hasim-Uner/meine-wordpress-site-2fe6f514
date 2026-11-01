<?php
/** The reader introduction must never overwrite an editor's own excerpt. */
define( 'ABSPATH', __DIR__ );
function add_action( ...$args ) {}
function wp_installing() { return false; }
function wp_doing_ajax() { return false; }
function wp_doing_cron() { return false; }
function get_option( ...$args ) { return $GLOBALS['excerpt_test']['option']; }
function update_option( $key, $value, $autoload = null ) { $GLOBALS['excerpt_test']['option'] = $value; }
function hu_article_content_hygiene_find_post_id( $slug ) { return $GLOBALS['excerpt_test']['id']; }
function get_post_field( $field, $id ) { return $GLOBALS['excerpt_test']['excerpt']; }
function wp_slash( $data ) { return $data; }
function is_wp_error( $result ) { return false === $result; }
function wp_update_post( $data, $error = false ) {
	$GLOBALS['excerpt_test']['writes'][] = $data;
	return $GLOBALS['excerpt_test']['fail'] ? false : $data['ID'];
}
require __DIR__ . '/../../blocksy-child/inc/article-content-hygiene-landingpage.php';
function excerpt_check( $condition, $message ) {
	if ( ! $condition ) { throw new RuntimeException( $message ); }
	echo "PASS $message\n";
}
function excerpt_reset( $excerpt = '', $id = 14802, $fail = false ) {
	$GLOBALS['excerpt_test'] = [ 'id' => $id, 'excerpt' => $excerpt, 'fail' => $fail, 'option' => '', 'writes' => [] ];
}
excerpt_reset();
hu_maybe_set_landingpage_article_reader_excerpt();
excerpt_check( [ [ 'ID' => 14802, 'post_excerpt' => hu_landingpage_article_reader_excerpt() ] ] === $GLOBALS['excerpt_test']['writes'], 'Only ID and excerpt are written; article body and title remain editor-owned' );
hu_maybe_set_landingpage_article_reader_excerpt();
excerpt_check( 1 === count( $GLOBALS['excerpt_test']['writes'] ), 'Completed migration does not write again' );
excerpt_reset( 'Mein eigener redaktioneller Vorspann.' );
hu_maybe_set_landingpage_article_reader_excerpt();
excerpt_check( [] === $GLOBALS['excerpt_test']['writes'], 'An existing editorial excerpt is preserved' );
excerpt_reset( '', 0 );
hu_maybe_set_landingpage_article_reader_excerpt();
excerpt_check( [] === $GLOBALS['excerpt_test']['writes'] && '' === $GLOBALS['excerpt_test']['option'], 'Missing article can be retried later' );
excerpt_reset( '', 14802, true );
hu_maybe_set_landingpage_article_reader_excerpt();
excerpt_check( '' === $GLOBALS['excerpt_test']['option'], 'A failed database write remains retryable' );
echo "OK landingpage-excerpt\n";
