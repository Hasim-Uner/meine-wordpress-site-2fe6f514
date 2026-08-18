<?php
/**
 * Global site header.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$brand_text   = function_exists( 'hu_get_site_wordmark_text' ) ? hu_get_site_wordmark_text() : 'HAŞIM ÜNER';
$eyebrow_text = nexus_get_site_header_eyebrow();
$panel_id     = 'nx-site-header-panel';
$routes       = function_exists( 'hu_get_commercial_route_map' ) ? hu_get_commercial_route_map() : [];
$request_url  = $routes['marketcheck'] ?? ( function_exists( 'hu_get_request_analysis_url' ) ? hu_get_request_analysis_url() : home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' ) );
$project_url  = $routes['project_request'] ?? ( function_exists( 'hu_get_navigation_project_request_url' )
	? hu_get_navigation_project_request_url()
	: add_query_arg( [ 'type' => 'project', 'focus' => 'implementation_scope' ], home_url( '/kontakt/' ) ) );
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

/*
 * Eine zentrale Navigationsquelle: derselbe Contract wird auch beim
 * WordPress-Menü-Rebuild verwendet. So können Theme-Wechsel oder Altzustände
 * nicht wieder "WordPress Agentur" bzw. den Marktcheck als globale CTA setzen.
 */
if ( function_exists( 'hu_get_primary_navigation_contract' ) ) {
	$strategy_nav_items = hu_get_primary_navigation_contract();
} else {
	$strategy_nav_items = [
		[
			'label'    => __( 'WordPress Freelancer', 'blocksy-child' ),
			'url'      => home_url( '/wordpress-freelancer-hannover/' ),
			'current'  => false,
			'class'    => 'nav-freelancer-link',
			'track'    => 'nav_header_freelancer',
			'category' => 'navigation',
		],
		[
			'label'    => __( 'Für Agenturen', 'blocksy-child' ),
			'url'      => home_url( '/whitelabel-retainer/' ),
			'current'  => false,
			'class'    => 'nav-agency-link',
			'track'    => 'nav_header_whitelabel',
			'category' => 'navigation',
		],
		[
			'label'    => __( 'Solar & Wärmepumpen', 'blocksy-child' ),
			'url'      => home_url( '/solar-waermepumpen-leadgenerierung/' ),
			'current'  => false,
			'class'    => 'nav-solar-link',
			'track'    => 'nav_header_solar',
			'category' => 'navigation',
		],
		[
			'label'    => __( 'Projekt anfragen', 'blocksy-child' ),
			'url'      => $project_url,
			'current'  => false,
			'class'    => 'nav-cta-button nav-project-link',
			'track'    => 'nav_header_project',
			'category' => 'lead_gen',
		],
	];
}

/*
 * Visuelle Navigationsebene: Die drei Geschäftswege bleiben semantisch Teil
 * desselben Routing-Contracts, werden aber als kompakte Route-Cards gerendert.
 * Ergebnisse, Über Haşim und die Projekt-CTA bleiben bewusst ruhiger.
 */
$route_visuals = [
	'nav-solar-link' => [
		'title'    => __( 'Solar & Wärmepumpe', 'blocksy-child' ),
		'subtitle' => __( 'Anfragesysteme', 'blocksy-child' ),
		'icon'     => 'energy',
	],
	'nav-freelancer-link' => [
		'title'    => __( 'WordPress', 'blocksy-child' ),
		'subtitle' => __( 'Direkte Projekte', 'blocksy-child' ),
		'icon'     => 'code',
	],
	'nav-agency-link' => [
		'title'    => __( 'Für Agenturen', 'blocksy-child' ),
		'subtitle' => __( 'White-Label', 'blocksy-child' ),
		'icon'     => 'layers',
	],
];

$render_route_icon = static function ( string $icon ): void {
	if ( 'energy' === $icon ) {
		?>
		<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
			<circle cx="12" cy="12" r="3.25"></circle>
			<path d="M12 2.5v3M12 18.5v3M2.5 12h3M18.5 12h3M5.3 5.3l2.1 2.1M16.6 16.6l2.1 2.1M18.7 5.3l-2.1 2.1M7.4 16.6l-2.1 2.1"></path>
		</svg>
		<?php
		return;
	}

	if ( 'layers' === $icon ) {
		?>
		<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
			<path d="m12 3 8 4.5-8 4.5-8-4.5L12 3Z"></path>
			<path d="m4 12 8 4.5 8-4.5M4 16.5l8 4.5 8-4.5"></path>
		</svg>
		<?php
		return;
	}
	?>
	<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
		<rect x="3" y="4.5" width="18" height="15" rx="2.5"></rect>
		<path d="M3 8.5h18M10 12l-2 2 2 2M14 12l2 2-2 2"></path>
	</svg>
	<?php
};

$render_strategy_navigation = static function ( string $context ) use ( $strategy_nav_items, $route_visuals, $render_route_icon ): void {
	$context    = sanitize_key( $context );
	$menu_class = 'nx-site-header__menu nx-site-header__menu--' . $context;

	echo '<ul class="' . esc_attr( $menu_class ) . '">';
	foreach ( $strategy_nav_items as $item ) {
		$li_classes = 'menu-item ' . (string) ( $item['class'] ?? '' );
		if ( ! empty( $item['current'] ) ) {
			$li_classes .= ' current-menu-item current_page_item';
		}

		$route_visual = null;
		foreach ( $route_visuals as $route_class => $visual ) {
			if ( false !== strpos( $li_classes, $route_class ) ) {
				$route_visual = $visual;
				$li_classes  .= ' nx-site-header__route-item';
				break;
			}
		}

		$is_project_cta = false !== strpos( $li_classes, 'nav-project-link' );
		$link_label     = (string) ( $item['label'] ?? '' );
		if ( is_array( $route_visual ) ) {
			$link_label = (string) $route_visual['title'] . ' – ' . (string) $route_visual['subtitle'];
		}
		?>
		<li class="<?php echo esc_attr( $li_classes ); ?>">
			<a
				href="<?php echo esc_url( (string) ( $item['url'] ?? home_url( '/' ) ) ); ?>"
				<?php echo ! empty( $item['current'] ) ? ' aria-current="page"' : ''; // raw-ok -- static attribute ?>
				aria-label="<?php echo esc_attr( $link_label ); ?>"
				data-track-action="<?php echo esc_attr( (string) ( $item['track'] ?? 'nav_header' ) ); ?>"
				data-track-category="<?php echo esc_attr( (string) ( $item['category'] ?? 'navigation' ) ); ?>"
			>
				<?php if ( is_array( $route_visual ) ) : ?>
					<span class="nx-site-header__route-icon">
						<?php $render_route_icon( (string) $route_visual['icon'] ); ?>
					</span>
					<span class="nx-site-header__route-copy">
						<strong><?php echo esc_html( (string) $route_visual['title'] ); ?></strong>
						<small><?php echo esc_html( (string) $route_visual['subtitle'] ); ?></small>
					</span>
				<?php else : ?>
					<span><?php echo esc_html( (string) ( $item['label'] ?? '' ) ); ?></span>
					<?php if ( $is_project_cta ) : ?>
						<span class="nx-site-header__cta-arrow" aria-hidden="true">↗</span>
					<?php endif; ?>
				<?php endif; ?>
			</a>
		</li>
		<?php
	}
	echo '</ul>';
};
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
/*
 * Der Premium-Layer ist bewusst nur für den globalen Standard-Header geladen.
 * Audit- und Energy-Header bleiben als fokussierte Spezialnavigation unberührt.
 * Da der Header über wp_body_open gerendert wird, wird das kleine Stylesheet
 * hier direkt referenziert; der Header ist bis zur JS-Reveal-Logik ohnehin
 * verborgen, wodurch kein sichtbarer ungestylter Zwischenzustand entsteht.
 */
$premium_header_css_path = get_stylesheet_directory() . '/assets/css/site-header-premium.css';
$premium_header_css_url  = get_stylesheet_directory_uri() . '/assets/css/site-header-premium.css';
if ( is_file( $premium_header_css_path ) ) {
	$premium_header_css_ver = function_exists( 'hu_get_asset_version' )
		? hu_get_asset_version( $premium_header_css_path )
		: (string) filemtime( $premium_header_css_path );
	?>
	<link rel="stylesheet" id="nexus-site-header-premium-css" href="<?php echo esc_url( add_query_arg( 'ver', $premium_header_css_ver, $premium_header_css_url ) ); ?>" media="all">
	<?php
}
?>

<header class="nx-site-header nx-site-header--premium" data-site-header role="banner">
	<div class="nx-container">
		<div class="nx-site-header__shell">
			<div class="nx-site-header__brand-block">
				<?php if ( '' !== $eyebrow_text ) : ?>
					<span class="nx-site-header__eyebrow"><?php echo esc_html( $eyebrow_text ); ?></span>
				<?php endif; ?>
				<a
					class="site-logo nx-site-header__brand"
					href="<?php echo esc_url( home_url( '/' ) ); ?>"
					rel="home"
					aria-label="<?php echo esc_attr( $home_label ); ?>"
				>
					<?php echo esc_html( $brand_text ); ?>
				</a>
			</div>

			<nav class="nx-site-header__nav" aria-label="<?php esc_attr_e( 'Primäre Navigation', 'blocksy-child' ); ?>">
				<?php $render_strategy_navigation( 'desktop' ); ?>
			</nav>

			<div class="nx-site-header__actions">
				<button
					type="button"
					class="nx-site-header__toggle"
					data-site-header-toggle
					aria-expanded="false"
					aria-controls="<?php echo esc_attr( $panel_id ); ?>"
					aria-label="<?php esc_attr_e( 'Navigation öffnen', 'blocksy-child' ); ?>"
				>
					<span class="nx-site-header__toggle-line"></span>
					<span class="nx-site-header__toggle-line"></span>
					<span class="nx-site-header__toggle-line"></span>
				</button>
			</div>
		</div>

		<div id="<?php echo esc_attr( $panel_id ); ?>" class="nx-site-header__panel" data-site-header-panel hidden>
			<div class="nx-site-header__panel-intro">
				<span>Navigation</span>
				<strong>Wählen Sie Ihren Weg.</strong>
				<p>Direktes Projekt, White-Label oder Solar &amp; Wärmepumpe.</p>
			</div>
			<nav class="nx-site-header__mobile-nav" aria-label="<?php esc_attr_e( 'Mobiles Menü', 'blocksy-child' ); ?>">
				<?php $render_strategy_navigation( 'mobile' ); ?>
			</nav>
			<p class="nx-site-header__panel-signature">WordPress · Tracking · Conversion</p>
		</div>
	</div>
</header>
