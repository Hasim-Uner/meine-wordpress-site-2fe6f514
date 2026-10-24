<?php
/**
 * Anfrage-Monitor: prueft taeglich, ob Anfragen ueber diese Website ankommen.
 *
 * Ein geplanter GitHub-Actions-Lauf (scripts/monitor/anfrage_monitor.py)
 * sendet einmal am Tag eine echte Testanfrage ueber das Kontaktformular im
 * Browser, prueft das Postfach und meldet das Ergebnis an
 * `POST /nexus/v1/monitor/result`. Die Testanfrage durchlaeuft den echten Weg
 * (CRM, interne Mail, Protokoll, Bestaetigung), traegt aber einen Marker:
 *
 * - Der CRM-Kontakt wird archiviert und nie zur Verkaufschance.
 * - Das Protokoll schreibt das Ereignis `monitor_test` statt
 *   `anfrage_gesendet`; keine Summe zaehlt es mit.
 * - Der Betreff der internen Mail beginnt mit `[MONITOR <lauf>]`, der der
 *   Bestaetigung an den Absender mit `[MONITOR <lauf> · Bestätigung]`.
 *
 * `GET /nexus/v1/monitor/status` und die Seite /anfrage-monitor/ zeigen drei
 * Ampeln: Anfragen kommen an, Seite ist aktuell, Stille (Tage seit der
 * letzten echten Anfrage gegen die gewohnten Abstaende). Beide enthalten
 * keine personenbezogenen Daten.
 *
 * Ohne die Konstante HU_MONITOR_TOKEN (wp-config.php, mindestens 32 Zeichen)
 * ist das Modul aus: keine Endpunkte, der Marker wird ignoriert, die Seite
 * sagt "nicht eingerichtet".
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Laeufe, die aufbewahrt werden. Option statt Tabelle: 90 kleine Eintraege. */
const HU_MONITOR_MAX_RUNS = 90;

/** Laeufe, die Status und Seite als Verlauf zeigen. */
const HU_MONITOR_HISTORY = 30;

/** Ab diesem Alter des letzten Laufs gilt der Monitor selbst als ausgefallen. */
const HU_MONITOR_STALE_HOURS = 26;

/** Ereignisname der Testanfragen im Protokoll `hu_inquiry_events`. */
const HU_MONITOR_EVENT_NAME = 'monitor_test';

/** Prueffelder, die der Taktgeber melden darf, in Reihenfolge des Weges. */
const HU_MONITOR_CHECKS = [ 'pageLoad', 'formSubmit', 'uiSuccess', 'mailAdmin', 'mailConfirmation' ];

/** Fehlerstellen, die WordPress selbst feststellt. */
const HU_MONITOR_SERVER_CHECKS = [ 'crmStored', 'eventRecorded' ];

/**
 * Geheimnis des Monitors oder leerer String, wenn nicht eingerichtet.
 *
 * @return string
 */
function hu_monitor_token() {
	if ( ! defined( 'HU_MONITOR_TOKEN' ) ) {
		return '';
	}

	$token = constant( 'HU_MONITOR_TOKEN' );

	return is_string( $token ) && strlen( $token ) >= 32 ? $token : '';
}

/**
 * @return bool
 */
function hu_monitor_is_enabled() {
	return '' !== hu_monitor_token();
}

/**
 * Lauf-ID einer Testanfrage, oder leer bei fehlendem oder falschem Marker.
 *
 * Ein falscher Token wird wie ein fehlender behandelt: keine Antwort, die ihn
 * bestaetigt, und die Anfrage durchlaeuft Spam-Guard und Rate-Limit normal.
 *
 * @param mixed $payload Roher Payload des Formulars.
 * @return string
 */
function hu_monitor_request_run_id( $payload ) {
	$token = hu_monitor_token();

	if ( '' === $token || ! is_array( $payload ) ) {
		return '';
	}

	$given = isset( $payload['monitor_token'] ) && is_string( $payload['monitor_token'] ) ? $payload['monitor_token'] : '';
	$run   = isset( $payload['monitor_run_id'] ) && is_string( $payload['monitor_run_id'] ) ? $payload['monitor_run_id'] : '';

	if ( '' === $given || ! hash_equals( $token, $given ) ) {
		return '';
	}

	return hu_monitor_is_valid_run_id( $run ) ? $run : '';
}

/**
 * @param mixed $run_id Lauf-ID.
 * @return bool
 */
function hu_monitor_is_valid_run_id( $run_id ) {
	return is_string( $run_id ) && 1 === preg_match( '/^[a-z0-9-]{8,40}$/', $run_id );
}

/**
 * Lauf der gerade verarbeiteten Anfrage merken und lesen.
 *
 * @param string|null $run_id Neuer Wert; null liest nur.
 * @return string
 */
function hu_monitor_current_run( $run_id = null ) {
	static $current = '';

	if ( null !== $run_id ) {
		$current = hu_monitor_is_valid_run_id( $run_id ) ? $run_id : '';
	}

	return $current;
}

/**
 * Testanfragen schreiben `monitor_test` statt `anfrage_gesendet`.
 * Aufgerufen von hu_record_inquiry_event().
 *
 * @param string $event Ereignisname.
 * @return string
 */
function hu_monitor_inquiry_event_name( $event ) {
	return '' !== hu_monitor_current_run() ? HU_MONITOR_EVENT_NAME : $event;
}

/**
 * Was der Kontakt-Handler fuer einen Lauf festgestellt hat.
 *
 * @param string $run_id Lauf-ID.
 * @param array  $facts  contactStored, notified, eventRecorded, confirmationSent.
 * @return void
 */
function hu_monitor_record_intake( $run_id, array $facts ) {
	if ( ! hu_monitor_is_valid_run_id( $run_id ) ) {
		return;
	}

	$intake = get_option( 'hu_monitor_intake', [] );
	$intake = is_array( $intake ) ? $intake : [];

	$intake[ $run_id ] = [
		'crmStored'        => ! empty( $facts['contactStored'] ),
		'notified'         => ! empty( $facts['notified'] ),
		'eventRecorded'    => ! empty( $facts['eventRecorded'] ),
		'confirmationSent' => ! empty( $facts['confirmationSent'] ),
		'at'               => time(),
	];

	uasort(
		$intake,
		static function ( $a, $b ) {
			return (int) $b['at'] <=> (int) $a['at'];
		}
	);

	update_option( 'hu_monitor_intake', array_slice( $intake, 0, 20, true ), false );
}

/**
 * @param string $run_id Lauf-ID.
 * @return array|null
 */
function hu_monitor_get_intake( $run_id ) {
	$intake = get_option( 'hu_monitor_intake', [] );

	return is_array( $intake ) && isset( $intake[ $run_id ] ) && is_array( $intake[ $run_id ] ) ? $intake[ $run_id ] : null;
}

/* ── REST ─────────────────────────────────────────────────────────── */

/**
 * @return void
 */
function hu_monitor_register_rest_routes() {
	if ( ! hu_monitor_is_enabled() ) {
		return;
	}

	register_rest_route(
		'nexus/v1',
		'/monitor/result',
		[
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => 'hu_monitor_handle_result',
			'permission_callback' => 'hu_monitor_authorize',
		]
	);

	register_rest_route(
		'nexus/v1',
		'/monitor/status',
		[
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'hu_monitor_handle_status',
			'permission_callback' => '__return_true',
		]
	);
}
add_action( 'rest_api_init', 'hu_monitor_register_rest_routes' );

/**
 * Bearer-Pruefung fuer den Taktgeber.
 *
 * @param WP_REST_Request $request Anfrage.
 * @return true|WP_Error
 */
function hu_monitor_authorize( $request ) {
	$header = (string) $request->get_header( 'authorization' );
	$given  = 0 === stripos( $header, 'Bearer ' ) ? trim( substr( $header, 7 ) ) : '';
	$token  = hu_monitor_token();

	if ( '' !== $token && '' !== $given && hash_equals( $token, $given ) ) {
		return true;
	}

	return new WP_Error( 'hu_monitor_unauthorized', 'Nicht berechtigt.', [ 'status' => 401 ] );
}

/**
 * Laufergebnis des Taktgebers annehmen.
 *
 * @param WP_REST_Request $request Anfrage.
 * @return WP_REST_Response
 */
function hu_monitor_handle_result( $request ) {
	$body = $request->get_json_params();
	$run  = hu_monitor_normalize_run( is_array( $body ) ? $body : [] );

	if ( is_wp_error( $run ) ) {
		return new WP_REST_Response( [ 'ok' => false, 'error' => $run->get_error_message() ], 400 );
	}

	hu_monitor_store_run( $run );

	return new WP_REST_Response( [ 'ok' => true, 'run' => $run ], 201 );
}

/**
 * Laufergebnis pruefen und um die Feststellungen von WordPress ergaenzen.
 *
 * @param array $body Roher Body.
 * @return array|WP_Error
 */
function hu_monitor_normalize_run( array $body ) {
	$run_id = isset( $body['runId'] ) ? $body['runId'] : '';
	if ( ! hu_monitor_is_valid_run_id( $run_id ) ) {
		return new WP_Error( 'hu_monitor_run_id', 'runId fehlt oder ist ungueltig.' );
	}

	$started = isset( $body['startedAt'] ) && is_string( $body['startedAt'] ) ? strtotime( $body['startedAt'] ) : false;
	if ( false === $started || $started > time() + 300 ) {
		return new WP_Error( 'hu_monitor_started_at', 'startedAt fehlt oder liegt in der Zukunft.' );
	}

	$raw_checks = isset( $body['checks'] ) && is_array( $body['checks'] ) ? $body['checks'] : [];
	$checks     = [];
	foreach ( HU_MONITOR_CHECKS as $key ) {
		$checks[ $key ] = isset( $raw_checks[ $key ] ) && true === $raw_checks[ $key ];
	}

	// Was der Server selbst gesehen hat, ersetzt jede Behauptung des Taktgebers.
	$intake                    = hu_monitor_get_intake( $run_id );
	$checks['crmStored']       = is_array( $intake ) && ! empty( $intake['crmStored'] );
	$checks['eventRecorded']   = is_array( $intake ) && ! empty( $intake['eventRecorded'] );

	$failed_at = null;
	foreach ( array_merge( HU_MONITOR_CHECKS, HU_MONITOR_SERVER_CHECKS ) as $key ) {
		if ( ! $checks[ $key ] ) {
			$failed_at = $key;
			break;
		}
	}

	$durations = [];
	if ( isset( $body['durationsMs'] ) && is_array( $body['durationsMs'] ) ) {
		foreach ( HU_MONITOR_CHECKS as $key ) {
			if ( isset( $body['durationsMs'][ $key ] ) && is_numeric( $body['durationsMs'][ $key ] ) ) {
				$durations[ $key ] = max( 0, min( 3600000, (int) $body['durationsMs'][ $key ] ) );
			}
		}
	}

	return [
		'runId'       => $run_id,
		'startedAt'   => gmdate( 'c', $started ),
		'ok'          => null === $failed_at,
		'failedAt'    => $failed_at,
		'checks'      => $checks,
		'durationsMs' => $durations,
	];
}

/**
 * @param array $run Normalisierter Lauf.
 * @return void
 */
function hu_monitor_store_run( array $run ) {
	$runs                   = hu_monitor_get_runs();
	$runs[ $run['runId'] ]  = $run;

	uasort(
		$runs,
		static function ( $a, $b ) {
			return strcmp( (string) $b['startedAt'], (string) $a['startedAt'] );
		}
	);

	update_option( 'hu_monitor_runs', array_slice( $runs, 0, HU_MONITOR_MAX_RUNS, true ), false );
	delete_transient( 'hu_monitor_status' );
}

/**
 * Laeufe, neuester zuerst, nach runId.
 *
 * @return array<string, array>
 */
function hu_monitor_get_runs() {
	$runs = get_option( 'hu_monitor_runs', [] );

	return is_array( $runs ) ? array_filter( $runs, 'is_array' ) : [];
}

/**
 * @return WP_REST_Response
 */
function hu_monitor_handle_status() {
	$response = new WP_REST_Response( hu_monitor_cached_status(), 200 );
	$response->header( 'Cache-Control', 'public, max-age=300' );

	return $response;
}

/* ── Status ───────────────────────────────────────────────────────── */

/**
 * Status, fuenf Minuten zwischengespeichert; ein neuer Lauf leert den Speicher.
 *
 * @return array
 */
function hu_monitor_cached_status() {
	$status = get_transient( 'hu_monitor_status' );

	if ( ! is_array( $status ) ) {
		$status = hu_monitor_status();
		set_transient( 'hu_monitor_status', $status, 5 * MINUTE_IN_SECONDS );
	}

	return $status;
}

/**
 * Die drei Ampeln und der Verlauf. Ohne personenbezogene Daten.
 *
 * Zustaende: `ok`, `warn`, `fail`, `none` (keine Aussage moeglich).
 *
 * @param int|null $now Zeitpunkt (Tests).
 * @return array
 */
function hu_monitor_status( $now = null ) {
	$now  = null === $now ? time() : (int) $now;
	$runs = array_values( hu_monitor_get_runs() );

	return [
		'enabled'     => hu_monitor_is_enabled(),
		'generatedAt' => gmdate( 'c', $now ),
		'anfragen'    => hu_monitor_status_inquiries( $runs, $now ),
		'aktuell'     => hu_monitor_status_updates( $now ),
		'stille'      => hu_monitor_status_silence( $now ),
		'verlauf'     => array_map(
			static function ( $run ) {
				return [
					'datum'    => (string) $run['startedAt'],
					'ok'       => ! empty( $run['ok'] ),
					'failedAt' => $run['failedAt'] ?? null,
				];
			},
			array_slice( $runs, 0, HU_MONITOR_HISTORY )
		),
	];
}

/**
 * @param array $runs Laeufe, neuester zuerst.
 * @param int   $now  Zeitpunkt.
 * @return array{state:string,label:string,at:string|null,failedAt:string|null}
 */
function hu_monitor_status_inquiries( array $runs, $now ) {
	if ( empty( $runs ) ) {
		return [ 'state' => 'none', 'label' => 'Noch kein Testlauf', 'at' => null, 'failedAt' => null ];
	}

	$last = $runs[0];
	$age  = $now - (int) strtotime( (string) $last['startedAt'] );

	if ( empty( $last['ok'] ) ) {
		return [
			'state'    => 'fail',
			'label'    => 'Testanfrage nicht vollständig angekommen',
			'at'       => (string) $last['startedAt'],
			'failedAt' => (string) $last['failedAt'],
		];
	}

	if ( $age > HU_MONITOR_STALE_HOURS * HOUR_IN_SECONDS ) {
		return [ 'state' => 'warn', 'label' => 'Letzter Testlauf ist älter als ein Tag', 'at' => (string) $last['startedAt'], 'failedAt' => null ];
	}

	return [ 'state' => 'ok', 'label' => 'Testanfrage angekommen', 'at' => (string) $last['startedAt'], 'failedAt' => null ];
}

/**
 * Offene Updates fuer Kern, Plugins und Themes aus den Update-Transients.
 *
 * Das Alter zaehlt ab dem ersten Statusaufruf, an dem Updates offen waren.
 *
 * @param int $now Zeitpunkt.
 * @return array{state:string,label:string,pending:int,days:int}
 */
function hu_monitor_status_updates( $now ) {
	$pending = 0;

	$core = get_site_transient( 'update_core' );
	if ( is_object( $core ) && ! empty( $core->updates ) && is_array( $core->updates ) ) {
		foreach ( $core->updates as $offer ) {
			if ( is_object( $offer ) && isset( $offer->response ) && 'upgrade' === $offer->response ) {
				++$pending;
				break;
			}
		}
	}

	foreach ( [ 'update_plugins', 'update_themes' ] as $transient ) {
		$data = get_site_transient( $transient );
		if ( is_object( $data ) && ! empty( $data->response ) && is_array( $data->response ) ) {
			$pending += count( $data->response );
		}
	}

	if ( 0 === $pending ) {
		delete_option( 'hu_monitor_updates_since' );

		return [ 'state' => 'ok', 'label' => 'Alles aktuell', 'pending' => 0, 'days' => 0 ];
	}

	$since = (int) get_option( 'hu_monitor_updates_since', 0 );
	if ( $since <= 0 ) {
		$since = $now;
		update_option( 'hu_monitor_updates_since', $since, false );
	}

	$days = (int) floor( max( 0, $now - $since ) / DAY_IN_SECONDS );

	return [
		'state'   => $days >= 7 ? 'fail' : 'warn',
		'label'   => 1 === $pending ? '1 Update offen' : $pending . ' Updates offen',
		'pending' => $pending,
		'days'    => $days,
	];
}

/**
 * Stille: Tage seit der letzten echten Anfrage gegen die gewohnten Abstaende.
 *
 * Schwelle = max(14 Tage, 1,5 x 90. Perzentil der Abstaende der letzten 180
 * Tage). Unter 6 echten Anfragen im Zeitraum gibt es keine Aussage.
 *
 * @param int $now Zeitpunkt.
 * @return array{state:string,label:string,days:int|null,threshold:int|null}
 */
function hu_monitor_status_silence( $now ) {
	$times = hu_monitor_real_inquiry_times( $now - 180 * DAY_IN_SECONDS );

	if ( count( $times ) < 6 ) {
		return [ 'state' => 'none', 'label' => 'Zu wenig Anfragen für eine Aussage', 'days' => null, 'threshold' => null ];
	}

	$gaps = [];
	for ( $i = 1, $n = count( $times ); $i < $n; $i++ ) {
		$gaps[] = ( $times[ $i ] - $times[ $i - 1 ] ) / DAY_IN_SECONDS;
	}
	sort( $gaps );

	$p90       = $gaps[ (int) min( count( $gaps ) - 1, ceil( 0.9 * count( $gaps ) ) - 1 ) ];
	$threshold = (int) ceil( max( 14, 1.5 * $p90 ) );
	$days      = (int) floor( max( 0, $now - end( $times ) ) / DAY_IN_SECONDS );

	return [
		'state'     => $days < $threshold ? 'ok' : 'fail',
		'label'     => 0 === $days ? 'Letzte Anfrage heute' : ( 1 === $days ? 'Letzte Anfrage vor 1 Tag' : 'Letzte Anfrage vor ' . $days . ' Tagen' ),
		'days'      => $days,
		'threshold' => $threshold,
	];
}

/**
 * Zeitpunkte echter Anfragen (ohne Testanfragen), aufsteigend.
 *
 * @param int $since Unix-Zeit.
 * @return int[]
 */
function hu_monitor_real_inquiry_times( $since ) {
	global $wpdb;

	if ( ! is_object( $wpdb ) || ! function_exists( 'hu_inquiry_events_install' ) || ! hu_inquiry_events_install() ) {
		return [];
	}

	$table = hu_inquiry_events_table();
	$rows  = $wpdb->get_col(
		$wpdb->prepare(
			"SELECT created_at FROM {$table} WHERE event = %s AND created_at >= %s ORDER BY created_at ASC",
			HU_INQUIRY_EVENT_NAME,
			gmdate( 'Y-m-d H:i:s', (int) $since )
		)
	);

	$times = [];
	foreach ( (array) $rows as $value ) {
		$time = strtotime( (string) $value . ' UTC' );
		if ( false !== $time ) {
			$times[] = $time;
		}
	}

	return $times;
}

/* ── Route /anfrage-monitor/ ──────────────────────────────────────── */

/**
 * @return string
 */
function hu_monitor_route_path() {
	return trailingslashit( '/' . ltrim( (string) wp_parse_url( home_url( '/anfrage-monitor/' ), PHP_URL_PATH ), '/' ) );
}

/**
 * @return bool
 */
function hu_monitor_is_route() {
	return function_exists( 'nexus_get_current_request_path' ) && nexus_get_current_request_path() === hu_monitor_route_path();
}

/**
 * @param bool     $preempt  Vorheriger Wert.
 * @param WP_Query $wp_query Hauptabfrage.
 * @return bool
 */
function hu_monitor_preempt_404( $preempt, $wp_query ) {
	if ( is_admin() || wp_doing_ajax() || ! ( $wp_query instanceof WP_Query ) || ! hu_monitor_is_route() ) {
		return $preempt;
	}

	$wp_query->is_404            = false;
	$wp_query->is_page           = true;
	$wp_query->is_singular       = true;
	$wp_query->is_home           = false;
	$wp_query->is_archive        = false;
	$wp_query->queried_object    = null;
	$wp_query->queried_object_id = 0;
	$wp_query->query_vars['pagename'] = 'anfrage-monitor';
	unset( $wp_query->query['error'], $wp_query->query_vars['error'] );

	status_header( 200 );

	return true;
}
add_filter( 'pre_handle_404', 'hu_monitor_preempt_404', 10, 2 );

/**
 * @param string $redirect_url Ziel.
 * @return string|false
 */
function hu_monitor_disable_canonical_redirect( $redirect_url ) {
	return hu_monitor_is_route() ? false : $redirect_url;
}
add_filter( 'redirect_canonical', 'hu_monitor_disable_canonical_redirect' );

/**
 * @param string $template Template.
 * @return string
 */
function hu_monitor_template( $template ) {
	if ( ! hu_monitor_is_route() ) {
		return $template;
	}

	$file = get_stylesheet_directory() . '/page-anfrage-monitor.php';

	return file_exists( $file ) ? $file : $template;
}
add_filter( 'template_include', 'hu_monitor_template', 99 );

/**
 * @param string[] $classes Body-Klassen.
 * @return string[]
 */
function hu_monitor_body_class( $classes ) {
	if ( hu_monitor_is_route() ) {
		$classes[] = 'anfrage-monitor-route';
	}

	return $classes;
}
add_filter( 'body_class', 'hu_monitor_body_class' );

/**
 * Zeitpunkt in Klartext ("heute, 06:14 Uhr"), Ortszeit der Website.
 *
 * @param string|null $iso ISO-Zeitpunkt.
 * @param int|null    $now Zeitpunkt (Tests).
 * @return string
 */
function hu_monitor_human_time( $iso, $now = null ) {
	$time = is_string( $iso ) ? strtotime( $iso ) : false;
	if ( false === $time ) {
		return '';
	}

	$now   = null === $now ? time() : (int) $now;
	$day   = wp_date( 'Y-m-d', $time );
	$clock = wp_date( 'H:i', $time ) . ' Uhr';

	if ( wp_date( 'Y-m-d', $now ) === $day ) {
		return 'heute, ' . $clock;
	}

	if ( wp_date( 'Y-m-d', $now - DAY_IN_SECONDS ) === $day ) {
		return 'gestern, ' . $clock;
	}

	return wp_date( 'd.m.Y', $time ) . ', ' . $clock;
}

/**
 * Menschliche Bezeichnung einer Fehlerstelle.
 *
 * @param string|null $key Pruefschluessel.
 * @return string
 */
function hu_monitor_check_label( $key ) {
	$labels = [
		'pageLoad'         => 'Kontaktseite lädt',
		'formSubmit'       => 'Formular lässt sich absenden',
		'uiSuccess'        => 'Bestätigung erscheint',
		'mailAdmin'        => 'Anfrage im Postfach',
		'mailConfirmation' => 'Bestätigung an Absender',
		'crmStored'        => 'Anfrage im CRM gespeichert',
		'eventRecorded'    => 'Anfrage protokolliert',
	];

	return is_string( $key ) && isset( $labels[ $key ] ) ? $labels[ $key ] : '';
}
