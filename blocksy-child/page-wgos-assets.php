<?php
/**
 * Template Name: WGOS Asset Hub
 * Description: Klickbare WGOS Asset-Landkarte
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$audit_url       = nexus_get_audit_url();
$calendar_url    = function_exists( 'nexus_get_audit_calendar_url' ) ? nexus_get_audit_calendar_url() : home_url( '/kontakt/' );
$payload         = function_exists( 'nexus_get_wgos_asset_explorer_payload' ) ? nexus_get_wgos_asset_explorer_payload() : [
	'wgosAssetPhases'  => [],
	'wgosAssetModules' => [],
	'wgosAssets'       => [],
	'summary'          => [],
];
$summary         = isset( $payload['summary'] ) && is_array( $payload['summary'] ) ? $payload['summary'] : [];
$asset_count     = isset( $summary['totalAssets'] ) ? (int) $summary['totalAssets'] : count( $payload['wgosAssets'] );
$phase_count     = isset( $payload['wgosAssetPhases'] ) && is_array( $payload['wgosAssetPhases'] ) ? count( $payload['wgosAssetPhases'] ) : 0;
$module_count    = isset( $payload['wgosAssetModules'] ) && is_array( $payload['wgosAssetModules'] ) ? count( $payload['wgosAssetModules'] ) : 0;
$hub_sections    = function_exists( 'nexus_get_wgos_asset_hub_sections' ) ? nexus_get_wgos_asset_hub_sections() : [];
$audit_cta_label = function_exists( 'nexus_get_audit_cta_label' ) ? nexus_get_audit_cta_label() : 'System-Diagnose anfragen';
$hero_subtitle   = sprintf(
	'%1$d Assets in %2$d Modulen, nach Phasen geordnet. Jedes Asset direkt klickbar.',
	$asset_count,
	$module_count
);
?>

<main id="main" class="site-main">
	<div class="wgos-wrapper">
		<section class="wgos-hero">
			<div class="wgos-container">
				<div class="wgos-hero-copy wgos-hero-copy--compact">
					<span class="wgos-kicker">WGOS Systemlandkarte</span>
					<h1 class="wgos-hero__title">Alle WGOS-Bausteine auf einen Blick.</h1>
					<p class="wgos-hero__subtitle"><?php echo esc_html( $hero_subtitle ); ?></p>

					<div class="wgos-trust-strip wgos-trust-strip--hero" aria-label="Strukturelle Kennzahlen">
						<div class="wgos-trust-item">
							<span class="wgos-trust-value"><?php echo esc_html( (string) $phase_count ); ?></span>
							<span class="wgos-trust-label">Phasen</span>
						</div>
						<div class="wgos-trust-item">
							<span class="wgos-trust-value"><?php echo esc_html( (string) $module_count ); ?></span>
							<span class="wgos-trust-label">Kernmodule</span>
						</div>
						<div class="wgos-trust-item">
							<span class="wgos-trust-value"><?php echo esc_html( (string) $asset_count ); ?></span>
							<span class="wgos-trust-label">versionierte Assets</span>
						</div>
					</div>

					<div class="wgos-hero__actions">
						<a href="#explorer" class="wgos-btn wgos-btn--primary" data-track="cta_click_explorer" data-track-action="cta_click_explorer" data-track-category="navigation" data-track-section="hero">Explorer öffnen</a>
					</div>
				</div>
			</div>
		</section>

		<section id="explorer" class="wgos-section wgos-section--white">
			<div class="wgos-container">
				<div class="wgos-section-head">
					<span class="wgos-principle-kicker">Explorer</span>
					<h2 class="wgos-h2">Asset Explorer</h2>
				</div>

				<div class="wgos-map-guide" aria-label="Explorer Nutzung">
					<span>1. Phase lesen</span>
					<span>2. Modul vergleichen</span>
					<span>3. Asset vertiefen</span>
				</div>

				<div id="wgos-asset-explorer-root" class="wgos-asset-explorer-root"></div>
				<noscript>
					<p class="wgos-section-intro">Der Explorer benötigt JavaScript. <a href="#library">Zur statischen Asset-Übersicht springen</a>.</p>
				</noscript>

				<div id="library" class="wgos-hub-library">
					<div class="wgos-section-head">
						<span class="wgos-principle-kicker">Library</span>
						<h2 class="wgos-h2">Alle Assets nach Modulen</h2>
					</div>

					<div class="wgos-hub-sections">
						<?php $hub_index = 0; ?>
						<?php foreach ( $hub_sections as $section ) : ?>
							<details class="wgos-hub-section-card"<?php echo 0 === $hub_index ? ' open' : ''; ?> style="--wgos-module-accent: <?php echo esc_attr( (string) $section['accent'] ); ?>;">
								<?php ++$hub_index; ?>
								<summary class="wgos-hub-section-card__head" aria-labelledby="<?php echo esc_attr( $section['module_id'] . '-list' ); ?>">
									<div>
										<span class="wgos-hub-section-card__phase"><?php echo esc_html( (string) $section['phase_step'] ); ?> · <?php echo esc_html( (string) $section['phase_label'] ); ?></span>
										<h3 id="<?php echo esc_attr( $section['module_id'] . '-list' ); ?>">
											<span class="wgos-hub-section-card__number"><?php echo esc_html( (string) $section['module_no'] ); ?></span>
											<?php echo esc_html( (string) $section['module'] ); ?>
										</h3>
									</div>
									<div class="wgos-hub-section-card__side">
										<p><?php echo esc_html( (string) $section['summary'] ); ?></p>
										<span class="wgos-hub-section-card__count"><?php echo esc_html( (string) count( (array) $section['items'] ) ); ?> Assets</span>
									</div>
								</summary>

								<div class="wgos-hub-asset-grid">
									<?php foreach ( (array) $section['items'] as $item ) : ?>
										<?php
										$is_live         = 'publish' === (string) $item['status'];
										$status_label    = $is_live ? 'Live' : 'Im Aufbau';
										$asset_link      = $is_live ? (string) $item['url'] : '#audit';
										$asset_cta_label = $is_live ? 'Asset im Detail' : 'Im Audit einordnen';
										?>
										<article id="<?php echo esc_attr( (string) $item['id'] ); ?>" class="wgos-hub-asset-card">
											<div class="wgos-hub-asset-card__top">
												<div>
													<?php if ( $is_live ) : ?>
														<h4><a href="<?php echo esc_url( $asset_link ); ?>"><?php echo esc_html( (string) $item['title'] ); ?></a></h4>
													<?php else : ?>
														<h4><?php echo esc_html( (string) $item['title'] ); ?></h4>
													<?php endif; ?>
													<p><?php echo esc_html( (string) $item['goal'] ); ?></p>
												</div>
												<div class="wgos-hub-asset-card__meta">
													<span><?php echo esc_html( (string) $item['credits'] ); ?> Credits</span>
													<span class="wgos-hub-asset-card__status<?php echo $is_live ? ' is-live' : ' is-draft'; ?>"><?php echo esc_html( $status_label ); ?></span>
												</div>
											</div>

											<dl class="wgos-hub-asset-card__facts">
												<div>
													<dt>Ergebnis</dt>
													<dd><?php echo esc_html( (string) $item['result'] ); ?></dd>
												</div>
												<div>
													<dt>Voraussetzung</dt>
													<dd><?php echo esc_html( (string) $item['prerequisite'] ); ?></dd>
												</div>
												<div>
													<dt>Fokus</dt>
													<dd><?php echo esc_html( (string) $item['keyword'] ); ?></dd>
												</div>
											</dl>

											<div class="wgos-hub-asset-card__actions">
												<a href="<?php echo esc_url( $asset_link ); ?>" class="wgos-btn wgos-btn--outline"><?php echo esc_html( $asset_cta_label ); ?></a>
											</div>
										</article>
									<?php endforeach; ?>
								</div>
							</details>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</section>

		<section id="audit" class="wgos-section wgos-section--gray wgos-final-cta">
			<div class="wgos-container">
				<div class="wgos-final-cta__inner">
					<span class="wgos-principle-kicker">Audit</span>
					<h2 class="wgos-h2">Welches Asset zuerst?</h2>
					<p class="wgos-prose">Die System-Diagnose klärt die Reihenfolge für Ihre Situation.</p>

					<div class="wgos-hero__actions">
						<a href="<?php echo esc_url( $audit_url ); ?>" class="wgos-btn wgos-btn--primary" data-track="cta_click_audit" data-track-action="cta_click_audit" data-track-category="lead_gen" data-track-section="asset_hub_cta"><?php echo esc_html( $audit_cta_label ); ?></a>
						<a href="<?php echo esc_url( $calendar_url ); ?>" class="wgos-btn wgos-btn--outline" data-track="cta_click_calendar" data-track-action="cta_click_calendar" data-track-category="lead_gen" data-track-section="asset_hub_cta">Strategiegespräch vereinbaren</a>
					</div>
				</div>
			</div>
		</section>
	</div>
</main>

<?php get_footer(); ?>
