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
$e3_cpl_reduction    = $e3_metrics['cpl_reduction']['display'] ?? 'über 85 %';
$e3_lead_count       = $e3_metrics['lead_count']['display'] ?? '1.750+';
$e3_sales_conversion = $e3_metrics['sales_conversion']['display'] ?? '12 %';
$e3_timeframe        = $e3_metrics['timeframe']['display'] ?? '6 Monate';

// ── Inhalte ───────────────────────────────────────────────────
// Bewusst als Orientierungsgrößen ausgewiesen, nicht als Marktstudie:
// die 50.000 € sind das eigene Fit-Kriterium (identisch zur Money-Page,
// "B2B ab ca. 50 k € pro Projekt"), Zyklus und Entscheiderzahl beschreiben
// den Beschaffungsprozess des Lesers. Die vierte Karte argumentiert über die
// nachprüfbare Bauweise der Portal-Formulare statt über eine Prozentzahl,
// die sich nicht belegen lässt.
$b2b_facts = [
	[ 'k' => 'ab 50.000 €', 'l' => 'Projektwert, ab dem sich ein eigenes Anfrage-System gegenüber Lead-Einkauf rechnet' ],
	[ 'k' => '3 – 6 Monate', 'l' => 'typische Dauer von der Erstanfrage bis zur Freigabe, wenn ein Buying-Center entscheidet' ],
	[ 'k' => '3 – 6', 'l' => 'Entscheider und Einflussnehmer, die in der Regel an einer gewerblichen Anfrage beteiligt sind' ],
	[ 'k' => 'Pro Termin', 'l' => 'rechnen Terminierungs-Dienstleister ab — unabhängig davon, ob Anschlussleistung, Budget und Entscheider am Tisch überhaupt passen' ],
];

// Feindbild ist bewusst der Terminierungs-Dienstleister, nicht das
// B2C-Lead-Portal: Ein Betrieb mit Hallendach-Projekten kauft bei Aroundhome
// oder DAA ohnehin nicht ein. Was im Gewerbe wirklich eingekauft wird, sind
// gelegte Vertriebstermine — dagegen argumentiert diese Seite.
$why_b2c_funnel_fails = [
	[
		't' => 'Qualifizierung kommt zu spät',
		's' => 'Ein gekaufter Termin ist ein Kalendereintrag. Ob Anschlussleistung, Dachstatik und Investitionsabsicht zusammenpassen, klärt sich erst, wenn Ihr Vertrieb schon angereist ist – und die Stunde ist bezahlt, egal wie sie ausgeht.',
	],
	[
		't' => 'Buying-Center wird ignoriert',
		's' => 'Im Gewerbe entscheiden Geschäftsführung, CFO, Energie-Manager und Technik gemeinsam. Ein Termin mit genau einer Person führt zu einer zweiten Runde, die niemand eingeplant hat.',
	],
	[
		't' => 'Förder- und Finanzierungslogik fehlt',
		's' => 'KfW-Programme, IAB, Sonderabschreibung, PPA-Modelle, Investitions-Contracting – das gehört in die Vorqualifizierung, nicht in ein Gespräch, in dem beide Seiten bei null anfangen.',
	],
	[
		't' => 'Fremde Kriterien',
		's' => 'Ein Callcenter entscheidet, was ein „guter" Gewerbe-Termin ist – nach eigener Erfolgsquote, nicht nach Ihrer Projektschwelle. Was durchfällt und was durchgeht, bestimmen Sie nicht.',
	],
];

$gewerbe_layers = [
	[
		't' => 'Money Page für Gewerbe-PV',
		's' => 'Hallendächer, Quartiere, PPA, Industrieanlagen – jeweils mit eigener Story, eigenen Referenzen und eigener Vorqualifizierung. Keine B2C-Maske mit „auch für Gewerbe".',
	],
	[
		't' => 'Buying-Center-taugliche Anfrage-Strecke',
		's' => 'Mehrstufiger Funnel: Erstanfrage von Energie-Manager, Vertiefung mit CFO/Geschäftsführung, technischer Termin. Status pro Stakeholder im CRM.',
	],
	[
		't' => 'Anfrage-Qualifizierung nach Projektwert',
		's' => 'Dachfläche, Anschlussleistung, geplanter Eigenverbrauch, vorhandene Trafostation, EEG-Status – Anfragen werden grün/gelb/rot sortiert, bevor der Vertrieb anruft. Welche Merkmale dabei zählen, steht im Detail unter <a href="' . esc_url( $qualified_url ) . '">qualifizierte PV-Anfragen</a>.',
	],
	[
		't' => 'CRM-Anschluss mit Stakeholder-Mapping',
		's' => 'HubSpot, Pipedrive oder Nexus-CRM bekommt das komplette Buying-Center inklusive Funktion, Status und Verantwortlichkeit. Keine isolierte Einzelanfrage.',
	],
	[
		't' => 'Tracking auf Auftragswert, nicht auf Klick',
		's' => '<a href="' . esc_url( $tracking_url ) . '">Server-Side Tracking</a> misst, welcher Kanal Anfragen ab 50.000 € Projektwert produziert – nicht nur, welcher Kanal billige Klicks liefert.',
	],
];

// Zeilenmodell für die Vergleichsmatrix (.hu-intercept__matrix): je Kriterium
// eine Zeile, damit gekaufter und eigener Termin direkt nebeneinander stehen
// statt in zwei gestapelten Panels.
$pv_termine_rows = [
	[
		'criterion' => 'Qualifizierung',
		'bought'    => 'Erst im Termin. Ob Anschlussleistung, Budget und Entscheiderrolle stimmen, zeigt sich, wenn Ihr Vertrieb schon im Raum sitzt.',
		'own'       => 'Vor dem Termin. Dachfläche, Anschlussleistung und Buying-Center laufen durch die Anfragestrecke, bevor jemand einen Kalender öffnet.',
	],
	[
		'criterion' => 'Bezahlt wird',
		'bought'    => 'Der Kalendereintrag. No-Shows und unpassende Gewerbe-Projekte gehen zu Ihren Lasten.',
		'own'       => 'Die qualifizierte Anfrage — einmalig aufgebaut, danach ohne Stückpreis je Termin.',
	],
	[
		'criterion' => 'Kriterien setzt',
		'bought'    => 'Ein fremdes Callcenter, nach eigenen Vorgaben statt nach Ihrer Buying-Center-Logik.',
		'own'       => 'Ihr CRM und Ihre Schwellen bestimmen, welcher Gewerbe-Termin überhaupt in den Vertrieb geht.',
	],
	[
		'criterion' => 'Gemessen wird',
		'bought'    => 'Preis pro Termin. Über den Projektwert dahinter sagt die Zahl nichts.',
		'own'       => 'Projektwert je Anfrage — die Größe, an der Ihr Vertrieb ohnehin gemessen wird.',
	],
];

$fit_yes = [
	[ 't' => 'Gewerbliche PV-Anbieter', 's' => 'Mit Fokus auf Hallendächer, Quartiere, Industrieanlagen oder PPA-Modelle.' ],
	[ 't' => 'Speicher-Lösungsanbieter', 's' => 'Großspeicher für Gewerbe, Eigenverbrauchsoptimierung, Spitzenlastmanagement.' ],
	[ 't' => 'EPC-Unternehmen', 's' => 'Engineering, Procurement & Construction für gewerbliche Energieprojekte.' ],
	[ 't' => 'Energie-Contractoren', 's' => 'Anbieter mit PPA-, Mietmodell- oder Investitions-Contracting-Strecken.' ],
];

$fit_no = [
	[ 't' => 'Reine B2C-Solarteure', 's' => 'Wer Privathäuser bestückt, braucht eine andere Funnel-Logik – die steht unter <a href="' . esc_url( $solar_money_url ) . '">Leadgenerierung für Photovoltaik und Wärmepumpe</a>.' ],
	[ 't' => 'Eintägige Mengen-Verkäufer', 's' => '„100 Anfragen morgen" ist im Gewerbe weder realistisch noch profitabel.' ],
	[ 't' => 'Reine Vermittler', 's' => 'Wer Anfragen nur weiterverkauft, braucht kein eigenes B2B-System.' ],
];

$faq = [
	[
		'question' => 'Was ist von gekauften PV-Terminen für den B2B-Vertrieb zu halten?',
		'answer'   => 'Termin-Anbieter verkaufen fertig gelegte Vertriebstermine statt Datensätze – klingt bequem, verlagert aber die Qualifizierung nach hinten: Ob Anschlussleistung, Entscheiderrolle und Investitionsabsicht stimmen, zeigt sich erst im Gespräch. Für Gewerbe-PV mit Buying-Center-Logik gilt dieselbe Rechnung wie beim Lead-Kauf: Kosten pro Auftrag zählen, nicht Kosten pro Termin. Ein eigenes System qualifiziert vor dem Termin – nicht danach.',
	],
	[
		'question' => 'Woran erkenne ich einen wertlosen Gewerbe-PV-Termin?',
		'answer'   => 'An vier Dingen, die vorher hätten geklärt sein müssen: Am Tisch sitzt niemand mit Freigabekompetenz. Die Anschlussleistung passt nicht zur eigenen Projektgröße. Es gibt kein Investitionsbudget, sondern eine Interessensbekundung. Und das Dach ist statisch oder rechtlich gar nicht bespielbar. Jeder dieser Punkte lässt sich vor dem Termin abfragen — wenn die Anfragestrecke danach fragt.',
	],
	[
		'question' => 'Was macht einen PV-Termin im Gewerbe überhaupt qualifiziert?',
		'answer'   => 'Dass die Entscheidung im Termin auch fallen könnte. Konkret heißt das: Projektwert oberhalb Ihrer Schwelle, Dachfläche und Anschlussleistung bekannt, EEG- und Netzanschluss-Status geklärt, Investitionsabsicht mit Zeithorizont, und mindestens ein Teilnehmer mit Budgetverantwortung. Ein Termin, der nur eines dieser Kriterien offen lässt, ist ein Informationsgespräch — das kann sinnvoll sein, sollte aber nicht als Vertriebstermin bezahlt werden.',
	],
	[
		'question' => 'Terminierung einkaufen oder selbst aufbauen — wann lohnt sich was?',
		'answer'   => 'Einkaufen lohnt sich, wenn Sie kurzfristig Vertriebskapazität auslasten wollen und die Streuverluste einkalkuliert sind. Selbst aufbauen lohnt sich, wenn Ihre Projekte groß genug sind, dass ein einzelner Fehltermin teurer ist als die Vorqualifizierung — und wenn Sie über zwölf Monate hinaus planen. Der Unterschied ist nicht Qualität gegen Menge, sondern wer die Kriterien setzt. Was in Ihrem Fall trägt, klärt der Marktcheck.',
	],
	[
		'question' => 'Warum reicht eine B2C-Solar-Seite nicht für Gewerbe-PV?',
		'answer'   => 'B2C- und B2B-PV haben unterschiedliche Sprache, unterschiedliche Entscheider und unterschiedliche Vertragsstrukturen. Ein gewerblicher Einkäufer mit 800 kWp Anschlussleistung und PPA-Bedarf erkennt sich in einer Hausbesitzer-Seite mit „CO₂-Fußabdruck" nicht wieder. Die Anfragequalität fällt drastisch.',
	],
	[
		'question' => 'Wie viele Buying-Center-Stakeholder werden im Funnel abgebildet?',
		'answer'   => 'Typischerweise drei bis sechs: Energie-Manager (technische Erstprüfung), Geschäftsführung (strategische Freigabe), CFO (Finanzierung), Einkauf (Vertragsbedingungen), ggf. Facility Management und Nachhaltigkeitsbeauftragte. Jeder Stakeholder bekommt seinen eigenen Funnel-Schritt – nicht alles wird auf eine Person abgewälzt.',
	],
	[
		'question' => 'Funktioniert die Case-Study-Referenz auch im B2B-Gewerbe?',
		'answer'   => sprintf( 'Die Case-Study-Referenz (%1$s qualifizierte Anfragen, %2$s Abschlussquote, %3$s niedrigere Cost per Lead in %4$s) deckt B2C-Wärmepumpen und B2C/B2B-Photovoltaik ab. Im reinen Gewerbe-PV sind die Ticketgrößen größer, die Abschlussquoten ähnlich, die Sales-Zyklen länger – die System-Logik bleibt identisch.', $e3_lead_count, $e3_sales_conversion, $e3_cpl_reduction, $e3_timeframe ),
	],
	[
		'question' => 'Wie passt das mit DAA, Aroundhome oder Check24 zusammen?',
		'answer'   => sprintf( 'Gar nicht — und das ist hier der Punkt. Diese Portale sind auf Privathaushalte gebaut. Ein Betrieb mit Hallendach- oder Quartiersprojekten kauft dort ohnehin nicht ein, deshalb argumentiert diese Seite auch nicht gegen Portale, sondern gegen eingekaufte Vertriebstermine. Wenn Sie den Portal-Vergleich für das Privatkundengeschäft suchen, steht er unter %s.', '<a href="' . esc_url( $portal_alternative_url ) . '">Photovoltaik- und Solar-Leads kaufen: die Alternative</a>' ),
	],
	[
		'question' => 'Wie lange dauert der Aufbau eines B2B-Solar-Systems?',
		'answer'   => 'Für ein vollständiges System mit Money Page, Vorqualifizierung, Buying-Center-Funnel, Tracking und CRM-Anbindung sind 6–10 Wochen Aufbauzeit realistisch. Die ersten qualifizierten Anfragen kommen typischerweise innerhalb der ersten 4–8 Wochen nach Live-Gang.',
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

<main id="primary" class="hu-intercept" role="main" data-track-page="b2b-solar-leads">

	<section class="hu-intercept__hero" id="hero" aria-labelledby="hu-b2b-hero-title">
		<div class="hu-intercept__container">
			<?php
			// Scope-Abgrenzung im Eyebrow: die Seite zieht laut GSC auch
			// Grosshandels-Queries ("b2b-handel pv", "b2b solar panels").
			// Der Zusatz sortiert diesen Intent sichtbar aus.
			?>
			<p class="hu-intercept__eyebrow">Gewerbliche Photovoltaik · Speicher · PPA — kein Modul-Großhandel</p>
			<?php
			// H1 nimmt den Meta-Title auf ("Photovoltaik B2B Leads & PV-Termine"),
			// damit der Einstieg das SERP-Versprechen einlöst. "B2B Solar Leads"
			// bleibt vorn — das ist das rankende Asset.
			?>
			<h1 class="hu-intercept__title" id="hu-b2b-hero-title">
				PV-Termine im B2B: eigene Photovoltaik-Anfragen statt eingekaufter Termine
			</h1>
			<p class="hu-intercept__lead">
				Gekaufte Gewerbe-Termine verlagern die Qualifizierung dorthin, wo sie am teuersten ist: in den Termin selbst. Gewerbliche Photovoltaik braucht eine Anfrage-Architektur, die <strong>Buying-Center</strong>, <strong>lange Sales-Zyklen</strong> und <strong>komplexe Förderlogik</strong> abbildet – und die qualifiziert, <em>bevor</em> Ihr Vertrieb anreist. Gebaut für Projekte <strong>ab 50.000 €</strong>.
			</p>
			<?php get_template_part( 'template-parts/seo-subpage-byline', null, [ 'template_path' => __FILE__ ] ); ?>
			<div class="hu-intercept__cta">
				<a class="hu-intercept__cta-primary"
				   href="<?php echo esc_url( $marktcheck_url ); ?>"
				   data-track-action="cta_marktcheck"
				   data-track-category="b2b_solar_leads"
				   data-track-section="hero">
					Marktcheck mit Fit-Entscheid starten
				</a>
				<a class="hu-intercept__cta-secondary"
				   href="<?php echo esc_url( $e3_url ); ?>"
				   data-track-action="cta_e3_case"
				   data-track-category="b2b_solar_leads"
				   data-track-section="hero">
					Case Study lesen (<?php echo esc_html( $e3_lead_count ); ?> Anfragen, <?php echo esc_html( $e3_sales_conversion ); ?> Abschlussquote)
				</a>
			</div>

			<?php
			// Proof früh sichtbar: Zahlen lagen bisher nur in einer
			// Button-Beschriftung und einer FAQ-Antwort. Quelle ist der
			// anonymisierte Canon (hu_e3_canon) — kein Kundenname.
			?>
			<dl class="hu-b2b-proof" aria-label="Belegte Ergebnisse aus der dokumentierten Fallstudie">
				<div class="hu-b2b-proof__item">
					<dt class="hu-b2b-proof__num"><?php echo esc_html( $e3_cpl_reduction ); ?></dt>
					<dd class="hu-b2b-proof__lbl">weniger Kosten pro Anfrage</dd>
				</div>
				<div class="hu-b2b-proof__item">
					<dt class="hu-b2b-proof__num"><?php echo esc_html( $e3_lead_count ); ?></dt>
					<dd class="hu-b2b-proof__lbl">qualifizierte Anfragen</dd>
				</div>
				<div class="hu-b2b-proof__item">
					<dt class="hu-b2b-proof__num"><?php echo esc_html( $e3_sales_conversion ); ?></dt>
					<dd class="hu-b2b-proof__lbl">Abschluss · Anfrage → Vertrag</dd>
				</div>
				<div class="hu-b2b-proof__item">
					<dt class="hu-b2b-proof__num"><?php echo esc_html( $e3_timeframe ); ?></dt>
					<dd class="hu-b2b-proof__lbl">dokumentierter Zeitraum</dd>
				</div>
			</dl>
			<p class="hu-b2b-proof__note">
				Ein realer Fall über <?php echo esc_html( $e3_timeframe ); ?>, eigener Anfrageweg statt eingekaufter Nachfrage — dokumentiert,
				keine pauschale Übertragbarkeitsgarantie.
			</p>
		</div>
	</section>

	<section class="hu-intercept__compare" id="fakten" aria-labelledby="hu-b2b-facts-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-b2b-facts-title">Warum gewerbliches PV-Lead-Geschäft anders funktioniert</h2>
			<p class="hu-intercept__section-lead">
				Vier Größenordnungen, an denen diese Architektur ausgerichtet ist — Erfahrungswerte
				aus Gewerbeprojekten und die eigene Fit-Schwelle, keine Marktstudie.
			</p>
			<div class="hu-intercept__grid hu-intercept__grid--four">
				<?php foreach ( $b2b_facts as $fact ) : ?>
					<article class="hu-intercept__card">
						<h3 class="hu-intercept__card-title"><?php echo esc_html( $fact['k'] ); ?></h3>
						<p class="hu-intercept__card-text"><?php echo esc_html( $fact['l'] ); ?></p>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<?php
	// PV-Termine steht bewusst direkt hinter dem Einstieg: der Meta-Title
	// verspricht "PV-Termine", die Seite muss das oben einlösen und nicht
	// erst nach zwei Kartenrastern. Anker #pv-termine bleibt erhalten.
	// Darstellung als Vergleichsmatrix (.hu-intercept__matrix) — bereits im
	// gemeinsamen Stylesheet vorhanden, bisher nur auf der TCO-Seite genutzt.
	?>
	<section class="hu-intercept__compare" id="pv-termine" aria-labelledby="hu-b2b-termine-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-b2b-termine-title">PV-Termine im B2B: gekauft oder selbst erzeugt?</h2>
			<p class="hu-intercept__section-lead">
				„PV-Termine B2B" klingt nach Abkürzung zum Abschluss. Im Gewerbe mit Buying-Center und langen Sales-Zyklen entscheidet aber nicht der Termin, sondern die Qualifizierung dahinter – sonst sitzen teure Vertriebstermine ohne Projektsubstanz im Kalender. Wie sich das auf die <a href="<?php echo esc_url( $cpl_url ); ?>">Kosten pro Anfrage</a> auswirkt, lässt sich durchrechnen.
			</p>
			<div class="hu-intercept__matrix" role="table" aria-label="Vergleich gekaufte und eigene PV-Termine im B2B">
				<div class="hu-intercept__matrix-head" role="row">
					<span role="columnheader">Kriterium</span>
					<span role="columnheader">Gekaufter PV-Termin</span>
					<span role="columnheader">Eigener PV-Termin</span>
				</div>
				<?php foreach ( $pv_termine_rows as $row ) : ?>
					<div class="hu-intercept__matrix-row" role="row">
						<span class="hu-intercept__matrix-criterion" role="cell"><?php echo esc_html( $row['criterion'] ); ?></span>
						<span class="hu-intercept__matrix-rent" role="cell"><?php echo esc_html( $row['bought'] ); ?></span>
						<span class="hu-intercept__matrix-own" role="cell"><?php echo esc_html( $row['own'] ); ?></span>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<?php // Anker umbenannt (vorher #warum-b2c-funnel): keine interne oder externe Referenz darauf, geprueft. ?>
	<section class="hu-intercept__why" id="warum-gekaufte-termine" aria-labelledby="hu-b2b-why-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-b2b-why-title">Warum eingekaufte Gewerbe-Termine scheitern</h2>
			<div class="hu-intercept__grid hu-intercept__grid--four">
				<?php foreach ( $why_b2c_funnel_fails as $item ) : ?>
					<article class="hu-intercept__card">
						<h3 class="hu-intercept__card-title"><?php echo esc_html( $item['t'] ); ?></h3>
						<p class="hu-intercept__card-text"><?php echo esc_html( $item['s'] ); ?></p>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="hu-intercept__system" id="architektur" aria-labelledby="hu-b2b-arch-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-b2b-arch-title">Die Architektur für gewerbliche PV-Anfragen</h2>
			<p class="hu-intercept__section-lead">
				Fünf Schichten, die das B2B-Gewerbe-Geschäft tragen – von der Sprache der Money Page bis zum Stakeholder-Mapping im CRM.
			</p>
			<ol class="hu-intercept__layers">
				<?php foreach ( $gewerbe_layers as $i => $layer ) : ?>
					<li class="hu-intercept__layer">
						<span class="hu-intercept__layer-index"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
						<div class="hu-intercept__layer-body">
							<h3 class="hu-intercept__layer-title"><?php echo esc_html( $layer['t'] ); ?></h3>
							<p class="hu-intercept__layer-text"><?php echo wp_kses_post( $layer['s'] ); ?></p>
						</div>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
	</section>

	<section class="hu-intercept__compare" id="fit" aria-labelledby="hu-b2b-fit-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-b2b-fit-title">Für wen das B2B-Solar-System passt</h2>
			<div class="hu-intercept__grid hu-intercept__grid--two">
				<div class="hu-intercept__panel hu-intercept__panel--positive">
					<h3 class="hu-intercept__panel-title">Passt</h3>
					<ul class="hu-intercept__facts">
						<?php foreach ( $fit_yes as $f ) : ?>
							<li>
								<span class="hu-intercept__fact-key"><?php echo esc_html( $f['t'] ); ?></span>
								<span class="hu-intercept__fact-label"><?php echo esc_html( $f['s'] ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
				<div class="hu-intercept__panel hu-intercept__panel--negative">
					<h3 class="hu-intercept__panel-title">Passt nicht</h3>
					<ul class="hu-intercept__facts">
						<?php foreach ( $fit_no as $f ) : ?>
							<li>
								<span class="hu-intercept__fact-key"><?php echo esc_html( $f['t'] ); ?></span>
								<span class="hu-intercept__fact-label"><?php echo wp_kses_post( $f['s'] ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
		</div>
	</section>

	<section class="hu-intercept__faq" id="faq" aria-labelledby="hu-b2b-faq-title">
		<div class="hu-intercept__container">
			<h2 class="hu-intercept__h2" id="hu-b2b-faq-title">Häufige Fragen zur gewerblichen Solar-Leadgenerierung</h2>
			<div class="hu-intercept__faq-list">
				<?php foreach ( $faq as $item ) : ?>
					<details class="hu-intercept__faq-item">
						<summary class="hu-intercept__faq-q"><?php echo esc_html( $item['question'] ); ?></summary>
						<?php // wp_kses_post: eine Antwort traegt einen kontextuellen Link (Intent-Trennung). ?>
						<p class="hu-intercept__faq-a"><?php echo wp_kses_post( $item['answer'] ); ?></p>
					</details>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="hu-intercept__final" id="final-cta" aria-labelledby="hu-b2b-final-title">
		<div class="hu-intercept__container hu-intercept__container--centered">
			<h2 class="hu-intercept__h2" id="hu-b2b-final-title">Gewerbe-PV-Strecke einordnen</h2>
			<p class="hu-intercept__final-text">
				Im Marktcheck zeigt sich, ob Ihre heutige Anfrage-Architektur Gewerbe-Buying-Center tragen kann – oder ob sie als B2C-Maske an gewerblichen Anfragen vorbei rennt.
			</p>
			<div class="hu-intercept__cta">
				<a class="hu-intercept__cta-primary"
				   href="<?php echo esc_url( $marktcheck_url ); ?>"
				   data-track-action="cta_marktcheck"
				   data-track-category="b2b_solar_leads"
				   data-track-section="final">
					Marktcheck mit Fit-Entscheid starten
				</a>
				<a class="hu-intercept__cta-secondary"
				   href="<?php echo esc_url( $solar_money_url ); ?>"
				   data-track-action="cta_money_page"
				   data-track-category="b2b_solar_leads"
				   data-track-section="final">
					Leadgenerierung für Photovoltaik &amp; Wärmepumpe ansehen
				</a>
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
