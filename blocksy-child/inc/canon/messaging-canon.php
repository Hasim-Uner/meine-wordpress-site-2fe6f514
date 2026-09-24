<?php
/**
 * Canonical customer-facing messaging anchors and wording guardrails.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define(
	'HU_MESSAGE_VALUE_ANCHOR_ARCHITECTURE',
	'WordPress, Tracking und Conversion gehören zusammen. Ich entwickle die technische Basis und messe, was daraus entsteht.'
);

define(
	'HU_MESSAGE_VALUE_ANCHOR_PRICE',
	'Scope und Preis stehen vor dem Start fest. Kein Paketpreis ohne geklärten Umfang.'
);

// ── Oeffentliche Kontaktadresse ───────────────────────────────────
// Eine Adresse, ueberall: kontakt@hasimuener.de. Die White-Label-Seite, die
// Solar-Seite und das Organization-Schema nannten frueher je eine eigene —
// drei Adressen fuer denselben Zweck, zwei davon auf indexierten Seiten.
// Sichtbare Kontaktwege lesen seither ab hier.
//
// Die abgeloesten Aliasse nehmen weiter Post an, werden aber nirgends mehr
// ausgegeben: zwei aktive Adressen sitewide sind eine Frage zu viel fuer den
// Empfaenger. Welche das sind, steht als Sperrliste in
// scripts/canon-forbidden-values.txt und wird von scripts/canon-guard.sh
// erzwungen — nicht hier, damit es genau eine Liste davon gibt.
//
// Seit 2026-09-18 ohne Ausnahme: Impressum und Datenschutzerklaerung lesen
// denselben Canon. Der rechtlich benannte Kontaktweg ist derselbe wie der
// beworbene; ihn getrennt zu pflegen hat nur die Chance erhoeht, dass eine der
// beiden Seiten stehen bleibt.
//
// Der tatsaechliche Absender der Transaktionsmails kommt aus der
// Laufzeitkonfiguration (NEXUS_BREVO_FROM_EMAIL et al. in inc/mail.php), nicht
// von hier. Wo Copy den Absender benennt, muss beides zusammenpassen.
define( 'HU_CONTACT_EMAIL', 'kontakt@hasimuener.de' );
define( 'HU_CONTACT_EMAIL_MAILTO', 'mailto:' . HU_CONTACT_EMAIL );

/**
 * Public contact address for visible contact paths.
 *
 * @return string
 */
function hu_get_contact_email() {
	return HU_CONTACT_EMAIL;
}

/**
 * Canonical mailto link for the public contact address.
 *
 * Eigener Getter, damit kein Aufrufer 'mailto:' selbst davorschreibt: genau
 * dort entstand die zweite Fassung, als Adresse und Link in verschiedenen
 * Zeilen gepflegt wurden.
 *
 * @return string
 */
function hu_get_contact_mailto() {
	return HU_CONTACT_EMAIL_MAILTO;
}

// Telefonnummer als zweiter direkter Weg. Impressum und Datenschutz tragen die
// Nummer bis heute je einmal hart im Template; der Fuss holt sie hier, damit
// eine dritte Kopie gar nicht erst entsteht.
define( 'HU_CONTACT_PHONE', '+4917676596580' );
define( 'HU_CONTACT_PHONE_DISPLAY', '0176 76596580' );

/**
 * Public phone number for visible contact paths.
 *
 * @param string $variant One of: link, display.
 * @return string
 */
function hu_get_contact_phone( $variant = 'display' ) {
	if ( 'link' === $variant ) {
		return 'tel:' . HU_CONTACT_PHONE;
	}

	return HU_CONTACT_PHONE_DISPLAY;
}

// ── Antwortzeit auf eine Anfrage ──────────────────────────────────
// Eine Zusage, ueberall: "innerhalb von 24 Stunden werktags". Sie gilt fuer
// jede Anfrage und seit 2026-09-18 auch fuer den Marktcheck-Befund
// (hu_marketcheck_reply_label() in canon/diagnose-canon.php liest von hier).
//
// Vorgeschichte, damit keine der abgeloesten Fassungen zurueckkommt:
// Startseite und White-Label-Seite versprachen eine Frist in Stunden, die
// Kontaktseite als gemeinsames Ziel beider CTAs eine deutlich weichere in
// Werktagen — die schwaechste Fassung stand genau dort, wo abgeschickt wird,
// das Versprechen brach im Formular. Die Vereinheitlichung auf Werktage
// (2026-09-12) hat das behoben, aber die langsamste Fassung zur Norm gemacht.
//
// Der Einwand gegen die Stundenangabe war, dass sie ueber ein Wochenende
// nicht einloesbar ist. Das traegt der Zusatz "werktags": gezaehlt werden
// Arbeitsstunden, nicht Kalenderstunden. Wer freitags abends schreibt, hat
// damit dieselbe Zusage wie wer dienstags morgens schreibt.
//
// Die Sperrliste der abgeloesten Fassungen steht in
// scripts/canon-forbidden-values.txt, nicht hier.
define( 'HU_RESPONSE_HOURS', 24 );
define( 'HU_RESPONSE_PROMISE_SHORT', 'innerhalb von ' . HU_RESPONSE_HOURS . ' Stunden werktags' );
define( 'HU_RESPONSE_PROMISE_SENTENCE', 'Antwort ' . HU_RESPONSE_PROMISE_SHORT . '.' );

/**
 * Kurzform der Antwortzusage: der nackte Wert ohne Rahmensatz.
 *
 * Fuer Label-Spalten, Kacheln und Microcopy, die "Antwort" schon als Label
 * tragen — der Fuss setzt ihn in ein <b>.
 *
 * @return string
 */
function hu_response_promise_short() {
	return HU_RESPONSE_PROMISE_SHORT;
}

/**
 * Satzform der Antwortzusage: abgeschlossener Satz mit Punkt.
 *
 * @return string
 */
function hu_response_promise_sentence() {
	return HU_RESPONSE_PROMISE_SENTENCE;
}

/**
 * Display value for the canonical response promise.
 *
 * Sechs Fassungen derselben Zusage, damit kein Aufrufer sie selbst
 * zusammensetzt:
 *
 * - `value`    der nackte Wert fuer Label-Spalten (Kurzform).
 * - `window`   die Praepositionalfassung fuer den Fliesstext ("Sie erhalten
 *              … eine Rueckmeldung"). Mit "innerhalb von" identisch zur
 *              Kurzform — beide Namen bleiben, weil die Aufrufer sie
 *              unterschiedlich rahmen.
 * - `badge`    die knappste Fassung fuer Menue-CTAs und Kennzahlkacheln, wo
 *              die Kurzform ueber zwei Zeilen laufen wuerde. Traegt "werktags"
 *              weiter mit: ohne den Zusatz waere es eine andere Zusage.
 * - `compact`  eine Zeile fuer Metadaten und Microcopy.
 * - `sentence` ein abgeschlossener Satz.
 * - `phrase`   derselbe Satz ohne Punkt, fuer Aufrufer, die selbst
 *              interpunktieren.
 *
 * @param string $variant One of: phrase, sentence, compact, window, value, badge.
 * @return string
 */
function hu_response_promise( $variant = 'phrase' ) {
	$short = hu_response_promise_short();

	if ( 'value' === $variant || 'window' === $variant ) {
		return $short;
	}

	if ( 'badge' === $variant ) {
		return sprintf( '%d h werktags', HU_RESPONSE_HOURS );
	}

	if ( 'sentence' === $variant ) {
		return hu_response_promise_sentence();
	}

	return sprintf( 'Antwort %s', $short );
}

// ── Versuch: Kostenlose Ersteinschaetzung ─────────────────────────
// Direktinteressenten ohne fertiges Projekt bekommen einen niedrigschwelligen
// ersten Schritt: URL schicken, drei Befunde schriftlich zurueck. Laufzeit,
// Messgroesse und Abbruchregel stehen in docs/experimente/ersteinschaetzung.md.
//
// Der Schalter nimmt den Versuch vollstaendig zurueck. Aus heisst: Startseite
// und /kontakt/ rendern exakt wie vorher, ?focus=ersteinschaetzung faellt
// auf die normale Projektanfrage zurueck. Abschalten ohne Deploy geht ueber
// wp-config.php:
//
//   define( 'HU_EXPERIMENT_ERSTEINSCHAETZUNG', false );
//
// Eine Ausnahme: Der Endpoint contact-request nimmt das Anliegen auch bei
// ausgeschaltetem Schalter an. Sonst ginge eine Einsendung aus einer noch
// zwischengespeicherten Seite nach dem Abschalten verloren.
//
// Gezaehlt wird nur ueber den Betreff der Mails (Praefix unten). Kein Cookie,
// kein Skript, kein Consent-Banner.
defined( 'HU_EXPERIMENT_ERSTEINSCHAETZUNG' ) || define( 'HU_EXPERIMENT_ERSTEINSCHAETZUNG', true );

// Kennung in URL (?focus=), Formular und CRM (_nexus_contact_request_type).
// Nie aendern, solange der Versuch laeuft: sonst zaehlen alte Links nicht mehr.
define( 'HU_FIRST_ASSESSMENT_KEY', 'ersteinschaetzung' );
define( 'HU_FIRST_ASSESSMENT_LABEL', 'Ersteinschätzung' );
// "Ein Satz": das Ziel-Feld ist bewusst keine zweite Nachricht.
define( 'HU_FIRST_ASSESSMENT_GOAL_MAXLENGTH', 240 );

/**
 * Whether the Ersteinschaetzung experiment is switched on.
 *
 * @return bool
 */
function hu_first_assessment_enabled() {
	return (bool) HU_EXPERIMENT_ERSTEINSCHAETZUNG;
}

/**
 * Stable key of the Ersteinschaetzung request type and contact focus.
 *
 * @return string
 */
function hu_first_assessment_key() {
	return HU_FIRST_ASSESSMENT_KEY;
}

/**
 * Contact URL that preselects the Ersteinschaetzung: /kontakt/?focus=ersteinschaetzung.
 *
 * @return string
 */
function hu_first_assessment_url() {
	$contact_url = function_exists( 'nexus_get_contact_url' ) ? nexus_get_contact_url() : home_url( '/kontakt/' );

	return add_query_arg( 'focus', HU_FIRST_ASSESSMENT_KEY, $contact_url );
}

/**
 * Customer-facing wording of the Ersteinschaetzung experiment.
 *
 * Jeder Text des Versuchs steht genau hier. Startseite, Kontaktseite,
 * Validierung und Mails lesen nur ab. Die Antwortzeit kommt aus
 * hu_response_promise(), nicht aus einer eigenen Fassung.
 *
 * @param string $key Text key.
 * @return string Empty string for an unknown key.
 */
function hu_first_assessment_text( $key ) {
	$review  = 'Ich sehe mir jede Einsendung selbst an.';
	$promise = 'Passt Ihre Seite zu meiner Arbeit, bekommen Sie drei konkrete Befunde per E-Mail. Wenn nicht, sage ich Ihnen das direkt.';

	$texts = [
		// Anliegen im Formular, Typ in CRM und Mail.
		'label'           => HU_FIRST_ASSESSMENT_LABEL,
		// Primaerer Button in Hero und Abschluss der Startseite.
		'cta'             => 'Kostenlose ' . HU_FIRST_ASSESSMENT_LABEL,
		// Zeile unter dem primaeren Button.
		'cta_note'        => 'URL schicken, drei Befunde schriftlich zurück. Ohne Verpflichtung.',
		// Karte im Abschluss der Startseite: Titel, was Besucher schicken, was sie bekommen.
		'card_title'      => 'Drei Befunde zu Ihrer Website.',
		'card_send'       => 'Website-URL und ein Satz zum Ziel',
		'card_get'        => 'Drei konkrete Befunde per E-Mail oder ein direktes Nein, wenn die Seite nicht zu meiner Arbeit passt',
		// Ueber dem Formular: Pruefung, Zusage, Antwortzeit.
		'intro'           => $review . ' ' . $promise . ' ' . hu_response_promise( 'sentence' ),
		// Dieselbe Zusage ohne Antwortzeit, fuer Flaechen, die die Antwortzeit
		// als eigene Zeile zeigen (Abschluss-Tafel im Artikel, [hu_abschluss]).
		'intro_short'     => $review . ' ' . $promise,
		// Dritter Schritt der Bestaetigungsmail: dieselbe Zusage wie im Formular.
		'promise'         => $promise,
		'step_title'      => 'Welche Website soll ich mir ansehen?',
		'website_label'   => 'Website-URL',
		'website_missing' => 'Bitte die Adresse Ihrer Website angeben.',
		'goal_label'      => 'Was soll die Website für Sie erreichen?',
		'goal_hint'       => 'ein Satz, optional',
		'submit'          => HU_FIRST_ASSESSMENT_LABEL . ' anfordern',
		// Betreff-Praefix der internen Mail und der Bestaetigung. Einzige
		// Zaehlstelle des Versuchs.
		'subject_prefix'  => '[' . HU_FIRST_ASSESSMENT_LABEL . ']',
	];

	return isset( $texts[ $key ] ) ? $texts[ $key ] : '';
}

/**
 * Return the canonical messaging model.
 *
 * @return array<string, mixed>
 */
function hu_messaging_canon() {
	return [
		'value_anchor_architecture' => HU_MESSAGE_VALUE_ANCHOR_ARCHITECTURE,
		'value_anchor_price'        => HU_MESSAGE_VALUE_ANCHOR_PRICE,
		'contact_email'             => HU_CONTACT_EMAIL,
		'contact_mailto'            => HU_CONTACT_EMAIL_MAILTO,
		'response_hours'            => HU_RESPONSE_HOURS,
		'response_promise_short'    => HU_RESPONSE_PROMISE_SHORT,
		'response_promise'          => HU_RESPONSE_PROMISE_SENTENCE,
		'what_we_dont_sell'         => [
			'Keine reine Design-Retusche ohne technischen oder messbaren Zweck.',
			'Keine Reporting-Fassade ohne belastbare Datengrundlage.',
			'Keine Anfrage- oder Umsatzgarantie ohne belastbare Grundlage.',
			'Keine Kundendaten-Blackbox, bei der Ownership unklar bleibt.',
			'Kein Full-Service-Versprechen für Leistungen, die nicht zum vereinbarten Scope gehören.',
		],
		'forbidden_terms'           => [
			'Pilotprojekt',
			'Pilot',
			'Beta',
			'Test',
			'eigentlich kostet das viel mehr',
			'ich bin neu',
			'starte gerade',
			'Berufsanfänger',
			'Modul',
		],
		'preferred_terms'           => [
			'Projekt anfragen',
			'direkte Zusammenarbeit',
			'White-Label',
			'Umsetzungspartner',
			'Baustein',
		],
		'term_definitions'          => [
			'Umsetzungspartner' => 'Betrieb, für den im Solar-/Wärmepumpen-Funnel nach dem Marktcheck ein eigenes Anfragesystem umgesetzt wird; kein Mitgründer, kein Anteilseigner und keine gesellschaftsrechtliche Partnerschaft.',
		],
	];
}

/**
 * Retired menu- and CTA-labels that a stored WordPress menu may still carry.
 *
 * Der Header schreibt alte Menuepunkte auf das aktuelle Label um und muss die
 * abgeloesten dafuer beim Namen nennen koennen. Sie stehen hier, weil der
 * Canon die einzige Datei ist, in der ein abgeloester Wert im Klartext
 * auftauchen darf — in inc/header.php wurden sie vorher aus Fragmenten
 * zusammengesetzt, nur um an der Sperre vorbeizukommen. Das hat den Wert
 * nicht entfernt, sondern unlesbar gemacht.
 *
 * Nur Label-Historie: nichts davon wird jemals ausgegeben.
 *
 * @return array<int, string>
 */
function hu_retired_cta_labels() {
	// canon/diagnose-canon.php laedt vor dieser Datei, der Zugriff ist also
	// sicher; defined() haelt die Funktion trotzdem unabhaengig von der
	// Ladereihenfolge in functions.php.
	$marketcheck = defined( 'HU_REQUEST_ANALYSIS_LABEL' ) ? HU_REQUEST_ANALYSIS_LABEL : 'Marktcheck';

	return [
		'Analyse starten',
		'System-Diagnose starten',
		'System-Diagnose anfragen',
		'System-Diagnose',
		$marketcheck,
		$marketcheck . ' · 60 Sek.',
		$marketcheck . ' · 48 h',
		$marketcheck . ' · 2 Werktage',
		'Audit starten',
		'Audit',
		'AI-Audit',
		'Anfrage stellen',
		'Direkt anfragen',
	];
}

/**
 * Canonical FAQ for the broad Conversion Tracking Setup product.
 *
 * The visible FAQ and the FAQPage JSON-LD both read this function so the
 * commercial scope cannot drift between copy and structured data.
 *
 * @return array<int, array{question:string,answer:string}>
 */
function hu_tracking_setup_faq_items() {
	$setup_price = function_exists( 'hu_tracking_price' )
		? hu_tracking_price( 'standard', 'setup', 'display', '1.290 €' )
		: '1.290 €';
	$delivery_window = function_exists( 'hu_tracking_delivery_weeks_display' )
		? hu_tracking_delivery_weeks_display()
		: '2 bis 3 Wochen';

	return [
		[
			'question' => 'Was kostet ein Conversion Tracking Setup?',
			'answer'   => sprintf( 'Der klar abgegrenzte Basisscope startet bei %1$s netto und wird in der Regel in %2$s umgesetzt. Vor dem Start steht schriftlich fest, welche Systeme, Formulare und Conversions enthalten sind.', $setup_price, $delivery_window ),
		],
		[
			'question' => 'Was ist im Basisscope ab 1.290 € enthalten?',
			'answer'   => 'Bestandsaufnahme und Messplan, GTM- und GA4-Struktur beziehungsweise Bereinigung, CMP- und Consent-Mode-Anbindung im vereinbarten Setup, Google Ads sowie bis zu drei Haupt-Conversions. Dazu kommen Abnahmetests, Dokumentation und Übergabe.',
		],
		[
			'question' => 'Ist Server-Side Tracking im Einstiegspreis enthalten?',
			'answer'   => 'Nein, nicht automatisch. Server-GTM, eigene Tracking-Subdomain, Meta CAPI und Browser-Server-Deduplizierung sind eine eigene Erweiterungsentscheidung. Wenn ein sauberes clientseitiges Setup das Problem löst, wird keine zusätzliche Server-Infrastruktur verkauft.',
		],
		[
			'question' => 'Wann ist Server-Side Tracking sinnvoll?',
			'answer'   => 'Wenn Serversignale, Meta CAPI, Deduplizierung oder eine kontrolliertere technische Messstrecke für das konkrete Setup gebraucht werden. Server-Side Tracking ersetzt weder Einwilligung noch rechtliche Prüfung und verspricht keine 100-Prozent-Attribution.',
		],
		[
			'question' => 'Kann das Tracking bis ins CRM und zu Offline Conversions erweitert werden?',
			'answer'   => 'Ja. CRM-Status, eindeutige Lead-Zuordnung, Offline Conversions und qualifizierte Rücksignale können als eigener Revenue-Scope umgesetzt werden. Diese Ebene wird separat aufgenommen und kalkuliert.',
		],
		[
			'question' => 'Wie wird geprüft, ob das Tracking wirklich funktioniert?',
			'answer'   => 'Die Abnahme arbeitet mit definierten Testfällen: erfolgreicher und fehlgeschlagener Formularversand, Consent-Zustände, Conversion-Aktionen und – falls beauftragt – Deduplizierung sowie CRM-Übergaben. Übergeben werden Messplan, QA-Protokoll, GTM-Versionen und bekannte Grenzen.',
		],
		[
			'question' => 'Brauche ich dafür einen Website-Relaunch?',
			'answer'   => 'Nein. Wenn die Website funktioniert und nur die Messung unsauber ist, kann das Conversion Tracking als eigenständiges Projekt eingerichtet oder bereinigt werden.',
		],
	];
}

/**
 * Add explicit product-family routes without changing the SEO owner of the
 * dedicated Server-Side Tracking service. Header navigation can use the broad
 * setup route while schema and specialist links keep pointing at SST.
 *
 * @param array<string,string> $routes Commercial route map.
 * @return array<string,string>
 */
function hu_position_tracking_product_route( $routes ) {
	if ( ! is_array( $routes ) ) {
		return $routes;
	}

	$routes['tracking_setup']    = home_url( '/ga4-tracking-setup/' );
	$routes['tracking_advanced'] = home_url( '/server-side-tracking-b2b/' );

	return $routes;
}
add_filter( 'hu_commercial_route_map', 'hu_position_tracking_product_route', 20 );

/**
 * Convert the canonical visible Tracking FAQ to FAQPage entities.
 *
 * @return array<int, array<string, mixed>>
 */
function hu_tracking_setup_faq_schema_entities() {
	$entities = [];

	foreach ( hu_tracking_setup_faq_items() as $item ) {
		$question = isset( $item['question'] ) ? trim( (string) $item['question'] ) : '';
		$answer   = isset( $item['answer'] ) ? trim( (string) $item['answer'] ) : '';

		if ( '' === $question || '' === $answer ) {
			continue;
		}

		$entities[] = [
			'@type'          => 'Question',
			'name'           => $question,
			'acceptedAnswer' => [
				'@type' => 'Answer',
				'text'  => $answer,
			],
		];
	}

	return $entities;
}

/**
 * Keep the output schema aligned with the broad Tracking product contract.
 *
 * The mature schema registry still owns route discovery. This pass only fixes
 * the product facts that changed with /ga4-tracking-setup/: broad setup first,
 * Server-Side as a separate advanced route, canonical price and the same FAQ
 * that is visibly rendered by page-ga4.php.
 *
 * @param string $markup JSON-LD markup emitted by hu_output_positioned_schema().
 * @return string
 */
function hu_align_tracking_product_schema_markup( $markup ) {
	if ( ! is_string( $markup ) || '' === $markup ) {
		return $markup;
	}

	$is_tracking_setup = is_page( 'ga4-tracking-setup' ) || is_page_template( 'page-ga4.php' );
	$setup_url         = home_url( '/ga4-tracking-setup/' );
	$advanced_url      = home_url( '/server-side-tracking-b2b/' );
	$setup_service_id  = $setup_url . '#service';
	$organization_id   = home_url( '/#organization' );
	$setup_value       = function_exists( 'hu_tracking_price' )
		? (float) hu_tracking_price( 'standard', 'setup', 'value', '1290' )
		: 1290.0;
	$setup_value       = (float) (int) $setup_value === $setup_value ? (int) $setup_value : $setup_value;

	return (string) preg_replace_callback(
		'#<script type="application/ld\+json">(.*?)</script>#s',
		static function ( $match ) use ( $is_tracking_setup, $setup_url, $advanced_url, $setup_service_id, $organization_id, $setup_value ) {
			$schema = json_decode( (string) $match[1], true );

			if ( ! is_array( $schema ) ) {
				return (string) $match[0];
			}

			$id   = isset( $schema['@id'] ) ? (string) $schema['@id'] : '';
			$type = $schema['@type'] ?? '';

			if ( $setup_service_id === $id ) {
				$schema['name']          = 'Conversion Tracking Setup für B2B-Websites';
				$schema['description']   = 'Conversion Tracking mit GA4, GTM, Consent Mode und Google Ads als klar abgegrenztes B2B-Setup. Server-Side, Meta CAPI und CRM werden nur nach technischem Bedarf ergänzt.';
				$schema['url']           = $setup_url;
				$schema['serviceType']   = 'Conversion Tracking Setup';
				$schema['serviceOutput'] = 'Geprüfte Messkette mit Messplan, definierten Conversions, QA-Protokoll und dokumentierter Übergabe';
				$schema['offers']        = [
					'@type'         => 'Offer',
					'price'         => $setup_value,
					'priceCurrency' => 'EUR',
					'url'           => $setup_url . '#angebot',
					'description'   => 'Basisscope mit Bestandsaufnahme, GTM und GA4, Consent-Anbindung, Google Ads, bis zu drei Haupt-Conversions, Abnahmetests und Dokumentation.',
				];
			}

			if ( $is_tracking_setup && 'FAQPage' === $type ) {
				$schema['mainEntity'] = hu_tracking_setup_faq_schema_entities();
			}

			if (
				$organization_id === $id
				&& isset( $schema['hasOfferCatalog']['itemListElement'] )
				&& is_array( $schema['hasOfferCatalog']['itemListElement'] )
			) {
				$items = [];

				foreach ( $schema['hasOfferCatalog']['itemListElement'] as $item ) {
					$service = is_array( $item ) && isset( $item['itemOffered'] ) && is_array( $item['itemOffered'] )
						? $item['itemOffered']
						: [];
					$name = isset( $service['name'] ) ? (string) $service['name'] : '';
					$url  = isset( $service['url'] ) ? (string) $service['url'] : '';

					if ( $advanced_url === $url || false !== stripos( $name, 'Server-Side Tracking' ) ) {
						continue;
					}

					$items[] = $item;
				}

				$tracking_items = [
					[
						'@type'       => 'Offer',
						'itemOffered' => [
							'@type'       => 'Service',
							'name'        => 'Conversion Tracking Setup',
							'description' => 'GA4, GTM, Consent Mode und Google Ads mit dokumentierter Abnahme; Server-Side und CRM nur bei begründetem Bedarf.',
							'url'         => $setup_url,
							'serviceType' => 'Conversion Tracking Setup',
							'provider'    => [ '@id' => $organization_id ],
						],
					],
					[
						'@type'       => 'Offer',
						'itemOffered' => [
							'@type'       => 'Service',
							'name'        => 'Server-Side Tracking & Attribution',
							'description' => 'Server-GTM, eigene Tracking-Subdomain, Meta CAPI, Deduplizierung und Paralleltest für Setups mit entsprechendem technischen Bedarf.',
							'url'         => $advanced_url,
							'serviceType' => 'Server-Side Tracking',
							'provider'    => [ '@id' => $organization_id ],
						],
					],
				];

				$insert_at = min( 1, count( $items ) );
				array_splice( $items, $insert_at, 0, $tracking_items );
				$schema['hasOfferCatalog']['itemListElement'] = array_values( $items );
			}

			$json = wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT );

			return is_string( $json ) && '' !== $json
				? '<script type="application/ld+json">' . $json . '</script>'
				: (string) $match[0];
		},
		$markup
	);
}

/**
 * Wrap the positioned schema renderer after all theme modules are loaded.
 *
 * @return void
 */
function hu_output_tracking_aligned_schema() {
	if ( ! function_exists( 'hu_output_positioned_schema' ) ) {
		return;
	}

	ob_start();
	hu_output_positioned_schema();
	$markup = (string) ob_get_clean();

	echo hu_align_tracking_product_schema_markup( $markup ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON-LD is re-encoded with wp_json_encode().
}

/**
 * Replace the normal renderer only after schema-positioning.php has registered it.
 *
 * @return void
 */
function hu_install_tracking_schema_alignment() {
	if ( ! function_exists( 'hu_output_positioned_schema' ) ) {
		return;
	}

	remove_action( 'wp_head', 'hu_output_positioned_schema', 10 );
	add_action( 'wp_head', 'hu_output_tracking_aligned_schema', 10 );
}
add_action( 'wp_loaded', 'hu_install_tracking_schema_alignment', 20 );