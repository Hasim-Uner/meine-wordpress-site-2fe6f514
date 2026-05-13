<?php
/**
 * Template Name: E3 New Energy Methodik-Case
 * Description: Methodik-Case fuer /e3-new-energy/.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$diagnostic_url = home_url( '/solar-waermepumpen-leadgenerierung/#energie-anfrage' );

$tracking_attrs = 'data-track-section="case_e3_methodology" data-track-funnel-stage="proof"';

$metrics = [
	'120 € → 20 € CPL',
	'1.750+ Anfragen',
	'12 % Abschlussquote',
	'9 Monate Laufzeit',
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
		'body'   => 'Viessmann-Anfragen gehen exklusiv an einen Betrieb. Portal-Leads werden parallel an 3-5 Anbieter verkauft. Wer als zweiter anruft, hat verloren.',
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
		'body'  => 'Eigene Domain, eigenes Formular, eigene Datenbank. Jede Anfrage ging exklusiv an E3, nicht an drei andere Anbieter gleichzeitig. Zusätzlich: CRM-Webhook direkt aus dem Formular, damit der Erstkontakt in Minuten erfolgte, nicht in Stunden — der gleiche Speed-Vorteil, den Viessmann-Anfragen strukturell hatten.',
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
		'title' => 'Monate 4–9 — Optimierung',
		'body'  => 'Auf Basis der nun belastbaren Daten wurden Audiences geschärft, Creatives iteriert, Formular-Felder gestrafft, Conversion-Pfade getestet. CPL stabilisierte sich bei ~20 € pro Anfrage. Conversion auf Auftrag erreichte 12 % — kein Viessmann-Niveau, aber deutlich über dem 1–5 %-Korridor der Portal-Leads.',
	],
];

$transferable = [
	'Die Vier-Eigenschaften-Logik (Intent, Exklusivität, Vorqualifizierung, Echtzeit) gilt für jeden Solar- oder SHK-Betrieb mit eigenem Vertrieb.',
	'Tracking-Fundament ist Voraussetzung, kein optionales Add-on. Ohne sauberes Attribution-Setup wird Optimierung zum Ratespiel.',
	'Drei Monate Implementierung sind realistisch — wer "in zwei Wochen Leads" verspricht, übersieht entweder das Tracking-Fundament oder die CRM-Integration oder beides.',
	'Vertriebs-Schulung wird oft als erste Maßnahme verkauft. Bei E3 war der Vertrieb nie das Problem. Das ist häufiger der Fall als die Branche zugibt.',
];

$specific = [
	'Konkrete Lead-Kosten hängen von Region, Produktmix und Werbedruck der Mitbewerber ab. 20 € CPL ist kein Versprechen, sondern ein Endpunkt einer 9-Monats-Optimierung.',
	'12 % Conversion auf Auftrag setzt einen funktionierenden Außendienst voraus. Ohne den nutzt das beste Anfrage-System nichts.',
	'Hersteller-Partner-Programme wie das von Viessmann sind eine wertvolle Zusatzquelle, ersetzen aber kein eigenes System — Partner-Kontingente sind begrenzt und nicht steuerbar.',
];

$cta_features = [
	'Preis: [Preis wird ergänzt]',
	'Wird auf eine spätere Umsetzung 1:1 verrechnet',
	'Schriftlicher Befund nach 7 Werktagen',
	'Keine Mindestlaufzeit, kein Pflicht-Termin',
];

get_header();
?>

<main id="main" class="site-main">
	<div class="energy-page-wrapper solar-page e3-methodology" <?php echo $tracking_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<section class="e3-hero" id="hero" <?php echo $tracking_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> aria-labelledby="e3-hero-title">
			<div class="e3-section__inner e3-hero__inner">
				<div class="e3-hero__content" data-reveal>
					<p class="e3-kicker">Methodik-Case · Solar &amp; Wärmepumpe</p>
					<h1 class="e3-hero__headline" id="e3-hero-title">Warum gekaufte Leads bei E3 nicht funktionierten — und was wir stattdessen gebaut haben.</h1>
					<p class="e3-hero__subline">E3 New Energy hatte zwei parallele Anfrage-Quellen: Portal-Leads für 150 € pro Stück und kostenlose Viessmann-Partner-Anfragen. Die kostenlosen konvertierten deutlich besser. Die Diagnose dieses Widerspruchs wurde zur Grundlage für ein eigenes Anfrage-System.</p>
				</div>

				<div class="e3-metric-grid" role="list" aria-label="E3 New Energy Kennzahlen">
					<?php foreach ( $metrics as $metric ) : ?>
						<div class="e3-metric-card" role="listitem" data-reveal>
							<strong><?php echo esc_html( $metric ); ?></strong>
						</div>
					<?php endforeach; ?>
				</div>

				<p class="e3-hero__micro" data-reveal>Mandat E3 New Energy. Implementierungsphase 3 Monate, Optimierung 6 Monate.</p>
			</div>
		</section>

		<section class="e3-section e3-section--paper" id="diagnose" <?php echo $tracking_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> aria-labelledby="diagnose-title">
			<div class="e3-section__inner">
				<div class="e3-section__head" data-reveal>
					<p class="e3-kicker">Ausgangslage</p>
					<h2 class="e3-section__title" id="diagnose-title">Das Problem war nie der Vertrieb. Es war die Lead-Quelle.</h2>
				</div>

				<div class="e3-prose e3-prose--wide" data-reveal>
					<p>E3 New Energy kaufte 2024 Solar- und Wärmepumpen-Anfragen über Portale wie Aroundhome, Check24 und DAA — rund 150 € pro Lead. Die Conversion schwankte zwischen 1 % und 5 % je nach Monat. Bei 150 € Einkauf und 3 % Durchschnitt heißt das rechnerisch 5.000 € reine Lead-Kosten pro Abschluss, bevor irgendein Cent Marge entsteht.</p>
					<p>Parallel dazu erhielt E3 als Premium-Partner von Viessmann Anfragen — kostenlos, direkt vom Interessenten ausgelöst, der aktiv nach einem Viessmann-Fachbetrieb in seiner Region gesucht hatte. Dieselbe Vertriebsmannschaft. Derselbe Außendienst. Dieselben Produkte. Diese Anfragen konvertierten deutlich besser.</p>
					<p>Damit war die übliche Erklärung — "der Vertrieb müsste schneller anrufen", "die Margen müssten höher sein", "das Telefonscript müsste besser werden" — sofort entkräftet. Gleicher Vertrieb, anderes Resultat. Der Unterschied lag nicht im Haus. Er lag in der Anfrage.</p>
				</div>

				<aside class="e3-pullquote" data-reveal>
					<p>Wenn dasselbe Vertriebsteam mit Anfrage A scheitert und mit Anfrage B abschließt, dann ist die Anfrage die Variable — nicht der Vertrieb.</p>
				</aside>
			</div>
		</section>

		<section class="e3-section e3-section--dark" id="hypothese" <?php echo $tracking_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> aria-labelledby="hypothese-title">
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

				<p class="e3-coda" data-reveal>Die Hypothese war einfach: Wenn ein eigenes Anfrage-System diese vier Eigenschaften strukturell nachbaut, sollte die Conversion sich denen der Viessmann-Anfragen annähern. Das war das Ziel der nächsten neun Monate.</p>
			</div>
		</section>

		<section class="e3-section e3-section--paper-2" id="massnahmen" <?php echo $tracking_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> aria-labelledby="massnahmen-title">
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

		<section class="e3-section e3-section--paper" id="verlauf" <?php echo $tracking_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> aria-labelledby="verlauf-title">
			<div class="e3-section__inner">
				<div class="e3-section__head" data-reveal>
					<p class="e3-kicker">Timeline</p>
					<h2 class="e3-section__title" id="verlauf-title">Drei Monate Implementierung. Sechs Monate Optimierung.</h2>
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

		<section class="e3-section e3-section--dark" id="lessons" <?php echo $tracking_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> aria-labelledby="lessons-title">
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

		<section class="e3-section e3-section--cta" id="cta" <?php echo $tracking_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> aria-labelledby="cta-title">
			<div class="e3-section__inner e3-cta" data-reveal>
				<p class="e3-kicker">Nächster Schritt</p>
				<h2 class="e3-section__title" id="cta-title">Wenn Sie eine ähnliche Asymmetrie zwischen Lead-Quellen sehen — oder vermuten — klären wir das in sieben Werktagen.</h2>
				<p class="e3-section__lede">Die System-Diagnose prüft Ihre konkrete Situation in vier Modulen: Anfrage-Quellen, Tracking, Funnel und Vertriebsanschluss. Sie erhalten einen schriftlichen Befund mit drei priorisierten Hebeln, einem konkreten nächsten Schritt und einer Wirtschaftlichkeits-Einordnung. Kein Pitch-Call.</p>

				<ul class="e3-feature-list">
					<!-- TODO: Preis für System-Diagnose finalisieren, dann hier ersetzen -->
					<?php foreach ( $cta_features as $feature ) : ?>
						<li><?php echo esc_html( $feature ); ?></li>
					<?php endforeach; ?>
				</ul>

				<div class="e3-cta__actions">
					<a class="e3-btn" href="<?php echo esc_url( $diagnostic_url ); ?>" data-track-action="cta_e3_to_diagnostic_request" data-track-category="lead_gen" data-track-section="case_e3_methodology">
						<span>Diagnose anfragen</span>
						<span class="e3-btn__arrow" aria-hidden="true">→</span>
					</a>
				</div>

				<p class="e3-cta__micro">Antwort innerhalb 48 Werktagsstunden per E-Mail.</p>
			</div>
		</section>
	</div>
</main>

<?php get_footer(); ?>
