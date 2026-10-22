<?php
/**
 * Template Name: Solar Leads kaufen – Alternative (Intercept)
 * Description: Intercept-Landingpage für Suchintent "Solar Leads kaufen",
 *              "Photovoltaik Leads kaufen", "Wärmepumpen Leads kaufen".
 *              Argumentation: Portal-Leads vs. eigenes Anfragesystem (CPL-Senkung).
 *              Primärer Pfad: Marktcheck auf /solar-waermepumpen-leadgenerierung/#marktcheck.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ── URLs ───────────────────────────────────────────────────────
$page_url        = home_url( '/solar-leads-kaufen-alternative/' );
$solar_money_url = function_exists( 'nexus_get_energy_systems_url' )
	? nexus_get_energy_systems_url()
	: home_url( '/solar-waermepumpen-leadgenerierung/' );
$marktcheck_url  = trailingslashit( $solar_money_url ) . '#marktcheck';
$e3_url          = home_url( '/case-study-solar-leadgenerierung/' );
$cost_study_url  = home_url( '/solar-leads-kosten-studie/' );
$marketcheck_price = hu_marketcheck_price();
$foundation_price = hu_foundation_price_display();
$extra_product_price = hu_foundation_extra_product_price_display();
$hosting_price = hu_foundation_hosting_display();
$waermepumpen_url = home_url( '/waermepumpen-leads/' );

$linked_assets = [
	[ 't' => 'Anfrage- und Auftragskosten berechnen', 's' => 'CPL, Vertriebszeit und Abschlussquote mit Ihren Zahlen einordnen.', 'url' => $cost_study_url ],
	[ 't' => 'Wärmepumpen-Anfragen prüfen', 's' => 'Bestandsheizung, Gebäude und Zeithorizont vor dem Rückruf erfassen.', 'url' => $waermepumpen_url ],
	[ 't' => 'PV-Termine für Gewerbeprojekte', 's' => 'Technische Substanz und Entscheidungsrollen vor der Terminvergabe.', 'url' => home_url( '/b2b-solar-leads/' ) ],
];

// ── E3-Proof-Canon ─────────────────────────────────────────────
$e3_canon            = hu_e3_canon();
$e3_metrics          = isset( $e3_canon['metrics'] ) && is_array( $e3_canon['metrics'] ) ? $e3_canon['metrics'] : [];
$e3_case_label       = isset( $e3_canon['case_label'] ) ? (string) $e3_canon['case_label'] : 'mittelständischer PV-Installationsbetrieb';
$e3_lead_count = hu_e3_metric( 'lead_count' );
$e3_sales_conversion = hu_e3_metric( 'sales_conversion' );
$e3_cpl_reduction = hu_e3_metric( 'cpl_reduction' );
$e3_cpl_before = hu_e3_metric( 'cpl_before' );
$e3_cpl_after = hu_e3_metric( 'cpl_after' );
$e3_timeframe = hu_e3_metric( 'timeframe' );

// ── Inhalt: Portal vs. eigenes System (Problem → Lösung) ──────
$portal_facts = [
	[
		'k' => 'Einkauf',
		'l' => 'Jeder zusätzliche Kontakt erzeugt erneut variable Kosten.',
	],
	[
		'k' => 'Kontaktlage',
		'l' => 'Exklusivität, Alter und Herkunft müssen vor dem Kauf geklärt sein.',
	],
	[
		'k' => 'Vertrieb',
		'l' => 'Nachqualifizierung und schnelle Reaktion bleiben beim Betrieb.',
	],
	[
		'k' => 'Externer Kanal',
		'l' => 'Die Quelle neuer Anfragen bleibt beim Anbieter. Bereits gewonnene Kundenbeziehungen können in Ihrem CRM bleiben.',
	],
];

$own_facts = [
	[
		'k' => $e3_cpl_after,
		'l' => 'Cost per Lead im eigenen Anfragesystem (Case-Study-Referenz)',
	],
	[
		'k' => 'Eigener Kontakt',
		'l' => 'Die Anfrage läuft direkt und ohne Portal-Parallelversand in den Vertrieb.',
	],
	[
		'k' => $e3_sales_conversion,
		'l' => 'Abschlussquote der vorqualifizierten CRM-Leads des dokumentierten Falls — kein allgemeines Versprechen.',
	],
	[
		'k' => 'Eigenes Asset',
		'l' => 'Seite, Tracking, Prozess und Datenbasis bleiben beim Betrieb.',
	],
];



$market_models = [
	[
		't' => 'Geteilte Datensätze',
		's' => 'Ein Kontakt kann mehreren Betrieben angeboten werden. Der Einstiegspreis ist nur zusammen mit Empfängerzahl, Alter und Rückerstattungsregeln aussagekräftig.',
	],
	[
		't' => 'Exklusive Datensätze',
		's' => 'Der Kontakt wird einem Betrieb zugesagt. Entscheidend bleibt, wie Exklusivität definiert, geprüft und bei Fehlkontakten behandelt wird.',
	],
	[
		't' => 'Regional erzeugte Leads',
		's' => 'Kampagnen werden für ein Zielgebiet ausgespielt. Vor dem Auftrag sollte klar sein, wem Werbekonto, Landingpage und Rohdaten gehören.',
	],
	[
		't' => 'Eigene Anfrage-Infrastruktur',
		's' => 'Suchseite, Vorqualifizierung, Messung und Übergabe laufen unter eigener Kontrolle. Der Aufbau dauert länger, schafft dafür ein übergebbares Asset.',
	],
];

$buyer_check_criteria = [
	[
		'k' => 'Intent-Stärke',
		'l' => 'Löst die Anfrage nur Informationsbedarf aus — oder ist bereits ein konkretes Projekt erkennbar?',
	],
	[
		'k' => 'Exklusivität',
		'l' => 'Wie viele Betriebe erhalten denselben Kontakt und wie wird diese Zusage kontrolliert?',
	],
	[
		'k' => 'Vorqualifizierung',
		'l' => 'Sind Region, Vorhaben, Gebäudesituation und zeitlicher Horizont vor dem Rückruf bekannt?',
	],
	[
		'k' => 'Übergabe & Daten',
		'l' => 'Wann kommt der Kontakt an, welche Einwilligung liegt vor und kann der Vertriebsstatus zurückgespielt werden?',
	],
];

$own_system_layers = [
	[
		't' => 'Kaufnahe Seite mit Vorqualifizierung',
		's' => 'Suchintention, Zielgebiet und Projektmerkmale werden vor dem ersten Vertriebskontakt zusammengeführt.',
	],
	[
		't' => 'Messbare Anfrage-Strecke',
		's' => 'Formular, Consent-Signale und Kampagnenquellen werden technisch nachvollziehbar verbunden.',
	],
	[
		't' => 'Vertriebsübergabe und Priorisierung',
		's' => 'Anfragen kommen mit den entscheidenden Kontextdaten an und können nach Fit und Dringlichkeit bearbeitet werden.',
	],
	[
		't' => 'Lernen auf eigener Datenbasis',
		's' => 'SEO und bezahlte Kanäle werden anhand der Anfragequalität gesteuert — nicht nur anhand eines Formularabschlusses.',
	],
];

$objections = [
	[
		'question' => 'Was kosten Photovoltaik-Leads im Einkauf?',
		'answer'   => 'Der Preis hängt unter anderem von Exklusivität, Region, Produkt, Vorqualifizierung und Rückerstattungsregeln ab. Ein einzelner CPL ist deshalb keine belastbare Einkaufsentscheidung. Der verlinkte Kostenleitfaden berechnet Anfrage- und Vertriebskosten mit Ihren Zahlen und erklärt die Grenzen des dokumentierten Referenzfalls.',
	],
	[
		'question' => 'Wo kann man PV-Leads kaufen – und worauf ist zu achten?',
		'answer'   => 'Der Markt lässt sich in geteilte Datensätze, exklusive Datensätze, regional erzeugte Kampagnen-Leads und eigene Anfrage-Infrastruktur einordnen. Unabhängig vom Anbieter entscheiden Intent-Stärke, Exklusivität, Vorqualifizierung sowie Übergabe- und Datenregeln über die Wirtschaftlichkeit.',
	],
	[
		'question' => 'Aber Portal-Leads liefern doch sofort Anfragen.',
		'answer'   => 'Zukauf kann eine kurzfristige Kapazitätslücke überbrücken. Ein eigener Anfragekanal hat einen anderen Zweck: Er soll Nachfrage, Qualifizierung und Lerndaten schrittweise in das Eigentum des Betriebs holen. Welcher Weg gerade sinnvoll ist, hängt von Zielgebiet, Vertrieb und Zeithorizont ab.',
	],
	[
		'question' => 'Was kostet der Aufbau eines eigenen Anfragesystems?',
		'answer'   => sprintf( 'Der Aufbau beginnt bei %s netto für eine Produktstrecke; jede weitere kostet %s zusätzlich. Rund %s Hosting pro Monat und Werbebudget kommen hinzu. Der Marktcheck vorab ist %s. Umfang und Start werden vor dem Auftrag vereinbart.', $foundation_price, $extra_product_price, $hosting_price, $marketcheck_price ),
	],
	[
		'question' => 'Wie lange dauert es, bis sich das rechnet?',
		'answer'   => sprintf( 'Eine allgemeine Amortisationsdauer wäre unseriös. Im dokumentierten Fall wurden über %1$s %2$s Anfragen insgesamt erfasst; die Abschlussquote der vorqualifizierten CRM-Leads lag bei %3$s und die Kosten pro Anfrage gingen von %4$s auf %5$s zurück. Ob diese Mechanik übertragbar ist, prüft der Marktcheck anhand Ihrer Ausgangslage.', $e3_timeframe, $e3_lead_count, $e3_sales_conversion, $e3_cpl_before, $e3_cpl_after ),
	],
	[
		'question' => 'Verkaufen Sie selbst Leads?',
		'answer'   => 'Nein. Es werden keine Datensätze weiterverkauft. Aufgebaut wird die Infrastruktur, die in Ihrem Eigentum bleibt: Code, Tracking, CRM-Anbindung und Datenhoheit. Was Sie damit anfangen, bleibt Ihre Entscheidung.',
	],
	[
		'question' => 'Funktioniert das auch für Wärmepumpen und Speicher?',
		'answer'   => 'Die Grundarchitektur kann für Photovoltaik, Wärmepumpe und Speicher eingesetzt werden. Suchintention, Fragen und Fit-Kriterien müssen jedoch pro Produkt angepasst werden. Für den generischen Wärmepumpen-Lead-Intent gibt es deshalb eine eigene Seite.',
	],
	[
		'question' => 'Wir nutzen schon DAA/Aroundhome/Check24. Was ist anders?',
		'answer'   => 'Der Unterschied liegt nicht im Namen des Anbieters, sondern im Eigentum am Anfrageweg. Prüfen Sie, wem Landingpage, Tracking, Kampagnendaten und Vertriebsfeedback gehören. Ein eigenes System soll diese Ebenen unter Ihrer Kontrolle verbinden; bestehender Zukauf kann während des Übergangs weiterlaufen.',
	],
];

// ── Schema.org: Service + FAQPage + BreadcrumbList ────────────
$author_person   = function_exists( 'hu_get_canonical_author_person' ) ? hu_get_canonical_author_person() : [ '@type' => 'Person', 'name' => 'Haşim Üner', 'url' => home_url( '/' ) ];
$breadcrumb_schema = function_exists( 'hu_get_solar_subpage_breadcrumb_schema' )
	? hu_get_solar_subpage_breadcrumb_schema( $page_url, 'Solar Leads kaufen – Alternative' )
	: [];

$service_schema = [
	'@context'    => 'https://schema.org',
	'@type'       => 'Service',
	'@id'         => trailingslashit( $page_url ) . '#service',
	'name'        => 'Eigenes Anfragesystem statt Portal-Leads für Solar, Wärmepumpe und Speicher',
	'serviceType' => 'Alternative zu Lead-Portalen: Aufbau eigener Anfrage-Infrastruktur',
	'url'         => $page_url,
	'description' => sprintf( 'Aufbau eines eigenen Anfragesystems für Solar-, Wärmepumpen- und Speicher-Anbieter im DACH-Raum. Referenz %1$s: %2$s niedrigere Cost per Lead in %3$s.', $e3_case_label, $e3_cpl_reduction, $e3_timeframe ),
	'provider'    => [ '@id' => home_url( '/#organization' ) ],
	'author'      => $author_person,
	'areaServed'  => [
		[ '@type' => 'Country', 'name' => 'Deutschland' ],
		[ '@type' => 'Country', 'name' => 'Österreich' ],
		[ '@type' => 'Country', 'name' => 'Schweiz' ],
	],
];

$faq_schema = [
	'@context'   => 'https://schema.org',
	'@type'      => 'FAQPage',
	'@id'        => trailingslashit( $page_url ) . '#faq',
	'url'        => trailingslashit( $page_url ) . '#faq',
	'mainEntity' => [],
];

foreach ( $objections as $faq_item ) {
	$faq_schema['mainEntity'][] = [
		'@type'          => 'Question',
		'name'           => $faq_item['question'],
		'acceptedAnswer' => [
			'@type' => 'Answer',
			'text'  => $faq_item['answer'],
		],
	];
}

get_header();
?>

<div id="primary" class="hu-intercept hu-buy" data-track-page="solar-leads-kaufen-alternative">

	<section class="hu-buy__band hu-buy__band--dark hu-buy__band--hero" id="hero" data-nx-theme="dark" data-track-section="hero" aria-labelledby="hu-buy-hero-title">
		<div class="hu-buy__container">
			<div class="hu-buy__hero-grid">
				<div class="hu-buy__hero-copy">
					<p class="hu-buy__eyebrow">Für Solar-, Wärmepumpen- und Speicher-Anbieter im DACH-Markt</p>
					<h1 class="hu-buy__h1" id="hu-buy-hero-title">
						<span class="hu-buy__h1-line">Solar Leads kaufen —</span>
						<span class="hu-buy__h1-line">oder eigene Anfragen</span>
						<span class="hu-buy__h1-line">gewinnen?</span>
					</h1>
					<p class="hu-buy__lead">
						Der Zukauf kann kurzfristig die Pipeline füllen. Er löst aber nicht automatisch Exklusivität, Vorqualifizierung, Messbarkeit oder Datenbesitz. Diese Seite zeigt, welche Modelle Sie vergleichen sollten — und wann ein eigener Anfrageweg wirtschaftlich sinnvoller sein kann.
					</p>
					<?php get_template_part( 'template-parts/seo-subpage-byline', null, [ 'template_path' => __FILE__ ] ); ?>
					<div class="hu-buy__actions">
						<a class="hu-buy__button hu-buy__button--primary"
						   href="<?php echo esc_url( $marktcheck_url ); ?>"
						   data-track-action="cta_marktcheck"
						   data-track-category="intercept_solar_leads"
						   data-track-section="hero">
							Marktcheck anfragen
							<span aria-hidden="true">→</span>
						</a>
						<a class="hu-buy__button hu-buy__button--secondary"
						   href="<?php echo esc_url( $cost_study_url ); ?>"
						   data-track-action="cta_cost_study"
						   data-track-category="internal_link_hierarchy"
						   data-track-section="hero">
							Kosten mit eigenen Zahlen prüfen
						</a>
					</div>
					<p class="hu-buy__microcopy"><?php echo esc_html( ucfirst( $marketcheck_price ) ); ?> · Befund per E-Mail · Absenden ist noch keine Buchung</p>
				</div>

				<aside class="hu-buy__proof" aria-labelledby="hu-buy-proof-title">
					<p class="hu-buy__proof-kicker">Dokumentierter Wechsel</p>
					<h2 class="hu-buy__proof-title" id="hu-buy-proof-title">Von gekauften Kontakten zu einem eigenen Anfrageweg</h2>
					<dl class="hu-buy__proof-grid">
						<div>
							<dt>Kosten pro Anfrage</dt>
							<dd><?php echo esc_html( $e3_cpl_before ); ?> <span>→</span> <?php echo esc_html( $e3_cpl_after ); ?></dd>
						</div>
						<div>
							<dt>Abschlussquote der CRM-Leads</dt>
							<dd><?php echo esc_html( $e3_sales_conversion ); ?></dd>
						</div>
						<div>
							<dt>Anfragen insgesamt</dt>
							<dd><?php echo esc_html( $e3_lead_count ); ?></dd>
						</div>
						<div>
							<dt>Beobachteter Zeitraum</dt>
							<dd><?php echo esc_html( $e3_timeframe ); ?></dd>
						</div>
					</dl>
					<p class="hu-buy__proof-note">Ein anonymisierter <?php echo esc_html( $e3_case_label ); ?>. Der erreichte CPL ist kein Durchschnitt über den gesamten Zeitraum; die Quote betrifft vorqualifizierte CRM-Leads. Kein allgemeines Ergebnisversprechen.</p>
					<a class="hu-buy__text-link"
					   href="<?php echo esc_url( $e3_url ); ?>"
					   data-track-action="cta_case_study"
					   data-track-category="proof"
					   data-track-section="hero">Methodik und Grenzen des Falls ansehen <span aria-hidden="true"><?php echo hu_arrow_up_right_svg(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static inline SVG ?></span></a>
				</aside>
			</div>

			<nav class="hu-buy__jump" aria-label="Entscheidungspfade auf dieser Seite">
				<a href="#markt-modelle"><span>01</span> Beschaffungsmodelle</a>
				<a href="#qualitaet"><span>02</span> Leadqualität prüfen</a>
				<a href="#system"><span>03</span> Eigenen Kanal verstehen</a>
			</nav>
		</div>
	</section>

	<section class="hu-buy__band hu-buy__band--light hu-buy__band--cream" id="vergleich" data-nx-theme="light" data-track-section="vergleich" aria-labelledby="hu-buy-compare-title">
		<div class="hu-buy__container">
			<header class="hu-buy__section-head">
				<p class="hu-buy__eyebrow">Die erste Entscheidung</p>
				<h2 class="hu-buy__h2" id="hu-buy-compare-title">Vergleichen Sie den Anfrageweg und die Arbeit bis zum Auftrag.</h2>
				<p class="hu-buy__section-lead">Prüfen Sie bei jedem Modell: Was wird geliefert, welche Angaben liegen vor und welche Arbeit bleibt bei Ihrem Team? Ein eigener Kanal braucht Aufbau und laufende Nachfragegewinnung. Ein Portal ergänzt Kontakte zu den vereinbarten Konditionen.</p>
			</header>
			<div class="hu-buy__compare-grid">
				<article class="hu-buy__compare-card hu-buy__compare-card--rent">
					<p class="hu-buy__compare-label">Variable Beschaffung</p>
					<h3>Portal-Leads kaufen</h3>
					<ul class="hu-buy__fact-list" role="list">
						<?php foreach ( $portal_facts as $fact ) : ?>
							<li><strong><?php echo esc_html( $fact['k'] ); ?></strong><span><?php echo esc_html( $fact['l'] ); ?></span></li>
						<?php endforeach; ?>
					</ul>
				</article>
				<article class="hu-buy__compare-card hu-buy__compare-card--own">
					<p class="hu-buy__compare-label">Eigene Infrastruktur</p>
					<h3>Eigenes Anfragesystem</h3>
					<ul class="hu-buy__fact-list" role="list">
						<?php foreach ( $own_facts as $fact ) : ?>
							<li><strong><?php echo esc_html( $fact['k'] ); ?></strong><span><?php echo esc_html( $fact['l'] ); ?></span></li>
						<?php endforeach; ?>
					</ul>
				</article>
			</div>
			<div class="hu-buy__model-grid solar-model-choice">
				<article class="hu-buy__model-card"><h3>Zukauf ergänzen</h3><p>Sinnvoll, wenn Sie kurzfristig Kontakte bearbeiten können und Vertrag, Projektpassung und Ihre tatsächlichen Kosten pro Auftrag tragfähig sind.</p></article>
				<article class="hu-buy__model-card"><h3>Beide Wege parallel nutzen</h3><p>Bestehende Versorgung weiterführen und den eigenen Kanal aufbauen. Quellen im CRM getrennt halten, damit ihre Kosten und Ergebnisse vergleichbar bleiben.</p></article>
				<article class="hu-buy__model-card"><h3>Eigenen Anfrageweg aufbauen</h3><p>Passend, wenn Zielgebiet, Marge und Vertrieb den Aufbau tragen. Code, Konten und Daten werden übergeben; laufende Nachfragegewinnung bleibt eine Aufgabe.</p></article>
			</div>
			<p class="hu-buy__compare-note">Für den Gesamtkostenvergleich zählen auf beiden Seiten Anfragegewinnung, Bearbeitung und Betreuung im selben Zeitraum. Beim eigenen Weg kommen Aufbau und Betrieb hinzu. Eine pauschale Amortisation lässt sich daraus nicht zusagen.</p>
			<div class="hu-buy__source-bar">
				<span>Sauber getrennte Quellen:</span>
				<a href="<?php echo esc_url( $cost_study_url ); ?>" data-track-action="source_cost_study" data-track-category="proof" data-track-section="vergleich">Kostenrechnung und Methodik</a>
				<a href="<?php echo esc_url( $e3_url ); ?>" data-track-action="source_case_study" data-track-category="proof" data-track-section="vergleich">eigene Case-Zahlen</a>
			</div>
		</div>
	</section>


	<section class="hu-buy__band hu-buy__band--light hu-buy__band--white" id="markt-modelle" data-nx-theme="light" data-track-section="markt-modelle" aria-labelledby="hu-buy-market-title">
		<div class="hu-buy__container">
			<header class="hu-buy__section-head">
				<p class="hu-buy__eyebrow">Vier Beschaffungsmodelle</p>
				<h2 class="hu-buy__h2" id="hu-buy-market-title">Welches Beschaffungsmodell passt zu Ihrem Betrieb?</h2>
				<p class="hu-buy__section-lead">Klären Sie Kontaktverteilung, Projektangaben, Kontenzugriff und Kündigungsfolgen schriftlich. Der Markenname allein beantwortet diese Fragen nicht.</p>
			</header>
			<div class="hu-buy__model-grid">
				<?php foreach ( $market_models as $i => $model ) : ?>
					<article class="hu-buy__model-card">
						<span class="hu-buy__model-number"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
						<h3><?php echo esc_html( $model['t'] ); ?></h3>
						<p><?php echo nexus_glossary_explain_text( $model['s'], 'lead-qualifizierung', 'Vorqualifizierung' ); ?></p>
					</article>
				<?php endforeach; ?>
			</div>
			<nav class="hu-buy__provider-nav" aria-label="Einordnungen einzelner Lead-Anbieter">
				<p>Anbieter einzeln einordnen:</p>
				<ul role="list">
					<li><a href="<?php echo esc_url( home_url( '/aroundhome-solar-einordnung/' ) ); ?>" data-track-action="provider_aroundhome" data-track-category="internal_link_hierarchy" data-track-section="markt-modelle">Aroundhome</a></li>
					<li><a href="<?php echo esc_url( home_url( '/wattfox-solar-leads-einordnung/' ) ); ?>" data-track-action="provider_wattfox" data-track-category="internal_link_hierarchy" data-track-section="markt-modelle">Wattfox</a></li>
					<li><a href="<?php echo esc_url( home_url( '/daa-photovoltaik-leads-einordnung/' ) ); ?>" data-track-action="provider_daa" data-track-category="internal_link_hierarchy" data-track-section="markt-modelle">DAA</a></li>
					<li><a href="<?php echo esc_url( home_url( '/checkfox-solar-waermepumpe-einordnung/' ) ); ?>" data-track-action="provider_checkfox" data-track-category="internal_link_hierarchy" data-track-section="markt-modelle">Checkfox</a></li>
					<li><a href="<?php echo esc_url( home_url( '/leadfluss-pv-leads-einordnung/' ) ); ?>" data-track-action="provider_leadfluss" data-track-category="internal_link_hierarchy" data-track-section="markt-modelle">Leadfluss</a></li>
				</ul>
			</nav>
		</div>
	</section>

	<section class="hu-buy__band hu-buy__band--dark hu-buy__band--warm" id="qualitaet" data-nx-theme="dark" data-track-section="qualitaet" aria-labelledby="hu-buy-quality-title">
		<div class="hu-buy__container">
			<header class="hu-buy__section-head hu-buy__section-head--split">
				<div>
					<p class="hu-buy__eyebrow">Vor jedem Einkauf prüfen</p>
					<h2 class="hu-buy__h2" id="hu-buy-quality-title">Qualifizierte PV-Anfragen: Vier Angaben vor dem Rückruf prüfen.</h2>
				</div>
				<p class="hu-buy__section-lead">Die Kriterien gelten für Portal-Zukauf, regionale Kampagnen und eigene Strecken. Ein Modell ist nur so gut wie die Informationen, die der Vertrieb vor dem Rückruf erhält.</p>
			</header>
			<ol class="hu-buy__quality-grid">
				<?php foreach ( $buyer_check_criteria as $i => $crit ) : ?>
					<li>
						<span class="hu-buy__quality-number"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
						<div><h3><?php echo esc_html( $crit['k'] ); ?></h3><p><?php echo esc_html( $crit['l'] ); ?></p></div>
					</li>
				<?php endforeach; ?>
			</ol>
			<details class="hu-buy__faq-item" name="hu-quality-detail">
				<summary>Welche Warnsignale sollte Ihr Vertrieb prüfen?</summary>
				<p>Fehlende Region, unklare Projektabsicht, ein unbekannter Erfassungszeitpunkt oder fehlende Einwilligungs- und Quelldaten gehören in die Nachprüfung. Ein älterer Kontakt ist nicht automatisch wertlos; entscheidend sind Erreichbarkeit, Projektstatus und Ihr weiterer Umgang damit.</p>
				<p>Erfassen Sie im CRM Quelle, Projektmerkmale, ersten Kontaktversuch und Ergebnis. Vergleichen Sie anschließend passende und gewonnene Projekte je Quelle; ein hoher Formularzähler allein belegt noch keine Lead-Qualität.</p>
			</details>
		</div>
	</section>

	<section class="hu-buy__band hu-buy__band--light hu-buy__band--cream" id="system" data-nx-theme="light" data-track-section="system" aria-labelledby="hu-buy-system-title">
		<div class="hu-buy__container">
			<header class="hu-buy__section-head">
				<p class="hu-buy__eyebrow">Die Alternative zum dauerhaften Zukauf</p>
				<h2 class="hu-buy__h2" id="hu-buy-system-title">Ein eigener Anfragekanal verbindet Nachfrage, Qualifizierung und Vertrieb.</h2>
				<p class="hu-buy__section-lead">Nicht jede Firma braucht sofort den vollständigen Aufbau. Der Marktcheck klärt zuerst, welcher Engpass tatsächlich vorliegt. Wenn ein eigener Kanal sinnvoll ist, greifen diese vier Ebenen ineinander.</p>
			</header>
			<ol class="hu-buy__layers">
				<?php foreach ( $own_system_layers as $i => $layer ) : ?>
					<li>
						<span class="hu-buy__layer-index"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
						<div class="hu-buy__layer-body">
							<h3><?php echo esc_html( $layer['t'] ); ?></h3>
							<p><?php echo esc_html( $layer['s'] ); ?></p>
						</div>
					</li>
				<?php endforeach; ?>
			</ol>
			<div class="hu-buy__inline-action">
				<p>Für Wärmepumpen braucht die Vorqualifizierung andere Projektangaben. Die <a href="<?php echo esc_url( $waermepumpen_url ); ?>" data-track-action="related_waermepumpen_leads" data-track-category="internal_link_hierarchy" data-track-section="system">Wärmepumpen-Leads-Seite</a> erklärt Bestandsheizung, Gebäude und Zeithorizont.</p>
				<a class="hu-buy__button hu-buy__button--primary" href="<?php echo esc_url( $marktcheck_url ); ?>" data-track-action="cta_marktcheck" data-track-category="intercept_solar_leads" data-track-section="system">Marktcheck anfragen <span aria-hidden="true">→</span></a>
			</div>
		</div>
	</section>

	<section class="hu-buy__band hu-buy__band--dark hu-buy__band--deep" id="nachweis" data-nx-theme="dark" data-track-section="nachweis" aria-labelledby="hu-buy-case-title">
		<div class="hu-buy__container">
			<div class="hu-buy__case-grid">
				<div>
					<p class="hu-buy__eyebrow">Beleg mit klarer Grenze</p>
					<h2 class="hu-buy__h2" id="hu-buy-case-title">Was sich im dokumentierten Fall verändert hat.</h2>
					<p class="hu-buy__section-lead">Der Fall beweist kein allgemeines Ergebnis. Er zeigt, was möglich wurde, als Anfrageweg, Vorqualifizierung und Messung nicht mehr voneinander getrennt liefen.</p>
					<ul class="hu-buy__case-mechanism" role="list">
						<li>Eigene Seitenstrecke statt ausschließlichem Datensatzbezug</li>
						<li>Projektkontext vor der Übergabe an den Vertrieb</li>
						<li>Rückführung von Anfragequalität und Abschlussstatus</li>
					</ul>
					<a class="hu-buy__button hu-buy__button--secondary" href="<?php echo esc_url( $e3_url ); ?>" data-track-action="cta_case_study" data-track-category="proof" data-track-section="nachweis">Vollständige Methodik lesen</a>
				</div>
				<dl class="hu-buy__case-stats">
					<div><dt>Vorher</dt><dd><?php echo esc_html( $e3_cpl_before ); ?> CPL</dd><span>gekaufte Portal-Anfragen</span></div>
					<div><dt>Nachher</dt><dd><?php echo esc_html( $e3_cpl_after ); ?> CPL</dd><span>eigener Anfrageweg</span></div>
					<div><dt>Vertrieb</dt><dd><?php echo esc_html( $e3_sales_conversion ); ?></dd><span>Abschlussquote der CRM-Leads</span></div>
					<div><dt>Zeitraum</dt><dd><?php echo esc_html( $e3_timeframe ); ?></dd><span><?php echo esc_html( $e3_lead_count ); ?> Anfragen insgesamt</span></div>
				</dl>
			</div>
		</div>
	</section>

	<section class="hu-buy__band hu-buy__band--light hu-buy__band--white" id="vertiefung" data-nx-theme="light" data-track-section="vertiefung" aria-labelledby="hu-buy-linked-title">
		<div class="hu-buy__container">
			<header class="hu-buy__section-head">
				<p class="hu-buy__eyebrow">Für die eigene Prüfung</p>
				<h2 class="hu-buy__h2" id="hu-buy-linked-title">Die nächste Frage gezielt vertiefen.</h2>
				<p class="hu-buy__section-lead">Rechnen Sie Ihre Kosten durch oder prüfen Sie die Anforderungen an Wärmepumpen- und Gewerbe-PV-Projekte.</p>
			</header>
			<div class="hu-buy__resource-grid">
				<?php foreach ( $linked_assets as $i => $item ) : ?>
					<article class="hu-buy__resource-card">
						<span><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
						<h3><a href="<?php echo esc_url( $item['url'] ); ?>" data-track-action="related_content" data-track-category="internal_link_hierarchy" data-track-section="vertiefung"><?php echo esc_html( $item['t'] ); ?></a></h3>
						<p><?php echo esc_html( $item['s'] ); ?></p>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="hu-buy__band hu-buy__band--dark hu-buy__band--warm" id="faq" data-nx-theme="dark" data-track-section="faq" aria-labelledby="hu-buy-faq-title">
		<div class="hu-buy__container hu-buy__container--narrow">
			<header class="hu-buy__section-head">
				<p class="hu-buy__eyebrow">Vor der Entscheidung</p>
				<h2 class="hu-buy__h2" id="hu-buy-faq-title">Häufige Fragen zum Kauf von Photovoltaik- und Solar-Leads.</h2>
			</header>
			<div class="hu-buy__faq-list">
				<?php foreach ( $objections as $item ) : ?>
					<details class="hu-buy__faq-item" name="hu-faq-buy-alternative">
						<summary><?php echo esc_html( $item['question'] ); ?></summary>
						<p><?php echo esc_html( $item['answer'] ); ?></p>
					</details>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="hu-buy__band hu-buy__band--light hu-buy__band--final" id="final-cta" data-nx-theme="light" data-track-section="final" aria-labelledby="hu-buy-final-title">
		<div class="hu-buy__container hu-buy__container--narrow hu-buy__final-inner">
			<p class="hu-buy__eyebrow">Nächster sinnvoller Schritt</p>
			<h2 class="hu-buy__h2" id="hu-buy-final-title">Klären Sie zuerst, welches Beschaffungsmodell zu Ihrem Betrieb passt.</h2>
			<p class="hu-buy__final-text">Der Marktcheck ordnet Zielgebiet, Vertrieb, aktuelle Leadquellen und Anfragequalität ein. Das Ergebnis ist eine klare Empfehlung für Zukauf, Übergang oder eigenen Anfragekanal — auch dann, wenn eine Umsetzung aktuell nicht sinnvoll ist.</p>
			<div class="hu-buy__actions hu-buy__actions--centered">
				<a class="hu-buy__button hu-buy__button--primary" href="<?php echo esc_url( $marktcheck_url ); ?>" data-track-action="cta_marktcheck" data-track-category="intercept_solar_leads" data-track-section="final">Marktcheck anfragen <span aria-hidden="true">→</span></a>
			</div>
			<p class="hu-buy__final-note"><?php echo esc_html( ucfirst( $marketcheck_price ) ); ?> · Absenden ist noch keine Buchung.</p>
			<p class="hu-buy__final-note">Erst die Gesamtmethode verstehen? <a href="<?php echo esc_url( $solar_money_url ); ?>" data-track-action="cta_money_page" data-track-category="internal_link_hierarchy" data-track-section="final">Das eigene Anfragesystem ansehen</a>.</p>
		</div>
	</section>

	<script type="application/ld+json"><?php echo wp_json_encode( $service_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?></script>
	<script type="application/ld+json"><?php echo wp_json_encode( $faq_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?></script>
	<?php if ( ! empty( $breadcrumb_schema ) ) : ?>
	<script type="application/ld+json"><?php echo wp_json_encode( $breadcrumb_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?></script>
	<?php endif; ?>
</div>

<?php
get_template_part(
	'template-parts/seo-subpage-sticky-cta',
	null,
	[
		'marktcheck_url' => $marktcheck_url,
		'track_category' => 'intercept_solar_leads',
	]
);

get_footer();
