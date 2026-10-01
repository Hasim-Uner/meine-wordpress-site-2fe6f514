<?php
/**
 * Global site footer.
 *
 * Ein Fuss fuer alle Seitentypen. Seit 2026-09 steht er auf dem
 * Designsystem (`assets/css/system.css`) statt auf einem eigenen
 * Farbsystem — `site-footer.css` ist damit abgeloest, nicht ergaenzt.
 *
 * Zwei Lautstaerken bleiben. Laut ist das Tuerregister: vier Wege mit ihrem
 * Selbstauskunftssatz, darunter je Tuer eine Zeile mit Bezeichnung, Betrag und
 * Pfeil. Leise sind das gruppierte Verzeichnis (Leistungen, Belege & Person,
 * Wissen, Rechtliches) und die Absenderzeile. Wege kommen aus
 * hu_get_site_footer_navigation_contract() (inc/commercial-routing.php), Tueren
 * aus hu_funnel_doors() (inc/funnel-doors.php) — dieselben wie im Kopf. Dieses
 * Template entscheidet nur, was auf der aktuellen Route erscheint.
 *
 * Die Route, auf der sich jemand bereits befindet, wird nicht mehr ausgeblendet,
 * sondern markiert (Kante in --stempel, Etikett "Ihr Weg"); hu_funnel_context()
 * liefert sie. Auf der Seite selbst zeigen ihre Tueren auf Anker der Seite.
 * Das Register erscheint damit auch auf der Startseite und der Solar-Seite. Nur
 * /kontakt/ zeigt es nicht: die Seite ist das Ziel jeder Tuer. Dasselbe gilt
 * fuer Money Pages, die in hu_footer_register_suppressed_templates() stehen
 * (derzeit /conversion-optimierung/). Die Betraege kommen aus dem Kanon; keine
 * Zahl steht in diesem Template.
 *
 * Tracking: `cta_footer_door_<schluessel>` mit `data-door`. Die frueheren
 * `cta_footer_pick_*` sind entfallen.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_year    = wp_date( 'Y' );
$primary_urls    = function_exists( 'nexus_get_primary_public_url_map' ) ? nexus_get_primary_public_url_map() : [];
$routes          = function_exists( 'hu_get_commercial_route_map' ) ? hu_get_commercial_route_map() : [];
$footer_contract = function_exists( 'hu_get_site_footer_navigation_contract' )
	? hu_get_site_footer_navigation_contract()
	: [ 'routes' => [], 'directory' => [] ];
$funnel_context  = function_exists( 'hu_funnel_context' )
	? hu_funnel_context()
	: [ 'mode' => 'voll', 'door' => 'projekt', 'route' => '' ];
$funnel_doors    = function_exists( 'hu_funnel_doors' ) ? hu_funnel_doors() : [];

/*
 * Das Register steht auf jeder Seite, nur nicht auf der Kontaktseite und nicht
 * auf Money Pages mit eigenem Abschluss. Die Entscheidung liegt zentral in
 * hu_footer_shows_register() (inc/funnel-doors.php); dieses Template liest sie
 * nur.
 */
$shows_register = function_exists( 'hu_footer_shows_register' )
	? hu_footer_shows_register()
	: ! ( function_exists( 'nexus_is_contact_page' ) && nexus_is_contact_page() );
$register       = (array) ( $footer_contract['routes'] ?? [] );
$current_route  = (string) $funnel_context['route'];

$contact_url = $routes['contact'] ?? ( $primary_urls['contact'] ?? nexus_get_contact_url() );
$form_url    = $contact_url;

$contact_email = function_exists( 'hu_get_contact_email' ) ? hu_get_contact_email() : 'kontakt@hasimuener.de';
$phone_link    = function_exists( 'hu_get_contact_phone' ) ? hu_get_contact_phone( 'link' ) : '';
$phone_display = function_exists( 'hu_get_contact_phone' ) ? hu_get_contact_phone( 'display' ) : '';

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
 * leiser als die vier kommerziellen Entscheidungen. Vier kleine Gruppen statt
 * einer Zeile, in der die lokale Agentur-Seite neben dem Impressum stand. Die
 * lokale Agentur-Seite bleibt bewusst hier statt im Header: Sie besitzt
 * lokale WordPress-Queries, ist aber kein globaler Geschaeftspfad.
 *
 * Ein Eintrag, der auf die aufgerufene Seite zeigt, bleibt stehen (die
 * Gruppen sollen auf jeder Seite gleich aussehen), traegt aber
 * aria-current="page".
 */
$directory    = (array) ( $footer_contract['directory'] ?? [] );
$request_path = function_exists( 'nexus_get_current_request_path' ) ? nexus_get_current_request_path() : '';
$is_here      = static function ( $url ) use ( $request_path ) {
	if ( '' === $request_path || '' !== (string) wp_parse_url( (string) $url, PHP_URL_FRAGMENT ) ) {
		return false;
	}

	$path = (string) wp_parse_url( (string) $url, PHP_URL_PATH );

	return '' !== $path && trailingslashit( $path ) === $request_path;
};

/*
 * Auf der Seite, auf der die Tuer liegt, zeigt sie auf den Anker der Seite
 * statt auf sich selbst (Solar-Seite: #marktcheck, #einstieg). Auf jeder
 * anderen Seite bleibt das Ziel unveraendert.
 */
$door_href = static function ( $url ) use ( $request_path ) {
	$fragment = (string) wp_parse_url( (string) $url, PHP_URL_FRAGMENT );
	$path     = (string) wp_parse_url( (string) $url, PHP_URL_PATH );

	if ( '' !== $fragment && '' !== $request_path && trailingslashit( $path ) === $request_path ) {
		return '#' . $fragment;
	}

	return (string) $url;
};

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

?>

<footer id="footer" class="fuss" role="contentinfo">
	<div class="blatt">
		<?php if ( $shows_register && ! empty( $register ) ) : ?>
			<nav class="register" aria-labelledby="fuss-wahl">
				<div class="register-kopf">
					<span id="fuss-wahl">Welcher Weg passt?</span>
					<span>Preise netto · <?php echo esc_html( function_exists( 'hu_response_promise' ) ? hu_response_promise() : '' ); ?></span>
				</div>

				<?php foreach ( $register as $way ) : ?>
					<?php $is_current_way = '' !== $current_route && (string) $way['route'] === $current_route; ?>
					<div class="weg<?php echo $is_current_way ? ' ist-hier' : ''; // raw-ok -- static class. ?>" role="group" aria-labelledby="fuss-weg-<?php echo esc_attr( (string) $way['route'] ); ?>">
						<p class="satz" id="fuss-weg-<?php echo esc_attr( (string) $way['route'] ); ?>"><?php
							echo esc_html( (string) $way['pre'] );
							?><b><?php echo esc_html( (string) $way['strong'] ); ?></b><?php
							echo esc_html( (string) $way['post'] );
							if ( $is_current_way ) :
								?><span class="hier">Ihr Weg</span><?php
							endif;
						?></p>

						<div class="tueren">
							<?php foreach ( (array) $way['doors'] as $door_key ) : ?>
								<?php
								if ( ! isset( $funnel_doors[ $door_key ] ) ) {
									continue;
								}

								$door = $funnel_doors[ $door_key ];
								$amount = (string) ( $door['footer_amount'] ?? $door['amount'] );
								?>
								<a
									class="<?php echo 'paid' === $door['tier'] ? 'bezahlt' : 'ohne-betrag'; ?>"
									href="<?php echo esc_url( $door_href( $door['url'] ) ); ?>"
									data-door="<?php echo esc_attr( $door['key'] ); ?>"
									data-track-action="<?php echo esc_attr( 'cta_footer_door_' . $door['key'] ); ?>"
									data-track-category="lead_gen"
									data-track-section="footer"
								>
									<span class="wie"><?php echo esc_html( $door['footer_label'] ); ?></span>
									<span class="betrag"><?php echo esc_html( '' !== $amount ? $amount : __( 'nach Umfang', 'blocksy-child' ) ); ?></span>
									<span class="pf" aria-hidden="true">&rarr;</span>
								</a>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endforeach; ?>
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
			<?php foreach ( $directory as $group ) : ?>
				<?php $group_id = 'fuss-' . sanitize_key( (string) ( $group['key'] ?? '' ) ); ?>
				<div class="gruppe">
					<span class="was" id="<?php echo esc_attr( $group_id ); ?>"><?php echo esc_html( (string) ( $group['title'] ?? '' ) ); ?></span>
					<ul aria-labelledby="<?php echo esc_attr( $group_id ); ?>">
						<?php foreach ( (array) ( $group['items'] ?? [] ) as $link ) : ?>
							<li><a
								href="<?php echo esc_url( (string) $link['url'] ); ?>"
								<?php echo $is_here( $link['url'] ) ? ' aria-current="page"' : ''; // raw-ok -- static attribute. ?>
								data-track-action="<?php echo esc_attr( (string) $link['track'] ); ?>"
								data-track-category="<?php echo esc_attr( (string) $link['category'] ); ?>"
								data-track-section="footer"
							><?php echo esc_html( (string) $link['label'] ); ?></a></li>
						<?php endforeach; ?>
					</ul>
				</div>
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
