<?php
/**
 * Anfrage-Ereignisse: serverseitiges Protokoll `anfrage_gesendet`.
 *
 * Eine Zeile je erfolgreich verarbeiteter Anfrage, geschrieben von den drei
 * REST-Handlern (`contact-request`, `whitelabel-request`, `audit-request`)
 * nach CRM-Schreibvorgang und Benachrichtigung. Der Browser sendet dafuer
 * nichts: Die frueheren Absende-Events im Browser (`dataLayer`, `_paq`)
 * erreichten keinen Empfaenger, weil weder GTM noch Matomo geladen werden.
 *
 * Gespeichert wird genau: Ereignis, Formular, Seitenpfad, utm_source,
 * utm_campaign, Zeitpunkt (UTC). Keine IP, kein User-Agent, keine E-Mail,
 * kein Name, keine Nachricht, kein Query-String, kein Cookie. Deshalb braucht
 * dieses Protokoll weder Banner noch Einwilligung (docs/architecture/PRIVACY.md).
 *
 * Eine Zeile je Anfrage; Rohdaten statt Tageszaehler, damit "Formular x
 * Quelle" ohne weitere Auswertung lesbar ist. Ein Fehler beim Schreiben
 * bricht nie eine Anfrage ab.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Name des einzigen Ereignisses dieses Protokolls. */
const HU_INQUIRY_EVENT_NAME = 'anfrage_gesendet';

/** Zeitfenster der Admin-Uebersicht in Tagen. */
const HU_INQUIRY_EVENTS_WINDOW_DAYS = 90;

/**
 * Formulare, die das Protokoll kennt, in Anzeigereihenfolge.
 *
 * `analyse` steht zusaetzlich zu den sechs Formularen aus dem Auftrag: Die
 * Solar-Seite hat drei Tueren am Endpunkt `audit-request`, und die Analyse
 * (Anfragesystem-Analyse) waere sonst unter `marktcheck` verschwunden.
 *
 * @return array<string, string> Schluessel => Anzeigename.
 */
function hu_inquiry_event_forms() {
	return [
		'kontakt'           => 'Kontaktformular',
		'ersteinschaetzung' => 'Ersteinschätzung',
		'whitelabel'        => 'White-Label',
		'marktcheck'        => 'Marktcheck (Solar)',
		'analyse'           => 'Analyse (Solar)',
		'sofortkontakt'     => 'Sofortkontakt (Solar)',
		'sst'               => 'Server-Side-Tracking',
	];
}

/**
 * Formular einer Kontaktanfrage.
 *
 * Die Ersteinschaetzung erkennt der Server am Anfragetyp. Die Seite
 * /server-side-tracking-b2b/ nutzt denselben Endpunkt mit Typ `project` und
 * Thema `tracking`, das auch das Kontaktformular anbietet; deshalb sendet sie
 * das versteckte Feld `form_origin=sst`.
 *
 * @param array $validated Validierter Payload.
 * @param array $payload   Roher Payload.
 * @return string
 */
function hu_inquiry_event_form_for_contact( $validated, $payload ) {
	if ( function_exists( 'nexus_is_first_assessment_request' ) && nexus_is_first_assessment_request( $validated['request_type'] ?? '' ) ) {
		return 'ersteinschaetzung';
	}

	$origin = isset( $payload['form_origin'] ) && is_scalar( $payload['form_origin'] ) ? sanitize_key( (string) $payload['form_origin'] ) : '';

	return 'sst' === $origin ? 'sst' : 'kontakt';
}

/**
 * Formular einer Anfrage am Endpunkt `audit-request`.
 *
 * @param array $validated Validierter Payload.
 * @return string
 */
function hu_inquiry_event_form_for_audit( $validated ) {
	$variant = isset( $validated['intake_variant'] ) ? (string) $validated['intake_variant'] : '';

	if ( 'sofortkontakt' === $variant || 'analyse' === $variant ) {
		return $variant;
	}

	return 'marktcheck';
}

/**
 * Kampagnenwert auf ein kurzes, sicheres Token reduzieren.
 *
 * Nur Kleinbuchstaben, Ziffern, Punkt, Unterstrich und Bindestrich: Aus der
 * URL kommen Kampagnennamen, keine Freitexte.
 *
 * @param mixed $value  Rohwert.
 * @param int   $length Maximale Laenge.
 * @return string
 */
function hu_inquiry_event_token( $value, $length ) {
	if ( ! is_scalar( $value ) ) {
		return '';
	}

	$value = strtolower( trim( (string) $value ) );
	$value = (string) preg_replace( '/\s+/', '-', $value );
	$value = (string) preg_replace( '/[^a-z0-9._-]/', '', $value );

	return substr( $value, 0, $length );
}

/**
 * Seitenpfad der absendenden Seite, ohne Query-String und Fragment.
 *
 * Quelle ist der Referer des Fetch-Aufrufs, der auf der Formularseite
 * startet. Fehlt er, dient die uebermittelte Landing-Page. Fremde Hosts
 * werden verworfen.
 *
 * @param array $payload Roher Payload.
 * @return string
 */
function hu_inquiry_event_page( $payload ) {
	$home_host  = (string) wp_parse_url( home_url( '/' ), PHP_URL_HOST );
	$candidates = [];

	if ( isset( $_SERVER['HTTP_REFERER'] ) && is_string( $_SERVER['HTTP_REFERER'] ) ) {
		$candidates[] = wp_unslash( $_SERVER['HTTP_REFERER'] );
	}

	if ( isset( $payload['landing_page_url'] ) && is_scalar( $payload['landing_page_url'] ) ) {
		$candidates[] = (string) $payload['landing_page_url'];
	}

	foreach ( $candidates as $candidate ) {
		$parts = wp_parse_url( trim( $candidate ) );
		$host  = is_array( $parts ) && isset( $parts['host'] ) ? strtolower( (string) $parts['host'] ) : '';

		if ( '' === $host || strtolower( $home_host ) !== $host ) {
			continue;
		}

		$path = isset( $parts['path'] ) ? (string) $parts['path'] : '/';
		$path = (string) preg_replace( '#[^A-Za-z0-9/_.~%-]#', '', $path );

		return substr( '' !== $path ? $path : '/', 0, 120 );
	}

	return '';
}

/**
 * Name der Protokolltabelle.
 *
 * @return string
 */
function hu_inquiry_events_table() {
	global $wpdb;

	return $wpdb->prefix . 'hu_inquiry_events';
}

/**
 * Tabelle beim ersten Gebrauch anlegen; ohne Theme-Reaktivierung deploybar.
 *
 * @return bool
 */
function hu_inquiry_events_install() {
	global $wpdb;

	if ( '1' === get_option( 'hu_inquiry_events_schema' ) ) {
		return true;
	}

	$table = hu_inquiry_events_table();
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta(
		"CREATE TABLE {$table} (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		event varchar(32) NOT NULL,
		form varchar(24) NOT NULL,
		seite varchar(120) NOT NULL DEFAULT '',
		utm_source varchar(64) NOT NULL DEFAULT '',
		utm_campaign varchar(96) NOT NULL DEFAULT '',
		created_at datetime NOT NULL,
		PRIMARY KEY  (id),
		KEY created_form (created_at,form)
	) " . $wpdb->get_charset_collate() . ';'
	);

	if ( $table !== $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table ) ) ) ) {
		return false;
	}

	update_option( 'hu_inquiry_events_schema', '1', false );

	return true;
}

/**
 * Eine erfolgreich verarbeitete Anfrage protokollieren.
 *
 * Gibt false zurueck, ohne etwas zu werfen, wenn das Formular unbekannt ist
 * oder das Schreiben scheitert: Das Protokoll darf keine Anfrage kosten.
 *
 * @param string $form    Schluessel aus hu_inquiry_event_forms().
 * @param array  $payload Roher Payload; gelesen werden nur ads_source,
 *                        utm_source, utm_campaign und landing_page_url.
 * @return bool
 */
function hu_record_inquiry_event( $form, $payload = [] ) {
	global $wpdb;

	if ( ! isset( hu_inquiry_event_forms()[ $form ] ) || ! is_object( $wpdb ) ) {
		return false;
	}

	$payload = is_array( $payload ) ? $payload : [];

	// `ads_source` ist das Feld, in das das Kontaktformular utm_source schreibt.
	$source = hu_inquiry_event_token( $payload['utm_source'] ?? '', 64 );
	if ( '' === $source ) {
		$source = hu_inquiry_event_token( $payload['ads_source'] ?? '', 64 );
	}

	try {
		if ( ! hu_inquiry_events_install() ) {
			return false;
		}

		$written = $wpdb->insert(
			hu_inquiry_events_table(),
			[
				'event'        => HU_INQUIRY_EVENT_NAME,
				'form'         => $form,
				'seite'        => hu_inquiry_event_page( $payload ),
				'utm_source'   => $source,
				'utm_campaign' => hu_inquiry_event_token( $payload['utm_campaign'] ?? '', 96 ),
				'created_at'   => gmdate( 'Y-m-d H:i:s' ),
			],
			[ '%s', '%s', '%s', '%s', '%s', '%s' ]
		);
	} catch ( \Throwable $error ) {
		return false;
	}

	return false !== $written;
}

/**
 * Anzahl je Formular und utm_source im Zeitfenster.
 *
 * @param int $days Zeitfenster in Tagen.
 * @return array<string, array{label:string,total:int,sources:array<string,int>}> In Anzeigereihenfolge, nur Formulare mit Eintraegen.
 */
function hu_inquiry_events_summary( $days = HU_INQUIRY_EVENTS_WINDOW_DAYS ) {
	global $wpdb;

	if ( ! is_object( $wpdb ) || ! hu_inquiry_events_install() ) {
		return [];
	}

	$table = hu_inquiry_events_table();
	$rows  = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT form, utm_source, COUNT(*) AS total FROM {$table} WHERE event = %s AND created_at >= %s GROUP BY form, utm_source",
			HU_INQUIRY_EVENT_NAME,
			gmdate( 'Y-m-d H:i:s', time() - (int) $days * DAY_IN_SECONDS )
		),
		'ARRAY_A'
	);

	$summary = [];
	foreach ( hu_inquiry_event_forms() as $form => $label ) {
		foreach ( (array) $rows as $row ) {
			if ( $form !== $row['form'] ) {
				continue;
			}

			$count = (int) $row['total'];

			if ( ! isset( $summary[ $form ] ) ) {
				$summary[ $form ] = [ 'label' => $label, 'total' => 0, 'sources' => [] ];
			}

			$summary[ $form ]['total']                       += $count;
			$summary[ $form ]['sources'][ (string) $row['utm_source'] ] = $count;
		}

		if ( isset( $summary[ $form ] ) ) {
			arsort( $summary[ $form ]['sources'] );
		}
	}

	return $summary;
}

/**
 * Admin-Seite unter dem CRM-Menue registrieren.
 *
 * @return void
 */
function hu_register_inquiry_events_admin_page() {
	if ( ! function_exists( 'nexus_get_crm_menu_slug' ) ) {
		return;
	}

	add_submenu_page(
		nexus_get_crm_menu_slug(),
		'Anfrage-Eingänge',
		'Anfrage-Eingänge',
		'manage_options',
		'hu-inquiry-events',
		'hu_render_inquiry_events_admin_page'
	);
}
add_action( 'admin_menu', 'hu_register_inquiry_events_admin_page', 31 );

/**
 * Admin-Seite: Eingaenge der letzten 90 Tage je Formular und utm_source.
 *
 * @return void
 */
function hu_render_inquiry_events_admin_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$summary = hu_inquiry_events_summary();
	$total   = array_sum( array_column( $summary, 'total' ) );

	echo '<div class="wrap"><h1>Anfrage-Eingänge</h1>';
	echo '<p>' . esc_html( sprintf( 'Erfolgreich abgesendete Anfragen der letzten %d Tage, serverseitig gezählt. Gespeichert werden nur Formular, Seitenpfad, utm_source, utm_campaign und Zeitpunkt – keine IP, keine Personendaten.', HU_INQUIRY_EVENTS_WINDOW_DAYS ) ) . '</p>';

	if ( empty( $summary ) ) {
		echo '<p><strong>Noch keine Einträge im Zeitraum.</strong></p></div>';
		return;
	}

	echo '<table class="widefat striped" style="max-width:640px"><thead><tr><th>Formular</th><th>utm_source</th><th style="text-align:right">Anfragen</th></tr></thead><tbody>';

	foreach ( $summary as $entry ) {
		echo '<tr><td><strong>' . esc_html( $entry['label'] ) . '</strong></td><td></td><td style="text-align:right"><strong>' . esc_html( (string) $entry['total'] ) . '</strong></td></tr>';

		foreach ( $entry['sources'] as $source => $count ) {
			echo '<tr><td></td><td>' . esc_html( '' !== $source ? $source : 'ohne utm_source' ) . '</td><td style="text-align:right">' . esc_html( (string) $count ) . '</td></tr>';
		}
	}

	echo '<tr><td><strong>Gesamt</strong></td><td></td><td style="text-align:right"><strong>' . esc_html( (string) $total ) . '</strong></td></tr>';
	echo '</tbody></table></div>';
}
