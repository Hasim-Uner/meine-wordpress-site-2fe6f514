<?php
/**
 * Template Name: B2B Solar Leads – Gewerbliche Photovoltaik
 * Description: Money-Page für gewerbliche PV-Leadgenerierung. Buying-Center,
 *              lange Sales-Zyklen, hohe Ticketgrößen. Abgrenzung zum
 *              B2C-Privatmarkt.
 *              Primärer CTA: Marktcheck auf /solar-waermepumpen-leadgenerierung/#marktcheck.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ── URLs ───────────────────────────────────────────────────────
$page_url        = home_url( '/b2b-solar-leads/' );
$solar_money_url = function_exists( 'nexus_get_energy_systems_url' )
	? nexus_get_energy_systems_url()
	: home_url( '/solar-waermepumpen-leadgenerierung/' );
$marktcheck_url  = trailingslashit( $solar_money_url ) . '#marktcheck';
$e3_url          = home_url( '/case-study-solar-leadgenerierung/' );

// Kontextuelle Cluster-Links: Ziele kommen aus der Registry in
// inc/seo-subpage-cluster-links.php, damit Pfade nicht doppelt gepflegt
// werden und mit den automatischen Footer-Links konsistent bleiben.
$cluster_map  = function_exists( 'hu_get_solar_cluster_link_map' ) ? hu_get_solar_cluster_link_map() : [];
$cluster_url  = static function ( $slug, $fallback ) use ( $cluster_map ) {
	return isset( $cluster_map[ $slug ]['path'] )
		? home_url( (string) $cluster_map[ $slug ]['path'] )
		: home_url( $fallback );
};
$qualified_url = $cluster_url( 'qualifizierte-pv-anfragen', '/qualifizierte-pv-anfragen/' );
$cpl_url       = $cluster_url( 'cost-per-lead-photovoltaik', '/cost-per-lead-photovoltaik/' );
$tracking_url  = $cluster_url( 'server-side-tracking-b2b', '/server-side-tracking-b2b/' );
// Intent-Trennung: wer den Portal-Vergleich sucht, gehoert auf die
// Kauf-Alternative — nicht auf diese Gewerbe-/Termin-Seite.
$portal_alternative_url = $cluster_url( 'solar-leads-kaufen-alternative', '/solar-leads-kaufen-alternative/' );

// ── E3-Canon ──────────────────────────────────────────────────
$e3_canon            = function_exists( 'hu_e3_canon' ) ? hu_e3_canon() : [];
$e3_metrics          = isset( $e3_canon['metrics'] ) && is_array( $e3_canon['metrics'] ) ? $e3_canon['metrics'] : [];
$e3_case_label       = isset( $e3_canon['case_label'] ) ? (string) $e3_canon['case_label'] : 'mittelständischer PV-Installationsbetrieb';
$e3_cpl_before       = $e3_metrics['cpl_before']['display'] ?? '150 €';
$e3_cpl_after        = $e3_metrics['cpl_after']['display'] ?? '22 €';
$e3_cpl_reduction    = $e3_metrics['cpl_reduction']['display'] ?? 'über 85 %';
$e3_lead_count       = $e3_metrics['lead_count']['display'] ?? '1.750+';
$e3_sales_conversion = $e3_metrics['sales_conversion']['display'] ?? '12 %';
$e3_conv_uplift      = $e3_metrics['sales_conversion_uplift']['display'] ?? '1 – 5 % → 12 %';
$e3_timeframe        = $e3_metrics['timeframe']['display'] ?? '6 Monate';

$pricing_canon = function_exists( 'hu_pricing_canon' ) ? hu_pricing_canon() : [];
$duration_min  = isset( $pricing_canon['foundation_duration_weeks_min'] ) ? (int) $pricing_canon['foundation_duration_weeks_min'] : 8;
$duration_max  = isset( $pricing_canon['foundation_duration_weeks_max'] ) ? (int) $pricing_canon['foundation_duration_weeks_max'] : 10;

// ── Inhalte ───────────────────────────────────────────────────
// Die 50.000 € sind die eigene Fit-Schwelle, keine Marktstudie. Alle übrigen
// Punkte sind Prüfkriterien statt ungesicherter Branchen-Durchschnittswerte.
$b2b_facts = [
	[ 'n' => '01', 'k' => 'ab 50.000 €', 't' => 'Projektwert', 'l' => 'Eigene Fit-Schwelle: Unterhalb davon ist ein vollständiges Gewerbe-Anfrage-System meist nicht der erste wirtschaftliche Hebel.' ],
	[ 'n' => '02', 'k' => 'prüfbar', 't' => 'Technische Substanz', 'l' => 'Dachfläche, Anschlussleistung, Trafostation und Nutzungsprofil müssen vor dem Vertriebsgespräch belastbar einzuordnen sein.' ],
	[ 'n' => '03', 'k' => 'entscheidungsfähig', 't' => 'Buying-Center', 'l' => 'Die Anfrage muss zeigen, wer fachlich prüft, wer Budget freigibt und wer den Vertrag verantwortet.' ],
	[ 'n' => '04', 'k' => 'terminierbar', 't' => 'Investitionspfad', 'l' => 'Budgetrahmen, Zeithorizont und nächster interner Beschluss gehören in die Vorqualifizierung — nicht in ein spätes Nachfassgespräch.' ],
];

// Feindbild ist bewusst der Terminierungs-Dienstleister, nicht das
// B2C-Lead-Portal: Ein Betrieb mit Hallendach-Projekten kauft bei Aroundhome
// oder DAA ohnehin nicht ein. Was im Gewerbe wirklich eingekauft wird, sind
// gelegte Vertriebstermine — dagegen argumentiert diese Seite.
$why_b2c_funnel_fails = [
	[
		't' => 'Der Termin ist das falsche Erfolgsmaß',
		's' => 'Ein voller Kalender sieht nach Pipeline aus. Ohne Projektwert, technische Machbarkeit und Freigabepfad ist er aber nur gebuchte Vertriebszeit.',
	],
	[
		't' => 'Die Prüfung landet beim teuersten Mitarbeiter',
		's' => 'Was ein Formular vorab klären könnte, prüft sonst der Vertrieb im Gespräch: Dach, Anschluss, Budget, Rolle und Zeitplan. Die Qualifizierung wird dadurch nicht vermieden, sondern verteuert.',
	],
	[
		't' => 'Eine Kontaktperson ist noch kein Buying-Center',
		's' => 'Technik, Geschäftsführung, Finanzen und Einkauf prüfen verschiedene Risiken. Fehlt die Freigaberolle, folgt nach dem vermeintlichen Termin eine neue Gesprächsrunde.',
	],
	[
		't' => 'Fremde Kriterien steuern Ihren Kalender',
		's' => 'Ein Terminierungs-Dienstleister optimiert auf angenommene Termine. Ihr Vertrieb braucht dagegen Projekte, die zu Zielgebiet, Projektschwelle und Lieferfähigkeit passen.',
	],
];

$gewerbe_layers = [
	[
		't' => 'Gewerbe-Money-Page',
		's' => 'Hallendach, Quartier, Speicher oder PPA werden in der Sprache des jeweiligen Investitionsfalls erklärt. Keine Privatkunden-Seite mit einem nachträglichen „auch für Gewerbe".',
	],
	[
		't' => 'Vorqualifizierung vor dem Kalender',
		's' => 'Projektart, Standort, Dachfläche, Anschlussleistung, Investitionsrahmen und Zeithorizont werden erfasst, bevor ein Vertriebstermin angeboten wird.',
	],
	[
		't' => 'Fit-Entscheidung nach Projektsubstanz',
		's' => 'Anfragen werden nach den Kriterien Ihres Vertriebs eingeordnet. Welche Signale dabei zählen, steht im Detail unter <a href="' . esc_url( $qualified_url ) . '">qualifizierte PV-Anfragen</a>.',
	],
	[
		't' => 'Buying-Center und CRM-Übergabe',
		's' => 'Rolle, Freigabestatus und nächster Schritt bleiben an der Anfrage. Der Vertrieb startet mit Kontext statt mit einer isolierten E-Mail-Adresse.',
	],
	[
		't' => 'Messung bis zur Vertriebsqualität',
		's' => '<a href="' . esc_url( $tracking_url ) . '">Server-Side Tracking</a> verbindet Anfragequelle und Qualifizierungsstatus. So wird sichtbar, welcher Kanal passende Gewerbe-Projekte liefert — nicht nur billige Klicks.',
	],
];

// Zeilenmodell für die Vergleichsmatrix (.hu-intercept__matrix): je Kriterium
// eine Zeile, damit gekaufter und eigener Termin direkt nebeneinander stehen
// statt in zwei gestapelten Panels.
$pv_termine_rows = [
	[
		'criterion' => 'Erfolgssignal',
		'bought'    => 'Der Termin wurde angenommen und in den Kalender gelegt.',
		'own'       => 'Projektwert, technische Eckdaten und Freigabepfad passen zu Ihren Kriterien.',
	],
	[
		'criterion' => 'Qualifizierung',
		'bought'    => 'Im oder nach dem Erstgespräch — durch Ihren Vertrieb.',
		'own'       => 'Vor der Terminvergabe — durch die eigene Anfragestrecke.',
	],
	[
		'criterion' => 'Kriterienhoheit',
		'bought'    => 'Der Anbieter definiert, wann ein Kontakt als Termin zählt.',
		'own'       => 'Ihr Betrieb definiert, wann eine Anfrage vertriebsreif ist.',
	],
	[
		'criterion' => 'Messung',
		'bought'    => 'Kosten und Anzahl der vereinbarten Termine.',
		'own'       => 'Quelle, Fit, Vertriebsstatus und späterer Auftrag.',
	],
];

$fit_yes = [
	[ 't' => 'Hoher Projektwert', 's' => 'Ihre Zielprojekte liegen ab der eigenen Fit-Schwelle von 50.000 €.' ],
	[ 't' => 'Eigener Vertrieb', 's' => 'Es gibt Kapazität, passende Anfragen zeitnah zu prüfen und weiterzuführen.' ],
	[ 't' => 'Klares Zielgebiet', 's' => 'Region, Projekttyp und technische Mindestkriterien lassen sich eindeutig abgrenzen.' ],
	[ 't' => 'Langfristiger Aufbau', 's' => 'Sie wollen Nachfrage, Daten und Optimierungswissen im eigenen Betrieb halten.' ],
];

$fit_no = [
	[ 't' => 'Reines Privatkundengeschäft', 's' => 'Für Einfamilienhäuser führt der passende Weg über <a href="' . esc_url( $solar_money_url ) . '">Leadgenerierung für Photovoltaik und Wärmepumpe</a>.' ],
	[ 't' => 'Sofortige Terminmenge', 's' => 'Wenn ausschließlich kurzfristig freie Kalender gefüllt werden sollen, ist ein eigener Aufbau nicht der schnellste Weg.' ],
	[ 't' => 'Keine Vertriebskapazität', 's' => 'Auch eine qualifizierte Anfrage verliert Wert, wenn intern niemand zeitnah übernehmen kann.' ],
	[ 't' => 'Unklare Projektschwelle', 's' => 'Ohne definierte Mindestkriterien lässt sich weder qualifizieren noch belastbar messen.' ],
];

$offer_steps = [
	[
		'n' => '01',
		't' => 'Marktcheck',
		's' => 'Region, Projektwert, Vertriebskapazität und heutige Anfragequellen werden eingeordnet. Ergebnis ist eine klare Fit-Entscheidung — auch gegen eine Umsetzung, wenn die Voraussetzungen fehlen.',
	],
	[
		'n' => '02',
		't' => 'Anfrage-System-Analyse',
		's' => 'Nur bei grünem oder gelbem Fit werden Suchintention, Buying-Center, Vorqualifizierung, Tracking und CRM-Übergabe als priorisierter Bauplan konkretisiert.',
	],
	[
		'n' => '03',
		't' => 'Umsetzung',
		's' => 'Money Page, Anfragestrecke, Messung und Übergabe werden gebaut. Umfang und Reihenfolge folgen der Analyse — nicht einem festen Leistungskatalog.',
	],
];

$faq = [
	[
		'question' => 'PV-Termine B2B einkaufen oder selbst aufbauen — wann lohnt sich was?',
		'answer'   => 'Einkaufen kann passen, wenn kurzfristig freie Vertriebskapazität gefüllt werden muss und Streuverluste einkalkuliert sind. Ein eigener Anfrageweg passt eher, wenn Projektwert, Zielgebiet und technische Mindestkriterien klar sind und Nachfrage langfristig im eigenen Betrieb aufgebaut werden soll. Der Marktcheck ordnet ein, welcher Weg zur aktuellen Lage passt.',
	],
	[
		'question' => 'Was macht eine Gewerbe-PV-Anfrage vertriebsreif?',
		'answer'   => 'Mindestens Projektart, Standort, Dachfläche, Anschlussleistung, Investitionsrahmen und Zeithorizont müssen einzuordnen sein. Dazu kommt die Rolle der Kontaktperson im Buying-Center. Fehlen diese Angaben, ist ein Gespräch nicht automatisch wertlos — es sollte nur nicht als qualifizierter Vertriebstermin behandelt werden.',
	],
	[
		'question' => 'Für welche Gewerbe-PV-Anbieter ist das Anfrage-System gedacht?',
		'answer'   => 'Für Anbieter mit eigenem Vertrieb, klarem Zielgebiet und Projekten ab der Fit-Schwelle von 50.000 €. Dazu können gewerbliche Photovoltaik, Speicher, EPC-Modelle oder Contracting gehören. Für reines Privatkundengeschäft oder reine Lead-Vermittlung ist diese Strecke nicht gedacht.',
	],
	[
		'question' => 'Was passiert nach dem Marktcheck?',
		'answer'   => 'Sie erhalten zuerst eine Fit-Entscheidung zu Region, Projektwert, Vertriebskapazität und Anfragequalität. Nur wenn ein eigener Anfrageweg wirtschaftlich und operativ trägt, folgt die vertiefte Anfrage-System-Analyse. Erst danach wird ein Umsetzungsumfang festgelegt.',
	],
	[
		'question' => 'Wie lange dauert die Umsetzung nach einer positiven Fit-Entscheidung?',
		'answer'   => sprintf( 'Für das Fundament sind je nach Ausgangslage %1$d–%2$d Wochen vorgesehen. Der genaue Umfang hängt davon ab, ob bestehende Website, Tracking und CRM weiterverwendet werden können. Eine feste Anfragezahl oder ein bestimmter Vertriebsabschluss wird nicht versprochen.', $duration_min, $duration_max ),
	],
	[
		'question' => 'Warum reicht eine B2C-Solar-Seite nicht für Gewerbe-PV?',
		'answer'   => 'Privat- und Gewerbeprojekte unterscheiden sich bei Sprache, Prüfkriterien, Freigaben und Vertragslogik. Eine Hausbesitzer-Strecke kann deshalb eine gewerbliche Anfrage zwar erfassen, bereitet den Vertrieb aber nicht auf Buying-Center, technische Vorprüfung und Investitionsentscheidung vor.',
	],
	[
		'question' => 'Ist die dokumentierte Fallstudie eine Ergebnisgarantie für Gewerbe-PV?',
		'answer'   => sprintf( 'Nein. Der anonymisierte Fall eines %1$s belegt den Mechanismus eines eigenen Anfragewegs: %2$s qualifizierte Anfragen, Kosten pro Anfrage von %3$s auf %4$s und eine Abschlussquote von %5$s im dokumentierten Zeitraum von %6$s. Projekttyp, Markt, Budget und Vertrieb unterscheiden sich; deshalb ist der Fall keine pauschale Übertragbarkeitsgarantie.', $e3_case_label, $e3_lead_count, $e3_cpl_before, $e3_cpl_after, $e3_sales_conversion, $e3_timeframe ),
	],
	[
		'question' => 'Geht es hier um Aroundhome, DAA oder Check24?',
		'answer'   => sprintf( 'Nein. Diese Seite behandelt eingekaufte Vertriebstermine für Gewerbe-PV. Der Vergleich mit Privatkunden-Portalen gehört zu einem anderen Such- und Geschäftsfall und steht unter %s.', '<a href="' . esc_url( $portal_alternative_url ) . '">Photovoltaik- und Solar-Leads kaufen: die Alternative</a>' ),
	],
];

// ── Schema.org ────────────────────────────────────────────────
$author_person     = function_exists( 'hu_get_canonical_author_person' ) ? hu_get_canonical_author_person() : [ '@type' => 'Person', 'name' => 'Haşim Üner', 'url' => home_url( '/' ) ];
$breadcrumb_schema = function_exists( 'hu_get_solar_subpage_breadcrumb_schema' )
	? hu_get_solar_subpage_breadcrumb_schema( $page_url, 'B2B Solar Leads (Gewerbe)' )
	: [];

$service_schema = [
	'@context'    => 'https://schema.org',
	'@type'       => 'Service',
	'@id'         => trailingslashit( $page_url ) . '#service',
	'name'        => 'PV-Termine und Anfrage-Systeme für gewerbliche Photovoltaik',
	'alternateName' => [ 'PV-Termine B2B', 'B2B Solar Leads', 'Gewerbe-PV-Anfragen' ],
	'serviceType' => 'Vorqualifizierung und Terminlogik für gewerbliche PV-, Speicher- und PPA-Anbieter',
	'url'         => $page_url,
	'description' => sprintf( 'Buying-Center-taugliche Anfrage- und Terminarchitektur für gewerbliche Photovoltaik-Projekte: Qualifizierung vor dem Termin statt Provision pro Kalendereintrag. Referenz %1$s: %2$s niedrigere Cost per Lead in %3$s.', $e3_case_label, $e3_cpl_reduction, $e3_timeframe ),
	'provider'    => [ '@id' => home_url( '/#organization' ) ],
	'author'      => $author_person,
	'audience'    => [
		'@type'        => 'Audience',
		'audienceType' => 'Gewerbliche Photovoltaik-, Speicher- und EPC-Anbieter im DACH-Raum',
	],
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

foreach ( $faq as $faq_item ) {
	$faq_schema['mainEntity'][] = [
		'@type'          => 'Question',
		'name'           => $faq_item['question'],
		'acceptedAnswer' => [
			'@type' => 'Answer',
			// Sichtbare Antwort kann Markup tragen; das Schema bekommt Klartext,
			// damit JSON-LD und sichtbarer Text dieselbe Aussage transportieren.
			'text'  => wp_strip_all_tags( $faq_item['answer'] ),
		],
	];
}

get_header();
?>

<main id="primary" class="hu-intercept hu-b2b" data-track-page="b2b-solar-leads">

	<section class="hu-b2b__band hu-b2b__band--dark hu-b2b__hero" id="hero" data-nx-theme="dark" data-track-section="hero" aria-labelledby="hu-b2b-hero-title">
		<div class="hu-intercept__container">
			<div class="hu-b2b__hero-grid">
				<div class="hu-b2b__hero-copy">
					<p class="hu-intercept__eyebrow">Gewerbe-PV · Speicher · PPA · kein Modul-Großhandel</p>
					<h1 class="hu-intercept__title" id="hu-b2b-hero-title">
						<span>PV-Termine B2B —</span>
						<span>qualifiziert, bevor</span>
						<span>Ihr Vertrieb anreist.</span>
					</h1>
					<p class="hu-intercept__lead">
						Ein Kalendereintrag ist noch keine Verkaufschance. Ein eigenes Anfrage-System prüft Projektwert, technische Substanz und Buying-Center, <strong>bevor Ihr Vertrieb Zeit blockiert</strong> — für Gewerbe-PV-Projekte ab <strong>50.000 €</strong>.
					</p>
					<?php get_template_part( 'template-parts/seo-subpage-byline', null, [ 'template_path' => __FILE__ ] ); ?>
					<div class="hu-intercept__cta hu-b2b__actions">
						<a class="hu-intercept__cta-primary"
						   href="<?php echo esc_url( $marktcheck_url ); ?>"
						   data-track-action="cta_marktcheck"
						   data-track-category="b2b_solar_leads"
						   data-track-section="hero">
							Marktcheck mit Fit-Entscheid starten <span aria-hidden="true">→</span>
						</a>
						<a class="hu-b2b__text-link"
						   href="<?php echo esc_url( $e3_url ); ?>"
						   data-track-action="cta_e3_case"
						   data-track-category="b2b_solar_leads"
						   data-track-section="hero">
							Dokumentierten Fall prüfen <span aria-hidden="true">↗</span>
						</a>
					</div>
					<p class="hu-b2b__microcopy">Region · Projektschwelle · Vertriebskapazität · händische Fit-Einordnung</p>
				</div>

				<aside class="hu-b2b__gate" aria-labelledby="hu-b2b-gate-title">
					<p class="hu-b2b__kicker">Vor dem Vertriebstermin</p>
					<h2 id="hu-b2b-gate-title">Erst Substanz.<br>Dann Termin.</h2>
					<p class="hu-b2b__gate-intro">Vier Signale müssen vor einer Kalenderbuchung belastbar sein.</p>
					<ol class="hu-b2b__gate-list">
						<li><span>01</span><div><strong>Projektwert passt</strong><small>Eigene Schwelle: ab 50.000 €</small></div></li>
						<li><span>02</span><div><strong>Technik ist einordenbar</strong><small>Dach, Anschluss und Nutzung</small></div></li>
						<li><span>03</span><div><strong>Buying-Center ist sichtbar</strong><small>Fachprüfung und Budgetfreigabe</small></div></li>
						<li><span>04</span><div><strong>Investitionspfad ist konkret</strong><small>Zeitraum und nächster Beschluss</small></div></li>
					</ol>
					<div class="hu-b2b__gate-result">
						<span>Ergebnis</span>
						<strong>Qualifizierte Anfrage → Vertriebstermin</strong>
					</div>
				</aside>
			</div>

			<div class="hu-b2b__proof" aria-labelledby="hu-b2b-proof-title">
				<div class="hu-b2b__proof-head">
					<p class="hu-b2b__kicker">Dokumentierter Mechanismus</p>
					<h2 id="hu-b2b-proof-title">Eigener Anfrageweg statt eingekaufter Kontakte</h2>
				</div>
				<dl class="hu-b2b__proof-grid" aria-label="Ergebnisse des anonymisierten Referenzfalls">
					<div><dt><?php echo esc_html( $e3_cpl_before ); ?> → <?php echo esc_html( $e3_cpl_after ); ?></dt><dd>Kosten pro Anfrage</dd></div>
					<div><dt><?php echo esc_html( $e3_conv_uplift ); ?></dt><dd>Abschlussquote</dd></div>
					<div><dt><?php echo esc_html( $e3_lead_count ); ?></dt><dd>qualifizierte Anfragen</dd></div>
					<div><dt><?php echo esc_html( $e3_timeframe ); ?></dt><dd>dokumentierter Zeitraum</dd></div>
				</dl>
				<p class="hu-b2b__proof-note">Anonymisierter <?php echo esc_html( $e3_case_label ); ?>. Der Fall belegt den Mechanismus, nicht ein pauschal übertragbares Ergebnis.</p>
			</div>
		</div>
	</section>

	<section class="hu-b2b__band hu-b2b__band--light" id="fakten" data-nx-theme="light" data-track-section="qualification-signals" aria-labelledby="hu-b2b-facts-title">
		<div class="hu-intercept__container">
			<div class="hu-b2b__section-head hu-b2b__section-head--split">
				<div>
					<p class="hu-intercept__eyebrow">Vor dem ersten Gespräch</p>
					<h2 class="hu-intercept__h2" id="hu-b2b-facts-title">Ein Termin zählt erst, wenn das Projekt dahinter trägt.</h2>
				</div>
				<p class="hu-intercept__section-lead">Diese vier Signale trennen eine interessante Adresse von einer Anfrage, für die Ihr Vertrieb Zeit reservieren sollte. Die 50.000 € sind dabei die eigene Fit-Schwelle — kein Marktmittelwert.</p>
			</div>
			<ol class="hu-b2b__signal-grid">
				<?php foreach ( $b2b_facts as $fact ) : ?>
					<li>
						<span class="hu-b2b__signal-index"><?php echo esc_html( $fact['n'] ); ?></span>
						<strong class="hu-b2b__signal-value"><?php echo esc_html( $fact['k'] ); ?></strong>
						<h3><?php echo esc_html( $fact['t'] ); ?></h3>
						<p><?php echo esc_html( $fact['l'] ); ?></p>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
	</section>

	<section class="hu-b2b__band hu-b2b__band--deep" id="pv-termine" data-nx-theme="dark" data-track-section="comparison" aria-labelledby="hu-b2b-termine-title">
		<div class="hu-intercept__container">
			<div class="hu-b2b__section-head hu-b2b__section-head--split">
				<div>
					<p class="hu-intercept__eyebrow">Kalendereintrag oder Opportunity?</p>
					<h2 class="hu-intercept__h2" id="hu-b2b-termine-title">PV-Termine B2B: gekauft oder selbst qualifiziert?</h2>
				</div>
				<p class="hu-intercept__section-lead">Der Unterschied liegt nicht im Kalender, sondern in Kriterienhoheit, Vorprüfung und Messung. Wie sich daraus echte <a href="<?php echo esc_url( $cpl_url ); ?>">Kosten pro Anfrage</a> ergeben, zeigt die separate CPL-Analyse.</p>
			</div>
			<div class="hu-b2b__table-wrap" role="region" aria-labelledby="hu-b2b-termine-title" tabindex="0">
				<table class="hu-b2b__comparison-table">
					<thead><tr><th scope="col">Kriterium</th><th scope="col">Gekaufter PV-Termin</th><th scope="col">Eigener Anfrageweg</th></tr></thead>
					<tbody>
						<?php foreach ( $pv_termine_rows as $row ) : ?>
							<tr>
								<th scope="row"><?php echo esc_html( $row['criterion'] ); ?></th>
								<td><?php echo esc_html( $row['bought'] ); ?></td>
								<td><?php echo esc_html( $row['own'] ); ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
	</section>

	<section class="hu-b2b__band hu-b2b__band--cream" id="warum-gekaufte-termine" data-nx-theme="light" data-track-section="pain-economics" aria-labelledby="hu-b2b-why-title">
		<div class="hu-intercept__container">
			<div class="hu-b2b__section-head">
				<p class="hu-intercept__eyebrow">Die versteckten Kosten</p>
				<h2 class="hu-intercept__h2" id="hu-b2b-why-title">Warum ein voller Kalender trotzdem eine schwache Pipeline sein kann.</h2>
				<p class="hu-intercept__section-lead">Terminierungs-Dienstleister optimieren auf angenommene Gespräche. Ihr Vertrieb braucht Projekte, die zu Region, technischer Machbarkeit und Freigabepfad passen.</p>
			</div>
			<ol class="hu-b2b__reason-grid">
				<?php foreach ( $why_b2c_funnel_fails as $i => $item ) : ?>
					<li>
						<span><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
						<h3><?php echo esc_html( $item['t'] ); ?></h3>
						<p><?php echo esc_html( $item['s'] ); ?></p>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
	</section>

	<section class="hu-b2b__band hu-b2b__band--dark" id="architektur" data-nx-theme="dark" data-track-section="mechanism" aria-labelledby="hu-b2b-arch-title">
		<div class="hu-intercept__container">
			<div class="hu-b2b__section-head hu-b2b__section-head--split">
				<div>
					<p class="hu-intercept__eyebrow">Der eigene Anfrageweg</p>
					<h2 class="hu-intercept__h2" id="hu-b2b-arch-title">Fünf Bausteine zwischen Suchanfrage und Vertriebstermin.</h2>
				</div>
				<p class="hu-intercept__section-lead">Die Website ist nur der sichtbare Einstieg. Erst Vorqualifizierung, CRM-Kontext und Messung machen daraus eine belastbare Gewerbe-PV-Strecke.</p>
			</div>
			<ol class="hu-b2b__process">
				<?php foreach ( $gewerbe_layers as $i => $layer ) : ?>
					<li>
						<span class="hu-b2b__process-index"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
						<div>
							<h3><?php echo esc_html( $layer['t'] ); ?></h3>
							<p><?php echo wp_kses_post( $layer['s'] ); ?></p>
						</div>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
	</section>

	<section class="hu-b2b__band hu-b2b__band--light" id="fit" data-nx-theme="light" data-track-section="fit" aria-labelledby="hu-b2b-fit-title">
		<div class="hu-intercept__container">
			<div class="hu-b2b__section-head hu-b2b__section-head--split">
				<div>
					<p class="hu-intercept__eyebrow">Klare Grenze statt breitem Pitch</p>
					<h2 class="hu-intercept__h2" id="hu-b2b-fit-title">Für wen sich ein eigener Gewerbe-PV-Anfrageweg trägt.</h2>
				</div>
				<p class="hu-intercept__section-lead">Ein eigenes System ist kein sinnvoller Standard für jeden Betrieb. Es braucht Projektsubstanz, Vertriebsfähigkeit und den Willen, Nachfrage langfristig selbst zu besitzen.</p>
			</div>
			<div class="hu-b2b__fit-grid">
				<section class="hu-b2b__fit-panel hu-b2b__fit-panel--yes" aria-labelledby="hu-b2b-fit-yes">
					<p class="hu-b2b__kicker">Guter Fit</p>
					<h3 id="hu-b2b-fit-yes">Das sollte vorhanden sein</h3>
					<ul>
						<?php foreach ( $fit_yes as $f ) : ?><li><strong><?php echo esc_html( $f['t'] ); ?></strong><span><?php echo esc_html( $f['s'] ); ?></span></li><?php endforeach; ?>
					</ul>
				</section>
				<section class="hu-b2b__fit-panel hu-b2b__fit-panel--no" aria-labelledby="hu-b2b-fit-no">
					<p class="hu-b2b__kicker">Kein sinnvoller Start</p>
					<h3 id="hu-b2b-fit-no">Dann passt ein anderer Weg besser</h3>
					<ul>
						<?php foreach ( $fit_no as $f ) : ?><li><strong><?php echo esc_html( $f['t'] ); ?></strong><span><?php echo wp_kses_post( $f['s'] ); ?></span></li><?php endforeach; ?>
					</ul>
				</section>
			</div>
		</div>
	</section>

	<section class="hu-b2b__band hu-b2b__band--cream" id="vorgehen" data-nx-theme="light" data-track-section="offer-ladder" aria-labelledby="hu-b2b-path-title">
		<div class="hu-intercept__container">
			<div class="hu-b2b__section-head hu-b2b__section-head--centered">
				<p class="hu-intercept__eyebrow">Der Weg zur Umsetzung</p>
				<h2 class="hu-intercept__h2" id="hu-b2b-path-title">Erst Fit entscheiden. Dann System planen. Erst danach bauen.</h2>
				<p class="hu-intercept__section-lead">Die Reihenfolge schützt beide Seiten vor einer Umsetzung, deren wirtschaftliche oder operative Grundlage noch nicht trägt.</p>
			</div>
			<ol class="hu-b2b__path">
				<?php foreach ( $offer_steps as $step ) : ?>
					<li><span><?php echo esc_html( $step['n'] ); ?></span><h3><?php echo esc_html( $step['t'] ); ?></h3><p><?php echo esc_html( $step['s'] ); ?></p></li>
				<?php endforeach; ?>
			</ol>
		</div>
	</section>

	<section class="hu-b2b__band hu-b2b__band--deep" id="faq" data-nx-theme="dark" data-track-section="faq" aria-labelledby="hu-b2b-faq-title">
		<div class="hu-intercept__container hu-b2b__faq-layout">
			<div class="hu-b2b__faq-intro">
				<p class="hu-intercept__eyebrow">Vor der Entscheidung</p>
				<h2 class="hu-intercept__h2" id="hu-b2b-faq-title">Häufige Fragen zu PV-Terminen im B2B.</h2>
				<p class="hu-intercept__section-lead">Kosten, Fit, Zeitraum und Grenzen — damit der Marktcheck keine Fragen erzeugt, die vorher hätten geklärt werden können.</p>
			</div>
			<div class="hu-intercept__faq-list">
				<?php foreach ( $faq as $item ) : ?>
					<details class="hu-intercept__faq-item" name="hu-b2b-faq">
						<summary class="hu-intercept__faq-q"><?php echo esc_html( $item['question'] ); ?></summary>
						<p class="hu-intercept__faq-a"><?php echo wp_kses_post( $item['answer'] ); ?></p>
					</details>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="hu-b2b__band hu-b2b__final" id="final-cta" data-nx-theme="dark" data-track-section="final" aria-labelledby="hu-b2b-final-title">
		<div class="hu-intercept__container hu-b2b__final-inner">
			<div>
				<p class="hu-intercept__eyebrow">Nächster sinnvoller Schritt</p>
				<h2 class="hu-intercept__h2" id="hu-b2b-final-title">Prüfen Sie zuerst, ob ein eigener Gewerbe-PV-Anfrageweg wirtschaftlich trägt.</h2>
			</div>
			<div class="hu-b2b__final-action">
				<p>Der Marktcheck ordnet Region, Projektschwelle, Vertriebskapazität und heutige Anfragequellen ein — mit klarer Empfehlung für oder gegen die nächste Analyse.</p>
				<a class="hu-intercept__cta-primary"
				   href="<?php echo esc_url( $marktcheck_url ); ?>"
				   data-track-action="cta_marktcheck"
				   data-track-category="b2b_solar_leads"
				   data-track-section="final">
					Marktcheck mit Fit-Entscheid starten <span aria-hidden="true">→</span>
				</a>
				<p class="hu-b2b__microcopy">Keine Zahlungsdaten · kein Pflicht-Call · händische Einordnung</p>
				<a class="hu-b2b__text-link"
				   href="<?php echo esc_url( $solar_money_url ); ?>"
				   data-track-action="cta_money_page"
				   data-track-category="b2b_solar_leads"
				   data-track-section="final">Privat- und Gewerbegeschäft kombiniert? Branchen-Seite ansehen <span aria-hidden="true">↗</span></a>
			</div>
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
		'track_category' => 'b2b_solar_leads',
	]
);

get_footer();
