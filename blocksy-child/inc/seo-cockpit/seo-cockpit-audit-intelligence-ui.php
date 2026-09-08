<?php
/**
 * SEO Cockpit Site Audit intelligence UI extension.
 *
 * Keeps the deterministic technical audit intact and adds separate Google,
 * Search Performance and information-architecture views on the same screen.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Whether the current admin request is the Site Audit screen. */
function nexus_seo_audit_intelligence_ui_is_screen() {
	return is_admin()
		&& isset( $_GET['page'] )
		&& 'nexus-seo-cockpit-site-audit' === sanitize_key( (string) wp_unslash( $_GET['page'] ) );
}

/** Small layout additions reusing the existing Site Audit visual system. */
function nexus_seo_audit_intelligence_ui_assets() {
	if ( ! nexus_seo_audit_intelligence_ui_is_screen() ) {
		return;
	}

	$css = '
		.nexus-audit-intel{margin-top:20px}
		.nexus-audit-intel__grid{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:12px;margin:16px 0 22px}
		.nexus-audit-intel__metric{background:#fff;border:1px solid #e2e4e7;border-radius:10px;padding:16px;min-width:0}
		.nexus-audit-intel__metric span,.nexus-audit-intel__metric small{display:block;color:#646970}
		.nexus-audit-intel__metric strong{display:block;font-size:24px;line-height:1.2;margin:5px 0}
		.nexus-audit-intel__note{color:#646970;max-width:900px}
		.nexus-audit-intel__status{display:inline-flex;align-items:center;padding:3px 8px;border-radius:999px;background:#f0f0f1;font-size:12px;font-weight:600}
		.nexus-audit-intel__status--ok{background:#edfaef;color:#116329}
		.nexus-audit-intel__status--warn{background:#fff8e5;color:#8a4b00}
		.nexus-audit-intel__url{max-width:420px;word-break:break-word}
		.nexus-audit-intel__table td,.nexus-audit-intel__table th{vertical-align:top}
		.nexus-audit-intel__stack{display:grid;gap:18px}
		@media(max-width:1200px){.nexus-audit-intel__grid{grid-template-columns:repeat(3,minmax(0,1fr))}}
		@media(max-width:782px){.nexus-audit-intel__grid{grid-template-columns:1fr 1fr}}
	';

	wp_add_inline_style( 'nexus-seo-cockpit-audit', $css );
}
add_action( 'admin_enqueue_scripts', 'nexus_seo_audit_intelligence_ui_assets', 35 );

/** Format a metric number. */
function nexus_seo_audit_intelligence_ui_number( $value, $decimals = 0 ) {
	return number_format_i18n( (float) $value, absint( $decimals ) );
}

/** Format a Search Console CTR value. */
function nexus_seo_audit_intelligence_ui_ctr( $value ) {
	return number_format_i18n( (float) $value * 100, 1 ) . '%';
}

/** Format a signed delta, preserving unavailable baselines. */
function nexus_seo_audit_intelligence_ui_delta( $value, $suffix = '%' ) {
	if ( null === $value ) {
		return '—';
	}

	$value = (float) $value;
	return ( $value > 0 ? '+' : '' ) . number_format_i18n( $value, 1 ) . $suffix;
}

/** German severity label for intelligence findings. */
function nexus_seo_audit_intelligence_ui_severity( $severity ) {
	$labels = [
		'critical' => 'Kritisch',
		'high'     => 'Hoch',
		'medium'   => 'Mittel',
		'low'      => 'Niedrig',
		'info'     => 'Hinweis',
	];

	return $labels[ sanitize_key( (string) $severity ) ] ?? 'Hinweis';
}

/** Render one compact metric card. */
function nexus_seo_audit_intelligence_ui_metric( $label, $value, $note = '' ) {
	?>
	<div class="nexus-audit-intel__metric">
		<span><?php echo esc_html( (string) $label ); ?></span>
		<strong><?php echo esc_html( (string) $value ); ?></strong>
		<?php if ( '' !== (string) $note ) : ?><small><?php echo esc_html( (string) $note ); ?></small><?php endif; ?>
	</div>
	<?php
}

/** Render non-score intelligence findings. */
function nexus_seo_audit_intelligence_ui_findings( $state ) {
	$findings = function_exists( 'nexus_seo_audit_get_intelligence_findings' )
		? nexus_seo_audit_get_intelligence_findings( $state )
		: [];
	?>
	<section class="nexus-audit__panel">
		<div class="nexus-audit__section-head">
			<div><p class="nexus-audit__eyebrow">SEO Intelligence</p><h2>Google-, Struktur- & Sitemap-Signale</h2></div>
			<p>Diagnosen ohne künstliche Score-Abzüge: Sie priorisieren Arbeit, statt den technischen 100er-Score zu verwässern.</p>
		</div>
		<div class="nexus-audit__table-wrap">
			<table class="widefat striped nexus-audit__table nexus-audit-intel__table">
				<thead><tr><th>Priorität</th><th>Signal</th><th>URL</th><th>Empfehlung</th></tr></thead>
				<tbody>
				<?php if ( empty( $findings ) ) : ?>
					<tr><td colspan="4">Keine zusätzlichen Google-, Struktur- oder Sitemap-Befunde.</td></tr>
				<?php else : ?>
					<?php foreach ( array_slice( $findings, 0, 120 ) as $finding ) : if ( ! is_array( $finding ) ) { continue; } ?>
					<tr>
						<td><span class="nexus-audit__severity nexus-audit__severity--<?php echo esc_attr( sanitize_key( (string) ( $finding['severity'] ?? 'info' ) ) ); ?>"><?php echo esc_html( nexus_seo_audit_intelligence_ui_severity( $finding['severity'] ?? 'info' ) ); ?></span></td>
						<td><strong><?php echo esc_html( (string) ( $finding['title'] ?? '' ) ); ?></strong><small class="nexus-audit__code"><?php echo esc_html( strtoupper( (string) ( $finding['code'] ?? '' ) ) ); ?></small></td>
						<td class="nexus-audit-intel__url"><a href="<?php echo esc_url( (string) ( $finding['url'] ?? '' ) ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( (string) ( $finding['url'] ?? '' ) ); ?></a></td>
						<td><?php echo esc_html( (string) ( $finding['recommendation'] ?? '' ) ); ?></td>
					</tr>
					<?php endforeach; ?>
				<?php endif; ?>
				</tbody>
			</table>
		</div>
	</section>
	<?php
}

/** Render the additional intelligence panels after the existing Site Audit UI. */
function nexus_seo_audit_intelligence_ui_render() {
	if ( ! nexus_seo_audit_intelligence_ui_is_screen() || ! function_exists( 'nexus_seo_audit_get_state' ) ) {
		return;
	}

	$state = nexus_seo_audit_get_state();
	if ( 'completed' !== (string) ( $state['status'] ?? '' ) ) {
		return;
	}

	$intel       = is_array( $state['intelligence'] ?? null ) ? $state['intelligence'] : [];
	$architecture= is_array( $intel['architecture'] ?? null ) ? $intel['architecture'] : [];
	$google      = is_array( $intel['google_index_summary'] ?? null ) ? $intel['google_index_summary'] : [];
	$site_checks = is_array( $intel['site_checks'] ?? null ) ? $intel['site_checks'] : [];
	$robots      = is_array( $site_checks['robots'] ?? null ) ? $site_checks['robots'] : [];
	$sitemap     = is_array( $site_checks['sitemap'] ?? null ) ? $site_checks['sitemap'] : [];
	$performance = function_exists( 'nexus_seo_audit_get_search_performance' ) ? nexus_seo_audit_get_search_performance( $state ) : [];
	?>
	<div class="wrap nexus-audit nexus-audit-intel">
		<section class="nexus-audit__panel">
			<div class="nexus-audit__section-head">
				<div><p class="nexus-audit__eyebrow">Google Index Health</p><h2>Was Google tatsächlich kennt</h2></div>
				<p class="nexus-audit-intel__note">URL Inspection prüft Googles indexierte Version und ergänzt den technischen Crawl. Sie ist kein Live-Rendering-Test.</p>
			</div>
			<?php if ( empty( $google ) ) : ?>
				<p>Für diesen Audit-Lauf liegen noch keine Google-Indexdaten vor. Nach Deployment einen neuen Audit starten.</p>
			<?php else : ?>
				<div class="nexus-audit-intel__grid">
					<?php nexus_seo_audit_intelligence_ui_metric( 'Indexierbar', absint( $google['indexable'] ?? 0 ), 'Audit-Scope' ); ?>
					<?php nexus_seo_audit_intelligence_ui_metric( 'Inspected', absint( $google['inspected'] ?? 0 ), 'Search Console' ); ?>
					<?php nexus_seo_audit_intelligence_ui_metric( 'PASS', absint( $google['passed'] ?? 0 ), 'Google Verdict' ); ?>
					<?php nexus_seo_audit_intelligence_ui_metric( 'Canonical abweichend', absint( $google['canonical_mismatch'] ?? 0 ), 'Google vs. Website' ); ?>
					<?php nexus_seo_audit_intelligence_ui_metric( 'Fetch-Probleme', absint( $google['fetch_problems'] ?? 0 ), 'Googlebot' ); ?>
				</div>
			<?php endif; ?>
		</section>

		<section class="nexus-audit__panel">
			<div class="nexus-audit__section-head">
				<div><p class="nexus-audit__eyebrow">Informationsarchitektur</p><h2>Crawl-Tiefe & interne Linkstärke</h2></div>
				<p class="nexus-audit-intel__note">„Linkwert“ ist eine relative interne Graph-Heuristik (0–100), kein Google PageRank.</p>
			</div>
			<div class="nexus-audit-intel__grid">
				<?php nexus_seo_audit_intelligence_ui_metric( 'Indexierbare Knoten', absint( $architecture['active_urls'] ?? 0 ), 'im Linkgraph' ); ?>
				<?php nexus_seo_audit_intelligence_ui_metric( 'Max. Tiefe', absint( $architecture['max_depth'] ?? 0 ), 'Klicks ab Startseite' ); ?>
				<?php nexus_seo_audit_intelligence_ui_metric( 'Ø Tiefe', nexus_seo_audit_intelligence_ui_number( (float) ( $architecture['average_depth'] ?? 0 ), 1 ), 'Klicks' ); ?>
				<?php nexus_seo_audit_intelligence_ui_metric( 'Unerreichbar', absint( $architecture['unreachable_urls'] ?? 0 ), 'vom Startseiten-Graph' ); ?>
				<?php nexus_seo_audit_intelligence_ui_metric( 'Redirect-Linkquellen', absint( $architecture['redirect_link_sources'] ?? 0 ), 'intern bereinigen' ); ?>
			</div>
		</section>

		<section class="nexus-audit__panel">
			<div class="nexus-audit__section-head">
				<div><p class="nexus-audit__eyebrow">Crawl-Steuerung</p><h2>robots.txt & XML-Sitemap</h2></div>
			</div>
			<div class="nexus-audit-intel__grid">
				<?php nexus_seo_audit_intelligence_ui_metric( 'robots.txt', isset( $robots['status'] ) ? (string) $robots['status'] : '—', 'HTTP-Status' ); ?>
				<?php nexus_seo_audit_intelligence_ui_metric( 'Sitemap-URLs', absint( $sitemap['url_count'] ?? 0 ), 'entdeckt' ); ?>
				<?php nexus_seo_audit_intelligence_ui_metric( 'Indexierbar fehlt', absint( $sitemap['missing_indexable'] ?? 0 ), 'in Sitemap' ); ?>
				<?php nexus_seo_audit_intelligence_ui_metric( 'Nicht indexierbar', absint( $sitemap['nonindexable_present'] ?? 0 ), 'trotzdem in Sitemap' ); ?>
				<?php nexus_seo_audit_intelligence_ui_metric( 'Sitemap-Fehler', count( (array) ( $sitemap['errors'] ?? [] ) ), 'Abruf/XML' ); ?>
			</div>
		</section>

		<section class="nexus-audit__panel">
			<div class="nexus-audit__section-head">
				<div><p class="nexus-audit__eyebrow">Search Performance</p><h2>28 Tage vs. vorherige 28 Tage</h2></div>
				<p class="nexus-audit-intel__note">Die Werte kommen aus der bereits verbundenen Search Console. Query-/Page-Zeilen sind GSC-Topdaten und nicht garantiert vollständig.</p>
			</div>
			<?php if ( 'ok' !== (string) ( $performance['status'] ?? '' ) ) : ?>
				<p><?php echo esc_html( (string) ( $performance['message'] ?? 'Search Performance nicht verfügbar.' ) ); ?></p>
			<?php else : ?>
				<?php
				$overview = is_array( $performance['overview'] ?? null ) ? $performance['overview'] : [];
				$current  = is_array( $overview['current'] ?? null ) ? $overview['current'] : [];
				?>
				<div class="nexus-audit-intel__grid">
					<?php nexus_seo_audit_intelligence_ui_metric( 'Klicks', nexus_seo_audit_intelligence_ui_number( $current['clicks'] ?? 0 ), nexus_seo_audit_intelligence_ui_delta( $overview['click_change_pct'] ?? null ) . ' vs. vorher' ); ?>
					<?php nexus_seo_audit_intelligence_ui_metric( 'Impressionen', nexus_seo_audit_intelligence_ui_number( $current['impressions'] ?? 0 ), nexus_seo_audit_intelligence_ui_delta( $overview['impression_change_pct'] ?? null ) . ' vs. vorher' ); ?>
					<?php nexus_seo_audit_intelligence_ui_metric( 'CTR', nexus_seo_audit_intelligence_ui_ctr( $current['ctr'] ?? 0 ), nexus_seo_audit_intelligence_ui_delta( $overview['ctr_change_pp'] ?? null, ' pp' ) ); ?>
					<?php nexus_seo_audit_intelligence_ui_metric( 'Ø Position', nexus_seo_audit_intelligence_ui_number( $current['position'] ?? 0, 1 ), nexus_seo_audit_intelligence_ui_delta( $overview['position_change'] ?? null, '' ) . ' Veränderung' ); ?>
					<?php nexus_seo_audit_intelligence_ui_metric( 'Chancen', count( (array) ( $performance['opportunities'] ?? [] ) ), 'priorisiert' ); ?>
				</div>

				<div class="nexus-audit__section-head"><div><p class="nexus-audit__eyebrow">Priorisierung</p><h2>Search-Chancen & Verluste</h2></div></div>
				<div class="nexus-audit__table-wrap">
					<table class="widefat striped nexus-audit__table nexus-audit-intel__table">
						<thead><tr><th>Signal</th><th>URL</th><th>Warum</th></tr></thead>
						<tbody>
						<?php if ( empty( $performance['opportunities'] ) ) : ?>
							<tr><td colspan="3">Keine auffällige Search-Performance-Chance nach den aktuellen Regeln.</td></tr>
						<?php else : ?>
							<?php foreach ( array_slice( (array) $performance['opportunities'], 0, 20 ) as $opportunity ) : if ( ! is_array( $opportunity ) ) { continue; } ?>
							<tr>
								<td><span class="nexus-audit__severity nexus-audit__severity--<?php echo esc_attr( sanitize_key( (string) ( $opportunity['severity'] ?? 'info' ) ) ); ?>"><?php echo esc_html( (string) ( $opportunity['title'] ?? '' ) ); ?></span></td>
								<td class="nexus-audit-intel__url"><a href="<?php echo esc_url( (string) ( $opportunity['url'] ?? '' ) ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( (string) ( $opportunity['url'] ?? '' ) ); ?></a></td>
								<td><?php echo esc_html( (string) ( $opportunity['reason'] ?? '' ) ); ?></td>
							</tr>
							<?php endforeach; ?>
						<?php endif; ?>
						</tbody>
					</table>
				</div>

				<?php if ( ! empty( $performance['cannibalization'] ) ) : ?>
					<div class="nexus-audit__section-head"><div><p class="nexus-audit__eyebrow">Query-Überschneidung</p><h2>Kannibalisierungs-Kandidaten</h2></div><p>Mehrere URLs für dieselbe Query sind nur ein Prüfhinweis, nicht automatisch ein Fehler.</p></div>
					<div class="nexus-audit__table-wrap">
						<table class="widefat striped nexus-audit__table nexus-audit-intel__table">
							<thead><tr><th>Query</th><th>URLs</th><th>Impressionen</th></tr></thead>
							<tbody>
							<?php foreach ( array_slice( (array) $performance['cannibalization'], 0, 12 ) as $candidate ) : if ( ! is_array( $candidate ) ) { continue; } ?>
							<tr>
								<td><strong><?php echo esc_html( (string) ( $candidate['query'] ?? '' ) ); ?></strong></td>
								<td class="nexus-audit-intel__url">
									<?php foreach ( array_keys( (array) ( $candidate['pages'] ?? [] ) ) as $candidate_url ) : ?>
										<a href="<?php echo esc_url( $candidate_url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $candidate_url ); ?></a><br>
									<?php endforeach; ?>
								</td>
								<td><?php echo esc_html( nexus_seo_audit_intelligence_ui_number( $candidate['impressions'] ?? 0 ) ); ?></td>
							</tr>
							<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				<?php endif; ?>
			<?php endif; ?>
		</section>

		<?php nexus_seo_audit_intelligence_ui_findings( $state ); ?>

		<section class="nexus-audit__panel">
			<div class="nexus-audit__section-head">
				<div><p class="nexus-audit__eyebrow">URL Intelligence</p><h2>Technik + Architektur + Google + Nachfrage</h2></div>
			</div>
			<div class="nexus-audit__table-wrap">
				<table class="widefat striped nexus-audit__table nexus-audit-intel__table">
					<thead><tr><th>URL</th><th>Tiefe</th><th>Inlinks</th><th>Linkwert</th><th>Google</th><th>Klicks</th><th>Impr.</th><th>Pos.</th><th>Top Query</th></tr></thead>
					<tbody>
					<?php
					$perf_pages = is_array( $performance['pages'] ?? null ) ? $performance['pages'] : [];
					foreach ( (array) ( $state['pages'] ?? [] ) as $page ) :
						if ( ! is_array( $page ) || ! function_exists( 'nexus_seo_audit_page_is_indexable_scope' ) || ! nexus_seo_audit_page_is_indexable_scope( $page ) ) { continue; }
						$url_key = nexus_seo_audit_normalize_url( (string) ( $page['url'] ?? '' ) );
						$arch    = is_array( $page['architecture'] ?? null ) ? $page['architecture'] : [];
						$perf    = is_array( $perf_pages[ $url_key ] ?? null ) ? $perf_pages[ $url_key ] : [];
						$current = is_array( $perf['current'] ?? null ) ? $perf['current'] : [];
						$queries = is_array( $perf['queries'] ?? null ) ? $perf['queries'] : [];
						$inspect = is_array( $page['google_index'] ?? null ) ? $page['google_index'] : [];
						$verdict = strtoupper( (string) ( $inspect['verdict'] ?? '' ) );
					?>
					<tr>
						<td class="nexus-audit-intel__url"><a href="<?php echo esc_url( (string) ( $page['url'] ?? '' ) ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( (string) ( $page['url'] ?? '' ) ); ?></a></td>
						<td><?php echo esc_html( array_key_exists( 'crawl_depth', $arch ) && null !== $arch['crawl_depth'] ? (string) absint( $arch['crawl_depth'] ) : '—' ); ?></td>
						<td><?php echo esc_html( isset( $arch['incoming_sources'] ) ? (string) absint( $arch['incoming_sources'] ) : '—' ); ?></td>
						<td><?php echo esc_html( isset( $arch['internal_authority'] ) ? nexus_seo_audit_intelligence_ui_number( $arch['internal_authority'], 1 ) : '—' ); ?></td>
						<td><span class="nexus-audit-intel__status <?php echo 'PASS' === $verdict ? 'nexus-audit-intel__status--ok' : ( '' !== $verdict ? 'nexus-audit-intel__status--warn' : '' ); ?>"><?php echo esc_html( 'PASS' === $verdict ? 'PASS' : ( (string) ( $inspect['coverage_state'] ?? '' ) ?: '—' ) ); ?></span></td>
						<td><?php echo esc_html( isset( $current['clicks'] ) ? nexus_seo_audit_intelligence_ui_number( $current['clicks'] ) : '—' ); ?></td>
						<td><?php echo esc_html( isset( $current['impressions'] ) ? nexus_seo_audit_intelligence_ui_number( $current['impressions'] ) : '—' ); ?></td>
						<td><?php echo esc_html( ! empty( $current['position'] ) ? nexus_seo_audit_intelligence_ui_number( $current['position'], 1 ) : '—' ); ?></td>
						<td><?php echo esc_html( (string) ( $queries[0]['query'] ?? '—' ) ); ?></td>
					</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</section>
	</div>
	<?php
}
add_action( 'admin_footer', 'nexus_seo_audit_intelligence_ui_render', 20 );
