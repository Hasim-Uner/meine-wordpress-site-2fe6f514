<?php
/**
 * Global site footer.
 *
 * Ein Fuss fuer alle Seitentypen. Vorher liefen drei Varianten nebeneinander:
 * der grosse Standard-Fuss, ein Audit-Minimalfuss und ein Energy-Minimalfuss,
 * jeder mit eigenen Route-Karten und eigenem CTA. Der Sammel-CTA sprach dabei
 * alle gleichzeitig an und traf niemanden.
 *
 * Stattdessen laesst die Selbstauskunft den Besucher sagen, wer er ist: drei
 * Ich-Saetze, jeder fuehrt auf seinen kommerziellen Weg (Agentur, Energie,
 * direktes Projekt — die drei Einstiege aus AGENTS.md). Danach folgen
 * Direktzeile, Messprotokoll, Verzeichnis und Schlusszeile.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_year = wp_date( 'Y' );
$primary_urls = function_exists( 'nexus_get_primary_public_url_map' ) ? nexus_get_primary_public_url_map() : [];
$routes       = function_exists( 'hu_get_commercial_route_map' ) ? hu_get_commercial_route_map() : [];

$home_url       = $routes['home'] ?? ( $primary_urls['home'] ?? home_url( '/' ) );
$energy_url     = $routes['energy'] ?? ( $primary_urls['energy'] ?? home_url( '/solar-waermepumpen-leadgenerierung/' ) );
$e3_url         = $primary_urls['e3'] ?? home_url( '/case-study-solar-leadgenerierung/' );
$results_url    = $routes['results'] ?? ( $primary_urls['results'] ?? home_url( '/ergebnisse/' ) );
$blog_url       = $primary_urls['blog'] ?? home_url( '/blog/' );
$glossary_url   = $primary_urls['glossary'] ?? home_url( '/glossar/' );
$agentur_url    = $routes['agentur_local'] ?? ( $primary_urls['agentur'] ?? home_url( '/wordpress-agentur-hannover/' ) );
$freelancer_url = $routes['freelancer'] ?? home_url( '/wordpress-freelancer-hannover/' );
$tracking_url   = $routes['tracking_b2b'] ?? home_url( '/server-side-tracking-b2b/' );
$about_url      = $routes['about'] ?? ( $primary_urls['about'] ?? home_url( '/hasim-uener/' ) );
$contact_url    = $routes['contact'] ?? ( $primary_urls['contact'] ?? nexus_get_contact_url() );
$whitelabel_url = $routes['whitelabel'] ?? ( function_exists( 'nexus_get_whitelabel_page_url' ) ? nexus_get_whitelabel_page_url() : home_url( '/whitelabel-retainer/' ) );
$imprint_url    = $primary_urls['impressum'] ?? home_url( '/impressum/' );
$privacy_url    = $primary_urls['datenschutz'] ?? home_url( '/datenschutz/' );

$contact_email = function_exists( 'hu_get_contact_email' ) ? hu_get_contact_email() : 'hallo@hasimuener.de';
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
		'post'   => ' und brauche WordPress-Technik unter meinem Namen.',
		'url'    => $whitelabel_url,
		'track'  => 'cta_footer_pick_agency',
	],
	[
		'pre'    => 'Ich bin ',
		'strong' => 'Solar- oder Wärmepumpenbetrieb',
		'post'   => ' und will eigene Anfragen statt gekaufter Portalleads.',
		'url'    => $energy_url,
		'track'  => 'cta_footer_pick_energy',
	],
	[
		'pre'    => 'Ich habe ',
		'strong' => 'eine Seite',
		'post'   => ', die nicht liefert, was sie soll.',
		'url'    => $freelancer_url,
		'track'  => 'cta_footer_pick_project',
	],
];

/*
 * Messprotokoll: vier nachpruefbare Angaben. Die Antwortzeit kommt aus dem
 * Messaging-Canon, damit sie nicht ein viertes Mal irgendwo hart steht und beim
 * naechsten Wechsel auseinanderlaeuft. Die uebrigen drei sind konstant.
 */
$response_window = '';
if ( function_exists( 'hu_response_promise' ) ) {
	$response_window = hu_response_promise( 'window' );
} elseif ( defined( 'HU_RESPONSE_HOURS' ) ) {
	$response_window = sprintf( 'innerhalb von %d Stunden werktags', HU_RESPONSE_HOURS );
}

$proof = array_values(
	array_filter(
		[
			[
				'label' => 'Antwortzeit',
				'value' => $response_window,
			],
			[
				'label' => 'Sitz',
				'value' => 'Pattensen, Region Hannover',
			],
			[
				'label' => 'Arbeitsweise',
				'value' => 'remote, DACH',
			],
			[
				'label' => 'Messung',
				'value' => 'ohne Cookie-Banner',
			],
		],
		static function ( $entry ) {
			return '' !== $entry['value'];
		}
	)
);

/*
 * Verzeichnis. "Projekt anfragen" faellt hier raus — die Selbstauskunft nennt
 * den Weg oben schon. Die cta_footer_nav_*-Werte bleiben unveraendert, damit die
 * Cockpit-Auswertung ueber den Umbau hinweg vergleichbar bleibt.
 */
$directory = [
	[
		'id'    => 'ft-leistungen',
		'title' => 'Leistungen',
		'links' => [
			[ $freelancer_url, 'WordPress Freelancer', 'cta_footer_nav_freelancer', 'navigation' ],
			[ $tracking_url, 'Server-Side Tracking B2B', 'cta_footer_nav_tracking', 'navigation' ],
			[ $energy_url, 'Solar & Wärmepumpen', 'cta_footer_nav_energy', 'navigation' ],
			[ $agentur_url, 'WordPress Agentur Hannover', 'cta_footer_nav_agentur', 'navigation' ],
		],
	],
	[
		'id'    => 'ft-belege',
		'title' => 'Belege',
		'links' => [
			[ $results_url, 'Ergebnisse & Case Studies', 'cta_footer_nav_results', 'trust' ],
			[ $e3_url, 'Fallstudie: Solar Leadgenerierung', 'cta_footer_nav_case_study_proof', 'trust' ],
			[ $blog_url, 'Insights', 'cta_footer_nav_insights', 'navigation' ],
			[ $glossary_url, 'Glossar für SEO, Tracking und Anfragesysteme', 'cta_footer_nav_glossary', 'navigation' ],
		],
	],
	[
		'id'    => 'ft-person',
		'title' => 'Person & Kontakt',
		'links' => [
			[ $whitelabel_url, 'Für Agenturen: White-Label', 'cta_footer_nav_whitelabel', 'lead_gen' ],
			[ $about_url, 'Über Haşim', 'cta_footer_nav_about', 'navigation' ],
			[ $contact_url, 'Direktkontakt', 'cta_footer_nav_contact', 'navigation' ],
		],
	],
];

$pick_arrow = '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M4 12h15M13 6l6 6-6 6"></path></svg>';

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

<footer id="footer" class="ft" aria-labelledby="ft-heading" role="contentinfo">
	<div class="ft__inner">
		<h2 id="ft-heading" class="ft__sr">Footer-Navigation</h2>

		<nav class="ft-pick" aria-label="Passenden Weg wählen">
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
		</nav>

		<div class="ft-direct">
			<a
				href="<?php echo esc_url( 'mailto:' . $contact_email ); ?>"
				data-track-action="cta_footer_direct_mail"
				data-track-category="lead_gen"
				data-track-section="footer"
			><?php echo esc_html( $contact_email ); ?></a>
			<?php if ( '' !== $phone_link && '' !== $phone_display ) : ?>
			<a
				href="<?php echo esc_url( $phone_link, [ 'tel' ] ); ?>"
				data-track-action="cta_footer_direct_phone"
				data-track-category="lead_gen"
				data-track-section="footer"
			><?php echo esc_html( $phone_display ); ?></a>
			<?php endif; ?>
		</div>

		<dl class="ft-proof">
			<?php foreach ( $proof as $entry ) : ?>
				<div>
					<dt class="ft-proof__label"><?php echo esc_html( (string) $entry['label'] ); ?></dt>
					<dd class="ft-proof__value"><?php echo esc_html( (string) $entry['value'] ); ?></dd>
				</div>
			<?php endforeach; ?>
		</dl>

		<div class="ft-dir">
			<div class="ft-dir__brand">
				<a class="ft-dir__logo site-logo" href="<?php echo esc_url( $home_url ); ?>" aria-label="Startseite - HAŞIM ÜNER">HAŞIM ÜNER</a>
				<p class="ft-dir__tag">WordPress, Tracking und Conversion als zusammenhängendes System — direkt mit Haşim Üner.</p>
			</div>

			<?php foreach ( $directory as $column ) : ?>
				<section class="ft-dir__col" aria-labelledby="<?php echo esc_attr( (string) $column['id'] ); ?>">
					<h3 id="<?php echo esc_attr( (string) $column['id'] ); ?>"><?php echo esc_html( (string) $column['title'] ); ?></h3>
					<ul class="ft-dir__list">
						<?php foreach ( $column['links'] as $link ) : ?>
							<li>
								<a
									href="<?php echo esc_url( (string) $link[0] ); ?>"
									data-track-action="<?php echo esc_attr( (string) $link[2] ); ?>"
									data-track-category="<?php echo esc_attr( (string) $link[3] ); ?>"
									data-track-section="footer"
								><?php echo esc_html( (string) $link[1] ); ?></a>
							</li>
						<?php endforeach; ?>
					</ul>
				</section>
			<?php endforeach; ?>
		</div>

		<div class="ft-end">
			<p>&copy; <time class="ft__year" datetime="<?php echo esc_attr( $current_year ); ?>"><?php echo esc_html( $current_year ); ?></time> Haşim Üner - WordPress, Tracking &amp; Conversion</p>

			<nav class="ft-end__meta" aria-label="Rechtliches">
				<a href="<?php echo esc_url( $imprint_url ); ?>">Impressum</a>
				<span aria-hidden="true">·</span>
				<a href="<?php echo esc_url( $privacy_url ); ?>">Datenschutz</a>
			</nav>

			<div class="ft-end__social">
				<a href="https://www.linkedin.com/in/hasim-uener/" aria-label="LinkedIn-Profil" rel="me noopener noreferrer" target="_blank">
					<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20.5 2h-17A1.5 1.5 0 0 0 2 3.5v17A1.5 1.5 0 0 0 3.5 22h17a1.5 1.5 0 0 0 1.5-1.5v-17A1.5 1.5 0 0 0 20.5 2zM8 19H5v-9h3zM6.5 8.25A1.75 1.75 0 1 1 8.3 6.5a1.78 1.78 0 0 1-1.8 1.75zM19 19h-3v-4.74c0-1.42-.6-1.93-1.38-1.93A1.74 1.74 0 0 0 13 14.19V19h-3v-9h2.9v1.3a3.11 3.11 0 0 1 2.7-1.4c1.55 0 3.36.86 3.36 3.66z"/></svg>
					LinkedIn
				</a>
			</div>
		</div>
	</div>
</footer>
