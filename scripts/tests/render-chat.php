<?php
/** Render the production facade without WordPress or any real API credentials. */
define( 'ABSPATH', __DIR__ );
function add_action( ...$args ) {}
function is_admin() { return false; }
function is_customize_preview() { return false; }
function is_feed() { return false; }
function is_embed() { return false; }
function wp_doing_ajax() { return false; }
function is_page( $pages ) { return false; }
function is_user_logged_in() { return false; }
function hu_chat_visitor_allowed() { return true; }
function hu_chat_config() { return [ 'mode' => 'preview' ]; }
function hu_chat_normalize_page( $path ) { return $path; }
function hu_chat_booking_urls() { return [ 'general' => 'https://cal.com/example/project', 'agency' => 'https://cal.com/example/agency' ]; }
function hu_get_contact_email() { return 'test@example.invalid'; }
function home_url( $path = '' ) { return 'https://hasimuener.de' . $path; }
function rest_url( $path ) { return home_url( '/wp-json/' . $path ); }
function get_stylesheet_directory() { return dirname( __DIR__, 2 ) . '/blocksy-child'; }
function get_stylesheet_directory_uri() { return home_url( '/theme' ); }
function add_query_arg( $key, $value, $url ) { return $url . '?' . urlencode( $key ) . '=' . urlencode( $value ); }
function wp_parse_url( $url, $component ) { return parse_url( $url, $component ); }
function wp_json_encode( $value, $flags = 0 ) { return json_encode( $value, $flags ); }
function esc_attr( $value ) { return htmlspecialchars( $value, ENT_QUOTES, 'UTF-8' ); }
$_SERVER['REQUEST_URI'] = '/';
require __DIR__ . '/../../blocksy-child/inc/chat-assistant/facade.php';
hu_chat_render_facade();
