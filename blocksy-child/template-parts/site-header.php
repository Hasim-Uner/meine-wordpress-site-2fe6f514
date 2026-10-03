<?php
/**
 * Global site header.
 *
 * Die Leiste aus dem Designsystem: eine schlanke Zeile, Wortmarke links, eine
 * Haarlinie als Abschluss. Sie steht im Fluss (sticky) statt darueber (fixed) —
 * eine dauerhaft sichtbare fixierte Leiste zwaenge jede Route zu einem eigenen
 * Ausgleich oben, und genau daran haengen heute noch mehrere Stylesheets.
 * `body.nx-custom-header-active` setzt die beiden Hoehen-Tokens deshalb auf
 * 0; siehe system.css.
 *
 * Modus und Tuer entscheidet hu_funnel_context() (inc/funnel-doors.php), nicht
 * dieses Template; der Fuss liest dieselbe Entscheidung:
 *
 * - voll:  Wortmarke, vier Wege, Haarlinie, Ergebnisse, Tuer, Menue. "Ueber
 *          Hasim" steht nur im Klappblatt und im Fuss.
 * - leser: Wortmarke, Artikelpfad (Wissen / Dossier), Tuer. Kein Hauptmenue.
 *          Einzelbeitraege tragen ueber dieses Template keinen zweiten Kopf
 *          mehr; blog-header.php ruft es auf. Die Klasse
 *          `nexus-article-reader-header` bleibt als Haken fuer die
 *          Geschwister-Selektoren der Artikel-Stylesheets und die Reader-
 *          Skripte; sie traegt keine eigene Regel mehr.
 * - fokus: Solar-Seite. Wortmarke links, rechts die Leiter der Seite als
 *          Textlinks (Marktcheck, Analyse, Sofortkontakt mit Betrag) auf die
 *          Anker der Seite. Kein Hauptmenue, nicht sticky, hoechstens 56 px,
 *          Haarlinie unten. Unter 561 px nur Sofortkontakt.
 *
 * Auf der Kontaktseite zeigt die Leiste keine Tuer: sie zeigt auf die Seite,
 * auf der man schon steht. Der Contract behaelt den CTA unveraendert — er speist
 * auch das gespeicherte WordPress-Menue (inc/menu-setup.php).
 *
 * Blog-Index und Archive laufen im Modus voll; Einzelbeitraege im Modus leser.
 *
 * Die data-track-Werte sind unveraendert uebernommen, damit die Zeitreihe
 * ueber den Umbau hinweg vergleichbar bleibt. Neue Tueren tragen
 * `nav_header_door_*` und `data-door`.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$brand_text = function_exists( 'hu_get_site_wordmark_text' ) ? hu_get_site_wordmark_text() : 'HAŞIM ÜNER';
$home_label = sprintf(
	/* translators: %s: site or brand name. */
	__( 'Startseite - %s', 'blocksy-child' ),
	$brand_text
);

$blatt_id        = 'leiste-blatt';
$header_contract = function_exists( 'hu_get_site_header_navigation_contract' )
	? hu_get_site_header_navigation_contract()
	: [];
$route_items       = isset( $header_contract['routes'] ) && is_array( $header_contract['routes'] ) ? $header_contract['routes'] : [];
$navigation_groups = isset( $header_contract['groups'] ) && is_array( $header_contract['groups'] ) ? $header_contract['groups'] : [];
$meta              = isset( $header_contract['meta'] ) && is_array( $header_contract['meta'] ) ? $header_contract['meta'] : [];

$funnel_context = function_exists( 'hu_funnel_context' )
	? hu_funnel_context()
	: [ 'mode' => 'voll', 'door' => 'projekt', 'route' => '' ];
$funnel_doors   = function_exists( 'hu_funnel_doors' ) ? hu_funnel_doors() : [];
$leiste_modus   = in_array( $funnel_context['mode'], [ 'leser', 'fokus' ], true ) ? $funnel_context['mode'] : 'voll';
$funnel_door    = ( null !== $funnel_context['door'] && isset( $funnel_doors[ $funnel_context['door'] ] ) )
	? $funnel_doors[ $funnel_context['door'] ]
	: null;

/*
 * Eine Liste fuer beide Ausgaben. Die Zeile zeigt sie waagerecht, das
 * Klappblatt senkrecht — dieselben Ziele in derselben Reihenfolge, das
 * Blatt zusaetzlich mit den Punkten, die nicht in der Zeile stehen (`row`
 * false). Die wechselnde Reihenfolge zwischen Kopf, Seite und Fuss war genau
 * das, was jeder Anordnung ihre Aussage genommen hat.
 *
 * `current` ist ein aria-current-Wert: `page`, wenn der Link genau diese
 * Seite ist, `true`, wenn die Seite nur im Bereich des Punkts liegt.
 * `beleg` markiert den ersten Punkt hinter der Haarlinie.
 */
$leiste_links = [];
$nav_items    = $route_items;

foreach ( $navigation_groups as $navigation_group ) {
	$nav_items = array_merge( $nav_items, (array) ( $navigation_group['items'] ?? [] ) );
}

foreach ( $nav_items as $nav_item ) {
	$current = $nav_item['current'] ?? '';

	$leiste_links[] = [
		'label'    => (string) ( $nav_item['label'] ?? '' ),
		'url'      => (string) ( $nav_item['url'] ?? home_url( '/' ) ),
		'current'  => in_array( $current, [ 'page', 'true' ], true ) ? $current : ( true === $current ? 'page' : '' ),
		'track'    => (string) ( $nav_item['track'] ?? '' ),
		'category' => (string) ( $nav_item['category'] ?? 'navigation' ),
		'row'      => ! array_key_exists( 'row', $nav_item ) || ! empty( $nav_item['row'] ),
		'beleg'    => 'group' === ( $nav_item['kind'] ?? '' ) && ! empty( $nav_item['row'] ),
	];
}

$row_links = array_values(
	array_filter(
		$leiste_links,
		static function ( $link ) {
			return $link['row'];
		}
	)
);

/**
 * Print one door (hu_funnel_door_link(), inc/funnel-doors.php). Row und Blatt
 * tragen dieselbe Tuer.
 *
 * @param array<string, string> $door Door record from hu_funnel_doors().
 * @return void
 */
$render_door = static function ( array $door ) {
	echo hu_funnel_door_link( $door ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside hu_funnel_door_link().
};

/*
 * Modus fokus: die Tueren der Energie-Leiter als Anker der Seite. Das Ziel
 * der Tuer liegt auf dieser Seite; nur sein Anker zaehlt. Betrag und Name
 * kommen aus hu_funnel_doors(), nicht aus diesem Template.
 */
$fokus_links = [];

if ( 'fokus' === $leiste_modus ) {
	foreach ( [ 'marktcheck', 'analyse', 'sofort' ] as $fokus_key ) {
		if ( ! isset( $funnel_doors[ $fokus_key ] ) ) {
			continue;
		}

		$fokus_fragment = (string) wp_parse_url( $funnel_doors[ $fokus_key ]['url'], PHP_URL_FRAGMENT );
		$fokus_links[]  = [
			'door' => $funnel_doors[ $fokus_key ],
			'href' => '' !== $fokus_fragment ? '#' . $fokus_fragment : $funnel_doors[ $fokus_key ]['url'],
		];
	}
}

$response_promise = function_exists( 'hu_response_promise' ) ? hu_response_promise( 'compact' ) : '';
$leiste_location  = (string) ( $meta['location'] ?? '' );

$reader_attributes = '';
$reader_dossier    = null;

if ( 'leser' === $leiste_modus && function_exists( 'hu_funnel_reader_dossier' ) ) {
	$reader_slug       = (string) get_post_field( 'post_name', get_queried_object_id() );
	$reader_dossier    = hu_funnel_reader_dossier( $reader_slug );
	$primary_urls      = function_exists( 'nexus_get_primary_public_url_map' ) ? nexus_get_primary_public_url_map() : [];
	$reader_blog_url   = $primary_urls['blog'] ?? home_url( '/blog/' );
	$reader_attributes = ' data-article-system-v1 data-article-system="v2" data-reader-dossier="' . esc_attr( $reader_dossier['slug'] ) . '"';
}
?>

<header
	class="leiste leiste--<?php echo esc_attr( $leiste_modus ); ?><?php echo is_front_page() ? ' st-messkopf tafel' : ''; // raw-ok -- static class. ?><?php echo null !== $reader_dossier ? ' nexus-article-reader-header' : ''; // raw-ok -- static class. ?>"
	data-leiste
	data-leiste-modus="<?php echo esc_attr( $leiste_modus ); ?>"
	role="banner"
	<?php echo $reader_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from static attribute names and an escaped slug. ?>
>
	<div class="blatt in" data-leiste-zeile>
		<a
			class="sig site-logo"
			href="<?php echo esc_url( home_url( '/' ) ); ?>"
			rel="home"
			aria-label="<?php echo esc_attr( $home_label ); ?>"
			<?php echo is_front_page() ? ' aria-current="page"' : ''; // raw-ok -- static attribute. ?>
			data-track-action="nav_header_home"
			data-track-category="navigation"
			data-track-section="header"
		><?php echo esc_html( $brand_text ); ?><i aria-hidden="true">.</i></a>

		<?php if ( 'leser' === $leiste_modus && null !== $reader_dossier ) : ?>
			<nav class="pfad" aria-label="<?php esc_attr_e( 'Artikelpfad', 'blocksy-child' ); ?>">
				<a href="<?php echo esc_url( $reader_blog_url ); ?>" data-track-action="article_reader_back_blog" data-track-category="navigation" data-track-section="article_reader_header"><?php esc_html_e( 'Wissen', 'blocksy-child' ); ?></a>
				<span aria-hidden="true">/</span>
				<a href="<?php echo esc_url( $reader_dossier['url'] ); ?>" data-track-action="article_reader_open_dossier" data-track-category="navigation" data-track-section="article_reader_header"><?php echo esc_html( $reader_dossier['label'] ); ?></a>
			</nav>
		<?php endif; ?>

		<div class="rechts">
			<?php if ( 'fokus' === $leiste_modus && ! empty( $fokus_links ) ) : ?>
				<nav class="leiter" aria-label="<?php esc_attr_e( 'Einstiege auf dieser Seite', 'blocksy-child' ); ?>">
					<?php foreach ( $fokus_links as $fokus_index => $fokus_link ) : ?>
						<?php $fokus_last = $fokus_index === count( $fokus_links ) - 1; ?>
						<?php if ( $fokus_index > 0 ) : ?>
							<span class="trenn<?php echo $fokus_last ? '' : ' opt'; // raw-ok -- static class. ?>" aria-hidden="true"></span>
						<?php endif; ?>
						<a
							<?php echo $fokus_last ? '' : ' class="opt"'; // raw-ok -- static attribute. ?>
							href="<?php echo esc_attr( $fokus_link['href'] ); ?>"
							data-door="<?php echo esc_attr( $fokus_link['door']['key'] ); ?>"
							data-track-action="<?php echo esc_attr( $fokus_link['door']['track'] ); ?>"
							data-track-category="lead_gen"
							data-track-section="header"
						><?php echo esc_html( $fokus_link['door']['short'] ); ?> <b><?php echo esc_html( $fokus_link['door']['amount'] ); ?></b></a>
					<?php endforeach; ?>
				</nav>
			<?php endif; ?>

			<?php if ( 'voll' === $leiste_modus ) : ?>
				<nav aria-label="<?php esc_attr_e( 'Hauptnavigation', 'blocksy-child' ); ?>">
					<?php foreach ( $row_links as $leiste_link ) : ?>
						<a
							<?php echo $leiste_link['beleg'] ? ' class="beleg"' : ''; // raw-ok -- static attribute. ?>
							href="<?php echo esc_url( $leiste_link['url'] ); ?>"
							<?php echo '' !== $leiste_link['current'] ? ' aria-current="' . esc_attr( $leiste_link['current'] ) . '"' : ''; ?>
							data-track-action="<?php echo esc_attr( $leiste_link['track'] ); ?>"
							data-track-category="<?php echo esc_attr( $leiste_link['category'] ); ?>"
							data-track-section="header"
						><?php echo esc_html( $leiste_link['label'] ); ?></a>
					<?php endforeach; ?>
				</nav>
			<?php endif; ?>

			<?php
			if ( null !== $funnel_door ) {
				$render_door( $funnel_door );
			}
			?>

			<?php if ( 'voll' === $leiste_modus ) : ?>
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
			<?php endif; ?>
		</div>
	</div>

	<?php if ( 'voll' === $leiste_modus ) : ?>
		<div class="blatt blatt-klapp" id="<?php echo esc_attr( $blatt_id ); ?>" data-leiste-blatt>
			<nav aria-label="<?php esc_attr_e( 'Navigation', 'blocksy-child' ); ?>">
				<?php foreach ( $leiste_links as $leiste_link ) : ?>
					<a
						href="<?php echo esc_url( $leiste_link['url'] ); ?>"
						<?php echo '' !== $leiste_link['current'] ? ' aria-current="' . esc_attr( $leiste_link['current'] ) . '"' : ''; ?>
						data-track-action="<?php echo esc_attr( $leiste_link['track'] ); ?>"
						data-track-category="<?php echo esc_attr( $leiste_link['category'] ); ?>"
						data-track-section="header"
					><?php echo esc_html( $leiste_link['label'] ); ?></a>
				<?php endforeach; ?>
			</nav>

			<?php
			if ( null !== $funnel_door ) {
				$render_door( $funnel_door );
			}
			?>

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
	<?php endif; ?>
</header>

<?php if ( null !== $reader_dossier ) : ?>
	<?php
	$reader_share_js_url  = get_stylesheet_directory_uri() . '/assets/js/article-reader-share.js';
	$reader_share_js_path = get_stylesheet_directory() . '/assets/js/article-reader-share.js';
	$reader_share_version = function_exists( 'hu_get_asset_version' ) ? hu_get_asset_version( $reader_share_js_path ) : wp_get_theme()->get( 'Version' );
	// CSS-String, kein JSON: wp_json_encode() schriebe "Leadökonomie" als
	// \u00f6, und das ist in CSS kein Escape (es erschien als "LEADU00F6KONOMIE").
	$reader_dossier_css_label = '"' . str_replace( '</', '<\\/', addcslashes( (string) $reader_dossier['label'], "\"\\" ) ) . '"';
	?>
	<script id="nexus-article-reader-share-loader" src="<?php echo esc_url( add_query_arg( 'ver', rawurlencode( (string) $reader_share_version ), $reader_share_js_url ) ); ?>"></script>

	<style id="nexus-article-reader-dossier-label">
		.nexus-article-reader-header ~ .nexus-single-container .nexus-article-hero--editorial::before {
			content: <?php echo $reader_dossier_css_label; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS string, escaped above. ?>;
		}
	</style>
<?php endif; ?>
