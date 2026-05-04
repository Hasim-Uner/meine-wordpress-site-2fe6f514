<?php
/**
 * Native page template for the canonical slug:
 * /solar-waermepumpen-leadgenerierung/
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$request_url = function_exists( 'nexus_get_primary_request_url' ) ? nexus_get_primary_request_url() : home_url( '/anfrage/#energie-anfrage' );
$request_cta = function_exists( 'nexus_get_primary_request_cta_label' ) ? nexus_get_primary_request_cta_label() : 'Anfrage stellen';
$page_url    = function_exists( 'nexus_get_energy_systems_url' ) ? nexus_get_energy_systems_url() : home_url( '/solar-waermepumpen-leadgenerierung/' );
$e3_url      = home_url( '/e3-new-energy/' );
$e3_canon    = function_exists( 'hu_e3_canon' ) ? hu_e3_canon() : [];
$e3_metrics  = isset( $e3_canon['metrics'] ) && is_array( $e3_canon['metrics'] ) ? $e3_canon['metrics'] : [];

$e3_case_label       = isset( $e3_canon['case_label'] ) ? (string) $e3_canon['case_label'] : 'E3 New Energy';
$e3_lead_count       = $e3_metrics['lead_count']['display'] ?? '1.750+';
$e3_sales_conversion = $e3_metrics['sales_conversion']['display'] ?? '12 %';
$e3_cpl_reduction    = $e3_metrics['cpl_reduction']['display'] ?? '-85,3 %';
$e3_cpl_conservative = $e3_metrics['cpl_reduction']['conservative_display'] ?? 'über 85 %';
$e3_cpl_before       = $e3_metrics['cpl_before']['display'] ?? '150 €';
$e3_cpl_after        = $e3_metrics['cpl_after']['display'] ?? '22 €';
$e3_timeframe        = $e3_metrics['timeframe']['display'] ?? '9 Monate';
$e3_timeframe_dative = $e3_metrics['timeframe']['display_dative'] ?? '9 Monaten';
$e3_proof_sentence   = function_exists( 'hu_e3_summary' ) ? hu_e3_summary( 'proof' ) : sprintf( 'Referenz %1$s: %2$s qualifizierte Anfragen, %3$s Abschlussquote und %4$s weniger Kosten pro Anfrage.', $e3_case_label, $e3_lead_count, $e3_sales_conversion, $e3_cpl_conservative );

$pain_cards = [
	[
		'title' => '80 € pro Lead — und die Hälfte geht nicht ans Telefon.',
		'text'  => 'Sie kaufen Anfragen bei Aroundhome, Check24 oder DAA. Aber die Hälfte sind Mieter ohne Dach, Preisvergleicher ohne Budget oder Kontakte, die schon bei drei anderen Anbietern angefragt haben. Ihr Vertrieb verliert Stunden mit Leuten, die nie kaufen werden.',
	],
	[
		'title' => 'Kein Überblick, welcher Kanal sich wirklich lohnt.',
		'text'  => 'Google Ads, Portal-Leads, Empfehlungen — aber niemand kann sauber sagen, wo die guten Abschlüsse herkommen. Ohne diese Klarheit investieren Sie blind.',
	],
	[
		'title' => 'Seit 2024 kommen die Anfragen nicht mehr von allein.',
		'text'  => 'Der PV-Boom ist vorbei. Der Markt normalisiert sich. Wer jetzt keine eigene Anfrage-Infrastruktur hat, ist abhängig von Portalen — und deren Preisen.',
	],
];

$tco_rows = [
	[
		'label'         => 'Initiales Setup',
		'rental'        => 'ca. 2.000 €',
		'ownership'     => '12.000–18.000 € einmalig',
	],
	[
		'label'         => 'Monatlich (ohne Werbebudget)',
		'rental'        => 'ca. 850 € Honorar + ca. 150 € SaaS / CRM',
		'ownership'     => 'ca. 50 € Hochleistungs-Hosting',
	],
	[
		'label'         => 'TCO über 24 Monate',
		'rental'        => 'ca. 26.000 €',
		'ownership'     => 'ca. 13.200–19.200 €',
		'highlight'     => true,
	],
	[
		'label'         => 'Bilanzwirkung',
		'rental'        => 'OPEX — fließt durch die GuV ab',
		'ownership'     => 'CAPEX — aktivierbares Asset',
	],
	[
		'label'         => 'Eigentum nach Vertragsende',
		'rental'        => '0 % (Funnel, CRM, Tracking abgeschaltet)',
		'ownership'     => '100 % (Code, Tracking, Daten bleiben)',
	],
];


$journey_cards = [
	[
		'label' => 'Phase 1',
		'title' => '„Lohnt sich das überhaupt?"',
		'text'  => 'Googelt Kosten, liest Vergleiche, ist skeptisch. Hier entscheidet sich in 8 Sekunden, ob Ihre Seite relevant wirkt.',
	],
	[
		'label' => 'Phase 2',
		'title' => '„Wer macht das bei uns in der Region?"',
		'text'  => 'Sucht lokale Anbieter, vergleicht Bewertungen, will Referenzen sehen. Wenn Ihre Seite hier keinen Proof liefert, geht er zum Nächsten.',
	],
	[
		'label' => 'Phase 3',
		'title' => '„Ich will ein Angebot — aber einfach."',
		'text'  => 'Will nicht 20 Felder ausfüllen. Will nicht 3 Tage auf einen Rückruf warten. Hier verlieren Sie die meisten Anfragen.',
	],
];

$proof_kpis = [
	[
		'value' => $e3_lead_count,
		'label' => 'qualifizierte Anfragen',
	],
	[
		'value' => $e3_cpl_reduction,
		'label' => 'Kosten pro Anfrage',
	],
	[
		'value' => $e3_sales_conversion,
		'label' => 'Abschlussquote',
	],
];

$faq_items = [
	[
		'question' => 'Was kostet das im Vergleich zur Performance-Agentur?',
		'answer'   => 'Initiales Setup: 12.000–18.000 € einmalig. Laufend: ca. 50 €/Monat Hochleistungs-Hosting. TCO über 24 Monate: 13.200–19.200 € — und Sie besitzen Code, Tracking und Daten. Eine Performance-Agentur mit Paket „Regio+" kostet im gleichen Zeitraum rund 26.000 € und Sie besitzen am Ende nichts. Bilanziell: CAPEX statt OPEX.',
	],
	[
		'question' => 'Funktioniert das auch für kleinere Betriebe mit 5–10 Mitarbeitern?',
		'answer'   => 'Belastbar ab 10 Mitarbeitern und mindestens 20 qualifizierten Anfragen pro Monat. Darunter trägt die Investition den TCO-Vorteil nicht — ehrlicher Hinweis: ein schlankerer Ansatz mit klarer Landingpage und sauberem Tracking ist dann der bessere Hebel.',
	],
	[
		'question' => 'Warum nicht einfach mehr Google Ads schalten?',
		'answer'   => 'Mehr Budget auf schlechte Seiten heißt mehr Geld für dieselben unqualifizierten Anfragen. Erst wenn Seite, Formular und serverseitiges Tracking sauber arbeiten, lohnt sich mehr Reichweite. Ohne eigene Datenebene bleiben Sie zudem bei den Performance-Daten der Plattform — und damit in deren Logik.',
	],
	[
		'question' => 'Brauchen wir eine neue Website?',
		'answer'   => 'Meistens nicht im Komplettumfang. Was Sie brauchen: hardcoded WordPress (kein Page-Builder, kein Plugin-Stack), serverseitiges Tracking auf eigenem Server, Conversion-Pfad ohne Mietsysteme. Ob das ein Teil-Umbau oder ein sauberer Erstaufbau wird, zeigt sich im ersten Schritt.',
	],
	[
		'question' => 'Wir nutzen schon eine Performance-Agentur — warum sollten wir wechseln?',
		'answer'   => 'Müssen Sie nicht. Drei Prüffragen: 1) Wem gehört der Code Ihrer Landingpage? 2) Wem gehört das CRM, in dem Ihre Leads liegen? 3) Wem gehört der Tracking-Account? Wenn die Antwort dreimal „uns" ist, brauchen Sie mich nicht. Wenn die Antwort dreimal „der Agentur" ist, mieten Sie ein System.',
	],
	[
		'question' => 'Wie schnell sieht man Ergebnisse?',
		'answer'   => 'Erste Verbesserungen entstehen oft nach den ersten Optimierungen an Tracking, Formularen und Conversion-Pfaden. Belastbare Skalierung braucht in der Regel mehrere Wochen bis Monate, weil Daten, Tests und Kanäle zusammenspielen müssen.',
	],
	[
		'question' => 'Was unterscheidet Sie von Lead-Portalen?',
		'answer'   => 'Portale vermieten Nachfrage. Sie zahlen für jeden Kontakt, den auch 3-4 Mitbewerber erhalten. Das System hier baut eigene Nachfrage-Infrastruktur auf, die Ihrem Betrieb gehört und langfristig für exklusive Anfragen sorgt.',
	],
	[
		'question' => 'Arbeiten Sie mit meinem bestehenden CRM?',
		'answer'   => 'Ja, sofern technisch sinnvoll. Ziel ist nicht ein neues Tool um jeden Preis, sondern ein System, in dem Anfragen, Quellen und Status sauber messbar werden und Leads nicht manuell abgetippt werden müssen.',
	],
];

$service_schema = [
	'@context'    => 'https://schema.org',
	'@type'       => 'Service',
	'@id'         => trailingslashit( $page_url ) . '#service',
	'name'        => 'Website als Vertriebssystem für Solar- und Wärmepumpen-Anbieter',
	'serviceType' => 'Aufbau eigener Anfrage-Systeme zur Ablösung von Portal-Leads für Solar-, Wärmepumpen-, Speicher- und Energie-Anbieter',
	'url'         => $page_url,
	'description' => sprintf( 'Eigenes Anfrage-System für Solar- und Wärmepumpen-Betriebe: Schluss mit teuren Portal-Leads. Referenz %1$s — %2$s weniger Kosten pro Anfrage in %3$s.', $e3_case_label, $e3_cpl_conservative, $e3_timeframe_dative ),
	'provider'    => [
		'@type' => 'Person',
		'name'  => 'Haşim Üner',
		'url'   => home_url( '/' ),
	],
	'audience'    => [
		'@type'        => 'Audience',
		'audienceType' => 'Solar-, Wärmepumpen-, Speicher- und Energie-Anbieter im DACH-Raum',
	],
	'areaServed'  => [
		[
			'@type' => 'Country',
			'name'  => 'Deutschland',
		],
	],
	'offers'      => [
		'@type'         => 'Offer',
		'price'         => '0',
		'priceCurrency' => 'EUR',
		'description'   => 'Diagnostische System-Einordnung für Ihre Anfrage-Prozesse.',
	],
];

$faq_schema = [
	'@context'   => 'https://schema.org',
	'@type'      => 'FAQPage',
	'@id'        => trailingslashit( $page_url ) . '#faq',
	'url'        => trailingslashit( $page_url ) . '#faq',
	'mainEntity' => [],
];

foreach ( $faq_items as $faq_item ) {
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
<main id="main" class="site-main">
	<div class="energy-page-wrapper" data-track-section="energy_service_landing" data-track-funnel-stage="energy_landing">
		<section class="nx-section nx-hero energy-hero" id="hero">
			<div class="nx-container">
				<div class="energy-hero__grid">
					<div class="energy-hero__copy">
						<span class="nx-badge nx-badge--gold">Für Solar- und Wärmepumpen-Betriebe mit 10–25 Mitarbeitern</span>
						<h1 class="nx-hero__title">Bauen Sie ein eigenes Anfrage-System statt weiter teure Leads zu mieten.</h1>
						<p class="nx-hero__subtitle">Ich helfe B2B-Betrieben, unabhängiger von Portal-Leads zu werden &mdash; mit eigener WordPress-Infrastruktur, sauberem Tracking, Vorqualifizierung und CRM-Anbindung.</p>
						<p class="nx-cta-microcopy"><?php echo esc_html( sprintf( 'Referenz %1$s: %2$s qualifizierte Anfragen · %3$s Abschlussquote · %4$s Kosten pro Anfrage (%5$s auf %6$s)', $e3_case_label, $e3_lead_count, $e3_sales_conversion, $e3_cpl_reduction, $e3_cpl_before, $e3_cpl_after ) ); ?></p>
						<div class="energy-hero__actions">
							<a href="#energie-anfrage" class="nx-btn nx-btn--primary" data-track-action="cta_energy_hero_audit" data-track-category="lead_gen">Kostenlose System-Diagnose starten</a>
							<a href="<?php echo esc_url( $e3_url ); ?>" class="energy-text-link" data-track-action="cta_energy_hero_case" data-track-category="trust">E3-Ergebnis ansehen →</a>
						</div>
						<p class="nx-cta-microcopy" style="margin-top: 1rem;">3 Fragen. 60 Sekunden. Sofortige Einschätzung &mdash; ohne Verkaufsgespräch.</p>
					</div>

				</div>
			</div>
		</section>

		<section class="nx-section energy-section energy-diagram-section" id="modell" aria-labelledby="modell-title">
			<div class="nx-container">
				<header class="energy-section__head energy-section__head--narrow">
					<span class="nx-badge nx-badge--ghost">Zwei Wege</span>
					<h2 id="modell-title">Nachfrage mieten — oder eigene Anfrage-Infrastruktur aufbauen.</h2>
					<p><?php echo esc_html( sprintf( 'Zwei Modelle, zwei Wirtschaftlichkeiten. Das eine setzt jeden Monat neu auf Portal-Budget. Das andere baut ein Anfrage-System, das in %1$s %2$s günstiger arbeitet.', $e3_timeframe_dative, $e3_cpl_conservative ) ); ?></p>
				</header>

				<div class="energy-compare" role="group" aria-label="Vergleich: Modell A Portal-Leads vs. Modell B eigenes Anfrage-System">
					<article class="energy-compare__card energy-compare__card--alt">
						<header class="energy-compare__head">
							<span class="energy-compare__label energy-compare__label--alt">Modell A</span>
							<h3>Nachfrage mieten</h3>
							<p class="energy-compare__sub">Aroundhome &middot; Check24 &middot; DAA</p>
						</header>
						<ul class="energy-compare__list">
							<li>
								<strong>bis 150 € pro Lead</strong>
								<span>50 % gehen nicht ans Telefon</span>
							</li>
							<li>
								<strong>Kein Überblick</strong>
								<span>Welcher Kanal lohnt sich wirklich?</span>
							</li>
							<li>
								<strong>Abhängigkeit</strong>
								<span>Budget-Stopp = Nachfrage-Stopp</span>
							</li>
						</ul>
						<footer class="energy-compare__verdict energy-compare__verdict--alt">
							Status quo: teuer &amp; kurzlebig
						</footer>
					</article>

					<article class="energy-compare__card energy-compare__card--success">
						<header class="energy-compare__head">
							<span class="energy-compare__label energy-compare__label--success">Modell B</span>
							<h3>Eigenes Anfrage-System</h3>
							<p class="energy-compare__sub">Fundament &middot; Conversion &middot; Skalierung</p>
						</header>
						<ol class="energy-compare__steps">
							<li>
								<span class="energy-compare__step-num">1</span>
								<div>
									<strong>Fundament ordnen</strong>
									<span>Tracking &amp; Datenebene &middot; Privacy-first &middot; Entscheidungssignale</span>
								</div>
							</li>
							<li>
								<span class="energy-compare__step-num">2</span>
								<div>
									<strong>Conversion-Pfade schärfen</strong>
									<span>Formular &middot; Call &middot; Buchung &middot; 8-Sekunden-Regel</span>
								</div>
							</li>
							<li>
								<span class="energy-compare__step-num">3</span>
								<div>
									<strong>Skalieren</strong>
									<span>Money Pages &amp; Proof &middot; bleibende Assets &middot; Unabhängigkeit</span>
								</div>
							</li>
						</ol>
						<footer class="energy-compare__verdict energy-compare__verdict--success">
							Bleibendes Anfrage-Asset
						</footer>
					</article>
				</div>

				<aside class="energy-compare__outcome" aria-label="Referenz E3 New Energy">
					<div class="energy-compare__outcome-head">
						<span class="nx-badge nx-badge--gold">Referenz <?php echo esc_html( $e3_case_label ); ?> &middot; <?php echo esc_html( $e3_timeframe ); ?></span>
						<h3>Was das in der Praxis bedeutet.</h3>
					</div>
					<div class="energy-compare__outcome-grid">
						<div class="energy-compare__outcome-stat">
							<strong><?php echo esc_html( $e3_cpl_reduction ); ?></strong>
							<span>Kosten pro Anfrage</span>
						</div>
						<div class="energy-compare__outcome-stat">
							<strong><?php echo esc_html( $e3_lead_count ); ?></strong>
							<span>qualifizierte Anfragen</span>
						</div>
						<div class="energy-compare__outcome-stat">
							<strong><?php echo esc_html( $e3_sales_conversion ); ?></strong>
							<span>Abschlussquote</span>
						</div>
					</div>
					<div class="energy-compare__outcome-actions">
						<a href="<?php echo esc_url( $request_url ); ?>" class="nx-btn nx-btn--primary" data-track-action="cta_energy_compare_request" data-track-category="lead_gen" data-track-section="energy_compare" data-track-funnel-stage="energy_compare">Standortbestimmung anfordern</a>
						<span class="energy-compare__outcome-microcopy">5 Fragen &middot; ca. 90 Sekunden &middot; Antwort per E-Mail in 48 Stunden</span>
					</div>
				</aside>
			</div>
		</section>

		<section class="nx-section energy-section energy-tco" id="tco" aria-labelledby="tco-title">
			<div class="nx-container">
				<header class="energy-section__head energy-section__head--narrow">
					<span class="nx-badge nx-badge--ghost">CAPEX statt OPEX</span>
					<h2 id="tco-title">Der gleiche Hebel, zwei Bilanzwirkungen: Miete oder Werkzeug.</h2>
					<p>Performance-Agenturen verkaufen Ihnen Reichweite zur Miete: Funnel auf deren Server, CRM unter deren Lizenz, Tracking unter deren Account. Vertrag endet, Hebel weg. Der gleiche monatliche Betrag fließt 24 Monate lang in ein System, das Ihnen am Ende nicht gehört.</p>
				</header>

				<div class="energy-tco__table-wrap">
					<table class="energy-tco__table" aria-describedby="tco-title">
						<caption class="screen-reader-text">TCO-Vergleich über 24 Monate: Performance-Agentur (Miete) gegenüber Infrastruktur-Aufbau (Eigentum).</caption>
						<thead>
							<tr>
								<th scope="col">Kostenpunkt</th>
								<th scope="col" class="energy-tco__col energy-tco__col--rental">Mietsystem<small>Performance-Agentur, Paket „Regio+"</small></th>
								<th scope="col" class="energy-tco__col energy-tco__col--ownership">Infrastruktur-Aufbau<small>WGOS &middot; Code im Eigentum</small></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( $tco_rows as $tco_row ) : ?>
								<tr<?php echo ! empty( $tco_row['highlight'] ) ? ' class="is-highlight"' : ''; ?>>
									<th scope="row"><?php echo esc_html( $tco_row['label'] ); ?></th>
									<td class="energy-tco__col energy-tco__col--rental"><?php echo esc_html( $tco_row['rental'] ); ?></td>
									<td class="energy-tco__col energy-tco__col--ownership"><?php echo esc_html( $tco_row['ownership'] ); ?></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>

				<p class="energy-tco__pointe">Nach 24 Monaten haben Sie im Mietmodell rund 26.000 € ausgegeben und besitzen nichts. Im Infrastruktur-Modell haben Sie weniger ausgegeben und ein laufendes System in Ihrer Bilanz. Der Unterschied ist nicht der Preis &mdash; der Unterschied ist, was am Tag der Vertragskündigung übrig bleibt.</p>

				<div class="energy-tco__cta">
					<a href="<?php echo esc_url( $request_url ); ?>" class="nx-btn nx-btn--ghost" data-track-action="cta_energy_tco_request" data-track-category="lead_gen" data-track-section="energy_tco" data-track-funnel-stage="energy_tco">TCO-Rechnung für Ihren Betrieb anfordern</a>
					<span class="energy-tco__cta-microcopy">Sie erhalten eine Aufstellung mit den Annahmen für Ihre Region und Ihr aktuelles Lead-Volumen. Kein Vertriebsgespräch erforderlich.</span>
				</div>
			</div>
		</section>

		<section class="nx-section energy-section energy-proof" id="proof">
			<div class="nx-container">
				<div class="energy-proof__layout">
					<div class="energy-proof__copy">
						<span class="nx-badge nx-badge--gold">Proof / Case Study</span>
						<h2>E3 New Energy.</h2>
						<p><strong>Vorher:</strong> Hohe Abhängigkeit von externen Leadquellen (ø <?php echo esc_html( $e3_cpl_before ); ?>/Lead). Leads waren oft schwer erreichbar, nicht qualifiziert und es fehlte ein klarer Überblick, welche Kanäle tatsächlich Termine und Abschlüsse erzeugten.</p>
						<p><strong>Umsetzung:</strong> Tracking-Fundament aufgebaut, Anfragepfade optimiert, smarte Vorqualifizierung eingeführt und eine strukturierte CRM-Übergabe eingerichtet.</p>
						<p><strong>Ergebnis nach <?php echo esc_html( $e3_timeframe_dative ); ?>:</strong> <?php echo esc_html( sprintf( '%1$s qualifizierte Anfragen, %2$s Abschlussquote und %3$s Kosten pro Anfrage (auf %4$s gesenkt).', $e3_lead_count, $e3_sales_conversion, $e3_cpl_reduction, $e3_cpl_after ) ); ?></p>
						<blockquote style="margin: 1.5rem 0; padding: 1rem 1.5rem; border-left: 3px solid var(--nx-gold); background: rgba(0,0,0,0.02); font-style: italic; font-size: 1.05rem; line-height: 1.5;">
							„Seit dem System sehen wir endlich, welche Kanäle wirklich Anfragen und Abschlüsse bringen. Wir konnten den teuren Lead-Einkauf massiv reduzieren und haben jetzt eine eigene Pipeline.“
						</blockquote>
						<div class="energy-proof__actions" style="margin-top: 1.5rem;">
							<a href="<?php echo esc_url( $e3_url ); ?>" class="nx-btn nx-btn--primary" data-track-action="cta_energy_proof_case" data-track-category="trust">Case Study lesen</a>
						</div>
					</div>

					<aside class="energy-proof__panel" aria-label="Ergebniskennzahlen">
						<div class="energy-proof-kpi-grid">
							<?php foreach ( $proof_kpis as $proof_kpi ) : ?>
								<div class="energy-proof-kpi">
									<strong><?php echo esc_html( $proof_kpi['value'] ); ?></strong>
									<span><?php echo esc_html( $proof_kpi['label'] ); ?></span>
								</div>
							<?php endforeach; ?>
						</div>
					</aside>
				</div>
			</div>
		</section>

		<section class="nx-section energy-section energy-section--alt" id="reibung">
			<div class="nx-container">
				<div class="energy-section__head">
					<span class="nx-badge nx-badge--ghost">Alltag im Vertrieb</span>
					<h2>Kommt Ihnen das bekannt vor?</h2>
				</div>
				<div class="energy-pain-grid">
					<?php foreach ( $pain_cards as $index => $pain_card ) : ?>
						<article class="energy-pain-card">
							<span class="energy-pain-card__index"><?php echo esc_html( str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
							<h3><?php echo esc_html( $pain_card['title'] ); ?></h3>
							<p><?php echo esc_html( $pain_card['text'] ); ?></p>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</section>

		<section class="nx-section energy-section" id="was-sie-besitzen">
			<div class="nx-container">
				<div class="energy-section__head energy-section__head--narrow">
					<span class="nx-badge nx-badge--gold">Keine Miet-Leads</span>
					<h2>Was Ihr Betrieb danach besitzt</h2>
					<p class="nx-subheadline" style="margin:1rem auto 0; text-align: center;">Kein Leadpaket. Keine gemietete Landingpage. Kein Agentur-Blindflug. Sie erhalten ein eigenes Anfrage-System.</p>
				</div>
				<div class="nx-grid nx-grid-2" style="margin-top:3rem;">
					<div class="nx-card nx-card--flat">
						<h3 class="nx-card__title">1. Eigene WordPress-Assets</h3>
						<p class="nx-card__text">Eine conversion-optimierte Seite sowie Proof- und Angebotsseiten, die langfristig in Ihrem Besitz bleiben.</p>
					</div>
					<div class="nx-card nx-card--flat">
						<h3 class="nx-card__title">2. Vorqualifizierung &amp; CRM</h3>
						<p class="nx-card__text">Smarte Formulare filtern unpassende Anfragen heraus und übergeben die Leads sauber an Ihr CRM-System.</p>
					</div>
					<div class="nx-card nx-card--flat">
						<h3 class="nx-card__title">3. Echtes Tracking</h3>
						<p class="nx-card__text">Sie sehen genau, welche Kampagne Anfragen und Termine erzeugt — volle Messbarkeit der Leadquellen.</p>
					</div>
					<div class="nx-card nx-card--flat">
						<h3 class="nx-card__title">4. Grundlage für Skalierung</h3>
						<p class="nx-card__text">Mit diesem Fundament können Sie Google Ads, Meta Ads und SEO endlich wirtschaftlich skalieren.</p>
					</div>
				</div>
			</div>
		</section>

		<section class="nx-section energy-section energy-section--alt" id="branchenverstaendnis">
			<div class="nx-container">
				<div class="energy-section__head energy-section__head--narrow">
					<span class="nx-badge nx-badge--gold">So denkt Ihr Kunde</span>
					<h2>Was ein Hausbesitzer durchmacht, bevor er bei Ihnen anfragt.</h2>
				</div>

				<div class="energy-journey-shell" aria-label="Entscheidungsphasen">
					<?php foreach ( $journey_cards as $journey_card ) : ?>
						<article class="energy-journey-card">
							<span class="energy-journey-card__label"><?php echo esc_html( $journey_card['label'] ); ?></span>
							<h3><?php echo esc_html( $journey_card['title'] ); ?></h3>
							<p><?php echo esc_html( $journey_card['text'] ); ?></p>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</section>

		<section class="nx-section energy-section" id="erstgespraech">
			<div class="nx-container">
				<div class="nx-cta-box energy-cta-box">
					<h2>Unsicher, ob Infrastruktur statt Miete für Ihren Betrieb passt?</h2>
					<p>5 Fragen, ca. 90 Sekunden. Sie beschreiben Region, Lead-Volumen und aktuellen Engpass — ich melde mich innerhalb von 48 Stunden mit einer ehrlichen Einordnung per E-Mail. Bei Nicht-Eignung bekommen Sie einen konkreten Hinweis auf eine realistischere Alternative.</p>
					<div class="energy-cta-box__actions">
						<a href="<?php echo esc_url( $request_url ); ?>" class="nx-btn nx-btn--primary" data-track-action="cta_energy_erstgespraech" data-track-category="lead_gen" data-track-section="energy_midpage" data-track-funnel-stage="energy_midpage">Standortbestimmung anfordern</a>
					</div>
				</div>
			</div>
		</section>


		<section class="nx-section energy-section" id="faq">
			<div class="nx-container">
				<div class="energy-section__head energy-section__head--narrow">
					<span class="nx-badge nx-badge--ghost">Häufige Fragen</span>
					<h2>Was Solar- und Wärmepumpen-Betriebe vor dem Erstgespräch wissen wollen.</h2>
				</div>
				<div class="nx-faq energy-faq">
					<?php foreach ( $faq_items as $index => $faq_item ) : ?>
						<details class="nx-faq__item"<?php echo 0 === $index ? ' open' : ''; ?>>
							<summary><?php echo esc_html( $faq_item['question'] ); ?></summary>
							<div class="nx-faq__content"><?php echo esc_html( $faq_item['answer'] ); ?></div>
						</details>
					<?php endforeach; ?>
				</div>
			</div>
		</section>

		<section class="nx-section energy-section energy-section--alt" id="trust-hasim">
			<div class="nx-container">
				<div class="energy-section__head energy-section__head--narrow" style="text-align: center;">
					<span class="nx-badge nx-badge--ghost">Über den Umsetzer</span>
					<h2 style="margin-top: 1rem;">Messbare Infrastruktur statt digitaler Broschüren.</h2>
				</div>
				<div class="nx-card nx-card--flat" style="max-width: 800px; margin: 3rem auto 0; padding: 3rem; text-align: center;">
					<p style="font-size: 1.125rem; line-height: 1.6; margin-bottom: 1.5rem;">
						Ich bin Haşim Üner und baue WordPress-basierte Anfrage-Systeme für Betriebe in der Erneuerbaren-Energien-Branche. Mein Fokus liegt nicht auf "schönem Webdesign", sondern auf echter Nachfrage-Architektur: Tracking, Vorqualifizierung, CRM-Anbindung und Conversion-Optimierung.
					</p>
					<p style="color: var(--nx-text-muted);">
						<strong>Mit Sitz in Hannover. Spezialisiert auf Lead-Autonomie im B2B-Handwerk.</strong>
					</p>
				</div>
			</div>
		</section>

		<section class="nx-section energy-section energy-final-cta" id="abschluss">
			<div class="nx-container">
				<div class="nx-cta-box energy-cta-box">
					<span class="nx-badge nx-badge--gold">Nächster Schritt</span>
					<h2>Eigene Infrastruktur statt geteilter Portal-Leads und gemieteter Agentur-Funnel.</h2>
					<p class="nx-cta-microcopy"><?php echo esc_html( $e3_proof_sentence ); ?></p>
					<div class="energy-cta-box__actions">
						<a href="<?php echo esc_url( $request_url ); ?>" class="nx-btn nx-btn--primary" data-track-action="cta_energy_footer_request" data-track-category="lead_gen" data-track-section="energy_footer" data-track-funnel-stage="energy_footer">Standortbestimmung anfordern</a>
					</div>
					<p class="energy-final-cta__microcopy">5 Fragen, ca. 90 Sekunden. Antwort innerhalb von 48 Stunden per E-Mail. Aktuell ehrlich kommuniziert: Einzelperson &mdash; begrenzte Slots pro Quartal.</p>
				</div>
			</div>
		</section>
	</div>
</main>

<script type="application/ld+json"><?php echo wp_json_encode( $service_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?></script>
<script type="application/ld+json"><?php echo wp_json_encode( $faq_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?></script>

<?php get_footer(); ?>
