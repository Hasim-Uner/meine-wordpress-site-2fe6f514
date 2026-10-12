<?php
/**
 * Spam-Schutz fuer die oeffentlichen Formular-Endpunkte.
 *
 * Ergaenzt Honeypot und IP-Rate-Limit der Handler um zwei Pruefungen, die
 * vor jedem CRM-Schreibvorgang laufen:
 *
 * 1. Zeitsperre: nexus-core.js schickt `form_elapsed_ms` mit (Millisekunden
 *    seit Seitenaufruf). Kommt eine Absendung schneller als
 *    NEXUS_SPAM_MIN_ELAPSED_MS, war es kein Mensch. Fehlt das Feld (Bot
 *    postet direkt an den Endpunkt oder alte, gecachte Seite), zaehlt das nur
 *    als Beobachtung `no_timing` und blockt nicht – ausser die Konstante
 *    NEXUS_SPAM_REQUIRE_TIMING ist true. Umschalten erst, wenn die Zaehler
 *    zeigen, dass echte Anfragen das Feld immer mitbringen.
 * 2. Inhaltsfilter auf den Freitextfeldern: kyrillische oder ostasiatische
 *    Schrift, mehr als ein Link, Link-Markup oder Link im Namen, eindeutige
 *    Spam-Begriffe (Casino, Porno …).
 *
 * Verworfen wird still: Der Handler antwortet wie beim Honeypot mit Erfolg,
 * damit der Absender keinen Treffer erkennt. Gezaehlt wird nur Tag,
 * Endpunkt und Grund – keine IP, keine Personendaten, kein Nachrichtentext.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'NEXUS_SPAM_MIN_ELAPSED_MS' ) ) {
	define( 'NEXUS_SPAM_MIN_ELAPSED_MS', 3000 );
}

if ( ! defined( 'NEXUS_SPAM_REQUIRE_TIMING' ) ) {
	define( 'NEXUS_SPAM_REQUIRE_TIMING', false );
}

/** Option mit den Tageszaehlern. */
const NEXUS_SPAM_STATS_OPTION = 'nexus_intake_spam_stats';

/** Aufbewahrung der Tageszaehler in Tagen. */
const NEXUS_SPAM_STATS_DAYS = 30;

/**
 * Freitextfelder, die der Inhaltsfilter liest. Auswahlwerte, URLs der
 * Attribution und Website-Felder bleiben bewusst draussen.
 *
 * @return string[]
 */
function nexus_intake_spam_text_fields() {
	return [ 'name', 'company', 'position', 'message', 'task', 'tracking_setup', 'analysis_question', 'current_challenge', 'extra_context' ];
}

/**
 * Begriffe, die in echten Anfragen an diese Seite nicht vorkommen.
 *
 * @return string[]
 */
function nexus_intake_spam_keywords() {
	$keywords = [ 'casino', 'kasino', 'porn', 'porno', 'xxx', 'onlyfans', 'escort', 'viagra', 'cialis', 'betting', 'gambling', 'glücksspiel', 'sportwetten' ];

	return (array) apply_filters( 'nexus_intake_spam_keywords', $keywords );
}

/**
 * Grund, warum eine Absendung Spam ist – oder leer, wenn sie durchgeht.
 *
 * @param array $payload Rohes Formular-Payload.
 * @return string Grund (`too_fast`, `no_timing`, `script`, `links`, `keyword`) oder ''.
 */
function nexus_intake_spam_reason( $payload ) {
	if ( ! is_array( $payload ) ) {
		return '';
	}

	$elapsed = $payload['form_elapsed_ms'] ?? null;

	if ( null !== $elapsed && '' !== $elapsed && is_numeric( $elapsed ) && (int) $elapsed < NEXUS_SPAM_MIN_ELAPSED_MS ) {
		return 'too_fast';
	}

	$texts = [];
	foreach ( nexus_intake_spam_text_fields() as $field ) {
		if ( isset( $payload[ $field ] ) && is_scalar( $payload[ $field ] ) ) {
			$texts[ $field ] = (string) $payload[ $field ];
		}
	}

	$joined = implode( "\n", $texts );

	if ( '' !== $joined ) {
		if ( preg_match( '/[\p{Cyrillic}\p{Han}\p{Hiragana}\p{Katakana}\p{Hangul}]/u', $joined ) ) {
			return 'script';
		}

		$link_count = preg_match_all( '~(?:https?://|www\.)~i', $joined );
		$name_link  = isset( $texts['name'] ) && preg_match( '~(?:https?://|www\.|\.(?:com|ru|xyz|top|online|site)\b)~i', $texts['name'] );

		if ( $link_count > 1 || $name_link || preg_match( '~\[url|<a\s~i', $joined ) ) {
			return 'links';
		}
	}

	$haystack = mb_strtolower( $joined . "\n" . ( isset( $payload['email'] ) && is_scalar( $payload['email'] ) ? (string) $payload['email'] : '' ) );

	foreach ( nexus_intake_spam_keywords() as $keyword ) {
		$keyword = mb_strtolower( (string) $keyword );

		if ( '' !== $keyword && preg_match( '/(?<![\p{L}\p{N}])' . preg_quote( $keyword, '/' ) . '/u', $haystack ) ) {
			return 'keyword';
		}
	}

	if ( null === $elapsed || '' === $elapsed ) {
		return NEXUS_SPAM_REQUIRE_TIMING ? 'no_timing' : '';
	}

	return '';
}

/**
 * Prueft eine Absendung und zaehlt das Ergebnis.
 *
 * @param array  $payload  Rohes Formular-Payload.
 * @param string $endpoint Kurzname des Endpunkts (contact, whitelabel, audit, analysis, blog).
 * @return bool true = verwerfen.
 */
function nexus_intake_is_spam( $payload, $endpoint ) {
	$reason = nexus_intake_spam_reason( $payload );

	if ( '' !== $reason ) {
		nexus_intake_spam_count( $endpoint, $reason );
		return true;
	}

	// Beobachtung fuer die Umschaltentscheidung: Absendungen ohne Zeitfeld,
	// die trotzdem durchgehen.
	if ( is_array( $payload ) && ( ! isset( $payload['form_elapsed_ms'] ) || '' === $payload['form_elapsed_ms'] ) ) {
		nexus_intake_spam_count( $endpoint, 'no_timing_passed' );
	}

	return false;
}

/**
 * Tageszaehler erhoehen. Ein Fehler hier bricht nie eine Anfrage ab.
 *
 * @param string $endpoint Endpunkt.
 * @param string $reason   Grund.
 * @return void
 */
function nexus_intake_spam_count( $endpoint, $reason ) {
	$stats = get_option( NEXUS_SPAM_STATS_OPTION, [] );
	$stats = is_array( $stats ) ? $stats : [];
	$day   = gmdate( 'Y-m-d' );
	$key   = sanitize_key( (string) $endpoint ) . ':' . sanitize_key( (string) $reason );

	$stats[ $day ][ $key ] = (int) ( $stats[ $day ][ $key ] ?? 0 ) + 1;

	$cutoff = gmdate( 'Y-m-d', time() - NEXUS_SPAM_STATS_DAYS * DAY_IN_SECONDS );
	foreach ( array_keys( $stats ) as $stored_day ) {
		if ( $stored_day < $cutoff ) {
			unset( $stats[ $stored_day ] );
		}
	}

	update_option( NEXUS_SPAM_STATS_OPTION, $stats, false );
}

/**
 * Admin-Seite unter dem CRM-Menue registrieren.
 *
 * @return void
 */
function nexus_register_intake_spam_admin_page() {
	if ( ! function_exists( 'nexus_get_crm_menu_slug' ) ) {
		return;
	}

	add_submenu_page(
		nexus_get_crm_menu_slug(),
		'Formular-Spam',
		'Formular-Spam',
		'manage_options',
		'nexus-intake-spam',
		'nexus_render_intake_spam_admin_page'
	);
}
add_action( 'admin_menu', 'nexus_register_intake_spam_admin_page', 32 );

/**
 * Admin-Seite: verworfene Absendungen der letzten 30 Tage je Endpunkt und Grund.
 *
 * @return void
 */
function nexus_render_intake_spam_admin_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$labels = [
		'too_fast'         => 'Zu schnell abgesendet',
		'no_timing'        => 'Ohne Zeitfeld (blockiert)',
		'script'           => 'Kyrillische/ostasiatische Schrift',
		'links'            => 'Links im Text',
		'keyword'          => 'Spam-Begriff',
		'no_timing_passed' => 'Ohne Zeitfeld, durchgelassen (nur Beobachtung)',
	];

	$stats  = get_option( NEXUS_SPAM_STATS_OPTION, [] );
	$totals = [];

	foreach ( is_array( $stats ) ? $stats : [] as $day_counts ) {
		foreach ( (array) $day_counts as $key => $count ) {
			$totals[ $key ] = ( $totals[ $key ] ?? 0 ) + (int) $count;
		}
	}

	ksort( $totals );

	echo '<div class="wrap"><h1>Formular-Spam</h1>';
	echo '<p>' . esc_html( sprintf( 'Verworfene Absendungen der letzten %d Tage. Honeypot-Treffer laufen vorher ab und sind hier nicht enthalten. Gespeichert werden nur Tag, Formular und Grund.', NEXUS_SPAM_STATS_DAYS ) ) . '</p>';
	echo '<p>' . esc_html( 'Zeitfeld-Pflicht: ' . ( NEXUS_SPAM_REQUIRE_TIMING ? 'an' : 'aus (nur Beobachtung)' ) ) . '</p>';

	if ( empty( $totals ) ) {
		echo '<p><strong>Noch keine Einträge im Zeitraum.</strong></p></div>';
		return;
	}

	echo '<table class="widefat striped" style="max-width:640px"><thead><tr><th>Formular</th><th>Grund</th><th style="text-align:right">Anzahl</th></tr></thead><tbody>';

	foreach ( $totals as $key => $count ) {
		list( $endpoint, $reason ) = array_pad( explode( ':', (string) $key, 2 ), 2, '' );
		echo '<tr><td>' . esc_html( $endpoint ) . '</td><td>' . esc_html( $labels[ $reason ] ?? $reason ) . '</td><td style="text-align:right">' . esc_html( (string) $count ) . '</td></tr>';
	}

	echo '</tbody></table></div>';
}
