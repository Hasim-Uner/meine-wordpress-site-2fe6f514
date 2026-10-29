<?php
/**
 * Serverseitiges Protokoll fuer Anfragen und Kampagnenaufrufe (`besuch`).
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
 * Kampagnenaufrufe zaehlen ohne Cookie jeden Aufruf, keine Personen.
 * Ein Fehler beim Schreiben bricht nie eine Anfrage oder einen Besuch ab.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Ereignisname fuer erfolgreich verarbeitete Anfragen. */
const HU_INQUIRY_EVENT_NAME = 'anfrage_gesendet';

/** Ereignisname fuer einen gefilterten Kampagnenaufruf. */
const HU_INQUIRY_VISIT_EVENT_NAME = 'besuch';

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
		'chat'              => 'KI-Assistent',
		'termin'            => 'Terminbuchung (Cal.com)',
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
 * Seitenpfad ohne Query-String und Fragment.
 *
 * Quelle ist der Referer des Fetch-Aufrufs, der auf der Formularseite
 * startet. Fehlt er, dient die uebermittelte Landing-Page. Fremde Hosts
 * werden verworfen. Bei Besuchen gilt ausschliesslich die aktuelle Request-URI,
 * nie die verweisende Seite oder ein vom Client gelieferter Host.
 *
 * @param array $payload         Roher Payload.
 * @param bool  $current_request Aktuellen Aufruf statt Formularseite lesen.
 * @return string
 */
function hu_inquiry_event_page( $payload, $current_request = false ) {
	$home_host  = (string) wp_parse_url( home_url( '/' ), PHP_URL_HOST );
	$candidates = [];

	if ( $current_request ) {
		$uri  = isset( $_SERVER['REQUEST_URI'] ) && is_string( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '/';
		$path = wp_parse_url( $uri, PHP_URL_PATH );
		// Nur den Host aus der eigenen Konfiguration verwenden, auch bei Host-Spoofing.
		$candidates[] = 'https://' . $home_host . '/' . ltrim( is_string( $path ) ? $path : '', '/' );
	} elseif ( isset( $_SERVER['HTTP_REFERER'] ) && is_string( $_SERVER['HTTP_REFERER'] ) ) {
		$candidates[] = wp_unslash( $_SERVER['HTTP_REFERER'] );
	}

	if ( ! $current_request && isset( $payload['landing_page_url'] ) && is_scalar( $payload['landing_page_url'] ) ) {
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
 * Eine erfolgreich verarbeitete Anfrage oder einen Kampagnenaufruf protokollieren.
 *
 * Gibt false zurueck, ohne etwas zu werfen, wenn das Formular unbekannt ist
 * oder das Schreiben scheitert: Das Protokoll darf keine Anfrage kosten.
 *
 * @param string $form    Schluessel aus hu_inquiry_event_forms(); leer fuer besuch.
 * @param array  $payload Roher Payload; gelesen werden nur ads_source,
 *                        utm_source, utm_campaign und landing_page_url.
 * @param string $event   anfrage_gesendet (Standard) oder besuch.
 * @return bool
 */
function hu_record_inquiry_event( $form, $payload = [], $event = HU_INQUIRY_EVENT_NAME ) {
	global $wpdb;

	if ( ! is_object( $wpdb ) || ! in_array( $event, [ HU_INQUIRY_EVENT_NAME, HU_INQUIRY_VISIT_EVENT_NAME ], true ) ) {
		return false;
	}

	$is_visit = HU_INQUIRY_VISIT_EVENT_NAME === $event;
	if ( $is_visit ? '' !== $form : ! isset( hu_inquiry_event_forms()[ $form ] ) ) {
		return false;
	}

	$payload = is_array( $payload ) ? $payload : [];

	// `ads_source` ist das Feld, in das das Kontaktformular utm_source schreibt.
	$source = hu_inquiry_event_token( $payload['utm_source'] ?? '', 64 );
	if ( '' === $source ) {
		$source = hu_inquiry_event_token( $payload['ads_source'] ?? '', 64 );
	}

	// Der Anfrage-Monitor schreibt seine Testanfragen als `monitor_test`;
	// die Anfrageauswertungen lesen nur `anfrage_gesendet`, nie Testanfragen.
	if ( ! $is_visit && function_exists( 'hu_monitor_inquiry_event_name' ) ) {
		$event = hu_monitor_inquiry_event_name( $event );
	}

	try {
		if ( ! hu_inquiry_events_install() ) {
			return false;
		}

		$written = $wpdb->insert(
			hu_inquiry_events_table(),
			[
				'event'        => $event,
				'form'         => $form,
				'seite'        => hu_inquiry_event_page( $payload, $is_visit ),
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
 * Kampagnenaufrufe ohne Browser-Request, Cookie oder Besucherkennung zaehlen.
 *
 * Der User-Agent wird nur fluechtig geprueft und nie gespeichert. Die Filter
 * reduzieren Scanner, koennen menschenaehnliche Scanner aber nicht erkennen.
 * Ein vorgeschalteter Seitencache muss UTM-Aufrufe bis zu WordPress durchlassen.
 *
 * @return void
 */
function hu_record_acquisition_visit() {
	if ( 'GET' !== ( $_SERVER['REQUEST_METHOD'] ?? '' ) || is_admin() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || is_feed() || is_preview() || current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( ! isset( $_GET['utm_source'] ) ) {
		return;
	}

	$source = hu_inquiry_event_token( wp_unslash( $_GET['utm_source'] ), 64 );
	if ( '' === $source ) {
		return;
	}

	// Schuetzt Antworten, die PHP erreichen; ersetzt keine Ausnahme im Host-Cache.
	// Auch ein Scanner darf keine cachebare Antwort fuer spaetere Besucher fuellen.
	if ( ! defined( 'DONOTCACHEPAGE' ) ) {
		define( 'DONOTCACHEPAGE', true );
	}
	nocache_headers();

	$accept = isset( $_SERVER['HTTP_ACCEPT'] ) && is_string( $_SERVER['HTTP_ACCEPT'] ) ? $_SERVER['HTTP_ACCEPT'] : '';
	$agent  = isset( $_SERVER['HTTP_USER_AGENT'] ) && is_string( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '';
	if ( ! preg_match( '~(?:^|,)\s*text/html(?:\s*;[^,]*)?(?:,|$)~i', $accept ) || preg_match( '/bot|crawl|spider|preview|fetch|scan|monitor|curl|python|headless|googleimageproxy|safelinks/i', $agent ) ) {
		return;
	}

	hu_record_inquiry_event(
		'',
		[
			'utm_source'   => $source,
			'utm_campaign' => isset( $_GET['utm_campaign'] ) ? wp_unslash( $_GET['utm_campaign'] ) : '',
		],
		HU_INQUIRY_VISIT_EVENT_NAME
	);
}
add_action( 'template_redirect', 'hu_record_acquisition_visit' );

/**
 * Kampagnenauswertung im gleichen Zeitfenster wie die Formularauswertung.
 *
 * @param int $days Zeitfenster in Tagen.
 * @return array<int, array{utm_source:string,utm_campaign:string,visits:int,inquiries:int}>
 */
function hu_inquiry_events_campaign_summary( $days = HU_INQUIRY_EVENTS_WINDOW_DAYS ) {
	global $wpdb;

	if ( ! is_object( $wpdb ) || ! hu_inquiry_events_install() ) {
		return [];
	}

	$table = hu_inquiry_events_table();
	$rows  = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT utm_source, utm_campaign, SUM(CASE WHEN event = %s THEN 1 ELSE 0 END) AS visits, SUM(CASE WHEN event = %s THEN 1 ELSE 0 END) AS inquiries FROM {$table} WHERE event IN (%s, %s) AND created_at >= %s GROUP BY utm_source, utm_campaign ORDER BY visits DESC, inquiries DESC, utm_source ASC, utm_campaign ASC",
			HU_INQUIRY_VISIT_EVENT_NAME,
			HU_INQUIRY_EVENT_NAME,
			HU_INQUIRY_VISIT_EVENT_NAME,
			HU_INQUIRY_EVENT_NAME,
			gmdate( 'Y-m-d H:i:s', time() - (int) $days * DAY_IN_SECONDS )
		),
		'ARRAY_A'
	);

	return array_map(
		static function ( $row ) {
			return [
				'utm_source'   => (string) $row['utm_source'],
				'utm_campaign' => (string) $row['utm_campaign'],
				'visits'       => (int) $row['visits'],
				'inquiries'    => (int) $row['inquiries'],
			];
		},
		(array) $rows
	);
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
 * Admin-Seite: Kampagnenaufrufe und Anfragen der letzten 90 Tage.
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
	echo '<p>' . esc_html( sprintf( 'Kampagnenaufrufe und erfolgreich abgesendete Anfragen der letzten %d Tage, serverseitig gezählt. Gespeichert werden nur Ereignis, Formular, Seitenpfad, utm_source, utm_campaign und Zeitpunkt – keine IP, keine Personendaten.', HU_INQUIRY_EVENTS_WINDOW_DAYS ) ) . '</p>';
	echo '<h2>Nach Quelle und Kampagne</h2>';
	echo '<p>Besuche zählen gefilterte Aufrufe mit utm_source, keine Personen oder eindeutigen Klicks. Ohne Cookie gibt es keine Deduplizierung; erneutes Laden zählt erneut. Scanner werden gefiltert, lassen sich aber nicht vollständig ausschließen. Anfragen/Besuche ist ein Verhältnis im selben Zeitraum, keine Zuordnung zu einzelnen Besuchern; bei 0 Besuchen ist es nicht berechenbar.</p>';

	$campaigns = hu_inquiry_events_campaign_summary();
	if ( empty( $campaigns ) ) {
		echo '<p>Noch keine Kampagnenereignisse im Zeitraum.</p>';
	} else {
		echo '<table class="widefat striped" style="max-width:960px"><thead><tr><th>Quelle</th><th>Kampagne</th><th style="text-align:right">Besuche</th><th style="text-align:right">Anfragen</th><th style="text-align:right">Anfragen/Besuche</th></tr></thead><tbody>';
		foreach ( $campaigns as $campaign ) {
			$ratio = $campaign['visits'] > 0 ? number_format_i18n( 100 * $campaign['inquiries'] / $campaign['visits'], 1 ) . ' %' : '–';
			echo '<tr><td>' . esc_html( '' !== $campaign['utm_source'] ? $campaign['utm_source'] : 'ohne utm_source' ) . '</td><td>' . esc_html( '' !== $campaign['utm_campaign'] ? $campaign['utm_campaign'] : 'ohne utm_campaign' ) . '</td><td style="text-align:right">' . esc_html( (string) $campaign['visits'] ) . '</td><td style="text-align:right">' . esc_html( (string) $campaign['inquiries'] ) . '</td><td style="text-align:right">' . esc_html( $ratio ) . '</td></tr>';
		}
		echo '</tbody></table>';
	}
	echo '<h2>Nach Formular</h2>';

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
