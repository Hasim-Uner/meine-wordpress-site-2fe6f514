<?php
/**
 * Global site footer.
 *
 * Ein Fuss fuer alle Seitentypen — Home, Solar/Energy und Audit rendern
 * dieselbe Komponente.
 *
 * Er hatte drei Jobs gleichzeitig in vier gleich schweren Bloecken: abschliessen,
 * navigieren, Vertrauen belegen. Jetzt gibt es zwei Lautstaerken. Laut sind die
 * drei Ich-Saetze, jeder fuehrt auf seinen kommerziellen Weg (Agentur, Energie,
 * direktes Projekt — die drei Einstiege aus AGENTS.md), plus die Direktzeile
 * darunter. Leise sind Verzeichnis- und Absenderzeile.
 *
 * Ersatzlos entfallen: die Spalten "Leistungen", "Belege" und "Person &
 * Kontakt" samt Spaltenkoepfen, die Markenzeile mit Claim und das
 * Messprotokoll als eigener Streifen. Die Leistungsziele stehen sitewide im
 * Hauptmenue; ein zweiter Link vom selben Dokument auf dasselbe Ziel bringt
 * intern nichts dazu. Der Claim steht in Titel, Meta-Description und Kopf. Die
 * vier Protokollangaben lesen sich in der Absenderzeile wie das Briefpapier,
 * das sie sind.
 *
 * Die drei cta_footer_pick_*-Werte bleiben unveraendert, damit die Zeitreihe
 * ueber den Umbau hinweg vergleichbar bleibt; dasselbe gilt fuer die
 * cta_footer_nav_*-Werte der ueberlebenden Verzeichnisziele.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_year = wp_date( 'Y' );
$primary_urls = function_exists( 'nexus_get_primary_public_url_map' ) ? nexus_get_primary_public_url_map() : [];
$routes       = function_exists( 'hu_get_commercial_route_map' ) ? hu_get_commercial_route_map() : [];

$energy_url     = $routes['energy'] ?? ( $primary_urls['energy'] ?? home_url( '/solar-waermepumpen-leadgenerierung/' ) );
$freelancer_url = $routes['freelancer'] ?? home_url( '/wordpress-freelancer-hannover/' );
$whitelabel_url = $routes['whitelabel'] ?? ( function_exists( 'nexus_get_whitelabel_page_url' ) ? nexus_get_whitelabel_page_url() : home_url( '/whitelabel-retainer/' ) );
$about_url      = $routes['about'] ?? ( $primary_urls['about'] ?? home_url( '/hasim-uener/' ) );
$e3_url         = $primary_urls['e3'] ?? home_url( '/case-study-solar-leadgenerierung/' );
$blog_url       = $primary_urls['blog'] ?? home_url( '/blog/' );
$glossary_url   = $primary_urls['glossary'] ?? home_url( '/glossar/' );
$contact_url    = $routes['contact'] ?? ( $primary_urls['contact'] ?? nexus_get_contact_url() );
$imprint_url    = $primary_urls['impressum'] ?? home_url( '/impressum/' );
$privacy_url    = $primary_urls['datenschutz'] ?? home_url( '/datenschutz/' );

$contact_email = function_exists( 'hu_get_contact_email' ) ? hu_get_contact_email() : 'kontakt@hasimuener.de';
$phone_link    = function_exists( 'hu_get_contact_phone' ) ? hu_get_contact_phone( 'link' ) : '';
$phone_display = function_exists( 'hu_get_contact_phone' ) ? hu_get_contact_phone( 'display' ) : '';

/*
 * Die drei Ich-Saetze. Der fette Teil benennt, wer spricht; der Rest sagt, was
 * fehlt. Aufgeteilt in drei Stuecke, damit jedes einzeln durch esc_html() geht
 * und trotzdem ein echtes <b> im Satz stehen kann.
 */
$picks = [
	[
		'pre'    => 'Ich bin ',
		'strong' => 'Agentur',
		'post'   => ' und brauche Technik unter meinem Namen.',
		'url'    => $whitelabel_url,
		'track'  => 'cta_footer_pick_agency',
	],
	[
		'pre'    => 'Ich bin ',
		'strong' => 'Solar- oder Wärmepumpenbetrieb',
		'post'   => ' und will eigene Anfragen statt Portalleads.',
		'url'    => $energy_url,
		'track'  => 'cta_footer_pick_energy',
	],
	[
		'pre'    => 'Ich habe ',
		'strong' => 'eine Seite',
		'post'   => ', die zu wenig Anfragen bringt.',
		'url'    => $freelancer_url,
		'track'  => 'cta_footer_pick_project',
	],
];

/*
 * Direktzeile: drei Wege, kein Formularzwang. "Kontaktformular" statt
 * "Direktkontakt" — wer nicht mailen will, sucht ein Formular und findet es
 * neben Adresse und Nummer statt in einer Linkspalte.
 */
$direct = [
	[
		'label' => $contact_email,
		'url'   => 'mailto:' . $contact_email,
		'track' => 'cta_footer_mail',
	],
];

if ( '' !== $phone_link && '' !== $phone_display ) {
	$direct[] = [
		'label' => $phone_display,
		'url'   => $phone_link,
		'track' => 'cta_footer_tel',
	];
}

$direct[] = [
	'label' => 'Kontaktformular',
	'url'   => $contact_url,
	'track' => 'cta_footer_form',
];

/*
 * Verzeichnis: eine Zeile statt drei Spalten. "Ergebnisse & Case Studies" und
 * "Fallstudie: Solar Leadgenerierung" waren zwei Links auf einen Fall — der
 * Fall bleibt, der Hub faellt weg.
 */
$directory = [
	[ $about_url, 'Über Haşim', 'cta_footer_nav_about', 'navigation' ],
	[ $e3_url, 'Fallstudie Solar', 'cta_footer_nav_case_study_proof', 'trust' ],
	[ $blog_url, 'Blog', 'cta_footer_nav_insights', 'navigation' ],
	[ $glossary_url, 'Glossar', 'cta_footer_nav_glossary', 'navigation' ],
	[ $imprint_url, 'Impressum', 'cta_footer_nav_imprint', 'navigation' ],
	[ $privacy_url, 'Datenschutz', 'cta_footer_nav_privacy', 'navigation' ],
];

/*
 * Absenderzeile. Die Antwortzeit kommt aus dem Messaging-Canon, damit sie nicht
 * ein weiteres Mal irgendwo hart steht und beim naechsten Wechsel gegen
 * /kontakt/ auseinanderlaeuft. Die uebrigen Angaben sind konstant.
 */
$response_value = '';
if ( function_exists( 'hu_response_promise' ) ) {
	$response_value = hu_response_promise( 'value' );
} elseif ( defined( 'HU_RESPONSE_HOURS' ) ) {
	$response_value = sprintf( '%d Stunden werktags', HU_RESPONSE_HOURS );
}

$imprint = [
	[
		'text' => sprintf( '© %s Haşim Üner', $current_year ),
	],
	[
		'text' => 'Pattensen, Region Hannover',
	],
	[
		'text' => 'remote in DACH',
	],
];

if ( '' !== $response_value ) {
	$imprint[] = [
		'text'  => 'Antwort in ',
		'value' => $response_value,
	];
}

$imprint[] = [
	'text'  => 'Messung ',
	'value' => 'ohne Cookie-Banner',
];

$pick_arrow = '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>';

/*
 * Der Fuss sitzt unter dem nutzbaren Seiteninhalt. Sein Stylesheet deshalb an
 * dieser Stelle laden statt ein weiteres renderblockierendes Asset in den
 * Dokumentkopf zu haengen.
 */
$footer_style_path = get_stylesheet_directory() . '/assets/css/site-footer.css';
$footer_style_url  = get_stylesheet_directory_uri() . '/assets/css/site-footer.css';
if ( file_exists( $footer_style_path ) ) {
	$footer_style_version = function_exists( 'hu_get_asset_version' )
		? hu_get_asset_version( $footer_style_path )
		: (string) filemtime( $footer_style_path );
	$footer_style_url = add_query_arg( 'ver', $footer_style_version, $footer_style_url );
}
?>
<link rel="stylesheet" id="nexus-site-footer-css" href="<?php echo esc_url( $footer_style_url ); ?>" media="all">

<footer id="footer" class="ft" role="contentinfo">
	<div class="ft__scale" aria-hidden="true"></div>

	<div class="ft__inner">
		<?php
		/*
		 * Der Kicker ist die sichtbare Ueberschrift der Routen und benennt
		 * gleichzeitig die Navigation. Vorher stand hier eine per .ft__sr
		 * versteckte h2 "Footer-Navigation" — die Klasse war in keinem
		 * ausgelieferten Stylesheet definiert, also stand die Ueberschrift
		 * sichtbar ueber dem Fuss. Ersatzlos aufgeloest.
		 */
		?>
		<nav class="ft-pick" aria-labelledby="ft-pick-kicker">
			<span class="ft-pick__kicker" id="ft-pick-kicker">Was trifft zu?</span>

			<ul class="ft-pick__list">
				<?php foreach ( $picks as $pick ) : ?>
					<li class="ft-pick__item">
						<a
							href="<?php echo esc_url( (string) $pick['url'] ); ?>"
							data-track-action="<?php echo esc_attr( (string) $pick['track'] ); ?>"
							data-track-category="lead_gen"
							data-track-section="footer"
						>
							<span class="ft-pick__line"><?php
								echo esc_html( (string) $pick['pre'] );
								?><b><?php echo esc_html( (string) $pick['strong'] ); ?></b><?php
								echo esc_html( (string) $pick['post'] );
							?></span>
							<span class="ft-pick__go" aria-hidden="true"><?php echo $pick_arrow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static inline SVG ?></span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>

			<p class="ft-direct">
				<span class="ft-direct__label">Lieber direkt</span>
				<?php foreach ( $direct as $entry ) : ?>
					<a
						href="<?php echo esc_url( (string) $entry['url'], [ 'http', 'https', 'mailto', 'tel' ] ); ?>"
						data-track-action="<?php echo esc_attr( (string) $entry['track'] ); ?>"
						data-track-category="lead_gen"
						data-track-section="footer"
					><?php echo esc_html( (string) $entry['label'] ); ?></a>
				<?php endforeach; ?>
			</p>
		</nav>

		<nav class="ft-close" aria-label="Weitere Seiten">
			<?php foreach ( $directory as $link ) : ?>
				<a
					href="<?php echo esc_url( (string) $link[0] ); ?>"
					data-track-action="<?php echo esc_attr( (string) $link[2] ); ?>"
					data-track-category="<?php echo esc_attr( (string) $link[3] ); ?>"
					data-track-section="footer"
				><?php echo esc_html( (string) $link[1] ); ?></a>
			<?php endforeach; ?>
		</nav>

		<p class="ft-imprint">
			<?php foreach ( $imprint as $index => $entry ) : ?>
				<?php if ( $index > 0 ) : ?>
					<span class="ft-imprint__sep" aria-hidden="true">·</span>
				<?php endif; ?>
				<span><?php
					echo esc_html( (string) $entry['text'] );
					if ( isset( $entry['value'] ) ) {
						?><b><?php echo esc_html( (string) $entry['value'] ); ?></b><?php
					}
				?></span>
			<?php endforeach; ?>

			<a
				class="ft-imprint__social"
				href="https://www.linkedin.com/in/hasim-uener/"
				aria-label="LinkedIn-Profil"
				rel="me noopener noreferrer"
				target="_blank"
			>
				<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20.5 2h-17A1.5 1.5 0 0 0 2 3.5v17A1.5 1.5 0 0 0 3.5 22h17a1.5 1.5 0 0 0 1.5-1.5v-17A1.5 1.5 0 0 0 20.5 2zM8 19H5v-9h3zM6.5 8.25A1.75 1.75 0 1 1 8.3 6.5a1.78 1.78 0 0 1-1.8 1.75zM19 19h-3v-4.74c0-1.42-.6-1.93-1.38-1.93A1.74 1.74 0 0 0 13 14.19V19h-3v-9h2.9v1.3a3.11 3.11 0 0 1 2.7-1.4c1.55 0 3.36.86 3.36 3.66z"/></svg>
			</a>
		</p>
	</div>
</footer>
