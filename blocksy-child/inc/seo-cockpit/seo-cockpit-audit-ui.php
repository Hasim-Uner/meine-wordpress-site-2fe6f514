<?php
/**
 * SEO Cockpit Site Audit admin UI.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function nexus_register_seo_audit_page() {
	if ( ! function_exists( 'nexus_get_seo_cockpit_menu_slug' ) || ! function_exists( 'nexus_get_seo_cockpit_view_cap' ) ) {
		return;
	}
	add_submenu_page(
		nexus_get_seo_cockpit_menu_slug(),
		'SEO Site Audit',
		'Site Audit',
		nexus_get_seo_cockpit_view_cap(),
		'nexus-seo-cockpit-site-audit',
		'nexus_render_seo_audit_page'
	);
}
add_action( 'admin_menu', 'nexus_register_seo_audit_page', 45 );

function nexus_enqueue_seo_audit_assets( $hook ) {
	if ( false === strpos( (string) $hook, 'nexus-seo-cockpit-site-audit' ) ) {
		return;
	}
	$path = get_stylesheet_directory() . '/assets/css/seo-cockpit-audit.css';
	if ( file_exists( $path ) ) {
		wp_enqueue_style( 'nexus-seo-cockpit-audit', get_stylesheet_directory_uri() . '/assets/css/seo-cockpit-audit.css', [ 'nexus-seo-cockpit-admin' ], filemtime( $path ) );
	}
}
add_action( 'admin_enqueue_scripts', 'nexus_enqueue_seo_audit_assets', 30 );

function nexus_seo_audit_severity_label( $severity ) {
	$labels = [ 'critical' => 'Kritisch', 'high' => 'Hoch', 'medium' => 'Mittel', 'low' => 'Niedrig', 'info' => 'Hinweis' ];
	return $labels[ sanitize_key( $severity ) ] ?? ucfirst( (string) $severity );
}

function nexus_seo_audit_actionable_count( $page ) {
	$count = 0;
	foreach ( (array) ( $page['issues'] ?? [] ) as $issue ) {
		if ( is_array( $issue ) && 'info' !== ( $issue['severity'] ?? 'info' ) ) {
			$count++;
		}
	}
	return $count;
}

function nexus_render_seo_audit_page() {
	if ( ! function_exists( 'nexus_current_user_can_view_seo_cockpit' ) || ! nexus_current_user_can_view_seo_cockpit() ) {
		wp_die( esc_html__( 'Nicht erlaubt.', 'blocksy-child' ) );
	}
	$state      = nexus_seo_audit_get_state();
	$status     = sanitize_key( (string) ( $state['status'] ?? 'idle' ) );
	$can_manage = function_exists( 'nexus_current_user_can_manage_seo_cockpit' ) && nexus_current_user_can_manage_seo_cockpit();
	$history    = get_option( NEXUS_SEO_AUDIT_HISTORY_OPTION, [] );
	$history    = is_array( $history ) ? $history : [];
	$categories = nexus_seo_audit_categories();
	?>
	<div class="wrap nexus-audit">
		<div class="nexus-audit__header">
			<div>
				<p class="nexus-audit__eyebrow">SEO Cockpit</p>
				<h1>Site Audit</h1>
				<p class="nexus-audit__lead">Technische und OnPage-Prüfung mit nachvollziehbaren Regeln statt Blackbox-Score.</p>
			</div>
			<?php if ( $can_manage ) : ?>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<input type="hidden" name="action" value="nexus_seo_audit_start">
					<?php wp_nonce_field( 'nexus_seo_audit_start' ); ?>
					<?php submit_button( in_array( $status, [ 'queued', 'running' ], true ) ? 'Audit läuft …' : ( 'completed' === $status ? 'Erneut prüfen' : 'Audit starten' ), 'primary', 'submit', false, in_array( $status, [ 'queued', 'running' ], true ) ? [ 'disabled' => 'disabled' ] : [] ); ?>
				</form>
			<?php endif; ?>
		</div>

		<?php if ( isset( $_GET['audit_notice'] ) && 'started' === sanitize_key( wp_unslash( $_GET['audit_notice'] ) ) ) : ?>
			<div class="notice notice-success is-dismissible"><p>Audit gestartet. Die URLs werden in kleinen Hintergrund-Batches geprüft.</p></div>
		<?php elseif ( isset( $_GET['audit_notice'] ) && 'already_running' === sanitize_key( wp_unslash( $_GET['audit_notice'] ) ) ) : ?>
			<div class="notice notice-warning"><p>Es läuft bereits ein Site Audit.</p></div>
		<?php endif; ?>

		<?php if ( in_array( $status, [ 'queued', 'running' ], true ) ) : ?>
			<?php $total = max( 1, absint( $state['total_urls'] ?? 0 ) ); $done = min( $total, absint( $state['processed_urls'] ?? 0 ) ); $progress = (int) round( $done / $total * 100 ); ?>
			<section class="nexus-audit__panel nexus-audit__progress-card">
				<div class="nexus-audit__progress-copy"><div><span class="nexus-audit__status">läuft</span><h2>Website wird geprüft</h2><p><?php echo esc_html( (string) $done ); ?> von <?php echo esc_html( (string) $total ); ?> URLs verarbeitet.</p></div><strong><?php echo esc_html( (string) $progress ); ?>%</strong></div>
				<div class="nexus-audit__progress"><span style="width:<?php echo esc_attr( (string) $progress ); ?>%"></span></div>
			</section>
			<script>window.setTimeout(function(){window.location.reload();},5000);</script>
		<?php elseif ( 'failed' === $status ) : ?>
			<div class="notice notice-error"><p>Audit abgebrochen: <?php echo esc_html( (string) ( $state['error_message'] ?? 'Unbekannter Fehler' ) ); ?></p></div>
		<?php elseif ( 'completed' !== $status ) : ?>
			<section class="nexus-audit__panel nexus-audit__empty"><h2>Noch kein Audit vorhanden</h2><p>Der erste Lauf prüft veröffentlichte WordPress-URLs. Core Web Vitals werden in V1 bewusst nicht in den URL-Score gerechnet, bis reale CrUX-Daten sauber mit diesem Layer verbunden sind.</p></section>
		<?php else : ?>
			<?php $score = absint( $state['health_score'] ?? 0 ); $counts = (array) ( $state['severity_counts'] ?? [] ); $issues = (array) ( $state['issues'] ?? [] ); $pages = (array) ( $state['pages'] ?? [] ); $cat = (array) ( $state['category_scores'] ?? [] ); ?>
			<section class="nexus-audit__summary">
				<div class="nexus-audit__score-card nexus-audit__panel"><div class="nexus-audit__score-ring"><strong><?php echo esc_html( (string) $score ); ?></strong><span>/ 100</span></div><div><p class="nexus-audit__eyebrow">SEO Health</p><h2><?php echo esc_html( $score >= 90 ? 'Sehr sauber' : ( $score >= 75 ? 'Solide Basis' : ( $score >= 55 ? 'Optimierungsbedarf' : 'Kritischer Zustand' ) ) ); ?></h2><p>Letzter Lauf: <?php echo esc_html( (string) ( $state['finished_at'] ?? '' ) ); ?></p></div></div>
				<div class="nexus-audit__metric nexus-audit__panel"><span>URLs</span><strong><?php echo esc_html( (string) count( $pages ) ); ?></strong><small>geprüft</small></div>
				<div class="nexus-audit__metric nexus-audit__panel"><span>Kritisch</span><strong><?php echo esc_html( (string) absint( $counts['critical'] ?? 0 ) ); ?></strong><small>sofort prüfen</small></div>
				<div class="nexus-audit__metric nexus-audit__panel"><span>Hoch</span><strong><?php echo esc_html( (string) absint( $counts['high'] ?? 0 ) ); ?></strong><small>hohe Priorität</small></div>
				<div class="nexus-audit__metric nexus-audit__panel"><span>Befunde</span><strong><?php echo esc_html( (string) count( $issues ) ); ?></strong><small>inkl. Hinweise</small></div>
			</section>

			<section class="nexus-audit__panel">
				<div class="nexus-audit__section-head"><div><p class="nexus-audit__eyebrow">Score-Modell</p><h2>Bereiche</h2></div><p>Nicht gemessene Daten werden nicht als Null gewertet.</p></div>
				<div class="nexus-audit__categories">
					<?php foreach ( $categories as $key => $config ) : $value = array_key_exists( $key, $cat ) ? $cat[ $key ] : null; ?>
						<div class="nexus-audit__category"><div><strong><?php echo esc_html( (string) $config['label'] ); ?></strong><small>Gewicht <?php echo esc_html( (string) absint( $config['weight'] ) ); ?></small></div><?php if ( null === $value ) : ?><span class="nexus-audit__not-measured">nicht bewertet</span><?php else : ?><strong class="nexus-audit__category-score"><?php echo esc_html( (string) absint( $value ) ); ?></strong><?php endif; ?></div>
					<?php endforeach; ?>
				</div>
			</section>

			<section class="nexus-audit__panel">
				<div class="nexus-audit__section-head"><div><p class="nexus-audit__eyebrow">Priorisierte Befunde</p><h2>Fehler & Empfehlungen</h2></div></div>
				<div class="nexus-audit__table-wrap"><table class="widefat striped nexus-audit__table"><thead><tr><th>Priorität</th><th>Problem</th><th>URL</th><th>Empfehlung</th></tr></thead><tbody>
				<?php foreach ( array_slice( $issues, 0, 150 ) as $issue ) : if ( ! is_array( $issue ) ) { continue; } ?>
					<tr><td><span class="nexus-audit__severity nexus-audit__severity--<?php echo esc_attr( sanitize_key( (string) ( $issue['severity'] ?? 'info' ) ) ); ?>"><?php echo esc_html( nexus_seo_audit_severity_label( (string) ( $issue['severity'] ?? 'info' ) ) ); ?></span></td><td><strong><?php echo esc_html( (string) ( $issue['title'] ?? '' ) ); ?></strong><small class="nexus-audit__code"><?php echo esc_html( strtoupper( (string) ( $issue['code'] ?? '' ) ) ); ?></small></td><td class="nexus-audit__url"><a href="<?php echo esc_url( (string) ( $issue['url'] ?? '' ) ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( (string) ( $issue['url'] ?? '' ) ); ?></a></td><td><?php echo esc_html( (string) ( $issue['recommendation'] ?? '' ) ); ?></td></tr>
				<?php endforeach; ?>
				</tbody></table></div>
			</section>

			<section class="nexus-audit__panel">
				<div class="nexus-audit__section-head"><div><p class="nexus-audit__eyebrow">URL-Ebene</p><h2>Alle geprüften Seiten</h2></div></div>
				<div class="nexus-audit__table-wrap"><table class="widefat striped nexus-audit__table"><thead><tr><th>Score</th><th>URL</th><th>HTTP</th><th>Title</th><th>H1</th><th>Befunde</th></tr></thead><tbody>
				<?php usort( $pages, static function( $a, $b ) { return absint( $a['score'] ?? 0 ) <=> absint( $b['score'] ?? 0 ); } ); foreach ( $pages as $page ) : if ( ! is_array( $page ) ) { continue; } $h1 = is_array( $page['h1'] ?? null ) ? $page['h1'] : []; ?>
					<tr><td><strong class="nexus-audit__page-score"><?php echo esc_html( (string) absint( $page['score'] ?? 0 ) ); ?></strong></td><td class="nexus-audit__url"><a href="<?php echo esc_url( (string) ( $page['url'] ?? '' ) ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( (string) ( $page['url'] ?? '' ) ); ?></a></td><td><?php echo esc_html( (string) ( absint( $page['status_code'] ?? 0 ) ?: '—' ) ); ?></td><td><?php echo esc_html( (string) ( $page['title'] ?? '—' ) ); ?></td><td><?php echo esc_html( (string) ( $h1[0] ?? '—' ) ); ?></td><td><?php echo esc_html( (string) nexus_seo_audit_actionable_count( $page ) ); ?></td></tr>
				<?php endforeach; ?>
				</tbody></table></div>
			</section>

			<?php if ( $history ) : ?><section class="nexus-audit__panel"><div class="nexus-audit__section-head"><div><p class="nexus-audit__eyebrow">Verlauf</p><h2>Letzte Audit-Läufe</h2></div></div><div class="nexus-audit__history"><?php foreach ( array_slice( $history, 0, 6 ) as $run ) : if ( ! is_array( $run ) ) { continue; } ?><div class="nexus-audit__history-item"><strong><?php echo esc_html( (string) absint( $run['health_score'] ?? 0 ) ); ?>/100</strong><span><?php echo esc_html( (string) ( $run['finished_at'] ?? '' ) ); ?></span><small><?php echo esc_html( (string) absint( $run['total_urls'] ?? 0 ) ); ?> URLs</small></div><?php endforeach; ?></div></section><?php endif; ?>
		<?php endif; ?>
	</div>
	<?php
}
