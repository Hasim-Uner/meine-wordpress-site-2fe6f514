<?php
/**
 * SEO Cockpit Research Intelligence: Eurostat provider.
 *
 * Pulls a small, cached set of public Eurostat Statistics API signals for
 * Germany-vs-EU renewable-energy comparisons. No API key is required.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the public Eurostat Statistics API base URL.
 *
 * @return string
 */
function nexus_get_seo_cockpit_eurostat_api_base_url() {
	return 'https://ec.europa.eu/eurostat/api/dissemination/statistics/1.0/data';
}

/**
 * Return the allowlisted Eurostat signals used by Research Intelligence.
 *
 * Dataset nrg_ind_ren exposes renewable-energy shares. V1 intentionally keeps
 * the scope small: total renewable share and renewable electricity share for
 * Germany and EU27_2020.
 *
 * @return array<string, array<string, string>>
 */
function nexus_get_seo_cockpit_eurostat_signals() {
	return [
		'de_total' => [
			'geo'     => 'DE',
			'nrg_bal' => 'REN',
		],
		'eu_total' => [
			'geo'     => 'EU27_2020',
			'nrg_bal' => 'REN',
		],
		'de_electricity' => [
			'geo'     => 'DE',
			'nrg_bal' => 'REN_ELC',
		],
		'eu_electricity' => [
			'geo'     => 'EU27_2020',
			'nrg_bal' => 'REN_ELC',
		],
	];
}

/**
 * Build one exact transient key for an allowlisted Eurostat signal.
 *
 * @param string $signal Signal key.
 * @return string
 */
function nexus_get_seo_cockpit_eurostat_cache_key( $signal ) {
	return 'nexus_eurostat_' . md5( sanitize_key( (string) $signal ) . '|nrg_ind_ren|v1' );
}

/**
 * Return one public Eurostat response for an allowlisted signal.
 *
 * @param string $signal Signal key.
 * @param bool   $force  Skip transient cache.
 * @return array<string, mixed>|WP_Error
 */
function nexus_get_seo_cockpit_eurostat_response( $signal, $force = false ) {
	$signals = nexus_get_seo_cockpit_eurostat_signals();
	$signal  = sanitize_key( (string) $signal );

	if ( ! isset( $signals[ $signal ] ) ) {
		return new WP_Error( 'nexus_eurostat_signal', 'Nicht freigegebenes Eurostat-Signal.' );
	}

	$cache_key = nexus_get_seo_cockpit_eurostat_cache_key( $signal );
	if ( ! $force ) {
		$cached = get_transient( $cache_key );
		if ( is_array( $cached ) ) {
			return $cached;
		}
	}

	$query = [
		'format'         => 'JSON',
		'lang'           => 'EN',
		'freq'           => 'A',
		'unit'           => 'PC',
		'nrg_bal'        => $signals[ $signal ]['nrg_bal'],
		'geo'            => $signals[ $signal ]['geo'],
		'lastTimePeriod' => 2,
	];

	$url = trailingslashit( nexus_get_seo_cockpit_eurostat_api_base_url() ) . 'nrg_ind_ren';
	$url = add_query_arg( $query, $url );

	$response = wp_remote_get(
		$url,
		[
			'timeout' => 15,
			'headers' => [
				'Accept'     => 'application/json',
				'User-Agent' => 'hasimuener.de SEO Cockpit Research Intelligence',
			],
		]
	);

	if ( is_wp_error( $response ) ) {
		return new WP_Error( 'nexus_eurostat_request', 'Eurostat konnte nicht erreicht werden: ' . $response->get_error_message() );
	}

	$status = (int) wp_remote_retrieve_response_code( $response );
	$body   = json_decode( (string) wp_remote_retrieve_body( $response ), true );
	$body   = is_array( $body ) ? $body : [];

	if ( $status < 200 || $status >= 300 ) {
		$message = sanitize_text_field( (string) ( $body['error']['message'] ?? $body['message'] ?? 'Unbekannte API-Antwort.' ) );
		return new WP_Error(
			'nexus_eurostat_http',
			sprintf( 'Eurostat antwortet mit HTTP %1$d: %2$s', $status, $message )
		);
	}

	if ( empty( $body['dimension']['time']['category']['index'] ) || ! isset( $body['value'] ) ) {
		return new WP_Error( 'nexus_eurostat_empty', 'Eurostat liefert für dieses Signal aktuell keine auswertbare Zeitreihe.' );
	}

	set_transient( $cache_key, $body, 12 * HOUR_IN_SECONDS );

	return $body;
}

/**
 * Delete only Eurostat Research Intelligence transients.
 *
 * @return void
 */
function nexus_delete_seo_cockpit_eurostat_cache() {
	foreach ( array_keys( nexus_get_seo_cockpit_eurostat_signals() ) as $signal ) {
		delete_transient( nexus_get_seo_cockpit_eurostat_cache_key( $signal ) );
	}
}

/**
 * Return time codes ordered by their JSON-stat position.
 *
 * @param array<string, mixed>|WP_Error $response Eurostat response.
 * @return array<int, string>
 */
function nexus_get_seo_cockpit_eurostat_time_codes( $response ) {
	if ( is_wp_error( $response ) || ! is_array( $response ) ) {
		return [];
	}

	$index = $response['dimension']['time']['category']['index'] ?? [];
	if ( ! is_array( $index ) ) {
		return [];
	}

	$positions = [];
	foreach ( $index as $code => $position ) {
		if ( is_numeric( $position ) ) {
			$positions[ (string) $code ] = (int) $position;
		}
	}

	asort( $positions, SORT_NUMERIC );

	return array_keys( $positions );
}

/**
 * Verify that the request really reduced every non-time dimension to one item.
 *
 * @param array<string, mixed>|WP_Error $response Eurostat response.
 * @return bool
 */
function nexus_seo_cockpit_eurostat_has_singleton_non_time_dimensions( $response ) {
	if ( is_wp_error( $response ) || ! is_array( $response ) ) {
		return false;
	}

	$ids   = is_array( $response['id'] ?? null ) ? $response['id'] : [];
	$sizes = is_array( $response['size'] ?? null ) ? $response['size'] : [];

	if ( empty( $ids ) || count( $ids ) !== count( $sizes ) ) {
		return false;
	}

	foreach ( $ids as $position => $id ) {
		if ( 'time' === (string) $id ) {
			continue;
		}
		if ( ! isset( $sizes[ $position ] ) || 1 !== (int) $sizes[ $position ] ) {
			return false;
		}
	}

	return true;
}

/**
 * Read one JSON-stat value by linear position.
 *
 * @param array<string, mixed>|WP_Error $response Eurostat response.
 * @param int                           $position Linear JSON-stat position.
 * @return float|null
 */
function nexus_get_seo_cockpit_eurostat_value_at( $response, $position ) {
	if ( is_wp_error( $response ) || ! is_array( $response ) ) {
		return null;
	}

	$values = $response['value'] ?? [];
	$value  = null;

	if ( is_array( $values ) ) {
		if ( array_key_exists( $position, $values ) ) {
			$value = $values[ $position ];
		} elseif ( array_key_exists( (string) $position, $values ) ) {
			$value = $values[ (string) $position ];
		}
	}

	return is_numeric( $value ) ? (float) $value : null;
}

/**
 * Convert one filtered two-period response into a compact signal.
 *
 * @param array<string, mixed>|WP_Error $response Eurostat response.
 * @return array<string, mixed>
 */
function nexus_get_seo_cockpit_eurostat_series_summary( $response ) {
	$empty = [
		'value'    => null,
		'previous' => null,
		'period'   => '',
		'delta_pp' => null,
		'updated'  => '',
	];

	if ( is_wp_error( $response ) || ! is_array( $response ) ) {
		return $empty;
	}

	if ( ! nexus_seo_cockpit_eurostat_has_singleton_non_time_dimensions( $response ) ) {
		return $empty;
	}

	$times = nexus_get_seo_cockpit_eurostat_time_codes( $response );
	if ( empty( $times ) ) {
		return $empty;
	}

	$rows = [];
	foreach ( $times as $position => $period ) {
		$value = nexus_get_seo_cockpit_eurostat_value_at( $response, (int) $position );
		if ( null !== $value ) {
			$rows[] = [
				'period' => sanitize_text_field( (string) $period ),
				'value'  => $value,
			];
		}
	}

	if ( empty( $rows ) ) {
		return $empty;
	}

	$latest   = $rows[ count( $rows ) - 1 ];
	$previous = count( $rows ) > 1 ? $rows[ count( $rows ) - 2 ] : null;

	return [
		'value'    => (float) $latest['value'],
		'previous' => is_array( $previous ) ? (float) $previous['value'] : null,
		'period'   => (string) $latest['period'],
		'delta_pp' => is_array( $previous ) ? (float) $latest['value'] - (float) $previous['value'] : null,
		'updated'  => sanitize_text_field( (string) ( $response['updated'] ?? '' ) ),
	];
}

/**
 * Build the compact Eurostat summary consumed by the Research UI.
 *
 * @param bool $force Skip provider caches.
 * @return array<string, mixed>
 */
function nexus_get_seo_cockpit_eurostat_summary( $force = false ) {
	$series = [];
	$errors = [];

	foreach ( array_keys( nexus_get_seo_cockpit_eurostat_signals() ) as $signal ) {
		$response = nexus_get_seo_cockpit_eurostat_response( $signal, $force );
		if ( is_wp_error( $response ) ) {
			$errors[ $signal ] = $response->get_error_message();
			$series[ $signal ] = nexus_get_seo_cockpit_eurostat_series_summary( $response );
			continue;
		}

		$series[ $signal ] = nexus_get_seo_cockpit_eurostat_series_summary( $response );
		if ( ! isset( $series[ $signal ]['value'] ) || ! is_numeric( $series[ $signal ]['value'] ) ) {
			$errors[ $signal ] = 'Eurostat-Antwort konnte nicht eindeutig auf die erwartete Zeitreihe reduziert werden.';
		}
	}

	$available = false;
	foreach ( $series as $item ) {
		if ( is_array( $item ) && isset( $item['value'] ) && is_numeric( $item['value'] ) ) {
			$available = true;
			break;
		}
	}

	return [
		'is_available'   => $available,
		'errors'         => $errors,
		'dataset'        => 'nrg_ind_ren',
		'de_total'       => $series['de_total'] ?? [],
		'eu_total'       => $series['eu_total'] ?? [],
		'de_electricity' => $series['de_electricity'] ?? [],
		'eu_electricity' => $series['eu_electricity'] ?? [],
	];
}

/**
 * Render one Eurostat metric card.
 *
 * @param string               $label Metric label.
 * @param array<string, mixed> $series Signal summary.
 * @param string               $context Short context line.
 * @return void
 */
function nexus_render_seo_cockpit_eurostat_metric_card( $label, $series, $context ) {
	$value  = isset( $series['value'] ) && is_numeric( $series['value'] ) ? (float) $series['value'] : null;
	$period = sanitize_text_field( (string) ( $series['period'] ?? '' ) );
	$delta  = isset( $series['delta_pp'] ) && is_numeric( $series['delta_pp'] ) ? (float) $series['delta_pp'] : null;
	$class  = null !== $delta ? ( $delta > 0 ? 'is-positive' : ( $delta < 0 ? 'is-negative' : '' ) ) : '';
	?>
	<article class="nexus-seo-cockpit__metric-card">
		<span class="nexus-seo-cockpit__metric-label"><?php echo esc_html( $label . ( '' !== $period ? ' · ' . $period : '' ) ); ?></span>
		<strong class="nexus-seo-cockpit__metric-value"><?php echo esc_html( null !== $value ? number_format_i18n( $value, 1 ) . ' %' : '—' ); ?></strong>
		<span class="nexus-seo-cockpit__research-trend <?php echo esc_attr( $class ); ?>">
			<?php
			if ( null !== $delta ) {
				echo esc_html( sprintf( '%1$s%2$s %-Punkte vs. Vorjahr', $delta > 0 ? '+' : '', number_format_i18n( $delta, 1 ) ) );
			} else {
				echo esc_html( 'kein Vorjahresvergleich' );
			}
			?>
		</span>
		<span class="nexus-seo-cockpit__research-trend"><?php echo esc_html( $context ); ?></span>
	</article>
	<?php
}

/**
 * Render the Eurostat provider panel.
 *
 * @param array<string, mixed> $summary Eurostat summary.
 * @param bool                 $can_manage Whether the current user may refresh provider caches.
 * @return void
 */
function nexus_render_seo_cockpit_eurostat_panel( $summary, $can_manage ) {
	$available = ! empty( $summary['is_available'] );
	$errors    = is_array( $summary['errors'] ?? null ) ? $summary['errors'] : [];
	?>
	<section class="nexus-seo-cockpit__panel nexus-seo-cockpit__panel--primary nexus-seo-cockpit__research-energy">
		<div class="nexus-seo-cockpit__panel-head">
			<div>
				<p class="nexus-seo-cockpit__eyebrow">Datenlayer 04 · EU-Vergleich</p>
				<h2>Eurostat</h2>
			</div>
			<div class="nexus-seo-cockpit__research-provider-actions">
				<span class="nexus-seo-cockpit__status-dot <?php echo $available ? 'is-connected' : 'is-warning'; ?>"><?php echo esc_html( $available ? 'API erreichbar' : 'Eurostat prüfen' ); ?></span>
				<?php if ( $can_manage ) : ?>
					<form method="post" action="<?php echo esc_url( nexus_get_seo_cockpit_admin_action_url( 'nexus_seo_cockpit_eurostat_refresh' ) ); ?>">
						<?php wp_nonce_field( 'nexus_seo_cockpit_eurostat_refresh' ); ?>
						<button type="submit" class="button">Eurostat neu laden</button>
					</form>
				<?php endif; ?>
			</div>
		</div>

		<p class="nexus-seo-cockpit__hint">Kostenlose EU-Primärdaten ohne API-Key. V1 vergleicht Deutschland mit EU27 bei erneuerbarer Energie insgesamt und beim Anteil erneuerbaren Stroms. Die Werte sind Kontextsignale, kein SEO- oder Lead-Score.</p>

		<?php if ( $available ) : ?>
			<div class="nexus-seo-cockpit__metrics nexus-seo-cockpit__research-energy-metrics">
				<?php nexus_render_seo_cockpit_eurostat_metric_card( 'Deutschland · Erneuerbare gesamt', (array) ( $summary['de_total'] ?? [] ), 'Anteil am Bruttoendenergieverbrauch' ); ?>
				<?php nexus_render_seo_cockpit_eurostat_metric_card( 'EU27 · Erneuerbare gesamt', (array) ( $summary['eu_total'] ?? [] ), 'Anteil am Bruttoendenergieverbrauch' ); ?>
				<?php nexus_render_seo_cockpit_eurostat_metric_card( 'Deutschland · Erneuerbarer Strom', (array) ( $summary['de_electricity'] ?? [] ), 'Anteil am Bruttostromverbrauch' ); ?>
				<?php nexus_render_seo_cockpit_eurostat_metric_card( 'EU27 · Erneuerbarer Strom', (array) ( $summary['eu_electricity'] ?? [] ), 'Anteil am Bruttostromverbrauch' ); ?>
			</div>
		<?php endif; ?>

		<div class="nexus-seo-cockpit__research-source-note">
			<strong>Quelle:</strong> Eurostat · Statistics API · Datensatz <code><?php echo esc_html( (string) ( $summary['dataset'] ?? 'nrg_ind_ren' ) ); ?></code>
			<span>· kostenloser öffentlicher REST-Zugang · JSON-stat 2.0</span>
		</div>

		<?php if ( ! empty( $errors ) ) : ?>
			<details class="nexus-seo-cockpit__research-errors">
				<summary>Teilweise fehlende Eurostat-Daten</summary>
				<ul>
					<?php foreach ( $errors as $key => $message ) : ?>
						<li><strong><?php echo esc_html( (string) $key ); ?>:</strong> <?php echo esc_html( (string) $message ); ?></li>
					<?php endforeach; ?>
				</ul>
			</details>
		<?php endif; ?>
	</section>
	<?php
}

/**
 * Clear Eurostat caches and return to the Research page.
 *
 * @return void
 */
function nexus_handle_seo_cockpit_eurostat_refresh() {
	if ( ! function_exists( 'nexus_current_user_can_manage_seo_cockpit' ) || ! nexus_current_user_can_manage_seo_cockpit() ) {
		wp_die( 'Nicht erlaubt.' );
	}

	check_admin_referer( 'nexus_seo_cockpit_eurostat_refresh' );
	nexus_delete_seo_cockpit_eurostat_cache();

	$slug = function_exists( 'nexus_get_seo_cockpit_research_slug' ) ? nexus_get_seo_cockpit_research_slug() : 'nexus-seo-cockpit-research';
	wp_safe_redirect( admin_url( 'admin.php?page=' . $slug . '&research_notice=eurostat_refresh' ) );
	exit;
}
add_action( 'admin_post_nexus_seo_cockpit_eurostat_refresh', 'nexus_handle_seo_cockpit_eurostat_refresh' );
