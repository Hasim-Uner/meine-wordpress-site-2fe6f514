<?php
/**
 * SEO Cockpit Research Intelligence.
 *
 * Adds an admin-only research layer for external primary-source data. The
 * first provider is Chrome UX Report (CrUX); future providers can be added
 * without widening the main cockpit UI or changing Search Console behavior.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the Research Intelligence admin slug.
 *
 * @return string
 */
function nexus_get_seo_cockpit_research_slug() {
	return 'nexus-seo-cockpit-research';
}

/**
 * Return the option name for Research Intelligence settings.
 *
 * @return string
 */
function nexus_get_seo_cockpit_research_option_name() {
	return 'nexus_seo_cockpit_research_settings';
}

/**
 * Return persisted Research Intelligence settings.
 *
 * @return array<string, string>
 */
function nexus_get_seo_cockpit_research_settings() {
	$settings = get_option( nexus_get_seo_cockpit_research_option_name(), [] );
	$settings = is_array( $settings ) ? $settings : [];

	return wp_parse_args(
		$settings,
		[
			'crux_api_key' => '',
		]
	);
}

/**
 * Return the effective CrUX API key.
 *
 * A wp-config.php constant wins over the WordPress option so production can
 * keep credentials outside the database when desired.
 *
 * @return string
 */
function nexus_get_seo_cockpit_crux_api_key() {
	if ( defined( 'NEXUS_CRUX_API_KEY' ) && NEXUS_CRUX_API_KEY ) {
		return trim( (string) NEXUS_CRUX_API_KEY );
	}

	$settings = nexus_get_seo_cockpit_research_settings();

	return trim( (string) ( $settings['crux_api_key'] ?? '' ) );
}

/**
 * Determine whether the CrUX key is supplied by wp-config.php.
 *
 * @return bool
 */
function nexus_seo_cockpit_crux_uses_constant() {
	return defined( 'NEXUS_CRUX_API_KEY' ) && (bool) NEXUS_CRUX_API_KEY;
}

/**
 * Return the canonical origin used for origin-level CrUX requests.
 *
 * @return string
 */
function nexus_get_seo_cockpit_crux_origin() {
	$home   = home_url( '/' );
	$scheme = (string) wp_parse_url( $home, PHP_URL_SCHEME );
	$host   = (string) wp_parse_url( $home, PHP_URL_HOST );
	$port   = absint( wp_parse_url( $home, PHP_URL_PORT ) );

	if ( '' === $scheme || '' === $host ) {
		return '';
	}

	$origin = strtolower( $scheme ) . '://' . strtolower( $host );
	if ( $port > 0 ) {
		$origin .= ':' . $port;
	}

	return $origin;
}

/**
 * Return the CrUX metrics used by the research panel.
 *
 * Thresholds are the p75 boundaries used for the three-state presentation.
 * TTFB is diagnostic rather than a Core Web Vital.
 *
 * @return array<string, array<string, mixed>>
 */
function nexus_get_seo_cockpit_crux_metric_definitions() {
	return [
		'largest_contentful_paint' => [
			'label'    => 'LCP',
			'unit'     => 'ms',
			'decimals' => 0,
			'good'     => 2500,
			'poor'     => 4000,
			'core'     => true,
		],
		'interaction_to_next_paint' => [
			'label'    => 'INP',
			'unit'     => 'ms',
			'decimals' => 0,
			'good'     => 200,
			'poor'     => 500,
			'core'     => true,
		],
		'cumulative_layout_shift' => [
			'label'    => 'CLS',
			'unit'     => '',
			'decimals' => 2,
			'good'     => 0.1,
			'poor'     => 0.25,
			'core'     => true,
		],
		'experimental_time_to_first_byte' => [
			'label'    => 'TTFB',
			'unit'     => 'ms',
			'decimals' => 0,
			'good'     => 800,
			'poor'     => 1800,
			'core'     => false,
		],
	];
}

/**
 * Return the CrUX REST endpoint.
 *
 * @param bool $history Whether to use the history endpoint.
 * @return string
 */
function nexus_get_seo_cockpit_crux_endpoint( $history = false ) {
	return $history
		? 'https://chromeuxreport.googleapis.com/v1/records:queryHistoryRecord'
		: 'https://chromeuxreport.googleapis.com/v1/records:queryRecord';
}

/**
 * Query one CrUX record with a defensive transient cache.
 *
 * @param string $form_factor PHONE or DESKTOP.
 * @param bool   $history     Whether to fetch the history record.
 * @param bool   $force       Whether to skip a cached response.
 * @return array<string, mixed>|WP_Error
 */
function nexus_get_seo_cockpit_crux_record( $form_factor, $history = false, $force = false ) {
	$form_factor = strtoupper( sanitize_key( (string) $form_factor ) );
	if ( ! in_array( $form_factor, [ 'PHONE', 'DESKTOP' ], true ) ) {
		return new WP_Error( 'nexus_crux_form_factor', 'Unbekannter CrUX-Formfaktor.' );
	}

	$api_key = nexus_get_seo_cockpit_crux_api_key();
	if ( '' === $api_key ) {
		return new WP_Error( 'nexus_crux_missing_key', 'CrUX ist noch nicht konfiguriert.' );
	}

	$origin = nexus_get_seo_cockpit_crux_origin();
	if ( '' === $origin ) {
		return new WP_Error( 'nexus_crux_origin', 'Die Website-Origin konnte nicht ermittelt werden.' );
	}

	$cache_key = function_exists( 'nexus_get_seo_cockpit_cache_key' )
		? nexus_get_seo_cockpit_cache_key( 'crux', [ $origin, $form_factor, $history ? 'history' : 'current' ] )
		: 'nexus_crux_' . md5( $origin . '|' . $form_factor . '|' . ( $history ? 'history' : 'current' ) );

	if ( ! $force ) {
		$cached = get_transient( $cache_key );
		if ( is_array( $cached ) ) {
			return $cached;
		}
	}

	$payload = [
		'origin'     => $origin,
		'formFactor' => $form_factor,
		'metrics'    => array_keys( nexus_get_seo_cockpit_crux_metric_definitions() ),
	];

	if ( $history ) {
		$payload['collectionPeriodCount'] = 40;
	}

	$endpoint = add_query_arg( 'key', $api_key, nexus_get_seo_cockpit_crux_endpoint( $history ) );
	$response = wp_remote_post(
		$endpoint,
		[
			'timeout' => 15,
			'headers' => [
				'Accept'       => 'application/json',
				'Content-Type' => 'application/json',
			],
			'body'    => wp_json_encode( $payload ),
		]
	);

	if ( is_wp_error( $response ) ) {
		return new WP_Error( 'nexus_crux_request', 'CrUX konnte nicht erreicht werden: ' . $response->get_error_message() );
	}

	$status = (int) wp_remote_retrieve_response_code( $response );
	$body   = json_decode( (string) wp_remote_retrieve_body( $response ), true );
	$body   = is_array( $body ) ? $body : [];

	if ( $status < 200 || $status >= 300 ) {
		$message = sanitize_text_field( (string) ( $body['error']['message'] ?? 'Unbekannte API-Antwort.' ) );

		return new WP_Error(
			'nexus_crux_http',
			sprintf( 'CrUX antwortet mit HTTP %1$d: %2$s', $status, $message )
		);
	}

	if ( empty( $body['record'] ) || ! is_array( $body['record'] ) ) {
		return new WP_Error( 'nexus_crux_empty', 'Für diese Origin liegen aktuell keine CrUX-Daten vor.' );
	}

	set_transient( $cache_key, $body, $history ? 12 * HOUR_IN_SECONDS : 6 * HOUR_IN_SECONDS );

	return $body;
}

/**
 * Delete only Research Intelligence CrUX transients.
 *
 * @return void
 */
function nexus_delete_seo_cockpit_crux_cache() {
	$origin = nexus_get_seo_cockpit_crux_origin();
	if ( '' === $origin ) {
		return;
	}

	foreach ( [ 'PHONE', 'DESKTOP' ] as $form_factor ) {
		foreach ( [ false, true ] as $history ) {
			$cache_key = function_exists( 'nexus_get_seo_cockpit_cache_key' )
				? nexus_get_seo_cockpit_cache_key( 'crux', [ $origin, $form_factor, $history ? 'history' : 'current' ] )
				: 'nexus_crux_' . md5( $origin . '|' . $form_factor . '|' . ( $history ? 'history' : 'current' ) );
			delete_transient( $cache_key );
		}
	}
}

/**
 * Read the p75 value for one metric from a current CrUX response.
 *
 * @param array<string, mixed>|WP_Error $record CrUX response.
 * @param string                        $metric Metric API key.
 * @return float|null
 */
function nexus_get_seo_cockpit_crux_current_p75( $record, $metric ) {
	if ( is_wp_error( $record ) || ! is_array( $record ) ) {
		return null;
	}

	$value = $record['record']['metrics'][ $metric ]['percentiles']['p75'] ?? null;

	return is_numeric( $value ) ? (float) $value : null;
}

/**
 * Return first and latest usable p75 values from a history response.
 *
 * @param array<string, mixed>|WP_Error $record CrUX history response.
 * @param string                        $metric Metric API key.
 * @return array{first: float|null, latest: float|null}
 */
function nexus_get_seo_cockpit_crux_history_p75_range( $record, $metric ) {
	$empty = [
		'first'  => null,
		'latest' => null,
	];

	if ( is_wp_error( $record ) || ! is_array( $record ) ) {
		return $empty;
	}

	$values = $record['record']['metrics'][ $metric ]['percentilesTimeseries']['p75s'] ?? [];
	if ( ! is_array( $values ) || empty( $values ) ) {
		return $empty;
	}

	$numeric = array_values(
		array_filter(
			$values,
			static function ( $value ) {
				return is_numeric( $value );
			}
		)
	);

	if ( empty( $numeric ) ) {
		return $empty;
	}

	return [
		'first'  => (float) reset( $numeric ),
		'latest' => (float) end( $numeric ),
	];
}

/**
 * Return a human label and CSS state for one p75 value.
 *
 * @param float|null                  $value Metric value.
 * @param array<string, mixed>        $definition Metric definition.
 * @return array{label: string, class: string}
 */
function nexus_get_seo_cockpit_crux_state( $value, $definition ) {
	if ( null === $value || ! isset( $definition['good'], $definition['poor'] ) ) {
		return [
			'label' => 'Keine Daten',
			'class' => 'is-neutral',
		];
	}

	if ( $value <= (float) $definition['good'] ) {
		return [
			'label' => 'Gut',
			'class' => 'is-positive',
		];
	}

	if ( $value <= (float) $definition['poor'] ) {
		return [
			'label' => 'Verbesserbar',
			'class' => 'is-neutral',
		];
	}

	return [
		'label' => 'Schlecht',
		'class' => 'is-negative',
	];
}

/**
 * Format one CrUX metric value for the admin UI.
 *
 * @param float|null           $value Metric value.
 * @param array<string, mixed> $definition Metric definition.
 * @return string
 */
function nexus_format_seo_cockpit_crux_value( $value, $definition ) {
	if ( null === $value ) {
		return '—';
	}

	$decimals = absint( $definition['decimals'] ?? 0 );
	$unit     = (string) ( $definition['unit'] ?? '' );
	$formatted = number_format_i18n( $value, $decimals );

	return '' !== $unit ? $formatted . ' ' . $unit : $formatted;
}

/**
 * Format a CrUX collection period end date.
 *
 * @param array<string, mixed>|WP_Error $record Current CrUX response.
 * @return string
 */
function nexus_get_seo_cockpit_crux_period_label( $record ) {
	if ( is_wp_error( $record ) || ! is_array( $record ) ) {
		return '—';
	}

	$date = $record['record']['collectionPeriod']['lastDate'] ?? [];
	if ( ! is_array( $date ) ) {
		return '—';
	}

	$year  = absint( $date['year'] ?? 0 );
	$month = absint( $date['month'] ?? 0 );
	$day   = absint( $date['day'] ?? 0 );
	if ( $year < 2000 || $month < 1 || $day < 1 ) {
		return '—';
	}

	return sprintf( '%02d.%02d.%04d', $day, $month, $year );
}

/**
 * Register the Research Intelligence submenu.
 *
 * @return void
 */
function nexus_register_seo_cockpit_research_page() {
	add_submenu_page(
		nexus_get_seo_cockpit_menu_slug(),
		'Research Intelligence',
		'Research',
		nexus_get_seo_cockpit_view_cap(),
		nexus_get_seo_cockpit_research_slug(),
		'nexus_render_seo_cockpit_research_page'
	);
}
add_action( 'admin_menu', 'nexus_register_seo_cockpit_research_page', 40 );

/**
 * Enqueue the small Research Intelligence stylesheet only on its admin page.
 *
 * @return void
 */
function nexus_enqueue_seo_cockpit_research_assets() {
	$page = isset( $_GET['page'] ) ? sanitize_key( (string) wp_unslash( $_GET['page'] ) ) : '';
	if ( nexus_get_seo_cockpit_research_slug() !== $page ) {
		return;
	}

	$path = get_stylesheet_directory() . '/assets/css/seo-cockpit-research.css';
	if ( ! file_exists( $path ) ) {
		return;
	}

	wp_enqueue_style(
		'nexus-seo-cockpit-research',
		get_stylesheet_directory_uri() . '/assets/css/seo-cockpit-research.css',
		[ 'nexus-seo-cockpit-admin' ],
		filemtime( $path )
	);
}
add_action( 'admin_enqueue_scripts', 'nexus_enqueue_seo_cockpit_research_assets', 30 );

/**
 * Persist or clear the runtime CrUX API key.
 *
 * @return void
 */
function nexus_handle_seo_cockpit_research_save() {
	if ( ! nexus_current_user_can_manage_seo_cockpit() ) {
		wp_die( 'Nicht erlaubt.' );
	}

	check_admin_referer( 'nexus_seo_cockpit_research_save' );

	if ( nexus_seo_cockpit_crux_uses_constant() ) {
		wp_safe_redirect( admin_url( 'admin.php?page=' . nexus_get_seo_cockpit_research_slug() . '&research_notice=constant' ) );
		exit;
	}

	$settings = nexus_get_seo_cockpit_research_settings();
	if ( ! empty( $_POST['clear_crux_api_key'] ) ) {
		$settings['crux_api_key'] = '';
	} else {
		$new_key = isset( $_POST['crux_api_key'] ) ? sanitize_text_field( (string) wp_unslash( $_POST['crux_api_key'] ) ) : '';
		if ( '' !== $new_key ) {
			$settings['crux_api_key'] = $new_key;
		}
	}

	update_option( nexus_get_seo_cockpit_research_option_name(), $settings, false );
	nexus_delete_seo_cockpit_crux_cache();

	wp_safe_redirect( admin_url( 'admin.php?page=' . nexus_get_seo_cockpit_research_slug() . '&research_notice=saved' ) );
	exit;
}
add_action( 'admin_post_nexus_seo_cockpit_research_save', 'nexus_handle_seo_cockpit_research_save' );

/**
 * Clear CrUX transients so the next page render fetches fresh source data.
 *
 * @return void
 */
function nexus_handle_seo_cockpit_research_refresh() {
	if ( ! nexus_current_user_can_manage_seo_cockpit() ) {
		wp_die( 'Nicht erlaubt.' );
	}

	check_admin_referer( 'nexus_seo_cockpit_research_refresh' );
	nexus_delete_seo_cockpit_crux_cache();

	wp_safe_redirect( admin_url( 'admin.php?page=' . nexus_get_seo_cockpit_research_slug() . '&research_notice=refresh' ) );
	exit;
}
add_action( 'admin_post_nexus_seo_cockpit_research_refresh', 'nexus_handle_seo_cockpit_research_refresh' );

/**
 * Render one form-factor metric group.
 *
 * @param string                        $label Display label.
 * @param array<string, mixed>|WP_Error $current Current CrUX response.
 * @param array<string, mixed>|WP_Error $history CrUX history response.
 * @return void
 */
function nexus_render_seo_cockpit_crux_metric_group( $label, $current, $history ) {
	$definitions = nexus_get_seo_cockpit_crux_metric_definitions();
	?>
	<section class="nexus-seo-cockpit__panel nexus-seo-cockpit__research-metrics">
		<div class="nexus-seo-cockpit__panel-head">
			<div>
				<p class="nexus-seo-cockpit__eyebrow">CrUX · <?php echo esc_html( $label ); ?></p>
				<h2>Felddaten am p75</h2>
			</div>
			<span class="nexus-seo-cockpit__chip">Stand <?php echo esc_html( nexus_get_seo_cockpit_crux_period_label( $current ) ); ?></span>
		</div>

		<?php if ( is_wp_error( $current ) ) : ?>
			<p class="notice notice-warning inline"><strong>Keine aktuellen Daten:</strong> <?php echo esc_html( $current->get_error_message() ); ?></p>
		<?php else : ?>
			<div class="nexus-seo-cockpit__metrics">
				<?php foreach ( $definitions as $metric => $definition ) : ?>
					<?php
					$value         = nexus_get_seo_cockpit_crux_current_p75( $current, $metric );
					$state         = nexus_get_seo_cockpit_crux_state( $value, $definition );
					$history_range = nexus_get_seo_cockpit_crux_history_p75_range( $history, $metric );
					$delta         = null;
					if ( null !== $history_range['first'] && null !== $history_range['latest'] ) {
						$delta = $history_range['latest'] - $history_range['first'];
					}
					$delta_class = null === $delta || 0.0 === (float) $delta ? 'is-neutral' : ( $delta < 0 ? 'is-positive' : 'is-negative' );
					?>
					<article class="nexus-seo-cockpit__metric-card">
						<span class="nexus-seo-cockpit__metric-label"><?php echo esc_html( (string) $definition['label'] ); ?><?php echo ! empty( $definition['core'] ) ? ' · Core Web Vital' : ' · Diagnose'; ?></span>
						<strong class="nexus-seo-cockpit__metric-value"><?php echo esc_html( nexus_format_seo_cockpit_crux_value( $value, $definition ) ); ?></strong>
						<span class="nexus-seo-cockpit__metric-delta <?php echo esc_attr( $state['class'] ); ?>"><?php echo esc_html( $state['label'] ); ?></span>
						<?php if ( null !== $delta ) : ?>
							<span class="nexus-seo-cockpit__research-trend <?php echo esc_attr( $delta_class ); ?>">
								<?php echo esc_html( ( $delta > 0 ? '+' : '' ) . nexus_format_seo_cockpit_crux_value( $delta, $definition ) ); ?> im Verlauf
							</span>
						<?php endif; ?>
					</article>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if ( is_wp_error( $history ) && ! is_wp_error( $current ) ) : ?>
			<p class="nexus-seo-cockpit__hint nexus-seo-cockpit__research-history-note">Verlauf aktuell nicht verfügbar: <?php echo esc_html( $history->get_error_message() ); ?></p>
		<?php endif; ?>
	</section>
	<?php
}

/**
 * Render the Research Intelligence admin page.
 *
 * @return void
 */
function nexus_render_seo_cockpit_research_page() {
	if ( ! nexus_current_user_can_view_seo_cockpit() ) {
		wp_die( 'Nicht erlaubt.' );
	}

	$api_key     = nexus_get_seo_cockpit_crux_api_key();
	$origin      = nexus_get_seo_cockpit_crux_origin();
	$can_manage  = nexus_current_user_can_manage_seo_cockpit();
	$uses_const  = nexus_seo_cockpit_crux_uses_constant();
	$notice      = isset( $_GET['research_notice'] ) ? sanitize_key( (string) wp_unslash( $_GET['research_notice'] ) ) : '';
	$phone       = '' !== $api_key ? nexus_get_seo_cockpit_crux_record( 'PHONE', false ) : new WP_Error( 'nexus_crux_missing_key', 'CrUX ist noch nicht konfiguriert.' );
	$phone_hist  = '' !== $api_key ? nexus_get_seo_cockpit_crux_record( 'PHONE', true ) : $phone;
	$desktop     = '' !== $api_key ? nexus_get_seo_cockpit_crux_record( 'DESKTOP', false ) : $phone;
	$desktop_hist = '' !== $api_key ? nexus_get_seo_cockpit_crux_record( 'DESKTOP', true ) : $phone;
	?>
	<div class="wrap nexus-seo-cockpit nexus-seo-cockpit__research">
		<p class="nexus-seo-cockpit__eyebrow">Research Intelligence</p>
		<h1>Primärdaten statt Bauchgefühl</h1>

		<?php if ( 'saved' === $notice ) : ?>
			<div class="notice notice-success is-dismissible"><p>Research-Einstellungen gespeichert.</p></div>
		<?php elseif ( 'refresh' === $notice ) : ?>
			<div class="notice notice-success is-dismissible"><p>CrUX-Cache geleert. Die Ansicht lädt frische Felddaten.</p></div>
		<?php elseif ( 'constant' === $notice ) : ?>
			<div class="notice notice-info is-dismissible"><p>Der CrUX-Key kommt aus <code>NEXUS_CRUX_API_KEY</code> und wird deshalb hier nicht überschrieben.</p></div>
		<?php endif; ?>

		<div class="nexus-seo-cockpit__toolbar">
			<div class="nexus-seo-cockpit__toolbar-meta">
				<span class="nexus-seo-cockpit__status-dot <?php echo '' !== $api_key ? 'is-connected' : 'is-warning'; ?>"><?php echo '' !== $api_key ? 'CrUX konfiguriert' : 'CrUX-Key fehlt'; ?></span>
				<span><strong>Origin:</strong> <code><?php echo esc_html( $origin ?: 'nicht ermittelbar' ); ?></code></span>
				<span><strong>Quelle:</strong> Chrome UX Report</span>
			</div>
			<?php if ( $can_manage && '' !== $api_key ) : ?>
				<div class="nexus-seo-cockpit__toolbar-actions">
					<form method="post" action="<?php echo esc_url( nexus_get_seo_cockpit_admin_action_url( 'nexus_seo_cockpit_research_refresh' ) ); ?>">
						<?php wp_nonce_field( 'nexus_seo_cockpit_research_refresh' ); ?>
						<button type="submit" class="button">CrUX neu laden</button>
					</form>
				</div>
			<?php endif; ?>
		</div>

		<section class="nexus-seo-cockpit__panel nexus-seo-cockpit__panel--primary nexus-seo-cockpit__research-intro">
			<div class="nexus-seo-cockpit__panel-head">
				<div>
					<p class="nexus-seo-cockpit__eyebrow">Datenlayer 01</p>
					<h2>Chrome UX Report</h2>
				</div>
				<span class="nexus-seo-cockpit__chip">echte Chrome-Felddaten</span>
			</div>
			<p class="nexus-seo-cockpit__hint">Origin-Level-Daten für Mobil und Desktop. Angezeigt werden LCP, INP, CLS und TTFB am 75. Perzentil sowie die Richtung des verfügbaren CrUX-Verlaufs. Keine Lighthouse-Labordaten und keine erfundenen Scores.</p>

			<?php if ( $can_manage ) : ?>
				<form class="nexus-seo-cockpit__research-key-form" method="post" action="<?php echo esc_url( nexus_get_seo_cockpit_admin_action_url( 'nexus_seo_cockpit_research_save' ) ); ?>">
					<?php wp_nonce_field( 'nexus_seo_cockpit_research_save' ); ?>
					<?php if ( $uses_const ) : ?>
						<p class="nexus-seo-cockpit__status is-positive">API-Key kommt aus <code>NEXUS_CRUX_API_KEY</code> in der Runtime-Konfiguration.</p>
					<?php else : ?>
						<label for="nexus-crux-api-key"><strong>CrUX API-Key</strong></label>
						<div class="nexus-seo-cockpit__research-key-row">
							<input id="nexus-crux-api-key" type="password" name="crux_api_key" value="" autocomplete="new-password" placeholder="<?php echo '' !== $api_key ? esc_attr( 'Gespeichert – leer lassen zum Beibehalten' ) : esc_attr( 'Google Cloud API-Key' ); ?>">
							<button type="submit" class="button button-primary">Speichern</button>
							<?php if ( '' !== $api_key ) : ?>
								<button type="submit" class="button" name="clear_crux_api_key" value="1">Key entfernen</button>
							<?php endif; ?>
						</div>
						<p class="description">Der Key wird nicht im Repo gespeichert. Alternativ <code>define( 'NEXUS_CRUX_API_KEY', '…' );</code> in der Runtime-Konfiguration setzen.</p>
					<?php endif; ?>
				</form>
			<?php endif; ?>
		</section>

		<?php if ( '' !== $api_key ) : ?>
			<div class="nexus-seo-cockpit__research-stack">
				<?php nexus_render_seo_cockpit_crux_metric_group( 'Mobil', $phone, $phone_hist ); ?>
				<?php nexus_render_seo_cockpit_crux_metric_group( 'Desktop', $desktop, $desktop_hist ); ?>
			</div>
		<?php else : ?>
			<section class="nexus-seo-cockpit__panel nexus-seo-cockpit__panel--setup">
				<p class="nexus-seo-cockpit__eyebrow">Einmalige Einrichtung</p>
				<h2>CrUX API-Key hinterlegen</h2>
				<ol class="nexus-seo-cockpit__steps">
					<li>Im bestehenden Google-Cloud-Projekt die <strong>Chrome UX Report API</strong> aktivieren.</li>
					<li>Einen API-Key erstellen beziehungsweise einen vorhandenen Key dafür freigeben.</li>
					<li>Den Key oben speichern oder als <code>NEXUS_CRUX_API_KEY</code> in der Runtime-Konfiguration setzen.</li>
				</ol>
			</section>
		<?php endif; ?>

		<section class="nexus-seo-cockpit__panel nexus-seo-cockpit__research-providers">
			<div class="nexus-seo-cockpit__panel-head">
				<div>
					<p class="nexus-seo-cockpit__eyebrow">Provider-Roadmap</p>
					<h2>Der Research-Layer bleibt modular</h2>
				</div>
			</div>
			<div class="nexus-seo-cockpit__research-provider-grid">
				<article><strong>CrUX</strong><span class="nexus-seo-cockpit__status is-positive">angebunden</span><p>Core Web Vitals und TTFB aus realen Chrome-Nutzungsdaten.</p></article>
				<article><strong>Destatis GENESIS</strong><span class="nexus-seo-cockpit__status is-neutral">noch nicht angebunden</span><p>Gebäude-, Unternehmens- und Regionaldaten für eigene Marktanalysen.</p></article>
				<article><strong>Eurostat</strong><span class="nexus-seo-cockpit__status is-neutral">noch nicht angebunden</span><p>EU-Vergleiche zu Energie, Gebäuden und wirtschaftlichen Indikatoren.</p></article>
				<article><strong>Energy-Charts</strong><span class="nexus-seo-cockpit__status is-neutral">noch nicht angebunden</span><p>PV-Leistung, Erzeugung, Preise und Energiemix für Solar-Dossiers.</p></article>
			</div>
		</section>
	</div>
	<?php
}
