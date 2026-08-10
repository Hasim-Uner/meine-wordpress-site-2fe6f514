<?php
/**
 * Template Name: Solar Case Study Methodik-Case
 * Description: Methodik-Case fuer /case-study-solar-leadgenerierung/.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$diagnostic_url = function_exists( 'hu_get_request_analysis_url' ) ? hu_get_request_analysis_url() : home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' );
$agentur_url    = function_exists( 'nexus_get_primary_public_url' ) ? nexus_get_primary_public_url( 'agentur', home_url( '/wordpress-agentur-hannover/' ) ) : home_url( '/wordpress-agentur-hannover/' );

$tracking_attrs = 'data-track-section="case_solar_methodology" data-track-funnel-stage="proof"';

$e3 = static function ( $metric, $field = 'display', $fallback = '' ) {
	return function_exists( 'hu_e3_metric' ) ? hu_e3_metric( $metric, $field, $fallback ) : $fallback;
};

$e3_canon      = function_exists( 'hu_e3_canon' ) ? hu_e3_canon() : [];
$e3_case_label = isset( $e3_canon['case_label'] ) ? (string) $e3_canon['case_label'] : 'mittelständischer PV-Installationsbetrieb';

$e3_cpl_before   = $e3( 'cpl_before', 'display', '150 €' );
$e3_cpl_after    = $e3( 'cpl_after', 'display', '22 €' );
$e3_cpl_ramp     = $e3( 'cpl_ramp', 'display', '70 – 100 €' );
$e3_lead_count   = $e3( 'lead_count', 'display', '1.750+' );
$e3_conv_before  = $e3( 'sales_conversion_before', 'display', '1 – 5 %' );
$e3_conv_after   = $e3( 'sales_conversion_after', 'display', '12 %' );
$e3_conv_uplift  = $e3( 'sales_conversion_uplift', 'display', '1 – 5 % → 12 %' );
$e3_timeframe    = $e3( 'timeframe', 'display', '6 Monate' );
$e3_timeframe_dt = $e3( 'timeframe', 'display_dative', '6 Monaten' );
$e3_portal_avg   = $e3( 'portal_conversion_avg', 'display', '3 %' );
$e3_portal_deal  = $e3( 'portal_cost_per_deal', 'display', '5.000 €' );
$e3_build        = $e3( 'build_months', 'display', '3 Monate' );
$e3_tuning       = $e3( 'tuning_months', 'display', '3 Monate' );

$cta_label = 'Marktcheck mit Fit-Entscheid starten';

/*
 * Kennzahlen tragen jetzt Wert UND Bezeichnung. Die Karte hatte im CSS immer
 * ein span fuer das Label, das Template hat es nie gerendert — deshalb standen
 * 148px hohe Karten mit einer Zeile und viel Leerraum darunter.
 */
$metrics = [
	[
		'value' => sprintf( '%s → %s', $e3_cpl_before, $e3_cpl_after ),
		'label' => 'Kosten pro Anfrage',
	],
	[
		'value' => $e3_lead_count,
		'label' => sprintf( 'qualifizierte Anfragen in %s', $e3_timeframe_dt ),
	],
	[
		'value' => $e3_conv_uplift,
		'label' => 'Abschlussquote auf Auftrag',
	],
	[
		'value' => $e3_timeframe,
		'label' => sprintf( '%s Aufbau, %s Optimierung', $e3_build, $e3_tuning ),
	],
];

$summary_rows = [
	[
		'label' => 'Ausgangslage',
		'body'  => sprintf( 'Zwei Anfrage-Quellen parallel: Portal-Leads zu %s pro Stück mit %s Abschluss — und kostenlose Hersteller-Anfragen, die deutlich besser abschlossen. Gleicher Vertrieb, gleiche Produkte.', $e3_cpl_before, $e3_conv_before ),
	],
	[
		'label' => 'Diagnose',
		'body'  => 'Nicht der Vertrieb war die Variable, sondern die Anfrage. Vier Eigenschaften trennten die beiden Quellen: Kaufabsicht, Exklusivität, Vorqualifizierung und Reaktionszeit.',
	],
	[
		'label' => 'Eingriff',
		'body'  => 'Alle vier Eigenschaften wurden in ein eigenes System übersetzt — Such-Intent statt Streuung, exklusive Anfragen, Multi-Step-Vorqualifizierung, CRM-Übergabe in Sekunden. Dazu ein Server-Side-Tracking, das die Attribution überhaupt erst belastbar machte.',
	],
	[
		'label' => 'Ergebnis',
		'body'  => sprintf( 'Kosten pro Anfrage von %s auf %s, Abschlussquote von %s auf %s, %s qualifizierte Anfragen in %s.', $e3_cpl_before, $e3_cpl_after, $e3_conv_before, $e3_conv_after, $e3_lead_count, $e3_timeframe_dt ),
	],
];

$hypotheses = [
	[
		'number' => '1.',
		'title'  => 'Kaufabsicht',
		'body'   => 'Wer auf Viessmann.de nach einem Fachbetrieb sucht, hat die Entscheidung "ich will eine neue Heizung" bereits getroffen. Wer ein Portal-Formular ausfüllt, will Preisvergleiche. Zwei verschiedene Kaufphasen — beides als "Lead" bezeichnet.',
	],
	[
		'number' => '2.',
		'title'  => 'Exklusivität',
		'body'   => 'Viessmann-Anfragen gehen exklusiv an einen Betrieb. Portal-Leads werden parallel an drei Anbieter verkauft. Wer als zweiter anruft, hat verloren.',
	],
	[
		'number' => '3.',
		'title'  => 'Vorqualifizierung',
		'body'   => 'Bei Viessmann hat der Interessent sich durch die Hersteller-Website navigiert, Produkte verglichen, einen Fachbetrieb gewählt. Bei Portalen reicht eine Postleitzahl plus ein Klick.',
	],
	[
		'number' => '4.',
		'title'  => 'Reaktionszeit',
		'body'   => 'Viessmann-Anfragen kommen in Minuten. Portal-Leads brauchen oft Stunden bis Tage — bis dahin ist die Aufmerksamkeit der Person längst woanders.',
	],
];

/*
 * Die Vergleichsmatrix ist der Kern des Falls: dieselben vier Eigenschaften,
 * drei Quellen nebeneinander. Bis 2026-08 stand diese Logik nur im Fliesstext
 * und war damit unsichtbar fuer jeden, der die Seite ueberfliegt.
 */
$comparison_rows = [
	[
		'property' => 'Kaufabsicht',
		'portal'   => 'Preisvergleich, Kaufphase offen',
		'maker'    => 'Entscheidung bereits getroffen',
		'own'      => 'Such-Intent plus kaufnahe Inhalte',
	],
	[
		'property' => 'Exklusivität',
		'portal'   => 'parallel an drei Anbieter verkauft',
		'maker'    => 'exklusiv an einen Betrieb',
		'own'      => 'exklusiv, eigene Datenbank',
	],
	[
		'property' => 'Vorqualifizierung',
		'portal'   => 'Postleitzahl plus ein Klick',
		'maker'    => 'Hersteller-Website durchlaufen',
		'own'      => 'Multi-Step mit Filterfragen und Lead-Score',
	],
	[
		'property' => 'Reaktionszeit',
		'portal'   => 'Stunden bis Tage',
		'maker'    => 'Minuten',
		'own'      => 'Minuten über CRM-Webhook',
	],
];

$implementation_blocks = [
	[
		'label' => 'Block A',
		'ref'   => 'zu Eigenschaft 1',
		'title' => 'Kaufabsicht nachbauen',
		'body'  => 'Statt breite Audiences auf Meta zu bewerben ("Hausbesitzer in Niedersachsen"), wurde der Ads-Stack neu strukturiert: Kampagnen auf Such-Intent (Google) und auf gezielte Lookalikes existierender Abschluss-Kunden (Meta). Parallel dazu Content-Seiten, die für hochintentionale Suchanfragen ranken — nicht für generische Awareness-Themen. Wer "Wärmepumpe Hannover Förderung 2025" sucht, ist näher am Kauf als wer "Solar lohnt sich" liest.',
	],
	[
		'label' => 'Block B',
		'ref'   => 'zu Eigenschaft 2',
		'title' => 'Exklusivität nachbauen',
		'body'  => 'Eigene Domain, eigenes Formular, eigene Datenbank. Jede Anfrage ging exklusiv an den Betrieb, nicht an drei andere Anbieter gleichzeitig. Zusätzlich: CRM-Webhook direkt aus dem Formular, damit der Erstkontakt in Minuten erfolgte, nicht in Stunden — der gleiche Geschwindigkeitsvorteil, den Viessmann-Anfragen strukturell hatten.',
	],
	[
		'label' => 'Block C',
		'ref'   => 'zu Eigenschaft 3',
		'title' => 'Vorqualifizierung nachbauen',
		'body'  => 'Multi-Step-Formular mit gezielten Filterfragen: Eigentümerstatus, Objekttyp, Zeitraum für die Umsetzung, monatliche Stromkosten, Telefon-Erreichbarkeit. Wer Mieter ohne Eigentümer-Abstimmung war, fiel raus. Wer "nur informieren" wollte, ging in einen separaten Funnel. Im CRM bekam jede Anfrage einen Lead-Score, damit der Vertrieb die A-Leads zuerst anrief.',
	],
	[
		'label' => 'Block D',
		'ref'   => 'zu Eigenschaft 4',
		'title' => 'Reaktionszeit und Datenfrische nachbauen',
		'body'  => 'Server-Side Tracking mit GA4 und Meta CAPI als Fundament — damit überhaupt sichtbar wurde, welche Kampagne welche Anfrage und welchen Abschluss erzeugte. Ohne saubere Attribution lässt sich nichts optimieren. Webhook-basierte CRM-Übergabe stellte sicher, dass Anfragen nicht in einem E-Mail-Postfach versanden, sondern in Sekunden im Vertriebssystem ankamen.',
	],
];

$timeline_phases = [
	[
		'title' => 'Monate 1–2 — Aufbau und Kreativ-Tests',
		'body'  => sprintf( 'Tracking-Infrastruktur, Landingpage-Architektur, Formular-Logik, CRM-Anbindung wurden parallel aufgesetzt. Erste Ads-Kampagnen liefen mit unterschiedlichen Creatives und Targetings. Kosten pro Anfrage in dieser Phase: %s — solide, aber noch nicht konkurrenzlos gegenüber dem Portal-Einkauf.', $e3_cpl_ramp ),
	],
	[
		'title' => 'Monat 3 — System läuft',
		'body'  => 'CRM-Anbindung produktiv, Lead-Score aktiv, Server-Side Tracking attribuiert sauber. Vertrieb arbeitet ausschließlich auf eigenen Anfragen, der Portal-Einkauf wird heruntergefahren.',
	],
	[
		'title' => 'Monate 4–6 — Optimierung',
		'body'  => sprintf( 'Auf Basis der nun belastbaren Daten wurden Audiences geschärft, Creatives iteriert, Formular-Felder gestrafft, Conversion-Pfade geprüft. Die Kosten pro Anfrage stabilisierten sich bei %s. Die Abschlussquote auf Auftrag erreichte %s — kein Hersteller-Niveau, aber deutlich über dem Korridor von %s bei Portal-Leads.', $e3_cpl_after, $e3_conv_after, $e3_conv_before ),
	],
];

/*
 * Drei belegte Plateaus, keine erfundene Monatskurve. Die Monate 3 bis 5 sind
 * nicht einzeln dokumentiert; wer sie zeichnet, erfindet Daten.
 */
$cpl_chart = [
	[
		'x'       => 150,
		'caption' => 'Vorher',
		'sub'     => 'Portal-Einkauf',
	],
	[
		'x'       => 380,
		'caption' => 'Monate 1–2',
		'sub'     => 'Aufbauphase',
	],
	[
		'x'       => 610,
		'caption' => 'Ab Monat 4',
		'sub'     => 'stabilisiert',
	],
];

$transferable = [
	'Die Vier-Eigenschaften-Logik (Kaufabsicht, Exklusivität, Vorqualifizierung, Reaktionszeit) gilt für jeden Photovoltaik- oder SHK-Betrieb mit eigenem Vertrieb.',
	'Tracking-Fundament ist Voraussetzung, kein optionales Add-on. Ohne sauberes Attribution-Setup wird Optimierung zum Ratespiel.',
	sprintf( '%s Implementierung sind realistisch — wer "in zwei Wochen Leads" verspricht, übersieht entweder das Tracking-Fundament oder die CRM-Integration oder beides.', $e3_build ),
	'Vertriebs-Schulung wird oft als erste Maßnahme verkauft. Bei diesem Betrieb war der Vertrieb nie das Problem. Das ist häufiger der Fall als die Branche zugibt.',
];

$specific = [
	sprintf( 'Konkrete Kosten pro Anfrage hängen von Region, Produktmix und Werbedruck der Mitbewerber ab. %s ist kein Versprechen, sondern der Endpunkt einer Strecke aus %s Implementierung und %s Optimierung.', $e3_cpl_after, $e3_build, $e3_tuning ),
	sprintf( '%s Abschlussquote auf Auftrag setzt einen funktionierenden Außendienst voraus. Ohne den nutzt das beste Anfragesystem nichts.', $e3_conv_after ),
	'Hersteller-Partner-Programme wie das von Viessmann sind eine wertvolle Zusatzquelle, ersetzen aber kein eigenes System — Partner-Kontingente sind begrenzt und nicht steuerbar.',
];

$fit_yes = [
	'Sie kaufen Anfragen über Portale zu und sehen die Abschlussquote schwanken, ohne den Grund benennen zu können.',
	'Ihr Vertrieb schließt bei manchen Quellen deutlich besser ab als bei anderen — bei gleicher Mannschaft.',
	'Sie haben eigenen Außendienst und tragen Projektwerte, bei denen eine Anfrage mehr wert ist als ihr Einkaufspreis.',
	'Sie wissen nicht sicher, welche Kampagne welchen Auftrag erzeugt hat.',
];

$fit_no = [
	'Sie suchen zusätzliche Anfragen für nächste Woche. Ein eigenes System braucht Monate, kein Portal-Ersatz auf Knopfdruck.',
	'Es gibt keinen Vertrieb, der Termine wahrnimmt. Dann ist die Anfrage nicht der Engpass.',
	'Die Anfragen sollen weiterverkauft oder auf mehrere Betriebe verteilt werden. Das ist genau das Modell, gegen das dieser Fall argumentiert.',
];

$cta_features = [
	'Marktcheck in drei Schritten — rund 60 Sekunden Eingabe',
	'Händisch geprüfter Befund innerhalb von 48 Stunden per E-Mail',
	'Einordnung von Anfrage-Quellen, Tracking und Vertriebsanschluss',
	'Kein Pflicht-Termin, kein Pitch-Call',
];

$tracking_repair_steps = [
	[
		'label' => 'Befund',
		'title' => 'Safari-ITP löschte First-Party-Cookies nach 24 Stunden.',
		'body'  => 'Im Ausgangszustand kam ein erheblicher Teil der Conversions nie in den Werbe-Konten an. Apples Intelligent Tracking Prevention beschneidet First-Party-Cookies, die per JavaScript gesetzt werden, auf 24 Stunden Lebensdauer. Wer am Donnerstag klickt und am Sonntag konvertiert, fehlt in der Attribution. Meta- und Google-Algorithmen optimierten dadurch gegen ein lückenhaftes Signal — sie lernten, falsche Klicker zu finden.',
	],
	[
		'label' => 'Eingriff',
		'title' => 'Server-Side-Setup auf eigener Subdomain.',
		'body'  => 'Statt Browser-Tags wurde ein dediziertes Server-Side-Tracking auf einer eigenen Subdomain aufgesetzt. Conversion-Events laufen über GA4 Measurement Protocol und Meta Conversions API direkt aus dem CRM in die Werbe-Konten — vorbei an ITP, vorbei an Adblockern, mit Consent Mode v2 als rechtlichem Rahmen.',
	],
	[
		'label' => 'Wirkung',
		'title' => 'Algorithmen sahen erstmals echte Käufer.',
		'body'  => 'Erst mit dieser First-Party-Attribution bekamen Meta und Google die belastbaren Kauf-Signale, die ihre Optimierung braucht. Ab Monat 3 konnten die Kampagnen erstmals auf echten Kaufsignalen lernen: mehr von den Mustern, die tatsächlich zu Abschlüssen führten, und weniger Klick-Touristen.',
	],
];

$insight_body = sprintf(
	'Höheres Werbebudget skaliert bei taubem Tracking nur den Verlust. Erst als der Vertrieb (CRM) und die Werbe-Algorithmen über First-Party-Attribution dieselbe Sprache sprachen, konnten die Werbekanäle kaufbereite Hausbesitzer von Klick-Touristen unterscheiden. Der Sturz von %s auf %s in %s lässt sich deshalb nicht einer einzelnen Optimierung zuschreiben: Belastbare Kaufsignale, geschärfte Audiences, iterierte Creatives und die neue Anfrage-Strecke wirkten zusammen.',
	$e3_cpl_before,
	$e3_cpl_after,
	$e3_timeframe_dt
);

$faq_items = function_exists( 'nexus_get_e3_case_faq_items' ) ? nexus_get_e3_case_faq_items() : [];

$e3_deeper = [
	[
		't'   => 'Cost per Lead Photovoltaik',
		's'   => sprintf( 'Wie die Senkung von %s auf %s konkret zustande kommt – drei Szenarien im Vergleich.', $e3_cpl_before, $e3_cpl_after ),
		'url' => home_url( '/cost-per-lead-photovoltaik/' ),
	],
	[
		't'   => 'Server-Side Tracking für B2B',
		's'   => 'Die Tracking-Architektur, die die Attribution sauber gehalten hat – GA4, Meta CAPI, eigener Server.',
		'url' => home_url( '/server-side-tracking-b2b/' ),
	],
	[
		't'   => 'Lead-Funnel Solar',
		's'   => 'Die fünf Funnel-Stufen vom Suchbegriff bis zum Auftrag – die Architektur hinter diesem Setup.',
		'url' => home_url( '/lead-funnel-solar/' ),
	],
	[
		't'   => 'TCO-Überschlag 24/36 Monate: Portal-Leads vs. eigenes System',
		's'   => 'Strategischer Vergleich mit CAPEX-vs-OPEX-Logik und 8-Kriterien-Matrix.',
		'url' => home_url( '/eigene-leadgenerierung-vs-portale/' ),
	],
];

get_header();
?>

<main id="main" class="site-main">
	<div class="energy-page-wrapper solar-page e3-methodology" <?php echo $tracking_attrs; // raw-ok phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<section class="e3-hero" id="hero" <?php echo $tracking_attrs; // raw-ok phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> aria-labelledby="e3-hero-title">
			<div class="e3-section__inner e3-hero__inner">
				<div class="e3-hero__content" data-reveal>
					<p class="e3-kicker">Methodik-Case · Photovoltaik &amp; Wärmepumpe</p>
					<h1 class="e3-hero__headline" id="e3-hero-title">Die kostenlosen Anfragen schlossen besser ab als die für <?php echo esc_html( $e3_cpl_before ); ?> gekauften. Was wir daraufhin für einen Photovoltaik-Betrieb gebaut haben.</h1>
					<p class="e3-hero__subline">Ein <?php echo esc_html( $e3_case_label ); ?> hatte zwei parallele Anfrage-Quellen: zugekaufte Portal-Leads und kostenlose Anfragen aus einem Hersteller-Partnerprogramm. Gleicher Vertrieb, gleiche Produkte, deutlich verschiedene Abschlussquote. Die Erklärung dieses Widerspruchs wurde zur Bauanleitung für ein eigenes Anfragesystem.</p>
				</div>

				<div class="e3-metric-grid" role="list" aria-label="Kennzahlen des Mandats">
					<?php foreach ( $metrics as $metric ) : ?>
						<div class="e3-metric-card" role="listitem" data-reveal>
							<strong><?php echo esc_html( $metric['value'] ); ?></strong>
							<span><?php echo esc_html( $metric['label'] ); ?></span>
						</div>
					<?php endforeach; ?>
				</div>

				<div class="e3-hero__foot" data-reveal>
					<a class="e3-btn" href="<?php echo esc_url( $diagnostic_url ); ?>" data-track-action="cta_case_study_hero_marktcheck" data-track-category="lead_gen" data-track-section="hero">
						<span><?php echo esc_html( $cta_label ); ?></span>
						<span class="e3-btn__arrow" aria-hidden="true">→</span>
					</a>
					<p class="e3-hero__micro">Sehen Sie dieselbe Asymmetrie zwischen Ihren Anfrage-Quellen? Der Marktcheck ordnet sie ein — händisch geprüft, Befund in 48 Stunden, ohne Pflicht-Termin.</p>
				</div>
			</div>
		</section>

		<section class="e3-section e3-section--paper-2 e3-section--summary" id="kurzfassung" <?php echo $tracking_attrs; // raw-ok phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> aria-labelledby="kurzfassung-title">
			<div class="e3-section__inner">
				<div class="e3-section__head" data-reveal>
					<p class="e3-kicker">Kurzfassung</p>
					<h2 class="e3-section__title" id="kurzfassung-title">Der Fall in vier Sätzen.</h2>
				</div>

				<dl class="e3-summary" data-reveal>
					<?php foreach ( $summary_rows as $row ) : ?>
						<div class="e3-summary__row">
							<dt><?php echo esc_html( $row['label'] ); ?></dt>
							<dd><?php echo esc_html( $row['body'] ); ?></dd>
						</div>
					<?php endforeach; ?>
				</dl>
			</div>
		</section>

		<section class="e3-section e3-section--paper" id="diagnose" <?php echo $tracking_attrs; // raw-ok phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> aria-labelledby="diagnose-title">
			<div class="e3-section__inner">
				<div class="e3-section__head" data-reveal>
					<p class="e3-kicker">Ausgangslage</p>
					<h2 class="e3-section__title" id="diagnose-title">Das Problem war nie der Vertrieb. Es war die Lead-Quelle.</h2>
				</div>

				<div class="e3-prose e3-prose--wide" data-reveal>
					<p>Der Betrieb kaufte 2024 Photovoltaik- und Wärmepumpen-Anfragen über Portale wie Aroundhome, Check24 und DAA — rund <?php echo esc_html( $e3_cpl_before ); ?> pro Anfrage. Die Abschlussquote schwankte je nach Monat im Korridor von <?php echo esc_html( $e3_conv_before ); ?>. Bei <?php echo esc_html( $e3_cpl_before ); ?> Einkauf und <?php echo esc_html( $e3_portal_avg ); ?> im Durchschnitt heißt das rechnerisch <?php echo esc_html( $e3_portal_deal ); ?> reine Anfrage-Kosten pro Abschluss, bevor irgendein Cent Marge entsteht.</p>
					<p>Parallel dazu erhielt der Betrieb als Premium-Partner von Viessmann Anfragen — kostenlos, direkt vom Interessenten ausgelöst, der aktiv nach einem Viessmann-Fachbetrieb in seiner Region gesucht hatte. Dieselbe Vertriebsmannschaft. Derselbe Außendienst. Dieselben Produkte. Diese Anfragen konvertierten deutlich besser.</p>
					<p>Damit war die übliche Erklärung — "der Vertrieb müsste schneller anrufen", "die Margen müssten höher sein", "das Telefonscript müsste besser werden" — sofort entkräftet. Gleicher Vertrieb, anderes Resultat. Der Unterschied lag nicht im Haus. Er lag in der Anfrage.</p>
				</div>

				<aside class="e3-pullquote" data-reveal>
					<p>Wenn dasselbe Vertriebsteam mit Anfrage A scheitert und mit Anfrage B abschließt, dann ist die Anfrage die Variable — nicht der Vertrieb.</p>
				</aside>
			</div>
		</section>

		<section class="e3-section e3-section--dark" id="hypothese" <?php echo $tracking_attrs; // raw-ok phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> aria-labelledby="hypothese-title">
			<div class="e3-section__inner">
				<div class="e3-section__head" data-reveal>
					<p class="e3-kicker">Arbeitshypothese</p>
					<h2 class="e3-section__title" id="hypothese-title">Vier Eigenschaften, die kostenlose Viessmann-Anfragen hatten — und Portal-Leads systematisch nicht.</h2>
				</div>

				<div class="e3-card-grid e3-card-grid--four">
					<?php foreach ( $hypotheses as $hypothesis ) : ?>
						<article class="e3-info-card" data-reveal>
							<div class="e3-info-card__number"><?php echo esc_html( $hypothesis['number'] ); ?></div>
							<h3 class="e3-info-card__title"><?php echo esc_html( $hypothesis['title'] ); ?></h3>
							<p class="e3-info-card__body"><?php echo esc_html( $hypothesis['body'] ); ?></p>
						</article>
					<?php endforeach; ?>
				</div>

				<div class="e3-matrix-wrap" data-reveal>
					<table class="e3-matrix">
						<caption>Dieselben vier Eigenschaften, drei Anfrage-Quellen im direkten Vergleich.</caption>
						<thead>
							<tr>
								<th scope="col">Eigenschaft</th>
								<th scope="col">Portal-Lead</th>
								<th scope="col">Hersteller-Anfrage</th>
								<th scope="col" class="e3-matrix__own">Eigenes System</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( $comparison_rows as $row ) : ?>
								<tr>
									<th scope="row"><?php echo esc_html( $row['property'] ); ?></th>
									<td><span class="e3-matrix__label">Portal-Lead</span><?php echo esc_html( $row['portal'] ); ?></td>
									<td><span class="e3-matrix__label">Hersteller-Anfrage</span><?php echo esc_html( $row['maker'] ); ?></td>
									<td class="e3-matrix__own"><span class="e3-matrix__label">Eigenes System</span><?php echo esc_html( $row['own'] ); ?></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>

				<p class="e3-coda" data-reveal>Die Hypothese war einfach: Wenn ein eigenes Anfragesystem diese vier Eigenschaften strukturell nachbaut, sollte die Abschlussquote sich der von Hersteller-Anfragen annähern. Das war das Ziel der nächsten <?php echo esc_html( $e3_timeframe ); ?>.</p>
			</div>
		</section>

		<section class="e3-section e3-section--paper-2" id="massnahmen" <?php echo $tracking_attrs; // raw-ok phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> aria-labelledby="massnahmen-title">
			<div class="e3-section__inner">
				<div class="e3-section__head" data-reveal>
					<p class="e3-kicker">Umsetzung</p>
					<h2 class="e3-section__title" id="massnahmen-title">Kein Tool installiert. Vier Eigenschaften strukturell rekonstruiert.</h2>
					<p class="e3-section__lede">Die Verlockung in dieser Situation ist, ein Werkzeug zu kaufen — eine Landingpage-Vorlage, ein CRM, einen "Lead-Magneten". Das war nicht der Weg. Jede der vier Eigenschaften wurde einzeln in ein eigenes System übersetzt.</p>
				</div>

				<div class="e3-implementation-list">
					<?php foreach ( $implementation_blocks as $block ) : ?>
						<article class="e3-implementation" data-reveal>
							<div class="e3-implementation__label">
								<?php echo esc_html( $block['label'] ); ?>
								<span class="e3-implementation__ref"><?php echo esc_html( $block['ref'] ); ?></span>
							</div>
							<div class="e3-implementation__body">
								<h3><?php echo esc_html( $block['title'] ); ?></h3>
								<p><?php echo esc_html( $block['body'] ); ?></p>
							</div>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</section>

		<section class="e3-section e3-section--paper-2" id="datenbereinigung" <?php echo $tracking_attrs; // raw-ok phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> aria-labelledby="datenbereinigung-title">
			<div class="e3-section__inner">
				<div class="e3-section__head" data-reveal>
					<p class="e3-kicker">Datenfundament · Monat 1–2</p>
					<h2 class="e3-section__title" id="datenbereinigung-title">Bereinigung der unsichtbaren Datenlecks.</h2>
					<p class="e3-section__lede">Bevor irgendeine Optimierung greifen konnte, musste die Attribution stimmen. Ohne diesen Schritt wäre der Sturz von <?php echo esc_html( $e3_cpl_before ); ?> auf <?php echo esc_html( $e3_cpl_after ); ?> nicht reproduzierbar.</p>
				</div>

				<div class="e3-implementation-list">
					<?php foreach ( $tracking_repair_steps as $step ) : ?>
						<article class="e3-implementation" data-reveal>
							<div class="e3-implementation__label"><?php echo esc_html( $step['label'] ); ?></div>
							<div class="e3-implementation__body">
								<h3><?php echo esc_html( $step['title'] ); ?></h3>
								<p><?php echo esc_html( $step['body'] ); ?></p>
							</div>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</section>

		<section class="e3-section e3-section--paper" id="verlauf" <?php echo $tracking_attrs; // raw-ok phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> aria-labelledby="verlauf-title">
			<div class="e3-section__inner">
				<div class="e3-section__head" data-reveal>
					<p class="e3-kicker">Verlauf</p>
					<h2 class="e3-section__title" id="verlauf-title"><?php echo esc_html( $e3_build ); ?> Implementierung. <?php echo esc_html( $e3_tuning ); ?> Optimierung.</h2>
					<p class="e3-section__lede">Drei belegte Zustände, keine geglättete Kurve. Für die Monate dazwischen liegen keine getrennten Werte vor — deshalb stehen sie hier auch nicht.</p>
				</div>

				<figure class="e3-chart" data-reveal>
					<svg class="e3-chart__svg" viewBox="0 0 760 320" role="img" aria-labelledby="cpl-chart-title cpl-chart-desc" preserveAspectRatio="xMidYMid meet">
						<title id="cpl-chart-title">Kosten pro Anfrage im Verlauf des Mandats</title>
						<desc id="cpl-chart-desc">Vorher, im Portal-Einkauf: <?php echo esc_html( $e3_cpl_before ); ?> pro Anfrage. In den Monaten 1 bis 2, der Aufbauphase: <?php echo esc_html( $e3_cpl_ramp ); ?>. Ab Monat 4, stabilisiert: <?php echo esc_html( $e3_cpl_after ); ?>.</desc>

						<line class="e3-chart__ref" x1="225" y1="40" x2="720" y2="40" stroke-dasharray="4 6" />
						<line class="e3-chart__axis" x1="40" y1="250" x2="720" y2="250" />

						<rect class="e3-chart__bar e3-chart__bar--before" x="75" y="40" width="150" height="210" />

						<rect class="e3-chart__bar e3-chart__bar--ramp" x="305" y="152" width="150" height="98" />
						<rect class="e3-chart__bar e3-chart__bar--range" x="305" y="110" width="150" height="42" />

						<rect class="e3-chart__bar e3-chart__bar--after" x="535" y="219" width="150" height="31" />

						<text class="e3-chart__value" x="150" y="28" text-anchor="middle"><?php echo esc_html( $e3_cpl_before ); ?></text>
						<text class="e3-chart__value" x="380" y="98" text-anchor="middle"><?php echo esc_html( $e3_cpl_ramp ); ?></text>
						<text class="e3-chart__value" x="610" y="207" text-anchor="middle"><?php echo esc_html( $e3_cpl_after ); ?></text>

						<?php foreach ( $cpl_chart as $chart_point ) : ?>
							<text class="e3-chart__caption" x="<?php echo esc_attr( (string) $chart_point['x'] ); ?>" y="274" text-anchor="middle"><?php echo esc_html( $chart_point['caption'] ); ?></text>
							<text class="e3-chart__sub" x="<?php echo esc_attr( (string) $chart_point['x'] ); ?>" y="294" text-anchor="middle"><?php echo esc_html( $chart_point['sub'] ); ?></text>
						<?php endforeach; ?>
					</svg>
					<figcaption>Kosten pro Anfrage. Die gestrichelte Linie markiert das Ausgangsniveau des Portal-Einkaufs.</figcaption>
				</figure>

				<div class="e3-timeline">
					<?php foreach ( $timeline_phases as $index => $phase ) : ?>
						<article class="e3-timeline__item" data-reveal>
							<div class="e3-timeline__index"><?php echo esc_html( str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></div>
							<div>
								<h3><?php echo esc_html( $phase['title'] ); ?></h3>
								<p><?php echo esc_html( $phase['body'] ); ?></p>
							</div>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</section>

		<aside class="e3-interstitial" <?php echo $tracking_attrs; // raw-ok phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> aria-label="Marktcheck starten">
			<div class="e3-section__inner e3-interstitial__inner" data-reveal>
				<p class="e3-interstitial__text">Bis hierhin ist das ein fremder Fall. Der Marktcheck macht daraus Ihren: Anfrage-Quellen, Tracking und Vertriebsanschluss, händisch eingeordnet.</p>
				<a class="e3-btn e3-btn--ghost" href="<?php echo esc_url( $diagnostic_url ); ?>" data-track-action="cta_case_study_mid_marktcheck" data-track-category="lead_gen" data-track-section="interstitial">
					<span><?php echo esc_html( $cta_label ); ?></span>
					<span class="e3-btn__arrow" aria-hidden="true">→</span>
				</a>
			</div>
		</aside>

		<section class="e3-section e3-section--dark" id="lessons" <?php echo $tracking_attrs; // raw-ok phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> aria-labelledby="lessons-title">
			<div class="e3-section__inner">
				<div class="e3-section__head" data-reveal>
					<p class="e3-kicker">Was generalisierbar ist</p>
					<h2 class="e3-section__title" id="lessons-title">Was sich auf andere Photovoltaik- und Wärmepumpen-Betriebe übertragen lässt — und was nicht.</h2>
				</div>

				<div class="e3-lessons">
					<div class="e3-list-panel" data-reveal>
						<h3>Übertragbar:</h3>
						<ul>
							<?php foreach ( $transferable as $item ) : ?>
								<li><?php echo esc_html( $item ); ?></li>
							<?php endforeach; ?>
						</ul>
					</div>

					<div class="e3-list-panel" data-reveal>
						<h3>Branchen- und situationsspezifisch:</h3>
						<ul>
							<?php foreach ( $specific as $item ) : ?>
								<li><?php echo esc_html( $item ); ?></li>
							<?php endforeach; ?>
						</ul>
					</div>
				</div>
			</div>
		</section>

		<section class="e3-section e3-section--paper" id="insight" <?php echo $tracking_attrs; // raw-ok phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> aria-labelledby="insight-title">
			<div class="e3-section__inner">
				<div class="e3-section__head" data-reveal>
					<p class="e3-kicker">Kernerkenntnis</p>
					<h2 class="e3-section__title" id="insight-title">Die fundamentale Erkenntnis aus <?php echo esc_html( $e3_timeframe_dt ); ?> Praxis.</h2>
				</div>

				<aside class="e3-pullquote" data-reveal>
					<p><?php echo esc_html( $insight_body ); ?></p>
				</aside>
			</div>
		</section>

		<?php if ( ! empty( $faq_items ) ) : ?>
			<section class="e3-section e3-section--paper-2" id="einwaende" <?php echo $tracking_attrs; // raw-ok phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> aria-labelledby="einwaende-title">
				<div class="e3-section__inner">
					<div class="e3-section__head" data-reveal>
						<p class="e3-kicker">Einwände</p>
						<h2 class="e3-section__title" id="einwaende-title">Die sieben Fragen, die nach diesem Fall meistens kommen.</h2>
					</div>

					<div class="e3-faq">
						<?php foreach ( $faq_items as $faq_index => $faq_item ) : ?>
							<details class="e3-faq__item" data-reveal<?php echo 0 === $faq_index ? ' open' : ''; ?>>
								<summary class="e3-faq__q"><?php echo esc_html( $faq_item['question'] ); ?></summary>
								<div class="e3-faq__a">
									<p><?php echo esc_html( $faq_item['answer'] ); ?></p>
								</div>
							</details>
						<?php endforeach; ?>
					</div>
				</div>
			</section>
		<?php endif; ?>

		<section class="e3-section e3-section--cta" id="cta" <?php echo $tracking_attrs; // raw-ok phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> aria-labelledby="cta-title">
			<div class="e3-section__inner e3-cta" data-reveal>
				<p class="e3-kicker">Nächster Schritt</p>
				<h2 class="e3-section__title" id="cta-title">Wenn Sie eine ähnliche Asymmetrie zwischen Lead-Quellen sehen — oder vermuten — klären wir das im Marktcheck.</h2>
				<p class="e3-section__lede">Der Marktcheck prüft Ihre konkrete Situation kompakt: Anfrage-Quellen, Tracking, Funnel und Vertriebsanschluss. Sie erhalten eine erste schriftliche Einordnung mit priorisiertem nächstem Schritt. Kein Pitch-Call.</p>

				<div class="e3-fit">
					<div class="e3-fit__panel e3-fit__panel--yes">
						<h3>Der Marktcheck passt, wenn:</h3>
						<ul>
							<?php foreach ( $fit_yes as $item ) : ?>
								<li><?php echo esc_html( $item ); ?></li>
							<?php endforeach; ?>
						</ul>
					</div>
					<div class="e3-fit__panel e3-fit__panel--no">
						<h3>Er passt nicht, wenn:</h3>
						<ul>
							<?php foreach ( $fit_no as $item ) : ?>
								<li><?php echo esc_html( $item ); ?></li>
							<?php endforeach; ?>
						</ul>
					</div>
				</div>

				<ul class="e3-feature-list">
					<?php foreach ( $cta_features as $feature ) : ?>
						<li><?php echo esc_html( $feature ); ?></li>
					<?php endforeach; ?>
				</ul>

				<div class="e3-cta__actions">
					<a class="e3-btn" href="<?php echo esc_url( $diagnostic_url ); ?>" data-track-action="cta_case_study_to_diagnostic_request" data-track-category="lead_gen" data-track-section="case_solar_methodology">
						<span><?php echo esc_html( $cta_label ); ?></span>
						<span class="e3-btn__arrow" aria-hidden="true">→</span>
					</a>
				</div>

				<p class="e3-cta__micro">Keine Zahlungsdaten, kein Abo — der Marktcheck prüft zuerst, ob der Fit überhaupt passt.</p>
			</div>
		</section>

		<section class="e3-section e3-section--paper-2 e3-section--deeper" id="vertiefung" <?php echo $tracking_attrs; // raw-ok phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> aria-labelledby="vertiefung-title">
			<div class="e3-section__inner">
				<div class="e3-section__head" data-reveal>
					<p class="e3-kicker">Methodik im Detail</p>
					<h2 class="e3-section__title" id="vertiefung-title">Vier Bausteine, die dieses Ergebnis möglich gemacht haben.</h2>
					<p class="e3-section__lede">
						Wer den Fall fachlich vertiefen will: hier die thematischen Seiten zu Kosten pro Anfrage, Tracking-Architektur, Funnel-Stufen und der TCO-Logik gegenüber Lead-Portalen.
					</p>
				</div>

				<ul class="e3-deeper-list">
					<?php foreach ( $e3_deeper as $deeper_item ) : ?>
						<li class="e3-deeper-item" data-reveal>
							<a class="e3-deeper-link"
							   href="<?php echo esc_url( $deeper_item['url'] ); ?>"
							   data-track-action="cta_case_study_deeper_link"
							   data-track-category="navigation"
							   data-track-section="vertiefung">
								<span class="e3-deeper-link__t"><?php echo esc_html( $deeper_item['t'] ); ?></span>
								<span class="e3-deeper-link__s"><?php echo esc_html( $deeper_item['s'] ); ?></span>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>

				<p class="e3-coda e3-coda--agentur" data-reveal>
					Wenn es nicht um Photovoltaik oder Wärmepumpe geht, sondern um ein erklärungsbedürftiges Angebot mit langem Entscheidungsweg, ist der passendere Einstieg die
					<a href="<?php echo esc_url( $agentur_url ); ?>" data-track-action="cta_case_study_to_agentur" data-track-category="navigation" data-track-section="vertiefung">WordPress Agentur Hannover</a>.
				</p>
			</div>
		</section>
	</div>
</main>

<?php
get_template_part(
	'template-parts/seo-subpage-sticky-cta',
	null,
	[
		'cta_url'        => $diagnostic_url,
		'track_category' => 'case_solar_methodology',
		'lead'           => 'Marktcheck',
		'sub'            => 'Fit-Befund statt Standardbericht',
		'label'          => 'Eigene Quellen prüfen',
		'track_action'   => 'cta_sticky_case_study_marktcheck',
	]
);

get_footer();
