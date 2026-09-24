<?php
/**
 * Global site header.
 *
 * Die Standardvariante ist seit 2026-09 die Leiste aus dem Designsystem:
 * eine schlanke Zeile, Wortmarke links, Navigation rechts, eine Haarlinie
 * als Abschluss. Sie steht im Fluss (sticky) statt darueber (fixed) — eine
 * dauerhaft sichtbare fixierte Leiste zwaenge jede Route zu einem eigenen
 * Ausgleich oben, und genau daran haengen heute noch mehrere Stylesheets.
 * `body.nx-custom-header-active` setzt die beiden Hoehen-Tokens deshalb auf
 * 0; siehe system.css.
 *
 * Ersatzlos entfallen: das Vollflaechen-Sheet mit Routenbeschreibungen,
 * Gruppenspalten und Kontaktblock, der Statusstreifen neben der Wortmarke
 * und der Scanline-Strich. Das Sheet war eine zweite Startseite im Kopf —
 * drei Routen mit je zwei Zeilen Beschreibung, bevor die Seite darunter
 * ueberhaupt sprechen durfte. Die Beschreibungen stehen jetzt dort, wo
 * entschieden wird: in Abschnitt 01 der Startseite.
 *
 * Blog- und Energy-Kontexte erreichen diese Datei nicht (siehe inc/header.php).
 * Der fruehere Growth-Audit-Sonderheader ist retired und entfernt.
 *
 * Die data-track-Werte sind unveraendert uebernommen, damit die Zeitreihe
 * ueber den Umbau hinweg vergleichbar bleibt.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$brand_text = function_exists( 'hu_get_site_wordmark_text' ) ? hu_get_site_wordmark_text() : 'HAŞIM ÜNER';
$routes     = function_exists( 'hu_get_commercial_route_map' ) ? hu_get_commercial_route_map() : [];
$home_label = sprintf(
	/* translators: %s: site or brand name. */
	__( 'Startseite - %s', 'blocksy-child' ),
	$brand_text
);
?>

<?php
$blatt_id        = 'leiste-blatt';
$header_contract = function_exists( 'hu_get_site_header_navigation_contract' )
	? hu_get_site_header_navigation_contract()
	: [];
$route_items       = isset( $header_contract['routes'] ) && is_array( $header_contract['routes'] ) ? $header_contract['routes'] : [];
$navigation_groups = isset( $header_contract['groups'] ) && is_array( $header_contract['groups'] ) ? $header_contract['groups'] : [];
$meta              = isset( $header_contract['meta'] ) && is_array( $header_contract['meta'] ) ? $header_contract['meta'] : [];
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

$cta_url   = (string) ( $cta_item['url'] ?? $project_url );
$cta_label = (string) ( $cta_item['label'] ?? 'Projekt anfragen' );

/*
 * Auf der Kontaktseite entfaellt der Anfrage-Button: er zeigt auf die Seite,
 * auf der man schon steht. Ein Ausgang, der zurueck auf sich selbst fuehrt,
 * ist kein Angebot, sondern eine Sackgasse neben dem Formular.
 *
 * Der Contract behaelt den CTA unveraendert — er speist auch das gespeicherte
 * WordPress-Menue (inc/menu-setup.php). Ausgeblendet wird nur die Ausgabe.
 */
$shows_cta = ! ( function_exists( 'nexus_is_contact_page' ) && nexus_is_contact_page() )
	&& '' !== $cta_label;

/*
 * Eine Liste fuer beide Ausgaben. Die Zeile zeigt sie waagerecht, das
 * Klappblatt senkrecht — dieselben Ziele in derselben Reihenfolge. Die
 * wechselnde Reihenfolge zwischen Kopf, Seite und Fuss war genau das,
 * was jeder Anordnung ihre Aussage genommen hat.
 */
$leiste_links = [];

foreach ( $route_items as $route_item ) {
	$leiste_links[] = [
		'label'    => (string) ( $route_item['label'] ?? '' ),
		'url'      => (string) ( $route_item['url'] ?? home_url( '/' ) ),
		'current'  => ! empty( $route_item['current'] ),
		'track'    => (string) ( $route_item['track'] ?? '' ),
		'category' => (string) ( $route_item['category'] ?? 'navigation' ),
	];
}

foreach ( $navigation_groups as $navigation_group ) {
	foreach ( (array) ( $navigation_group['items'] ?? [] ) as $group_item ) {
		$leiste_links[] = [
			'label'    => (string) ( $group_item['label'] ?? '' ),
			'url'      => (string) ( $group_item['url'] ?? home_url( '/' ) ),
			'current'  => ! empty( $group_item['current'] ),
			'track'    => (string) ( $group_item['track'] ?? '' ),
			'category' => (string) ( $group_item['category'] ?? 'navigation' ),
		];
	}
}

$response_promise = function_exists( 'hu_response_promise' ) ? hu_response_promise( 'compact' ) : '';
$leiste_location  = (string) ( $meta['location'] ?? '' );
?>

<header class="leiste" data-leiste role="banner">
	<div class="blatt in" data-leiste-zeile>
		<a
			class="sig site-logo"
			href="<?php echo esc_url( home_url( '/' ) ); ?>"
			rel="home"
			aria-label="<?php echo esc_attr( $home_label ); ?>"
			data-track-action="nav_header_about"
			data-track-category="navigation"
			data-track-section="header"
		><?php echo esc_html( $brand_text ); ?><i aria-hidden="true">.</i></a>

		<div class="rechts">
			<nav aria-label="<?php esc_attr_e( 'Hauptnavigation', 'blocksy-child' ); ?>">
				<?php foreach ( $leiste_links as $leiste_link ) : ?>
					<a
						href="<?php echo esc_url( $leiste_link['url'] ); ?>"
						<?php echo $leiste_link['current'] ? ' aria-current="page"' : ''; // raw-ok -- static attribute. ?>
						data-track-action="<?php echo esc_attr( $leiste_link['track'] ); ?>"
						data-track-category="<?php echo esc_attr( $leiste_link['category'] ); ?>"
						data-track-section="header"
					><?php echo esc_html( $leiste_link['label'] ); ?></a>
				<?php endforeach; ?>
			</nav>

			<?php if ( $shows_cta ) : ?>
				<a
					class="tun"
					href="<?php echo esc_url( $cta_url ); ?>"
					data-track-action="<?php echo esc_attr( (string) ( $cta_item['track'] ?? 'nav_header_project' ) ); ?>"
					data-track-category="<?php echo esc_attr( (string) ( $cta_item['category'] ?? 'lead_gen' ) ); ?>"
					data-track-section="<?php echo esc_attr( (string) ( $cta_item['section'] ?? 'header' ) ); ?>"
				><?php echo esc_html( $cta_label ); ?></a>
			<?php endif; ?>

			<button
				type="button"
				class="klappe"
				data-leiste-klappe
				aria-expanded="false"
				aria-controls="<?php echo esc_attr( $blatt_id ); ?>"
				data-track-action="nav_menu_toggle"
				data-track-category="navigation"
				data-track-section="header"
			>
				<span
					data-leiste-wort
					data-wort-auf="<?php esc_attr_e( 'Menü', 'blocksy-child' ); ?>"
					data-wort-zu="<?php esc_attr_e( 'Schließen', 'blocksy-child' ); ?>"
				><?php esc_html_e( 'Menü', 'blocksy-child' ); ?></span>
				<span class="balken" aria-hidden="true"></span>
			</button>
		</div>
	</div>

	<div class="blatt blatt-klapp" id="<?php echo esc_attr( $blatt_id ); ?>" data-leiste-blatt>
		<nav aria-label="<?php esc_attr_e( 'Navigation', 'blocksy-child' ); ?>">
			<?php foreach ( $leiste_links as $leiste_link ) : ?>
				<a
					href="<?php echo esc_url( $leiste_link['url'] ); ?>"
					<?php echo $leiste_link['current'] ? ' aria-current="page"' : ''; // raw-ok -- static attribute. ?>
					data-track-action="<?php echo esc_attr( $leiste_link['track'] ); ?>"
					data-track-category="<?php echo esc_attr( $leiste_link['category'] ); ?>"
					data-track-section="header"
				><?php echo esc_html( $leiste_link['label'] ); ?></a>
			<?php endforeach; ?>
		</nav>

		<?php if ( $shows_cta ) : ?>
			<a
				class="tun"
				href="<?php echo esc_url( $cta_url ); ?>"
				data-track-action="<?php echo esc_attr( (string) ( $cta_item['track'] ?? 'nav_header_project' ) ); ?>"
				data-track-category="<?php echo esc_attr( (string) ( $cta_item['category'] ?? 'lead_gen' ) ); ?>"
				data-track-section="header"
			><?php echo esc_html( $cta_label ); ?> <span class="pf" aria-hidden="true">&rarr;</span></a>
		<?php endif; ?>

		<?php if ( '' !== $leiste_location || '' !== $response_promise ) : ?>
			<span class="wo">
				<?php
				echo esc_html(
					trim(
						implode(
							' · ',
							array_filter( [ $leiste_location, $response_promise ] )
						)
					)
				);
				?>
			</span>
		<?php endif; ?>
	</div>
</header>
