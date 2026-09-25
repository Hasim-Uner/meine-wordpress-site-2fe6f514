<?php
/**
 * Template Name: GA4 Tracking Landing
 * Description: Conversion Tracking Setup fuer B2B – GA4, GTM, Consent, Ads und optionale Server-/CRM-Erweiterung.
 *
 * Die Route bleibt /ga4-tracking-setup/. Diese Seite besitzt den breiten
 * Tracking-Kaufintent; /server-side-tracking-b2b/ bleibt die spezialisierte
 * Vertiefung fuer Server-GTM, CAPI und deduplizierte Serversignale.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'hu_forced_singular_seo_map', function ( $map ) {
	if ( ! is_array( $map ) ) {
		$map = [];
	}

	$map['ga4-tracking-setup'] = [
		'title'       => 'Conversion Tracking einrichten lassen | GA4, GTM & Consent',
		'description' => 'Conversion Tracking für B2B-Websites: GA4, GTM, Consent Mode und Google Ads sauber einrichten lassen. Optional Server-Side, Meta CAPI und CRM. Ab 1.290 € netto.',
	];

	return $map;
}, 90 );

add_filter( 'body_class', function ( $classes ) {
	$classes[] = 'hu-wayfinding-active';
	$classes[] = 'hu-wayfinding-tracking-setup';
	$classes[] = 'hu-tracking-setup-page';
	return array_values( array_unique( $classes ) );
} );

add_action( 'wp_enqueue_scripts', function () {
	$dir = get_stylesheet_directory();
	$uri = get_stylesheet_directory_uri();
	$navigation_css = '/assets/css/navigation-ecosystem.css';
	$navigation_js  = '/assets/js/navigation-ecosystem.js';
	$route_css      = '/assets/css/tracking-setup.css';

	if ( is_file( $dir . $navigation_css ) ) {
		wp_enqueue_style( 'hu-navigation-ecosystem', $uri . $navigation_css, [], (string) filemtime( $dir . $navigation_css ) );
	}
	if ( is_file( $dir . $route_css ) ) {
		wp_enqueue_style( 'hu-tracking-setup', $uri . $route_css, [ 'hu-navigation-ecosystem' ], (string) filemtime( $dir . $route_css ) );
	}
	if ( is_file( $dir . $navigation_js ) ) {
		wp_enqueue_script( 'hu-navigation-ecosystem', $uri . $navigation_js, [], (string) filemtime( $dir . $navigation_js ), true );
	}
}, 45 );

function hu_render_tracking_setup_breadcrumb() {
	?>
	<nav class="hu-wayfinding-breadcrumb" aria-label="Breadcrumb" data-track-section="breadcrumb">
		<ol>
			<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>" data-track-action="breadcrumb_home" data-track-category="navigation">Startseite</a></li>
			<li><span aria-current="page">Conversion Tracking Setup</span></li>
		</ol>
	</nav>
	<?php
}
add_action( 'wp_body_open', 'hu_render_tracking_setup_breadcrumb', 30 );

function hu_render_tracking_setup_toc() {
	$items = [
		[ 'id' => 'probleme', 'label' => 'Diagnose' ],
		[ 'id' => 'messkette', 'label' => 'Messkette' ],
		[ 'id' => 'umfang', 'label' => 'Umfang' ],
		[ 'id' => 'pruefung', 'label' => 'Abnahme' ],
		[ 'id' => 'ablauf', 'label' => 'Ablauf' ],
		[ 'id' => 'angebot', 'label' => 'Setup & Preis' ],
		[ 'id' => 'faq', 'label' => 'FAQ' ],
	];
	?>
	<nav class="hu-page-toc hu-tracking-register" aria-label="Abschnitte dieses Dokuments" data-hu-rail="true" data-track-section="page_toc">
		<span class="hu-tracking-register__marke" aria-hidden="true">Register</span>
		<div class="hu-tracking-register__eintraege">
			<?php foreach ( $items as $index => $item ) : ?>
				<a href="#<?php echo esc_attr( $item['id'] ); ?>" data-track-action="toc_<?php echo esc_attr( sanitize_key( $item['id'] ) ); ?>" data-track-category="navigation">
					<span class="hu-tracking-register__nr" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $index + 1 ) ); ?></span>
					<span class="hu-tracking-register__txt"><?php echo esc_html( $item['label'] ); ?></span>
				</a>
			<?php endforeach; ?>
		</div>
	</nav>
	<?php
}
add_action( 'wp_body_open', 'hu_render_tracking_setup_toc', 31 );

$page = function_exists( 'nexus_get_wgos_cluster_page' ) ? nexus_get_wgos_cluster_page() : [];

$contact_url = function_exists( 'hu_get_contact_intake_url' )
	? hu_get_contact_intake_url( 'project', 'tracking' )
	: add_query_arg(
		[
			'type'  => 'project',
			'focus' => 'tracking',
		],
		home_url( '/kontakt/' )
	);

$server_side_url = home_url( '/server-side-tracking-b2b/' );
$results_url     = function_exists( 'nexus_get_primary_public_url' )
	? nexus_get_primary_public_url( 'results', home_url( '/case-study-solar-leadgenerierung/' ) )
	: home_url( '/case-study-solar-leadgenerierung/' );
$response_label  = hu_response_promise( 'compact' );
$setup_price     = function_exists( 'hu_tracking_price' )
	? hu_tracking_price( 'standard', 'setup', 'display', '1.290 €' )
	: '1.290 €';
$pro_setup_price = function_exists( 'hu_tracking_price' )
	? hu_tracking_price( 'pro', 'setup', 'display', '1.900 €' )
	: '1.900 €';
$individual_price = function_exists( 'hu_tracking_price' )
	? hu_tracking_price( 'individual', 'setup', 'display', 'ab 3.500 €' )
	: 'ab 3.500 €';
$delivery_window = function_exists( 'hu_tracking_delivery_weeks_display' )
	? hu_tracking_delivery_weeks_display()
	: '2 bis 3 Wochen';
$faq_items       = function_exists( 'hu_tracking_setup_faq_items' )
	? hu_tracking_setup_faq_items()
	: ( isset( $page['faq_items'] ) && is_array( $page['faq_items'] ) ? $page['faq_items'] : [] );

get_header();
?>

<div class="site-main doku ga4-page" data-track-page="conversion_tracking_setup">
	<header class="kopfteil" data-track-section="tracking_hero">
		<div class="blatt">
			<p class="gegenstand">Conversion Tracking Setup · B2B</p>
			<h1>Conversion Tracking für B2B: GA4, GTM, Consent &amp; Ads sauber eingerichtet.</h1>
			<p class="aufriss">
				<span class="erst">Nicht nur messen, ob ein Formular abgeschickt wurde.</span>
				Ich richte die Messkette so ein, dass Website, Google Tag Manager, GA4, Consent und Werbeplattformen nachvollziehbar zusammenspielen. Server-Side und CRM kommen nur dazu, wenn sie für Ihr Setup einen klaren technischen oder wirtschaftlichen Zweck erfüllen.
			</p>

			<div class="ausgang">
				<a class="tun" href="<?php echo esc_url( $contact_url ); ?>" data-track-action="cta_tracking_project" data-track-category="lead_gen">
					Tracking-Setup prüfen lassen <span class="pf" aria-hidden="true">→</span>
				</a>
				<a class="tun still" href="#angebot">Setup &amp; Preis ansehen</a>
			</div>
			<p class="mono"><?php echo esc_html( $response_label ); ?> · Setup ab <?php echo esc_html( $setup_price ); ?> netto · <?php echo esc_html( $delivery_window ); ?> · dokumentierte Übergabe</p>

			<div class="meta" aria-label="Produktüberblick">
				<dl>
					<div><dt>Messbasis</dt><dd>GTM, GA4, Events</dd></div>
					<div><dt>Consent &amp; Ads</dt><dd>CMP, Consent Mode, Conversions</dd></div>
					<div><dt>Optional</dt><dd>Server-Side, CAPI, CRM</dd></div>
					<div><dt>Abnahme</dt><dd>Testfälle, Befund, Dokumentation</dd></div>
				</dl>
			</div>
		</div>
	</header>

	<section id="probleme" data-track-section="tracking_problems">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">01</span><span class="titel">Diagnose</span><span class="strich"></span></div></div>
			<div class="haupt">
				<p class="mono stempelfarbe">Wenn Messung technisch vorhanden ist, aber geschäftlich nichts erklärt</p>
				<h2 class="kopf">Die Events feuern. Die Zahlen passen trotzdem nicht zusammen.</h2>
				<p class="vorspann">Ein Tracking-Setup ist erst dann brauchbar, wenn klar ist, welche Conversion gezählt wurde, unter welchem Consent-Zustand sie entstand und ob daraus im Vertrieb überhaupt ein relevanter Lead wurde.</p>
				<div class="protokoll" aria-label="Typische Tracking-Probleme">
					<div class="z"><span>01</span><b>GA4 und Google Ads zeigen unterschiedliche Conversion-Zahlen.</b></div>
					<div class="z"><span>02</span><b>Formulare fehlen in der Messung oder werden mehrfach gezählt.</b></div>
					<div class="z"><span>03</span><b>Consent, Tags und Werbesignale verhalten sich nicht nachvollziehbar.</b></div>
					<div class="z"><span>04</span><b>Kampagnen sehen Leads – aber nicht, welche davon fachlich passen.</b></div>
				</div>
			</div>
			<aside class="marg"><p class="note"><span class="label">Wichtig</span><b>Abweichung bedeutet nicht automatisch Fehler.</b> GA4, Ads und CRM können wegen Zählweise, Attribution, Consent und Zeitfenstern unterschiedliche Werte zeigen. Ziel ist ein erklärbares System – keine künstlich identischen Zahlen.</p></aside>
		</div>
	</section>

	<section id="messkette" data-track-section="tracking_measurement_chain">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">02</span><span class="titel">Messkette</span><span class="strich"></span></div></div>
			<div class="voll">
				<div class="tafel">
					<p class="mono stempelfarbe">Vom Seitenbesuch bis zum Vertriebsstatus</p>
					<h2 class="kopf">Eine Messkette. Sechs prüfbare Übergaben.</h2>
					<p class="vorspann">Jede Stufe bekommt eine eindeutige Aufgabe. Dadurch lässt sich ein Fehler dort untersuchen, wo er entsteht, statt pauschal neue Tags oder Server-Infrastruktur darüberzulegen.</p>
					<div class="hu-tracking-chain" aria-label="Tracking-Messkette von Website bis CRM">
						<div class="hu-tracking-chain__knoten"><span class="hu-tracking-chain__nr">01</span><strong class="hu-tracking-chain__titel">Website</strong><p class="hu-tracking-chain__text">Einstiegsseite, Formular und relevante Interaktion.</p><span class="hu-tracking-chain__status">Quelle</span></div>
						<div class="hu-tracking-chain__knoten"><span class="hu-tracking-chain__nr">02</span><strong class="hu-tracking-chain__titel">Data Layer / GTM</strong><p class="hu-tracking-chain__text">Events, Parameter, Trigger und eindeutige Namen.</p><span class="hu-tracking-chain__status">Logik</span></div>
						<div class="hu-tracking-chain__knoten"><span class="hu-tracking-chain__nr">03</span><strong class="hu-tracking-chain__titel">Consent</strong><p class="hu-tracking-chain__text">Definierter Datenfluss je Einwilligungszustand.</p><span class="hu-tracking-chain__status">Zustand</span></div>
						<div class="hu-tracking-chain__knoten"><span class="hu-tracking-chain__nr">04</span><strong class="hu-tracking-chain__titel">GA4 / Ads</strong><p class="hu-tracking-chain__text">Analyse- und Kampagnensignale mit klarer Conversion-Logik.</p><span class="hu-tracking-chain__status">Nutzung</span></div>
						<div class="hu-tracking-chain__knoten" data-state="optional"><span class="hu-tracking-chain__nr">05</span><strong class="hu-tracking-chain__titel">Server</strong><p class="hu-tracking-chain__text">Server-GTM, Tracking-Subdomain, Deduplizierung und CAPI.</p><span class="hu-tracking-chain__status">Optional</span></div>
						<div class="hu-tracking-chain__knoten" data-state="optional"><span class="hu-tracking-chain__nr">06</span><strong class="hu-tracking-chain__titel">CRM</strong><p class="hu-tracking-chain__text">Qualifizierter Lead, Angebot und Auftrag als Rücksignal.</p><span class="hu-tracking-chain__status">Optional</span></div>
					</div>
					<p class="hu-tracking-chain__legende"><span>durchgezogen = Kernstrecke</span><span>gestrichelt = nur bei begründetem Bedarf</span></p>
				</div>
			</div>
		</div>
	</section>

	<section id="umfang" data-track-section="tracking_scope">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">03</span><span class="titel">Umfang</span><span class="strich"></span></div></div>
			<div class="haupt">
				<p class="mono stempelfarbe">Ein Produkt, modularer Scope</p>
				<h2 class="kopf">Was konkret eingerichtet wird.</h2>
				<p class="vorspann">GA4 und Google Tag Manager können als eigenständiges Tracking-Projekt eingerichtet oder bereinigt werden. Consent Mode und Google Ads gehören in den Basisscope, wenn diese Systeme im Projekt genutzt werden. Server-Side, Meta CAPI und CRM sind eigene Erweiterungsentscheidungen.</p>
				<div class="protokoll">
					<div class="z"><span>01 · Measurement</span><b>Bestandsaufnahme, GTM-Struktur, GA4, Event- und Conversion-Plan, Formularmessung</b></div>
					<div class="z"><span>02 · Consent &amp; Ads</span><b>CMP-Anbindung, Consent Mode, Google Ads Conversions und Enhanced Conversions soweit passend</b></div>
					<div class="z"><span>03 · Server</span><b>Server-GTM, eigene Tracking-Subdomain, Deduplizierung und Meta CAPI nur bei begründetem Bedarf</b></div>
					<div class="z"><span>04 · Revenue</span><b>CRM-Status, Offline Conversions und Rückgabe qualifizierter Geschäftssignale für komplexere Setups</b></div>
				</div>
				<dl class="hu-tracking-output" aria-label="Ergebnis der Umsetzung">
					<div><dt>Messlogik</dt><dd>Welche Conversion wann zählt</dd></div>
					<div><dt>Prüfung</dt><dd>Welche Zustände getestet wurden</dd></div>
					<div><dt>Übergabe</dt><dd>Was dokumentiert und wartbar bleibt</dd></div>
				</dl>
				<div class="ausgang"><a class="textlink" href="<?php echo esc_url( $server_side_url ); ?>">Server-Side Tracking im Detail</a></div>
			</div>
			<aside class="marg">
				<p class="note"><span class="label">Kein Pflicht-Upgrade</span><b>Nicht jeder braucht Server-Side.</b> Wenn ein sauberes clientseitiges Setup das Problem löst, ist zusätzliche Infrastruktur kein Qualitätsmerkmal.</p>
				<p class="note"><span class="label">Datenschutz</span>Consent wird technisch respektiert. Das Setup verspricht weder eine Umgehung von Einwilligung noch 100&nbsp;% Attribution.</p>
			</aside>
		</div>
	</section>

	<section id="pruefung" data-track-section="tracking_acceptance">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">04</span><span class="titel">Abnahme</span><span class="strich"></span></div></div>
			<div class="haupt">
				<p class="mono stempelfarbe">Nicht „Tag ist grün“, sondern definierte Testfälle</p>
				<h2 class="kopf">So wird geprüft, ob das Setup tatsächlich funktioniert.</h2>
				<p class="vorspann">Die Übergabe dokumentiert Sollzustand, Testweg, Ergebnis und bekannte Grenzen. Damit lässt sich das Setup später nachvollziehen – auch ohne mich.</p>
				<div class="protokoll" aria-label="Tracking-Abnahmetests">
					<div class="z"><span>Formular erfolgreich</span><b>genau ein definierter Conversion-Vorgang</b></div>
					<div class="z"><span>Formularfehler</span><b>keine Conversion bei fehlgeschlagenem Versand</b></div>
					<div class="z"><span>Consent</span><b>definierter Datenfluss für die geprüften Einwilligungszustände</b></div>
					<div class="z"><span>Ads</span><b>Conversion-Aktion, Parameter und Importweg nachvollziehbar</b></div>
					<div class="z"><span>Deduplizierung</span><b>Browser- und Serversignal erzeugen keine Doppelzählung, falls Server-Side beauftragt ist</b></div>
					<div class="z"><span>CRM</span><b>Lead-ID und Statusübergabe bis zum definierten Geschäftssignal, falls CRM Teil des Scopes ist</b></div>
				</div>

				<div class="hu-tracking-proof-ledger" aria-label="Beispiel für die technische Übergabe">
					<p class="hu-tracking-proof-ledger__eyebrow">Beispielstruktur der Übergabe · keine Kundendaten</p>
					<div class="hu-tracking-proof-ledger__grid">
						<div><span>01 · Messplan</span><strong>Event · Trigger · Parameter · Zielsystem</strong><p>Für jede Haupt-Conversion steht schriftlich fest, was zählt und wohin das Signal geht.</p></div>
						<div><span>02 · QA-Protokoll</span><strong>Testfall · Consent · Soll · Ist</strong><p>Die Abnahme hält nicht nur den Endzustand fest, sondern auch den geprüften Weg dorthin.</p></div>
						<div><span>03 · Handover</span><strong>GTM-Version · Zugänge · bekannte Grenzen</strong><p>Konten, Versionen und Dokumentation bleiben nachvollziehbar und in Ihrer Verfügung.</p></div>
					</div>
				</div>
			</div>
			<aside class="marg">
				<p class="note"><span class="label">Proof-Prinzip</span><b>Der Beleg ist die reproduzierbare Prüfung.</b> Solange kein eigener Tracking-Kundenfall veröffentlicht werden kann, zeige ich keine erfundene Erfolgsstory, sondern die technische Abnahme, an der das Projekt gemessen wird.</p>
				<p class="note"><span class="label">Arbeitsbelege</span><a class="satzlink" href="<?php echo esc_url( $results_url ); ?>">Vorhandene Projekte und Ergebnisse ansehen.</a></p>
			</aside>
		</div>
	</section>

	<section id="ablauf" data-track-section="tracking_process">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">05</span><span class="titel">Ablauf</span><span class="strich"></span></div></div>
			<div class="haupt">
				<p class="mono stempelfarbe">Diagnose vor Ausbau</p>
				<h2 class="kopf">Fünf Schritte vom Befund zur dokumentierten Messung.</h2>
				<div class="protokoll">
					<div class="z"><span>01 · Audit</span><b>bestehende Tags, Ziele, Consent und Datenabweichungen aufnehmen</b></div>
					<div class="z"><span>02 · Messkonzept</span><b>Conversions, Events, Parameter und Verantwortlichkeiten festlegen</b></div>
					<div class="z"><span>03 · Umsetzung</span><b>GTM, GA4, Consent und Plattformen nach Scope konfigurieren</b></div>
					<div class="z"><span>04 · Paralleltest</span><b>Browser, Plattform und optional Server/CRM gegeneinander prüfen</b></div>
					<div class="z"><span>05 · Handover</span><b>Testprotokoll, offene Punkte und wartbare Dokumentation übergeben</b></div>
				</div>
			</div>
		</div>
	</section>

	<section id="angebot" data-track-section="tracking_offer">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">06</span><span class="titel">Angebot</span><span class="strich"></span></div></div>
			<div class="voll">
				<div class="tafel">
					<p class="mono stempelfarbe">Klarer Basisscope · Erweiterung nur bei Bedarf</p>
					<h2>Was Sie ab <?php echo esc_html( $setup_price ); ?> netto konkret bekommen.</h2>
					<p class="aufriss">Der Einstiegspreis gilt für ein klar abgegrenztes B2B-Setup. Vor dem Start steht schriftlich fest, welche Systeme, Formulare und Conversions enthalten sind. Komplexere Server-, Meta- oder CRM-Strecken werden nicht stillschweigend in denselben Scope gepackt.</p>

					<div class="hu-tracking-offer-grid" aria-label="Tracking-Angebotsleiter">
						<article class="hu-tracking-offer-card" data-level="core">
							<p class="hu-tracking-offer-card__level">01 · Basis</p>
							<h3>GA4, GTM, Consent &amp; Google Ads</h3>
							<p class="hu-tracking-offer-card__price">ab <?php echo esc_html( $setup_price ); ?> <small>netto</small></p>
							<p>Für eine Website mit klaren Haupt-Conversions und einem überschaubaren Google-Stack.</p>
							<ul>
								<li>Bestandsaufnahme und schriftlicher Messplan</li>
								<li>GTM- und GA4-Struktur bzw. Bereinigung</li>
								<li>CMP/Consent Mode im vereinbarten Setup</li>
								<li>Google Ads und bis zu drei Haupt-Conversions</li>
								<li>Abnahmetests, Dokumentation und Übergabe</li>
							</ul>
						</article>

						<article class="hu-tracking-offer-card" data-level="advanced">
							<p class="hu-tracking-offer-card__level">02 · Performance</p>
							<h3>Server-Side &amp; Meta CAPI</h3>
							<p class="hu-tracking-offer-card__price">ab <?php echo esc_html( $pro_setup_price ); ?> <small>netto</small></p>
							<p>Wenn Serversignale, eine eigene Tracking-Subdomain, Meta CAPI oder Deduplizierung Teil des eigentlichen Problems sind.</p>
							<ul>
								<li>Server-GTM und eigene Tracking-Subdomain</li>
								<li>Browser-/Server-Deduplizierung</li>
								<li>Enhanced Conversions und Meta CAPI soweit passend</li>
								<li>Paralleltest vor der Umschaltung</li>
							</ul>
							<a class="satzlink" href="<?php echo esc_url( $server_side_url ); ?>">Server-Side Tracking im Detail</a>
						</article>

						<article class="hu-tracking-offer-card" data-level="revenue">
							<p class="hu-tracking-offer-card__level">03 · Individuell</p>
							<h3>CRM &amp; Offline Conversions</h3>
							<p class="hu-tracking-offer-card__price"><?php echo esc_html( $individual_price ); ?> <small>netto</small></p>
							<p>Wenn nicht das Formular, sondern Leadqualität, Angebot oder Auftrag das relevante Optimierungssignal sein soll.</p>
							<ul>
								<li>CRM-Status und eindeutige Lead-Zuordnung</li>
								<li>Offline Conversions bzw. qualifizierte Rücksignale</li>
								<li>mehrere Domains oder individuelle Datenstrecken nach Aufnahme</li>
							</ul>
						</article>
					</div>

					<div class="hu-tracking-offer-contract" aria-label="Rahmen des Tracking-Projekts">
						<div><span>Scope</span><b>vor Projektstart schriftlich abgegrenzt</b></div>
						<div><span>Umsetzung</span><b><?php echo esc_html( $delivery_window ); ?></b></div>
						<div><span>Abnahme</span><b>Testprotokoll + Dokumentation</b></div>
						<div><span>Ownership</span><b>Konten und Zugänge bleiben bei Ihnen</b></div>
					</div>

					<p class="hu-tracking-offer-note"><strong>Wichtig:</strong> „ab <?php echo esc_html( $setup_price ); ?>“ ist kein Lockpreis für beliebig viele Systeme. Wenn der Bestand oder die gewünschte Messkette größer ist, wird der Umfang vor der Umsetzung neu abgegrenzt – nicht während des Projekts nachverkauft.</p>
					<div class="ausgang"><a class="tun" href="<?php echo esc_url( $contact_url ); ?>" data-track-action="cta_tracking_project" data-track-category="lead_gen">Tracking-Setup prüfen lassen <span class="pf" aria-hidden="true">→</span></a></div>
				</div>
			</div>
		</div>
	</section>

	<section id="faq" data-track-section="tracking_faq">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">07</span><span class="titel">FAQ</span><span class="strich"></span></div></div>
			<div class="haupt">
				<p class="mono stempelfarbe">Vor der Beauftragung</p>
				<h2 class="kopf leise">Häufige Fragen zum Tracking-Setup.</h2>
				<?php if ( ! empty( $faq_items ) ) : ?>
					<div class="fragen">
						<?php foreach ( $faq_items as $item ) : ?>
							<?php
							$question = isset( $item['question'] ) ? (string) $item['question'] : '';
							$answer   = isset( $item['answer'] ) ? (string) $item['answer'] : '';
							if ( '' === $question || '' === $answer ) { continue; }
							?>
							<details><summary><?php echo esc_html( $question ); ?></summary><div class="huelle"><div><p class="antwort"><?php echo esc_html( $answer ); ?></p></div></div></details>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</section>

	<section class="abschluss" id="anfrage" data-track-section="tracking_final_cta">
		<div class="blatt reihe"><div class="ganz"><div class="tafel"><div class="reihe">
			<div class="haupt">
				<p class="mono stempelfarbe">Nächster Schritt</p>
				<h2>Welche Conversion fehlt, zählt doppelt oder kommt im Vertrieb nicht an?</h2>
				<p class="aufriss">Beschreiben Sie kurz Website, bestehendes Setup und die Abweichung, die Sie gerade sehen. Sie bekommen eine Einschätzung, welche Ebene zuerst geprüft werden sollte – ohne automatischen Server-Side-Upsell.</p>
				<div class="ausgang"><a class="tun" href="<?php echo esc_url( $contact_url ); ?>" data-track-action="cta_tracking_project" data-track-category="lead_gen">Tracking-Setup prüfen lassen <span class="pf" aria-hidden="true">→</span></a></div>
			</div>
			<aside class="marg"><p class="note"><span class="label">Ergebnis</span>Ein klarer Befund, ein abgegrenzter Scope und – wenn Sie die Umsetzung beauftragen – ein geprüftes, dokumentiertes Setup.</p></aside>
		</div></div></div></div>
	</section>
</div>

<?php get_footer();