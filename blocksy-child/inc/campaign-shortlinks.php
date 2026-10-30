<?php
/**
 * Saubere Kurzlinks fuer Akquise-Mails.
 *
 * Im Mailtext steht nur `hasimuener.de/partner/` bzw. `/zusammenarbeit/`.
 * Der Kurzlink leitet per 302 auf die Zielseite weiter und haengt dabei
 * utm_source und utm_campaign an. Das cookiefreie Protokoll in
 * inquiry-events.php zaehlt den Aufruf auf der Zielseite, das Whitelabel-
 * Formular uebernimmt die Parameter in die Anfrage.
 *
 * 302 statt 301: Browser cachen 301 dauerhaft; so bleibt das Ziel aenderbar.
 * Kein Cookie, kein Speicher, kein Protokoll an dieser Stelle.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Kurzlink => Ziel und Kampagnenparameter.
 *
 * @return array<string, array{target:string, utm_source:string, utm_campaign:string}>
 */
function hu_get_campaign_shortlinks() {
	return [
		'/partner/'        => [
			'target'       => '/whitelabel-retainer/',
			'utm_source'   => 'akquise',
			'utm_campaign' => 'ausschreibung',
		],
		'/zusammenarbeit/' => [
			'target'       => '/whitelabel-retainer/',
			'utm_source'   => 'akquise',
			'utm_campaign' => 'kaltakquise',
		],
	];
}

/**
 * Kurzlink erkennen und mit Kampagnenparametern weiterleiten.
 *
 * @return void
 */
function hu_handle_campaign_shortlink() {
	if ( is_admin() || ! in_array( $_SERVER['REQUEST_METHOD'] ?? '', [ 'GET', 'HEAD' ], true ) ) {
		return;
	}

	$path = (string) wp_parse_url( (string) ( $_SERVER['REQUEST_URI'] ?? '' ), PHP_URL_PATH );
	$path = '/' . trim( strtolower( $path ), '/' ) . '/';

	$links = hu_get_campaign_shortlinks();
	if ( ! isset( $links[ $path ] ) ) {
		return;
	}

	$link     = $links[ $path ];
	$location = add_query_arg(
		[
			'utm_source'   => $link['utm_source'],
			'utm_campaign' => $link['utm_campaign'],
		],
		home_url( $link['target'] )
	);

	if ( ! defined( 'DONOTCACHEPAGE' ) ) {
		define( 'DONOTCACHEPAGE', true );
	}
	nocache_headers();
	header( 'X-Robots-Tag: noindex', true );
	wp_safe_redirect( $location, 302, 'Campaign shortlink' );
	exit;
}
add_action( 'template_redirect', 'hu_handle_campaign_shortlink', -10 );
