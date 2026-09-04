<?php
/**
 * Global site header.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$brand_text = function_exists( 'hu_get_site_wordmark_text' ) ? hu_get_site_wordmark_text() : 'HAŞIM ÜNER';
$routes     = function_exists( 'hu_get_commercial_route_map' ) ? hu_get_commercial_route_map() : [];
$request_url = $routes['marketcheck'] ?? ( function_exists( 'hu_get_request_analysis_url' )
	? hu_get_request_analysis_url()
	: home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' ) );
$audit_header_meta_items = function_exists( 'nexus_get_audit_header_meta_items' ) ? nexus_get_audit_header_meta_items() : [];
$home_label = sprintf(
	/* translators: %s: site or brand name. */
	__( 'Startseite - %s', 'blocksy-child' ),
	$brand_text
);

if ( empty( $audit_header_meta_items ) ) {
	$audit_header_meta_items = [
		'Manueller Marktcheck',
		'Solar- und Wärmepumpen-Fokus',
	];
}
?>

<?php if ( function_exists( 'nexus_is_audit_page' ) && nexus_is_audit_page() ) : ?>
<header class="nx-site-header nx-site-header--audit is-visible" data-site-header role="banner">
	<div class="nx-container">
		<div class="nx-site-header__shell nx-site-header__shell--audit">
			<div class="nx-site-header__brand-block">
				<span class="nx-site-header__eyebrow">Marktcheck</span>
				<a
					class="site-logo nx-site-header__brand"
					href="<?php echo esc_url( home_url( '/' ) ); ?>"
					rel="home"
					aria-label="<?php echo esc_attr( $home_label ); ?>"
				>
					<?php echo esc_html( $brand_text ); ?>
				</a>
			</div>

			<div class="nx-site-header__audit-meta" aria-label="Audit-Microcopy">
				<?php foreach ( $audit_header_meta_items as $audit_header_meta_item ) : ?>
					<span><?php echo esc_html( $audit_header_meta_item ); ?></span>
				<?php endforeach; ?>
			</div>

			<div class="nx-site-header__audit-actions">
				<a class="nx-site-header__audit-link" href="<?php echo esc_url( $request_url ); ?>" data-track-action="cta_audit_header_analysis" data-track-category="lead_gen" data-track-section="audit_header" data-track-funnel-stage="audit_header">Marktcheck</a>
			</div>
		</div>
	</div>
</header>
<?php return; endif; ?>

<?php if ( function_exists( 'nexus_is_energy_systems_context' ) && nexus_is_energy_systems_context() ) : ?>
<header class="nx-site-header nx-site-header--energy" data-site-header role="banner">
	<div class="nx-container">
		<div class="nx-site-header__shell nx-site-header__shell--energy">
			<a
				class="site-logo nx-site-header__brand"
				href="<?php echo esc_url( home_url( '/' ) ); ?>"
				rel="home"
				aria-label="<?php echo esc_attr( $home_label ); ?>"
			>
				<?php echo esc_html( $brand_text ); ?>
			</a>

			<a class="nx-site-header__energy-cta" href="<?php echo esc_url( $request_url ); ?>" data-track-action="cta_energy_header_analysis" data-track-category="lead_gen" data-track-section="energy_header" data-track-funnel-stage="energy_header">
				<span class="nx-site-header__energy-cta-label">Marktcheck</span>
				<span class="nx-site-header__energy-cta-microcopy" aria-hidden="true">Befund in 48 h</span>
			</a>
		</div>
	</div>
</header>
<?php return; endif; ?>

<?php
$panel_id          = 'nx-site-header-sheet';
$header_contract   = function_exists( 'hu_get_site_header_navigation_contract' )
	? hu_get_site_header_navigation_contract()
	: [];
$route_items       = isset( $header_contract['routes'] ) && is_array( $header_contract['routes'] ) ? $header_contract['routes'] : [];
$navigation_groups = isset( $header_contract['groups'] ) && is_array( $header_contract['groups'] ) ? $header_contract['groups'] : [];
$meta              = isset( $header_contract['meta'] ) && is_array( $header_contract['meta'] ) ? $header_contract['meta'] : [];
$toggle_tracking   = isset( $header_contract['toggle'] ) && is_array( $header_contract['toggle'] ) ? $header_contract['toggle'] : [];
$project_url       = $routes['project_request'] ?? ( function_exists( 'hu_get_navigation_project_request_url' )
	? hu_get_navigation_project_request_url()
	: home_url( '/kontakt/' ) );
$cta_item          = isset( $header_contract['cta'] ) && is_array( $header_contract['cta'] )
	? $header_contract['cta']
	: [
		'label'       => __( 'Projekt anfragen', 'blocksy-child' ),
		'short_label' => __( 'Anfragen', 'blocksy-child' ),
		'url'         => $project_url,
		'track'       => 'nav_header_project',
		'category'    => 'lead_gen',
		'section'     => 'header',
	];
$response_promise = function_exists( 'hu_response_promise' ) ? hu_response_promise( 'compact' ) : '';
?>

<header class="nx-site-header nx-site-header--sheet is-visible" data-site-header role="banner">
	<div class="nx-container nx-site-header__bar">
		<a
			class="nx-site-header__brand site-logo"
			href="<?php echo esc_url( home_url( '/' ) ); ?>"
			rel="home"
			aria-label="<?php echo esc_attr( $home_label ); ?>"
			data-site-header-bar-link
			data-track-action="nav_header_about"
			data-track-category="navigation"
			data-track-section="header"
		>
			<?php echo esc_html( $brand_text ); ?>
		</a>

		<div class="nx-site-header__actions">
			<span class="nx-site-header__status">
				<i aria-hidden="true"></i>
				<?php esc_html_e( 'WordPress · Tracking · Conversion', 'blocksy-child' ); ?>
			</span>

			<a
				class="nx-site-header__cta"
				href="<?php echo esc_url( (string) ( $cta_item['url'] ?? $project_url ) ); ?>"
				aria-label="<?php echo esc_attr( (string) ( $cta_item['label'] ?? 'Projekt anfragen' ) ); ?>"
				data-site-header-bar-link
				data-track-action="<?php echo esc_attr( (string) ( $cta_item['track'] ?? 'nav_header_project' ) ); ?>"
				data-track-category="<?php echo esc_attr( (string) ( $cta_item['category'] ?? 'lead_gen' ) ); ?>"
				data-track-section="<?php echo esc_attr( (string) ( $cta_item['section'] ?? 'header' ) ); ?>"
			>
				<span class="nx-site-header__cta-full"><?php echo esc_html( (string) ( $cta_item['label'] ?? 'Projekt anfragen' ) ); ?></span>
				<span class="nx-site-header__cta-short"><?php echo esc_html( (string) ( $cta_item['short_label'] ?? 'Anfragen' ) ); ?></span>
			</a>

			<button
				type="button"
				class="nx-site-header__toggle"
				data-site-header-toggle
				aria-expanded="false"
				aria-controls="<?php echo esc_attr( $panel_id ); ?>"
				aria-label="<?php esc_attr_e( 'Navigation öffnen', 'blocksy-child' ); ?>"
				data-track-action="<?php echo esc_attr( (string) ( $toggle_tracking['track'] ?? 'nav_menu_toggle' ) ); ?>"
				data-track-category="<?php echo esc_attr( (string) ( $toggle_tracking['category'] ?? 'navigation' ) ); ?>"
				data-track-section="<?php echo esc_attr( (string) ( $toggle_tracking['section'] ?? 'header' ) ); ?>"
			>
				<span class="nx-site-header__toggle-label" data-label-open="<?php esc_attr_e( 'Menü', 'blocksy-child' ); ?>" data-label-close="<?php esc_attr_e( 'Schließen', 'blocksy-child' ); ?>"><?php esc_html_e( 'Menü', 'blocksy-child' ); ?></span>
				<span class="nx-site-header__toggle-glyph" aria-hidden="true"><span></span><span></span></span>
			</button>
		</div>
	</div>

	<span class="nx-site-header__scanline" aria-hidden="true"></span>

	<div
		id="<?php echo esc_attr( $panel_id ); ?>"
		class="nx-site-header__sheet"
		data-site-header-panel
		role="dialog"
		aria-modal="true"
		aria-label="<?php esc_attr_e( 'Navigation', 'blocksy-child' ); ?>"
		aria-hidden="true"
		inert
	>
		<div class="nx-container nx-site-header__sheet-grid">
			<nav class="nx-site-header__sheet-main" aria-label="<?php esc_attr_e( 'Hauptnavigation', 'blocksy-child' ); ?>">
				<ul class="nx-site-header__routes">
					<?php foreach ( $route_items as $route_item ) : ?>
						<?php
						$route_classes = 'nx-site-header__route ' . (string) ( $route_item['class'] ?? '' );
						if ( ! empty( $route_item['current'] ) ) {
							$route_classes .= ' is-current';
						}
						?>
						<li class="<?php echo esc_attr( $route_classes ); ?>">
							<a
								href="<?php echo esc_url( (string) ( $route_item['url'] ?? home_url( '/' ) ) ); ?>"
								<?php echo ! empty( $route_item['current'] ) ? ' aria-current="page"' : ''; // raw-ok -- static attribute. ?>
								data-track-action="<?php echo esc_attr( (string) ( $route_item['track'] ?? '' ) ); ?>"
								data-track-category="<?php echo esc_attr( (string) ( $route_item['category'] ?? 'navigation' ) ); ?>"
								data-track-section="<?php echo esc_attr( (string) ( $route_item['section'] ?? 'header' ) ); ?>"
							>
								<span class="nx-site-header__route-copy">
									<span class="nx-site-header__route-kicker"><?php echo esc_html( (string) ( $route_item['kicker'] ?? '' ) ); ?></span>
									<strong class="nx-site-header__route-title"><?php echo esc_html( (string) ( $route_item['label'] ?? '' ) ); ?></strong>
								</span>
								<span class="nx-site-header__route-desc"><?php echo esc_html( (string) ( $route_item['desc'] ?? '' ) ); ?></span>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>

				<div class="nx-site-header__groups">
					<?php foreach ( $navigation_groups as $navigation_group ) : ?>
						<div class="nx-site-header__group">
							<p class="nx-site-header__group-title"><?php echo esc_html( (string) ( $navigation_group['title'] ?? '' ) ); ?></p>
							<ul>
								<?php foreach ( (array) ( $navigation_group['items'] ?? [] ) as $group_item ) : ?>
									<li<?php echo ! empty( $group_item['current'] ) ? ' class="is-current"' : ''; // raw-ok -- static attribute. ?>>
										<a
											href="<?php echo esc_url( (string) ( $group_item['url'] ?? home_url( '/' ) ) ); ?>"
											<?php echo ! empty( $group_item['current'] ) ? ' aria-current="page"' : ''; // raw-ok -- static attribute. ?>
											data-track-action="<?php echo esc_attr( (string) ( $group_item['track'] ?? '' ) ); ?>"
											data-track-category="<?php echo esc_attr( (string) ( $group_item['category'] ?? 'navigation' ) ); ?>"
											data-track-section="<?php echo esc_attr( (string) ( $group_item['section'] ?? 'header' ) ); ?>"
										>
											<?php echo esc_html( (string) ( $group_item['label'] ?? '' ) ); ?>
										</a>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
					<?php endforeach; ?>
				</div>

				<?php if ( '' !== $response_promise ) : ?>
					<div class="nx-site-header__sheet-foot">
						<?php echo esc_html( $response_promise ); ?>
					</div>
				<?php endif; ?>
			</nav>

			<div class="nx-site-header__meta" aria-label="<?php esc_attr_e( 'Kontakt und Standort', 'blocksy-child' ); ?>">
				<p class="nx-site-header__meta-title"><?php esc_html_e( 'Direkter Draht', 'blocksy-child' ); ?></p>
				<ul>
					<?php foreach ( (array) ( $meta['links'] ?? [] ) as $meta_link ) : ?>
						<li>
							<a
								href="<?php echo esc_url( (string) ( $meta_link['url'] ?? '' ) ); ?>"
								data-track-action="<?php echo esc_attr( (string) ( $meta_link['track'] ?? '' ) ); ?>"
								data-track-category="<?php echo esc_attr( (string) ( $meta_link['category'] ?? 'navigation' ) ); ?>"
								data-track-section="<?php echo esc_attr( (string) ( $meta_link['section'] ?? 'header' ) ); ?>"
							>
								<?php echo esc_html( (string) ( $meta_link['label'] ?? '' ) ); ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
				<?php if ( ! empty( $meta['location'] ) ) : ?>
					<p class="nx-site-header__location"><?php echo esc_html( (string) $meta['location'] ); ?></p>
				<?php endif; ?>
			</div>
		</div>
	</div>
</header>
