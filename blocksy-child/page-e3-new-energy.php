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

$e3_cpl_before  = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'cpl_before', 'display', '150 €' ) : '150 €';
$e3_cpl_after   = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'cpl_after', 'display', '22 €' ) : '22 €';
$e3_lead_count  = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'lead_count', 'display', '1.750+' ) : '1.750+';
$e3_conv_uplift = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'sales_conversion_uplift', 'display', '1 – 5 % → 12 %' ) : '1 – 5 % → 12 %';
$e3_timeframe   = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'timeframe', 'display', '6 Monate' ) : '6 Monate';

$metrics = [
	sprintf( '%s → %s CPL', $e3_cpl_before, $e3_cpl_after ),
	sprintf( '%s Anfragen', $e3_lead_count ),
	sprintf( '%s Abschlussquote', $e3_conv_uplift ),
	sprintf( '%s Laufzeit', $e3_timeframe ),
];

$hypotheses = [
	[
		'number' => '1.',
		'title'  => 'Intent-Stärke',
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
		'title'  => 'Echtzeit',
		'body'   => 'Viessmann-Anfragen kommen in Minuten. Portal-Leads brauchen oft Stunden bis Tage — bis dahin ist die Aufmerksamkeit der Person längst woanders.',
	],
];

$implementation_blocks = [
	[
		'label' => 'Block A',
		'title' => 'Intent-Stärke nachbauen',
		'body'  => 'Statt breite Audiences auf Meta zu bewerben ("Hausbesitzer in Niedersachsen"), wurde der Ads-Stack neu strukturiert: Kampagnen auf Such-Intent (Google) und auf gezielte Lookalikes existierender Abschluss-Kunden (Meta). Parallel dazu Content-Seiten, die für hochintentionale Suchanfragen ranken — nicht für generische Awareness-Themen. Wer "Wärmepumpe Hannover Förderung 2025" sucht, ist näher am Kauf als wer "Solar lohnt sich" liest.',
	],
	[
		'label' => 'Block B',
		'title' => 'Exklusivität nachbauen',
		'body'  => 'Eigene Domain, eigenes Formular, eigene Datenbank. Jede Anfrage ging exklusiv an den Betrieb, nicht an drei andere Anbieter gleichzeitig. Zusätzlich: CRM-Webhook direkt aus dem Formular, damit der Erstkontakt in Minuten erfolgte, nicht in Stunden — der gleiche Speed-Vorteil, den Viessmann-Anfragen strukturell hatten.',
	],
	[
		'label' => 'Block C',
		'title' => 'Vorqualifizierung nachbauen',
		'body'  => 'Multi-Step-Formular mit gezielten Filterfragen: Eigentümerstatus, Objekttyp, Zeitraum für die Umsetzung, monatliche Stromkosten, Telefon-Erreichbarkeit. Wer Mieter ohne Eigentümer-Abstimmung war, fiel raus. Wer "nur informieren" wollte, ging in einen separaten Funnel. Im CRM bekam jede Anfrage einen Lead-Score, damit der Vertrieb die A-Leads zuerst anrief.',
	],
	[
		'label' => 'Block D',
		'title' => 'Echtzeit und Datenfrische nachbauen',
		'body'  => 'Server-Side Tracking mit GA4 und Meta CAPI als Fundament — damit überhaupt sichtbar wurde, welche Kampagne welche Anfrage und welchen Abschluss erzeugte. Ohne saubere Attribution lässt sich nichts optimieren. Webhook-basierte CRM-Übergabe stellte sicher, dass Anfragen nicht in einem E-Mail-Postfach versanden, sondern in Sekunden im Vertriebssystem ankamen.',
	],
];

$timeline_phases = [
	[
		'title' => 'Monate 1–2 — Aufbau und Kreativ-Tests',
		'body'  => 'Tracking-Infrastruktur, Landingpage-Architektur, Formular-Logik, CRM-Anbindung wurden parallel aufgesetzt. Erste Ads-Kampagnen liefen mit unterschiedlichen Creatives und Targetings. Leadkosten in dieser Phase: 70–100 € pro Anfrage — solide, aber noch nicht konkurrenzlos gegenüber Portal-Einkauf.',
	],
	[
		'title' => 'Monat 3 — System läuft',
		'body'  => 'CRM-Anbindung produktiv, Lead-Score aktiv, Server-Side Tracking attribuiert sauber. Vertrieb arbeitet ausschließlich auf eigenen Anfragen, Portal-Einkauf wird heruntergefahren.',
	],
	[
		'title' => 'Monate 4–6 — Optimierung',
		'body'  => 'Auf Basis der nun belastbaren Daten wurden Audiences geschärft, Creatives iteriert, Formular-Felder gestrafft, Conversion-Pfade getestet. CPL stabilisierte sich bei ~22 € pro Anfrage. Conversion auf Auftrag erreichte 12 % — kein Viessmann-Niveau, aber deutlich über dem 1–5 %-Korridor der Portal-Leads.',
	],
];

$transferable = [
	'Die Vier-Eigenschaften-Logik (Intent, Exklusivität, Vorqualifizierung, Echtzeit) gilt für jeden Solar- oder SHK-Betrieb mit eigenem Vertrieb.',
	'Tracking-Fundament ist Voraussetzung, kein optionales Add-on. Ohne sauberes Attribution-Setup wird Optimierung zum Ratespiel.',
	'Drei Monate Implementierung sind realistisch — wer "in zwei Wochen Leads" verspricht, übersieht entweder das Tracking-Fundament oder die CRM-Integration oder beides.',
	'Vertriebs-Schulung wird oft als erste Maßnahme verkauft. Bei diesem Betrieb war der Vertrieb nie das Problem. Das ist häufiger der Fall als die Branche zugibt.',
];

$specific = [
	'Konkrete Lead-Kosten hängen von Region, Produktmix und Werbedruck der Mitbewerber ab. 22 € CPL ist kein Versprechen, sondern ein Endpunkt einer 6-Monats-Strecke aus Implementierung und Optimierung.',
	'12 % Conversion auf Auftrag setzt einen funktionierenden Außendienst voraus. Ohne den nutzt das beste Anfrage-System nichts.',
	'Hersteller-Partner-Programme wie das von Viessmann sind eine wertvolle Zusatzquelle, ersetzen aber kein eigenes System — Partner-Kontingente sind begrenzt und nicht steuerbar.',
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
		'body'  => 'Erst mit dieser First-Party-Attribution bekamen Meta und Google die belastbaren Kauf-Signale, die ihre Optimierung braucht. Der CPL-Sturz war ab Monat 3 keine Marketing-Magie, sondern eine zwingende Folge: Wer den Algorithmen sagt, wer wirklich gekauft hat, bekommt mehr von genau diesen Leuten — und weniger Klick-Touristen.',
	],
];

$insight_body = sprintf(
	'Höheres Werbebudget skaliert bei taubem Tracking nur den Verlust. Erst als der Vertrieb (CRM) und der Werbe-Algorithmus über First-Party-Attribution dieselbe Sprache sprachen, konnte die KI der Werbekanäle kaufbereite Hausbesitzer von Klick-Touristen unterscheiden. Der CPL-Sturz von %s auf %s in %s war keine Optimierungsleistung — er war die mathematische Folge sauberer Daten.',
	function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'cpl_before' ) : '150 €',
	function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'cpl_after' ) : '22 €',
	function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'timeframe', 'display_dative' ) : '6 Monaten'
);

$e3_deeper = [
	[
		't'   => 'Cost per Lead Photovoltaik',
		's'   => 'Wie die CPL-Senkung von 150 € auf 22 € konkret zustande kommt – drei Szenarien im Vergleich.',
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
					<p class="e3-kicker">Methodik-Case · Solar &amp; Wärmepumpe</p>
					<h1 class="e3-hero__headline" id="e3-hero-title">Warum gekaufte Leads bei einem mittelständischen PV-Installationsbetrieb nicht funktionierten — und was wir stattdessen gebaut haben.</h1>
					<p class="e3-hero__subline">Ein mittelständischer PV-Installationsbetrieb hatte zwei parallele Anfrage-Quellen: Portal-Leads für 150 € pro Stück und kostenlose Viessmann-Partner-Anfragen. Die kostenlosen konvertierten deutlich besser. Die Diagnose dieses Widerspruchs wurde zur Grundlage für ein eigenes Anfrage-System.</p>
				</div>

				<div class="e3-metric-grid" role="list" aria-label="Kennzahlen des mittelständischen PV-Installationsbetriebs">
					<?php foreach ( $metrics as $metric ) : ?>
						<div class="e3-metric-card" role="listitem" data-reveal>
							<strong><?php echo esc_html( $metric ); ?></strong>
						</div>
					<?php endforeach; ?>
				</div>

				<p class="e3-hero__micro" data-reveal>Mandat mittelständischer PV-Installationsbetrieb. Implementierungsphase 3 Monate, Optimierung 3 Monate.</p>
			</div>
		</section>

		<section class="e3-section e3-section--paper" id="diagnose" <?php echo $tracking_attrs; // raw-ok phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> aria-labelledby="diagnose-title">
			<div class="e3-section__inner">
				<div class="e3-section__head" data-reveal>
					<p class="e3-kicker">Ausgangslage</p>
					<h2 class="e3-section__title" id="diagnose-title">Das Problem war nie der Vertrieb. Es war die Lead-Quelle.</h2>
				</div>

				<div class="e3-prose e3-prose--wide" data-reveal>
					<p>Der mittelständische PV-Installationsbetrieb kaufte 2024 Solar- und Wärmepumpen-Anfragen über Portale wie Aroundhome, Check24 und DAA — rund 150 € pro Lead. Die Conversion schwankte zwischen 1 % und 5 % je nach Monat. Bei 150 € Einkauf und 3 % Durchschnitt heißt das rechnerisch 5.000 € reine Lead-Kosten pro Abschluss, bevor irgendein Cent Marge entsteht.</p>
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

				<p class="e3-coda" data-reveal>Die Hypothese war einfach: Wenn ein eigenes Anfrage-System diese vier Eigenschaften strukturell nachbaut, sollte die Conversion sich denen der Viessmann-Anfragen annähern. Das war das Ziel der nächsten sechs Monate.</p>
			</div>
		</section>

		<section class="e3-section e3-section--paper-2" id="massnahmen" <?php echo $tracking_attrs; // raw-ok phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> aria-labelledby="massnahmen-title">
			<div class="e3-section__inner">
				<div class="e3-section__head" data-reveal>
					<p class="e3-kicker">Umsetzung</p>
					<h2 class="e3-section__title" id="massnahmen-title">Kein Tool installiert. Vier Eigenschaften strukturell rekonstruiert.</h2>
					<p class="e3-section__lede">Die Verlockung in dieser Situation ist, ein Tool zu kaufen — eine Landingpage-Vorlage, ein CRM, einen "Lead-Magneten". Das war nicht der Weg. Wir haben jede der vier Eigenschaften einzeln in ein eigenes System übersetzt.</p>
				</div>

				<div class="e3-implementation-list">
					<?php foreach ( $implementation_blocks as $block ) : ?>
						<article class="e3-implementation" data-reveal>
							<div class="e3-implementation__label"><?php echo esc_html( $block['label'] ); ?></div>
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
					<p class="e3-section__lede">Bevor irgendeine Optimierung greifen konnte, musste die Attribution stimmen. Ohne diesen Schritt wäre der CPL-Sturz von <?php echo esc_html( function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'cpl_before' ) : '150 €' ); ?> auf <?php echo esc_html( function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'cpl_after' ) : '22 €' ); ?> nicht reproduzierbar.</p>
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
					<p class="e3-kicker">Timeline</p>
					<h2 class="e3-section__title" id="verlauf-title">Drei Monate Implementierung. Drei Monate Optimierung.</h2>
				</div>

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

		<section class="e3-section e3-section--dark" id="lessons" <?php echo $tracking_attrs; // raw-ok phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> aria-labelledby="lessons-title">
			<div class="e3-section__inner">
				<div class="e3-section__head" data-reveal>
					<p class="e3-kicker">Was generalisierbar ist</p>
					<h2 class="e3-section__title" id="lessons-title">Was sich aus diesem Mandat auf andere Solar- und Wärmepumpen-Betriebe übertragen lässt — und was nicht.</h2>
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
					<h2 class="e3-section__title" id="insight-title">Die fundamentale Erkenntnis aus <?php echo esc_html( function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'timeframe', 'display_dative' ) : '6 Monaten' ); ?> Praxis.</h2>
				</div>

				<aside class="e3-pullquote" data-reveal>
					<p><?php echo esc_html( $insight_body ); ?></p>
				</aside>
			</div>
		</section>

		<section class="e3-section e3-section--paper-2 e3-section--deeper" id="vertiefung" <?php echo $tracking_attrs; // raw-ok phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> aria-labelledby="vertiefung-title">
			<div class="e3-section__inner">
				<div class="e3-section__head" data-reveal>
					<p class="e3-kicker">Methodik im Detail</p>
					<h2 class="e3-section__title" id="vertiefung-title">Vier Bausteine, die dieses Ergebnis möglich gemacht haben.</h2>
					<p class="e3-section__lead">
						Wer den Case fachlich vertiefen will: hier die thematischen Seiten zu CPL-Rechnung, Tracking-Architektur, Funnel-Stufen und der TCO-Logik gegenüber Lead-Portalen.
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
					Wenn es nicht um Solar oder Wärmepumpe geht, sondern um ein erklärungsbedürftiges B2B-Angebot auf WordPress, ist der passendere Einstieg die
					<a href="<?php echo esc_url( $agentur_url ); ?>" data-track-action="cta_case_study_to_agentur" data-track-category="navigation" data-track-section="vertiefung">WordPress Agentur Hannover</a>.
				</p>
			</div>
		</section>

		<section class="e3-section e3-section--cta" id="cta" <?php echo $tracking_attrs; // raw-ok phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> aria-labelledby="cta-title">
			<div class="e3-section__inner e3-cta" data-reveal>
				<p class="e3-kicker">Nächster Schritt</p>
				<h2 class="e3-section__title" id="cta-title">Wenn Sie eine ähnliche Asymmetrie zwischen Lead-Quellen sehen — oder vermuten — klären wir das im Marktcheck.</h2>
				<p class="e3-section__lede">Der Marktcheck prüft Ihre konkrete Situation kompakt: Anfrage-Quellen, Tracking, Funnel und Vertriebsanschluss. Sie erhalten eine erste schriftliche Einordnung mit priorisiertem nächstem Schritt. Kein Pitch-Call.</p>

				<ul class="e3-feature-list">
					<?php foreach ( $cta_features as $feature ) : ?>
						<li><?php echo esc_html( $feature ); ?></li>
					<?php endforeach; ?>
				</ul>

				<div class="e3-cta__actions">
					<a class="e3-btn" href="<?php echo esc_url( $diagnostic_url ); ?>" data-track-action="cta_case_study_to_diagnostic_request" data-track-category="lead_gen" data-track-section="case_solar_methodology">
						<span>Marktcheck mit Fit-Entscheid starten</span>
						<span class="e3-btn__arrow" aria-hidden="true">→</span>
					</a>
				</div>

				<p class="e3-cta__micro">Keine Zahlungsdaten, kein Abo — der Marktcheck prüft zuerst, ob der Fit überhaupt passt.</p>
			</div>
		</section>
	</div>
</main>

<?php get_footer(); ?>
