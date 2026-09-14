<?php
/**
 * Global site footer.
 *
 * Ein Fuss fuer alle Seitentypen. Seit 2026-09 steht er auf dem
 * Designsystem (`assets/css/system.css`) statt auf einem eigenen
 * Farbsystem — `site-footer.css` ist damit abgeloest, nicht ergaenzt.
 *
 * Zwei Lautstaerken bleiben. Laut sind die vier kommerziellen Wege aus dem
 * globalen Routing-Contract: direktes WordPress-Projekt, Tracking,
 * White-Label und Solar/Waermepumpe. Leise sind Query-Owner, Belege, Wissen,
 * Rechtliches und die Absenderzeile.
 *
 * Die Route, auf der sich jemand bereits befindet, wird in der lauten Wahl
 * nicht noch einmal angeboten. Der Footer bleibt damit ein Orientierungs-
 * und Wechselpunkt statt einer Sammlung von Selbstlinks.
 *
 * Auf der Startseite ist der direkte Freelancer-Pfad bereits ausgefuehrt;
 * die erneute Wegewahl im Footer entfaellt dort komplett. Kontakt laeuft ueber
 * die gemeinsame Route.
 *
 * Die bestehenden cta_footer_pick_*-Werte bleiben fuer die historischen Wege
 * unveraendert. Tracking erhaelt einen eigenen stabilen Wert. Dasselbe gilt
 * fuer die cta_footer_nav_*-Werte der Verzeichnisziele.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_year = wp_date( 'Y' );
$primary_urls = function_exists( 'nexus_get_primary_public_url_map' ) ? nexus_get_primary_public_url_map() : [];
$routes       = function_exists( 'hu_get_commercial_route_map' ) ? hu_get_commercial_route_map() : [];
$shows_picks  = ! is_front_page();

$energy_url        = $routes['energy'] ?? ( $primary_urls['energy'] ?? home_url( '/solar-waermepumpen-leadgenerierung/' ) );
$freelancer_url    = $routes['freelancer'] ?? home_url( '/' );
$tracking_url      = $routes['tracking_b2b'] ?? home_url( '/server-side-tracking-b2b/' );
$whitelabel_url    = $routes['whitelabel'] ?? ( function_exists( 'nexus_get_whitelabel_page_url' ) ? nexus_get_whitelabel_page_url() : home_url( '/whitelabel-retainer/' ) );
$agentur_local_url = $routes['agentur_local'] ?? home_url( '/wordpress-agentur-hannover/' );
$results_url       = $routes['results'] ?? ( $primary_urls['results'] ?? home_url( '/ergebnisse/' ) );
$about_url         = $routes['about'] ?? ( $primary_urls['about'] ?? home_url( '/hasim-uener/' ) );
$e3_url            = $primary_urls['e3'] ?? home_url( '/case-study-solar-leadgenerierung/' );
$blog_url          = $primary_urls['blog'] ?? home_url( '/blog/' );
$glossary_url      = $primary_urls['glossary'] ?? home_url( '/glossar/' );
$contact_url       = $routes['contact'] ?? ( $primary_urls['contact'] ?? nexus_get_contact_url() );
$form_url          = $contact_url;
$imprint_url       = $primary_urls['impressum'] ?? home_url( '/impressum/' );
$privacy_url       = $primary_urls['datenschutz'] ?? home_url( '/datenschutz/' );

$contact_email = function_exists( 'hu_get_contact_email' ) ? hu_get_contact_email() : 'kontakt@hasimuener.de';
$phone_link    = function_exists( 'hu_get_contact_phone' ) ? hu_get_contact_phone( 'link' ) : '';
$phone_display = function_exists( 'hu_get_contact_phone' ) ? hu_get_contact_phone( 'display' ) : '';

$current_pick_route = '';

if ( is_page( 'server-side-tracking-b2b' ) || is_page_template( 'page-server-side-tracking-b2b.php' ) ) {
	$current_pick_route = 'tracking';
} elseif ( is_page( 'whitelabel-retainer' ) || is_page_template( 'page-whitelabel-retainer.php' ) ) {
	$current_pick_route = 'whitelabel';
} elseif ( is_page( 'solar-waermepumpen-leadgenerierung' ) || is_page_template( 'page-solar-waermepumpen-leadgenerierung.php' ) ) {
	$current_pick_route = 'energy';
} elseif ( is_front_page() ) {
	$current_pick_route = 'freelancer';
}

/*
 * Die vier Wege folgen derselben Reihenfolge wie der globale Header:
 * direkte Umsetzung, Messung, Agentur-Partnerschaft, Spezialisierung.
 * Das ist absichtlich task-first statt rein zielgruppenbasiert.
 */
$picks = [
	[
		'route'  => 'freelancer',
		'pre'    => 'Ich habe ',
		'strong' => 'eine Website',
		'post'   => ', die neu gebaut oder besser werden soll.',
		'url'    => $freelancer_url,
		'track'  => 'cta_footer_pick_project',
	],
	[
		'route'  => 'tracking',
		'pre'    => 'Ich brauche ',
		'strong' => 'belastbare Messung',
		'post'   => ' für Anfragen, Kampagnen und CRM.',
		'url'    => $tracking_url,
		'track'  => 'cta_footer_pick_tracking',
	],
	[
		'route'  => 'whitelabel',
		'pre'    => 'Ich bin ',
		'strong' => 'Agentur',
		'post'   => ' und brauche Technik unter meinem Namen.',
		'url'    => $whitelabel_url,
		'track'  => 'cta_footer_pick_agency',
	],
	[
		'route'  => 'energy',
		'pre'    => 'Ich bin ',
		'strong' => 'Solar- oder Wärmepumpenbetrieb',
		'post'   => ' und will eigene Anfragen statt Portalleads.',
		'url'    => $energy_url,
		'track'  => 'cta_footer_pick_energy',
	],
];

if ( '' !== $current_pick_route ) {
	$picks = array_values(
		array_filter(
			$picks,
			static function ( $pick ) use ( $current_pick_route ) {
				return (string) $pick['route'] !== $current_pick_route;
			}
		)
	);
}

/*
 * Direktzeile: drei Wege, kein Formularzwang. "Kontaktformular" statt
 * "Direktkontakt" — wer nicht mailen will, sucht ein Formular und findet
 * es neben Adresse und Nummer statt in einer Linkspalte.
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
	'url'   => $form_url,
	'track' => 'cta_footer_form',
];

/*
 * Verzeichnis: breit genug fuer Crawl- und Orientierungswege, aber deutlich
 * leiser als die vier kommerziellen Entscheidungen. Die lokale Agentur-Seite
 * bleibt bewusst hier statt im Header: Sie besitzt lokale WordPress-Queries,
 * ist aber kein globaler Geschaeftspfad. Ergebnisse und Solar-Fallstudie sind
 * getrennt, weil der Hub inzwischen auch technische und weitere oeffentliche
 * Arbeiten belegt.
 */
$directory = [
	[ $results_url, 'Ergebnisse', 'cta_footer_nav_results', 'trust' ],
	[ $agentur_local_url, 'WordPress Agentur Hannover', 'cta_footer_nav_agentur_local', 'navigation' ],
	[ $about_url, 'Über Haşim', 'cta_footer_nav_about', 'navigation' ],
	[ $e3_url, 'Solar-Fallstudie', 'cta_footer_nav_case_study_proof', 'trust' ],
	[ $blog_url, 'Blog', 'cta_footer_nav_insights', 'navigation' ],
	[ $glossary_url, 'Glossar', 'cta_footer_nav_glossary', 'navigation' ],
	[ $imprint_url, 'Impressum', 'cta_footer_nav_imprint', 'navigation' ],
	[ $privacy_url, 'Datenschutz', 'cta_footer_nav_privacy', 'navigation' ],
];

/*
 * Absenderzeile, zwei Spalten, eine Zeile je Angabe. Die Antwortzeit kommt
 * aus dem Messaging-Canon, damit sie nicht ein weiteres Mal irgendwo hart
 * steht und beim naechsten Wechsel gegen /kontakt/ auseinanderlaeuft.
 *
 * Die Anschrift ist bewusst zeichengleich mit 'streetAddress' +
 * 'postalCode' + 'addressLocality' aus hu_output_schema()
 * (inc/org-schema.php), damit sichtbare Angabe, JSON-LD und Google
 * Business Profile dieselbe Zeichenkette tragen. Wer sie hier aendert,
 * aendert sie dort mit. Der regionale Zusatz bleibt eine eigene Zeile:
 * 'Region Hannover' ist kein Bestandteil der postalischen Anschrift und
 * wuerde den Abgleich verwaessern.
 */
$response_value = function_exists( 'hu_response_promise' ) ? hu_response_promise( 'value' ) : '';

$sender_left = [];

if ( '' !== $response_value ) {
	$sender_left[] = [
		'label' => 'Antwort',
		'value' => $response_value,
	];
}

$sender_left[] = [
	'label'   => 'Sitz',
	'value'   => 'Warschauer Str. 5, 30982 Pattensen',
	'address' => true,
];

$sender_left[] = [
	'label' => 'Region',
	'value' => 'Region Hannover',
];

$sender_right = [
	[
		'label' => 'Arbeitsweise',
		'value' => 'remote in DACH, 1:1',
	],
	[
		'label' => 'Messung',
		'value' => 'ohne Cookie-Banner',
	],
];

$pick_arrow = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" width="16" height="16" aria-hidden="true" focusable="false"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>';
?>

<?php if ( is_page( 'kontakt' ) ) : ?>
	<style id="contact-density-polish">
		.contact-page.contact-page--scoped {
			padding-top: clamp(2.25rem, 3.8vw, 3.5rem);
			padding-bottom: 0;
		}

		.contact-page--scoped .contact-page__shell {
			width: min(100%, 84rem);
			grid-template-columns: minmax(13.25rem, 0.42fr) minmax(0, 1.58fr);
			gap: clamp(2rem, 3.6vw, 3.25rem);
		}

		.contact-page--scoped .contact-title {
			max-width: 12ch;
			font-size: clamp(2.35rem, 1.7rem + 1.8vw, 3.5rem);
			line-height: 1.01;
			letter-spacing: -0.04em;
		}

		.contact-page--scoped .contact-lead {
			max-width: 32ch;
			font-size: 0.9rem;
			line-height: 1.58;
		}

		.contact-page--scoped .contact-intro__facts {
			margin-top: 1.15rem;
		}

		.contact-page--scoped .contact-intro__facts p {
			padding-block: 0.66rem;
			font-size: 0.79rem;
		}

		.contact-page--scoped .contact-direct-mail {
			margin-top: 1rem;
		}

		.fuss {
			padding-top: 0;
			border-top: 0;
		}

		.fuss > .blatt {
			max-width: 84rem;
			padding-inline: clamp(1.25rem, 4vw, 3rem);
		}

		.fuss .wahl {
			margin-top: 0;
			margin-bottom: var(--s3);
			padding-top: clamp(1.1rem, 2vw, 1.5rem);
		}

		.fuss .wahl > .mono {
			padding-block: var(--s1);
		}

		@media (max-width: 980px) and (min-width: 821px) {
			.contact-page--scoped .contact-page__shell {
				width: min(100%, 72rem);
				grid-template-columns: minmax(13rem, 0.55fr) minmax(0, 1.45fr);
				gap: 2rem;
			}

			.contact-page--scoped .contact-title {
				font-size: clamp(2.3rem, 4.2vw, 3.15rem);
			}
		}

		@media (max-width: 820px) {
			.contact-page.contact-page--scoped {
				padding-top: 2rem;
				padding-bottom: 0;
			}

			.contact-page--scoped .contact-page__shell {
				grid-template-columns: 1fr;
				gap: 2.25rem;
			}

			.fuss {
				padding-top: 0;
			}
		}
	</style>
<?php endif; ?>

<footer id="footer" class="fuss" role="contentinfo">
	<div class="blatt">
		<?php if ( $shows_picks && ! empty( $picks ) ) : ?>
			<nav class="wahl" aria-labelledby="fuss-wahl">
				<span class="mono" id="fuss-wahl">Welcher Weg passt?</span>

				<ul>
					<?php foreach ( $picks as $pick ) : ?>
						<li>
							<a
								href="<?php echo esc_url( (string) $pick['url'] ); ?>"
								data-track-action="<?php echo esc_attr( (string) $pick['track'] ); ?>"
								data-track-category="lead_gen"
								data-track-section="footer"
							>
								<span><?php
									echo esc_html( (string) $pick['pre'] );
									?><b><?php echo esc_html( (string) $pick['strong'] ); ?></b><?php
									echo esc_html( (string) $pick['post'] );
								?></span>
								<span class="pf" aria-hidden="true"><?php echo $pick_arrow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static inline SVG ?></span>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</nav>
		<?php endif; ?>

		<p class="direkt">
			<span class="was">Lieber direkt</span>
			<?php foreach ( $direct as $entry ) : ?>
				<a
					href="<?php echo esc_url( (string) $entry['url'], [ 'http', 'https', 'mailto', 'tel' ] ); ?>"
					data-track-action="<?php echo esc_attr( (string) $entry['track'] ); ?>"
					data-track-category="lead_gen"
					data-track-section="footer"
				><?php echo esc_html( (string) $entry['label'] ); ?></a>
			<?php endforeach; ?>
		</p>

		<nav class="verzeichnis" aria-label="Weitere Seiten">
			<?php foreach ( $directory as $link ) : ?>
				<a
					href="<?php echo esc_url( (string) $link[0] ); ?>"
					data-track-action="<?php echo esc_attr( (string) $link[2] ); ?>"
					data-track-category="<?php echo esc_attr( (string) $link[3] ); ?>"
					data-track-section="footer"
				><?php echo esc_html( (string) $link[1] ); ?></a>
			<?php endforeach; ?>
		</nav>

		<div class="absender">
			<?php foreach ( [ $sender_left, $sender_right ] as $sender_column ) : ?>
				<div class="protokoll">
					<?php foreach ( $sender_column as $entry ) : ?>
						<div class="z">
							<span><?php echo esc_html( (string) $entry['label'] ); ?></span>
							<?php if ( ! empty( $entry['address'] ) ) : ?>
								<address><?php echo esc_html( (string) $entry['value'] ); ?></address>
							<?php else : ?>
								<b><?php echo esc_html( (string) $entry['value'] ); ?></b>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endforeach; ?>
		</div>

		<div class="schluss">
			<span><?php echo esc_html( sprintf( '© %s Haşim Üner', $current_year ) ); ?></span>

			<a
				href="https://www.linkedin.com/in/hasim-uener/"
				aria-label="LinkedIn-Profil"
				rel="me noopener noreferrer"
				target="_blank"
			>
				<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M20.5 2h-17A1.5 1.5 0 0 0 2 3.5v17A1.5 1.5 0 0 0 3.5 22h17a1.5 1.5 0 0 0 1.5-1.5v-17A1.5 1.5 0 0 0 20.5 2zM8 19H5v-9h3zM6.5 8.25A1.75 1.75 0 1 1 8.3 6.5a1.78 1.78 0 0 1-1.8 1.75zM19 19h-3v-4.74c0-1.42-.6-1.93-1.38-1.93A1.74 1.74 0 0 0 13 14.19V19h-3v-9h2.9v1.3a3.11 3.11 0 0 1 2.7-1.4c1.55 0 3.36.86 3.36 3.66z"/></svg>
			</a>
		</div>
	</div>
</footer>
