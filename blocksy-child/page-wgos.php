<?php
/**
 * Template Name: WGOS Client Dashboard
 * Description: Internes Client- und Delivery-Dashboard für WGOS-Projekte.
 *
 * Content bleibt template-driven; SEO-Meta liegt zentral in inc/seo-meta.php.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! headers_sent() ) {
	header( 'X-Robots-Tag: noindex, nofollow', true );
}
add_action( 'wp_head', static function () {
	echo '<meta name="robots" content="noindex,nofollow" />' . "\n";
}, 1 );

$dashboard_url = get_permalink( get_queried_object_id() );

if ( ! $dashboard_url ) {
	$dashboard_url = function_exists( 'hu_get_wgos_dashboard_url' ) ? hu_get_wgos_dashboard_url() : home_url( '/wgos/' );
}

if ( function_exists( 'hu_require_wgos_dashboard_access' ) ) {
	hu_require_wgos_dashboard_access( $dashboard_url );
} else {
	if ( ! is_user_logged_in() ) {
		wp_safe_redirect( wp_login_url( $dashboard_url ) );
		exit;
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_safe_redirect( home_url( '/' ) );
		exit;
	}
}

$current_user = wp_get_current_user();
$client_name  = $current_user instanceof WP_User && '' !== trim( (string) $current_user->display_name )
	? (string) $current_user->display_name
	: 'Client';

// V1 nutzt nur sichere Fallback-Daten. Spätere wgos_project-Queries müssen immer
// serverseitig per client_user_id = get_current_user_id() gefiltert werden.
$dashboard = [
	'current_sprint'   => 'Sprint noch nicht gesetzt',
	'credits_used'     => '0',
	'credits_total'    => '0',
	'next_review_call' => 'Review-Call noch nicht geplant',
	'project_status'   => 'Onboarding / Vorbereitung',
];

$open_tasks = [
	'Zugänge und Ansprechpartner final bestätigen',
	'Aktuelle Prioritäten für den nächsten Sprint freigeben',
	'Tracking- und Formularstrecke nach Livegang gemeinsam prüfen',
];

$delivered_assets = [
	'Projektstruktur angelegt',
	'Prioritätenboard vorbereitet',
	'Dokumentationsbereich vorbereitet',
];

$next_priorities = [
	'Projektmodule und Credit-Plan bestätigen',
	'Erste Lieferobjekte in Sprint-Reihenfolge bringen',
	'Nächsten Review-Termin im Projektkalender festlegen',
];

$project_modules = [
	[
		'name'   => 'Strategie & Priorisierung',
		'status' => 'Vorbereitet',
	],
	[
		'name'   => 'Technisches Fundament',
		'status' => 'Offen',
	],
	[
		'name'   => 'Messbarkeit & Tracking',
		'status' => 'Offen',
	],
	[
		'name'   => 'Anfragepfad & Conversion',
		'status' => 'Offen',
	],
];

$documentation_items = [
	[
		'label' => 'Projekt-Dokumentation',
		'value' => 'Wird im Kickoff hinterlegt',
	],
	[
		'label' => 'Review-Notizen',
		'value' => 'Noch keine Review-Notizen hinterlegt',
	],
	[
		'label' => 'Zugänge & Freigaben',
		'value' => 'Nur projektbezogen und nicht öffentlich',
	],
];

get_header();
?>

<main id="main" class="site-main">
	<div class="wgos-wrapper" data-track-section="wgos_client_dashboard">

		<section class="wgos-hero">
			<div class="wgos-container">
				<div class="wgos-hero__content">
					<span class="wgos-kicker">Internes Client Dashboard</span>
					<h1 class="wgos-hero__title">Projektstatus für <?php echo esc_html( $client_name ); ?></h1>
					<p class="wgos-hero__subtitle">Operative Übersicht für Sprint, Credits, Aufgaben, gelieferte Assets und nächste Prioritäten.</p>
				</div>
			</div>
		</section>

		<section class="wgos-section wgos-section--white" id="status">
			<div class="wgos-container">
				<div class="wgos-component-grid">
					<article class="wgos-core-area-card">
						<span class="wgos-core-card__number">01</span>
						<h2>Aktueller Sprint</h2>
						<p class="wgos-core-area-card__intro"><?php echo esc_html( $dashboard['current_sprint'] ); ?></p>
					</article>
					<article class="wgos-core-area-card">
						<span class="wgos-core-card__number">02</span>
						<h2>Verbrauchte Credits</h2>
						<p class="wgos-core-area-card__intro"><?php echo esc_html( $dashboard['credits_used'] ); ?> von <?php echo esc_html( $dashboard['credits_total'] ); ?></p>
					</article>
					<article class="wgos-core-area-card">
						<span class="wgos-core-card__number">03</span>
						<h2>Nächster Review-Call</h2>
						<p class="wgos-core-area-card__intro"><?php echo esc_html( $dashboard['next_review_call'] ); ?></p>
					</article>
					<article class="wgos-core-area-card">
						<span class="wgos-core-card__number">04</span>
						<h2>Projektstatus</h2>
						<p class="wgos-core-area-card__intro"><?php echo esc_html( $dashboard['project_status'] ); ?></p>
					</article>
				</div>
			</div>
		</section>

		<section class="wgos-section wgos-section--gray" id="arbeit">
			<div class="wgos-container">
				<div class="wgos-section-head">
					<span class="wgos-principle-kicker">Sprint-Übersicht</span>
					<h2 class="wgos-h2">Aufgaben, Assets und Prioritäten</h2>
				</div>

				<div class="wgos-pricing-grid">
					<article class="wgos-pricing-card">
						<div class="wgos-pricing-card__head">
							<h3>Offene Aufgaben</h3>
						</div>
						<ul class="wgos-pricing-card__features">
							<?php foreach ( $open_tasks as $task ) : ?>
								<li><?php echo esc_html( $task ); ?></li>
							<?php endforeach; ?>
						</ul>
					</article>

					<article class="wgos-pricing-card">
						<div class="wgos-pricing-card__head">
							<h3>Gelieferte Assets</h3>
						</div>
						<ul class="wgos-pricing-card__features">
							<?php foreach ( $delivered_assets as $asset ) : ?>
								<li><?php echo esc_html( $asset ); ?></li>
							<?php endforeach; ?>
						</ul>
					</article>

					<article class="wgos-pricing-card">
						<div class="wgos-pricing-card__head">
							<h3>Nächste Prioritäten</h3>
						</div>
						<ul class="wgos-pricing-card__features">
							<?php foreach ( $next_priorities as $priority ) : ?>
								<li><?php echo esc_html( $priority ); ?></li>
							<?php endforeach; ?>
						</ul>
					</article>
				</div>
			</div>
		</section>

		<section class="wgos-section wgos-section--white" id="module">
			<div class="wgos-container">
				<div class="wgos-section-head">
					<span class="wgos-principle-kicker">Projektmodule</span>
					<h2 class="wgos-h2">Aktueller Modulstatus</h2>
				</div>

				<div class="wgos-table-wrap">
					<table class="wgos-credits-table wgos-credits-table--compact">
						<thead>
							<tr>
								<th>Modul</th>
								<th>Status</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( $project_modules as $module ) : ?>
								<tr>
									<td><?php echo esc_html( $module['name'] ); ?></td>
									<td><?php echo esc_html( $module['status'] ); ?></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
		</section>

		<section class="wgos-section wgos-section--gray" id="dokumentation">
			<div class="wgos-container">
				<div class="wgos-section-head">
					<span class="wgos-principle-kicker">Dokumentation</span>
					<h2 class="wgos-h2">Projektlinks und Hinweise</h2>
				</div>

				<div class="wgos-component-grid">
					<?php foreach ( $documentation_items as $item ) : ?>
						<article class="wgos-core-area-card">
							<h3><?php echo esc_html( $item['label'] ); ?></h3>
							<p class="wgos-core-area-card__intro"><?php echo esc_html( $item['value'] ); ?></p>
						</article>
					<?php endforeach; ?>
				</div>

				<p class="wgos-section-intro">Dieses Dashboard zeigt keine globalen Kundendaten. Spätere dynamische Projektdaten werden ausschließlich für den aktuell eingeloggten Nutzer geladen.</p>
			</div>
		</section>

	</div>
</main>

<?php get_footer(); ?>
