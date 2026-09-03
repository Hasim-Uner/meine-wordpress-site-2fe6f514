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
 * Der Projekt-CTA für die mobile Leiste.
 *
 * Unter 1101px ist die Desktop-Nav ausgeblendet, und damit lag auch die
 * einzige Handlung des Headers hinter dem Burger. Auf einer Seite, deren Zweck
 * Anfragen sind, stand auf dem Telefon eine 64px-Leiste mit Wortmarke, Burger
 * und 209px Nichts dazwischen. Der Energy-Header macht auf derselben Breite
 * längst das Richtige (Wortmarke + Marktcheck); der globale Header zieht nach.
 *
 * Bewusst dieselbe Quelle wie die Desktop-Nav statt eines zweiten Labels: der
 * CTA darf nicht auseinanderlaufen. Sichtbar ist immer nur eine der beiden
 * Instanzen — die jeweils andere liegt auf display:none und damit auch aus dem
 * Accessibility-Baum.
 */
$mobile_cta_item = null;
foreach ( $strategy_nav_items as $strategy_nav_item ) {
	if ( false !== strpos( (string) ( $strategy_nav_item['class'] ?? '' ), 'nav-project-link' ) ) {
		$mobile_cta_item = $strategy_nav_item;
		break;
	}
}

/*
 * Visuelle Navigationsebene: Die drei Geschäftswege bleiben semantisch Teil
 * desselben Routing-Contracts. Ihre Zweckangaben erscheinen nur im mobilen
 * Routenwähler; die Desktop-Leiste zeigt die kompakten Titel ohne Icons.
 * Ergebnisse und Über Haşim bleiben bewusst ruhige Sekundärziele; die
 * Projekt-CTA behält ihre eigenständige Rolle.
 */
$route_visuals = [
	'nav-solar-link' => [
		'title'    => __( 'Solar & Wärmepumpe', 'blocksy-child' ),
		'subtitle' => __( 'Anfragesysteme', 'blocksy-child' ),
	],
	'nav-freelancer-link' => [
		'title'    => __( 'WordPress', 'blocksy-child' ),
		'subtitle' => __( 'Direkte Projekte', 'blocksy-child' ),
	],
	'nav-agency-link' => [
		'title'    => __( 'Für Agenturen', 'blocksy-child' ),
		'subtitle' => __( 'White-Label', 'blocksy-child' ),
	],
];

$render_strategy_navigation = static function ( string $context ) use ( $strategy_nav_items, $route_visuals ): void {
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
					<span class="nx-site-header__route-copy">
						<strong><?php echo esc_html( (string) $route_visual['title'] ); ?></strong>
						<small><?php echo esc_html( (string) $route_visual['subtitle'] ); ?></small>
					</span>
					<span class="nx-site-header__route-arrow" aria-hidden="true">
						<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
							<path d="M5 12h14M13 6l6 6-6 6"></path>
						</svg>
					</span>
				<?php else : ?>
					<span><?php echo esc_html( (string) ( $item['label'] ?? '' ) ); ?></span>
					<?php if ( $is_project_cta ) : ?>
						<span class="nx-site-header__cta-arrow" aria-hidden="true"><?php echo hu_arrow_up_right_svg(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static inline SVG ?></span>
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
				<?php if ( is_array( $mobile_cta_item ) ) : ?>
					<a
						class="nx-site-header__mobile-cta"
						href="<?php echo esc_url( (string) ( $mobile_cta_item['url'] ?? home_url( '/kontakt/' ) ) ); ?>"
						aria-label="<?php echo esc_attr( (string) ( $mobile_cta_item['label'] ?? 'Projekt anfragen' ) ); ?>"
						data-track-action="cta_header_mobile_project"
						data-track-category="<?php echo esc_attr( (string) ( $mobile_cta_item['category'] ?? 'lead_gen' ) ); ?>"
						data-track-section="header_mobile"
					>
						<span class="nx-site-header__mobile-cta-full"><?php echo esc_html( (string) ( $mobile_cta_item['label'] ?? 'Projekt anfragen' ) ); ?></span>
						<span class="nx-site-header__mobile-cta-short"><?php esc_html_e( 'Anfragen', 'blocksy-child' ); ?></span>
						<span class="nx-site-header__mobile-cta-arrow" aria-hidden="true"><?php echo hu_arrow_up_right_svg( 13 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static inline SVG ?></span>
					</a>
				<?php endif; ?>

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
