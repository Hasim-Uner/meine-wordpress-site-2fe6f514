<?php
/**
 * Template Name: Solar Leads kaufen – Alternative (Intercept)
 * Description: Intercept-Landingpage für Suchintent "Solar Leads kaufen",
 *              "Photovoltaik Leads kaufen", "Wärmepumpen Leads kaufen".
 *              Argumentation: Portal-Leads vs. eigenes Anfrage-System (CPL-Senkung).
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
$tco_url         = home_url( '/eigene-leadgenerierung-vs-portale/' );
$waermepumpen_url = home_url( '/waermepumpen-leads/' );

$linked_assets = [
	[
		't'   => 'Was Solar-Leads wirklich kosten',
		's'   => 'Marktstudie zu Preismodellen, Cost per Order, Methodik und belastbaren Vergleichsgrößen.',
		'url' => $cost_study_url,
	],
	[
		't'   => 'Portal-Zukauf oder eigenes Asset?',
		's'   => 'Strategischer TCO-Vergleich über 24 Monate mit Eigentums- und Abhängigkeitslogik.',
		'url' => $tco_url,
	],
	[
		't'   => 'Cost per Lead Photovoltaik',
		's'   => 'Drei CPL-Szenarien und die Kosten, die im reinen Leadpreis nicht sichtbar werden.',
		'url' => home_url( '/cost-per-lead-photovoltaik/' ),
	],
	[
		't'   => 'Lead-Funnel für Solar-Betriebe',
		's'   => 'Die fünf Stufen vom ersten Suchsignal bis zur qualifizierten Anfrage im Vertrieb.',
		'url' => home_url( '/lead-funnel-solar/' ),
	],
];

// ── E3-Proof-Canon ─────────────────────────────────────────────
$e3_canon            = function_exists( 'hu_e3_canon' ) ? hu_e3_canon() : [];
$e3_metrics          = isset( $e3_canon['metrics'] ) && is_array( $e3_canon['metrics'] ) ? $e3_canon['metrics'] : [];
$e3_case_label       = isset( $e3_canon['case_label'] ) ? (string) $e3_canon['case_label'] : 'mittelständischer PV-Installationsbetrieb';
$e3_lead_count       = $e3_metrics['lead_count']['display'] ?? '1.750+';
$e3_sales_conversion = $e3_metrics['sales_conversion']['display'] ?? '12 %';
$e3_conv_uplift      = $e3_metrics['sales_conversion_uplift']['display'] ?? '1 – 5 % → 12 %';
$e3_conv_before      = $e3_metrics['sales_conversion_before']['display'] ?? '1 – 5 %';
$e3_cpl_reduction    = $e3_metrics['cpl_reduction']['display'] ?? 'über 85 %';
$e3_cpl_before       = $e3_metrics['cpl_before']['display'] ?? '150 €';
$e3_cpl_after        = $e3_metrics['cpl_after']['display'] ?? '22 €';
$e3_timeframe        = $e3_metrics['timeframe']['display'] ?? '6 Monate';

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
		'k' => 'Kein Asset',
		'l' => 'Mit dem Ende des Einkaufs endet auch der Anfragezufluss.',
	],
];

$own_facts = [
	[
		'k' => $e3_cpl_after,
		'l' => 'Cost per Lead im eigenen Anfrage-System (Case-Study-Referenz)',
	],
	[
		'k' => 'Eigener Kontakt',
		'l' => 'Die Anfrage läuft direkt und ohne Portal-Parallelversand in den Vertrieb.',
	],
	[
		'k' => $e3_conv_uplift,
		'l' => 'Abschlussquote im dokumentierten Fall — kein allgemeines Versprechen.',
	],
	[
		'k' => 'Eigenes Asset',
		'l' => 'Seite, Tracking, Prozess und Datenbasis bleiben beim Betrieb.',
	],
];

$why_portals_fail = [
	[
		't' => 'Der Leadpreis ist nicht der Auftragspreis',
		's' => 'Entscheidend ist, wie viele Kontakte, Rückrufversuche und Angebotsstunden bis zu einem gewonnenen Auftrag nötig sind.',
	],
	[
		't' => 'Exklusivität verändert die Vertriebslage',
		's' => 'Erhält mehr als ein Betrieb denselben Kontakt, wird Reaktionsgeschwindigkeit zum Wettbewerbsfaktor — noch vor der Beratung.',
	],
	[
		't' => 'Ungeklärte Daten kosten Vertriebszeit',
		's' => 'Fehlen Region, Vorhaben, Gebäudesituation oder Investitionsnähe, übernimmt der Vertrieb die Qualifizierung nach dem Einkauf.',
	],
	[
		't' => 'Zukauf baut keinen eigenen Kanal auf',
		's' => 'Auch ein guter Lieferant bleibt ein externer Zugang zur Nachfrage. Der Betrieb sammelt dabei kaum eigene Lern- und Steuerungsdaten.',
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
		'answer'   => 'Der Preis hängt unter anderem von Exklusivität, Region, Produkt, Vorqualifizierung und Rückerstattungsregeln ab. Ein einzelner CPL ist deshalb keine belastbare Einkaufsentscheidung. Die verlinkte Solar-Leads-Kosten-Studie trennt Marktpreise, Cost per Order und eigene Case-Werte methodisch voneinander.',
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
		'question' => 'Was kostet der Aufbau eines eigenen Anfrage-Systems?',
		'answer'   => 'Der Umfang hängt von bestehender Website, Messbarkeit, Vorqualifizierung, CRM-Anschluss und Zielgebiet ab. Deshalb steht vor einem Angebot der Marktcheck und bei passendem Fit eine vertiefte Analyse. Verglichen wird nicht nur Setup gegen Leadpreis, sondern die Gesamtkosten über den geplanten Zeitraum und der Wert des verbleibenden Assets.',
	],
	[
		'question' => 'Wie lange dauert es, bis sich das rechnet?',
		'answer'   => sprintf( 'Eine allgemeine Amortisationsdauer wäre unseriös. Im dokumentierten Fall wurden über %1$s mehr als %2$s qualifizierte Anfragen erfasst; die Abschlussquote lag bei %3$s und die Kosten pro Anfrage gingen von %4$s auf %5$s zurück. Ob diese Mechanik übertragbar ist, prüft der Marktcheck anhand Ihrer Ausgangslage.', $e3_timeframe, $e3_lead_count, $e3_sales_conversion, $e3_cpl_before, $e3_cpl_after ),
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
	'name'        => 'Eigenes Anfrage-System statt Portal-Leads für Solar, Wärmepumpe und Speicher',
	'serviceType' => 'Alternative zu Lead-Portalen: Aufbau eigener Anfrage-Infrastruktur',
	'url'         => $page_url,
	'description' => sprintf( 'Aufbau eines eigenen Anfrage-Systems für Solar-, Wärmepumpen- und Speicher-Anbieter im DACH-Raum. Referenz %1$s: %2$s niedrigere Cost per Lead in %3$s.', $e3_case_label, $e3_cpl_reduction, $e3_timeframe ),
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

<main id="primary" class="hu-intercept hu-buy" role="main" data-track-page="solar-leads-kaufen-alternative">

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
							Marktcheck mit Fit-Entscheid starten
							<span aria-hidden="true">→</span>
						</a>
						<a class="hu-buy__button hu-buy__button--secondary"
						   href="<?php echo esc_url( $cost_study_url ); ?>"
						   data-track-action="cta_cost_study"
						   data-track-category="internal_link_hierarchy"
						   data-track-section="hero">
							Kostenstudie öffnen
						</a>
					</div>
					<p class="hu-buy__microcopy">Keine Zahlungsdaten · kein Pflicht-Call · händische Fit-Einordnung statt Tool-Ergebnis</p>
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
							<dt>Abschlussquote</dt>
							<dd><?php echo esc_html( $e3_conv_uplift ); ?></dd>
						</div>
						<div>
							<dt>Qualifizierte Anfragen</dt>
							<dd><?php echo esc_html( $e3_lead_count ); ?></dd>
						</div>
						<div>
							<dt>Beobachteter Zeitraum</dt>
							<dd><?php echo esc_html( $e3_timeframe ); ?></dd>
						</div>
					</dl>
					<p class="hu-buy__proof-note">Ein anonymisierter <?php echo esc_html( $e3_case_label ); ?>. Mechanismus-Beleg, keine Übertragbarkeitsgarantie.</p>
					<a class="hu-buy__text-link"
					   href="<?php echo esc_url( $e3_url ); ?>"
					   data-track-action="cta_case_study"
					   data-track-category="proof"
					   data-track-section="hero">Methodik und Grenzen des Falls ansehen <span aria-hidden="true">↗</span></a>
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
				<h2 class="hu-buy__h2" id="hu-buy-compare-title">Nicht der Leadpreis entscheidet. Sondern was nach dem Kauf übrig bleibt.</h2>
				<p class="hu-buy__section-lead">Ein günstiger Kontakt kann teuer werden, wenn der Vertrieb erst Eignung, Region und Ernsthaftigkeit klären muss. Ein eigener Kanal kostet Aufbau — schafft dafür Daten, Lernkurve und ein übergebbares Asset.</p>
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
					<h3>Eigenes Anfrage-System</h3>
					<ul class="hu-buy__fact-list" role="list">
						<?php foreach ( $own_facts as $fact ) : ?>
							<li><strong><?php echo esc_html( $fact['k'] ); ?></strong><span><?php echo esc_html( $fact['l'] ); ?></span></li>
						<?php endforeach; ?>
					</ul>
				</article>
			</div>
			<div class="hu-buy__source-bar">
				<span>Sauber getrennte Quellen:</span>
				<a href="<?php echo esc_url( $cost_study_url ); ?>" data-track-action="source_cost_study" data-track-category="proof" data-track-section="vergleich">Marktpreise und Methodik</a>
				<a href="<?php echo esc_url( $e3_url ); ?>" data-track-action="source_case_study" data-track-category="proof" data-track-section="vergleich">eigene Case-Zahlen</a>
			</div>
		</div>
	</section>

	<section class="hu-buy__band hu-buy__band--dark hu-buy__band--deep" id="kostenlogik" data-nx-theme="dark" data-track-section="kostenlogik" aria-labelledby="hu-buy-economics-title">
		<div class="hu-buy__container">
			<header class="hu-buy__section-head hu-buy__section-head--split">
				<div>
					<p class="hu-buy__eyebrow">Vom CPL zum Auftrag</p>
					<h2 class="hu-buy__h2" id="hu-buy-economics-title">Der wirtschaftliche Engpass beginnt oft erst nach dem Leadkauf.</h2>
				</div>
				<p class="hu-buy__section-lead">Die Einkaufsrechnung endet beim Datensatz. Die Vertriebsrechnung beginnt dort: Rückruf, Nachqualifizierung, Termin, Angebot und Abschluss.</p>
			</header>
			<div class="hu-buy__reason-grid">
				<?php foreach ( $why_portals_fail as $i => $item ) : ?>
					<article class="hu-buy__reason-card">
						<span class="hu-buy__index"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
						<h3><?php echo esc_html( $item['t'] ); ?></h3>
						<p><?php echo esc_html( $item['s'] ); ?></p>
					</article>
				<?php endforeach; ?>
			</div>
			<aside class="hu-buy__equation" aria-label="Entscheidungslogik für die Kosten pro gewonnenem Auftrag">
				<span>Leadpreis</span><b aria-hidden="true">+</b><span>Vertriebseinsatz</span><b aria-hidden="true">+</b><span>Streuverlust</span><b aria-hidden="true">=</b><strong>Kosten bis zum Auftrag</strong>
			</aside>
			<a class="hu-buy__text-link" href="<?php echo esc_url( $cost_study_url ); ?>" data-track-action="cta_cost_study_economics" data-track-category="internal_link_hierarchy" data-track-section="kostenlogik">Die vollständige Kostenmethodik lesen <span aria-hidden="true">→</span></a>
		</div>
	</section>

	<section class="hu-buy__band hu-buy__band--light hu-buy__band--white" id="markt-modelle" data-nx-theme="light" data-track-section="markt-modelle" aria-labelledby="hu-buy-market-title">
		<div class="hu-buy__container">
			<header class="hu-buy__section-head">
				<p class="hu-buy__eyebrow">Vier Beschaffungsmodelle</p>
				<h2 class="hu-buy__h2" id="hu-buy-market-title">Photovoltaik-Leads-Anbieter lassen sich nicht über einen Preis vergleichen.</h2>
				<p class="hu-buy__section-lead">Vergleichbar werden Angebote erst, wenn Modell, Eigentum, Exklusivität, Datenlage und Übergabe gemeinsam betrachtet werden. Diese Einordnung ist bewusst neutral.</p>
			</header>
			<div class="hu-buy__model-grid">
				<?php foreach ( $market_models as $i => $model ) : ?>
					<article class="hu-buy__model-card">
						<span class="hu-buy__model-number"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
						<h3><?php echo esc_html( $model['t'] ); ?></h3>
						<p><?php echo esc_html( $model['s'] ); ?></p>
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
					<h2 class="hu-buy__h2" id="hu-buy-quality-title">Vier Merkmale entscheiden, ob aus einem Lead ein gutes Verkaufsgespräch werden kann.</h2>
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
				<p>Für den generischen Suchintent rund um Wärmepumpen-Anfragen bleibt die eigene <a href="<?php echo esc_url( $waermepumpen_url ); ?>" data-track-action="related_waermepumpen_leads" data-track-category="internal_link_hierarchy" data-track-section="system">Wärmepumpen-Leads-Seite</a> der thematische Owner.</p>
				<a class="hu-buy__button hu-buy__button--primary" href="<?php echo esc_url( $marktcheck_url ); ?>" data-track-action="cta_marktcheck" data-track-category="intercept_solar_leads" data-track-section="system">Eigenen Anfrageweg prüfen lassen <span aria-hidden="true">→</span></a>
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
					<div><dt>Vertrieb</dt><dd><?php echo esc_html( $e3_sales_conversion ); ?></dd><span>Abschlussquote im Fall</span></div>
					<div><dt>Zeitraum</dt><dd><?php echo esc_html( $e3_timeframe ); ?></dd><span><?php echo esc_html( $e3_lead_count ); ?> qualifizierte Anfragen</span></div>
				</dl>
			</div>
		</div>
	</section>

	<section class="hu-buy__band hu-buy__band--light hu-buy__band--white" id="vertiefung" data-nx-theme="light" data-track-section="vertiefung" aria-labelledby="hu-buy-linked-title">
		<div class="hu-buy__container">
			<header class="hu-buy__section-head">
				<p class="hu-buy__eyebrow">Für die eigene Prüfung</p>
				<h2 class="hu-buy__h2" id="hu-buy-linked-title">Kosten, Eigentum und Funnel getrennt vertiefen.</h2>
				<p class="hu-buy__section-lead">Jede Vertiefung beantwortet eine andere Entscheidung. So bleibt diese Seite beim Kauf-Intent, ohne benachbarte Queries zu übernehmen.</p>
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
				<a class="hu-buy__button hu-buy__button--primary" href="<?php echo esc_url( $marktcheck_url ); ?>" data-track-action="cta_marktcheck" data-track-category="intercept_solar_leads" data-track-section="final">Marktcheck mit Fit-Entscheid starten <span aria-hidden="true">→</span></a>
			</div>
			<p class="hu-buy__final-note">Erst die Gesamtmethode verstehen? <a href="<?php echo esc_url( $solar_money_url ); ?>" data-track-action="cta_money_page" data-track-category="internal_link_hierarchy" data-track-section="final">Das eigene Anfrage-System ansehen</a>.</p>
		</div>
	</section>

	<script type="application/ld+json"><?php echo wp_json_encode( $service_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?></script>
	<script type="application/ld+json"><?php echo wp_json_encode( $faq_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?></script>
	<?php if ( ! empty( $breadcrumb_schema ) ) : ?>
	<script type="application/ld+json"><?php echo wp_json_encode( $breadcrumb_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?></script>
	<?php endif; ?>
</main>

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
